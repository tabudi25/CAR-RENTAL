-- Migration: Add rental history tracking
-- Created: 2024

-- Rental history table to track completed rentals
CREATE TABLE IF NOT EXISTS rental_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    car_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    actual_return_date DATE,
    total_days INT NOT NULL,
    daily_rate DECIMAL(10,2) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('completed', 'overdue', 'damaged') DEFAULT 'completed',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
);

-- Add payment status to bookings
ALTER TABLE bookings ADD COLUMN payment_status ENUM('pending', 'paid', 'refunded') DEFAULT 'pending';
ALTER TABLE bookings ADD COLUMN payment_method VARCHAR(50);
ALTER TABLE bookings ADD COLUMN payment_reference VARCHAR(100);

-- Add indexes for rental history
CREATE INDEX idx_rental_history_user_id ON rental_history(user_id);
CREATE INDEX idx_rental_history_car_id ON rental_history(car_id);
CREATE INDEX idx_rental_history_dates ON rental_history(start_date, end_date);
CREATE INDEX idx_rental_history_status ON rental_history(status);
