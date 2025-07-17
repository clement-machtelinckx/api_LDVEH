<?php
// src/Controller/AdventureController.php
namespace App\Controller;

use App\Entity\Adventure;
use App\Entity\Adventurer;
use App\Repository\BookRepository;
use App\Repository\PageRepository;
use App\Service\AdventureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AdventureController extends AbstractController
{
    #[Route('/api/adventure/start', name: 'api_start_adventure', methods: ['POST'])]
    public function startAdventure(
        Request $request,
        BookRepository $bookRepo,
        EntityManagerInterface $em,
        AdventureService $adventureService
    ): JsonResponse {
        $user = $this->getUser();
    
        $data = json_decode($request->getContent(), true);
        $bookId = $data['bookId'] ?? null;
        $adventurerName = $data['adventurerName'] ?? 'Héros anonyme';
    
        if (!$bookId) {
            return $this->json(['error' => 'bookId manquant'], 400);
        }
    
        $book = $bookRepo->find($bookId);
        if (!$book) {
            return $this->json(['error' => 'Livre introuvable'], 404);
        }
    
        // Création de l’aventurier ici (c’est ta logique au-dessus)
        $adventurer = new Adventurer();
        $adventurer->setUser($user);
        $adventurer->setAdventurerName($adventurerName);
        $adventurer->setAbility(random_int(18, 20));
        $adventurer->setEndurance(random_int(10, 20));
        $em->persist($adventurer);
    
        // Création de l’aventure
        $adventure = $adventureService->startAdventure($user, $book, $adventurer);
    
        return $this->json([
            'message' => 'Aventure démarrée',
            'adventureId' => $adventure->getId(),
            'adventurerId' => $adventurer->getId(),
            'pageId' => $adventure->getCurrentPage()->getId(),
            'fromPageId' => null,
        ]);
    }

    
    #[Route('/api/adventure/{id}/finish', name: 'api_adventure_finish', methods: ['POST'])]
    public function finishAdventure(
        Adventure $adventure,
        AdventureService $adventureService
    ): JsonResponse {
        $user = $this->getUser();

        if ($adventure->getUser() !== $user) {
            throw new AccessDeniedException('Vous ne pouvez terminer que vos propres aventures.');
        }
        if ($adventure->isFinished()) {
            return $this->json(['message' => 'Cette aventure est déjà terminée.'], 400);
        }
        $adventureService->finishAdventure($adventure);

        $adventureService->deleteAdventure($adventure);

        return $this->json([
            'message' => 'Aventure terminée avec succès.',
            'adventureId' => $adventure->getId(),
            'finishedAt' => $adventure->getEndedAt()?->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/api/adventure/{id}', name: 'api_adventure_delete', methods: ['DELETE'])]
    public function deleteAdventure(
        Adventure $adventure,
        AdventureService $adventureService
    ): JsonResponse {
        $user = $this->getUser();

        if ($adventure->getUser() !== $user) {
            return $this->json(['error' => 'Accès interdit.'], 403);
        }

        $adventureService->deleteAdventure($adventure);

        return $this->json(['message' => 'Aventure supprimée.']);
    }

    
}
