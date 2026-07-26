<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
require_admin();
$in_admin = true;
$page_title = 'Manage Users';

$users = $pdo->query(
    "SELECT u.*, (SELECT COUNT(*) FROM tickets t WHERE t.user_id = u.user_id) AS ticket_count
     FROM users u ORDER BY u.created_at DESC"
)->fetchAll();

$success = flash('success');
include __DIR__ . '/../includes/header.php';
?>
<div class="container">
  <h1><center> Manage Users</h1></center>
  <div class="admin-shell">
    <?php include __DIR__ . '/_nav.php'; ?>
    <div>
      <?php if ($success): ?><div class="alert alert-success"><?= clean($success) ?></div><?php endif; ?>
      <table class="data-table">
        <thead><tr><th>Username</th><th>Full Name</th><th>Email</th><th>Contact</th><th>Type</th><th>Bookings</th><th>Joined</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($users as $u): ?>
          <tr>
            <td><?= clean($u['username']) ?></td>
            <td><?= clean($u['full_name']) ?></td>
            <td><?= clean($u['email']) ?></td>
            <td><?= clean($u['contact_no']) ?></td>
            <td><span class="pill <?= $u['user_type']==='admin' ? 'pill-admin' : 'pill-customer' ?>"><?= clean($u['user_type']) ?></span></td>
            <td><?= (int)$u['ticket_count'] ?></td>
            <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
            <td>
              <?php if ($u['user_type'] !== 'admin'): ?>
              <form method="post" action="delete_user.php" onsubmit="return confirm('Delete user <?= clean($u['username']) ?> and all their bookings?');">
                <input type="hidden" name="user_id" value="<?= (int)$u['user_id'] ?>">
                <button class="btn btn-danger btn-sm" type="submit">Delete</button>
              </form>
              <?php else: ?>
                <span class="muted">—</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
