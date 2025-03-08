<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250306155003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE group_role (id INT AUTO_INCREMENT NOT NULL, role VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE group_role_user (group_role_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_F39FF83C500376A0 (group_role_id), INDEX IDX_F39FF83CA76ED395 (user_id), PRIMARY KEY(group_role_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE group_role_group (group_role_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_4DD1CD58500376A0 (group_role_id), INDEX IDX_4DD1CD58FE54D947 (group_id), PRIMARY KEY(group_role_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE group_role_user ADD CONSTRAINT FK_F39FF83C500376A0 FOREIGN KEY (group_role_id) REFERENCES group_role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_role_user ADD CONSTRAINT FK_F39FF83CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_role_group ADD CONSTRAINT FK_4DD1CD58500376A0 FOREIGN KEY (group_role_id) REFERENCES group_role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_role_group ADD CONSTRAINT FK_4DD1CD58FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C561220EA6');
        $this->addSql('DROP INDEX IDX_6DC044C561220EA6 ON `group`');
        $this->addSql('ALTER TABLE `group` DROP creator_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6493E506FC8');
        $this->addSql('DROP INDEX IDX_8D93D6493E506FC8 ON user');
        $this->addSql('ALTER TABLE user DROP walk_group_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE group_role_user DROP FOREIGN KEY FK_F39FF83C500376A0');
        $this->addSql('ALTER TABLE group_role_user DROP FOREIGN KEY FK_F39FF83CA76ED395');
        $this->addSql('ALTER TABLE group_role_group DROP FOREIGN KEY FK_4DD1CD58500376A0');
        $this->addSql('ALTER TABLE group_role_group DROP FOREIGN KEY FK_4DD1CD58FE54D947');
        $this->addSql('DROP TABLE group_role');
        $this->addSql('DROP TABLE group_role_user');
        $this->addSql('DROP TABLE group_role_group');
        $this->addSql('ALTER TABLE `group` ADD creator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C561220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_6DC044C561220EA6 ON `group` (creator_id)');
        $this->addSql('ALTER TABLE user ADD walk_group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6493E506FC8 FOREIGN KEY (walk_group_id) REFERENCES `group` (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8D93D6493E506FC8 ON user (walk_group_id)');
    }
}
