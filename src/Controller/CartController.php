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



    #[Route('/cart', name: 'cart_show')]
    #[IsGranted('ROLE_USER')]

    public function showCart(): Response
    {
        $cart = $this->cartService->getCart();
        $products = $this->entityManager->getRepository(Produits::class)->findAll();

        if ($cart === null) {
            return $this->render('cart/index.html.twig', [
                'cart' => null,
                'totalPrice' => 0,
                'products' => $products,
            ]);
        }

        // $totalPrice = $this->cartService->calculateTotalPrice($cart);

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'products' => $products,

            // 'totalPrice' => $totalPrice,
        ]);
    }



    #[Route('/cart/add/{productId}', name: 'cart_add_item')]




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


    // public function addItem(int $productId): RedirectResponse
    // {
    //     $product = $this->entityManager->getRepository(Produits::class)->find($productId);

    //     if ($product) {
    //         $cartItem = new CartItem();
    //         $cartItem->setProduct($product);
    //         $cartItem->setQuantity(1); // Adjust quantity as needed
    //         $this->cartService->addItemToCart($cartItem);
    //     }

    //     return $this->redirectToRoute('cart_show');
    // }





    // public function addItem(int $productId): RedirectResponse
    // {
    //     $product = $this->entityManager->getRepository(Produits::class)->find($productId);

    //     if ($product) {
    //         $cart = $this->cartService->getCart();
    //         $existingCartItem = null;

    //         foreach ($cart->getCartItems() as $cartItem) {  // Assurez-vous que c'est getCartItems()
    //             if ($cartItem->getProductId() === $product->getId()) {  // Utilisez getProductId() au lieu de getProduct()
    //                 $existingCartItem = $cartItem;
    //                 break;
    //             }
    //         }

    //         if ($existingCartItem) {
    //             $existingCartItem->setQuantity($existingCartItem->getQuantity() + 1);
    //         } else {
    //             $cartItem = new CartItem();
    //             $cartItem->setProductId($product->getId());  // Utilisez setProductId() au lieu de setProduct()
    //             $cartItem->setName($product->getNom());
    //             $cartItem->setPrice($product->getPrixTTC());
    //             $cartItem->setWeight($product->getPoids());
    //             $cartItem->setQuantity(1);
    //             $cart->addCartItem($cartItem);
    //         }

    //         $this->entityManager->persist($cart);
    //         $this->entityManager->flush();
    //     }

    //     return $this->redirectToRoute('cart_show');
    // }





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
