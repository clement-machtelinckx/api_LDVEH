<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Service\RefreshTokenManager;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JwtAuthenticationSuccessSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RefreshTokenManager $refreshTokenManager,
        private LoggerInterface $logger
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
        ];
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        try {
            $refreshData = $this->refreshTokenManager->issueForUser($user);

            $data = $event->getData();
            $data['refresh_token'] = $refreshData['rawToken'];
            $data['refresh_token_expires_at'] = $refreshData['expiresAt']->format(\DateTimeInterface::ATOM);
            
            // Ensure user data is present
            if (!isset($data['user'])) {
                $data['user'] = [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'roles' => $user->getRoles(),
                ];
            }

            $event->setData($data);
        } catch (\Exception $e) {
            $this->logger->error('Failed to add refresh token to login response', [
                'exception' => $e->getMessage(),
                'userId' => $user->getId(),
            ]);
        }
    }
}
