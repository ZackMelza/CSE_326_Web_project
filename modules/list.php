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
<body>
<div class="app-shell">
  <div class="app-frame">
    <div class="project-topbar">
      <div class="brand-mark">
        <div class="brand-badge">LS</div>
        <div class="brand-copy">
          <span class="brand-eyebrow">Protected Search</span>
          <span class="brand-title">Project Content List</span>
        </div>
      </div>
      <div class="topbar-links">
        <a class="topbar-link" href="dashboard.php">Dashboard</a>
        <a class="topbar-link" href="../index.php">Home</a>
        <a class="topbar-link" href="../auth/logout.php">Logout</a>
      </div>
    </div>

    <div class="search-layout">
      <section class="page-panel search-panel hero-card">
        <span class="hero-kicker">List Deliverable</span>
        <div class="row g-4 align-items-end">
          <div class="col-lg-7">
            <h1 class="hero-title mb-3">Search project content using a bookmarkable keyword filter.</h1>
            <p class="hero-copy mb-0">
              This page queries the thematic `posts` table with PDO prepared statements and keeps
              the search in the URL through the `GET` method, exactly as required by the assignment.
            </p>
          </div>
          <div class="col-lg-5">
            <form method="get" action="list.php" class="row g-3 align-items-end">
              <div class="col-12">
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
              <div class="col-12 d-grid">
                <button type="submit" class="btn btn-primary">Search</button>
              </div>
            </form>
          </div>
        </div>
      </section>

      <?php if (isset($loadError)): ?>
        <div class="alert alert-danger"><?= e($loadError) ?></div>
      <?php elseif ($posts === []): ?>
        <div class="alert alert-secondary">No matching records were found.</div>
      <?php else: ?>
        <div class="results-grid">
          <?php foreach ($posts as $post): ?>
            <article class="soft-card result-card">
              <span class="badge text-bg-primary mb-3"><?= e((string) $post['category']) ?></span>
              <h2 class="h4 mb-2"><?= e((string) $post['title']) ?></h2>
              <p class="result-meta mb-3">Created by <?= e((string) $post['username']) ?></p>
              <p class="mb-0"><?= e((string) $post['summary']) ?></p>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>
