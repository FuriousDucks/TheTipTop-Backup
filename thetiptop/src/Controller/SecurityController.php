<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }
        
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($error) {
            $this->addFlash('error', 'Identifiants incorrects');
        }

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
    }


    #[Route('/admin/login', name: 'admin_login')]
    public function adminLogin(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('@EasyAdmin/page/login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,
            'action' => 'login',
            'translation_domain' => 'admin',
            'favicon_path' => '/images/app/logo.png',
            'page_title' => 'Admin Login',
            'csrf_token_intention' => 'authenticate',
            'target_path' =>'/admin',
            'username_label' => 'Votre identifiant',
            'password_label' => 'Votre mot de passe',
            'sign_in_label' => 'Connexion',
            'username_parameter' => 'my_custom_username_field',
            'password_parameter' => 'my_custom_password_field',
            'forgot_password_enabled' => true,
            'forgot_password_path' => '/reset-password',
            'forgot_password_label' => 'Oublié votre mot de passe ?',
            'remember_me_enabled' => false,
        ]);
    }
}
