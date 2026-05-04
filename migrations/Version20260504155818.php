<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Create translations table for CMS-managed UI strings (one row per key+locale).
 */
final class Version20260504155818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create translations table for CMS-managed UI strings';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE translations (id BINARY(16) NOT NULL, translation_key VARCHAR(255) NOT NULL, locale VARCHAR(10) NOT NULL, value LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_TRANSLATION_KEY (translation_key), INDEX IDX_TRANSLATION_LOCALE (locale), UNIQUE INDEX UNIQ_TRANSLATION_KEY_LOCALE (translation_key, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE translations');
    }
}
