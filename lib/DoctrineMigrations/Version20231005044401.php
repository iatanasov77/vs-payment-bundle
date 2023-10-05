<?php

declare(strict_types=1);

namespace Vankosoft\PaymentBundle\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231005044401 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE VSPAY_PricingPlanSubscriptions (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, pricing_plan_id INT DEFAULT NULL, date DATETIME NOT NULL, INDEX IDX_6B1FCAA2A76ED395 (user_id), INDEX IDX_6B1FCAA229628C71 (pricing_plan_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSPAY_PricingPlan_PaidServices (pricing_plan_id INT NOT NULL, paid_service_period_id INT NOT NULL, INDEX IDX_93A21F2029628C71 (pricing_plan_id), INDEX IDX_93A21F20F504121C (paid_service_period_id), PRIMARY KEY(pricing_plan_id, paid_service_period_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE VSPAY_PricingPlanSubscriptions ADD CONSTRAINT FK_6B1FCAA2A76ED395 FOREIGN KEY (user_id) REFERENCES VSUM_Users (id)');
        $this->addSql('ALTER TABLE VSPAY_PricingPlanSubscriptions ADD CONSTRAINT FK_6B1FCAA229628C71 FOREIGN KEY (pricing_plan_id) REFERENCES VSPAY_PricingPlans (id)');
        $this->addSql('ALTER TABLE VSPAY_PricingPlan_PaidServices ADD CONSTRAINT FK_93A21F2029628C71 FOREIGN KEY (pricing_plan_id) REFERENCES VSPAY_PricingPlans (id)');
        $this->addSql('ALTER TABLE VSPAY_PricingPlan_PaidServices ADD CONSTRAINT FK_93A21F20F504121C FOREIGN KEY (paid_service_period_id) REFERENCES VSUS_PayedServiceSubscriptionPeriods (id)');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id maintenance_page_id  INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id ) REFERENCES VSCMS_Pages (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id )');
        $this->addSql('ALTER TABLE VSPAY_Order CHANGE status status ENUM(\'shopping_cart\', \'paid_order\', \'failed_order\')');
        $this->addSql('ALTER TABLE VSPAY_PricingPlans DROP FOREIGN KEY FK_194B9632F504121C');
        $this->addSql('DROP INDEX UNIQ_194B9632F504121C ON VSPAY_PricingPlans');
        $this->addSql('ALTER TABLE VSPAY_PricingPlans ADD currency_id INT DEFAULT NULL, ADD price NUMERIC(8, 2) DEFAULT \'0.00\' NOT NULL, ADD subscription_priority INT DEFAULT 1 NOT NULL, DROP paid_service_period_id');
        $this->addSql('ALTER TABLE VSPAY_PricingPlans ADD CONSTRAINT FK_194B963238248176 FOREIGN KEY (currency_id) REFERENCES VSPAY_Currency (id)');
        $this->addSql('CREATE INDEX IDX_194B963238248176 ON VSPAY_PricingPlans (currency_id)');
        $this->addSql('ALTER TABLE VSUM_UsersInfo CHANGE title title ENUM(\'mr\', \'mrs\', \'miss\')');
        $this->addSql('ALTER TABLE VSUS_PayedServiceSubscriptions DROP subscription_code, DROP subscription_priority');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE VSPAY_PricingPlanSubscriptions DROP FOREIGN KEY FK_6B1FCAA2A76ED395');
        $this->addSql('ALTER TABLE VSPAY_PricingPlanSubscriptions DROP FOREIGN KEY FK_6B1FCAA229628C71');
        $this->addSql('ALTER TABLE VSPAY_PricingPlan_PaidServices DROP FOREIGN KEY FK_93A21F2029628C71');
        $this->addSql('ALTER TABLE VSPAY_PricingPlan_PaidServices DROP FOREIGN KEY FK_93A21F20F504121C');
        $this->addSql('DROP TABLE VSPAY_PricingPlanSubscriptions');
        $this->addSql('DROP TABLE VSPAY_PricingPlan_PaidServices');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id  maintenance_page_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id) REFERENCES VSCMS_Pages (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id)');
        $this->addSql('ALTER TABLE VSPAY_Order CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE VSPAY_PricingPlans DROP FOREIGN KEY FK_194B963238248176');
        $this->addSql('DROP INDEX IDX_194B963238248176 ON VSPAY_PricingPlans');
        $this->addSql('ALTER TABLE VSPAY_PricingPlans ADD paid_service_period_id INT NOT NULL, DROP currency_id, DROP price, DROP subscription_priority');
        $this->addSql('ALTER TABLE VSPAY_PricingPlans ADD CONSTRAINT FK_194B9632F504121C FOREIGN KEY (paid_service_period_id) REFERENCES VSUS_PayedServiceSubscriptionPeriods (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_194B9632F504121C ON VSPAY_PricingPlans (paid_service_period_id)');
        $this->addSql('ALTER TABLE VSUM_UsersInfo CHANGE title title VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE VSUS_PayedServiceSubscriptions ADD subscription_code VARCHAR(64) NOT NULL COMMENT \'Subscription Code Group Payed Services for an identical parameter but with differents levels(priority).\', ADD subscription_priority INT NOT NULL COMMENT \'Subscription Priority is the level of a Subscription Code.\'');
    }
}
