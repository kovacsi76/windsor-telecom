<?php

namespace App\Tests;

use App\Entity\Client;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Stage;
use App\Entity\Type;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function testCanCreateInstanceOfOrder()
    {
        $order = new Order();
        $this->assertInstanceOf('App\Entity\Order', $order);

        return $order;
    }

    /**
     * @depends testCanCreateInstanceOfOrder
     */
    public function testCanSetAndGetOrderProperties(Order $order)
    {
        $data = [
            'client' => new Client(),
            'product' => new Product(),
            'type' => new Type(),
            'stage' => new Stage(),
        ];

        $order
            ->setClient($data['client'])
            ->setProduct($data['product'])
            ->setType($data['type'])
            ->setStage($data['stage']);

        $this->assertNull($order->getId());
        $this->assertEquals($data['client'], $order->getClient());
        $this->assertEquals($data['product'], $order->getProduct());
        $this->assertEquals($data['type'], $order->getType());
        $this->assertEquals($data['stage'], $order->getStage());
    }
}
