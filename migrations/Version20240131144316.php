<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240131144316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auction (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, item_name VARCHAR(255) NOT NULL, item_description VARCHAR(255) DEFAULT NULL, price DOUBLE PRECISION NOT NULL, min_bid INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(255) NOT NULL, INDEX IDX_DEE4F593A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE download_files (id INT AUTO_INCREMENT NOT NULL, auction_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, realname VARCHAR(255) NOT NULL, realpath VARCHAR(255) NOT NULL, publicpath VARCHAR(255) NOT NULL, mime_type VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_294FCCE557B8F0DE (auction_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offer (id INT AUTO_INCREMENT NOT NULL, auction_id INT NOT NULL, user_id INT NOT NULL, amount DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_29D6873E57B8F0DE (auction_id), INDEX IDX_29D6873EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_token (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_C74F2195C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE auction ADD CONSTRAINT FK_DEE4F593A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE download_files ADD CONSTRAINT FK_294FCCE557B8F0DE FOREIGN KEY (auction_id) REFERENCES auction (id)');
        $this->addSql('ALTER TABLE offer ADD CONSTRAINT FK_29D6873E57B8F0DE FOREIGN KEY (auction_id) REFERENCES auction (id)');
        $this->addSql('ALTER TABLE offer ADD CONSTRAINT FK_29D6873EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auction DROP FOREIGN KEY FK_DEE4F593A76ED395');
        $this->addSql('ALTER TABLE download_files DROP FOREIGN KEY FK_294FCCE557B8F0DE');
        $this->addSql('ALTER TABLE offer DROP FOREIGN KEY FK_29D6873E57B8F0DE');
        $this->addSql('ALTER TABLE offer DROP FOREIGN KEY FK_29D6873EA76ED395');
        $this->addSql('DROP TABLE auction');
        $this->addSql('DROP TABLE download_files');
        $this->addSql('DROP TABLE offer');
        $this->addSql('DROP TABLE refresh_token');
        $this->addSql('DROP TABLE user');
    }
}
