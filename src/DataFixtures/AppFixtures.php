<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Stage;
use App\Entity\Type;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    protected array $clients = [
        'Company A' => ['name' => 'Company A', 'email' => 'ceo@company_a.co.uk'],
        'Company Z' => ['name' => 'Company Z', 'email' => 'buyer@company_z.co.uk'],
        'A Person' => ['name' => 'A Person', 'email' => 'a.person@gmail.com'],
    ];
    protected array $products = [
        'SIP trunks' => ['name' => 'SIP trunks'],
        'Broadband' => ['name' => 'Broadband'],
        'Phone numbers' => ['name' => 'Phone numbers'],
    ];
    protected array $types = [
        'Free trial' => ['name' => 'Free trial'],
        'Contract' => ['name' => 'Contract'],
    ];
    protected array $stages = [
        'Created' => ['name' => 'Created'],
        'Approved' => ['name' => 'Approved'],
        'Signed' => ['name' => 'Signed'],
        'Delivered' => ['name' => 'Delivered'],
        'Completed' => ['name' => 'Completed'],
        'Expired' => ['name' => 'Expired'],
    ];

    protected array $orders = [
        'order 1' => [
            'client' => 'Company A',
            'product' => 'SIP trunks',
            'type' => 'Contract',
            'stage' => 'Completed',
        ],
        'order 2' => [
            'client' => 'Company Z',
            'product' => 'SIP trunks',
            'type' => 'Contract',
            'stage' => 'Signed',
        ],
        'order 3' => [
            'client' => 'Company Z',
            'product' => 'Phone numbers',
            'type' => 'Contract',
            'stage' => 'Delivered',
        ],
        'order 4' => [
            'client' => 'A Person',
            'product' => 'Broadband',
            'type' => 'Free trial',
            'stage' => 'Expired',
        ],
        'order 5' => [
            'client' => 'A Person',
            'product' => 'Broadband',
            'type' => 'Contract',
            'stage' => 'Created',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        $clients = array_map(
            function($entry) use ($manager) {
                $client = new Client();
                $client
                    ->setName($entry['name'])
                    ->setEmail($entry['email']);
                $manager->persist($client);
                return $client;
            },
            $this->clients
        );

        $products = array_map(
            function($entry) use ($manager) {
                $product = new Product();
                $product->setName($entry['name']);
                $manager->persist($product);
                return $product;
            },
            $this->products
        );

        $types = array_map(
            function($entry) use ($manager) {
                $type = new Type();
                $type->setName($entry['name']);
                $manager->persist($type);
                return $type;
            },
            $this->types
        );

        $stages = array_map(
            function($entry) use ($manager) {
                $stage = new Stage();
                $stage->setName($entry['name']);
                $manager->persist($stage);
                return $stage;
            },
            $this->stages
        );

        $orders = array_map(
            function($entry) use ($manager, $clients, $products, $types, $stages) {
                $order = new Order();
                $order
                    ->setClient($clients[$entry['client']])
                    ->setProduct($products[$entry['product']])
                    ->setType($types[$entry['type']])
                    ->setStage($stages[$entry['stage']]);
                $manager->persist($order);
                return $order;
            },
            $this->orders
        );

        $manager->flush();
    }
}
