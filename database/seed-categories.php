<?php
/**
 * CityDirectory — Category Seeder
 * Run once per city to populate categories.
 * 
 * Usage: php database/seed-categories.php [tampakan|gensan]
 */

require_once __DIR__ . '/../includes/config-loader.php';
require_once __DIR__ . '/../includes/db.php';

$profile = $argv[1] ?? 'tampakan';

// Master category list — enable/disable per city profile
$categories = [
    // Business categories
    ['Restaurants & Eateries',  'restaurants-eateries',  'business', '🍽️', 1, true, true],
    ['Cafes & Milk Tea',        'cafes-milk-tea',        'business', '☕', 2, true, true],
    ['Bakeries',                'bakeries',              'business', '🍞', 3, true, true],
    ['Street Food & Carinderias','street-food-carinderias','business','🥘', 4, true, true],
    ['Bars & Nightlife',        'bars-nightlife',        'business', '🍻', 5, false, true],
    ['Hotels & Resorts',        'hotels-resorts',        'business', '🏨', 6, true, true],
    ['Vacation Rentals',        'vacation-rentals',      'business', '🏡', 7, true, true],
    ['Barbershops & Salons',    'barbershops-salons',    'business', '💇', 8, true, true],
    ['Spas & Wellness',         'spas-wellness',         'business', '💆', 9, false, true],
    ['Banks & Financial',       'banks-financial',       'business', '🏦', 10, true, true],
    ['Pawnshops & Remittance',  'pawnshops-remittance',  'business', '💰', 11, true, true],
    ['Mechanics & Auto Repair', 'mechanics-auto-repair', 'business', '🔧', 12, true, true],
    ['Gas Stations',            'gas-stations',          'business', '⛽', 13, true, true],
    ['Grocery & Sari-Sari',     'grocery-sari-sari',     'business', '🛒', 14, true, true],
    ['Hardware & Construction', 'hardware-construction', 'business', '🔨', 15, true, true],
    ['Clinics & Hospitals',     'clinics-hospitals',     'business', '🏥', 16, true, true],
    ['Pharmacies',              'pharmacies',            'business', '💊', 17, true, true],
    ['Dental Clinics',          'dental-clinics',        'business', '🦷', 18, true, true],
    ['Schools & Tutorials',     'schools-tutorials',     'business', '🎓', 19, true, true],
    ['Churches & Worship',      'churches-worship',      'business', '⛪', 20, true, true],
    ['Government Offices',      'government-offices',    'business', '🏛️', 21, true, true],
    ['Legal Services',          'legal-services',        'business', '⚖️', 22, false, true],
    ['IT & Computer Shops',     'it-computer-shops',     'business', '💻', 23, true, true],
    ['Printing & Photo',        'printing-photo',        'business', '🖨️', 24, true, true],
    ['Laundry Services',        'laundry-services',      'business', '👔', 25, true, true],
    ['Pet Shops & Vet',         'pet-shops-vet',         'business', '🐕', 26, false, true],
    ['Fitness & Gyms',          'fitness-gyms',          'business', '💪', 27, false, true],
    ['Transport & Logistics',   'transport-logistics',   'business', '🚛', 28, false, true],
    ['Co-working Spaces',       'co-working-spaces',     'business', '🏢', 29, false, true],
    ['Real Estate & Property',  'real-estate-property',  'business', '🏠', 30, true, true],
    ['Farm Supplies & Agri',    'farm-supplies-agri',    'business', '🌾', 31, true, true],
    ['Funeral Services',        'funeral-services',      'business', '⚱️', 32, true, true],
    ['Water Refilling',         'water-refilling',       'business', '💧', 33, true, true],

    // Tourism categories
    ['Waterfalls',              'waterfalls',            'tourism', '🌊', 1, true, true],
    ['Farms & Agri-Tourism',    'farms-agri-tourism',    'tourism', '🌿', 2, true, true],
    ['View Decks & Scenic',     'view-decks-scenic',     'tourism', '🏔️', 3, true, true],
    ['Springs & Resorts',       'springs-resorts',       'tourism', '♨️', 4, true, true],
    ['Parks & Nature',          'parks-nature',          'tourism', '🌳', 5, true, true],
    ['Caves & Adventures',      'caves-adventures',      'tourism', '⛰️', 6, true, true],
    ['Cultural & Heritage',     'cultural-heritage',     'tourism', '🏛️', 7, true, true],
    ['Food Tours',              'food-tours',            'tourism', '🍢', 8, true, true],

    // Community / Creators
    ['Creative Content & Vloggers','creative-vloggers',  'creator', '📹', 1, true, true],
];

// column indexes: 0=name, 1=slug, 2=type, 3=icon, 4=sort, 5=tampakan, 6=gensan
$profile_idx = $profile === 'gensan' ? 6 : 5;

$inserted = 0;
foreach ($categories as $cat) {
    if (!$cat[$profile_idx]) continue;

    $exists = db_value("SELECT COUNT(*) FROM categories WHERE slug = ?", [$cat[1]]);
    if ($exists > 0) continue;

    db_execute(
        "INSERT INTO categories (name, slug, type, icon, sort_order, is_active) VALUES (?, ?, ?, ?, ?, 1)",
        [$cat[0], $cat[1], $cat[2], $cat[3], $cat[4]]
    );
    $inserted++;
    echo "  ✓ {$cat[0]}\n";
}

echo "\nSeeded {$inserted} categories for {$profile}\n";
