<?php
session_start();

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

require_admin();

$pdo = db();
$errors = [];
$editId = request_id();
$form = ['code' => '', 'name' => '', 'sector' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? 'save');

    if ($action === 'delete') {
        $id = post_int('id');
        $stmt = $pdo->prepare('DELETE FROM specialties WHERE id = :id');
        $stmt->execute(['id' => $id]);
        log_action($pdo, current_user_id(), 'delete', 'specialty', $id);
        set_flash('success', 'Specialty deleted.');
        redirect('dashboard.php');
    }

    $id = post_int('id');
    $form = [
        'code' => strtoupper(post_string('code')),
        'name' => post_string('name'),
        'sector' => post_string('sector'),
    ];

    if ($form['code'] === '' || $form['name'] === '' || $form['sector'] === '') {
        $errors[] = 'All specialty fields are required.';
    }

    if ($errors === []) {
        if ($id > 0) {
            $stmt = $pdo->prepare(
                'UPDATE specialties
                 SET code = :code, name = :name, sector = :sector
                 WHERE id = :id'
            );
            $stmt->execute($form + ['id' => $id]);
            log_action($pdo, current_user_id(), 'update', 'specialty', $id);
            set_flash('success', 'Specialty updated.');
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO specialties (code, name, sector)
                 VALUES (:code, :name, :sector)'
            );
            $stmt->execute($form);
            $newId = (int) $pdo->lastInsertId();
            log_action($pdo, current_user_id(), 'create', 'specialty', $newId);
            set_flash('success', 'Specialty created.');
        }
        redirect('dashboard.php');
    }
}

if ($editId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM specialties WHERE id = :id');
    $stmt->execute(['id' => $editId]);
    $record = $stmt->fetch();
    if ($record) {
        $form = [
            'code' => (string) $record['code'],
            'name' => (string) $record['name'],
            'sector' => (string) $record['sector'],
        ];
    }
}

$flash = get_flash();
$specialties = $pdo->query('SELECT * FROM specialties ORDER BY name')->fetchAll();
?>
<?php require_once __DIR__ . '/../../includes/header.php'; ?>
<body>
<div class="app-shell">
  <div class="app-frame">
    <div class="project-topbar">
      <div class="brand-mark">
        <div class="brand-badge">SP</div>
        <div class="brand-copy">
          <span class="brand-eyebrow">Admin CRUD</span>
          <span class="brand-title">Specialties</span>
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
        <span class="section-label"><?= $editId > 0 ? 'Edit specialty' : 'Create specialty' ?></span>
        <form method="post" action="dashboard.php<?= $editId > 0 ? '?id=' . $editId : '' ?>" class="row g-3">
          <input type="hidden" name="id" value="<?= e((string) $editId) ?>">
          <div class="col-md-4">
            <label for="code" class="form-label">Code</label>
            <input id="code" name="code" class="form-control" value="<?= e($form['code']) ?>" required>
          </div>
          <div class="col-md-4">
            <label for="name" class="form-label">Name</label>
            <input id="name" name="name" class="form-control" value="<?= e($form['name']) ?>" required>
          </div>
          <div class="col-md-4">
            <label for="sector" class="form-label">Sector</label>
            <input id="sector" name="sector" class="form-control" value="<?= e($form['sector']) ?>" required>
          </div>
          <div class="col-12 stack-actions">
            <button type="submit" name="action" value="save" class="btn btn-primary">Save Specialty</button>
            <a class="btn btn-outline-secondary" href="dashboard.php">Reset</a>
          </div>
        </form>
      </section>

      <section class="page-panel dashboard-side">
        <span class="section-label">Current specialties</span>
        <div class="table-responsive">
          <table class="table app-table">
            <thead>
              <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Sector</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($specialties as $specialty): ?>
                <tr>
                  <td><?= e((string) $specialty['code']) ?></td>
                  <td><?= e((string) $specialty['name']) ?></td>
                  <td><?= e((string) $specialty['sector']) ?></td>
                  <td class="table-actions">
                    <a class="btn btn-sm btn-outline-secondary" href="dashboard.php?id=<?= e((string) $specialty['id']) ?>">Edit</a>
                    <form method="post" action="dashboard.php" class="inline-form">
                      <input type="hidden" name="id" value="<?= e((string) $specialty['id']) ?>">
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
