<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250307083421 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE group_role_group DROP FOREIGN KEY FK_4DD1CD58500376A0');
        $this->addSql('ALTER TABLE group_role_group DROP FOREIGN KEY FK_4DD1CD58FE54D947');
        $this->addSql('ALTER TABLE group_role_user DROP FOREIGN KEY FK_F39FF83C500376A0');
        $this->addSql('ALTER TABLE group_role_user DROP FOREIGN KEY FK_F39FF83CA76ED395');
        $this->addSql('DROP TABLE group_role_group');
        $this->addSql('DROP TABLE group_role_user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE group_role_group (group_role_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_4DD1CD58500376A0 (group_role_id), INDEX IDX_4DD1CD58FE54D947 (group_id), PRIMARY KEY(group_role_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE group_role_user (group_role_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_F39FF83C500376A0 (group_role_id), INDEX IDX_F39FF83CA76ED395 (user_id), PRIMARY KEY(group_role_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE group_role_group ADD CONSTRAINT FK_4DD1CD58500376A0 FOREIGN KEY (group_role_id) REFERENCES group_role (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_role_group ADD CONSTRAINT FK_4DD1CD58FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_role_user ADD CONSTRAINT FK_F39FF83C500376A0 FOREIGN KEY (group_role_id) REFERENCES group_role (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_role_user ADD CONSTRAINT FK_F39FF83CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
