<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Photo;
use App\Entity\StatutOrder;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\CartService;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/order')]
class OrderController extends AbstractController
{
    private $cartService;
    private $orderRepository;
    private $entityManager;

    public function __construct(CartService $cartService, OrderRepository $orderRepository, EntityManagerInterface $entityManager)
    {
        $this->cartService = $cartService;
        $this->orderRepository = $orderRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/validate-order', name: 'app_order_validate')]
    public function validateOrder(): Response
    {
        $customer = $this->getUser()->getCustomer();

        $order = new Order();
        $order->setCustomer($customer);

        $statutOrder = $this->entityManager->getRepository(StatutOrder::class)->findOneBy(['name' => 'En attente de paiement']);
        $order->setStatutOrder($statutOrder);

        $cartItems = $this->cartService->getCart();
        foreach ($cartItems as $item) {
            $photo = $this->entityManager->getRepository(Photo::class)->find($item['photoId']);
            if (!$photo) {
                // Gérer l'erreur si la photo n'est pas trouvée
                continue;
            }

            $orderItem = new OrderItem();
            $orderItem->setPhoto($photo);
            $orderItem->setQuantity($item['qty']);
            $orderItem->setPrice($item['price']);
            $order->addOrderItem($orderItem);
        }

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_order_confirmation');
    }

    #[Route('/confirmation', name: 'app_order_confirmation')]
    public function confirmation(): Response
    {
        $items = $this->cartService->getCart();
        $cartTotal = $this->cartService->getTotalCart();
        // récupération de l'id de la commande de l'utilisateur en cours
        $orderId = $this->orderRepository->findOneBy(['customer' => $this->getUser()->getCustomer()], ['createdAt' => 'DESC']);

        return $this->render('order/confirmation.html.twig', [
            'items' => $items,
            'subtotal' => $cartTotal['subtotal'],
            'tva' => $cartTotal['tva'],
            'total' => $cartTotal['total'],
            'orderId' => $orderId->getId()
        ]);
    }

    #[Route('/confirm-order', name: 'app_order_confirm', methods: ['POST'])]
    public function confirmOrder(Request $request): Response
    {
        $orderId = $request->request->get('orderId');
        $order = $this->entityManager->getRepository(Order::class)->find($orderId);

        if (!$order) {
            return $this->redirectToRoute('app_order_error');
        }

        $deliveryMode = $request->request->get('deliveryMode');
        $paymentMode = $request->request->get('paymentMode');

        $order->setDeliveryMode($deliveryMode);
        $order->setPaymentMode($paymentMode);

        $statutOrder = $this->entityManager->getRepository(StatutOrder::class)->findOneBy(['name' => 'En cours de traitement']);
        $order->setStatutOrder($statutOrder);

        $this->entityManager->flush();

        //on vide le panier
        $this->cartService->emptyCart();

        return $this->redirectToRoute('app_order_thankyou');
    }

    #[Route('/thank-you', name: 'app_order_thankyou')]
    public function thankyou(): Response
    {
        return $this->render('order/thankyou.html.twig');
    }

    #[Route('/error', name: 'app_order_error')]
    public function error(): Response
    {
        return $this->render('order/error.html.twig');
    }

    // Mes commandes
    #[isGranted('ROLE_CUSTOMER')]
    #[Route('/my-orders', name: 'app_my_orders')]
    public function myOrders(): Response
    {
        // affiche les orders de l'utilisateur connecté
        $orders = $this->orderRepository->findBy(['customer' => $this->getUser()->getCustomer()], ['createdAt' => 'DESC']);

        return $this->render('order/my_orders.html.twig', [
            'orders' => $orders
        ]);
    }

    // Détail d'une commande
    #[isGranted('ROLE_CUSTOMER')]
    #[Route('/order-details/{id}', name: 'app_order_details')]
    public function orderDetails($id): Response
    {
        $order = $this->orderRepository->find($id);

        if (!$order || $order->getCustomer() !== $this->getUser()->getCustomer()) {
            return $this->redirectToRoute('app_my_orders');
        }

        return $this->render('order/order_details.html.twig', [
            'order' => $order
        ]);
    }
}
