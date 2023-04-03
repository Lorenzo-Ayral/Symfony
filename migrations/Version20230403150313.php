<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230403150313 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE plat_ingredient (id INT AUTO_INCREMENT NOT NULL, plat_id_id INT DEFAULT NULL, ingredient_id_id INT DEFAULT NULL, INDEX IDX_E0ED47FBEF4C182B (plat_id_id), INDEX IDX_E0ED47FB6676F996 (ingredient_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE plat_ingredient ADD CONSTRAINT FK_E0ED47FBEF4C182B FOREIGN KEY (plat_id_id) REFERENCES plat (id)');
        $this->addSql('ALTER TABLE plat_ingredient ADD CONSTRAINT FK_E0ED47FB6676F996 FOREIGN KEY (ingredient_id_id) REFERENCES ingredients (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plat_ingredient DROP FOREIGN KEY FK_E0ED47FBEF4C182B');
        $this->addSql('ALTER TABLE plat_ingredient DROP FOREIGN KEY FK_E0ED47FB6676F996');
        $this->addSql('DROP TABLE plat_ingredient');
    }
}
