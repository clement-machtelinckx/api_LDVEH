<?php

namespace App\Factory;

use App\Entity\Page;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Page>
 */
final class PageFactory extends PersistentProxyObjectFactory
{
    private static int $pageNumberCounter = 1;

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Page::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'book' => BookFactory::new(),
            'pageNumber' => self::$pageNumberCounter++,
            'content' => self::faker()->paragraphs(3, true),
            'combatIsBlocking' => false,
            'endingType' => null,
            'monster' => null,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Page $page): void {})
        ;
    }

    public function withMonster(): static
    {
        return $this->with([
            'monster' => MonsterFactory::new(),
            'combatIsBlocking' => true,
        ]);
    }

    public function asVictoryEnding(): static
    {
        return $this->with([
            'endingType' => 'victory',
        ]);
    }

    public function asDeathEnding(): static
    {
        return $this->with([
            'endingType' => 'death',
        ]);
    }

    public function withCombat(bool $isBlocking = true): static
    {
        return $this->with([
            'monster' => MonsterFactory::new(),
            'combatIsBlocking' => $isBlocking,
        ]);
    }
}
