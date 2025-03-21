<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250321152102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE block_list (id INT AUTO_INCREMENT NOT NULL, blocker_id INT NOT NULL, blocked_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_82A6AA63548D5975 (blocker_id), INDEX IDX_82A6AA6321FF5136 (blocked_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE block_list ADD CONSTRAINT FK_82A6AA63548D5975 FOREIGN KEY (blocker_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE block_list ADD CONSTRAINT FK_82A6AA6321FF5136 FOREIGN KEY (blocked_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE block_list DROP FOREIGN KEY FK_82A6AA63548D5975');
        $this->addSql('ALTER TABLE block_list DROP FOREIGN KEY FK_82A6AA6321FF5136');
        $this->addSql('DROP TABLE block_list');
    }
}
