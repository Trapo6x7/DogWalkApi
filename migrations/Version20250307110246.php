<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250307110246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE group_request (id INT AUTO_INCREMENT NOT NULL, walk_group_id INT NOT NULL, user_id INT NOT NULL, is_accepted TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_BD97DB933E506FC8 (walk_group_id), INDEX IDX_BD97DB93A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE group_request ADD CONSTRAINT FK_BD97DB933E506FC8 FOREIGN KEY (walk_group_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE group_request ADD CONSTRAINT FK_BD97DB93A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE group_request DROP FOREIGN KEY FK_BD97DB933E506FC8');
        $this->addSql('ALTER TABLE group_request DROP FOREIGN KEY FK_BD97DB93A76ED395');
        $this->addSql('DROP TABLE group_request');
    }
}
