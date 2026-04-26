<?php
if (!function_exists('app_base_url')) {
    function app_base_url(): string
    {
        $scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php');

        foreach (['/auth/', '/modules/', '/api/'] as $segment) {
            $position = strpos($scriptName, $segment);
            if ($position !== false) {
                return rtrim(substr($scriptName, 0, $position), '/');
            }
        }

        $directory = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

        return $directory === '/' ? '' : $directory;
    }
}

$appBaseUrl = app_base_url();
$stylesheetPath = __DIR__ . '/../assests/css/styles.css';
$stylesheetVersion = is_file($stylesheetPath) ? (string) filemtime($stylesheetPath) : (string) time();

if (!function_exists('render_site_footer')) {
    function render_site_footer(): string
    {
        $baseUrl = app_base_url();

        return '<footer class="site-footer" role="contentinfo">
  <div class="site-footer__inner">
    <p class="site-footer__title">Appointment Lists Service</p>
    <p class="site-footer__text">
      Coursework public-service application for managing specialties, appointment lists,
      candidates, rankings, member tracking, and JSON API access.
    </p>
    <ul class="site-footer__links">
      <li><a href="' . $baseUrl . '/index.php">Home</a></li>
      <li><a href="' . $baseUrl . '/modules/list.php">Search appointment lists</a></li>
      <li><a href="' . $baseUrl . '/modules/dashboard.php">User dashboard</a></li>
      <li><a href="' . $baseUrl . '/auth/login.php">Sign in</a></li>
      <li><a href="' . $baseUrl . '/api/stats/index.php">API status</a></li>
    </ul>
  </div>
</footer>';
    }
}

if (!function_exists('render_breadcrumbs')) {
    function render_breadcrumbs(): string
    {
        $path = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/';

        if ($path === '/' || str_ends_with($path, '/index.php')) {
            return '';
        }

        $labels = [
            'login.php' => 'Sign in',
            'register.php' => 'Create account',
            'dashboard.php' => 'Dashboard',
            'list.php' => 'Search appointment lists',
        ];

        if (str_contains($path, '/modules/admin/')) {
            $current = 'Administration';
        } elseif (str_contains($path, '/modules/specialties/')) {
            $current = 'Specialties';
        } elseif (str_contains($path, '/modules/lists/')) {
            $current = 'Appointment lists';
        } elseif (str_contains($path, '/modules/candidates/')) {
            $current = 'Candidates';
        } elseif (str_contains($path, '/modules/entries/')) {
            $current = 'Candidate entries';
        } elseif (str_contains($path, '/modules/tracking/')) {
            $current = 'Tracked candidates';
        } else {
            $current = $labels[basename($path)] ?? 'Current page';
        }

        return '<nav class="breadcrumbs" aria-label="Breadcrumb">'
            . '<ol><li><a href="' . app_base_url() . '/index.php">Home</a></li><li aria-current="page">'
            . htmlspecialchars($current, ENT_QUOTES, 'UTF-8')
            . '</li></ol></nav>';
    }
}

ob_start(static function (string $html): string {
    $html = preg_replace(
        '/<body>/',
        '<body><a class="skip-link" href="#main-content">Skip to main content</a>',
        $html,
        1
    ) ?? $html;

    $html = preg_replace(
        '/<div class="app-shell">/',
        '<main id="main-content" class="app-shell">',
        $html,
        1
    ) ?? $html;

    $html = preg_replace(
        '/(<div class="project-topbar">.*?<\/div>\s*)(?=\n\n    <(?:section|div|aside))/s',
        '$1' . render_breadcrumbs(),
        $html,
        1
    ) ?? $html;

    $html = preg_replace(
        '/<\/div>\s*<\/body>/',
        '</main>' . render_site_footer() . '</body>',
        $html,
        1
    ) ?? $html;

    return $html;
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Appointment Lists Service</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= htmlspecialchars($appBaseUrl, ENT_QUOTES, 'UTF-8') ?>/assests/css/styles.css?v=<?= htmlspecialchars($stylesheetVersion, ENT_QUOTES, 'UTF-8') ?>" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
