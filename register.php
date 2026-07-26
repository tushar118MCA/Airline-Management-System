<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';
$page_title = 'Create Account';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = clean($_POST['username'] ?? '');
    $full_name = clean($_POST['full_name'] ?? '');
    $email     = clean($_POST['email'] ?? '');
    $address   = clean($_POST['address'] ?? '');
    $contact   = clean($_POST['contact_no'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    if ($username === '' || $full_name === '' || $email === '' || $password === '') {
        $errors[] = 'Please fill in all required fields.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT 1 FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Username or email is already registered.';
        }
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare(
            "INSERT INTO users (username, password, full_name, email, address, contact_no, user_type)
             VALUES (?, ?, ?, ?, ?, ?, 'customer')"
        );
        $stmt->execute([$username, $hash, $full_name, $email, $address, $contact]);
        flash('success', 'Account created! Please log in.');
        header('Location: login.php');
        exit;
    }
}

include __DIR__ . '/includes/header.php';
?>
<div class="container narrow">
  <div class="card">
    <h1>Create New User Account</h1>
    <p class="muted">Register to book tickets, view your bookings, and cancel within 24 hours.</p>

    <?php foreach ($errors as $e): ?>
      <div class="alert alert-error"><?= clean($e) ?></div>
    <?php endforeach; ?>

    <form method="post" class="stack">
      <div class="row-2">
        <div class="field">
          <label>Username *</label>
          <input type="text" name="username" required value="<?= clean($_POST['username'] ?? '') ?>">
        </div>
        <div class="field">
          <label>Full Name *</label>
          <input type="text" name="full_name" required value="<?= clean($_POST['full_name'] ?? '') ?>">
        </div>
      </div>
      <div class="row-2">
        <div class="field">
          <label>Email *</label>
          <input type="email" name="email" required value="<?= clean($_POST['email'] ?? '') ?>">
        </div>
        <div class="field">
          <label>Contact No.</label>
          <input type="tel" name="contact_no" value="<?= clean($_POST['contact_no'] ?? '') ?>">
        </div>
      </div>
      <div class="field">
        <label>Address</label>
        <input type="text" name="address" value="<?= clean($_POST['address'] ?? '') ?>">
      </div>
      <div class="row-2">
        <div class="field">
          <label>Password *</label>
          <input type="password" name="password" required minlength="6">
        </div>
        <div class="field">
          <label>Confirm Password *</label>
          <input type="password" name="confirm_password" required minlength="6">
        </div>
      </div>
      <button class="btn btn-primary" type="submit">Create Account</button>
    </form>
    <p class="muted" style="margin-top:16px;">Already have an account? <a href="login.php">Log in</a></p>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
