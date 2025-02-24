<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Page;
use App\Entity\Choice;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class BookFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $book = new Book();
        $book->setTitle("Le livre de la jungle");
        $book->setDescription("blablabla");
        $manager->persist($book);

        $pages = [];
        for ($i = 1; $i <= 5; $i++) {
            $page = new Page();
            $page->setContent("Contenu de la page " . $i);
            $page->setBook($book);
            $manager->persist($page);
            $pages[] = $page;
        }
        
        foreach ($pages as $page) {
            for ($j = 1; $j <= 2; $j++) {
                $choice = new Choice();
                $choice->setText("Choix " . $j . " pour la page " . $page->getId());
                // Redirige aléatoirement vers une page existante
                $randomPage = $pages[array_rand($pages)];
                $choice->setPage($page);
                $choice->setNextPage($randomPage);
                $page->addChoice($choice);
                $manager->persist($choice);
            }
        }
        $manager->flush();
    }
}
