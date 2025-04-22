<?php
// src/Command/ImportBookCommand.php

namespace App\Command;

use App\Entity\Book;
use App\Entity\Page;
use App\Entity\Choice;
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
        $json = file_get_contents(__DIR__ . '/../../parsed_book.json');
        $data = json_decode($json, true);

        $book = new Book();
        $book->setTitle('Loup Solitaire - Les Maîtres des Ténèbres');
        $book->setDescription('Importé automatiquement depuis le PDF');
        $this->em->persist($book);

        // map temporaire pour retrouver les pages par numéro
        $pageMap = [];

        // Création des pages sans les choix
        foreach ($data as $entry) {
            $page = new Page();
            $page->setPageNumber($entry['pageNumber']);
            $page->setContent($entry['content']);
            $page->setBook($book);
            $this->em->persist($page);

            $pageMap[$entry['pageNumber']] = $page;
        }

        $this->em->flush(); // Pour que toutes les pages aient un ID

        // Création des choix maintenant que toutes les pages existent
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

        $output->writeln('📘 Livre importé avec succès !');
        return Command::SUCCESS;
    }
}
