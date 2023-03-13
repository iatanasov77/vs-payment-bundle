<?php

declare(strict_types=1);

namespace Vankosoft\PaymentBundle\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230309172133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE VSUS_MailchimpAudiences (id INT AUTO_INCREMENT NOT NULL, audience_id VARCHAR(16) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VSUS_NewsletterSubscriptions (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, mailchimp_audience_id INT NOT NULL, user_email VARCHAR(64) NOT NULL, date DATETIME NOT NULL, INDEX IDX_E521F0DCA76ED395 (user_id), INDEX IDX_E521F0DCF03423AE (mailchimp_audience_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE VSUS_NewsletterSubscriptions ADD CONSTRAINT FK_E521F0DCA76ED395 FOREIGN KEY (user_id) REFERENCES VSUM_Users (id)');
        $this->addSql('ALTER TABLE VSUS_NewsletterSubscriptions ADD CONSTRAINT FK_E521F0DCF03423AE FOREIGN KEY (mailchimp_audience_id) REFERENCES VSUS_MailchimpAudiences (id)');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id maintenance_page_id  INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id ) REFERENCES VSCMS_Pages (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id )');
        $this->addSql('ALTER TABLE VSPAY_Order CHANGE status status ENUM(\'shopping_cart\', \'paid_order\', \'failed_order\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE VSUS_NewsletterSubscriptions DROP FOREIGN KEY FK_E521F0DCA76ED395');
        $this->addSql('ALTER TABLE VSUS_NewsletterSubscriptions DROP FOREIGN KEY FK_E521F0DCF03423AE');
        $this->addSql('DROP TABLE VSUS_MailchimpAudiences');
        $this->addSql('DROP TABLE VSUS_NewsletterSubscriptions');
        $this->addSql('ALTER TABLE VSAPP_Settings DROP FOREIGN KEY FK_4A491FD507FAB6A');
        $this->addSql('DROP INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings');
        $this->addSql('ALTER TABLE VSAPP_Settings CHANGE maintenance_page_id  maintenance_page_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE VSAPP_Settings ADD CONSTRAINT FK_4A491FD507FAB6A FOREIGN KEY (maintenance_page_id) REFERENCES VSCMS_Pages (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4A491FD507FAB6A ON VSAPP_Settings (maintenance_page_id)');
        $this->addSql('ALTER TABLE VSPAY_Order CHANGE status status VARCHAR(255) DEFAULT NULL');
    }
}
