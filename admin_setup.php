<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';
$page_title = 'Admin Setup';

$result = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hash = password_hash('admin123', PASSWORD_BCRYPT);
    try {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = 'admin'");
        $stmt->execute();
        if ($stmt->fetch()) {
            $pdo->prepare("UPDATE users SET password = ?, user_type = 'admin' WHERE username = 'admin'")
                ->execute([$hash]);
            $result = "Admin password reset. Username: admin  Password: admin123";
        } else {
            $pdo->prepare(
                "INSERT INTO users (username, password, full_name, email, address, contact_no, user_type)
                 VALUES ('admin', ?, 'System Administrator', 'admin@getair.com', 'GetAir HQ', '9999999999', 'admin')"
            )->execute([$hash]);
            $result = "Admin account created. Username: admin  Password: admin123";
        }
    } catch (Exception $e) {
        $error = 'Could not update the database: ' . $e->getMessage();
    }
}

include __DIR__ . '/includes/header.php';
?>
<div class="container narrow">
  <div class="card">
    <h1>Admin Setup</h1>
    <p class="muted">This is a one-time browser-based fix for the "admin can't log in" issue —
      it (re)generates a proper password hash for the <code>admin</code> account directly in
      MySQL, no terminal required.</p>

    <?php if ($result): ?>
      <div class="alert alert-success"><?= clean($result) ?></div>
      <a href="admin/login.php" class="btn btn-primary">Go to Admin Login</a>
      <div class="alert alert-error" style="margin-top:18px;">
        Important: delete this file (<code>admin_setup.php</code>) from your project folder now
        that the admin account is fixed, so no one else can reset the admin password.
      </div>
    <?php else: ?>
      <?php if ($error): ?><div class="alert alert-error"><?= clean($error) ?></div><?php endif; ?>
      <form method="post">
        <button class="btn btn-primary" type="submit">Fix Admin Login</button>
      </form>
    <?php endif; ?>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
