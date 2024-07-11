<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\User;
use App\Entity\CartItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class CartService
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function getCart(): ?Cart
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return null;
        }

        $cart = $this->entityManager->getRepository(Cart::class)->findOneBy(['user' => $user]);

        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $this->entityManager->persist($cart);
            $this->entityManager->flush();
        }

        return $cart;
    }

    public function addItemToCart(CartItem $item): void
    {
        $cart = $this->getCart();
        if ($cart) {
            $cart->addCartItem($item);
            $this->entityManager->persist($item);
            $this->entityManager->flush();
        }
    }

    public function removeItemFromCart(CartItem $item): void
    {
        $cart = $this->getCart();
        if ($cart) {
            $cart->removeCartItem($item);
            $this->entityManager->remove($item);
            $this->entityManager->flush();
        }
    }

    public function getCartTotal(Cart $cart): float
    {
        $total = 0;

        foreach ($cart->getCartItems() as $cartItem) {
            $total += $cartItem->getProduct()->getPrixTTC() * $cartItem->getQuantity();
        }

        return $total;
    }

    public function clearCart(): void
    {
        $cart = $this->getCart();
        if ($cart) {
            foreach ($cart->getCartItems() as $item) {
                $this->entityManager->remove($item);
            }
            $cart->clearCartItems();
            $this->entityManager->flush();
        }
    }
}
