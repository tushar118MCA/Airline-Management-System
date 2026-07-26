<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';
$page_title = 'Search Results';

$source      = clean($_GET['source'] ?? '');
$destination = clean($_GET['destination'] ?? '');
$date        = clean($_GET['date'] ?? '');

$sql = "SELECT f.*, a.name AS airline_name FROM flights f
        LEFT JOIN airlines a ON a.airline_id = f.airline_id
        WHERE f.source = ? AND f.destination = ? AND f.flight_date >= CURRENT_DATE";
$params = [$source, $destination];
if ($date !== '') {
    $sql .= " AND f.flight_date = ?";
    $params[] = $date;
}
$sql .= " ORDER BY f.flight_date, f.flight_time";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$flights = $stmt->fetchAll();

$classStmt = $pdo->prepare("SELECT * FROM classes WHERE flight_id = ? ORDER BY fare");

include __DIR__ . '/includes/header.php';
?>
<div class="container">
  <h1><?= clean($source) ?> <span style="color:var(--amber);">→</span> <?= clean($destination) ?></h1>
  <p class="muted"><?= count($flights) ?> flight(s) found<?= $date ? ' on ' . clean($date) : '' ?>.</p>

  <?php if (!$flights): ?>
    <div class="alert alert-info">No flights found for this route<?= $date ? ' and date' : '' ?>. Try a Different Search.</div>
    <a href="book_tickets.php" class="btn btn-outline">New Search</a>
  <?php endif; ?>

  <?php foreach ($flights as $f):
      $classStmt->execute([$f['flight_id']]);
      $classes = $classStmt->fetchAll();
  ?>
    <form method="post" action="book_ticket.php" class="flight-card">
      <input type="hidden" name="flight_id" value="<?= (int)$f['flight_id'] ?>">
      <div>
        <div class="flight-route">
          <span><?= clean($f['source']) ?></span>
          <span class="divider">✈</span>
          <span><?= clean($f['destination']) ?></span>
        </div>
        <div class="flight-meta">
          <span>Flight: <b><?= clean($f['flight_no']) ?></b></span>
          <span>Airline: <b><?= clean($f['airline_name'] ?? 'GetAir') ?></b></span>
          <span>Date: <b><?= date('d M Y', strtotime($f['flight_date'])) ?></b></span>
          <span>Time: <b><?= date('h:i A', strtotime($f['flight_time'])) ?></b></span>
        </div>
      </div>
      <div class="class-options">
        <?php foreach ($classes as $i => $c): ?>
          <label class="class-chip">
            <input type="radio" name="class_type" value="<?= clean($c['class_type']) ?>" <?= $i === 0 ? 'checked' : '' ?> required>
            <div class="ct"><?= clean($c['class_type']) ?></div>
            <div class="cf">Rs.<?= number_format($c['fare'], 2) ?></div>
          </label>
        <?php endforeach; ?>
      </div>
      <button class="btn btn-amber" type="submit">Select</button>
    </form>
  <?php endforeach; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
