<?php

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'getair_db');
define('DB_USER', 'root');
define('DB_PASS', '');   // <-- change to your MySQL/phpMyAdmin password

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . htmlspecialchars($e->getMessage()) .
        "<br>Check config/db.php and confirm the 'getair_db' database exists in phpMyAdmin.");
}
