<?php

namespace App\Tests\Unit\Factory;

use App\Factory\AdventureFactory;
use App\Factory\AdventureHistoryFactory;
use App\Factory\AdventurerFactory;
use App\Factory\BookFactory;
use App\Factory\ChoiceFactory;
use App\Factory\EquipmentFactory;
use App\Factory\FeedbackFactory;
use App\Factory\FightHistoryFactory;
use App\Factory\MonsterFactory;
use App\Factory\PageFactory;
use App\Factory\SkillFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * Tests to verify all factories create valid entities with proper defaults
 */
class FactoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    public function testUserFactoryCreatesValidEntity(): void
    {
        $user = UserFactory::createOne();
        
        $this->assertNotNull($user->getId());
        $this->assertNotEmpty($user->getEmail());
        $this->assertStringContainsString('@', $user->getEmail());
        $this->assertNotEmpty($user->getPassword());
        // Password should be hashed (bcrypt starts with $2y$)
        $this->assertStringStartsWith('$2y$', $user->getPassword());
    }

    public function testUserFactoryWithStates(): void
    {
        $admin = UserFactory::new()->asAdmin()->create();
        $this->assertContains('ROLE_ADMIN', $admin->getRoles());

        $user = UserFactory::new()->asUser()->create();
        $this->assertContains('ROLE_USER', $user->getRoles());

        $customPassword = UserFactory::new()->withPlainPassword('test123')->create();
        $this->assertStringStartsWith('$2y$', $customPassword->getPassword());
    }

    public function testBookFactoryCreatesValidEntity(): void
    {
        $book = BookFactory::createOne();
        
        $this->assertNotNull($book->getId());
        $this->assertNotEmpty($book->getTitle());
        $this->assertNotEmpty($book->getAuthor());
        $this->assertNotEmpty($book->getDescription());
        $this->assertNotNull($book->getPublicationDate());
        $this->assertInstanceOf(\DateTimeInterface::class, $book->getPublicationDate());
    }

    public function testPageFactoryCreatesValidEntity(): void
    {
        $page = PageFactory::createOne();
        
        $this->assertNotNull($page->getId());
        $this->assertNotNull($page->getBook());
        $this->assertNotNull($page->getPageNumber());
        $this->assertNotEmpty($page->getContent());
        $this->assertIsBool($page->isCombatIsBlocking());
    }

    public function testPageFactoryWithStates(): void
    {
        $pageWithMonster = PageFactory::new()->withMonster()->create();
        $this->assertNotNull($pageWithMonster->getMonster());
        $this->assertTrue($pageWithMonster->isCombatIsBlocking());

        $victoryPage = PageFactory::new()->asVictoryEnding()->create();
        $this->assertEquals('victory', $victoryPage->getEndingType());
        $this->assertTrue($victoryPage->isVictory());

        $deathPage = PageFactory::new()->asDeathEnding()->create();
        $this->assertEquals('death', $deathPage->getEndingType());
        $this->assertFalse($deathPage->isVictory());
    }

    public function testChoiceFactoryCreatesValidEntity(): void
    {
        $choice = ChoiceFactory::createOne();
        
        $this->assertNotNull($choice->getId());
        $this->assertNotNull($choice->getPage());
        $this->assertNotEmpty($choice->getText());
        $this->assertIsBool($choice->isRequiresVictory());
        // By default, should not have nextPageNumber to avoid triggering ChoiceListener
        $this->assertNull($choice->getNextPageNumber());
    }

    public function testChoiceFactoryWithStates(): void
    {
        $choiceRequiresVictory = ChoiceFactory::new()->requiresVictory()->create();
        $this->assertTrue($choiceRequiresVictory->isRequiresVictory());
    }

    public function testAdventurerFactoryCreatesValidEntity(): void
    {
        $adventurer = AdventurerFactory::createOne();
        
        $this->assertNotNull($adventurer->getId());
        $this->assertNotEmpty($adventurer->getAdventurerName());
        $this->assertNotNull($adventurer->getUser());
        $this->assertGreaterThan(0, $adventurer->getAbility());
        $this->assertGreaterThan(0, $adventurer->getEndurance());
    }

    public function testAdventurerFactoryWithStates(): void
    {
        $strong = AdventurerFactory::new()->withHighStats()->create();
        $this->assertGreaterThanOrEqual(18, $strong->getAbility());

        $weak = AdventurerFactory::new()->withLowStats()->create();
        $this->assertLessThanOrEqual(12, $weak->getAbility());

        $custom = AdventurerFactory::new()->withStats(20, 30)->create();
        $this->assertEquals(20, $custom->getAbility());
        $this->assertEquals(30, $custom->getEndurance());
    }

    public function testMonsterFactoryCreatesValidEntity(): void
    {
        $monster = MonsterFactory::createOne();
        
        $this->assertNotNull($monster->getId());
        $this->assertNotEmpty($monster->getMonsterName());
        $this->assertGreaterThan(0, $monster->getAbility());
        $this->assertGreaterThan(0, $monster->getEndurance());
    }

    public function testMonsterFactoryWithStates(): void
    {
        $strong = MonsterFactory::new()->strong()->create();
        $this->assertGreaterThanOrEqual(16, $strong->getAbility());

        $weak = MonsterFactory::new()->weak()->create();
        $this->assertLessThanOrEqual(10, $weak->getAbility());

        $custom = MonsterFactory::new()->withStats(15, 20)->create();
        $this->assertEquals(15, $custom->getAbility());
        $this->assertEquals(20, $custom->getEndurance());
    }

    public function testAdventureFactoryCreatesValidEntity(): void
    {
        $adventure = AdventureFactory::createOne();
        
        $this->assertNotNull($adventure->getId());
        $this->assertNotNull($adventure->getUser());
        $this->assertNotNull($adventure->getAdventurer());
        $this->assertNotNull($adventure->getBook());
        $this->assertNotNull($adventure->getCurrentPage());
        
        // Check relational consistency - user and adventurer's user should be the same
        $this->assertEquals($adventure->getUser()->getId(), $adventure->getAdventurer()->getUser()->getId());
        // Check book consistency - adventure book and current page's book should be the same
        $this->assertEquals($adventure->getBook()->getId(), $adventure->getCurrentPage()->getBook()->getId());
    }

    public function testAdventureFactoryWithStates(): void
    {
        $finished = AdventureFactory::new()->finished()->create();
        $this->assertTrue($finished->isFinished());
        $this->assertNotNull($finished->getEndedAt());

        $inProgress = AdventureFactory::new()->inProgress()->create();
        $this->assertFalse($inProgress->isFinished());
        $this->assertNull($inProgress->getEndedAt());
    }

    public function testFightHistoryFactoryCreatesValidEntity(): void
    {
        $fightHistory = FightHistoryFactory::createOne();
        
        $this->assertNotNull($fightHistory->getId());
        $this->assertNotNull($fightHistory->getAdventurer());
        $this->assertNotNull($fightHistory->getMonster());
        $this->assertIsBool($fightHistory->isVictory());
    }

    public function testFightHistoryFactoryWithStates(): void
    {
        $victory = FightHistoryFactory::new()->asVictory()->create();
        $this->assertTrue($victory->isVictory());

        $defeat = FightHistoryFactory::new()->asDefeat()->create();
        $this->assertFalse($defeat->isVictory());

        $noMonster = FightHistoryFactory::new()->withoutMonster()->create();
        $this->assertNull($noMonster->getMonster());
    }

    public function testEquipmentFactoryCreatesValidEntity(): void
    {
        $equipment = EquipmentFactory::createOne();
        
        $this->assertNotNull($equipment->getId());
        $this->assertNotEmpty($equipment->getName());
        $this->assertNotEmpty($equipment->getDescription());
        $this->assertNotEmpty($equipment->getEffect());
    }

    public function testSkillFactoryCreatesValidEntity(): void
    {
        $skill = SkillFactory::createOne();
        
        $this->assertNotNull($skill->getId());
        $this->assertNotEmpty($skill->getName());
        $this->assertNotEmpty($skill->getDescription());
        $this->assertNotEmpty($skill->getEffect());
    }

    public function testFeedbackFactoryCreatesValidEntity(): void
    {
        $feedback = FeedbackFactory::createOne();
        
        $this->assertNotNull($feedback->getId());
        $this->assertNotNull($feedback->getUser());
        $this->assertNotEmpty($feedback->getEmail());
        $this->assertStringContainsString('@', $feedback->getEmail());
        $this->assertNotEmpty($feedback->getMessage());
        $this->assertGreaterThanOrEqual(1, $feedback->getRating());
        $this->assertLessThanOrEqual(5, $feedback->getRating());
        $this->assertNotNull($feedback->getCreatedAt());
        $this->assertEquals('new', $feedback->getStatus());
    }

    public function testFeedbackFactoryWithStates(): void
    {
        $processed = FeedbackFactory::new()->asProcessed()->create();
        $this->assertEquals('processed', $processed->getStatus());

        $resolved = FeedbackFactory::new()->asResolved()->create();
        $this->assertEquals('resolved', $resolved->getStatus());

        $high = FeedbackFactory::new()->withHighRating()->create();
        $this->assertGreaterThanOrEqual(4, $high->getRating());

        $low = FeedbackFactory::new()->withLowRating()->create();
        $this->assertLessThanOrEqual(2, $low->getRating());
    }

    public function testAdventureHistoryFactoryCreatesValidEntity(): void
    {
        $history = AdventureHistoryFactory::createOne();
        
        $this->assertNotNull($history->getId());
        $this->assertNotNull($history->getUser());
        $this->assertNotNull($history->getBook());
        $this->assertNotEmpty($history->getBookTitle());
        $this->assertNotEmpty($history->getAdventurerName());
        $this->assertNotNull($history->getFinishAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $history->getFinishAt());
    }

    public function testMultiplePagesForSameBookHaveUniquePageNumbers(): void
    {
        $book = BookFactory::createOne();
        
        $page1 = PageFactory::createOne(['book' => $book]);
        $page2 = PageFactory::createOne(['book' => $book]);
        $page3 = PageFactory::createOne(['book' => $book]);
        
        // All pages should have different page numbers
        $pageNumbers = [
            $page1->getPageNumber(),
            $page2->getPageNumber(),
            $page3->getPageNumber(),
        ];
        
        $this->assertCount(3, array_unique($pageNumbers), 'Page numbers should be unique for the same book');
    }
}
