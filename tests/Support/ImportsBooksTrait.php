<?php

namespace App\Tests\Support;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

trait ImportsBooksTrait
{
    protected function importBooksIfNeeded(): void
    {
        $bookRepository = static::getContainer()->get(BookRepository::class);

        if ($bookRepository->count([]) > 0) {
            return;
        }

        $application = new Application(static::bootKernel());
        $application->setAutoExit(false);

        $command = $application->find('app:import-books');
        $tester = new CommandTester($command);
        $tester->execute([
            '--env' => 'test',
        ]);

        static::ensureKernelShutdown();
    }
}