<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210107213125 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favoris (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, items LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', session_id VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8933C432A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, property_id INT DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INT NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_C53D045F549213EC (property_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE property (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, surface INT NOT NULL, rooms INT NOT NULL, bedrooms INT NOT NULL, floor INT NOT NULL, price INT NOT NULL, heat INT NOT NULL, type INT NOT NULL, mode INT NOT NULL, city VARCHAR(255) NOT NULL, adress VARCHAR(255) NOT NULL, sold TINYINT(1) DEFAULT \'0\' NOT NULL, created_at DATETIME NOT NULL, postalcode VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8BF21CDE2B36786B (title), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE favoris ADD CONSTRAINT FK_8933C432A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F549213EC FOREIGN KEY (property_id) REFERENCES property (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F549213EC');
        $this->addSql('ALTER TABLE favoris DROP FOREIGN KEY FK_8933C432A76ED395');
        $this->addSql('DROP TABLE favoris');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE property');
        $this->addSql('DROP TABLE user');
    }
}
