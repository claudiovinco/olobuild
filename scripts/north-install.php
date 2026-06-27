<?php
/**
 * Installer DEMO tema "North" (ricreazione cohere.com/north) su un sito Olobuild.
 *   wp eval-file north-install.php --allow-root --skip-plugins=woocommerce
 * Crea: menu WP "North Nav", olo_template "North — Home" (header+body+footer),
 * pagina /north-demo/ full-width. Idempotente. Stampa URL + id.
 * Immagini = stock Unsplash (placeholder; il design Cohere è copyright).
 */

if ( ! defined( 'ABSPATH' ) ) { fwrite( STDERR, "Run via wp eval-file\n" ); exit( 1 ); }

// ── helpers ───────────────────────────────────────────────────────────────
$GLOBALS['__nid'] = 0;
function nn( $type, $settings = [], $children = [], $style = [], $advanced = [] ) {
    $GLOBALS['__nid']++;
    return [
        'id'       => substr( md5( $type . '|' . $GLOBALS['__nid'] ), 0, 8 ),
        'type'     => $type,
        'settings' => $settings,
        'style'    => (object) $style,
        'advanced' => (object) $advanced,
        'children' => $children,
    ];
}
function col( $children, $w = '1-1' ) {
    return nn( 'column', [ 'width_default' => '', 'width_small' => '', 'width_medium' => $w, 'width_large' => '' ], $children );
}
function row( $children, $layout = '100', $gap = 24, $valign = 'center' ) {
    return nn( 'row', [ 'layout' => $layout, 'gap' => $gap, 'vertical_align' => $valign, 'stack_mobile' => true ], $children );
}
function sec( $children, $width = 'default', $padding = 'large', $style = [], $advanced = [] ) {
    return nn( 'section', [ 'style' => 'default', 'width' => $width, 'padding' => $padding ], $children, $style, $advanced );
}

// ── imagery (stock placeholder) ─────────────────────────────────────────────
$U = function( $id, $w = 1200 ) { return "https://images.unsplash.com/{$id}?w={$w}&q=72&auto=format&fit=crop"; };
$A        = 'https://mosaic.clod.eu/wp-content/uploads/north-assets/'; // mockup UI fedeli (SVG)
$GRASS    = $U( 'photo-1501854140801-50d01698950b', 1920 ); // aerial green hills
$HERO_UI  = $A . 'north-ui-hero.svg';                       // UI agente (hero)
$IMG_WORK = $U( 'photo-1573497019940-1c28c88b4f3e', 1000 ); // woman desk
$IMG_MESH = $U( 'photo-1620641788421-7a1c342ea42e', 1000 ); // abstract
$IMG_SEC  = $U( 'photo-1550751827-4bd374c3f58b', 1000 );    // cyber
$UI1      = $A . 'north-ui-discover.svg';                   // Finance Agent + connettori
$UI2      = $A . 'north-ui-create.svg';                     // Financial Summary doc
$UI3      = $A . 'north-ui-automate.svg';                   // agent flow
$DOC      = $A . 'north-ui-legal.svg';                      // Licensing Contract
$CITY     = $U( 'photo-1486406146926-c627a92ad1ab', 1200 ); // skyscrapers
$GRAD     = $U( 'photo-1557672172-298e090bd0f1', 1200 );    // gradient
$VORONOI  = $A . 'north-voronoi.svg';                       // diagramma a celle menta

// helper: lista link footer (iconlist senza icona)
$flinks = function( $arr ) {
    $out = []; $i = 0;
    foreach ( $arr as $t ) { $out[] = [ 'id' => 'fl' . ( $i++ ), 'icon' => '', 'text' => $t, 'link' => '#', 'color' => '' ]; }
    return $out;
};

// ── WP menu per la nav ──────────────────────────────────────────────────────
$menu_name = 'North Nav';
$menu_obj  = wp_get_nav_menu_object( $menu_name );
if ( $menu_obj ) {
    foreach ( (array) wp_get_nav_menu_items( $menu_obj->term_id ) as $it ) { wp_delete_post( $it->ID, true ); }
    $menu_id = $menu_obj->term_id;
} else {
    $menu_id = wp_create_nav_menu( $menu_name );
}
foreach ( [ 'Products', 'Solutions', 'Research', 'Resources', 'Company' ] as $label ) {
    wp_update_nav_menu_item( $menu_id, 0, [
        'menu-item-title' => $label, 'menu-item-url' => '#', 'menu-item-status' => 'publish', 'menu-item-type' => 'custom',
    ] );
}

// ── News posts (3 articoli North reali per la sezione News) ─────────────────
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
$news_posts = [
    [ 'Introducing North: The next era of enterprise AI', '2026-03-10 09:00:00', $U( 'photo-1518770660439-4636190af475', 900 ), 'A secure, agentic workspace that turns complexity into clarity for the enterprise.' ],
    [ 'Defining AI automation: A new kind of workplace', '2026-02-24 09:00:00', $U( 'photo-1551434678-e076c223a692', 900 ), 'How agents that operate in lockstep with your people are reshaping how work flows.' ],
    [ 'Bringing secure AI to critical systems', '2026-02-10 09:00:00', $U( 'photo-1550751827-4bd374c3f58b', 900 ), 'Private deployments, zero-trust access and audit-ready visibility for regulated teams.' ],
];
foreach ( $news_posts as $np ) {
    $ex = @get_page_by_title( $np[0], OBJECT, 'post' );
    if ( $ex ) { continue; }
    $pid = wp_insert_post( [
        'post_title'   => $np[0], 'post_status' => 'publish', 'post_type' => 'post',
        'post_date'    => $np[1], 'post_excerpt' => $np[3],
        'post_content' => '<p>' . $np[3] . '</p>',
    ] );
    if ( $pid && ! is_wp_error( $pid ) ) {
        $aid = media_sideload_image( $np[2], $pid, $np[0], 'id' );
        if ( ! is_wp_error( $aid ) ) { set_post_thumbnail( $pid, $aid ); }
    }
}

// ── SECTIONS ────────────────────────────────────────────────────────────────
$sections = [];

// HEADER: announcement + megamenu
$sections[] = sec( [
    row( [ col( [ nn( 'announcementbar', [
        'text' => "North Mini Code. Cohere's first model for developers. ",
        'accent_text' => 'Learn more', 'link_url' => '#',
        'bg_color' => '#000000', 'text_color' => '#ffffff', 'accent_color' => '#ffffff',
        'text_transform' => 'none', 'font_size' => '13', 'letter_spacing' => '0', 'font_weight' => '500',
    ] ) ] ) ] ),
    row( [ col( [ nn( 'megamenu', [
        'menu_id' => (int) $menu_id, 'logo_text' => 'North', 'logo_text_color' => '#ffffff', 'logo_text_size' => '22',
        'text_color' => '#ffffff', 'hover_color' => '#ffffff', 'active_color' => '#ffffff', 'nav_bg' => 'transparent',
        'header_mode' => 'default', 'font_size' => '15', 'item_gap' => '22',
        'extra_link_1_label' => 'Sign in', 'extra_link_1_url' => '#',
        'extra_link_2_label' => 'Request a demo', 'extra_link_2_url' => '#', 'extra_link_2_button' => true,
        'extra_links_right' => true,
        'btn_bg' => '#ffffff', 'btn_color' => '#062C22', 'btn_radius' => [ 'tl' => 999, 'tr' => 999, 'br' => 999, 'bl' => 999 ],
        'search_icon' => false, 'social_facebook' => '', 'social_instagram' => '', 'social_x' => '', 'social_linkedin' => '', 'social_in_navbar' => false,
    ] ) ] ) ] ),
], 'fullbleed', 'remove-vertical', [ 'bg' => [ 'type' => 'solid', 'color' => '#062C22' ], 'padding_top' => '0', 'padding_bottom' => '0' ] );

// HERO
$sections[] = sec( [
    row( [ col( [ nn( 'northvideohero', [
        'eyebrow_text' => 'NORTH', 'crest_on' => true,
        'headline_text' => 'AI for business that turns complexity into clarity',
        'mock_mode' => 'video', 'video_poster' => $HERO_UI,
        'bg_fixed_image' => $GRASS, 'bg_fixed_from' => 38, 'bg_color' => '#062C22',
        'content_padding' => [ 'top' => 150, 'right' => 40, 'bottom' => 80, 'left' => 40 ],
    ] ) ] ) ] ),
], 'fullbleed', 'remove-vertical', [ 'padding_top' => '0', 'padding_bottom' => '0' ] );

// SUB-HERO "Empower" su erba parallasse
$sections[] = sec( [
    row( [ col( [
        nn( 'textmask', [
            'text' => 'Empower your workforce with AI agents that operate in lockstep with your people, data, and tools.',
            'multiline' => true,
            'font_size' => '58', 'font_size_tablet' => '42', 'font_size_mobile' => '28',
            'font_weight' => '500', 'text_transform' => 'none', 'letter_spacing' => '-1',
            'text_align' => 'center', 'text_fill' => '#ffffff', 'font_family' => '',
            'mask_mode' => 'none', 'blend_mode' => 'normal',
            'min_height' => '58vh', 'vertical_align' => 'center', 'bg_color' => 'rgba(0,0,0,0)',
            'video_url' => '', 'video_poster' => '',
            'scroll_animate' => true, 'scroll_opacity' => true, 'scroll_opacity_from' => '28', 'scroll_opacity_to' => '100',
            'scroll_scale' => false, 'scroll_blur' => false,
            'tile_padding' => [ 'top' => 20, 'right' => 60, 'bottom' => 6, 'left' => 60 ],
        ] ),
        nn( 'button', [ 'text' => 'Request a demo', 'url' => '#', 'bg_color' => '#ffffff', 'text_color' => '#062C22', 'border_radius' => '999', 'padding_x' => '30', 'padding_y' => '15', 'font_size' => '16', 'font_weight' => '600', 'alignment' => 'center' ] ),
    ] ) ] ),
], 'fullbleed', 'large', [
    'bg' => [ 'type' => 'image', 'image_url' => $GRASS, 'image_size' => 'cover', 'image_position' => 'center', 'image_repeat' => 'no-repeat', 'image_parallax' => true ],
    'padding_top' => '40', 'padding_bottom' => '120',
] );

// TRUST LOGOS (marquee)
$logos = [];
foreach ( [ 'Oracle', 'Dell Technologies', 'RBC', 'LG CNS', 'Fujitsu', 'Bell', 'Asana', 'SAP', 'Salesforce', 'Notion', 'TD Bank', 'McKinsey & Company', 'Accenture', 'BambooHR' ] as $i => $name ) {
    $logos[] = [ 'id' => 'lg-' . $i, 'title' => $name, 'url' => '', 'badge' => '', 'icon' => '', 'timestamp' => '' ];
}
$sections[] = sec( [
    row( [ col( [ nn( 'headline', [ 'heading' => 'Organizations that trust us', 'tag' => 'p', 'heading_size' => 'sm', 'heading_color' => '#6B7280', 'alignment' => 'center' ] ) ] ) ] ),
    row( [ col( [ nn( 'newsticker', [
        'items' => $logos, 'show_label' => false, 'animation_type' => 'marquee',
        'marquee_duration' => 38, 'marquee_gap' => 64, 'marquee_direction' => 'left',
        'bg_color' => 'transparent', 'text_color' => '#212121', 'font_size' => 22, 'font_weight' => '600',
        'separator' => '', 'height' => '60', 'pause_on_hover' => true,
    ] ) ] ) ] ),
], 'expand', 'large', [ 'bg' => [ 'type' => 'solid', 'color' => '#ffffff' ], 'padding_top' => '64', 'padding_bottom' => '64' ] );

// MORE MINDSPACE — header pulito + showcase 2 FOTO (donna + voronoi) + 3 card icona alte
$mind_cards = [
    [ 'icon' => 'gauge',        'title' => 'Scalability',  'description' => "Supercharge your team's ability to get more done with customizable AI agents and automated workflows.", 'link_text' => 'Learn more', 'link_url' => '#', 'counter' => '', 'counter_label' => '', 'title_accent' => '', 'media_image' => '', 'media_label' => '', 'footer_text' => '' ],
    [ 'icon' => 'sparkles',     'title' => 'Productivity', 'description' => 'Get instant, context-aware answers and generate reports securely grounded in your internal sources of truth.', 'link_text' => 'Learn more', 'link_url' => '#', 'counter' => '', 'counter_label' => '', 'title_accent' => '', 'media_image' => '', 'media_label' => '', 'footer_text' => '' ],
    [ 'icon' => 'shield-check', 'title' => 'Security',     'description' => 'Protect your business with private deployment options, as well as industry-leading security and data protection.', 'link_text' => 'Learn more', 'link_url' => '#', 'counter' => '', 'counter_label' => '', 'title_accent' => '', 'media_image' => '', 'media_label' => '', 'footer_text' => '' ],
];
$mm_radius = [ 'tl' => 20, 'tr' => 20, 'br' => 20, 'bl' => 20 ];
$sections[] = sec( [
    row( [
        col( [ nn( 'headline', [ 'heading' => 'More mindspace, less mayhem', 'tag' => 'h2', 'heading_size' => 'xl', 'heading_color' => '#212121', 'alignment' => 'left' ] ) ], '1-2' ),
        col( [ nn( 'text', [ 'content' => '<p>North sets the standard for business performance by helping teams automate work and accelerate decisions that drive results — all in one scalable, secure workspace.</p>', 'text_color' => '#4B5563', 'font_size' => '18' ] ) ], '1-2' ),
    ], '50-50' ),
    // Showcase 2 foto — pulito e minimale (donna alla scrivania + diagramma voronoi)
    row( [
        col( [ nn( 'image', [ 'image_url' => $IMG_WORK, 'aspect_ratio' => '4/3', 'object_fit' => 'cover', 'object_position' => 'center center', 'border_radius' => $mm_radius ] ) ], '1-2' ),
        col( [ nn( 'image', [ 'image_url' => $VORONOI, 'aspect_ratio' => '4/3', 'object_fit' => 'cover', 'object_position' => 'center center', 'border_radius' => $mm_radius ] ) ], '1-2' ),
    ], '50-50', 28, 'stretch' ),
    row( [ col( [ nn( 'spacer', [ 'height' => '20' ] ) ] ) ] ),
    // 3 card icona/testo, alte e pulite (niente foto → "2 foto, non 3")
    row( [ col( [ nn( 'info-cards', [
        'container_bg' => [ 'type' => 'none' ], 'container_padding' => 0, 'container_radius' => [ 'tl' => 0, 'tr' => 0, 'br' => 0, 'bl' => 0 ],
        'card_bg' => [ 'type' => 'solid', 'color' => '#F4F7F2' ], 'card_color' => '#212121', 'card_accent_color' => '#062C22',
        'card_padding' => 48, 'card_radius' => [ 'tl' => 18, 'tr' => 18, 'br' => 18, 'bl' => 18 ],
        'columns' => 3, 'items_gap' => 28,
        'show_media' => false, 'show_icon' => true, 'icon_color' => '#062C22', 'icon_bg_color' => '',
        'show_counter' => false, 'show_counter_label' => false, 'show_arrow' => false, 'show_footer' => false, 'show_link_text' => true, 'show_divider' => true,
        'title_font_family' => 'sans', 'title_size' => 26, 'title_weight' => '600', 'title_italic' => false, 'description_size' => 16,
        'items' => $mind_cards,
    ] ) ] ) ] ),
], 'expand', 'large', [ 'bg' => [ 'type' => 'solid', 'color' => '#ffffff' ], 'padding_top' => '80', 'padding_bottom' => '96' ] );

// ACCELERATE (stack-scroll)
$acc_cards = [
    [ 'eyebrow' => 'ADVANCED SEARCH AND RETRIEVAL', 'title' => 'Discover', 'accent' => '', 'text' => '<p>From basic Q&amp;A to complex decision making, North surfaces verifiable insights grounded in your data.</p>', 'media' => $UI1, 'media_label' => '', 'color' => '#0A2E22', 'text_color' => '#ffffff' ],
    [ 'eyebrow' => 'GENERATIVE AI', 'title' => 'Create', 'accent' => '', 'text' => '<p>Co-create documents, generate summaries, and produce tables and charts instantly.</p>', 'media' => $UI2, 'media_label' => '', 'color' => '#0A2E22', 'text_color' => '#ffffff' ],
    [ 'eyebrow' => 'WORKFLOW AUTOMATION', 'title' => 'Automate', 'accent' => '', 'text' => '<p>Deploy AI agents across teams to eliminate tedious tasks and accelerate complex workflows.</p>', 'media' => $UI3, 'media_label' => '', 'color' => '#0A2E22', 'text_color' => '#ffffff' ],
];
$sections[] = sec( [
    row( [
        col( [ nn( 'headline', [ 'heading' => 'Accelerate impact and outcomes', 'tag' => 'h2', 'heading_size' => 'xl', 'heading_color' => '#0A2E22', 'alignment' => 'left' ] ) ], '1-2' ),
        col( [ nn( 'text', [ 'content' => '<p>Enable seamless human-agent collaboration, automate routine tasks, and transform fragmented data into actionable insights.</p>', 'text_color' => '#33523f', 'font_size' => '18' ] ) ], '1-2' ),
    ], '50-50' ),
    row( [ col( [ nn( 'stackscroll', [ 'title_display' => true, 'show_number' => false, 'cards' => $acc_cards ] ) ] ) ] ),
], 'expand', 'large', [ 'bg' => [ 'type' => 'solid', 'color' => '#F1FDEA' ], 'padding_top' => '80', 'padding_bottom' => '96' ] );

// PUT AI TO WORK (tabs)
$use_items = [
    [ 'id' => 'uc-1', 'nav_label' => 'Legal', 'title' => '', 'text' => 'Accelerate contract review and redlining, ensure compliance, and uncover insights from large volumes of data to improve accuracy and reduce risk.', 'button_text' => '', 'button_url' => '', 'image' => $DOC ],
    [ 'id' => 'uc-2', 'nav_label' => 'Sales', 'title' => '', 'text' => 'Surface the right account intelligence, draft outreach, and turn scattered notes into next steps — so reps spend time selling, not searching.', 'button_text' => '', 'button_url' => '', 'image' => $UI1 ],
    [ 'id' => 'uc-3', 'nav_label' => 'Finance', 'title' => '', 'text' => 'Generate summaries, reconcile figures, and produce reports grounded in your systems of record, with full auditability.', 'button_text' => '', 'button_url' => '', 'image' => $UI2 ],
    [ 'id' => 'uc-4', 'nav_label' => 'Operations', 'title' => '', 'text' => 'Automate routine workflows across tools and teams to remove bottlenecks and keep work moving.', 'button_text' => '', 'button_url' => '', 'image' => $UI3 ],
];
$sections[] = sec( [
    row( [ col( [
        nn( 'headline', [ 'heading' => 'Put AI to work', 'tag' => 'h2', 'heading_size' => 'xl', 'heading_color' => '#212121', 'alignment' => 'center' ] ),
        nn( 'spacer', [ 'height' => '10' ] ),
        nn( 'text', [ 'content' => '<p>No matter the team, and no matter the task, North frees your teams to focus on the work that propels your business forward.</p>', 'text_color' => '#4B5563', 'font_size' => '18', 'alignment' => 'center' ] ),
    ] ) ] ),
    row( [ col( [ nn( 'switcherpanel', [
        'items' => $use_items, 'nav_position' => 'top', 'image_position' => 'right', 'image_bleed' => true, 'layout_mode' => 'split', 'preset' => 'custom',
        'panel_bg' => '#ffffff', 'panel_text_color' => '#212121', 'panel_title_color' => '#0A2E22', 'panel_text_size' => 18, 'panel_image_radius' => 16, 'panel_image_width' => 52,
        'nav_container_bg' => '#F3F4F6', 'nav_container_radius' => 999, 'nav_container_padding' => 6, 'nav_radius' => 999, 'nav_padding_x' => 22, 'nav_padding_y' => 10,
        'nav_active_bg' => '#1A1A1A', 'nav_active_color' => '#ffffff', 'nav_inactive_color' => '#4B5563', 'nav_indicator_type' => 'none', 'nav_uppercase' => false, 'nav_font_weight' => '600', 'nav_font_size' => 15,
        'hero_height' => 0, 'animation' => 'fade',
    ] ) ] ) ] ),
], 'expand', 'large', [ 'bg' => [ 'type' => 'solid', 'color' => '#ffffff' ], 'padding_top' => '90', 'padding_bottom' => '90' ] );

// SECURITY (bento)
$sec_items = [
    [ 'id' => 'sb-1', 'card_type' => 'image', 'image' => $CITY, 'title' => 'Secure by design', 'subtitle' => 'Safeguard sensitive data with a zero-trust security framework, precise access controls, and audit-ready visibility.', 'link' => '#' ],
    [ 'id' => 'sb-2', 'card_type' => 'icon', 'icon' => 'star', 'icon_color' => '#B9FBE7', 'card_bg' => '#0E1B2E', 'title' => 'Fully customizable', 'body' => "Design and deploy agent-powered workflows tailored to your team's tools, processes, and objectives.", 'image' => '', 'subtitle' => '' ],
    [ 'id' => 'sb-3', 'card_type' => 'graphic', 'card_bg' => '#0E1B2E', 'title' => 'Natively interoperable', 'body' => 'Connect North to existing tools, data, and monitoring systems with flexible APIs and built-in connectors.', 'image' => '', 'subtitle' => '' ],
    [ 'id' => 'sb-4', 'card_type' => 'image', 'image' => $GRAD, 'title' => 'Privately deployable', 'subtitle' => "Run North in your own VPC, on-prem environment, or through Cohere's secure Model Vault inference platform.", 'link' => '#' ],
];
$sections[] = sec( [
    row( [ col( [
        nn( 'headline', [ 'heading' => 'Private. Secure. Compliant.', 'tag' => 'h2', 'heading_size' => 'xl', 'heading_color' => '#ffffff', 'alignment' => 'left' ] ),
        nn( 'text', [ 'content' => '<p>This is what enterprise-ready AI looks like</p>', 'text_color' => 'rgba(255,255,255,0.8)', 'font_size' => '28' ] ),
        nn( 'spacer', [ 'height' => '28' ] ),
    ] ) ] ),
    row( [ col( [ nn( 'overlaygrid', [
        'items' => $sec_items, 'columns' => '2', 'columns_mobile' => '1', 'gap' => 'medium', 'height' => '360', 'match_height' => true, 'layout_mode' => 'uniform',
        'overlay_position' => 'bottom', 'overlay_style' => 'overlay-primary', 'overlay_color' => 'rgba(6,19,36,0.55)', 'overlay_gradient' => true, 'item_radius' => 18,
        'title_color' => '#ffffff', 'title_size' => 'h3', 'subtitle_color' => 'rgba(255,255,255,0.82)', 'subtitle_size' => 15,
        'show_cta' => true, 'cta_text' => 'Learn more', 'cta_style' => 'arrow', 'hover_effect' => 'zoom', 'shadow' => 'none',
    ] ) ] ) ] ),
], 'expand', 'large', [ 'bg' => [ 'type' => 'solid', 'color' => '#061324' ], 'padding_top' => '120', 'padding_bottom' => '120' ] );

// QUOTES — slider con grafica destra che morfa rettangolo↔parallelogramma a ogni slide
$sections[] = sec( [
    row( [ col( [ nn( 'northquoteslider', [
        'heading' => 'Why enterprises and innovators choose Cohere',
        'slant' => true, 'autoplay' => false,
        'bg_color' => '#ffffff', 'heading_color' => '#212121', 'quote_color' => '#212121',
        'author_color' => '#212121', 'role_color' => '#6B7280', 'logo_color' => '#062C22', 'arrow_color' => '#212121',
        'graphic_color' => '#0A2E22', 'graphic_line_color' => '#9DF5D6', 'quote_size' => 26,
        'items' => [
            [ 'quote' => "We jointly announced a customized platform, North for Banking, to enable RBC to accelerate the development of our genAI solutions securely and efficiently and we're pleased with our results to date. As our collaboration continues, we welcome the opportunity to use North for Banking to enable AI to unlock value across the enterprise.", 'author_name' => 'Dr. Foteini Agrafioti', 'author_role' => 'SVP, Data & AI & Chief Science Officer, RBC', 'logo_text' => 'RBC' ],
            [ 'quote' => 'North lets our teams move from question to verified answer in seconds — grounded in our own data, without the risk, so our people can focus on judgment instead of search.', 'author_name' => 'Head of Data', 'author_role' => 'Global Enterprise', 'logo_text' => '' ],
            [ 'quote' => 'The combination of private deployment and verifiable, grounded answers is exactly what a regulated environment needs from enterprise AI.', 'author_name' => 'VP, Engineering', 'author_role' => 'Financial Services', 'logo_text' => '' ],
        ],
    ] ) ] ) ] ),
], 'fullbleed', 'remove-vertical', [ 'bg' => [ 'type' => 'solid', 'color' => '#ffffff' ], 'padding_top' => '96', 'padding_bottom' => '96' ] );

// NEWS (postgrid + notch)
$sections[] = sec( [
    row( [ col( [ nn( 'headline', [ 'heading' => 'News and insights', 'tag' => 'h2', 'heading_size' => 'lg', 'heading_color' => '#212121', 'alignment' => 'left' ] ) ] ) ] ),
    row( [ col( [ nn( 'postgrid', [
        'post_type' => 'post', 'posts_per_page' => '3', 'columns' => '3', 'gap' => 'medium',
        'corner_cut' => true, 'corner_size' => 38, 'card_style' => 'default', 'card_primary_bg' => '#EFECE6',
        'image_height' => '200', 'image_radius' => '0', 'card_radius' => '6',
        'show_image' => true, 'show_category' => false, 'show_excerpt' => false, 'show_meta' => true,
        'link_text' => 'Read more', 'link_style' => 'text',
    ] ) ] ) ] ),
], 'expand', 'large', [ 'bg' => [ 'type' => 'solid', 'color' => '#ffffff' ], 'padding_top' => '40', 'padding_bottom' => '96' ] );

// CTA "Accelerate your AI roadmap"
$sections[] = sec( [
    row( [
        col( [
            nn( 'headline', [ 'heading' => 'Accelerate your AI roadmap', 'tag' => 'h2', 'heading_size' => 'xl', 'heading_color' => '#ffffff', 'alignment' => 'left' ] ),
            nn( 'spacer', [ 'height' => '14' ] ),
            nn( 'text', [ 'content' => "<p>Connect with an expert to explore how Cohere's products can fit your stack, data, and goals.</p>", 'text_color' => 'rgba(255,255,255,0.86)', 'font_size' => '18' ] ),
            nn( 'spacer', [ 'height' => '20' ] ),
            nn( 'iconlist', [
                'items' => [
                    [ 'id' => 'ck1', 'icon' => 'check', 'text' => 'Align AI to your workflows and use cases', 'color' => '' ],
                    [ 'id' => 'ck2', 'icon' => 'check', 'text' => 'Choose deployment options that fit your infrastructure', 'color' => '' ],
                    [ 'id' => 'ck3', 'icon' => 'check', 'text' => 'Move from pilot to production — safely and securely', 'color' => '' ],
                ],
                'icon_color' => '#ffffff', 'text_color' => 'rgba(255,255,255,0.92)', 'text_size' => '16', 'icon_size' => '20', 'gap' => '14',
            ] ),
        ], '1-2' ),
        col( [ nn( 'form', [
            'fields' => [
                [ 'id' => 'cf1', 'field_type' => 'text', 'label' => '', 'placeholder' => 'First name', 'name' => 'first', 'required' => true, 'width' => '1-2', 'options' => '', 'icon' => '' ],
                [ 'id' => 'cf2', 'field_type' => 'text', 'label' => '', 'placeholder' => 'Last name', 'name' => 'last', 'required' => true, 'width' => '1-2', 'options' => '', 'icon' => '' ],
                [ 'id' => 'cf3', 'field_type' => 'email', 'label' => '', 'placeholder' => 'Business email', 'name' => 'email', 'required' => true, 'width' => '1-1', 'options' => '', 'icon' => '' ],
                [ 'id' => 'cf4', 'field_type' => 'text', 'label' => '', 'placeholder' => 'Job Title', 'name' => 'title', 'required' => false, 'width' => '1-1', 'options' => '', 'icon' => '' ],
                [ 'id' => 'cf5', 'field_type' => 'select', 'label' => '', 'placeholder' => 'Country/Region', 'name' => 'country', 'required' => false, 'width' => '1-2', 'options' => "United States\nCanada\nUnited Kingdom\nItaly\nGermany\nFrance\nOther", 'icon' => '' ],
                [ 'id' => 'cf6', 'field_type' => 'text', 'label' => '', 'placeholder' => 'Phone number (optional)', 'name' => 'phone', 'required' => false, 'width' => '1-2', 'options' => '', 'icon' => '' ],
                [ 'id' => 'cf7', 'field_type' => 'select', 'label' => '', 'placeholder' => 'Company size (no. of employees)', 'name' => 'size', 'required' => false, 'width' => '1-1', 'options' => "1-50\n51-200\n201-1000\n1001-5000\n5000+", 'icon' => '' ],
                [ 'id' => 'cf8', 'field_type' => 'select', 'label' => '', 'placeholder' => 'Product of interest', 'name' => 'product', 'required' => false, 'width' => '1-1', 'options' => "North\nCommand\nEmbed\nRerank\nOther", 'icon' => '' ],
                [ 'id' => 'cf9', 'field_type' => 'textarea', 'label' => '', 'placeholder' => 'How do you plan to use AI?', 'name' => 'usecase', 'required' => false, 'width' => '1-1', 'options' => '', 'icon' => '' ],
            ],
            'submit_text' => 'Submit', 'submit_alignment' => 'left', 'submit_bg' => '#1a1a1a', 'submit_color' => '#ffffff', 'submit_radius' => '999', 'submit_font_weight' => '600',
            'bg' => [ 'type' => 'solid', 'color' => '#ffffff' ], 'tile_padding' => [ 'top' => 40, 'right' => 40, 'bottom' => 40, 'left' => 40 ],
            'label_color' => '#6b7280', 'input_color' => '#111827', 'input_border_color' => '#d1d5db', 'input_radius' => '10', 'form_max_width' => '0', 'gap' => '14',
            'privacy_text' => 'Please refer to our Privacy Policy for details.',
        ] ) ], '1-2' ),
    ], '50-50', 40, 'top' ),
], 'fullbleed', 'large', [ 'bg' => [ 'type' => 'solid', 'color' => '#062C22' ], 'padding_top' => '96', 'padding_bottom' => '96' ] );

// FOOTER / Newsletter "AI moves fast"
$footHead = function ( $txt ) { return nn( 'headline', [ 'heading' => $txt, 'tag' => 'p', 'heading_size' => 'sm', 'heading_color' => '#8a8f99', 'alignment' => 'left' ] ); };
$footCol = function ( $items, $color = '#cfd2d8' ) { return nn( 'iconlist', [ 'items' => $items, 'icon_size' => '1', 'icon_color' => 'transparent', 'text_color' => $color, 'text_size' => '15', 'gap' => '16', 'layout' => 'vertical' ] ); };
$sections[] = sec( [
    row( [ col( [
        nn( 'newsletter', [ 'title' => 'AI moves fast', 'subtitle' => "We'll keep you up to date with the latest.", 'email_placeholder' => 'Enter your business email', 'button_text' => '→', 'layout' => 'stacked', 'max_width' => '460', 'alignment' => 'left', 'bg_color' => 'transparent', 'btn_bg' => 'transparent', 'title_color' => '#ff7759', 'text_color' => '#9aa0aa' ] ),
    ] ) ] ),
    row( [ col( [ nn( 'spacer', [ 'height' => '48' ] ) ] ) ] ),
    row( [
        col( [ $footHead( 'Products' ), $footCol( $flinks( [ 'North', 'Compass', 'Command', 'Transcribe', 'Embed', 'Rerank', 'Customization', 'Pricing' ] ) ) ], '1-4' ),
        col( [ $footHead( 'Solutions' ), $footCol( $flinks( [ 'Technology', 'Energy and Utilities', 'Financial Services', 'Healthcare and Life Sciences', 'Manufacturing', 'Public Sector', 'Telecommunications', 'Deployment Options', 'Model Vault' ] ) ) ], '1-4' ),
        col( [ $footHead( 'Resources' ), $footCol( $flinks( [ 'Blog', 'Customer Stories', 'Developers', 'Events', 'On-Demand Events', 'Merch Store', 'LLM University', 'Documentation', 'Release Notes', 'Models Overview' ] ) ) ], '1-4' ),
        col( [ $footHead( 'Company' ), $footCol( $flinks( [ 'About', 'Careers', 'Research', 'Newsroom', 'Partners', 'Security', 'Trust Center', 'Legal Center' ] ) ) ], '1-4' ),
    ], '25-25-25-25', 40, 'top' ),
    row( [ col( [ nn( 'divider', [ 'color' => 'rgba(255,255,255,0.12)', 'thickness' => '1', 'margin_top' => '48', 'margin_bottom' => '24' ] ) ] ) ] ),
    row( [ col( [ nn( 'text', [ 'content' => '<p>Cohere © 2026 &nbsp;·&nbsp; Privacy &nbsp;·&nbsp; Terms of Use &nbsp;·&nbsp; Manage Cookies &nbsp;·&nbsp; English</p>', 'text_color' => '#7c828c', 'font_size' => '13' ] ) ] ) ] ),
], 'fullbleed', 'large', [ 'bg' => [ 'type' => 'solid', 'color' => '#17171C' ], 'padding_top' => '80', 'padding_bottom' => '56' ] );

// ── upsert olo_template ──────────────────────────────────────────────────────
$db = new Olo_Database();
global $wpdb;
$tbl = $wpdb->prefix . 'olo_templates';
$tpl_title = 'North — Home';
$existing  = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$tbl} WHERE title = %s", $tpl_title ) );
if ( $existing ) {
    $db->update_template( (int) $existing, [ 'content' => $sections, 'status' => 'published', 'type' => 'page' ] );
    $tpl_id = (int) $existing;
} else {
    $tpl_id = (int) $db->create_template( [ 'title' => $tpl_title, 'type' => 'page', 'content' => $sections, 'status' => 'published' ] );
}

// ── upsert pagina /north-demo/ ───────────────────────────────────────────────
$page = get_page_by_path( 'north-demo' );
$content = '[olo_template id="' . $tpl_id . '"]';
$page_data = [ 'post_title' => 'North Demo', 'post_name' => 'north-demo', 'post_status' => 'publish', 'post_type' => 'page', 'post_content' => $content ];
if ( $page ) { $page_data['ID'] = $page->ID; $page_id = wp_update_post( $page_data ); }
else { $page_id = wp_insert_post( $page_data ); }

// template full-width (canvas) se disponibile
$tpls = wp_get_theme()->get_page_templates( null, 'page' );
$chosen = '';
foreach ( (array) $tpls as $file => $name ) {
    if ( preg_match( '/canvas|full|blank|olob|builder|elementor/i', $name . ' ' . $file ) ) { $chosen = $file; break; }
}
if ( $chosen ) { update_post_meta( $page_id, '_wp_page_template', $chosen ); }

echo "TEMPLATE_ID=$tpl_id\n";
echo "PAGE_ID=$page_id\n";
echo "PAGE_TEMPLATE=" . ( $chosen ?: '(default)' ) . "\n";
echo "URL=" . get_permalink( $page_id ) . "\n";
echo "SECTIONS=" . count( $sections ) . "\n";
echo "DONE\n";
