<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250321123615 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE scheduled_session (id INT AUTO_INCREMENT NOT NULL, session_id INT DEFAULT NULL, plan_id INT DEFAULT NULL, user_id INT DEFAULT NULL, scheduled_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B4838390613FECDF (session_id), INDEX IDX_B4838390E899029B (plan_id), INDEX IDX_B4838390A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE scheduled_session ADD CONSTRAINT FK_B4838390613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE scheduled_session ADD CONSTRAINT FK_B4838390E899029B FOREIGN KEY (plan_id) REFERENCES plan (id)');
        $this->addSql('ALTER TABLE scheduled_session ADD CONSTRAINT FK_B4838390A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE exercise CHANGE created_by_id created_by_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE scheduled_session DROP FOREIGN KEY FK_B4838390613FECDF');
        $this->addSql('ALTER TABLE scheduled_session DROP FOREIGN KEY FK_B4838390E899029B');
        $this->addSql('ALTER TABLE scheduled_session DROP FOREIGN KEY FK_B4838390A76ED395');
        $this->addSql('DROP TABLE scheduled_session');
        $this->addSql('ALTER TABLE exercise CHANGE created_by_id created_by_id INT DEFAULT NULL');
    }
}
