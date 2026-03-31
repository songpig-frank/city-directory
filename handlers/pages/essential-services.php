<?php
/**
 * Public: Essential Services
 */
$emergency = db_query(
    "SELECT l.*, c.icon as category_icon 
     FROM listings l 
     JOIN categories c ON l.category_id = c.id 
     WHERE c.slug = 'emergency-health' AND l.status = 'active'
     ORDER BY l.name ASC"
);

// Fallback if no entries yet
if (empty($emergency)) {
    $emergency = [
        ['name' => 'MDRRMO Tampakan', 'phone' => '0917-XXX-XXXX', 'address' => 'Poblacion', 'category_icon' => 'heart-pulse'],
        ['name' => 'Tampakan Police Station', 'phone' => '0998-XXX-XXXX', 'address' => 'Poblacion', 'category_icon' => 'shield'],
        ['name' => 'Bureau of Fire Protection', 'phone' => '0915-XXX-XXXX', 'address' => 'Poblacion', 'category_icon' => 'flame'],
    ];
}

$title = 'Essential Services & Emergency - ' . config('city');
$content = render('essential-services', [
    'emergency' => $emergency
]);

echo render('layout', [
    'title' => $title,
    'content' => $content
]);
