<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use MailchimpMarketing\ApiClient;
use MailchimpMarketing\ApiException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ProductRepository $productRepository): Response
    {

        return $this->render('pages/home.html.twig', [
            'controller_name' => 'HomeController',
            'title' => 'Accueil',
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/newsletter', name: 'newsletter')]
    public function newsLetter(Request $request): Response
    {
        $mail = $request->request->get('email');
        if(!filter_var($mail, FILTER_VALIDATE_EMAIL) || empty($mail)) {
            $this->addFlash('error', 'Veuillez entrer une adresse mail valide');
            return $this->redirectToRoute('home');
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

                $this->addFlash('success', 'Vous êtes bien inscrit à la newsletter');
                return $this->redirectToRoute('home');


            } catch (ApiException $e) {

                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('home');

            } catch (\Exception $e) {

                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('home');

            }
        }

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

    #[Route('/apropos', name: 'apropos')]
    public function apropos(): Response
    {
        return $this->render('pages/apropos.html.twig', [
            'controller_name' => 'HomeController',
            'title' => 'Apropos',
        ]);
    }

    #[Route('/faq', name: 'faq')]
    public function faq(): Response
    {
        return $this->render('pages/faq.html.twig', [
            'controller_name' => 'HomeController',
            'title' => 'FAQ',
        ]);
    }
}
