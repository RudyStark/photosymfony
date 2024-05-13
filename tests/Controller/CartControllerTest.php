<?php

namespace App\Tests\Controller;

use App\Controller\CartController;
use App\Entity\Photo;
use App\Repository\PhotoRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class CartControllerTest extends WebTestCase
{
    private $session;
    private $photoRepository;

    protected function setUp(): void
    {
        $this->session = new Session(new MockArraySessionStorage());
        $this->photoRepository = $this->createMock(PhotoRepository::class);
        // Set up your controller here if necessary
    }

    public function testIndexWithEmptyCart()
    {
        $client = static::createClient();
        $client->getContainer()->set('session', $this->session);

        $crawler = $client->request('GET', '/cart');
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Votre panier est vide !', $client->getResponse()->getContent());
    }

    public function testCalculTotalCart()
    {
        $cartController = new CartController();
        $items = [
            ['priceHT' => 10, 'tva' => 2, 'price' => 12, 'qty' => 2],
            ['priceHT' => 20, 'tva' => 4, 'price' => 24, 'qty' => 1]
        ];
        $result = $cartController->calculTotalCart($items);
        $this->assertEquals(40, $result['subtotal']);
        $this->assertEquals(6, $result['tva']);
        $this->assertEquals(48, $result['total']);
    }

    public function testFormatPrice()
    {
        $cartController = new CartController();
        $this->assertEquals('1 000,00', $cartController->formatPrice(1000));
    }

    public function testAddItemToCart()
    {
        $client = static::createClient();
        $client->getContainer()->set('session', $this->session);

        $photo = new Photo();
        $photo->setTitle('Test Photo');
        $photo->setPrice(100);
        $photo->setUrl('http://example.com/photo.jpg');

        $this->photoRepository->method('find')->willReturn($photo);

        $client->getContainer()->set(PhotoRepository::class, $this->photoRepository);

        $client->request('POST', '/cart/add', ['id' => 1, 'qty' => 1]);

        $this->assertResponseStatusCodeSame(302); // Expecting redirection
        $this->assertTrue($client->getResponse()->isRedirect('/cart')); // Ensuring it redirects to the cart page
    }

    public function testChangeQuantity()
    {
        $client = static::createClient();
        $client->getContainer()->set('session', $this->session);

        $photo = new Photo();
        $photo->setTitle('Test Photo');
        $photo->setPrice(100);
        $photo->setUrl('http://example.com/photo.jpg');

        $this->photoRepository->method('find')->willReturn($photo);

        $client->getContainer()->set(PhotoRepository::class, $this->photoRepository);

        $client->request('POST', '/cart/add', ['id' => 1, 'qty' => 1]);
        $this->assertResponseStatusCodeSame(302); // Expecting redirection
        $this->assertTrue($client->getResponse()->isRedirect('/cart')); // Ensuring it redirects to the cart page

        $client->request('POST', '/cart/update/1', ['qty' => 2]);
        $this->assertResponseStatusCodeSame(302); // Expecting redirection
        $this->assertTrue($client->getResponse()->isRedirect('/cart')); // Ensuring it redirects to the cart page
    }
}

