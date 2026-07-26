<?php
require_once __DIR__ . '/functions.php';
$base = isset($in_admin) && $in_admin ? '' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($page_title) ? clean($page_title) . ' · GetAir' : 'GetAir' ?></title>
<link rel="stylesheet" href="<?= isset($in_admin) && $in_admin ? '../css/style.css' : 'css/style.css' ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
</head>
<body>
<header class="site-header">
  <div class="nav-wrap">
    <a href="<?= isset($in_admin) && $in_admin ? '../index.php' : 'index.php' ?>" class="brand">Get<span>Air</span></a>
    <nav class="main-nav">
      <?php $root = isset($in_admin) && $in_admin ? '../' : ''; ?>
      <a href="<?= $root ?>index.php">Home</a>
      <?php if (is_admin()): ?>
        <a href="<?= isset($in_admin) && $in_admin ? 'dashboard.php' : 'admin/dashboard.php' ?>">Admin Panel</a>
      <?php elseif (is_logged_in()): ?>
        <a href="<?= $root ?>dashboard.php">Dashboard</a>
      <?php else: ?>
        <a href="<?= $root ?>book_tickets.php">Book Tickets</a>
        <a href="<?= $root ?>check_pnr.php">Check PNR</a>
      <?php endif; ?>
      <a href="<?= $root ?>about.php">About Us</a>
      <?php if (is_logged_in()): ?>
        <a href="<?= is_admin() ? (isset($in_admin) && $in_admin ? 'logout.php' : 'admin/logout.php') : $root . 'logout.php' ?>">Logout</a>
      <?php else: ?>
        <a href="<?= $root ?>login.php">Login</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main>
