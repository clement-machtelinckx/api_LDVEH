<?php

namespace App\Tests\Unit;

use App\Entity\User;
use App\Entity\Adventurer;
use App\Entity\Adventure;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testSetAndGetEmail(): void
    {
        $user = new User();
        $user->setEmail("test@example.com");
        $this->assertSame("test@example.com", $user->getEmail());
    }

    public function testUserIdentifier(): void
    {
        $user = new User();
        $user->setEmail("joueur@donjon.com");
        $this->assertSame("joueur@donjon.com", $user->getUserIdentifier());
    }

    public function testSetAndGetPassword(): void
    {
        $user = new User();
        $user->setPassword("hashed-password");
        $this->assertSame("hashed-password", $user->getPassword());
    }

    public function testSetAndGetRoles(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        $roles = $user->getRoles();

        $this->assertContains('ROLE_ADMIN', $roles);
        $this->assertContains('ROLE_USER', $roles); // Toujours présent
        $this->assertCount(2, array_unique($roles));
    }

    public function testAddAndRemoveAdventurer(): void
    {
        $user = new User();
        $adventurer = new Adventurer();

        $user->addAdventurer($adventurer);
        $this->assertCount(1, $user->getAdventurers());
        $this->assertSame($user, $adventurer->getUser());

        $user->removeAdventurer($adventurer);
        $this->assertCount(0, $user->getAdventurers());
        $this->assertNull($adventurer->getUser());
    }

    public function testAddAndRemoveAdventure(): void
    {
        $user = new User();
        $adventure = new Adventure();

        $user->addAdventure($adventure);
        $this->assertCount(1, $user->getAdventures());
        $this->assertSame($user, $adventure->getUser());

        $user->removeAdventure($adventure);
        $this->assertCount(0, $user->getAdventures());
        $this->assertNull($adventure->getUser());
    }

    public function testToStringReturnsEmail(): void
    {
        $user = new User();
        $user->setEmail("display@user.com");
        $this->assertSame("display@user.com", (string)$user);
    }
}
