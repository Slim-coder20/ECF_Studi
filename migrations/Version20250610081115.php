<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250610081115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE trajet ADD vehicule_id INT DEFAULT NULL');
        $this->addSql('UPDATE trajet SET vehicule_id = 6'); // ✅ ID existant
        $this->addSql('ALTER TABLE trajet MODIFY vehicule_id INT NOT NULL');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98C4A4A3511 FOREIGN KEY (vehicule_id) REFERENCES vehicule (id)');
        $this->addSql('CREATE INDEX IDX_2B5BA98C4A4A3511 ON trajet (vehicule_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98C4A4A3511
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_2B5BA98C4A4A3511 ON trajet
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trajet DROP vehicule_id
        SQL);
    }
}
