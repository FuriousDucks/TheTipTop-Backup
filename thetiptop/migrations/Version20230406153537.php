<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230406153537 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE winner DROP FOREIGN KEY FK_CF6600E19EB6921');
        $this->addSql('DROP INDEX UNIQ_CF6600E19EB6921 ON winner');
        $this->addSql('ALTER TABLE winner ADD customer_id INT NOT NULL, DROP client_id');
        $this->addSql('ALTER TABLE winner ADD CONSTRAINT FK_CF6600E9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('CREATE INDEX IDX_CF6600E9395C3F3 ON winner (customer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE winner DROP FOREIGN KEY FK_CF6600E9395C3F3');
        $this->addSql('DROP INDEX IDX_CF6600E9395C3F3 ON winner');
        $this->addSql('ALTER TABLE winner ADD client_id INT DEFAULT NULL, DROP customer_id');
        $this->addSql('ALTER TABLE winner ADD CONSTRAINT FK_CF6600E19EB6921 FOREIGN KEY (client_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CF6600E19EB6921 ON winner (client_id)');
    }
}
