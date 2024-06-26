<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201118153855 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE access (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, token VARCHAR(36) NOT NULL, created_at DATETIME NOT NULL, used_at DATETIME DEFAULT NULL, INDEX IDX_6692B54ED5CA9E6 (service_id), INDEX IDX_6692B545F37A13B (token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, sum INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_6D28840DED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_status (id INT AUTO_INCREMENT NOT NULL, payment_id INT DEFAULT NULL, status SMALLINT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_5E38FE8A4C3A3BB (payment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE provider (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(20) NOT NULL, slug VARCHAR(20) NOT NULL, token VARCHAR(32) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE provider_payment (id INT AUTO_INCREMENT NOT NULL, payment_id INT NOT NULL, provider_id INT NOT NULL, token VARCHAR(36) NOT NULL, user VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_9D7026AA4C3A3BB (payment_id), INDEX IDX_9D7026AAA53A8AA (provider_id), INDEX IDX_9D7026AA5F37A13B (token), INDEX IDX_9D7026AAA53A8AA8D93D649 (provider_id, user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, sum INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_test (service_id INT NOT NULL, test_id INT NOT NULL, INDEX IDX_163CCAE0ED5CA9E6 (service_id), INDEX IDX_163CCAE01E5D0459 (test_id), PRIMARY KEY(service_id, test_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE access ADD CONSTRAINT FK_6692B54ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE payment_status ADD CONSTRAINT FK_5E38FE8A4C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id)');
        $this->addSql('ALTER TABLE provider_payment ADD CONSTRAINT FK_9D7026AA4C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id)');
        $this->addSql('ALTER TABLE provider_payment ADD CONSTRAINT FK_9D7026AAA53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id)');
        $this->addSql('ALTER TABLE service_test ADD CONSTRAINT FK_163CCAE0ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_test ADD CONSTRAINT FK_163CCAE01E5D0459 FOREIGN KEY (test_id) REFERENCES test (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment_status DROP FOREIGN KEY FK_5E38FE8A4C3A3BB');
        $this->addSql('ALTER TABLE provider_payment DROP FOREIGN KEY FK_9D7026AA4C3A3BB');
        $this->addSql('ALTER TABLE provider_payment DROP FOREIGN KEY FK_9D7026AAA53A8AA');
        $this->addSql('ALTER TABLE access DROP FOREIGN KEY FK_6692B54ED5CA9E6');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DED5CA9E6');
        $this->addSql('ALTER TABLE service_test DROP FOREIGN KEY FK_163CCAE0ED5CA9E6');
        $this->addSql('DROP TABLE access');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE payment_status');
        $this->addSql('DROP TABLE provider');
        $this->addSql('DROP TABLE provider_payment');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE service_test');
    }
}
