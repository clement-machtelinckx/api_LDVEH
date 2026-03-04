<?php

namespace App\Tests\Unit;

use App\Entity\AdventureHistory;
use App\Entity\Book;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class AdventureHistoryTest extends TestCase
{
    public function testSetAndGetId(): void
    {
        $history = new AdventureHistory();
        $history->setId(42);
        $this->assertSame(42, $history->getId());
    }

    public function testSetAndGetBookTitle(): void
    {
        $history = new AdventureHistory();
        $history->setBookTitle("La traversée infernale");
        $this->assertSame("La traversée infernale", $history->getBookTitle());
    }

    public function testSetAndGetBookTitleNull(): void
    {
        $history = new AdventureHistory();
        $history->setBookTitle(null);
        $this->assertNull($history->getBookTitle());
    }

    public function testSetAndGetAdventurerName(): void
    {
        $history = new AdventureHistory();
        $history->setAdventurerName("Loup Solitaire");
        $this->assertSame("Loup Solitaire", $history->getAdventurerName());
    }

    public function testSetAndGetFinishAt(): void
    {
        $date = new \DateTimeImmutable('2025-01-15');
        $history = new AdventureHistory();
        $history->setFinishAt($date);
        $this->assertSame($date, $history->getFinishAt());
    }

    public function testSetAndGetBook(): void
    {
        $book = new Book();
        $history = new AdventureHistory();
        $history->setBook($book);
        $this->assertSame($book, $history->getBook());
    }

    public function testSetAndGetBookNull(): void
    {
        $history = new AdventureHistory();
        $history->setBook(null);
        $this->assertNull($history->getBook());
    }

    public function testSetAndGetUser(): void
    {
        $user = new User();
        $history = new AdventureHistory();
        $history->setUser($user);
        $this->assertSame($user, $history->getUser());
    }

    public function testSetAndGetUserNull(): void
    {
        $history = new AdventureHistory();
        $history->setUser(null);
        $this->assertNull($history->getUser());
    }
}
