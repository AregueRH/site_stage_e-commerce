<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{

    
    #[Route('/products/list', name: 'products_list_user')]
    #[IsGranted('ROLE_USER')]
    
    public function indexList(EntityManagerInterface $em): Response
    {
        // $products récupére tout les elements issus de la classe Produits dans la BDD
        $products = $em->getRepository(Produits::class)->findAll();
        // dd($products);
        return $this->render('/product/list.html.twig', [
            'products' => $products,
        
        ]);
    }


    #[Route('/products', name: 'products_list')]
    #[IsGranted('ROLE_ADMIN')]
    
    public function list(EntityManagerInterface $em): Response
    {
        // $products récupére tout les elements issus de la classe Produits dans la BDD
        $products = $em->getRepository(Produits::class)->findAll();
        
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }
    
    
    #[Route('/products/{id}/edit', name:'product.edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
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
    #[IsGranted('ROLE_ADMIN')]
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
    
    #[Route('products/{id}/delete', name: 'produit.delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function remove(Produits $produit, EntityManagerInterface $em)
    {   
        $em->remove($produit);
        $em->flush();
        $this->addFlash('success', 'Le produit a bien été supprimé');
        return $this->redirectToRoute('products_list');
    }
}
