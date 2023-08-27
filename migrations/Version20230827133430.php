<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230827133430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add moderator comment to tests';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE test ADD moderator_comment TEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE test DROP moderator_comment');
    }
}
