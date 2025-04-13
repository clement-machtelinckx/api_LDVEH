<?php

// src/DataFixtures/BookFixtures.php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Page;
use App\Entity\Choice;
use App\Entity\Monster;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class BookFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // BOOK
        $book = new Book();
        $book->setTitle("Le livre de la jungle");
        $book->setDescription("Un livre plein d'aventures et de dangers.");
        $manager->persist($book);

        // MONSTERS
        $monsters = [];
        $monsterNames = ['Kroshul', 'Mamba Rouge', 'Tigre Fantôme', 'Serpent Géant'];
        foreach ($monsterNames as $name) {
            $monster = new Monster();
            $monster->setMonsterName($name);
            $monster->setAbility(rand(5, 10));
            $monster->setEndurance(rand(5, 10));
            $manager->persist($monster);
            $monsters[] = $monster;
        }

        // PAGES
        $pages = [];
        for ($i = 1; $i <= 10; $i++) {
            $page = new Page();
            $page->setContent("Contenu de la page " . $i);
            $page->setPageNumber($i);
            $page->setBook($book);

            // 1 page sur 3 a un monstre
            if ($i % 3 === 0) {
                $monster = $monsters[array_rand($monsters)];
                $page->setMonster($monster);
                $page->setCombatIsBlocking($i % 2 === 0); // 1 fois bloquant, 1 fois non
            }

            $manager->persist($page);
            $pages[] = $page;
        }

        // CHOICES
        foreach ($pages as $page) {
            for ($j = 1; $j <= 2; $j++) {
                $choice = new Choice();
                $choice->setText("Choix $j pour la page ");
                $choice->setPage($page);
                $choice->setNextPage($pages[array_rand($pages)]);
                $manager->persist($choice);
            }
        }

        $manager->flush();
    }
}
