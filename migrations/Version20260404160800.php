<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260404160800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE homepage_billboard (id BINARY(16) NOT NULL, button_url VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, image_id BINARY(16) DEFAULT NULL, INDEX IDX_7F6D0C823DA5256D (image_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_billboard_translations (id BINARY(16) NOT NULL, title VARCHAR(255) DEFAULT NULL, subtitle VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, button_text VARCHAR(100) DEFAULT NULL, image_alt VARCHAR(255) DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_C5D260D92C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_HP_BB_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_custom_solution (id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, image_id BINARY(16) DEFAULT NULL, INDEX IDX_9F7BE9553DA5256D (image_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_custom_solution_translations (id BINARY(16) NOT NULL, indicator VARCHAR(255) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, block1 LONGTEXT DEFAULT NULL, block2 LONGTEXT DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_B0D603E62C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_HP_CSOL_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_hero (id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, video_id BINARY(16) DEFAULT NULL, INDEX IDX_9DCFCE3129C1004E (video_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_hero_translations (id BINARY(16) NOT NULL, title_line1 VARCHAR(255) DEFAULT NULL, title_line2 VARCHAR(255) DEFAULT NULL, kicker VARCHAR(255) DEFAULT NULL, heading VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, scroll_down_label VARCHAR(100) DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_F9925C652C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_HP_HERO_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_human_focused (id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_human_focused_translations (id BINARY(16) NOT NULL, indicator VARCHAR(255) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, block_left LONGTEXT DEFAULT NULL, block_right LONGTEXT DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_DBEE19A52C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_HP_HF_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_panel_translations (id BINARY(16) NOT NULL, title VARCHAR(255) DEFAULT NULL, tag VARCHAR(100) DEFAULT NULL, description LONGTEXT DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_69903A082C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_HP_PANEL_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_panels (id BINARY(16) NOT NULL, sort_order INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, image_id BINARY(16) DEFAULT NULL, INDEX IDX_F64128DF3DA5256D (image_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_product_feature_translations (id BINARY(16) NOT NULL, title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_72961B6A2C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_HP_PFEAT_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_product_features (id BINARY(16) NOT NULL, icon VARCHAR(100) DEFAULT NULL, sort_order INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, product_id BINARY(16) NOT NULL, INDEX IDX_344A27C54584665A (product_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_product_translations (id BINARY(16) NOT NULL, title VARCHAR(255) DEFAULT NULL, badge VARCHAR(100) DEFAULT NULL, description LONGTEXT DEFAULT NULL, specs_title VARCHAR(255) DEFAULT NULL, download_label VARCHAR(255) DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_C71F8A9A2C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_HP_PROD_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_products (id BINARY(16) NOT NULL, product_type VARCHAR(20) NOT NULL, specs JSON DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, image_id BINARY(16) DEFAULT NULL, INDEX IDX_61AECEC33DA5256D (image_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_text_animation (id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_text_animation_translations (id BINARY(16) NOT NULL, word1 VARCHAR(255) DEFAULT NULL, word2 VARCHAR(255) DEFAULT NULL, word3 VARCHAR(255) DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_8219F5A32C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_HP_TANIM_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_tracking_feature_translations (id BINARY(16) NOT NULL, title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_E553A8182C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_HP_TRACK_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_tracking_features (id BINARY(16) NOT NULL, icon VARCHAR(100) DEFAULT NULL, sort_order INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_why_card_translations (id BINARY(16) NOT NULL, title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_D0A2F6062C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_HP_WHY_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_why_cards (id BINARY(16) NOT NULL, icon VARCHAR(100) DEFAULT NULL, sort_order INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE homepage_billboard ADD CONSTRAINT FK_7F6D0C823DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE homepage_billboard_translations ADD CONSTRAINT FK_C5D260D92C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES homepage_billboard (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE homepage_custom_solution ADD CONSTRAINT FK_9F7BE9553DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE homepage_custom_solution_translations ADD CONSTRAINT FK_B0D603E62C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES homepage_custom_solution (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE homepage_hero ADD CONSTRAINT FK_9DCFCE3129C1004E FOREIGN KEY (video_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE homepage_hero_translations ADD CONSTRAINT FK_F9925C652C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES homepage_hero (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE homepage_human_focused_translations ADD CONSTRAINT FK_DBEE19A52C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES homepage_human_focused (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE homepage_panel_translations ADD CONSTRAINT FK_69903A082C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES homepage_panels (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE homepage_panels ADD CONSTRAINT FK_F64128DF3DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE homepage_product_feature_translations ADD CONSTRAINT FK_72961B6A2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES homepage_product_features (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE homepage_product_features ADD CONSTRAINT FK_344A27C54584665A FOREIGN KEY (product_id) REFERENCES homepage_products (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE homepage_product_translations ADD CONSTRAINT FK_C71F8A9A2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES homepage_products (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE homepage_products ADD CONSTRAINT FK_61AECEC33DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE homepage_text_animation_translations ADD CONSTRAINT FK_8219F5A32C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES homepage_text_animation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE homepage_tracking_feature_translations ADD CONSTRAINT FK_E553A8182C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES homepage_tracking_features (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE homepage_why_card_translations ADD CONSTRAINT FK_D0A2F6062C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES homepage_why_cards (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE homepage_billboard DROP FOREIGN KEY FK_7F6D0C823DA5256D');
        $this->addSql('ALTER TABLE homepage_billboard_translations DROP FOREIGN KEY FK_C5D260D92C2AC5D3');
        $this->addSql('ALTER TABLE homepage_custom_solution DROP FOREIGN KEY FK_9F7BE9553DA5256D');
        $this->addSql('ALTER TABLE homepage_custom_solution_translations DROP FOREIGN KEY FK_B0D603E62C2AC5D3');
        $this->addSql('ALTER TABLE homepage_hero DROP FOREIGN KEY FK_9DCFCE3129C1004E');
        $this->addSql('ALTER TABLE homepage_hero_translations DROP FOREIGN KEY FK_F9925C652C2AC5D3');
        $this->addSql('ALTER TABLE homepage_human_focused_translations DROP FOREIGN KEY FK_DBEE19A52C2AC5D3');
        $this->addSql('ALTER TABLE homepage_panel_translations DROP FOREIGN KEY FK_69903A082C2AC5D3');
        $this->addSql('ALTER TABLE homepage_panels DROP FOREIGN KEY FK_F64128DF3DA5256D');
        $this->addSql('ALTER TABLE homepage_product_feature_translations DROP FOREIGN KEY FK_72961B6A2C2AC5D3');
        $this->addSql('ALTER TABLE homepage_product_features DROP FOREIGN KEY FK_344A27C54584665A');
        $this->addSql('ALTER TABLE homepage_product_translations DROP FOREIGN KEY FK_C71F8A9A2C2AC5D3');
        $this->addSql('ALTER TABLE homepage_products DROP FOREIGN KEY FK_61AECEC33DA5256D');
        $this->addSql('ALTER TABLE homepage_text_animation_translations DROP FOREIGN KEY FK_8219F5A32C2AC5D3');
        $this->addSql('ALTER TABLE homepage_tracking_feature_translations DROP FOREIGN KEY FK_E553A8182C2AC5D3');
        $this->addSql('ALTER TABLE homepage_why_card_translations DROP FOREIGN KEY FK_D0A2F6062C2AC5D3');
        $this->addSql('DROP TABLE homepage_billboard');
        $this->addSql('DROP TABLE homepage_billboard_translations');
        $this->addSql('DROP TABLE homepage_custom_solution');
        $this->addSql('DROP TABLE homepage_custom_solution_translations');
        $this->addSql('DROP TABLE homepage_hero');
        $this->addSql('DROP TABLE homepage_hero_translations');
        $this->addSql('DROP TABLE homepage_human_focused');
        $this->addSql('DROP TABLE homepage_human_focused_translations');
        $this->addSql('DROP TABLE homepage_panel_translations');
        $this->addSql('DROP TABLE homepage_panels');
        $this->addSql('DROP TABLE homepage_product_feature_translations');
        $this->addSql('DROP TABLE homepage_product_features');
        $this->addSql('DROP TABLE homepage_product_translations');
        $this->addSql('DROP TABLE homepage_products');
        $this->addSql('DROP TABLE homepage_text_animation');
        $this->addSql('DROP TABLE homepage_text_animation_translations');
        $this->addSql('DROP TABLE homepage_tracking_feature_translations');
        $this->addSql('DROP TABLE homepage_tracking_features');
        $this->addSql('DROP TABLE homepage_why_card_translations');
        $this->addSql('DROP TABLE homepage_why_cards');
    }
}
