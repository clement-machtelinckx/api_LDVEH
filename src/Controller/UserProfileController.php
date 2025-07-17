<?php
// src/Controller/UserProfileController.php
namespace App\Controller;

use App\Dto\UserProfileUpdateInput;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserProfileController extends AbstractController
{
    #[Route('/api/profile', name: 'user_profile_get', methods: ['GET'])]
    public function getProfile(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->json($user, 200, [], ['groups' => ['user:read']]);
    }

    #[Route('/api/profile', name: 'user_profile_update', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function updateProfile(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();

        $data = $serializer->deserialize($request->getContent(), UserProfileUpdateInput::class, 'json');
        $errors = $validator->validate($data);

        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }
        
        $user->setEmail($data->email);

        if ($data->newPassword) {
            $user->setPassword($passwordHasher->hashPassword($user, $data->newPassword));
        }

        $em->flush();

        return $this->json(['message' => 'Profil mis à jour avec succès.']);
    }
}
