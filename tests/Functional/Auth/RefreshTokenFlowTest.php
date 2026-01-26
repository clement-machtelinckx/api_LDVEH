<?php

namespace App\Tests\Functional\Auth;

use App\Entity\RefreshToken;
use App\Factory\UserFactory;
use App\Tests\Functional\ApiTestCase;

class RefreshTokenFlowTest extends ApiTestCase
{
    /**
     * Test that login returns a refresh_token
     */
    public function testLoginReturnsRefreshToken(): void
    {
        // Arrange: Create a user with a known password
        $user = UserFactory::createOne([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Act: Login
        $response = $this->browser()
            ->post('/api/login', [
                'json' => [
                    'email' => 'test@example.com',
                    'password' => 'password123',
                ],
            ])
            ->assertStatus(200)
            ->json()
        ;

        // Assert
        $this->assertArrayHasKey('token', $response->decoded());
        $this->assertArrayHasKey('refresh_token', $response->decoded());
        $this->assertArrayHasKey('refresh_token_expires_at', $response->decoded());
        $this->assertArrayHasKey('user', $response->decoded());
        $this->assertEquals('test@example.com', $response->decoded()['user']['email']);
    }

    /**
     * Test that refresh token can be used to get new tokens (rotation)
     */
    public function testRefreshTokenRotation(): void
    {
        // Arrange: Login to get initial tokens
        $user = UserFactory::createOne([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $loginResponse = $this->browser()
            ->post('/api/login', [
                'json' => [
                    'email' => 'test@example.com',
                    'password' => 'password123',
                ],
            ])
            ->assertStatus(200)
            ->json()
        ;

        $initialRefreshToken = $loginResponse->decoded()['refresh_token'];
        $this->assertNotEmpty($initialRefreshToken, 'Initial refresh token should not be empty');

        // Act: Use refresh token to get new tokens
        $refreshResponse = $this->browser()
            ->post('/api/token/refresh', [
                'json' => [
                    'refresh_token' => $initialRefreshToken,
                ],
            ])
            ->assertStatus(200)
            ->json()
        ;

        $newRefreshToken = $refreshResponse->decoded()['refresh_token'];
        $this->assertNotEmpty($newRefreshToken, 'New refresh token should not be empty');
        $this->assertNotSame($initialRefreshToken, $newRefreshToken, 'New refresh token should be different from the old one');

        // Assert: Old refresh token should no longer work (rotation)
        $this->browser()
            ->post('/api/token/refresh', [
                'json' => [
                    'refresh_token' => $initialRefreshToken,
                ],
            ])
            ->assertStatus(401)
        ;
    }

    /**
     * Test that invalid refresh token returns 401
     */
    public function testInvalidRefreshTokenReturns401(): void
    {
        $this->browser()
            ->post('/api/token/refresh', [
                'json' => [
                    'refresh_token' => 'rt_invalid_token_that_does_not_exist',
                ],
            ])
            ->assertStatus(401)
            ->assertJsonMatches('error', 'Refresh token invalid or expired')
        ;
    }

    /**
     * Test that empty/missing refresh token returns 400
     */
    public function testEmptyRefreshTokenReturns400(): void
    {
        // Empty refresh_token
        $this->browser()
            ->post('/api/token/refresh', [
                'json' => [
                    'refresh_token' => '',
                ],
            ])
            ->assertStatus(400)
        ;

        // Missing refresh_token
        $this->browser()
            ->post('/api/token/refresh', [
                'json' => [],
            ])
            ->assertStatus(400)
        ;
    }

    /**
     * Test that expired refresh token returns 401
     */
    public function testExpiredRefreshTokenReturns401(): void
    {
        // Arrange: Create a user and manually create an expired refresh token
        $user = UserFactory::createOne([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $rawToken = 'rt_' . bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $rawToken);

        $expiredToken = new RefreshToken();
        $expiredToken->setUser($user->_real());
        $expiredToken->setTokenHash($tokenHash);
        $expiredToken->setExpiresAt(new \DateTimeImmutable('-1 day')); // Expired yesterday

        $em = static::getContainer()->get('doctrine')->getManager();
        $em->persist($expiredToken);
        $em->flush();

        // Act: Try to use expired token
        $this->browser()
            ->post('/api/token/refresh', [
                'json' => [
                    'refresh_token' => $rawToken,
                ],
            ])
            ->assertStatus(401)
            ->assertJsonMatches('error', 'Refresh token invalid or expired')
        ;
    }

    /**
     * Test that revoked refresh token returns 401
     */
    public function testRevokedRefreshTokenReturns401(): void
    {
        // Arrange: Create a user and manually create a revoked refresh token
        $user = UserFactory::createOne([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $rawToken = 'rt_' . bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $rawToken);

        $revokedToken = new RefreshToken();
        $revokedToken->setUser($user->_real());
        $revokedToken->setTokenHash($tokenHash);
        $revokedToken->setExpiresAt(new \DateTimeImmutable('+30 days'));
        $revokedToken->setRevokedAt(new \DateTimeImmutable()); // Already revoked

        $em = static::getContainer()->get('doctrine')->getManager();
        $em->persist($revokedToken);
        $em->flush();

        // Act: Try to use revoked token
        $this->browser()
            ->post('/api/token/refresh', [
                'json' => [
                    'refresh_token' => $rawToken,
                ],
            ])
            ->assertStatus(401)
            ->assertJsonMatches('error', 'Refresh token invalid or expired')
        ;
    }

    /**
     * Test that new JWT token can be used and refresh creates new tokens
     */
    public function testRefreshedJwtTokenWorks(): void
    {
        // Arrange: Login to get initial tokens
        $user = UserFactory::createOne([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $loginResponse = $this->browser()
            ->post('/api/login', [
                'json' => [
                    'email' => 'test@example.com',
                    'password' => 'password123',
                ],
            ])
            ->assertStatus(200)
            ->json()
        ;

        $initialRefreshToken = $loginResponse->decoded()['refresh_token'];

        // Act: Use refresh token to get new JWT
        $refreshResponse = $this->browser()
            ->post('/api/token/refresh', [
                'json' => [
                    'refresh_token' => $initialRefreshToken,
                ],
            ])
            ->assertStatus(200)
            ->json()
        ;

        $newJwt = $refreshResponse->decoded()['token'];
        $newRefreshToken = $refreshResponse->decoded()['refresh_token'];
        
        $this->assertNotEmpty($newJwt, 'New JWT should not be empty');
        $this->assertNotEmpty($newRefreshToken, 'New refresh token should not be empty');
        // The refresh token should always be different due to rotation
        $this->assertNotSame($initialRefreshToken, $newRefreshToken, 'New refresh token should be different from the old one');
    }
}
