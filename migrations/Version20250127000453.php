<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250127000453 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE session_exercise (id INT AUTO_INCREMENT NOT NULL, session_id INT DEFAULT NULL, exercise_id INT DEFAULT NULL, sets INT DEFAULT NULL, reps_per_set JSON DEFAULT NULL, weight INT DEFAULT NULL, duration INT DEFAULT NULL, INDEX IDX_2576B574613FECDF (session_id), INDEX IDX_2576B574E934951A (exercise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE session_exercise ADD CONSTRAINT FK_2576B574613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE session_exercise ADD CONSTRAINT FK_2576B574E934951A FOREIGN KEY (exercise_id) REFERENCES exercises (id)');
        $this->addSql('ALTER TABLE exercises DROP FOREIGN KEY FK_AEDAD51C613FECDF');
        $this->addSql('DROP INDEX IDX_AEDAD51C613FECDF ON exercises');
        $this->addSql('ALTER TABLE exercises ADD description LONGTEXT DEFAULT NULL, ADD muscles VARCHAR(255) DEFAULT NULL, DROP session_id, DROP sets, DROP reps_per_set, DROP duration, DROP weight');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE session_exercise DROP FOREIGN KEY FK_2576B574613FECDF');
        $this->addSql('ALTER TABLE session_exercise DROP FOREIGN KEY FK_2576B574E934951A');
        $this->addSql('DROP TABLE session_exercise');
        $this->addSql('ALTER TABLE exercises ADD session_id INT NOT NULL, ADD sets INT NOT NULL, ADD reps_per_set JSON DEFAULT NULL, ADD duration INT DEFAULT NULL, ADD weight DOUBLE PRECISION DEFAULT NULL, DROP description, DROP muscles');
        $this->addSql('ALTER TABLE exercises ADD CONSTRAINT FK_AEDAD51C613FECDF FOREIGN KEY (session_id) REFERENCES session (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_AEDAD51C613FECDF ON exercises (session_id)');
    }
}
