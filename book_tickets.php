<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';
$page_title = 'Book Tickets';

$sources = $pdo->query("SELECT DISTINCT source FROM flights ORDER BY source")->fetchAll();
$dests   = $pdo->query("SELECT DISTINCT destination FROM flights ORDER BY destination")->fetchAll();

include __DIR__ . '/includes/header.php';
?>
<div class="container narrow">
  <div class="card">
    <center> <h1>Book Tickets</h1></center>
    <center>  <p class="muted">Search flights by route and date.</p>  </center>
    <form method="get" action="search_results.php" class="stack">
      <div class="row-2">
        <div class="field">
          <label>From</label>
          <select name="source" required>
            <option value="">Select source</option>
            <?php foreach ($sources as $s): ?>
              <option value="<?= clean($s['source']) ?>"><?= clean($s['source']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field">
          <label>To</label>
          <select name="destination" required>
            <option value="">Select destination</option>
            <?php foreach ($dests as $d): ?>
              <option value="<?= clean($d['destination']) ?>"><?= clean($d['destination']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="field">
        <label>Date of Journey</label>
        <input type="date" name="date" min="<?= date('Y-m-d') ?>">
        <span class="muted" style="font-size:.8rem;">Leave blank to see all upcoming dates on this route.</span>
      </div>
      <button class="btn btn-primary" type="submit">Search Flights</button>
    </form>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
