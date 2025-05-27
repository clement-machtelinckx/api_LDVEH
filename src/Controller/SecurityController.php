<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Security $security, JWTTokenManagerInterface $jwtManager, SessionInterface $session): Response
    {
        // Vérifie si l'utilisateur est déjà connecté en tant qu'admin
        if ($security->isGranted('ROLE_ADMIN')) {
            $user = $security->getUser();
            $token = $jwtManager->create($user);

            // Stocker le token dans une session
            $session->set('jwt_token', $token);

            return $this->redirectToRoute('api_doc'); 
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function loginCheck(
        Request $request,
        JWTTokenManagerInterface $jwtManager
    ): JsonResponse {
        // Vérifier si l'utilisateur a bien soumis des identifiants
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            return new JsonResponse(['error' => 'Invalid login credentials'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Générer le token JWT
        $token = $jwtManager->create($user);

        return new JsonResponse([
            'token' => $token,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles()
            ]
        ]);
    }

    #[Route('/api/register', name: 'app_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;


        if (!$email || !$password) {
            return $this->json(['error' => 'Email et mot de passe requis'], 400);
        }

        if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
            return $this->json([
                'error' => 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, un chiffre et un caractère spécial.'
            ], 400);
        }

        // Vérifier si l'utilisateur existe déjà
        $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            return $this->json(['error' => 'Cet utilisateur existe déjà'], 409);
        }

        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);

        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $em->persist($user);
        $em->flush();

        return $this->json(['message' => 'Utilisateur créé avec succès ✅']);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(SessionInterface $session): void
    {
        $session->remove('jwt_token');

        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}