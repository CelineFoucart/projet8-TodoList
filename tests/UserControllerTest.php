<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    use FixtureTrait;

    public function testIndexAsAnonymous(): void
    {
        $client = static::createClient();
        $client->request('GET', '/users');
        $this->assertResponseRedirects('/login');
    }

    public function testIndexWithAccessDenied(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $this->loginUser($client, 'johndoe@gmail.com');
        $client->request('GET', '/users');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testIndexAsAdmin(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $this->loginUser($client, 'admin@gmail.com');
        $client->request('GET', '/users');
        $this->assertSelectorTextContains('h2', 'Liste des utilisateurs');
    }
}
