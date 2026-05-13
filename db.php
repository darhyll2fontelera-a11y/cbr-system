<?php
$conn = new mysqli("localhost", "root", "", "blood_donation_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Safely add user_id column if it doesn't exist yet
$cols = [];
$r = $conn->query("SHOW COLUMNS FROM donations");
if ($r) { while ($c = $r->fetch_assoc()) $cols[] = $c['Field']; }

if (!in_array('user_id', $cols)) {
    $conn->query("ALTER TABLE donations ADD COLUMN user_id INT DEFAULT NULL");
}
if (!in_array('type', $cols)) {
    $conn->query("ALTER TABLE donations ADD COLUMN type VARCHAR(20) DEFAULT 'donation'");
}
?>