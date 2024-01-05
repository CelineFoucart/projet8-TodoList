<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
        
    }

    public function load(ObjectManager $manager): void
    {
        $user = $this->getUser("John Doe", "johndoe@gmail.com");
        $admin = $this->getUser("Admin", "admin@gmail.com", ["ROLE_USER", "ROLE_ADMIN"]);
        $manager->persist($user);
        $manager->persist($admin);

        for ($i=0; $i < 15; $i++) { 
            $task = (new Task())
                ->setTitle("Task number {$i}")
                ->setContent("Content of the task $i")
                ->setCreatedAt(new DateTime())
                ->setDone((bool) rand(0, 1))
                ->setAuthor($user);
            
            $manager->persist($task);
        }

        $task = (new Task())->setTitle("Task Anonymous")->setContent("Content")->setCreatedAt(new DateTime())->setDone(True);
        $manager->persist($task);
        $manager->flush();
    }

    private function getUser(string $username, string $email, array $roles = ['ROLE_USER']): User
    {
        $user = (new User())
            ->setUsername($username)
            ->setEmail($email)
            ->setRoles($roles)
            ->setIsVerified(true);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, "password123"));

        return $user;
    }
}
