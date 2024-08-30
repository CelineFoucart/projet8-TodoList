<?php

declare(strict_types=1);

namespace App\Tests;

use App\DataFixtures\AppFixtures;
use App\Entity\User;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait FixtureTrait
{
    /**
     * @var AbstractDatabaseTool Used to hydrate the database with test data.
     */
    protected AbstractDatabaseTool $databaseTool;

    /**
     * Hydrates the test database.
     */
    protected function makeFixture(): void
    {
        /** 
         * @var DatabaseToolCollection 
         */
        $databaseToolCollection = static::getContainer()->get(DatabaseToolCollection::class);

        $this->databaseTool = $databaseToolCollection->get();
        $this->databaseTool->loadFixtures(
            [AppFixtures::class]
        );
    }

    /**
     * Logs in a user.
     */
    protected function loginUser(KernelBrowser $client, string $email): User
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => $email]);

        if (null === $user) {
            throw new \Exception('This user does not exist!');
        }

        $client->loginUser($user);

        return $user;
    }
}
