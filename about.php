<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = 'About Us';
include __DIR__ . '/includes/header.php';
?>
<div class="container narrow">
  <h1><center> About GetAir</h1></center>
  <p class="muted">GetAir is a demo Airline Management system built to show how a full booking
    pipeline — search, seat class selection, passenger details, payment, e-ticketing, and
    cancellation — fits together end to end on PHP and PostgreSQL.</p>
  <div class="card" style="margin-top:24px;">
    <h3><center> Our promise</center></h3>
    <ul class="muted">
      <li>Transparent fares across Economy, Premium Economy, and Business Class.</li>
      <li>Instant PNR and a printable digital boarding pass on every booking.</li>
      <li>Free cancellation within 24 hours of booking, no questions asked.</li>
      <li>An admin console for managing flights, fares, users, and passenger .</li>
    </ul>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
