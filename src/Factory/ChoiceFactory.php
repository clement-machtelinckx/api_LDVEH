<?php

namespace App\Factory;

use App\Entity\Choice;
use App\Entity\Page;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends PersistentProxyObjectFactory<Choice>
 */
final class ChoiceFactory extends PersistentProxyObjectFactory
{
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Choice::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'page' => PageFactory::new(),
            'text' => self::faker()->sentence(),
            'requiresVictory' => false,
            'nextPage' => null,
            'nextPageNumber' => null,
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }

    public function fromPage(PageFactory|Page|Proxy $page): static
    {
        return $this->with([
            'page' => $page,
        ]);
    }

    public function withText(string $text): static
    {
        return $this->with([
            'text' => $text,
        ]);
    }

    public function withNextPage(): static
    {
        return $this->with([
            'nextPage' => PageFactory::new(),
        ]);
    }

    public function toPage(PageFactory|Page|Proxy $page): static
    {
        return $this->with([
            'nextPage' => $page,
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

    public function free(): static
    {
        return $this->with([
            'requiresVictory' => false,
        ]);
    }
}