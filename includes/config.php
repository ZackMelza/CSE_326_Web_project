<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'cse326_user');
define('DB_PASS', getenv('DB_PASS') ?: 'StrongPass123!');
define('DB_NAME', getenv('DB_NAME') ?: 'cse326_auth');

if (function_exists('mysqli_report')) {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
}

function get_db_connection(): mysqli
{
    if (!extension_loaded('mysqli')) {
        throw new RuntimeException(
            'MySQL driver is missing. Install/enable php-mysqli (or php-mysql).'
        );
    }

    static $connection = null;

    if ($connection instanceof mysqli) {
        return $connection;
    }

    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $connection->set_charset('utf8mb4');

    return $connection;
}
