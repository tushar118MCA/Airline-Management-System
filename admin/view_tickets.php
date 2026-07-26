<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
require_admin();
$in_admin = true;
$page_title = 'Tickets & Passengers';

$filter = clean($_GET['status'] ?? 'ALL');
$sql = "SELECT t.*, f.flight_no, f.source, f.destination, f.flight_date, u.username
        FROM tickets t
        JOIN flights f ON f.flight_id = t.flight_id
        JOIN users u ON u.user_id = t.user_id";
$params = [];
if (in_array($filter, ['CONFIRMED', 'CANCELLED'])) {
    $sql .= " WHERE t.status = ?";
    $params[] = $filter;
}
$sql .= " ORDER BY t.booking_datetime DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tickets = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="container">
  <h1><center> Tickets &amp; Passengers</center> </h1>
  <div class="admin-shell">
    <?php include __DIR__ . '/_nav.php'; ?>
    <div>
      <div style="margin-bottom:16px; display:flex; gap:10px;">
        <a href="view_tickets.php?status=ALL" class="btn btn-sm <?= $filter==='ALL'?'btn-primary':'btn-outline' ?>">All</a>
        <a href="view_tickets.php?status=CONFIRMED" class="btn btn-sm <?= $filter==='CONFIRMED'?'btn-primary':'btn-outline' ?>">Confirmed</a>
        <a href="view_tickets.php?status=CANCELLED" class="btn btn-sm <?= $filter==='CANCELLED'?'btn-primary':'btn-outline' ?>">Cancelled</a>
      </div>

      <table class="data-table">
        <thead>
          <tr><th>PNR</th><th>Passenger</th><th>Age/Gender</th><th>Booked By</th><th>Route</th><th>Flight</th><th>Class</th><th>Fare</th><th>Status</th></tr>
        </thead>
        <tbody>
          <?php foreach ($tickets as $t): ?>
          <tr>
            <td><a href="../ticket.php?pnr=<?= urlencode($t['pnr']) ?>" target="_blank"><?= clean($t['pnr']) ?></a></td>
            <td><?= clean($t['passenger_name']) ?></td>
            <td><?= (int)$t['age'] ?> / <?= clean($t['gender']) ?></td>
            <td><?= clean($t['username']) ?></td>
            <td><?= clean($t['source']) ?> → <?= clean($t['destination']) ?></td>
            <td><?= clean($t['flight_no']) ?></td>
            <td><?= clean(ucfirst($t['class_type'])) ?></td>
            <td>Rs.<?= number_format($t['fare_amount'], 2) ?></td>
            <td><span class="pill <?= $t['status'] === 'CONFIRMED' ? 'pill-confirmed' : 'pill-cancelled' ?>"><?= clean($t['status']) ?></span></td>
          </tr>
          <?php endforeach; ?>
          <?php if (!$tickets): ?>
            <tr><td colspan="9" class="muted">No tickets found Till Yet</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
