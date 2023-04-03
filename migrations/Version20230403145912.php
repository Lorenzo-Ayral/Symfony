<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230403145912 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE menu_plat (id INT AUTO_INCREMENT NOT NULL, menu_id_id INT DEFAULT NULL, plat_id_id INT DEFAULT NULL, INDEX IDX_E8775249EEE8BD30 (menu_id_id), INDEX IDX_E8775249EF4C182B (plat_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE menu_plat ADD CONSTRAINT FK_E8775249EEE8BD30 FOREIGN KEY (menu_id_id) REFERENCES menu (id)');
        $this->addSql('ALTER TABLE menu_plat ADD CONSTRAINT FK_E8775249EF4C182B FOREIGN KEY (plat_id_id) REFERENCES plat (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu_plat DROP FOREIGN KEY FK_E8775249EEE8BD30');
        $this->addSql('ALTER TABLE menu_plat DROP FOREIGN KEY FK_E8775249EF4C182B');
        $this->addSql('DROP TABLE menu_plat');
    }
}
