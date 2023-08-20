<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230820085550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Test: add source_name, drop calculator, drop analysis';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE IF EXISTS analysis_condition');
        $this->addSql('DROP TABLE IF EXISTS analysis_block');
        $this->addSql('DROP TABLE IF EXISTS analysis');

        $this->addSql('ALTER TABLE test ADD source_name VARCHAR(50) DEFAULT NULL, DROP calculator, CHANGE slug slug VARCHAR(50) NOT NULL, CHANGE xml_filename xml_filename VARCHAR(50) DEFAULT NULL, CHANGE calculator_name calculator_name VARCHAR(50) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D87F7E0C989D9B62 ON test (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_D87F7E0C989D9B62 ON test');
        $this->addSql('ALTER TABLE test ADD calculator VARCHAR(20) DEFAULT NULL, DROP source_name, CHANGE slug slug VARCHAR(255) NOT NULL, CHANGE xml_filename xml_filename VARCHAR(20) DEFAULT NULL, CHANGE calculator_name calculator_name VARCHAR(20) DEFAULT NULL');
    }
}
