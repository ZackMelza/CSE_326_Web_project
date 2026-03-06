<?php

declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}

function post(string $key): string
{
    return isset($_POST[$key]) ? trim((string) $_POST[$key]) : "";
}

function redirectToDashboard(string $role): void
{
    if ($role === ROLE_ADMIN) {
        header("Location: /modules/admin/dashboard.php");
        exit;
    } elseif ($role === ROLE_SUBMIT) {
        header("Location: /modules/submit/dashboard.php");
        exit;
    } elseif ($role === ROLE_SEARCH) {
        header("Location: /modules/search/dashboard.php");
        exit;
    }

    echo "Invalid role.";
    exit;
}
