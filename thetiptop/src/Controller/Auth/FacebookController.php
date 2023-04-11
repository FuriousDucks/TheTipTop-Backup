<?php

namespace App\Controller\Auth;

use Exception;
use Symfony\Component\Routing\Annotation\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class FacebookController extends AbstractController
{
    #[Route('/connexion/facebook', name: 'login_facebook')]
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('facebook_main')
            ->redirect(
                [
                    'public_profile', 'email'
                ],
                [
                    'prompt' => 'consent',
                ]
            );
    }

    #[Route('/connect/facebook/check', name: 'connect_facebook_check')]
    public function connectCheckAction()
    {
        try {
            if (!$this->getUser())
                $this->addFlash('danger', 'Vous n\'êtes pas connecté');
            else
                $this->addFlash('success', 'Vous êtes connecté');
            return $this->redirectToRoute('profil');
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()]);
        }
    }
}
