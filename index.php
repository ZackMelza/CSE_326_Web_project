<?php
session_start();

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

try {
    $pdo = db();
    $stats = [
        'specialties' => (int) $pdo->query('SELECT COUNT(*) FROM specialties')->fetchColumn(),
        'lists' => (int) $pdo->query('SELECT COUNT(*) FROM appointment_lists')->fetchColumn(),
        'candidates' => (int) $pdo->query('SELECT COUNT(*) FROM candidates')->fetchColumn(),
        'entries' => (int) $pdo->query('SELECT COUNT(*) FROM candidate_list_entries')->fetchColumn(),
    ];
} catch (Throwable $exception) {
    $stats = [
        'specialties' => 0,
        'lists' => 0,
        'candidates' => 0,
        'entries' => 0,
    ];
}
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>
<body>
<div class="app-shell">
  <div class="app-frame">
    <div class="project-topbar">
      <div class="brand-mark">
        <div class="brand-badge">EEY</div>
        <div class="brand-copy">
          <span class="brand-eyebrow">gov.cy coursework service</span>
          <span class="brand-title">Educational Service Commission</span>
        </div>
      </div>
      <div class="topbar-links">
        <?php if (is_logged_in()): ?>
          <a class="topbar-link" href="modules/dashboard.php">My account</a>
          <a class="topbar-link" href="modules/list.php">Search lists</a>
          <?php if (is_admin()): ?>
            <a class="topbar-link" href="modules/admin/dashboard.php">Administration</a>
          <?php endif; ?>
          <a class="topbar-link" href="auth/logout.php">Sign out</a>
        <?php else: ?>
          <a class="topbar-link" href="auth/login.php">Sign in</a>
          <a class="topbar-link" href="auth/register.php">Create account</a>
        <?php endif; ?>
      </div>
    </div>

    <section class="page-panel search-panel hero-card mb-4">
      <span class="hero-kicker">Επιτροπή Εκπαιδευτικής Υπηρεσίας</span>
      <h1 class="hero-title">Educational Service Commission</h1>
      <p class="hero-copy">
        Access services and information for appointment lists, candidate rankings, specialties,
        announcements, and account-based tracking.
      </p>

      <div class="feature-strip">
        <div class="feature-chip">
          <strong><a href="modules/list.php">Search appointment lists</a></strong>
          <span>Find candidates by name, city, specialty, appointment list, or status.</span>
        </div>
        <div class="feature-chip">
          <strong><a href="modules/tracking/dashboard.php">Track candidates</a></strong>
          <span>Save candidates to your account and add a short tracking note.</span>
        </div>
        <div class="feature-chip">
          <strong><a href="modules/admin/dashboard.php">Administration</a></strong>
          <span>Authorised staff can maintain specialties, lists, candidates, and rankings.</span>
        </div>
      </div>
    </section>

    <section class="eey-layout">
      <div class="page-panel dashboard-hero">
        <span class="section-label">Services</span>
        <div class="section-heading-row">
          <h2 class="h3 mb-0">Available services</h2>
          <a href="modules/list.php">View search service</a>
        </div>

        <div class="service-list">
          <article class="service-item">
            <h3><a href="modules/list.php">Search appointment-list entries</a></h3>
            <p>Browse ranked candidate entries using keyword, specialty, list, and status filters.</p>
          </article>
          <article class="service-item">
            <h3><a href="modules/tracking/dashboard.php">Track candidates</a></h3>
            <p>Keep a personal watchlist of candidates and add notes linked to your account.</p>
          </article>
          <article class="service-item">
            <h3><a href="modules/admin/dashboard.php">Manage service data</a></h3>
            <p>Authorised staff can maintain specialties, appointment lists, candidates, and rankings.</p>
          </article>
          <article class="service-item">
            <h3><a href="api/stats/index.php">Access service API</a></h3>
            <p>View read-only JSON data for statistics, lists, candidates, entries, and search results.</p>
          </article>
        </div>
      </div>

      <aside class="page-panel dashboard-side">
        <span class="section-label">Calendar</span>
        <div class="calendar-list">
          <article class="calendar-item">
            <time datetime="2026-04-26"><span>26</span>Apr</time>
            <p>Appointment-list search service available for project demonstration.</p>
          </article>
          <article class="calendar-item">
            <time datetime="2026-04-27"><span>27</span>Apr</time>
            <p>Candidate tracking and administration workflow review.</p>
          </article>
          <article class="calendar-item">
            <time datetime="2026-04-30"><span>30</span>Apr</time>
            <p>Final database and API verification deadline.</p>
          </article>
        </div>
      </aside>
    </section>

    <section class="eey-layout mt-4">
      <div class="page-panel dashboard-hero">
        <span class="section-label">Announcements</span>
        <div class="section-heading-row">
          <h2 class="h3 mb-0">Latest announcements</h2>
          <a href="modules/search/dashboard.php">View search information</a>
        </div>
        <div class="announcement-list">
          <article class="announcement-item">
            <time datetime="2026-04-26">26 April 2026</time>
            <h3><a href="modules/list.php">Appointment-list search and filtering are available</a></h3>
            <p>Users can search ranked candidate entries by name, city, specialty, list, and status.</p>
          </article>
          <article class="announcement-item">
            <time datetime="2026-04-24">24 April 2026</time>
            <h3><a href="modules/admin/dashboard.php">Administration tools updated</a></h3>
            <p>Authorised staff can manage specialties, lists, candidates, and ranking entries.</p>
          </article>
          <article class="announcement-item">
            <time datetime="2026-04-24">24 April 2026</time>
            <h3><a href="api/search/index.php?q=maria&status=active">API search endpoint ready</a></h3>
            <p>Search results can be returned as JSON for Postman or browser testing.</p>
          </article>
        </div>
      </div>

      <aside class="page-panel dashboard-side">
        <span class="section-label">Service summary</span>
        <div class="metric-grid">
          <div class="metric-box">
            <strong><?= e((string) $stats['specialties']) ?></strong>
            <span>specialties</span>
          </div>
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
        </div>
      </aside>
    </section>

    <section class="page-panel search-panel mt-4">
      <span class="section-label">Useful links</span>
      <div class="useful-links">
        <a href="modules/list.php">Search appointment lists</a>
        <a href="modules/tracking/dashboard.php">Tracked candidates</a>
        <a href="modules/admin/dashboard.php">Administration dashboard</a>
        <a href="postman/CSE_326_Web_project.postman_collection.json">Postman collection</a>
      </div>
    </section>

    <section class="page-panel search-panel mt-4">
      <span class="section-label">Help and contact</span>
      <div class="contact-grid">
        <div>
          <h2 class="h4">Need help using the service?</h2>
          <p class="hero-copy mb-0">
            Sign in with one of the demo accounts, open the search service, or contact the project administrator
            during the presentation for assistance with access and test data.
          </p>
        </div>
        <dl class="contact-details">
          <div>
            <dt>Staff account</dt>
            <dd>admin@example.com / Password123!</dd>
          </div>
          <div>
            <dt>Member account</dt>
            <dd>writer@example.com / Password123!</dd>
          </div>
          <div>
            <dt>Support email</dt>
            <dd>info@eey.gov.cy</dd>
          </div>
        </dl>
      </div>
    </section>
  </div>
</div>
</body>
</html>
