<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';

$stats = [
    'users' => (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'specialties' => (int) $pdo->query('SELECT COUNT(*) FROM specialties')->fetchColumn(),
    'lists' => (int) $pdo->query('SELECT COUNT(*) FROM appointment_lists')->fetchColumn(),
    'candidates' => (int) $pdo->query('SELECT COUNT(*) FROM candidates')->fetchColumn(),
    'entries' => (int) $pdo->query('SELECT COUNT(*) FROM candidate_list_entries')->fetchColumn(),
    'tracked_candidates' => (int) $pdo->query('SELECT COUNT(*) FROM tracked_candidates')->fetchColumn(),
];

api_response(['data' => $stats]);
