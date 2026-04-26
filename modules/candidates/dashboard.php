<?php
session_start();

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

require_admin();

$pdo = db();
$errors = [];
$editId = request_id();
$form = ['first_name' => '', 'last_name' => '', 'email' => '', 'city' => '', 'phone' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? 'save');

    if ($action === 'delete') {
        $id = post_int('id');
        $stmt = $pdo->prepare('DELETE FROM candidates WHERE id = :id');
        $stmt->execute(['id' => $id]);
        log_action($pdo, current_user_id(), 'delete', 'candidate', $id);
        set_flash('success', 'Candidate deleted.');
        redirect('dashboard.php');
    }

    $id = post_int('id');
    $form = [
        'first_name' => post_string('first_name'),
        'last_name' => post_string('last_name'),
        'email' => post_string('email'),
        'city' => post_string('city'),
        'phone' => post_string('phone'),
    ];

    if (in_array('', $form, true)) {
        $errors[] = 'All candidate fields are required.';
    } elseif (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Candidate email is invalid.';
    }

    if ($errors === []) {
        if ($id > 0) {
            $stmt = $pdo->prepare(
                'UPDATE candidates
                 SET first_name = :first_name, last_name = :last_name, email = :email, city = :city, phone = :phone
                 WHERE id = :id'
            );
            $stmt->execute($form + ['id' => $id]);
            log_action($pdo, current_user_id(), 'update', 'candidate', $id);
            set_flash('success', 'Candidate updated.');
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO candidates (first_name, last_name, email, city, phone)
                 VALUES (:first_name, :last_name, :email, :city, :phone)'
            );
            $stmt->execute($form);
            $newId = (int) $pdo->lastInsertId();
            log_action($pdo, current_user_id(), 'create', 'candidate', $newId);
            set_flash('success', 'Candidate created.');
        }
        redirect('dashboard.php');
    }
}

if ($editId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM candidates WHERE id = :id');
    $stmt->execute(['id' => $editId]);
    $record = $stmt->fetch();
    if ($record) {
        $form = [
            'first_name' => (string) $record['first_name'],
            'last_name' => (string) $record['last_name'],
            'email' => (string) $record['email'],
            'city' => (string) $record['city'],
            'phone' => (string) $record['phone'],
        ];
    }
}

$flash = get_flash();
$candidates = $pdo->query('SELECT * FROM candidates ORDER BY last_name, first_name')->fetchAll();
?>
<?php require_once __DIR__ . '/../../includes/header.php'; ?>
<body>
<div class="app-shell">
  <div class="app-frame">
    <div class="project-topbar">
      <div class="brand-mark">
        <div class="brand-badge">CD</div>
        <div class="brand-copy">
          <span class="brand-eyebrow">Admin CRUD</span>
          <span class="brand-title">Candidates</span>
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
        <span class="section-label"><?= $editId > 0 ? 'Edit candidate' : 'Create candidate' ?></span>
        <form method="post" action="dashboard.php<?= $editId > 0 ? '?id=' . $editId : '' ?>" class="row g-3">
          <input type="hidden" name="id" value="<?= e((string) $editId) ?>">
          <div class="col-md-4">
            <label for="first_name" class="form-label">First name</label>
            <input id="first_name" name="first_name" class="form-control" value="<?= e((string) $form['first_name']) ?>" required>
          </div>
          <div class="col-md-4">
            <label for="last_name" class="form-label">Last name</label>
            <input id="last_name" name="last_name" class="form-control" value="<?= e((string) $form['last_name']) ?>" required>
          </div>
          <div class="col-md-4">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" class="form-control" value="<?= e((string) $form['email']) ?>" required>
          </div>
          <div class="col-md-6">
            <label for="city" class="form-label">City</label>
            <input id="city" name="city" class="form-control" value="<?= e((string) $form['city']) ?>" required>
          </div>
          <div class="col-md-6">
            <label for="phone" class="form-label">Phone</label>
            <input id="phone" name="phone" class="form-control" value="<?= e((string) $form['phone']) ?>" required>
          </div>
          <div class="col-12 stack-actions">
            <button type="submit" name="action" value="save" class="btn btn-primary">Save Candidate</button>
            <a class="btn btn-outline-secondary" href="dashboard.php">Reset</a>
          </div>
        </form>
      </section>

      <section class="page-panel dashboard-side">
        <span class="section-label">Current candidates</span>
        <div class="table-responsive">
          <table class="table app-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>City</th>
                <th>Phone</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($candidates as $candidate): ?>
                <tr>
                  <td><?= e($candidate['first_name'] . ' ' . $candidate['last_name']) ?></td>
                  <td><?= e((string) $candidate['email']) ?></td>
                  <td><?= e((string) $candidate['city']) ?></td>
                  <td><?= e((string) $candidate['phone']) ?></td>
                  <td class="table-actions">
                    <a class="btn btn-sm btn-outline-secondary" href="dashboard.php?id=<?= e((string) $candidate['id']) ?>">Edit</a>
                    <form method="post" action="dashboard.php" class="inline-form">
                      <input type="hidden" name="id" value="<?= e((string) $candidate['id']) ?>">
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
