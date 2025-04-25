<?php
// src/Command/ImportBookCommand.php

namespace App\Command;

use App\Entity\Book;
use App\Entity\Page;
use App\Entity\Choice;
use App\Entity\Monster;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:import-book')]
class ImportBookCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $json = file_get_contents(__DIR__ . '/../../book04_Le_Gouffre_Maudit.json');
        $data = json_decode($json, true);

        $book = new Book();
        $book->setTitle('Loup Solitaire - Gouffre Maudit');
        $book->setAuthor('Joe Dever');
        $book->setDescription('Importé automatiquement depuis le PDF');
        $this->em->persist($book);

        $pageMap = [];

        foreach ($data as $entry) {
            $page = new Page();
            $page->setPageNumber($entry['pageNumber']);
            $page->setContent($entry['content']);
            $page->setCombatIsBlocking($entry['isBlocking'] ?? false);
            $page->setBook($book);

            // Optional: endingType ("death" ou "victory")
            if (isset($entry['endingType'])) {
                $page->setEndingType($entry['endingType']); 
            }

            // Optional: monster
            if (isset($entry['monster'])) {
                $monsterData = $entry['monster'];
                $monster = new Monster();
                $monster->setMonsterName($monsterData['monsterName']);
                $monster->setAbility($monsterData['ability']);
                $monster->setEndurance($monsterData['endurance']);
                $this->em->persist($monster);

                $page->setMonster($monster); 
            }

            $this->em->persist($page);
            $pageMap[$entry['pageNumber']] = $page;
        }

        $this->em->flush();

        // Création des choix
        foreach ($data as $entry) {
            if (!isset($entry['choices'])) {
                continue;
            }

            $fromPage = $pageMap[$entry['pageNumber']] ?? null;
            if (!$fromPage) continue;

            foreach ($entry['choices'] as $choiceData) {
                $choice = new Choice();
                $choice->setText($choiceData['text']);
                $choice->setPage($fromPage);
                $choice->setNextPage($pageMap[$choiceData['nextPage']] ?? null);
                $this->em->persist($choice);
            }
        }

        $this->em->flush();

        $output->writeln('📘 Livre importé avec succès avec endings + monstres !');
        return Command::SUCCESS;
    }
}
