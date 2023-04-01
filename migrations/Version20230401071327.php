<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230401071327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE plan (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, month INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plan_question (plan_id INT NOT NULL, question_id INT NOT NULL, INDEX IDX_B8927E34E899029B (plan_id), INDEX IDX_B8927E341E27F6BF (question_id), PRIMARY KEY(plan_id, question_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE plan_question ADD CONSTRAINT FK_B8927E34E899029B FOREIGN KEY (plan_id) REFERENCES plan (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE plan_question ADD CONSTRAINT FK_B8927E341E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD plan_id INT DEFAULT NULL, ADD room_id INT DEFAULT NULL, ADD has_replied TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649E899029B FOREIGN KEY (plan_id) REFERENCES plan (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64954177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649E899029B ON user (plan_id)');
        $this->addSql('CREATE INDEX IDX_8D93D64954177093 ON user (room_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649E899029B');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D64954177093');
        $this->addSql('ALTER TABLE plan_question DROP FOREIGN KEY FK_B8927E34E899029B');
        $this->addSql('ALTER TABLE plan_question DROP FOREIGN KEY FK_B8927E341E27F6BF');
        $this->addSql('DROP TABLE plan');
        $this->addSql('DROP TABLE plan_question');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP INDEX IDX_8D93D649E899029B ON `user`');
        $this->addSql('DROP INDEX IDX_8D93D64954177093 ON `user`');
        $this->addSql('ALTER TABLE `user` DROP plan_id, DROP room_id, DROP has_replied');
    }
}
