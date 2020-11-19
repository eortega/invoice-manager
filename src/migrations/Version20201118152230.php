<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201118152230 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, invoice_report_id_id INT DEFAULT NULL, number VARCHAR(25) NOT NULL, amount INT NOT NULL, selling_price INT NOT NULL, due_on DATE NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_9065174496901F54 (number), INDEX IDX_90651744EA9FB65A (invoice_report_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice_report (id INT AUTO_INCREMENT NOT NULL, file_name VARCHAR(190) NOT NULL, records INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice_report_error (id INT AUTO_INCREMENT NOT NULL, invoice_report_id_id INT NOT NULL, data LONGTEXT NOT NULL, line INT NOT NULL, error VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_C806947CEA9FB65A (invoice_report_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744EA9FB65A FOREIGN KEY (invoice_report_id_id) REFERENCES invoice_report (id)');
        $this->addSql('ALTER TABLE invoice_report_error ADD CONSTRAINT FK_C806947CEA9FB65A FOREIGN KEY (invoice_report_id_id) REFERENCES invoice_report (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_90651744EA9FB65A');
        $this->addSql('ALTER TABLE invoice_report_error DROP FOREIGN KEY FK_C806947CEA9FB65A');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE invoice_report');
        $this->addSql('DROP TABLE invoice_report_error');
    }
}
