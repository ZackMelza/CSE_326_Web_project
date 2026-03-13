<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>
<body class="bg-light">
<div class="container py-5">
  <div class="card shadow-sm">
    <div class="card-body p-4">
      <h1 class="h3 mb-3">Session Auth Demo</h1>

      <?php if (is_logged_in()): ?>
        <div class="alert alert-success mb-3">
          Logged in as
          <strong><?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?></strong>
          (<?= htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8') ?>)
        </div>
        <a class="btn btn-outline-danger" href="logout.php">Logout</a>
      <?php else: ?>
        <div class="alert alert-secondary mb-3">You are not logged in.</div>
        <a class="btn btn-primary me-2" href="login.php">Login</a>
        <a class="btn btn-outline-primary" href="register.php">Create account</a>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>
