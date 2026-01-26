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
        // Use lazy references instead of creating entities immediately
        return [
            'user' => UserFactory::new(),
            'adventurer' => AdventurerFactory::new(),
            'book' => BookFactory::new(),
            'currentPage' => PageFactory::new(),
            'startedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 month', 'now')),
            'isFinished' => false,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function(Adventure $adventure): void {
                // Ensure relational consistency
                // Make sure adventurer.user is the same as adventure.user
                if ($adventure->getAdventurer() && $adventure->getUser()) {
                    $adventure->getAdventurer()->setUser($adventure->getUser());
                }
                // Make sure currentPage.book is the same as adventure.book
                if ($adventure->getCurrentPage() && $adventure->getBook()) {
                    $adventure->getCurrentPage()->setBook($adventure->getBook());
                }
            })
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
