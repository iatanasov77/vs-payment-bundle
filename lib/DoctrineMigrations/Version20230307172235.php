<?php

declare(strict_types=1);

namespace Vankosoft\PaymentBundle\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230307172235 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE VSPAY_GatewayConfig (id INT AUTO_INCREMENT NOT NULL, gateway_name VARCHAR(255) NOT NULL, factory_name VARCHAR(255) NOT NULL, config LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', title VARCHAR(255) DEFAULT \'\' NOT NULL, description VARCHAR(255) DEFAULT NULL, use_sandbox TINYINT(1) NOT NULL, sandbox_config LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSPAY_Order (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, payment_method_id INT DEFAULT NULL, payment_id INT DEFAULT NULL, total_amount DOUBLE PRECISION NOT NULL, currency_code VARCHAR(8) NOT NULL, description VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, status ENUM(\'shopping_cart\', \'paid_order\', \'failed_order\'), INDEX IDX_87954502A76ED395 (user_id), INDEX IDX_879545025AA1164F (payment_method_id), UNIQUE INDEX UNIQ_879545024C3A3BB (payment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSPAY_OrderItem (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, object_id INT DEFAULT NULL, object_type VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, currency_code VARCHAR(8) NOT NULL, INDEX IDX_1C9B655C8D9F6D38 (order_id), INDEX IDX_1C9B655C232D562B (object_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSPAY_Payment (id INT AUTO_INCREMENT NOT NULL, number VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, client_email VARCHAR(255) DEFAULT NULL, client_id VARCHAR(255) DEFAULT NULL, total_amount INT DEFAULT NULL, currency_code VARCHAR(255) DEFAULT NULL, details LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSPAY_PaymentMethod (id INT AUTO_INCREMENT NOT NULL, gateway_id INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, name VARCHAR(64) NOT NULL, active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_1CCD1B9F989D9B62 (slug), INDEX IDX_1CCD1B9F577F8E00 (gateway_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSUS_PayedServiceCategories (id INT AUTO_INCREMENT NOT NULL, taxon_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_9E88F124DE13F470 (taxon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSUS_PayedServiceSubscriptionPeriods (id INT AUTO_INCREMENT NOT NULL, payed_service_id INT DEFAULT NULL, subscription_period VARCHAR(64) NOT NULL, price VARCHAR(255) NOT NULL, currency_code VARCHAR(255) NOT NULL, INDEX IDX_1018A6BE5139FC0A (payed_service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSUS_PayedServiceSubscriptions (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, payed_service_id INT DEFAULT NULL, date DATETIME NOT NULL, subscription_code VARCHAR(64) NOT NULL COMMENT \'Subscription Code Group Payed Services for an identical parameter but with differents levels(priority).\', subscription_priority INT NOT NULL COMMENT \'Subscription Priority is the level of a Subscription Code.\', INDEX IDX_11A46ECAA76ED395 (user_id), INDEX IDX_11A46ECA5139FC0A (payed_service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSUS_PayedServices (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, title VARCHAR(64) NOT NULL, description LONGTEXT NOT NULL, subscription_code VARCHAR(64) NOT NULL COMMENT \'Subscription Code Group Payed Services for an identical parameter but with differents levels(priority).\', subscription_priority INT NOT NULL COMMENT \'Subscription Priority is the level of a Subscription Code.\', INDEX IDX_5E8A244512469DE2 (category_id), UNIQUE INDEX subscription_idx (subscription_code, subscription_priority), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSUS_PayedServicesAttributes (id INT AUTO_INCREMENT NOT NULL, payed_service_id INT DEFAULT NULL, name VARCHAR(64) NOT NULL, value VARCHAR(64) NOT NULL, INDEX IDX_685989135139FC0A (payed_service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE VSPAY_Order ADD CONSTRAINT FK_87954502A76ED395 FOREIGN KEY (user_id) REFERENCES VSUM_Users (id)');
        $this->addSql('ALTER TABLE VSPAY_Order ADD CONSTRAINT FK_879545025AA1164F FOREIGN KEY (payment_method_id) REFERENCES VSPAY_PaymentMethod (id)');
        $this->addSql('ALTER TABLE VSPAY_Order ADD CONSTRAINT FK_879545024C3A3BB FOREIGN KEY (payment_id) REFERENCES VSPAY_Payment (id)');
        $this->addSql('ALTER TABLE VSPAY_OrderItem ADD CONSTRAINT FK_1C9B655C8D9F6D38 FOREIGN KEY (order_id) REFERENCES VSPAY_Order (id)');
        $this->addSql('ALTER TABLE VSPAY_OrderItem ADD CONSTRAINT FK_1C9B655C232D562B FOREIGN KEY (object_id) REFERENCES VSUS_PayedServiceSubscriptionPeriods (id)');
        $this->addSql('ALTER TABLE VSPAY_PaymentMethod ADD CONSTRAINT FK_1CCD1B9F577F8E00 FOREIGN KEY (gateway_id) REFERENCES VSPAY_GatewayConfig (id)');
        $this->addSql('ALTER TABLE VSUS_PayedServiceCategories ADD CONSTRAINT FK_9E88F124DE13F470 FOREIGN KEY (taxon_id) REFERENCES VSAPP_Taxons (id)');
        $this->addSql('ALTER TABLE VSUS_PayedServiceSubscriptionPeriods ADD CONSTRAINT FK_1018A6BE5139FC0A FOREIGN KEY (payed_service_id) REFERENCES VSUS_PayedServices (id)');
        $this->addSql('ALTER TABLE VSUS_PayedServiceSubscriptions ADD CONSTRAINT FK_11A46ECAA76ED395 FOREIGN KEY (user_id) REFERENCES VSUM_Users (id)');
        $this->addSql('ALTER TABLE VSUS_PayedServiceSubscriptions ADD CONSTRAINT FK_11A46ECA5139FC0A FOREIGN KEY (payed_service_id) REFERENCES VSUS_PayedServiceSubscriptionPeriods (id)');
        $this->addSql('ALTER TABLE VSUS_PayedServices ADD CONSTRAINT FK_5E8A244512469DE2 FOREIGN KEY (category_id) REFERENCES VSUS_PayedServiceCategories (id)');
        $this->addSql('ALTER TABLE VSUS_PayedServicesAttributes ADD CONSTRAINT FK_685989135139FC0A FOREIGN KEY (payed_service_id) REFERENCES VSUS_PayedServices (id)');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id maintenance_page_id  INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id ) REFERENCES VSCMS_Pages (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id )');
        $this->addSql('ALTER TABLE VSCMS_TocPage CHANGE position position INT DEFAULT 999999');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE VSPAY_Order DROP FOREIGN KEY FK_87954502A76ED395');
        $this->addSql('ALTER TABLE VSPAY_Order DROP FOREIGN KEY FK_879545025AA1164F');
        $this->addSql('ALTER TABLE VSPAY_Order DROP FOREIGN KEY FK_879545024C3A3BB');
        $this->addSql('ALTER TABLE VSPAY_OrderItem DROP FOREIGN KEY FK_1C9B655C8D9F6D38');
        $this->addSql('ALTER TABLE VSPAY_OrderItem DROP FOREIGN KEY FK_1C9B655C232D562B');
        $this->addSql('ALTER TABLE VSPAY_PaymentMethod DROP FOREIGN KEY FK_1CCD1B9F577F8E00');
        $this->addSql('ALTER TABLE VSUS_PayedServiceCategories DROP FOREIGN KEY FK_9E88F124DE13F470');
        $this->addSql('ALTER TABLE VSUS_PayedServiceSubscriptionPeriods DROP FOREIGN KEY FK_1018A6BE5139FC0A');
        $this->addSql('ALTER TABLE VSUS_PayedServiceSubscriptions DROP FOREIGN KEY FK_11A46ECAA76ED395');
        $this->addSql('ALTER TABLE VSUS_PayedServiceSubscriptions DROP FOREIGN KEY FK_11A46ECA5139FC0A');
        $this->addSql('ALTER TABLE VSUS_PayedServices DROP FOREIGN KEY FK_5E8A244512469DE2');
        $this->addSql('ALTER TABLE VSUS_PayedServicesAttributes DROP FOREIGN KEY FK_685989135139FC0A');
        $this->addSql('DROP TABLE VSPAY_GatewayConfig');
        $this->addSql('DROP TABLE VSPAY_Order');
        $this->addSql('DROP TABLE VSPAY_OrderItem');
        $this->addSql('DROP TABLE VSPAY_Payment');
        $this->addSql('DROP TABLE VSPAY_PaymentMethod');
        $this->addSql('DROP TABLE VSUS_PayedServiceCategories');
        $this->addSql('DROP TABLE VSUS_PayedServiceSubscriptionPeriods');
        $this->addSql('DROP TABLE VSUS_PayedServiceSubscriptions');
        $this->addSql('DROP TABLE VSUS_PayedServices');
        $this->addSql('DROP TABLE VSUS_PayedServicesAttributes');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id  maintenance_page_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id) REFERENCES VSCMS_Pages (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id)');
        $this->addSql('ALTER TABLE VSCMS_TocPage CHANGE position position INT DEFAULT NULL');
    }
}
