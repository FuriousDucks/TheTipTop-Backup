<?php

namespace App\Controller;

use MailchimpMarketing\ApiClient;
use MailchimpMarketing\ApiException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    #[Route('/newsletter', name: 'newsletter')]
    public function newsLetter(Request $request): Response
    {
        $mail = $request->request->get('email');
        if(!filter_var($mail, FILTER_VALIDATE_EMAIL) || empty($mail)) {
            return $this->json(['message' => 'Email invalide'], 400);
        } else {
            try {
                $mailchimp = new ApiClient();
                $mailchimp->setConfig([
                  'apiKey' => $this->getParameter('mailchimp_key'),
                  'server' => $this->getParameter('mailchimp_server')
                ]);

                $mailchimp->lists->addListMember($this->getParameter('mailchimp_list_id'), [
                    "email_address" => $mail,
                    "status" => "subscribed",
                ]);

                if($request->isXmlHttpRequest()) {
                    return $this->json(['message' => 'Vous êtes bien inscrit à la newsletter'], 200);
                } else {
                    $this->addFlash('success', 'Vous êtes bien inscrit à la newsletter');
                    return $this->redirectToRoute('home');
                }

            } catch (ApiException $e) {
                if($request->isXmlHttpRequest()) {
                    return $this->json(['message' => $e->getMessage()], 500);
                } else {
                    $this->addFlash('error', $e->getMessage());
                    return $this->redirectToRoute('home');
                }
            } catch (\Exception $e) {
                if($request->isXmlHttpRequest()) {
                    return $this->json(['message' =>$e->getMessage()], 500);
                } else {
                    $this->addFlash('error', $e->getMessage());
                    return $this->redirectToRoute('home');
                }
            }
        }

    }
}
