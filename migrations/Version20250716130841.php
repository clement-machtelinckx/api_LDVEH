<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250716130841 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adventure_history (id INT AUTO_INCREMENT NOT NULL, book_id INT DEFAULT NULL, user_id INT DEFAULT NULL, book_title VARCHAR(255) DEFAULT NULL, adventurer_name VARCHAR(255) NOT NULL, finish_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_72B00E2F16A2B381 (book_id), INDEX IDX_72B00E2FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE adventure_history ADD CONSTRAINT FK_72B00E2F16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE adventure_history ADD CONSTRAINT FK_72B00E2FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adventure_history DROP FOREIGN KEY FK_72B00E2F16A2B381');
        $this->addSql('ALTER TABLE adventure_history DROP FOREIGN KEY FK_72B00E2FA76ED395');
        $this->addSql('DROP TABLE adventure_history');
    }
}
