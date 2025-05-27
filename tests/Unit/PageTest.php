<?php

namespace App\Tests\Unit;

use App\Entity\Page;
use App\Entity\Book;
use App\Entity\Monster;
use App\Entity\Choice;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{
    public function testSetAndGetId(): void
    {
        $page = new Page();
        $ref = new \ReflectionClass($page);
        $property = $ref->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($page, 42);

        $this->assertSame(42, $page->getId());
    }

    public function testSetAndGetContent(): void
    {
        $page = new Page();
        $page->setContent("Vous entrez dans une grotte sombre...");
        $this->assertSame("Vous entrez dans une grotte sombre...", $page->getContent());
    }

    public function testSetAndGetBook(): void
    {
        $book = new Book();
        $page = new Page();
        $page->setBook($book);
        $this->assertSame($book, $page->getBook());
    }

    public function testSetAndGetPageNumber(): void
    {
        $page = new Page();
        $page->setPageNumber(13);
        $this->assertSame(13, $page->getPageNumber());
    }

    public function testSetAndGetMonster(): void
    {
        $monster = new Monster();
        $page = new Page();
        $page->setMonster($monster);
        $this->assertSame($monster, $page->getMonster());
    }

    public function testCombatIsBlocking(): void
    {
        $page = new Page();
        $page->setCombatIsBlocking(true);
        $this->assertTrue($page->isCombatIsBlocking());

        $page->setCombatIsBlocking(false);
        $this->assertFalse($page->isCombatIsBlocking());

        $page->setCombatIsBlocking(null);
        $this->assertNull($page->isCombatIsBlocking());
    }

    public function testSetAndGetEndingType(): void
    {
        $page = new Page();
        $page->setEndingType('death');
        $this->assertSame('death', $page->getEndingType());
    }

    public function testIsEnding(): void
    {
        $page = new Page();
        $this->assertFalse($page->isEnding());

        $page->setEndingType('victory');
        $this->assertTrue($page->isEnding());
    }

    public function testIsVictory(): void
    {
        $page = new Page();

        $page->setEndingType('victory');
        $this->assertTrue($page->isVictory());

        $page->setEndingType('death');
        $this->assertFalse($page->isVictory());

        $page->setEndingType(null);
        $this->assertFalse($page->isVictory());
    }

    public function testAddAndRemoveChoice(): void
    {
        $page = new Page();
        $choice = new Choice();

        $page->addChoice($choice);
        $this->assertCount(1, $page->getChoices());
        $this->assertSame($page, $choice->getPage());

        $page->removeChoice($choice);
        $this->assertCount(0, $page->getChoices());
        $this->assertNull($choice->getPage());
    }

    public function testToStringWithId(): void
    {
        $page = new Page();
        $ref = new \ReflectionClass($page);
        $property = $ref->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($page, 9);

        $this->assertSame("9", (string)$page);
    }
}
