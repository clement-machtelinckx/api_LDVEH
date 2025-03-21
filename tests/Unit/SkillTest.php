<?php

namespace App\Tests\Entity;

use App\Entity\Skill;
use PHPUnit\Framework\TestCase;

class SkillTest extends TestCase
{
    public function testSkillEntity(): void
    {
        $skill = new Skill();

        // Test des setters
        $skill->setId(1);
        $skill->setName('Fireball');
        $skill->setDescription('Launches a ball of fire.');
        $skill->setEffect('Burns the enemy');

        // Vérification des getters
        $this->assertSame(1, $skill->getId());
        $this->assertSame('Fireball', $skill->getName());
        $this->assertSame('Launches a ball of fire.', $skill->getDescription());
        $this->assertSame('Burns the enemy', $skill->getEffect());
    }
}
