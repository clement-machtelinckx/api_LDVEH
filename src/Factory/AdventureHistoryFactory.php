<?php

namespace App\Factory;

use App\Entity\AdventureHistory;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<AdventureHistory>
 */
final class AdventureHistoryFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return AdventureHistory::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return function() {
            $book = BookFactory::new()->create();
            
            return [
                'user' => UserFactory::new(),
                'book' => $book,
                'bookTitle' => $book->getTitle(),
                'adventurerName' => self::faker()->firstName(),
                'finishAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 month', 'now')),
            ];
        };
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(AdventureHistory $adventureHistory): void {})
        ;
    }
}
