<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250319142729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE session_plan (session_id INT NOT NULL, plan_id INT NOT NULL, INDEX IDX_1B2B5B57613FECDF (session_id), INDEX IDX_1B2B5B57E899029B (plan_id), PRIMARY KEY(session_id, plan_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE session_plan ADD CONSTRAINT FK_1B2B5B57613FECDF FOREIGN KEY (session_id) REFERENCES session (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session_plan ADD CONSTRAINT FK_1B2B5B57E899029B FOREIGN KEY (plan_id) REFERENCES plan (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE session_plan DROP FOREIGN KEY FK_1B2B5B57613FECDF');
        $this->addSql('ALTER TABLE session_plan DROP FOREIGN KEY FK_1B2B5B57E899029B');
        $this->addSql('DROP TABLE session_plan');
    }
}
