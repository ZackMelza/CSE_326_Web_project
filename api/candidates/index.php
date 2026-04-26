<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';

$rows = $pdo->query(
    'SELECT id, first_name, last_name, email, city, phone, created_at
     FROM candidates
     ORDER BY last_name ASC, first_name ASC'
)->fetchAll();

api_response(['data' => $rows]);
