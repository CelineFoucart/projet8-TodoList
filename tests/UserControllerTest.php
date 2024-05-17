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

    public function testCreateAsAnonymous(): void
    {
        $client = static::createClient();
        $client->request('GET', '/users/create');
        $this->assertResponseRedirects('/login');
    }

    public function testCreateWithAccessDenied(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $this->loginUser($client, 'johndoe@gmail.com');
        $client->request('GET', '/users/create');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testCreateAsAdmin(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $this->loginUser($client, 'admin@gmail.com');
        $client->request('GET', '/users/create');
        $this->assertSelectorTextContains('h2', 'Créer un utilisateur');
    }

    public function testCreateAsAdminWithValidData(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $this->loginUser($client, 'admin@gmail.com');
        $client->request('GET', '/users/create');
        $client->submitForm('Ajouter', [
            'user[username]' => 'UserTest',
            'user[email]' => 'test@domaine.fr',
            'user[plainPassword][first]' => 'password123',
            'user[plainPassword][second]' => 'password123',
            'user[roles]' => ['ROLE_ADMIN'],
            'user[isVerified]' => true
        ]);

        $repository = static::getContainer()->get(UserRepository::class);
        $user = $repository->findOneBy(['username' => 'UserTest']);

        $this->assertNotNull($user);
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

    public function testEditAsAdminWithValidData(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $this->loginUser($client, 'admin@gmail.com');

        $user = $this->getUser('johndoe@gmail.com');
        $client->request('GET', '/users/' . $user->getId() . '/edit');

        $client->submitForm('Modifier', [
            'user[username]' => 'John Doe Edited',
            'user[email]' => 'johndoe@gmail.com',
            'user[plainPassword][first]' => 'password123',
            'user[plainPassword][second]' => 'password123',
            'user[roles]' => ['ROLE_USER'],
            'user[isVerified]' => true
        ]);

        $repository = static::getContainer()->get(UserRepository::class);
        /** @var User */
        $afterEdition = $repository->find($user->getId());
        $this->assertEquals('John Doe Edited', $afterEdition->getUsername());
        $this->assertEquals('johndoe@gmail.com', $afterEdition->getEmail());
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
