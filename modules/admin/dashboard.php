<?php

declare(strict_types=1);

require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../includes/functions.php";
require_once __DIR__ . "/../../includes/header.php";
require_once __DIR__ . "/../../includes/nav.php";

$userRole = ROLE_ADMIN; // simulation
?>

<h1>Admin Dashboard</h1>

<?php
if ($userRole === ROLE_ADMIN) {
    echo "<p>Welcome Admin!</p>";
} elseif ($userRole === ROLE_SUBMIT) {
    echo "<p>You are not allowed here.</p>";
} else {
    echo "<p>Unauthorized access.</p>";
}
?>

<?php require_once __DIR__ . "/../../includes/footer.php"; ?>
