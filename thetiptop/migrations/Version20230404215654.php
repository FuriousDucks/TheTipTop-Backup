<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230404215654 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contest_game (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) DEFAULT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, max_winners INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ticket ADD contest_id INT NOT NULL');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA31CD0F0DE FOREIGN KEY (contest_id) REFERENCES contest_game (id)');
        $this->addSql('CREATE INDEX IDX_97A0ADA31CD0F0DE ON ticket (contest_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA31CD0F0DE');
        $this->addSql('DROP TABLE contest_game');
        $this->addSql('DROP INDEX IDX_97A0ADA31CD0F0DE ON ticket');
        $this->addSql('ALTER TABLE ticket DROP contest_id');
    }
}
