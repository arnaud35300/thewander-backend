<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200305161444 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE INDEX idx_name ON property (name)');
        $this->addSql('CREATE INDEX idx_nickname ON user (nickname)');
        $this->addSql('CREATE INDEX idx_slug ON user (slug)');
        $this->addSql('CREATE INDEX idx_name ON celestial_body (name)');
        $this->addSql('CREATE INDEX idx_slug ON celestial_body (slug)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX idx_name ON celestial_body');
        $this->addSql('DROP INDEX idx_slug ON celestial_body');
        $this->addSql('DROP INDEX idx_name ON property');
        $this->addSql('DROP INDEX idx_nickname ON user');
        $this->addSql('DROP INDEX idx_slug ON user');
    }
}
