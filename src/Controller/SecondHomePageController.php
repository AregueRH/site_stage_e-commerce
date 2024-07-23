<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SecondHomePageController extends AbstractController
{
    #[Route('/second/home/page', name: 'app_second_home_page')]
    public function index(): Response
    {
        return $this->render('second_home_page/index.html.twig');
    }
}
