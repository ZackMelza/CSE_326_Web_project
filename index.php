<?php
session_start();

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
?>
<body>
<div class="app-shell">
  <div class="app-frame">
    <div class="project-topbar">
      <div class="brand-mark">
        <div class="brand-badge">C3</div>
        <div class="brand-copy">
          <span class="brand-eyebrow">Course Project</span>
          <span class="brand-title">Backend Assignment Portal</span>
        </div>
      </div>
      <div class="topbar-links">
        <a class="topbar-link" href="auth/login.php">Login</a>
        <a class="topbar-link" href="auth/register.php">Register</a>
        <a class="topbar-link" href="modules/list.php">List</a>
      </div>
    </div>

    <section class="page-panel search-panel hero-card mb-4">
      <span class="hero-kicker">Submission Ready</span>
      <h1 class="hero-title">PDO authentication, protected pages, and keyword search in one clean coursework build.</h1>
      <p class="hero-copy">
        The repository now follows the second backend assignment structure with a secure register/login/logout
        flow, a protected dashboard, and a searchable list page backed by a thematic table linked to users.
      </p>

      <div class="feature-strip">
        <div class="feature-chip">
          <strong>Auth Flow</strong>
          <span>Register, login, logout, session guard, and escaped output.</span>
        </div>
        <div class="feature-chip">
          <strong>Database</strong>
          <span>PDO connection, schema, seed data, and foreign-key-linked records.</span>
        </div>
        <div class="feature-chip">
          <strong>Deliverables</strong>
          <span>Protected dashboard plus GET-based keyword search list page.</span>
        </div>
      </div>
    </section>

    <?php if (is_logged_in()): ?>
      <section class="dashboard-grid">
        <div class="page-panel dashboard-hero">
          <span class="section-label">Current Session</span>
          <h2 class="h1 mb-3">You are signed in as <?= e((string) $_SESSION['username']) ?>.</h2>
          <p class="hero-copy mb-4">
            Your current role is <strong><?= e((string) ($_SESSION['role'] ?? 'member')) ?></strong>.
            Use the navigation below to open the protected pages included in the assignment.
          </p>
          <div class="stack-actions">
            <a class="btn btn-primary" href="modules/dashboard.php">Open Dashboard</a>
            <a class="btn btn-outline-primary" href="modules/list.php">Browse List Page</a>
            <a class="btn btn-outline-danger" href="auth/logout.php">Logout</a>
          </div>
        </div>

        <aside class="page-panel dashboard-side">
          <span class="section-label">System Status</span>
          <div class="dashboard-list">
            <div class="dashboard-list-item">
              <strong>Authentication</strong>
              Session state is active and the protected routes are available.
            </div>
            <div class="dashboard-list-item">
              <strong>Search Module</strong>
              The keyword-search deliverable is ready under `modules/list.php`.
            </div>
            <div class="dashboard-list-item">
              <strong>Documentation</strong>
              The project journal is being maintained for the final presentation.
            </div>
          </div>
        </aside>
      </section>
    <?php else: ?>
      <section class="dashboard-grid">
        <div class="page-panel dashboard-hero">
          <span class="section-label">Access Control</span>
          <h2 class="h1 mb-3">Start with registration or sign in using a seeded account.</h2>
          <p class="hero-copy mb-4">
            Anonymous visitors can use the public auth pages, but the dashboard and the list page
            stay behind a session guard until a valid login is completed.
          </p>
          <div class="stack-actions">
            <a class="btn btn-primary" href="auth/login.php">Login</a>
            <a class="btn btn-outline-primary" href="auth/register.php">Create Account</a>
          </div>
        </div>

        <aside class="page-panel dashboard-side">
          <span class="section-label">Demo Accounts</span>
          <div class="dashboard-list">
            <div class="dashboard-list-item">
              <strong>Admin</strong>
              `admin@example.com` with password `Password123!`
            </div>
            <div class="dashboard-list-item">
              <strong>Writer</strong>
              `writer@example.com` with password `Password123!`
            </div>
            <div class="dashboard-list-item">
              <strong>Viewer</strong>
              `viewer@example.com` with password `Password123!`
            </div>
          </div>
        </aside>
      </section>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
