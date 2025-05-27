<?php

namespace App\Tests\Unit;

use App\Entity\Monster;
use PHPUnit\Framework\TestCase;

class MonsterTest extends TestCase
{
    public function testSetAndGetId(): void
    {
        $monster = new Monster();
        $refClass = new \ReflectionClass($monster);
        $property = $refClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($monster, 5);

        $this->assertSame(5, $monster->getId());
    }

    public function testSetAndGetMonsterName(): void
    {
        $monster = new Monster();
        $monster->setMonsterName("Gorak");
        $this->assertSame("Gorak", $monster->getMonsterName());
    }

    public function testSetAndGetAbility(): void
    {
        $monster = new Monster();
        $monster->setAbility(8);
        $this->assertSame(8, $monster->getAbility());
    }

    public function testSetAndGetEndurance(): void
    {
        $monster = new Monster();
        $monster->setEndurance(20);
        $this->assertSame(20, $monster->getEndurance());
    }

    public function testToStringReturnsMonsterName(): void
    {
        $monster = new Monster();
        $monster->setMonsterName("Kragg");
        $this->assertSame("Kragg", (string)$monster);
    }
}
    