<?php

namespace App\Tests\Unit;

use App\Entity\Book;
use App\Entity\Page;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
    public function testSetAndGetId(): void
    {
        $book = new Book();
        $book->setId(5);
        $this->assertSame(5, $book->getId());
    }

    public function testSetAndGetTitle(): void
    {
        $book = new Book();
        $book->setTitle("La traversée infernale");
        $this->assertSame("La traversée infernale", $book->getTitle());
    }

    public function testSetAndGetAuthor(): void
    {
        $book = new Book();
        $book->setAuthor("Joe Dever");
        $this->assertSame("Joe Dever", $book->getAuthor());
    }

    public function testSetAndGetDescription(): void
    {
        $book = new Book();
        $desc = "Deuxième volume de la saga de Loup Solitaire.";
        $book->setDescription($desc);
        $this->assertSame($desc, $book->getDescription());
    }

    public function testSetAndGetPublicationDate(): void
    {
        $date = new \DateTimeImmutable("1985-05-01");
        $book = new Book();
        $book->setPublicationDate($date);
        $this->assertSame($date, $book->getPublicationDate());
    }

    public function testPrePersistSetsDates(): void
    {
        $book = new Book();
        $book->onPrePersist();

        $this->assertInstanceOf(\DateTimeImmutable::class, $book->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $book->getUpdatedAt());
    }

    public function testPreUpdateUpdatesUpdatedAt(): void
    {
        $book = new Book();
        $book->onPrePersist(); // Set initial value
        sleep(1);
        $book->onPreUpdate();

        $this->assertGreaterThan(
            $book->getCreatedAt()->getTimestamp(),
            $book->getUpdatedAt()->getTimestamp()
        );
    }

    public function testAddAndRemovePage(): void
    {
        $book = new Book();
        $page = new Page();

        $book->addPage($page);
        $this->assertCount(1, $book->getPage());
        $this->assertSame($book, $page->getBook());

        $book->removePage($page);
        $this->assertCount(0, $book->getPage());
        $this->assertNull($page->getBook());
    }

    public function testToStringReturnsTitle(): void
    {
        $book = new Book();
        $book->setTitle("Le Gouffre Maudit");
        $this->assertSame("Le Gouffre Maudit", (string)$book);
    }
}
