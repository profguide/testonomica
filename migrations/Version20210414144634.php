<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210414144634 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, test INT DEFAULT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, name_en VARCHAR(255) DEFAULT NULL, text LONGTEXT DEFAULT NULL, text_en LONGTEXT DEFAULT NULL, variety VARCHAR(255) DEFAULT NULL, updated_at DATETIME NOT NULL, img VARCHAR(255) DEFAULT NULL, wrong LONGTEXT DEFAULT NULL, wrong_en LONGTEXT DEFAULT NULL, correct LONGTEXT DEFAULT NULL, correct_en LONGTEXT DEFAULT NULL, enabled_back TINYINT(1) NOT NULL, enabled_forward TINYINT(1) NOT NULL, show_answer TINYINT(1) NOT NULL, INDEX IDX_B6F7494ED87F7E0C (test), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_item (id INT AUTO_INCREMENT NOT NULL, question INT DEFAULT NULL, value VARCHAR(255) NOT NULL, correct TINYINT(1) NOT NULL, text LONGTEXT NOT NULL, text_en LONGTEXT DEFAULT NULL, updated_at DATETIME NOT NULL, img VARCHAR(255) DEFAULT NULL, INDEX IDX_41F5C8F1B6F7494E (question), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles TEXT NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494ED87F7E0C FOREIGN KEY (test) REFERENCES test (id)');
        $this->addSql('ALTER TABLE question_item ADD CONSTRAINT FK_41F5C8F1B6F7494E FOREIGN KEY (question) REFERENCES question (id)');
        $this->addSql('DROP INDEX IDX_23A0E66989D9B62 ON article');
        $this->addSql('CREATE INDEX IDX_23A0E66989D9B62 ON article (slug)');
        $this->addSql('DROP INDEX IDX_F6FE76F0989D9B62 ON article_catalog');
        $this->addSql('CREATE INDEX IDX_F6FE76F0989D9B62 ON article_catalog (slug)');
        $this->addSql('DROP INDEX IDX_64C19C1989D9B62 ON category');
        $this->addSql('CREATE INDEX IDX_64C19C1989D9B62 ON category (slug)');
        $this->addSql('DROP INDEX IDX_9D7026AAA53A8AA8D93D649 ON provider_payment');
        $this->addSql('CREATE INDEX IDX_9D7026AAA53A8AA8D93D649 ON provider_payment (provider_id, user)');
        $this->addSql('DROP INDEX IDX_D87F7E0C989D9B62 ON test');
        $this->addSql('ALTER TABLE test ADD is_xml_source TINYINT(1) NOT NULL, ADD calculator VARCHAR(20) DEFAULT NULL, ADD result_view LONGTEXT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_D87F7E0C989D9B62 ON test (slug)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE question_item DROP FOREIGN KEY FK_41F5C8F1B6F7494E');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE question_item');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP INDEX IDX_23A0E66989D9B62 ON article');
        $this->addSql('CREATE INDEX IDX_23A0E66989D9B62 ON article (slug(191))');
        $this->addSql('DROP INDEX IDX_F6FE76F0989D9B62 ON article_catalog');
        $this->addSql('CREATE INDEX IDX_F6FE76F0989D9B62 ON article_catalog (slug(191))');
        $this->addSql('DROP INDEX IDX_64C19C1989D9B62 ON category');
        $this->addSql('CREATE INDEX IDX_64C19C1989D9B62 ON category (slug(191))');
        $this->addSql('DROP INDEX IDX_9D7026AAA53A8AA8D93D649 ON provider_payment');
        $this->addSql('CREATE INDEX IDX_9D7026AAA53A8AA8D93D649 ON provider_payment (provider_id, user(191))');
        $this->addSql('DROP INDEX IDX_D87F7E0C989D9B62 ON test');
        $this->addSql('ALTER TABLE test DROP is_xml_source, DROP calculator, DROP result_view');
        $this->addSql('CREATE INDEX IDX_D87F7E0C989D9B62 ON test (slug(191))');
    }
}
