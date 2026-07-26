<?php $cur = basename($_SERVER['PHP_SELF']); ?>
<div class="admin-side">
  <center> <a href="dashboard.php" class="<?= $cur === 'dashboard.php' ? 'active' : '' ?>">Overview</a> </center>
  <center> <a href="manage_flights.php" class="<?= $cur === 'manage_flights.php' ? 'active' : '' ?>">Flights</a> </center>
  <center> <a href="view_tickets.php" class="<?= $cur === 'view_tickets.php' ? 'active' : '' ?>">Tickets &amp; Passengers</a> </center>
  <center> <a href="manage_users.php" class="<?= $cur === 'manage_users.php' ? 'active' : '' ?>">Users</a> </center>
</div>
