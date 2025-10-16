const express = require("express");
const bodyParser = require("body-parser");
const cors = require("cors");
const bcrypt = require("bcrypt");
const path = require("path");
const { runMigrations } = require("./db/migrate");

// Import models
const User = require("./db/models/User");
const Car = require("./db/models/Car");
const Booking = require("./db/models/Booking");

const app = express();
const PORT = 8080;

// Middleware
app.use(cors());
app.use(bodyParser.json());
app.use(express.static("."));

// Run migrations on startup
runMigrations().catch(console.error);

// Authentication middleware
const authenticateUser = (req, res, next) => {
  const { userId, userType } = req.body;
  if (!userId || !userType) {
    return res.status(401).json({ error: "Authentication required" });
  }
  next();
};

// User routes
app.post("/api/register", async (req, res) => {
  try {
    const { email, password, userType, name, phone } = req.body;

    // Check if user already exists
    const existingUser = await User.findByEmail(email);
    if (existingUser) {
      return res.status(400).json({ error: "Email already exists" });
    }

    // Hash password
    const hashedPassword = await bcrypt.hash(password, 10);

    // Create user
    const userId = await User.create({
      name: name || email.split("@")[0],
      email,
      password: hashedPassword,
      phone,
      user_type: userType,
    });

    res.status(201).json({
      message: "User registered successfully",
      userId,
      userType,
    });
  } catch (error) {
    console.error("Registration error:", error);
    res.status(500).json({ error: "Failed to register user" });
  }
});

app.post("/api/login", async (req, res) => {
  try {
    console.log("Login request received:", req.body);
    const { email, password } = req.body;

    const user = await User.findByEmail(email);
    if (!user) {
      return res.status(401).json({ error: "Invalid credentials" });
    }

    const isValidPassword = await bcrypt.compare(password, user.password);
    if (!isValidPassword) {
      return res.status(401).json({ error: "Invalid credentials" });
    }

    res.json({
      message: "Login successful",
      userId: user.id,
      userType: user.user_type,
      userName: user.name,
    });
  } catch (error) {
    console.error("Login error:", error);
    res.status(500).json({ error: "Login failed" });
  }
});

// Car routes
app.get("/api/cars", async (req, res) => {
  try {
    const cars = await Car.getAll();
    res.json(cars);
  } catch (error) {
    console.error("Get cars error:", error);
    res.status(500).json({ error: "Failed to fetch cars" });
  }
});

app.get("/api/cars/available", async (req, res) => {
  try {
    const cars = await Car.getAvailable();
    res.json(cars);
  } catch (error) {
    console.error("Get available cars error:", error);
    res.status(500).json({ error: "Failed to fetch available cars" });
  }
});

app.get("/api/cars/search", async (req, res) => {
  try {
    const { q } = req.query;
    const cars = await Car.search(q);
    res.json(cars);
  } catch (error) {
    console.error("Search cars error:", error);
    res.status(500).json({ error: "Failed to search cars" });
  }
});

app.post("/api/cars", authenticateUser, async (req, res) => {
  try {
    console.log("Server received car data:", req.body);
    console.log("Request headers:", req.headers);
    const carData = req.body;
    const carId = await Car.create(carData);
    res.status(201).json({ message: "Car created successfully", carId });
  } catch (error) {
    console.error("Create car error:", error);
    res.status(500).json({ error: "Failed to create car" });
  }
});

app.put("/api/cars/:id", authenticateUser, async (req, res) => {
  try {
    const { id } = req.params;
    const carData = req.body;
    await Car.update(id, carData);
    res.json({ message: "Car updated successfully" });
  } catch (error) {
    console.error("Update car error:", error);
    res.status(500).json({ error: "Failed to update car" });
  }
});

app.put("/api/cars/:id/status", authenticateUser, async (req, res) => {
  try {
    const { id } = req.params;
    const { status } = req.body;
    await Car.updateStatus(id, status);
    res.json({ message: "Car status updated successfully" });
  } catch (error) {
    console.error("Update car status error:", error);
    res.status(500).json({ error: "Failed to update car status" });
  }
});

// Booking routes
app.get("/api/bookings", async (req, res) => {
  try {
    const bookings = await Booking.getAll();
    res.json(bookings);
  } catch (error) {
    console.error("Get bookings error:", error);
    res.status(500).json({ error: "Failed to fetch bookings" });
  }
});

app.get("/api/bookings/active", async (req, res) => {
  try {
    const bookings = await Booking.getActiveRentals();
    res.json(bookings);
  } catch (error) {
    console.error("Get active bookings error:", error);
    res.status(500).json({ error: "Failed to fetch active bookings" });
  }
});

app.post("/api/bookings", authenticateUser, async (req, res) => {
  try {
    const bookingData = req.body;
    const bookingId = await Booking.create(bookingData);
    res.status(201).json({ message: "Booking created successfully", bookingId });
  } catch (error) {
    console.error("Create booking error:", error);
    res.status(500).json({ error: "Failed to create booking" });
  }
});

app.put("/api/bookings/:id", authenticateUser, async (req, res) => {
  try {
    const { id } = req.params;
    const bookingData = req.body;
    await Booking.update(id, bookingData);
    res.json({ message: "Booking updated successfully" });
  } catch (error) {
    console.error("Update booking error:", error);
    res.status(500).json({ error: "Failed to update booking" });
  }
});

// User management routes
app.get("/api/users", authenticateUser, async (req, res) => {
  try {
    const users = await User.getAll();
    res.json(users);
  } catch (error) {
    console.error("Get users error:", error);
    res.status(500).json({ error: "Failed to fetch users" });
  }
});

app.get("/api/users/customers", authenticateUser, async (req, res) => {
  try {
    const customers = await User.getCustomersWithBookings();
    res.json(customers);
  } catch (error) {
    console.error("Get customers error:", error);
    res.status(500).json({ error: "Failed to fetch customers" });
  }
});

// Dashboard stats
app.get("/api/dashboard/stats", async (req, res) => {
  try {
    const [carStats, bookingStats] = await Promise.all([Car.getStats(), Booking.getStats()]);

    res.json({
      cars: carStats,
      bookings: bookingStats,
    });
  } catch (error) {
    console.error("Get dashboard stats error:", error);
    res.status(500).json({ error: "Failed to fetch dashboard stats" });
  }
});

// Start server
app.listen(PORT, () => {
  console.log(`Server running on http://localhost:${PORT}`);
  console.log("MySQL connected successfully");
});

module.exports = app;
