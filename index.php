<?php

declare(strict_types=1);

require_once __DIR__ . "/includes/config.php";
require_once __DIR__ . "/includes/functions.php";
require_once __DIR__ . "/includes/header.php";
require_once __DIR__ . "/includes/nav.php";
?>

<h1>Welcome to <?= e(APP_NAME) ?></h1>
<p>Select module:</p>
<ul>
    <li><a href="/modules/admin/dashboard.php">Admin Module</a></li>
    <li><a href="/modules/submit/dashboard.php">Submit Module</a></li>
    <li><a href="/modules/search/dashboard.php">Search Module</a></li>
</ul>

<?php require_once __DIR__ . "/includes/footer.php"; ?>
