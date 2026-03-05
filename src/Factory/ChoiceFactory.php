<?php

namespace App\Factory;

use App\Entity\Choice;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Choice>
 */
final class ChoiceFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Choice::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'page' => PageFactory::new(),
            'text' => self::faker()->sentence(),
            'requiresVictory' => false,
            // nextPageNumber is intentionally left null to avoid triggering ChoiceListener
            // Use withNextPage() or withNextPageNumber() states to set it explicitly
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Choice $choice): void {})
        ;
    }

    public function withNextPage(): static
    {
        return $this->with([
            'nextPage' => PageFactory::new(),
        ]);
    }

    public function withNextPageNumber(int $pageNumber): static
    {
        return $this->with([
            'nextPageNumber' => $pageNumber,
        ]);
    }

    public function requiresVictory(): static
    {
        return $this->with([
            'requiresVictory' => true,
        ]);
    }
}
