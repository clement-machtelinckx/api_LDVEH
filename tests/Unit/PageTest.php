<?php

namespace App\Tests\Entity;

use App\Entity\Page;
use App\Entity\Book;
use App\Entity\Choice;
use PHPUnit\Framework\TestCase;
use Zenstruck\Foundry\Test\Factories;

class PageTest extends TestCase
{
    use Factories;

    public function testGettersAndSetters(): void
    {
        $page = new Page();

        // Test setId and getId
        $page->setId(1);
        $this->assertEquals(1, $page->getId());

        // Test setContent and getContent
        $page->setContent('Sample content');
        $this->assertEquals('Sample content', $page->getContent());

        // Test setPageNumber and getPageNumber
        $page->setPageNumber(10);
        $this->assertEquals(10, $page->getPageNumber());
    }

    public function testBookRelationship(): void
    {
        $book = new Book();
        $page = new Page();

        // Test setBook and getBook
        $page->setBook($book);
        $this->assertSame($book, $page->getBook());
    }

    public function testChoicesCollection(): void
    {
        $page = new Page();
        $choice = new Choice();

        // Test addChoice
        $page->addChoice($choice);
        $this->assertCount(1, $page->getChoices());
        $this->assertTrue($page->getChoices()->contains($choice));

        // Test removeChoice
        $page->removeChoice($choice);
        $this->assertCount(0, $page->getChoices());
        $this->assertFalse($page->getChoices()->contains($choice));
    }

    public function testToString(): void
    {
        $page = new Page();
        $page->setId(1);
        $this->assertEquals('1', (string) $page);
    }
}
