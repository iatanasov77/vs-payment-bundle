<?php

declare(strict_types=1);

namespace Vankosoft\PaymentBundle\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240104064257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE VSPAY_Coupons (id INT AUTO_INCREMENT NOT NULL, currency_id INT DEFAULT NULL, code VARCHAR(16) NOT NULL, name VARCHAR(255) DEFAULT NULL, amount_off NUMERIC(8, 2) DEFAULT NULL, percent_off NUMERIC(8, 2) DEFAULT NULL, valid TINYINT(1) NOT NULL, INDEX IDX_117A76D538248176 (currency_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'Coupons fields are Inspired by Stripe Coupon Fields\' ');
        $this->addSql('ALTER TABLE VSPAY_Coupons ADD CONSTRAINT FK_117A76D538248176 FOREIGN KEY (currency_id) REFERENCES VSPAY_Currency (id)');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id maintenance_page_id  INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id ) REFERENCES VSCMS_Pages (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id )');
        $this->addSql('ALTER TABLE VSPAY_Order ADD coupon_id INT DEFAULT NULL, CHANGE status status ENUM(\'shopping_cart\', \'paid_order\', \'pending_order\', \'failed_order\')');
        $this->addSql('ALTER TABLE VSPAY_Order ADD CONSTRAINT FK_8795450266C5951B FOREIGN KEY (coupon_id) REFERENCES VSPAY_Coupons (id)');
        $this->addSql('CREATE INDEX IDX_8795450266C5951B ON VSPAY_Order (coupon_id)');
        $this->addSql('ALTER TABLE VSUM_UsersInfo CHANGE title title ENUM(\'mr\', \'mrs\', \'miss\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE VSPAY_Order DROP FOREIGN KEY FK_8795450266C5951B');
        $this->addSql('ALTER TABLE VSPAY_Coupons DROP FOREIGN KEY FK_117A76D538248176');
        $this->addSql('DROP TABLE VSPAY_Coupons');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id  maintenance_page_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id) REFERENCES VSCMS_Pages (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id)');
        $this->addSql('DROP INDEX IDX_8795450266C5951B ON VSPAY_Order');
        $this->addSql('ALTER TABLE VSPAY_Order DROP coupon_id, CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE VSUM_UsersInfo CHANGE title title VARCHAR(255) DEFAULT NULL');
    }
}
