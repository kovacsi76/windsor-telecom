<?php

namespace App\Tests;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testCanCreateInstanceOfProduct()
    {
        $product = new Product();
        $this->assertInstanceOf('App\Entity\Product', $product);

        return $product;
    }

    /**
     * @depends testCanCreateInstanceOfProduct
     */
    public function testCanSetAndGetProductProperties(Product $product)
    {
        $data = [
            'name' => 'Test Product',
        ];

        $product->setName($data['name']);

        $this->assertNull($product->getId());
        $this->assertEquals($data['name'], $product->getName());
    }
}
