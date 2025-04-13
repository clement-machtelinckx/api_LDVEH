<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250413145710 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE choice ADD requires_victory TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE page ADD monster_id INT DEFAULT NULL, ADD combat_is_blocking TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB620C5FF1223 FOREIGN KEY (monster_id) REFERENCES monster (id)');
        $this->addSql('CREATE INDEX IDX_140AB620C5FF1223 ON page (monster_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE choice DROP requires_victory');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB620C5FF1223');
        $this->addSql('DROP INDEX IDX_140AB620C5FF1223 ON page');
        $this->addSql('ALTER TABLE page DROP monster_id, DROP combat_is_blocking');
    }
}
