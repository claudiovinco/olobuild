<?php
/**
 * Create a comprehensive Single Template for Location (Rental Property).
 * Run with: wp eval-file /tmp/create-rental-template.php --allow-root
 */

function uid() {
    return 'tile-' . uniqid() . '-' . substr(md5(mt_rand()), 0, 8);
}

function section($settings, $children, $style = [], $advanced = []) {
    return [
        'id' => uid(), 'type' => 'section',
        'settings' => array_merge(['style' => 'default', 'width' => 'default', 'padding' => 'default'], $settings),
        'style' => $style, 'advanced' => $advanced, 'children' => $children,
    ];
}

function row($children, $settings = [], $style = [], $advanced = []) {
    return [
        'id' => uid(), 'type' => 'row',
        'settings' => array_merge([
            'layout' => '100', 'gap' => '16', 'column_gap' => 'default',
            'vertical_align' => 'stretch', 'stack_mobile' => true
        ], $settings),
        'style' => $style, 'advanced' => $advanced, 'children' => $children,
    ];
}

function col($width, $children, $style = [], $advanced = []) {
    return [
        'id' => uid(), 'type' => 'column',
        'settings' => ['width_default' => '', 'width_small' => '', 'width_medium' => $width, 'width_large' => ''],
        'style' => $style, 'advanced' => $advanced, 'children' => $children,
    ];
}

function el($type, $settings = [], $dynamic = [], $style = [], $advanced = []) {
    $node = [
        'id' => uid(), 'type' => $type,
        'settings' => $settings, 'style' => $style, 'advanced' => $advanced,
    ];
    if (!empty($dynamic)) $node['dynamic'] = $dynamic;
    return $node;
}

// ═══════════════════════════════════════════════════════════════
// BUILD THE TEMPLATE CONTENT
// ═══════════════════════════════════════════════════════════════

$content = [];

// ── Section 1: Breadcrumbs ──
$content[] = section(
    ['style' => 'muted', 'padding' => 'small'],
    [row([col('1-1', [
        el('breadcrumbs', ['separator' => '/', 'home_label' => 'Home', 'show_home' => true, 'show_current' => true])
    ])])]
);

// ── Section 2: Hero (Image + Title Info) ──
$content[] = section(
    ['style' => 'default', 'width' => 'default', 'padding' => 'default'],
    [row([
        // Left: Featured Image
        col('2-3', [
            el('image', [
                'image_url' => '', 'alt_text' => '', 'caption' => '',
                'link_url' => '', 'link_target' => '_self',
                'object_fit' => 'cover', 'height' => '450px',
            ], [
                'image_url' => ['source' => 'current_post', 'field' => 'featured_image'],
                'alt_text'  => ['source' => 'current_post', 'field' => 'post_title'],
            ], ['border_radius' => 12])
        ]),
        // Right: Title + Info
        col('1-3', [
            el('headline', [
                'heading' => '', 'subtitle' => '', 'tag' => 'h1',
                'alignment' => 'left', 'decoration' => 'none',
                'heading_color' => '#F3F4F6', 'subtitle_color' => '#9CA3AF', 'heading_size' => 'lg',
            ], [
                'heading'  => ['source' => 'current_post', 'field' => 'post_title'],
                'subtitle' => ['source' => 'current_post', 'field' => 'post_excerpt'],
            ]),
            el('divider', ['style' => 'solid', 'width' => '100', 'thickness' => '1', 'color' => '#374151']),
            el('iconbox', [
                'icon_emoji' => '📍', 'title' => 'Location', 'description' => '',
                'alignment' => 'left', 'text_color' => '#F3F4F6', 'icon_size' => '2',
                'link_url' => '', 'link_text' => '',
            ], [
                'description' => ['source' => 'custom_field', 'field' => 'location_address'],
            ]),
            el('iconbox', [
                'icon_emoji' => '📞', 'title' => 'Contact', 'description' => '',
                'alignment' => 'left', 'text_color' => '#F3F4F6', 'icon_size' => '2',
                'link_url' => '', 'link_text' => '',
            ], [
                'description' => ['source' => 'custom_field', 'field' => 'location_phone'],
            ]),
            el('iconbox', [
                'icon_emoji' => '⭐', 'title' => 'Rating', 'description' => '',
                'alignment' => 'left', 'text_color' => '#F3F4F6', 'icon_size' => '2',
                'link_url' => '', 'link_text' => '',
            ], [
                'description' => ['source' => 'custom_field', 'field' => 'location_rating'],
            ]),
            el('button', [
                'text' => 'Book Now', 'url' => '#', 'target' => '_blank',
                'alignment' => 'left', 'bg_color' => '#6366F1', 'text_color' => '#FFFFFF',
                'border_radius' => '8', 'padding_x' => '32', 'padding_y' => '14',
                'font_size' => '16', 'full_width' => true,
            ], [
                'url' => ['source' => 'custom_field', 'field' => 'rental_booking_url'],
            ]),
        ], ['padding_top' => '16', 'padding_left' => '16', 'padding_right' => '16'])
    ], ['gap' => '24', 'vertical_align' => 'start'])]
);

// ── Section 3: Quick Stats (Counters) ──
$content[] = section(
    ['style' => 'muted', 'padding' => 'small'],
    [row([
        col('1-4', [el('counter', [
            'number' => '0', 'label' => 'Bedrooms', 'prefix' => '', 'suffix' => '',
            'icon_emoji' => '🛏️', 'text_color' => '#F3F4F6',
            'number_font_size' => '40', 'label_font_size' => '13', 'padding' => '24',
        ], ['number' => ['source' => 'custom_field', 'field' => 'rental_bedrooms']])]),
        col('1-4', [el('counter', [
            'number' => '0', 'label' => 'Bathrooms', 'prefix' => '', 'suffix' => '',
            'icon_emoji' => '🚿', 'text_color' => '#F3F4F6',
            'number_font_size' => '40', 'label_font_size' => '13', 'padding' => '24',
        ], ['number' => ['source' => 'custom_field', 'field' => 'rental_bathrooms']])]),
        col('1-4', [el('counter', [
            'number' => '0', 'label' => 'Guests', 'prefix' => '', 'suffix' => '',
            'icon_emoji' => '👥', 'text_color' => '#F3F4F6',
            'number_font_size' => '40', 'label_font_size' => '13', 'padding' => '24',
        ], ['number' => ['source' => 'custom_field', 'field' => 'rental_guests']])]),
        col('1-4', [el('counter', [
            'number' => '0', 'label' => 'Sqm', 'prefix' => '', 'suffix' => ' m²',
            'icon_emoji' => '📐', 'text_color' => '#F3F4F6',
            'number_font_size' => '40', 'label_font_size' => '13', 'padding' => '24',
        ], ['number' => ['source' => 'custom_field', 'field' => 'rental_sqm']])]),
    ], ['gap' => '16'])]
);

// ── Section 4: Photo Gallery ──
$content[] = section(
    ['style' => 'default', 'padding' => 'default'],
    [row([col('1-1', [
        el('headline', [
            'heading' => 'Photo Gallery', 'subtitle' => '', 'tag' => 'h2',
            'alignment' => 'left', 'decoration' => 'line', 'decoration_color' => '#6366F1',
            'heading_size' => 'md',
        ]),
        el('gallery', [
            'images' => [], 'columns' => '3', 'gap' => '8',
            'img_height' => '220px', 'object_fit' => 'cover',
        ], [
            'images' => ['source' => 'custom_field', 'field' => 'location_gallery'],
        ]),
    ])])]
);

// ── Section 5: Description ──
$content[] = section(
    ['style' => 'default', 'padding' => 'default'],
    [row([col('1-1', [
        el('headline', [
            'heading' => 'About This Property', 'subtitle' => '', 'tag' => 'h2',
            'alignment' => 'left', 'decoration' => 'line', 'decoration_color' => '#6366F1',
            'heading_size' => 'md',
        ]),
        el('content', [
            'heading' => '', 'text' => '', 'image' => '',
        ], [
            'text' => ['source' => 'current_post', 'field' => 'post_content'],
        ]),
    ])])]
);

// ── Section 6: Video Tour ──
$content[] = section(
    ['style' => 'muted', 'padding' => 'default'],
    [row([col('1-1', [
        el('headline', [
            'heading' => 'Virtual Tour', 'subtitle' => 'Explore the property', 'tag' => 'h2',
            'alignment' => 'center', 'decoration' => 'none', 'heading_size' => 'md',
        ]),
        el('video', [
            'url' => '', 'autoplay' => false, 'muted' => true, 'loop' => false,
            'poster' => '', 'aspect_ratio' => '16:9',
        ]),
    ])])]
);

// ── Section 7: Pricing ──
$content[] = section(
    ['style' => 'default', 'padding' => 'default'],
    [
        row([col('1-1', [
            el('headline', [
                'heading' => 'Pricing', 'subtitle' => 'Flexible rates for every stay', 'tag' => 'h2',
                'alignment' => 'center', 'decoration' => 'line', 'decoration_color' => '#6366F1',
                'heading_size' => 'md',
            ]),
        ])]),
        row([
            col('1-3', [el('pricing', [
                'plan_name' => 'Nightly', 'price' => '', 'period' => '€/night',
                'features' => "Min 1 night\nCleaning fee included\nSelf check-in",
                'cta_text' => 'Book Now', 'cta_url' => '#', 'is_popular' => false,
                'bg_color' => '#1F2937', 'accent_color' => '#6366F1', 'text_color' => '#F3F4F6',
            ], [
                'price'   => ['source' => 'custom_field', 'field' => 'rental_price_night'],
                'cta_url' => ['source' => 'custom_field', 'field' => 'rental_booking_url'],
            ])]),
            col('1-3', [el('pricing', [
                'plan_name' => 'Weekly', 'price' => '', 'period' => '€/week',
                'features' => "7+ nights discount\nCleaning included\nFlexible cancellation",
                'cta_text' => 'Book Now', 'cta_url' => '#', 'is_popular' => true,
                'bg_color' => '#1F2937', 'accent_color' => '#6366F1', 'text_color' => '#F3F4F6',
            ], [
                'price'   => ['source' => 'custom_field', 'field' => 'rental_price_week'],
                'cta_url' => ['source' => 'custom_field', 'field' => 'rental_booking_url'],
            ])]),
            col('1-3', [el('pricing', [
                'plan_name' => 'Monthly', 'price' => '', 'period' => '€/month',
                'features' => "30+ nights\nAll utilities included\nBest value",
                'cta_text' => 'Book Now', 'cta_url' => '#', 'is_popular' => false,
                'bg_color' => '#1F2937', 'accent_color' => '#6366F1', 'text_color' => '#F3F4F6',
            ], [
                'price'   => ['source' => 'custom_field', 'field' => 'rental_price_month'],
                'cta_url' => ['source' => 'custom_field', 'field' => 'rental_booking_url'],
            ])]),
        ], ['gap' => '24']),
    ]
);

// ── Section 8: Price Table ──
$content[] = section(
    ['style' => 'muted', 'padding' => 'small'],
    [row([col('1-1', [
        el('table', [
            'headers' => 'Fee|Amount|Notes',
            'rows' => "Cleaning fee|€30|One-time\nDeposit|€100|Refundable\nMin stay|1 night|Nightly rate",
            'striped' => true, 'header_bg' => '#6366F1', 'header_text' => '#FFFFFF',
            'border_color' => '#374151', 'hover_row' => true,
        ]),
    ])])]
);

// ── Section 9: Details (IconBox grid) ──
$content[] = section(
    ['style' => 'default', 'padding' => 'default'],
    [
        row([col('1-1', [
            el('headline', [
                'heading' => 'Property Details', 'subtitle' => '', 'tag' => 'h2',
                'alignment' => 'left', 'decoration' => 'line', 'decoration_color' => '#6366F1',
                'heading_size' => 'md',
            ]),
        ])]),
        row([
            col('1-3', [el('iconbox', [
                'icon_emoji' => '📧', 'title' => 'Email', 'description' => '',
                'alignment' => 'left', 'text_color' => '#F3F4F6', 'icon_size' => '2',
                'link_url' => '', 'link_text' => '',
            ], ['description' => ['source' => 'custom_field', 'field' => 'location_email']])]),
            col('1-3', [el('iconbox', [
                'icon_emoji' => '🌐', 'title' => 'Website', 'description' => '',
                'alignment' => 'left', 'text_color' => '#F3F4F6', 'icon_size' => '2',
                'link_url' => '', 'link_text' => 'Visit',
            ], ['description' => ['source' => 'custom_field', 'field' => 'location_website']])]),
            col('1-3', [el('iconbox', [
                'icon_emoji' => '🏢', 'title' => 'Floor', 'description' => '',
                'alignment' => 'left', 'text_color' => '#F3F4F6', 'icon_size' => '2',
                'link_url' => '', 'link_text' => '',
            ], ['description' => ['source' => 'custom_field', 'field' => 'rental_floor']])]),
        ], ['gap' => '16']),
        row([
            col('1-3', [el('iconbox', [
                'icon_emoji' => '🏷️', 'title' => 'Price Range', 'description' => '',
                'alignment' => 'left', 'text_color' => '#F3F4F6', 'icon_size' => '2',
                'link_url' => '', 'link_text' => '',
            ], ['description' => ['source' => 'custom_field', 'field' => 'location_price_range']])]),
            col('1-3', [el('iconbox', [
                'icon_emoji' => '🏙️', 'title' => 'City', 'description' => '',
                'alignment' => 'left', 'text_color' => '#F3F4F6', 'icon_size' => '2',
                'link_url' => '', 'link_text' => '',
            ], ['description' => ['source' => 'custom_field', 'field' => 'location_city']])]),
            col('1-3', [el('iconbox', [
                'icon_emoji' => '🔑', 'title' => 'Check-in', 'description' => '',
                'alignment' => 'left', 'text_color' => '#F3F4F6', 'icon_size' => '2',
                'link_url' => '', 'link_text' => '',
            ], ['description' => ['source' => 'custom_field', 'field' => 'rental_checkin']])]),
        ], ['gap' => '16']),
    ]
);

// ── Section 10: Specs + Highlights ──
$content[] = section(
    ['style' => 'muted', 'padding' => 'default'],
    [row([
        col('1-2', [
            el('headline', [
                'heading' => 'Specifications', 'subtitle' => '', 'tag' => 'h3',
                'alignment' => 'left', 'decoration' => 'none', 'heading_size' => 'sm',
            ]),
            el('desclist', [
                'items' => "Deposit|€100 (refundable)\nCleaning Fee|€30\nMin Stay|1 night\nCheck-in|14:00\nCheck-out|11:00",
                'layout' => 'stacked', 'term_color' => '#F3F4F6',
                'definition_color' => '#9CA3AF', 'separator' => true, 'border_color' => '#374151',
            ]),
        ]),
        col('1-2', [
            el('headline', [
                'heading' => 'Highlights', 'subtitle' => '', 'tag' => 'h3',
                'alignment' => 'left', 'decoration' => 'none', 'heading_size' => 'sm',
            ]),
            el('list', [
                'items' => '',
                'icon_default' => 'check', 'icon_color' => '#22C55E',
                'text_color' => '#F3F4F6', 'spacing' => '12', 'icon_size' => '18',
            ], [
                'items' => ['source' => 'custom_field', 'field' => 'location_highlights'],
            ]),
        ]),
    ], ['gap' => '32'])]
);

// ── Section 11: Accordion ──
$content[] = section(
    ['style' => 'default', 'padding' => 'default'],
    [row([col('1-1', [
        el('headline', [
            'heading' => 'More Information', 'subtitle' => '', 'tag' => 'h2',
            'alignment' => 'left', 'decoration' => 'line', 'decoration_color' => '#6366F1',
            'heading_size' => 'md',
        ]),
        el('accordion', [
            'panels' => [
                ['id' => uid(), 'title' => 'Amenities', 'content' => 'Wi-Fi, Air Conditioning, Heating, Kitchen, Washing Machine, TV, Iron, Hair Dryer, Coffee Machine, Workspace'],
                ['id' => uid(), 'title' => 'House Rules', 'content' => 'No smoking, No pets, No parties, Quiet hours (22:00 - 08:00)'],
                ['id' => uid(), 'title' => 'Check-in / Check-out', 'content' => 'Self check-in available. Check-in: 14:00, Check-out: 11:00. Late check-out upon request.'],
                ['id' => uid(), 'title' => 'Cancellation Policy', 'content' => 'Free cancellation up to 7 days before arrival. 50% refund for cancellations within 7 days.'],
            ],
            'toggle_mode' => false, 'default_open' => 'first',
            'icon_position' => 'right', 'icon_style' => 'chevron',
            'header_bg' => '#111827', 'header_bg_active' => '#1a2332',
            'header_text_color' => '#F3F4F6', 'content_bg' => '#0d1117',
            'border_color' => '#374151', 'text_color' => '#d1d5db',
            'gap' => '4', 'border_radius' => '8',
        ]),
    ])])]
);

// ── Section 12: Tabs (Neighborhood) ──
$content[] = section(
    ['style' => 'muted', 'padding' => 'default'],
    [row([col('1-1', [
        el('headline', [
            'heading' => 'The Neighborhood', 'subtitle' => 'Discover what\'s around', 'tag' => 'h2',
            'alignment' => 'center', 'decoration' => 'none', 'heading_size' => 'md',
        ]),
        el('tabs', [
            'tabs_data' => "Getting Around\nThe property is well connected by public transport. Metro and bus stops within 2 minutes walk.\n---\nFood & Drink\nNumerous restaurants, cafés, and bars in the neighborhood. Local markets for fresh produce.\n---\nAttractions\nWalking distance to major landmarks. Museums, parks and historical sites nearby.\n---\nShopping\nLocal boutiques and markets. Major shopping areas accessible by public transport.",
            'accent_color' => '#6366F1', 'text_color' => '#F3F4F6',
        ]),
    ])])]
);

// ── Section 13: Panel + Overlay ──
$content[] = section(
    ['style' => 'default', 'padding' => 'default'],
    [row([
        col('1-2', [
            el('panel', [
                'style' => 'default', 'title' => 'Availability', 'title_element' => 'h3',
                'meta' => 'Updated daily',
                'content' => 'This property is currently available for booking. Contact us for exact dates and special requests.',
                'image' => '', 'link_url' => '', 'link_target' => '_self',
            ]),
        ]),
        col('1-2', [
            el('overlay', [
                'image_url' => '', 'title' => '', 'description' => 'View on map',
                'link_url' => '', 'link_target' => '_self',
                'overlay_color' => '#000000', 'text_color' => '#FFFFFF',
                'hover_effect' => 'fade', 'overlay_opacity' => '50',
                'border_radius' => '8', 'height' => '250',
            ], [
                'image_url' => ['source' => 'current_post', 'field' => 'featured_image'],
                'title'     => ['source' => 'current_post', 'field' => 'post_title'],
            ]),
        ]),
    ], ['gap' => '24'])]
);

// ── Section 14: Progress (Ratings) ──
$content[] = section(
    ['style' => 'muted', 'padding' => 'default'],
    [row([col('2-3', [
        el('headline', [
            'heading' => 'Guest Ratings', 'subtitle' => 'Based on recent reviews', 'tag' => 'h2',
            'alignment' => 'left', 'decoration' => 'none', 'heading_size' => 'md',
        ]),
        el('progress', [
            'bars' => "Cleanliness|95\nLocation|90\nValue for Money|85\nComfort|88\nCommunication|92",
            'bar_color' => '#6366F1', 'bar_bg' => '#1F2937', 'text_color' => '#F3F4F6',
            'height' => '16', 'show_percentage' => true, 'animated' => true, 'border_radius' => '8',
        ]),
    ])])]
);

// ── Section 15: Testimonial + Quotation ──
$content[] = section(
    ['style' => 'default', 'padding' => 'default'],
    [row([
        col('1-2', [
            el('testimonial', [
                'quote' => 'Amazing stay! The apartment was exactly as described. Clean, well-equipped and in a perfect location. Would definitely come back!',
                'author_name' => 'Marco R.', 'author_role' => 'Guest — March 2025',
                'avatar' => '', 'rating' => '5',
                'bg_color' => '#1F2937', 'text_color' => '#F3F4F6',
            ]),
        ]),
        col('1-2', [
            el('quotation', [
                'content' => 'We put our heart into making this space feel like home. Every detail has been thought of to ensure your comfort during your stay.',
                'author' => 'Your Host', 'style' => 'default', 'alignment' => 'left',
            ]),
        ]),
    ], ['gap' => '24'])]
);

// ── Section 16: Host (Team) + Alert ──
$content[] = section(
    ['style' => 'muted', 'padding' => 'default'],
    [row([
        col('1-3', [
            el('team', [
                'photo' => '', 'name' => '', 'role' => 'Superhost',
                'bio' => 'Passionate about hospitality. Always available to help make your stay unforgettable.',
                'link_url' => '', 'bg_color' => '#1F2937', 'text_color' => '#F3F4F6',
            ], [
                'name' => ['source' => 'author', 'field' => 'display_name'],
            ]),
        ]),
        col('2-3', [
            el('headline', [
                'heading' => 'Your Host', 'subtitle' => 'Here to help', 'tag' => 'h2',
                'alignment' => 'left', 'decoration' => 'none', 'heading_size' => 'md',
            ]),
            el('alert', [
                'alert_type' => 'info', 'title' => 'Response Time',
                'message' => 'Your host typically responds within 1 hour. Feel free to reach out with any questions before or during your stay.',
                'show_icon' => true,
            ]),
        ]),
    ], ['gap' => '24', 'vertical_align' => 'center'])]
);

// ── Section 17: Slideshow ──
$content[] = section(
    ['style' => 'default', 'padding' => 'default'],
    [row([col('1-1', [
        el('headline', [
            'heading' => 'Property Showcase', 'subtitle' => '', 'tag' => 'h2',
            'alignment' => 'center', 'decoration' => 'none', 'heading_size' => 'md',
        ]),
        el('slideshow', [
            'slides' => [
                ['id' => uid(), 'image' => '', 'title' => 'Living Area', 'subtitle' => 'Bright and spacious', 'link' => ''],
                ['id' => uid(), 'image' => '', 'title' => 'Kitchen', 'subtitle' => 'Fully equipped', 'link' => ''],
                ['id' => uid(), 'image' => '', 'title' => 'Bedroom', 'subtitle' => 'Comfortable and quiet', 'link' => ''],
            ],
            'autoplay' => true, 'autoplay_speed' => '5000',
            'show_arrows' => true, 'show_dots' => true,
            'slide_height' => '400', 'overlay_color' => '#000000',
            'text_color' => '#FFFFFF', 'transition' => 'slide',
        ]),
    ])])]
);

// ── Section 18: Map ──
$content[] = section(
    ['style' => 'default', 'padding' => 'default'],
    [row([col('1-1', [
        el('headline', [
            'heading' => 'Location', 'subtitle' => 'Find us on the map', 'tag' => 'h2',
            'alignment' => 'left', 'decoration' => 'line', 'decoration_color' => '#6366F1',
            'heading_size' => 'md',
        ]),
        el('map', [
            'mode' => 'single', 'address' => 'Rome, Italy', 'zoom' => '15', 'height' => '400',
        ], [
            'address' => ['source' => 'custom_field', 'field' => 'location_map'],
        ]),
    ])])]
);

// ── Section 19: Countdown (offer) ──
$content[] = section(
    ['style' => 'primary', 'padding' => 'default'],
    [row([col('1-1', [
        el('headline', [
            'heading' => 'Special Offer', 'subtitle' => 'Book before the deal expires!', 'tag' => 'h2',
            'alignment' => 'center', 'decoration' => 'none', 'heading_size' => 'md',
            'heading_color' => '#FFFFFF', 'subtitle_color' => '#E0E7FF',
        ]),
        el('countdown', [
            'target_date' => date('Y-m-d', strtotime('+30 days')),
            'expired_message' => 'Offer expired', 'show_labels' => true,
        ]),
    ])])]
);

// ── Section 20: CTA (Hero element) ──
$content[] = section(
    ['style' => 'secondary', 'padding' => 'large'],
    [row([col('1-1', [
        el('hero', [
            'title' => 'Ready to Book?', 'subtitle' => 'Secure your dates now — limited availability',
            'cta_text' => 'Book Now', 'cta_url' => '#',
            'background_color' => 'transparent', 'text_color' => '#FFFFFF',
            'cta_bg_color' => '#FFFFFF', 'cta_text_color' => '#6366F1',
        ], [
            'cta_url' => ['source' => 'custom_field', 'field' => 'rental_booking_url'],
        ]),
    ])])]
);

// ── Section 21: HTML embed + Social ──
$content[] = section(
    ['style' => 'default', 'padding' => 'small'],
    [row([col('1-1', [
        el('divider', ['style' => 'solid', 'width' => '60', 'thickness' => '1', 'color' => '#374151', 'alignment' => 'center']),
        el('spacer', ['height' => '16']),
        el('social', [
            'links' => "facebook|https://facebook.com\ntwitter|https://twitter.com\ninstagram|https://instagram.com\nwhatsapp|https://wa.me/",
            'size' => '28', 'alignment' => 'center', 'gap' => '16',
        ]),
        el('spacer', ['height' => '16']),
        el('html', [
            'code' => '<div style="text-align:center;padding:16px 0;color:#9CA3AF;font-size:13px;">Property listed on Mosaic Builder &middot; All rights reserved</div>',
        ]),
    ])])]
);


// ═══════════════════════════════════════════════════════════════
// INSERT INTO DATABASE
// ═══════════════════════════════════════════════════════════════

$db = new Mosaic_Database();
$template_id = $db->create_template([
    'title'    => 'Single: location (Rental Property)',
    'type'     => 'single',
    'content'  => $content,
    'settings' => [
        'single_post_type'  => 'location',
        'content_max_width' => 1200,
        'page_bg'           => ['type' => 'none'],
    ],
    'status'   => 'published',
]);

if ($template_id) {
    echo "Template created: ID #{$template_id}\n";
    echo "Activating for post_type 'location'...\n";
    update_option('mosaic_active_single_location', $template_id);
    echo "Done! Active single template for 'location' = #{$template_id}\n";
} else {
    echo "ERROR: Failed to create template.\n";
}
