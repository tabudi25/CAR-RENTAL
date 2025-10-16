# AJIS Car Rental Management System

A comprehensive car rental management system with database integration, built with Node.js, Express, and MySQL.

## Features

- 🚗 **Car Management**: Add, edit, view, and manage rental cars
- 👥 **User Management**: Admin, staff, and customer user types
- 📋 **Booking System**: Complete reservation and rental tracking
- 📊 **Dashboard**: Real-time statistics and analytics
- 🔐 **Authentication**: Secure login system with password hashing
- 💾 **Database Integration**: MySQL database with migrations

## Prerequisites

- Node.js (v14 or higher)
- MySQL (v5.7 or higher)
- XAMPP (for local development)

## Installation

1. **Clone the repository**

   ```bash
   git clone <repository-url>
   cd CAR-RENTAL
   ```

2. **Install dependencies**

   ```bash
   npm install
   ```

3. **Setup MySQL Database**

   - Start XAMPP and ensure MySQL is running
   - Create a database named `car_rental_db`
   - Update database credentials in `db/config.js` if needed

4. **Run Database Migrations**

   ```bash
   npm run migrate
   ```

5. **Start the Server**

   ```bash
   npm start
   ```

6. **Access the Application**
   - Open `http://localhost:8080` in your browser
   - Login with admin credentials:
     - Email: `admin@ajis.com`
     - Password: `password`

## Database Schema

### Tables Created

1. **users** - User accounts (admin, staff, customers)
2. **cars** - Rental car inventory
3. **bookings** - Reservation and rental records
4. **rental_history** - Completed rental tracking
5. **migrations** - Migration tracking

### Sample Data

The migration includes:

- Admin user account
- Sample cars (BMW i5, Mitsubishi Montero, Nissan Urvan, Toyota Vios)
- Sample customers and staff accounts

## API Endpoints

### Authentication

- `POST /api/login` - User login
- `POST /api/register` - User registration

### Cars

- `GET /api/cars` - Get all cars
- `GET /api/cars/available` - Get available cars
- `GET /api/cars/search?q=query` - Search cars
- `POST /api/cars` - Create new car
- `PUT /api/cars/:id` - Update car
- `PUT /api/cars/:id/status` - Update car status

### Bookings

- `GET /api/bookings` - Get all bookings
- `GET /api/bookings/active` - Get active rentals
- `POST /api/bookings` - Create booking
- `PUT /api/bookings/:id` - Update booking

### Users

- `GET /api/users` - Get all users
- `GET /api/users/customers` - Get customers with booking stats

### Dashboard

- `GET /api/dashboard/stats` - Get dashboard statistics

## File Structure

```
CAR-RENTAL/
├── db/
│   ├── migrations/          # Database migration files
│   ├── models/             # Database models
│   ├── config.js           # Database configuration
│   ├── database.js         # Database connection
│   └── migrate.js          # Migration runner
├── js/
│   └── api.js              # Client-side API service
├── admin.html              # Admin dashboard
├── staff.html              # Staff dashboard
├── customer.html           # Customer interface
├── login.html              # Login page
├── server.js               # Express server
├── package.json            # Dependencies
└── README.md               # This file
```

## Usage

### Admin Dashboard

- Manage users, cars, and bookings
- View comprehensive statistics
- Monitor rental activities

### Staff Dashboard

- Manage vehicles and bookings
- View customer information
- Generate reports

### Customer Interface

- Browse available cars
- Make reservations
- View booking history

## Development

### Running Migrations

```bash
npm run migrate
```

### Development Mode

```bash
npm run dev
```

### Full Setup

```bash
npm run setup
```

## Database Migrations

The system includes a migration system to manage database schema changes:

1. **001_create_tables.sql** - Creates initial tables
2. **002_insert_sample_data.sql** - Inserts sample data
3. **003_add_rental_history.sql** - Adds rental history tracking

## Troubleshooting

### Common Issues

1. **Database Connection Error**

   - Ensure MySQL is running
   - Check database credentials in `db/config.js`
   - Verify database `car_rental_db` exists

2. **Migration Errors**

   - Check MySQL user permissions
   - Ensure database exists before running migrations

3. **Port Already in Use**
   - Change PORT in `server.js` if 8080 is occupied
   - Kill existing processes using the port

### Logs

Check console output for detailed error messages and migration status.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

MIT License - see LICENSE file for details.
