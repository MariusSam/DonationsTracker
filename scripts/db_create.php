<?php

$pdo = new PDO('sqlite:../data/database.db');

// Create charities table with soft delete
$pdo->exec("CREATE TABLE IF NOT EXISTS charities (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL,
    deleted_at TEXT DEFAULT NULL
)");

// Create donations table with soft delete
$pdo->exec("CREATE TABLE IF NOT EXISTS donations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    donor_name TEXT NOT NULL,
    amount REAL NOT NULL CHECK(amount > 0),
    charity_id INTEGER NOT NULL,
    donated_at TEXT NOT NULL,
    FOREIGN KEY(charity_id) REFERENCES charities(id)
)");

echo "Database setup complete.\n";

// Call the seed_database.php script
require_once 'db_seed.php';
