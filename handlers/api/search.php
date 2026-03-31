<?php
/**
 * API: Search
 */
header('Content-Type: application/json');

$q = $_GET['q'] ?? '';
if (strlen($q) < 2) {
    echo json_encode(['success' => true, 'data' => []]);
    exit;
}

$results = db_query(
    "SELECT id, name, slug, type FROM listings 
     WHERE (name LIKE ? OR description LIKE ? OR address LIKE ?) AND status = 'active'
     LIMIT 10",
    ["%$q%", "%$q%", "%$q%"]
);

$data = array_map(function($r) {
    return [
        'id' => $r['id'],
        'name' => $r['name'],
        'url' => base_url(listing_url($r)),
        'type' => $r['type']
    ];
}, $results);

echo json_encode(['success' => true, 'data' => $data]);
