<?php

namespace App\Tests\Unit;

use App\Entity\Equipment;
use App\Enum\EquipmentSlot;
use App\Enum\EquipmentType;
use PHPUnit\Framework\TestCase;

class EquipmentTest extends TestCase
{
    public function testGetSetId(): void
    {
        $equipment = new Equipment();
        $equipment->setId(1);

        $this->assertEquals(1, $equipment->getId());
    }

    public function testGetSetName(): void
    {
        $equipment = new Equipment();
        $equipment->setName('Épée');

        $this->assertEquals('Épée', $equipment->getName());
    }

    public function testGetSetDescription(): void
    {
        $equipment = new Equipment();
        $equipment->setDescription('Une épée tranchante.');

        $this->assertEquals('Une épée tranchante.', $equipment->getDescription());
    }

    public function testGetSetSlug(): void
    {
        $equipment = new Equipment();
        $equipment->setSlug('epee');

        $this->assertEquals('epee', $equipment->getSlug());
    }

    public function testGetSetType(): void
    {
        $equipment = new Equipment();
        $equipment->setType(EquipmentType::Weapon);

        $this->assertSame(EquipmentType::Weapon, $equipment->getType());
    }

    public function testGetSetSlot(): void
    {
        $equipment = new Equipment();
        $this->assertNull($equipment->getSlot());

        $equipment->setSlot(EquipmentSlot::Head);
        $this->assertSame(EquipmentSlot::Head, $equipment->getSlot());
    }

    public function testEnduranceBonusDefaultsToZero(): void
    {
        $equipment = new Equipment();
        $this->assertEquals(0, $equipment->getEnduranceBonus());
    }

    public function testGetSetEnduranceBonus(): void
    {
        $equipment = new Equipment();
        $equipment->setEnduranceBonus(4);

        $this->assertEquals(4, $equipment->getEnduranceBonus());
    }

    public function testHealAmountDefaultsToZero(): void
    {
        $equipment = new Equipment();
        $this->assertEquals(0, $equipment->getHealAmount());
    }

    public function testGetSetHealAmount(): void
    {
        $equipment = new Equipment();
        $equipment->setHealAmount(4);

        $this->assertEquals(4, $equipment->getHealAmount());
    }

    public function testIsConsumable(): void
    {
        $potion = new Equipment();
        $potion->setType(EquipmentType::Potion);
        $this->assertTrue($potion->isConsumable());

        $meal = new Equipment();
        $meal->setType(EquipmentType::Meal);
        $this->assertTrue($meal->isConsumable());

        $weapon = new Equipment();
        $weapon->setType(EquipmentType::Weapon);
        $this->assertFalse($weapon->isConsumable());

        $special = new Equipment();
        $special->setType(EquipmentType::SpecialObject);
        $this->assertFalse($special->isConsumable());
    }

    public function testGoesInBackpack(): void
    {
        $potion = new Equipment();
        $potion->setType(EquipmentType::Potion);
        $this->assertTrue($potion->goesInBackpack());

        $meal = new Equipment();
        $meal->setType(EquipmentType::Meal);
        $this->assertTrue($meal->goesInBackpack());

        $backpackItem = new Equipment();
        $backpackItem->setType(EquipmentType::BackpackItem);
        $this->assertTrue($backpackItem->goesInBackpack());

        $weapon = new Equipment();
        $weapon->setType(EquipmentType::Weapon);
        $this->assertFalse($weapon->goesInBackpack());

        $special = new Equipment();
        $special->setType(EquipmentType::SpecialObject);
        $this->assertFalse($special->goesInBackpack());
    }

    public function testTypeLabel(): void
    {
        $equipment = new Equipment();
        $equipment->setType(EquipmentType::Weapon);
        $this->assertEquals('Arme', $equipment->getTypeLabel());
    }

    public function testSlotLabel(): void
    {
        $equipment = new Equipment();
        $this->assertEquals('-', $equipment->getSlotLabel());

        $equipment->setSlot(EquipmentSlot::Head);
        $this->assertEquals('Tête', $equipment->getSlotLabel());
    }

    public function testToString(): void
    {
        $equipment = new Equipment();
        $equipment->setName('Casque');

        $this->assertEquals('Casque', (string) $equipment);
    }
}
