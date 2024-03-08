<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileControllerTest extends WebTestCase
{
    use FixtureTrait;

    public function testAnonymous(): void
    {
        $client = static::createClient();
        $client->request('GET', '/profile');
        $this->assertResponseRedirects('/login');
    }

    public function testAccessAsLoggedUser(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $user = $this->loginUser($client, 'johndoe@gmail.com');
        
        $crawler = $client->request('GET', '/profile');
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Profil');
        $username = $crawler->filter('#profile_username')->extract(['value']);
        $this->assertEquals($user->getUsername(), $username[0]);
    }

    public function testSubmitValidEmail(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $user = $this->loginUser($client, 'johndoe@gmail.com');

        $client->request('GET', '/profile');
        $client->submitForm('Enregistrer', [
            'profile[username]' => $user->getUsername(),
            'profile[email]' => 'johndoeedited@gmail.com',
        ]);
        $client->followRedirect();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'johndoeedited@gmail.com']);
        
        $this->assertNotNull($user);
    }

    public function testInvalidEmail(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $user = $this->loginUser($client, 'johndoe@gmail.com');
        $client->request('GET', '/profile');
        $client->submitForm('Enregistrer', [
            'profile[username]' => $user->getUsername(),
            'profile[email]' => 'johndoeedite',
        ]);
        $this->assertSelectorExists('.invalid-feedback');
    }

    public function testSubmitUsernameNotChanged(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $this->loginUser($client, 'johndoe@gmail.com');

        $client->request('GET', '/profile');
        $client->submitForm('Enregistrer', [
            'profile[username]' => 'JohnDoe2',
        ]);
        $client->followRedirect();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'johndoe@gmail.com']);
        
        $this->assertNotEquals($user->getUsername(), 'JohnDoe2');
    }

    public function testSubmitPassword(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $this->loginUser($client, 'johndoe@gmail.com');

        $client->request('GET', '/profile');
        $client->submitForm('Enregistrer', [
            'profile[plainPassword][first]' => 'passwordedited123',
            'profile[plainPassword][second]' => 'passwordedited123',
        ]);
        $client->followRedirect();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'johndoe@gmail.com']);

        /** @var UserPasswordHasherInterface */
        $paswordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);
        $this->assertTrue($paswordHasher->isPasswordValid($user, 'passwordedited123'));
    }

    public function testSubmitInvalidPassword(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $this->loginUser($client, 'johndoe@gmail.com');

        $client->request('GET', '/profile');
        $client->submitForm('Enregistrer', [
            'profile[plainPassword][first]' => 'passwordedited123',
            'profile[plainPassword][second]' => 'passworded',
        ]);
        $this->assertSelectorExists('.invalid-feedback');
    }
}
