<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260305124351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adventurer_skill (adventurer_id INT NOT NULL, skill_id INT NOT NULL, INDEX IDX_75371B009BF95FB8 (adventurer_id), INDEX IDX_75371B005585C142 (skill_id), PRIMARY KEY(adventurer_id, skill_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE adventurer_equipment (id INT AUTO_INCREMENT NOT NULL, adventurer_id INT NOT NULL, equipment_id INT NOT NULL, quantity INT DEFAULT 1 NOT NULL, INDEX IDX_CD1B68819BF95FB8 (adventurer_id), INDEX IDX_CD1B6881517FE9FE (equipment_id), UNIQUE INDEX adventurer_equipment_unique (adventurer_id, equipment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE adventurer_skill ADD CONSTRAINT FK_75371B009BF95FB8 FOREIGN KEY (adventurer_id) REFERENCES adventurer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE adventurer_skill ADD CONSTRAINT FK_75371B005585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE adventurer_equipment ADD CONSTRAINT FK_CD1B68819BF95FB8 FOREIGN KEY (adventurer_id) REFERENCES adventurer (id)');
        $this->addSql('ALTER TABLE adventurer_equipment ADD CONSTRAINT FK_CD1B6881517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id)');
        $this->addSql('ALTER TABLE adventurer ADD gold INT DEFAULT 0 NOT NULL, ADD max_endurance INT NOT NULL, ADD mastered_weapon_slug VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE equipment ADD slug VARCHAR(255) NOT NULL, ADD type VARCHAR(50) NOT NULL, ADD slot VARCHAR(20) DEFAULT NULL, ADD endurance_bonus INT DEFAULT 0 NOT NULL, ADD heal_amount INT DEFAULT 0 NOT NULL, DROP effect');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D338D583989D9B62 ON equipment (slug)');
        $this->addSql('ALTER TABLE page ADD requires_meal TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE skill ADD slug VARCHAR(255) NOT NULL, DROP effect');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5E3DE477989D9B62 ON skill (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adventurer_skill DROP FOREIGN KEY FK_75371B009BF95FB8');
        $this->addSql('ALTER TABLE adventurer_skill DROP FOREIGN KEY FK_75371B005585C142');
        $this->addSql('ALTER TABLE adventurer_equipment DROP FOREIGN KEY FK_CD1B68819BF95FB8');
        $this->addSql('ALTER TABLE adventurer_equipment DROP FOREIGN KEY FK_CD1B6881517FE9FE');
        $this->addSql('DROP TABLE adventurer_skill');
        $this->addSql('DROP TABLE adventurer_equipment');
        $this->addSql('ALTER TABLE adventurer DROP gold, DROP max_endurance, DROP mastered_weapon_slug');
        $this->addSql('ALTER TABLE page DROP requires_meal');
        $this->addSql('DROP INDEX UNIQ_D338D583989D9B62 ON equipment');
        $this->addSql('ALTER TABLE equipment ADD effect LONGTEXT DEFAULT NULL, DROP slug, DROP type, DROP slot, DROP endurance_bonus, DROP heal_amount');
        $this->addSql('DROP INDEX UNIQ_5E3DE477989D9B62 ON skill');
        $this->addSql('ALTER TABLE skill ADD effect LONGTEXT DEFAULT NULL, DROP slug');
    }
}
