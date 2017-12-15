<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171215042154 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F098D9F6D38');
        $this->addSql('DROP INDEX IDX_52EA1F098D9F6D38 ON order_item');
        $this->addSql('ALTER TABLE order_item CHANGE order_id `order` CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F09F5299398 FOREIGN KEY (`order`) REFERENCES `order` (id)');
        $this->addSql('CREATE INDEX IDX_52EA1F09F5299398 ON order_item (`order`)');
        $this->addSql('ALTER TABLE `order` DROP INDEX UNIQ_F52993989395C3F3, ADD INDEX IDX_F52993989395C3F3 (customer_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `order` DROP INDEX IDX_F52993989395C3F3, ADD UNIQUE INDEX UNIQ_F52993989395C3F3 (customer_id)');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F09F5299398');
        $this->addSql('DROP INDEX IDX_52EA1F09F5299398 ON order_item');
        $this->addSql('ALTER TABLE order_item CHANGE `order` order_id CHAR(36) DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('CREATE INDEX IDX_52EA1F098D9F6D38 ON order_item (order_id)');
    }
}
