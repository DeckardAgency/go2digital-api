<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260404162047 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE homepage_custom_image (id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, desktop_image_id BINARY(16) DEFAULT NULL, mobile_image_id BINARY(16) DEFAULT NULL, INDEX IDX_7A112699E803AB10 (desktop_image_id), INDEX IDX_7A112699F0928933 (mobile_image_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_custom_image_translations (id BINARY(16) NOT NULL, alt VARCHAR(255) DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_760B41492C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_HP_CIMG_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_featured_lab_item_translations (id BINARY(16) NOT NULL, title VARCHAR(255) DEFAULT NULL, subtitle LONGTEXT DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_6683F2D02C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_HP_FLAB_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_featured_lab_items (id BINARY(16) NOT NULL, slug VARCHAR(255) DEFAULT NULL, categories JSON DEFAULT NULL, sort_order INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, image_id BINARY(16) DEFAULT NULL, INDEX IDX_B0D822C43DA5256D (image_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_rentals_image (id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, image_id BINARY(16) DEFAULT NULL, INDEX IDX_312330EF3DA5256D (image_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_rentals_image_translations (id BINARY(16) NOT NULL, text VARCHAR(255) DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_9963A4862C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_HP_RENT_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_why_section (id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_why_section_translations (id BINARY(16) NOT NULL, label VARCHAR(255) DEFAULT NULL, headline LONGTEXT DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_DC27A63C2C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_HP_WHYSEC_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE homepage_custom_image ADD CONSTRAINT FK_7A112699E803AB10 FOREIGN KEY (desktop_image_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE homepage_custom_image ADD CONSTRAINT FK_7A112699F0928933 FOREIGN KEY (mobile_image_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE homepage_custom_image_translations ADD CONSTRAINT FK_760B41492C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES homepage_custom_image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE homepage_featured_lab_item_translations ADD CONSTRAINT FK_6683F2D02C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES homepage_featured_lab_items (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE homepage_featured_lab_items ADD CONSTRAINT FK_B0D822C43DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE homepage_rentals_image ADD CONSTRAINT FK_312330EF3DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE homepage_rentals_image_translations ADD CONSTRAINT FK_9963A4862C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES homepage_rentals_image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE homepage_why_section_translations ADD CONSTRAINT FK_DC27A63C2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES homepage_why_section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE homepage_hero ADD mobile_video_id BINARY(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE homepage_hero ADD CONSTRAINT FK_9DCFCE31E4F6AC10 FOREIGN KEY (mobile_video_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_9DCFCE31E4F6AC10 ON homepage_hero (mobile_video_id)');
        $this->addSql('ALTER TABLE homepage_panels ADD stat_value VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE homepage_product_translations ADD indicator_text VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE homepage_why_cards ADD dot_pattern JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE homepage_custom_image DROP FOREIGN KEY FK_7A112699E803AB10');
        $this->addSql('ALTER TABLE homepage_custom_image DROP FOREIGN KEY FK_7A112699F0928933');
        $this->addSql('ALTER TABLE homepage_custom_image_translations DROP FOREIGN KEY FK_760B41492C2AC5D3');
        $this->addSql('ALTER TABLE homepage_featured_lab_item_translations DROP FOREIGN KEY FK_6683F2D02C2AC5D3');
        $this->addSql('ALTER TABLE homepage_featured_lab_items DROP FOREIGN KEY FK_B0D822C43DA5256D');
        $this->addSql('ALTER TABLE homepage_rentals_image DROP FOREIGN KEY FK_312330EF3DA5256D');
        $this->addSql('ALTER TABLE homepage_rentals_image_translations DROP FOREIGN KEY FK_9963A4862C2AC5D3');
        $this->addSql('ALTER TABLE homepage_why_section_translations DROP FOREIGN KEY FK_DC27A63C2C2AC5D3');
        $this->addSql('DROP TABLE homepage_custom_image');
        $this->addSql('DROP TABLE homepage_custom_image_translations');
        $this->addSql('DROP TABLE homepage_featured_lab_item_translations');
        $this->addSql('DROP TABLE homepage_featured_lab_items');
        $this->addSql('DROP TABLE homepage_rentals_image');
        $this->addSql('DROP TABLE homepage_rentals_image_translations');
        $this->addSql('DROP TABLE homepage_why_section');
        $this->addSql('DROP TABLE homepage_why_section_translations');
        $this->addSql('ALTER TABLE homepage_hero DROP FOREIGN KEY FK_9DCFCE31E4F6AC10');
        $this->addSql('DROP INDEX IDX_9DCFCE31E4F6AC10 ON homepage_hero');
        $this->addSql('ALTER TABLE homepage_hero DROP mobile_video_id');
        $this->addSql('ALTER TABLE homepage_panels DROP stat_value');
        $this->addSql('ALTER TABLE homepage_product_translations DROP indicator_text');
        $this->addSql('ALTER TABLE homepage_why_cards DROP dot_pattern');
    }
}
