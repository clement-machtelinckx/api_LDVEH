<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250107145906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, author VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, publication_date DATETIME DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE choice (id INT AUTO_INCREMENT NOT NULL, page_id INT NOT NULL, next_page_id INT DEFAULT NULL, text VARCHAR(255) DEFAULT NULL, INDEX IDX_C1AB5A92C4663E4 (page_id), INDEX IDX_C1AB5A92CEC84946 (next_page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page (id INT AUTO_INCREMENT NOT NULL, book_id INT NOT NULL, content LONGTEXT DEFAULT NULL, INDEX IDX_140AB62016A2B381 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // $this->addSql('ALTER TABLE choice ADD page_id INT NOT NULL');
        // $this->addSql('ALTER TABLE choice ADD next_page_id INT DEFAULT NULL');

        $this->addSql('ALTER TABLE choice ADD CONSTRAINT FK_C1AB5A92C4663E4 FOREIGN KEY (page_id) REFERENCES page (id)');
        $this->addSql('ALTER TABLE choice ADD CONSTRAINT FK_C1AB5A92CEC84946 FOREIGN KEY (next_page_id) REFERENCES page (id)');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB62016A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE choice DROP FOREIGN KEY FK_C1AB5A92C4663E4');
        $this->addSql('ALTER TABLE choice DROP FOREIGN KEY FK_C1AB5A92CEC84946');
        $this->addSql('ALTER TABLE choice DROP page_id');
        $this->addSql('ALTER TABLE choice DROP next_page_id');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB62016A2B381');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE choice');
        $this->addSql('DROP TABLE page');
    }
}
