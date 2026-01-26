<?php

namespace App\Factory;

use App\Entity\Equipment;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Equipment>
 */
final class EquipmentFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Equipment::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        $equipmentTypes = [
            ['name' => 'Sword', 'description' => 'A sharp blade', 'effect' => '+2 Ability'],
            ['name' => 'Shield', 'description' => 'A sturdy shield', 'effect' => '+2 Endurance'],
            ['name' => 'Healing Potion', 'description' => 'Restores health', 'effect' => '+4 Endurance'],
            ['name' => 'Magic Ring', 'description' => 'Enhances abilities', 'effect' => '+1 Ability'],
        ];
        
        $equipment = self::faker()->randomElement($equipmentTypes);
        
        return [
            'name' => $equipment['name'],
            'description' => $equipment['description'],
            'effect' => $equipment['effect'],
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Equipment $equipment): void {})
        ;
    }
}
