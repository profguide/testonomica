<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Здесь было создание ключа uuid в result. но всё пошло не так, я потом удалил ключ.
 * не достаточно было откатиться, пришлось ковыряться в phpmyadmin, и поэтому я вернул как было вручную.
 * а миграции просто очистил
 */
final class Version20230930091725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'nothing, stub';
    }

    public function up(Schema $schema): void
    {

    }

    public function down(Schema $schema): void
    {

    }
}
