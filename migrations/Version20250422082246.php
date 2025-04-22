<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250422082246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adventure (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, book_id INT NOT NULL, adventurer_id INT NOT NULL, current_page_id INT NOT NULL, from_last_page_id INT DEFAULT NULL, started_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ended_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_finished TINYINT(1) DEFAULT NULL, INDEX IDX_9E858E0FA76ED395 (user_id), INDEX IDX_9E858E0F16A2B381 (book_id), UNIQUE INDEX UNIQ_9E858E0F9BF95FB8 (adventurer_id), INDEX IDX_9E858E0FA64BE661 (current_page_id), INDEX IDX_9E858E0F578C854A (from_last_page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE adventure ADD CONSTRAINT FK_9E858E0FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE adventure ADD CONSTRAINT FK_9E858E0F16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE adventure ADD CONSTRAINT FK_9E858E0F9BF95FB8 FOREIGN KEY (adventurer_id) REFERENCES adventurer (id)');
        $this->addSql('ALTER TABLE adventure ADD CONSTRAINT FK_9E858E0FA64BE661 FOREIGN KEY (current_page_id) REFERENCES page (id)');
        $this->addSql('ALTER TABLE adventure ADD CONSTRAINT FK_9E858E0F578C854A FOREIGN KEY (from_last_page_id) REFERENCES page (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adventure DROP FOREIGN KEY FK_9E858E0FA76ED395');
        $this->addSql('ALTER TABLE adventure DROP FOREIGN KEY FK_9E858E0F16A2B381');
        $this->addSql('ALTER TABLE adventure DROP FOREIGN KEY FK_9E858E0F9BF95FB8');
        $this->addSql('ALTER TABLE adventure DROP FOREIGN KEY FK_9E858E0FA64BE661');
        $this->addSql('ALTER TABLE adventure DROP FOREIGN KEY FK_9E858E0F578C854A');
        $this->addSql('DROP TABLE adventure');
    }
}
