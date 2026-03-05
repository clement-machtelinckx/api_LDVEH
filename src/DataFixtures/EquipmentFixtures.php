<?php

namespace App\DataFixtures;

use App\Entity\Equipment;
use App\Enum\EquipmentSlot;
use App\Enum\EquipmentType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class EquipmentFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['equipment', 'game'];
    }

    public function load(ObjectManager $manager): void
    {
        $equipments = [
            // ── Armes (portées à la main, max 2) ──
            ['name' => 'Hache',            'slug' => 'hache',        'type' => EquipmentType::Weapon],  // arme de départ
            ['name' => 'Épée',             'slug' => 'epee',         'type' => EquipmentType::Weapon],  // table 1
            ['name' => 'Masse d\'Armes',   'slug' => 'masse_armes', 'type' => EquipmentType::Weapon],  // table 5
            ['name' => 'Bâton',            'slug' => 'baton',        'type' => EquipmentType::Weapon],  // table 7
            ['name' => 'Lance',            'slug' => 'lance',        'type' => EquipmentType::Weapon],  // table 8
            ['name' => 'Glaive',           'slug' => 'glaive',       'type' => EquipmentType::Weapon],  // table 0

            // ── Objets Spéciaux (pas dans le sac à dos) ──
            ['name' => 'Carte Géographique', 'slug' => 'carte_geographique', 'type' => EquipmentType::SpecialObject],                                              // départ
            ['name' => 'Casque',             'slug' => 'casque',             'type' => EquipmentType::SpecialObject, 'slot' => EquipmentSlot::Head, 'enduranceBonus' => 2],  // table 2
            ['name' => 'Cotte de Mailles',   'slug' => 'cotte_mailles',     'type' => EquipmentType::SpecialObject, 'slot' => EquipmentSlot::Torso, 'enduranceBonus' => 4], // table 4

            // ── Sac à Dos (max 8 objets, repas inclus) ──
            ['name' => 'Repas',              'slug' => 'repas',             'type' => EquipmentType::Meal],                                 // départ (x1), table 3 (x2)
            ['name' => 'Potion de Guérison', 'slug' => 'potion_guerison',  'type' => EquipmentType::Potion, 'healAmount' => 4],             // table 6, +4 END après combat
        ];

        foreach ($equipments as $data) {
            $equipment = new Equipment();
            $equipment->setName($data['name']);
            $equipment->setSlug($data['slug']);
            $equipment->setType($data['type']);
            $equipment->setSlot($data['slot'] ?? null);
            $equipment->setEnduranceBonus($data['enduranceBonus'] ?? 0);
            $equipment->setHealAmount($data['healAmount'] ?? 0);

            $manager->persist($equipment);
        }

        $manager->flush();
    }
}
