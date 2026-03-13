<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    redirect('index.php');
}

$errors = [];
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($username === '' || strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if ($errors === []) {
        try {
            $db = get_db_connection();

            $checkStmt = $db->prepare('SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
            $checkStmt->bind_param('ss', $username, $email);
            $checkStmt->execute();
            $existing = $checkStmt->get_result()->fetch_assoc();
            $checkStmt->close();

            if ($existing) {
                $errors[] = 'Username or email is already in use.';
            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $insertStmt = $db->prepare('INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)');
                $insertStmt->bind_param('sss', $username, $email, $passwordHash);
                $insertStmt->execute();
                $newUserId = $insertStmt->insert_id;
                $insertStmt->close();

                login_user($newUserId, $username, $email);
                redirect('index.php');
            }
        } catch (Throwable $e) {
            $errors[] = 'Database connection failed. Enable php-mysqli and verify DB settings in includes/config.php.';
        }
    }
}
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>
<body class="bg-light">
<div class="container py-5" style="max-width: 520px;">
  <div class="card shadow-sm">
    <div class="card-body p-4">
      <h1 class="h4 mb-3">Create account</h1>

      <?php if ($errors !== []): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" action="register.php" novalidate>
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input id="username" name="username" class="form-control" required
                 value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input id="email" type="email" name="email" class="form-control" required
                 value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input id="password" type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="confirm_password" class="form-label">Confirm password</label>
          <input id="confirm_password" type="password" name="confirm_password" class="form-control" required>
        </div>
        <button class="btn btn-primary w-100" type="submit">Register</button>
      </form>

      <p class="mt-3 mb-0">
        Already have an account?
        <a href="login.php">Login</a>
      </p>
    </div>
  </div>
</div>
</body>
</html>
