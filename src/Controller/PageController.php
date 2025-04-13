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
    #[Route('/page/{pageId}/adventurer/{adventurerId}/from/{fromPageId}', name: 'app_view_page', methods: ['GET'])]
    public function viewPage(
        int $pageId,
        int $adventurerId,
        int $fromPageId,
        PageRepository $pageRepo,
        AdventurerRepository $adventurerRepo,
        CombatService $combatService
    ): JsonResponse {
        $adventurer = $adventurerRepo->find($adventurerId);
        $fromPage = $pageRepo->find($fromPageId);
        $targetPage = $pageRepo->find($pageId);
    
        if (!$adventurer || !$fromPage || !$targetPage) {
            return $this->json(['error' => 'Page, page précédente ou aventurier introuvable'], 404);
        }
    
        // Vérifie que la page courante est bien une des destinations possibles depuis la page précédente
        $isAccessible = false;
        foreach ($fromPage->getChoices() as $choice) {
            if ($choice->getNextPage()?->getId() === $pageId) {
                $isAccessible = true;
                break;
            }
        }
    
        if (!$isAccessible) {
            return $this->json([
                'error' => 'Cette page n’est pas accessible depuis la page précédente.',
                'fromPageId' => $fromPageId,
                'requestedPageId' => $pageId,
            ], 403);
        }
    
        // Vérifie si le combat de la page précédente est bloquant
        if (!$combatService->canAccessPage($fromPage, $adventurer)) {
            return $this->json([
                'error' => 'Vous devez vaincre le monstre pour continuer.',
                'monsterId' => $fromPage->getMonster()?->getId(),
                'monsterName' => $fromPage->getMonster()?->getMonsterName(),
            ], 403);
        }
    
        // Récupère les choix
        $choices = [];
        foreach ($targetPage->getChoices() as $choice) {
            $choices[] = [
                'text' => $choice->getText(),
                'nextPage' => $choice->getNextPage()?->getId(),
            ];
        }
    
        return $this->json([
            'pageId' => $targetPage->getId(),
            'pageNumber' => $targetPage->getPageNumber(),
            'content' => $targetPage->getContent(),
            'monsterId' => $targetPage->getMonster()?->getId(),
            'monster' => $targetPage->getMonster()?->getMonsterName(),
            'canAccess' => $combatService->canAccessPage($targetPage, $adventurer),
            'isBlocking' => $targetPage->isCombatIsBlocking(),
            'choices' => $choices,
        ]);
    }
}
