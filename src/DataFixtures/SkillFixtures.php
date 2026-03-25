<?php

namespace App\DataFixtures;

use App\Entity\Skill;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class SkillFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['skill', 'game'];
    }

    public function load(ObjectManager $manager): void
    {
        $skills = [
            ['name' => 'Camouflage',                      'slug' => 'camouflage',           'description' => 'Se fondre dans le décor, passer pour un habitant en ville, se cacher en sécurité.'],
            ['name' => 'Chasse',                           'slug' => 'chasse',               'description' => 'Ne pas mourir de faim (sauf régions arides). Aide à se déplacer vite et avec agilité.'],
            ['name' => 'Sixième Sens',                     'slug' => 'sixieme_sens',          'description' => 'Détecte les dangers imminents. Révèle les intentions d\'un inconnu ou la nature d\'un objet étrange.'],
            ['name' => 'Orientation',                      'slug' => 'orientation',           'description' => 'Choisir la bonne direction, suivre une piste, retrouver une personne ou un objet caché.'],
            ['name' => 'Guérison',                         'slug' => 'guerison',              'description' => '+1 ENDURANCE par paragraphe sans combat (si en dessous du total initial).'],
            ['name' => 'Maîtrise des Armes',               'slug' => 'maitrise_armes',        'description' => 'Tire une arme via la Table de Hasard. +2 HABILETÉ en combat avec cette arme.'],
            ['name' => 'Bouclier Psychique',               'slug' => 'bouclier_psychique',    'description' => 'Pas de perte d\'ENDURANCE en cas d\'agression mentale.'],
            ['name' => 'Puissance Psychique',              'slug' => 'puissance_psychique',   'description' => 'Attaque mentale : +2 HABILETÉ (certains ennemis peuvent y être insensibles).'],
            ['name' => 'Communication Animale',            'slug' => 'communication_animale', 'description' => 'Communiquer avec certains animaux, deviner les intentions de certains autres.'],
            ['name' => 'Maîtrise Psychique de la Matière', 'slug' => 'maitrise_psychique',    'description' => 'Déplacer de petits objets par concentration mentale.'],
        ];

        foreach ($skills as $data) {
            $skill = new Skill();
            $skill->setName($data['name']);
            $skill->setSlug($data['slug']);
            $skill->setDescription($data['description']);

            $manager->persist($skill);
        }

        $manager->flush();
    }
}
