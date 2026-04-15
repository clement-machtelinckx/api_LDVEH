<?php

namespace App\Enum;

enum EquipmentType: string
{
    case Weapon = 'weapon';
    case SpecialObject = 'special_object';
    case Potion = 'potion';
    case Meal = 'meal';
    case BackpackItem = 'backpack_item';
    case QuestItem = 'quest_item';

    public function label(): string
    {
        return match ($this) {
            self::Weapon => 'Arme',
            self::SpecialObject => 'Objet Spécial',
            self::Potion => 'Potion',
            self::Meal => 'Repas',
            self::BackpackItem => 'Sac à Dos',
            self::QuestItem => 'Objet de Quête',
        };
    }

    public function goesInBackpack(): bool
    {
        return in_array($this, [self::Potion, self::Meal, self::BackpackItem]);
    }

    public function isConsumable(): bool
    {
        return in_array($this, [self::Potion, self::Meal]);
    }
}
