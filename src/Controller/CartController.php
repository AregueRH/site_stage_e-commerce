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

    public function __construct(CartService $cartService, EntityManagerInterface $entityManager)
    {
        $this->cartService = $cartService;
        $this->entityManager = $entityManager;
    }



    #[Route('/cart', name: 'cart_show')]
    #[IsGranted('ROLE_USER')]

    public function showCart(): Response
    {
        $cart = $this->cartService->getCart();
        if ($cart === null) {
            return $this->render('cart/index.html.twig', [
                'cart' => null,
                'totalPrice' => 0,
            ]);
        }

        // $totalPrice = $this->cartService->calculateTotalPrice($cart);

        return $this->render('cart/index.html.twig', [
            'cart' => $cart
            // 'totalPrice' => $totalPrice,
        ]);
    }


    




    /**
     * @Route("/cart/add/{productId}", name="cart_add_item")
     */
    // public function addItem(int $productId): RedirectResponse
    // {
    //     $product = $this->entityManager->getRepository(Produits::class)->find($productId);

    //     if ($product) {
    //         $cartItem = new CartItem();
    //         $cartItem->addCartItem($product);
    //         $cartItem->setQuantity(1); // Vous pouvez ajuster la quantité selon vos besoins
    //         $this->cartService->addItemToCart($cartItem);
    //     }

    //     return $this->redirectToRoute('cart_show');
    // }


    /**
     * @Route("/cart/remove/{itemId}", name="cart_remove_item")
     */
    public function removeItem(int $itemId): RedirectResponse
    {
        $item = $this->entityManager->getRepository(CartItem::class)->find($itemId);

        if ($item) {
            $this->cartService->removeItemFromCart($item);
        }

        return $this->redirectToRoute('cart_show');
    }

    /**
     * @Route("/cart/clear", name="cart_clear")
     */
    public function clearCart(): RedirectResponse
    {
        $this->cartService->clearCart();

        return $this->redirectToRoute('cart_show');
    }
}
