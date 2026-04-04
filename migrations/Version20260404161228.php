<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260404161228 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blog_page_content (id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, seo_metadata_id BINARY(16) DEFAULT NULL, UNIQUE INDEX UNIQ_5E7934BB18F9C0D5 (seo_metadata_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE blog_page_content_translations (id BINARY(16) NOT NULL, page_title VARCHAR(255) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, filter_all_label VARCHAR(100) DEFAULT NULL, read_more_label VARCHAR(100) DEFAULT NULL, no_results_title VARCHAR(255) DEFAULT NULL, no_results_text VARCHAR(500) DEFAULT NULL, view_all_label VARCHAR(100) DEFAULT NULL, all_loaded_text VARCHAR(100) DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_E9AB18432C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_BLOG_PC_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE contact_info (id BINARY(16) NOT NULL, `key` VARCHAR(50) NOT NULL, value VARCHAR(255) NOT NULL, href VARCHAR(255) DEFAULT NULL, sort_order INT NOT NULL, is_external TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE contact_page_content (id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, background_id BINARY(16) DEFAULT NULL, seo_metadata_id BINARY(16) DEFAULT NULL, INDEX IDX_2812E908C93D69EA (background_id), UNIQUE INDEX UNIQ_2812E90818F9C0D5 (seo_metadata_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE contact_page_content_translations (id BINARY(16) NOT NULL, page_title VARCHAR(255) DEFAULT NULL, battery_line1 VARCHAR(255) DEFAULT NULL, battery_line2 VARCHAR(255) DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_B9C36C142C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_CONTACT_PC_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE esg_card_translations (id BINARY(16) NOT NULL, text LONGTEXT DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_93E3202D2C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_ESG_CARD_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE esg_cards (id BINARY(16) NOT NULL, icon VARCHAR(100) DEFAULT NULL, sort_order INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE esg_page_content (id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, video_id BINARY(16) DEFAULT NULL, mobile_bg_id BINARY(16) DEFAULT NULL, seo_metadata_id BINARY(16) DEFAULT NULL, INDEX IDX_1871EF29C1004E (video_id), INDEX IDX_1871EF54C7B1E4 (mobile_bg_id), UNIQUE INDEX UNIQ_1871EF18F9C0D5 (seo_metadata_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE esg_page_content_translations (id BINARY(16) NOT NULL, hero_label VARCHAR(255) DEFAULT NULL, intro_small LONGTEXT DEFAULT NULL, intro_large LONGTEXT DEFAULT NULL, download_report_label VARCHAR(255) DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_BEDF7A472C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_ESG_PC_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE esg_pillar_translations (id BINARY(16) NOT NULL, title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_E355BE192C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_ESG_PIL_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE esg_pillars (id BINARY(16) NOT NULL, icon VARCHAR(100) DEFAULT NULL, sort_order INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE esg_vision_badge_translations (id BINARY(16) NOT NULL, title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_60DF93182C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_ESG_VB_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE esg_vision_badges (id BINARY(16) NOT NULL, sort_order INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE lab_page_content (id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, seo_metadata_id BINARY(16) DEFAULT NULL, UNIQUE INDEX UNIQ_B5243F8218F9C0D5 (seo_metadata_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE lab_page_content_translations (id BINARY(16) NOT NULL, page_title VARCHAR(255) DEFAULT NULL, breadcrumb VARCHAR(255) DEFAULT NULL, intro LONGTEXT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, filter_all_label VARCHAR(100) DEFAULT NULL, no_results_text VARCHAR(500) DEFAULT NULL, view_all_label VARCHAR(100) DEFAULT NULL, view_project_label VARCHAR(100) DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_64FD3D2F2C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_LAB_PC_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE navigation_item_translations (id BINARY(16) NOT NULL, label VARCHAR(255) NOT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_EA9400AB2C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_NAV_ITEM_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE navigation_items (id BINARY(16) NOT NULL, url VARCHAR(255) NOT NULL, sort_order INT NOT NULL, is_active TINYINT NOT NULL, nav_group VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, parent_id BINARY(16) DEFAULT NULL, INDEX IDX_5F227988727ACA70 (parent_id), INDEX IDX_NAV_GROUP (nav_group), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE page_translations (id BINARY(16) NOT NULL, title VARCHAR(255) DEFAULT NULL, body LONGTEXT DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_78AB76C92C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_PAGE_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE pages (id BINARY(16) NOT NULL, slug VARCHAR(255) NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, seo_metadata_id BINARY(16) DEFAULT NULL, UNIQUE INDEX UNIQ_2074E575989D9B62 (slug), UNIQUE INDEX UNIQ_2074E57518F9C0D5 (seo_metadata_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE settings (id BINARY(16) NOT NULL, `key` VARCHAR(255) NOT NULL, value JSON NOT NULL, setting_group VARCHAR(100) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_SETTING_GROUP (setting_group), UNIQUE INDEX UNIQ_SETTING_KEY (`key`), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE social_links (id BINARY(16) NOT NULL, platform VARCHAR(50) NOT NULL, url VARCHAR(255) NOT NULL, icon VARCHAR(100) DEFAULT NULL, sort_order INT NOT NULL, is_active TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE team_member_translations (id BINARY(16) NOT NULL, position VARCHAR(255) DEFAULT NULL, bio LONGTEXT DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_AF0AB4AE2C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_TEAM_MEM_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE team_members (id BINARY(16) NOT NULL, name VARCHAR(255) NOT NULL, sort_order INT NOT NULL, is_active TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, image_id BINARY(16) DEFAULT NULL, INDEX IDX_BAD9A3C83DA5256D (image_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE team_page_content (id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, seo_metadata_id BINARY(16) DEFAULT NULL, UNIQUE INDEX UNIQ_C23D6FE18F9C0D5 (seo_metadata_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE team_page_content_translations (id BINARY(16) NOT NULL, page_title VARCHAR(255) DEFAULT NULL, intro LONGTEXT DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_CA5774592C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_TEAM_PC_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE blog_page_content ADD CONSTRAINT FK_5E7934BB18F9C0D5 FOREIGN KEY (seo_metadata_id) REFERENCES seo_metadata (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE blog_page_content_translations ADD CONSTRAINT FK_E9AB18432C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES blog_page_content (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contact_page_content ADD CONSTRAINT FK_2812E908C93D69EA FOREIGN KEY (background_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE contact_page_content ADD CONSTRAINT FK_2812E90818F9C0D5 FOREIGN KEY (seo_metadata_id) REFERENCES seo_metadata (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE contact_page_content_translations ADD CONSTRAINT FK_B9C36C142C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES contact_page_content (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE esg_card_translations ADD CONSTRAINT FK_93E3202D2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES esg_cards (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE esg_page_content ADD CONSTRAINT FK_1871EF29C1004E FOREIGN KEY (video_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE esg_page_content ADD CONSTRAINT FK_1871EF54C7B1E4 FOREIGN KEY (mobile_bg_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE esg_page_content ADD CONSTRAINT FK_1871EF18F9C0D5 FOREIGN KEY (seo_metadata_id) REFERENCES seo_metadata (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE esg_page_content_translations ADD CONSTRAINT FK_BEDF7A472C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES esg_page_content (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE esg_pillar_translations ADD CONSTRAINT FK_E355BE192C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES esg_pillars (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE esg_vision_badge_translations ADD CONSTRAINT FK_60DF93182C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES esg_vision_badges (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lab_page_content ADD CONSTRAINT FK_B5243F8218F9C0D5 FOREIGN KEY (seo_metadata_id) REFERENCES seo_metadata (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE lab_page_content_translations ADD CONSTRAINT FK_64FD3D2F2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES lab_page_content (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE navigation_item_translations ADD CONSTRAINT FK_EA9400AB2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES navigation_items (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE navigation_items ADD CONSTRAINT FK_5F227988727ACA70 FOREIGN KEY (parent_id) REFERENCES navigation_items (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE page_translations ADD CONSTRAINT FK_78AB76C92C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES pages (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pages ADD CONSTRAINT FK_2074E57518F9C0D5 FOREIGN KEY (seo_metadata_id) REFERENCES seo_metadata (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE team_member_translations ADD CONSTRAINT FK_AF0AB4AE2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES team_members (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team_members ADD CONSTRAINT FK_BAD9A3C83DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE team_page_content ADD CONSTRAINT FK_C23D6FE18F9C0D5 FOREIGN KEY (seo_metadata_id) REFERENCES seo_metadata (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE team_page_content_translations ADD CONSTRAINT FK_CA5774592C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES team_page_content (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog_page_content DROP FOREIGN KEY FK_5E7934BB18F9C0D5');
        $this->addSql('ALTER TABLE blog_page_content_translations DROP FOREIGN KEY FK_E9AB18432C2AC5D3');
        $this->addSql('ALTER TABLE contact_page_content DROP FOREIGN KEY FK_2812E908C93D69EA');
        $this->addSql('ALTER TABLE contact_page_content DROP FOREIGN KEY FK_2812E90818F9C0D5');
        $this->addSql('ALTER TABLE contact_page_content_translations DROP FOREIGN KEY FK_B9C36C142C2AC5D3');
        $this->addSql('ALTER TABLE esg_card_translations DROP FOREIGN KEY FK_93E3202D2C2AC5D3');
        $this->addSql('ALTER TABLE esg_page_content DROP FOREIGN KEY FK_1871EF29C1004E');
        $this->addSql('ALTER TABLE esg_page_content DROP FOREIGN KEY FK_1871EF54C7B1E4');
        $this->addSql('ALTER TABLE esg_page_content DROP FOREIGN KEY FK_1871EF18F9C0D5');
        $this->addSql('ALTER TABLE esg_page_content_translations DROP FOREIGN KEY FK_BEDF7A472C2AC5D3');
        $this->addSql('ALTER TABLE esg_pillar_translations DROP FOREIGN KEY FK_E355BE192C2AC5D3');
        $this->addSql('ALTER TABLE esg_vision_badge_translations DROP FOREIGN KEY FK_60DF93182C2AC5D3');
        $this->addSql('ALTER TABLE lab_page_content DROP FOREIGN KEY FK_B5243F8218F9C0D5');
        $this->addSql('ALTER TABLE lab_page_content_translations DROP FOREIGN KEY FK_64FD3D2F2C2AC5D3');
        $this->addSql('ALTER TABLE navigation_item_translations DROP FOREIGN KEY FK_EA9400AB2C2AC5D3');
        $this->addSql('ALTER TABLE navigation_items DROP FOREIGN KEY FK_5F227988727ACA70');
        $this->addSql('ALTER TABLE page_translations DROP FOREIGN KEY FK_78AB76C92C2AC5D3');
        $this->addSql('ALTER TABLE pages DROP FOREIGN KEY FK_2074E57518F9C0D5');
        $this->addSql('ALTER TABLE team_member_translations DROP FOREIGN KEY FK_AF0AB4AE2C2AC5D3');
        $this->addSql('ALTER TABLE team_members DROP FOREIGN KEY FK_BAD9A3C83DA5256D');
        $this->addSql('ALTER TABLE team_page_content DROP FOREIGN KEY FK_C23D6FE18F9C0D5');
        $this->addSql('ALTER TABLE team_page_content_translations DROP FOREIGN KEY FK_CA5774592C2AC5D3');
        $this->addSql('DROP TABLE blog_page_content');
        $this->addSql('DROP TABLE blog_page_content_translations');
        $this->addSql('DROP TABLE contact_info');
        $this->addSql('DROP TABLE contact_page_content');
        $this->addSql('DROP TABLE contact_page_content_translations');
        $this->addSql('DROP TABLE esg_card_translations');
        $this->addSql('DROP TABLE esg_cards');
        $this->addSql('DROP TABLE esg_page_content');
        $this->addSql('DROP TABLE esg_page_content_translations');
        $this->addSql('DROP TABLE esg_pillar_translations');
        $this->addSql('DROP TABLE esg_pillars');
        $this->addSql('DROP TABLE esg_vision_badge_translations');
        $this->addSql('DROP TABLE esg_vision_badges');
        $this->addSql('DROP TABLE lab_page_content');
        $this->addSql('DROP TABLE lab_page_content_translations');
        $this->addSql('DROP TABLE navigation_item_translations');
        $this->addSql('DROP TABLE navigation_items');
        $this->addSql('DROP TABLE page_translations');
        $this->addSql('DROP TABLE pages');
        $this->addSql('DROP TABLE settings');
        $this->addSql('DROP TABLE social_links');
        $this->addSql('DROP TABLE team_member_translations');
        $this->addSql('DROP TABLE team_members');
        $this->addSql('DROP TABLE team_page_content');
        $this->addSql('DROP TABLE team_page_content_translations');
    }
}
