<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';
$page_title = 'Check PNR';

$pnr = clean($_GET['pnr'] ?? ($_POST['pnr'] ?? ''));
if ($pnr !== '') {
    header('Location: ticket.php?pnr=' . urlencode($pnr));
    exit;
}

include __DIR__ . '/includes/header.php';
?>
<div class="container narrow">
  <div class="card">
    <center>  <h1>Check PNR Status</h1> </center>
    <p class="muted">Enter your 7-digit PNR to view ticket details and the boarding pass.</p>
    <form method="get" action="check_pnr.php" class="stack">
      <div class="field">
        <label>PNR</label>
        <input type="text" name="pnr" required placeholder="eg. 3245674">
      </div>
      <button class="btn btn-primary" type="submit">Check Status</button>
    </form>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
