const { execute } = require("../database");

class Booking {
  static async create(bookingData) {
    const { user_id, car_id, start_date, end_date, total_price, status = "pending" } = bookingData;

    const query = `
      INSERT INTO bookings (user_id, car_id, start_date, end_date, total_price, status) 
      VALUES (?, ?, ?, ?, ?, ?)
    `;

    const [result] = await execute(query, [user_id, car_id, start_date, end_date, total_price, status]);
    return result.insertId;
  }

  static async getAll() {
    const query = `
      SELECT 
        b.*,
        u.name as customer_name,
        u.email as customer_email,
        u.phone as customer_phone,
        c.name as car_name,
        c.plate as car_plate,
        c.image as car_image
      FROM bookings b
      JOIN users u ON b.user_id = u.id
      JOIN cars c ON b.car_id = c.id
      ORDER BY b.created_at DESC
    `;

    const [rows] = await execute(query);
    return rows;
  }

  static async findById(id) {
    const query = `
      SELECT 
        b.*,
        u.name as customer_name,
        u.email as customer_email,
        u.phone as customer_phone,
        c.name as car_name,
        c.plate as car_plate,
        c.image as car_image
      FROM bookings b
      JOIN users u ON b.user_id = u.id
      JOIN cars c ON b.car_id = c.id
      WHERE b.id = ?
    `;

    const [rows] = await execute(query, [id]);
    return rows[0];
  }

  static async findByUserId(userId) {
    const query = `
      SELECT 
        b.*,
        c.name as car_name,
        c.plate as car_plate,
        c.image as car_image
      FROM bookings b
      JOIN cars c ON b.car_id = c.id
      WHERE b.user_id = ?
      ORDER BY b.created_at DESC
    `;

    const [rows] = await execute(query, [userId]);
    return rows;
  }

  static async findByStatus(status) {
    const query = `
      SELECT 
        b.*,
        u.name as customer_name,
        u.email as customer_email,
        u.phone as customer_phone,
        c.name as car_name,
        c.plate as car_plate,
        c.image as car_image
      FROM bookings b
      JOIN users u ON b.user_id = u.id
      JOIN cars c ON b.car_id = c.id
      WHERE b.status = ?
      ORDER BY b.created_at DESC
    `;

    const [rows] = await execute(query, [status]);
    return rows;
  }

  static async update(id, bookingData) {
    const { status, payment_status, payment_method, payment_reference } = bookingData;

    const query = `
      UPDATE bookings 
      SET status = ?, payment_status = ?, payment_method = ?, 
          payment_reference = ?, updated_at = CURRENT_TIMESTAMP 
      WHERE id = ?
    `;

    await execute(query, [status, payment_status, payment_method, payment_reference, id]);
  }

  static async updateStatus(id, status) {
    const query = "UPDATE bookings SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
    await execute(query, [status, id]);
  }

  static async delete(id) {
    const query = "DELETE FROM bookings WHERE id = ?";
    await execute(query, [id]);
  }

  static async getStats() {
    const query = `
      SELECT 
        COUNT(*) as total_bookings,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_bookings,
        SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_bookings,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_bookings,
        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_bookings,
        SUM(total_price) as total_revenue
      FROM bookings
    `;

    const [rows] = await execute(query);
    return rows[0];
  }

  static async getActiveRentals() {
    const query = `
      SELECT 
        b.*,
        u.name as customer_name,
        u.email as customer_email,
        u.phone as customer_phone,
        c.name as car_name,
        c.plate as car_plate,
        c.image as car_image
      FROM bookings b
      JOIN users u ON b.user_id = u.id
      JOIN cars c ON b.car_id = c.id
      WHERE b.status IN ('pending', 'confirmed')
      ORDER BY b.created_at DESC
    `;

    const [rows] = await execute(query);
    return rows;
  }
}

module.exports = Booking;
