<?php

namespace App\Factory;

use App\Entity\Book;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Book>
 */
final class BookFactory extends PersistentProxyObjectFactory
{
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Book::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'title' => self::faker()->sentence(3),
            'author' => self::faker()->name(),
            'description' => self::faker()->paragraph(),
            'publicationDate' => self::faker()->dateTimeBetween('-50 years', 'now'),
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }

    public function titled(string $title): static
    {
        return $this->with([
            'title' => $title,
        ]);
    }

    public function book1(): static
    {
        return $this->with([
            'title' => 'Loup Solitaire - Les Maîtres des Ténèbres',
            'author' => 'Joe Dever',
        ]);
    }

    public function book2(): static
    {
        return $this->with([
            'title' => 'Loup Solitaire - La Traversée Infernale',
            'author' => 'Joe Dever',
        ]);
    }

    public function book3(): static
    {
        return $this->with([
            'title' => 'Loup Solitaire - Les Grottes de Kalte',
            'author' => 'Joe Dever',
        ]);
    }

    public function book4(): static
    {
        return $this->with([
            'title' => 'Loup Solitaire - Le Gouffre Maudit',
            'author' => 'Joe Dever',
        ]);
    }
}