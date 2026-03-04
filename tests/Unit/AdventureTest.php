<?php

namespace App\Tests\Unit;

use App\Entity\Adventure;
use App\Entity\Adventurer;
use App\Entity\Book;
use App\Entity\Page;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class AdventureTest extends TestCase
{
    public function testConstructorSetsStartedAt(): void
    {
        $adventure = new Adventure();
        $this->assertInstanceOf(\DateTimeImmutable::class, $adventure->getStartedAt());
    }

    public function testSetAndGetUser(): void
    {
        $user = new User();
        $adventure = new Adventure();
        $adventure->setUser($user);
        $this->assertSame($user, $adventure->getUser());
    }

    public function testSetAndGetBook(): void
    {
        $book = new Book();
        $adventure = new Adventure();
        $adventure->setBook($book);
        $this->assertSame($book, $adventure->getBook());
    }

    public function testSetAndGetAdventurer(): void
    {
        $adventurer = new Adventurer();
        $adventure = new Adventure();
        $adventure->setAdventurer($adventurer);
        $this->assertSame($adventurer, $adventure->getAdventurer());
    }

    public function testSetAndGetCurrentPage(): void
    {
        $page = new Page();
        $adventure = new Adventure();
        $adventure->setCurrentPage($page);
        $this->assertSame($page, $adventure->getCurrentPage());
    }

    public function testSetAndGetFromLastPage(): void
    {
        $page = new Page();
        $adventure = new Adventure();
        $adventure->setFromLastPage($page);
        $this->assertSame($page, $adventure->getFromLastPage());
    }

    public function testSetAndGetStartedAt(): void
    {
        $date = new \DateTimeImmutable('2025-01-01');
        $adventure = new Adventure();
        $adventure->setStartedAt($date);
        $this->assertSame($date, $adventure->getStartedAt());
    }

    public function testSetAndGetEndedAt(): void
    {
        $date = new \DateTimeImmutable('2025-01-02');
        $adventure = new Adventure();
        $adventure->setEndedAt($date);
        $this->assertSame($date, $adventure->getEndedAt());
    }

    public function testSetAndGetIsFinished(): void
    {
        $adventure = new Adventure();

        $adventure->setIsFinished(true);
        $this->assertTrue($adventure->isFinished());

        $adventure->setIsFinished(false);
        $this->assertFalse($adventure->isFinished());

        $adventure->setIsFinished(null);
        $this->assertNull($adventure->isFinished());
    }
}
