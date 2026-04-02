<?php
session_start();

require_once __DIR__ . '/../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<body>
<div class="app-shell">
  <div class="app-frame">
    <div class="project-topbar">
      <div class="brand-mark">
        <div class="brand-badge">DB</div>
        <div class="brand-copy">
          <span class="brand-eyebrow">Protected Area</span>
          <span class="brand-title">Project Dashboard</span>
        </div>
      </div>
      <div class="topbar-links">
        <a class="topbar-link" href="../index.php">Home</a>
        <a class="topbar-link" href="list.php">List</a>
        <a class="topbar-link" href="../auth/logout.php">Logout</a>
      </div>
    </div>

    <div class="dashboard-grid">
      <section class="page-panel dashboard-hero">
        <span class="section-label">Authenticated Session</span>
        <h1 class="hero-title">Welcome, <?= e((string) $_SESSION['username']) ?>.</h1>
        <p class="hero-copy mb-4">
          This page exists behind a session guard and confirms that the backend authentication
          flow is active. The user role is loaded from the database-backed login session.
        </p>

        <div class="status-banner success mb-4">
          Logged in with role <strong><?= e((string) ($_SESSION['role'] ?? 'member')) ?></strong>.
        </div>

        <div class="stack-actions">
          <a class="btn btn-primary" href="list.php">Open Content List</a>
          <a class="btn btn-outline-secondary" href="../index.php">Back Home</a>
          <a class="btn btn-outline-danger" href="../auth/logout.php">Logout</a>
        </div>
      </section>

      <aside class="page-panel dashboard-side">
        <span class="section-label">What This Proves</span>
        <div class="dashboard-list">
          <div class="dashboard-list-item">
            <strong>Session Guard</strong>
            Anonymous visitors are redirected to the login page.
          </div>
          <div class="dashboard-list-item">
            <strong>Escaped Output</strong>
            Username and role are rendered safely with `htmlspecialchars()`.
          </div>
          <div class="dashboard-list-item">
            <strong>Assignment Flow</strong>
            This page is one of the core protected deliverables in the second assignment.
          </div>
        </div>
      </aside>
    </div>
  </div>
</div>
</body>
</html>
