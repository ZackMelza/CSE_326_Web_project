<?php

declare(strict_types=1);

require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../includes/functions.php";
require_once __DIR__ . "/../../includes/header.php";
require_once __DIR__ . "/../../includes/nav.php";

$userRole = ROLE_SUBMIT; // simulation
?>

<h1>Submit Dashboard</h1>

<?php
if ($userRole === ROLE_SUBMIT) {
    echo "<p>Welcome Submit User!</p>";
} elseif ($userRole === ROLE_ADMIN) {
    echo "<p>Admin users should use the admin dashboard.</p>";
} else {
    echo "<p>Unauthorized access.</p>";
}
?>

<?php require_once __DIR__ . "/../../includes/footer.php"; ?>
