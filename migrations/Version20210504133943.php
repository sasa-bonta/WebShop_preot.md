<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210504133943 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD available_amount INT NOT NULL');
        $this->addSql('ALTER TABLE product RENAME INDEX product_code_uindex TO UNIQ_D34A04AD77153098');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP available_amount');
        $this->addSql('ALTER TABLE product RENAME INDEX uniq_d34a04ad77153098 TO product_code_uindex');
    }
}
