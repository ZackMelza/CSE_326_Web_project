<?php
declare(strict_types=1);

function redirect(string $url): never
{
    header("Location: {$url}");
    exit;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function require_auth(): void
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../auth/login.php');
        exit;
    }
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
