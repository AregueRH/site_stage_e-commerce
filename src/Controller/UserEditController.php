<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserEditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserEditController extends AbstractController
{
    #[Route('/profile/edit', name: 'user_edit')]
    #[IsGranted('ROLE_USER')]

    public function edit(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // si le mdp est changé , il le récupére ici pour ensuite le hasher
            if ($plainPassword = $form->get('plainPassword')->getData()) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            $em->flush();
            $this->addFlash('success', 'Votre profil a été mis à jour.');

            return $this->redirectToRoute('app_user_viewer');
        }

        return $this->render('user_edit/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
