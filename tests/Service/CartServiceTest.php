<?php

namespace App\Tests\Service;

use App\Service\CartService;
use App\Repository\PhotoRepository;
use App\Entity\Photo;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class CartServiceTest extends KernelTestCase
{
    private $cartService;

    protected function setUp(): void
    {
        $photoRepository = $this->createMock(PhotoRepository::class);
        $photoRepository->method('find')->willReturn($this->getPhotoMock());

        $session = new Session(new MockArraySessionStorage());

        // Mock the RequestStack and its getSession method
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getSession')->willReturn($session);

        $this->cartService = new CartService($requestStack, $photoRepository);
    }

    public function testAddPhotoToCart()
    {
        $result = $this->cartService->addPhotoToCart(1, 2);

        $this->assertArrayHasKey('photo', $result);
        $this->assertArrayHasKey('cartQty', $result);
        $this->assertEquals(2, $result['cartQty']);
    }

    public function testGetCart()
    {
        $cart = $this->cartService->getCart();

        $this->assertIsArray($cart);
        $this->assertEmpty($cart);
    }

    /**
     * Test le total du panier.
     * @return void
     */
    public function testGetTotalCart()
    {
        $result = $this->cartService->addPhotoToCart(1, 2);
        $cartTotal = $this->cartService->getTotalCart();

        $this->assertArrayHasKey('subtotal', $cartTotal);
        $this->assertArrayHasKey('tva', $cartTotal);
        $this->assertArrayHasKey('total', $cartTotal);
        $this->assertEquals(16.0, $cartTotal['subtotal']);
        $this->assertEquals(2.0, $cartTotal['tva']);
        $this->assertEquals(20.0, $cartTotal['total']);
    }

    /**
     * Mock l'entité Photo.
     */
    private function getPhotoMock()
    {
        $photo = $this->createMock(Photo::class);
        $photo->method('getPrice')->willReturn(10.0);
        $photo->method('getTitle')->willReturn('Test Photo');
        $photo->method('getUrl')->willReturn('test_url');
        $photo->method('getSlug')->willReturn('test_slug');

        return $photo;
    }
}
