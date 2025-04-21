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
    #[Route('/page/{pageId}/adventurer/{adventurerId}', name: 'app_view_first_page', methods: ['GET'])]
    #[Route('/page/{pageId}/adventurer/{adventurerId}/from/{fromPageId}', name: 'app_view_page', methods: ['GET'])]
    public function viewPage(
        int $pageId,
        int $adventurerId,
        PageRepository $pageRepo,
        AdventurerRepository $adventurerRepo,
        CombatService $combatService,
        ?int $fromPageId = null,
    ): JsonResponse {
        $adventurer = $adventurerRepo->find($adventurerId);
        $targetPage = $pageRepo->find($pageId);
        $fromPage = $fromPageId ? $pageRepo->find($fromPageId) : null;
    
        if (!$adventurer || !$targetPage) {
            return $this->json(['error' => 'Aventurier ou page cible introuvable'], 404);
        }
    
        // Si fromPageId est fourni, on vérifie qu’on peut accéder à pageId depuis fromPageId
        if ($fromPageId) {
            if (!$fromPage) {
                return $this->json(['error' => 'Page précédente introuvable'], 404);
            }
    
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
    
            // On vérifie si le combat sur la page précédente est bloquant
            if (!$combatService->canAccessPage($fromPage, $adventurer)) {
                return $this->json([
                    'error' => 'Vous devez vaincre le monstre pour continuer.',
                    'monsterId' => $fromPage->getMonster()?->getId(),
                    'monsterName' => $fromPage->getMonster()?->getMonsterName(),
                ], 403);
            }
        }
    
        // Récupère les choix depuis la page cible
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
