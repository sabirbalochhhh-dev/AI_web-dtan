-- HealthCare Pharmacy Database Schema
-- Created for PHP/MySQL website
-- Charset: utf8mb4

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP DATABASE IF EXISTS healthcare_pharmacy;
CREATE DATABASE healthcare_pharmacy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE healthcare_pharmacy;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category_id INT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT NOT NULL,
    availability TINYINT(1) DEFAULT 1,
    status TINYINT(1) DEFAULT 1,
    image VARCHAR(255) DEFAULT 'default.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_medicines_category
        FOREIGN KEY (category_id) REFERENCES categories(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE pharmacists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    specialty VARCHAR(150) NOT NULL,
    bio TEXT NOT NULL,
    photo VARCHAR(255) DEFAULT 'default.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) DEFAULT 'default.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(50) DEFAULT '',
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Sample admin user (password: admin123)
INSERT INTO users (username, password_hash) VALUES
('admin', '$2y$10$2vTQ5ZVBv3rZE6iFQS7zQefwA7w6d8A5M4T2WjQ1K2Ih7h7l9A8oK');

-- Sample categories
INSERT INTO categories (name) VALUES
('Pain Relief'),
('Cold & Flu'),
('Vitamins'),
('Digestive Health');

-- Sample medicines
INSERT INTO medicines (name, category_id, price, description, availability, status, image) VALUES
('Paracetamol 500mg', 1, 12.99, 'Fast pain relief for headaches and fever.', 1, 1, 'paracetamol.jpg'),
('Cough Syrup', 2, 8.50, 'Relieves chest congestion and dry cough.', 1, 1, 'cough-syrup.jpg'),
('Vitamin C Plus', 3, 15.75, 'Immune support with vitamin C and zinc.', 1, 1, 'vitamin-c.jpg'),
('Antacid Tablets', 4, 6.20, 'Effective relief for acid reflux and indigestion.', 1, 1, 'antacid.jpg');

-- Sample pharmacists
INSERT INTO pharmacists (name, specialty, bio, photo) VALUES
('Dr. Ayesha Khan', 'Clinical Pharmacist', 'Specializes in patient counseling and medication safety.', 'ayesha.jpg'),
('Dr. Mark Wilson', 'Community Pharmacy', 'Focused on chronic medication management and wellness support.', 'mark.jpg'),
('Dr. Sara Ahmed', 'Oncology Pharmacy', 'Expert in safe oncology drug counseling and support.', 'sara.jpg');

-- Sample gallery items
INSERT INTO gallery (title, description, image) VALUES
('Storefront', 'Modern pharmacy interior with friendly service.', 'storefront.jpg'),
('Consultation Room', 'Private consultation space for patient care.', 'consultation.jpg'),
('Medicine Shelf', 'Wide selection of certified medicines and supplements.', 'shelf.jpg');

-- Sample contact messages
INSERT INTO contact_messages (name, email, phone, message) VALUES
('John Doe', 'john@example.com', '+1 555 111 2222', 'I would like to know about prescription refill options.'),
('Maria Gomez', 'maria@example.com', '+1 555 333 4444', 'Can you share your store timings for this weekend?');

SET FOREIGN_KEY_CHECKS = 1;
