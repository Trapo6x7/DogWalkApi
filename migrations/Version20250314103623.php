<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250314103623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE walk DROP FOREIGN KEY FK_8D917A552F68B530');
        $this->addSql('DROP INDEX IDX_8D917A552F68B530 ON walk');
        $this->addSql('ALTER TABLE walk DROP group_id_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE walk ADD group_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE walk ADD CONSTRAINT FK_8D917A552F68B530 FOREIGN KEY (group_id_id) REFERENCES `group` (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8D917A552F68B530 ON walk (group_id_id)');
    }
}
