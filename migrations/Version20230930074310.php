<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230930074310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add new_id to result';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE provider_user_result CHANGE result_id result_id INT NOT NULL');
        $this->addSql('ALTER TABLE test CHANGE moderator_comment moderator_comment LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE provider_user_result CHANGE result_id result_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE test CHANGE moderator_comment moderator_comment TEXT DEFAULT NULL');
    }
}
