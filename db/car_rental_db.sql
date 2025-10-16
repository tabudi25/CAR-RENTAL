-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS car_rental_db;
USE car_rental_db;

-- Drop tables if they exist to avoid errors
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS cars;
DROP TABLE IF EXISTS users;

-- Database structure for car rental website

-- Users table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  user_type ENUM('customer', 'staff', 'admin') NOT NULL,
  name VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add admin user
INSERT INTO users (email, password, user_type, name) VALUES
('admin@ajiscarrental.com', '$2b$10$iLEz3tkVKeOCwuagcqhBUOIoNMZW0Bj4bjMgr1o9FL4oAVCkEGEFW', 'admin', 'System Administrator');

-- Cars table
CREATE TABLE IF NOT EXISTS cars (
  id INT AUTO_INCREMENT PRIMARY KEY,
  model VARCHAR(100) NOT NULL,
  brand VARCHAR(100) NOT NULL,
  year INT NOT NULL,
  price_per_day DECIMAL(10,2) NOT NULL,
  image_url VARCHAR(255),
  available BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bookings table
CREATE TABLE IF NOT EXISTS bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  car_id INT NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  total_price DECIMAL(10,2) NOT NULL,
  status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample car data
INSERT INTO cars (model, brand, year, price_per_day, image_url, available) VALUES
('5 Series', 'BMW', 2024, 150.00, '2024-BMW-5-Series-i5-for-China-1.jpg', TRUE),
('Urvan', 'Nissan', 2023, 100.00, 'Nissan-Urvan-CP063_02-scaled.webp', TRUE),
('Vios', 'Toyota', 2023, 80.00, 'Toyota-Vios-1-https___bmwjoyfest.vn_.jpg', TRUE),
('Montero', 'Mitsubishi', 2022, 120.00, 'mitsubishi-montero-2027.jpg', TRUE);