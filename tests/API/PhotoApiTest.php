<?php

namespace App\Tests\API;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

// command line for running the test
// ./bin/phpunit tests/API/PhotoApiTest.php
class PhotoApiTest extends WebTestCase
{
    public function testCreatePhoto(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/photos',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json'],
            json_encode([
                'title' => 'Test Photo',
                'description' => 'Test Description',
                'url' => 'https://example.com/test-photo.jpg',
                'price' => 9.99,
                'metaInfo' => ['key' => 'value'],
                'tags' => ['/api/tags/1']
            ])
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testGetPhotos(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/photos');
        $this->assertResponseIsSuccessful();
    }

    public function testUpdatePhoto(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/photos',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json'],
            json_encode([
                'title' => 'Test Photo',
                'description' => 'Test Description',
                'url' => 'https://example.com/test-photo.jpg',
                'price' => 9.99,
                'metaInfo' => ['key' => 'value']
            ])
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        echo $client->getResponse()->getContent(); // Afficher la réponse du POST
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data); // Vérifier que l'identifiant est présent

        $client->request(
            'PUT',
            '/api/photos/' . $data['id'],
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json'],
            json_encode([
                'title' => 'Updated Photo',
                'description' => 'Updated Description',
                'url' => 'https://example.com/updated-photo.jpg',
                'price' => 19.99,
                'metaInfo' => ['key' => 'updated-value']
            ])
        );
        echo $client->getResponse()->getContent(); // Afficher la réponse du PUT
        $this->assertResponseIsSuccessful(); // Vérifier que la mise à jour est réussie
    }

    public function testDeletePhoto(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/photos',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json'],
            json_encode([
                'title' => 'Test Photo',
                'description' => 'Test Description',
                'url' => 'https://example.com/test-photo.jpg',
                'price' => 9.99,
                'metaInfo' => ['key' => 'value']
            ])
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $data = json_decode($client->getResponse()->getContent(), true);

        $client->request('DELETE', '/api/photos/' . $data['id']);
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}
