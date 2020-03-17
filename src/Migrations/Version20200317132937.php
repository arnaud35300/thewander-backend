<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200317132937 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE preference (id INT AUTO_INCREMENT NOT NULL, volume SMALLINT NOT NULL, soundscape VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD preference_id INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D81022C0 FOREIGN KEY (preference_id) REFERENCES preference (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649D81022C0 ON user (preference_id)');
        $this->addSql('ALTER TABLE celestial_body DROP nb_stars');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D81022C0');
        $this->addSql('DROP TABLE preference');
        $this->addSql('ALTER TABLE celestial_body ADD nb_stars INT DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_8D93D649D81022C0 ON user');
        $this->addSql('ALTER TABLE user DROP preference_id');
    }
}
