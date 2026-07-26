<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';
$page_title = 'My Dashboard';
require_login();

$stmt = $pdo->prepare(
    "SELECT t.*, f.flight_no, f.source, f.destination, f.flight_date, f.flight_time
     FROM tickets t JOIN flights f ON f.flight_id = t.flight_id
     WHERE t.user_id = ? ORDER BY t.booking_datetime DESC"
);
$stmt->execute([$_SESSION['user_id']]);
$tickets = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>
<div class="container">
  <center><h1>Welcome back, <?= clean($_SESSION['full_name']) ?></h1></center>
  <center><p class="muted">Here are all the tickets booked under your account.</p></center>

  <div style="margin-bottom:24px;">
    <a href="book_tickets.php" class="btn btn-primary">Book a new flight</a>
  </div>

  <?php if (!$tickets): ?>
    <div class="alert alert-info">You haven't booked any tickets yet.</div>
  <?php else: ?>
    <table class="data-table">
      <thead>
        <tr>
          <th>PNR</th><th>Route</th><th>Flight</th><th>Date</th><th>Class</th>
          <th>Passengers</th><th>Fare</th><th>Status</th> <th> Actions</th><th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($tickets as $t): ?>
        <tr>
          <td><?= clean($t['pnr']) ?></td>
          <td><?= clean($t['source']) ?> → <?= clean($t['destination']) ?></td>
          <td><?= clean($t['flight_no']) ?></td>
          <td><?= date('d M Y', strtotime($t['flight_date'])) ?></td>
          <td><?= clean(ucfirst($t['class_type'])) ?></td>
          <td><?= (int)$t['no_of_passengers'] ?></td>
          <td>Rs <?= number_format($t['fare_amount'], 2) ?></td>
          <td><span class="pill <?= $t['status'] === 'CONFIRMED' ? 'pill-confirmed' : 'pill-cancelled' ?>"><?= clean($t['status']) ?></span></td>
          <td style="display:flex; gap:12px;">
            <a href="ticket.php?pnr=<?= urlencode($t['pnr']) ?>" class="btn btn-outline btn-sm">View</a>
            <?php if (is_cancellable($t['booking_datetime'], $t['status'])): ?>
              <a href="cancel_ticket.php?pnr=<?= urlencode($t['pnr']) ?>" class="btn btn-danger btn-sm">Cancel</a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
