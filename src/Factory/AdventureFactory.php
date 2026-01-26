<?php

namespace App\Factory;

use App\Entity\Adventure;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Adventure>
 */
final class AdventureFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Adventure::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return function() {
            // Create a user first
            $user = UserFactory::new()->create();
            
            // Create an adventurer with the same user
            $adventurer = AdventurerFactory::new()->create(['user' => $user]);
            
            // Create a book
            $book = BookFactory::new()->create();
            
            // Create a page for the book
            $currentPage = PageFactory::new()->create(['book' => $book]);
            
            return [
                'user' => $user,
                'adventurer' => $adventurer,
                'book' => $book,
                'currentPage' => $currentPage,
                'startedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 month', 'now')),
                'isFinished' => false,
            ];
        };
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Adventure $adventure): void {})
        ;
    }

    public function finished(): static
    {
        return $this->with([
            'isFinished' => true,
            'endedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 week', 'now')),
        ]);
    }

    public function inProgress(): static
    {
        return $this->with([
            'isFinished' => false,
            'endedAt' => null,
        ]);
    }
}
