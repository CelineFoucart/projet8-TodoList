<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ApplicationAvailabilityRoutesTest extends WebTestCase
{
    /**
     * @dataProvider urlPublicProvider
     */
    public function testPageIsAccessedByAnonymous(string $url): void
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertResponseIsSuccessful();
    }

    /**
     * @dataProvider urlProtectedProvider
     */
    public function testPageIsNotAccessedByAnonymous(string $url): void
    {
        $client = self::createClient();
        $client->request('GET', $url);
        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function urlPublicProvider(): \Generator
    {
        yield ['/'];
        yield ['/login'];
        yield ['/register'];
        yield ['/terms'];
    }

    public function urlProtectedProvider(): \Generator
    {
        yield ['/tasks'];
        yield ['/tasks/create'];
        yield ['/users'];
        yield ['/users/create'];
    }
}
