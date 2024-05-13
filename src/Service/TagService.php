<?php

namespace App\Service;

use App\Repository\TagRepository;

class TagService
{
    private $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function getMostUsedTags(int $limit = 4): array
    {
        return $this->tagRepository->findMostUsedTags($limit);
    }
}
