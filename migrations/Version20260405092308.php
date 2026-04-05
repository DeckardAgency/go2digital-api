<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260405092308 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cities (id BINARY(16) NOT NULL, cdn_city_id INT NOT NULL, name VARCHAR(255) NOT NULL, location JSON DEFAULT NULL, sort_order INT NOT NULL, is_active TINYINT NOT NULL, last_synced_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE totems (id BINARY(16) NOT NULL, cdn_totem_id INT NOT NULL, name VARCHAR(500) NOT NULL, name_en VARCHAR(500) DEFAULT NULL, totem_type VARCHAR(50) DEFAULT NULL, location JSON DEFAULT NULL, screens INT NOT NULL, header_image VARCHAR(500) DEFAULT NULL, is_installed TINYINT NOT NULL, is_big_screen TINYINT NOT NULL, screen_width INT NOT NULL, screen_height INT NOT NULL, postbuy_category VARCHAR(50) DEFAULT NULL, ad_duration INT NOT NULL, description LONGTEXT DEFAULT NULL, description_en LONGTEXT DEFAULT NULL, reach INT NOT NULL, video_url VARCHAR(500) DEFAULT NULL, totem_motion VARCHAR(50) DEFAULT NULL, sort_order INT NOT NULL, is_published TINYINT NOT NULL, manual_overrides JSON DEFAULT NULL, last_synced_at DATETIME DEFAULT NULL, images JSON DEFAULT NULL, floor_plans JSON DEFAULT NULL, totem_screens JSON DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, city_id BINARY(16) NOT NULL, INDEX IDX_890D05848BAC62AF (city_id), INDEX IDX_TOTEM_CDN_ID (cdn_totem_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE totems ADD CONSTRAINT FK_890D05848BAC62AF FOREIGN KEY (city_id) REFERENCES cities (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE totems DROP FOREIGN KEY FK_890D05848BAC62AF');
        $this->addSql('DROP TABLE cities');
        $this->addSql('DROP TABLE totems');
    }
}
