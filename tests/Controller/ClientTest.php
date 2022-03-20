<?php

namespace App\Tests\Controller;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\DataFixtures\AppFixtures;
use Symfony\Component\HttpFoundation\Response;

class ClientTest extends ApiTestCase
{
    public function testGetClientsListSuccessful(): array
    {
        $httpClient = static::createClient();

        $response = $httpClient->request('GET', '/api/clients');
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $headers = $response->getHeaders();
        $this->assertIsArray($headers);
        $this->assertArrayHasKey('content-type', $headers);
        $this->assertTrue(in_array('application/ld+json; charset=utf-8', $headers['content-type']));

        $content = json_decode($response->getContent(), true);
        $this->assertCount($content['hydra:totalItems'], AppFixtures::CLIENTS);

        return $content;
    }

    /**
     * @depends testGetClientsListSuccessful
     */
    public function testAddNewClientSuccessful(array $content): int
    {
        $httpClient = static::createClient();

        $nextId = $content['hydra:totalItems'] + 1;
        $data = ['name' => 'Gremlins Air', 'email' => 'head.honcho@gremlins.air'];

        $response = $httpClient->request('POST', '/api/clients', ['json' => $data, 'headers' => ['CONTENT_TYPE' => 'application/ld+json', 'ACCEPT' => 'application/ld+json']]);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $headers = $response->getHeaders();
        $this->assertIsArray($headers);
        $this->assertArrayHasKey('content-type', $headers);
        $this->assertTrue(in_array('application/ld+json; charset=utf-8', $headers['content-type']));

        $content = json_decode($response->getContent(), true);
        $this->assertEquals($nextId, $content['id'], 'Client with ID ' . $nextId . ' expected');
        $this->assertEquals($data['name'], $content['name']);
        $this->assertEquals($data['email'], $content['email']);

        return $nextId;
    }

    /**
     * @depends testAddNewClientSuccessful
     */
    public function testGetClients(int $amount): void
    {
        $httpClient = static::createClient();

        $response = $httpClient->request('GET', '/api/clients');
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $headers = $response->getHeaders();
        $this->assertIsArray($headers);
        $this->assertArrayHasKey('content-type', $headers);
        $this->assertTrue(in_array('application/ld+json; charset=utf-8', $headers['content-type']));

        $content = json_decode($response->getContent(), true);
        $this->assertEquals($content['hydra:totalItems'], $amount);
    }
}
