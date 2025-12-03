<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251126203220 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE categories_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE payments_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE properties_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reservations_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reviews_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "users_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE categories (id INT NOT NULL, name VARCHAR(100) NOT NULL, slug VARCHAR(100) NOT NULL, description TEXT DEFAULT NULL, icon VARCHAR(50) DEFAULT NULL, color VARCHAR(7) DEFAULT NULL, is_active BOOLEAN NOT NULL, sort_order INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3AF34668989D9B62 ON categories (slug)');
        $this->addSql('CREATE TABLE payments (id INT NOT NULL, user_id INT NOT NULL, reservation_id INT NOT NULL, transaction_id VARCHAR(100) NOT NULL, amount NUMERIC(10, 2) NOT NULL, currency VARCHAR(3) NOT NULL, status VARCHAR(20) NOT NULL, method VARCHAR(50) NOT NULL, description TEXT DEFAULT NULL, card_last_four VARCHAR(4) DEFAULT NULL, card_brand VARCHAR(50) DEFAULT NULL, billing_name VARCHAR(255) DEFAULT NULL, billing_email VARCHAR(255) DEFAULT NULL, billing_address TEXT DEFAULT NULL, refunded_amount NUMERIC(10, 2) DEFAULT NULL, refunded_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, refund_reason TEXT DEFAULT NULL, metadata JSON DEFAULT NULL, failure_reason TEXT DEFAULT NULL, gateway_reference VARCHAR(50) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, paid_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_65D29B322FC0CB0F ON payments (transaction_id)');
        $this->addSql('CREATE INDEX IDX_65D29B32A76ED395 ON payments (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_65D29B32B83297E7 ON payments (reservation_id)');
        $this->addSql('CREATE TABLE properties (id INT NOT NULL, owner_id INT DEFAULT NULL, category_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, price NUMERIC(10, 2) NOT NULL, address VARCHAR(255) NOT NULL, city VARCHAR(100) NOT NULL, country VARCHAR(100) DEFAULT NULL, postal_code VARCHAR(20) DEFAULT NULL, latitude NUMERIC(10, 7) DEFAULT NULL, longitude NUMERIC(10, 7) DEFAULT NULL, type VARCHAR(50) NOT NULL, bedrooms INT NOT NULL, bathrooms INT NOT NULL, capacity INT NOT NULL, surface NUMERIC(8, 2) DEFAULT NULL, main_image VARCHAR(255) DEFAULT NULL, images JSON NOT NULL, amenities JSON NOT NULL, status VARCHAR(20) NOT NULL, is_available BOOLEAN NOT NULL, is_featured BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_87C331C77E3C61F9 ON properties (owner_id)');
        $this->addSql('CREATE INDEX IDX_87C331C712469DE2 ON properties (category_id)');
        $this->addSql('CREATE TABLE reservations (id INT NOT NULL, user_id INT NOT NULL, property_id INT NOT NULL, reference VARCHAR(50) NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, guests INT NOT NULL, message TEXT DEFAULT NULL, special_requests TEXT DEFAULT NULL, status VARCHAR(20) NOT NULL, total_price NUMERIC(10, 2) NOT NULL, price_per_night NUMERIC(10, 2) NOT NULL, nights INT NOT NULL, service_fee NUMERIC(10, 2) DEFAULT NULL, cleaning_fee NUMERIC(10, 2) DEFAULT NULL, cancellation_reason TEXT DEFAULT NULL, cancelled_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, confirmed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4DA239AEA34913 ON reservations (reference)');
        $this->addSql('CREATE INDEX IDX_4DA239A76ED395 ON reservations (user_id)');
        $this->addSql('CREATE INDEX IDX_4DA239549213EC ON reservations (property_id)');
        $this->addSql('CREATE TABLE reviews (id INT NOT NULL, author_id INT NOT NULL, property_id INT NOT NULL, reservation_id INT DEFAULT NULL, rating INT NOT NULL, comment TEXT NOT NULL, title VARCHAR(255) DEFAULT NULL, cleanliness_rating INT DEFAULT NULL, communication_rating INT DEFAULT NULL, location_rating INT DEFAULT NULL, value_rating INT DEFAULT NULL, is_approved BOOLEAN NOT NULL, is_reported BOOLEAN NOT NULL, owner_response TEXT DEFAULT NULL, owner_response_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6970EB0FF675F31B ON reviews (author_id)');
        $this->addSql('CREATE INDEX IDX_6970EB0F549213EC ON reviews (property_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6970EB0FB83297E7 ON reviews (reservation_id)');
        $this->addSql('CREATE TABLE "users" (id INT NOT NULL, email VARCHAR(180) NOT NULL, name VARCHAR(100) DEFAULT NULL, username VARCHAR(100) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, phone VARCHAR(20) DEFAULT NULL, address TEXT DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, last_login_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_active BOOLEAN NOT NULL, is_verified BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON "users" (email)');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B32A76ED395 FOREIGN KEY (user_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B32B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE properties ADD CONSTRAINT FK_87C331C77E3C61F9 FOREIGN KEY (owner_id) REFERENCES "users" (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE properties ADD CONSTRAINT FK_87C331C712469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA239A76ED395 FOREIGN KEY (user_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA239549213EC FOREIGN KEY (property_id) REFERENCES properties (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0FF675F31B FOREIGN KEY (author_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0F549213EC FOREIGN KEY (property_id) REFERENCES properties (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0FB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE categories_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE payments_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE properties_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reservations_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reviews_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "users_id_seq" CASCADE');
        $this->addSql('ALTER TABLE payments DROP CONSTRAINT FK_65D29B32A76ED395');
        $this->addSql('ALTER TABLE payments DROP CONSTRAINT FK_65D29B32B83297E7');
        $this->addSql('ALTER TABLE properties DROP CONSTRAINT FK_87C331C77E3C61F9');
        $this->addSql('ALTER TABLE properties DROP CONSTRAINT FK_87C331C712469DE2');
        $this->addSql('ALTER TABLE reservations DROP CONSTRAINT FK_4DA239A76ED395');
        $this->addSql('ALTER TABLE reservations DROP CONSTRAINT FK_4DA239549213EC');
        $this->addSql('ALTER TABLE reviews DROP CONSTRAINT FK_6970EB0FF675F31B');
        $this->addSql('ALTER TABLE reviews DROP CONSTRAINT FK_6970EB0F549213EC');
        $this->addSql('ALTER TABLE reviews DROP CONSTRAINT FK_6970EB0FB83297E7');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE payments');
        $this->addSql('DROP TABLE properties');
        $this->addSql('DROP TABLE reservations');
        $this->addSql('DROP TABLE reviews');
        $this->addSql('DROP TABLE "users"');
    }
}
