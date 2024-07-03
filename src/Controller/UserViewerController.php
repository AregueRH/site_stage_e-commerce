<?php

namespace App\Controller;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserViewerController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/user/viewer', name: 'app_user_viewer')]
    public function index(): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->security->getUser();

        // Rendre la vue avec les informations de l'utilisateur
        return $this->render('user_viewer/index.html.twig', [
            'user' => $user,
        ]);
    }
}