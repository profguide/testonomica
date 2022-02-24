<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220224194434 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Question: add range and timer. ProviderPayment: granted_access: type change';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE provider_payment CHANGE granted_access granted_access TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE question ADD `range` INT DEFAULT 0 NOT NULL, ADD timer INT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE provider_payment CHANGE granted_access granted_access TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE question DROP `range`, DROP timer');
    }
}
