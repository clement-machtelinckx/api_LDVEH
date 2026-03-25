<?php

namespace App\Enum;

enum EquipmentSlot: string
{
    case Head = 'head';
    case Torso = 'torso';
    case Feet = 'feet';
    case Hands = 'hands';
    case Back = 'back';

    public function label(): string
    {
        return match ($this) {
            self::Head => 'Tête',
            self::Torso => 'Torse',
            self::Feet => 'Pieds',
            self::Hands => 'Mains',
            self::Back => 'Dos (cape)',
        };
    }
}
