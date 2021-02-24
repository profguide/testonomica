<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210224181416 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, catalog_id INT DEFAULT NULL, test_id INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, name_en VARCHAR(255) NOT NULL, subtitle VARCHAR(255) DEFAULT NULL, subtitle_en VARCHAR(255) DEFAULT NULL, annotation TEXT DEFAULT NULL, annotation_en TEXT DEFAULT NULL, meta_title VARCHAR(255) NOT NULL, meta_title_en VARCHAR(255) NOT NULL, meta_description TEXT NOT NULL, meta_description_en TEXT NOT NULL, content LONGTEXT NOT NULL, content_en LONGTEXT NOT NULL, active TINYINT(1) NOT NULL, active_en TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, img VARCHAR(255) DEFAULT NULL, img_wide VARCHAR(255) DEFAULT NULL, INDEX IDX_23A0E66CC3C66FC (catalog_id), UNIQUE INDEX UNIQ_23A0E661E5D0459 (test_id), INDEX IDX_23A0E66989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE article_catalog (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, name_en VARCHAR(255) NOT NULL, meta_title VARCHAR(255) NOT NULL, meta_title_en VARCHAR(255) NOT NULL, meta_description TEXT NOT NULL, meta_description_en TEXT NOT NULL, INDEX IDX_F6FE76F0989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66CC3C66FC FOREIGN KEY (catalog_id) REFERENCES article_catalog (id)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E661E5D0459 FOREIGN KEY (test_id) REFERENCES test (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66CC3C66FC');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE article_catalog');
    }
}
