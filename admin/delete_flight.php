<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['flight_id'] ?? 0);
    $pdo->prepare("DELETE FROM flights WHERE flight_id = ?")->execute([$id]);
    flash('success', 'Flight deleted.');
}
header('Location: manage_flights.php');
exit;
