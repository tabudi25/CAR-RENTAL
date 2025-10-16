-- Insert admin user (ignore if exists)
INSERT IGNORE INTO users (email, password, user_type, name) VALUES 
('admin@ajis.com', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Admin User');

-- Insert sample cars using existing schema
INSERT IGNORE INTO cars (model, brand, year, price_per_day, image_url, available) VALUES 
('i5', 'BMW', 2024, 5500.00, '2024-BMW-5-Series-i5-for-China-1.jpg', 1),
('Montero', 'Mitsubishi', 2027, 4200.00, 'mitsubishi-montero-2027.jpg', 1),
('Urvan', 'Nissan', 2023, 6200.00, 'Nissan-Urvan-CP063_02-scaled.webp', 1),
('Vios', 'Toyota', 2023, 1400.00, 'Toyota-Vios-1-https___bmwjoyfest.vn_.jpg', 1);

-- Insert sample customers (ignore if exists)
INSERT IGNORE INTO users (email, password, user_type, name) VALUES 
('john@example.com', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 'John Doe'),
('jane@example.com', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 'Jane Smith');

-- Insert sample staff (ignore if exists)
INSERT IGNORE INTO users (email, password, user_type, name) VALUES 
('staff@ajis.com', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff', 'Staff Member');
