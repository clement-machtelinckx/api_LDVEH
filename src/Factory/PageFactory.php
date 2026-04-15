<?php

namespace App\Factory;

use App\Entity\Book;
use App\Entity\Monster;
use App\Entity\Page;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends PersistentProxyObjectFactory<Page>
 */
final class PageFactory extends PersistentProxyObjectFactory
{
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Page::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'book' => BookFactory::new(),
            'pageNumber' => self::faker()->numberBetween(1, 350),
            'content' => self::faker()->paragraph(),
            'monster' => null,
            'combatIsBlocking' => false,
            'endingType' => null,
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }

    public function forBook(BookFactory|Book|Proxy $book): static
    {
        return $this->with([
            'book' => $book,
        ]);
    }

    public function withPageNumber(int $pageNumber): static
    {
        return $this->with([
            'pageNumber' => $pageNumber,
        ]);
    }

    public function withMonster(): static
    {
        return $this->with([
            'monster' => MonsterFactory::new(),
            'combatIsBlocking' => true,
        ]);
    }

    public function withSpecificMonster(MonsterFactory|Monster|Proxy $monster, bool $isBlocking = true): static
    {
        return $this->with([
            'monster' => $monster,
            'combatIsBlocking' => $isBlocking,
        ]);
    }

    public function withCombat(bool $isBlocking = true): static
    {
        return $this->with([
            'monster' => MonsterFactory::new(),
            'combatIsBlocking' => $isBlocking,
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

    public function normal(): static
    {
        return $this->with([
            'endingType' => null,
            'monster' => null,
            'combatIsBlocking' => false,
        ]);
    }
}