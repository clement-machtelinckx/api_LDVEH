<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Page;
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

        for ($i = 1; $i <= 5; $i++) {
            $page = new Page();
            $page->setContent("Contenu de la page " . $i);
            $page->setBook($book);

            $manager->persist($page);
        }
        
        $manager->flush();
    }
}
