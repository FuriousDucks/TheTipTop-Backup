<?php

namespace App\Controller;

use App\Form\CustomerType;
use App\Form\ResetPasswordType;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[Route('/profile', name: 'my-account')]
    public function index(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($this->getUser());
        $form = $this->createForm(CustomerType::class, $user);
        $form->handleRequest($request);
        $passwordForm = $this->createForm(ResetPasswordType::class, $user);
        $passwordForm->handleRequest($request);
        try {
            if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
                if($userPasswordHasher->isPasswordValid($user, $passwordForm->get('oldPassword')->getData())) {
                    throw new \Exception('Ancien mot de passe incorrect');
                } else {
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $passwordForm->get('password')->getData()
                        )
                    );
                    $em->persist($user);
                    $em->flush();
                    $this->addFlash('success', 'Votre mot de passe a bien été mis à jour');
                }
            } elseif ($form->isSubmitted() && $form->isValid()) {
                $user->setPassword(
                    $user->getPassword()
                );
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', 'Votre profil a bien été mis à jour');
            }

            return $this->render('pages/profile.html.twig', [
                'profileForm' => $form->createView(),
                'passwordForm' => $passwordForm->createView(),
            ]);

        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('my-account');
        }
    }

    #[Route('/mes-gains', name: 'myearnings')]
    public function earnings(CustomerRepository $customerRepository): Response
    {
        $customer = $customerRepository->find($this->getUser());
        return $this->render('pages/earnings.html.twig', [
            'gains' => $customer->getGains()
        ]);
    }
}
