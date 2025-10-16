const mysql = require("mysql2/promise");

const dbConfig = {
  host: "localhost",
  user: "root",
  password: "",
  database: "car_rental_db",
};

async function testDB() {
  let connection;

  try {
    connection = await mysql.createConnection(dbConfig);
    console.log("✅ Connected to database");

    // Check users table structure
    const [usersColumns] = await connection.execute("DESCRIBE users");
    console.log("Users table columns:", usersColumns);

    // Check cars table structure
    const [carsColumns] = await connection.execute("DESCRIBE cars");
    console.log("Cars table columns:", carsColumns);
  } catch (error) {
    console.error("Error:", error.message);
  } finally {
    if (connection) {
      await connection.end();
    }
  }
}

testDB();
