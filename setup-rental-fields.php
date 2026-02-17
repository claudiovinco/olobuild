<?php
/**
 * Run: wp eval-file /tmp/setup-rental-fields.php --allow-root --path=/var/www/wordpress
 *
 * Adds rental/property fields tab to the POI field group + new taxonomy + sample data.
 */

// === 1. Add "Rentals" category and "property" taxonomy ===
$cat_tax = get_option('cptui_taxonomies', []);

// Property type taxonomy
$cat_tax['property_type'] = [
    'name'              => 'property_type',
    'label'             => 'Property Types',
    'singular_label'    => 'Property Type',
    'public'            => 'true',
    'publicly_queryable'=> 'true',
    'hierarchical'      => 'true',
    'show_ui'           => 'true',
    'show_in_menu'      => 'true',
    'show_admin_column' => 'true',
    'show_in_rest'      => 'true',
    'labels'            => [],
    'object_types'      => ['location'],
];
update_option('cptui_taxonomies', $cat_tax);

// Create property type terms
wp_insert_term('Apartment', 'property_type', ['slug' => 'apartment']);
wp_insert_term('Villa', 'property_type', ['slug' => 'villa']);
wp_insert_term('B&B', 'property_type', ['slug' => 'bb']);
wp_insert_term('Hotel Room', 'property_type', ['slug' => 'hotel-room']);
wp_insert_term('Studio', 'property_type', ['slug' => 'studio']);

// Add "Rentals" to location_category
$rentals_term = wp_insert_term('Rentals', 'location_category', ['slug' => 'rentals']);
$rentals_term_id = is_array($rentals_term) ? $rentals_term['term_id'] : (is_wp_error($rentals_term) ? get_term_by('slug', 'rentals', 'location_category')->term_id : 0);
if ($rentals_term_id) {
    update_term_meta($rentals_term_id, 'marker_color', '#0891b2'); // cyan
}
echo "Taxonomies and terms created.\n";

// === 2. Add rental-specific ACF fields to the existing POI group ===
// We'll create a separate field group for rental fields (conditional on category)
acf_import_field_group([
    'key'    => 'group_mosaic_poi_rental',
    'title'  => 'POI – Rental / Property Details',
    'fields' => [

        // ════════════ TAB: Property ════════════
        [
            'key'       => 'field_rental_tab_property',
            'label'     => 'Property',
            'name'      => '',
            'type'      => 'tab',
            'placement' => 'top',
        ],
        [
            'key'          => 'field_rental_bedrooms',
            'label'        => 'Bedrooms',
            'name'         => 'rental_bedrooms',
            'type'         => 'number',
            'min'          => 0,
            'max'          => 20,
            'step'         => 1,
            'placeholder'  => '2',
        ],
        [
            'key'          => 'field_rental_bathrooms',
            'label'        => 'Bathrooms',
            'name'         => 'rental_bathrooms',
            'type'         => 'number',
            'min'          => 0,
            'max'          => 10,
            'step'         => 1,
            'placeholder'  => '1',
        ],
        [
            'key'          => 'field_rental_guests',
            'label'        => 'Max Guests',
            'name'         => 'rental_guests',
            'type'         => 'number',
            'min'          => 1,
            'max'          => 50,
            'step'         => 1,
            'placeholder'  => '4',
        ],
        [
            'key'          => 'field_rental_sqm',
            'label'        => 'Size (m²)',
            'name'         => 'rental_sqm',
            'type'         => 'number',
            'min'          => 1,
            'step'         => 1,
            'placeholder'  => '65',
        ],
        [
            'key'          => 'field_rental_floor',
            'label'        => 'Floor',
            'name'         => 'rental_floor',
            'type'         => 'text',
            'placeholder'  => '3rd',
        ],

        // ════════════ TAB: Pricing ════════════
        [
            'key'       => 'field_rental_tab_pricing',
            'label'     => 'Pricing',
            'name'      => '',
            'type'      => 'tab',
            'placement' => 'top',
        ],
        [
            'key'          => 'field_rental_price_night',
            'label'        => 'Price per Night (€)',
            'name'         => 'rental_price_night',
            'type'         => 'number',
            'min'          => 0,
            'step'         => 1,
            'placeholder'  => '120',
            'prepend'      => '€',
        ],
        [
            'key'          => 'field_rental_price_week',
            'label'        => 'Price per Week (€)',
            'name'         => 'rental_price_week',
            'type'         => 'number',
            'min'          => 0,
            'step'         => 1,
            'placeholder'  => '750',
            'prepend'      => '€',
        ],
        [
            'key'          => 'field_rental_price_month',
            'label'        => 'Price per Month (€)',
            'name'         => 'rental_price_month',
            'type'         => 'number',
            'min'          => 0,
            'step'         => 1,
            'placeholder'  => '2500',
            'prepend'      => '€',
        ],
        [
            'key'          => 'field_rental_cleaning_fee',
            'label'        => 'Cleaning Fee (€)',
            'name'         => 'rental_cleaning_fee',
            'type'         => 'number',
            'min'          => 0,
            'step'         => 1,
            'placeholder'  => '50',
        ],
        [
            'key'          => 'field_rental_deposit',
            'label'        => 'Security Deposit (€)',
            'name'         => 'rental_deposit',
            'type'         => 'number',
            'min'          => 0,
            'step'         => 1,
            'placeholder'  => '200',
        ],
        [
            'key'          => 'field_rental_min_stay',
            'label'        => 'Minimum Stay (nights)',
            'name'         => 'rental_min_stay',
            'type'         => 'number',
            'min'          => 1,
            'step'         => 1,
            'default_value' => 2,
        ],

        // ════════════ TAB: Amenities ════════════
        [
            'key'       => 'field_rental_tab_amenities',
            'label'     => 'Amenities',
            'name'      => '',
            'type'      => 'tab',
            'placement' => 'top',
        ],
        [
            'key'     => 'field_rental_amenities',
            'label'   => 'Amenities',
            'name'    => 'rental_amenities',
            'type'    => 'checkbox',
            'choices' => [
                'wifi'           => 'Wi-Fi',
                'ac'             => 'Air Conditioning',
                'heating'        => 'Heating',
                'kitchen'        => 'Kitchen',
                'washing_machine'=> 'Washing Machine',
                'dryer'          => 'Dryer',
                'dishwasher'     => 'Dishwasher',
                'tv'             => 'TV / Smart TV',
                'balcony'        => 'Balcony / Terrace',
                'garden'         => 'Garden',
                'pool'           => 'Swimming Pool',
                'parking'        => 'Free Parking',
                'garage'         => 'Garage',
                'elevator'       => 'Elevator',
                'doorman'        => 'Doorman / Concierge',
                'safe'           => 'Safe',
                'iron'           => 'Iron',
                'hair_dryer'     => 'Hair Dryer',
                'coffee_machine' => 'Coffee Machine',
                'bbq'            => 'BBQ Area',
                'workspace'      => 'Dedicated Workspace',
                'baby_crib'      => 'Baby Crib',
                'high_chair'     => 'High Chair',
            ],
            'layout' => 'horizontal',
        ],
        [
            'key'     => 'field_rental_rules',
            'label'   => 'House Rules',
            'name'    => 'rental_rules',
            'type'    => 'checkbox',
            'choices' => [
                'no_smoking'  => 'No Smoking',
                'no_pets'     => 'No Pets',
                'no_parties'  => 'No Parties',
                'quiet_hours' => 'Quiet Hours (22:00-8:00)',
            ],
            'layout' => 'horizontal',
        ],
        [
            'key'     => 'field_rental_checkin',
            'label'   => 'Check-in Time',
            'name'    => 'rental_checkin',
            'type'    => 'text',
            'placeholder' => '15:00',
        ],
        [
            'key'     => 'field_rental_checkout',
            'label'   => 'Check-out Time',
            'name'    => 'rental_checkout',
            'type'    => 'text',
            'placeholder' => '10:00',
        ],

        // ════════════ TAB: Booking ════════════
        [
            'key'       => 'field_rental_tab_booking',
            'label'     => 'Booking',
            'name'      => '',
            'type'      => 'tab',
            'placement' => 'top',
        ],
        [
            'key'         => 'field_rental_booking_url',
            'label'       => 'Booking URL',
            'name'        => 'rental_booking_url',
            'type'        => 'url',
            'placeholder' => 'https://www.airbnb.com/rooms/...',
        ],
        [
            'key'         => 'field_rental_airbnb',
            'label'       => 'Airbnb URL',
            'name'        => 'rental_airbnb',
            'type'        => 'url',
        ],
        [
            'key'         => 'field_rental_booking_com',
            'label'       => 'Booking.com URL',
            'name'        => 'rental_booking_com',
            'type'        => 'url',
        ],
        [
            'key'          => 'field_rental_availability',
            'label'        => 'Availability Status',
            'name'         => 'rental_availability',
            'type'         => 'select',
            'choices'      => [
                'available'   => 'Available',
                'booked'      => 'Currently Booked',
                'maintenance' => 'Under Maintenance',
            ],
            'default_value' => 'available',
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
    'menu_order'            => 1,
    'position'              => 'normal',
    'style'                 => 'default',
    'label_placement'       => 'top',
    'instruction_placement' => 'label',
    'active'                => true,
]);

echo "Rental field group created.\n";

// === 3. Create sample rental locations ===
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$rentals = [
    [
        'title'   => 'Loft Panoramico Trastevere',
        'excerpt' => 'Stunning loft with rooftop terrace and 360° views over Rome. Perfect for couples.',
        'content' => '<!-- wp:heading -->
<h2>A Rooftop Gem in the Heart of Trastevere</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>This beautifully renovated loft sits atop a historic palazzo in Trastevere, offering breathtaking panoramic views from its private rooftop terrace. Wake up to see the dome of St. Peter\'s, enjoy your morning coffee overlooking the Roman rooftops.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>The space features exposed wooden beams, contemporary Italian design, a fully equipped kitchen with espresso machine, and a luxurious rain shower. Located on a quiet cobblestone street, yet just steps from Trastevere\'s best restaurants and nightlife.</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul>
<li>Private rooftop terrace with outdoor dining</li>
<li>King-size bed with premium linens</li>
<li>Nespresso machine and welcome basket</li>
<li>5-minute walk to Piazza Santa Maria</li>
</ul>
<!-- /wp:list -->',
        'lat'     => 41.8870,
        'lng'     => 12.4700,
        'addr'    => 'Vicolo del Cinque 23, Roma',
        'cat'     => 'rentals',
        'zone'    => 'trastevere',
        'prop'    => 'apartment',
        'meta'    => [
            'location_phone'       => '+39 333 1234567',
            'location_email'       => 'info@trastevereloft.com',
            'location_website'     => 'https://trastevereloft.example.com',
            'location_rating'      => 4.5,
            'location_price_range' => '€€€',
            'location_address'     => 'Vicolo del Cinque 23',
            'location_city'        => 'Roma',
            'location_zip'         => '00153',
            'location_languages'   => ['it', 'en', 'fr'],
            'location_highlights'  => "360° rooftop terrace\nDesigner interior\nQuiet street location\nSelf check-in",
            'rental_bedrooms'      => 1,
            'rental_bathrooms'     => 1,
            'rental_guests'        => 2,
            'rental_sqm'           => 55,
            'rental_floor'         => '4th (top)',
            'rental_price_night'   => 180,
            'rental_price_week'    => 1100,
            'rental_price_month'   => 3800,
            'rental_cleaning_fee'  => 60,
            'rental_deposit'       => 300,
            'rental_min_stay'      => 2,
            'rental_amenities'     => ['wifi', 'ac', 'heating', 'kitchen', 'washing_machine', 'tv', 'balcony', 'coffee_machine', 'iron', 'hair_dryer', 'workspace'],
            'rental_rules'         => ['no_smoking', 'no_parties', 'quiet_hours'],
            'rental_checkin'       => '15:00',
            'rental_checkout'      => '10:00',
            'rental_booking_url'   => 'https://booking.example.com/trastevere-loft',
            'rental_availability'  => 'available',
        ],
    ],
    [
        'title'   => 'Villa Appia con Piscina',
        'excerpt' => 'Elegant villa with private pool near the Appian Way. Ideal for families.',
        'content' => '<!-- wp:heading -->
<h2>A Private Oasis on the Ancient Appian Way</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Nestled among umbrella pines along the historic Via Appia Antica, this elegant villa offers the rare combination of countryside tranquility and city proximity. The private garden with swimming pool is perfect for lazy Roman afternoons.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Three spacious bedrooms, a large living area with fireplace, and a fully equipped country kitchen. The villa is surrounded by archaeological sites and nature, yet just 20 minutes from the Colosseum by bus or taxi.</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul>
<li>Private swimming pool (May-October)</li>
<li>Large garden with ancient Roman ruins nearby</li>
<li>Free private parking for 2 cars</li>
<li>BBQ area and outdoor dining</li>
<li>Ideal for families with children</li>
</ul>
<!-- /wp:list -->',
        'lat'     => 41.8552,
        'lng'     => 12.5240,
        'addr'    => 'Via Appia Antica 120, Roma',
        'cat'     => 'rentals',
        'zone'    => 'centro-storico',
        'prop'    => 'villa',
        'meta'    => [
            'location_phone'       => '+39 333 9876543',
            'location_email'       => 'stay@villaappia.example.com',
            'location_website'     => 'https://villaappia.example.com',
            'location_rating'      => 5,
            'location_price_range' => '€€€€',
            'location_address'     => 'Via Appia Antica 120',
            'location_city'        => 'Roma',
            'location_zip'         => '00179',
            'location_languages'   => ['it', 'en', 'de'],
            'location_highlights'  => "Private pool\nAncient Roman surroundings\nFree parking\nPerfect for families",
            'rental_bedrooms'      => 3,
            'rental_bathrooms'     => 2,
            'rental_guests'        => 8,
            'rental_sqm'           => 180,
            'rental_floor'         => 'Ground + 1st',
            'rental_price_night'   => 350,
            'rental_price_week'    => 2200,
            'rental_price_month'   => 7500,
            'rental_cleaning_fee'  => 120,
            'rental_deposit'       => 500,
            'rental_min_stay'      => 3,
            'rental_amenities'     => ['wifi', 'ac', 'heating', 'kitchen', 'washing_machine', 'dryer', 'dishwasher', 'tv', 'garden', 'pool', 'parking', 'bbq', 'baby_crib', 'high_chair', 'safe', 'iron', 'hair_dryer', 'coffee_machine'],
            'rental_rules'         => ['no_smoking', 'no_parties'],
            'rental_checkin'       => '16:00',
            'rental_checkout'      => '10:00',
            'rental_booking_url'   => 'https://booking.example.com/villa-appia',
            'rental_availability'  => 'available',
        ],
    ],
    [
        'title'   => 'Studio San Lorenzo',
        'excerpt' => 'Cozy studio in the university district. Budget-friendly with great transport links.',
        'content' => '<!-- wp:heading -->
<h2>Smart Living in Rome\'s Student Quarter</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>A modern, compact studio in lively San Lorenzo — Rome\'s creative and university district. Freshly renovated with smart storage solutions, this is the perfect base for solo travelers or couples who want to experience authentic Roman neighborhood life.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Steps from Termini station, surrounded by craft beer pubs, street art, and affordable trattorias. The neighborhood is vibrant, young, and welcoming.</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul>
<li>2 minutes from Termini station</li>
<li>Vibrant nightlife neighborhood</li>
<li>Supermarket on the ground floor</li>
<li>Perfect for digital nomads</li>
</ul>
<!-- /wp:list -->',
        'lat'     => 41.8962,
        'lng'     => 12.5170,
        'addr'    => 'Via dei Sabelli 44, Roma',
        'cat'     => 'rentals',
        'zone'    => 'centro-storico',
        'prop'    => 'studio',
        'meta'    => [
            'location_phone'       => '+39 340 5551234',
            'location_email'       => 'book@studiosanlorenzo.example.com',
            'location_rating'      => 4,
            'location_price_range' => '€',
            'location_address'     => 'Via dei Sabelli 44',
            'location_city'        => 'Roma',
            'location_zip'         => '00185',
            'location_languages'   => ['it', 'en'],
            'location_highlights'  => "2 min from Termini\nBudget friendly\nGreat for digital nomads\nSelf check-in",
            'rental_bedrooms'      => 0,
            'rental_bathrooms'     => 1,
            'rental_guests'        => 2,
            'rental_sqm'           => 28,
            'rental_floor'         => '2nd',
            'rental_price_night'   => 65,
            'rental_price_week'    => 400,
            'rental_price_month'   => 1400,
            'rental_cleaning_fee'  => 30,
            'rental_deposit'       => 100,
            'rental_min_stay'      => 1,
            'rental_amenities'     => ['wifi', 'ac', 'heating', 'kitchen', 'washing_machine', 'tv', 'iron', 'hair_dryer', 'coffee_machine', 'workspace'],
            'rental_rules'         => ['no_smoking', 'no_pets', 'no_parties', 'quiet_hours'],
            'rental_checkin'       => '14:00',
            'rental_checkout'      => '11:00',
            'rental_booking_url'   => 'https://booking.example.com/studio-sanlorenzo',
            'rental_availability'  => 'available',
        ],
    ],
];

foreach ($rentals as $r) {
    $post_id = wp_insert_post([
        'post_title'   => $r['title'],
        'post_type'    => 'location',
        'post_status'  => 'publish',
        'post_excerpt' => $r['excerpt'],
        'post_content' => $r['content'],
    ]);

    if (is_wp_error($post_id)) {
        echo "Error creating {$r['title']}\n";
        continue;
    }

    // Map coordinates
    update_post_meta($post_id, 'location_map', [
        'lat'     => $r['lat'],
        'lng'     => $r['lng'],
        'zoom'    => 15,
        'address' => $r['addr'],
    ]);

    // Taxonomies
    wp_set_object_terms($post_id, $r['cat'], 'location_category');
    wp_set_object_terms($post_id, $r['zone'], 'location_zone');
    wp_set_object_terms($post_id, $r['prop'], 'property_type');

    // Meta fields
    foreach ($r['meta'] as $key => $value) {
        if (is_array($value)) {
            delete_post_meta($post_id, $key);
            foreach ($value as $v) {
                add_post_meta($post_id, $key, $v);
            }
        } else {
            update_post_meta($post_id, $key, $value);
        }
    }

    // Download a placeholder image
    $img_url = 'https://loremflickr.com/640/480/rome+apartment';
    $attach_id = media_sideload_image($img_url, $post_id, $r['title'], 'id');
    if (!is_wp_error($attach_id)) {
        set_post_thumbnail($post_id, $attach_id);
    }

    echo "Created: {$r['title']} (ID: $post_id)\n";
}

echo "\nDone! 3 rental properties created.\n";
