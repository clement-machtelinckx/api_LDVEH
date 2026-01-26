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
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public static function class(): string
    {
        return User::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'email' => self::faker()->unique()->safeEmail(),
            'password' => 'password', // Plain password, will be hashed in afterInstantiate
            'roles' => [],
        ];
    }
    
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function(User $user): void {
                $password = $user->getPassword();
                // Hash the password if it appears to be plain text (not already hashed)
                // Hashed passwords are typically much longer and contain special characters
                if ($password && strlen($password) < 50) {
                    $user->setPassword(
                        $this->passwordHasher->hashPassword($user, $password)
                    );
                }
            })
        ;
    }

    public function asAdmin(): static
    {
        return $this->with([
            'roles' => ['ROLE_ADMIN'],
        ]);
    }

    public function asUser(): static
    {
        return $this->with([
            'roles' => ['ROLE_USER'],
        ]);
    }

    public function withPlainPassword(string $plainPassword): static
    {
        return $this->with([
            'password' => $plainPassword,
        ]);
    }
}
