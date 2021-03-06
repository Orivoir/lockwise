<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191216074057 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE account (id INT AUTO_INCREMENT NOT NULL, plateform VARCHAR(255) NOT NULL, password LONGTEXT NOT NULL, create_at DATETIME NOT NULL, update_at DATETIME DEFAULT NULL, is_remove TINYINT(1) NOT NULL, is_favorite TINYINT(1) NOT NULL, login VARCHAR(255) DEFAULT NULL, remove_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE code_recup (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, content VARCHAR(255) NOT NULL, create_at DATETIME NOT NULL, is_remove TINYINT(1) NOT NULL, remove_at DATETIME DEFAULT NULL, INDEX IDX_ED41D3399B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE code_recup ADD CONSTRAINT FK_ED41D3399B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE code_recup DROP FOREIGN KEY FK_ED41D3399B6B5FBA');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE code_recup');
    }
}
