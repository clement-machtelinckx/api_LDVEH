<?php

namespace App\Controller;

use App\Repository\AdventurerRepository;
use App\Repository\MonsterRepository;
use App\Service\CombatService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FightController extends AbstractController
{
    #[Route('/fight', name: 'app_fight', methods: ['POST'])]
    public function fight(
        Request $request,
        AdventurerRepository $adventurerRepo,
        MonsterRepository $monsterRepo,
        CombatService $combatService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $adventurer = $adventurerRepo->find($data['adventurerId'] ?? 0);
        $monster = $monsterRepo->find($data['monsterId'] ?? 0);

        if (!$adventurer || !$monster) {
            return $this->json(['error' => 'Invalid adventurer or monster ID'], 400);
        }

        $result = $combatService->fight($adventurer, $monster);

        return $this->json($result);
    }
}
