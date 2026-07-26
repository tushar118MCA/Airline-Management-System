<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
$in_admin = true;
$page_title = 'Admin Login';

// Already logged in as admin? Skip straight to the panel.
if (is_admin()) {
    header('Location: dashboard.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND user_type = 'admin'");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['user_id']   = $admin['user_id'];
        $_SESSION['username']  = $admin['username'];
        $_SESSION['full_name'] = $admin['full_name'];
        $_SESSION['user_type'] = 'admin';

        header('Location: dashboard.php');
        exit;
    }
    $error = 'Invalid admin username or password.';
}

include __DIR__ . '/../includes/header.php';
?>
<div class="container narrow">
  <div class="card">
    <center><h1>Admin Login</h1></center>
    <p class="muted">This login is for Admin of GetAir only.Customers should use the regular
    <a href="../login.php">Login Page</a>.</p>

    <?php if ($error): ?><div class="alert alert-error"><?= clean($error) ?></div><?php endif; ?>

    <form method="post" class="stack">
      <div class="field">
        <label>Admin Username</label>
        <input type="text" name="username" placeholder="Enter admin username" required autofocus>
      </div>
      <div class="field">
        <label>Password</label>
        <input type="password" name="password" placeholder="Enter password" required>
      </div>
      <button class="btn btn-primary" type="submit">Admin Login</button>
    </form>
    
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
