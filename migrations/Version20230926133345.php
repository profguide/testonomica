<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230926133345 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add provider_user';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE provider_user (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', provider_id INT NOT NULL, ext_user_id VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_681BB535A53A8AA (provider_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE provider_user ADD CONSTRAINT FK_681BB535A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE provider_user DROP FOREIGN KEY FK_681BB535A53A8AA');
        $this->addSql('DROP TABLE provider_user');
    }
}
