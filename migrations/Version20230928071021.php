<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230928071021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add hash to result';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE result ADD hash VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE result DROP hash');
    }
}
