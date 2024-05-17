<?php

namespace App\Tests;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

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

    public function testCreateWithValidData(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $this->loginUser($client, 'johndoe@gmail.com');
        $client->request('GET', '/tasks/create');

        $client->submitForm('Ajouter', [
            'task[title]' => 'Lorem ipsum',
            'task[content]' => 'Lorem ipsum sit amet',
        ]);
        $client->followRedirect();

        $repository = static::getContainer()->get(TaskRepository::class);
        $task = $repository->findOneBy(['title' => 'Lorem ipsum']);
        
        $this->assertNotNull($task);
    }

    public function testCreateWithInvalidData(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $this->loginUser($client, 'johndoe@gmail.com');
        $client->request('GET', '/tasks/create');

        $client->submitForm('Ajouter', [
            'task[title]' => '',
            'task[content]' => 'Lorem ipsum sit amet',
        ]);
        $this->assertSelectorExists('.invalid-feedback');
    }

    public function testEditAsAnonymous(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $task = $this->getTask('Task number 0');
        $client->request('GET', '/tasks/'.$task->getId().'/edit');
        $this->assertResponseRedirects('/login');
    }

    public function testEditAsLoggin(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $task = $this->getTask('Task number 0');
        $this->loginUser($client, 'johndoe@gmail.com');
        $client->request('GET', '/tasks/'.$task->getId().'/edit');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testEditWithValidData(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $task = $this->getTask('Task number 0');
        $this->loginUser($client, 'johndoe@gmail.com');
        $client->request('GET', '/tasks/'.$task->getId().'/edit');
        $client->submitForm('Modifier', [
            'task[title]' => 'Task number 0 Edit',
            'task[content]' => 'Lorem ipsum sit amet',
        ]);
        $client->followRedirect();
        
        $task = $this->getTask('Task number 0 Edit');
        $this->assertNotNull($task);
    }

    public function testEditWithInvalidData(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $task = $this->getTask('Task number 0');
        $this->loginUser($client, 'johndoe@gmail.com');
        $client->request('GET', '/tasks/'.$task->getId().'/edit');
        $client->submitForm('Modifier', [
            'task[title]' => 'Task number 0 Edit',
            'task[content]' => '',
        ]);
        $this->assertSelectorExists('.invalid-feedback');
    }

    public function testToggleWithValidTask(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $this->loginUser($client, 'johndoe@gmail.com');
        $task = $this->getTask('Task number 0');
        $currentToggle = $task->isDone();

        $client->request('POST', '/tasks/'.$task->getId().'/toggle');
        static::assertSame(302, $client->getResponse()->getStatusCode());
        
        $task = $this->getTask('Task number 0');
        $this->assertNotEquals($currentToggle, $task->isDone());
    }

    public function testToggleWithValidTaskAsAdmin(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $this->loginUser($client, 'johndoe@gmail.com');
        $task = $this->getTask('Task number 0');
        $currentToggle = $task->isDone();

        $client->request('POST', '/tasks/'.$task->getId().'/toggle');
        static::assertSame(302, $client->getResponse()->getStatusCode());
        
        $task = $this->getTask('Task number 0');
        $this->assertNotEquals($currentToggle, $task->isDone());
    }

    public function testToggleWithAnonymousUser(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $this->loginUser($client, 'admin@gmail.com');
        $task = $this->getTask('Task number 0');
        $currentToggle = $task->isDone();

        $client->request('POST', '/tasks/'.$task->getId().'/toggle');
        static::assertSame(302, $client->getResponse()->getStatusCode());

        $this->assertNotEquals($currentToggle, $task->isDone());
    }

    public function testToggleWithNotFoundTask(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $this->loginUser($client, 'johndoe@gmail.com');

        $client->request('POST', '/tasks/44444444/toggle');
        static::assertSame(404, $client->getResponse()->getStatusCode());
    }
    
    public function testToggleAnonymousTaskAsNotAdmin(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $this->loginUser($client, 'johndoe@gmail.com');
        $task = $this->getTask('Task Anonymous');
        $currentToggle = $task->isDone();

        $client->request('POST', '/tasks/'.$task->getId().'/toggle');
        static::assertSame(403, $client->getResponse()->getStatusCode());
        
        $task = $this->getTask('Task Anonymous');
        $this->assertEquals($currentToggle, $task->isDone());
    }

    public function testDeleteTask(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $this->loginUser($client, 'johndoe@gmail.com');
        $task = $this->getTask('Task number 0');

        $client->request('POST', '/tasks/'.$task->getId().'/delete');
        static::assertSame(302, $client->getResponse()->getStatusCode());
        
        $task = $this->getTask('Task number 0');
        $this->assertNull($task);
    }

    public function testDeleteTaskAnonymousAsNotAdmin(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $this->loginUser($client, 'johndoe@gmail.com');
        $task = $this->getTask('Task Anonymous');

        $client->request('POST', '/tasks/'.$task->getId().'/delete');
        static::assertSame(403, $client->getResponse()->getStatusCode());
        
        $task = $this->getTask('Task Anonymous');
        $this->assertNotNull($task);
    }

    protected function getTask(string $title): ?Task
    {
        /** @var TaskRepository */
        $repository = static::getContainer()->get(TaskRepository::class);
        $task = $repository->findOneBy(['title' => $title]);

        return $task;
    }
}
