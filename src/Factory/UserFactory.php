<?php

namespace App\Factory;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public static function class(): string
    {
        return User::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'email' => self::faker()->unique()->safeEmail(),
            'password' => 'password',
            'roles' => ['ROLE_USER'],
        ];
    }

    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function (User $user): void {
                $password = $user->getPassword();

                // Hash uniquement si le mot de passe semble être en clair
                if ($password && strlen($password) < 50) {
                    $user->setPassword(
                        $this->passwordHasher->hashPassword($user, $password)
                    );
                }
            });
    }

    public function asAdmin(): static
    {
        return $this->with([
            'roles' => ['ROLE_ADMIN', 'ROLE_USER'],
        ]);
    }

    public function asUser(): static
    {
        return $this->with([
            'roles' => ['ROLE_USER'],
        ]);
    }

    public function withEmail(string $email): static
    {
        return $this->with([
            'email' => $email,
        ]);
    }

    public function withPlainPassword(string $plainPassword): static
    {
        return $this->with([
            'password' => $plainPassword,
        ]);
    }
}