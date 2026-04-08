<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260408192222 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE homepage_possibilities (id BINARY(16) NOT NULL, sort_order INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE homepage_possibility_translations (id BINARY(16) NOT NULL, title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, locale VARCHAR(5) NOT NULL, translatable_id BINARY(16) NOT NULL, INDEX IDX_2B96F2512C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_HP_POSS_TRANS_LOCALE (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE homepage_possibility_translations ADD CONSTRAINT FK_2B96F2512C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES homepage_possibilities (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE homepage_possibility_translations DROP FOREIGN KEY FK_2B96F2512C2AC5D3');
        $this->addSql('DROP TABLE homepage_possibilities');
        $this->addSql('DROP TABLE homepage_possibility_translations');
    }
}
