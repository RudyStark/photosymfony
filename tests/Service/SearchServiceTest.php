<?php

namespace App\Tests\Service;

use App\Service\SearchService;
use App\Repository\PhotoRepository;
use App\Repository\TagRepository;
use PHPUnit\Framework\TestCase;

class SearchServiceTest extends TestCase
{
    private $photoRepository;
    private $tagRepository;
    private $searchService;

    protected function setUp(): void
    {
        // Création des mocks pour PhotoRepository et TagRepository
        $this->photoRepository = $this->createMock(PhotoRepository::class);
        $this->tagRepository = $this->createMock(TagRepository::class);

        // Instanciation du service à tester avec les repositories mockés
        $this->searchService = new SearchService($this->photoRepository, $this->tagRepository);
    }

    public function testSearchByQuery()
    {
        // Définition du résultat attendu
        $expectedResult = ['photo1', 'photo2', 'photo3'];

        // Configuration du mock pour retourner le résultat attendu
        $this->photoRepository->expects($this->once())
            ->method('findByTitle')
            ->with($this->equalTo('query'))
            ->willReturn($expectedResult);

        // Appel de la méthode à tester
        $result = $this->searchService->searchByQuery('query');

        // Vérification que le résultat correspond au résultat attendu
        $this->assertEquals($expectedResult, $result);
    }

    public function testSearchByQueryEmpty()
    {
        $expectedResult = ['photo1', 'photo2', 'photo3'];

        $this->photoRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($expectedResult);

        $result = $this->searchService->searchByQuery('');

        $this->assertEquals($expectedResult, $result);
    }
}
