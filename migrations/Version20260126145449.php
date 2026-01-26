<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260126145449 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adventure (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, book_id INTEGER NOT NULL, adventurer_id INTEGER NOT NULL, current_page_id INTEGER NOT NULL, from_last_page_id INTEGER DEFAULT NULL, started_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , ended_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , is_finished BOOLEAN DEFAULT NULL, CONSTRAINT FK_9E858E0FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9E858E0F16A2B381 FOREIGN KEY (book_id) REFERENCES book (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9E858E0F9BF95FB8 FOREIGN KEY (adventurer_id) REFERENCES adventurer (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9E858E0FA64BE661 FOREIGN KEY (current_page_id) REFERENCES page (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9E858E0F578C854A FOREIGN KEY (from_last_page_id) REFERENCES page (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_9E858E0FA76ED395 ON adventure (user_id)');
        $this->addSql('CREATE INDEX IDX_9E858E0F16A2B381 ON adventure (book_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9E858E0F9BF95FB8 ON adventure (adventurer_id)');
        $this->addSql('CREATE INDEX IDX_9E858E0FA64BE661 ON adventure (current_page_id)');
        $this->addSql('CREATE INDEX IDX_9E858E0F578C854A ON adventure (from_last_page_id)');
        $this->addSql('CREATE TABLE adventure_history (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, book_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, book_title VARCHAR(255) DEFAULT NULL, adventurer_name VARCHAR(255) NOT NULL, finish_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_72B00E2F16A2B381 FOREIGN KEY (book_id) REFERENCES book (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_72B00E2FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_72B00E2F16A2B381 ON adventure_history (book_id)');
        $this->addSql('CREATE INDEX IDX_72B00E2FA76ED395 ON adventure_history (user_id)');
        $this->addSql('CREATE TABLE adventurer (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, adventurer_name VARCHAR(255) NOT NULL, ability INTEGER NOT NULL, endurance INTEGER NOT NULL, CONSTRAINT FK_FC286782A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_FC286782A76ED395 ON adventurer (user_id)');
        $this->addSql('CREATE TABLE book (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, author VARCHAR(255) DEFAULT NULL, description CLOB DEFAULT NULL, publication_date DATETIME DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE TABLE choice (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, page_id INTEGER NOT NULL, next_page_id INTEGER DEFAULT NULL, text VARCHAR(255) DEFAULT NULL, requires_victory BOOLEAN DEFAULT NULL, CONSTRAINT FK_C1AB5A92C4663E4 FOREIGN KEY (page_id) REFERENCES page (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C1AB5A92CEC84946 FOREIGN KEY (next_page_id) REFERENCES page (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_C1AB5A92C4663E4 ON choice (page_id)');
        $this->addSql('CREATE INDEX IDX_C1AB5A92CEC84946 ON choice (next_page_id)');
        $this->addSql('CREATE TABLE equipment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, effect CLOB DEFAULT NULL)');
        $this->addSql('CREATE TABLE feedback (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, email VARCHAR(255) NOT NULL, message CLOB NOT NULL, rating INTEGER DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , status VARCHAR(50) NOT NULL, CONSTRAINT FK_D2294458A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_D2294458A76ED395 ON feedback (user_id)');
        $this->addSql('CREATE TABLE fight_history (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, adventurer_id INTEGER NOT NULL, monster_id INTEGER DEFAULT NULL, victory BOOLEAN DEFAULT NULL, CONSTRAINT FK_34D90E219BF95FB8 FOREIGN KEY (adventurer_id) REFERENCES adventurer (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_34D90E21C5FF1223 FOREIGN KEY (monster_id) REFERENCES monster (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_34D90E219BF95FB8 ON fight_history (adventurer_id)');
        $this->addSql('CREATE INDEX IDX_34D90E21C5FF1223 ON fight_history (monster_id)');
        $this->addSql('CREATE TABLE monster (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, monster_name VARCHAR(255) NOT NULL, ability INTEGER NOT NULL, endurance INTEGER NOT NULL)');
        $this->addSql('CREATE TABLE page (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, book_id INTEGER NOT NULL, monster_id INTEGER DEFAULT NULL, content CLOB DEFAULT NULL, page_number INTEGER NOT NULL, combat_is_blocking BOOLEAN DEFAULT NULL, ending_type VARCHAR(50) DEFAULT NULL, CONSTRAINT FK_140AB62016A2B381 FOREIGN KEY (book_id) REFERENCES book (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_140AB620C5FF1223 FOREIGN KEY (monster_id) REFERENCES monster (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_140AB62016A2B381 ON page (book_id)');
        $this->addSql('CREATE INDEX IDX_140AB620C5FF1223 ON page (monster_id)');
        $this->addSql('CREATE TABLE refresh_token (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, token_hash VARCHAR(64) NOT NULL, expires_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , revoked_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , replaced_by_token_hash VARCHAR(64) DEFAULT NULL, CONSTRAINT FK_C74F2195A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C74F2195B3BC57DA ON refresh_token (token_hash)');
        $this->addSql('CREATE INDEX idx_user ON refresh_token (user_id)');
        $this->addSql('CREATE INDEX idx_expires_at ON refresh_token (expires_at)');
        $this->addSql('CREATE TABLE skill (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, effect CLOB DEFAULT NULL)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, firstname VARCHAR(100) DEFAULT NULL, lastname VARCHAR(100) DEFAULT NULL, nickname VARCHAR(100) DEFAULT NULL, gender VARCHAR(20) DEFAULT NULL, date_of_birth DATETIME DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE adventure');
        $this->addSql('DROP TABLE adventure_history');
        $this->addSql('DROP TABLE adventurer');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE choice');
        $this->addSql('DROP TABLE equipment');
        $this->addSql('DROP TABLE feedback');
        $this->addSql('DROP TABLE fight_history');
        $this->addSql('DROP TABLE monster');
        $this->addSql('DROP TABLE page');
        $this->addSql('DROP TABLE refresh_token');
        $this->addSql('DROP TABLE skill');
        $this->addSql('DROP TABLE user');
    }
}
