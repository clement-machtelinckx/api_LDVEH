<?php


namespace App\Tests\Unit;

use App\Entity\FightHistory;
use App\Entity\Adventurer;
use App\Entity\Monster;
use PHPUnit\Framework\TestCase;

class FightHistoryTest extends TestCase
{
    public function testSetAndGetAdventurer(): void
    {
        $adventurer = new Adventurer();
        $fightHistory = new FightHistory();

        $fightHistory->setAdventurer($adventurer);
        $this->assertSame($adventurer, $fightHistory->getAdventurer());
    }

    public function testSetAndGetMonster(): void
    {
        $monster = new Monster();
        $fightHistory = new FightHistory();

        $fightHistory->setMonster($monster);
        $this->assertSame($monster, $fightHistory->getMonster());
    }

    public function testSetAndGetVictoryTrue(): void
    {
        $fightHistory = new FightHistory();
        $fightHistory->setVictory(true);

        $this->assertTrue($fightHistory->isVictory());
    }

    public function testSetAndGetVictoryFalse(): void
    {
        $fightHistory = new FightHistory();
        $fightHistory->setVictory(false);

        $this->assertFalse($fightHistory->isVictory());
    }

    public function testSetAndGetVictoryNull(): void
    {
        $fightHistory = new FightHistory();
        $fightHistory->setVictory(null);

        $this->assertNull($fightHistory->isVictory());
    }
}
