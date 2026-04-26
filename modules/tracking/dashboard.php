<?php
session_start();

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

require_auth();

$loadError = null;
$pdo = db();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? 'save');

    if ($action === 'delete') {
        $id = post_int('id');
        $stmt = $pdo->prepare('DELETE FROM tracked_candidates WHERE id = :id AND user_id = :user_id');
        $stmt->execute(['id' => $id, 'user_id' => current_user_id()]);
        set_flash('success', 'Tracked candidate removed.');
        redirect('dashboard.php');
    }

    $candidateId = post_int('candidate_id');
    $label = post_string('label');

    if ($candidateId <= 0 || $label === '') {
        $errors[] = 'Candidate and label are required.';
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO tracked_candidates (user_id, candidate_id, label, is_active)
             VALUES (:user_id, :candidate_id, :label, 1)
             ON DUPLICATE KEY UPDATE label = VALUES(label), is_active = 1'
        );
        $stmt->execute([
            'user_id' => current_user_id(),
            'candidate_id' => $candidateId,
            'label' => $label,
        ]);
        set_flash('success', 'Tracked candidate saved.');
        redirect('dashboard.php');
    }
}

$flash = get_flash();

try {
    $candidates = $pdo->query('SELECT id, first_name, last_name FROM candidates ORDER BY last_name, first_name')->fetchAll();
    $tracked = $pdo->prepare(
        'SELECT tracked_candidates.id, tracked_candidates.label, tracked_candidates.is_active,
                candidates.first_name, candidates.last_name, candidates.city
         FROM tracked_candidates
         INNER JOIN candidates ON candidates.id = tracked_candidates.candidate_id
         WHERE tracked_candidates.user_id = :user_id
         ORDER BY tracked_candidates.created_at DESC'
    );
    $tracked->execute(['user_id' => current_user_id()]);
    $trackedCandidates = $tracked->fetchAll();
} catch (Throwable $exception) {
    $candidates = [];
    $trackedCandidates = [];
    $loadError = 'Candidate tracking is not available until the updated database schema and seed data are imported.';
}
?>
<?php require_once __DIR__ . '/../../includes/header.php'; ?>
<body>
<div class="app-shell">
  <div class="app-frame">
    <div class="project-topbar">
      <div class="brand-mark">
        <div class="brand-badge">TR</div>
        <div class="brand-copy">
          <span class="brand-eyebrow">Member Module</span>
          <span class="brand-title">Tracked Candidates</span>
        </div>
      </div>
      <div class="topbar-links">
        <a class="topbar-link" href="../dashboard.php">Dashboard</a>
        <a class="topbar-link" href="../list.php">Search</a>
        <?php if (is_admin()): ?>
          <a class="topbar-link" href="../admin/dashboard.php">Admin</a>
        <?php endif; ?>
        <a class="topbar-link" href="../../auth/logout.php">Logout</a>
      </div>
    </div>

    <?php if ($flash): ?>
      <div class="alert alert-success mb-4"><?= e((string) $flash['message']) ?></div>
    <?php endif; ?>
    <?php if ($errors !== []): ?>
      <div class="alert alert-danger mb-4"><?= e(implode(' ', $errors)) ?></div>
    <?php endif; ?>
    <?php if ($loadError !== null): ?>
      <div class="alert alert-danger mb-4">
        <?= e($loadError) ?>
      </div>
    <?php endif; ?>

    <div class="crud-layout">
      <section class="page-panel dashboard-hero">
        <span class="section-label">Track candidate</span>
        <form method="post" action="dashboard.php" class="row g-3">
          <div class="col-md-5">
            <label for="candidate_id" class="form-label">Candidate</label>
            <select id="candidate_id" name="candidate_id" class="form-select" required>
              <option value="0">Select candidate</option>
              <?php foreach ($candidates as $candidate): ?>
                <option value="<?= e((string) $candidate['id']) ?>">
                  <?= e($candidate['first_name'] . ' ' . $candidate['last_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-7">
            <label for="label" class="form-label">Tracking note</label>
            <input id="label" name="label" class="form-control" placeholder="High priority, waiting for review, etc." required>
          </div>
          <div class="col-12">
            <button type="submit" name="action" value="save" class="btn btn-primary" <?= $loadError !== null ? 'disabled' : '' ?>>Save Tracking Entry</button>
          </div>
        </form>
      </section>

      <section class="page-panel dashboard-side">
        <span class="section-label">My watchlist</span>
        <div class="dashboard-list">
          <?php if ($trackedCandidates === []): ?>
            <div class="dashboard-list-item">
              <strong>No tracked candidates yet</strong>
              Add one from the form to create your member-level CRUD record.
            </div>
          <?php else: ?>
            <?php foreach ($trackedCandidates as $trackedCandidate): ?>
              <div class="dashboard-list-item">
                <strong><?= e($trackedCandidate['first_name'] . ' ' . $trackedCandidate['last_name']) ?></strong>
                <?= e((string) $trackedCandidate['city']) ?> • <?= e((string) $trackedCandidate['label']) ?>
                <form method="post" action="dashboard.php" class="mt-3">
                  <input type="hidden" name="id" value="<?= e((string) $trackedCandidate['id']) ?>">
                  <button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger">Remove</button>
                </form>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </section>
    </div>
  </div>
</div>
</body>
</html>
