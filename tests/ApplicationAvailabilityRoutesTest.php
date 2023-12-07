<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ApplicationAvailabilityRoutesTest extends WebTestCase
{
    /**
     * @dataProvider urlPublicProvider
     */
    public function testPageIsAccessedByAnonymous($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertResponseIsSuccessful();
    }

    /**
     * @dataProvider urlProtectedProvider
     */
    public function testPageIsNotAccessedByAnonymous($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);
        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function urlPublicProvider()
    {
        yield ['/'];
        yield ['/login'];
        yield ['/register'];
        
    }

    public function urlProtectedProvider()
    {
        yield ['/tasks'];
        yield ['/tasks/create'];
        yield ['/users'];
        yield ['/users/create'];
    }
}
