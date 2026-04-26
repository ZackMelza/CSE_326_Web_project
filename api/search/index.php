<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';

$keyword = trim((string) ($_GET['q'] ?? ''));
$status = trim((string) ($_GET['status'] ?? ''));
$specialtyId = (int) ($_GET['specialty_id'] ?? 0);

$sql = '
    SELECT
        candidate_list_entries.id,
        candidate_list_entries.ranking,
        candidate_list_entries.status,
        candidate_list_entries.notes,
        candidates.first_name,
        candidates.last_name,
        candidates.city,
        appointment_lists.title AS list_title,
        specialties.name AS specialty_name
    FROM candidate_list_entries
    INNER JOIN candidates ON candidates.id = candidate_list_entries.candidate_id
    INNER JOIN appointment_lists ON appointment_lists.id = candidate_list_entries.list_id
    INNER JOIN specialties ON specialties.id = appointment_lists.specialty_id
    WHERE 1 = 1
';

$params = [];

if ($keyword !== '') {
    $sql .= '
        AND (
            candidates.first_name LIKE :keyword
            OR candidates.last_name LIKE :keyword
            OR candidates.city LIKE :keyword
            OR appointment_lists.title LIKE :keyword
            OR specialties.name LIKE :keyword
        )
    ';
    $params['keyword'] = '%' . $keyword . '%';
}

if ($status !== '') {
    $sql .= ' AND candidate_list_entries.status = :status';
    $params['status'] = $status;
}

if ($specialtyId > 0) {
    $sql .= ' AND specialties.id = :specialty_id';
    $params['specialty_id'] = $specialtyId;
}

$sql .= ' ORDER BY candidate_list_entries.ranking ASC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

api_response([
    'filters' => [
        'q' => $keyword,
        'status' => $status,
        'specialty_id' => $specialtyId,
    ],
    'data' => $stmt->fetchAll(),
]);
