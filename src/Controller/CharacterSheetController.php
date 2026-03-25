<?php

namespace App\Controller;

use App\Entity\Adventurer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CharacterSheetController extends AbstractController
{
    private const BASE_ABILITY = 10;
    private const BASE_ENDURANCE = 20;
    private const DISTRIBUTABLE_POINTS = 5;
    private const REQUIRED_TOTAL = self::BASE_ABILITY + self::BASE_ENDURANCE + self::DISTRIBUTABLE_POINTS;

    #[Route('/api/character-sheets', name: 'api_character_sheet_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            return $this->json(['error' => 'Payload JSON invalide.'], 400);
        }

        $name = trim((string) ($payload['name'] ?? ''));
        $avatar = trim((string) ($payload['avatar'] ?? ''));
        $ability = (int) ($payload['ability'] ?? -1);
        $endurance = (int) ($payload['endurance'] ?? -1);

        $validationError = $this->validateInput($name, $ability, $endurance);
        if ($validationError !== null) {
            return $this->json(['error' => $validationError], 400);
        }

        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non authentifie.'], 401);
        }

        $adventurer = new Adventurer();
        $adventurer->setUser($user);
        $adventurer->setAdventurerName($name);
        $adventurer->setAvatar($avatar !== '' ? $avatar : null);
        $adventurer->setAbility($ability);
        $adventurer->setEndurance($endurance);

        $em->persist($adventurer);
        $em->flush();

        return $this->json([
            'message' => 'Fiche personnage creee.',
            'adventurer' => [
                'id' => $adventurer->getId(),
                'name' => $adventurer->getAdventurerName(),
                'avatar' => $adventurer->getAvatar(),
                'ability' => $adventurer->getAbility(),
                'endurance' => $adventurer->getEndurance(),
            ],
        ], 201);
    }

    private function validateInput(string $name, int $ability, int $endurance): ?string
    {
        if ($name === '') {
            return 'Le nom est obligatoire.';
        }

        if ($ability < self::BASE_ABILITY || $endurance < self::BASE_ENDURANCE) {
            return 'Minimum: HABILETE 10 et ENDURANCE 20.';
        }

        if (($ability + $endurance) !== self::REQUIRED_TOTAL) {
            return sprintf('La repartition doit totaliser %d points au total (%d + %d + %d).', self::REQUIRED_TOTAL, self::BASE_ABILITY, self::BASE_ENDURANCE, self::DISTRIBUTABLE_POINTS);
        }

        return null;
    }
}

