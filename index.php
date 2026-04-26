<?php
session_start();

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

try {
    $pdo = db();
    $stats = [
        'specialties' => (int) $pdo->query('SELECT COUNT(*) FROM specialties')->fetchColumn(),
        'lists' => (int) $pdo->query('SELECT COUNT(*) FROM appointment_lists')->fetchColumn(),
        'candidates' => (int) $pdo->query('SELECT COUNT(*) FROM candidates')->fetchColumn(),
        'entries' => (int) $pdo->query('SELECT COUNT(*) FROM candidate_list_entries')->fetchColumn(),
    ];
} catch (Throwable $exception) {
    $stats = [
        'specialties' => 0,
        'lists' => 0,
        'candidates' => 0,
        'entries' => 0,
    ];
}
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>
<body>
<div class="app-shell">
  <div class="app-frame">
    <div class="project-topbar">
      <div class="brand-mark">
        <div class="brand-badge">EEY</div>
        <div class="brand-copy">
          <span class="brand-eyebrow">CSE 326 Project</span>
          <span class="brand-title">Appointment Tracking Portal</span>
        </div>
      </div>
      <div class="topbar-links">
        <?php if (is_logged_in()): ?>
          <a class="topbar-link" href="modules/dashboard.php">Dashboard</a>
          <a class="topbar-link" href="modules/list.php">Search</a>
          <?php if (is_admin()): ?>
            <a class="topbar-link" href="modules/admin/dashboard.php">Admin</a>
          <?php endif; ?>
          <a class="topbar-link" href="auth/logout.php">Logout</a>
        <?php else: ?>
          <a class="topbar-link" href="auth/login.php">Login</a>
          <a class="topbar-link" href="auth/register.php">Register</a>
        <?php endif; ?>
      </div>
    </div>

    <section class="page-panel search-panel hero-card mb-4">
      <span class="hero-kicker">Checklist Ready</span>
      <h1 class="hero-title">Relational MySQL schema, role-based sessions, CRUD modules, JSON API, and responsive coursework UI.</h1>
      <p class="hero-copy">
        The project now models specialties, appointment lists, candidates, ranked entries, user tracking,
        and audit activity in a single PHP and MariaDB application. Members can search and track candidates,
        while admins manage the core entities from protected CRUD screens.
      </p>

      <div class="feature-strip">
        <div class="feature-chip">
          <strong>7 Tables</strong>
          <span>Users, specialties, appointment lists, candidates, entries, tracking, and audit logs.</span>
        </div>
        <div class="feature-chip">
          <strong>4 CRUD Areas</strong>
          <span>Admins manage specialties, lists, candidates, and ranked entries.</span>
        </div>
        <div class="feature-chip">
          <strong>6 API Endpoints</strong>
          <span>Postman-ready JSON endpoints live under `api/`.</span>
        </div>
      </div>
    </section>

    <section class="dashboard-grid">
      <div class="page-panel dashboard-hero">
        <span class="section-label">Module Selector</span>
        <h2 class="h1 mb-3">Choose the part of the system you want to demonstrate.</h2>
        <p class="hero-copy mb-4">
          The landing page acts as a clean entry point for the search area, member tracking, and the
          admin management screens required by the assignment screenshot.
        </p>

        <div class="results-grid compact-grid">
          <article class="soft-card result-card">
            <h3 class="h5">Search Module</h3>
            <p class="mb-3">Browse appointment list entries with specialty and status filters.</p>
            <a class="btn btn-primary w-100" href="modules/list.php">Open Search</a>
          </article>
          <article class="soft-card result-card">
            <h3 class="h5">Tracking Module</h3>
            <p class="mb-3">Members can keep personal tracked-candidate watchlists.</p>
            <a class="btn btn-primary w-100" href="modules/tracking/dashboard.php">Open Tracking</a>
          </article>
          <article class="soft-card result-card">
            <h3 class="h5">Admin Module</h3>
            <p class="mb-3">Admins manage specialties, lists, candidates, and ranked entries.</p>
            <a class="btn btn-primary w-100" href="modules/admin/dashboard.php">Open Admin</a>
          </article>
        </div>
      </div>

      <aside class="page-panel dashboard-side">
        <span class="section-label">Live Summary</span>
        <div class="metric-grid">
          <div class="metric-box">
            <strong><?= e((string) $stats['specialties']) ?></strong>
            <span>specialties</span>
          </div>
          <div class="metric-box">
            <strong><?= e((string) $stats['lists']) ?></strong>
            <span>appointment lists</span>
          </div>
          <div class="metric-box">
            <strong><?= e((string) $stats['candidates']) ?></strong>
            <span>candidates</span>
          </div>
          <div class="metric-box">
            <strong><?= e((string) $stats['entries']) ?></strong>
            <span>ranked entries</span>
          </div>
        </div>

        <div class="dashboard-list">
          <div class="dashboard-list-item">
            <strong>Admin account</strong>
            `admin@example.com` / `Password123!`
          </div>
          <div class="dashboard-list-item">
            <strong>Member accounts</strong>
            `writer@example.com` and `viewer@example.com` share `Password123!`
          </div>
          <div class="dashboard-list-item">
            <strong>Postman demo</strong>
            The ready-made collection is stored in `postman/CSE_326_Web_project.postman_collection.json`.
          </div>
        </div>
      </aside>
    </section>
  </div>
</div>
</body>
</html>
