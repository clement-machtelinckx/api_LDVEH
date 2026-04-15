<?php

namespace App\Tests\Unit;

use App\Entity\Adventurer;
use App\Entity\Skill;
use PHPUnit\Framework\TestCase;

class SkillTest extends TestCase
{
    public function testGetSetId(): void
    {
        $skill = new Skill();
        $skill->setId(1);

        $this->assertSame(1, $skill->getId());
    }

    public function testGetSetName(): void
    {
        $skill = new Skill();
        $skill->setName('Guérison');

        $this->assertSame('Guérison', $skill->getName());
    }

    public function testGetSetDescription(): void
    {
        $skill = new Skill();
        $skill->setDescription('Récupération progressive.');

        $this->assertSame('Récupération progressive.', $skill->getDescription());
    }

    public function testGetSetSlug(): void
    {
        $skill = new Skill();
        $skill->setSlug('guerison');

        $this->assertSame('guerison', $skill->getSlug());
    }

    public function testToString(): void
    {
        $skill = new Skill();
        $skill->setName('Camouflage');

        $this->assertEquals('Camouflage', (string) $skill);
    }

    public function testAddRemoveAdventurer(): void
    {
        $skill = new Skill();
        $adventurer = new Adventurer();

        $this->assertCount(0, $skill->getAdventurers());

        $skill->addAdventurer($adventurer);
        $this->assertCount(1, $skill->getAdventurers());
        $this->assertTrue($adventurer->getSkills()->contains($skill));

        // Double ajout ignoré
        $skill->addAdventurer($adventurer);
        $this->assertCount(1, $skill->getAdventurers());

        $skill->removeAdventurer($adventurer);
        $this->assertCount(0, $skill->getAdventurers());
        $this->assertFalse($adventurer->getSkills()->contains($skill));
    }
}
