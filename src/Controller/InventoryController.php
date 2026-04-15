<?php

namespace App\Controller;

use App\Repository\AdventurerRepository;
use App\Repository\EquipmentRepository;
use App\Service\EquipmentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class InventoryController extends AbstractController
{
    #[Route('/api/adventurer/{adventurerId}/consume/{slug}', name: 'app_consume', methods: ['POST'])]
    public function consume(
        int $adventurerId,
        string $slug,
        AdventurerRepository $adventurerRepo,
        EquipmentRepository $equipmentRepo,
        EquipmentService $equipmentService,
        EntityManagerInterface $em,
    ): JsonResponse {
        $adventurer = $adventurerRepo->findWithFullInventory($adventurerId, $this->getUser());
        if (!$adventurer) {
            return $this->json(['error' => 'Aventurier introuvable.'], 404);
        }

        $equipment = $equipmentRepo->findOneBy(['slug' => $slug]);
        if (!$equipment) {
            return $this->json(['error' => 'Equipement introuvable.'], 404);
        }

        $ae = $adventurer->findAdventurerEquipment($equipment);
        if (!$ae) {
            return $this->json(['error' => 'Vous ne possedez pas cet objet.'], 400);
        }

        if (!$equipment->isConsumable()) {
            return $this->json(['error' => $equipment->getName() . ' n\'est pas consommable.'], 400);
        }

        $healAmount = $equipmentService->consume($adventurer, $equipment);

        if ($healAmount > 0) {
            $effectiveMax = $adventurer->getEffectiveMaxEndurance();
            $newEndurance = min($effectiveMax, $adventurer->getEndurance() + $healAmount);
            $adventurer->setEndurance($newEndurance);
            $em->flush();
        }

        return $this->json([
            'consumed' => $equipment->getName(),
            'healAmount' => $healAmount,
            'endurance' => $adventurer->getEndurance(),
        ]);
    }

    #[Route('/api/adventurer/{adventurerId}/drop/{slug}', name: 'app_drop', methods: ['POST'])]
    public function drop(
        int $adventurerId,
        string $slug,
        AdventurerRepository $adventurerRepo,
        EquipmentRepository $equipmentRepo,
        EquipmentService $equipmentService,
    ): JsonResponse {
        $adventurer = $adventurerRepo->findWithFullInventory($adventurerId, $this->getUser());
        if (!$adventurer) {
            return $this->json(['error' => 'Aventurier introuvable.'], 404);
        }

        $equipment = $equipmentRepo->findOneBy(['slug' => $slug]);
        if (!$equipment) {
            return $this->json(['error' => 'Equipement introuvable.'], 404);
        }

        $ae = $adventurer->findAdventurerEquipment($equipment);
        if (!$ae) {
            return $this->json(['error' => 'Vous ne possedez pas cet objet.'], 400);
        }

        $equipmentService->removeEquipment($adventurer, $equipment, 1);

        return $this->json([
            'dropped' => $equipment->getName(),
            'slug' => $slug,
        ]);
    }

    #[Route('/api/adventurer/{adventurerId}/gold', name: 'app_gold', methods: ['POST'])]
    public function gold(
        int $adventurerId,
        Request $request,
        AdventurerRepository $adventurerRepo,
        EntityManagerInterface $em,
    ): JsonResponse {
        $adventurer = $adventurerRepo->findOneBy([
            'id' => $adventurerId,
            'user' => $this->getUser(),
        ]);
        if (!$adventurer) {
            return $this->json(['error' => 'Aventurier introuvable.'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $amount = $data['amount'] ?? 0;

        if ($amount === 0) {
            return $this->json(['error' => 'Montant invalide.'], 400);
        }

        $adventurer->addGold($amount);
        $em->flush();

        return $this->json([
            'gold' => $adventurer->getGold(),
        ]);
    }
}
