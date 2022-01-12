<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220112113900 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update PaymentProvider: add column granted_access.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE provider_payment ADD granted_access TINYINT(1) NOT NULL DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE provider_payment DROP granted_access');
    }
}
