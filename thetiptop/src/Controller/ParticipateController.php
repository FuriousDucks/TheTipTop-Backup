<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Repository\TicketRepository;
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
    public function luck(Request $request, TicketRepository $ticketRepository): Response
    {
        try {
            $ticket = $ticketRepository->findOneBy(['number' => $request->get('ticketNumber')]);
            if ($ticket->getContest()->getStartDate() > new \DateTime() || $ticket->getContest()->getEndDate() < new \DateTime()) {
                $this->addFlash('danger', 'Le concours n\'est pas encore ouvert ou a déjà été clôturé.');
                throw new Exception('Le concours n\'est pas encore ouvert ou a déjà été clôturé.');
            } else if ($ticket->getContest()->getMaxWinners() <= $ticket->getContest()->getMaxWinners()) {
                $this->addFlash('danger', 'Le concours est déjà terminé.');
                throw new Exception('Le concours est déjà terminé.');
            }
            return $this->render('pages/participate_form.html.twig');
        } catch (\Throwable $th) {
            $this->addFlash('danger', $th->getMessage());
            return $this->redirectToRoute('participate');
        }
    }
}
