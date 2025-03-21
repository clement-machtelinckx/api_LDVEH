<?php

namespace App\Tests\Entity;

use App\Entity\Equipment;
use PHPUnit\Framework\TestCase;

class EquipmentTest extends TestCase
{
    public function testGetSetId()
    {
        $equipment = new Equipment();
        $equipment->setId(1);
        
        $this->assertEquals(1, $equipment->getId());
    }

    public function testGetSetName()
    {
        $equipment = new Equipment();
        $equipment->setName("Sword of Destiny");
        
        $this->assertEquals("Sword of Destiny", $equipment->getName());
    }

    public function testGetSetDescription()
    {
        $equipment = new Equipment();
        $equipment->setDescription("A legendary sword imbued with magic.");
        
        $this->assertEquals("A legendary sword imbued with magic.", $equipment->getDescription());
    }

    public function testGetSetEffect()
    {
        $equipment = new Equipment();
        $equipment->setEffect("Increases attack power by 20%.");
        
        $this->assertEquals("Increases attack power by 20%.", $equipment->getEffect());
    }
}
