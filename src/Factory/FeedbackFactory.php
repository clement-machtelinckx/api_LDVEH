<?php

namespace App\Factory;

use App\Entity\Feedback;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Feedback>
 */
final class FeedbackFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Feedback::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'user' => UserFactory::new(),
            'email' => self::faker()->safeEmail(),
            'message' => self::faker()->paragraph(),
            'rating' => self::faker()->numberBetween(1, 5),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 month', 'now')),
            'status' => 'new',
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Feedback $feedback): void {})
        ;
    }

    public function asProcessed(): static
    {
        return $this->with([
            'status' => 'processed',
        ]);
    }

    public function asResolved(): static
    {
        return $this->with([
            'status' => 'resolved',
        ]);
    }

    public function withHighRating(): static
    {
        return $this->with([
            'rating' => self::faker()->numberBetween(4, 5),
        ]);
    }

    public function withLowRating(): static
    {
        return $this->with([
            'rating' => self::faker()->numberBetween(1, 2),
        ]);
    }
}
