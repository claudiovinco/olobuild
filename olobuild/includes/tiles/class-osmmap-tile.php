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

        $show_marker     = filter_var( $s['marker'], FILTER_VALIDATE_BOOLEAN );
        $scroll_zoom     = filter_var( $s['scroll_wheel_zoom'], FILTER_VALIDATE_BOOLEAN );
        $dragging        = filter_var( $s['dragging'], FILTER_VALIDATE_BOOLEAN );
        $popup_text      = esc_js( wp_strip_all_tags( $s['marker_popup'] ) );
        $tile_layer      = in_array( $s['tile_layer'], [ 'standard', 'hot', 'positron', 'voyager', 'dark', 'satellite', 'topo', 'esri_street', 'gray', 'opentopomap' ], true ) ? $s['tile_layer'] : 'standard';
        $marker_color    = $this->safe_color_css( $s['marker_color'] ) ?: '#e74c3c';

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

        wp_enqueue_style( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', [], '1.9.4' );
        wp_enqueue_script( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], '1.9.4', true );

        ob_start();
        ?>

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
                var markerIcon = L.divIcon({
                    html: '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="40" viewBox="0 0 24 36"><path d="M12 0C5.4 0 0 5.4 0 12c0 9 12 24 12 24s12-15 12-24C24 5.4 18.6 0 12 0zm0 16c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4z" fill="<?php echo esc_attr( $marker_color ); ?>"/></svg>',
                    iconSize: [28, 40],
                    iconAnchor: [14, 40],
                    popupAnchor: [0, -40],
                    className: 'olo-osm-marker'
                });

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
