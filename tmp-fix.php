<?php
error_reporting(0);
require_once "/var/www/wordpress/wp-load.php";
global $wpdb;

// 1. Show all location posts with full details
echo "=== TUTTI I POST LOCATION ===\n";
$locations = get_posts(['post_type' => 'location', 'numberposts' => -1, 'post_status' => 'any']);
foreach ($locations as $loc) {
    echo "\n--- #{$loc->ID} {$loc->post_title} [{$loc->post_status}] ---\n";
    echo "Content: " . substr($loc->post_content, 0, 300) . "\n";
    // Categories
    $cats = wp_get_object_terms($loc->ID, 'location_category');
    if ($cats && !is_wp_error($cats)) {
        $cat_names = array_map(function($c) { return $c->name; }, $cats);
        echo "Categories: " . implode(', ', $cat_names) . "\n";
    }
    // All custom meta
    $meta = get_post_meta($loc->ID);
    foreach ($meta as $k => $v) {
        if (strpos($k, '_wp_') === 0 || strpos($k, '_edit_') === 0) continue;
        $val = $v[0] ?? '';
        if (strlen($val) > 3 && !is_serialized($val)) {
            echo "  meta $k: " . substr($val, 0, 200) . "\n";
        }
    }
}

// 2. Show location_category terms
echo "\n\n=== LOCATION CATEGORIES ===\n";
$terms = get_terms(['taxonomy' => 'location_category', 'hide_empty' => false]);
foreach ($terms as $t) {
    echo "  #{$t->term_id} {$t->name} (slug: {$t->slug}, count: {$t->count})\n";
}

// 3. Search for "Stunning loft" in all posts
echo "\n\n=== RICERCA 'Stunning' ===\n";
$found = $wpdb->get_results("SELECT ID, post_title, post_type FROM {$wpdb->posts} WHERE post_content LIKE '%Stunning%'", ARRAY_A);
foreach ($found as $p) {
    echo "  #{$p['ID']} [{$p['post_type']}] {$p['post_title']}\n";
}
// Also in meta
$found_meta = $wpdb->get_results("SELECT post_id, meta_key, SUBSTRING(meta_value, 1, 300) as val FROM {$wpdb->postmeta} WHERE meta_value LIKE '%Stunning%'", ARRAY_A);
foreach ($found_meta as $m) {
    echo "  meta post #{$m['post_id']} key={$m['meta_key']}: {$m['val']}\n";
}
