<?php

declare(strict_types=1);

namespace App\Test;

use App\Tests\FixtureTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    use FixtureTrait;

    public function testHomePageAsAnonymous(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('#login-btn', 'Se connecter');
        $this->assertSelectorTextContains('#register-btn', "S'inscrire");
    }

    public function testHomePageAsLoggedIn(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $this->loginUser($client, 'johndoe@gmail.com');
        
        $client->request('GET', '/');
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('.btn-success', 'Créer une nouvelle tâche');
        $this->assertSelectorTextContains('.btn-info', 'Consulter la liste des tâches à faire');
    }
}
