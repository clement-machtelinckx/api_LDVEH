<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\Page;
use App\Entity\User;
use App\Entity\Adventure;
use App\Entity\Adventurer;
use App\Entity\AdventureHistory;
use App\Repository\AdventureRepository;
use Doctrine\ORM\EntityManagerInterface;

class AdventureService
{
    public function __construct(
        private AdventureRepository $adventureRepository,
        private EntityManagerInterface $em
    ) {}

    public function startAdventure(User $user, Book $book, Adventurer $adventurer): Adventure
    {

        $existing = $this->adventureRepository->findOneBy(['user' => $user, 'book' => $book, 'isFinished' => false]);

        if ($existing) {
            $this->em->remove($existing);
            $this->em->flush();
        }

        $startPage = $book->getPage()->first();
        if (!$startPage instanceof Page) {
            throw new \LogicException("Le livre n’a pas de page de départ.");
        }

        $adventure = new Adventure();
        $adventure->setUser($user);
        $adventure->setBook($book);
        $adventure->setAdventurer($adventurer);
        $adventure->setCurrentPage($startPage);
        $adventure->setFromLastPage($startPage); // Au début, c’est la même
        $adventure->setIsFinished(false);

        $this->em->persist($adventure);
        $this->em->flush();

        return $adventure;
    }

    public function updatePage(Adventure $adventure, Page $newPage, Page $fromPage): void
    {
        $adventure->setCurrentPage($newPage);
        $adventure->setFromLastPage($fromPage);
        $this->em->flush();
    }

public function finishAdventure(Adventure $adventure): void
{
    $adventure->setIsFinished(true);
    $adventure->setEndedAt(new \DateTimeImmutable());
    
    // Snapshot de la victoire
    if ($adventure->getCurrentPage()?->isVictory()) {
        $history = new AdventureHistory();
        $history->setUser($adventure->getUser());
        $history->setBook($adventure->getBook());
        $history->setBookTitle($adventure->getBook()->getTitle());
        $history->setAdventurerName($adventure->getAdventurer()->getAdventurerName());
        $history->setFinishAt(new \DateTimeImmutable());

        $this->em->persist($history);
    }

    $this->em->flush();
}

}
