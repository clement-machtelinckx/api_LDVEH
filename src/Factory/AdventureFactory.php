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
     *
     * @todo inject services if required
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
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'adventurer' => AdventurerFactory::new(),
            'book' => BookFactory::new(),
            'currentPage' => PageFactory::new(),
            'startedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'user' => UserFactory::new(),
        ];
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
}
