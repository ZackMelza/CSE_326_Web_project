<?php
session_start();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (is_logged_in()) {
    redirect('../modules/dashboard.php');
}

$errors = [];
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($username === '') {
        $errors[] = 'Username is required.';
    }

    if ($email === '') {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email format is invalid.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    if ($confirmPassword === '') {
        $errors[] = 'Confirm password is required.';
    } elseif ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if ($errors === []) {
        try {
            $pdo = db();

            $emailStmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
            $emailStmt->execute(['email' => $email]);
            if ($emailStmt->fetch()) {
                $errors[] = 'Email is already registered.';
            }

            $usernameStmt = $pdo->prepare('SELECT id FROM users WHERE username = :username LIMIT 1');
            $usernameStmt->execute(['username' => $username]);
            if ($usernameStmt->fetch()) {
                $errors[] = 'Username is already taken.';
            }

            if ($errors === []) {
                $insertStmt = $pdo->prepare(
                    'INSERT INTO users (username, email, password_hash, role) VALUES (:username, :email, :password_hash, :role)'
                );
                $insertStmt->execute([
                    'username' => $username,
                    'email' => $email,
                    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                    'role' => 'member',
                ]);

                header('Location: login.php?registered=1');
                exit;
            }
        } catch (Throwable $exception) {
            $errors[] = 'A database error occurred. Please try again later.';
        }
    }
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<body class="bg-light">
<div class="container py-5" style="max-width: 580px;">
  <div class="card shadow-sm">
    <div class="card-body p-4">
      <h1 class="h3 mb-3">Create Account</h1>

      <?php if ($errors !== []): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
              <li><?= e($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" action="register.php" novalidate>
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input id="username" name="username" class="form-control" value="<?= e($username) ?>" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input id="email" name="email" type="email" class="form-control" value="<?= e($email) ?>" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input id="password" name="password" type="password" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="confirm_password" class="form-label">Confirm Password</label>
          <input id="confirm_password" name="confirm_password" type="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Register</button>
      </form>

      <p class="mt-3 mb-0">
        Already registered?
        <a href="login.php">Login here</a>
      </p>
    </div>
  </div>
</div>
</body>
</html>
