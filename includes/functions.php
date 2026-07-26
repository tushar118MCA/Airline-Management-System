<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function clean($v) {
    return htmlspecialchars(trim($v ?? ''), ENT_QUOTES, 'UTF-8');
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return is_logged_in() && $_SESSION['user_type'] === 'admin';
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function require_admin() {
    if (!is_admin()) {
        // Called from files inside /admin, so this stays relative to that folder
        header('Location: login.php');
        exit;
    }
}

function generate_pnr($pdo) {
    do {
        $pnr = (string) random_int(1000000, 9999999);
        $stmt = $pdo->prepare("SELECT 1 FROM tickets WHERE pnr = ?");
        $stmt->execute([$pnr]);
    } while ($stmt->fetch());
    return $pnr;
}

function generate_transaction_id($pdo) {
    do {
        $txn = (string) random_int(100000000, 999999999);
        $stmt = $pdo->prepare("SELECT 1 FROM payments WHERE transaction_id = ?");
        $stmt->execute([$txn]);
    } while ($stmt->fetch());
    return $txn;
}

function flash($key, $msg = null) {
    if ($msg !== null) {
        $_SESSION['flash'][$key] = $msg;
        return;
    }
    if (!empty($_SESSION['flash'][$key])) {
        $out = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $out;
    }
    return null;
}

/** Can this ticket still be cancelled? (must be CONFIRMED and booked within the last 24h) */
function is_cancellable($booking_datetime, $status) {
    if ($status !== 'CONFIRMED') return false;
    $booked = strtotime($booking_datetime);
    return (time() - $booked) <= (24 * 60 * 60);
}

function hours_left_to_cancel($booking_datetime) {
    $booked = strtotime($booking_datetime);
    $remaining = (24 * 60 * 60) - (time() - $booked);
    if ($remaining <= 0) return 0;
    return round($remaining / 3600, 1);
}
