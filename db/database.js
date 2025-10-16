const mysql = require("mysql2/promise");
const config = require("./config");

// Create connection pool
const pool = mysql.createPool({
  host: config.host,
  user: config.user,
  password: config.password,
  database: config.database,
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
});

// Test connection
async function testConnection() {
  try {
    const connection = await pool.getConnection();
    console.log("MySQL connected successfully");
    connection.release();
  } catch (error) {
    console.error("Error connecting to database:", error);
  }
}

// Execute query with error handling
async function execute(query, params = []) {
  try {
    const [rows] = await pool.execute(query, params);
    return [rows];
  } catch (error) {
    console.error("Database query error:", error);
    throw error;
  }
}

// Test connection on startup
testConnection();

module.exports = {
  execute,
  pool,
};
