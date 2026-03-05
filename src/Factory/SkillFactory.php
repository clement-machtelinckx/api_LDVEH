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
        $skills = [
            [
                'name' => 'Camouflage',
                'description' => 'Se fondre dans le décor.',
                'effect' => 'Permet de se cacher ou passer inaperçu.',
            ],
            [
                'name' => 'Chasse',
                'description' => 'Trouver de la nourriture en milieu naturel.',
                'effect' => 'Évite de dépenser un repas hors zone aride.',
            ],
            [
                'name' => 'Guérison',
                'description' => 'Récupération progressive.',
                'effect' => '+1 ENDURANCE hors combat sans dépasser le maximum.',
            ],
            [
                'name' => 'Maîtrise des Armes',
                'description' => 'Spécialisation martiale.',
                'effect' => '+2 HABILETÉ avec l’arme maîtrisée.',
            ],
        ];

        $skill = self::faker()->randomElement($skills);

        return [
            'name' => $skill['name'],
            'description' => $skill['description'],
            'effect' => $skill['effect'],
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }

    public function named(string $name, string $description = 'Description', string $effect = 'Effet'): static
    {
        return $this->with([
            'name' => $name,
            'description' => $description,
            'effect' => $effect,
        ]);
    }

    public function camouflage(): static
    {
        return $this->with([
            'name' => 'Camouflage',
            'description' => 'Se fondre dans le décor.',
            'effect' => 'Permet de se cacher ou passer inaperçu.',
        ]);
    }

    public function healing(): static
    {
        return $this->with([
            'name' => 'Guérison',
            'description' => 'Récupération progressive.',
            'effect' => '+1 ENDURANCE hors combat sans dépasser le maximum.',
        ]);
    }

    public function weaponMastery(): static
    {
        return $this->with([
            'name' => 'Maîtrise des Armes',
            'description' => 'Spécialisation martiale.',
            'effect' => '+2 HABILETÉ avec l’arme maîtrisée.',
        ]);
    }
}