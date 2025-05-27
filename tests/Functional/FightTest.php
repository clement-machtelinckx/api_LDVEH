<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Factory\AdventurerFactory;
use App\Factory\MonsterFactory;
use App\Repository\UserRepository;

class FightTest extends ApiTestCase
{
    public function test_fight_returns_valid_result(): void
    {
        // 🧑‍💼 Récupère l'utilisateur des fixtures
        /** @var UserRepository $userRepo */
        $userRepo = static::getContainer()->get(UserRepository::class);
        $user = $userRepo->findOneByEmail('admin@example.com');

        dump($user);
        // 🧝‍♂️ Crée un aventurier pour ce user
        $adventurer = AdventurerFactory::createOne([
            'adventurerName' => 'Yazii',
            'ability' => 15,
            'endurance' => 10,
            'user' => $user
        ]);

        // 👹 Crée un monstre
        $monster = MonsterFactory::createOne([
            'monsterName' => 'Big Bad',
            'ability' => 14,
            'endurance' => 12
        ]);

        // 🔐 Login & token
        $this->browser()
            ->post('/api/login', [
                'json' => [
                    'email' => 'admin@example.com',
                    'password' => 'admin123'
                ]
            ])
            ->assertSuccessful()
            ->use(function ($browser) use ($adventurer, $monster) {
                $token = $browser->json('token');

                $browser->post('/fight', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                    ],
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
