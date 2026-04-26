<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';

$rows = $pdo->query(
    'SELECT appointment_lists.id, appointment_lists.title, appointment_lists.publish_year, appointment_lists.status,
            specialties.code AS specialty_code, specialties.name AS specialty_name
     FROM appointment_lists
     INNER JOIN specialties ON specialties.id = appointment_lists.specialty_id
     ORDER BY appointment_lists.publish_year DESC, appointment_lists.title ASC'
)->fetchAll();

api_response(['data' => $rows]);
