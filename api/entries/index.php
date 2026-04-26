<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';

$rows = $pdo->query(
    'SELECT candidate_list_entries.id, candidate_list_entries.ranking, candidate_list_entries.status,
            candidates.first_name, candidates.last_name,
            appointment_lists.title AS list_title,
            specialties.name AS specialty_name
     FROM candidate_list_entries
     INNER JOIN candidates ON candidates.id = candidate_list_entries.candidate_id
     INNER JOIN appointment_lists ON appointment_lists.id = candidate_list_entries.list_id
     INNER JOIN specialties ON specialties.id = appointment_lists.specialty_id
     ORDER BY candidate_list_entries.ranking ASC'
)->fetchAll();

api_response(['data' => $rows]);
