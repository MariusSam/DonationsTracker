<?php

$pdo = new PDO('sqlite:../data/database.db');

// Check if the charities table is empty
$charityCount = $pdo->query("SELECT COUNT(*) FROM charities WHERE deleted_at IS NULL")->fetchColumn();

if ($charityCount == 0) {
    echo "Seeding charities table...\n";

    $pdo->exec("INSERT INTO charities (name, email) VALUES
        ('Feeding America', 'feeding@america.com'),
        ('Good 360', 'good@360.com'),
        ('Direct Relief', 'direct@relief.com'),
        ('Salvation Army', 'salvation@army.com.com'),
        ('United Way Worldwide', 'united@way.com')
    ");

    echo "Charities table seeded.\n";
} else {
    echo "Charities table already has data.\n";
}

// Check if the donations table is empty
$donationCount = $pdo->query("SELECT COUNT(*) FROM donations")->fetchColumn();

if ($donationCount == 0) {
    echo "Seeding donations table...\n";

    $pdo->exec("INSERT INTO donations (donor_name, amount, charity_id, donated_at) VALUES
        ('Unicef', 10000.00, 1, '2024-08-24 10:00:00'),
        ('Global Partnership for Education', 5000.00, 2, '2024-08-25 11:00:00'),
        ('USAID', 75000.50, 1, '2024-08-24 12:00:00'),
        ('AJA Foundation', 8000.50, 3, '2024-08-23 12:00:00'),
        ('The OPEC Fund', 7500, 4, '2024-08-25 12:00:00')
    ");
    
    echo "Donations table seeded.\n";
} else {
    echo "Donations table already has data.\n";
}