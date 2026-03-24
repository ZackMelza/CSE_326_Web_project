<?php
session_start();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (is_logged_in()) {
    redirect('../modules/dashboard.php');
}

$errors = [];
$email = trim($_POST['email'] ?? '');
$registered = isset($_GET['registered']) && $_GET['registered'] === '1';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = 'Email and password are required.';
    } else {
        try {
            $stmt = db()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password_hash'])) {
                $errors[] = 'Λανθασμένα στοιχεία σύνδεσης.';
            } else {
                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['role'] = (string) $user['role'];
                $_SESSION['username'] = (string) $user['username'];

                header('Location: ../modules/dashboard.php');
                exit;
            }
        } catch (Throwable $exception) {
            $errors[] = 'Unable to sign in right now. Please try again later.';
        }
    }
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<body class="bg-light">
<div class="container py-5" style="max-width: 580px;">
  <div class="card shadow-sm">
    <div class="card-body p-4">
      <h1 class="h3 mb-3">Login</h1>

      <?php if ($registered): ?>
        <div class="alert alert-success">Registration completed. You can now sign in.</div>
      <?php endif; ?>

      <?php if ($errors !== []): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
              <li><?= e($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" action="login.php" novalidate>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input id="email" name="email" type="email" class="form-control" value="<?= e($email) ?>" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input id="password" name="password" type="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
      </form>

      <p class="mt-3 mb-0">
        Need an account?
        <a href="register.php">Register here</a>
      </p>
    </div>
  </div>
</div>
</body>
</html>
