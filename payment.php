<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';
$page_title = 'Payment';

require_login();
if (empty($_SESSION['pending_booking'])) {
    header('Location: book_tickets.php');
    exit;
}
$b = $_SESSION['pending_booking'];

$stmt = $pdo->prepare("SELECT * FROM flights WHERE flight_id = ?");
$stmt->execute([$b['flight_id']]);
$flight = $stmt->fetch();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_mode = clean($_POST['payment_mode'] ?? '');
    $card_number  = clean($_POST['card_number'] ?? '');

    if ($payment_mode === '') {
        $errors[] = 'Please select a payment mode.';
    }
    if (in_array($payment_mode, ['Credit Card', 'Debit Card']) && strlen(preg_replace('/\D/', '', $card_number)) < 12) {
        $errors[] = 'Please enter a valid card number.';
    }

    if (!$errors) {
        try {
            $pdo->beginTransaction();

            $pnr = generate_pnr($pdo);
            $stmt = $pdo->prepare(
                "INSERT INTO tickets (pnr, flight_id, user_id, class_type, passenger_name, age, gender,
                    meal_choice, lounge_access, priority_checkin, insurance, no_of_passengers, fare_amount, status)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?, 'CONFIRMED')"
            );
            $stmt->execute([
                $pnr, $b['flight_id'], $_SESSION['user_id'], $b['class_type'], $b['passenger_name'],
                $b['age'], $b['gender'], $b['meal_choice'], $b['lounge_access'], $b['priority_checkin'],
                $b['insurance'], $b['no_of_passengers'], $b['fare_amount']
            ]);
            $ticket_id = $pdo->lastInsertId();

            $txn = generate_transaction_id($pdo);
            $pdo->prepare(
                "INSERT INTO payments (ticket_id, transaction_id, amount, payment_mode, status)
                 VALUES (?,?,?,?, 'SUCCESS')"
            )->execute([$ticket_id, $txn, $b['fare_amount'], $payment_mode]);

            $pdo->commit();
            unset($_SESSION['pending_booking'], $_SESSION['selection']);

            header('Location: ticket.php?pnr=' . urlencode($pnr));
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Payment could not be processed. Please try again.';
        }
    }
}

include __DIR__ . '/includes/header.php';
?>
<div class="container narrow">
  <div class="card" style="margin-bottom:20px;">
    <center> <h3 class="mb-0">Amount Payable</h3> </center>
    <center> <p class="muted">PNR will be generated after payment is confirmed.</p> </center>
    <div class="flight-meta">
      <span><?= clean($flight['source']) ?> → <?= clean($flight['destination']) ?></span>
      <span>Passenger: <b><?= clean($b['passenger_name']) ?></b></span>
      <span>Class: <b><?= clean(ucfirst($b['class_type'])) ?></b></span>
      <span>Passengers: <b><?= (int)$b['no_of_passengers'] ?></b></span>
    </div>
    <h2 style="margin-top:16px; color:var(--sky-1);">Rs. <?= number_format($b['fare_amount'], 2) ?></h2> 
  </div>

  <div class="card">
    <h1>Payment Details</h1>
    <?php foreach ($errors as $e): ?><div class="alert alert-error"><?= clean($e) ?></div><?php endforeach; ?>

    <form method="post" class="stack">
      <div class="field">
        <label>Payment Mode *</label>
        <select name="payment_mode" required id="payMode">
          <option value="">Select</option>
          <option>Credit Card</option>
          <option>Debit Card</option>
          <option>UPI</option>
          <option>Net Banking</option>
        </select>
      </div>
      <div class="row-2">
        <div class="field">
          <label>Card / UPI Reference</label>
          <input type="text" name="card_number" placeholder="1234 4242 6546 4249">
        </div>
        <div class="field">
          <label>Expiry (MM/YY)</label>
          <input type="text" name="expiry" placeholder="12/30">
        </div>
      </div>
      <button class="btn btn-amber" type="submit">Rs. <?= number_format($b['fare_amount'], 2) ?></button>
    </form>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
