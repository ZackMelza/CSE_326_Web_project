<?php
session_start();

require_once __DIR__ . '/../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<body class="bg-light">
<div class="container py-5">
  <div class="card shadow-sm">
    <div class="card-body p-4">
      <h1 class="h3 mb-3">Dashboard</h1>
      <p class="mb-2">Welcome, <strong><?= e((string) $_SESSION['username']) ?></strong>.</p>
      <p class="text-muted mb-4">Role: <?= e((string) ($_SESSION['role'] ?? 'member')) ?></p>
      <div class="d-flex gap-2 flex-wrap">
        <a class="btn btn-primary" href="list.php">Open Content List</a>
        <a class="btn btn-outline-secondary" href="../index.php">Home</a>
        <a class="btn btn-outline-danger" href="../auth/logout.php">Logout</a>
      </div>
    </div>
  </div>
</div>
</body>
</html>
