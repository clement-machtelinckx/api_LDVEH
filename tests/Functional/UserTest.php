<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserTest extends ApiTestCase
{
    public function testRegisterSuccess(): void
    {
        $this->browser()
            ->post('/api/register', [
                'json' => [
                    'email' => 'newuser@test.com',
                    'password' => 'StrongP@ss1',
                ],
            ])
            ->assertStatus(200)
            ->assertJsonMatches('message', 'Utilisateur créé avec succès.');

        /** @var UserRepository $users */
        $users = static::getContainer()->get(UserRepository::class);
        /** @var UserPasswordHasherInterface $hasher */
        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        $user = $users->findOneBy(['email' => 'newuser@test.com']);

        $this->assertNotNull($user);
        $this->assertSame(['ROLE_USER'], $user->getRoles());
        $this->assertNotSame('StrongP@ss1', $user->getPassword());
        $this->assertTrue($hasher->isPasswordValid($user, 'StrongP@ss1'));
    }

    public function testRegisterFailsWhenPasswordIsTooWeak(): void
    {
        $this->browser()
            ->post('/api/register', [
                'json' => [
                    'email' => 'weak@test.com',
                    'password' => 'weak',
                ],
            ])
            ->assertStatus(400)
            ->assertJsonMatches('error', 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, un chiffre et un caractère spécial.');
    }

    public function testRegisterFailsWhenEmailAlreadyExists(): void
    {
        UserFactory::new()
            ->withEmail('existing@test.com')
            ->withPlainPassword('StrongP@ss1')
            ->create();

        $this->browser()
            ->post('/api/register', [
                'json' => [
                    'email' => 'existing@test.com',
                    'password' => 'StrongP@ss1',
                ],
            ])
            ->assertStatus(409)
            ->assertJsonMatches('error', 'Cet utilisateur existe déjà');
    }

    public function testRegisterFailsWhenPayloadIsMissing(): void
    {
        $this->browser()
            ->post('/api/register', [
                'json' => [
                    'email' => 'missing@test.com',
                ],
            ])
            ->assertStatus(400)
            ->assertJsonMatches('error', 'Email et mot de passe requis');
    }

    public function testLoginSuccessReturnsTokenRefreshTokenAndUser(): void
    {
        UserFactory::new()
            ->withEmail('login@test.com')
            ->withPlainPassword('StrongP@ss1')
            ->create();

        $response = $this->browser()
            ->post('/api/login', [
                'json' => [
                    'email' => 'login@test.com',
                    'password' => 'StrongP@ss1',
                ],
            ])
            ->assertStatus(200);

        $data = $response->json()->decoded();

        $this->assertArrayHasKey('token', $data);
        $this->assertArrayHasKey('refresh_token', $data);

        $this->assertNotEmpty($data['token']);
        $this->assertNotEmpty($data['refresh_token']);
    }

    public function testLoginFailsWithBadPassword(): void
    {
        UserFactory::new()
            ->withEmail('badpass@test.com')
            ->withPlainPassword('StrongP@ss1')
            ->create();

        $this->browser()
            ->post('/api/login', [
                'json' => [
                    'email' => 'badpass@test.com',
                    'password' => 'WrongP@ss1',
                ],
            ])
            ->assertStatus(401);
    }

    public function testRefreshTokenSuccessReturnsNewTokens(): void
    {
        UserFactory::new()
            ->withEmail('refresh@test.com')
            ->withPlainPassword('StrongP@ss1')
            ->create();

       $loginResponse = $this->browser()
            ->post('/api/login', [
                'json' => [
                    'email' => 'refresh@test.com',
                    'password' => 'StrongP@ss1',
                ],
            ])
            ->assertStatus(200);

        $loginData = $loginResponse->json()->decoded();
        $oldRefreshToken = $loginData['refresh_token'];

        $refreshResponse = $this->browser()
            ->post('/api/token/refresh', [
                'json' => [
                    'refresh_token' => $oldRefreshToken,
                ],
            ])
            ->assertStatus(200);

        $refreshData = $refreshResponse->json()->decoded();

        $this->assertArrayHasKey('token', $refreshData);
        $this->assertArrayHasKey('refresh_token', $refreshData);
        $this->assertNotEmpty($refreshData['token']);
        $this->assertNotEmpty($refreshData['refresh_token']);
        $this->assertNotSame($oldRefreshToken, $refreshData['refresh_token']);
    }

    public function testRefreshTokenFailsWhenMissing(): void
    {
        $this->browser()
            ->post('/api/token/refresh', [
                'json' => [],
            ])
            ->assertStatus(400)
            ->assertJsonMatches('error', 'refresh_token is required');
    }

    public function testRefreshTokenFailsWhenInvalid(): void
    {
        $this->browser()
            ->post('/api/token/refresh', [
                'json' => [
                    'refresh_token' => 'invalid-refresh-token',
                ],
            ])
            ->assertStatus(401)
            ->assertJsonMatches('error', 'Invalid refresh token');
    }

    public function testOldRefreshTokenBecomesInvalidAfterRotation(): void
    {
        UserFactory::new()
            ->withEmail('rotation@test.com')
            ->withPlainPassword('StrongP@ss1')
            ->create();

        $loginResponse = $this->browser()
            ->post('/api/login', [
                'json' => [
                    'email' => 'rotation@test.com',
                    'password' => 'StrongP@ss1',
                ],
            ])
            ->assertStatus(200);

        $loginData = $loginResponse->json()->decoded();
        $oldRefreshToken = $loginData['refresh_token'];

        $this->browser()
            ->post('/api/token/refresh', [
                'json' => [
                    'refresh_token' => $oldRefreshToken,
                ],
            ])
            ->assertStatus(200);

        $this->browser()
            ->post('/api/token/refresh', [
                'json' => [
                    'refresh_token' => $oldRefreshToken,
                ],
            ])
            ->assertStatus(401)
            ->assertJsonMatches('error', 'Invalid refresh token');
    }

    public function testProtectedRouteRequiresAuthentication(): void
    {
        $this->browser()
            ->get('/api/my-adventurers')
            ->assertStatus(401);
    }

    public function testProtectedRouteWorksWithValidJwt(): void
    {
        $user = UserFactory::new()
            ->withEmail('secured@test.com')
            ->withPlainPassword('StrongP@ss1')
            ->create();

        $this->browser()
            ->get('/api/my-adventurers', [
                'server' => $this->authHeadersFor($user->_real()),
            ])
            ->assertStatus(200);
    }
}