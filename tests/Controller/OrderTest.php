<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use App\Entity\Client;
use App\Entity\Product;
use App\Entity\Stage;
use App\Entity\Type;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class OrderTest extends WebTestCase
{
    public function testGetOrdersListSuccessful(): array
    {
        $httpClient = static::createClient();

        $crawler = $httpClient->request('GET', '/api/orders');
        $response = $httpClient->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $headers = $response->headers->all();
        $this->assertIsArray($headers);
        $this->assertArrayHasKey('content-type', $headers);
        $this->assertTrue(in_array('application/ld+json; charset=utf-8', $headers['content-type']));

        $content = json_decode($response->getContent(), true);
        $this->assertEquals(count(AppFixtures::ORDERS), $content['hydra:totalItems']);

        return $content;
    }

    /**
     * @depends testGetOrdersListSuccessful
     */
    public function testCreateNewOrderWithWrongStageFail()
    {
        $httpClient = static::createClient();
        $entityManager = static::$container->get('doctrine')->getManager();

        $client = $entityManager->getRepository(Client::class)
            ->findOneBy(['name' => AppFixtures::CLIENTS['A Person']['name']]);
        $product = $entityManager->getRepository(Product::class)
            ->findOneBy(['name' => AppFixtures::PRODUCTS['Broadband']['name']]);
        $type = $entityManager->getRepository(Type::class)
            ->findOneBy(['name' => AppFixtures::TYPES['Free trial']['name']]);
        $stage = $entityManager->getRepository(Stage::class)
            ->findOneBy(['name' => AppFixtures::STAGES['Delivered']['name']]);

        $data = [
            'client' => '/api/clients/' . $client->getId(),
            'product' => '/api/products/' . $product->getId(),
            'type' => '/api/types/' . $type->getId(),
            'stage' => '/api/stages/' . $stage->getId(),
        ];

        $crawler = $httpClient->request(
            'POST',
            '/api/orders',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/ld+json'],
            json_encode($data)
        );
        $response = $httpClient->getResponse();
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());

        $headers = $response->headers->all();
        $this->assertIsArray($headers);
        $this->assertArrayHasKey('content-type', $headers);
        $this->assertTrue(in_array('application/ld+json; charset=utf-8', $headers['content-type']));

        $content = json_decode($response->getContent(), true);
        $this->assertStringContainsString(
            'stage: New order needs to be created with stage "Created"',
            $content['hydra:description']
        );
    }

    /**
     * @depends testGetOrdersListSuccessful
     */
    public function testCreateNewOrderSuccessful(array $results): int
    {
        $httpClient = static::createClient();
        $entityManager = static::$container->get('doctrine')->getManager();

        $client = $entityManager->getRepository(Client::class)
            ->findOneBy(['name' => AppFixtures::CLIENTS['A Person']['name']]);
        $product = $entityManager->getRepository(Product::class)
            ->findOneBy(['name' => AppFixtures::PRODUCTS['Broadband']['name']]);
        $type = $entityManager->getRepository(Type::class)
            ->findOneBy(['name' => AppFixtures::TYPES['Free trial']['name']]);
        $stage = $entityManager->getRepository(Stage::class)
            ->findOneBy(['name' => AppFixtures::STAGES['Created']['name']]);

        $nextId = $results['hydra:totalItems'] + 1;
        $data = [
            'client' => '/api/clients/' . $client->getId(),
            'product' => '/api/products/' . $product->getId(),
            'type' => '/api/types/' . $type->getId(),
            'stage' => '/api/stages/' . $stage->getId(),
        ];

        $crawler = $httpClient->request(
            'POST',
            '/api/orders',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/ld+json'],
            json_encode($data)
        );
        $response = $httpClient->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $headers = $response->headers->all();
        $this->assertIsArray($headers);
        $this->assertArrayHasKey('content-type', $headers);
        $this->assertTrue(in_array('application/ld+json; charset=utf-8', $headers['content-type']));

        $content = json_decode($response->getContent(), true);
        $this->assertEquals($nextId, $content['id'], 'Order with ID ' . $nextId . ' expected');
        $this->assertEquals($data['client'], $content['client']);
        $this->assertEquals($data['product'], $content['product']);
        $this->assertEquals($data['type'], $content['type']);
        $this->assertEquals($data['stage'], $content['stage']);

        return $nextId;
    }

    /**
     * @depends testCreateNewOrderSuccessful
     */
    public function testGetOrders(int $amount): void
    {
        $httpClient = static::createClient();

        $crawler = $httpClient->request('GET', '/api/orders');
        $response = $httpClient->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $headers = $response->headers->all();
        $this->assertIsArray($headers);
        $this->assertArrayHasKey('content-type', $headers);
        $this->assertTrue(in_array('application/ld+json; charset=utf-8', $headers['content-type']));

        $content = json_decode($response->getContent(), true);
        $this->assertEquals($amount, $content['hydra:totalItems']);
    }

    /**
     * @depends testCreateNewOrderSuccessful
     */
    public function testTransitionOrderToNextStageSuccessful(int $currentId): void
    {
        $httpClient = static::createClient();
        $entityManager = static::$container->get('doctrine')->getManager();

        $stage = $entityManager->getRepository(Stage::class)
            ->findOneBy(['name' => AppFixtures::STAGES['Approved']['name']]);

        $data = [
            'stage' => '/api/stages/' . $stage->getId(),
        ];

        $crawler = $httpClient->request(
            'PUT',
            '/api/orders/' . $currentId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/ld+json'],
            json_encode($data)
        );
        $response = $httpClient->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $headers = $response->headers->all();
        $this->assertIsArray($headers);
        $this->assertArrayHasKey('content-type', $headers);
        $this->assertTrue(in_array('application/ld+json; charset=utf-8', $headers['content-type']));

        $content = json_decode($response->getContent(), true);
        $this->assertEquals($currentId, $content['id'], 'Order with ID ' . $currentId . ' expected');
        $this->assertEquals($data['stage'], $content['stage']);
    }

    /**
     * @depends testCreateNewOrderSuccessful
     */
    public function testTransitionOrderToContractThirdStageFail(int $currentId): void
    {
        $httpClient = static::createClient();
        $entityManager = static::$container->get('doctrine')->getManager();

        $stage = $entityManager->getRepository(Stage::class)
            ->findOneBy(['name' => AppFixtures::STAGES['Signed']['name']]);

        $data = [
            'stage' => '/api/stages/' . $stage->getId(),
        ];

        $crawler = $httpClient->request(
            'PUT',
            '/api/orders/' . $currentId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/ld+json'],
            json_encode($data)
        );
        $response = $httpClient->getResponse();
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());

        $headers = $response->headers->all();
        $this->assertIsArray($headers);
        $this->assertArrayHasKey('content-type', $headers);
        $this->assertTrue(in_array('application/ld+json; charset=utf-8', $headers['content-type']));

        $content = json_decode($response->getContent(), true);
        $this->assertStringContainsString(
            'stage: Transition from "Approved" to "Signed" not allowed',
            $content['hydra:description']
        );
    }

    /**
     * @depends testCreateNewOrderSuccessful
     */
    public function testTransitionOrderToFreeTrialThirdStageSuccessful(int $currentId): void
    {
        $httpClient = static::createClient();
        $entityManager = static::$container->get('doctrine')->getManager();

        $stage = $entityManager->getRepository(Stage::class)
            ->findOneBy(['name' => AppFixtures::STAGES['Delivered']['name']]);

        $data = [
            'stage' => '/api/stages/' . $stage->getId(),
        ];

        $crawler = $httpClient->request(
            'PUT',
            '/api/orders/' . $currentId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/ld+json'],
            json_encode($data)
        );
        $response = $httpClient->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $headers = $response->headers->all();
        $this->assertIsArray($headers);
        $this->assertArrayHasKey('content-type', $headers);
        $this->assertTrue(in_array('application/ld+json; charset=utf-8', $headers['content-type']));

        $content = json_decode($response->getContent(), true);
        $this->assertEquals($currentId, $content['id'], 'Order with ID ' . $currentId . ' expected');
        $this->assertEquals($data['stage'], $content['stage']);
    }
}
