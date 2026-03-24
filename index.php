<?php
session_start();

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
?>
<body class="bg-light">
<div class="container py-5">
  <div class="card shadow-sm">
    <div class="card-body p-4">
      <h1 class="h2 mb-3">CSE 326 Backend Assignment</h1>
      <p class="text-muted mb-4">
        This project now follows the assignment structure with PDO authentication,
        a protected dashboard, and a keyword-search list page.
      </p>

      <?php if (is_logged_in()): ?>
        <div class="alert alert-success">
          Logged in as <strong><?= e((string) $_SESSION['username']) ?></strong>
          with role <strong><?= e((string) ($_SESSION['role'] ?? 'member')) ?></strong>.
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <a class="btn btn-primary" href="modules/dashboard.php">Dashboard</a>
          <a class="btn btn-outline-primary" href="modules/list.php">List Page</a>
          <a class="btn btn-outline-danger" href="auth/logout.php">Logout</a>
        </div>
      <?php else: ?>
        <div class="alert alert-secondary">You are not logged in.</div>
        <div class="d-flex gap-2 flex-wrap">
          <a class="btn btn-primary" href="auth/login.php">Login</a>
          <a class="btn btn-outline-primary" href="auth/register.php">Register</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>
