<?php

namespace App\Tests\Unit;

use App\Entity\Adventurer;
use App\Entity\Equipment;
use App\Entity\Skill;
use App\Enum\EquipmentType;
use App\Service\EquipmentService;
use App\Service\SkillService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class SkillServiceTest extends TestCase
{
    private EntityManagerInterface $em;
    private SkillService $service;
    private EquipmentService $equipmentService;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->em->method('flush');
        $this->service = new SkillService($this->em);
        $this->equipmentService = new EquipmentService($this->em);
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

    private function makeSkill(string $slug, string $name = 'Skill'): Skill
    {
        $skill = new Skill();
        $skill->setName($name);
        $skill->setSlug($slug);

        return $skill;
    }

    private function makeWeapon(string $slug = 'epee'): Equipment
    {
        $eq = new Equipment();
        $eq->setName('Épée');
        $eq->setSlug($slug);
        $eq->setType(EquipmentType::Weapon);

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

    // ── addSkill ────────────────────────────────────────────

    public function testAddSkill(): void
    {
        $adventurer = $this->makeAdventurer();
        $skill = $this->makeSkill('guerison', 'Guérison');

        $this->service->addSkill($adventurer, $skill);

        $this->assertCount(1, $adventurer->getSkills());
        $this->assertTrue($adventurer->getSkills()->contains($skill));
    }

    public function testAddSkillMaxFiveThrows(): void
    {
        $adventurer = $this->makeAdventurer();

        for ($i = 0; $i < Adventurer::MAX_SKILLS; $i++) {
            $this->service->addSkill($adventurer, $this->makeSkill('skill_' . $i));
        }

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('maximum 5 disciplines');

        $this->service->addSkill($adventurer, $this->makeSkill('skill_extra'));
    }

    public function testAddDuplicateSkillThrows(): void
    {
        $adventurer = $this->makeAdventurer();
        $skill = $this->makeSkill('guerison', 'Guérison');

        $this->service->addSkill($adventurer, $skill);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('possède déjà la discipline');

        $this->service->addSkill($adventurer, $skill);
    }

    // ── applyHealing ────────────────────────────────────────

    public function testApplyHealingWithGuerison(): void
    {
        $adventurer = $this->makeAdventurer(25, 20);
        $adventurer->addSkill($this->makeSkill('guerison'));

        $healed = $this->service->applyHealing($adventurer);

        $this->assertEquals(1, $healed);
        $this->assertEquals(21, $adventurer->getEndurance());
    }

    public function testApplyHealingWithoutGuerison(): void
    {
        $adventurer = $this->makeAdventurer(25, 20);

        $healed = $this->service->applyHealing($adventurer);

        $this->assertEquals(0, $healed);
        $this->assertEquals(20, $adventurer->getEndurance());
    }

    public function testApplyHealingDoesNotExceedMax(): void
    {
        $adventurer = $this->makeAdventurer(25, 25);
        $adventurer->addSkill($this->makeSkill('guerison'));

        $healed = $this->service->applyHealing($adventurer);

        $this->assertEquals(0, $healed);
        $this->assertEquals(25, $adventurer->getEndurance());
    }

    // ── isExemptFromMeal ────────────────────────────────────

    public function testIsExemptFromMealWithChasse(): void
    {
        $adventurer = $this->makeAdventurer();
        $adventurer->addSkill($this->makeSkill('chasse'));

        $this->assertTrue($this->service->isExemptFromMeal($adventurer));
    }

    public function testIsExemptFromMealWithoutChasse(): void
    {
        $adventurer = $this->makeAdventurer();

        $this->assertFalse($this->service->isExemptFromMeal($adventurer));
    }

    // ── handleMeal ──────────────────────────────────────────

    public function testHandleMealWithChasseSkipsEating(): void
    {
        $adventurer = $this->makeAdventurer(25, 20);
        $adventurer->addSkill($this->makeSkill('chasse'));

        $result = $this->service->handleMeal($adventurer, $this->equipmentService);

        $this->assertTrue($result);
        $this->assertEquals(20, $adventurer->getEndurance());
    }

    public function testHandleMealConsumesFromInventory(): void
    {
        $adventurer = $this->makeAdventurer(25, 20);
        $meal = $this->makeMeal();
        $this->equipmentService->addEquipment($adventurer, $meal, 2);

        $result = $this->service->handleMeal($adventurer, $this->equipmentService);

        $this->assertTrue($result);
        $ae = $adventurer->findAdventurerEquipment($meal);
        $this->assertNotNull($ae);
        $this->assertEquals(1, $ae->getQuantity());
    }

    public function testHandleMealWithoutFoodLosesEndurance(): void
    {
        $adventurer = $this->makeAdventurer(25, 20);

        $result = $this->service->handleMeal($adventurer, $this->equipmentService);

        $this->assertFalse($result);
        $this->assertEquals(17, $adventurer->getEndurance());
    }

    public function testHandleMealEnduranceDoesNotGoBelowZero(): void
    {
        $adventurer = $this->makeAdventurer(25, 2);

        $this->service->handleMeal($adventurer, $this->equipmentService);

        $this->assertEquals(0, $adventurer->getEndurance());
    }

    // ── getCombatAbilityBonus ───────────────────────────────

    public function testCombatBonusNoSkills(): void
    {
        $adventurer = $this->makeAdventurer();

        $bonus = $this->service->getCombatAbilityBonus($adventurer);

        $this->assertEquals(0, $bonus);
    }

    public function testCombatBonusWeaponMasteryWithMasteredWeapon(): void
    {
        $adventurer = $this->makeAdventurer();
        $adventurer->addSkill($this->makeSkill('maitrise_armes'));
        $adventurer->setMasteredWeaponSlug('epee');

        $weapon = $this->makeWeapon('epee');
        $this->equipmentService->addEquipment($adventurer, $weapon);

        $bonus = $this->service->getCombatAbilityBonus($adventurer);

        $this->assertEquals(2, $bonus);
    }

    public function testCombatBonusWeaponMasteryWithoutMasteredWeapon(): void
    {
        $adventurer = $this->makeAdventurer();
        $adventurer->addSkill($this->makeSkill('maitrise_armes'));
        $adventurer->setMasteredWeaponSlug('epee');

        // A une hache, pas l'épée maîtrisée
        $weapon = $this->makeWeapon('hache');
        $this->equipmentService->addEquipment($adventurer, $weapon);

        $bonus = $this->service->getCombatAbilityBonus($adventurer);

        $this->assertEquals(0, $bonus);
    }

    public function testCombatBonusWeaponMasteryWithoutSlugSet(): void
    {
        $adventurer = $this->makeAdventurer();
        $adventurer->addSkill($this->makeSkill('maitrise_armes'));
        // masteredWeaponSlug = null

        $bonus = $this->service->getCombatAbilityBonus($adventurer);

        $this->assertEquals(0, $bonus);
    }

    public function testCombatBonusPsychicPower(): void
    {
        $adventurer = $this->makeAdventurer();
        $adventurer->addSkill($this->makeSkill('puissance_psychique'));

        $bonus = $this->service->getCombatAbilityBonus($adventurer);

        $this->assertEquals(2, $bonus);
    }

    public function testCombatBonusPsychicPowerEnemyImmune(): void
    {
        $adventurer = $this->makeAdventurer();
        $adventurer->addSkill($this->makeSkill('puissance_psychique'));

        $bonus = $this->service->getCombatAbilityBonus($adventurer, enemyImmuneToMindforce: true);

        $this->assertEquals(0, $bonus);
    }

    public function testCombatBonusBothSkillsStacked(): void
    {
        $adventurer = $this->makeAdventurer();
        $adventurer->addSkill($this->makeSkill('maitrise_armes'));
        $adventurer->addSkill($this->makeSkill('puissance_psychique'));
        $adventurer->setMasteredWeaponSlug('epee');

        $this->equipmentService->addEquipment($adventurer, $this->makeWeapon('epee'));

        $bonus = $this->service->getCombatAbilityBonus($adventurer);

        $this->assertEquals(4, $bonus);
    }

    // ── getNoWeaponPenalty ──────────────────────────────────

    public function testNoWeaponPenalty(): void
    {
        $adventurer = $this->makeAdventurer();

        $penalty = $this->service->getNoWeaponPenalty($adventurer);

        $this->assertEquals(-4, $penalty);
    }

    public function testNoWeaponPenaltyWithWeapon(): void
    {
        $adventurer = $this->makeAdventurer();
        $this->equipmentService->addEquipment($adventurer, $this->makeWeapon());

        $penalty = $this->service->getNoWeaponPenalty($adventurer);

        $this->assertEquals(0, $penalty);
    }

    // ── hasPsychicShield ────────────────────────────────────

    public function testHasPsychicShield(): void
    {
        $adventurer = $this->makeAdventurer();
        $adventurer->addSkill($this->makeSkill('bouclier_psychique'));

        $this->assertTrue($this->service->hasPsychicShield($adventurer));
    }

    public function testHasNoPsychicShield(): void
    {
        $adventurer = $this->makeAdventurer();

        $this->assertFalse($this->service->hasPsychicShield($adventurer));
    }

    // ── buildCombatModifiers ────────────────────────────────

    public function testBuildCombatModifiersDefault(): void
    {
        $adventurer = $this->makeAdventurer();
        $this->equipmentService->addEquipment($adventurer, $this->makeWeapon());

        $modifiers = $this->service->buildCombatModifiers($adventurer);

        $this->assertEquals(0, $modifiers->abilityBonus);
        $this->assertFalse($modifiers->enemyImmuneToMindforce);
        $this->assertFalse($modifiers->hasPsychicShield);
    }

    public function testBuildCombatModifiersNoWeapon(): void
    {
        $adventurer = $this->makeAdventurer();

        $modifiers = $this->service->buildCombatModifiers($adventurer);

        $this->assertEquals(-4, $modifiers->abilityBonus);
    }

    public function testBuildCombatModifiersFullBuild(): void
    {
        $adventurer = $this->makeAdventurer();
        $adventurer->addSkill($this->makeSkill('maitrise_armes'));
        $adventurer->addSkill($this->makeSkill('puissance_psychique'));
        $adventurer->addSkill($this->makeSkill('bouclier_psychique'));
        $adventurer->setMasteredWeaponSlug('epee');

        $this->equipmentService->addEquipment($adventurer, $this->makeWeapon('epee'));

        $modifiers = $this->service->buildCombatModifiers($adventurer);

        // +2 maîtrise + 2 psychique + 0 (a une arme) = +4
        $this->assertEquals(4, $modifiers->abilityBonus);
        $this->assertFalse($modifiers->enemyImmuneToMindforce);
        $this->assertTrue($modifiers->hasPsychicShield);
    }

    public function testBuildCombatModifiersWithImmunity(): void
    {
        $adventurer = $this->makeAdventurer();
        $adventurer->addSkill($this->makeSkill('puissance_psychique'));

        $this->equipmentService->addEquipment($adventurer, $this->makeWeapon());

        $modifiers = $this->service->buildCombatModifiers($adventurer, enemyImmuneToMindforce: true);

        $this->assertEquals(0, $modifiers->abilityBonus);
        $this->assertTrue($modifiers->enemyImmuneToMindforce);
    }
}
