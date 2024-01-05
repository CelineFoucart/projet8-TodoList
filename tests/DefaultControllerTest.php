<?php

declare(strict_types=1);

namespace App\Test;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\FixtureTrait;

class DefaultControllerTest extends WebTestCase
{
    use FixtureTrait;

    public function testHomePageAsAnonymous(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('#login-btn', "Se connecter");
        $this->assertSelectorTextContains('#register-btn', "S'inscrire");
    }
}
