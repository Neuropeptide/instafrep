<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200701124704 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE post_notification (id INT NOT NULL, linked_post_id INT NOT NULL, INDEX IDX_14690B1920C13BBB (linked_post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE post_notification ADD CONSTRAINT FK_14690B1920C13BBB FOREIGN KEY (linked_post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE post_notification ADD CONSTRAINT FK_14690B19BF396750 FOREIGN KEY (id) REFERENCES notification (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification ADD discr VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE post_notification');
        $this->addSql('ALTER TABLE notification DROP discr');
    }
}
