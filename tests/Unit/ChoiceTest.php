<?php

namespace App\Tests\Entity;

use App\Entity\Choice;
use App\Entity\Page;
use PHPUnit\Framework\TestCase;
use Zenstruck\Foundry\Test\Factories;

class ChoiceTest extends TestCase
{
    use Factories;

    public function testGettersAndSetters(): void
    {
        $choice = new Choice();

        // Test setId and getId
        $choice->setId(1);
        $this->assertEquals(1, $choice->getId());

        // Test setText and getText
        $choice->setText('Sample text');
        $this->assertEquals('Sample text', $choice->getText());

        // Test setNextPageNumber and getNextPageNumber
        $choice->setNextPageNumber(10);
        $this->assertEquals(10, $choice->getNextPageNumber());
    }

    public function testPageRelationship(): void
    {
        $page = new Page();
        $choice = new Choice();

        // Test setPage and getPage
        $choice->setPage($page);
        $this->assertSame($page, $choice->getPage());
    }

    public function testNextPageRelationship(): void
    {
        $nextPage = new Page();
        $choice = new Choice();

        // Test setNextPage and getNextPage
        $choice->setNextPage($nextPage);
        $this->assertSame($nextPage, $choice->getNextPage());

        // Test getNextPageNumber when nextPage is set
        $nextPage->setPageNumber(20);
        $this->assertEquals(20, $choice->getNextPageNumber());
    }

    public function testToString(): void
    {
        $choice = new Choice();
        $choice->setId(1);
        $this->assertEquals('1', (string) $choice);
    }
}
