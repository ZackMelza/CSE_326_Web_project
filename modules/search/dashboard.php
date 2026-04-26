<?php
session_start();

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

require_auth();

try {
    $pdo = db();
    $specialties = $pdo->query('SELECT name FROM specialties ORDER BY name LIMIT 5')->fetchAll();
    $publishedLists = (int) $pdo
        ->query("SELECT COUNT(*) FROM appointment_lists WHERE status = 'published'")
        ->fetchColumn();
} catch (Throwable $exception) {
    $specialties = [];
    $publishedLists = 0;
}
?>
<?php require_once __DIR__ . '/../../includes/header.php'; ?>
<body>
<div class="app-shell">
  <div class="app-frame">
    <div class="project-topbar">
      <div class="brand-mark">
        <div class="brand-badge">QS</div>
        <div class="brand-copy">
          <span class="brand-eyebrow">Search Preview</span>
          <span class="brand-title">Query Dashboard</span>
        </div>
      </div>
      <div class="topbar-links">
        <a class="topbar-link" href="../dashboard.php">Dashboard</a>
        <a class="topbar-link" href="../list.php">Open Results</a>
        <a class="topbar-link" href="../../auth/logout.php">Logout</a>
      </div>
    </div>

    <div class="dashboard-grid">
      <section class="page-panel dashboard-hero">
        <span class="section-label">Search Area</span>
        <h1 class="hero-title">Use the browser or the JSON API to inspect appointment data.</h1>
        <p class="hero-copy mb-4">
          The search module has a responsive HTML interface in `modules/list.php` and matching
          API coverage under `api/search/index.php` for Postman demonstration.
        </p>
        <div class="stack-actions">
          <a class="btn btn-primary" href="../list.php">Open Search UI</a>
          <a class="btn btn-outline-secondary" href="../../api/search/index.php">Open Search API</a>
        </div>
      </section>

      <aside class="page-panel dashboard-side">
        <span class="section-label">Current Coverage</span>
        <div class="dashboard-list">
          <div class="dashboard-list-item">
            <strong><?= e((string) $publishedLists) ?> published lists</strong>
            Search results can be filtered by status, specialty, and keyword.
          </div>
          <?php foreach ($specialties as $specialty): ?>
            <div class="dashboard-list-item">
              <strong>Specialty</strong>
              <?= e((string) $specialty['name']) ?>
            </div>
          <?php endforeach; ?>
        </div>
      </aside>
    </div>
  </div>
</div>
</body>
</html>
