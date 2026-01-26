<?php

namespace App\Controller;

use App\Dto\RefreshTokenRequest;
use App\Service\RefreshTokenManager;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RefreshTokenController extends AbstractController
{
    public function __construct(
        private RefreshTokenManager $refreshTokenManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private LoggerInterface $logger
    ) {
    }

    #[Route('/api/token/refresh', name: 'api_token_refresh', methods: ['POST'])]
    public function refresh(Request $request): JsonResponse
    {
        $content = $request->getContent();
        
        if (empty($content)) {
            return new JsonResponse(['error' => 'Request body is required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $dto = $this->serializer->deserialize(
                $content,
                RefreshTokenRequest::class,
                'json'
            );
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid JSON'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            
            return new JsonResponse(
                ['error' => implode(', ', $errorMessages)],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $refreshToken = $dto->getRefreshToken();
        
        if (empty($refreshToken)) {
            return new JsonResponse(['error' => 'Refresh token is required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $result = $this->refreshTokenManager->refresh($refreshToken);
            
            return new JsonResponse($result, JsonResponse::HTTP_OK);
        } catch (\RuntimeException $e) {
            return new JsonResponse(
                ['error' => 'Refresh token invalid or expired'],
                JsonResponse::HTTP_UNAUTHORIZED
            );
        } catch (\Exception $e) {
            $this->logger->error('Unexpected error during token refresh', [
                'exception' => $e->getMessage(),
            ]);
            
            return new JsonResponse(
                ['error' => 'An error occurred while refreshing token'],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
