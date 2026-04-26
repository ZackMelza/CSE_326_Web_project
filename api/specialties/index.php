<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';

$rows = $pdo->query('SELECT id, code, name, sector, created_at FROM specialties ORDER BY name')->fetchAll();
api_response(['data' => $rows]);
