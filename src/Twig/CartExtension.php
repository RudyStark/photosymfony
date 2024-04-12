<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\Component\HttpFoundation\RequestStack;

class CartExtension extends AbstractExtension
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('total_cart_items', [$this, 'getTotalCartItems']),
        ];
    }

    public function getTotalCartItems(): int
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);
        $totalItems = 0;
        foreach ($cart as $id => $details) {
            $totalItems += $details['quantity'];
        }

        return $totalItems;
    }
}

