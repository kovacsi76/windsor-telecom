<?php

namespace App\Tests;

use App\Entity\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testCanCreateInstanceOfClient()
    {
        $client = new Client();
        $this->assertInstanceOf('App\Entity\Client', $client);

        return $client;
    }

    /**
     * @depends testCanCreateInstanceOfClient
     */
    public function testCanSetAndGetClientProperties(Client $client)
    {
        $data = [
            'name' => 'Test Client',
            'email' => 'test.client@domain.com',
        ];

        $client
            ->setName($data['name'])
            ->setEmail($data['email']);

        $this->assertNull($client->getId());
        $this->assertEquals($data['name'], $client->getName());
        $this->assertEquals($data['email'], $client->getEmail());
    }
}
