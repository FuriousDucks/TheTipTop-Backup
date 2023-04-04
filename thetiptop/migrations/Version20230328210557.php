<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230328210557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer ADD date_of_birth VARCHAR(255) NOT NULL, ADD facebook_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user DROP date_of_birth, DROP tel, DROP facebook_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer DROP date_of_birth, DROP facebook_id');
        $this->addSql('ALTER TABLE user ADD date_of_birth VARCHAR(255) DEFAULT NULL, ADD tel VARCHAR(255) DEFAULT NULL, ADD facebook_id VARCHAR(255) DEFAULT NULL');
    }
}
