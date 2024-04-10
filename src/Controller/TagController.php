<?php

namespace App\Controller;

use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends AbstractController
{
    #[Route('/tag/{slug}', name: 'tag_show')]
    public function show(string $slug, TagRepository $tagRepository): Response
    {
        $tag = $tagRepository->findOneBy(['slug' => $slug]);

        // Si le tag n'existe pas, on lève une exception
        if (!$tag) {
            throw $this->createNotFoundException('Le tag demandé n\'existe pas.');
        }

        return $this->render('tag/show.html.twig', [
            'tag' => $tag,
            'photos' => $tag->getPhotos(),
        ]);
    }
}
