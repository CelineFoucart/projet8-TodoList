<?php

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
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

    public function testEditAsAdmin(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $this->loginUser($client, 'admin@gmail.com');
        $user = $this->getUser('johndoe@gmail.com');

        $client->request('GET', '/users/' . $user->getId() . '/edit');
        $this->assertSelectorTextContains('h2', 'Modifier le compte '.$user->getUsername());
    }

    protected function getUser(string $email): User
    {
        /** @var UserRepository */
        $repository = static::getContainer()->get(UserRepository::class);
        $task = $repository->findOneBy(['email' => $email]);

        if (null === $task) {
            throw new \Exception('No user named');
        }

        return $task;
    }
}
