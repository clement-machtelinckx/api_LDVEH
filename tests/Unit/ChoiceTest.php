<?php

namespace App\Tests\Unit;

use App\Entity\Choice;
use App\Entity\Page;
use PHPUnit\Framework\TestCase;

class ChoiceTest extends TestCase
{
    public function testSetAndGetId(): void
    {
        $choice = new Choice();
        $choice->setId(42);
        $this->assertSame(42, $choice->getId());
    }

    public function testSetAndGetText(): void
    {
        $choice = new Choice();
        $choice->setText("Aller à la page 32");
        $this->assertSame("Aller à la page 32", $choice->getText());
    }

    public function testSetAndGetPage(): void
    {
        $page = new Page();
        $choice = new Choice();
        $choice->setPage($page);
        $this->assertSame($page, $choice->getPage());
    }

    public function testSetAndGetNextPage(): void
    {
        $nextPage = new Page();
        $choice = new Choice();
        $choice->setNextPage($nextPage);
        $this->assertSame($nextPage, $choice->getNextPage());
    }

    public function testSetAndGetNextPageNumberTemporary(): void
    {
        $choice = new Choice();
        $choice->setNextPageNumber(77);
        $this->assertSame(77, $choice->getNextPageNumber());
    }

    public function testGetNextPageNumberFromNextPage(): void
    {
        $page = $this->createMock(Page::class);
        $page->method('getPageNumber')->willReturn(12);

        $choice = new Choice();
        $choice->setNextPage($page);

        $this->assertSame(12, $choice->getNextPageNumber());
    }

    public function testSetAndGetRequiresVictory(): void
    {
        $choice = new Choice();
        $choice->setRequiresVictory(true);
        $this->assertTrue($choice->isRequiresVictory());

        $choice->setRequiresVictory(false);
        $this->assertFalse($choice->isRequiresVictory());

        $choice->setRequiresVictory(null);
        $this->assertNull($choice->isRequiresVictory());
    }


    public function testToStringReturnsIdAsString(): void
    {
        $choice = new Choice();
        $choice->setId(88);
        $this->assertSame("88", (string) $choice);
    }
}
