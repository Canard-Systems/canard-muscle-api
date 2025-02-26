<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250226101718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_7BA2F5EBC732DAC7 ON api_token');
        $this->addSql('ALTER TABLE api_token ADD token_hash VARCHAR(64) NOT NULL, CHANGE encrypted_token encrypted_token LONGTEXT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7BA2F5EBB3BC57DA ON api_token (token_hash)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_7BA2F5EBB3BC57DA ON api_token');
        $this->addSql('ALTER TABLE api_token DROP token_hash, CHANGE encrypted_token encrypted_token VARCHAR(768) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7BA2F5EBC732DAC7 ON api_token (encrypted_token)');
    }
}
