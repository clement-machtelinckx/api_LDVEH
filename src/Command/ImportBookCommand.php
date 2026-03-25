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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:import-books')]
class ImportBookCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('delete', 'd', InputOption::VALUE_NONE, 'Supprimer tous les livres existants avant import');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('delete')) {
            $output->writeln('<comment>Suppression des livres existants...</comment>');
            $this->em->getConnection()->executeStatement('SET FOREIGN_KEY_CHECKS=0');
            $this->em->getConnection()->executeStatement('DELETE FROM choice');
            $this->em->getConnection()->executeStatement('DELETE FROM monster');
            $this->em->getConnection()->executeStatement('DELETE FROM page');
            $this->em->getConnection()->executeStatement('DELETE FROM book');
            $this->em->getConnection()->executeStatement('SET FOREIGN_KEY_CHECKS=1');
            $output->writeln('<info>Base nettoyee.</info>');
        }

        $booksToImport = [
            ['file' => 'book01_maitre_tenebre.json', 'title' => 'Loup Solitaire - Les Maîtres des Ténèbres'],
            ['file' => 'book02_traversee_infernal.json', 'title' => 'Loup Solitaire - La Traversée Infernale'],
            ['file' => 'book03_Les_Grottes_de_Kalte.json', 'title' => 'Loup Solitaire - Les Grottes de Kalte'],
            ['file' => 'book04_Le_Gouffre_Maudit.json', 'title' => 'Loup Solitaire - Le Gouffre Maudit'],
        ];

        foreach ($booksToImport as $bookData) {
            $jsonPath = __DIR__ . '/../../' . $bookData['file'];

            if (!file_exists($jsonPath)) {
                $output->writeln("<error>❌ Fichier introuvable : {$bookData['file']}</error>");
                continue;
            }

            $data = json_decode(file_get_contents($jsonPath), true);

            $book = new Book();
            $book->setTitle($bookData['title']);
            $book->setAuthor('Joe Dever');
            $book->setDescription('Importé automatiquement depuis le PDF');
            $this->em->persist($book);

            $pageMap = [];

            foreach ($data as $entry) {
                $page = new Page();
                $page->setPageNumber($entry['pageNumber']);
                $page->setContent($entry['content']);
                $page->setBook($book);
                $page->setCombatIsBlocking($entry['isBlocking'] ?? false);

                if (isset($entry['endingType'])) {
                    $page->setEndingType($entry['endingType']);
                }

                // Stocker les events d'inventaire/stats en JSON
                $events = [];
                if (!empty($entry['itemsGained'])) $events['itemsGained'] = $entry['itemsGained'];
                if (!empty($entry['itemsLost'])) $events['itemsLost'] = $entry['itemsLost'];
                if (isset($entry['enduranceChange']) && $entry['enduranceChange'] !== null) $events['enduranceChange'] = $entry['enduranceChange'];
                if (isset($entry['abilityChange']) && $entry['abilityChange'] !== null) $events['abilityChange'] = $entry['abilityChange'];
                if (!empty($events)) {
                    $page->setEvents($events);
                }

                if (isset($entry['monster']) && $entry['monster']) {
                    $monsters = $entry['monster'];
                    if (isset($monsters['monsterName'])) {
                        $monsters = [$monsters];
                    }
                    foreach ($monsters as $i => $monsterData) {
                        if (empty($monsterData['ability']) || empty($monsterData['endurance'])) {
                            continue;
                        }
                        $monster = new Monster();
                        $monster->setMonsterName($monsterData['monsterName']);
                        $monster->setAbility($monsterData['ability']);
                        $monster->setEndurance($monsterData['endurance']);
                        $this->em->persist($monster);
                        if ($page->getMonster() === null) {
                            $page->setMonster($monster);
                        }
                    }
                }

                $this->em->persist($page);
                $pageMap[$entry['pageNumber']] = $page;
            }

            $this->em->flush();

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
                    if (!empty($choiceData['condition'])) {
                        $choice->setCondition($choiceData['condition']);
                    }
                    $this->em->persist($choice);
                }
            }

            $this->em->flush();
            $output->writeln("✅ Livre importé : <info>{$bookData['title']}</info>");
        }

        $output->writeln('<comment>📚 Tous les livres ont été importés avec succès !</comment>');
        return Command::SUCCESS;
    }
}
