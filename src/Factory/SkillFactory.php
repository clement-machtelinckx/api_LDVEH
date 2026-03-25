<?php

namespace App\Factory;

use App\Entity\Skill;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Skill>
 */
final class SkillFactory extends PersistentProxyObjectFactory
{
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Skill::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->word(),
            'slug' => self::faker()->unique()->slug(2),
            'description' => self::faker()->sentence(),
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }

    public function named(string $name, string $slug, string $description = 'Description'): static
    {
        return $this->with([
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
        ]);
    }

    public function camouflage(): static
    {
        return $this->with([
            'name' => 'Camouflage',
            'slug' => 'camouflage',
            'description' => 'Se fondre dans le décor.',
        ]);
    }

    public function healing(): static
    {
        return $this->with([
            'name' => 'Guérison',
            'slug' => 'guerison',
            'description' => 'Récupération progressive.',
        ]);
    }

    public function hunting(): static
    {
        return $this->with([
            'name' => 'Chasse',
            'slug' => 'chasse',
            'description' => 'Trouver de la nourriture en milieu naturel.',
        ]);
    }

    public function weaponMastery(): static
    {
        return $this->with([
            'name' => 'Maîtrise des Armes',
            'slug' => 'maitrise_armes',
            'description' => 'Spécialisation martiale.',
        ]);
    }

    public function psychicPower(): static
    {
        return $this->with([
            'name' => 'Puissance Psychique',
            'slug' => 'puissance_psychique',
            'description' => 'Attaque mentale en combat.',
        ]);
    }

    public function psychicShield(): static
    {
        return $this->with([
            'name' => 'Bouclier Psychique',
            'slug' => 'bouclier_psychique',
            'description' => 'Protection contre les agressions mentales.',
        ]);
    }
}