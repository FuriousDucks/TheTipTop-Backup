<?php

namespace App\EmailNotification;

use App\Entity\Admin;
use App\Entity\Winner;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Mailer\MailerInterface;

class ToUser
{


    private $mailer;
    private $sender;
    private $params;
    public function __construct(MailerInterface $mailer, ContainerBagInterface $params)
    {
        $this->mailer = $mailer;
        $this->params = $params;
        $this->sender = $this->params->get('mail_from');
    }

    public function resetPassword($emailAddress, $firstname, $token)
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->sender, 'ThéTipTop'))
            ->to($emailAddress)
            ->subject('Réinitialisation de votre mot de passe')
            ->htmlTemplate('mails/reset-password.html.twig')
            ->context([
                'firstname' => $firstname,
                'token' => $token
            ]);
        $this->mailer->send($email);
    }
}
