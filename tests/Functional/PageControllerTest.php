<?php

namespace App\Tests\Functional;

use App\Factory\AdventureFactory;
use App\Factory\AdventurerFactory;
use App\Factory\BookFactory;
use App\Factory\ChoiceFactory;
use App\Factory\FightHistoryFactory;
use App\Factory\MonsterFactory;
use App\Factory\PageFactory;
use App\Factory\UserFactory;

class PageControllerTest extends ApiTestCase
{
    public function testViewPageRequiresAuthentication(): void
    {
        $this->browser()
            ->get('/page/1/adventurer/1')
            ->assertStatus(401);
    }

    public function testViewPageReturns404WhenAdventurerNotFound(): void
    {
        $user = UserFactory::createOne(['email' => 'test@example.com']);
        
        $this->browser()
            ->get('/page/1/adventurer/999999', ['server' => $this->authHeadersFor($user->_real())])
            ->assertStatus(404)
            ->assertJsonMatches('error', 'Aventurier ou page cible introuvable');
    }

    public function testViewPageReturns404WhenPageNotFound(): void
    {
        $user = UserFactory::createOne(['email' => 'test@example.com']);
        $adventurer = AdventurerFactory::createOne([
            'user' => $user,
            'AdventurerName' => 'Loup Solitaire',
            'Ability' => 15,
            'Endurance' => 25,
        ]);
        
        $this->browser()
            ->get('/page/999999/adventurer/' . $adventurer->getId(), ['server' => $this->authHeadersFor($user->_real())])
            ->assertStatus(404)
            ->assertJsonMatches('error', 'Aventurier ou page cible introuvable');
    }

    public function testViewPageReturns404WhenNoActiveAdventure(): void
    {
        $user = UserFactory::createOne(['email' => 'test@example.com']);
        $book = BookFactory::createOne(['title' => 'Test Book']);
        $page = PageFactory::createOne(['book' => $book, 'pageNumber' => 1, 'content' => 'Test content']);
        $adventurer = AdventurerFactory::createOne([
            'user' => $user,
            'AdventurerName' => 'Loup Solitaire',
            'Ability' => 15,
            'Endurance' => 25,
        ]);
        
        // No adventure created, or all are finished
        
        $this->browser()
            ->get('/page/' . $page->getId() . '/adventurer/' . $adventurer->getId(), 
                ['server' => $this->authHeadersFor($user->_real())])
            ->assertStatus(404)
            ->assertJsonMatches('error', 'Aucune aventure en cours pour cet aventurier');
    }

    public function testViewPageReturns404WhenFromPageNotFound(): void
    {
        $user = UserFactory::createOne(['email' => 'test@example.com']);
        $book = BookFactory::createOne(['title' => 'Test Book']);
        $page = PageFactory::createOne(['book' => $book, 'pageNumber' => 1, 'content' => 'Test content']);
        $adventurer = AdventurerFactory::createOne([
            'user' => $user,
            'AdventurerName' => 'Loup Solitaire',
            'Ability' => 15,
            'Endurance' => 25,
        ]);
        
        // Create active adventure
        AdventureFactory::createOne([
            'user' => $user,
            'book' => $book,
            'adventurer' => $adventurer,
            'currentPage' => $page,
            'isFinished' => false,
        ]);
        
        $this->browser()
            ->get('/page/' . $page->getId() . '/adventurer/' . $adventurer->getId() . '/from/999999', 
                ['server' => $this->authHeadersFor($user->_real())])
            ->assertStatus(404)
            ->assertJsonMatches('error', 'Page précédente introuvable');
    }

    public function testViewPageReturns403WhenPageNotAccessibleFromChoices(): void
    {
        $user = UserFactory::createOne(['email' => 'test@example.com']);
        $book = BookFactory::createOne(['title' => 'Test Book']);
        
        $fromPage = PageFactory::createOne(['book' => $book, 'pageNumber' => 1, 'content' => 'From page']);
        $targetPage = PageFactory::createOne(['book' => $book, 'pageNumber' => 2, 'content' => 'Target page']);
        $otherPage = PageFactory::createOne(['book' => $book, 'pageNumber' => 3, 'content' => 'Other page']);
        
        // Create choice that does NOT point to targetPage
        ChoiceFactory::createOne([
            'page' => $fromPage,
            'nextPage' => $otherPage,
            'text' => 'Go to other page',
        ]);
        
        $adventurer = AdventurerFactory::createOne([
            'user' => $user,
            'AdventurerName' => 'Loup Solitaire',
            'Ability' => 15,
            'Endurance' => 25,
        ]);
        
        AdventureFactory::createOne([
            'user' => $user,
            'book' => $book,
            'adventurer' => $adventurer,
            'currentPage' => $fromPage,
            'isFinished' => false,
        ]);
        
        $this->browser()
            ->get('/page/' . $targetPage->getId() . '/adventurer/' . $adventurer->getId() . '/from/' . $fromPage->getId(), 
                ['server' => $this->authHeadersFor($user->_real())])
            ->assertStatus(403)
            ->assertJsonMatches('fromPageId', $fromPage->getId())
            ->assertJsonMatches('requestedPageId', $targetPage->getId());
    }

    public function testViewPageReturns403WhenBlockingCombatNotDefeated(): void
    {
        $user = UserFactory::createOne(['email' => 'test@example.com']);
        $book = BookFactory::createOne(['title' => 'Test Book']);
        
        $monster = MonsterFactory::createOne([
            'monsterName' => 'Golem',
            'ability' => 15,
            'endurance' => 10,
        ]);
        
        $fromPage = PageFactory::createOne([
            'book' => $book, 
            'pageNumber' => 1, 
            'content' => 'From page',
            'monster' => $monster,
            'combatIsBlocking' => true,
        ]);
        
        $targetPage = PageFactory::createOne(['book' => $book, 'pageNumber' => 2, 'content' => 'Target page']);
        
        // Create choice pointing to targetPage
        ChoiceFactory::createOne([
            'page' => $fromPage,
            'nextPage' => $targetPage,
            'text' => 'Continue',
        ]);
        
        $adventurer = AdventurerFactory::createOne([
            'user' => $user,
            'AdventurerName' => 'Loup Solitaire',
            'Ability' => 15,
            'Endurance' => 25,
        ]);
        
        AdventureFactory::createOne([
            'user' => $user,
            'book' => $book,
            'adventurer' => $adventurer,
            'currentPage' => $fromPage,
            'isFinished' => false,
        ]);
        
        // No FightHistory created, so monster not defeated
        
        $this->browser()
            ->get('/page/' . $targetPage->getId() . '/adventurer/' . $adventurer->getId() . '/from/' . $fromPage->getId(), 
                ['server' => $this->authHeadersFor($user->_real())])
            ->assertStatus(403)
            ->assertJsonMatches('error', 'Vous devez vaincre le monstre pour continuer.')
            ->assertJsonMatches('monsterId', $monster->getId())
            ->assertJsonMatches('monsterName', 'Golem');
    }

    public function testViewPageReturns200WhenAccessibleWithoutFromPage(): void
    {
        $user = UserFactory::createOne(['email' => 'test@example.com']);
        $book = BookFactory::createOne(['title' => 'Test Book']);
        $page = PageFactory::createOne([
            'book' => $book, 
            'pageNumber' => 1, 
            'content' => 'Test content',
        ]);
        
        $adventurer = AdventurerFactory::createOne([
            'user' => $user,
            'AdventurerName' => 'Loup Solitaire',
            'Ability' => 15,
            'Endurance' => 25,
        ]);
        
        AdventureFactory::createOne([
            'user' => $user,
            'book' => $book,
            'adventurer' => $adventurer,
            'currentPage' => $page,
            'isFinished' => false,
        ]);
        
        $this->browser()
            ->get('/page/' . $page->getId() . '/adventurer/' . $adventurer->getId(), 
                ['server' => $this->authHeadersFor($user->_real())])
            ->assertStatus(200)
            ->assertJsonMatches('pageId', $page->getId())
            ->assertJsonMatches('pageNumber', 1)
            ->assertJsonMatches('content', 'Test content');
    }

    public function testViewPageReturns200AfterCombatVictory(): void
    {
        $user = UserFactory::createOne(['email' => 'test@example.com']);
        $book = BookFactory::createOne(['title' => 'Test Book']);
        
        $monster = MonsterFactory::createOne([
            'monsterName' => 'Golem',
            'ability' => 15,
            'endurance' => 10,
        ]);
        
        $fromPage = PageFactory::createOne([
            'book' => $book, 
            'pageNumber' => 1, 
            'content' => 'From page',
            'monster' => $monster,
            'combatIsBlocking' => true,
        ]);
        
        $targetPage = PageFactory::createOne(['book' => $book, 'pageNumber' => 2, 'content' => 'Target page']);
        
        ChoiceFactory::createOne([
            'page' => $fromPage,
            'nextPage' => $targetPage,
            'text' => 'Continue',
        ]);
        
        $adventurer = AdventurerFactory::createOne([
            'user' => $user,
            'AdventurerName' => 'Loup Solitaire',
            'Ability' => 15,
            'Endurance' => 25,
        ]);
        
        AdventureFactory::createOne([
            'user' => $user,
            'book' => $book,
            'adventurer' => $adventurer,
            'currentPage' => $fromPage,
            'isFinished' => false,
        ]);
        
        // Create fight history with victory
        FightHistoryFactory::createOne([
            'adventurer' => $adventurer,
            'monster' => $monster,
            'victory' => true,
        ]);
        
        $this->browser()
            ->get('/page/' . $targetPage->getId() . '/adventurer/' . $adventurer->getId() . '/from/' . $fromPage->getId(), 
                ['server' => $this->authHeadersFor($user->_real())])
            ->assertStatus(200)
            ->assertJsonMatches('pageId', $targetPage->getId())
            ->assertJsonMatches('pageNumber', 2);
    }
}
