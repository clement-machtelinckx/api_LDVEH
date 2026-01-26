<?php

namespace App\Tests\Unit;

use App\Entity\Feedback;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class FeedbackTest extends TestCase
{
    public function testSetAndGetId(): void
    {
        $feedback = new Feedback();
        $feedback->setId(10);
        $this->assertSame(10, $feedback->getId());
    }

    public function testSetAndGetEmail(): void
    {
        $feedback = new Feedback();
        $feedback->setEmail("user@example.com");
        $this->assertSame("user@example.com", $feedback->getEmail());
    }

    public function testSetAndGetMessage(): void
    {
        $feedback = new Feedback();
        $message = "This is a great game!";
        $feedback->setMessage($message);
        $this->assertSame($message, $feedback->getMessage());
    }

    public function testSetAndGetRating(): void
    {
        $feedback = new Feedback();
        $feedback->setRating(5);
        $this->assertSame(5, $feedback->getRating());
    }

    public function testSetAndGetRatingNull(): void
    {
        $feedback = new Feedback();
        $feedback->setRating(null);
        $this->assertNull($feedback->getRating());
    }

    public function testSetAndGetCreatedAt(): void
    {
        $date = new \DateTimeImmutable('2025-01-20');
        $feedback = new Feedback();
        $feedback->setCreatedAt($date);
        $this->assertSame($date, $feedback->getCreatedAt());
    }

    public function testSetAndGetStatus(): void
    {
        $feedback = new Feedback();
        $feedback->setStatus("reviewed");
        $this->assertSame("reviewed", $feedback->getStatus());
    }

    public function testDefaultStatus(): void
    {
        $feedback = new Feedback();
        $this->assertSame("new", $feedback->getStatus());
    }

    public function testSetAndGetUser(): void
    {
        $user = new User();
        $feedback = new Feedback();
        $feedback->setUser($user);
        $this->assertSame($user, $feedback->getUser());
    }

    public function testSetAndGetUserNull(): void
    {
        $feedback = new Feedback();
        $feedback->setUser(null);
        $this->assertNull($feedback->getUser());
    }
}
