<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';
$page_title = 'User Login';
$error = null;
$success = flash('success');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = clean($_POST['username'] ?? '');
    $password  = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND user_type = 'customer'");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['user_id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['user_type'] = $user['user_type'];

        header('Location: dashboard.php');
        exit;
    }
    $error = 'Invalid username or password.';
}

include __DIR__ . '/includes/header.php';
?>
<div class="container narrow">
  <div class="card">
    <center> <h1> User Login </h1></center>
    <center><p class="muted">Sign in to book &amp; manage your tickets.</p></center>

    <?php if ($success): ?><div class="alert alert-success"><?= clean($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= clean($error) ?></div><?php endif; ?>

    <form method="post" class="stack">
      <div class="field">
        <label>Username</label>
        <input type="text" name="username" placeholder="Enter your username" required>
      </div>
      <div class="field">
        <label>Password</label>
        <input type="password" name="password" placeholder="Enter your password" required>
      </div>
      <button class="btn btn-primary" type="submit">Login</button>
    </form>
    <p class="muted" style="margin-top:16px;">
      <center> <a href="register.php">Create New User Account</a> </center>
    </p>
    <center><p class="fine muted" style="font-size:.8rem;">GetAir Admin Use Only <a href="admin/login.php"> Admin Login</a> login</a> instead.</p></center>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
