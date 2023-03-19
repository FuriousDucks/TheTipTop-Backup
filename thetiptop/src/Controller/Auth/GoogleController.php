<?php

namespace App\Controller\Auth;

use Exception;
use Symfony\Component\Routing\Annotation\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class GoogleController extends AbstractController
{
    #[Route('/connexion/google', name: 'login_google')]
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('google_main')
            ->redirect(
                ['profile', 'email'],
                ['prompt' => 'consent']
            );
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheckAction()
    {
        try {
            if (!$this->getUser())
                $this->addFlash('danger', 'Vous n\'êtes pas connecté');
            else
                $this->addFlash('success', 'Vous êtes connecté');
            return $this->redirectToRoute('home');
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()]);
        }
    }
}
