<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/order')]
#[IsGranted('ROLE_CUSTOMER')]
class OrderController extends AbstractController
{
    #[Route('/details', name: 'app_order_details')]
    public function index(): Response
    {
        return $this->render('order/index.html.twig', [
        ]);
    }
}
