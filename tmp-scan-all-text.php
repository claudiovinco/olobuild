<?php
error_reporting(0);
require_once "/var/www/wordpress/wp-load.php";
global $wpdb;

// 1. Tutti i meta delle baite che contengono testo
echo "=== META TESTUALE DELLE BAITE ===\n";
$baite = get_posts(['post_type' => 'olo_service', 'numberposts' => -1, 'post_status' => 'publish']);
$all_meta_texts = [];

foreach ($baite as $b) {
    $lang = get_post_meta($b->ID, '_olo_lang_lang', true);
    if ($lang) continue; // skip copie

    $meta = get_post_meta($b->ID);
    foreach ($meta as $key => $values) {
        if (strpos($key, '_olo_lang') === 0) continue;
        if (strpos($key, '_wp_') === 0) continue;
        if (strpos($key, '_edit_') === 0) continue;
        if (strpos($key, '_location') === 0) continue;
        if (strpos($key, '_rental') === 0) continue;
        $val = $values[0] ?? '';
        if (!is_string($val) || strlen($val) < 3) continue;
        if (is_numeric($val)) continue;
        if (preg_match('/^https?:\/\//', $val)) continue;
        if (preg_match('/^[0-9:.\-]+$/', $val)) continue;
        if (preg_match('/^(available|unavailable|wifi|no_smoking|parking|field_)/', $val)) continue;
        // Testo reale
        if (!isset($all_meta_texts[$key])) $all_meta_texts[$key] = [];
        $all_meta_texts[$key][$val] = true;
    }
}

foreach ($all_meta_texts as $key => $vals) {
    echo "\n  meta '$key':\n";
    foreach (array_keys($vals) as $v) {
        echo "    - $v\n";
    }
}

// 2. Scan olo-booking PHP files per stringhe hardcoded
echo "\n\n=== STRINGHE HARDCODED IN OLO-BOOKING ===\n";
$booking_path = '/var/www/wordpress/wp-content/plugins/olo-booking/';
if (is_dir($booking_path)) {
    $files = glob($booking_path . 'includes/*.php');
    $files[] = $booking_path . 'olo-booking.php';
    foreach ($files as $file) {
        if (!file_exists($file)) continue;
        $content = file_get_contents($file);
        // Find Italian strings in quotes
        preg_match_all("/['\"]([A-ZÀ-Ú][a-zà-ú]+(?: [a-zà-ú']+){1,}[.!?]?)['\"]/" , $content, $matches);
        if (!empty($matches[1])) {
            echo "\n  " . basename($file) . ":\n";
            $unique = array_unique($matches[1]);
            foreach ($unique as $m) {
                if (strlen($m) > 5) echo "    - $m\n";
            }
        }
    }
    // Also check JS
    $js_files = glob($booking_path . 'assets/js/*.js');
    foreach ($js_files as $file) {
        if (!file_exists($file)) continue;
        $content = file_get_contents($file);
        preg_match_all("/['\"]([A-ZÀ-Ú][a-zà-ú]+(?: [a-zà-ú']+){1,}[.!?]?)['\"]/" , $content, $matches);
        if (!empty($matches[1])) {
            echo "\n  " . basename($file) . ":\n";
            $unique = array_unique($matches[1]);
            foreach ($unique as $m) {
                if (strlen($m) > 5) echo "    - $m\n";
            }
        }
    }
} else {
    echo "  olo-booking non trovato in $booking_path\n";
}

// 3. Scan the actual rendered output of a baita page
echo "\n\n=== REGOLE BAITE (rental_rules meta) ===\n";
foreach ($baite as $b) {
    $lang = get_post_meta($b->ID, '_olo_lang_lang', true);
    if ($lang) continue;
    $rules = get_post_meta($b->ID, 'rental_rules', true);
    if ($rules) {
        echo "  #{$b->ID} {$b->post_title}: $rules\n";
    }
}

// 4. Scan olo-booking frontend class for hardcoded text
echo "\n\n=== OLO-BOOKING FRONTEND RENDER ===\n";
$frontend_file = $booking_path . 'includes/class-frontend.php';
if (file_exists($frontend_file)) {
    $content = file_get_contents($frontend_file);
    // Find all Italian text strings
    preg_match_all("/(?:>|echo\s+['\"])([A-ZÀ-Úa-zà-ú][^<'\"]{4,}?)(?:<|['\"])/", $content, $matches);
    if (!empty($matches[1])) {
        $unique = array_unique($matches[1]);
        foreach ($unique as $m) {
            $m = trim($m);
            if (strlen($m) > 5 && !preg_match('/^[\s{}$]/', $m)) {
                echo "  - $m\n";
            }
        }
    }
}

// 5. Check all olo_service meta keys that contain text
echo "\n\n=== TUTTI I META KEY CON TESTO (primo servizio come esempio) ===\n";
$first = $baite[0] ?? null;
if ($first) {
    $all = get_post_meta($first->ID);
    foreach ($all as $k => $v) {
        if (strpos($k, '_') === 0) continue;
        $val = $v[0] ?? '';
        echo "  $k = " . substr($val, 0, 150) . "\n";
    }
}
