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
<body>
<div class="app-shell">
  <div class="app-frame">
    <div class="project-topbar">
      <div class="brand-mark">
        <div class="brand-badge">M2</div>
        <div class="brand-copy">
          <span class="brand-eyebrow">CSE 326 Backend</span>
          <span class="brand-title">Authentication Portal</span>
        </div>
      </div>
      <div class="topbar-links">
        <a class="topbar-link" href="../index.php">Home</a>
        <a class="topbar-link" href="register.php">Register</a>
      </div>
    </div>

    <div class="page-panel auth-layout">
      <section class="auth-showcase">
        <span class="section-label">Protected Entry</span>
        <h1 class="hero-title">Sign in to the dashboard and searchable project content.</h1>
        <p class="hero-copy">
          Logging in creates the PHP session used to access the protected dashboard and the
          assignment list page. The authentication check stays server-side, not just visual.
        </p>

        <div class="feature-strip">
          <div class="feature-chip">
            <strong>Sessions</strong>
            <span>Authenticated users get role and username in session state.</span>
          </div>
          <div class="feature-chip">
            <strong>Guards</strong>
            <span>Dashboard and list pages block anonymous access.</span>
          </div>
          <div class="feature-chip">
            <strong>Search</strong>
            <span>The protected list page supports bookmarkable keyword filters.</span>
          </div>
        </div>

        <div class="metric-grid">
          <div class="metric-box">
            <strong>2</strong>
            <span>protected areas unlocked after authentication</span>
          </div>
          <div class="metric-box">
            <strong>0</strong>
            <span>plain-text passwords stored in the database</span>
          </div>
        </div>
      </section>

      <section class="auth-form-wrap">
        <div class="card auth-card hero-card">
          <div class="card-body p-4 p-lg-5">
            <span class="hero-kicker">Login</span>
            <h2 class="h2 mb-3">Welcome Back</h2>
            <p class="muted-note mb-4">Sign in with the email and password stored in the database.</p>

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
              <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input id="password" name="password" type="password" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <p class="mt-4 mb-0 muted-note">
              Need an account?
              <a href="register.php">Register here</a>
            </p>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>
</body>
</html>
