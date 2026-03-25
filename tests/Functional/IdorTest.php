<?php

namespace App\Tests\Functional;

use App\Factory\AdventureFactory;
use App\Factory\AdventurerFactory;
use App\Factory\BookFactory;
use App\Factory\MonsterFactory;
use App\Factory\PageFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;

class IdorTest extends ApiTestCase
{
    use Factories;

    public function testCannotAccessAnotherUserAdventurerInPageController(): void
    {
        $user1 = UserFactory::createOne(['email' => 'user1@example.com']);
        $user2 = UserFactory::createOne(['email' => 'user2@example.com']);

        $book = BookFactory::createOne();
        $page = PageFactory::createOne(['book' => $book]);
        
        $adventurer2 = AdventurerFactory::createOne([
            'user' => $user2,
            'AdventurerName' => 'Adventurer 2',
        ]);

        AdventureFactory::createOne([
            'user' => $user2,
            'book' => $book,
            'adventurer' => $adventurer2,
            'currentPage' => $page,
            'isFinished' => false,
        ]);

        // User 1 tries to access User 2's adventurer page
        $this->browser()
            ->get('/page/' . $page->getId() . '/adventurer/' . $adventurer2->getId(), [
                'server' => $this->authHeadersFor($user1->_real())
            ])
            ->assertStatus(404); 
    }

    public function testCannotFightWithAnotherUserAdventurer(): void
    {
        $user1 = UserFactory::createOne(['email' => 'user1@example.com']);
        $user2 = UserFactory::createOne(['email' => 'user2@example.com']);

        $adventurer2 = AdventurerFactory::createOne([
            'user' => $user2,
            'AdventurerName' => 'Adventurer 2',
        ]);

        $monster = MonsterFactory::createOne();

        // User 1 tries to make User 2's adventurer fight
        $this->browser()
            ->post('/fight', [
                'server' => $this->authHeadersFor($user1->_real()),
                'json' => [
                    'adventurerId' => $adventurer2->getId(),
                    'monsterId' => $monster->getId(),
                ]
            ])
            ->assertStatus(404);
    }

    public function testCanAccessOwnAdventurer(): void
    {
        $user = UserFactory::createOne(['email' => 'user@example.com']);
        $book = BookFactory::createOne();
        $page = PageFactory::createOne(['book' => $book]);
        
        $adventurer = AdventurerFactory::createOne([
            'user' => $user,
            'AdventurerName' => 'My Adventurer',
        ]);

        AdventureFactory::createOne([
            'user' => $user,
            'book' => $book,
            'adventurer' => $adventurer,
            'currentPage' => $page,
            'isFinished' => false,
        ]);

        $this->browser()
            ->get('/page/' . $page->getId() . '/adventurer/' . $adventurer->getId(), [
                'server' => $this->authHeadersFor($user->_real())
            ])
            ->assertStatus(200)
            ->assertJsonMatches('pageId', $page->getId());
    }
}
