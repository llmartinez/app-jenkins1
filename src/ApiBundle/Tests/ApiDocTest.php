<?php

namespace ApiBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as WebTestCase;

class ApiDocTest extends WebTestCase
{
    public function testApiDoc()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/api/doc');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('API documentation', $crawler->filter('#header h1')->text());
    }
}