<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add typography_map JSON column to homepage singleton tables.
 * Enables per-section mapping of display-element identifiers to typography preset slugs.
 */
final class Version20260422120000 extends AbstractMigration
{
    private const TABLES = [
        'homepage_hero',
        'homepage_billboard',
        'homepage_custom_image',
        'homepage_custom_solution',
        'homepage_human_focused',
        'homepage_text_animation',
        'homepage_why_section',
        'homepage_analytics',
        'homepage_rentals_image',
    ];

    public function getDescription(): string
    {
        return 'Add typography_map JSON column to homepage singleton tables';
    }

    public function up(Schema $schema): void
    {
        foreach (self::TABLES as $table) {
            $this->addSql(sprintf('ALTER TABLE %s ADD typography_map JSON DEFAULT NULL', $table));
        }
    }

    public function down(Schema $schema): void
    {
        foreach (self::TABLES as $table) {
            $this->addSql(sprintf('ALTER TABLE %s DROP typography_map', $table));
        }
    }
}
