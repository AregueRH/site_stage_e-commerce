<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'products_list')]
    public function index(EntityManagerInterface $em): Response
    {
        // pour récupérer tout les produits
        $products = $em->getRepository(Produits::class)->findAll();
        
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }


    #[Route('/products/{id}/edit', name:'product.edit', methods: ['GET', 'POST'])]
    public function edit(Produits $produit, Request $request, EntityManagerInterface $em) {
        $formProd = $this->createForm(ProductType::class, $produit);
        $formProd->handleRequest($request);

        if ($formProd->isSubmitted() && $formProd->isValid()) {
            $em->flush();
            $this->addFlash('success', 'la recette a bien été modifié');
            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('product/edit.html.twig', 
        [
            'produit' => $produit,
                'formProd' => $formProd->createView()
            
        ]);
    }
}


