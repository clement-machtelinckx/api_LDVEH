<?php

namespace App\Controller;

use App\Repository\AdventurerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdventurerSheetController extends AbstractController
{
    #[Route('/api/adventurer/{id}/sheet', name: 'app_adventurer_sheet', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function __invoke(
        int $id,
        AdventurerRepository $adventurerRepo,
    ): JsonResponse {
        $adventurer = $adventurerRepo->findWithFullInventory($id);

        if (!$adventurer) {
            return $this->json(['error' => 'Aventurier introuvable.'], 404);
        }

        // Sécurité : seul le propriétaire ou un admin peut voir la fiche
        $user = $this->getUser();
        if ($adventurer->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'Accès refusé.'], 403);
        }

        // Armes
        $weapons = [];
        foreach ($adventurer->getAdventurerEquipments() as $ae) {
            $eq = $ae->getEquipment();
            if ($eq->getType() === \App\Enum\EquipmentType::Weapon) {
                $weapons[] = [
                    'name' => $eq->getName(),
                    'slug' => $eq->getSlug(),
                ];
            }
        }

        // Objets spéciaux (avec bonus)
        $specialObjects = [];
        foreach ($adventurer->getAdventurerEquipments() as $ae) {
            $eq = $ae->getEquipment();
            if ($eq->getType() === \App\Enum\EquipmentType::SpecialObject) {
                $item = [
                    'name' => $eq->getName(),
                    'slug' => $eq->getSlug(),
                    'slot' => $eq->getSlot()?->value,
                ];
                if ($eq->getEnduranceBonus() > 0) {
                    $item['enduranceBonus'] = $eq->getEnduranceBonus();
                }
                $specialObjects[] = $item;
            }
        }

        // Sac à dos (potions, repas, objets)
        $backpack = [];
        foreach ($adventurer->getAdventurerEquipments() as $ae) {
            $eq = $ae->getEquipment();
            if ($eq->goesInBackpack()) {
                $item = [
                    'name' => $eq->getName(),
                    'slug' => $eq->getSlug(),
                    'type' => $eq->getType()->value,
                    'quantity' => $ae->getQuantity(),
                ];
                if ($eq->getHealAmount() > 0) {
                    $item['healAmount'] = $eq->getHealAmount();
                }
                $backpack[] = $item;
            }
        }

        // Objets de quête
        $questItems = [];
        foreach ($adventurer->getAdventurerEquipments() as $ae) {
            $eq = $ae->getEquipment();
            if ($eq->getType() === \App\Enum\EquipmentType::QuestItem) {
                $questItems[] = [
                    'name' => $eq->getName(),
                    'slug' => $eq->getSlug(),
                ];
            }
        }

        // Disciplines Kaï
        $skills = [];
        foreach ($adventurer->getSkills() as $skill) {
            $skills[] = [
                'name' => $skill->getName(),
                'slug' => $skill->getSlug(),
                'description' => $skill->getDescription(),
            ];
        }

        // Compteurs sac à dos
        $backpackCount = 0;
        foreach ($backpack as $item) {
            $backpackCount += $item['quantity'];
        }

        return $this->json([
            'id' => $adventurer->getId(),
            'name' => $adventurer->getAdventurerName(),
            'ability' => $adventurer->getAbility(),
            'endurance' => $adventurer->getEndurance(),
            'maxEndurance' => $adventurer->getMaxEndurance(),
            'effectiveMaxEndurance' => $adventurer->getEffectiveMaxEndurance(),
            'gold' => $adventurer->getGold(),
            'masteredWeaponSlug' => $adventurer->getMasteredWeaponSlug(),
            'weapons' => $weapons,
            'specialObjects' => $specialObjects,
            'backpack' => [
                'items' => $backpack,
                'count' => $backpackCount,
                'max' => $adventurer::MAX_BACKPACK,
            ],
            'questItems' => $questItems,
            'skills' => $skills,
            'limits' => [
                'maxWeapons' => $adventurer::MAX_WEAPONS,
                'maxBackpack' => $adventurer::MAX_BACKPACK,
                'maxGold' => $adventurer::MAX_GOLD,
                'maxSkills' => $adventurer::MAX_SKILLS,
            ],
        ]);
    }
}