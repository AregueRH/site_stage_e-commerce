<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\CartItem;
use App\Entity\Produits;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    private $cartService;
    private $entityManager;

    public function __construct(CartService $cartService, EntityManagerInterface $em)
    {
        $this->cartService = $cartService;
        $this->entityManager = $em;
    }

// une route de Symfony permettant d'afficher le panier d'un utilisateur authentifié, en récupérant les infos nécessaires depuis les services et la base de données, puis en les passant à la vue Twig pour le rendu final.

    #[Route('/cart', name: 'cart_show')]
    #[IsGranted('ROLE_USER')]
    public function showCart(): Response
    {
        $cart = $this->cartService->getCart();
//  Cette ligne appelle le service de panier (cartService) pour obtenir le panier de l'utilisateur actuellement connecté. Le service de panier (cartService) encapsule la logique de récupération du panier.

        if ($cart === null) {
            return $this->render('cart/index.html.twig', [
                'cart' => null,
                'totalPrice' => 0,
                'products' => [],
                'productsWithQuantities' => [],
            ]);
        }
// Si le panier est vide, rend la vue cart/index.html.twig avec des valeurs par défaut (panier null, prix total 0, aucun produit, aucune quantité).

        $cartItems = $cart->getCartItems();
        $productsWithQuantities = [];
        $productIds = [];

        foreach ($cartItems as $item) {
            $productIds[] = $item->getProduct()->getId();
            $product = $item->getProduct();
            $productsWithQuantities[] = [
                'product' => $product,
                'quantity' => $item->getQuantity(),
            ];
        }
// $cartItems = $cart->getCartItems(); : Récupère les articles du panier.
// foreach ($cartItems as $item) : Boucle sur chaque article du panier pour obtenir les produits et leurs quantités.
// $productIds[] = $item->getProduct()->getId(); : Stocke les identifiants des produits.
// $productsWithQuantities[] = ['product' => $product, 'quantity' => $item->getQuantity()]; : Crée un tableau associatif contenant chaque produit et sa quantité.

        $products = $this->entityManager->getRepository(Produits::class)->findBy(['id' => $productIds]);
        $totalPrice = $this->cartService->getCartTotal($cart);

// Utilise l'EntityManager pour récupérer les produits à partir de la base de données en utilisant les identifiants des produits collectés précédemment.

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'products' => $products,
            'productsWithQuantities' => $productsWithQuantities,
            'totalPrice' => $totalPrice,
        ]);
    }
//  Rend la vue cart/index.html.twig avec les données du panier (le panier lui-même, les produits, les produits avec leurs quantités)



    #[Route('/cart/add/{productId}', name: 'cart_add_item')]
    #[IsGranted('ROLE_USER')]

    public function addItem(int $productId): RedirectResponse
    {
        $product = $this->entityManager->getRepository(Produits::class)->find($productId);

        if ($product) {
            $cart = $this->cartService->getCart();
            $existingCartItem = null;

            foreach ($cart->getCartItems() as $cartItem) {
                if ($cartItem->getProductId() === $product->getId()) {
                    $existingCartItem = $cartItem;
                    break;
                }
            }

            if ($existingCartItem) {
                $existingCartItem->setQuantity($existingCartItem->getQuantity() + 1);
            } else {
                $cartItem = new CartItem();
                $cartItem->setProduct($product);
                $cartItem->setProductId($product->getId());
                $cartItem->setName($product->getNom());
                $cartItem->setPrice($product->getPrixTTC());
                $cartItem->setWeight($product->getPoids());
                $cartItem->setQuantity(1);
                $cart->addCartItem($cartItem);
                $this->entityManager->persist($cartItem); // Ajout de cette ligne
            }

            $this->entityManager->persist($cart);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart/remove/{productId}', name: 'cart_remove_item')]
    #[IsGranted('ROLE_USER')]

    public function suppItem(int $productId): RedirectResponse
    {
        $product = $this->entityManager->getRepository(Produits::class)->find($productId);

        if ($product) {
            $cart = $this->cartService->getCart();
            $existingCartItem = null;

            foreach ($cart->getCartItems() as $cartItem) {
                if ($cartItem->getProductId() === $product->getId()) {
                    $existingCartItem = $cartItem;
                    break;
                }
            }

            if ($existingCartItem) {
                $newQuantity = $existingCartItem->getQuantity() - 1;
                if ($newQuantity > 0) {
                    $existingCartItem->setQuantity($newQuantity);
                } else {
                    // Si la quantité est 0 ou moins, retirer l'article du panier
                    $cart->removeCartItem($existingCartItem);
                    $this->entityManager->remove($existingCartItem);
                }
                $this->entityManager->persist($cart);
                $this->entityManager->flush();
            }

            $this->entityManager->persist($cart);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('cart_show');
    }



    /**
     * @Route("/cart/remove/{itemId}", name="cart_remove_item")
     */
    // public function removeItem(int $itemId): RedirectResponse
    // {
    //     $item = $this->entityManager->getRepository(CartItem::class)->find($itemId);

    //     if ($item) {
    //         $this->cartService->removeItemFromCart($item);
    //     }

    //     return $this->redirectToRoute('cart_show');
    // }

    /**
     * @Route("/cart/clear", name="cart_clear")
     */
    // public function clearCart(): RedirectResponse
    // {
    //     $this->cartService->clearCart();

    //     return $this->redirectToRoute('cart_show');
    // }
}
