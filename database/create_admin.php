<?php

require_once __DIR__ . '/../config/db.php';

$hash = password_hash('admin123', PASSWORD_BCRYPT);

$stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = 'admin'");
$stmt->execute();

if ($stmt->fetch()) {
    $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'")->execute([$hash]);
    echo "Admin password reset to 'admin123'.\n";
} else {
    $pdo->prepare(
        "INSERT INTO users (username, password, full_name, email, address, contact_no, user_type)
         VALUES ('admin', ?, 'System Administrator', 'admin@getair.com', 'GetAir HQ', '9999999999', 'admin')"
    )->execute([$hash]);
    echo "Admin user created. username=admin password=admin123\n";
}
