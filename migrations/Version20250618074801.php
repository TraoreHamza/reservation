<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250618074801 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__quotation AS SELECT id, client_id, price, date, created_at, updated_at FROM quotation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE quotation
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE quotation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client_id INTEGER NOT NULL, price VARCHAR(50) NOT NULL, date VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , CONSTRAINT FK_474A8DB919EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO quotation (id, client_id, price, date, created_at, updated_at) SELECT id, client_id, price, date, created_at, updated_at FROM __temp__quotation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__quotation
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_474A8DB919EB6921 ON quotation (client_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__quotation AS SELECT id, client_id, price, date, created_at, updated_at FROM quotation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE quotation
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE quotation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client_id INTEGER NOT NULL, room_id INTEGER NOT NULL, price VARCHAR(50) NOT NULL, date VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , CONSTRAINT FK_474A8DB919EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_474A8DB954177093 FOREIGN KEY (room_id) REFERENCES room (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO quotation (id, client_id, price, date, created_at, updated_at) SELECT id, client_id, price, date, created_at, updated_at FROM __temp__quotation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__quotation
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_474A8DB919EB6921 ON quotation (client_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_474A8DB954177093 ON quotation (room_id)
        SQL);
    }
}
