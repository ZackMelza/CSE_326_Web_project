<?php
session_start();

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

require_admin();

$pdo = db();
$errors = [];
$editId = request_id();
$form = ['specialty_id' => 0, 'title' => '', 'publish_year' => date('Y'), 'status' => 'draft'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? 'save');

    if ($action === 'delete') {
        $id = post_int('id');
        $stmt = $pdo->prepare('DELETE FROM appointment_lists WHERE id = :id');
        $stmt->execute(['id' => $id]);
        log_action($pdo, current_user_id(), 'delete', 'appointment_list', $id);
        set_flash('success', 'Appointment list deleted.');
        redirect('dashboard.php');
    }

    $id = post_int('id');
    $form = [
        'specialty_id' => post_int('specialty_id'),
        'title' => post_string('title'),
        'publish_year' => post_int('publish_year'),
        'status' => post_string('status'),
    ];

    if ($form['specialty_id'] <= 0 || $form['title'] === '' || $form['publish_year'] <= 0 || $form['status'] === '') {
        $errors[] = 'All appointment list fields are required.';
    }

    if ($errors === []) {
        if ($id > 0) {
            $stmt = $pdo->prepare(
                'UPDATE appointment_lists
                 SET specialty_id = :specialty_id, title = :title, publish_year = :publish_year, status = :status
                 WHERE id = :id'
            );
            $stmt->execute($form + ['id' => $id]);
            log_action($pdo, current_user_id(), 'update', 'appointment_list', $id);
            set_flash('success', 'Appointment list updated.');
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO appointment_lists (specialty_id, title, publish_year, status)
                 VALUES (:specialty_id, :title, :publish_year, :status)'
            );
            $stmt->execute($form);
            $newId = (int) $pdo->lastInsertId();
            log_action($pdo, current_user_id(), 'create', 'appointment_list', $newId);
            set_flash('success', 'Appointment list created.');
        }
        redirect('dashboard.php');
    }
}

if ($editId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM appointment_lists WHERE id = :id');
    $stmt->execute(['id' => $editId]);
    $record = $stmt->fetch();
    if ($record) {
        $form = [
            'specialty_id' => (int) $record['specialty_id'],
            'title' => (string) $record['title'],
            'publish_year' => (int) $record['publish_year'],
            'status' => (string) $record['status'],
        ];
    }
}

$flash = get_flash();
$specialties = $pdo->query('SELECT id, name FROM specialties ORDER BY name')->fetchAll();
$lists = $pdo->query(
    'SELECT appointment_lists.*, specialties.name AS specialty_name
     FROM appointment_lists
     INNER JOIN specialties ON specialties.id = appointment_lists.specialty_id
     ORDER BY appointment_lists.publish_year DESC, appointment_lists.title ASC'
)->fetchAll();
?>
<?php require_once __DIR__ . '/../../includes/header.php'; ?>
<body>
<div class="app-shell">
  <div class="app-frame">
    <div class="project-topbar">
      <div class="brand-mark">
        <div class="brand-badge">AL</div>
        <div class="brand-copy">
          <span class="brand-eyebrow">Admin CRUD</span>
          <span class="brand-title">Appointment Lists</span>
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
        <span class="section-label"><?= $editId > 0 ? 'Edit list' : 'Create list' ?></span>
        <form method="post" action="dashboard.php<?= $editId > 0 ? '?id=' . $editId : '' ?>" class="row g-3">
          <input type="hidden" name="id" value="<?= e((string) $editId) ?>">
          <div class="col-md-4">
            <label for="specialty_id" class="form-label">Specialty</label>
            <select id="specialty_id" name="specialty_id" class="form-select" required>
              <option value="0">Select specialty</option>
              <?php foreach ($specialties as $specialty): ?>
                <option value="<?= e((string) $specialty['id']) ?>" <?= $form['specialty_id'] === (int) $specialty['id'] ? 'selected' : '' ?>>
                  <?= e((string) $specialty['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label for="title" class="form-label">Title</label>
            <input id="title" name="title" class="form-control" value="<?= e((string) $form['title']) ?>" required>
          </div>
          <div class="col-md-2">
            <label for="publish_year" class="form-label">Year</label>
            <input id="publish_year" name="publish_year" type="number" class="form-control" value="<?= e((string) $form['publish_year']) ?>" required>
          </div>
          <div class="col-md-2">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select" required>
              <?php foreach (['draft', 'review', 'published', 'archived'] as $value): ?>
                <option value="<?= e($value) ?>" <?= $form['status'] === $value ? 'selected' : '' ?>><?= e(ucfirst($value)) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12 stack-actions">
            <button type="submit" name="action" value="save" class="btn btn-primary">Save List</button>
            <a class="btn btn-outline-secondary" href="dashboard.php">Reset</a>
          </div>
        </form>
      </section>

      <section class="page-panel dashboard-side">
        <span class="section-label">Current lists</span>
        <div class="table-responsive">
          <table class="table app-table">
            <thead>
              <tr>
                <th>Title</th>
                <th>Specialty</th>
                <th>Year</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($lists as $list): ?>
                <tr>
                  <td><?= e((string) $list['title']) ?></td>
                  <td><?= e((string) $list['specialty_name']) ?></td>
                  <td><?= e((string) $list['publish_year']) ?></td>
                  <td><?= e((string) $list['status']) ?></td>
                  <td class="table-actions">
                    <a class="btn btn-sm btn-outline-secondary" href="dashboard.php?id=<?= e((string) $list['id']) ?>">Edit</a>
                    <form method="post" action="dashboard.php" class="inline-form">
                      <input type="hidden" name="id" value="<?= e((string) $list['id']) ?>">
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
