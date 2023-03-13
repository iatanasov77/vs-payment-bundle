<?php

declare(strict_types=1);

namespace Vankosoft\PaymentBundle\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230313101120 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE VSPAY_ProductCategories (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, taxon_id INT DEFAULT NULL, INDEX IDX_7D0F9A49727ACA70 (parent_id), UNIQUE INDEX UNIQ_7D0F9A49DE13F470 (taxon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSPAY_Product_Categories (product_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_DC57D6954584665A (product_id), INDEX IDX_DC57D69512469DE2 (category_id), PRIMARY KEY(product_id, category_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE VSPAY_ProductCategories ADD CONSTRAINT FK_7D0F9A49727ACA70 FOREIGN KEY (parent_id) REFERENCES VSPAY_ProductCategories (id)');
        $this->addSql('ALTER TABLE VSPAY_ProductCategories ADD CONSTRAINT FK_7D0F9A49DE13F470 FOREIGN KEY (taxon_id) REFERENCES VSAPP_Taxons (id)');
        $this->addSql('ALTER TABLE VSPAY_Product_Categories ADD CONSTRAINT FK_DC57D6954584665A FOREIGN KEY (product_id) REFERENCES VSPAY_Products (id)');
        $this->addSql('ALTER TABLE VSPAY_Product_Categories ADD CONSTRAINT FK_DC57D69512469DE2 FOREIGN KEY (category_id) REFERENCES VSPAY_ProductCategories (id)');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id maintenance_page_id  INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id ) REFERENCES VSCMS_Pages (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id )');
        $this->addSql('ALTER TABLE VSPAY_Order CHANGE status status ENUM(\'shopping_cart\', \'paid_order\', \'failed_order\')');
        $this->addSql('ALTER TABLE VSPAY_OrderItem ADD product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSPAY_OrderItem ADD CONSTRAINT FK_1C9B655C4584665A FOREIGN KEY (product_id) REFERENCES VSPAY_Products (id)');
        $this->addSql('CREATE INDEX IDX_1C9B655C4584665A ON VSPAY_OrderItem (product_id)');
        $this->addSql('ALTER TABLE VSPAY_Products ADD published TINYINT(1) NOT NULL, ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_568BFA30989D9B62 ON VSPAY_Products (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE VSPAY_ProductCategories DROP FOREIGN KEY FK_7D0F9A49727ACA70');
        $this->addSql('ALTER TABLE VSPAY_ProductCategories DROP FOREIGN KEY FK_7D0F9A49DE13F470');
        $this->addSql('ALTER TABLE VSPAY_Product_Categories DROP FOREIGN KEY FK_DC57D6954584665A');
        $this->addSql('ALTER TABLE VSPAY_Product_Categories DROP FOREIGN KEY FK_DC57D69512469DE2');
        $this->addSql('DROP TABLE VSPAY_ProductCategories');
        $this->addSql('DROP TABLE VSPAY_Product_Categories');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id  maintenance_page_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id) REFERENCES VSCMS_Pages (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id)');
        $this->addSql('ALTER TABLE VSPAY_Order CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE VSPAY_OrderItem DROP FOREIGN KEY FK_1C9B655C4584665A');
        $this->addSql('DROP INDEX IDX_1C9B655C4584665A ON VSPAY_OrderItem');
        $this->addSql('ALTER TABLE VSPAY_OrderItem DROP product_id');
        $this->addSql('DROP INDEX UNIQ_568BFA30989D9B62 ON VSPAY_Products');
        $this->addSql('ALTER TABLE VSPAY_Products DROP published, DROP slug');
    }
}
