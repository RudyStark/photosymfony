<?php


namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    public function testDisplayLoginPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertPageTitleContains('Log in!');
        $this->assertSelectorTextContains('h1', 'Please sign in');
    }

    public function testLoginWithValidCredentials()
    {
        $client = static::createClient();
        $client->request('GET', '/login', [], [], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Please sign in');
    }

    public function testLoginWithInvalidCredentials()
    {
        $client = static::createClient();
        $client->request('GET', '/login', [], [], [
            'PHP_AUTH_USER' => 'invalid_username',
            'PHP_AUTH_PW'   => 'invalid_password',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Please sign in');
    }

    public function testLogout()
    {
        $client = static::createClient();
        $client->request('GET', '/logout');

        $this->assertResponseRedirects('/login');
    }
}
