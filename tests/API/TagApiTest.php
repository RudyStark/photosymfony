<?php

namespace App\Tests\API;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TagApiTest extends WebTestCase
{
    public function testCreateTag(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/tags',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json'],
            json_encode(['name' => 'Test Tag'])
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testGetTags(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/tags');
        $this->assertResponseIsSuccessful();
    }

    public function testUpdateTag(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/tags',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json'],
            json_encode(['name' => 'Test Tag'])
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $data = json_decode($client->getResponse()->getContent(), true);

        // Debugging output
        echo $client->getResponse()->getContent();

        $client->request(
            'PUT',
            '/api/tags/' . $data['id'],
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json'],
            json_encode(['name' => 'Updated Tag'])
        );
        $this->assertResponseIsSuccessful();
    }


    public function testDeleteTag(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/tags',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json'],
            json_encode(['name' => 'Test Tag'])
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $data = json_decode($client->getResponse()->getContent(), true);

        // Debugging output
        echo $client->getResponse()->getContent();

        $client->request('DELETE', '/api/tags/' . $data['id']);
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

}

