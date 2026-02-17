<?php
/**
 * Run: wp eval-file /tmp/setup-galleries.php --allow-root --path=/var/www/wordpress
 * Downloads images and populates gallery fields for all locations.
 */

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

// Flickr loremflickr URLs with unique seeds per location
$galleries = [
    115 => ['query' => 'colosseum+rome',       'count' => 5, 'name' => 'Colosseum'],
    116 => ['query' => 'pantheon+rome',         'count' => 4, 'name' => 'Pantheon'],
    117 => ['query' => 'trevi+fountain+rome',   'count' => 4, 'name' => 'Trevi Fountain'],
    118 => ['query' => 'sistine+chapel',        'count' => 5, 'name' => 'Vatican Museums'],
    119 => ['query' => 'villa+borghese',        'count' => 4, 'name' => 'Galleria Borghese'],
    120 => ['query' => 'maxxi+rome',            'count' => 3, 'name' => 'MAXXI Museum'],
    121 => ['query' => 'italian+pasta',         'count' => 4, 'name' => 'Roscioli'],
    122 => ['query' => 'trastevere+rome',       'count' => 4, 'name' => 'Da Enzo al 29'],
    187 => ['query' => 'rome+apartment+terrace','count' => 5, 'name' => 'Loft Trastevere'],
    188 => ['query' => 'villa+pool+italy',      'count' => 6, 'name' => 'Villa Appia'],
    189 => ['query' => 'rome+studio+modern',    'count' => 4, 'name' => 'Studio San Lorenzo'],
];

foreach ($galleries as $post_id => $info) {
    $attach_ids = [];

    for ($i = 1; $i <= $info['count']; $i++) {
        // Use random lock to get different images each time
        $seed = $post_id * 100 + $i;
        $tmp = '/tmp/gallery-' . $post_id . '-' . $i . '.jpg';

        // Download with unique seed
        $url = 'https://loremflickr.com/800/600/' . $info['query'] . '?lock=' . $seed;
        $data = @file_get_contents($url);

        if (!$data || strlen($data) < 1000) {
            echo "  Skip image $i for {$info['name']} (download failed)\n";
            continue;
        }

        file_put_contents($tmp, $data);

        // Verify it's actually a JPEG
        $finfo = mime_content_type($tmp);
        if (strpos($finfo, 'image/jpeg') === false && strpos($finfo, 'image/png') === false) {
            echo "  Skip image $i for {$info['name']} (not an image: $finfo)\n";
            unlink($tmp);
            continue;
        }

        // Import as attachment
        $file_array = [
            'name'     => $info['name'] . ' - Photo ' . $i . '.jpg',
            'tmp_name' => $tmp,
        ];

        $attach_id = media_handle_sideload($file_array, $post_id);

        if (is_wp_error($attach_id)) {
            echo "  Error importing image $i for {$info['name']}: " . $attach_id->get_error_message() . "\n";
            if (file_exists($tmp)) unlink($tmp);
            continue;
        }

        $attach_ids[] = $attach_id;
    }

    if (!empty($attach_ids)) {
        // ACF gallery field stores as serialized array of attachment IDs
        update_post_meta($post_id, 'location_gallery', $attach_ids);
        echo "OK {$info['name']}: " . count($attach_ids) . " photos\n";
    } else {
        echo "WARN {$info['name']}: no photos imported\n";
    }
}

echo "\nDone!\n";
