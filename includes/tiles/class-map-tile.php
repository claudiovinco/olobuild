<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Map_Tile extends Olo_Tile_Base {

    protected $type     = 'map';
    protected $name     = 'Mappa Pro';
    protected $icon     = 'dashicons-location';
    protected $category = 'media';
    protected $defaults = [
        'mode'                 => 'single',
        'address'              => '',
        'latitude'             => '41.9028',
        'longitude'            => '12.4964',
        'zoom'                 => '13',
        'height'               => '400',
        'tile_layer'           => 'standard',
        'marker'               => true,
        'marker_popup'         => '',
        'marker_color'         => '#e74c3c',
        'loc_post_type'        => 'location',
        'loc_osm_field'        => 'location_map',
        'loc_taxonomy'         => '',
        'loc_show_filters'     => false,
        'loc_filter_style'     => 'pills',
        'loc_filter_align'     => 'left',
        'loc_cluster'          => true,
        'loc_fit_bounds'       => true,
        'loc_default_zoom'     => '13',
        'loc_default_center'   => '41.9028, 12.4964',
        'loc_popup_show_image'   => true,
        'loc_popup_show_excerpt' => true,
        'loc_popup_show_link'    => true,
        'loc_max_locations'    => '100',
        'loc_tile_layer'       => 'osm',
        // Services mode
        'svc_booking_mode'         => 'accommodation',
        'svc_show_altitude_filter'  => true,
        'svc_altitude_ranges'       => '0-1000,1000-1500,1500-2000,2000-9999',
        'svc_show_locality_filter'  => true,
        'svc_show_guests_filter'    => true,
        'svc_guests_ranges'         => '1-2,3-5,6-8,9-99',
        'svc_show_price_filter'     => true,
        'svc_price_ranges'          => '0-100,100-150,150-200,200-9999',
        'svc_show_bedrooms_filter'  => true,
        'svc_show_amenities_filter' => true,
        'svc_amenities_list'        => 'wifi,fireplace,sauna,hottub,pets,ski,bbq,garden',
        'svc_filter_style'          => 'default',
        'svc_filter_position'       => 'top',
        'svc_tile_layer'           => 'positron',
        'svc_default_center'       => '46.07, 11.12',
        'svc_default_zoom'         => '10',
        'svc_fit_bounds'           => true,
        'svc_cluster'              => true,
        'svc_popup_show_image'     => true,
        'svc_popup_show_excerpt'   => true,
        'svc_popup_show_price'     => true,
        'svc_popup_show_altitude'  => true,
        'svc_popup_show_specs'     => true,
        'svc_popup_show_amenities' => false,
        'svc_popup_show_gallery'   => false,
        'svc_popup_show_valley'    => true,
        'svc_popup_max_width'      => '280',
        'svc_popup_img_height'     => '180',
        'svc_popup_btn_text'       => 'Scopri e Prenota',
        'svc_popup_btn_color'      => '',
        'svc_popup_bg'             => '#ffffff',
        'svc_popup_color'          => '#333333',
        'svc_popup_radius'         => '8',
        'border_radius'        => '8',
        // ── Infrastructure (all modes) ──
        'fullscreen_btn'       => true,
        'marker_shape'         => 'pin',
        // ── Multi-marker UI (locations + services) ──
        'view_mode'            => 'list',
        'grid_columns'         => 2,
        'sort_default'         => 'default',
        'results_per_page'     => 10,
        'card_max_height'      => 0,
        'card_border_radius'   => 8,
        'show_location_search' => true,
        'show_radius'          => false,
        'radius_default'       => 5,
        // ── SEO Schema ──
        'emit_schema'          => true,
        // ── Split-view layout (locations + services) ──
        'map_position'         => 'left',
        'map_width'            => '',         // legacy; if set, determines map dim. filter_width takes precedence.
        'filter_columns'       => 2,
        'filter_position'      => '',         // top | bottom | left | right (empty = fall back to legacy svc_filter_position, then 'right')
        'filter_width'         => '45',       // % of the tile taken by the filters+results panel (20-80). Map takes the rest.
        'btn_text'             => 'Ricerca',
        'btn_bg'               => '#2563EB',
        'btn_color'            => '#FFFFFF',
            'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'address', 'type' => 'text',  'label' => 'Indirizzo o coordinate' ],
            [ 'key' => 'zoom',    'type' => 'range', 'label' => 'Livello zoom', 'min' => 1, 'max' => 19, 'step' => 1 ],
            [ 'key' => 'height',  'type' => 'range', 'label' => 'Altezza (px)', 'min' => 150, 'max' => 800, 'step' => 10 ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $mode = $s['mode'] ?? 'single';

        if ( $mode === 'dynamic_service' ) {
            return $this->render_dynamic_service( $s );
        }

        if ( $mode === 'locations' ) {
            return $this->render_locations( $s );
        }

        if ( $mode === 'services' ) {
            return $this->render_services( $s );
        }

        return $this->render_single( $s );
    }

    /**
     * Dynamic service mode: reads GPS from current olo_service post meta.
     */
    private function render_dynamic_service( $s ) {
        global $post;

        $lat = 0;
        $lng = 0;
        $title = '';

        if ( $post && $post->post_type === 'olo_service' ) {
            $lat = floatval( get_post_meta( $post->ID, '_olo_service_latitude', true ) );
            $lng = floatval( get_post_meta( $post->ID, '_olo_service_longitude', true ) );
            $title = get_the_title( $post->ID );
        }

        if ( ! $lat && ! $lng ) {
            return '<div style="padding:24px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);background:var(--olo-color-muted, #F3F4F6);border-radius:8px">'
                 . '<p style="margin:0">Nessuna coordinata GPS impostata per questo servizio.</p>'
                 . '</div>';
        }

        $zoom   = absint( $s['zoom'] ) ?: 13;
        $height = absint( $s['height'] ) ?: 400;
        $radius = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 8 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );

        $src = "https://www.openstreetmap.org/export/embed.html?bbox="
             . ( $lng - 0.02 ) . ',' . ( $lat - 0.01 ) . ','
             . ( $lng + 0.02 ) . ',' . ( $lat + 0.01 )
             . "&layer=mapnik&marker=" . $lat . ',' . $lng;

        $fs_enabled = ! empty( $s['fullscreen_btn'] );
        $uid = 'olo-map-dyn-' . wp_unique_id();

        ob_start();
        ?>
        <?php if ( $radius_hover_css !== '' ) : ?>
        <style>.<?php echo esc_attr( $uid ); ?>{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo esc_attr( $uid ); ?>:hover{border-radius:<?php echo $radius_hover_css; ?> !important}</style>
        <?php endif; ?>
        <div class="olo-map olo-map-dynamic <?php echo esc_attr( $uid ); ?>" style="position:relative; border-radius: <?php echo $radius; ?>; overflow: hidden;">
            <iframe
                src="<?php echo esc_url( $src ); ?>"
                style="width: 100%; height: <?php echo $height; ?>px; border: 0;"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                title="<?php echo esc_attr( $title ); ?>"
            ></iframe>
            <?php if ( $fs_enabled ) : ?>
                <?php echo $this->build_fullscreen_btn( $uid ); ?>
                <style>
                .<?php echo esc_attr( $uid ); ?> .olo-map-fs-btn {
                    position: absolute; top: 10px; right: 10px; z-index: 1000;
                    width: 34px; height: 34px;
                    background: rgba(255,255,255,0.95);
                    border: 1px solid rgba(0,0,0,0.15);
                    border-radius: 6px; cursor: pointer; padding: 6px;
                    box-shadow: 0 1px 4px rgba(0,0,0,0.15);
                    display: flex; align-items: center; justify-content: center;
                    color: #374151;
                }
                .<?php echo esc_attr( $uid ); ?> .olo-map-fs-btn:hover { background: #fff; }
                </style>
                <script>
                (function(){
                    var root = document.querySelector('.<?php echo esc_js( $uid ); ?>');
                    if (!root) return;
                    var btn = root.querySelector('.olo-map-fs-btn');
                    if (!btn) return;
                    btn.addEventListener('click', function() {
                        if (!document.fullscreenElement) {
                            if (root.requestFullscreen) root.requestFullscreen();
                            else if (root.webkitRequestFullscreen) root.webkitRequestFullscreen();
                        } else {
                            if (document.exitFullscreen) document.exitFullscreen();
                            else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
                        }
                    });
                })();
                </script>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Get tile layer URL by key.
     */
    private function get_tile_layer_url( $key ) {
        $urls = [
            'standard'    => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            'osm'         => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            'hot'         => 'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png',
            'positron'    => 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
            'voyager'     => 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',
            'dark'        => 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',
            'satellite'   => 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
            'topo'        => 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}',
            'esri_street' => 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}',
            'gray'        => 'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}',
            'opentopomap' => 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
        ];
        return $urls[ $key ] ?? $urls['standard'];
    }

    /**
     * Return the correct attribution string for a given tile layer key.
     */
    private function get_tile_layer_attr( $key ) {
        $osm   = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>';
        $carto = $osm . ' &copy; <a href="https://carto.com/">CARTO</a>';
        $esri  = 'Tiles &copy; Esri';
        $topo  = $osm . ', SRTM | &copy; <a href="https://opentopomap.org">OpenTopoMap</a>';
        $attrs = [
            'standard'    => $osm,
            'osm'         => $osm,
            'hot'         => $osm . ' | HOT',
            'positron'    => $carto,
            'voyager'     => $carto,
            'dark'        => $carto,
            'satellite'   => $esri,
            'topo'        => $esri,
            'esri_street' => $esri,
            'gray'        => $esri,
            'opentopomap' => $topo,
        ];
        return $attrs[ $key ] ?? $osm;
    }

    /**
     * Single mode: Leaflet map with marker.
     */
    private function render_single( $s ) {
        $lat          = floatval( $s['latitude'] ) ?: 41.9028;
        $lng          = floatval( $s['longitude'] ) ?: 12.4964;
        $zoom         = absint( $s['zoom'] ) ?: 13;
        $height       = absint( $s['height'] ) ?: 400;
        $radius       = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
        $show_marker  = filter_var( $s['marker'], FILTER_VALIDATE_BOOLEAN );
        $scroll_zoom  = filter_var( $s['scroll_wheel_zoom'] ?? false, FILTER_VALIDATE_BOOLEAN );
        $dragging     = filter_var( $s['dragging'] ?? true, FILTER_VALIDATE_BOOLEAN );
        $popup_text   = esc_js( wp_strip_all_tags( $s['marker_popup'] ?? '' ) );
        $marker_color = $this->safe_color_css( $s['marker_color'] ?? '' ) ?: '#e74c3c';
        $marker_type  = sanitize_key( $s['marker_type'] ?? 'pin' );
        $marker_image = esc_url( $s['marker_image'] ?? '' );
        $marker_size  = absint( $s['marker_size'] ?? 36 ) ?: 36;
        $tile_url     = esc_js( $this->get_tile_layer_url( $s['tile_layer'] ?? 'standard' ) );

        // If user didn't override marker_type but set marker_shape, adopt it
        $shape_override = sanitize_key( $s['marker_shape'] ?? 'pin' );
        if ( empty( $s['marker_type'] ) && $shape_override && $shape_override !== 'pin' ) {
            $marker_type = $shape_override;
        }

        $scroll_zoom_js = $scroll_zoom ? 'true' : 'false';
        $dragging_js    = $dragging ? 'true' : 'false';
        $map_id         = 'olo-map-' . wp_rand( 10000, 99999 );
        $uid            = 'olo-map-single-' . wp_unique_id();
        $fs_enabled     = ! empty( $s['fullscreen_btn'] );

        wp_enqueue_style( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', [], '1.9.4' );
        wp_enqueue_script( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], '1.9.4', true );

        ob_start();
        ?>

        <?php if ( $radius_hover_css !== '' ) : ?>
        <style>#<?php echo esc_attr( $map_id ); ?>{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}#<?php echo esc_attr( $map_id ); ?>:hover{border-radius:<?php echo $radius_hover_css; ?> !important}</style>
        <?php endif; ?>
        <div class="olo-map-wrap <?php echo esc_attr( $uid ); ?>" style="position:relative;">
        <div id="<?php echo esc_attr( $map_id ); ?>" class="olo-map" style="height:<?php echo $height; ?>px; border-radius:<?php echo $radius; ?>; overflow:hidden;"></div>
        <?php if ( $fs_enabled ) : ?>
            <?php echo $this->build_fullscreen_btn( $map_id ); ?>
            <style>
            .<?php echo esc_attr( $uid ); ?> .olo-map-fs-btn {
                position: absolute; top: 10px; right: 10px; z-index: 1000;
                width: 34px; height: 34px;
                background: rgba(255,255,255,0.95);
                border: 1px solid rgba(0,0,0,0.15);
                border-radius: 6px; cursor: pointer; padding: 6px;
                box-shadow: 0 1px 4px rgba(0,0,0,0.15);
                display: flex; align-items: center; justify-content: center;
                color: #374151;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-map-fs-btn:hover { background: #fff; }
            .<?php echo esc_attr( $uid ); ?>:fullscreen .olo-map,
            .<?php echo esc_attr( $uid ); ?>:-webkit-full-screen .olo-map { height: 100vh !important; border-radius: 0 !important; }
            </style>
            <script>
            (function(){
                var root = document.querySelector('.<?php echo esc_js( $uid ); ?>');
                if (!root) return;
                var btn = root.querySelector('.olo-map-fs-btn');
                if (!btn) return;
                btn.addEventListener('click', function() {
                    if (!document.fullscreenElement) {
                        if (root.requestFullscreen) root.requestFullscreen();
                        else if (root.webkitRequestFullscreen) root.webkitRequestFullscreen();
                    } else {
                        if (document.exitFullscreen) document.exitFullscreen();
                        else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
                    }
                });
                document.addEventListener('fullscreenchange', function() {
                    var el = document.getElementById('<?php echo esc_js( $map_id ); ?>');
                    setTimeout(function() {
                        if (el && el._oloMap && el._oloMap.invalidateSize) el._oloMap.invalidateSize();
                        else if (window.L && el && el.classList.contains('leaflet-container')) {
                            // noop, handled elsewhere
                        }
                    }, 200);
                });
            })();
            </script>
        <?php endif; ?>
        </div>

        <script>
        (function(){
            var mapEl = document.getElementById('<?php echo esc_js( $map_id ); ?>');
            if (!mapEl) return;

            function initOloMap() {
                if (typeof L === 'undefined') {
                    setTimeout(initOloMap, 100);
                    return;
                }

                var map = L.map('<?php echo esc_js( $map_id ); ?>', {
                    scrollWheelZoom: <?php echo $scroll_zoom_js; ?>,
                    dragging: <?php echo $dragging_js; ?>
                }).setView([<?php echo $lat; ?>, <?php echo $lng; ?>], <?php echo $zoom; ?>);

                L.tileLayer('<?php echo $tile_url; ?>', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    maxZoom: 19
                }).addTo(map);

                <?php if ( $show_marker ) : ?>
                var markerIcon;
                <?php if ( $marker_type === 'image' && $marker_image ) : ?>
                markerIcon = L.icon({
                    iconUrl: '<?php echo $marker_image; ?>',
                    iconSize: [<?php echo $marker_size; ?>, <?php echo $marker_size; ?>],
                    iconAnchor: [<?php echo $marker_size / 2; ?>, <?php echo $marker_size; ?>],
                    popupAnchor: [0, -<?php echo $marker_size; ?>],
                    className: 'olo-osm-marker-img'
                });
                <?php else :
                    $ms = $marker_size;
                    $c  = esc_attr( $marker_color );
                    $svgs = [
                        'pin'     => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $ms . '" height="' . round( $ms * 1.43 ) . '" viewBox="0 0 24 36"><path d="M12 0C5.4 0 0 5.4 0 12c0 9 12 24 12 24s12-15 12-24C24 5.4 18.6 0 12 0zm0 16c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4z" fill="' . $c . '" stroke="#fff" stroke-width="0.8"/></svg>',
                        'drop'    => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $ms . '" height="' . round( $ms * 1.43 ) . '" viewBox="0 0 24 34"><path d="M12 0C5.4 0 0 5.4 0 12c0 8 12 22 12 22s12-14 12-22C24 5.4 18.6 0 12 0z" fill="' . $c . '" stroke="#fff" stroke-width="1"/></svg>',
                        'circle'  => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $ms . '" height="' . $ms . '" viewBox="0 0 24 24"><circle cx="12" cy="12" r="11" fill="' . $c . '" stroke="#fff" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="#fff"/></svg>',
                        'square'  => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $ms . '" height="' . $ms . '" viewBox="0 0 24 24"><rect x="1" y="1" width="22" height="22" rx="4" fill="' . $c . '" stroke="#fff" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="#fff"/></svg>',
                        'diamond' => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $ms . '" height="' . $ms . '" viewBox="0 0 24 24"><path d="M12 1L23 12 12 23 1 12z" fill="' . $c . '" stroke="#fff" stroke-width="1.5"/><circle cx="12" cy="12" r="3.5" fill="#fff"/></svg>',
                        'star'    => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $ms . '" height="' . $ms . '" viewBox="0 0 24 24"><path d="M12 1l3.09 6.26L22 8.27l-5 4.87 1.18 6.88L12 16.77 5.82 20.02 7 13.14 2 8.27l6.91-1.01z" fill="' . $c . '" stroke="#fff" stroke-width="0.8"/></svg>',
                        'flag'    => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $ms . '" height="' . round( $ms * 1.2 ) . '" viewBox="0 0 24 30"><line x1="4" y1="2" x2="4" y2="28" stroke="' . $c . '" stroke-width="2.5" stroke-linecap="round"/><path d="M4 3h16l-4 5.5 4 5.5H4z" fill="' . $c . '" opacity="0.9"/></svg>',
                        'flag-wave' => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $ms . '" height="' . round( $ms * 1.3 ) . '" viewBox="0 0 30 38"><line x1="4" y1="2" x2="4" y2="36" stroke="#555" stroke-width="2" stroke-linecap="round"/><circle cx="4" cy="2" r="1.8" fill="#888"/><path fill="' . $c . '" opacity="0.92" d="M4 4 Q14 2 20 6 Q26 10 24 14 L4 14Z"><animate attributeName="d" dur="1.2s" repeatCount="indefinite" values="M4 4 Q14 2 20 6 Q26 10 24 14 L4 14Z;M4 4 Q10 7 18 5 Q24 3 26 8 Q28 13 24 14 L4 14Z;M4 4 Q14 2 20 6 Q26 10 24 14 L4 14Z"/></path><path fill="' . $c . '" opacity="0.65" d="M4 4 Q14 1 22 5 Q28 9 26 14 L4 14Z"><animate attributeName="d" dur="1.2s" begin="0.15s" repeatCount="indefinite" values="M4 4 Q14 1 22 5 Q28 9 26 14 L4 14Z;M4 4 Q9 6 16 4 Q22 2 26 7 Q29 12 26 14 L4 14Z;M4 4 Q14 1 22 5 Q28 9 26 14 L4 14Z"/></path></svg>',
                        'heart'   => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $ms . '" height="' . round( $ms * 0.9 ) . '" viewBox="0 0 24 22"><path d="M12 21C12 21 2 13.5 2 7.5 2 4.46 4.46 2 7.5 2c1.74 0 3.41.81 4.5 2.09A5.99 5.99 0 0116.5 2C19.54 2 22 4.46 22 7.5 22 13.5 12 21 12 21z" fill="' . $c . '" stroke="#fff" stroke-width="1"/></svg>',
                    ];
                    $svg = $svgs[ $marker_type ] ?? $svgs['pin'];
                    $is_tall = in_array( $marker_type, [ 'pin', 'drop', 'flag', 'flag-wave' ], true );
                    $icon_h  = $is_tall ? round( $ms * 1.43 ) : ( $marker_type === 'heart' ? round( $ms * 0.9 ) : $ms );
                    $anchor_y = $is_tall ? $icon_h : round( $icon_h / 2 );
                ?>
                markerIcon = L.divIcon({
                    html: '<?php echo $svg; ?>',
                    iconSize: [<?php echo $ms; ?>, <?php echo $icon_h; ?>],
                    iconAnchor: [<?php echo round( $ms / 2 ); ?>, <?php echo $anchor_y; ?>],
                    popupAnchor: [0, -<?php echo $anchor_y; ?>],
                    className: 'olo-osm-marker'
                });
                <?php endif; ?>

                var marker = L.marker([<?php echo $lat; ?>, <?php echo $lng; ?>], { icon: markerIcon }).addTo(map);
                    <?php if ( ! empty( $popup_text ) ) : ?>
                marker.bindPopup('<?php echo $popup_text; ?>');
                    <?php endif; ?>
                <?php endif; ?>
            }

            initOloMap();
        })();
        </script>

        <style>
        .olo-osm-marker { background: none !important; border: none !important; }
        </style>

        <?php
        return ob_get_clean();
    }

    /**
     * Locations mode: split-view Leaflet map with filters + results panel.
     */
    private function render_locations( $s ) {
        $locations = $this->query_locations( $s );
        $terms     = $this->get_taxonomy_terms( $s, $locations );

        $center = [ 41.9028, 12.4964 ];
        $center_match = $this->parse_coords( $s['loc_default_center'] );
        if ( $center_match ) {
            $center = [ $center_match['lat'], $center_match['lng'] ];
        }

        $height        = absint( $s['height'] ) ?: 700;
        $map_id        = 'olo-plm-map-' . wp_rand( 10000, 99999 );
        $uid           = 'olo-plm-loc-' . wp_rand( 10000, 99999 );
        $map_pos       = ( ($s['map_position'] ?? 'left') === 'right' ) ? 'right' : 'left';
        // filter_position: new setting takes precedence (when non-empty).
        // Fall back to legacy svc_filter_position for BC, then default 'right'.
        $filter_pos_raw = ! empty( $s['filter_position'] )
            ? $s['filter_position']
            : ( ! empty( $s['svc_filter_position'] ) ? $s['svc_filter_position'] : 'right' );
        $filter_pos = in_array( $filter_pos_raw, [ 'top', 'bottom', 'left', 'right' ], true ) ? $filter_pos_raw : 'right';
        // filter_width is the primary control (user picks panel size, map fills the rest).
        // map_width stays as legacy fallback. Both accepted as plain number ("45"), percent ("45%") or px.
        list( $map_w, $filter_w ) = $this->resolve_dims( $s );
        $f_cols        = max( 1, min( 4, absint( $s['filter_columns'] ?? 2 ) ) );
        $grid_cols     = max( 1, min( 4, absint( $s['grid_columns'] ?? 2 ) ) );
        $view_mode     = in_array( $s['view_mode'] ?? 'list', [ 'list', 'grid' ], true ) ? $s['view_mode'] : 'list';
        $sort_default  = in_array( $s['sort_default'] ?? 'default', [ 'default', 'title_asc', 'title_desc', 'newest', 'distance' ], true ) ? $s['sort_default'] : 'default';
        $per_page      = max( 1, absint( $s['results_per_page'] ?? 10 ) );
        $card_mh       = max( 0, absint( $s['card_max_height'] ?? 0 ) );
        $card_r        = max( 0, Olo_Tile_Utils::radius_int( $s['card_border_radius'] ?? 8 ) );
        $show_search   = ! empty( $s['show_location_search'] );
        $show_radius   = ! empty( $s['show_radius'] );
        $radius_d      = max( 1, min( 50, absint( $s['radius_default'] ?? 5 ) ) );
        $show_filters  = ! empty( $s['loc_show_filters'] ) && ! empty( $terms );
        $color         = $this->safe_hex( $s['loc_marker_color'] ?? $s['marker_color'] ?? '', '#2563EB' );
        $btn_bg        = $this->safe_hex( $s['btn_bg'] ?? '', '#2563EB' );
        $btn_color     = $this->safe_hex( $s['btn_color'] ?? '', '#FFFFFF' );
        $btn_text      = $s['btn_text'] ?: 'Ricerca';
        $use_cluster   = ! empty( $s['loc_cluster'] );
        $fit_bounds    = ! empty( $s['loc_fit_bounds'] );
        $fs_enabled    = ! empty( $s['fullscreen_btn'] );
        $emit_schema   = ! empty( $s['emit_schema'] );
        $marker_shape  = sanitize_key( $s['marker_shape'] ?? 'pin' );
        if ( $marker_shape === 'image' ) $marker_shape = 'pin';
        $tile_key      = ! empty( $s['loc_tile_layer'] ) ? $s['loc_tile_layer'] : 'osm';
        $tile_url      = $this->get_tile_layer_url( $tile_key );
        $tile_attr     = $this->get_tile_layer_attr( $tile_key );
        $zoom          = absint( $s['loc_default_zoom'] ) ?: 13;

        $this->enqueue_leaflet( $use_cluster );

        $marker_info = $this->build_marker_shape_svg( $marker_shape, $color, 32 );

        $js_data = [
            'uid'           => $uid,
            'mapId'         => $map_id,
            'items'         => array_values( $locations ),
            'center'        => $center,
            'zoom'          => $zoom,
            'color'         => $color,
            'perPage'       => $per_page,
            'useCluster'    => $use_cluster,
            'fitBounds'     => $fit_bounds,
            'popupImage'    => (bool) $s['loc_popup_show_image'],
            'popupExcerpt'  => (bool) $s['loc_popup_show_excerpt'],
            'popupLink'     => (bool) $s['loc_popup_show_link'],
            'showRadius'    => $show_radius,
            'radiusDefault' => $radius_d,
            'sortDefault'   => $sort_default,
            'viewMode'      => $view_mode,
            'showFilters'   => $show_filters,
            'tileUrl'       => $tile_url,
            'tileAttr'      => $tile_attr,
            'markerSvg'     => $marker_info['svg'],
            'markerW'       => $marker_info['w'],
            'markerH'       => $marker_info['h'],
            'markerAnchorY' => $marker_info['anchor_y'],
            'mode'          => 'locations',
            'i18n'          => [
                'noResults'  => olo_t( 'Nessun risultato trovato' ),
                'tryFilters' => olo_t( 'Prova a modificare i filtri' ),
                'cam'        => olo_t( 'cam.' ),
                'bagni'      => olo_t( 'bagni' ),
                'notte'      => olo_t( 'notte' ),
            ],
        ];

        ob_start();
        ?>
        <style>
        <?php echo $this->build_plm_css( $uid, $map_pos, $map_w, $height, $f_cols, $grid_cols, $card_r, $card_mh, $color, $btn_bg, $btn_color, $filter_pos, $filter_w ); ?>
        </style>

        <div class="olo-tile olo-tile--plm <?php echo esc_attr( $uid ); ?>" role="region" aria-label="<?php echo esc_attr( olo_t( 'Mappa luoghi' ) ); ?>">
            <div class="plm-map-panel">
                <div class="plm-map" id="<?php echo esc_attr( $map_id ); ?>"></div>
                <?php if ( $fs_enabled ) : ?>
                    <button type="button" class="plm-fullscreen-btn" id="<?php echo esc_attr( $uid ); ?>-fs" title="<?php echo esc_attr( olo_t( 'Schermo intero' ) ); ?>" aria-label="<?php echo esc_attr( olo_t( 'Attiva o disattiva schermo intero' ) ); ?>">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3H5a2 2 0 00-2 2v3m18 0V5a2 2 0 00-2-2h-3m0 18h3a2 2 0 002-2v-3M3 16v3a2 2 0 002 2h3"/></svg>
                    </button>
                <?php endif; ?>
            </div>

            <div class="plm-results-panel">
                <div class="plm-filters" id="<?php echo esc_attr( $uid ); ?>-filters">
                    <div class="plm-filters-grid">
                        <?php if ( $show_search ) : ?>
                            <div class="plm-filter-group plm-filter-group--full">
                                <span class="plm-filter-label"><?php echo esc_html( olo_t( 'Localit&agrave;' ) ); ?></span>
                                <div class="plm-autocomplete-wrap">
                                    <input type="text" class="plm-filter-input" data-filter="location" placeholder="<?php echo esc_attr( olo_t( 'Cerca citt&agrave;, zona, indirizzo...' ) ); ?>" autocomplete="off" />
                                    <ul class="plm-autocomplete-list" id="<?php echo esc_attr( $uid ); ?>-ac"></ul>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ( $show_radius ) : ?>
                            <div class="plm-filter-group plm-filter-group--full">
                                <span class="plm-filter-label"><?php echo esc_html( olo_t( 'Raggio di ricerca' ) ); ?></span>
                                <div class="plm-radius-wrap">
                                    <input type="range" min="1" max="50" value="<?php echo esc_attr( $radius_d ); ?>" data-filter="radius" />
                                    <span class="plm-radius-val"><?php echo esc_html( $radius_d ); ?> km</span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ( $show_filters ) : ?>
                            <div class="plm-filter-group plm-filter-group--full">
                                <span class="plm-filter-label"><?php echo esc_html( olo_t( 'Categoria' ) ); ?></span>
                                <select class="plm-filter-select" data-filter="taxonomy">
                                    <option value=""><?php echo esc_html( olo_t( 'Tutte' ) ); ?></option>
                                    <?php foreach ( $terms as $term ) : ?>
                                        <option value="<?php echo esc_attr( $term['slug'] ); ?>"><?php echo esc_html( $term['name'] ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="plm-actions">
                        <button type="button" class="plm-btn-search" id="<?php echo esc_attr( $uid ); ?>-search"><?php echo esc_html( $btn_text ); ?></button>
                        <button type="button" class="plm-btn-reset" id="<?php echo esc_attr( $uid ); ?>-reset" title="<?php echo esc_attr( olo_t( 'Azzera filtri' ) ); ?>" aria-label="<?php echo esc_attr( olo_t( 'Azzera filtri' ) ); ?>">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 019-9 9.75 9.75 0 016.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 01-9 9 9.75 9.75 0 01-6.74-2.74L3 16"/><path d="M3 21v-5h5"/></svg>
                        </button>
                    </div>
                </div>

                <div class="plm-results-header">
                    <span class="plm-results-count" id="<?php echo esc_attr( $uid ); ?>-count"><strong>0</strong> <?php echo esc_html( olo_t( 'Risultati' ) ); ?></span>
                    <div class="plm-sort-wrap">
                        <select class="plm-sort-select" id="<?php echo esc_attr( $uid ); ?>-sort">
                            <option value="default" <?php selected( $sort_default, 'default' ); ?>><?php echo esc_html( olo_t( 'Ordine predefinito' ) ); ?></option>
                            <option value="title_asc" <?php selected( $sort_default, 'title_asc' ); ?>><?php echo esc_html( olo_t( 'Titolo A-Z' ) ); ?></option>
                            <option value="title_desc" <?php selected( $sort_default, 'title_desc' ); ?>><?php echo esc_html( olo_t( 'Titolo Z-A' ) ); ?></option>
                            <option value="newest" <?php selected( $sort_default, 'newest' ); ?>><?php echo esc_html( olo_t( 'Pi&ugrave; recenti' ) ); ?></option>
                            <option value="distance" <?php selected( $sort_default, 'distance' ); ?>><?php echo esc_html( olo_t( 'Distanza' ) ); ?></option>
                        </select>
                        <div class="plm-view-toggles" role="group" aria-label="<?php echo esc_attr( olo_t( 'Tipo di vista' ) ); ?>">
                            <button type="button" class="plm-view-btn<?php echo $view_mode === 'list' ? ' is-active' : ''; ?>" data-view="list" aria-label="<?php echo esc_attr( olo_t( 'Vista lista' ) ); ?>" title="<?php echo esc_attr( olo_t( 'Vista lista' ) ); ?>">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><circle cx="4" cy="6" r="1" fill="currentColor"/><circle cx="4" cy="12" r="1" fill="currentColor"/><circle cx="4" cy="18" r="1" fill="currentColor"/></svg>
                            </button>
                            <button type="button" class="plm-view-btn<?php echo $view_mode === 'grid' ? ' is-active' : ''; ?>" data-view="grid" aria-label="<?php echo esc_attr( olo_t( 'Vista griglia' ) ); ?>" title="<?php echo esc_attr( olo_t( 'Vista griglia' ) ); ?>">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="plm-results-list<?php echo $view_mode === 'grid' ? ' plm-grid-view' : ''; ?>" id="<?php echo esc_attr( $uid ); ?>-list"></div>

                <div class="plm-pagination" id="<?php echo esc_attr( $uid ); ?>-pagination">
                    <button type="button" class="plm-page-btn" data-page="prev"><?php echo esc_html( olo_t( '&larr; Precedente' ) ); ?></button>
                    <span class="plm-page-info" id="<?php echo esc_attr( $uid ); ?>-pageinfo">1 / 1</span>
                    <button type="button" class="plm-page-btn" data-page="next"><?php echo esc_html( olo_t( 'Successivo &rarr;' ) ); ?></button>
                </div>
            </div>
        </div>

        <?php if ( $emit_schema && ! empty( $locations ) ) : ?>
            <script type="application/ld+json"><?php echo wp_json_encode( $this->build_multi_schema( $locations, 'Place' ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
        <?php endif; ?>

        <?php echo $this->build_plm_js( $js_data ); ?>
        <?php
        return ob_get_clean();
    }

    /**
     * Query locations from CPT using ACF OSM field.
     */
    private function query_locations( $s ) {
        $post_type = sanitize_key( $s['loc_post_type'] );
        $osm_field = sanitize_key( $s['loc_osm_field'] );
        $max       = min( 500, absint( $s['loc_max_locations'] ) );

        if ( ! post_type_exists( $post_type ) ) {
            return [];
        }

        $query = new WP_Query( [
            'post_type'      => $post_type,
            'posts_per_page' => $max,
            'post_status'    => 'publish',
            'no_found_rows'  => true,
        ] );

        $locations = [];
        $taxonomy  = sanitize_key( $s['loc_taxonomy'] );

        foreach ( $query->posts as $post ) {
            // Read raw meta to get the array (get_field may return rendered HTML)
            $field_value = get_post_meta( $post->ID, $osm_field, true );

            if ( empty( $field_value ) || ! is_array( $field_value ) || ! isset( $field_value['lat'], $field_value['lng'] ) ) {
                continue;
            }

            $lat = floatval( $field_value['lat'] );
            $lng = floatval( $field_value['lng'] );

            if ( $lat === 0.0 && $lng === 0.0 ) {
                continue;
            }

            $location = [
                'id'      => $post->ID,
                'title'   => get_the_title( $post ),
                'lat'     => $lat,
                'lng'     => $lng,
                'address' => $field_value['address'] ?? '',
            ];

            // Thumbnail
            $thumb = get_the_post_thumbnail_url( $post->ID, 'medium' );
            if ( $thumb ) {
                $location['image'] = $thumb;
            }

            // Excerpt
            $excerpt = has_excerpt( $post->ID )
                ? get_the_excerpt( $post->ID )
                : wp_trim_words( $post->post_content, 20, '&hellip;' );
            if ( $excerpt ) {
                $location['excerpt'] = $excerpt;
            }

            // Permalink
            $location['url'] = get_permalink( $post->ID );

            // Extra meta fields
            $meta_fields = [
                'location_phone'       => 'phone',
                'location_email'       => 'email',
                'location_website'     => 'website',
                'location_hours'       => 'hours',
                'location_price_range' => 'price',
                'location_ticket_url'  => 'ticket_url',
            ];
            foreach ( $meta_fields as $meta_key => $json_key ) {
                $val = get_post_meta( $post->ID, $meta_key, true );
                if ( $val ) {
                    $location[ $json_key ] = $val;
                }
            }
            $rating = get_post_meta( $post->ID, 'location_rating', true );
            if ( $rating ) {
                $location['rating'] = floatval( $rating );
            }
            // Accessibility as array
            $access = get_post_meta( $post->ID, 'location_accessibility', false );
            if ( ! empty( $access ) ) {
                $location['accessibility'] = $access;
            }

            // Gallery (first 4 for popup strip — thumb + full URL)
            $gallery_ids = get_post_meta( $post->ID, 'location_gallery', true );
            if ( ! empty( $gallery_ids ) && is_array( $gallery_ids ) ) {
                $gallery_items = [];
                foreach ( array_slice( $gallery_ids, 0, 4 ) as $att_id ) {
                    $thumb = wp_get_attachment_image_url( $att_id, 'thumbnail' );
                    $full  = wp_get_attachment_image_url( $att_id, 'large' );
                    if ( $thumb && $full ) {
                        $gallery_items[] = [ 'thumb' => $thumb, 'full' => $full ];
                    }
                }
                if ( $gallery_items ) {
                    $location['gallery']       = $gallery_items;
                    $location['gallery_count'] = count( $gallery_ids );
                }
            }

            // Rental fields (only if present)
            $rental_fields = [
                'rental_bedrooms'    => 'bedrooms',
                'rental_bathrooms'   => 'bathrooms',
                'rental_guests'      => 'guests',
                'rental_sqm'         => 'sqm',
                'rental_price_night' => 'price_night',
                'rental_price_week'  => 'price_week',
                'rental_booking_url' => 'booking_url',
                'rental_availability'=> 'availability',
            ];
            foreach ( $rental_fields as $meta_key => $json_key ) {
                $val = get_post_meta( $post->ID, $meta_key, true );
                if ( $val !== '' && $val !== false ) {
                    $location[ $json_key ] = is_numeric( $val ) ? floatval( $val ) : $val;
                }
            }

            // Taxonomy terms + marker color
            if ( $taxonomy && taxonomy_exists( $taxonomy ) ) {
                $post_terms = get_the_terms( $post->ID, $taxonomy );
                if ( $post_terms && ! is_wp_error( $post_terms ) ) {
                    $location['terms'] = wp_list_pluck( $post_terms, 'slug' );
                    // Use marker_color from first term
                    $color = get_term_meta( $post_terms[0]->term_id, 'marker_color', true );
                    if ( $color ) {
                        $location['color'] = $color;
                    }
                }
            }

            $locations[] = $location;
        }

        wp_reset_postdata();

        return $locations;
    }

    /**
     * Collect unique taxonomy terms from queried locations.
     */
    private function get_taxonomy_terms( $s, $locations ) {
        $taxonomy = sanitize_key( $s['loc_taxonomy'] );
        if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
            return [];
        }

        $terms = get_terms( [
            'taxonomy'   => $taxonomy,
            'hide_empty' => true,
        ] );

        if ( is_wp_error( $terms ) || empty( $terms ) ) {
            return [];
        }

        $result = [];
        foreach ( $terms as $term ) {
            $entry = [
                'slug' => $term->slug,
                'name' => $term->name,
            ];
            $color = get_term_meta( $term->term_id, 'marker_color', true );
            if ( $color ) {
                $entry['color'] = $color;
            }
            $result[] = $entry;
        }

        return $result;
    }

    /**
     * Render filter UI (pills or dropdown).
     */
    private function render_filters( $terms, $style, $map_id, $align = 'left' ) {
        $align_cls = $align === 'center' ? ' olo-filter-center' : ( $align === 'right' ? ' olo-filter-right' : '' );
        ?>
        <div class="olo-map-filters<?php echo $align_cls; ?>" data-map-target="<?php echo esc_attr( $map_id ); ?>">
            <?php if ( $style === 'dropdown' ) : ?>
                <select class="olo-map-filter-select uk-select" data-filter-select>
                    <option value=""><?php esc_html_e( 'Tutti', 'olobuild' ); ?></option>
                    <?php foreach ( $terms as $term ) : ?>
                        <option value="<?php echo esc_attr( $term['slug'] ); ?>">
                            <?php echo esc_html( $term['name'] ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php elseif ( $style === 'minimal' ) : ?>
                <button class="olo-map-filter-pill olo-map-filter-pill--minimal olo-map-filter-active" data-filter="">
                    <?php esc_html_e( 'Tutti', 'olobuild' ); ?>
                </button>
                <?php foreach ( $terms as $term ) : ?>
                    <button class="olo-map-filter-pill olo-map-filter-pill--minimal" data-filter="<?php echo esc_attr( $term['slug'] ); ?>">
                        <?php echo esc_html( $term['name'] ); ?>
                    </button>
                <?php endforeach; ?>
            <?php else : ?>
                <button class="olo-map-filter-pill olo-map-filter-active" data-filter="">
                    <?php esc_html_e( 'Tutti', 'olobuild' ); ?>
                </button>
                <?php foreach ( $terms as $term ) : ?>
                    <button class="olo-map-filter-pill" data-filter="<?php echo esc_attr( $term['slug'] ); ?>">
                        <?php if ( ! empty( $term['color'] ) ) : ?>
                            <span class="olo-map-filter-dot" style="background:<?php echo $this->safe_color_css( $term['color'] ); ?>"></span>
                        <?php endif; ?>
                        <?php echo esc_html( $term['name'] ); ?>
                    </button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Services mode: Leaflet map with markers from olo_service CPT.
     */
    private function render_services( $s ) {
        $locations = $this->query_services( $s );

        $center = [ 46.07, 11.12 ];
        $center_match = $this->parse_coords( $s['svc_default_center'] );
        if ( $center_match ) {
            $center = [ $center_match['lat'], $center_match['lng'] ];
        }

        $height        = absint( $s['height'] ) ?: 700;
        $map_id        = 'olo-plm-map-' . wp_rand( 10000, 99999 );
        $uid           = 'olo-plm-svc-' . wp_rand( 10000, 99999 );
        $map_pos       = ( ($s['map_position'] ?? 'left') === 'right' ) ? 'right' : 'left';
        // filter_position: new setting takes precedence (when non-empty).
        // Fall back to legacy svc_filter_position for BC, then default 'right'.
        $filter_pos_raw = ! empty( $s['filter_position'] )
            ? $s['filter_position']
            : ( ! empty( $s['svc_filter_position'] ) ? $s['svc_filter_position'] : 'right' );
        $filter_pos = in_array( $filter_pos_raw, [ 'top', 'bottom', 'left', 'right' ], true ) ? $filter_pos_raw : 'right';
        // filter_width is the primary control (user picks panel size, map fills the rest).
        // map_width stays as legacy fallback. Both accepted as plain number ("45"), percent ("45%") or px.
        list( $map_w, $filter_w ) = $this->resolve_dims( $s );
        $f_cols        = max( 1, min( 4, absint( $s['filter_columns'] ?? 2 ) ) );
        $grid_cols     = max( 1, min( 4, absint( $s['grid_columns'] ?? 2 ) ) );
        $view_mode     = in_array( $s['view_mode'] ?? 'list', [ 'list', 'grid' ], true ) ? $s['view_mode'] : 'list';
        $sort_default  = in_array( $s['sort_default'] ?? 'default', [ 'default', 'title_asc', 'title_desc', 'newest', 'distance' ], true ) ? $s['sort_default'] : 'default';
        $per_page      = max( 1, absint( $s['results_per_page'] ?? 10 ) );
        $card_mh       = max( 0, absint( $s['card_max_height'] ?? 0 ) );
        $card_r        = max( 0, Olo_Tile_Utils::radius_int( $s['card_border_radius'] ?? 8 ) );
        $show_search   = ! empty( $s['show_location_search'] );
        $show_radius   = ! empty( $s['show_radius'] );
        $radius_d      = max( 1, min( 50, absint( $s['radius_default'] ?? 5 ) ) );
        $color         = $this->safe_hex( $s['svc_marker_color'] ?? $s['marker_color'] ?? '', '#2563EB' );
        $btn_bg        = $this->safe_hex( $s['btn_bg'] ?? '', '#2563EB' );
        $btn_color     = $this->safe_hex( $s['btn_color'] ?? '', '#FFFFFF' );
        $btn_text      = $s['btn_text'] ?: 'Ricerca';
        $use_cluster   = ! empty( $s['svc_cluster'] );
        $fit_bounds    = ! empty( $s['svc_fit_bounds'] );
        $fs_enabled    = ! empty( $s['fullscreen_btn'] );
        $emit_schema   = ! empty( $s['emit_schema'] );
        $marker_shape  = sanitize_key( $s['marker_shape'] ?? 'pin' );
        if ( $marker_shape === 'image' ) $marker_shape = 'pin';
        $tile_key      = ! empty( $s['svc_tile_layer'] ) ? $s['svc_tile_layer'] : 'positron';
        $tile_url      = $this->get_tile_layer_url( $tile_key );
        $tile_attr     = $this->get_tile_layer_attr( $tile_key );
        $zoom          = absint( $s['svc_default_zoom'] ) ?: 10;

        $booking_mode     = $s['svc_booking_mode'] ?? 'accommodation';
        $schema_item_type = ( $booking_mode === 'accommodation' ) ? 'LodgingBusiness' : 'LocalBusiness';

        $this->enqueue_leaflet( $use_cluster );

        $marker_info = $this->build_marker_shape_svg( $marker_shape, $color, 32 );

        $js_data = [
            'uid'           => $uid,
            'mapId'         => $map_id,
            'items'         => array_values( $locations ),
            'center'        => $center,
            'zoom'          => $zoom,
            'color'         => $color,
            'perPage'       => $per_page,
            'useCluster'    => $use_cluster,
            'fitBounds'     => $fit_bounds,
            'popupImage'    => (bool) $s['svc_popup_show_image'],
            'popupExcerpt'  => (bool) $s['svc_popup_show_excerpt'],
            'popupPrice'    => (bool) $s['svc_popup_show_price'],
            'popupAltitude' => (bool) $s['svc_popup_show_altitude'],
            'popupSpecs'    => (bool) $s['svc_popup_show_specs'],
            'popupValley'   => (bool) $s['svc_popup_show_valley'],
            'popupMaxWidth' => absint( $s['svc_popup_max_width'] ) ?: 280,
            'popupImgHeight'=> absint( $s['svc_popup_img_height'] ) ?: 180,
            'popupBtnText'  => $s['svc_popup_btn_text'] ?: 'Scopri e Prenota',
            'popupBtnColor' => $this->safe_hex( $s['svc_popup_btn_color'] ?? '', 'var(--olo-color-primary, #e1474f)' ),
            'popupBg'       => $this->safe_hex( $s['svc_popup_bg'] ?? '', '#ffffff' ),
            'popupColor'    => $this->safe_hex( $s['svc_popup_color'] ?? '', '#333333' ),
            'popupRadius'   => Olo_Tile_Utils::radius_int( $s['svc_popup_radius'] ?? 8 ),
            'popupLink'     => true,
            'showRadius'    => $show_radius,
            'radiusDefault' => $radius_d,
            'sortDefault'   => $sort_default,
            'viewMode'      => $view_mode,
            'tileUrl'       => $tile_url,
            'tileAttr'      => $tile_attr,
            'markerSvg'     => $marker_info['svg'],
            'markerW'       => $marker_info['w'],
            'markerH'       => $marker_info['h'],
            'markerAnchorY' => $marker_info['anchor_y'],
            'mode'          => 'services',
            'i18n'          => [
                'noResults'  => olo_t( 'Nessun risultato trovato' ),
                'tryFilters' => olo_t( 'Prova a modificare i filtri' ),
                'cam'        => olo_t( 'cam.' ),
                'bagni'      => olo_t( 'bagni' ),
                'notte'      => olo_t( 'notte' ),
            ],
        ];

        ob_start();
        ?>
        <style>
        <?php echo $this->build_plm_css( $uid, $map_pos, $map_w, $height, $f_cols, $grid_cols, $card_r, $card_mh, $color, $btn_bg, $btn_color, $filter_pos, $filter_w ); ?>
        </style>

        <div class="olo-tile olo-tile--plm <?php echo esc_attr( $uid ); ?>" role="region" aria-label="<?php echo esc_attr( olo_t( 'Mappa servizi' ) ); ?>">
            <div class="plm-map-panel">
                <div class="plm-map" id="<?php echo esc_attr( $map_id ); ?>"></div>
                <?php if ( $fs_enabled ) : ?>
                    <button type="button" class="plm-fullscreen-btn" id="<?php echo esc_attr( $uid ); ?>-fs" title="<?php echo esc_attr( olo_t( 'Schermo intero' ) ); ?>" aria-label="<?php echo esc_attr( olo_t( 'Attiva o disattiva schermo intero' ) ); ?>">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3H5a2 2 0 00-2 2v3m18 0V5a2 2 0 00-2-2h-3m0 18h3a2 2 0 002-2v-3M3 16v3a2 2 0 002 2h3"/></svg>
                    </button>
                <?php endif; ?>
            </div>

            <div class="plm-results-panel">
                <div class="plm-filters" id="<?php echo esc_attr( $uid ); ?>-filters">
                    <div class="plm-filters-grid">
                        <?php if ( $show_search ) : ?>
                            <div class="plm-filter-group plm-filter-group--full">
                                <span class="plm-filter-label"><?php echo esc_html( olo_t( 'Localit&agrave;' ) ); ?></span>
                                <div class="plm-autocomplete-wrap">
                                    <input type="text" class="plm-filter-input" data-filter="location" placeholder="<?php echo esc_attr( olo_t( 'Cerca citt&agrave;, zona, indirizzo...' ) ); ?>" autocomplete="off" />
                                    <ul class="plm-autocomplete-list" id="<?php echo esc_attr( $uid ); ?>-ac"></ul>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ( $show_radius ) : ?>
                            <div class="plm-filter-group plm-filter-group--full">
                                <span class="plm-filter-label"><?php echo esc_html( olo_t( 'Raggio di ricerca' ) ); ?></span>
                                <div class="plm-radius-wrap">
                                    <input type="range" min="1" max="50" value="<?php echo esc_attr( $radius_d ); ?>" data-filter="radius" />
                                    <span class="plm-radius-val"><?php echo esc_html( $radius_d ); ?> km</span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php $this->render_svc_filters_in_grid( $s, $locations ); ?>
                    </div>

                    <div class="plm-actions">
                        <button type="button" class="plm-btn-search" id="<?php echo esc_attr( $uid ); ?>-search"><?php echo esc_html( $btn_text ); ?></button>
                        <button type="button" class="plm-btn-reset" id="<?php echo esc_attr( $uid ); ?>-reset" title="<?php echo esc_attr( olo_t( 'Azzera filtri' ) ); ?>" aria-label="<?php echo esc_attr( olo_t( 'Azzera filtri' ) ); ?>">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 019-9 9.75 9.75 0 016.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 01-9 9 9.75 9.75 0 01-6.74-2.74L3 16"/><path d="M3 21v-5h5"/></svg>
                        </button>
                    </div>
                </div>

                <div class="plm-results-header">
                    <span class="plm-results-count" id="<?php echo esc_attr( $uid ); ?>-count"><strong>0</strong> <?php echo esc_html( olo_t( 'Risultati' ) ); ?></span>
                    <div class="plm-sort-wrap">
                        <select class="plm-sort-select" id="<?php echo esc_attr( $uid ); ?>-sort">
                            <option value="default" <?php selected( $sort_default, 'default' ); ?>><?php echo esc_html( olo_t( 'Ordine predefinito' ) ); ?></option>
                            <option value="title_asc" <?php selected( $sort_default, 'title_asc' ); ?>><?php echo esc_html( olo_t( 'Titolo A-Z' ) ); ?></option>
                            <option value="title_desc" <?php selected( $sort_default, 'title_desc' ); ?>><?php echo esc_html( olo_t( 'Titolo Z-A' ) ); ?></option>
                            <option value="newest" <?php selected( $sort_default, 'newest' ); ?>><?php echo esc_html( olo_t( 'Pi&ugrave; recenti' ) ); ?></option>
                            <option value="distance" <?php selected( $sort_default, 'distance' ); ?>><?php echo esc_html( olo_t( 'Distanza' ) ); ?></option>
                        </select>
                        <div class="plm-view-toggles" role="group" aria-label="<?php echo esc_attr( olo_t( 'Tipo di vista' ) ); ?>">
                            <button type="button" class="plm-view-btn<?php echo $view_mode === 'list' ? ' is-active' : ''; ?>" data-view="list" aria-label="<?php echo esc_attr( olo_t( 'Vista lista' ) ); ?>" title="<?php echo esc_attr( olo_t( 'Vista lista' ) ); ?>">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><circle cx="4" cy="6" r="1" fill="currentColor"/><circle cx="4" cy="12" r="1" fill="currentColor"/><circle cx="4" cy="18" r="1" fill="currentColor"/></svg>
                            </button>
                            <button type="button" class="plm-view-btn<?php echo $view_mode === 'grid' ? ' is-active' : ''; ?>" data-view="grid" aria-label="<?php echo esc_attr( olo_t( 'Vista griglia' ) ); ?>" title="<?php echo esc_attr( olo_t( 'Vista griglia' ) ); ?>">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="plm-results-list<?php echo $view_mode === 'grid' ? ' plm-grid-view' : ''; ?>" id="<?php echo esc_attr( $uid ); ?>-list"></div>

                <div class="plm-pagination" id="<?php echo esc_attr( $uid ); ?>-pagination">
                    <button type="button" class="plm-page-btn" data-page="prev"><?php echo esc_html( olo_t( '&larr; Precedente' ) ); ?></button>
                    <span class="plm-page-info" id="<?php echo esc_attr( $uid ); ?>-pageinfo">1 / 1</span>
                    <button type="button" class="plm-page-btn" data-page="next"><?php echo esc_html( olo_t( 'Successivo &rarr;' ) ); ?></button>
                </div>
            </div>
        </div>

        <?php if ( $emit_schema && ! empty( $locations ) ) : ?>
            <script type="application/ld+json"><?php echo wp_json_encode( $this->build_multi_schema( $locations, $schema_item_type ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
        <?php endif; ?>

        <?php echo $this->build_plm_js( $js_data ); ?>
        <?php
        return ob_get_clean();
    }

    /**
     * Render service-specific filters inside the split-view filters grid.
     * Uses simple select dropdowns so they fit the unified filter layout.
     */
    private function render_svc_filters_in_grid( $s, $locations ) {
        // Altitude
        if ( ! empty( $s['svc_show_altitude_filter'] ) ) {
            $ranges = array_filter( array_map( 'trim', explode( ',', $s['svc_altitude_ranges'] ) ) );
            if ( $ranges ) {
                echo '<div class="plm-filter-group"><span class="plm-filter-label">Altitudine</span>';
                echo '<select class="plm-filter-select" data-filter="altitude"><option value="">Tutte</option>';
                foreach ( $ranges as $range ) {
                    $parts = explode( '-', $range );
                    if ( count( $parts ) !== 2 ) continue;
                    $min = intval( $parts[0] );
                    $max = intval( $parts[1] );
                    if ( $min === 0 ) $lbl = '< ' . number_format( $max, 0, ',', '.' ) . 'm';
                    elseif ( $max >= 9000 ) $lbl = '> ' . number_format( $min, 0, ',', '.' ) . 'm';
                    else $lbl = number_format( $min, 0, ',', '.' ) . ' - ' . number_format( $max, 0, ',', '.' ) . 'm';
                    echo '<option value="' . esc_attr( $range ) . '">' . esc_html( $lbl ) . '</option>';
                }
                echo '</select></div>';
            }
        }
        // Locality / Valley
        if ( ! empty( $s['svc_show_locality_filter'] ) ) {
            $localities = [];
            foreach ( $locations as $loc ) {
                if ( ! empty( $loc['valley'] ) ) $localities[ $loc['valley'] ] = true;
            }
            if ( $localities ) {
                $names = array_keys( $localities );
                sort( $names );
                echo '<div class="plm-filter-group"><span class="plm-filter-label">Localit&agrave;</span>';
                echo '<select class="plm-filter-select" data-filter="valley"><option value="">Tutte</option>';
                foreach ( $names as $n ) {
                    echo '<option value="' . esc_attr( $n ) . '">' . esc_html( $n ) . '</option>';
                }
                echo '</select></div>';
            }
        }
        // Guests
        if ( ! empty( $s['svc_show_guests_filter'] ) ) {
            $ranges = array_filter( array_map( 'trim', explode( ',', $s['svc_guests_ranges'] ) ) );
            if ( $ranges ) {
                echo '<div class="plm-filter-group"><span class="plm-filter-label">Ospiti</span>';
                echo '<select class="plm-filter-select" data-filter="guests"><option value="">Tutti</option>';
                foreach ( $ranges as $range ) {
                    $parts = explode( '-', $range );
                    if ( count( $parts ) !== 2 ) continue;
                    $min = intval( $parts[0] );
                    $max = intval( $parts[1] );
                    $lbl = $max >= 99 ? $min . '+' : $min . '-' . $max;
                    echo '<option value="' . esc_attr( $range ) . '">' . esc_html( $lbl ) . '</option>';
                }
                echo '</select></div>';
            }
        }
        // Price
        if ( ! empty( $s['svc_show_price_filter'] ) ) {
            $ranges = array_filter( array_map( 'trim', explode( ',', $s['svc_price_ranges'] ) ) );
            if ( $ranges ) {
                echo '<div class="plm-filter-group"><span class="plm-filter-label">Prezzo / notte</span>';
                echo '<select class="plm-filter-select" data-filter="price"><option value="">Tutti</option>';
                foreach ( $ranges as $range ) {
                    $parts = explode( '-', $range );
                    if ( count( $parts ) !== 2 ) continue;
                    $min = intval( $parts[0] );
                    $max = intval( $parts[1] );
                    if ( $min === 0 ) $lbl = '< €' . $max;
                    elseif ( $max >= 9000 ) $lbl = '€' . $min . '+';
                    else $lbl = '€' . $min . ' - €' . $max;
                    echo '<option value="' . esc_attr( $range ) . '">' . esc_html( $lbl ) . '</option>';
                }
                echo '</select></div>';
            }
        }
        // Bedrooms
        if ( ! empty( $s['svc_show_bedrooms_filter'] ) ) {
            $has = false;
            foreach ( $locations as $loc ) {
                if ( isset( $loc['bedrooms'] ) ) { $has = true; break; }
            }
            if ( $has ) {
                echo '<div class="plm-filter-group"><span class="plm-filter-label">Camere</span>';
                echo '<select class="plm-filter-select" data-filter="bedrooms"><option value="">Qualsiasi</option>';
                echo '<option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4+">4+</option>';
                echo '</select></div>';
            }
        }
        // Amenities (multi-select as comma list)
        if ( ! empty( $s['svc_show_amenities_filter'] ) ) {
            $amenities = array_filter( array_map( 'trim', explode( ',', $s['svc_amenities_list'] ) ) );
            if ( $amenities ) {
                $labels = class_exists( 'Olo_Amenities_Catalog' )
                    ? Olo_Amenities_Catalog::get_all_labels()
                    : [];
                echo '<div class="plm-filter-group plm-filter-group--full"><span class="plm-filter-label">Servizi</span>';
                echo '<div class="plm-amenities-pills">';
                foreach ( $amenities as $slug ) {
                    $lbl = $labels[ $slug ] ?? ucfirst( $slug );
                    echo '<button type="button" class="plm-amenity-pill" data-amenity="' . esc_attr( $slug ) . '">' . esc_html( $lbl ) . '</button>';
                }
                echo '</div></div>';
            }
        }
    }

    /**
     * Query olo_service posts with GPS coordinates.
     */
    private function query_services( $s ) {
        if ( ! post_type_exists( 'olo_service' ) ) {
            return [];
        }

        $args = [
            'post_type'      => 'olo_service',
            'posts_per_page' => 200,
            'post_status'    => 'publish',
            'no_found_rows'  => true,
        ];

        // Filter by service type if specified
        $booking_mode = $s['svc_booking_mode'] ?? '';
        if ( $booking_mode ) {
            $args['meta_query'] = [
                [
                    'key'   => '_olo_service_type',
                    'value' => $booking_mode,
                ],
            ];
        }

        $query = new WP_Query( $args );
        $locations = [];

        foreach ( $query->posts as $post ) {
            $lat = floatval( get_post_meta( $post->ID, '_olo_service_latitude', true ) );
            $lng = floatval( get_post_meta( $post->ID, '_olo_service_longitude', true ) );

            if ( $lat === 0.0 && $lng === 0.0 ) {
                continue;
            }

            $location = [
                'id'    => $post->ID,
                'title' => get_the_title( $post ),
                'lat'   => $lat,
                'lng'   => $lng,
                'url'   => get_permalink( $post->ID ),
            ];

            // Altitude
            $altitude = get_post_meta( $post->ID, '_olo_service_altitude', true );
            if ( $altitude !== '' && $altitude !== false ) {
                $location['altitude'] = intval( $altitude );
            }

            // Thumbnail
            $thumb = get_the_post_thumbnail_url( $post->ID, 'medium' );
            if ( $thumb ) {
                $location['image'] = $thumb;
            }

            // Excerpt
            $excerpt = has_excerpt( $post->ID )
                ? get_the_excerpt( $post->ID )
                : wp_trim_words( $post->post_content, 20, '&hellip;' );
            if ( $excerpt ) {
                $location['excerpt'] = $excerpt;
            }

            // Service-specific meta
            $meta_map = [
                '_olo_service_price'         => 'price',
                '_olo_service_capacity'      => 'capacity',
                '_olo_service_valley'        => 'valley',
                '_olo_service_bedrooms'      => 'bedrooms',
                '_olo_service_beds'          => 'beds',
                '_olo_service_bathrooms'     => 'bathrooms',
                '_olo_service_sqm'           => 'sqm',
                '_olo_service_type'          => 'service_type',
            ];

            foreach ( $meta_map as $meta_key => $json_key ) {
                $val = get_post_meta( $post->ID, $meta_key, true );
                if ( $val !== '' && $val !== false ) {
                    $location[ $json_key ] = is_numeric( $val ) ? floatval( $val ) : $val;
                }
            }

            // Amenities (stored as serialized array)
            $raw_amenities = get_post_meta( $post->ID, '_olo_service_amenities', true );
            if ( $raw_amenities ) {
                $amenities = maybe_unserialize( $raw_amenities );
                if ( is_array( $amenities ) && ! empty( $amenities ) ) {
                    $location['amenities'] = array_values( $amenities );
                    // Amenity labels for popup display
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
                            'bikes' => 'Biciclette',
                        ];
                    $labeled = [];
                    foreach ( $amenities as $slug ) {
                        $labeled[] = [
                            'slug'  => $slug,
                            'label' => $amenity_labels[ $slug ] ?? ucfirst( $slug ),
                        ];
                    }
                    $location['amenities_labeled'] = $labeled;
                }
            }

            // Gallery (first 4 attachments for popup mini-gallery)
            $gallery_ids = get_post_meta( $post->ID, '_olo_service_gallery', true );
            if ( ! empty( $gallery_ids ) && is_array( $gallery_ids ) ) {
                $gallery_items = [];
                foreach ( array_slice( $gallery_ids, 0, 4 ) as $att_id ) {
                    $thumb = wp_get_attachment_image_url( $att_id, 'thumbnail' );
                    $full  = wp_get_attachment_image_url( $att_id, 'large' );
                    if ( $thumb && $full ) {
                        $gallery_items[] = [ 'thumb' => $thumb, 'full' => $full ];
                    }
                }
                if ( $gallery_items ) {
                    $location['gallery']       = $gallery_items;
                    $location['gallery_count'] = count( $gallery_ids );
                }
            }

            $locations[] = $location;
        }

        wp_reset_postdata();

        return $locations;
    }

    /**
     * Render the filter bar (dispatches to compact or pills based on style).
     */
    private function render_filter_bar( $filter_style, $s, $locations, $map_id ) {
        if ( $filter_style === 'compact' ) :
        ?>
        <div class="olo-map-filters-bar olo-map-filters-bar--compact" data-map-target="<?php echo esc_attr( $map_id ); ?>">
            <?php $this->render_compact_filters( $s, $locations, $map_id ); ?>
            <div class="olo-map-svc-filter-counter" data-map-target="<?php echo esc_attr( $map_id ); ?>">
                <span data-svc-count><?php echo count( $locations ); ?></span> / <?php echo count( $locations ); ?> risultati
            </div>
        </div>
        <?php else : ?>
        <div class="olo-map-filters-bar olo-map-filters-bar--<?php echo esc_attr( $filter_style ); ?>" data-map-target="<?php echo esc_attr( $map_id ); ?>">
            <?php
            if ( ! empty( $s['svc_show_altitude_filter'] ) ) {
                $this->render_altitude_filter( $s['svc_altitude_ranges'], $map_id );
            }
            if ( ! empty( $s['svc_show_locality_filter'] ) ) {
                $this->render_locality_filter( $locations, $map_id );
            }
            if ( ! empty( $s['svc_show_guests_filter'] ) ) {
                $this->render_range_filter( 'guests', $s['svc_guests_ranges'], $map_id );
            }
            if ( ! empty( $s['svc_show_price_filter'] ) ) {
                $this->render_range_filter( 'price', $s['svc_price_ranges'], $map_id );
            }
            if ( ! empty( $s['svc_show_bedrooms_filter'] ) ) {
                $this->render_bedrooms_filter( $locations, $map_id );
            }
            if ( ! empty( $s['svc_show_amenities_filter'] ) ) {
                $this->render_amenities_filter( $s['svc_amenities_list'], $map_id );
            }
            ?>
            <div class="olo-map-svc-filter-counter" data-map-target="<?php echo esc_attr( $map_id ); ?>">
                <span data-svc-count><?php echo count( $locations ); ?></span> / <?php echo count( $locations ); ?> risultati
            </div>
        </div>
        <?php
        endif;
    }

    /**
     * Render compact dropdown filters.
     */
    private function render_compact_filters( $s, $locations, $map_id ) {
        ?>
        <div class="olo-map-compact-row">
        <?php
        // Altitude
        if ( ! empty( $s['svc_show_altitude_filter'] ) ) {
            $ranges = array_filter( array_map( 'trim', explode( ',', $s['svc_altitude_ranges'] ) ) );
            if ( $ranges ) {
                ?>
                <select class="olo-map-compact-select" data-filter-type="altitude" data-map-target="<?php echo esc_attr( $map_id ); ?>">
                    <option value=""><?php echo esc_html( olo_t( 'Altitudine' ) ); ?></option>
                    <?php foreach ( $ranges as $range ) :
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
                <?php
            }
        }

        // Locality / Valley
        if ( ! empty( $s['svc_show_locality_filter'] ) ) {
            $localities = [];
            foreach ( $locations as $loc ) {
                if ( ! empty( $loc['valley'] ) ) $localities[ $loc['valley'] ] = true;
            }
            if ( $localities ) {
                $names = array_keys( $localities );
                sort( $names );
                ?>
                <select class="olo-map-compact-select" data-filter-type="valley" data-map-target="<?php echo esc_attr( $map_id ); ?>">
                    <option value=""><?php echo esc_html( olo_t( 'Localit&agrave;' ) ); ?></option>
                    <?php foreach ( $names as $n ) : ?>
                    <option value="<?php echo esc_attr( $n ); ?>"><?php echo esc_html( $n ); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php
            }
        }

        // Guests
        if ( ! empty( $s['svc_show_guests_filter'] ) ) {
            $ranges = array_filter( array_map( 'trim', explode( ',', $s['svc_guests_ranges'] ) ) );
            if ( $ranges ) {
                ?>
                <select class="olo-map-compact-select" data-filter-type="guests" data-map-target="<?php echo esc_attr( $map_id ); ?>">
                    <option value=""><?php echo esc_html( olo_t( 'Ospiti' ) ); ?></option>
                    <?php foreach ( $ranges as $range ) :
                        $parts = explode( '-', $range );
                        if ( count( $parts ) !== 2 ) continue;
                        $min = intval( $parts[0] );
                        $max = intval( $parts[1] );
                        $label = $max >= 99 ? $min . '+' : $min . '-' . $max;
                    ?>
                    <option value="<?php echo esc_attr( $range ); ?>"><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php
            }
        }

        // Price
        if ( ! empty( $s['svc_show_price_filter'] ) ) {
            $ranges = array_filter( array_map( 'trim', explode( ',', $s['svc_price_ranges'] ) ) );
            if ( $ranges ) {
                ?>
                <select class="olo-map-compact-select" data-filter-type="price" data-map-target="<?php echo esc_attr( $map_id ); ?>">
                    <option value=""><?php echo esc_html( olo_t( 'Prezzo / notte' ) ); ?></option>
                    <?php foreach ( $ranges as $range ) :
                        $parts = explode( '-', $range );
                        if ( count( $parts ) !== 2 ) continue;
                        $min = intval( $parts[0] );
                        $max = intval( $parts[1] );
                        if ( $min === 0 ) $label = '< €' . $max;
                        elseif ( $max >= 9000 ) $label = '€' . $min . '+';
                        else $label = '€' . $min . ' - €' . $max;
                    ?>
                    <option value="<?php echo esc_attr( $range ); ?>"><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php
            }
        }

        // Bedrooms
        if ( ! empty( $s['svc_show_bedrooms_filter'] ) ) {
            $has_bedrooms = false;
            foreach ( $locations as $loc ) {
                if ( isset( $loc['bedrooms'] ) ) { $has_bedrooms = true; break; }
            }
            if ( $has_bedrooms ) {
                ?>
                <select class="olo-map-compact-select" data-filter-type="bedrooms" data-map-target="<?php echo esc_attr( $map_id ); ?>">
                    <option value=""><?php echo esc_html( olo_t( 'Camere' ) ); ?></option>
                    <option value="1"><?php echo esc_html( olo_t( '1 camera' ) ); ?></option>
                    <option value="2"><?php echo esc_html( olo_t( '2 camere' ) ); ?></option>
                    <option value="3"><?php echo esc_html( olo_t( '3 camere' ) ); ?></option>
                    <option value="4+"><?php echo esc_html( olo_t( '4+ camere' ) ); ?></option>
                </select>
                <?php
            }
        }
        ?>
        </div>
        <?php
    }

    /**
     * Render altitude filter pills.
     */
    private function render_altitude_filter( $ranges_str, $map_id ) {
        $ranges = array_filter( array_map( 'trim', explode( ',', $ranges_str ) ) );
        if ( empty( $ranges ) ) {
            return;
        }

        $labels = [];
        foreach ( $ranges as $range ) {
            $parts = explode( '-', $range );
            if ( count( $parts ) !== 2 ) continue;
            $min = intval( $parts[0] );
            $max = intval( $parts[1] );

            if ( $min === 0 ) {
                $label = '< ' . number_format( $max, 0, ',', '.' ) . 'm';
            } elseif ( $max >= 9000 ) {
                $label = '> ' . number_format( $min, 0, ',', '.' ) . 'm';
            } else {
                $label = number_format( $min, 0, ',', '.' ) . ' - ' . number_format( $max, 0, ',', '.' ) . 'm';
            }
            $labels[] = [ 'range' => $range, 'label' => $label ];
        }
        ?>
        <div class="olo-map-svc-filter-group" data-filter-type="altitude" data-map-target="<?php echo esc_attr( $map_id ); ?>">
            <span class="olo-map-svc-filter-label"><?php echo esc_html( olo_t( 'Altitudine' ) ); ?></span>
            <div class="olo-map-svc-filter-pills">
                <button class="olo-map-filter-pill olo-map-filter-active" data-svc-filter=""><?php echo esc_html( olo_t( 'Tutte' ) ); ?></button>
                <?php foreach ( $labels as $item ) : ?>
                    <button class="olo-map-filter-pill" data-svc-filter="<?php echo esc_attr( $item['range'] ); ?>">
                        <?php echo esc_html( $item['label'] ); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render locality filter pills (dynamic from unique localities).
     */
    private function render_locality_filter( $locations, $map_id ) {
        $localities = [];
        foreach ( $locations as $loc ) {
            if ( ! empty( $loc['valley'] ) ) {
                $localities[ $loc['valley'] ] = true;
            }
        }
        if ( empty( $localities ) ) {
            return;
        }
        $locality_names = array_keys( $localities );
        sort( $locality_names );
        ?>
        <div class="olo-map-svc-filter-group" data-filter-type="valley" data-map-target="<?php echo esc_attr( $map_id ); ?>">
            <span class="olo-map-svc-filter-label"><?php echo esc_html( olo_t( 'Localit&agrave;' ) ); ?></span>
            <div class="olo-map-svc-filter-pills">
                <button class="olo-map-filter-pill olo-map-filter-active" data-svc-filter=""><?php echo esc_html( olo_t( 'Tutte' ) ); ?></button>
                <?php foreach ( $locality_names as $loc_name ) : ?>
                    <button class="olo-map-filter-pill" data-svc-filter="<?php echo esc_attr( $loc_name ); ?>">
                        <?php echo esc_html( $loc_name ); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render range filter pills (reusable for guests and price).
     */
    private function render_range_filter( $type, $ranges_str, $map_id ) {
        $ranges = array_filter( array_map( 'trim', explode( ',', $ranges_str ) ) );
        if ( empty( $ranges ) ) {
            return;
        }

        $labels_map = [
            'guests' => olo_t( 'Ospiti' ),
            'price'  => olo_t( 'Prezzo / notte' ),
        ];
        $group_label = $labels_map[ $type ] ?? ucfirst( $type );

        $labels = [];
        foreach ( $ranges as $range ) {
            $parts = explode( '-', $range );
            if ( count( $parts ) !== 2 ) continue;
            $min = intval( $parts[0] );
            $max = intval( $parts[1] );

            if ( $type === 'price' ) {
                if ( $min === 0 ) {
                    $label = '< &euro;' . $max;
                } elseif ( $max >= 9000 ) {
                    $label = '&euro;' . $min . '+';
                } else {
                    $label = '&euro;' . $min . ' - &euro;' . $max;
                }
            } else {
                // guests
                if ( $max >= 99 ) {
                    $label = $min . '+';
                } else {
                    $label = $min . '-' . $max;
                }
            }
            $labels[] = [ 'range' => $range, 'label' => $label ];
        }
        ?>
        <div class="olo-map-svc-filter-group" data-filter-type="<?php echo esc_attr( $type ); ?>" data-map-target="<?php echo esc_attr( $map_id ); ?>">
            <span class="olo-map-svc-filter-label"><?php echo esc_html( $group_label ); ?></span>
            <div class="olo-map-svc-filter-pills">
                <button class="olo-map-filter-pill olo-map-filter-active" data-svc-filter=""><?php echo esc_html( olo_t( 'Tutti' ) ); ?></button>
                <?php foreach ( $labels as $item ) : ?>
                    <button class="olo-map-filter-pill" data-svc-filter="<?php echo esc_attr( $item['range'] ); ?>">
                        <?php echo $item['label']; ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render bedrooms filter pills (1, 2, 3, 4+).
     */
    private function render_bedrooms_filter( $locations, $map_id ) {
        $has_bedrooms = false;
        foreach ( $locations as $loc ) {
            if ( isset( $loc['bedrooms'] ) ) {
                $has_bedrooms = true;
                break;
            }
        }
        if ( ! $has_bedrooms ) {
            return;
        }
        ?>
        <div class="olo-map-svc-filter-group" data-filter-type="bedrooms" data-map-target="<?php echo esc_attr( $map_id ); ?>">
            <span class="olo-map-svc-filter-label"><?php echo esc_html( olo_t( 'Camere' ) ); ?></span>
            <div class="olo-map-svc-filter-pills">
                <button class="olo-map-filter-pill olo-map-filter-active" data-svc-filter=""><?php echo esc_html( olo_t( 'Tutte' ) ); ?></button>
                <button class="olo-map-filter-pill" data-svc-filter="1">1</button>
                <button class="olo-map-filter-pill" data-svc-filter="2">2</button>
                <button class="olo-map-filter-pill" data-svc-filter="3">3</button>
                <button class="olo-map-filter-pill" data-svc-filter="4+">4+</button>
            </div>
        </div>
        <?php
    }

    /**
     * Render amenities filter pills (multi-select toggle).
     */
    private function render_amenities_filter( $amenities_str, $map_id ) {
        $amenities = array_filter( array_map( 'trim', explode( ',', $amenities_str ) ) );
        if ( empty( $amenities ) ) {
            return;
        }

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
                'bikes' => 'Biciclette', 'elevator' => 'Ascensore', 'accessible' => 'Accessibile',
            ];

        $amenity_icons = [
            'wifi'      => '&#128246;',
            'fireplace' => '&#128293;',
            'sauna'     => '&#9832;',
            'hottub'    => '&#128704;',
            'pets'      => '&#128054;',
            'ski'       => '&#9975;',
            'bbq'       => '&#127830;',
            'garden'    => '&#127793;',
        ];
        ?>
        <div class="olo-map-svc-filter-group olo-map-svc-filter-group--amenities" data-filter-type="amenities" data-map-target="<?php echo esc_attr( $map_id ); ?>">
            <span class="olo-map-svc-filter-label"><?php echo esc_html( olo_t( 'Servizi' ) ); ?></span>
            <div class="olo-map-svc-filter-pills">
                <?php foreach ( $amenities as $slug ) :
                    $label = $amenity_labels[ $slug ] ?? ucfirst( $slug );
                    $icon  = $amenity_icons[ $slug ] ?? '';
                ?>
                    <button class="olo-map-filter-pill olo-map-filter-pill--amenity" data-svc-filter="<?php echo esc_attr( $slug ); ?>">
                        <?php if ( $icon ) : ?><span class="olo-map-amenity-icon"><?php echo $icon; ?></span><?php endif; ?>
                        <?php echo esc_html( $label ); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    private function parse_coords( $address ) {
        if ( preg_match( '/^(-?\d+\.?\d*)\s*,\s*(-?\d+\.?\d*)$/', trim( $address ), $m ) ) {
            return [ 'lat' => floatval( $m[1] ), 'lng' => floatval( $m[2] ) ];
        }
        return null;
    }

    /**
     * Normalize a single dimension: accepts "55", "55%", "400px". Plain numbers → "%".
     * Falls back to $fallback when invalid or out of range (10-95 for %).
     */
    private function normalize_dim( $v, $fallback = '55%' ) {
        $v = trim( (string) $v );
        if ( preg_match( '/^(\d+)(%|px)$/', $v, $m ) ) {
            if ( $m[2] === '%' ) {
                $n = max( 10, min( 95, (int) $m[1] ) );
                return $n . '%';
            }
            return $v; // keep "Xpx" untouched
        }
        if ( preg_match( '/^\d+$/', $v ) ) {
            $n = max( 10, min( 95, (int) $v ) );
            return $n . '%';
        }
        return $fallback;
    }

    /**
     * Legacy alias kept for BC (some code paths may still call this).
     */
    private function normalize_map_width( $v ) {
        return $this->normalize_dim( $v, '55%' );
    }

    /**
     * Resolve map vs filter dimensions.
     * Priority:
     *   1. filter_width set → panel takes that %, map takes the rest
     *   2. map_width set    → legacy; map takes that %, panel takes the rest
     *   3. default          → map 55%, panel 45%
     * Returns [ $map_w_css, $filter_w_css ] both as percent strings like "45%".
     */
    private function resolve_dims( $s ) {
        $fw_raw = trim( (string) ( $s['filter_width'] ?? '' ) );
        $mw_raw = trim( (string) ( $s['map_width'] ?? '' ) );

        if ( $fw_raw !== '' ) {
            $filter_w = $this->normalize_dim( $fw_raw, '45%' );
            // Complement as percent if filter is percent; otherwise 1fr fallback
            if ( preg_match( '/^(\d+)%$/', $filter_w, $m ) ) {
                $map_w = ( 100 - (int) $m[1] ) . '%';
            } else {
                $map_w = '1fr'; // fixed filter px → map flexible
            }
            return [ $map_w, $filter_w ];
        }
        if ( $mw_raw !== '' ) {
            $map_w = $this->normalize_dim( $mw_raw, '55%' );
            if ( preg_match( '/^(\d+)%$/', $map_w, $m ) ) {
                $filter_w = ( 100 - (int) $m[1] ) . '%';
            } else {
                $filter_w = '1fr';
            }
            return [ $map_w, $filter_w ];
        }
        return [ '55%', '45%' ];
    }

    /**
     * Safe color fallback (class may extend Olo_Tile_Base that provides safe_color_css,
     * but guard here in case of environments where it's missing).
     */
    private function safe_hex( $c, $fallback = '#e74c3c' ) {
        if ( method_exists( $this, 'safe_color_css' ) ) {
            $sanitized = $this->safe_color_css( $c );
            if ( $sanitized ) {
                return $sanitized;
            }
        }
        if ( is_string( $c ) && preg_match( '/^#[0-9a-fA-F]{3,8}$/', $c ) ) {
            return $c;
        }
        return $fallback;
    }

    /**
     * Build an SVG string for a marker shape colored with $color.
     * Shapes: pin, drop, circle, square, diamond, star, flag, heart.
     * Returns [ 'svg' => string, 'w' => int, 'h' => int, 'anchor_y' => int ].
     */
    private function build_marker_shape_svg( $shape, $color, $size = 32 ) {
        $ms = absint( $size ) ?: 32;
        $c  = esc_attr( $color );
        $tall_h = (int) round( $ms * 1.43 );
        $heart_h = (int) round( $ms * 0.9 );
        $flag_h  = (int) round( $ms * 1.2 );

        $svgs = [
            'pin'     => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $ms . '" height="' . $tall_h . '" viewBox="0 0 24 36"><path d="M12 0C5.4 0 0 5.4 0 12c0 9 12 24 12 24s12-15 12-24C24 5.4 18.6 0 12 0zm0 16c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4z" fill="' . $c . '" stroke="#fff" stroke-width="0.8"/></svg>',
            'drop'    => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $ms . '" height="' . $tall_h . '" viewBox="0 0 24 34"><path d="M12 0C5.4 0 0 5.4 0 12c0 8 12 22 12 22s12-14 12-22C24 5.4 18.6 0 12 0z" fill="' . $c . '" stroke="#fff" stroke-width="1"/></svg>',
            'circle'  => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $ms . '" height="' . $ms . '" viewBox="0 0 24 24"><circle cx="12" cy="12" r="11" fill="' . $c . '" stroke="#fff" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="#fff"/></svg>',
            'square'  => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $ms . '" height="' . $ms . '" viewBox="0 0 24 24"><rect x="1" y="1" width="22" height="22" rx="4" fill="' . $c . '" stroke="#fff" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="#fff"/></svg>',
            'diamond' => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $ms . '" height="' . $ms . '" viewBox="0 0 24 24"><path d="M12 1L23 12 12 23 1 12z" fill="' . $c . '" stroke="#fff" stroke-width="1.5"/><circle cx="12" cy="12" r="3.5" fill="#fff"/></svg>',
            'star'    => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $ms . '" height="' . $ms . '" viewBox="0 0 24 24"><path d="M12 1l3.09 6.26L22 8.27l-5 4.87 1.18 6.88L12 16.77 5.82 20.02 7 13.14 2 8.27l6.91-1.01z" fill="' . $c . '" stroke="#fff" stroke-width="0.8"/></svg>',
            'flag'    => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $ms . '" height="' . $flag_h . '" viewBox="0 0 24 30"><line x1="4" y1="2" x2="4" y2="28" stroke="' . $c . '" stroke-width="2.5" stroke-linecap="round"/><path d="M4 3h16l-4 5.5 4 5.5H4z" fill="' . $c . '" opacity="0.9"/></svg>',
            'heart'   => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $ms . '" height="' . $heart_h . '" viewBox="0 0 24 22"><path d="M12 21C12 21 2 13.5 2 7.5 2 4.46 4.46 2 7.5 2c1.74 0 3.41.81 4.5 2.09A5.99 5.99 0 0116.5 2C19.54 2 22 4.46 22 7.5 22 13.5 12 21 12 21z" fill="' . $c . '" stroke="#fff" stroke-width="1"/></svg>',
        ];

        $shape = isset( $svgs[ $shape ] ) ? $shape : 'pin';
        $svg   = $svgs[ $shape ];

        $is_tall = in_array( $shape, [ 'pin', 'drop', 'flag' ], true );
        if ( $shape === 'heart' ) {
            $h = $heart_h;
        } elseif ( $shape === 'flag' ) {
            $h = $flag_h;
        } elseif ( $is_tall ) {
            $h = $tall_h;
        } else {
            $h = $ms;
        }
        $anchor_y = $is_tall ? $h : (int) round( $h / 2 );

        return [
            'svg'       => $svg,
            'w'         => $ms,
            'h'         => $h,
            'anchor_y'  => $anchor_y,
            'is_tall'   => $is_tall,
        ];
    }

    /**
     * Build cluster CSS overrides scoped to a given uid and colored with $color.
     * Uses $color + alpha hex suffix to tint the cluster background.
     */
    private function build_cluster_css( $uid, $color ) {
        $c = $this->safe_hex( $color, '#e1474f' );
        $sel = '.' . $uid;
        return $sel . ' .marker-cluster-small { background-color: ' . $c . '33; }'
             . $sel . ' .marker-cluster-small div { background-color: ' . $c . '; color: #fff; }'
             . $sel . ' .marker-cluster-medium { background-color: ' . $c . '44; }'
             . $sel . ' .marker-cluster-medium div { background-color: ' . $c . '; color: #fff; }'
             . $sel . ' .marker-cluster-large { background-color: ' . $c . '55; }'
             . $sel . ' .marker-cluster-large div { background-color: ' . $c . '; color: #fff; }';
    }

    /**
     * Build JSON-LD ItemList schema for a list of locations/services.
     * $item_type: 'Place' | 'LodgingBusiness' | 'LocalBusiness'.
     */
    private function build_multi_schema( $locations, $item_type = 'Place' ) {
        $item_type = in_array( $item_type, [ 'Place', 'LodgingBusiness', 'LocalBusiness' ], true ) ? $item_type : 'Place';
        $list_items = [];
        foreach ( $locations as $i => $loc ) {
            $schema_item = [
                '@type' => $item_type,
                'name'  => isset( $loc['title'] ) ? $loc['title'] : '',
            ];
            if ( ! empty( $loc['url'] ) ) {
                $schema_item['url'] = $loc['url'];
            }
            if ( ! empty( $loc['image'] ) ) {
                $schema_item['image'] = $loc['image'];
            }
            if ( isset( $loc['lat'], $loc['lng'] ) ) {
                $schema_item['geo'] = [
                    '@type'     => 'GeoCoordinates',
                    'latitude'  => floatval( $loc['lat'] ),
                    'longitude' => floatval( $loc['lng'] ),
                ];
            }
            if ( ! empty( $loc['address'] ) ) {
                $schema_item['address'] = [
                    '@type'         => 'PostalAddress',
                    'streetAddress' => $loc['address'],
                ];
            }
            if ( ! empty( $loc['price'] ) && $item_type !== 'Place' ) {
                $schema_item['priceRange'] = '€' . $loc['price'];
            }
            $list_items[] = [
                '@type'    => 'ListItem',
                'position' => $i + 1,
                'item'     => $schema_item,
            ];
        }
        return [
            '@context'        => 'https://schema.org',
            '@type'           => 'ItemList',
            'itemListElement' => $list_items,
        ];
    }

    /**
     * Build the fullscreen toggle button HTML.
     */
    private function build_fullscreen_btn( $uid ) {
        ob_start();
        ?>
        <button type="button" class="olo-map-fs-btn" data-fs-for="<?php echo esc_attr( $uid ); ?>" title="<?php echo esc_attr( olo_t( 'Schermo intero' ) ); ?>" aria-label="<?php echo esc_attr( olo_t( 'Attiva o disattiva schermo intero' ) ); ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M8 3H5a2 2 0 00-2 2v3m18 0V5a2 2 0 00-2-2h-3m0 18h3a2 2 0 002-2v-3M3 16v3a2 2 0 002 2h3"/></svg>
        </button>
        <?php
        return ob_get_clean();
    }

    /**
     * Enqueue Leaflet (and MarkerCluster if requested).
     */
    private function enqueue_leaflet( $cluster ) {
        $vendor = defined( 'OLO_URL' ) ? OLO_URL . 'assets/vendor/leaflet/' : 'https://unpkg.com/leaflet@1.9.4/dist/';
        if ( ! wp_style_is( 'leaflet', 'enqueued' ) && ! wp_style_is( 'leaflet-css', 'enqueued' ) ) {
            wp_enqueue_style( 'leaflet', $vendor . 'leaflet.css', [], '1.9.4' );
        }
        if ( ! wp_script_is( 'leaflet', 'enqueued' ) ) {
            wp_enqueue_script( 'leaflet', $vendor . 'leaflet.js', [], '1.9.4', true );
        }
        if ( $cluster ) {
            if ( ! wp_style_is( 'leaflet-markercluster-css', 'enqueued' ) ) {
                wp_enqueue_style( 'leaflet-markercluster-css', $vendor . 'leaflet.markercluster.css', [ 'leaflet' ], '1.5.3' );
                wp_enqueue_style( 'leaflet-markercluster-default-css', $vendor . 'leaflet.markercluster-default.css', [ 'leaflet-markercluster-css' ], '1.5.3' );
            }
            if ( ! wp_script_is( 'leaflet-markercluster-js', 'enqueued' ) ) {
                wp_enqueue_script( 'leaflet-markercluster-js', $vendor . 'leaflet.markercluster.js', [ 'leaflet' ], '1.5.3', true );
            }
        }
    }

    /**
     * Build split-view CSS scoped to a unique id. Mirrors the PropertyMapSearch layout.
     */
    private function build_plm_css( $uid, $map_pos, $map_w, $height, $f_cols, $g_cols, $card_r, $card_mh, $color, $btn_bg, $btn_color, $filter_pos = 'right', $filter_w = '45%' ) {
        $sel = '.' . $uid;

        // Two areas: M (map) and R (results-panel which contains filters + list + pag).
        // $map_w and $filter_w are both defined — map and panel share the flexible dimension.
        // Both are already percent strings (or "1fr" if px-based).
        if ( $filter_pos === 'right' ) {
            $grid_template = 'grid-template-columns: ' . $map_w . ' ' . $filter_w . '; grid-template-rows: 100%; grid-template-areas: "M R";';
        } elseif ( $filter_pos === 'left' ) {
            $grid_template = 'grid-template-columns: ' . $filter_w . ' ' . $map_w . '; grid-template-rows: 100%; grid-template-areas: "R M";';
        } elseif ( $filter_pos === 'top' ) {
            $grid_template = 'grid-template-columns: 100%; grid-template-rows: ' . $filter_w . ' ' . $map_w . '; grid-template-areas: "R" "M";';
        } else { // bottom
            $filter_pos    = 'bottom';
            $grid_template = 'grid-template-columns: 100%; grid-template-rows: ' . $map_w . ' ' . $filter_w . '; grid-template-areas: "M" "R";';
        }

        ob_start();
        ?>
        <?php echo $sel; ?> { display: grid; <?php echo $grid_template; ?> width: 100%; max-width: 100%; height: <?php echo $height; ?>px; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06); font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, sans-serif; box-sizing: border-box; }
        <?php echo $sel; ?> .plm-map-panel { position: relative; grid-area: M; min-height: 0; min-width: 0; }
        <?php echo $sel; ?> .plm-map { width: 100%; height: 100%; z-index: 1; }
        <?php echo $sel; ?> .plm-fullscreen-btn { position: absolute; top: 10px; right: 10px; z-index: 1000; width: 34px; height: 34px; background: #fff; border: 2px solid rgba(0,0,0,0.2); border-radius: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0; color: #374151; }
        <?php echo $sel; ?> .plm-fullscreen-btn:hover { background: #f4f4f4; }
        <?php echo $sel; ?> .plm-results-panel { grid-area: R; display: flex; flex-direction: column; overflow: hidden; background: #FAFBFC; min-height: 0; min-width: 0; }
        <?php echo $sel; ?> .plm-filters { padding: 16px 20px; background: #fff; border-bottom: 1px solid #E5E7EB; flex-shrink: 0; min-width: 0; box-sizing: border-box; }
        <?php echo $sel; ?> .plm-filters-grid { display: grid; grid-template-columns: repeat(<?php echo $f_cols; ?>, 1fr); gap: 10px; }
        <?php echo $sel; ?> .plm-filter-group { display: flex; flex-direction: column; gap: 3px; }
        <?php echo $sel; ?> .plm-filter-group--full { grid-column: 1 / -1; }
        <?php echo $sel; ?> .plm-filter-label { font-size: 11px; font-weight: 600; color: #6B7280; text-transform: uppercase; letter-spacing: 0.04em; }
        <?php echo $sel; ?> .plm-filter-input, <?php echo $sel; ?> .plm-filter-select { padding: 8px 10px; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 13px; color: #374151; background: #fff; width: 100%; box-sizing: border-box; font-family: inherit; transition: border-color 0.15s; }
        <?php echo $sel; ?> .plm-filter-input:focus, <?php echo $sel; ?> .plm-filter-select:focus { outline: none; border-color: <?php echo $color; ?>; box-shadow: 0 0 0 3px <?php echo $color; ?>22; }
        <?php echo $sel; ?> .plm-radius-wrap { display: flex; align-items: center; gap: 8px; }
        <?php echo $sel; ?> .plm-radius-wrap input[type="range"] { flex: 1; accent-color: <?php echo $color; ?>; height: 4px; }
        <?php echo $sel; ?> .plm-radius-val { font-size: 12px; font-weight: 600; color: #374151; min-width: 40px; text-align: right; }
        <?php echo $sel; ?> .plm-amenities-pills { display: flex; flex-wrap: wrap; gap: 6px; }
        <?php echo $sel; ?> .plm-amenity-pill { padding: 5px 10px; border: 1px solid #D1D5DB; border-radius: 999px; background: #fff; font-size: 11px; color: #374151; cursor: pointer; font-family: inherit; transition: all 0.15s; }
        <?php echo $sel; ?> .plm-amenity-pill:hover { background: #F3F4F6; }
        <?php echo $sel; ?> .plm-amenity-pill.is-active { background: <?php echo $color; ?>; border-color: <?php echo $color; ?>; color: #fff; }
        <?php echo $sel; ?> .plm-actions { display: flex; gap: 8px; margin-top: 12px; align-items: center; }
        <?php echo $sel; ?> .plm-btn-search { flex: 1; padding: 10px 16px; background: <?php echo $btn_bg; ?>; color: <?php echo $btn_color; ?>; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; transition: opacity 0.2s; font-family: inherit; }
        <?php echo $sel; ?> .plm-btn-search:hover { opacity: 0.88; }
        <?php echo $sel; ?> .plm-btn-reset { padding: 10px 14px; background: transparent; color: #6B7280; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 13px; cursor: pointer; font-family: inherit; transition: background 0.15s; }
        <?php echo $sel; ?> .plm-btn-reset:hover { background: #F3F4F6; }
        <?php echo $sel; ?> .plm-results-header { display: flex; align-items: center; justify-content: space-between; padding: 10px 20px; background: #fff; border-bottom: 1px solid #E5E7EB; flex-shrink: 0; gap: 8px; flex-wrap: wrap; }
        <?php echo $sel; ?> .plm-results-count { font-size: 13px; font-weight: 600; color: #374151; }
        <?php echo $sel; ?> .plm-results-count strong { color: <?php echo $color; ?>; }
        <?php echo $sel; ?> .plm-sort-wrap { display: flex; align-items: center; gap: 8px; }
        <?php echo $sel; ?> .plm-sort-select { padding: 5px 8px; border: 1px solid #D1D5DB; border-radius: 4px; font-size: 12px; color: #374151; background: #fff; font-family: inherit; }
        <?php echo $sel; ?> .plm-view-toggles { display: flex; gap: 2px; }
        <?php echo $sel; ?> .plm-view-btn { width: 30px; height: 30px; border: 1px solid #D1D5DB; background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0; color: #9CA3AF; transition: background 0.15s, color 0.15s; }
        <?php echo $sel; ?> .plm-view-btn:first-child { border-radius: 4px 0 0 4px; }
        <?php echo $sel; ?> .plm-view-btn:last-child { border-radius: 0 4px 4px 0; }
        <?php echo $sel; ?> .plm-view-btn.is-active { background: <?php echo $color; ?>; border-color: <?php echo $color; ?>; color: #fff; }
        <?php echo $sel; ?> .plm-results-list { flex: 1; overflow-y: auto; padding: 12px 16px; min-height: 0; }
        <?php echo $sel; ?> .plm-card { display: grid; grid-template-columns: 38% 1fr; border: 1px solid #E5E7EB; border-radius: <?php echo $card_r; ?>px; overflow: hidden;<?php if ( $card_mh > 0 ) echo ' max-height: ' . $card_mh . 'px;'; ?> background: #fff; margin-bottom: 10px; transition: box-shadow 0.2s, transform 0.15s; cursor: pointer; text-decoration: none; color: inherit; }
        <?php echo $sel; ?> .plm-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1); transform: translateY(-1px); }
        <?php echo $sel; ?> .plm-card.is-highlighted { box-shadow: 0 0 0 2px <?php echo $color; ?>, 0 4px 12px rgba(0,0,0,0.1); }
        <?php echo $sel; ?> .plm-results-list.plm-grid-view { display: grid; grid-template-columns: repeat(<?php echo $g_cols; ?>, 1fr); gap: 10px; padding: 12px 16px; align-content: start; }
        <?php echo $sel; ?> .plm-results-list.plm-grid-view .plm-card { display: flex; flex-direction: column; margin-bottom: 0; position: relative; min-height: 220px; }
        <?php echo $sel; ?> .plm-results-list.plm-grid-view .plm-card-img { position: absolute; inset: 0; height: 100%; }
        <?php echo $sel; ?> .plm-results-list.plm-grid-view .plm-card-body { position: relative; z-index: 2; margin-top: auto; background: linear-gradient(0deg, rgba(0,0,0,0.78) 0%, rgba(0,0,0,0.45) 70%, transparent 100%); padding: 40px 12px 10px; border-radius: 0 0 <?php echo $card_r; ?>px <?php echo $card_r; ?>px; }
        <?php echo $sel; ?> .plm-results-list.plm-grid-view .plm-card-title,
        <?php echo $sel; ?> .plm-results-list.plm-grid-view .plm-card-sub,
        <?php echo $sel; ?> .plm-results-list.plm-grid-view .plm-card-price { color: #fff; }
        <?php echo $sel; ?> .plm-card-img { position: relative; overflow: hidden; background: #E5E7EB; min-height: 110px; }
        <?php echo $sel; ?> .plm-card-img img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.3s; }
        <?php echo $sel; ?> .plm-card:hover .plm-card-img img { transform: scale(1.05); }
        <?php echo $sel; ?> .plm-card-img-ph { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 28px; color: #9CA3AF; }
        <?php echo $sel; ?> .plm-card-body { padding: 12px 14px; display: flex; flex-direction: column; gap: 4px; min-width: 0; }
        <?php echo $sel; ?> .plm-card-title { font-size: 14px; font-weight: 700; color: #1F2937; margin: 0; line-height: 1.3; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        <?php echo $sel; ?> .plm-card-sub { font-size: 12px; color: #6B7280; margin: 0; }
        <?php echo $sel; ?> .plm-card-specs { display: flex; gap: 10px; font-size: 11px; color: #6B7280; flex-wrap: wrap; margin-top: 4px; }
        <?php echo $sel; ?> .plm-card-specs span { display: inline-flex; align-items: center; gap: 3px; white-space: nowrap; }
        <?php echo $sel; ?> .plm-card-price { font-size: 16px; font-weight: 700; color: <?php echo $color; ?>; margin-top: auto; padding-top: 4px; text-align: right; }
        <?php echo $sel; ?> .plm-pagination { display: flex; justify-content: center; align-items: center; gap: 8px; padding: 10px 20px; background: #fff; border-top: 1px solid #E5E7EB; flex-shrink: 0; }
        <?php echo $sel; ?> .plm-page-btn { padding: 6px 14px; border: 1px solid #D1D5DB; border-radius: 4px; background: #fff; color: #374151; font-size: 13px; cursor: pointer; font-family: inherit; transition: background 0.15s; }
        <?php echo $sel; ?> .plm-page-btn:hover { background: #F3F4F6; }
        <?php echo $sel; ?> .plm-page-btn:disabled { opacity: 0.4; cursor: default; }
        <?php echo $sel; ?> .plm-page-info { font-size: 12px; color: #6B7280; }
        <?php echo $sel; ?> .plm-no-results { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px 20px; color: #9CA3AF; text-align: center; gap: 8px; }
        <?php echo $sel; ?> .plm-no-results-title { font-size: 15px; font-weight: 600; color: #6B7280; }
        <?php echo $sel; ?> .plm-autocomplete-wrap { position: relative; }
        <?php echo $sel; ?> .plm-autocomplete-list { display: none; position: absolute; z-index: 1000; top: 100%; left: 0; right: 0; margin: 2px 0 0; padding: 4px 0; list-style: none; background: #fff; border: 1px solid #D1D5DB; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.12); max-height: 220px; overflow-y: auto; }
        <?php echo $sel; ?> .plm-autocomplete-list.is-open { display: block; }
        <?php echo $sel; ?> .plm-ac-item { padding: 8px 12px; font-size: 13px; color: #374151; cursor: pointer; line-height: 1.35; }
        <?php echo $sel; ?> .plm-ac-item:hover, <?php echo $sel; ?> .plm-ac-item.is-active { background: #F3F4F6; }
        <?php echo $sel; ?> .plm-popup { font-family: inherit; min-width: 200px; }
        <?php echo $sel; ?> .plm-popup img { width: 100%; height: auto; object-fit: cover; border-radius: 4px; margin-bottom: 6px; display: block; }
        <?php echo $sel; ?> .plm-popup h4 { margin: 0 0 2px; font-size: 13px; font-weight: 700; color: #1F2937; }
        <?php echo $sel; ?> .plm-popup .plm-popup-sub { font-size: 11px; color: #6B7280; margin: 2px 0 4px; }
        <?php echo $sel; ?> .plm-popup .plm-popup-price { font-size: 15px; font-weight: 700; color: <?php echo $color; ?>; margin: 0 0 6px; }
        <?php echo $sel; ?> .plm-popup a.plm-popup-link { display: inline-block; padding: 4px 12px; background: <?php echo $color; ?>; color: #fff; border-radius: 4px; font-size: 11px; font-weight: 600; text-decoration: none; }
        <?php echo $sel; ?> .plm-marker-icon { background: none !important; border: none !important; }
        <?php echo $sel; ?> .marker-cluster-small { background-color: <?php echo $color; ?>33; }
        <?php echo $sel; ?> .marker-cluster-small div { background-color: <?php echo $color; ?>; color: #fff; }
        <?php echo $sel; ?> .marker-cluster-medium { background-color: <?php echo $color; ?>44; }
        <?php echo $sel; ?> .marker-cluster-medium div { background-color: <?php echo $color; ?>; color: #fff; }
        <?php echo $sel; ?> .marker-cluster-large { background-color: <?php echo $color; ?>55; }
        <?php echo $sel; ?> .marker-cluster-large div { background-color: <?php echo $color; ?>; color: #fff; }
        @media (max-width: 900px) {
            <?php echo $sel; ?> {
                grid-template-columns: 1fr;
                grid-template-rows: 350px 500px;
                grid-template-areas: "M" "R";
                height: auto;
            }
            <?php echo $sel; ?> .plm-map-panel { height: 350px; }
            <?php echo $sel; ?> .plm-results-panel { height: 500px; }
            <?php echo $sel; ?> .plm-results-list.plm-grid-view { grid-template-columns: 1fr; }
        }
        @media (max-width: 600px) {
            <?php echo $sel; ?> .plm-filters-grid { grid-template-columns: 1fr; }
            <?php echo $sel; ?> .plm-card { grid-template-columns: 1fr; }
            <?php echo $sel; ?> .plm-card-img { height: 160px; }
        }
        <?php
        return ob_get_clean();
    }

    /**
     * Build the inline JS that initializes the Leaflet map + filters + list.
     * Self-contained IIFE that waits for L global; clones PropertyMapSearch behavior.
     */
    private function build_plm_js( $data ) {
        $json = wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        ob_start();
        ?>
        <script>
        (function(){
            var D = <?php echo $json; ?>;
            function ready(fn) {
                if (document.readyState !== 'loading') return fn();
                document.addEventListener('DOMContentLoaded', fn);
            }
            function waitL(fn, tries) {
                tries = tries || 0;
                if (typeof L !== 'undefined') return fn();
                if (tries > 100) return;
                setTimeout(function(){ waitL(fn, tries + 1); }, 50);
            }
            ready(function(){ waitL(function(){ init(D); }); });

            function init(D) {
                var UID = D.uid, MAP_ID = D.mapId;
                var ITEMS = D.items || [];
                var CENTER = D.center, ZOOM = D.zoom, COLOR = D.color;
                var PER_PAGE = D.perPage;
                var USE_CLUSTER = D.useCluster && typeof L.markerClusterGroup === 'function';
                var IS_SVC = D.mode === 'services';

                var root    = document.querySelector('.' + UID);
                if (!root) return;
                var mapEl   = document.getElementById(MAP_ID);
                var listEl  = document.getElementById(UID + '-list');
                var countEl = document.getElementById(UID + '-count');
                var sortEl  = document.getElementById(UID + '-sort');
                var searchBtn = document.getElementById(UID + '-search');
                var resetBtn  = document.getElementById(UID + '-reset');
                var pageInfoEl = document.getElementById(UID + '-pageinfo');
                var pagEl = document.getElementById(UID + '-pagination');
                var fsBtn = document.getElementById(UID + '-fs');
                if (!mapEl) return;

                var filtered = ITEMS.slice();
                var page = 1;
                var geocoded = null;
                var markerMap = {};
                var highlightId = null;
                var isGridView = D.viewMode === 'grid';
                var activeAmenities = [];

                var map = L.map(mapEl, { scrollWheelZoom: true }).setView(CENTER, ZOOM);
                L.tileLayer(D.tileUrl, { attribution: D.tileAttr, maxZoom: 19 }).addTo(map);
                // Force recompute once layout is settled, avoids 400x200 default when
                // the grid assigns the panel size after Leaflet has already measured.
                setTimeout(function(){ try { map.invalidateSize(); } catch(e) {} }, 100);
                setTimeout(function(){ try { map.invalidateSize(); } catch(e) {} }, 500);
                if (window.ResizeObserver) {
                    try { new ResizeObserver(function(){ map.invalidateSize(); }).observe(mapEl); } catch(e) {}
                }

                function makeIcon() {
                    return L.divIcon({
                        className: 'plm-marker-icon',
                        html: D.markerSvg,
                        iconSize: [D.markerW, D.markerH],
                        iconAnchor: [Math.round(D.markerW / 2), D.markerAnchorY],
                        popupAnchor: [0, -D.markerAnchorY]
                    });
                }
                var icon = makeIcon();
                var clusterGroup = USE_CLUSTER ? L.markerClusterGroup({ maxClusterRadius: 50 }) : null;

                function escHtml(str) {
                    var d = document.createElement('div');
                    d.textContent = str == null ? '' : String(str);
                    return d.innerHTML;
                }

                function buildPopup(p) {
                    var html = '<div class="plm-popup"'
                        + ' style="max-width:' + (D.popupMaxWidth || 280) + 'px;'
                        + (IS_SVC ? 'background:' + D.popupBg + ';color:' + D.popupColor + ';border-radius:' + D.popupRadius + 'px;padding:8px;' : '')
                        + '">';
                    if (D.popupImage && p.image) {
                        html += '<img src="' + p.image + '" alt="' + escHtml(p.title) + '" loading="lazy" decoding="async" ';
                        if (D.popupImgHeight) html += 'style="height:' + D.popupImgHeight + 'px;" ';
                        html += '/>';
                    }
                    html += '<h4>' + escHtml(p.title) + '</h4>';
                    var sub = [];
                    if (IS_SVC) {
                        if (D.popupValley && p.valley) sub.push(escHtml(p.valley));
                        if (D.popupAltitude && p.altitude !== undefined) sub.push((+p.altitude).toLocaleString('it-IT') + ' m');
                        if (D.popupSpecs) {
                            if (p.bedrooms) sub.push(p.bedrooms + ' cam.');
                            if (p.bathrooms) sub.push(p.bathrooms + ' bagni');
                            if (p.sqm) sub.push(p.sqm + ' m\u00B2');
                        }
                    } else {
                        if (p.address) sub.push(escHtml(p.address));
                        if (D.popupExcerpt && p.excerpt) sub.push(escHtml(p.excerpt));
                    }
                    if (sub.length) html += '<div class="plm-popup-sub">' + sub.join(' &middot; ') + '</div>';
                    if (IS_SVC && D.popupPrice && p.price) {
                        html += '<div class="plm-popup-price" style="color:' + (D.popupBtnColor || COLOR) + '">&euro; ' + escHtml(p.price) + '</div>';
                    }
                    if ((IS_SVC ? D.popupLink : D.popupLink) && p.url) {
                        var btnText = IS_SVC ? (D.popupBtnText || 'Scopri') : 'Dettagli';
                        var btnBg = IS_SVC ? (D.popupBtnColor || COLOR) : COLOR;
                        html += '<a class="plm-popup-link" href="' + p.url + '" style="background:' + btnBg + '">' + escHtml(btnText) + '</a>';
                    }
                    html += '</div>';
                    return html;
                }

                ITEMS.forEach(function(p) {
                    var mk = L.marker([p.lat, p.lng], { icon: icon });
                    mk.bindPopup(buildPopup(p));
                    mk.on('click', function(){ highlightCard(p.id); scrollToCard(p.id); });
                    markerMap[p.id] = mk;
                    if (clusterGroup) clusterGroup.addLayer(mk);
                    else mk.addTo(map);
                });
                if (clusterGroup) map.addLayer(clusterGroup);

                function fitBoundsToVisible(items) {
                    if (!items.length || !D.fitBounds) return;
                    if (items.length === 1) { map.setView([items[0].lat, items[0].lng], 15); return; }
                    var b = items.map(function(p){ return [p.lat, p.lng]; });
                    map.fitBounds(b, { padding: [40, 40], maxZoom: 16 });
                }
                fitBoundsToVisible(ITEMS);

                function haversine(la1, ln1, la2, ln2) {
                    var R = 6371, dLat = (la2-la1)*Math.PI/180, dLng = (ln2-ln1)*Math.PI/180;
                    var a = Math.sin(dLat/2)*Math.sin(dLat/2) + Math.cos(la1*Math.PI/180)*Math.cos(la2*Math.PI/180)*Math.sin(dLng/2)*Math.sin(dLng/2);
                    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                }

                function getFilters() {
                    var f = {};
                    root.querySelectorAll('[data-filter]').forEach(function(el) {
                        var k = el.getAttribute('data-filter');
                        if (el.type === 'range') f[k] = parseFloat(el.value) || 0;
                        else f[k] = (el.value || '').trim();
                    });
                    return f;
                }

                function rangeTest(val, range) {
                    if (!range) return true;
                    var parts = range.split('-');
                    if (parts.length !== 2) return true;
                    var min = parseFloat(parts[0]), max = parseFloat(parts[1]);
                    var v = parseFloat(val);
                    return v >= min && v <= max;
                }

                function applyFilters() {
                    var f = getFilters();
                    var radius = f.radius || D.radiusDefault;

                    filtered = ITEMS.filter(function(p) {
                        if (geocoded && f.location && D.showRadius) {
                            if (haversine(geocoded.lat, geocoded.lng, p.lat, p.lng) > radius) return false;
                        }
                        if (f.taxonomy && (!p.terms || p.terms.indexOf(f.taxonomy) === -1)) return false;
                        if (IS_SVC) {
                            if (f.altitude && p.altitude !== undefined && !rangeTest(p.altitude, f.altitude)) return false;
                            if (f.valley && p.valley !== f.valley) return false;
                            if (f.guests && p.capacity !== undefined && !rangeTest(p.capacity, f.guests)) return false;
                            if (f.price && p.price !== undefined && !rangeTest(p.price, f.price)) return false;
                            if (f.bedrooms) {
                                if (f.bedrooms === '4+') { if (!(p.bedrooms >= 4)) return false; }
                                else if (parseInt(p.bedrooms, 10) !== parseInt(f.bedrooms, 10)) return false;
                            }
                            if (activeAmenities.length && p.amenities) {
                                for (var i = 0; i < activeAmenities.length; i++) {
                                    if (p.amenities.indexOf(activeAmenities[i]) === -1) return false;
                                }
                            } else if (activeAmenities.length && !p.amenities) return false;
                        }
                        return true;
                    });
                    applySort();
                    page = 1;
                    updateMap();
                    renderList();
                    updateCount();
                    updatePagination();
                }

                function applySort() {
                    var s = sortEl ? sortEl.value : (D.sortDefault || 'default');
                    if (s === 'title_asc') filtered.sort(function(a,b){ return (a.title||'').localeCompare(b.title||''); });
                    else if (s === 'title_desc') filtered.sort(function(a,b){ return (b.title||'').localeCompare(a.title||''); });
                    else if (s === 'newest') filtered.sort(function(a,b){ return (b.id||0) - (a.id||0); });
                    else if (s === 'distance') {
                        var ref = geocoded || { lat: CENTER[0], lng: CENTER[1] };
                        filtered.sort(function(a,b){ return haversine(ref.lat, ref.lng, a.lat, a.lng) - haversine(ref.lat, ref.lng, b.lat, b.lng); });
                    }
                }

                function updateMap() {
                    var vis = {};
                    filtered.forEach(function(p){ vis[p.id] = true; });
                    if (clusterGroup) {
                        clusterGroup.clearLayers();
                        ITEMS.forEach(function(p){ if (vis[p.id]) clusterGroup.addLayer(markerMap[p.id]); });
                    } else {
                        ITEMS.forEach(function(p){
                            var m = markerMap[p.id];
                            if (vis[p.id]) { if (!map.hasLayer(m)) m.addTo(map); }
                            else { if (map.hasLayer(m)) map.removeLayer(m); }
                        });
                    }
                    fitBoundsToVisible(filtered);
                }

                function renderList() {
                    if (!listEl) return;
                    var start = (page - 1) * PER_PAGE, end = start + PER_PAGE;
                    var items = filtered.slice(start, end);
                    if (!items.length) {
                        listEl.innerHTML = '<div class="plm-no-results">'
                          + '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>'
                          + '<div class="plm-no-results-title">' + (D.i18n && D.i18n.noResults || 'Nessun risultato trovato') + '</div>'
                          + '<div style="font-size:13px">' + (D.i18n && D.i18n.tryFilters || 'Prova a modificare i filtri') + '</div></div>';
                        return;
                    }
                    var html = '';
                    items.forEach(function(p) {
                        html += '<a href="' + (p.url || '#') + '" class="plm-card' + (highlightId === p.id ? ' is-highlighted' : '') + '" data-prop-id="' + escHtml(p.id) + '">';
                        html += '<div class="plm-card-img">';
                        if (p.image) html += '<img src="' + p.image + '" alt="' + escHtml(p.title) + '" loading="lazy" decoding="async" />';
                        else html += '<div class="plm-card-img-ph" aria-hidden="true">&#127968;</div>';
                        html += '</div><div class="plm-card-body">';
                        html += '<h4 class="plm-card-title">' + escHtml(p.title) + '</h4>';
                        var sub = [];
                        if (p.valley) sub.push(escHtml(p.valley));
                        else if (p.address) sub.push(escHtml(p.address));
                        if (p.altitude !== undefined) sub.push((+p.altitude).toLocaleString('it-IT') + ' m');
                        if (sub.length) html += '<p class="plm-card-sub">' + sub.join(' &middot; ') + '</p>';
                        if (IS_SVC && D.popupSpecs) {
                            var specs = [];
                            if (p.bedrooms) specs.push('<span>' + p.bedrooms + ' ' + (D.i18n && D.i18n.cam || 'cam.') + '</span>');
                            if (p.bathrooms) specs.push('<span>' + p.bathrooms + ' ' + (D.i18n && D.i18n.bagni || 'bagni') + '</span>');
                            if (p.sqm) specs.push('<span>' + p.sqm + ' m\u00B2</span>');
                            if (specs.length) html += '<div class="plm-card-specs">' + specs.join('') + '</div>';
                        }
                        var priceVal = p.price || p.price_night;
                        if (priceVal) html += '<div class="plm-card-price">&euro; ' + escHtml(priceVal) + (IS_SVC ? (' / ' + (D.i18n && D.i18n.notte || 'notte')) : '') + '</div>';
                        html += '</div></a>';
                    });
                    listEl.innerHTML = html;

                    listEl.querySelectorAll('.plm-card').forEach(function(card) {
                        card.addEventListener('click', function(e) {
                            e.preventDefault();
                            var id = card.getAttribute('data-prop-id');
                            var numId = parseInt(id, 10);
                            var mk = markerMap[numId] != null ? markerMap[numId] : markerMap[id];
                            if (mk) {
                                map.panTo(mk.getLatLng(), { animate: true, duration: 0.3 });
                                map.setZoom(Math.max(map.getZoom(), 15));
                                mk.openPopup();
                                highlightCard(numId);
                            }
                            var link = card.getAttribute('href');
                            if (link && link !== '#') setTimeout(function(){ window.location.href = link; }, 600);
                        });
                    });
                }

                function updateCount() {
                    if (!countEl) return;
                    var n = filtered.length;
                    countEl.innerHTML = '<strong>' + n + '</strong> Risultat' + (n === 1 ? 'o' : 'i');
                }

                function updatePagination() {
                    if (!pagEl) return;
                    var total = Math.max(1, Math.ceil(filtered.length / PER_PAGE));
                    var prev = pagEl.querySelector('[data-page="prev"]');
                    var next = pagEl.querySelector('[data-page="next"]');
                    if (prev) prev.disabled = page <= 1;
                    if (next) next.disabled = page >= total;
                    if (pageInfoEl) pageInfoEl.textContent = page + ' / ' + total;
                }

                function highlightCard(id) {
                    highlightId = id;
                    if (!listEl) return;
                    listEl.querySelectorAll('.plm-card').forEach(function(c) {
                        c.classList.toggle('is-highlighted', String(c.getAttribute('data-prop-id')) === String(id));
                    });
                }
                function scrollToCard(id) {
                    for (var i = 0; i < filtered.length; i++) {
                        if (String(filtered[i].id) === String(id)) {
                            var tp = Math.floor(i / PER_PAGE) + 1;
                            if (tp !== page) { page = tp; renderList(); updatePagination(); }
                            break;
                        }
                    }
                    setTimeout(function(){
                        if (!listEl) return;
                        var c = listEl.querySelector('[data-prop-id="' + id + '"]');
                        if (c) { c.scrollIntoView({ behavior:'smooth', block:'center' }); highlightCard(id); }
                    }, 100);
                }

                /* Geocoding */
                function geocode(q, cb) {
                    if (!q) { geocoded = null; cb(); return; }
                    fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(q))
                        .then(function(r){ return r.json(); })
                        .then(function(data){
                            if (data && data.length) geocoded = { lat: parseFloat(data[0].lat), lng: parseFloat(data[0].lon) };
                            else geocoded = null;
                            cb();
                        })
                        .catch(function(){ geocoded = null; cb(); });
                }

                /* Autocomplete */
                var acList = document.getElementById(UID + '-ac');
                var locInput = root.querySelector('[data-filter="location"]');
                var acTimer, acIdx = -1;
                function acShow(items) {
                    if (!acList) return;
                    if (!items.length) { acHide(); return; }
                    acList.innerHTML = items.map(function(it){
                        return '<li class="plm-ac-item" data-lat="' + it.lat + '" data-lng="' + it.lon + '">' + escHtml(it.display_name) + '</li>';
                    }).join('');
                    acList.classList.add('is-open'); acIdx = -1;
                }
                function acHide() { if (acList) { acList.classList.remove('is-open'); acList.innerHTML = ''; acIdx = -1; } }
                function acSearch(q) {
                    if (!q || q.length < 2) { acHide(); return; }
                    fetch('https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=5&q=' + encodeURIComponent(q))
                        .then(function(r){ return r.json(); })
                        .then(function(data){ acShow(data || []); })
                        .catch(function(){ acHide(); });
                }
                if (locInput) {
                    locInput.addEventListener('input', function(){
                        clearTimeout(acTimer);
                        var v = locInput.value.trim();
                        acTimer = setTimeout(function(){ acSearch(v); }, 300);
                    });
                    locInput.addEventListener('keydown', function(e) {
                        if (!acList || !acList.classList.contains('is-open')) return;
                        var items = acList.querySelectorAll('.plm-ac-item');
                        if (!items.length) return;
                        if (e.key === 'ArrowDown') { e.preventDefault(); acIdx = Math.min(acIdx+1, items.length-1); items.forEach(function(it,i){ it.classList.toggle('is-active', i === acIdx); }); }
                        else if (e.key === 'ArrowUp') { e.preventDefault(); acIdx = Math.max(acIdx-1, 0); items.forEach(function(it,i){ it.classList.toggle('is-active', i === acIdx); }); }
                        else if (e.key === 'Enter') { e.preventDefault(); if (acIdx >= 0 && items[acIdx]) items[acIdx].click(); }
                        else if (e.key === 'Escape') { acHide(); }
                    });
                    if (acList) {
                        acList.addEventListener('click', function(e){
                            var it = e.target.closest('.plm-ac-item');
                            if (!it) return;
                            var lat = parseFloat(it.getAttribute('data-lat'));
                            var lng = parseFloat(it.getAttribute('data-lng'));
                            geocoded = { lat: lat, lng: lng };
                            locInput.value = it.textContent || '';
                            acHide();
                            try { map.setView([lat, lng], 13); } catch(e){}
                            applyFilters();
                        });
                    }
                    document.addEventListener('click', function(e) {
                        if (!locInput.contains(e.target) && (!acList || !acList.contains(e.target))) acHide();
                    });
                }

                /* Search button */
                if (searchBtn) {
                    searchBtn.addEventListener('click', function(){
                        var v = locInput ? locInput.value.trim() : '';
                        if (v) {
                            searchBtn.disabled = true;
                            var orig = searchBtn.textContent;
                            searchBtn.textContent = 'Ricerca...';
                            geocode(v, function(){
                                searchBtn.disabled = false;
                                searchBtn.textContent = orig;
                                applyFilters();
                            });
                        } else {
                            geocoded = null; applyFilters();
                        }
                    });
                }

                /* Reset */
                if (resetBtn) {
                    resetBtn.addEventListener('click', function(){
                        root.querySelectorAll('[data-filter]').forEach(function(el) {
                            if (el.type === 'range') {
                                el.value = D.radiusDefault;
                                var w = el.closest('.plm-radius-wrap');
                                if (w) { var s = w.querySelector('.plm-radius-val'); if (s) s.textContent = D.radiusDefault + ' km'; }
                            } else el.value = '';
                        });
                        root.querySelectorAll('.plm-amenity-pill.is-active').forEach(function(p){ p.classList.remove('is-active'); });
                        activeAmenities = [];
                        geocoded = null;
                        if (sortEl) sortEl.value = D.sortDefault || 'default';
                        filtered = ITEMS.slice();
                        page = 1;
                        updateMap(); renderList(); updateCount(); updatePagination();
                    });
                }

                /* Sort */
                if (sortEl) {
                    sortEl.addEventListener('change', function(){ applySort(); page = 1; renderList(); updatePagination(); });
                }

                /* Radius slider label update */
                var radiusInput = root.querySelector('[data-filter="radius"]');
                if (radiusInput) {
                    radiusInput.addEventListener('input', function(){
                        var w = radiusInput.closest('.plm-radius-wrap');
                        if (w) { var s = w.querySelector('.plm-radius-val'); if (s) s.textContent = radiusInput.value + ' km'; }
                    });
                }

                /* Amenity toggles */
                root.querySelectorAll('.plm-amenity-pill').forEach(function(pill) {
                    pill.addEventListener('click', function(){
                        var slug = pill.getAttribute('data-amenity');
                        var idx = activeAmenities.indexOf(slug);
                        if (idx >= 0) { activeAmenities.splice(idx, 1); pill.classList.remove('is-active'); }
                        else { activeAmenities.push(slug); pill.classList.add('is-active'); }
                    });
                });

                /* Taxonomy/filter change → live apply (select) */
                root.querySelectorAll('.plm-filter-select').forEach(function(sel){
                    sel.addEventListener('change', function(){ applyFilters(); });
                });

                /* Pagination */
                if (pagEl) {
                    pagEl.addEventListener('click', function(e) {
                        var btn = e.target.closest('[data-page]');
                        if (!btn || btn.disabled) return;
                        var total = Math.max(1, Math.ceil(filtered.length / PER_PAGE));
                        if (btn.getAttribute('data-page') === 'prev' && page > 1) page--;
                        else if (btn.getAttribute('data-page') === 'next' && page < total) page++;
                        renderList(); updatePagination();
                        if (listEl) listEl.scrollTop = 0;
                    });
                }

                /* View toggle */
                root.querySelectorAll('.plm-view-btn').forEach(function(btn) {
                    btn.addEventListener('click', function(){
                        root.querySelectorAll('.plm-view-btn').forEach(function(b){ b.classList.remove('is-active'); });
                        btn.classList.add('is-active');
                        isGridView = btn.getAttribute('data-view') === 'grid';
                        if (listEl) listEl.classList.toggle('plm-grid-view', isGridView);
                    });
                });

                /* Fullscreen */
                if (fsBtn) {
                    fsBtn.addEventListener('click', function(){
                        if (!document.fullscreenElement) {
                            if (root.requestFullscreen) root.requestFullscreen();
                            else if (root.webkitRequestFullscreen) root.webkitRequestFullscreen();
                        } else {
                            if (document.exitFullscreen) document.exitFullscreen();
                            else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
                        }
                    });
                    document.addEventListener('fullscreenchange', function(){ setTimeout(function(){ try{ map.invalidateSize(); } catch(e){} }, 200); });
                }

                /* Initial render */
                applyFilters();
            }
        })();
        </script>
        <?php
        $raw = ob_get_clean();

        // WordPress filters (wpautop / wptexturize / wp_kses or similar) can convert
        // `&` into `&#038;` inside the rendered tile output, breaking the inline JS
        // (e.g. `if (a && b)` becomes `if (a &#038;&#038; b)` — SyntaxError).
        // Extract the script body and wrap it with a base64 bootstrap so no character
        // passes through any HTML-encoding filter.
        if ( preg_match( '/<script>([\s\S]*?)<\/script>/', $raw, $m ) ) {
            $encoded = base64_encode( $m[1] );
            return '<script>(new Function(atob("' . $encoded . '")))();</script>';
        }

        return $raw;
    }

    /**
     * Common CSS block for fullscreen button + new enhancements, scoped to $uid.
     */
    private function build_common_enhance_css( $uid, $color, $card_radius = 8, $card_max_h = 0, $grid_cols = 2 ) {
        $c = $this->safe_hex( $color, '#e1474f' );
        $uid_sel = '.' . $uid;
        ob_start();
        ?>
        <?php echo $uid_sel; ?> { position: relative; }
        <?php echo $uid_sel; ?> .olo-map-fs-btn {
            position: absolute; top: 10px; right: 10px; z-index: 1000;
            width: 34px; height: 34px;
            background: rgba(255,255,255,0.95);
            border: 1px solid rgba(0,0,0,0.15);
            border-radius: 6px; cursor: pointer;
            padding: 6px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.15);
            display: flex; align-items: center; justify-content: center;
            color: #374151;
            transition: background 0.15s;
        }
        <?php echo $uid_sel; ?> .olo-map-fs-btn:hover { background: #fff; }
        <?php echo $uid_sel; ?>:fullscreen .olo-map-canvas,
        <?php echo $uid_sel; ?>:-webkit-full-screen .olo-map-canvas { height: 100vh !important; border-radius: 0 !important; }
        <?php echo $this->build_cluster_css( $uid, $c ); ?>
        <?php echo $uid_sel; ?> .leaflet-popup-content img { max-width: 100%; height: auto; }

        /* ── Multi-marker UI (locations/services) ── */
        <?php echo $uid_sel; ?> .olo-map-toolbar {
            display: flex; align-items: center; justify-content: space-between;
            gap: 8px; padding: 8px 10px; background: #fff;
            border: 1px solid #E5E7EB; border-radius: 6px;
            margin-bottom: 8px; flex-wrap: wrap;
        }
        <?php echo $uid_sel; ?> .olo-map-toolbar .olo-map-count { font-size: 13px; color: #374151; font-weight: 600; }
        <?php echo $uid_sel; ?> .olo-map-toolbar .olo-map-count strong { color: <?php echo $c; ?>; }
        <?php echo $uid_sel; ?> .olo-map-toolbar-right { display: flex; gap: 8px; align-items: center; }
        <?php echo $uid_sel; ?> .olo-map-sort {
            padding: 5px 8px; border: 1px solid #D1D5DB; border-radius: 4px;
            font-size: 12px; color: #374151; background: #fff; font-family: inherit;
        }
        <?php echo $uid_sel; ?> .olo-map-view-toggles { display: flex; gap: 0; }
        <?php echo $uid_sel; ?> .olo-map-view-btn {
            width: 30px; height: 30px; border: 1px solid #D1D5DB; background: #fff;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            padding: 0; color: #9CA3AF;
        }
        <?php echo $uid_sel; ?> .olo-map-view-btn:first-child { border-radius: 4px 0 0 4px; }
        <?php echo $uid_sel; ?> .olo-map-view-btn:last-child  { border-radius: 0 4px 4px 0; border-left: 0; }
        <?php echo $uid_sel; ?> .olo-map-view-btn.is-active {
            background: <?php echo $c; ?>; border-color: <?php echo $c; ?>; color: #fff;
        }

        /* Location search autocomplete */
        <?php echo $uid_sel; ?> .olo-map-search-wrap { position: relative; margin-bottom: 8px; }
        <?php echo $uid_sel; ?> .olo-map-search-input {
            width: 100%; padding: 8px 12px; border: 1px solid #D1D5DB;
            border-radius: 6px; font-size: 13px; box-sizing: border-box; font-family: inherit;
        }
        <?php echo $uid_sel; ?> .olo-map-search-input:focus {
            outline: none; border-color: <?php echo $c; ?>;
            box-shadow: 0 0 0 3px <?php echo $c; ?>22;
        }
        <?php echo $uid_sel; ?> .olo-map-ac-list {
            display: none; position: absolute; z-index: 1100; top: 100%; left: 0; right: 0;
            margin: 2px 0 0; padding: 4px 0; list-style: none;
            background: #fff; border: 1px solid #D1D5DB; border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.12); max-height: 220px; overflow-y: auto;
        }
        <?php echo $uid_sel; ?> .olo-map-ac-list.is-open { display: block; }
        <?php echo $uid_sel; ?> .olo-map-ac-item {
            padding: 8px 12px; font-size: 13px; color: #374151; cursor: pointer;
            line-height: 1.35;
        }
        <?php echo $uid_sel; ?> .olo-map-ac-item:hover,
        <?php echo $uid_sel; ?> .olo-map-ac-item.is-active { background: #F3F4F6; }

        /* Radius slider */
        <?php echo $uid_sel; ?> .olo-map-radius-wrap {
            display: flex; align-items: center; gap: 8px; margin-bottom: 8px;
            padding: 6px 10px; background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 6px;
        }
        <?php echo $uid_sel; ?> .olo-map-radius-wrap label {
            font-size: 11px; font-weight: 600; color: #6B7280;
            text-transform: uppercase; letter-spacing: 0.04em;
        }
        <?php echo $uid_sel; ?> .olo-map-radius-wrap input[type="range"] {
            flex: 1; accent-color: <?php echo $c; ?>; height: 4px;
        }
        <?php echo $uid_sel; ?> .olo-map-radius-val {
            font-size: 12px; font-weight: 600; color: #374151; min-width: 52px; text-align: right;
        }

        /* Results list */
        <?php echo $uid_sel; ?> .olo-map-results {
            margin-top: 10px; display: flex; flex-direction: column; gap: 8px;
        }
        <?php echo $uid_sel; ?> .olo-map-results.is-grid {
            display: grid; gap: 10px;
            grid-template-columns: repeat(<?php echo absint( $grid_cols ) ?: 2; ?>, 1fr);
        }
        <?php echo $uid_sel; ?> .olo-map-card {
            display: grid; grid-template-columns: 38% 1fr;
            border: 1px solid #E5E7EB; background: #fff;
            border-radius: <?php echo absint( $card_radius ); ?>px;
            overflow: hidden;
            <?php if ( absint( $card_max_h ) > 0 ) : ?>max-height: <?php echo absint( $card_max_h ); ?>px;<?php endif; ?>
            cursor: pointer; text-decoration: none; color: inherit;
            transition: box-shadow 0.2s, transform 0.15s;
        }
        <?php echo $uid_sel; ?> .olo-map-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); transform: translateY(-1px);
        }
        <?php echo $uid_sel; ?> .olo-map-card.is-highlighted {
            box-shadow: 0 0 0 2px <?php echo $c; ?>, 0 4px 12px rgba(0,0,0,0.1);
        }
        <?php echo $uid_sel; ?> .olo-map-card-img {
            background: #E5E7EB; min-height: 110px; position: relative; overflow: hidden;
        }
        <?php echo $uid_sel; ?> .olo-map-card-img img {
            width: 100%; height: 100%; object-fit: cover; display: block;
        }
        <?php echo $uid_sel; ?> .olo-map-card-body {
            padding: 10px 12px; display: flex; flex-direction: column; gap: 4px; min-width: 0;
        }
        <?php echo $uid_sel; ?> .olo-map-card-title {
            font-size: 13px; font-weight: 700; color: #1F2937; margin: 0; line-height: 1.3;
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }
        <?php echo $uid_sel; ?> .olo-map-card-sub { font-size: 12px; color: #6B7280; margin: 0; }
        <?php echo $uid_sel; ?> .olo-map-card-price {
            font-size: 14px; font-weight: 700; color: <?php echo $c; ?>; margin-top: auto;
        }

        /* Grid mode: image background + gradient overlay + white text */
        <?php echo $uid_sel; ?> .olo-map-results.is-grid .olo-map-card {
            display: flex; flex-direction: column; position: relative; min-height: 200px;
        }
        <?php echo $uid_sel; ?> .olo-map-results.is-grid .olo-map-card-img {
            position: absolute; inset: 0; height: 100%;
        }
        <?php echo $uid_sel; ?> .olo-map-results.is-grid .olo-map-card-body {
            position: relative; z-index: 2; margin-top: auto;
            background: linear-gradient(0deg, rgba(0,0,0,0.78) 0%, rgba(0,0,0,0.45) 70%, transparent 100%);
            padding: 40px 12px 10px;
        }
        <?php echo $uid_sel; ?> .olo-map-results.is-grid .olo-map-card-title { color: #fff; }
        <?php echo $uid_sel; ?> .olo-map-results.is-grid .olo-map-card-sub { color: rgba(255,255,255,0.85); }
        <?php echo $uid_sel; ?> .olo-map-results.is-grid .olo-map-card-price { color: #fff; }

        /* Pagination */
        <?php echo $uid_sel; ?> .olo-map-pagination {
            display: flex; justify-content: center; align-items: center;
            gap: 8px; padding: 10px; margin-top: 10px;
            background: #fff; border: 1px solid #E5E7EB; border-radius: 6px;
        }
        <?php echo $uid_sel; ?> .olo-map-page-btn {
            padding: 6px 14px; border: 1px solid #D1D5DB; border-radius: 4px;
            background: #fff; color: #374151; font-size: 13px; cursor: pointer;
            font-family: inherit;
        }
        <?php echo $uid_sel; ?> .olo-map-page-btn:hover { background: #F3F4F6; }
        <?php echo $uid_sel; ?> .olo-map-page-btn:disabled { opacity: 0.4; cursor: default; }
        <?php echo $uid_sel; ?> .olo-map-page-info { font-size: 12px; color: #6B7280; }

        @media (max-width: 600px) {
            <?php echo $uid_sel; ?> .olo-map-results.is-grid { grid-template-columns: 1fr; }
            <?php echo $uid_sel; ?> .olo-map-card { grid-template-columns: 1fr; }
        }
        <?php
        return ob_get_clean();
    }

    /**
     * Build the shared JS that enhances an already-initialized map (via external olo-map.js).
     * Adds: fullscreen, cluster color (handled via CSS), view toggle, sort, pagination,
     * location search (Nominatim), radius filter, bidirectional marker↔card sync.
     * The script hooks after the canvas has been initialized by olo-map.js.
     */
    private function build_multi_enhance_js( $uid, $map_id, $config ) {
        $cfg_json = wp_json_encode( $config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        ob_start();
        ?>
        <script>
        (function(){
            var CFG = <?php echo $cfg_json; ?>;
            var UID = CFG.uid, MAP_ID = CFG.mapId;
            var PER_PAGE = parseInt(CFG.perPage, 10) || 0; // 0 = all
            var CAN_SEARCH = !!CFG.searchEnabled;
            var CAN_RADIUS = !!CFG.radiusEnabled;
            var RADIUS_DEFAULT = parseFloat(CFG.radiusDefault) || 5;

            function init() {
                var canvas = document.getElementById(MAP_ID);
                var root   = document.querySelector('.' + UID);
                if (!canvas || !root) return;
                // Wait until olo-map.js has built the map & markers.
                if (!canvas._oloMap || !canvas._oloMarkers) {
                    return setTimeout(init, 100);
                }

                var map     = canvas._oloMap;
                var markers = canvas._oloMarkers;
                var locs    = (canvas._oloConfig && canvas._oloConfig.locations) || [];

                /* state */
                var filtered    = locs.slice();
                var page        = 1;
                var geocoded    = null;
                var radiusKm    = RADIUS_DEFAULT;
                var isGridView  = (CFG.viewMode === 'grid');
                var highlightId = null;

                /* ── Haversine ── */
                function haversine(lat1, lng1, lat2, lng2) {
                    var R = 6371;
                    var dLat = (lat2 - lat1) * Math.PI / 180;
                    var dLng = (lng2 - lng1) * Math.PI / 180;
                    var a = Math.sin(dLat/2)*Math.sin(dLat/2)
                          + Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)
                          * Math.sin(dLng/2)*Math.sin(dLng/2);
                    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                }

                function escHtml(str) {
                    var d = document.createElement('div');
                    d.textContent = str == null ? '' : String(str);
                    return d.innerHTML;
                }

                /* ── Fullscreen ── */
                var fsBtn = root.querySelector('.olo-map-fs-btn[data-fs-for="' + MAP_ID + '"]');
                if (fsBtn) {
                    fsBtn.addEventListener('click', function() {
                        var el = root;
                        if (!document.fullscreenElement) {
                            if (el.requestFullscreen) el.requestFullscreen();
                            else if (el.webkitRequestFullscreen) el.webkitRequestFullscreen();
                        } else {
                            if (document.exitFullscreen) document.exitFullscreen();
                            else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
                        }
                    });
                    document.addEventListener('fullscreenchange', function() {
                        setTimeout(function(){ try { map.invalidateSize(); } catch(e) {} }, 200);
                    });
                }

                /* ── Marker↔Card sync ── */
                var markerById = {};
                markers.forEach(function(m) {
                    if (m._oloId != null) markerById[m._oloId] = m;
                    m.on('click', function() {
                        highlight(m._oloId);
                        scrollToCard(m._oloId);
                    });
                });

                /* ── Location search autocomplete ── */
                var searchInput = root.querySelector('.olo-map-search-input[data-search-for="' + MAP_ID + '"]');
                var acList      = root.querySelector('.olo-map-ac-list[data-ac-for="' + MAP_ID + '"]');
                var acTimer = null, acIdx = -1;

                function acShow(items) {
                    if (!acList) return;
                    if (!items.length) { acHide(); return; }
                    acList.innerHTML = items.map(function(it) {
                        return '<li class="olo-map-ac-item" data-lat="' + it.lat + '" data-lng="' + it.lon + '">' + escHtml(it.display_name) + '</li>';
                    }).join('');
                    acList.classList.add('is-open');
                    acIdx = -1;
                }
                function acHide() {
                    if (!acList) return;
                    acList.classList.remove('is-open');
                    acList.innerHTML = '';
                    acIdx = -1;
                }
                function acSearch(q) {
                    if (!q || q.length < 2) { acHide(); return; }
                    var url = 'https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=5&q=' + encodeURIComponent(q);
                    fetch(url).then(function(r){ return r.json(); })
                              .then(function(data){ acShow(data || []); })
                              .catch(function(){ acHide(); });
                }
                if (searchInput && CAN_SEARCH) {
                    searchInput.addEventListener('input', function() {
                        clearTimeout(acTimer);
                        var v = searchInput.value.trim();
                        acTimer = setTimeout(function(){ acSearch(v); }, 300);
                    });
                    searchInput.addEventListener('keydown', function(e) {
                        if (!acList || !acList.classList.contains('is-open')) return;
                        var items = acList.querySelectorAll('.olo-map-ac-item');
                        if (!items.length) return;
                        if (e.key === 'ArrowDown') { e.preventDefault(); acIdx = Math.min(acIdx+1, items.length-1); items.forEach(function(it,i){ it.classList.toggle('is-active', i===acIdx); }); }
                        else if (e.key === 'ArrowUp') { e.preventDefault(); acIdx = Math.max(acIdx-1, 0); items.forEach(function(it,i){ it.classList.toggle('is-active', i===acIdx); }); }
                        else if (e.key === 'Enter') { e.preventDefault(); if (acIdx >= 0 && items[acIdx]) items[acIdx].click(); }
                        else if (e.key === 'Escape') { acHide(); }
                    });
                    if (acList) {
                        acList.addEventListener('click', function(e) {
                            var it = e.target.closest('.olo-map-ac-item');
                            if (!it) return;
                            var lat = parseFloat(it.getAttribute('data-lat'));
                            var lng = parseFloat(it.getAttribute('data-lng'));
                            geocoded = { lat: lat, lng: lng };
                            var lbl = it.textContent || '';
                            searchInput.value = lbl;
                            acHide();
                            try { map.setView([lat, lng], 13); } catch(e) {}
                            applyFilters();
                        });
                    }
                    document.addEventListener('click', function(e) {
                        if (!searchInput.contains(e.target) && (!acList || !acList.contains(e.target))) acHide();
                    });
                }

                /* ── Radius slider ── */
                var radiusInput = root.querySelector('.olo-map-radius-slider[data-radius-for="' + MAP_ID + '"]');
                var radiusVal   = root.querySelector('.olo-map-radius-val[data-radius-for="' + MAP_ID + '"]');
                if (radiusInput && CAN_RADIUS) {
                    radiusInput.addEventListener('input', function() {
                        radiusKm = parseFloat(radiusInput.value) || RADIUS_DEFAULT;
                        if (radiusVal) radiusVal.textContent = radiusKm + ' km';
                        applyFilters();
                    });
                }

                /* ── Filter logic (distance-only client-side; taxonomy/range filters already handled by olo-map.js) ── */
                function applyFilters() {
                    filtered = locs.filter(function(p) {
                        if (geocoded && CAN_RADIUS) {
                            var d = haversine(geocoded.lat, geocoded.lng, p.lat, p.lng);
                            if (d > radiusKm) return false;
                        } else if (geocoded && CAN_SEARCH && !CAN_RADIUS) {
                            // search without radius: no distance cap, just re-sort by distance if applicable
                        }
                        return true;
                    });
                    applySort();
                    page = 1;
                    updateMapVisibility();
                    renderList();
                    updatePagination();
                    updateCount();
                }

                /* ── Sort ── */
                var sortEl = root.querySelector('.olo-map-sort[data-sort-for="' + MAP_ID + '"]');
                function applySort() {
                    var mode = sortEl ? sortEl.value : (CFG.sortDefault || 'default');
                    if (mode === 'title_asc')  filtered.sort(function(a,b){ return (a.title||'').localeCompare(b.title||''); });
                    else if (mode === 'title_desc') filtered.sort(function(a,b){ return (b.title||'').localeCompare(a.title||''); });
                    else if (mode === 'newest') filtered.sort(function(a,b){ return (b.id||0) - (a.id||0); });
                    else if (mode === 'distance') {
                        var ref = geocoded || { lat: map.getCenter().lat, lng: map.getCenter().lng };
                        filtered.sort(function(a,b){ return haversine(ref.lat, ref.lng, a.lat, a.lng) - haversine(ref.lat, ref.lng, b.lat, b.lng); });
                    }
                }
                if (sortEl) {
                    sortEl.addEventListener('change', function() {
                        applySort(); page = 1; renderList(); updatePagination();
                    });
                }

                /* ── Map visibility update based on distance filter ── */
                function updateMapVisibility() {
                    if (!geocoded || !CAN_RADIUS) return; // only distance filter uses this path
                    var visibleIds = {};
                    filtered.forEach(function(p) { visibleIds[p.id] = true; });
                    var group = canvas._oloGroup;
                    if (group && typeof group.clearLayers === 'function') {
                        group.clearLayers();
                        markers.forEach(function(m){
                            if (visibleIds[m._oloId]) group.addLayer(m);
                        });
                    }
                }

                /* ── Rendering the results list ── */
                var listEl = root.querySelector('.olo-map-results[data-list-for="' + MAP_ID + '"]');
                var pagEl  = root.querySelector('.olo-map-pagination[data-pag-for="' + MAP_ID + '"]');
                var countEl = root.querySelector('.olo-map-count[data-count-for="' + MAP_ID + '"]');

                function renderList() {
                    if (!listEl) return;
                    var items;
                    if (PER_PAGE > 0) {
                        var start = (page - 1) * PER_PAGE;
                        items = filtered.slice(start, start + PER_PAGE);
                    } else {
                        items = filtered;
                    }
                    if (!items.length) {
                        listEl.innerHTML = '<div style="padding:16px;text-align:center;color:#9CA3AF;font-size:13px">Nessun risultato</div>';
                        return;
                    }
                    listEl.classList.toggle('is-grid', isGridView);
                    var html = items.map(function(p) {
                        var h = '<a href="' + (p.url || '#') + '" class="olo-map-card' + (highlightId === p.id ? ' is-highlighted' : '') + '" data-result-id="' + escHtml(p.id) + '">';
                        h += '<div class="olo-map-card-img">';
                        if (p.image) h += '<img src="' + p.image + '" alt="' + escHtml(p.title) + '" loading="lazy" decoding="async" />';
                        h += '</div>';
                        h += '<div class="olo-map-card-body">';
                        h += '<h4 class="olo-map-card-title">' + escHtml(p.title) + '</h4>';
                        var sub = [];
                        if (p.valley)   sub.push(escHtml(p.valley));
                        if (p.address)  sub.push(escHtml(p.address));
                        if (p.altitude !== undefined) sub.push((+p.altitude).toLocaleString('it-IT') + ' m');
                        if (sub.length) h += '<p class="olo-map-card-sub">' + sub.join(' &middot; ') + '</p>';
                        if (p.price)    h += '<div class="olo-map-card-price">&euro; ' + escHtml(p.price) + '</div>';
                        else if (p.price_night) h += '<div class="olo-map-card-price">&euro; ' + escHtml(p.price_night) + '</div>';
                        h += '</div></a>';
                        return h;
                    }).join('');
                    listEl.innerHTML = html;

                    listEl.querySelectorAll('.olo-map-card').forEach(function(card) {
                        card.addEventListener('click', function(e) {
                            var id = card.getAttribute('data-result-id');
                            var intId = parseInt(id, 10);
                            var mk = markerById[intId] != null ? markerById[intId] : markerById[id];
                            if (mk) {
                                e.preventDefault();
                                map.panTo(mk.getLatLng(), { animate: true, duration: 0.3 });
                                map.setZoom(Math.max(map.getZoom(), 14));
                                if (mk.openPopup) mk.openPopup();
                                highlight(intId);
                                if (card.href && card.getAttribute('href') !== '#') {
                                    setTimeout(function(){ window.location.href = card.href; }, 600);
                                }
                            }
                        });
                    });
                }

                function updatePagination() {
                    if (!pagEl) return;
                    if (PER_PAGE <= 0) { pagEl.style.display = 'none'; return; }
                    pagEl.style.display = '';
                    var totalPages = Math.max(1, Math.ceil(filtered.length / PER_PAGE));
                    var prev = pagEl.querySelector('[data-page="prev"]');
                    var next = pagEl.querySelector('[data-page="next"]');
                    var info = pagEl.querySelector('.olo-map-page-info');
                    if (prev) prev.disabled = page <= 1;
                    if (next) next.disabled = page >= totalPages;
                    if (info) info.textContent = page + ' di ' + totalPages;
                }

                function updateCount() {
                    if (countEl) countEl.innerHTML = '<strong>' + filtered.length + '</strong> risultat' + (filtered.length === 1 ? 'o' : 'i');
                }

                function highlight(id) {
                    highlightId = id;
                    if (!listEl) return;
                    listEl.querySelectorAll('.olo-map-card').forEach(function(c) {
                        var cid = c.getAttribute('data-result-id');
                        c.classList.toggle('is-highlighted', String(cid) === String(id));
                    });
                }

                function scrollToCard(id) {
                    if (!listEl || PER_PAGE <= 0) {
                        if (!listEl) return;
                        var card = listEl.querySelector('[data-result-id="' + id + '"]');
                        if (card) card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return;
                    }
                    for (var i = 0; i < filtered.length; i++) {
                        if (String(filtered[i].id) === String(id)) {
                            var targetPage = Math.floor(i / PER_PAGE) + 1;
                            if (targetPage !== page) { page = targetPage; renderList(); updatePagination(); }
                            break;
                        }
                    }
                    setTimeout(function(){
                        if (!listEl) return;
                        var card = listEl.querySelector('[data-result-id="' + id + '"]');
                        if (card) card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 100);
                }

                /* ── Pagination click ── */
                if (pagEl) {
                    pagEl.addEventListener('click', function(e) {
                        var btn = e.target.closest('[data-page]');
                        if (!btn || btn.disabled) return;
                        var tp = PER_PAGE > 0 ? Math.max(1, Math.ceil(filtered.length / PER_PAGE)) : 1;
                        if (btn.getAttribute('data-page') === 'prev' && page > 1) page--;
                        else if (btn.getAttribute('data-page') === 'next' && page < tp) page++;
                        renderList(); updatePagination();
                        if (listEl) listEl.scrollTop = 0;
                    });
                }

                /* ── View toggle ── */
                root.querySelectorAll('.olo-map-view-btn[data-view-for="' + MAP_ID + '"]').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        root.querySelectorAll('.olo-map-view-btn[data-view-for="' + MAP_ID + '"]').forEach(function(b){ b.classList.remove('is-active'); });
                        btn.classList.add('is-active');
                        isGridView = btn.getAttribute('data-view') === 'grid';
                        renderList();
                    });
                });

                /* ── Initial sort + render ── */
                applySort();
                renderList();
                updatePagination();
                updateCount();
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                setTimeout(init, 50);
            }
        })();
        </script>
        <?php
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}";
            echo $border_hover_css . $border_effect_css . '</style>';
        }
        return ob_get_clean();
    }

    private function address_to_bbox( $address, $zoom ) {
        $lat = 41.9028;
        $lng = 12.4964;
        $delta = 0.05 / max( 1, $zoom / 10 );

        return ( $lng - $delta ) . ',' . ( $lat - $delta ) . ',' . ( $lng + $delta ) . ',' . ( $lat + $delta );
    }
}
