<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210415124024 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE analysis (id INT AUTO_INCREMENT NOT NULL, test_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, progress_percent_variable_name VARCHAR(255) DEFAULT NULL, progress_variable_name VARCHAR(255) DEFAULT NULL, progress_variable_max INT DEFAULT NULL, INDEX IDX_33C7301E5D0459 (test_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE analysis_block (id INT AUTO_INCREMENT NOT NULL, analysis_id INT DEFAULT NULL, text LONGTEXT NOT NULL, text_en LONGTEXT NOT NULL, INDEX IDX_B4F21F977941003F (analysis_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE analysis_condition (id INT AUTO_INCREMENT NOT NULL, block_id INT DEFAULT NULL, variable_name VARCHAR(255) NOT NULL, referent_value INT NOT NULL, comparison VARCHAR(255) NOT NULL, INDEX IDX_4F5D9179E9ED820C (block_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE analysis ADD CONSTRAINT FK_33C7301E5D0459 FOREIGN KEY (test_id) REFERENCES test (id)');
        $this->addSql('ALTER TABLE analysis_block ADD CONSTRAINT FK_B4F21F977941003F FOREIGN KEY (analysis_id) REFERENCES analysis (id)');
        $this->addSql('ALTER TABLE analysis_condition ADD CONSTRAINT FK_4F5D9179E9ED820C FOREIGN KEY (block_id) REFERENCES analysis_block (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE analysis_block DROP FOREIGN KEY FK_B4F21F977941003F');
        $this->addSql('ALTER TABLE analysis_condition DROP FOREIGN KEY FK_4F5D9179E9ED820C');
        $this->addSql('DROP TABLE analysis');
        $this->addSql('DROP TABLE analysis_block');
        $this->addSql('DROP TABLE analysis_condition');
    }
}
