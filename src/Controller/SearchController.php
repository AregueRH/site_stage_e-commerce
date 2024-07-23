<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\ProduitsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    #[IsGranted('ROLE_USER')]

    public function search(Request $request, ProduitsRepository $produitsRepository): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
        $produits = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $nom = $form->get('nom')->getData();
            $produits = $produitsRepository->findByNom($nom);
        }

        return $this->render('search/index.html.twig', [
            'form' => $form->createView(),
            'produits' => $produits,
        ]);
    }
}



