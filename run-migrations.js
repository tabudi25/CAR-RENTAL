#!/usr/bin/env node

const { runMigrations } = require("./db/migrate");

console.log("🚀 Starting Car Rental Database Migrations...\n");

runMigrations()
  .then(() => {
    console.log("\n✅ All migrations completed successfully!");
    console.log("🎉 Your car rental database is ready to use!");
    process.exit(0);
  })
  .catch((error) => {
    console.error("\n❌ Migration failed:", error.message);
    process.exit(1);
  });
