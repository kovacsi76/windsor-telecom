<?php

namespace App\Tests;

use App\Entity\Type;
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    public function testCanCreateInstanceOfType()
    {
        $type = new Type();
        $this->assertInstanceOf('App\Entity\Type', $type);

        return $type;
    }

    /**
     * @depends testCanCreateInstanceOfType
     */
    public function testCanSetAndGetTypeProperties(Type $type)
    {
        $data = [
            'name' => 'Test Type',
        ];

        $type->setName($data['name']);

        $this->assertNull($type->getId());
        $this->assertEquals($data['name'], $type->getName());
    }
}
