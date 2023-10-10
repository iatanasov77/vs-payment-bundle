<?php

declare(strict_types=1);

namespace Vankosoft\PaymentBundle\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231010051517 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE VSPAY_PricingPlan_PaidServices DROP FOREIGN KEY FK_93A21F20F504121C');
        $this->addSql('ALTER TABLE VSPAY_PricingPlan_PaidServices DROP FOREIGN KEY FK_93A21F2029628C71');
        $this->addSql('DROP TABLE VSPAY_PricingPlan_PaidServices');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id maintenance_page_id  INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id ) REFERENCES VSCMS_Pages (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id )');
        $this->addSql('ALTER TABLE VSPAY_Order CHANGE status status ENUM(\'shopping_cart\', \'paid_order\', \'failed_order\')');
        $this->addSql('ALTER TABLE VSPAY_PricingPlans ADD paid_service_id INT NOT NULL');
        $this->addSql('ALTER TABLE VSPAY_PricingPlans ADD CONSTRAINT FK_194B963287FFD8A7 FOREIGN KEY (paid_service_id) REFERENCES VSUS_PayedServiceSubscriptionPeriods (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_194B963287FFD8A7 ON VSPAY_PricingPlans (paid_service_id)');
        $this->addSql('ALTER TABLE VSUM_UsersInfo CHANGE title title ENUM(\'mr\', \'mrs\', \'miss\')');
        $this->addSql('ALTER TABLE VVP_VideoPlatformStorages CHANGE storage_type storage_type ENUM(\'coconut\', \'local\' , \'s3\' , \'digitalocean\')');
        $this->addSql('ALTER TABLE VVP_Videos_Files CHANGE storage_type storage_type ENUM(\'coconut\', \'local\' , \'s3\' , \'digitalocean\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE VSPAY_PricingPlan_PaidServices (pricing_plan_id INT NOT NULL, paid_service_period_id INT NOT NULL, INDEX IDX_93A21F2029628C71 (pricing_plan_id), INDEX IDX_93A21F20F504121C (paid_service_period_id), PRIMARY KEY(pricing_plan_id, paid_service_period_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE VSPAY_PricingPlan_PaidServices ADD CONSTRAINT FK_93A21F20F504121C FOREIGN KEY (paid_service_period_id) REFERENCES VSUS_PayedServiceSubscriptionPeriods (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE VSPAY_PricingPlan_PaidServices ADD CONSTRAINT FK_93A21F2029628C71 FOREIGN KEY (pricing_plan_id) REFERENCES VSPAY_PricingPlans (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id  maintenance_page_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id) REFERENCES VSCMS_Pages (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id)');
        $this->addSql('ALTER TABLE VSPAY_Order CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE VSPAY_PricingPlans DROP FOREIGN KEY FK_194B963287FFD8A7');
        $this->addSql('DROP INDEX IDX_194B963287FFD8A7 ON VSPAY_PricingPlans');
        $this->addSql('ALTER TABLE VSPAY_PricingPlans DROP paid_service_id');
        $this->addSql('ALTER TABLE VSUM_UsersInfo CHANGE title title VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE VVP_VideoPlatformStorages CHANGE storage_type storage_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE VVP_Videos_Files CHANGE storage_type storage_type VARCHAR(255) DEFAULT NULL');
    }
}
