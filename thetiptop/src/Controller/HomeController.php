<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[Route('/legals', name: 'legals')]
    public function legals(): Response
    {
        return $this->render('pages/legalMentions.html.twig', [
            'title' => 'Legals',
        ]);
    }
}
