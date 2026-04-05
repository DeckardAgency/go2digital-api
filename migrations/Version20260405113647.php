<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260405113647 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE seo_metadata ADD og_type VARCHAR(50) DEFAULT NULL, ADD twitter_card VARCHAR(50) DEFAULT NULL, ADD canonical_url VARCHAR(255) DEFAULT NULL, ADD robots VARCHAR(50) DEFAULT NULL, ADD no_index TINYINT DEFAULT NULL, ADD no_follow TINYINT DEFAULT NULL');
        $this->addSql('ALTER TABLE seo_metadata_translations ADD og_title VARCHAR(255) DEFAULT NULL, ADD og_description VARCHAR(500) DEFAULT NULL, ADD twitter_title VARCHAR(255) DEFAULT NULL, ADD twitter_description VARCHAR(500) DEFAULT NULL');
        $this->addSql('ALTER TABLE totems ADD seo_metadata_id BINARY(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE totems ADD CONSTRAINT FK_890D058418F9C0D5 FOREIGN KEY (seo_metadata_id) REFERENCES seo_metadata (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_890D058418F9C0D5 ON totems (seo_metadata_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE seo_metadata DROP og_type, DROP twitter_card, DROP canonical_url, DROP robots, DROP no_index, DROP no_follow');
        $this->addSql('ALTER TABLE seo_metadata_translations DROP og_title, DROP og_description, DROP twitter_title, DROP twitter_description');
        $this->addSql('ALTER TABLE totems DROP FOREIGN KEY FK_890D058418F9C0D5');
        $this->addSql('DROP INDEX UNIQ_890D058418F9C0D5 ON totems');
        $this->addSql('ALTER TABLE totems DROP seo_metadata_id');
    }
}
