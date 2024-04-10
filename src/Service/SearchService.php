<?php

namespace App\Service;

use App\Repository\PhotoRepository;
use App\Repository\TagRepository;

class SearchService
{
    private PhotoRepository $photoRepository;
    private TagRepository $tagRepository;

    public function __construct(PhotoRepository $photoRepository, TagRepository $tagRepository)
    {
        $this->photoRepository = $photoRepository;
        $this->tagRepository = $tagRepository;
    }

    public function searchByQuery(string $query): array
    {
        // Si le terme de recherche est vide, on retourne toutes les photos
        if (empty($query)) {
            return $this->photoRepository->findAll();
        }

        // Recherche par titre
        $photosByTitle = $this->photoRepository->findByTitle($query);

        // Par tags
        $tag = $this->tagRepository->findByName($query);
        if ($tag) {
            $photosByTag = $tag->getPhotos()->toArray();
        } else {
            $photosByTag = [];
        }

        // Fusion des résultats
        $allPhotos = array_merge($photosByTitle, $photosByTag);
        $allPhotos = array_unique($allPhotos, SORT_REGULAR);

        return $allPhotos;
    }
}
