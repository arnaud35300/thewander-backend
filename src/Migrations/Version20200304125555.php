<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200304125555 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_57698A6A5E237E06 ON role (name)');
        $this->addSql('ALTER TABLE property DROP unit, DROP value');
        $this->addSql('ALTER TABLE celestial_body ADD xPosition INT DEFAULT NULL, ADD yPosition INT DEFAULT NULL, DROP x_position, DROP y_position');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8879E8E55E237E06 ON rank (name)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE celestial_body ADD x_position INT DEFAULT NULL, ADD y_position INT DEFAULT NULL, DROP xPosition, DROP yPosition');
        $this->addSql('ALTER TABLE property ADD unit INT DEFAULT NULL, ADD value VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('DROP INDEX UNIQ_8879E8E55E237E06 ON rank');
        $this->addSql('DROP INDEX UNIQ_57698A6A5E237E06 ON role');
    }
}
