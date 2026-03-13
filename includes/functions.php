<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function redirect(string $url): never
{
    header("Location: {$url}");
    exit;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function login_user(int $id, string $username, string $email): void
{
    $_SESSION['user_id'] = $id;
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;
}

function logout_user(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}
