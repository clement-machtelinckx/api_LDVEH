<?php

namespace App\Factory;

use App\Entity\Equipment;
use App\Enum\EquipmentSlot;
use App\Enum\EquipmentType;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Equipment>
 */
final class EquipmentFactory extends PersistentProxyObjectFactory
{
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Equipment::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->word(),
            'slug' => self::faker()->unique()->slug(2),
            'description' => self::faker()->sentence(),
            'type' => EquipmentType::BackpackItem,
            'slot' => null,
            'enduranceBonus' => 0,
            'healAmount' => 0,
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }

    public function weapon(string $slug = 'epee'): static
    {
        return $this->with([
            'name' => 'Épée',
            'slug' => $slug,
            'type' => EquipmentType::Weapon,
            'slot' => null,
        ]);
    }

    public function armor(string $slug = 'cotte_de_mailles', EquipmentSlot $slot = EquipmentSlot::Torso, int $bonus = 4): static
    {
        return $this->with([
            'name' => 'Cotte de Mailles',
            'slug' => $slug,
            'type' => EquipmentType::SpecialObject,
            'slot' => $slot,
            'enduranceBonus' => $bonus,
        ]);
    }

    public function helmet(int $bonus = 2): static
    {
        return $this->with([
            'name' => 'Casque',
            'slug' => 'casque',
            'type' => EquipmentType::SpecialObject,
            'slot' => EquipmentSlot::Head,
            'enduranceBonus' => $bonus,
        ]);
    }

    public function potion(int $healAmount = 4): static
    {
        return $this->with([
            'name' => 'Potion de Guérison',
            'slug' => 'potion_guerison',
            'type' => EquipmentType::Potion,
            'healAmount' => $healAmount,
        ]);
    }

    public function meal(): static
    {
        return $this->with([
            'name' => 'Repas',
            'slug' => 'repas',
            'type' => EquipmentType::Meal,
            'healAmount' => 0,
        ]);
    }

    public function questItem(string $slug = 'cle_doree'): static
    {
        return $this->with([
            'name' => 'Clé Dorée',
            'slug' => $slug,
            'type' => EquipmentType::QuestItem,
        ]);
    }
}