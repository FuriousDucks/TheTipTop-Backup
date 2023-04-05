<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\Winner;
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
        return $this->render('pages/participate.html.twig');
    }

    #[Route('/participer/tenter-la-chance', name: 'luck')]
    public function luck(Request $request, TicketRepository $ticketRepository, WinnerRepository $winnerRepository, EntityManagerInterface $entityManager): Response
    {
        try {
            $ticketNumber = $request->get('number');
            if (!preg_match('/^[0-9]{7}$/', $ticketNumber)) {
                $this->addFlash('danger', 'Le numéro de ticket est invalide.');
                throw new Exception('Le numéro de ticket est invalide.');
            }
            $tickets = $ticketRepository->findAll();
            $ticket = array_filter($tickets, function ($ticket) use ($ticketNumber) {
                return $ticket->getNumber() == $ticketNumber;
            });
            $ticket = array_pop($ticket);
            if ($ticket->getContest()->getStartDate() > new \DateTime() || $ticket->getContest()->getEndDate() < new \DateTime())
                throw new Exception('Le concours n\'est pas encore ouvert ou a déjà été clôturé.');
            else if ($ticket->getContest()->getMaxWinners() <= $ticket->getContest()->getMaxWinners())
                throw new Exception('Le concours est déjà terminé.');
            elseif ($winnerRepository->findOneBy(['ticket' => $ticket]))
                throw new Exception('Vous avez déjà gagné un concours.');
            else {
                /* $winner = new Winner();
                $winner->setTicket($ticket);
                $winner->setClient($this->getUser());
                $winner->setDateOfDraw(new \DateTime());
                $entityManager->persist($winner);
                $entityManager->flush();
                $this->addFlash('success', 'Vous avez gagné le concours.'); 
                return $this->redirectToRoute('participate');*/
                return $this->redirectToRoute('participate');
            }
        } catch (\Throwable $th) {
            $this->addFlash('danger', $th->getMessage());
            return $this->render('pages/participate.html.twig', [
                'error' => $th->getMessage()
            ]);
        }
    }

    public function rules()
    {
        $possibilities = [1, 2, 3, 4, 5, 6];
    }
}
