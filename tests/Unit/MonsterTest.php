<?php

namespace App\Tests\Entity;

use App\Entity\Monster;
use App\Factory\MonsterFactory;
use App\Tests\Functional\ApiTestCase;
use PHPUnit\Framework\TestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MonsterTest extends ApiTestCase
{
    
    use Factories;
    use ResetDatabase;
    
    public function testMonsterProperties(): void
    {

        $monster = new Monster();
        $monster->setMonsterName('Gobelin')
                ->setAbility(8)
                ->setEndurance(14);

        $this->assertSame('Gobelin', $monster->getMonsterName());
        $this->assertSame(8, $monster->getAbility());
        $this->assertSame(14, $monster->getEndurance());
        $this->assertSame('Gobelin', (string) $monster);
    }
    public function testMonsterFactory(): void
    {
        $monster = MonsterFactory::createOne([
            'monsterName' => 'Orc',
            'ability' => 10,
            'endurance' => 12,
        ]);

        $this->assertNotNull($monster->getId());
        $this->assertSame('Orc', $monster->getMonsterName());
        $this->assertSame(10, $monster->getAbility());
        $this->assertSame(12, $monster->getEndurance());
    }
}
