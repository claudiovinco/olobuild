<?php
/**
 * Smoke-test render delle tile North (nuova + estese). Eseguire con:
 *   wp eval-file north-smoke.php
 * Stampa OK/ERRORE per ogni tile. Cattura Throwable.
 */
$tests = [
    [ 'Olo_NorthVideoHero_Tile', [
        'eyebrow_text' => 'NORTH', 'crest_on' => true,
        'headline_text' => 'AI for business that turns complexity into clarity',
        'mock_mode' => 'video', 'video_poster' => 'https://example.com/p.jpg',
        'bg_fixed_image' => 'https://example.com/grass.jpg', 'bg_fixed_from' => 42,
    ] ],
    [ 'Olo_Newsticker_Tile', [
        'animation_type' => 'marquee', 'logo_grayscale' => true, 'logo_height' => 32, 'logo_opacity' => 80,
        'items' => [ [ 'logo' => 'https://example.com/oracle.svg', 'title' => 'Oracle', 'url' => '' ], [ 'title' => 'Plain', 'url' => '#' ] ],
    ] ],
    [ 'Olo_InfoCards_Tile', [
        'show_link_text' => true,
        'items' => [ [ 'title' => 'Scalability', 'description' => 'x', 'link_text' => 'Learn more', 'link_url' => '#' ] ],
    ] ],
    [ 'Olo_OverlayGrid_Tile', [
        'items' => [
            [ 'card_type' => 'icon', 'icon' => 'star', 'icon_color' => '#B9FBE7', 'title' => 'Fully customizable', 'body' => 'desc', 'card_bg' => '#0E1B2E' ],
            [ 'card_type' => 'graphic', 'title' => 'Natively interoperable', 'body' => 'desc', 'card_bg' => '#0E1B2E' ],
            [ 'card_type' => 'image', 'image' => '', 'title' => 'Secure by design' ],
        ],
    ] ],
    [ 'Olo_Testimonial_Tile', [
        'layout' => 'carousel',
        'items' => [ [ 'logo' => 'https://example.com/rbc.svg', 'quote' => 'Great.', 'author_name' => 'Foteini', 'author_role' => 'SVP' ] ],
    ] ],
    [ 'Olo_PostGrid_Tile', [ 'corner_cut' => true, 'corner_size' => 32, 'columns' => 3 ] ],
    [ 'Olo_Stackscroll_Tile', [
        'title_display' => true,
        'cards' => [ [ 'eyebrow' => 'ADVANCED SEARCH AND RETRIEVAL', 'title' => 'Discover', 'text' => 'x' ] ],
    ] ],
];

foreach ( $tests as $t ) {
    list( $cls, $settings ) = $t;
    if ( ! class_exists( $cls ) ) { echo "MISSING CLASS: $cls\n"; continue; }
    try {
        $tile = new $cls();
        $ref  = new ReflectionMethod( $cls, 'render' );
        $args = $ref->getNumberOfParameters() >= 2 ? [ $settings, [] ] : [ $settings ];
        $html = $ref->invokeArgs( $tile, $args );
        $len  = strlen( (string) $html );
        echo "OK  $cls  (" . $len . " bytes)\n";
    } catch ( \Throwable $e ) {
        echo "ERRORE  $cls : " . $e->getMessage() . "  @ " . basename( $e->getFile() ) . ":" . $e->getLine() . "\n";
    }
}
echo "SMOKE DONE\n";
