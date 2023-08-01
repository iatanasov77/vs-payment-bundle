<?php

declare(strict_types=1);

namespace Vankosoft\PaymentBundle\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230731125503 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE VSPAY_PricingPlanCategories (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, taxon_id INT DEFAULT NULL, INDEX IDX_E6AE2009727ACA70 (parent_id), UNIQUE INDEX UNIQ_E6AE2009DE13F470 (taxon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSPAY_PricingPlans (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, paid_service_period_id INT NOT NULL, active TINYINT(1) NOT NULL, title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, premium TINYINT(1) NOT NULL, discount NUMERIC(8, 2) DEFAULT NULL, INDEX IDX_194B963212469DE2 (category_id), UNIQUE INDEX UNIQ_194B9632F504121C (paid_service_period_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE VSPAY_PricingPlanCategories ADD CONSTRAINT FK_E6AE2009727ACA70 FOREIGN KEY (parent_id) REFERENCES VSPAY_PricingPlanCategories (id)');
        $this->addSql('ALTER TABLE VSPAY_PricingPlanCategories ADD CONSTRAINT FK_E6AE2009DE13F470 FOREIGN KEY (taxon_id) REFERENCES VSAPP_Taxons (id)');
        $this->addSql('ALTER TABLE VSPAY_PricingPlans ADD CONSTRAINT FK_194B963212469DE2 FOREIGN KEY (category_id) REFERENCES VSPAY_PricingPlanCategories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE VSPAY_PricingPlans ADD CONSTRAINT FK_194B9632F504121C FOREIGN KEY (paid_service_period_id) REFERENCES VSUS_PayedServiceSubscriptionPeriods (id)');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id maintenance_page_id  INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id ) REFERENCES VSCMS_Pages (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id )');
        $this->addSql('ALTER TABLE VSPAY_Order CHANGE status status ENUM(\'shopping_cart\', \'paid_order\', \'failed_order\')');
        $this->addSql('ALTER TABLE VSUM_UsersInfo CHANGE title title ENUM(\'mr\', \'mrs\', \'miss\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE VSPAY_PricingPlanCategories DROP FOREIGN KEY FK_E6AE2009727ACA70');
        $this->addSql('ALTER TABLE VSPAY_PricingPlanCategories DROP FOREIGN KEY FK_E6AE2009DE13F470');
        $this->addSql('ALTER TABLE VSPAY_PricingPlans DROP FOREIGN KEY FK_194B963212469DE2');
        $this->addSql('ALTER TABLE VSPAY_PricingPlans DROP FOREIGN KEY FK_194B9632F504121C');
        $this->addSql('DROP TABLE VSPAY_PricingPlanCategories');
        $this->addSql('DROP TABLE VSPAY_PricingPlans');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id  maintenance_page_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id) REFERENCES VSCMS_Pages (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id)');
        $this->addSql('ALTER TABLE VSPAY_Order CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE VSUM_UsersInfo CHANGE title title VARCHAR(255) DEFAULT NULL');
    }
}
