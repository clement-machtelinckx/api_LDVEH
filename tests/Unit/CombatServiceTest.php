<?php

namespace App\Tests\Unit;

use App\Entity\Adventurer;
use App\Entity\FightHistory;
use App\Entity\Monster;
use App\Entity\Page;
use App\Repository\FightHistoryRepository;
use App\Service\CombatService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class CombatServiceTest extends TestCase
{
    private FightHistoryRepository $fightHistoryRepo;
    private EntityManagerInterface $em;
    private CombatService $combatService;

    protected function setUp(): void
    {
        $this->fightHistoryRepo = $this->createMock(FightHistoryRepository::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->combatService = new CombatService($this->fightHistoryRepo, $this->em);
    }

    public function testCanAccessPageWhenNoMonster(): void
    {
        $page = new Page();
        $page->setMonster(null);
        $page->setCombatIsBlocking(true);

        $adventurer = new Adventurer();

        $result = $this->combatService->canAccessPage($page, $adventurer);
        $this->assertTrue($result);
    }

    public function testCanAccessPageWhenCombatNotBlocking(): void
    {
        $monster = new Monster();
        $page = new Page();
        $page->setMonster($monster);
        $page->setCombatIsBlocking(false);

        $adventurer = new Adventurer();

        $result = $this->combatService->canAccessPage($page, $adventurer);
        $this->assertTrue($result);
    }

    public function testCanAccessPageWhenCombatBlockingNull(): void
    {
        $monster = new Monster();
        $page = new Page();
        $page->setMonster($monster);
        $page->setCombatIsBlocking(null);

        $adventurer = new Adventurer();

        $result = $this->combatService->canAccessPage($page, $adventurer);
        $this->assertTrue($result);
    }

    public function testCanAccessPageWhenBlockingCombatNotDefeated(): void
    {
        $monster = new Monster();
        $page = new Page();
        $page->setMonster($monster);
        $page->setCombatIsBlocking(true);

        $adventurer = new Adventurer();

        // Mock repository to return null (monster not defeated)
        $this->fightHistoryRepo
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'adventurer' => $adventurer,
                'monster' => $monster,
                'victory' => true,
            ])
            ->willReturn(null);

        $result = $this->combatService->canAccessPage($page, $adventurer);
        $this->assertFalse($result);
    }

    public function testCanAccessPageWhenBlockingCombatDefeated(): void
    {
        $monster = new Monster();
        $page = new Page();
        $page->setMonster($monster);
        $page->setCombatIsBlocking(true);

        $adventurer = new Adventurer();
        $fightHistory = new FightHistory();

        // Mock repository to return a fight history (monster defeated)
        $this->fightHistoryRepo
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'adventurer' => $adventurer,
                'monster' => $monster,
                'victory' => true,
            ])
            ->willReturn($fightHistory);

        $result = $this->combatService->canAccessPage($page, $adventurer);
        $this->assertTrue($result);
    }

    public function testHasDefeatedReturnsFalseWhenNoHistory(): void
    {
        $adventurer = new Adventurer();
        $monster = new Monster();

        $this->fightHistoryRepo
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'adventurer' => $adventurer,
                'monster' => $monster,
                'victory' => true,
            ])
            ->willReturn(null);

        $result = $this->combatService->hasDefeated($adventurer, $monster);
        $this->assertFalse($result);
    }

    public function testHasDefeatedReturnsTrueWhenHistoryExists(): void
    {
        $adventurer = new Adventurer();
        $monster = new Monster();
        $fightHistory = new FightHistory();

        $this->fightHistoryRepo
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'adventurer' => $adventurer,
                'monster' => $monster,
                'victory' => true,
            ])
            ->willReturn($fightHistory);

        $result = $this->combatService->hasDefeated($adventurer, $monster);
        $this->assertTrue($result);
    }

    public function testRecordFightPersistsAndFlushes(): void
    {
        $adventurer = new Adventurer();
        $monster = new Monster();
        $victory = true;

        $this->em
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($fightHistory) use ($adventurer, $monster, $victory) {
                return $fightHistory instanceof FightHistory
                    && $fightHistory->getAdventurer() === $adventurer
                    && $fightHistory->getMonster() === $monster
                    && $fightHistory->isVictory() === $victory;
            }));

        $this->em
            ->expects($this->once())
            ->method('flush');

        $this->combatService->recordFight($adventurer, $monster, $victory);
    }

    public function testMapQaToBandBoundaries(): void
    {
        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->combatService);
        $method = $reflection->getMethod('mapQaToBand');
        $method->setAccessible(true);

        // Test boundary values
        $this->assertEquals(-11, $method->invoke($this->combatService, -15)); // <= -11
        $this->assertEquals(-11, $method->invoke($this->combatService, -11));
        
        $this->assertEquals(-10, $method->invoke($this->combatService, -10)); // -10/-9
        $this->assertEquals(-10, $method->invoke($this->combatService, -9));
        
        $this->assertEquals(-8, $method->invoke($this->combatService, -8)); // -8/-7
        $this->assertEquals(-8, $method->invoke($this->combatService, -7));
        
        $this->assertEquals(-6, $method->invoke($this->combatService, -6)); // -6/-5
        $this->assertEquals(-6, $method->invoke($this->combatService, -5));
        
        $this->assertEquals(-4, $method->invoke($this->combatService, -4)); // -4/-3
        $this->assertEquals(-4, $method->invoke($this->combatService, -3));
        
        $this->assertEquals(-2, $method->invoke($this->combatService, -2)); // -2/-1
        $this->assertEquals(-2, $method->invoke($this->combatService, -1));
        
        $this->assertEquals(0, $method->invoke($this->combatService, 0));   // 0
        
        $this->assertEquals(1, $method->invoke($this->combatService, 1));   // +1/+2
        $this->assertEquals(1, $method->invoke($this->combatService, 2));
        
        $this->assertEquals(3, $method->invoke($this->combatService, 3));   // +3/+4
        $this->assertEquals(3, $method->invoke($this->combatService, 4));
        
        $this->assertEquals(5, $method->invoke($this->combatService, 5));   // +5/+6
        $this->assertEquals(5, $method->invoke($this->combatService, 6));
        
        $this->assertEquals(7, $method->invoke($this->combatService, 7));   // +7/+8
        $this->assertEquals(7, $method->invoke($this->combatService, 8));
        
        $this->assertEquals(9, $method->invoke($this->combatService, 9));   // +9/+10
        $this->assertEquals(9, $method->invoke($this->combatService, 10));
        
        $this->assertEquals(11, $method->invoke($this->combatService, 11));  // >= +11
        $this->assertEquals(11, $method->invoke($this->combatService, 15));
    }
}
