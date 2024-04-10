<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Repository\PhotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PhotoRepository $photoRepository): Response
    {
        $photos = $photoRepository->findAll();

        return $this->render('home/index.html.twig', [
            'photos' => $photos,
        ]);
    }

    #[Route('/photo', name: 'app_photo')]
    public function photoList(): Response
    {
        return $this->render('photo/index.html.twig', [
        ]);
    }

    //display photo
    #[Route('/photo/{slug}', name: 'app_display_photo')]
    public function displayPhoto(string $slug, PhotoRepository $photoRepository): Response
    {
        $photo = $photoRepository->findOneBySlug($slug);

        if (!$photo) {
            throw $this->createNotFoundException('La photo demandée n\'existe pas.');
        }

        return $this->render('home/photo.html.twig', [
            'photo' => $photo,
        ]);
    }
}
