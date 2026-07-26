<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['user_id'] ?? 0);
    $stmt = $pdo->prepare("SELECT user_type FROM users WHERE user_id = ?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() === 'customer') {
        $pdo->prepare("DELETE FROM users WHERE user_id = ?")->execute([$id]);
        flash('Success', 'User Deleted.');
    }
}
header('Location: manage_users.php');
exit;
