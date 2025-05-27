<?php

namespace App\Tests\Unit;

use App\Entity\Adventurer;
use App\Entity\User;
use App\Entity\FightHistory;
use App\Entity\Adventure;
use PHPUnit\Framework\TestCase;

class AdventurerTest extends TestCase
{
    public function testSetAndGetId(): void
    {
        $adventurer = new Adventurer();
        $adventurer->setId(10);
        $this->assertSame(10, $adventurer->getId());
    }

    public function testSetAndGetAdventurerName(): void
    {
        $adventurer = new Adventurer();
        $adventurer->setAdventurerName('Lone Wolf');
        $this->assertSame('Lone Wolf', $adventurer->getAdventurerName());
    }

    public function testSetAndGetAbility(): void
    {
        $adventurer = new Adventurer();
        $adventurer->setAbility(18);
        $this->assertSame(18, $adventurer->getAbility());
    }

    public function testSetAndGetEndurance(): void
    {
        $adventurer = new Adventurer();
        $adventurer->setEndurance(24);
        $this->assertSame(24, $adventurer->getEndurance());
    }

    public function testSetAndGetUser(): void
    {
        $user = new User();
        $adventurer = new Adventurer();
        $adventurer->setUser($user);
        $this->assertSame($user, $adventurer->getUser());
    }

    public function testAddAndRemoveFightHistory(): void
    {
        $adventurer = new Adventurer();
        $fightHistory = new FightHistory();

        // Ajout
        $adventurer->addFightHistory($fightHistory);
        $this->assertCount(1, $adventurer->getFightHistories());
        $this->assertSame($adventurer, $fightHistory->getAdventurer());

        // Suppression
        $adventurer->removeFightHistory($fightHistory);
        $this->assertCount(0, $adventurer->getFightHistories());
        $this->assertNull($fightHistory->getAdventurer());
    }

    public function testSetAndGetAdventure(): void
    {
        $adventurer = new Adventurer();
        $adventure = new Adventure();

        $adventurer->setAdventure($adventure);

        $this->assertSame($adventure, $adventurer->getAdventure());
        $this->assertSame($adventurer, $adventure->getAdventurer()); // test bidirectionnel
    }

    public function testToStringReturnsName(): void
    {
        $adventurer = new Adventurer();
        $adventurer->setAdventurerName("Kaidan");

        $this->assertSame("Kaidan", (string)$adventurer);
    }
}
