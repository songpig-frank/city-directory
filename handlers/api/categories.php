<?php
/**
 * API: Categories
 */
header('Content-Type: application/json');

$type = $_GET['type'] ?? '';
$query = "SELECT * FROM categories";
$params = [];

if ($type) {
    $query .= " WHERE type = ?";
    $params[] = $type;
}

$query .= " ORDER BY sort_order ASC, name ASC";

$categories = db_query($query, $params);

echo json_encode(['success' => true, 'data' => $categories]);
