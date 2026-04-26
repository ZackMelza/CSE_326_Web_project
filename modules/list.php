<?php
session_start();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

require_auth();

$keyword = trim((string) ($_GET['keyword'] ?? ''));
$status = trim((string) ($_GET['status'] ?? ''));
$specialtyId = (int) ($_GET['specialty_id'] ?? 0);

try {
    $pdo = db();

    $specialties = $pdo->query('SELECT id, name FROM specialties ORDER BY name')->fetchAll();

    $sql = '
        SELECT
            candidate_list_entries.id,
            candidate_list_entries.ranking,
            candidate_list_entries.status,
            candidate_list_entries.notes,
            candidates.first_name,
            candidates.last_name,
            candidates.city,
            appointment_lists.title AS list_title,
            appointment_lists.publish_year,
            specialties.name AS specialty_name
        FROM candidate_list_entries
        INNER JOIN candidates ON candidates.id = candidate_list_entries.candidate_id
        INNER JOIN appointment_lists ON appointment_lists.id = candidate_list_entries.list_id
        INNER JOIN specialties ON specialties.id = appointment_lists.specialty_id
        WHERE 1 = 1
    ';

    $params = [];

    if ($keyword !== '') {
        $sql .= '
            AND (
                candidates.first_name LIKE :keyword
                OR candidates.last_name LIKE :keyword
                OR candidates.city LIKE :keyword
                OR appointment_lists.title LIKE :keyword
                OR specialties.name LIKE :keyword
            )
        ';
        $params['keyword'] = '%' . $keyword . '%';
    }

    if ($status !== '') {
        $sql .= ' AND candidate_list_entries.status = :status';
        $params['status'] = $status;
    }

    if ($specialtyId > 0) {
        $sql .= ' AND specialties.id = :specialty_id';
        $params['specialty_id'] = $specialtyId;
    }

    $sql .= ' ORDER BY candidate_list_entries.ranking ASC, candidates.last_name ASC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $entries = $stmt->fetchAll();
} catch (Throwable $exception) {
    $specialties = [];
    $entries = [];
    $loadError = 'Unable to load the candidate search results right now.';
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<body>
<div class="app-shell">
  <div class="app-frame">
    <div class="project-topbar">
      <div class="brand-mark">
        <div class="brand-badge">SR</div>
        <div class="brand-copy">
          <span class="brand-eyebrow">Search Module</span>
          <span class="brand-title">Candidate List Browser</span>
        </div>
      </div>
      <div class="topbar-links">
        <a class="topbar-link" href="dashboard.php">Dashboard</a>
        <a class="topbar-link" href="tracking/dashboard.php">Tracking</a>
        <?php if (is_admin()): ?>
          <a class="topbar-link" href="admin/dashboard.php">Admin</a>
        <?php endif; ?>
        <a class="topbar-link" href="../auth/logout.php">Logout</a>
      </div>
    </div>

    <div class="search-layout">
      <section class="page-panel search-panel hero-card">
        <span class="hero-kicker">GET Filters</span>
        <div class="row g-4 align-items-end">
          <div class="col-lg-5">
            <h1 class="hero-title mb-3">Search ranked candidates by name, specialty, city, and list status.</h1>
            <p class="hero-copy mb-0">
              This is the assignment search UI backed by the new appointment-tracking domain model.
              Filters stay in the URL, so the exact query can be bookmarked or reused in Postman demos.
            </p>
          </div>
          <div class="col-lg-7">
            <form method="get" action="list.php" class="row g-3">
              <div class="col-md-5">
                <label for="keyword" class="form-label">Keyword</label>
                <input id="keyword" name="keyword" class="form-control" value="<?= e($keyword) ?>" placeholder="Candidate, city, list or specialty">
              </div>
              <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select">
                  <option value="">All</option>
                  <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                  <option value="review" <?= $status === 'review' ? 'selected' : '' ?>>Review</option>
                  <option value="archived" <?= $status === 'archived' ? 'selected' : '' ?>>Archived</option>
                </select>
              </div>
              <div class="col-md-4">
                <label for="specialty_id" class="form-label">Specialty</label>
                <select id="specialty_id" name="specialty_id" class="form-select">
                  <option value="0">All specialties</option>
                  <?php foreach ($specialties as $specialty): ?>
                    <option value="<?= e((string) $specialty['id']) ?>" <?= $specialtyId === (int) $specialty['id'] ? 'selected' : '' ?>>
                      <?= e((string) $specialty['name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-sm-6 col-lg-3">
                <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
              </div>
              <div class="col-sm-6 col-lg-3">
                <a class="btn btn-outline-secondary w-100" href="list.php">Reset</a>
              </div>
            </form>
          </div>
        </div>
      </section>

      <?php if (isset($loadError)): ?>
        <div class="alert alert-danger"><?= e($loadError) ?></div>
      <?php elseif ($entries === []): ?>
        <div class="alert alert-secondary">No matching candidate entries were found.</div>
      <?php else: ?>
        <div class="results-grid">
          <?php foreach ($entries as $entry): ?>
            <article class="soft-card result-card">
              <span class="badge text-bg-primary mb-3"><?= e((string) $entry['specialty_name']) ?></span>
              <h2 class="h4 mb-2"><?= e($entry['first_name'] . ' ' . $entry['last_name']) ?></h2>
              <p class="result-meta mb-3">
                Rank #<?= e((string) $entry['ranking']) ?> in <?= e((string) $entry['list_title']) ?>
                (<?= e((string) $entry['publish_year']) ?>)
              </p>
              <p class="mb-2"><strong>Status:</strong> <?= e((string) $entry['status']) ?></p>
              <p class="mb-2"><strong>City:</strong> <?= e((string) $entry['city']) ?></p>
              <p class="mb-0"><?= e((string) $entry['notes']) ?></p>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>
