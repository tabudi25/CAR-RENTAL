// API service for database operations
class CarRentalAPI {
  constructor() {
    this.baseURL = "http://localhost:8080/api";
  }

  async request(endpoint, options = {}) {
    const url = `${this.baseURL}${endpoint}`;
    const config = {
      headers: {
        "Content-Type": "application/json",
        ...options.headers,
      },
      ...options,
    };

    try {
      const response = await fetch(url, config);
      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.error || "Request failed");
      }

      return data;
    } catch (error) {
      console.error("API request failed:", error);
      throw error;
    }
  }

  // User operations
  async login(email, password) {
    return this.request("/login", {
      method: "POST",
      body: JSON.stringify({ email, password }),
    });
  }

  async register(userData) {
    return this.request("/register", {
      method: "POST",
      body: JSON.stringify(userData),
    });
  }

  // Car operations
  async getCars() {
    return this.request("/cars");
  }

  async getAvailableCars() {
    return this.request("/cars/available");
  }

  async searchCars(query) {
    return this.request(`/cars/search?q=${encodeURIComponent(query)}`);
  }

  async createCar(carData) {
    // Add authentication data
    const userId = localStorage.getItem("userId");
    const userType = localStorage.getItem("userType");

    if (!userId || !userType) {
      throw new Error("User not authenticated. Please log in first.");
    }

    const dataWithAuth = {
      ...carData,
      userId: parseInt(userId),
      userType,
    };

    console.log("API Request - Sending data:", dataWithAuth);
    console.log("JSON string:", JSON.stringify(dataWithAuth));

    return this.request("/cars", {
      method: "POST",
      body: JSON.stringify(dataWithAuth),
    });
  }

  async updateCar(id, carData) {
    return this.request(`/cars/${id}`, {
      method: "PUT",
      body: JSON.stringify(carData),
    });
  }

  async updateCarStatus(id, status) {
    return this.request(`/cars/${id}/status`, {
      method: "PUT",
      body: JSON.stringify({ status }),
    });
  }

  // Booking operations
  async getBookings() {
    return this.request("/bookings");
  }

  async getActiveBookings() {
    return this.request("/bookings/active");
  }

  async createBooking(bookingData) {
    return this.request("/bookings", {
      method: "POST",
      body: JSON.stringify(bookingData),
    });
  }

  async updateBooking(id, bookingData) {
    return this.request(`/bookings/${id}`, {
      method: "PUT",
      body: JSON.stringify(bookingData),
    });
  }

  // User management
  async getUsers() {
    return this.request("/users");
  }

  async getCustomers() {
    return this.request("/users/customers");
  }

  // Dashboard
  async getDashboardStats() {
    return this.request("/dashboard/stats");
  }
}

// Create global API instance
window.CarRentalAPI = new CarRentalAPI();

// Helper functions for backward compatibility with localStorage
window.DatabaseAPI = {
  // Cars
  async getCars() {
    try {
      return await window.CarRentalAPI.getCars();
    } catch (error) {
      console.error("Failed to fetch cars from database:", error);
      // Fallback to localStorage
      return JSON.parse(localStorage.getItem("cars") || "[]");
    }
  },

  async saveCars(cars) {
    try {
      // Update each car in database
      for (const car of cars) {
        if (car.id) {
          await window.CarRentalAPI.updateCar(car.id, car);
        } else {
          await window.CarRentalAPI.createCar(car);
        }
      }
    } catch (error) {
      console.error("Failed to save cars to database:", error);
      // Fallback to localStorage
      localStorage.setItem("cars", JSON.stringify(cars));
    }
  },

  // Bookings/Reservations
  async getReservations() {
    try {
      return await window.CarRentalAPI.getBookings();
    } catch (error) {
      console.error("Failed to fetch reservations from database:", error);
      // Fallback to localStorage
      return JSON.parse(localStorage.getItem("reservations") || "[]");
    }
  },

  async saveReservation(reservation) {
    try {
      await window.CarRentalAPI.createBooking(reservation);
    } catch (error) {
      console.error("Failed to save reservation to database:", error);
      // Fallback to localStorage
      const reservations = JSON.parse(localStorage.getItem("reservations") || "[]");
      reservations.push(reservation);
      localStorage.setItem("reservations", JSON.stringify(reservations));
    }
  },

  // Users
  async getUsers() {
    try {
      return await window.CarRentalAPI.getUsers();
    } catch (error) {
      console.error("Failed to fetch users from database:", error);
      // Fallback to localStorage
      return JSON.parse(localStorage.getItem("users") || "[]");
    }
  },

  // Dashboard stats
  async getDashboardStats() {
    try {
      return await window.CarRentalAPI.getDashboardStats();
    } catch (error) {
      console.error("Failed to fetch dashboard stats from database:", error);
      // Fallback to localStorage calculation
      const cars = JSON.parse(localStorage.getItem("cars") || "[]");
      const reservations = JSON.parse(localStorage.getItem("reservations") || "[]");

      return {
        cars: {
          total_cars: cars.length,
          available_cars: cars.filter((c) => c.status === "available").length,
          reserved_cars: cars.filter((c) => c.status === "reserved").length,
          rented_cars: cars.filter((c) => c.status === "rented").length,
        },
        bookings: {
          total_bookings: reservations.length,
          pending_bookings: reservations.filter((r) => r.status === "pending").length,
          confirmed_bookings: reservations.filter((r) => r.status === "confirmed").length,
          total_revenue: reservations.reduce((sum, r) => sum + (r.total_price || 0), 0),
        },
      };
    }
  },
};
