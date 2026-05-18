-- Zionite Charity Database Schema
-- MySQL Database Schema for Humanitarian Charity Website

-- Create Database
CREATE DATABASE IF NOT EXISTS zionite_charity;
USE zionite_charity;

-- Table: Admin Users
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    status ENUM('active', 'inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Volunteers
CREATE TABLE IF NOT EXISTS volunteers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(50),
    country VARCHAR(50),
    skills TEXT,
    availability TEXT,
    motivation TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Donations
CREATE TABLE IF NOT EXISTS donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    amount DECIMAL(10, 2) NOT NULL,
    donation_type ENUM('one-time', 'monthly') DEFAULT 'one-time',
    payment_method ENUM('credit-card', 'paypal', 'bank-transfer', 'cash') DEFAULT 'credit-card',
    purpose TEXT,
    is_anonymous TINYINT(1) DEFAULT 0,
    message TEXT,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Projects
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(50),
    image VARCHAR(255),
    location VARCHAR(100),
    start_date DATE,
    end_date DATE,
    budget DECIMAL(10, 2),
    status ENUM('ongoing', 'completed', 'upcoming') DEFAULT 'ongoing',
    featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Reports
CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    report_type ENUM('annual', 'quarterly', 'project', 'financial') DEFAULT 'annual',
    year INT,
    quarter INT,
    file_path VARCHAR(255),
    file_size DECIMAL(10, 2),
    download_count INT DEFAULT 0,
    published_date DATE,
    status ENUM('published', 'draft') DEFAULT 'published',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Partners
CREATE TABLE IF NOT EXISTS partners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    logo VARCHAR(255),
    website VARCHAR(255),
    description TEXT,
    partnership_type ENUM('sponsor', 'collaborator', 'donor', 'media') DEFAULT 'collaborator',
    status ENUM('active', 'inactive') DEFAULT 'active',
    contact_person VARCHAR(100),
    contact_email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Contact Messages
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status ENUM('unread', 'read', 'replied') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Services
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    icon VARCHAR(50),
    image VARCHAR(255),
    order_index INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Testimonials
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(100),
    image VARCHAR(255),
    testimonial TEXT NOT NULL,
    rating INT DEFAULT 5,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Default Admin User (Password: admin123 - hashed)
INSERT INTO admin_users (username, password, email, full_name, status) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@zionitecharity.org', 'Administrator', 'active');

-- Insert Default Services
INSERT INTO services (title, description, icon, order_index) VALUES
('Emotional Support', 'Providing counseling and emotional support to those suffering silently in our communities.', 'heart', 1),
('Food Assistance', 'Distributing food packages and meals to families and individuals in need.', 'utensils', 2),
('Clothing Support', 'Collecting and distributing clothing to help those with limited resources.', 'tshirt', 3),
('Hospital Visits', 'Regular visits to hospitals to bring comfort and support to patients.', 'hospital', 4),
('Orphanage Support', 'Supporting orphanages with supplies, education, and care for children.', 'child', 5),
('Elderly Care', 'Providing companionship and assistance to elderly residents in care homes.', 'user', 6),
('Prayer Support', 'Offering spiritual support and prayer for those in need of comfort.', 'pray', 7),
('Humanitarian Activities', 'Organizing and participating in various humanitarian aid activities.', 'hands-helping', 8);

-- Insert Default Testimonials
INSERT INTO testimonials (name, role, testimonial, rating) VALUES
('Sarah Johnson', 'Beneficiary', 'Zionite Charity helped me when I was at my lowest. Their emotional support program gave me hope and strength to move forward.', 5),
('Michael Thompson', 'Volunteer', 'Volunteering with Zionite Charity has been a life-changing experience. The impact we make in people\'s lives is immeasurable.', 5),
('Emily Davis', 'Donor', 'I\'ve been supporting Zionite Charity for years. Their transparency and dedication to helping those in need is truly inspiring.', 5);

-- Insert Sample Projects
INSERT INTO projects (title, description, category, location, start_date, end_date, budget, status, featured) VALUES
('Community Food Drive', 'Monthly food distribution program serving over 500 families in need across the city.', 'Food Assistance', 'Multiple Locations', '2024-01-01', '2024-12-31', 50000.00, 'ongoing', 1),
('Hospital Visit Program', 'Weekly visits to local hospitals bringing comfort, gifts, and support to patients.', 'Hospital Visits', 'City Hospital', '2024-02-01', '2024-12-31', 25000.00, 'ongoing', 1),
('Orphanage Renovation', 'Complete renovation of the Sunshine Orphanage including new facilities and equipment.', 'Orphanage Support', 'Sunshine Orphanage', '2024-03-01', '2024-08-31', 75000.00, 'ongoing', 0),
('Elderly Christmas Party', 'Annual Christmas celebration for elderly residents with gifts, meals, and entertainment.', 'Elderly Care', 'Elderly Home', '2024-12-20', '2024-12-25', 15000.00, 'upcoming', 0);

-- Insert Sample Partners
INSERT INTO partners (name, website, description, partnership_type, status) VALUES
('Local Food Bank', 'https://localfoodbank.org', 'Partner organization providing food supplies for our distribution programs.', 'collaborator', 'active'),
('City Hospital', 'https://cityhospital.org', 'Medical facility partnership for hospital visit programs.', 'collaborator', 'active'),
('TechCorp Foundation', 'https://techcorpfoundation.org', 'Major sponsor providing financial support for our projects.', 'sponsor', 'active'),
('MediaOne News', 'https://mediaone.news', 'Media partner helping spread awareness about our activities.', 'media', 'active');
