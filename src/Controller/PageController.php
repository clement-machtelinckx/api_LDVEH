<?php

namespace App\Controller;

use App\Repository\AdventurerRepository;
use App\Repository\PageRepository;
use App\Service\CombatService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('/page/{pageId}/adventurer/{adventurerId}', name: 'view_page', methods: ['GET'])]
    public function viewPage(
        int $pageId,
        int $adventurerId,
        PageRepository $pageRepo,
        AdventurerRepository $adventurerRepo,
        CombatService $combatService
    ): JsonResponse {
        $page = $pageRepo->find($pageId);
        $adventurer = $adventurerRepo->find($adventurerId);

        if (!$page || !$adventurer) {
            return $this->json(['error' => 'Page ou aventurier introuvable'], 404);
        }

        if (!$combatService->canAccessPage($page, $adventurer)) {
            return $this->json([
                'error' => 'Vous devez vaincre le monstre pour accéder à cette page.',
                'monsterId' => $page->getMonster()?->getId(),
                'monsterName' => $page->getMonster()?->getMonsterName()
            ], 403);
        }
        

        // Récupération des choix disponibles pour cette page
        $choices = [];
        foreach ($page->getChoices() as $choice) {
            $choices[] = [
                'text' => $choice->getText(),
                'nextPage' => $choice->getNextPage()->getId(),
            ];
        }

        return $this->json([
            'pageId' => $page->getId(),
            'pageNumber' => $page->getPageNumber(),
            'content' => $page->getContent(),
            'monsterId' => $page->getMonster()?->getId(),
            'monster' => $page->getMonster()?->getMonsterName(),
            'canAccess' => $combatService->canAccessPage($page, $adventurer),
            'isBlocking' => $page->isCombatIsBlocking(),
            'choices' => $choices,



        ]);
    }
}
