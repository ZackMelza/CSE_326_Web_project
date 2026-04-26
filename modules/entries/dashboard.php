<?php
session_start();

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

require_admin();

$pdo = db();
$errors = [];
$editId = request_id();
$form = ['candidate_id' => 0, 'list_id' => 0, 'ranking' => '', 'status' => 'active', 'notes' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? 'save');

    if ($action === 'delete') {
        $id = post_int('id');
        $stmt = $pdo->prepare('DELETE FROM candidate_list_entries WHERE id = :id');
        $stmt->execute(['id' => $id]);
        log_action($pdo, current_user_id(), 'delete', 'candidate_list_entry', $id);
        set_flash('success', 'Candidate entry deleted.');
        redirect('dashboard.php');
    }

    $id = post_int('id');
    $form = [
        'candidate_id' => post_int('candidate_id'),
        'list_id' => post_int('list_id'),
        'ranking' => post_int('ranking'),
        'status' => post_string('status'),
        'notes' => post_string('notes'),
    ];

    if ($form['candidate_id'] <= 0 || $form['list_id'] <= 0 || $form['ranking'] <= 0 || $form['status'] === '' || $form['notes'] === '') {
        $errors[] = 'All entry fields are required.';
    }

    if ($errors === []) {
        if ($id > 0) {
            $stmt = $pdo->prepare(
                'UPDATE candidate_list_entries
                 SET candidate_id = :candidate_id, list_id = :list_id, ranking = :ranking, status = :status, notes = :notes
                 WHERE id = :id'
            );
            $stmt->execute($form + ['id' => $id]);
            log_action($pdo, current_user_id(), 'update', 'candidate_list_entry', $id);
            set_flash('success', 'Candidate entry updated.');
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO candidate_list_entries (candidate_id, list_id, ranking, status, notes)
                 VALUES (:candidate_id, :list_id, :ranking, :status, :notes)'
            );
            $stmt->execute($form);
            $newId = (int) $pdo->lastInsertId();
            log_action($pdo, current_user_id(), 'create', 'candidate_list_entry', $newId);
            set_flash('success', 'Candidate entry created.');
        }
        redirect('dashboard.php');
    }
}

if ($editId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM candidate_list_entries WHERE id = :id');
    $stmt->execute(['id' => $editId]);
    $record = $stmt->fetch();
    if ($record) {
        $form = [
            'candidate_id' => (int) $record['candidate_id'],
            'list_id' => (int) $record['list_id'],
            'ranking' => (int) $record['ranking'],
            'status' => (string) $record['status'],
            'notes' => (string) $record['notes'],
        ];
    }
}

$flash = get_flash();
$candidates = $pdo->query('SELECT id, first_name, last_name FROM candidates ORDER BY last_name, first_name')->fetchAll();
$lists = $pdo->query('SELECT id, title FROM appointment_lists ORDER BY publish_year DESC, title ASC')->fetchAll();
$entries = $pdo->query(
    'SELECT
        candidate_list_entries.*,
        candidates.first_name,
        candidates.last_name,
        appointment_lists.title AS list_title
     FROM candidate_list_entries
     INNER JOIN candidates ON candidates.id = candidate_list_entries.candidate_id
     INNER JOIN appointment_lists ON appointment_lists.id = candidate_list_entries.list_id
     ORDER BY candidate_list_entries.ranking ASC'
)->fetchAll();
?>
<?php require_once __DIR__ . '/../../includes/header.php'; ?>
<body>
<div class="app-shell">
  <div class="app-frame">
    <div class="project-topbar">
      <div class="brand-mark">
        <div class="brand-badge">EN</div>
        <div class="brand-copy">
          <span class="brand-eyebrow">Admin CRUD</span>
          <span class="brand-title">Candidate List Entries</span>
        </div>
      </div>
      <div class="topbar-links">
        <a class="topbar-link" href="../admin/dashboard.php">Admin</a>
        <a class="topbar-link" href="../dashboard.php">Dashboard</a>
        <a class="topbar-link" href="../../auth/logout.php">Logout</a>
      </div>
    </div>

    <?php if ($flash): ?>
      <div class="alert alert-success mb-4"><?= e((string) $flash['message']) ?></div>
    <?php endif; ?>
    <?php if ($errors !== []): ?>
      <div class="alert alert-danger mb-4"><?= e(implode(' ', $errors)) ?></div>
    <?php endif; ?>

    <div class="crud-layout">
      <section class="page-panel dashboard-hero">
        <span class="section-label"><?= $editId > 0 ? 'Edit entry' : 'Create entry' ?></span>
        <form method="post" action="dashboard.php<?= $editId > 0 ? '?id=' . $editId : '' ?>" class="row g-3">
          <input type="hidden" name="id" value="<?= e((string) $editId) ?>">
          <div class="col-md-4">
            <label for="candidate_id" class="form-label">Candidate</label>
            <select id="candidate_id" name="candidate_id" class="form-select" required>
              <option value="0">Select candidate</option>
              <?php foreach ($candidates as $candidate): ?>
                <option value="<?= e((string) $candidate['id']) ?>" <?= $form['candidate_id'] === (int) $candidate['id'] ? 'selected' : '' ?>>
                  <?= e($candidate['first_name'] . ' ' . $candidate['last_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label for="list_id" class="form-label">Appointment list</label>
            <select id="list_id" name="list_id" class="form-select" required>
              <option value="0">Select list</option>
              <?php foreach ($lists as $list): ?>
                <option value="<?= e((string) $list['id']) ?>" <?= $form['list_id'] === (int) $list['id'] ? 'selected' : '' ?>>
                  <?= e((string) $list['title']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <label for="ranking" class="form-label">Ranking</label>
            <input id="ranking" name="ranking" type="number" class="form-control" value="<?= e((string) $form['ranking']) ?>" required>
          </div>
          <div class="col-md-2">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select" required>
              <?php foreach (['active', 'review', 'archived'] as $value): ?>
                <option value="<?= e($value) ?>" <?= $form['status'] === $value ? 'selected' : '' ?>><?= e(ucfirst($value)) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12">
            <label for="notes" class="form-label">Notes</label>
            <textarea id="notes" name="notes" class="form-control" rows="3" required><?= e((string) $form['notes']) ?></textarea>
          </div>
          <div class="col-12 stack-actions">
            <button type="submit" name="action" value="save" class="btn btn-primary">Save Entry</button>
            <a class="btn btn-outline-secondary" href="dashboard.php">Reset</a>
          </div>
        </form>
      </section>

      <section class="page-panel dashboard-side">
        <span class="section-label">Current entries</span>
        <div class="table-responsive">
          <table class="table app-table">
            <thead>
              <tr>
                <th>Candidate</th>
                <th>List</th>
                <th>Rank</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($entries as $entry): ?>
                <tr>
                  <td><?= e($entry['first_name'] . ' ' . $entry['last_name']) ?></td>
                  <td><?= e((string) $entry['list_title']) ?></td>
                  <td><?= e((string) $entry['ranking']) ?></td>
                  <td><?= e((string) $entry['status']) ?></td>
                  <td class="table-actions">
                    <a class="btn btn-sm btn-outline-secondary" href="dashboard.php?id=<?= e((string) $entry['id']) ?>">Edit</a>
                    <form method="post" action="dashboard.php" class="inline-form">
                      <input type="hidden" name="id" value="<?= e((string) $entry['id']) ?>">
                      <button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </div>
</div>
</body>
</html>
