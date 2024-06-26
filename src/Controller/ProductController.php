<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Form\ProductType;
use Doctrine\ORM\EntityManager;
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
            $this->addFlash('success', 'la recette a bien été modifiée');
            return $this->redirectToRoute('products_list');
        }

        return $this->render('product/edit.html.twig', 
        [
            'produit' => $produit,
                'formProd' => $formProd->createView()
            
        ]);
    }
    #[Route('products/create' , name : 'produit.create')]
    public function create(Request $request, EntityManagerInterface $em) 
    {
        $produit = new Produits();
        $formulaireProd = $this->createForm(ProductType::class, $produit) ;
        $formulaireProd->handleRequest($request);

        if ($formulaireProd->isSubmitted() && $formulaireProd->isValid()) {
            $em->persist($produit);
            $em->flush();
            $this->addFlash('success', 'la recette a bien été créée');
            return $this->redirectToRoute('products_list');
        }
        
        return $this->render('product/create.html.twig', [
            'formProd' => $formulaireProd
        ]);
        
    }

    #[Route('products/{id}/edit', name: 'produit.delete', methods: ['DELETE'])]
    public function remove(Produits $produit, EntityManagerInterface $em)
    {   
        $em->remove($produit);
        $em->flush();
        $this->addFlash('success', 'Le produit a bien été modifié');
        return $this->redirectToRoute('products_list');
    }
}


// #[Route('recettes/create', name: 'recipe.create')]
//     public function create2(Request $request, EntityManagerInterface $em) 
//     {
//         $recipe = new Recipe();
//         $form = $this->createForm(RecipeType::class, $recipe);
//         $form->handleRequest($request);
//         if ($form->isSubmitted() && $form->isValid()) {
            
//             $em->persist($recipe);
//             $em->flush();
//             $this->addFlash('success', 'la recette a bien été créée');
//             return $this->redirectToRoute('recipe.index');
//         }
//         return $this->render('recipe/create.html.twig', [
//             'form' => $form
//         ]);
//     }