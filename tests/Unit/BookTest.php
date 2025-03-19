<?php

namespace App\Tests\Entity;

use App\Entity\Book;
use App\Factory\PageFactory;
use PHPUnit\Framework\TestCase;
use Zenstruck\Foundry\Test\Factories;

class BookTest extends TestCase
{
    use Factories;

    public function testGettersAndSetters(): void
    {
        $book = new Book();

        // Test setId and getId
        $book->setId(1);
        $this->assertEquals(1, $book->getId());

        // Test setTitle and getTitle
        $book->setTitle('The Hobbit');
        $this->assertEquals('The Hobbit', $book->getTitle());

        // Test setAuthor and getAuthor
        $book->setAuthor('J.R.R. Tolkien');
        $this->assertEquals('J.R.R. Tolkien', $book->getAuthor());

        // Test setDescription and getDescription
        $book->setDescription('A great adventure.');
        $this->assertEquals('A great adventure.', $book->getDescription());

        // Test setPublicationDate and getPublicationDate
        $publicationDate = new \DateTime();
        $book->setPublicationDate($publicationDate);
        $this->assertEquals($publicationDate, $book->getPublicationDate());

        // Test setCreatedAt and getCreatedAt
        $createdAt = new \DateTimeImmutable();
        $book->setCreatedAt($createdAt);
        $this->assertEquals($createdAt, $book->getCreatedAt());

        // Test setUpdatedAt and getUpdatedAt
        $updatedAt = new \DateTimeImmutable();
        $book->setUpdatedAt($updatedAt);
        $this->assertEquals($updatedAt, $book->getUpdatedAt());
    }

    public function testLifecycleCallbacks(): void
    {
        $book = new Book();

        // Simulate prePersist callback
        $book->onPrePersist();
        $this->assertNotNull($book->getCreatedAt());
        $this->assertNotNull($book->getUpdatedAt());

        // Simulate preUpdate callback
        $book->onPreUpdate();
        $this->assertNotNull($book->getUpdatedAt());
    }

    public function testPageCollection(): void
    {
        $book = new Book();
        $page = PageFactory::createOne();

        // Test addPage
        $book->addPage($page);
        $this->assertCount(1, $book->getPage());
        $this->assertTrue($book->getPage()->contains($page));

        // Test removePage
        $book->removePage($page);
        $this->assertCount(0, $book->getPage());
        $this->assertFalse($book->getPage()->contains($page));
    }

    public function testToString(): void
    {
        $book = new Book();
        $book->setTitle('The Hobbit');
        $this->assertEquals('The Hobbit', (string) $book);
    }
}
