<?php

namespace App\Tests\Functional;

use App\Factory\AdventurerFactory;
use App\Factory\MonsterFactory;
use App\Factory\UserFactory;
use App\Repository\FightHistoryRepository;

class FightControllerTest extends ApiTestCase
{
    public function testFightRequiresAuthentication(): void
    {
        $this->browser()
            ->post('/fight', [
                'json' => [
                    'adventurerId' => 1,
                    'monsterId' => 1,
                ],
            ])
            ->assertStatus(401);
    }

    public function testFightReturns400WithInvalidAdventurerId(): void
    {
        $user = UserFactory::createOne(['email' => 'test@example.com']);
        $monster = MonsterFactory::createOne([
            'monsterName' => 'Golem',
            'ability' => 15,
            'endurance' => 10,
        ]);

        $this->browser()
            ->post('/fight', [
                'json' => [
                    'adventurerId' => 999999,
                    'monsterId' => $monster->getId(),
                ],
                'server' => $this->authHeadersFor($user->_real()),
            ])
            ->assertStatus(400)
            ->assertJsonMatches('error', 'Invalid adventurer or monster ID');
    }

    public function testFightReturns400WithInvalidMonsterId(): void
    {
        $user = UserFactory::createOne(['email' => 'test@example.com']);
        $adventurer = AdventurerFactory::createOne([
            'user' => $user,
            'AdventurerName' => 'Loup Solitaire',
            'Ability' => 15,
            'Endurance' => 25,
        ]);

        $this->browser()
            ->post('/fight', [
                'json' => [
                    'adventurerId' => $adventurer->getId(),
                    'monsterId' => 999999,
                ],
                'server' => $this->authHeadersFor($user->_real()),
            ])
            ->assertStatus(400)
            ->assertJsonMatches('error', 'Invalid adventurer or monster ID');
    }

    public function testFightReturns400WithZeroIds(): void
    {
        $user = UserFactory::createOne(['email' => 'test@example.com']);

        $this->browser()
            ->post('/fight', [
                'json' => [
                    'adventurerId' => 0,
                    'monsterId' => 0,
                ],
                'server' => $this->authHeadersFor($user->_real()),
            ])
            ->assertStatus(400)
            ->assertJsonMatches('error', 'Invalid adventurer or monster ID');
    }

    public function testFightSuccessfullyWithAdventurerVictory(): void
    {
        $user = UserFactory::createOne(['email' => 'test@example.com']);
        
        // Create a very strong adventurer to ensure victory
        $adventurer = AdventurerFactory::createOne([
            'user' => $user,
            'AdventurerName' => 'Loup Solitaire',
            'Ability' => 25,
            'Endurance' => 50,
        ]);

        // Create a very weak monster to ensure defeat
        $monster = MonsterFactory::createOne([
            'monsterName' => 'Faible Gobelin',
            'ability' => 5,
            'endurance' => 1,
        ]);

        $this->browser()
            ->post('/fight', [
                'json' => [
                    'adventurerId' => $adventurer->getId(),
                    'monsterId' => $monster->getId(),
                ],
                'server' => $this->authHeadersFor($user->_real()),
            ])
            ->assertStatus(200)
            ->assertJsonMatches('winner', 'adventurer')
            ->assertJsonMatches('adventurer.adventurerName', 'Loup Solitaire')
            ->assertJsonMatches('monster.monsterName', 'Faible Gobelin');

        // Verify that FightHistory was persisted with victory=true
        $container = static::getContainer();
        $fightHistoryRepo = $container->get(FightHistoryRepository::class);
        
        $fightHistory = $fightHistoryRepo->findOneBy([
            'adventurer' => $adventurer->_real(),
            'monster' => $monster->_real(),
            'victory' => true,
        ]);

        $this->assertNotNull($fightHistory, 'FightHistory should be persisted with victory=true');
        $this->assertTrue($fightHistory->isVictory(), 'FightHistory victory should be true');
    }

    public function testFightReturnsCorrectStructure(): void
    {
        $user = UserFactory::createOne(['email' => 'test@example.com']);
        
        $adventurer = AdventurerFactory::createOne([
            'user' => $user,
            'AdventurerName' => 'Loup Solitaire',
            'Ability' => 20,
            'Endurance' => 30,
        ]);

        $monster = MonsterFactory::createOne([
            'monsterName' => 'Orc',
            'ability' => 10,
            'endurance' => 5,
        ]);

        $response = $this->browser()
            ->post('/fight', [
                'json' => [
                    'adventurerId' => $adventurer->getId(),
                    'monsterId' => $monster->getId(),
                ],
                'server' => $this->authHeadersFor($user->_real()),
            ])
            ->assertStatus(200);

        // Verify the response structure contains expected fields
        $response
            ->assertJsonMatches('adventurer.adventurerName', 'Loup Solitaire')
            ->assertJsonMatches('adventurer.base', 20)
            ->assertJsonMatches('monster.monsterName', 'Orc')
            ->assertJsonMatches('monster.base', 10);

        // Verify winner is one of the valid values
        $json = $response->json()->decoded();
        $this->assertContains($json['winner'], ['adventurer', 'monster', null]);
        
        // Verify history array exists
        $this->assertIsArray($json['history']);
        $this->assertNotEmpty($json['history']);
        
        // Verify log string exists
        $this->assertIsString($json['log']);
    }
}
