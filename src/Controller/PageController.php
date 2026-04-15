<?php

namespace App\Controller;

use App\Repository\AdventureRepository;
use App\Repository\AdventurerRepository;
use App\Repository\PageRepository;
use App\Service\AdventureService;
use App\Service\CombatService;
use App\Service\EquipmentService;
use App\Service\NavigationService;
use App\Service\SkillService;
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
        AdventureRepository $adventureRepo,
        CombatService $combatService,
        AdventureService $adventureService,
        SkillService $skillService,
        EquipmentService $equipmentService,
        NavigationService $navigationService,
        ?int $fromPageId = null,
    ): JsonResponse {
        $adventurer = $adventurerRepo->findWithFullInventory($adventurerId, $this->getUser());
        $targetPage = $pageRepo->find($pageId);
        $fromPage = $fromPageId ? $pageRepo->find($fromPageId) : null;
    
        if (!$adventurer || !$targetPage) {
            return $this->json(['error' => 'Aventurier ou page cible introuvable'], 404);
        }
    
        $adventure = $adventureRepo->findOneBy([
            'adventurer' => $adventurer,
            'isFinished' => false
        ]);
    
        if (!$adventure) {
            return $this->json(['error' => 'Aucune aventure en cours pour cet aventurier'], 404);
        }
    
        if ($fromPageId) {
            if (!$fromPage) {
                return $this->json(['error' => 'Page precedente introuvable'], 404);
            }

            // Trouver le choix emprunte
            $takenChoice = null;
            foreach ($fromPage->getChoices() as $choice) {
                if ($choice->getNextPage()?->getId() === $pageId) {
                    $takenChoice = $choice;
                    break;
                }
            }

            if (!$takenChoice) {
                return $this->json([
                    'error' => 'Cette page n\'est pas accessible depuis la page precedente.',
                    'fromPageId' => $fromPageId,
                    'requestedPageId' => $pageId,
                ], 403);
            }

            // Verifier la condition du choix
            $condition = $takenChoice->getCondition();
            if ($condition && !$navigationService->checkCondition($adventurer, $condition)) {
                return $this->json([
                    'error' => 'Vous ne remplissez pas la condition pour ce choix.',
                    'condition' => $condition,
                ], 403);
            }

            if (!$combatService->canAccessPage($fromPage, $adventurer)) {
                return $this->json([
                    'error' => 'Vous devez vaincre le monstre pour continuer.',
                    'monsterId' => $fromPage->getMonster()?->getId(),
                    'monsterName' => $fromPage->getMonster()?->getMonsterName(),
                ], 403);
            }

            $isNewPage = $adventure->getCurrentPage()?->getId() !== $targetPage->getId();

            // Mise a jour de la progression
            $adventureService->updatePage($adventure, $targetPage, $fromPage);

            if ($isNewPage) {
                // Consommer la condition du choix (gold, item)
                $navigationService->consumeCondition($adventurer, $takenChoice->getCondition());

                // Appliquer les events du choix pris (ex: perte END sur jet de des)
                $navigationService->applyChoiceEvents($adventurer, $takenChoice);

                // Appliquer les events de la page (items, endurance)
                $navigationService->applyPageEvents($adventurer, $targetPage);

                // Guerison : +1 END si discipline Guerison et pas de combat
                if ($targetPage->getMonster() === null) {
                    $skillService->applyHealing($adventurer);
                }

                // Repas obligatoire
                if ($targetPage->isRequiresMeal()) {
                    $skillService->handleMeal($adventurer, $equipmentService);
                }
            }
        }

        $choices = [];
        foreach ($targetPage->getChoices() as $choice) {
            $isDiceRoll = str_starts_with($choice->getText() ?? '', 'Jet de des:');
            $choiceData = [
                'text' => $choice->getText(),
                'nextPage' => $choice->getNextPage()?->getId(),
                'requiresDiceRoll' => $isDiceRoll,
            ];
            $cond = $choice->getCondition();
            if ($cond) {
                $choiceData['condition'] = $cond;
                $choiceData['available'] = $navigationService->checkCondition($adventurer, $cond);
            } else {
                $choiceData['available'] = true;
            }
            $choices[] = $choiceData;
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
            'endingType' => $targetPage->getEndingType(),
            'adventurerAbility' => $adventurer->getAbility(),
            'adventurerEndurance' => $adventurer->getEndurance(),
            'adventurerGold' => $adventurer->getGold(),
        ]);
    }
}
