<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    redirect('index.php');
}

$errors = [];
$identity = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identity = trim($_POST['identity'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($identity === '' || $password === '') {
        $errors[] = 'Username/email and password are required.';
    } else {
        try {
            $db = get_db_connection();
            $stmt = $db->prepare('SELECT id, username, email, password_hash FROM users WHERE username = ? OR email = ? LIMIT 1');
            $stmt->bind_param('ss', $identity, $identity);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$user || !password_verify($password, $user['password_hash'])) {
                $errors[] = 'Invalid credentials.';
            } else {
                login_user((int) $user['id'], $user['username'], $user['email']);
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
      <h1 class="h4 mb-3">Login</h1>

      <?php if ($errors !== []): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" action="login.php" novalidate>
        <div class="mb-3">
          <label for="identity" class="form-label">Username or email</label>
          <input id="identity" name="identity" class="form-control" required
                 value="<?= htmlspecialchars($identity, ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input id="password" type="password" name="password" class="form-control" required>
        </div>
        <button class="btn btn-primary w-100" type="submit">Login</button>
      </form>

      <p class="mt-3 mb-0">
        Need an account?
        <a href="register.php">Register</a>
      </p>
    </div>
  </div>
</div>
</body>
</html>
