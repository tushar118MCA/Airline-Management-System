<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';
$page_title = 'Cancel Booked Ticket';

$pnr = clean($_GET['pnr'] ?? ($_POST['pnr'] ?? ''));
$ticket = null;
$message = null;
$messageType = 'error';

if ($pnr !== '') {
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE pnr = ?");
    $stmt->execute([$pnr]);
    $ticket = $stmt->fetch();
    if (!$ticket) {
        $message = "No ticket found for PNR \"$pnr\".";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_cancel']) && $ticket) {
    if ($ticket['status'] !== 'CONFIRMED') {
        $message = 'This ticket is already cancelled.';
    } elseif (!is_cancellable($ticket['booking_datetime'], $ticket['status'])) {
        $message = 'This ticket can no longer be cancelled — the 24-hour free cancellation window has passed.';
    } else {
        $pdo->prepare("UPDATE tickets SET status = 'CANCELLED', cancelled_at = NOW() WHERE ticket_id = ?")
            ->execute([$ticket['ticket_id']]);
        $pdo->prepare("UPDATE payments SET status = 'REFUNDED' WHERE ticket_id = ?")
            ->execute([$ticket['ticket_id']]);
        $message = "Ticket $pnr has been cancelled and the fare will be refunded.";
        $messageType = 'success';
        $stmt = $pdo->prepare("SELECT * FROM tickets WHERE pnr = ?");
        $stmt->execute([$pnr]);
        $ticket = $stmt->fetch();
    }
}

include __DIR__ . '/includes/header.php';
?>
<div class="container narrow">
  <div class="card">
    <center> <h1>Cancel Booked Tickets</h1> </center>
    <center><p class="muted">Free cancellation is available within 24 hours of booking.</p></center>

    <form method="get" action="cancel_ticket.php" class="stack" style="margin-bottom: 10px;">
      <div class="field">
        <label>Enter the PNR</label>
        <input type="text" name="pnr" required value="<?= clean($pnr) ?>" placeholder="e.g. 1374614">
      </div>
      <button class="btn btn-primary" type="submit">Look Up Ticket</button>
    </form>

    <?php if ($message): ?>
      <div class="alert alert-<?= $messageType ?>"><?= clean($message) ?></div>
    <?php endif; ?>

    <?php if ($ticket): ?>
      <hr class="divider-line">
      <div class="flight-meta" style="margin-bottom:14px;">
        <span>PNR: <b><?= clean($ticket['pnr']) ?></b></span>
        <span>Passenger: <b><?= clean($ticket['passenger_name']) ?></b></span>
        <span>Booked: <b><?= date('d M Y, h:i A', strtotime($ticket['booking_datetime'])) ?></b></span>
        <span>Status:
          <span class="pill <?= $ticket['status'] === 'CONFIRMED' ? 'pill-confirmed' : 'pill-cancelled' ?>"><?= clean($ticket['status']) ?></span>
        </span>
      </div>

      <?php if ($ticket['status'] === 'CONFIRMED'): ?>
        <?php if (is_cancellable($ticket['booking_datetime'], $ticket['status'])): ?>
          <div class="badge-24h">⏱ <?= hours_left_to_cancel($ticket['booking_datetime']) ?> hour(s) left to cancel free of charge</div>
          <form method="post" style="margin-top:16px;">
            <input type="hidden" name="pnr" value="<?= clean($ticket['pnr']) ?>">
            <button class="btn btn-danger" type="submit" name="confirm_cancel" value="1"
              onclick="return confirm('Cancel ticket <?= clean($ticket['pnr']) ?>? This cannot be undone.');">
              Cancel Ticket
            </button>
          </form>
        <?php else: ?>
          <div class="alert alert-error">The 24-hour free cancellation window has passed for this ticket.</div>
        <?php endif; ?>
      <?php endif; ?>

      <p style="margin-top:16px;"><a href="ticket.php?pnr=<?= urlencode($ticket['pnr']) ?>">View Full Ticket &amp; Boarding Pass</a></p>
    <?php endif; ?>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
