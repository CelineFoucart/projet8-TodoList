<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    use FixtureTrait;

    public function testSuccessfullLogin(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            'username' => 'John Doe',
            'password' => 'password123'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/');
    }

    public function testLoginWithBadCredentials(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            'username' => 'John Doe',
            'password' => 'fakepassword'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testAccessAsLoggedUser(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $this->loginUser($client, 'johndoe@gmail.com');
        
        $client->request('GET', '/login');
        $this->assertResponseRedirects('/');
    }

    public function testLogout(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $this->loginUser($client, 'johndoe@gmail.com');
        
        $client->request('GET', '/logout');
        $this->assertResponseRedirects('/');
    }
}
