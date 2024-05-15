<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Form\TagFormType;
use App\Repository\PhotoRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PhotoRepository $photoRepository, TagRepository $tagRepository): Response
    {
        $photos = $photoRepository->findAll();
        $form = $this->createForm(TagFormType::class);

        return $this->render('home/index.html.twig', [
            'photos' => $photos,
            'form' => $form->createView(),
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

    #[Route('/tags', name: 'app_tags', methods: ['GET', 'POST'])]
    public function tags(Request $request, TagRepository $tagRepository): Response
    {
        $form = $this->createForm(TagFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tags = $form->get('name')->getData();

            // Convert ArrayCollection to array
            $tagsArray = $tags->toArray();

            // Convert array to string
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

        // Create an instance of the form
        $form = $this->createForm(TagFormType::class);

        return $this->render('home/index.html.twig', [
            'photos' => $photos,
            'form' => $form->createView(), // Pass the form to the template
        ]);
    }

}
