<?php

namespace App\Controller;

use App\Repository\ProduitsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\SearchType;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
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
