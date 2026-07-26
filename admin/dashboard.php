<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
require_admin();
$in_admin = true;
$page_title = 'Admin Overview';

$flightCount   = $pdo->query("SELECT COUNT(*) c FROM flights")->fetch()['c'];
$userCount     = $pdo->query("SELECT COUNT(*) c FROM users WHERE user_type = 'customer'")->fetch()['c'];
$ticketCount   = $pdo->query("SELECT COUNT(*) c FROM tickets WHERE status = 'CONFIRMED'")->fetch()['c'];
$revenue       = $pdo->query("SELECT COALESCE(SUM(amount),0) s FROM payments WHERE status = 'SUCCESS'")->fetch()['s'];
$recentTickets = $pdo->query(
    "SELECT t.pnr, t.passenger_name, t.status, t.booking_datetime, f.source, f.destination, f.flight_no
     FROM tickets t JOIN flights f ON f.flight_id = t.flight_id
     ORDER BY t.booking_datetime DESC LIMIT 8"
)->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="container">
  <h1><center>Admin Panel</center></h1>
  <div class="admin-shell">
    <?php include __DIR__ . '/_nav.php'; ?>
    <div>
      <div class="grid-3" style="margin-bottom:24px;">
        <div class="stat-card"><div class="num"><?= (int)$flightCount ?></div><div class="lbl">Active Flights</div></div>
        <div class="stat-card"><div class="num"><?= (int)$userCount ?></div><div class="lbl">Registered Customers</div></div>
        <div class="stat-card"><div class="num"><?= (int)$ticketCount ?></div><div class="lbl">Confirmed Tickets</div></div>
      </div>
      <div class="stat-card" style="margin-bottom:24px;">
        <div class="lbl">Total Revenue Collect</div>
        <div class="num" style="color:var(--success);"> ₹ <?= number_format($revenue, 2) ?></div>
      </div>

      <h3>Recent Bookings</h3>
      <table class="data-table">
        <thead><tr><th>PNR</th><th>Passenger</th><th>Route</th><th>Flight</th><th>Booked</th><th>Status</th></tr></thead>
        <tbody>
          <?php foreach ($recentTickets as $t): ?>
          <tr>
            <td><?= clean($t['pnr']) ?></td>
            <td><?= clean($t['passenger_name']) ?></td>
            <td><?= clean($t['source']) ?> → <?= clean($t['destination']) ?></td>
            <td><?= clean($t['flight_no']) ?></td>
            <td><?= date('d M Y, h:i A', strtotime($t['booking_datetime'])) ?></td>
            <td><span class="pill <?= $t['status'] === 'CONFIRMED' ? 'pill-confirmed' : 'pill-cancelled' ?>"><?= clean($t['status']) ?></span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
