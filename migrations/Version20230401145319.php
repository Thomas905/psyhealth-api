<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230401145319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE room ADD plan_id INT NOT NULL');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519BE899029B FOREIGN KEY (plan_id) REFERENCES plan (id)');
        $this->addSql('CREATE INDEX IDX_729F519BE899029B ON room (plan_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519BE899029B');
        $this->addSql('DROP INDEX IDX_729F519BE899029B ON room');
        $this->addSql('ALTER TABLE room DROP plan_id');
    }
}
