<?php

namespace App\EventListener;

use App\Entity\Choice;
use App\Entity\Page;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;

#[AsEntityListener(event: 'prePersist', entity: Choice::class)]
#[AsEntityListener(event: 'preUpdate', entity: Choice::class)]
class ChoiceListener
{
    public function prePersist(Choice $choice, PrePersistEventArgs $event): void
    {
        $this->handleNextPage($choice, $event->getObjectManager());
    }

    public function preUpdate(Choice $choice, PreUpdateEventArgs $event): void
    {
        $this->handleNextPage($choice, $event->getObjectManager());
    }

    private function handleNextPage(Choice $choice, $em): void
    {
        if ($choice->getNextPageNumber() !== null && $choice->getNextPage() === null) {
            $existingPage = $em->getRepository(Page::class)->findOneBy(['pageNumber' => $choice->getNextPageNumber()]);
    
            if (!$existingPage) {
                $newPage = new Page();
                $newPage->setPageNumber($choice->getNextPageNumber());
    
                // Associer le même livre que la page actuelle
                if ($choice->getPage() !== null) {
                    $newPage->setBook($choice->getPage()->getBook());
                }
    
                $em->persist($newPage);
                $choice->setNextPage($newPage);
            } else {
                $choice->setNextPage($existingPage);
            }
        }
    }
    
}
