<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Repository\PhotoRepository;
use App\Service\SearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;

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

    //Search
    #[Route('/search', name: 'app_search')]
    public function search(Request $request, SearchService $searchService, SessionInterface $session): Response
    {
        $query = $request->query->get('query', '');

        $photos = $searchService->searchByQuery($query);

        if (empty($query)) {
            $session->getFlashBag()->add('info', 'Votre recherche est vide, voici toutes les photos.');
        }

        return $this->render('photo/search.html.twig', [
            'photos' => $photos,
        ]);
    }

    /**
     * Recherche des photos par tag
     * @param string $tagName
     * @param SearchService $searchService
     * @return Response
     */
    #[Route('/search/tag/{tagName}', name: 'app_search_tag')]
    public function searchByTag(string $tagName, SearchService $searchService): Response
    {
        $photos = $searchService->searchByQuery($tagName);

        return $this->render('photo/search.html.twig', [
            'photos' => $photos,
        ]);
    }

}
