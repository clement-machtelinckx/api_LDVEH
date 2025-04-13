<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250413160106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fight_history (id INT AUTO_INCREMENT NOT NULL, adventurer_id INT NOT NULL, monster_id INT DEFAULT NULL, victory TINYINT(1) DEFAULT NULL, INDEX IDX_34D90E219BF95FB8 (adventurer_id), INDEX IDX_34D90E21C5FF1223 (monster_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fight_history ADD CONSTRAINT FK_34D90E219BF95FB8 FOREIGN KEY (adventurer_id) REFERENCES adventurer (id)');
        $this->addSql('ALTER TABLE fight_history ADD CONSTRAINT FK_34D90E21C5FF1223 FOREIGN KEY (monster_id) REFERENCES monster (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fight_history DROP FOREIGN KEY FK_34D90E219BF95FB8');
        $this->addSql('ALTER TABLE fight_history DROP FOREIGN KEY FK_34D90E21C5FF1223');
        $this->addSql('DROP TABLE fight_history');
    }
}
