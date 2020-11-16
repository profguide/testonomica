<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201113105450 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, sum INT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_status (id INT AUTO_INCREMENT NOT NULL, payment_id INT DEFAULT NULL, status SMALLINT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_5E38FE8A4C3A3BB (payment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE provider (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(20) NOT NULL, slug VARCHAR(20) NOT NULL, token VARCHAR(32) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE provider_access (id INT AUTO_INCREMENT NOT NULL, provider_id INT DEFAULT NULL, token VARCHAR(36) NOT NULL, created_at DATETIME NOT NULL, used_at DATETIME DEFAULT NULL, INDEX IDX_5158C8C4A53A8AA (provider_id), INDEX IDX_5158C8C45F37A13B (token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE provider_payment (id INT AUTO_INCREMENT NOT NULL, payment_id INT NOT NULL, provider_id INT NOT NULL, token VARCHAR(36) NOT NULL, user VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_9D7026AA4C3A3BB (payment_id), INDEX IDX_9D7026AAA53A8AA (provider_id), INDEX IDX_9D7026AA5F37A13B (token), INDEX IDX_9D7026AAA53A8AA8D93D649 (provider_id, user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payment_status ADD CONSTRAINT FK_5E38FE8A4C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id)');
        $this->addSql('ALTER TABLE provider_access ADD CONSTRAINT FK_5158C8C4A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id)');
        $this->addSql('ALTER TABLE provider_payment ADD CONSTRAINT FK_9D7026AA4C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id)');
        $this->addSql('ALTER TABLE provider_payment ADD CONSTRAINT FK_9D7026AAA53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment_status DROP FOREIGN KEY FK_5E38FE8A4C3A3BB');
        $this->addSql('ALTER TABLE provider_payment DROP FOREIGN KEY FK_9D7026AA4C3A3BB');
        $this->addSql('ALTER TABLE provider_access DROP FOREIGN KEY FK_5158C8C4A53A8AA');
        $this->addSql('ALTER TABLE provider_payment DROP FOREIGN KEY FK_9D7026AAA53A8AA');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE payment_status');
        $this->addSql('DROP TABLE provider');
        $this->addSql('DROP TABLE provider_access');
        $this->addSql('DROP TABLE provider_payment');
    }
}
