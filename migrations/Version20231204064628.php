<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231204064628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gn_post_category ADD premium TINYINT(1) DEFAULT NULL, CHANGE post_category_url post_category_url LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE gn_post_images ADD CONSTRAINT FK_39EBA45E4B89032C FOREIGN KEY (post_id) REFERENCES gn_posts (id)');
        $this->addSql('ALTER TABLE gn_post_category_post ADD CONSTRAINT FK_C998B6F81EF31071 FOREIGN KEY (gn_post_id) REFERENCES gn_posts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gn_post_category_post ADD CONSTRAINT FK_C998B6F89B749105 FOREIGN KEY (gn_post_category_id) REFERENCES gn_post_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gn_subscriptions ADD CONSTRAINT FK_FDD2D0D9A516C237 FOREIGN KEY (subscription_role_id) REFERENCES gn_user_role (id)');
        $this->addSql('ALTER TABLE gn_users ADD CONSTRAINT FK_4BFCA9A54C745A76 FOREIGN KEY (gn_country_id) REFERENCES gn_country (id)');
        $this->addSql('ALTER TABLE gn_users ADD CONSTRAINT FK_4BFCA9A558478984 FOREIGN KEY (gn_subscription_id) REFERENCES gn_subscriptions (id)');
        $this->addSql('ALTER TABLE gn_user_gn_role ADD CONSTRAINT FK_696567C5F214C0C8 FOREIGN KEY (gn_user_id) REFERENCES gn_users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gn_user_gn_role ADD CONSTRAINT FK_696567C5837931F1 FOREIGN KEY (gn_role_id) REFERENCES gn_user_role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES gn_users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gn_post_category DROP premium, CHANGE post_category_url post_category_url LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE gn_post_category_post DROP FOREIGN KEY FK_C998B6F81EF31071');
        $this->addSql('ALTER TABLE gn_post_category_post DROP FOREIGN KEY FK_C998B6F89B749105');
        $this->addSql('ALTER TABLE gn_post_images DROP FOREIGN KEY FK_39EBA45E4B89032C');
        $this->addSql('ALTER TABLE gn_subscriptions DROP FOREIGN KEY FK_FDD2D0D9A516C237');
        $this->addSql('ALTER TABLE gn_user_gn_role DROP FOREIGN KEY FK_696567C5F214C0C8');
        $this->addSql('ALTER TABLE gn_user_gn_role DROP FOREIGN KEY FK_696567C5837931F1');
        $this->addSql('ALTER TABLE gn_users DROP FOREIGN KEY FK_4BFCA9A54C745A76');
        $this->addSql('ALTER TABLE gn_users DROP FOREIGN KEY FK_4BFCA9A558478984');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
    }
}
