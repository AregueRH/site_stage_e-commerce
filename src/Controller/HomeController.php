<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        // $user = new User();
        // $user->setEmail('user@user.fr')
        // ->setUsername('usertest')
        // ->setPassword($hasher->hashPassword($user, 'user'))
        // ->setRoles([]);

        // $em->persist($user);
        // $em->flush();



        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);    }
}
