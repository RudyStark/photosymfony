<?php

namespace App\Controller;

use App\Entity\Photo;
use Symfony\UX\Turbo\TurboBundle;
use App\Repository\PhotoRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    /**
     * Utilisation temporaire d'une valeur de TVA - A rajouter dans la base avec les produits!
     */
    private const TVA = 0.2;

    #[Route('/cart', name: 'app_cart')]
    public function index(SessionInterface $session)
    {
        $items = $session->get('cart', []);

        $cartTotal = $this->calculTotalCart($items);

        return $this->render('cart/index.html.twig', [
            'subtotal' => $cartTotal['subtotal'],
            'tva' => $cartTotal['tva'],
            'total' => $cartTotal['total'],
            'items' => $items
        ]);
    }

    /** Retourne le total panier */
    public function calculTotalCart(array $items)
    {
        $cartTotal = ['subtotal' => 0, 'tva' => 0, 'total' => 0, 'qty' => 0];
        // Calcul total Price
        foreach ($items as $item) {

            $cartTotal['subtotal']  += $item['priceHT'] * $item['qty'];
            $cartTotal['tva']       += $item['tva'];
            $cartTotal['total']     += $item['price'] * $item['qty'];
        }
        return $cartTotal;
    }

    public function formatPrice(int $price)
    {
        return number_format($price, 2, ',', ' ');
    }

    #[Route('/cart/add', name: 'app_cart_add', methods: ['POST'])]
    public function add(SessionInterface $session, PhotoRepository $repository, Request $request)
    {
        $id = $request->request->get('id', null);
        if ($id == null)
            return $this->redirectToRoute('app_home'); //nothing to do here

        $photo = $repository->find($id);
        $cart = $session->get('cart', []);

        /*  $cart = [];
         $session->set('cart', $cart);
         exit(); */

        if (isset($cart[$id]) && isset($cart[$id]['qty']))
            $cart[$id]['qty'] += $request->request->get('qty', 0);
        else {
            $cart[$id]['url'] = $photo->getUrl();
            $cart[$id]['qty'] = $request->request->get('qty', 0);
            $cart[$id]['title'] = $photo->getTitle();
            $cart[$id]['slug'] = $photo->getSlug();
            $cart[$id]['price'] = $photo->getPrice();
            $cart[$id]['priceHT'] = round($photo->getPrice() - $photo->getPrice() * self::TVA,2);
            $cart[$id]['tva'] = round($photo->getPrice()*self::TVA,2);
        }
        //$cart['qty'] += $request->request->get('qty', 0);

        $session->set('cart', $cart);


        // Total QTY in cart
        $cartQty = array_reduce($cart, fn ($sum, $item): int => $sum + $item['qty'], 0);

        // Put this value on session for displaying on reload of page
        $session->set('cartQty', $cartQty);

        //dd(TurboBundle::STREAM_FORMAT, $request->getPreferredFormat());
        if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
            // If the request comes from Turbo, set the content type as text/vnd.turbo-stream.html and only send the HTML to update

            return $this->render(
                'cart/_cartNav.html.twig',
                [
                    'cartQty' => $cartQty,
                    'photo' => $photo
                ],
                new Response('',
                    200,
                    ['content-type' => TurboBundle::STREAM_MEDIA_TYPE]
                )
            );
        }

        // si pas de TURBO (js desactive) : redirection vers la panier !
        return $this->redirectToRoute('app_cart');
    }


    #[Route('/cart/update/{id}', name: 'app_cart_update')]
    public function changeQuantity($id=null, SessionInterface $session, Request $request)
    {
        $cart = $session->get('cart', []);
        $qty = $request->request->get('qty', null);
        if ($id !== null && isset($cart[$id]) && $qty !== null) {
            if($qty == 0)
                unset($cart[$id]);
            else
                $cart[$id]['qty'] = $qty;
        }

        $session->set('cart', $cart);

        // Total QTY in cart
        $cartQty = array_reduce($cart, fn ($sum, $item): int => $sum + $item['qty'], 0);

        // Put this value on session for displaying on reload of page
        $session->set('cartQty', $cartQty);

        return $this->redirectToRoute('app_cart');
    }
}
