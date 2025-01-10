<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;

class JwtAuthenticator extends AbstractAuthenticator
{
    public function authenticate(Request $request): Passport
    {
        $authorizationHeader = $request->headers->get('Authorization');

        if (null === $authorizationHeader || !str_starts_with($authorizationHeader, 'Bearer ')) {
            throw new AuthenticationException('No JWT token found');
        }

        $jwt = substr($authorizationHeader, 7);

        return new SelfValidatingPassport(new UserBadge($jwt));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response('Authentication Failed: ' . $exception->getMessage(), Response::HTTP_UNAUTHORIZED);
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization') && str_starts_with($request->headers->get('Authorization'), 'Bearer ');
    }
}