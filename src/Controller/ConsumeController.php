<?php

namespace App\Controller;

use App\Repository\AdventurerRepository;
use App\Repository\EquipmentRepository;
use App\Service\EquipmentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ConsumeController extends AbstractController
{
    #[Route('/api/adventurer/{adventurerId}/consume/{equipmentSlug}', name: 'app_consume', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function __invoke(
        int $adventurerId,
        string $equipmentSlug,
        AdventurerRepository $adventurerRepo,
        EquipmentRepository $equipmentRepo,
        EquipmentService $equipmentService,
        EntityManagerInterface $em,
    ): JsonResponse {
        $adventurer = $adventurerRepo->findWithFullInventory($adventurerId);

        if (!$adventurer) {
            return $this->json(['error' => 'Aventurier introuvable.'], 404);
        }

        if ($adventurer->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'Accès refusé.'], 403);
        }

        $equipment = $equipmentRepo->findOneBy(['slug' => $equipmentSlug]);

        if (!$equipment) {
            return $this->json(['error' => 'Équipement introuvable.'], 404);
        }

        // Vérifier que l'aventurier possède cet équipement
        $ae = $adventurer->findAdventurerEquipment($equipment);

        if (!$ae) {
            return $this->json(['error' => 'L\'aventurier ne possède pas cet objet.'], 400);
        }

        if (!$equipment->isConsumable()) {
            return $this->json(['error' => $equipment->getName() . ' n\'est pas consommable.'], 400);
        }

        $healAmount = $equipmentService->consume($adventurer, $equipment);

        // Appliquer le soin
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
            'effectiveMaxEndurance' => $adventurer->getEffectiveMaxEndurance(),
        ]);
    }
}
