<?php

namespace App\Service;

use App\Entity\Photo;
use App\Repository\PhotoRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private const TVA = 0.20;
    private $session;
    private $photoRepository;

    public function __construct(RequestStack $requestStack, PhotoRepository $photoRepository)
    {
        $this->session = $requestStack->getSession();
        $this->photoRepository = $photoRepository;
    }

    public function getCart(): array
    {
        return $this->session->get('cart', []);
    }

    public function addPhotoToCart(int $photoId, int $quantity): array
    {
        $cart = $this->session->get('cart', []);
        $photo = $this->photoRepository->find($photoId);

        if (!$photo) {
            return ['photo' => null, 'cartQty' => 0];
        }

        if (isset($cart[$photoId])) {
            $cart[$photoId]['qty'] += $quantity;
        } else {
            $cart[$photoId] = [
                'photoId' => $photoId,
                'qty' => $quantity,
                'price' => $photo->getPrice(),
                'title' => $photo->getTitle(),
                'url' => $photo->getUrl(),
                'slug' => $photo->getSlug(),
                'priceHT' => $this->calculatePriceHT($photo->getPrice()),
                'tva' => $this->calculateTVA($photo->getPrice())
            ];
        }

        $this->session->set('cart', $cart);

        // Calculate total quantity in cart
        $cartQty = $this->getTotalQuantity();

        return ['photo' => $photo, 'cartQty' => $cartQty];
    }

    public function getTotalCart()
    {
        $items = $this->session->get('cart', []);
        $total = ['subtotal' => 0, 'tva' => 0, 'total' => 0];

        foreach ($items as $item) {
            $total['subtotal'] += $item['priceHT'] * $item['qty'];
            $total['tva'] += $item['tva'];
            $total['total'] += $item['price'] * $item['qty'];
        }

        return $total;
    }

    private function calculatePriceHT(float $price): float
    {
        return round($price - $price * self::TVA, 2);
    }

    private function calculateTVA(float $price): float
    {
        return round($price * self::TVA, 2);
    }

    public function formatPrice(float $price): string
    {
        return number_format($price, 2, ',', ' ');
    }

    public function getTotalQuantity(): int
    {
        $cart = $this->getCart();
        return array_reduce($cart, fn ($sum, $item): int => $sum + $item['qty'], 0);
    }

    public function updateTotalQuantityInSession(): void
    {
        $cartQty = $this->getTotalQuantity();
        $this->session->set('cartQty', $cartQty);
    }

    public function updateQuantity($photoId, $quantity)
    {
        $cart = $this->session->get('cart', []);

        if (isset($cart[$photoId])) {
            if ($quantity > 0) {
                $cart[$photoId]['qty'] = $quantity;
            } else {
                unset($cart[$photoId]);
            }

            $this->session->set('cart', $cart);
        }
    }

    // vide le panier
    public function emptyCart(): void
    {
        $this->session->set('cart', []);
    }
}
