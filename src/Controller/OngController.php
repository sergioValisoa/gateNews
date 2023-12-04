<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OngController extends AbstractController
{
    /**
     * @Route("/ong", name="app_ong")
     */
    public function index(): Response
    {
        return $this->render('ong/index.html.twig');
    }
}
