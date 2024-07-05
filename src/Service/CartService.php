namespace App\Service;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private $em;
    private $session;

    public function __construct(EntityManagerInterface $em, SessionInterface $session)
    {
        $this->em = $em;
        $this->session = $session;
    }

    public function getCart(User $user): Cart
    {
        $cart = $this->em->getRepository(Cart::class)->findOneBy(['user' => $user]);
        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $this->em->persist($cart);
            $this->em->flush();
        }

        return $cart;
    }

    public function addProductToCart(User $user, Product $product, int $quantity): void
    {
        $cart = $this->getCart($user);
        $item = $this->em->getRepository(CartItem::class)->findOneBy([
            'cart' => $cart,
            'product' => $product,
        ]);

        if ($item) {
            $item->setQuantity($item->getQuantity() + $quantity);
        } else {
            $item = new CartItem();
            $item->setCart($cart);
            $item->setProduct($product);
            $item->setQuantity($quantity);
            $this->em->persist($item);
        }

        $this->em->flush();
    }

    // Ajoutez d'autres méthodes comme removeProductFromCart, clearCart, etc.
}
