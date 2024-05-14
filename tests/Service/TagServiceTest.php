<?php

namespace App\Tests\Service;

use App\Service\TagService;
use App\Repository\TagRepository;
use PHPUnit\Framework\TestCase;

class TagServiceTest extends TestCase
{
    private $tagRepository;
    private $tagService;

    protected function setUp(): void
    {
        // Mock le TagRepository
        $this->tagRepository = $this->createMock(TagRepository::class);

        // Instancie le TagService en lui passant le TagRepository mocké
        $this->tagService = new TagService($this->tagRepository);
    }

    public function testGetMostUsedTags()
    {
        // Définit le résultat attendu
        $expectedResult = ['tag1', 'tag2', 'tag3', 'tag4'];

        // Configure le mock pour qu'il retourne le résultat attendu
        $this->tagRepository->expects($this->once())
            ->method('findMostUsedTags')
            ->with($this->equalTo(4))
            ->willReturn($expectedResult);

        // Appelle la méthode à tester
        $result = $this->tagService->getMostUsedTags(4);

        // Vérifie que le résultat retourné est celui attendu
        $this->assertEquals($expectedResult, $result);
    }
}
