<?php

declare(strict_types=1);

require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../includes/functions.php";
require_once __DIR__ . "/../../includes/header.php";
require_once __DIR__ . "/../../includes/nav.php";

$userRole = ROLE_SEARCH; // simulation
?>

<h1>Search Dashboard</h1>

<?php
if ($userRole === ROLE_SEARCH) {
    echo "<p>Welcome Search User!</p>";
} elseif ($userRole === ROLE_ADMIN || $userRole === ROLE_SUBMIT) {
    echo "<p>Please use the dashboard assigned to your role.</p>";
} else {
    echo "<p>Unauthorized access.</p>";
}
?>

<?php require_once __DIR__ . "/../../includes/footer.php"; ?>
