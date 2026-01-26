<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration to add refresh_token table for JWT token rotation mechanism
 */
final class Version20260126150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add refresh_token table for JWT token rotation mechanism';
    }

    public function up(Schema $schema): void
    {
        // Create refresh_token table
        $this->addSql('CREATE TABLE refresh_token (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
            user_id INTEGER NOT NULL, 
            token_hash VARCHAR(64) NOT NULL, 
            expires_at DATETIME NOT NULL, 
            created_at DATETIME NOT NULL, 
            revoked_at DATETIME DEFAULT NULL, 
            replaced_by_token_hash VARCHAR(64) DEFAULT NULL, 
            CONSTRAINT FK_C74F2195A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        )');
        
        // Create unique index on token_hash
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C74F2195B3BC57DA ON refresh_token (token_hash)');
        
        // Create index on user_id for performance
        $this->addSql('CREATE INDEX idx_user ON refresh_token (user_id)');
        
        // Create index on expires_at for cleanup queries
        $this->addSql('CREATE INDEX idx_expires_at ON refresh_token (expires_at)');
    }

    public function down(Schema $schema): void
    {
        // Drop refresh_token table
        $this->addSql('DROP TABLE refresh_token');
    }
}
