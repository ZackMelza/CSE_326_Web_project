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
    if (!is_logged_in()) {
        redirect('../auth/login.php');
    }
}

function current_user_id(): int
{
    return (int) ($_SESSION['user_id'] ?? 0);
}

function current_username(): string
{
    return (string) ($_SESSION['username'] ?? 'Guest');
}

function current_role(): string
{
    return (string) ($_SESSION['role'] ?? 'member');
}

function is_admin(): bool
{
    return current_role() === 'admin';
}

function require_admin(): void
{
    require_auth();

    if (!is_admin()) {
        http_response_code(403);
        exit('Forbidden');
    }
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash']) || !is_array($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function post_string(string $key): string
{
    return trim((string) ($_POST[$key] ?? ''));
}

function post_int(string $key): int
{
    return (int) ($_POST[$key] ?? 0);
}

function request_id(string $key = 'id'): int
{
    return (int) ($_GET[$key] ?? 0);
}

function log_action(PDO $pdo, int $userId, string $action, string $entityType, int $entityId): void
{
    $stmt = $pdo->prepare(
        'INSERT INTO audit_logs (user_id, action, entity_type, entity_id)
         VALUES (:user_id, :action, :entity_type, :entity_id)'
    );
    $stmt->execute([
        'user_id' => $userId,
        'action' => $action,
        'entity_type' => $entityType,
        'entity_id' => $entityId,
    ]);
}
