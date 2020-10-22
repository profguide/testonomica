<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201020080045 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates table test';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE test (id INT AUTO_INCREMENT NOT NULL, catalog_id INT NOT NULL, slug VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, name_en VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, description_en LONGTEXT NOT NULL, annotation LONGTEXT NOT NULL, annotation_en LONGTEXT NOT NULL, duration SMALLINT NOT NULL, active TINYINT(1) NOT NULL, active_en TINYINT(1) NOT NULL, xml_filename VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE test');
    }
}
