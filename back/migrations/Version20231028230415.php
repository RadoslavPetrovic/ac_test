<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231028230415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE label (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, color VARCHAR(9) NOT NULL)');
        $this->addSql('CREATE TABLE label_task (label_id INTEGER NOT NULL, task_id INTEGER NOT NULL, PRIMARY KEY(label_id, task_id), CONSTRAINT FK_9E464EE933B92F39 FOREIGN KEY (label_id) REFERENCES label (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9E464EE98DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_9E464EE933B92F39 ON label_task (label_id)');
        $this->addSql('CREATE INDEX IDX_9E464EE98DB60186 ON label_task (task_id)');
        $this->addSql('CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, is_completed BOOLEAN NOT NULL, task_list_id INTEGER NOT NULL, position INTEGER NOT NULL, start_on DATE DEFAULT NULL, due_on DATE DEFAULT NULL, is_important BOOLEAN NOT NULL, completed_on DATE DEFAULT NULL)');
        $this->addSql('CREATE TABLE task_list (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, position INTEGER NOT NULL, is_completed BOOLEAN NOT NULL, is_trashed BOOLEAN NOT NULL)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, avatar_url VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
        $this->addSql('CREATE TABLE user_task (user_id INTEGER NOT NULL, task_id INTEGER NOT NULL, PRIMARY KEY(user_id, task_id), CONSTRAINT FK_28FF97ECA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_28FF97EC8DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_28FF97ECA76ED395 ON user_task (user_id)');
        $this->addSql('CREATE INDEX IDX_28FF97EC8DB60186 ON user_task (task_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE label');
        $this->addSql('DROP TABLE label_task');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE task_list');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_task');
    }
}
