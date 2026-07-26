<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';
$page_title = 'Ticket Details';

$pnr = clean($_GET['pnr'] ?? '');
$stmt = $pdo->prepare(
    "SELECT t.*, f.flight_no, f.flight_name, f.source, f.destination, f.flight_date, f.flight_time,
            a.name AS airline_name, p.transaction_id, p.payment_mode, u.username
     FROM tickets t
     JOIN flights f ON f.flight_id = t.flight_id
     LEFT JOIN airlines a ON a.airline_id = f.airline_id
     LEFT JOIN payments p ON p.ticket_id = t.ticket_id
     JOIN users u ON u.user_id = t.user_id
     WHERE t.pnr = ?"
);
$stmt->execute([$pnr]);
$t = $stmt->fetch();

include __DIR__ . '/includes/header.php';
?>
<div class="container narrow">
  <?php if (!$t): ?>
    <div class="alert alert-error">No ticket found for PNR "<?= clean($pnr) ?>".</div>
    <a href="check_pnr.php" class="btn btn-outline">Check another PNR</a>
  <?php else: ?>

    <div class="text-center no-print" style="margin-bottom:24px;">
      <h1>GetAir Ticket Details</h1>
      
    </div>

    <div class="card" style="margin-bottom:24px;">
      <center>  <h3 class="mb-0">Ticket Details</h3></center>
      <div class="flight-meta" style="margin-top:14px;">
        <span>PNR: <b><?= clean($t['pnr']) ?></b></span>
        <span>Flight No: <b><?= clean($t['flight_no']) ?></b></span>
        <span>Date of Journey: <b><?= date('Y-m-d', strtotime($t['flight_date'])) ?></b></span>
        <span>Class: <b><?= clean(ucfirst($t['class_type'])) ?></b></span>
        <span>Payment ID: <b><?= clean($t['transaction_id'] ?? '—') ?></b></span>
        <span>Lounge Access: <b><?= $t['lounge_access'] ? 'yes' : 'no' ?></b></span>
        <span>Priority Checkin: <b><?= $t['priority_checkin'] ? 'yes' : 'no' ?></b></span>
        <span>Insurance: <b><?= $t['insurance'] ? 'yes' : 'no' ?></b></span>
        <span>Booked By: <b><?= clean($t['username']) ?></b></span>
        <span>No. of Passengers: <b><?= (int)$t['no_of_passengers'] ?></b></span>
        <span>Status:
          <span class="pill <?= $t['status'] === 'CONFIRMED' ? 'pill-confirmed' : 'pill-cancelled' ?>"><?= clean($t['status']) ?></span>
        </span>
      </div>
    </div>

    <h3 class="text-center">Online Boarding Pass</h3>
    <div class="boarding-pass">
      <div class="bp-main">
        <div class="bp-eyebrow">Boarding Pass</div>
        <div class="bp-airline">Get<span>Air</span> Airline</div>
        <div class="bp-grid">
          <div class="bp-field"><div class="lbl">PNR</div><div class="val"><?= clean($t['pnr']) ?></div></div>
          <div class="bp-field"><div class="lbl">Passenger No.</div><div class="val">1</div></div>
          <div class="bp-field"><div class="lbl">Name</div><div class="val"><?= clean($t['passenger_name']) ?></div></div>
          <div class="bp-field"><div class="lbl">Gender</div><div class="val"><?= clean($t['gender']) ?></div></div>
          <div class="bp-field"><div class="lbl">Meal Choice</div><div class="val"><?= $t['meal_choice'] ? 'yes' : 'no' ?></div></div>
          <div class="bp-field"><div class="lbl">Age</div><div class="val"><?= (int)$t['age'] ?></div></div>
        </div>
      </div>
      <div class="notch top"></div>
      <div class="tear"></div>
      <div class="notch bottom"></div>
      <div class="bp-stub">
        <div>
          <div class="lbl">Route</div>
          <div class="val"><?= clean($t['source']) ?> ✈ <?= clean($t['destination']) ?></div>
        </div>
        <div>
          <div class="lbl">Flight</div>
          <div class="val"><?= clean($t['flight_no']) ?></div>
        </div>
        <div>
          <div class="lbl">Date / Time</div>
          <div class="val"><?= date('d M Y', strtotime($t['flight_date'])) ?><br><?= date('h:i A', strtotime($t['flight_time'])) ?></div>
        </div>
      </div>
    </div>

    <div class="text-center no-print" style="display:flex; gap:12px; justify-content:center; margin-top:10px;">
      <button class="btn btn-primary" onclick="window.print()">Print</button>
      <a href="dashboard.php" class="btn btn-outline">My Bookings</a>
      <?php if (is_cancellable($t['booking_datetime'], $t['status'])): ?>
        <a href="cancel_ticket.php?pnr=<?= urlencode($t['pnr']) ?>" class="btn btn-danger">Cancel Ticket</a>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
