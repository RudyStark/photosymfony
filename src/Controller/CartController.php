<?php

namespace App\Controller;

use Symfony\UX\Turbo\TurboBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\CartService;

class CartController extends AbstractController
{
    private $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    #[Route('/cart', name: 'app_cart')]
    public function index()
    {
        // Mise à jour de la quantité totale dans la session
        $this->cartService->updateTotalQuantityInSession();

        $items = $this->cartService->getCart();
        $cartTotal = $this->cartService->getTotalCart();

        return $this->render('cart/index.html.twig', [
            'subtotal' => $cartTotal['subtotal'],
            'tva' => $cartTotal['tva'],
            'total' => $cartTotal['total'],
            'items' => $items
        ]);
    }

    #[Route('/cart/add', name: 'app_cart_add', methods: ['POST'])]
    public function add(Request $request)
    {
        $id = $request->request->get('id', null);
        $qty = $request->request->get('qty', 0);

        $result = $this->cartService->addPhotoToCart($id, $qty);
        $photo = $result['photo'];
        $cartQty = $result['cartQty'];

        if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
            // If the request comes from Turbo, set the content type as text/vnd.turbo-stream.html and only send the HTML to update

            return $this->render(
                'cart/_cartNav.html.twig',
                ['items' => $this->cartService->getCart(), 'photo' => $photo, 'cartQty' => $cartQty],
                new Response('',
                    200,
                    ['content-type' => TurboBundle::STREAM_MEDIA_TYPE]
                )
            );
        }

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/update/{id}', name: 'app_cart_update')]
    public function changeQuantity($id, Request $request)
    {
        $this->cartService->updateQuantity($id, $request->request->get('qty'));

        // Mise à jour de la quantité totale dans la session
        $this->cartService->updateTotalQuantityInSession();

        return $this->redirectToRoute('app_cart');
    }
}
