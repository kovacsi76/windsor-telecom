<?php

namespace App\Tests;

use App\Entity\Stage;
use PHPUnit\Framework\TestCase;

class StageTest extends TestCase
{
    public function testCanCreateInstanceOfStage()
    {
        $stage = new Stage();
        $this->assertInstanceOf('App\Entity\Stage', $stage);

        return $stage;
    }

    /**
     * @depends testCanCreateInstanceOfStage
     */
    public function testCanSetAndGetStageProperties(Stage $stage)
    {
        $data = [
            'name' => 'Test Stage',
        ];

        $stage->setName($data['name']);

        $this->assertNull($stage->getId());
        $this->assertEquals($data['name'], $stage->getName());
    }
}
