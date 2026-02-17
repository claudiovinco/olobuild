<?php
/**
 * Run: wp eval-file /tmp/setup-poi-fields.php --allow-root --path=/var/www/wordpress
 *
 * Creates a comprehensive ACF field group for Location POIs with tabs.
 */

// Delete old simple field groups
$old_groups = ['group_mosaic_location_map', 'group_mosaic_location_details'];
foreach ($old_groups as $key) {
    $post = get_posts([
        'post_type'  => 'acf-field-group',
        'meta_query' => [['key' => 'acf_field_group_key', 'value' => $key]],
        'numberposts' => 1,
    ]);
    // Also try by post_name
    $post2 = get_posts([
        'post_type'  => 'acf-field-group',
        'name'       => $key,
        'numberposts' => 1,
    ]);
    foreach (array_merge($post, $post2) as $p) {
        wp_delete_post($p->ID, true);
        echo "Deleted old group: {$p->post_title}\n";
    }
}

// Create the comprehensive field group
acf_import_field_group([
    'key'    => 'group_mosaic_poi',
    'title'  => 'POI – Point of Interest',
    'fields' => [

        // ════════════ TAB: Position ════════════
        [
            'key'   => 'field_poi_tab_position',
            'label' => 'Position',
            'name'  => '',
            'type'  => 'tab',
            'placement' => 'top',
        ],
        [
            'key'          => 'field_poi_map',
            'label'        => 'Map Location',
            'name'         => 'location_map',
            'type'         => 'open_street_map',
            'instructions' => 'Click on the map or search an address to set the location.',
            'center_lat'   => '41.9028',
            'center_lng'   => '12.4964',
            'zoom'         => '13',
            'height'       => '400',
            'return_format' => 'leaflet',
            'required'     => 1,
        ],
        [
            'key'          => 'field_poi_address',
            'label'        => 'Full Address',
            'name'         => 'location_address',
            'type'         => 'text',
            'instructions' => 'Street address as displayed to visitors.',
            'placeholder'  => 'Via Roma 1, 00100 Roma RM',
        ],
        [
            'key'          => 'field_poi_city',
            'label'        => 'City',
            'name'         => 'location_city',
            'type'         => 'text',
            'placeholder'  => 'Roma',
        ],
        [
            'key'          => 'field_poi_zip',
            'label'        => 'ZIP Code',
            'name'         => 'location_zip',
            'type'         => 'text',
            'placeholder'  => '00100',
        ],

        // ════════════ TAB: Contacts ════════════
        [
            'key'   => 'field_poi_tab_contacts',
            'label' => 'Contacts',
            'name'  => '',
            'type'  => 'tab',
            'placement' => 'top',
        ],
        [
            'key'         => 'field_poi_phone',
            'label'       => 'Phone',
            'name'        => 'location_phone',
            'type'        => 'text',
            'placeholder' => '+39 06 1234567',
        ],
        [
            'key'         => 'field_poi_email',
            'label'       => 'Email',
            'name'        => 'location_email',
            'type'        => 'email',
            'placeholder' => 'info@example.com',
        ],
        [
            'key'         => 'field_poi_website',
            'label'       => 'Website',
            'name'        => 'location_website',
            'type'        => 'url',
            'placeholder' => 'https://www.example.com',
        ],
        [
            'key'         => 'field_poi_social_facebook',
            'label'       => 'Facebook',
            'name'        => 'location_facebook',
            'type'        => 'url',
            'placeholder' => 'https://facebook.com/...',
        ],
        [
            'key'         => 'field_poi_social_instagram',
            'label'       => 'Instagram',
            'name'        => 'location_instagram',
            'type'        => 'url',
            'placeholder' => 'https://instagram.com/...',
        ],

        // ════════════ TAB: Hours & Pricing ════════════
        [
            'key'   => 'field_poi_tab_hours',
            'label' => 'Hours & Pricing',
            'name'  => '',
            'type'  => 'tab',
            'placement' => 'top',
        ],
        [
            'key'          => 'field_poi_hours',
            'label'        => 'Opening Hours',
            'name'         => 'location_hours',
            'type'         => 'textarea',
            'instructions' => 'One line per day or group of days.',
            'placeholder'  => "Mon-Fri: 9:00-18:00\nSat: 10:00-14:00\nSun: Closed",
            'rows'         => 4,
            'new_lines'    => 'br',
        ],
        [
            'key'          => 'field_poi_price_range',
            'label'        => 'Price Range',
            'name'         => 'location_price_range',
            'type'         => 'select',
            'instructions' => 'Indicative price level.',
            'choices'      => [
                ''     => '— Not applicable —',
                'free' => 'Free',
                '€'    => '€ – Budget',
                '€€'   => '€€ – Mid-range',
                '€€€'  => '€€€ – Premium',
                '€€€€' => '€€€€ – Luxury',
            ],
            'default_value' => '',
        ],
        [
            'key'          => 'field_poi_ticket_url',
            'label'        => 'Ticket / Booking URL',
            'name'         => 'location_ticket_url',
            'type'         => 'url',
            'placeholder'  => 'https://tickets.example.com',
        ],

        // ════════════ TAB: Details ════════════
        [
            'key'   => 'field_poi_tab_details',
            'label' => 'Details',
            'name'  => '',
            'type'  => 'tab',
            'placement' => 'top',
        ],
        [
            'key'          => 'field_poi_rating',
            'label'        => 'Rating',
            'name'         => 'location_rating',
            'type'         => 'number',
            'instructions' => 'From 1 to 5 (half steps allowed).',
            'min'          => 1,
            'max'          => 5,
            'step'         => 0.5,
        ],
        [
            'key'          => 'field_poi_accessibility',
            'label'        => 'Accessibility',
            'name'         => 'location_accessibility',
            'type'         => 'checkbox',
            'instructions' => 'Select all that apply.',
            'choices'      => [
                'wheelchair'  => 'Wheelchair accessible',
                'parking'     => 'Parking available',
                'elevator'    => 'Elevator',
                'audio_guide' => 'Audio guide',
                'pet_friendly' => 'Pet friendly',
            ],
            'layout' => 'horizontal',
        ],
        [
            'key'          => 'field_poi_languages',
            'label'        => 'Languages Spoken',
            'name'         => 'location_languages',
            'type'         => 'checkbox',
            'choices'      => [
                'it' => 'Italian',
                'en' => 'English',
                'fr' => 'French',
                'de' => 'German',
                'es' => 'Spanish',
                'zh' => 'Chinese',
                'ja' => 'Japanese',
            ],
            'layout' => 'horizontal',
        ],
        [
            'key'          => 'field_poi_highlights',
            'label'        => 'Highlights',
            'name'         => 'location_highlights',
            'type'         => 'textarea',
            'instructions' => 'Key features, one per line. Shown as bullet list.',
            'placeholder'  => "UNESCO World Heritage Site\nGuided tours available\nGift shop on premises",
            'rows'         => 4,
            'new_lines'    => 'br',
        ],

        // ════════════ TAB: Gallery ════════════
        [
            'key'   => 'field_poi_tab_gallery',
            'label' => 'Gallery',
            'name'  => '',
            'type'  => 'tab',
            'placement' => 'top',
        ],
        [
            'key'          => 'field_poi_gallery',
            'label'        => 'Photo Gallery',
            'name'         => 'location_gallery',
            'type'         => 'gallery',
            'instructions' => 'Additional photos for this location.',
            'return_format' => 'array',
            'preview_size'  => 'medium',
            'min'           => 0,
            'max'           => 20,
        ],
    ],
    'location' => [
        [
            [
                'param'    => 'post_type',
                'operator' => '==',
                'value'    => 'location',
            ],
        ],
    ],
    'menu_order'            => 0,
    'position'              => 'normal',
    'style'                 => 'default',
    'label_placement'       => 'top',
    'instruction_placement' => 'label',
    'active'                => true,
]);

echo "POI field group created with 5 tabs.\n";

// ── Populate extra fields for existing locations ──
$extras = [
    115 => [ // Colosseum
        'location_address'       => 'Piazza del Colosseo, 1',
        'location_city'          => 'Roma',
        'location_zip'           => '00184',
        'location_email'         => 'info@colosseo.it',
        'location_price_range'   => '€€',
        'location_ticket_url'    => 'https://colosseo.it/biglietti',
        'location_accessibility' => ['wheelchair', 'audio_guide', 'elevator'],
        'location_languages'     => ['it', 'en', 'fr', 'de', 'es'],
        'location_highlights'    => "UNESCO World Heritage Site\nUnderground levels tours\nCombined ticket with Roman Forum\nNight tours available in summer",
    ],
    116 => [ // Pantheon
        'location_address'       => 'Piazza della Rotonda',
        'location_city'          => 'Roma',
        'location_zip'           => '00186',
        'location_email'         => '',
        'location_price_range'   => 'free',
        'location_ticket_url'    => '',
        'location_accessibility' => ['wheelchair'],
        'location_languages'     => ['it', 'en'],
        'location_highlights'    => "World's largest unreinforced concrete dome\nFree admission\nRaphael's tomb inside\nThe oculus rain experience",
    ],
    117 => [ // Trevi Fountain
        'location_address'       => 'Piazza di Trevi',
        'location_city'          => 'Roma',
        'location_zip'           => '00187',
        'location_price_range'   => 'free',
        'location_accessibility' => ['wheelchair'],
        'location_languages'     => [],
        'location_highlights'    => "Open 24/7\nCoin-tossing tradition\nBaroque masterpiece\nStunning night illumination",
    ],
    118 => [ // Vatican Museums
        'location_address'       => 'Viale Vaticano',
        'location_city'          => 'Città del Vaticano',
        'location_zip'           => '00120',
        'location_email'         => 'info@museivaticani.va',
        'location_price_range'   => '€€€',
        'location_ticket_url'    => 'https://tickets.museivaticani.va',
        'location_accessibility' => ['wheelchair', 'audio_guide', 'elevator'],
        'location_languages'     => ['it', 'en', 'fr', 'de', 'es', 'zh', 'ja'],
        'location_highlights'    => "Sistine Chapel\nRaphael Rooms\nGallery of Maps\nFriday night openings\nFree last Sunday of month",
    ],
    119 => [ // Galleria Borghese
        'location_address'       => 'Piazzale Scipione Borghese, 5',
        'location_city'          => 'Roma',
        'location_zip'           => '00197',
        'location_email'         => 'info@galleriaborghese.it',
        'location_price_range'   => '€€',
        'location_ticket_url'    => 'https://galleriaborghese.beniculturali.it/prenota',
        'location_accessibility' => ['wheelchair', 'audio_guide', 'elevator'],
        'location_languages'     => ['it', 'en', 'fr'],
        'location_highlights'    => "Bernini sculptures\nCaravaggio paintings\nMandatory reservation\nVilla Borghese gardens nearby",
    ],
    120 => [ // MAXXI
        'location_address'       => 'Via Guido Reni, 4A',
        'location_city'          => 'Roma',
        'location_zip'           => '00196',
        'location_email'         => 'info@maxxi.art',
        'location_price_range'   => '€',
        'location_ticket_url'    => 'https://www.maxxi.art/biglietti',
        'location_accessibility' => ['wheelchair', 'elevator', 'parking'],
        'location_languages'     => ['it', 'en'],
        'location_highlights'    => "Zaha Hadid architecture\nRotating exhibitions\nSaturday evening openings\nRestaurant with terrace",
    ],
    121 => [ // Roscioli
        'location_address'       => 'Via dei Giubbonari, 21',
        'location_city'          => 'Roma',
        'location_zip'           => '00186',
        'location_email'         => 'info@salumeriaroscioli.com',
        'location_price_range'   => '€€€',
        'location_accessibility' => [],
        'location_languages'     => ['it', 'en'],
        'location_highlights'    => "Best carbonara in Rome\n2,600+ wine labels\nBakery next door\nReservation essential",
    ],
    122 => [ // Da Enzo
        'location_address'       => 'Via dei Vascellari, 29',
        'location_city'          => 'Roma',
        'location_zip'           => '00153',
        'location_email'         => '',
        'location_price_range'   => '€€',
        'location_accessibility' => ['pet_friendly'],
        'location_languages'     => ['it', 'en'],
        'location_highlights'    => "No reservations – arrive early\nCash only\nOrganic farm ingredients\nHomemade tiramisu",
    ],
];

foreach ($extras as $post_id => $fields) {
    foreach ($fields as $key => $value) {
        if (is_array($value)) {
            delete_post_meta($post_id, $key);
            foreach ($value as $v) {
                add_post_meta($post_id, $key, $v);
            }
        } else {
            update_post_meta($post_id, $key, $value);
        }
    }
    echo "Populated: " . get_the_title($post_id) . "\n";
}

// Update address in map meta too (use location_address as display address)
foreach ($extras as $post_id => $fields) {
    $map_meta = get_post_meta($post_id, 'location_map', true);
    if (is_array($map_meta) && !empty($fields['location_address'])) {
        $addr = $fields['location_address'];
        if (!empty($fields['location_city'])) {
            $addr .= ', ' . $fields['location_city'];
        }
        $map_meta['address'] = $addr;
        update_post_meta($post_id, 'location_map', $map_meta);
    }
}

echo "\nAll done! Go to wp-admin > Locations to see the new fields.\n";
