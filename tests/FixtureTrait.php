<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\User;
use App\DataFixtures\AppFixtures;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

trait FixtureTrait
{
    protected AbstractDatabaseTool $databaseTool;

    /**
     * Hydrates the test database
     * 
     * @return void
     */
    protected function makeFixture(): void
    {
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures(
            [AppFixtures::class]
        );
    }

    protected function loginUser(KernelBrowser $client, string $email): User
    {
        /** @var UserRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => $email]);
        $client->loginUser($user);

        return $user;
    }
}
