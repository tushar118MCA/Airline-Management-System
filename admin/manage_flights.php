<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
require_admin();
$in_admin = true;
$page_title = 'Manage Flights';

$flights = $pdo->query(
    "SELECT f.*, a.name AS airline_name FROM flights f
     LEFT JOIN airlines a ON a.airline_id = f.airline_id
     ORDER BY f.flight_date, f.flight_time"
)->fetchAll();

$success = flash('success');
include __DIR__ . '/../includes/header.php';
?>
<div class="container">
  <h1><center>Manage Flights </center></h1>
  
  <div class="admin-shell">
    <?php include __DIR__ . '/_nav.php'; ?>
    <center> <p class="muted">Signed in as <?= clean($_SESSION['full_name']) ?></p> </center>
    <div>
      <?php if ($success): ?><div class="alert alert-success"><?= clean($success) ?></div><?php endif; ?>
      <div style="margin-bottom:18px;"><a href="flight_form.php" class="btn btn-primary"> Add Flight</a></div>

      <table class="data-table">
        <thead>
          <center > <tr><th>Flight No</th><th>Airline</th><th>Route</th><th>Date</th><th>Time</th><th>Seats</th><th>Fares</th><th>Actions</th></tr> </center>
        </thead>
        <tbody>
          <?php foreach ($flights as $f):
            $c = $pdo->prepare("SELECT class_type, fare FROM classes WHERE flight_id = ? ORDER BY fare");
            $c->execute([$f['flight_id']]);
            $classes = $c->fetchAll();
          ?>
          <tr>
            <td><?= clean($f['flight_no']) ?></td>
            <td><?= clean($f['airline_name'] ?? '—') ?></td>
            <td><?= clean($f['source']) ?> → <?= clean($f['destination']) ?></td>
            <td><?= date('d M Y', strtotime($f['flight_date'])) ?></td>
            <td><?= date('h:i A', strtotime($f['flight_time'])) ?></td>
            <td><?= (int)$f['total_seats'] ?></td>
            <td style="font-size:.8rem;">
              <?php foreach ($classes as $cl): ?>
                <div><?= clean(ucfirst($cl['class_type'])) ?>: ₹<?= number_format($cl['fare'],2) ?></div>
              <?php endforeach; ?>
            </td>
            <td style="display:flex; gap:12px;">
              <a href="flight_form.php?id=<?= (int)$f['flight_id'] ?>" class="btn btn-outline btn-sm">Edit</a>
              <form method="post" action="delete_flight.php" onsubmit="return confirm('Delete flight <?= clean($f['flight_no']) ?> and all its bookings data?');">
                <input type="hidden" name="flight_id" value="<?= (int)$f['flight_id'] ?>">
                <button class="btn btn-danger btn-sm" type="submit">Delete</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (!$flights): ?>
            <tr><td colspan="8" class="muted">No flights yet. Add your first flight.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
