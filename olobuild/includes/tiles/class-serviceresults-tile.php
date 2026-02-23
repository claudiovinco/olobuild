<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_ServiceResults_Tile extends Olo_Tile_Base {

    protected $type     = 'serviceresults';
    protected $name     = 'Risultati strutture';
    protected $icon     = 'dashicons-list-view';
    protected $category = 'dynamic';
    protected $defaults = [
        // Layout
        'layout'            => 'map-left',
        'map_height'        => '500',
        'map_sticky'        => true,
        'tile_layer'        => 'positron',
        'default_zoom'      => '10',
        'fit_bounds'        => true,
        // Filtri
        'visible_filters'   => 'valley,guests,bedrooms,altitude,amenities',
        'altitude_ranges'   => '0-1000,1000-1500,1500-2000,2000-9999',
        'guests_ranges'     => '1-2,3-4,5-6,7-99',
        'amenities_list'    => 'wifi,fireplace,parking,sauna,hottub,bbq,garden,pets',
        // Card
        'columns'           => '2',
        'columns_mobile'    => '1',
        'gap'               => 'medium',
        'card_style'        => 'default',
        'card_primary_bg'   => '#6366F1',
        'match_height'      => false,
        'image_height'      => '180',
        'image_radius'      => '0',
        'card_radius'       => '8',
        'card_content'      => 'excerpt,stats,price',
        'excerpt_length'    => '15',
        'price_prefix'      => '&euro;',
        'price_suffix'      => '/notte',
        'link_text'         => 'Dettagli',
        'link_style'        => 'text',
        // Hover
        'hover_effect'      => 'none',
        'fx_kenburns'       => false,
        'fx_kenburns_speed' => '20',
        'fx_kenburns_scale' => '1.12',
        // Overlay
        'overlay_gradient'  => false,
        'overlay_color'     => '#000000',
        'overlay_opacity'   => '50',
        'overlay_direction' => 'bottom',
        'overlay_height'    => '50',
        // Testo
        'body_padding'      => '15',
        'title_size'        => '1',
        'title_color'       => '',
        'excerpt_size'      => '0.92',
        'excerpt_color'     => '',
        'body_bg'           => '',
        'body_bg_opacity'   => '100',
        // Badge / ribbon
        'show_service_opening' => false,
        'opening_bg_annual'    => '#059669',
        'opening_bg_seasonal'  => '#d97706',
        'opening_size'         => '11',
        'ribbon_field'         => '',
        'ribbon_position'      => 'top-right',
        'ribbon_bg'            => '#e11d48',
        'ribbon_color'         => '#ffffff',
        // Ordinamento
        'random_order'      => false,
        // Paginazione
        'items_per_page'    => '8',
        'pagination_style'  => 'numbers',
        // Colori
        'marker_color'      => '#e11d48',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $results_id = 'olo-svresults-' . wp_unique_id();
        $services   = $this->query_services( $s );

        // Parse visible_filters CSV
        $vf = array_flip( array_filter( array_map( 'trim', explode( ',', $s['visible_filters'] ?? '' ) ) ) );
        $has_filters = ! empty( $vf );

        // Parse card_content CSV
        $cc = array_flip( array_filter( array_map( 'trim', explode( ',', $s['card_content'] ?? '' ) ) ) );

        // Valleys from data
        $valleys = [];
        foreach ( $services as $svc ) {
            if ( ! empty( $svc['valley'] ) ) {
                $valleys[ $svc['valley'] ] = true;
            }
        }
        $valleys = array_keys( $valleys );
        sort( $valleys );

        // Club categories from data
        $club_cats = [];
        foreach ( $services as $svc ) {
            if ( ! empty( $svc['club_categories'] ) ) {
                foreach ( $svc['club_categories'] as $cc ) {
                    $club_cats[ $cc ] = true;
                }
            }
        }
        $club_cats = array_keys( $club_cats );
        sort( $club_cats );

        // Ranges
        $alt_ranges = array_filter( array_map( 'trim', explode( ',', $s['altitude_ranges'] ) ) );
        $guests_ranges = array_filter( array_map( 'trim', explode( ',', $s['guests_ranges'] ) ) );
        $amenities = array_filter( array_map( 'trim', explode( ',', $s['amenities_list'] ) ) );

        $amenity_labels = class_exists( 'Olo_Amenities_Catalog' )
            ? Olo_Amenities_Catalog::get_all_labels()
            : [
                'wifi' => 'WiFi', 'fireplace' => 'Camino', 'parking' => 'Parcheggio',
                'kitchen' => 'Cucina', 'tv' => 'TV', 'bbq' => 'BBQ', 'terrace' => 'Terrazza',
                'heating' => 'Riscaldamento', 'sauna' => 'Sauna', 'hottub' => 'Idromassaggio',
                'ski' => 'Sci', 'garden' => 'Giardino', 'pets' => 'Animali',
                'washer' => 'Lavatrice', 'highchair' => 'Seggiolone', 'aircon' => 'Aria Cond.',
                'dishwasher' => 'Lavastoviglie', 'linens' => 'Biancheria',
                'towels' => 'Asciugamani', 'pool' => 'Piscina', 'hiking' => 'Escursioni',
            ];

        $config = [
            'services'          => $services,
            'layout'            => $s['layout'],
            'tileLayer'         => $s['tile_layer'],
            'defaultZoom'       => intval( $s['default_zoom'] ),
            'fitBounds'         => ! empty( $s['fit_bounds'] ),
            'itemsPerPage'      => intval( $s['items_per_page'] ),
            'paginationStyle'   => $s['pagination_style'],
            'randomOrder'       => ! empty( $s['random_order'] ),
            'columns'           => intval( $s['columns'] ),
            'columnsMobile'     => intval( $s['columns_mobile'] ),
            'gap'               => $s['gap'],
            'cardStyle'         => $s['card_style'],
            'imageHeight'       => intval( $s['image_height'] ),
            'imageRadius'       => intval( $s['image_radius'] ),
            'cardRadius'        => intval( $s['card_radius'] ),
            'showExcerpt'       => isset( $cc['excerpt'] ),
            'excerptLength'     => intval( $s['excerpt_length'] ),
            'showStats'         => isset( $cc['stats'] ),
            'showPrice'         => isset( $cc['price'] ),
            'pricePrefix'       => $s['price_prefix'],
            'priceSuffix'       => $s['price_suffix'],
            'linkText'          => $s['link_text'],
            'linkStyle'         => $s['link_style'],
            'markerColor'       => $s['marker_color'],
            'amenityLabels'     => $amenity_labels,
            'mapHeight'         => intval( $s['map_height'] ),
            // Hover / Ken Burns
            'hoverEffect'       => $s['hover_effect'],
            'fxKenburns'        => ! empty( $s['fx_kenburns'] ),
            // Overlay
            'overlayGradient'   => ! empty( $s['overlay_gradient'] ),
            'overlayColor'      => $s['overlay_color'],
            'overlayOpacity'    => intval( $s['overlay_opacity'] ),
            'overlayDirection'  => $s['overlay_direction'],
            'overlayHeight'     => intval( $s['overlay_height'] ),
            // Testo
            'bodyPadding'       => intval( $s['body_padding'] ),
            'titleSize'         => floatval( $s['title_size'] ),
            'titleColor'        => $s['title_color'],
            'excerptSize'       => floatval( $s['excerpt_size'] ),
            'excerptColor'      => $s['excerpt_color'],
            'bodyBg'            => $s['body_bg'],
            'bodyBgOpacity'     => intval( $s['body_bg_opacity'] ),
            // Card
            'matchHeight'       => ! empty( $s['match_height'] ),
            'cardPrimaryBg'     => $s['card_primary_bg'],
            // Badge / ribbon
            'showServiceOpening' => ! empty( $s['show_service_opening'] ),
            'openingBgAnnual'   => $s['opening_bg_annual'],
            'openingBgSeasonal' => $s['opening_bg_seasonal'],
            'openingSize'       => intval( $s['opening_size'] ),
            'ribbonPosition'    => $s['ribbon_position'],
            'ribbonBg'          => $s['ribbon_bg'],
            'ribbonColor'       => $s['ribbon_color'],
            // Label tradotte per il JS client-side
            'labels' => [
                'ospiti'       => olo_t( 'ospiti' ),
                'cam'          => olo_t( 'cam.' ),
                'bagni'        => olo_t( 'bagni' ),
                'caricaAltro'  => olo_t( 'Carica altro' ),
                'dettagli'     => olo_t( 'Dettagli' ),
            ],
        ];

        $layout_class = 'olo-svresults--' . esc_attr( $s['layout'] );
        $map_height   = intval( $s['map_height'] );
        $uid          = $results_id;

        // Style variables
        $hover_effect   = $s['hover_effect'] ?? 'none';
        $kenburns_on    = ! empty( $s['fx_kenburns'] );
        $kenburns_speed = max( 10, min( 40, absint( $s['fx_kenburns_speed'] ?? 20 ) ) );
        $kenburns_scale = max( 1.05, min( 1.25, floatval( $s['fx_kenburns_scale'] ?? 1.12 ) ) );
        $image_radius   = max( 0, absint( $s['image_radius'] ?? 0 ) );
        $image_height   = max( 100, absint( $s['image_height'] ?? 180 ) );
        $card_radius    = max( 0, absint( $s['card_radius'] ?? 8 ) );
        $body_padding   = max( 0, min( 40, absint( $s['body_padding'] ?? 15 ) ) );
        $title_size     = max( 0.7, min( 2.5, floatval( $s['title_size'] ?? 1 ) ) );
        $excerpt_size   = max( 0.7, min( 1.5, floatval( $s['excerpt_size'] ?? 0.92 ) ) );
        $title_color    = $s['title_color'] ?? '';
        $excerpt_color  = $s['excerpt_color'] ?? '';
        $body_bg        = $s['body_bg'] ?? '';
        $body_bg_opacity = max( 0, min( 100, absint( $s['body_bg_opacity'] ?? 100 ) ) );
        $ribbon_bg      = $s['ribbon_bg'] ?? '#e11d48';
        $ribbon_color   = $s['ribbon_color'] ?? '#ffffff';

        ob_start();
        ?>
        <style>
            #<?php echo $uid; ?> .olo-svresults-card-img { transition: transform 0.5s ease, filter 0.5s ease; width: 100%; object-fit: cover; display: block; }
            /* Hover effects */
            #<?php echo $uid; ?> .olo-svresults-card:hover .olo-svr-hover-zoom { transform: scale(1.08); }
            #<?php echo $uid; ?> .olo-svresults-card:hover .olo-svr-hover-zoom-rotate { transform: scale(1.08) rotate(2deg); }
            #<?php echo $uid; ?> .olo-svr-hover-brightness { filter: brightness(0.7); }
            #<?php echo $uid; ?> .olo-svresults-card:hover .olo-svr-hover-brightness { filter: brightness(1); }
            #<?php echo $uid; ?> .olo-svr-hover-desaturate { filter: grayscale(100%); }
            #<?php echo $uid; ?> .olo-svresults-card:hover .olo-svr-hover-desaturate { filter: grayscale(0%); }
            #<?php echo $uid; ?> .olo-svr-hover-blur-in { filter: blur(3px); }
            #<?php echo $uid; ?> .olo-svresults-card:hover .olo-svr-hover-blur-in { filter: blur(0); }
            #<?php echo $uid; ?> .olo-svresults-card:hover .olo-svr-hover-slide-up { transform: translateY(-8px) scale(1.02); }
            #<?php echo $uid; ?> .olo-svr-hover-glow { filter: brightness(1); }
            #<?php echo $uid; ?> .olo-svresults-card:hover .olo-svr-hover-glow { filter: brightness(1.15) saturate(1.2); box-shadow: 0 0 20px rgba(255,255,255,0.3); }
            #<?php echo $uid; ?> .olo-svresults-card { perspective: 800px; }
            #<?php echo $uid; ?> .olo-svresults-card:hover .olo-svr-hover-tilt { transform: rotateY(4deg) rotateX(2deg) scale(1.03); }
            <?php if ( $kenburns_on ) : ?>
            @keyframes olo-kb-<?php echo $uid; ?> {
                0% { transform: scale(1); }
                50% { transform: scale(<?php echo $kenburns_scale; ?>); }
                100% { transform: scale(1); }
            }
            #<?php echo $uid; ?> .olo-svr-kenburns { animation: olo-kb-<?php echo $uid; ?> <?php echo $kenburns_speed; ?>s ease-in-out infinite; }
            #<?php echo $uid; ?> .olo-svresults-card:nth-child(2n) .olo-svr-kenburns { animation-delay: -<?php echo round( $kenburns_speed / 3, 1 ); ?>s; }
            #<?php echo $uid; ?> .olo-svresults-card:nth-child(3n) .olo-svr-kenburns { animation-delay: -<?php echo round( $kenburns_speed * 2 / 3, 1 ); ?>s; }
            <?php endif; ?>
            /* Ribbon */
            #<?php echo $uid; ?> .olo-svr-ribbon { position: absolute; z-index: 2; font-size: 11px; font-weight: 700; padding: 4px 12px; text-transform: uppercase; letter-spacing: 0.5px; }
            #<?php echo $uid; ?> .olo-svr-ribbon--top-right { top: 0; right: 14px; border-radius: 0 0 4px 4px; }
            #<?php echo $uid; ?> .olo-svr-ribbon--top-left { top: 0; left: 14px; border-radius: 0 0 4px 4px; }
            /* Opening badge */
            #<?php echo $uid; ?> .olo-svr-opening { position: absolute; bottom: 8px; left: 8px; z-index: 2; color: #fff; font-weight: 600; padding: 2px 10px; border-radius: 4px; line-height: 1.4; }
            <?php if ( ! empty( $s['match_height'] ) ) : ?>
            #<?php echo $uid; ?> .olo-svresults-card { display: flex; flex-direction: column; height: 100%; }
            #<?php echo $uid; ?> .olo-svresults-card-body { flex: 1; display: flex; flex-direction: column; }
            #<?php echo $uid; ?> .olo-svresults-card-footer { margin-top: auto; }
            <?php endif; ?>
            <?php if ( $s['card_style'] === 'primary' ) :
                $primary_bg = esc_attr( $s['card_primary_bg'] ?? '#6366F1' );
            ?>
            #<?php echo $uid; ?> .olo-svresults-card--primary { background-color: <?php echo $primary_bg; ?> !important; border-color: <?php echo $primary_bg; ?>; }
            #<?php echo $uid; ?> .olo-svresults-card--primary .olo-svresults-card-title,
            #<?php echo $uid; ?> .olo-svresults-card--primary .olo-svresults-card-title a,
            #<?php echo $uid; ?> .olo-svresults-card--primary .olo-svresults-card-body,
            #<?php echo $uid; ?> .olo-svresults-card--primary .olo-svresults-card-stats,
            #<?php echo $uid; ?> .olo-svresults-card--primary .olo-svresults-card-excerpt,
            #<?php echo $uid; ?> .olo-svresults-card--primary .olo-svresults-card-price,
            #<?php echo $uid; ?> .olo-svresults-card--primary .olo-svresults-card-link { color: #fff !important; }
            #<?php echo $uid; ?> .olo-svresults-card--primary .olo-svresults-card-btn { background: rgba(255,255,255,0.2); border-color: #fff; color: #fff; }
            <?php endif; ?>
            /* Button link */
            #<?php echo $uid; ?> .olo-svresults-card-btn { display: inline-block; padding: 6px 16px; border: 1px solid var(--olo-color-primary, #6366F1); border-radius: 4px; font-size: 0.85em; font-weight: 500; color: var(--olo-color-primary, #6366F1); text-decoration: none; transition: all 0.2s; }
            #<?php echo $uid; ?> .olo-svresults-card-btn:hover { background: var(--olo-color-primary, #6366F1); color: #fff; }
            /* Card-clickable wrap */
            #<?php echo $uid; ?> .olo-svresults-card-wrap { text-decoration: none; color: inherit; display: block; }
            #<?php echo $uid; ?> .olo-svresults-card-wrap:hover .olo-svresults-card { box-shadow: 0 4px 16px rgba(0,0,0,0.1); }
        </style>

        <div id="<?php echo esc_attr( $results_id ); ?>"
             class="olo-svresults <?php echo $layout_class; ?>"
             data-svresults-config="<?php echo base64_encode( wp_json_encode( $config ) ); ?>">

            <?php if ( $has_filters ) : ?>
            <div class="olo-svresults-filters">
                <div class="olo-svresults-filters-row">

                    <?php if ( isset( $vf['valley'] ) && ! empty( $valleys ) ) : ?>
                    <select class="olo-svresults-select" data-filter="valley">
                        <option value=""><?php echo esc_html( olo_t( 'Località' ) ); ?></option>
                        <?php foreach ( $valleys as $v ) : ?>
                        <option value="<?php echo esc_attr( $v ); ?>"><?php echo esc_html( $v ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php endif; ?>

                    <?php if ( isset( $vf['guests'] ) && ! empty( $guests_ranges ) ) : ?>
                    <select class="olo-svresults-select" data-filter="guests">
                        <option value=""><?php echo esc_html( olo_t( 'Ospiti' ) ); ?></option>
                        <?php foreach ( $guests_ranges as $range ) :
                            $parts = explode( '-', $range );
                            if ( count( $parts ) !== 2 ) continue;
                            $min = intval( $parts[0] );
                            $max = intval( $parts[1] );
                            $label = $max >= 99 ? $min . '+' : $min . '-' . $max;
                        ?>
                        <option value="<?php echo esc_attr( $range ); ?>"><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php endif; ?>

                    <?php if ( isset( $vf['bedrooms'] ) ) : ?>
                    <select class="olo-svresults-select" data-filter="bedrooms">
                        <option value=""><?php echo esc_html( olo_t( 'Camere' ) ); ?></option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4+">4+</option>
                    </select>
                    <?php endif; ?>

                    <?php if ( isset( $vf['altitude'] ) && ! empty( $alt_ranges ) ) : ?>
                    <select class="olo-svresults-select" data-filter="altitude">
                        <option value=""><?php echo esc_html( olo_t( 'Altitudine' ) ); ?></option>
                        <?php foreach ( $alt_ranges as $range ) :
                            $parts = explode( '-', $range );
                            if ( count( $parts ) !== 2 ) continue;
                            $min = intval( $parts[0] );
                            $max = intval( $parts[1] );
                            if ( $min === 0 ) $label = '< ' . number_format( $max, 0, ',', '.' ) . 'm';
                            elseif ( $max >= 9000 ) $label = '> ' . number_format( $min, 0, ',', '.' ) . 'm';
                            else $label = number_format( $min, 0, ',', '.' ) . ' - ' . number_format( $max, 0, ',', '.' ) . 'm';
                        ?>
                        <option value="<?php echo esc_attr( $range ); ?>"><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php endif; ?>

                    <?php if ( isset( $vf['type'] ) ) : ?>
                    <select class="olo-svresults-select" data-filter="type">
                        <option value=""><?php echo esc_html( olo_t( 'Tipologia' ) ); ?></option>
                        <option value="baita"><?php echo esc_html( olo_t( 'Baita' ) ); ?></option>
                        <option value="apartment"><?php echo esc_html( olo_t( 'Appartamento' ) ); ?></option>
                        <option value="room"><?php echo esc_html( olo_t( 'Camera' ) ); ?></option>
                        <option value="chalet"><?php echo esc_html( olo_t( 'Chalet' ) ); ?></option>
                    </select>
                    <?php endif; ?>

                    <?php if ( isset( $vf['club'] ) && ! empty( $club_cats ) ) : ?>
                    <select class="olo-svresults-select" data-filter="club">
                        <option value=""><?php echo esc_html( olo_t( 'Club di Prodotto' ) ); ?></option>
                        <?php foreach ( $club_cats as $cc ) : ?>
                        <option value="<?php echo esc_attr( $cc ); ?>"><?php echo esc_html( $cc ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php endif; ?>

                </div>

                <?php if ( isset( $vf['amenities'] ) && ! empty( $amenities ) ) : ?>
                <div class="olo-svresults-amenities">
                    <?php foreach ( $amenities as $slug ) :
                        $label = $amenity_labels[ $slug ] ?? ucfirst( $slug );
                    ?>
                    <button type="button" class="olo-svresults-amenity-pill" data-amenity="<?php echo esc_attr( $slug ); ?>">
                        <?php echo esc_html( $label ); ?>
                    </button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="olo-svresults-counter">
                    <span data-results-count><?php echo count( $services ); ?></span> / <?php echo count( $services ); ?> <?php echo esc_html( olo_t( 'strutture' ) ); ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="olo-svresults-body">
                <?php if ( $s['layout'] !== 'cards-only' ) : ?>
                <div class="olo-svresults-map-wrap" style="height:<?php echo $map_height; ?>px;<?php if ( ! empty( $s['map_sticky'] ) ) echo 'position:sticky;top:80px;'; ?>">
                    <div class="olo-svresults-map" id="<?php echo esc_attr( $results_id ); ?>-map"></div>
                </div>
                <?php endif; ?>

                <div class="olo-svresults-cards-wrap">
                    <div class="olo-svresults-grid"></div>
                    <div class="olo-svresults-empty" style="display:none;">
                        <p><?php echo esc_html( olo_t( 'Nessuna struttura trovata con i filtri selezionati.' ) ); ?></p>
                    </div>
                    <div class="olo-svresults-pagination"></div>
                </div>
            </div>

        </div>
        <?php
        return ob_get_clean();
    }

    private function query_services( $s = [] ) {
        if ( ! post_type_exists( 'olo_service' ) ) {
            return [];
        }

        $args = [
            'post_type'      => 'olo_service',
            'posts_per_page' => 200,
            'post_status'    => 'publish',
            'no_found_rows'  => true,
        ];

        $query    = new WP_Query( $args );
        $services = [];

        foreach ( $query->posts as $post ) {
            $svc = [
                'id'    => $post->ID,
                'title' => get_the_title( $post ),
                'url'   => get_permalink( $post->ID ),
            ];

            // GPS
            $lat = floatval( get_post_meta( $post->ID, '_olo_service_latitude', true ) );
            $lng = floatval( get_post_meta( $post->ID, '_olo_service_longitude', true ) );
            if ( $lat !== 0.0 || $lng !== 0.0 ) {
                $svc['lat'] = $lat;
                $svc['lng'] = $lng;
            }

            // Thumbnail
            $thumb = get_the_post_thumbnail_url( $post->ID, 'medium_large' );
            if ( $thumb ) {
                $svc['image'] = $thumb;
            }

            // Excerpt
            $excerpt = has_excerpt( $post->ID )
                ? get_the_excerpt( $post->ID )
                : wp_trim_words( $post->post_content, 20, '&hellip;' );
            if ( $excerpt ) {
                $svc['excerpt'] = $excerpt;
            }

            // Altitude
            $altitude = get_post_meta( $post->ID, '_olo_service_altitude', true );
            if ( $altitude !== '' && $altitude !== false ) {
                $svc['altitude'] = intval( $altitude );
            }

            // Meta
            $meta_map = [
                '_olo_service_price'     => 'price',
                '_olo_service_capacity'  => 'capacity',
                '_olo_service_valley'    => 'valley',
                '_olo_service_bedrooms'  => 'bedrooms',
                '_olo_service_beds'      => 'beds',
                '_olo_service_bathrooms' => 'bathrooms',
                '_olo_service_sqm'       => 'sqm',
                '_olo_service_type'      => 'service_type',
            ];

            foreach ( $meta_map as $meta_key => $json_key ) {
                $val = get_post_meta( $post->ID, $meta_key, true );
                if ( $val !== '' && $val !== false ) {
                    $svc[ $json_key ] = is_numeric( $val ) ? floatval( $val ) : $val;
                }
            }

            // Club categories
            $clubs_raw = get_post_meta( $post->ID, '_olo_service_clubs', true );
            $club_cats = [];
            if ( is_array( $clubs_raw ) && ! empty( $clubs_raw ) ) {
                foreach ( $clubs_raw as $club ) {
                    $cat = trim( $club['category'] ?? '' );
                    if ( $cat !== '' ) {
                        $club_cats[] = $cat;
                    }
                }
            } else {
                $legacy_cat = get_post_meta( $post->ID, '_olo_service_club_category', true );
                if ( $legacy_cat ) {
                    $club_cats[] = trim( $legacy_cat );
                }
            }
            if ( ! empty( $club_cats ) ) {
                $svc['club_categories'] = array_values( array_unique( $club_cats ) );
            }

            // Amenities
            $raw = get_post_meta( $post->ID, '_olo_service_amenities', true );
            if ( $raw ) {
                $am = maybe_unserialize( $raw );
                if ( is_array( $am ) && ! empty( $am ) ) {
                    $svc['amenities'] = array_values( $am );
                }
            }

            // Ribbon (from configurable meta key)
            if ( ! empty( $s['ribbon_field'] ) ) {
                $ribbon_val = get_post_meta( $post->ID, sanitize_key( $s['ribbon_field'] ), true );
                if ( ! empty( $ribbon_val ) ) {
                    $svc['ribbon'] = $ribbon_val;
                }
            }

            // Service opening
            if ( ! empty( $s['show_service_opening'] ) ) {
                $opening = get_post_meta( $post->ID, '_olo_service_opening', true );
                if ( $opening ) {
                    $svc['service_opening'] = $opening;
                }
            }

            $services[] = $svc;
        }

        wp_reset_postdata();
        return $services;
    }
}
