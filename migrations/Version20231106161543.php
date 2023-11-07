<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231106161543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE etudiant_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE instructeur_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reservation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reset_password_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE etudiant (id INT NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL, phone VARCHAR(15) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE instructeur (id INT NOT NULL, nom VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL, phone VARCHAR(15) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE reservation (id INT NOT NULL, etudiant_id INT DEFAULT NULL, instructeurs_id INT DEFAULT NULL, date_exam TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, amount NUMERIC(10, 2) DEFAULT NULL, transaction_state VARCHAR(50) DEFAULT NULL, type_permis VARCHAR(10) NOT NULL, hours INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_42C84955DDEAB1A3 ON reservation (etudiant_id)');
        $this->addSql('CREATE INDEX IDX_42C849559E88F929 ON reservation (instructeurs_id)');
        $this->addSql('CREATE TABLE reset_password_request (id INT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)');
        $this->addSql('COMMENT ON COLUMN reset_password_request.requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reset_password_request.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, etudiant_id INT DEFAULT NULL, instructeur_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649DDEAB1A3 ON "user" (etudiant_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64925FCA809 ON "user" (instructeur_id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849559E88F929 FOREIGN KEY (instructeurs_id) REFERENCES instructeur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D64925FCA809 FOREIGN KEY (instructeur_id) REFERENCES instructeur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE etudiant_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE instructeur_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reservation_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reset_password_request_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C84955DDEAB1A3');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C849559E88F929');
        $this->addSql('ALTER TABLE reset_password_request DROP CONSTRAINT FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649DDEAB1A3');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D64925FCA809');
        $this->addSql('DROP TABLE etudiant');
        $this->addSql('DROP TABLE instructeur');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE "user"');
    }
}
