<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260408214312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE homepage_analytics_tab_translations (id BINARY(16) NOT NULL, label VARCHAR(255) DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_2C81E7952C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_HP_ATAB_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_analytics_tabs (id BINARY(16) NOT NULL, curve_type VARCHAR(50) NOT NULL, y_labels JSON DEFAULT NULL, sort_order INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE homepage_analytics_tab_translations ADD CONSTRAINT FK_2C81E7952C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES homepage_analytics_tabs (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE homepage_analytics_tab_translations DROP FOREIGN KEY FK_2C81E7952C2AC5D3');
        $this->addSql('DROP TABLE homepage_analytics_tab_translations');
        $this->addSql('DROP TABLE homepage_analytics_tabs');
    }
}
