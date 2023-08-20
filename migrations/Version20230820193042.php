<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230820193042 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE author (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(50) NOT NULL, name VARCHAR(100) NOT NULL, name_en VARCHAR(100) NOT NULL, about LONGTEXT NOT NULL, about_en LONGTEXT NOT NULL, INDEX IDX_BDAFD8C8989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE test_author (test_id INT NOT NULL, author_id INT NOT NULL, INDEX IDX_2A76DFE61E5D0459 (test_id), INDEX IDX_2A76DFE6F675F31B (author_id), PRIMARY KEY(test_id, author_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE test_author ADD CONSTRAINT FK_2A76DFE61E5D0459 FOREIGN KEY (test_id) REFERENCES test (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE test_author ADD CONSTRAINT FK_2A76DFE6F675F31B FOREIGN KEY (author_id) REFERENCES author (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE test_author DROP FOREIGN KEY FK_2A76DFE61E5D0459');
        $this->addSql('ALTER TABLE test_author DROP FOREIGN KEY FK_2A76DFE6F675F31B');
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP TABLE test_author');
        $this->addSql('ALTER TABLE user CHANGE roles roles TEXT NOT NULL');
    }
}
