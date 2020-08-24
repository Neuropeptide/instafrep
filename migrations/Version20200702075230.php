<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200702075230 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post ADD papa_group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF0EDAF55 FOREIGN KEY (papa_group_id) REFERENCES `group` (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DF0EDAF55 ON post (papa_group_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DF0EDAF55');
        $this->addSql('DROP INDEX IDX_5A8A6C8DF0EDAF55 ON post');
        $this->addSql('ALTER TABLE post DROP papa_group_id');
    }
}
