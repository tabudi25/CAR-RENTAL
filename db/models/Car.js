const { execute } = require("../database");

class Car {
  static async create(carData) {
    const { name, plate, type, category, seats, price, status = "available", image } = carData;

    // Use the new schema columns
    const query = `
      INSERT INTO cars (name, plate, type, category, seats, price, status, image, model, brand, year, price_per_day, image_url, available) 
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    `;

    const [result] = await execute(query, [
      name,
      plate,
      type,
      category,
      seats,
      price,
      status,
      image,
      name,
      name.split(" ")[0],
      new Date().getFullYear(),
      price,
      image,
      1,
    ]);
    return result.insertId;
  }

  static async getAll() {
    const query = "SELECT * FROM cars ORDER BY created_at DESC";
    const [rows] = await execute(query);
    return rows.map((car) => this.transformCar(car));
  }

  static async findById(id) {
    const query = "SELECT * FROM cars WHERE id = ?";
    const [rows] = await execute(query, [id]);
    return rows[0] ? this.transformCar(rows[0]) : null;
  }

  static async findByStatus(status) {
    const query = "SELECT * FROM cars WHERE available = ? ORDER BY created_at DESC";
    const available = status === "available" ? 1 : 0;
    const [rows] = await execute(query, [available]);
    return rows.map((car) => this.transformCar(car));
  }

  static async findByCategory(category) {
    const query = "SELECT * FROM cars WHERE brand = ? ORDER BY created_at DESC";
    const [rows] = await execute(query, [category]);
    return rows.map((car) => this.transformCar(car));
  }

  static async search(searchTerm) {
    const query = `
      SELECT * FROM cars 
      WHERE model LIKE ? OR brand LIKE ? 
      ORDER BY created_at DESC
    `;
    const searchPattern = `%${searchTerm}%`;
    const [rows] = await execute(query, [searchPattern, searchPattern]);
    return rows.map((car) => this.transformCar(car));
  }

  static async update(id, carData) {
    const { name, plate, type, category, seats, price, status, image } = carData;

    const query = `
      UPDATE cars 
      SET model = ?, brand = ?, price_per_day = ?, image_url = ?, available = ?
      WHERE id = ?
    `;

    await execute(query, [name, name.split(" ")[0], price, image, status === "available" ? 1 : 0, id]);
  }

  static async updateStatus(id, status) {
    const query = "UPDATE cars SET available = ? WHERE id = ?";
    const available = status === "available" ? 1 : 0;
    await execute(query, [available, id]);
  }

  static async delete(id) {
    const query = "DELETE FROM cars WHERE id = ?";
    await execute(query, [id]);
  }

  static async getAvailable() {
    const query = "SELECT * FROM cars WHERE available = 1 ORDER BY created_at DESC";
    const [rows] = await execute(query);
    return rows.map((car) => this.transformCar(car));
  }

  static async getStats() {
    const query = `
      SELECT 
        COUNT(*) as total_cars,
        SUM(CASE WHEN available = 1 THEN 1 ELSE 0 END) as available_cars,
        SUM(CASE WHEN available = 0 THEN 1 ELSE 0 END) as reserved_cars,
        0 as rented_cars
      FROM cars
    `;

    const [rows] = await execute(query);
    return rows[0];
  }

  // Transform database car to application format
  static transformCar(car) {
    return {
      id: car.id,
      name: car.model,
      plate: `${car.brand}-${car.id}`,
      type: "Automatic", // Default
      category: this.getCategoryFromBrand(car.brand),
      seats: this.getSeatsFromBrand(car.brand),
      price: parseFloat(car.price_per_day),
      status: car.available ? "available" : "reserved",
      image: car.image_url,
      created_at: car.created_at,
    };
  }

  static getCategoryFromBrand(brand) {
    const categories = {
      BMW: "luxury",
      Mitsubishi: "suv",
      Nissan: "van",
      Toyota: "sedan",
    };
    return categories[brand] || "sedan";
  }

  static getSeatsFromBrand(brand) {
    const seats = {
      BMW: 5,
      Mitsubishi: 5,
      Nissan: 12,
      Toyota: 4,
    };
    return seats[brand] || 4;
  }
}

module.exports = Car;
