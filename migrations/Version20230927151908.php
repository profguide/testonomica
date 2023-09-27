<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230927151908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add table provider_user_result';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE provider_user_result (id INT AUTO_INCREMENT NOT NULL, user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', result_id INT NOT NULL, test_id INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_B3A757DCA76ED395 (user_id), INDEX IDX_B3A757DC7A7B643 (result_id), INDEX IDX_B3A757DC1E5D0459 (test_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE provider_user_result ADD CONSTRAINT FK_B3A757DCA76ED395 FOREIGN KEY (user_id) REFERENCES provider_user (id)');
        $this->addSql('ALTER TABLE provider_user_result ADD CONSTRAINT FK_B3A757DC7A7B643 FOREIGN KEY (result_id) REFERENCES result (id)');
        $this->addSql('ALTER TABLE provider_user_result ADD CONSTRAINT FK_B3A757DC1E5D0459 FOREIGN KEY (test_id) REFERENCES test (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE provider_user_result DROP FOREIGN KEY FK_B3A757DCA76ED395');
        $this->addSql('ALTER TABLE provider_user_result DROP FOREIGN KEY FK_B3A757DC7A7B643');
        $this->addSql('ALTER TABLE provider_user_result DROP FOREIGN KEY FK_B3A757DC1E5D0459');
        $this->addSql('DROP TABLE provider_user_result');
    }
}
