<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250610181602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE comment DROP FOREIGN KEY FK_9474526C2F68B530
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_9474526C2F68B530 ON comment
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comment CHANGE group_id_id group_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comment ADD CONSTRAINT FK_9474526CFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9474526CFE54D947 ON comment (group_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE comment DROP FOREIGN KEY FK_9474526CFE54D947
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_9474526CFE54D947 ON comment
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comment CHANGE group_id group_id_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comment ADD CONSTRAINT FK_9474526C2F68B530 FOREIGN KEY (group_id_id) REFERENCES `group` (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9474526C2F68B530 ON comment (group_id_id)
        SQL);
    }
}
