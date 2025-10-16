// // Database connection and operations
// const mysql = require('mysql');
// const config = require('./config');

// // Create connection
// const db = mysql.createConnection(config);

// // Connect to MySQL
// db.connect(err => {
//   if (err) {
//     console.error('Error connecting to database:', err);
//     return;
//   }
//   console.log('MySQL connected successfully');
// });

// // Create database tables if they don't exist
// function initDatabase() {
//   // Users table
//   db.query(`
//     CREATE TABLE IF NOT EXISTS users (
//       id INT AUTO_INCREMENT PRIMARY KEY,
//       email VARCHAR(100) UNIQUE NOT NULL,
//       password VARCHAR(255) NOT NULL,
//       user_type ENUM('customer', 'staff', 'admin') NOT NULL,
//       name VARCHAR(100),
//       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
//     )
//   `);

//   // Cars table
//   db.query(`
//     CREATE TABLE IF NOT EXISTS cars (
//       id INT AUTO_INCREMENT PRIMARY KEY,
//       model VARCHAR(100) NOT NULL,
//       brand VARCHAR(100) NOT NULL,
//       year INT NOT NULL,
//       price_per_day DECIMAL(10,2) NOT NULL,
//       image_url VARCHAR(255),
//       available BOOLEAN DEFAULT TRUE
//     )
//   `);

//   // Bookings table
//   db.query(`
//     CREATE TABLE IF NOT EXISTS bookings (
//       id INT AUTO_INCREMENT PRIMARY KEY,
//       user_id INT NOT NULL,
//       car_id INT NOT NULL,
//       start_date DATE NOT NULL,
//       end_date DATE NOT NULL,
//       total_price DECIMAL(10,2) NOT NULL,
//       status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
//       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//       FOREIGN KEY (user_id) REFERENCES users(id),
//       FOREIGN KEY (car_id) REFERENCES cars(id)
//     )
//   `);
// }

// // User operations
// const userOperations = {
//   // Create a new user
//   createUser: (userData, callback) => {
//     db.query('INSERT INTO users SET ?', userData, callback);
//   },
  
//   // Find user by email
//   findUserByEmail: (email, callback) => {
//     db.query('SELECT * FROM users WHERE email = ?', [email], callback);
//   },
  
//   // Login user
//   loginUser: (email, password, callback) => {
//     db.query('SELECT * FROM users WHERE email = ? AND password = ?', [email, password], callback);
//   }
// };

// // Car operations
// const carOperations = {
//   // Get all cars
//   getAllCars: (callback) => {
//     db.query('SELECT * FROM cars', callback);
//   },
  
//   // Get available cars
//   getAvailableCars: (callback) => {
//     db.query('SELECT * FROM cars WHERE available = TRUE', callback);
//   }
// };

// // Booking operations
// const bookingOperations = {
//   // Create a new booking
//   createBooking: (bookingData, callback) => {
//     db.query('INSERT INTO bookings SET ?', bookingData, callback);
//   },
  
//   // Get user bookings
//   getUserBookings: (userId, callback) => {
//     db.query(`
//       SELECT b.*, c.model, c.brand, c.image_url 
//       FROM bookings b
//       JOIN cars c ON b.car_id = c.id
//       WHERE b.user_id = ?
//       ORDER BY b.created_at DESC
//     `, [userId], callback);
//   }
// };

// // Initialize database
// initDatabase();

// module.exports = {
//   db,
//   userOperations,
//   carOperations,
//   bookingOperations
// };