<?php

namespace App\Controller;

use App\Repository\AdventureRepository;
use App\Repository\AdventurerRepository;
use App\Service\NavigationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DiceRollController extends AbstractController
{
    #[Route('/api/adventurer/{adventurerId}/dice-roll', name: 'app_dice_roll', methods: ['POST'])]
    public function roll(
        int $adventurerId,
        AdventurerRepository $adventurerRepo,
        AdventureRepository $adventureRepo,
        NavigationService $navigationService,
    ): JsonResponse {
        $adventurer = $adventurerRepo->findOneBy([
            'id' => $adventurerId,
            'user' => $this->getUser(),
        ]);
        if (!$adventurer) {
            return $this->json(['error' => 'Aventurier introuvable'], 404);
        }

        $adventure = $adventureRepo->findOneBy([
            'adventurer' => $adventurer,
            'isFinished' => false,
        ]);
        if (!$adventure) {
            return $this->json(['error' => 'Aucune aventure en cours'], 404);
        }

        $currentPage = $adventure->getCurrentPage();
        if (!$currentPage) {
            return $this->json(['error' => 'Aucune page courante'], 404);
        }

        $roll = random_int(0, 9);

        $matched = $navigationService->matchDiceRoll($currentPage, $roll);

        return $this->json([
            'roll' => $roll,
            'nextPage' => $matched ? $matched->getNextPage()?->getId() : null,
            'choiceText' => $matched ? $matched->getText() : null,
            'events' => $matched ? $matched->getEvents() : null,
        ]);
    }
}
