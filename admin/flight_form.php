<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
require_admin();
$in_admin = true;

$id = (int)($_GET['id'] ?? 0);
$editing = $id > 0;
$page_title = $editing ? 'Edit Flight' : 'Add Flight';

$airlines = $pdo->query("SELECT * FROM airlines ORDER BY name")->fetchAll();

$flight = ['flight_no'=>'','flight_name'=>'','source'=>'','destination'=>'','flight_date'=>'','flight_time'=>'','airline_id'=>'','total_seats'=>180];
$fares = ['economy'=>'', 'premium economy'=>'', 'business'=>''];

if ($editing) {
    $stmt = $pdo->prepare("SELECT * FROM flights WHERE flight_id = ?");
    $stmt->execute([$id]);
    $flight = $stmt->fetch();
    if (!$flight) { header('Location: manage_flights.php'); exit; }

    $stmt = $pdo->prepare("SELECT class_type, fare FROM classes WHERE flight_id = ?");
    $stmt->execute([$id]);
    foreach ($stmt->fetchAll() as $row) $fares[$row['class_type']] = $row['fare'];
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flight_no   = clean($_POST['flight_no']);
    $flight_name = clean($_POST['flight_name']);
    $source      = clean($_POST['source']);
    $destination = clean($_POST['destination']);
    $flight_date = clean($_POST['flight_date']);
    $flight_time = clean($_POST['flight_time']);
    $airline_id  = (int)$_POST['airline_id'];
    $total_seats = (int)$_POST['total_seats'];
    $fareInputs  = [
        'economy'          => (float)$_POST['fare_economy'],
        'premium economy'  => (float)$_POST['fare_premium'],
        'business'         => (float)$_POST['fare_business'],
    ];

    if (!$flight_no || !$source || !$destination || !$flight_date || !$flight_time) {
        $errors[] = 'Please complete all required flight fields.';
    }

    if (!$errors) {
        try {
            $pdo->beginTransaction();
            if ($editing) {
                $pdo->prepare(
                    "UPDATE flights SET flight_no=?, flight_name=?, source=?, destination=?,
                     flight_date=?, flight_time=?, airline_id=?, total_seats=? WHERE flight_id=?"
                )->execute([$flight_no,$flight_name,$source,$destination,$flight_date,$flight_time,$airline_id ?: null,$total_seats,$id]);
                $flight_id = $id;
            } else {
                $stmt = $pdo->prepare(
                    "INSERT INTO flights (flight_no, flight_name, source, destination, flight_date, flight_time, airline_id, total_seats)
                     VALUES (?,?,?,?,?,?,?,?)"
                );
                $stmt->execute([$flight_no,$flight_name,$source,$destination,$flight_date,$flight_time,$airline_id ?: null,$total_seats]);
                $flight_id = $pdo->lastInsertId();
            }

            foreach ($fareInputs as $type => $fare) {
                $stmt = $pdo->prepare("SELECT class_id FROM classes WHERE flight_id = ? AND class_type = ?");
                $stmt->execute([$flight_id, $type]);
                if ($cid = $stmt->fetchColumn()) {
                    $pdo->prepare("UPDATE classes SET fare = ? WHERE class_id = ?")->execute([$fare, $cid]);
                } else {
                    $pdo->prepare("INSERT INTO classes (flight_id, class_type, fare, seats_available) VALUES (?,?,?,?)")
                        ->execute([$flight_id, $type, $fare, (int)($total_seats/3)]);
                }
            }

            $pdo->commit();
            flash('success', $editing ? 'Flight updated.' : 'Flight added.');
            header('Location: manage_flights.php');
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Could not save flight: ' . $e->getMessage();
        }
    }
    $flight = compact('flight_no','flight_name','source','destination','flight_date','flight_time','airline_id','total_seats');
    $fares = $fareInputs;
}

include __DIR__ . '/../includes/header.php';
?>
<div class="container narrow">
  <h1><?= $editing ? 'Edit Flight' : 'Add Flight' ?></h1>
  <div class="card">
    <?php foreach ($errors as $e): ?><div class="alert alert-error"><?= clean($e) ?></div><?php endforeach; ?>
    <form method="post" class="stack">
      <div class="row-2">
        <div class="field"><label>Flight No *</label><input type="text" name="flight_no" required value="<?= clean($flight['flight_no']) ?>"></div>
        <div class="field"><label>Flight Name *</label><input type="text" name="flight_name" value="<?= clean($flight['flight_name']) ?>"></div>
      </div>
      <div class="row-2">
        <div class="field"><label>Source *</label><input type="text" name="source" required value="<?= clean($flight['source']) ?>"></div>
        <div class="field"><label>Destination *</label><input type="text" name="destination" required value="<?= clean($flight['destination']) ?>"></div>
      </div>
      <div class="row-2">
        <div class="field"><label>Date *</label><input type="date" name="flight_date" required value="<?= clean($flight['flight_date']) ?>"></div>
        <div class="field"><label>Time *</label><input type="time" name="flight_time" required value="<?= clean(substr($flight['flight_time'],0,5)) ?>"></div>
      </div>
      <div class="row-2">
        <div class="field">
          <label>Airline</label>
          <select name="airline_id">
            <option value="">—</option>
            <?php foreach ($airlines as $a): ?>
              <option value="<?= (int)$a['airline_id'] ?>" <?= $flight['airline_id'] == $a['airline_id'] ? 'selected' : '' ?>><?= clean($a['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field"><label>Total Seats</label><input type="number" name="total_seats" value="<?= (int)$flight['total_seats'] ?>"></div>
      </div>

      <hr class="divider-line">
      <h3 class="mb-0">Class Fares</h3>
      <div class="row-3">
        <div class="field"><label>Economy  </label><input type="number" step="0.01" name="fare_economy" value="<?= clean($fares['economy']) ?>"></div>
        <div class="field"><label>Premium Economy </label><input type="number" step="0.01" name="fare_premium" value="<?= clean($fares['premium economy']) ?>"></div>
        <div class="field"><label>Business </label><input type="number" step="0.01" name="fare_business" value="<?= clean($fares['business']) ?>"></div>
      </div>

      <button class="btn btn-primary" type="submit"><?= $editing ? 'Save Changes' : 'Add Flight' ?></button>
      <a href="manage_flights.php" class="btn btn-outline">Cancel</a>
    </form>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
