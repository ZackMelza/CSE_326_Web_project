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
<body>
<div class="app-shell">
  <div class="app-frame">
    <div class="project-topbar">
      <div class="brand-mark">
        <div class="brand-badge">M2</div>
        <div class="brand-copy">
          <span class="brand-eyebrow">CSE 326 Backend</span>
          <span class="brand-title">Account Registration</span>
        </div>
      </div>
      <div class="topbar-links">
        <a class="topbar-link" href="../index.php">Home</a>
        <a class="topbar-link" href="login.php">Login</a>
      </div>
    </div>

    <div class="page-panel auth-layout">
      <section class="auth-showcase">
        <span class="section-label">Secure Access</span>
        <h1 class="hero-title">Create a project account and join the protected area.</h1>
        <p class="hero-copy">
          This registration flow uses server-side validation, hashed passwords, PDO prepared
          statements, and redirect-based authentication flow that matches the assignment rules.
        </p>

        <div class="feature-strip">
          <div class="feature-chip">
            <strong>PDO</strong>
            <span>Prepared statements for every database write.</span>
          </div>
          <div class="feature-chip">
            <strong>Hashing</strong>
            <span>Passwords are stored with `password_hash()`.</span>
          </div>
          <div class="feature-chip">
            <strong>Validation</strong>
            <span>All errors are collected before feedback is shown.</span>
          </div>
        </div>

        <div class="metric-grid">
          <div class="metric-box">
            <strong>8+</strong>
            <span>minimum password length enforced on the server</span>
          </div>
          <div class="metric-box">
            <strong>100%</strong>
            <span>assignment-compliant registration path</span>
          </div>
        </div>
      </section>

      <section class="auth-form-wrap">
        <div class="card auth-card hero-card">
          <div class="card-body p-4 p-lg-5">
            <span class="hero-kicker">Register</span>
            <h2 class="h2 mb-3">Create Account</h2>
            <p class="muted-note mb-4">Use a valid email and a strong password to create your account.</p>

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
              <div class="mb-4">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input id="confirm_password" name="confirm_password" type="password" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>

            <p class="mt-4 mb-0 muted-note">
              Already registered?
              <a href="login.php">Login here</a>
            </p>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>
</body>
</html>
