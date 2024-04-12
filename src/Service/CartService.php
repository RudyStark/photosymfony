<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private const CART_KEY = 'cart';
    private $session;

    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    public function addPhoto(int $photoId, int $quantity = 1): void
    {
        $cart = $this->getCart();
        if (isset($cart[$photoId])) {
            // Si la photo est déjà dans le panier, on incrémente la quantité
            $cart[$photoId]['quantity'] += $quantity;
        } else {
            // Sinon, on ajoute la photo au panier
            $cart[$photoId] = ['quantity' => $quantity];
        }
        $this->session->set(self::CART_KEY, $cart);
    }

    public function removePhoto(int $photoId): void
    {
        $cart = $this->getCart();
        if (isset($cart[$photoId])) {
            // On retire la photo du panier
            unset($cart[$photoId]);
        }
        $this->session->set(self::CART_KEY, $cart);
    }

    public function updateQuantity(int $photoId, int $quantity): void
    {
        $cart = $this->getCart();
        if (isset($cart[$photoId]) && $quantity > 0) {
            // Met à jour la quantité de la photo dans le panier
            $cart[$photoId]['quantity'] = $quantity;
        } else {
            // Si la quantité est 0 ou la photo n'est pas dans le panier, on retire la photo du panier
            unset($cart[$photoId]);
        }
        $this->session->set(self::CART_KEY, $cart);
    }

    public function getCart(): array
    {
        // Récupère le panier de la session, retourne un tableau vide si aucun panier n'est trouvé
        return $this->session->get(self::CART_KEY, []);
    }

    public function clearCart(): void
    {
        // Vide le panier
        $this->session->set(self::CART_KEY, []);
    }
}
