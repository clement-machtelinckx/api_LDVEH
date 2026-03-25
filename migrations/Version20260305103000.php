<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260305103000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add avatar column to adventurer';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE adventurer ADD avatar VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE adventurer DROP avatar');
    }
}

