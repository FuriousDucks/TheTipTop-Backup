<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('pages/home.html.twig', [
            'controller_name' => 'HomeController',
            'title' => 'Accueil',
        ]);
    }

    #[Route('/mentions-legales', name: 'mentionsLegales')]
    public function legals(): Response
    {
        return $this->render('pages/legalMentions.html.twig', [
            'title' => 'mentionsLegales',
        ]);
    }

    #[Route('/CGU', name: 'CGU')]
    public function CGU(): Response
    {
        return $this->render('pages/cgu.html.twig', [
            'title' => 'CGU',
        ]);
    }

    #[Route('/{any}', name: 'fallback', requirements: ['any' => '.*'])]
    public function fallback(): Response
    {
        return $this->render('pages/page404.html.twig', [
            'title' => 'Error',
        ]);
    }

    public function handle404(ResourceNotFoundException $exception): Response
    {
        return $this->redirectToRoute('fallback');
    }
}
