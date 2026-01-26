<?php

namespace App\Service;

use App\Entity\RefreshToken;
use App\Entity\User;
use App\Repository\RefreshTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class RefreshTokenManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RefreshTokenRepository $refreshTokenRepository,
        private JWTTokenManagerInterface $jwtManager,
        private ParameterBagInterface $params,
        private LoggerInterface $logger
    ) {
    }

    /**
     * Issue a new refresh token for a user
     * 
     * @return array{rawToken: string, expiresAt: \DateTimeImmutable}
     */
    public function issueForUser(User $user): array
    {
        $rawToken = $this->generateRawToken();
        $tokenHash = $this->hashToken($rawToken);
        
        $ttl = (int) $this->params->get('refresh_token_ttl');
        $expiresAt = new \DateTimeImmutable("+{$ttl} seconds");

        $refreshToken = new RefreshToken();
        $refreshToken->setUser($user);
        $refreshToken->setTokenHash($tokenHash);
        $refreshToken->setExpiresAt($expiresAt);

        $this->entityManager->persist($refreshToken);
        $this->entityManager->flush();

        $this->logger->info('Refresh token issued', [
            'userId' => $user->getId(),
            'refreshTokenId' => $refreshToken->getId(),
        ]);

        return [
            'rawToken' => $rawToken,
            'expiresAt' => $expiresAt,
        ];
    }

    /**
     * Refresh tokens (rotate): revoke old token and issue new JWT + refresh token
     * 
     * @return array{token: string, refresh_token: string, refresh_token_expires_at: string}
     * @throws \RuntimeException if token is invalid
     */
    public function refresh(string $rawToken): array
    {
        $tokenHash = $this->hashToken($rawToken);
        $now = new \DateTimeImmutable();

        $refreshToken = $this->refreshTokenRepository->findValidByTokenHash($tokenHash, $now);

        if (!$refreshToken) {
            $this->logger->warning('Refresh token invalid or expired');
            throw new \RuntimeException('Refresh token invalid or expired');
        }

        $user = $refreshToken->getUser();
        if (!$user) {
            $this->logger->warning('Refresh token user not found');
            throw new \RuntimeException('Refresh token invalid or expired');
        }

        // Issue new tokens
        $newRefreshData = $this->issueForUser($user);
        $newJwt = $this->jwtManager->create($user);

        // Revoke old refresh token (rotation)
        $newTokenHash = $this->hashToken($newRefreshData['rawToken']);
        $refreshToken->setRevokedAt(new \DateTimeImmutable());
        $refreshToken->setReplacedByTokenHash($newTokenHash);
        $this->entityManager->flush();

        $this->logger->info('Refresh token rotated', [
            'userId' => $user->getId(),
            'oldRefreshTokenId' => $refreshToken->getId(),
        ]);

        return [
            'token' => $newJwt,
            'refresh_token' => $newRefreshData['rawToken'],
            'refresh_token_expires_at' => $newRefreshData['expiresAt']->format(\DateTimeInterface::ATOM),
        ];
    }

    /**
     * Revoke a refresh token (for logout, etc.)
     */
    public function revoke(string $rawToken): void
    {
        $tokenHash = $this->hashToken($rawToken);
        $now = new \DateTimeImmutable();

        $refreshToken = $this->refreshTokenRepository->findValidByTokenHash($tokenHash, $now);

        if ($refreshToken) {
            $refreshToken->setRevokedAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            $this->logger->info('Refresh token revoked', [
                'refreshTokenId' => $refreshToken->getId(),
            ]);
        }
    }

    /**
     * Generate a random raw token
     */
    private function generateRawToken(): string
    {
        $randomBytes = random_bytes(48);
        $base64 = rtrim(strtr(base64_encode($randomBytes), '+/', '-_'), '=');
        
        return 'rt_' . $base64;
    }

    /**
     * Hash a raw token using SHA-256
     */
    private function hashToken(string $rawToken): string
    {
        return hash('sha256', $rawToken);
    }
}
