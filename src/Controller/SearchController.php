<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\ProduitsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function search(Request $request, ProduitsRepository $produitsRepository): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        // $produit est ici d'abord initialisé en un tableau vide.
        $produits = [];

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // $nom récupere ici la valeur de 'nom' envoyée par le formulaire SearchType.
                $nom = $form->get('nom')->getData();
                // $produit récupere tout les elements renvoyés par la requête issu de l'entrée 'nom' de SearchType.
                $produits = $produitsRepository->findByNom($nom);
            } else {
                $this->addFlash('error', 'Le formulaire n\'est pas valide.');
            }

            // renvoie une réponse
            return $this->render('search/index.html.twig', [
                'form' => $form->createView(),
                'produits' => $produits,
            ]);
        }
        
        return $this->render('search/index.html.twig', [
            'form' => $form->createView(),
            'produits' => $produits,
        ]);
    }
}
