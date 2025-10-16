const mysql = require("mysql2/promise");
const fs = require("fs");
const path = require("path");

// Database configuration
const dbConfig = {
  host: "localhost",
  user: "root",
  password: "",
  database: "car_rental_db",
  multipleStatements: true,
};

async function runMigrations() {
  let connection;

  try {
    console.log("🔄 Starting database migrations...");

    // Connect to database
    connection = await mysql.createConnection(dbConfig);
    console.log("✅ Connected to MySQL database");

    // Get migration files
    const migrationsDir = path.join(__dirname, "migrations");
    const migrationFiles = fs
      .readdirSync(migrationsDir)
      .filter((file) => file.endsWith(".sql"))
      .sort();

    console.log(`📁 Found ${migrationFiles.length} migration files`);

    // Create migrations tracking table
    await connection.execute(`
      CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        filename VARCHAR(255) NOT NULL,
        executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      )
    `);

    // Get already executed migrations
    const [executedMigrations] = await connection.execute("SELECT filename FROM migrations ORDER BY id");
    const executedFiles = executedMigrations.map((row) => row.filename);

    // Run pending migrations
    for (const file of migrationFiles) {
      if (!executedFiles.includes(file)) {
        console.log(`🔄 Running migration: ${file}`);

        const migrationSQL = fs.readFileSync(path.join(migrationsDir, file), "utf8");

        // Split SQL statements and execute them one by one
        const statements = migrationSQL
          .split(";")
          .map((stmt) => stmt.trim())
          .filter((stmt) => stmt.length > 0);

        for (const statement of statements) {
          if (statement.trim()) {
            await connection.execute(statement);
          }
        }

        // Record migration as executed
        await connection.execute("INSERT INTO migrations (filename) VALUES (?)", [file]);

        console.log(`✅ Migration ${file} completed`);
      } else {
        console.log(`⏭️  Migration ${file} already executed`);
      }
    }

    console.log("🎉 All migrations completed successfully!");
  } catch (error) {
    console.error("❌ Migration failed:", error.message);
    process.exit(1);
  } finally {
    if (connection) {
      await connection.end();
      console.log("🔌 Database connection closed");
    }
  }
}

// Run migrations if this file is executed directly
if (require.main === module) {
  runMigrations();
}

module.exports = { runMigrations };
