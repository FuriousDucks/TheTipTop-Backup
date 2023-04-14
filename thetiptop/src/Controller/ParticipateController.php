<?php

namespace App\Controller;

use App\Entity\Winner;
use App\Repository\ProductRepository;
use App\Repository\TicketRepository;
use App\Repository\WinnerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipateController extends AbstractController
{
    #[Route('/participer', name: 'participate')]
    public function index(): Response
    {
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('danger', 'Vous devez vérifier votre adresse mail avant de participer.');
            return $this->redirectToRoute('home');
        }
        return $this->render('pages/participate.html.twig');
    }

    #[Route('/participer/tenter-la-chance', name: 'luck')]
    public function luck(Request $request, TicketRepository $ticketRepository, WinnerRepository $winnerRepository, ProductRepository $productRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('danger', 'Vous devez vérifier votre adresse mail avant de participer.');
            return $this->redirectToRoute('home');
        }
        try {
            $ticketNumber = $request->get('number');
            if (!preg_match('/^[0-9]{10}$/', $ticketNumber)) {
                $this->addFlash('danger', 'Le numéro de ticket est invalide.');
                throw new Exception('Le numéro de ticket est invalide.');
            }
            $tickets = $ticketRepository->findAll();
            $ticket = array_filter($tickets, function ($ticket) use ($ticketNumber) {
                return $ticket->getNumber() == $ticketNumber;
            });
            $ticket = array_pop($ticket);
            if ($ticket->getContest()->getStartDate() > new \DateTime() || $ticket->getContest()->getEndDate() < new \DateTime()) {
                throw new Exception('Le concours n\'est pas encore ouvert ou a déjà été clôturé.');
            } elseif (count($ticket->getContest()->getTickets()) === $ticket->getContest()->getMaxWinners()) {
                throw new Exception('Le nombre maximum de gagnants a été atteint.');
            } elseif ($winnerRepository->findOneBy(['ticket' => $ticket])) {
                throw new Exception('Ce ticket a déjà été gagnant.');
            } else {
                $products = $productRepository->findAll();
                dump($products);
                dd($this->rules($winnerRepository, $ticket->getContest()->getMaxWinners(), $products));
                $winner = new Winner();
                $winner->setTicket($ticket);
                $winner->setCustomer($this->getUser());
                $winner->setDateOfDraw(new \DateTime());
                $product = $productRepository->find($this->rules($winnerRepository, $ticket->getContest()->getMaxWinners(), $products) ?? $products[0]->getId());
                $winner->setProduct($product);
                $winner->setRecovered(false);
                $entityManager->persist($winner);
                $entityManager->flush();
                $this->addFlash('success', 'Félicitations, vous avez gagné ' . $product->getTitle() . ' avec le ticket ' . $ticket->getNumber() . ', vous pouvez le voir dans la page "Mes gains".');
                return $this->redirectToRoute('participate');
            }
        } catch (\Throwable $th) {
            $this->addFlash('danger', $th->getMessage());
            return $this->render('pages/participate.html.twig', [
                'error' => $th->getMessage()
            ]);
        }
    }

    public function rules(WinnerRepository $winnerRepository, $max, array $products): int
    {
        try {
            $ids = [];
            foreach ($products as $product) {
                array_push($ids, $product->getId());
            }
            $possibilities = $ids;
            shuffle($possibilities);
            $gain = 0;
            foreach ($possibilities as $possibility) {
                if ($gain !== 0) {
                    break;
                }
                $count = $winnerRepository->count(['product' => $possibility]);
                $percentage = (($count * 100) / $max);
                switch ($possibility) {
                    case 1:
                        if ($percentage < 60) {
                            $gain = $possibility;
                        }
                        break;
                    case 2:
                        if ($percentage < 20) {
                            $gain = $possibility;
                        }
                        break;
                    case 3:
                        if ($percentage < 10) {
                            $gain = $possibility;
                        }
                        break;
                    case 4:
                        if ($percentage < 6) {
                            $gain = $possibility;
                        }
                        break;
                    case 5:
                        if ($percentage < 4) {
                            $gain = $possibility;
                        }
                        break;
                    default:
                        break;
                }
            }
            return $gain;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
