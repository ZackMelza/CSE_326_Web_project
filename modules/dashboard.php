<?php
session_start();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

require_auth();

try {
    $pdo = db();
    $trackedCountStmt = $pdo->prepare('SELECT COUNT(*) FROM tracked_candidates WHERE user_id = :user_id');
    $trackedCountStmt->execute(['user_id' => current_user_id()]);
    $stats = [
        'lists' => (int) $pdo->query('SELECT COUNT(*) FROM appointment_lists')->fetchColumn(),
        'candidates' => (int) $pdo->query('SELECT COUNT(*) FROM candidates')->fetchColumn(),
        'entries' => (int) $pdo->query('SELECT COUNT(*) FROM candidate_list_entries')->fetchColumn(),
        'tracked' => (int) $trackedCountStmt->fetchColumn(),
    ];

    $trackedStmt = $pdo->prepare(
        'SELECT tracked_candidates.label, candidates.first_name, candidates.last_name
         FROM tracked_candidates
         INNER JOIN candidates ON candidates.id = tracked_candidates.candidate_id
         WHERE tracked_candidates.user_id = :user_id
         ORDER BY tracked_candidates.created_at DESC
         LIMIT 3'
    );
    $trackedStmt->execute(['user_id' => current_user_id()]);
    $trackedCandidates = $trackedStmt->fetchAll();
} catch (Throwable $exception) {
    $stats = ['lists' => 0, 'candidates' => 0, 'entries' => 0, 'tracked' => 0];
    $trackedCandidates = [];
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
          <span class="brand-title">User Dashboard</span>
        </div>
      </div>
      <div class="topbar-links">
        <a class="topbar-link" href="../index.php">Home</a>
        <a class="topbar-link" href="list.php">Search</a>
        <a class="topbar-link" href="tracking/dashboard.php">Tracking</a>
        <?php if (is_admin()): ?>
          <a class="topbar-link" href="admin/dashboard.php">Admin</a>
        <?php endif; ?>
        <a class="topbar-link" href="../auth/logout.php">Logout</a>
      </div>
    </div>

    <div class="dashboard-grid">
      <section class="page-panel dashboard-hero">
        <span class="section-label">Authenticated Session</span>
        <h1 class="hero-title">Welcome, <?= e(current_username()) ?>.</h1>
        <p class="hero-copy mb-4">
          You are signed in as <strong><?= e(current_role()) ?></strong>. Members can search and track
          candidates, while admins also get access to the management dashboards.
        </p>

        <div class="metric-grid mb-4">
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
          <div class="metric-box">
            <strong><?= e((string) $stats['tracked']) ?></strong>
            <span>tracked candidates</span>
          </div>
        </div>

        <div class="stack-actions">
          <a class="btn btn-primary" href="list.php">Open Search</a>
          <a class="btn btn-outline-primary" href="tracking/dashboard.php">Manage Tracking</a>
          <?php if (is_admin()): ?>
            <a class="btn btn-outline-secondary" href="admin/dashboard.php">Admin Hub</a>
          <?php endif; ?>
        </div>
      </section>

      <aside class="page-panel dashboard-side">
        <span class="section-label">Recent Tracking</span>
        <div class="dashboard-list">
          <?php if ($trackedCandidates === []): ?>
            <div class="dashboard-list-item">
              <strong>No tracked candidates yet</strong>
              Open the tracking module to add candidates to your watchlist.
            </div>
          <?php else: ?>
            <?php foreach ($trackedCandidates as $tracked): ?>
              <div class="dashboard-list-item">
                <strong><?= e($tracked['first_name'] . ' ' . $tracked['last_name']) ?></strong>
                <?= e((string) $tracked['label']) ?>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
          <div class="dashboard-list-item">
            <strong>Session proof</strong>
            Username and role are stored in PHP session state and escaped on output.
          </div>
        </div>
      </aside>
    </div>
  </div>
</div>
</body>
</html>
