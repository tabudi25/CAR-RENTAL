// // Simple Express server to handle database operations
// const express = require('express');
// const bodyParser = require('body-parser');
// const cors = require('cors');
// const bcrypt = require('bcrypt');
// const { userOperations, carOperations, bookingOperations } = require('./db/database');

// const app = express();
// const PORT = 8080;

// // Middleware
// app.use(cors());
// app.use(bodyParser.json());
// app.use(express.static('.'));

// // User routes
// app.post('/api/register', (req, res) => {
//   const { email, password, userType, name } = req.body;
  
//   userOperations.findUserByEmail(email, (err, results) => {
//     if (err) {
//       return res.status(500).json({ error: 'Database error' });
//     }
    
//     if (results.length > 0) {
//       return res.status(400).json({ error: 'Email already exists' });
//     }
    
//     // Hash password before storing
//     bcrypt.hash(password, 10, (err, hashedPassword) => {
//       if (err) {
//         return res.status(500).json({ error: 'Password encryption failed' });
//       }
      
//       const userData = {
//         email,
//         password: hashedPassword,
//         user_type: userType,
//         name: name || email.split('@')[0]
//       };
      
//       userOperations.createUser(userData, (err, result) => {
//         if (err) {
//           console.error('Registration error:', err);
//           return res.status(500).json({ error: 'Failed to register user' });
//         }
        
//         res.status(201).json({ 
//           message: 'User registered successfully',
//           userId: result.insertId,
//           userType
//         });
//       });
//     });
//   });
// });

// app.post('/api/login', (req, res) => {
//   const { email, password } = req.body;
  
//   userOperations.findUserByEmail(email, (err, results) => {
//     if (err) {
//       return res.status(500).json({ error: 'Database error' });
//     }
    
//     if (results.length === 0) {
//       return res.status(401).json({ error: 'Invalid credentials' });
//     }
    
//     const user = results[0];
    
//     // Compare password with hashed password in database
//     bcrypt.compare(password, user.password, (err, isMatch) => {
//       if (err) {
//         return res.status(500).json({ error: 'Authentication error' });
//       }
      
//       if (!isMatch) {
//         return res.status(401).json({ error: 'Invalid credentials' });
//       }
      
//       res.json({
//         message: 'Login successful',
//         userId: user.id,
//         userType: user.user_type
//       });
//     });
//   });
// });

// // Car routes
// app.get('/api/cars', (req, res) => {
//   carOperations.getAllCars((err, results) => {
//     if (err) {
//       return res.status(500).json({ error: 'Database error' });
//     }
    
//     res.json(results);
//   });
// });

// app.get('/api/cars/available', (req, res) => {
//   carOperations.getAvailableCars((err, results) => {
//     if (err) {
//       return res.status(500).json({ error: 'Database error' });
//     }
    
//     res.json(results);
//   });
// });

// // Booking routes
// app.post('/api/bookings', (req, res) => {
//   const { userId, carId, startDate, endDate, totalPrice } = req.body;
  
//   const bookingData = {
//     user_id: userId,
//     car_id: carId,
//     start_date: startDate,
//     end_date: endDate,
//     total_price: totalPrice
//   };
  
//   bookingOperations.createBooking(bookingData, (err, result) => {
//     if (err) {
//       return res.status(500).json({ error: 'Failed to create booking' });
//     }
    
//     res.status(201).json({
//       message: 'Booking created successfully',
//       bookingId: result.insertId
//     });
//   });
// });

// app.get('/api/bookings/user/:userId', (req, res) => {
//   const userId = req.params.userId;
  
//   bookingOperations.getUserBookings(userId, (err, results) => {
//     if (err) {
//       return res.status(500).json({ error: 'Database error' });
//     }
    
//     res.json(results);
//   });
// });

// // Start server
// app.listen(PORT, () => {
//   console.log(`Server running on http://localhost:${PORT}`);
// });