<?php

namespace App\Tests\Unit;

use App\Entity\Adventurer;
use App\Entity\Equipment;
use App\Enum\EquipmentSlot;
use App\Enum\EquipmentType;
use App\Service\EquipmentService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class EquipmentServiceTest extends TestCase
{
    private EntityManagerInterface $em;
    private EquipmentService $service;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->em->method('flush');
        $this->service = new EquipmentService($this->em);
    }

    // ── Helpers ──────────────────────────────────────────────

    private function makeAdventurer(int $maxEndurance = 25, int $endurance = 20): Adventurer
    {
        $adventurer = new Adventurer();
        $adventurer->setAdventurerName('Test');
        $adventurer->setAbility(15);
        $adventurer->setEndurance($endurance);
        $adventurer->setMaxEndurance($maxEndurance);

        return $adventurer;
    }

    private function makeWeapon(string $slug = 'epee'): Equipment
    {
        $eq = new Equipment();
        $eq->setName('Épée');
        $eq->setSlug($slug);
        $eq->setType(EquipmentType::Weapon);

        return $eq;
    }

    private function makeArmor(string $slug = 'casque', EquipmentSlot $slot = EquipmentSlot::Head, int $bonus = 2): Equipment
    {
        $eq = new Equipment();
        $eq->setName('Casque');
        $eq->setSlug($slug);
        $eq->setType(EquipmentType::SpecialObject);
        $eq->setSlot($slot);
        $eq->setEnduranceBonus($bonus);

        return $eq;
    }

    private function makePotion(int $healAmount = 4): Equipment
    {
        $eq = new Equipment();
        $eq->setName('Potion de Guérison');
        $eq->setSlug('potion_guerison');
        $eq->setType(EquipmentType::Potion);
        $eq->setHealAmount($healAmount);

        return $eq;
    }

    private function makeMeal(): Equipment
    {
        $eq = new Equipment();
        $eq->setName('Repas');
        $eq->setSlug('repas');
        $eq->setType(EquipmentType::Meal);

        return $eq;
    }

    private function makeBackpackItem(string $slug = 'corde'): Equipment
    {
        $eq = new Equipment();
        $eq->setName('Corde');
        $eq->setSlug($slug);
        $eq->setType(EquipmentType::BackpackItem);

        return $eq;
    }

    // ── addEquipment ────────────────────────────────────────

    public function testAddWeapon(): void
    {
        $adventurer = $this->makeAdventurer();
        $weapon = $this->makeWeapon();

        $removed = $this->service->addEquipment($adventurer, $weapon);

        $this->assertNull($removed);
        $this->assertCount(1, $adventurer->getAdventurerEquipments());
    }

    public function testAddTwoWeaponsSucceeds(): void
    {
        $adventurer = $this->makeAdventurer();
        $weapon1 = $this->makeWeapon('epee');
        $weapon2 = $this->makeWeapon('hache');

        $this->service->addEquipment($adventurer, $weapon1);
        $this->service->addEquipment($adventurer, $weapon2);

        $this->assertCount(2, $adventurer->getAdventurerEquipments());
    }

    public function testAddThirdWeaponThrows(): void
    {
        $adventurer = $this->makeAdventurer();
        $this->service->addEquipment($adventurer, $this->makeWeapon('epee'));
        $this->service->addEquipment($adventurer, $this->makeWeapon('hache'));

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('maximum 2 armes');

        $this->service->addEquipment($adventurer, $this->makeWeapon('lance'));
    }

    public function testAddSlotEquipmentReplacesExisting(): void
    {
        $adventurer = $this->makeAdventurer();
        $helmet1 = $this->makeArmor('casque_cuir', EquipmentSlot::Head, 1);
        $helmet2 = $this->makeArmor('casque_fer', EquipmentSlot::Head, 3);

        $this->service->addEquipment($adventurer, $helmet1);
        $removed = $this->service->addEquipment($adventurer, $helmet2);

        $this->assertSame($helmet1, $removed);
        $this->assertCount(1, $adventurer->getAdventurerEquipments());

        $ae = $adventurer->getAdventurerEquipments()->first();
        $this->assertSame($helmet2, $ae->getEquipment());
    }

    public function testAddBackpackItemsUpToMax(): void
    {
        $adventurer = $this->makeAdventurer();

        for ($i = 0; $i < Adventurer::MAX_BACKPACK; $i++) {
            $item = $this->makeBackpackItem('item_' . $i);
            $this->service->addEquipment($adventurer, $item);
        }

        $this->assertCount(Adventurer::MAX_BACKPACK, $adventurer->getAdventurerEquipments());
    }

    public function testAddBackpackItemOverMaxThrows(): void
    {
        $adventurer = $this->makeAdventurer();

        for ($i = 0; $i < Adventurer::MAX_BACKPACK; $i++) {
            $this->service->addEquipment($adventurer, $this->makeBackpackItem('item_' . $i));
        }

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('sac à dos plein');

        $this->service->addEquipment($adventurer, $this->makeBackpackItem('item_extra'));
    }

    public function testAddMultipleQuantityBackpackItem(): void
    {
        $adventurer = $this->makeAdventurer();
        $potion = $this->makePotion();

        $this->service->addEquipment($adventurer, $potion, 3);

        $ae = $adventurer->findAdventurerEquipment($potion);
        $this->assertNotNull($ae);
        $this->assertEquals(3, $ae->getQuantity());
    }

    public function testAddQuantityExceedingBackpackThrows(): void
    {
        $adventurer = $this->makeAdventurer();
        $potion = $this->makePotion();

        $this->expectException(\LogicException::class);
        $this->service->addEquipment($adventurer, $potion, Adventurer::MAX_BACKPACK + 1);
    }

    // ── consume ─────────────────────────────────────────────

    public function testConsumePotion(): void
    {
        $adventurer = $this->makeAdventurer();
        $potion = $this->makePotion(4);

        $this->service->addEquipment($adventurer, $potion, 2);
        $healAmount = $this->service->consume($adventurer, $potion);

        $this->assertEquals(4, $healAmount);
        $ae = $adventurer->findAdventurerEquipment($potion);
        $this->assertNotNull($ae);
        $this->assertEquals(1, $ae->getQuantity());
    }

    public function testConsumeLastPotionRemovesFromInventory(): void
    {
        $adventurer = $this->makeAdventurer();
        $potion = $this->makePotion();

        $this->service->addEquipment($adventurer, $potion, 1);
        $this->service->consume($adventurer, $potion);

        $this->assertNull($adventurer->findAdventurerEquipment($potion));
        $this->assertCount(0, $adventurer->getAdventurerEquipments());
    }

    public function testConsumeNonConsumableThrows(): void
    {
        $adventurer = $this->makeAdventurer();
        $weapon = $this->makeWeapon();

        $this->service->addEquipment($adventurer, $weapon);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('n\'est pas consommable');

        $this->service->consume($adventurer, $weapon);
    }

    public function testConsumeMeal(): void
    {
        $adventurer = $this->makeAdventurer();
        $meal = $this->makeMeal();

        $this->service->addEquipment($adventurer, $meal, 1);
        $healAmount = $this->service->consume($adventurer, $meal);

        $this->assertEquals(0, $healAmount);
        $this->assertCount(0, $adventurer->getAdventurerEquipments());
    }

    // ── replaceWeapon ───────────────────────────────────────

    public function testReplaceWeapon(): void
    {
        $adventurer = $this->makeAdventurer();
        $old = $this->makeWeapon('epee');
        $new = $this->makeWeapon('glaive');

        $this->service->addEquipment($adventurer, $old);
        $this->service->replaceWeapon($adventurer, $old, $new);

        $this->assertNull($adventurer->findAdventurerEquipment($old));
        $this->assertNotNull($adventurer->findAdventurerEquipment($new));
    }

    public function testReplaceWeaponWithNonWeaponThrows(): void
    {
        $adventurer = $this->makeAdventurer();
        $weapon = $this->makeWeapon();
        $potion = $this->makePotion();

        $this->service->addEquipment($adventurer, $weapon);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('doivent être des armes');

        $this->service->replaceWeapon($adventurer, $weapon, $potion);
    }

    // ── removeEquipment ─────────────────────────────────────

    public function testRemoveEquipmentDecrementsQuantity(): void
    {
        $adventurer = $this->makeAdventurer();
        $potion = $this->makePotion();

        $this->service->addEquipment($adventurer, $potion, 3);
        $this->service->removeEquipment($adventurer, $potion, 1);

        $ae = $adventurer->findAdventurerEquipment($potion);
        $this->assertNotNull($ae);
        $this->assertEquals(2, $ae->getQuantity());
    }

    public function testRemoveEquipmentCompletely(): void
    {
        $adventurer = $this->makeAdventurer();
        $weapon = $this->makeWeapon();

        $this->service->addEquipment($adventurer, $weapon);
        $this->service->removeEquipment($adventurer, $weapon);

        $this->assertCount(0, $adventurer->getAdventurerEquipments());
    }

    // ── effectiveMaxEndurance ────────────────────────────────

    public function testEffectiveMaxEnduranceWithBonuses(): void
    {
        $adventurer = $this->makeAdventurer(25);
        $helmet = $this->makeArmor('casque', EquipmentSlot::Head, 2);
        $armor = $this->makeArmor('cotte', EquipmentSlot::Torso, 4);

        $this->service->addEquipment($adventurer, $helmet);
        $this->service->addEquipment($adventurer, $armor);

        $this->assertEquals(31, $adventurer->getEffectiveMaxEndurance());
    }

    public function testEffectiveMaxEnduranceWithoutBonuses(): void
    {
        $adventurer = $this->makeAdventurer(25);

        $this->assertEquals(25, $adventurer->getEffectiveMaxEndurance());
    }

    public function testEffectiveMaxEnduranceAfterRemovingArmor(): void
    {
        $adventurer = $this->makeAdventurer(25);
        $helmet = $this->makeArmor('casque', EquipmentSlot::Head, 2);

        $this->service->addEquipment($adventurer, $helmet);
        $this->assertEquals(27, $adventurer->getEffectiveMaxEndurance());

        $this->service->removeEquipment($adventurer, $helmet);
        $this->assertEquals(25, $adventurer->getEffectiveMaxEndurance());
    }
}
