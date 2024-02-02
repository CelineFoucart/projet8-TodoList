<?php

namespace App\Tests;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    use FixtureTrait;

    public function testIndexAsAnonymous(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tasks');
        $this->assertResponseRedirects('/login');
    }

    public function testIndexAsLoggedIn(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $this->loginUser($client, 'johndoe@gmail.com');
        $client->request('GET', '/tasks');
        $this->assertSelectorTextContains('h2', 'Liste des tâches');
        $this->assertSelectorTextContains('h4 a', 'Task number 0');
    }

    public function testCreateAsAnonymous(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tasks/create');
        $this->assertResponseRedirects('/login');
    }

    public function testCreateAsLoggedIn(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $this->loginUser($client, 'johndoe@gmail.com');
        $client->request('GET', '/tasks/create');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testEditAsAnonymous(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $task = $this->getTask('Task number 0');
        $client->request('GET', '/tasks/'.$task->getId().'/edit');

        $this->assertResponseRedirects('/login');
    }

    protected function getTask(string $title): Task
    {
        /** @var TaskRepository */
        $repository = static::getContainer()->get(TaskRepository::class);
        $task = $repository->findOneBy(['title' => $title]);

        if (null === $task) {
            throw new \Exception('No task named');
        }

        return $task;
    }
}
