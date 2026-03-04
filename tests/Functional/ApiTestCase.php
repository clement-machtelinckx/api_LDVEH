<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\HttpOptions;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ApiTestCase extends KernelTestCase
{
    use HasBrowser {
        browser as baseKernelBrowser;
    }
    use Factories;
    use ResetDatabase;

    protected function browser(array $options = [], array $server = []): \Zenstruck\Browser\KernelBrowser
    {
        return $this->baseKernelBrowser($options, $server)
            ->setDefaultHttpOptions(
                HttpOptions::create()
                    ->withHeader('Accept', 'application/ld+json')
            );
    }

    /**
     * Generate JWT authentication headers for a given user
     */
    protected function authHeadersFor(User $user): array
    {
        $container = static::getContainer();
        $jwtManager = $container->get(JWTTokenManagerInterface::class);
        $token = $jwtManager->create($user);
        
        return [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ];
    }
}
