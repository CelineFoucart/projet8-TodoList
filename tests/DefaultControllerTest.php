<?php

declare(strict_types=1);

namespace App\Test;

use App\Entity\User;
use App\Tests\FixtureTrait;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
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
        $this->assertSelectorTextContains('#login-btn', "Se connecter");
        $this->assertSelectorTextContains('#register-btn', "S'inscrire");
    }

    public function testHomePageAsLoggedIn(): void
    {
        // Given
        $client = static::createClient();
        $this->makeFixture();
        $this->loginUser($client, 'johndoe@gmail.com');
        
        // When
        $client->request('GET', '/');

        // Then
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('.btn-success', "Créer une nouvelle tâche");
        $this->assertSelectorTextContains('.btn-info', "Consulter la liste des tâches à faire");
    }

    private function loginUser(KernelBrowser $client, string $email): User
    {
        /** @var UserRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => $email]);
        $client->loginUser($user);

        return $user;
    }
}
