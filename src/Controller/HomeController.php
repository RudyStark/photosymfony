<?php

namespace App\Controller;

use App\Form\TagFormType;
use App\Repository\PhotoRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PhotoRepository $photoRepository): Response
    {
        $photos = $photoRepository->findAll();

        return $this->render('home/admin_base.html.twig', [
            'photos' => $photos,
        ]);
    }

    #[Route('/photo', name: 'app_photo')]
    public function photoList(): Response
    {
        return $this->render('photo/admin_base.html.twig', [
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

        // Créer le formulaire
        $form = $this->createForm(TagFormType::class);

        return $this->render('home/photo.html.twig', [
            'photo' => $photo,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/tags', name: 'app_tags', methods: ['GET', 'POST'])]
    public function tags(Request $request): Response
    {
        $form = $this->createForm(TagFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tags = $form->get('name')->getData();

            // Convertir la collection d'objets Tag en tableau
            $tagsArray = $tags->toArray();

            // On récupère les noms des tags pour les passer en paramètre
            $tagsString = implode(',', array_map(function ($tag) { return $tag->getName(); }, $tagsArray));

            return $this->redirectToRoute('app_photos_by_tags', [
                'tags' => $tagsString,
            ]);
        }

        return $this->render('home/photo.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/photos/{tags}', name: 'app_photos_by_tags')]
    public function photosByTags(string $tags, PhotoRepository $photoRepository): Response
    {
        $tags = explode(',', $tags);
        $photos = $photoRepository->findByTags($tags);

        $form = $this->createForm(TagFormType::class);

        return $this->render('home/admin_base.html.twig', [
            'photos' => $photos,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/search', name: 'app_search', methods: ['GET'])]
    public function search(Request $request, PhotoRepository $photoRepository): Response
    {
        $query = $request->query->get('q', '');
        $photos = $photoRepository->search($query);

        // On retourne un tableau de photos
        $photos = array_map(function ($photo) {
            return [
                'id' => $photo->getId(),
                'title' => $photo->getTitle(),
                'url' => $this->generateUrl('app_display_photo', ['slug' => $photo->getSlug()]),
                'thumbnailUrl' => $photo->getUrl(),
            ];
        }, $photos);

        return $this->json($photos);
    }

}
