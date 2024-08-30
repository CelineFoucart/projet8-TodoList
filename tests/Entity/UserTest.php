<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUser(): void
    {
        $user = (new User())->setUsername('John Doe')->setEmail('johndoe@gmail.com');
        $this->assertEquals('John Doe', $user->getUsername());
        $this->assertEquals('johndoe@gmail.com', $user->getEmail());
        $this->assertEquals('ROLE_USER', $user->getRoles()[0]);
    }

    public function testAddTaskToUser(): void
    {
        $user = (new User())->setUsername('John Doe');
        $task = (new Task())->setTitle('task 1')->setContent('content');
        $user->addTask($task);

        $this->assertEquals('task 1', $user->getTasks()->first()->getTitle());
    }

    public function testRemoveTaskFromUser(): void
    {
        $user = (new User())->setUsername('John Doe');
        $task = (new Task())->setTitle('task 1')->setContent('content');
        $user->addTask($task);

        $user->removeTask($task);
        $this->assertTrue($user->getTasks()->isEmpty());
    }
}
