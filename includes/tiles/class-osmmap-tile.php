<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Osmmap_Tile extends Olo_Tile_Base {

    protected $type     = 'osmmap';
    protected $name     = 'Mappa';
    protected $icon     = 'dashicons-location-alt';
    protected $category = 'content';
    protected $defaults = [
        'latitude'          => '45.4642',
        'longitude'         => '9.1900',
        'zoom'              => '13',
        'height'            => '400',
        'marker'            => true,
        'marker_popup'      => 'La nostra sede',
        'scroll_wheel_zoom' => false,
        'dragging'          => true,
        'tile_layer'        => 'standard',
        'border_radius'     => '0',
        'marker_color'      => '#e74c3c',
    ];

    public function get_controls() { return []; }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $lat    = floatval( $s['latitude'] ) ?: 45.4642;
        $lng    = floatval( $s['longitude'] ) ?: 9.1900;
        $zoom   = absint( $s['zoom'] ) ?: 13;
        $height = absint( $s['height'] ) ?: 400;
        $radius = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );

        $show_marker     = filter_var( $s['marker'], FILTER_VALIDATE_BOOLEAN );
        $scroll_zoom     = filter_var( $s['scroll_wheel_zoom'], FILTER_VALIDATE_BOOLEAN );
        $dragging        = filter_var( $s['dragging'], FILTER_VALIDATE_BOOLEAN );
        $popup_text      = esc_js( wp_strip_all_tags( $s['marker_popup'] ) );
        $tile_layer      = in_array( $s['tile_layer'], [ 'standard', 'hot', 'positron', 'voyager', 'dark', 'satellite', 'topo', 'esri_street', 'gray', 'opentopomap' ], true ) ? $s['tile_layer'] : 'standard';
        $marker_color    = $this->safe_color_css( $s['marker_color'] ) ?: '#e74c3c';
        $marker_type     = sanitize_key( $s['marker_type'] ?? 'pin' );
        $marker_image    = esc_url( $s['marker_image'] ?? '' );
        $marker_size     = absint( $s['marker_size'] ?? 36 ) ?: 36;

        $map_id = 'olo-osm-' . wp_rand( 10000, 99999 );

        // Tile layer URLs
        $tile_urls = [
            'standard'    => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
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

        $tile_url = esc_js( $tile_urls[ $tile_layer ] );
        $scroll_zoom_js = $scroll_zoom ? 'true' : 'false';
        $dragging_js    = $dragging ? 'true' : 'false';

        // Use local Leaflet copy (consistent with class-map-tile.php, no CDN dependency)
        wp_enqueue_style( 'leaflet', OLO_URL . 'assets/vendor/leaflet/leaflet.css', [], OLO_VERSION );
        wp_enqueue_script( 'leaflet', OLO_URL . 'assets/vendor/leaflet/leaflet.js', [], OLO_VERSION, true );

        ob_start();
        ?>
        <?php if ( $radius_hover_css !== '' ) : ?>
        <style>#<?php echo esc_attr( $map_id ); ?>{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}#<?php echo esc_attr( $map_id ); ?>:hover{border-radius:<?php echo $radius_hover_css; ?> !important}</style>
        <?php endif; ?>

        <div id="<?php echo esc_attr( $map_id ); ?>" class="olo-osmmap" style="height:<?php echo $height; ?>px; border-radius:<?php echo $radius; ?>; overflow:hidden;"></div>

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
                    iconAnchor: [<?php echo round( $marker_size / 2 ); ?>, <?php echo $marker_size; ?>],
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
}
