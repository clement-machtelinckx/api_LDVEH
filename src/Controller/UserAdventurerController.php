<?php

namespace App\Controller;

use App\Repository\AdventurerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserAdventurerController extends AbstractController
{
    #[Route('/api/my-adventurers', name: 'app_my_adventurers', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function __invoke(
        AdventurerRepository $adventurerRepository,
        SerializerInterface $serializer
    ): JsonResponse {
        $user = $this->getUser();

        $adventurers = $adventurerRepository->findBy(['user' => $user]);

        return $this->json($adventurers, 200, [], ['groups' => ['adventurer:read']]);

    }
}
