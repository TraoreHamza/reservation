<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250618091541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE booking (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, status VARCHAR(255) NOT NULL, start_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , end_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            )
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE booking_Equipment (booking_id INTEGER NOT NULL, Equipment_id INTEGER NOT NULL, PRIMARY KEY(booking_id, Equipment_id), CONSTRAINT FK_212EA5823301C60 FOREIGN KEY (booking_id) REFERENCES booking (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_212EA582806F0F5C FOREIGN KEY (Equipment_id) REFERENCES Equipment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_212EA5823301C60 ON booking_Equipment (booking_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_212EA582806F0F5C ON booking_Equipment (Equipment_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE booking_option (booking_id INTEGER NOT NULL, option_id INTEGER NOT NULL, PRIMARY KEY(booking_id, option_id), CONSTRAINT FK_E11C3B4E3301C60 FOREIGN KEY (booking_id) REFERENCES booking (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_E11C3B4EA7C41D6F FOREIGN KEY (option_id) REFERENCES option (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E11C3B4E3301C60 ON booking_option (booking_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E11C3B4EA7C41D6F ON booking_option (option_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE client (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(50) DEFAULT NULL, addresse VARCHAR(255) DEFAULT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE Equipment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(60) NOT NULL, type VARCHAR(255) NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE Equipment_room (Equipment_id INTEGER NOT NULL, room_id INTEGER NOT NULL, PRIMARY KEY(Equipment_id, room_id), CONSTRAINT FK_9D43C8AB806F0F5C FOREIGN KEY (Equipment_id) REFERENCES Equipment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9D43C8AB54177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9D43C8AB806F0F5C ON Equipment_room (Equipment_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9D43C8AB54177093 ON Equipment_room (room_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE favorite (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, room_id INTEGER DEFAULT NULL, users_id INTEGER DEFAULT NULL, added_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , CONSTRAINT FK_68C58ED954177093 FOREIGN KEY (room_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_68C58ED967B3B43D FOREIGN KEY (users_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_68C58ED954177093 ON favorite (room_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_68C58ED967B3B43D ON favorite (users_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE location (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, city VARCHAR(255) DEFAULT NULL, department VARCHAR(80) DEFAULT NULL, number VARCHAR(50) DEFAULT NULL, state VARCHAR(255) DEFAULT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE option (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(60) NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE option_room (option_id INTEGER NOT NULL, room_id INTEGER NOT NULL, PRIMARY KEY(option_id, room_id), CONSTRAINT FK_9A91FB78A7C41D6F FOREIGN KEY (option_id) REFERENCES option (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9A91FB7854177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9A91FB78A7C41D6F ON option_room (option_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9A91FB7854177093 ON option_room (room_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE quotation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, room_id INTEGER NOT NULL, client_id INTEGER NOT NULL, price VARCHAR(50) NOT NULL, date VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , CONSTRAINT FK_474A8DB954177093 FOREIGN KEY (room_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_474A8DB919EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_474A8DB954177093 ON quotation (room_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_474A8DB919EB6921 ON quotation (client_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE review (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, star INTEGER DEFAULT NULL, content VARCHAR(255) DEFAULT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE room (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(50) NOT NULL, capacity INTEGER NOT NULL, description CLOB DEFAULT NULL, is_available BOOLEAN NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE room_room (room_source INTEGER NOT NULL, room_target INTEGER NOT NULL, PRIMARY KEY(room_source, room_target), CONSTRAINT FK_119BBBFF1EC4B9B2 FOREIGN KEY (room_source) REFERENCES room (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_119BBBFF721E93D FOREIGN KEY (room_target) REFERENCES room (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_119BBBFF1EC4B9B2 ON room_room (room_source)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_119BBBFF721E93D ON room_room (room_target)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
            , warning INTEGER NOT NULL, is_banned BOOLEAN NOT NULL, is_active BOOLEAN NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            )
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
            )
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE booking
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE booking_Equipment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE booking_option
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE client
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE Equipment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE Equipment_room
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE favorite
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE location
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE option
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE option_room
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE quotation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE review
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE room
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE room_room
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "user"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
