<?php

namespace App\Tests\Functional;

use App\Factory\AdventurerFactory;
use App\Factory\MonsterFactory;
use App\Factory\UserFactory;

class FightTest extends ApiTestCase
{
    public function test_fight_returns_valid_result(): void
    {
        $user = UserFactory::createOne(['password' => 'test123']);
        $adventurer = AdventurerFactory::createOne([
            'adventurerName' => 'Yazii',
            'ability' => 15,
            'endurance' => 10,
            'user' => $user
        ]);

        $monster = MonsterFactory::createOne([
            'monsterName' => 'Big Bad',
            'ability' => 14,
            'endurance' => 12
        ]);

        $this->browser()
            ->post('/api/login', [
                'json' => [
                    'email' => $user->getEmail(),
                    'password' => 'test123'
                ]
            ])
            ->assertSuccessful()
            ->use(function ($browser) use ($adventurer, $monster) {
                $browser->post('/fight', [
                    'json' => [
                        'adventurerId' => $adventurer->getId(),
                        'monsterId' => $monster->getId()
                    ]
                ])
                ->assertStatus(200)
                ->assertJsonMatches('winner', 'adventurer');
            });
    }
}
