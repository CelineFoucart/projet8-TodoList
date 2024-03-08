<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    use FixtureTrait;

    public function testAnonymous(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', "S'inscrire");
    }

    public function testSubmitInvalidUsername(): void
    {
        $client = static::createClient();
        $this->makeFixture();

        $client->request('GET', '/register');
        $client->submitForm('Inscription', [
            'registration_form[username]' => '',
            'registration_form[email]' => 'laureendoe@gmail.com',
            'registration_form[plainPassword][first]' => 'passwordedited123',
            'registration_form[plainPassword][second]' => 'passwordedited123',
            'registration_form[agreeTerms]' => true,
        ]);
        
        $this->assertSelectorExists('.invalid-feedback');
    }

    public function testSubmitInvalidEmail(): void
    {
        $client = static::createClient();
        $this->makeFixture();

        $client->request('GET', '/register');
        $client->submitForm('Inscription', [
            'registration_form[username]' => 'Laureen',
            'registration_form[email]' => 'laureendoe',
            'registration_form[plainPassword][first]' => 'passwordedited123',
            'registration_form[plainPassword][second]' => 'passwordedited123',
            'registration_form[agreeTerms]' => true,
        ]);
        
        $this->assertSelectorExists('.invalid-feedback');
    }

    public function testSubmitInvalidPassword(): void
    {
        $client = static::createClient();
        $this->makeFixture();

        $client->request('GET', '/register');
        $client->submitForm('Inscription', [
            'registration_form[username]' => 'Laureen',
            'registration_form[email]' => 'laureendoe@gmail.com',
            'registration_form[plainPassword][first]' => 'passwordedited123',
            'registration_form[plainPassword][second]' => 'password',
            'registration_form[agreeTerms]' => true,
        ]);
        
        $this->assertSelectorExists('.invalid-feedback');
    }

    public function testSubmitValid(): void
    {
        $client = static::createClient();
        $this->makeFixture();

        $client->request('GET', '/register');
        $client->submitForm('Inscription', [
            'registration_form[username]' => 'Laureen',
            'registration_form[email]' => 'laureendoe@gmail.com',
            'registration_form[plainPassword][first]' => 'passwordedited123',
            'registration_form[plainPassword][second]' => 'passwordedited123',
            'registration_form[agreeTerms]' => true,
        ]);
        $client->followRedirect();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'laureendoe@gmail.com']);
        $this->assertNotNull($user);
    }
}
