<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    public function show404(): Response
    {
        return $this->render('error/404.html.twig', [], Response::HTTP_NOT_FOUND);
    }

    public function show500(): Response
    {
        return $this->render('error/500.html.twig', [], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
