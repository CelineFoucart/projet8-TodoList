<?php

namespace App\Tests\Repository;

use App\Repository\UserRepository;
use App\Tests\FixtureTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserRepositoryTest extends KernelTestCase
{
    use FixtureTrait;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->makeFixture();
    }

    public function testUpgradePassword(): void
    {
        /** 
         * @var UserRepository 
         */
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'johndoe@gmail.com']);
        /** 
         * @var UserPasswordHasherInterface 
         */
        $paswordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        $userRepository->upgradePassword($user, $paswordHasher->hashPassword($user, 'passwordedited123'));

        $this->assertTrue($paswordHasher->isPasswordValid($user, 'passwordedited123'));
    }
}
