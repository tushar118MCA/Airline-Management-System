<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';
$page_title = 'Passenger Details';

// Preserve the chosen flight/class across a login redirect
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['flight_id'], $_POST['class_type'])) {
    $_SESSION['selection'] = [
        'flight_id'  => (int)$_POST['flight_id'],
        'class_type' => clean($_POST['class_type']),
    ];
}

if (!is_logged_in()) {
    flash('info', 'Please log in to continue booking your ticket.');
    header('Location: login.php');
    exit;
}
if (empty($_SESSION['selection'])) {
    header('Location: book_tickets.php');
    exit;
}

$sel = $_SESSION['selection'];
$stmt = $pdo->prepare("SELECT f.*, a.name AS airline_name FROM flights f
                        LEFT JOIN airlines a ON a.airline_id = f.airline_id WHERE f.flight_id = ?");
$stmt->execute([$sel['flight_id']]);
$flight = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM classes WHERE flight_id = ? AND class_type = ?");
$stmt->execute([$sel['flight_id'], $sel['class_type']]);
$class = $stmt->fetch();

if (!$flight || !$class) {
    header('Location: book_tickets.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['passenger_name'])) {
    $passenger_name   = clean($_POST['passenger_name']);
    $age              = (int)($_POST['age'] ?? 0);
    $gender           = clean($_POST['gender'] ?? '');
    $no_of_passengers = max(1, (int)($_POST['no_of_passengers'] ?? 1));
    $meal_choice      = isset($_POST['meal_choice']);
    $lounge_access    = isset($_POST['lounge_access']);
    $priority_checkin = isset($_POST['priority_checkin']);
    $insurance        = isset($_POST['insurance']);

    if ($passenger_name === '' || $age <= 0 || $gender === '') {
        $errors[] = 'Please complete all required passenger fields.';
    }

    if (!$errors) {
        $fare = $class['fare'] * $no_of_passengers;
        if ($lounge_access)    $fare += 25;
        if ($priority_checkin) $fare += 15;
        if ($insurance)        $fare += 20;

        $_SESSION['pending_booking'] = [
            'flight_id'        => $flight['flight_id'],
            'class_type'       => $class['class_type'],
            'passenger_name'   => $passenger_name,
            'age'              => $age,
            'gender'           => $gender,
            'no_of_passengers' => $no_of_passengers,
            'meal_choice'      => $meal_choice,
            'lounge_access'    => $lounge_access,
            'priority_checkin' => $priority_checkin,
            'insurance'        => $insurance,
            'fare_amount'      => $fare,
        ];
        header('Location: payment.php');
        exit;
    }
}

include __DIR__ . '/includes/header.php';
?>
<div class="container narrow">
  <div class="card" style="margin-bottom:20px;">
    <h3 class="mb-0">Flight Summary</h3>
    <div class="flight-meta" style="margin-top:10px;">
      <span><?= clean($flight['source']) ?> → <?= clean($flight['destination']) ?></span>
      <span>Flight: <b><?= clean($flight['flight_no']) ?></b></span>
      <span>Date: <b><?= date('d M Y', strtotime($flight['flight_date'])) ?></b></span>
      <span>Class: <b><?= clean(ucfirst($class['class_type'])) ?></b></span>
      <span>Fare: <b>Rs <?= number_format($class['fare'], 2) ?></b> / passenger</span>
    </div>
  </div>

  <div class="card">
    <h1>Passenger Details</h1>
    <?php foreach ($errors as $e): ?><div class="alert alert-error"><?= clean($e) ?></div><?php endforeach; ?>

    <form method="post" class="stack">
      <div class="row-2">
        <div class="field">
          <label>Passenger Name *</label>
          <input type="text" name="passenger_name" required value="<?= clean($_SESSION['full_name'] ?? '') ?>">
        </div>
        <div class="field">
          <label>Age *</label>
          <input type="number" name="age" min="1" max="120" required>
        </div>
      </div>
      <div class="row-2">
        <div class="field">
          <label>Gender *</label>
          <select name="gender" required>
            <option value="">Select</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
          </select>
        </div>
        <div class="field">
          <label>No. of Passengers *</label>
          <input type="number" name="no_of_passengers" min="1" max="9" value="1" required>
        </div>
      </div>

      <div class="field">
        <label>Add-ons</label>
        <div class="checkbox-list">
          <label><input type="checkbox" name="meal_choice" value="1"> Meal Choice</label>
          <label><input type="checkbox" name="lounge_access" value="1"> Lounge Access (Rs 25)</label>
          <label><input type="checkbox" name="priority_checkin" value="1"> Priority Check-in (Rs 15)</label>
          <label><input type="checkbox" name="insurance" value="1"> Travel Insurance (Rs 20)</label>
        </div>
      </div>

      <button class="btn btn-primary" type="submit">Continue to Payment</button>
    </form>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
