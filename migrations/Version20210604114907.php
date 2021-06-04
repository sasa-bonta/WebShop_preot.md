<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210604114907 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, total DOUBLE PRECISION NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, user_id INT NOT NULL, payment_type VARCHAR(255) NOT NULL, payment_currency VARCHAR(255) NOT NULL, payment_card_code VARCHAR(16) DEFAULT NULL, payment_card_cvv VARCHAR(3) DEFAULT NULL, payment_card_expires_at DATE DEFAULT NULL, address_country VARCHAR(255) NOT NULL, address_state VARCHAR(255) NOT NULL, address_city VARCHAR(255) NOT NULL, address_address VARCHAR(255) NOT NULL, address_phone_number VARCHAR(255) NOT NULL, address_recipient VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_item (id INT AUTO_INCREMENT NOT NULL, product_code VARCHAR(255) NOT NULL, amount INT NOT NULL, price DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_item');
    }
}
