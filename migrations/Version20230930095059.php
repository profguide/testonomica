<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230930095059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE provider_user_result ADD CONSTRAINT FK_B3A757DC7A7B643 FOREIGN KEY (result_id) REFERENCES result (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE provider_user_result DROP FOREIGN KEY FK_B3A757DC7A7B643');
    }
}
