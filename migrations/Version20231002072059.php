<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231002072059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE provider_user_result DROP FOREIGN KEY FK_B3A757DC7A7B643');
        $this->addSql('ALTER TABLE provider_user_result CHANGE result_id result_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE provider_user_result ADD CONSTRAINT FK_B3A757DC7A7B643 FOREIGN KEY (result_id) REFERENCES result (new_id)');
        $this->addSql('ALTER TABLE result MODIFY id INT NOT NULL');
//        $this->addSql('DROP INDEX UNIQ_136AC113BD06B3B3 ON result');
        $this->addSql('DROP INDEX `primary` ON result');
        $this->addSql('ALTER TABLE result DROP id');
        $this->addSql('ALTER TABLE result ADD PRIMARY KEY (new_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE provider_user_result DROP FOREIGN KEY FK_B3A757DC7A7B643');
        $this->addSql('ALTER TABLE provider_user_result CHANGE result_id result_id INT NOT NULL');
        $this->addSql('ALTER TABLE provider_user_result ADD CONSTRAINT FK_B3A757DC7A7B643 FOREIGN KEY (result_id) REFERENCES result (id)');
        $this->addSql('ALTER TABLE result ADD id INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_136AC113BD06B3B3 ON result (new_id)');
    }
}
