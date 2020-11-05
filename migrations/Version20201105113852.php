<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201105113852 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, name_en VARCHAR(255) NOT NULL, pic VARCHAR(255) DEFAULT NULL, active TINYINT(1) NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_64C19C1989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE result (id INT AUTO_INCREMENT NOT NULL, test_id INT DEFAULT NULL, uuid VARCHAR(36) NOT NULL, data LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_136AC1131E5D0459 (test_id), INDEX IDX_136AC113D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE test (id INT AUTO_INCREMENT NOT NULL, catalog_id INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, name_en VARCHAR(255) DEFAULT NULL, description LONGTEXT NOT NULL, description_en LONGTEXT DEFAULT NULL, annotation LONGTEXT NOT NULL, annotation_en LONGTEXT DEFAULT NULL, duration SMALLINT NOT NULL, active TINYINT(1) NOT NULL, active_en TINYINT(1) NOT NULL, xml_filename VARCHAR(20) DEFAULT NULL, calculator_name VARCHAR(20) DEFAULT NULL, INDEX IDX_D87F7E0CCC3C66FC (catalog_id), INDEX IDX_D87F7E0C989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE result ADD CONSTRAINT FK_136AC1131E5D0459 FOREIGN KEY (test_id) REFERENCES test (id)');
        $this->addSql('ALTER TABLE test ADD CONSTRAINT FK_D87F7E0CCC3C66FC FOREIGN KEY (catalog_id) REFERENCES category (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE test DROP FOREIGN KEY FK_D87F7E0CCC3C66FC');
        $this->addSql('ALTER TABLE result DROP FOREIGN KEY FK_136AC1131E5D0459');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE result');
        $this->addSql('DROP TABLE test');
    }
}
