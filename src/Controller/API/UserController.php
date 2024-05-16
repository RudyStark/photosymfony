<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController {
    private $security;
    private $serializer;

    public function __construct(Security $security, SerializerInterface $serializer) {
        $this->security = $security;
        $this->serializer = $serializer;
    }

    #[Route('/api/user/profile', name: 'api_user_profile')]
    public function profile(): Response {
        $user = $this->security->getUser();

        if (!$user) {
            return $this->json(['message' => 'Not Authenticated'], Response::HTTP_FORBIDDEN);
        }

        $userData = $this->serializer->serialize($user, 'json', ['groups' => 'user:read']);

        return new Response($userData, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}
