<?php
session_start();

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

require_admin();

try {
    $pdo = db();
    $stats = [
        'specialties' => (int) $pdo->query('SELECT COUNT(*) FROM specialties')->fetchColumn(),
        'lists' => (int) $pdo->query('SELECT COUNT(*) FROM appointment_lists')->fetchColumn(),
        'candidates' => (int) $pdo->query('SELECT COUNT(*) FROM candidates')->fetchColumn(),
        'entries' => (int) $pdo->query('SELECT COUNT(*) FROM candidate_list_entries')->fetchColumn(),
    ];

    $recentLogs = $pdo->query(
        'SELECT action, entity_type, entity_id, created_at
         FROM audit_logs
         ORDER BY created_at DESC
         LIMIT 5'
    )->fetchAll();
} catch (Throwable $exception) {
    $stats = ['specialties' => 0, 'lists' => 0, 'candidates' => 0, 'entries' => 0];
    $recentLogs = [];
}
?>
<?php require_once __DIR__ . '/../../includes/header.php'; ?>
<body>
<div class="app-shell">
  <div class="app-frame">
    <div class="project-topbar">
      <div class="brand-mark">
        <div class="brand-badge">AD</div>
        <div class="brand-copy">
          <span class="brand-eyebrow">Role: admin</span>
          <span class="brand-title">Administration Hub</span>
        </div>
      </div>
      <div class="topbar-links">
        <a class="topbar-link" href="../dashboard.php">Dashboard</a>
        <a class="topbar-link" href="../list.php">Search</a>
        <a class="topbar-link" href="../../auth/logout.php">Logout</a>
      </div>
    </div>

    <section class="page-panel search-panel hero-card mb-4">
      <span class="hero-kicker">CRUD Control</span>
      <h1 class="hero-title">Manage the four core entities required by the project brief.</h1>
      <p class="hero-copy">
        These management pages cover specialties, appointment lists, candidates, and candidate-list
        entries. All actions use prepared statements and are restricted to the admin role.
      </p>
      <div class="results-grid compact-grid">
        <article class="soft-card result-card">
          <h2 class="h5">Specialties</h2>
          <p class="mb-3">Create and maintain specialty codes and sectors.</p>
          <a class="btn btn-primary w-100" href="../specialties/dashboard.php">Open</a>
        </article>
        <article class="soft-card result-card">
          <h2 class="h5">Appointment Lists</h2>
          <p class="mb-3">Manage yearly lists and their publication status.</p>
          <a class="btn btn-primary w-100" href="../lists/dashboard.php">Open</a>
        </article>
        <article class="soft-card result-card">
          <h2 class="h5">Candidates</h2>
          <p class="mb-3">Store candidate profile and contact information.</p>
          <a class="btn btn-primary w-100" href="../candidates/dashboard.php">Open</a>
        </article>
        <article class="soft-card result-card">
          <h2 class="h5">Ranked Entries</h2>
          <p class="mb-3">Assign candidates to appointment lists and manage rank/status.</p>
          <a class="btn btn-primary w-100" href="../entries/dashboard.php">Open</a>
        </article>
      </div>
    </section>

    <div class="dashboard-grid">
      <section class="page-panel dashboard-hero">
        <span class="section-label">Admin Metrics</span>
        <div class="metric-grid">
          <div class="metric-box">
            <strong><?= e((string) $stats['specialties']) ?></strong>
            <span>specialties</span>
          </div>
          <div class="metric-box">
            <strong><?= e((string) $stats['lists']) ?></strong>
            <span>lists</span>
          </div>
          <div class="metric-box">
            <strong><?= e((string) $stats['candidates']) ?></strong>
            <span>candidates</span>
          </div>
          <div class="metric-box">
            <strong><?= e((string) $stats['entries']) ?></strong>
            <span>entries</span>
          </div>
        </div>
      </section>

      <aside class="page-panel dashboard-side">
        <span class="section-label">Recent Audit</span>
        <div class="dashboard-list">
          <?php if ($recentLogs === []): ?>
            <div class="dashboard-list-item">
              <strong>No audit items</strong>
              Seed or CRUD actions will appear here.
            </div>
          <?php else: ?>
            <?php foreach ($recentLogs as $log): ?>
              <div class="dashboard-list-item">
                <strong><?= e((string) $log['action']) ?></strong>
                <?= e((string) $log['entity_type']) ?> #<?= e((string) $log['entity_id']) ?> at <?= e((string) $log['created_at']) ?>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </aside>
    </div>
  </div>
</div>
</body>
</html>
