<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230927140259 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add provider payment_policy, test_policy, access_count, access_limit';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE provider ADD payment_policy VARCHAR(50) NOT NULL, ADD test_policy VARCHAR(50) NOT NULL, ADD access_count INT NOT NULL, ADD access_limit INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE provider DROP payment_policy, DROP test_policy, DROP access_count, DROP access_limit');
    }
}
