const { execute } = require("../database");

class User {
  static async create(userData) {
    const { name, email, password, phone, user_type = "customer" } = userData;

    const query = `
      INSERT INTO users (name, email, password, user_type) 
      VALUES (?, ?, ?, ?)
    `;

    const [result] = await execute(query, [name, email, password, user_type]);
    return result.insertId;
  }

  static async findByEmail(email) {
    const query = "SELECT * FROM users WHERE email = ?";
    const [rows] = await execute(query, [email]);
    return rows[0];
  }

  static async findById(id) {
    const query = "SELECT * FROM users WHERE id = ?";
    const [rows] = await execute(query, [id]);
    return rows[0];
  }

  static async getAll() {
    const query = "SELECT * FROM users ORDER BY created_at DESC";
    const [rows] = await execute(query);
    return rows;
  }

  static async getByType(userType) {
    const query = "SELECT * FROM users WHERE user_type = ? ORDER BY created_at DESC";
    const [rows] = await execute(query, [userType]);
    return rows;
  }

  static async update(id, userData) {
    const { name, email, phone, user_type } = userData;

    const query = `
      UPDATE users 
      SET name = ?, email = ?, phone = ?, user_type = ?, updated_at = CURRENT_TIMESTAMP 
      WHERE id = ?
    `;

    await execute(query, [name, email, phone, user_type, id]);
  }

  static async delete(id) {
    const query = "DELETE FROM users WHERE id = ?";
    await execute(query, [id]);
  }

  static async getCustomersWithBookings() {
    const query = `
      SELECT 
        u.id,
        u.name,
        u.email,
        u.phone,
        COUNT(b.id) as total_bookings,
        MAX(b.created_at) as last_booking
      FROM users u
      LEFT JOIN bookings b ON u.id = b.user_id
      WHERE u.user_type = 'customer'
      GROUP BY u.id, u.name, u.email, u.phone
      ORDER BY total_bookings DESC
    `;

    const [rows] = await execute(query);
    return rows;
  }
}

module.exports = User;
