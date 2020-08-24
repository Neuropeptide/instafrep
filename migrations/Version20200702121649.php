<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200702121649 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE like_notification (id INT NOT NULL, linked_post_id INT NOT NULL, INDEX IDX_B5E71D0320C13BBB (linked_post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE like_notification ADD CONSTRAINT FK_B5E71D0320C13BBB FOREIGN KEY (linked_post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE like_notification ADD CONSTRAINT FK_B5E71D03BF396750 FOREIGN KEY (id) REFERENCES notification (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE like_notification');
    }
}
