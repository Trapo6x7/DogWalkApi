<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250422140146 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE dog_race (dog_id INT NOT NULL, race_id INT NOT NULL, INDEX IDX_18E44E6F634DFEB (dog_id), INDEX IDX_18E44E6F6E59D40D (race_id), PRIMARY KEY(dog_id, race_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dog_race ADD CONSTRAINT FK_18E44E6F634DFEB FOREIGN KEY (dog_id) REFERENCES dog (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dog_race ADD CONSTRAINT FK_18E44E6F6E59D40D FOREIGN KEY (race_id) REFERENCES race (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dog DROP race
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE dog_race DROP FOREIGN KEY FK_18E44E6F634DFEB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dog_race DROP FOREIGN KEY FK_18E44E6F6E59D40D
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE dog_race
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dog ADD race VARCHAR(255) NOT NULL
        SQL);
    }
}
