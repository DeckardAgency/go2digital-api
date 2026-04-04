<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260404160355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blog_categories (id BINARY(16) NOT NULL, slug VARCHAR(100) NOT NULL, sort_order INT NOT NULL, is_active TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_DC356481989D9B62 (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE blog_category_translations (id BINARY(16) NOT NULL, name VARCHAR(255) NOT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_85D2E1FE2C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_BLOG_CAT_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE blog_post_translations (id BINARY(16) NOT NULL, title VARCHAR(255) NOT NULL, excerpt LONGTEXT DEFAULT NULL, body LONGTEXT DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_2497E3322C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_BLOG_POST_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE blog_posts (id BINARY(16) NOT NULL, slug VARCHAR(255) NOT NULL, date DATE NOT NULL, author VARCHAR(255) NOT NULL, featured TINYINT NOT NULL, layout_hint VARCHAR(50) DEFAULT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, image_id BINARY(16) DEFAULT NULL, category_id BINARY(16) DEFAULT NULL, seo_metadata_id BINARY(16) DEFAULT NULL, UNIQUE INDEX UNIQ_78B2F932989D9B62 (slug), INDEX IDX_78B2F9323DA5256D (image_id), INDEX IDX_78B2F93212469DE2 (category_id), UNIQUE INDEX UNIQ_78B2F93218F9C0D5 (seo_metadata_id), INDEX IDX_BLOG_STATUS (status), INDEX IDX_BLOG_FEATURED (featured), INDEX IDX_BLOG_DATE (date), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE files (id BINARY(16) NOT NULL, filename VARCHAR(255) NOT NULL, original_filename VARCHAR(255) NOT NULL, mime_type VARCHAR(100) NOT NULL, size BIGINT NOT NULL, disk VARCHAR(50) NOT NULL, path VARCHAR(500) NOT NULL, description LONGTEXT DEFAULT NULL, category VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, uploaded_by_id BINARY(16) DEFAULT NULL, INDEX IDX_6354059A2B28FE8 (uploaded_by_id), INDEX IDX_FILE_CATEGORY (category), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE lab_categories (id BINARY(16) NOT NULL, slug VARCHAR(100) NOT NULL, sort_order INT NOT NULL, is_active TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_52A09173989D9B62 (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE lab_category_translations (id BINARY(16) NOT NULL, name VARCHAR(255) NOT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_64ABE8172C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_LAB_CAT_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE lab_project_translations (id BINARY(16) NOT NULL, title VARCHAR(255) NOT NULL, short_title VARCHAR(100) DEFAULT NULL, subtitle VARCHAR(500) DEFAULT NULL, body LONGTEXT DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_20C89EAC2C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_LAB_PROJ_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE lab_projects (id BINARY(16) NOT NULL, slug VARCHAR(255) NOT NULL, featured TINYINT NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, image_id BINARY(16) DEFAULT NULL, seo_metadata_id BINARY(16) DEFAULT NULL, UNIQUE INDEX UNIQ_869F9839989D9B62 (slug), INDEX IDX_869F98393DA5256D (image_id), UNIQUE INDEX UNIQ_869F983918F9C0D5 (seo_metadata_id), INDEX IDX_LAB_STATUS (status), INDEX IDX_LAB_FEATURED (featured), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE lab_project_categories (lab_project_id BINARY(16) NOT NULL, lab_category_id BINARY(16) NOT NULL, INDEX IDX_4EB86B397E3EC887 (lab_project_id), INDEX IDX_4EB86B39984B07D9 (lab_category_id), PRIMARY KEY (lab_project_id, lab_category_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE media (id BINARY(16) NOT NULL, filename VARCHAR(255) NOT NULL, original_filename VARCHAR(255) NOT NULL, mime_type VARCHAR(100) NOT NULL, size BIGINT NOT NULL, width INT DEFAULT NULL, height INT DEFAULT NULL, duration INT DEFAULT NULL, collection VARCHAR(100) NOT NULL, disk VARCHAR(50) NOT NULL, path VARCHAR(500) NOT NULL, thumbnails JSON DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, uploaded_by_id BINARY(16) DEFAULT NULL, INDEX IDX_6A2CA10CA2B28FE8 (uploaded_by_id), INDEX IDX_MEDIA_COLLECTION (collection), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE media_translations (id BINARY(16) NOT NULL, alt VARCHAR(255) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_AF46700B2C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_MEDIA_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE seo_metadata (id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, og_image_id BINARY(16) DEFAULT NULL, INDEX IDX_AEB395366EFCB8B8 (og_image_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE seo_metadata_translations (id BINARY(16) NOT NULL, title VARCHAR(255) DEFAULT NULL, description VARCHAR(500) DEFAULT NULL, keywords VARCHAR(500) DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_BEF45D302C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_SEO_META_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE users (id BINARY(16) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, is_active TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE blog_category_translations ADD CONSTRAINT FK_85D2E1FE2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES blog_categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_post_translations ADD CONSTRAINT FK_2497E3322C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES blog_posts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_posts ADD CONSTRAINT FK_78B2F9323DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE blog_posts ADD CONSTRAINT FK_78B2F93212469DE2 FOREIGN KEY (category_id) REFERENCES blog_categories (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE blog_posts ADD CONSTRAINT FK_78B2F93218F9C0D5 FOREIGN KEY (seo_metadata_id) REFERENCES seo_metadata (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE files ADD CONSTRAINT FK_6354059A2B28FE8 FOREIGN KEY (uploaded_by_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE lab_category_translations ADD CONSTRAINT FK_64ABE8172C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES lab_categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lab_project_translations ADD CONSTRAINT FK_20C89EAC2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES lab_projects (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lab_projects ADD CONSTRAINT FK_869F98393DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE lab_projects ADD CONSTRAINT FK_869F983918F9C0D5 FOREIGN KEY (seo_metadata_id) REFERENCES seo_metadata (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE lab_project_categories ADD CONSTRAINT FK_4EB86B397E3EC887 FOREIGN KEY (lab_project_id) REFERENCES lab_projects (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lab_project_categories ADD CONSTRAINT FK_4EB86B39984B07D9 FOREIGN KEY (lab_category_id) REFERENCES lab_categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CA2B28FE8 FOREIGN KEY (uploaded_by_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE media_translations ADD CONSTRAINT FK_AF46700B2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES media (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE seo_metadata ADD CONSTRAINT FK_AEB395366EFCB8B8 FOREIGN KEY (og_image_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE seo_metadata_translations ADD CONSTRAINT FK_BEF45D302C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES seo_metadata (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog_category_translations DROP FOREIGN KEY FK_85D2E1FE2C2AC5D3');
        $this->addSql('ALTER TABLE blog_post_translations DROP FOREIGN KEY FK_2497E3322C2AC5D3');
        $this->addSql('ALTER TABLE blog_posts DROP FOREIGN KEY FK_78B2F9323DA5256D');
        $this->addSql('ALTER TABLE blog_posts DROP FOREIGN KEY FK_78B2F93212469DE2');
        $this->addSql('ALTER TABLE blog_posts DROP FOREIGN KEY FK_78B2F93218F9C0D5');
        $this->addSql('ALTER TABLE files DROP FOREIGN KEY FK_6354059A2B28FE8');
        $this->addSql('ALTER TABLE lab_category_translations DROP FOREIGN KEY FK_64ABE8172C2AC5D3');
        $this->addSql('ALTER TABLE lab_project_translations DROP FOREIGN KEY FK_20C89EAC2C2AC5D3');
        $this->addSql('ALTER TABLE lab_projects DROP FOREIGN KEY FK_869F98393DA5256D');
        $this->addSql('ALTER TABLE lab_projects DROP FOREIGN KEY FK_869F983918F9C0D5');
        $this->addSql('ALTER TABLE lab_project_categories DROP FOREIGN KEY FK_4EB86B397E3EC887');
        $this->addSql('ALTER TABLE lab_project_categories DROP FOREIGN KEY FK_4EB86B39984B07D9');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CA2B28FE8');
        $this->addSql('ALTER TABLE media_translations DROP FOREIGN KEY FK_AF46700B2C2AC5D3');
        $this->addSql('ALTER TABLE seo_metadata DROP FOREIGN KEY FK_AEB395366EFCB8B8');
        $this->addSql('ALTER TABLE seo_metadata_translations DROP FOREIGN KEY FK_BEF45D302C2AC5D3');
        $this->addSql('DROP TABLE blog_categories');
        $this->addSql('DROP TABLE blog_category_translations');
        $this->addSql('DROP TABLE blog_post_translations');
        $this->addSql('DROP TABLE blog_posts');
        $this->addSql('DROP TABLE files');
        $this->addSql('DROP TABLE lab_categories');
        $this->addSql('DROP TABLE lab_category_translations');
        $this->addSql('DROP TABLE lab_project_translations');
        $this->addSql('DROP TABLE lab_projects');
        $this->addSql('DROP TABLE lab_project_categories');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE media_translations');
        $this->addSql('DROP TABLE seo_metadata');
        $this->addSql('DROP TABLE seo_metadata_translations');
        $this->addSql('DROP TABLE users');
    }
}
