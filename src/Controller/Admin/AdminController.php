<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\Photo;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_admin_index')]
    public function index(): Response
    {
        // Fetch data
        $userCount = $this->entityManager->getRepository(User::class)->count([]);
        $photoCount = $this->entityManager->getRepository(Photo::class)->count([]);
        $orderCount = $this->entityManager->getRepository(Order::class)->count([]);
        // Assuming you have a method to calculate statistics
        $statistics = $this->calculateStatistics();

        return $this->render('admin/dashboard.html.twig', [
            'count_users' => $userCount,
            'count_photos' => $photoCount,
            'count_orders' => $orderCount,
            'statistics' => $statistics,
        ]);
    }

    private function calculateStatistics()
    {
        // Implement your logic to calculate statistics here
        // This is just a placeholder
        return [
            'total_sales' => 1000,
            'new_users_this_month' => 50,
            // Add more as needed
        ];
    }
}
