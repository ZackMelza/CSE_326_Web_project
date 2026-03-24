<?php
session_start();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$keyword = trim($_GET['keyword'] ?? '');

try {
    $pdo = db();

    if ($keyword === '') {
        $stmt = $pdo->prepare(
            'SELECT posts.id, posts.title, posts.category, posts.summary, users.username
             FROM posts
             INNER JOIN users ON users.id = posts.user_id
             ORDER BY posts.created_at DESC'
        );
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare(
            'SELECT posts.id, posts.title, posts.category, posts.summary, users.username
             FROM posts
             INNER JOIN users ON users.id = posts.user_id
             WHERE posts.title LIKE :keyword
                OR posts.category LIKE :keyword
                OR posts.summary LIKE :keyword
             ORDER BY posts.created_at DESC'
        );
        $stmt->execute(['keyword' => '%' . $keyword . '%']);
    }

    $posts = $stmt->fetchAll();
} catch (Throwable $exception) {
    $posts = [];
    $loadError = 'Unable to load content right now.';
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<body class="bg-light">
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
      <h1 class="h3 mb-1">Project Content List</h1>
      <p class="text-muted mb-0">Searchable protected list built with PDO and prepared statements.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      <a class="btn btn-outline-secondary" href="dashboard.php">Dashboard</a>
      <a class="btn btn-outline-danger" href="../auth/logout.php">Logout</a>
    </div>
  </div>

  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <form method="get" action="list.php" class="row g-3 align-items-end">
        <div class="col-md-9">
          <label for="keyword" class="form-label">Keyword Search</label>
          <input
            id="keyword"
            name="keyword"
            type="text"
            class="form-control"
            placeholder="Search by title, category, or summary"
            value="<?= e($keyword) ?>"
          >
        </div>
        <div class="col-md-3 d-grid">
          <button type="submit" class="btn btn-primary">Search</button>
        </div>
      </form>
    </div>
  </div>

  <?php if (isset($loadError)): ?>
    <div class="alert alert-danger"><?= e($loadError) ?></div>
  <?php elseif ($posts === []): ?>
    <div class="alert alert-secondary">No matching records were found.</div>
  <?php else: ?>
    <div class="row g-4">
      <?php foreach ($posts as $post): ?>
        <div class="col-md-6 col-lg-4">
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              <span class="badge text-bg-primary mb-2"><?= e((string) $post['category']) ?></span>
              <h2 class="h5"><?= e((string) $post['title']) ?></h2>
              <p class="text-muted small mb-2">Created by <?= e((string) $post['username']) ?></p>
              <p class="mb-0"><?= e((string) $post['summary']) ?></p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
