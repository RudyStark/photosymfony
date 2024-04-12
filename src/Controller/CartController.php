<?php

namespace App\Controller;

use App\Repository\PhotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart')]
class CartController extends AbstractController
{
    #[Route('/add/{id}', name: 'cart_add', methods: ['POST'])]
    public function add($id, Request $request, SessionInterface $session): Response
    {
        // Récupération du panier actuel depuis la session, ou initialisation à un tableau vide si non existant
        $cart = $session->get('cart', []);

        // Vérification si l'ID de la photo existe déjà dans le panier
        if (!empty($cart[$id])) {
            // Incrémentation de la quantité pour cet ID de photo
            $cart[$id]['quantity']++;
        } else {
            // Ajout de la photo avec une quantité initiale de 1 si elle n'est pas déjà dans le panier
            $cart[$id] = ['quantity' => 1];
        }

        // Mise à jour de la session avec le nouveau panier
        $session->set('cart', $cart);

        // Vérification si la requête est une requête AJAX
        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'total' => array_sum(array_map(function ($item) {
                    return $item['quantity'];
                }, $cart))
            ]);
        }

        return $this->redirectToRoute('cart_show');
    }

    #[Route('/', name: 'cart_show', methods: ['GET'])]
    public function show(SessionInterface $session, PhotoRepository $photoRepository): Response
    {
        $cart = $session->get('cart', []);
        $cartWithData = [];

        foreach ($cart as $id => $details) {
            $photo = $photoRepository->find($id);
            if ($photo) {
                $cartWithData[] = [
                    'photo' => $photo,
                    // Assurez-vous que 'quantity' est défini comme un entier ou un flottant ici
                    'quantity' => $details['quantity'] // Modifiez selon la structure réelle de votre tableau
                ];
            }
        }

        // Total du panier
        $total = 0;
        foreach ($cartWithData as $item) {
            $total += $item['photo']->getPrice() * $item['quantity'];
        }

        return $this->render('cart/show.html.twig', [
            'items' => $cartWithData,
            'total' => $total,
        ]);
    }

    #[Route('/increase/{id}', name: 'cart_increase', methods: ['GET'])]
    public function increase($id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        if (isset($cart[$id])) {
            // Incrémentez spécifiquement la quantité de l'article.
            $cart[$id]['quantity']++;
        } else {
            // Si l'article n'est pas encore dans le panier, on ajoute l'article avec une quantité de 1.
            $cart[$id] = ['quantity' => 1];
        }
        $session->set('cart', $cart);

        return $this->redirectToRoute('cart_show');
    }

    #[Route('/decrease/{id}', name: 'cart_decrease', methods: ['GET'])]
    public function decrease($id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        if (isset($cart[$id]) && $cart[$id]['quantity'] > 1) {
            // Décrémentez la quantité de l'article.
            $cart[$id]['quantity']--;
        } else {
            // On supprime l'article du panier si la quantité est inférieure ou égale à 1.
            unset($cart[$id]);
        }
        $session->set('cart', $cart);

        return $this->redirectToRoute('cart_show');
    }


    #[Route('/remove/{id}', name: 'cart_remove', methods: ['GET'])]
    public function remove($id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
        }
        $session->set('cart', $cart);

        return $this->redirectToRoute('cart_show');
    }

    #[Route('/clear', name: 'cart_clear', methods: ['GET'])]
    public function clear(SessionInterface $session): Response
    {
        $session->remove('cart');

        return $this->redirectToRoute('cart_show');
    }

}
