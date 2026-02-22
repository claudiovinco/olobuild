<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_ServiceAmenities_Tile extends Olo_Tile_Base {

    protected $type     = 'serviceamenities';
    protected $name     = 'Servizi e Comfort';
    protected $icon     = 'dashicons-yes-alt';
    protected $category = 'booking';
    protected $defaults = [
        'meta_prefix'   => '_olo_service_',
        'columns'       => 4,
        'gap_x'         => 24,
        'gap_y'         => 10,
        'show_title'    => true,
        'title_text'    => 'Servizi e comfort',
        'title_size'    => 18,
        'title_color'   => '#1F2937',
        'check_color'   => '',
        'use_icons'     => false,
        'icon_color'    => '',
        'icon_size'     => 18,
        'text_color'    => '#374151',
        'text_size'     => 14,
    ];

    /** Mappa SVG Lucide line-style per ogni amenity. */
    private static $icon_paths = [
        'wifi'       => '<path d="M12 20h.01"/><path d="M2 8.82a15 15 0 0 1 20 0"/><path d="M5 12.86a10 10 0 0 1 14 0"/><path d="M8.5 16.9a5 5 0 0 1 7 0"/>',
        'heating'    => '<path d="M12 12c-2-2.67 0-6 0-6s4 3.33 0 6"/><path d="M12 21a9 9 0 0 0 0-18 9 9 0 0 0 0 18z"/>',
        'aircon'     => '<path d="M12 2v8"/><path d="m4.93 10.93 1.41 1.41"/><path d="M2 18h2"/><path d="M20 18h2"/><path d="m19.07 10.93-1.41 1.41"/><path d="M22 22H2"/><path d="M16 18a4 4 0 0 0-8 0"/>',
        'fireplace'  => '<path d="M8 16c0-4 4-8 4-8s4 4 4 8a4 4 0 0 1-8 0"/><rect x="2" y="2" width="20" height="20" rx="2"/>',
        'tv'         => '<rect x="2" y="7" width="20" height="15" rx="2" ry="2"/><polyline points="17 2 12 7 7 2"/>',
        'pets'       => '<circle cx="11" cy="4" r="2"/><circle cx="18" cy="8" r="2"/><circle cx="4" cy="8" r="2"/><path d="M12 12c-2 4-6 6-6 6h12s-4-2-6-6"/>',
        'smoking'    => '<path d="M18 12H2v4h16"/><path d="M22 12v4"/><path d="M7 12v-1a2 2 0 0 1 2-2h0a2 2 0 0 0 2-2V4"/>',
        'elevator'   => '<rect x="3" y="2" width="18" height="20" rx="2"/><path d="m9 8 3-3 3 3"/><path d="m9 16 3 3 3-3"/>',
        'accessible' => '<circle cx="16" cy="4" r="1"/><path d="m18 19-3-8H9l-2 8"/><circle cx="9.5" cy="21.5" r="1.5"/>',
        'kitchen'    => '<path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2l-4 6 4 7"/><path d="M21 15v7"/>',
        'oven'       => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 8h20"/><circle cx="8" cy="14" r="2"/><circle cx="16" cy="14" r="2"/>',
        'microwave'  => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="M6 8h8v8H6z"/><circle cx="18" cy="9" r=".5"/><circle cx="18" cy="12" r=".5"/>',
        'dishwasher' => '<rect x="2" y="2" width="20" height="20" rx="2"/><path d="M2 10h20"/><circle cx="8" cy="6" r="1"/><circle cx="12" cy="16" r="3"/>',
        'fridge'     => '<rect x="4" y="2" width="16" height="20" rx="2"/><path d="M4 10h16"/><path d="M8 6v2"/><path d="M8 14v3"/>',
        'coffee'     => '<path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" y1="2" x2="6" y2="4"/><line x1="10" y1="2" x2="10" y2="4"/><line x1="14" y1="2" x2="14" y2="4"/>',
        'kettle'     => '<path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><path d="M6 2v2"/><path d="M10 2v2"/>',
        'washer'     => '<rect x="2" y="2" width="20" height="20" rx="2"/><circle cx="12" cy="14" r="4"/><path d="M2 8h20"/><circle cx="7" cy="5" r="1"/>',
        'dryer'      => '<path d="M4 14h2"/><path d="M18 14h2"/><path d="M12 2a10 10 0 0 1 0 20 10 10 0 0 1 0-20"/><path d="M12 8a4 4 0 0 0-4 4"/>',
        'iron'       => '<path d="M2 18h12l6-6c1-1 2-3 2-5V4H10l-8 14z"/><path d="M14 4v4"/>',
        'hairdryer'  => '<path d="M22 12a4 4 0 0 0-4-4h-2V6a4 4 0 0 0-8 0v8a4 4 0 0 0 4 4h2"/><circle cx="18" cy="12" r="1"/>',
        'bathtub'    => '<path d="M4 12h16a1 1 0 0 1 1 1v3a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4v-3a1 1 0 0 1 1-1"/><path d="M6 12V5a2 2 0 0 1 2-2h.5"/>',
        'parking'    => '<rect x="2" y="2" width="20" height="20" rx="4"/><path d="M9 17V7h4a3 3 0 0 1 0 6H9"/>',
        'garage'     => '<path d="M2 20V8l10-6 10 6v12"/><path d="M6 20v-4h12v4"/><path d="M6 16h12"/>',
        'garden'     => '<path d="M12 10a6 6 0 0 0-6-6 6 6 0 0 0 6 6"/><path d="M12 10a6 6 0 0 1 6-6 6 6 0 0 1-6 6"/><path d="M12 10v12"/>',
        'terrace'    => '<path d="M2 22h20"/><path d="M6 18v4"/><path d="M18 18v4"/><rect x="4" y="14" width="16" height="4"/><path d="M12 2v6"/><path d="M8 6l4-4 4 4"/>',
        'bbq'        => '<path d="M12 2a4 4 0 0 0-4 4c0 2 2 4 4 4s4-2 4-4a4 4 0 0 0-4-4"/><path d="M12 10v6"/><path d="M8 22l4-6 4 6"/>',
        'pool'       => '<path d="M2 6c.6.5 1.2 1 2.5 1C7 7 7 5 9.5 5c2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/><path d="M2 12c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/><path d="M2 18c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/>',
        'hottub'     => '<path d="M7 2v2"/><path d="M12 2v2"/><path d="M17 2v2"/><path d="M2 8h20v4c0 4-4 8-10 8S2 16 2 12V8z"/>',
        'ski'        => '<path d="m18 4-6 6"/><path d="m4 20 16-16"/><circle cx="18" cy="4" r="2"/>',
        'bikes'      => '<circle cx="18.5" cy="17.5" r="3.5"/><circle cx="5.5" cy="17.5" r="3.5"/><circle cx="15" cy="5" r="1"/><path d="M12 17.5V14l-3-3 4-3 2 3h2"/>',
        'playground' => '<circle cx="12" cy="4" r="2"/><path d="M12 6v8"/><path d="m8 14 4 8 4-8"/>',
        'sauna'      => '<path d="M12 2v4"/><path d="M8 4v2"/><path d="M16 4v2"/><rect x="4" y="10" width="16" height="12" rx="2"/>',
        'hiking'     => '<path d="m3 22 2-8h4l2-4 3 6h4l3-12"/>',
        'linens'     => '<path d="M2 4v16a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8.34a2 2 0 0 0-.59-1.42l-4.34-4.34A2 2 0 0 0 15.66 2H4a2 2 0 0 0-2 2"/><path d="M2 12h20"/>',
        'towels'     => '<path d="M6 2v6a3 3 0 0 0 6 0V2"/><path d="M12 2v6a3 3 0 0 0 6 0V2"/><path d="M6 14h12v8H6z"/>',
        'cleaning'   => '<path d="M12 2v8"/><path d="M4.93 10.93l2.83 2.83"/><path d="M2 18h2"/><path d="M20 18h2"/><path d="m19.07 10.93-2.83 2.83"/><path d="M12 22v-4"/>',
        'crib'       => '<path d="M2 16h20"/><path d="M4 8h16v8H4z"/><path d="M4 16v4"/><path d="M20 16v4"/><path d="M8 8V5a2 2 0 0 1 4 0v3"/>',
        'highchair'  => '<path d="M6 4h12v6H6z"/><path d="M9 10v12"/><path d="M15 10v12"/><path d="M6 16h12"/>',
        'safe'       => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="12" cy="12" r="3"/><path d="M12 9v-2"/>',
        // Alias italiani (chiavi usate nei post meta)
        'camino'        => '<path d="M8 16c0-4 4-8 4-8s4 4 4 8a4 4 0 0 1-8 0"/><rect x="2" y="2" width="20" height="20" rx="2"/>',
        'parcheggio'    => '<rect x="2" y="2" width="20" height="20" rx="4"/><path d="M9 17V7h4a3 3 0 0 1 0 6H9"/>',
        'cucina'        => '<path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2l-4 6 4 7"/><path d="M21 15v7"/>',
        'terrazza'      => '<path d="M2 22h20"/><path d="M6 18v4"/><path d="M18 18v4"/><rect x="4" y="14" width="16" height="4"/><path d="M12 2v6"/><path d="M8 6l4-4 4 4"/>',
        'riscaldamento' => '<path d="M12 12c-2-2.67 0-6 0-6s4 3.33 0 6"/><path d="M12 21a9 9 0 0 0 0-18 9 9 0 0 0 0 18z"/>',
        'lavastoviglie' => '<rect x="2" y="2" width="20" height="20" rx="2"/><path d="M2 10h20"/><circle cx="8" cy="6" r="1"/><circle cx="12" cy="16" r="3"/>',
        'biancheria'    => '<path d="M2 4v16a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8.34a2 2 0 0 0-.59-1.42l-4.34-4.34A2 2 0 0 0 15.66 2H4a2 2 0 0 0-2 2"/><path d="M2 12h20"/>',
        'asciugamani'   => '<path d="M6 2v6a3 3 0 0 0 6 0V2"/><path d="M12 2v6a3 3 0 0 0 6 0V2"/><path d="M6 14h12v8H6z"/>',
        'lavatrice'     => '<rect x="2" y="2" width="20" height="20" rx="2"/><circle cx="12" cy="14" r="4"/><path d="M2 8h20"/><circle cx="7" cy="5" r="1"/>',
        'jacuzzi'       => '<path d="M7 2v2"/><path d="M12 2v2"/><path d="M17 2v2"/><path d="M2 8h20v4c0 4-4 8-10 8S2 16 2 12V8z"/>',
        'sci'           => '<path d="m18 4-6 6"/><path d="m4 20 16-16"/><circle cx="18" cy="4" r="2"/>',
        'animali'       => '<circle cx="11" cy="4" r="2"/><circle cx="18" cy="8" r="2"/><circle cx="4" cy="8" r="2"/><path d="M12 12c-2 4-6 6-6 6h12s-4-2-6-6"/>',
        'giardino'      => '<path d="M12 10a6 6 0 0 0-6-6 6 6 0 0 0 6 6"/><path d="M12 10a6 6 0 0 1 6-6 6 6 0 0 1-6 6"/><path d="M12 10v12"/>',
        'seggiolone'    => '<path d="M6 4h12v6H6z"/><path d="M9 10v12"/><path d="M15 10v12"/><path d="M6 16h12"/>',
        'aria_cond'     => '<path d="M12 2v8"/><path d="m4.93 10.93 1.41 1.41"/><path d="M2 18h2"/><path d="M20 18h2"/><path d="m19.07 10.93-1.41 1.41"/><path d="M22 22H2"/><path d="M16 18a4 4 0 0 0-8 0"/>',
        'vista_montagna' => '<path d="m8 3 4 8 5-5 5 15H2L8 3z"/><path d="m2 21 6-6 4 4 5-5 5 5"/>',
        'frigorifero'   => '<rect x="4" y="2" width="16" height="20" rx="2"/><path d="M4 10h16"/><path d="M8 6v2"/><path d="M8 14v3"/>',
        'forno'         => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 8h20"/><circle cx="8" cy="14" r="2"/><circle cx="16" cy="14" r="2"/>',
        'microonde'     => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="M6 8h8v8H6z"/><circle cx="18" cy="9" r=".5"/><circle cx="18" cy="12" r=".5"/>',
        'ascensore'     => '<rect x="3" y="2" width="18" height="20" rx="2"/><path d="m9 8 3-3 3 3"/><path d="m9 16 3 3 3-3"/>',
        'piscina'       => '<path d="M2 6c.6.5 1.2 1 2.5 1C7 7 7 5 9.5 5c2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/><path d="M2 12c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/><path d="M2 18c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/>',
        'biciclette'    => '<circle cx="18.5" cy="17.5" r="3.5"/><circle cx="5.5" cy="17.5" r="3.5"/><circle cx="15" cy="5" r="1"/><path d="M12 17.5V14l-3-3 4-3 2 3h2"/>',
        'cassaforte'    => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="12" cy="12" r="3"/><path d="M12 9v-2"/>',
        'asciugacapelli' => '<path d="M22 12a4 4 0 0 0-4-4h-2V6a4 4 0 0 0-8 0v8a4 4 0 0 0 4 4h2"/><circle cx="18" cy="12" r="1"/>',
        'ferro_stiro'   => '<path d="M2 18h12l6-6c1-1 2-3 2-5V4H10l-8 14z"/><path d="M14 4v4"/>',
        'pulizia'       => '<path d="M12 2v8"/><path d="M4.93 10.93l2.83 2.83"/><path d="M2 18h2"/><path d="M20 18h2"/><path d="m19.07 10.93-2.83 2.83"/><path d="M12 22v-4"/>',
        'culla'         => '<path d="M2 16h20"/><path d="M4 8h16v8H4z"/><path d="M4 16v4"/><path d="M20 16v4"/><path d="M8 8V5a2 2 0 0 1 4 0v3"/>',
        'bollitore'     => '<path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><path d="M6 2v2"/><path d="M10 2v2"/>',
        'caffe'         => '<path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" y1="2" x2="6" y2="4"/><line x1="10" y1="2" x2="10" y2="4"/><line x1="14" y1="2" x2="14" y2="4"/>',
        'vasca'         => '<path d="M4 12h16a1 1 0 0 1 1 1v3a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4v-3a1 1 0 0 1 1-1"/><path d="M6 12V5a2 2 0 0 1 2-2h.5"/>',
        'garage_it'     => '<path d="M2 20V8l10-6 10 6v12"/><path d="M6 20v-4h12v4"/><path d="M6 16h12"/>',
        'sentieri'      => '<path d="m3 22 2-8h4l2-4 3 6h4l3-12"/>',
        'area_giochi'   => '<circle cx="12" cy="4" r="2"/><path d="M12 6v8"/><path d="m8 14 4 8 4-8"/>',
        'fumatori'      => '<path d="M18 12H2v4h16"/><path d="M22 12v4"/><path d="M7 12v-1a2 2 0 0 1 2-2h0a2 2 0 0 0 2-2V4"/>',
        'disabili'      => '<circle cx="16" cy="4" r="1"/><path d="m18 19-3-8H9l-2 8"/><circle cx="9.5" cy="21.5" r="1.5"/>',
    ];

    private function get_amenity_labels() {
        if ( class_exists( 'Olo_Amenities_Catalog' ) ) {
            return Olo_Amenities_Catalog::get_all_labels();
        }
        // Fallback hardcoded
        return [
            'wifi' => 'Wi-Fi', 'heating' => 'Riscaldamento', 'aircon' => 'Aria condizionata',
            'fireplace' => 'Camino / Stufa', 'tv' => 'TV', 'pets' => 'Animali ammessi',
            'smoking' => 'Fumatori', 'elevator' => 'Ascensore', 'accessible' => 'Accesso disabili',
            'kitchen' => 'Cucina attrezzata', 'oven' => 'Forno', 'microwave' => 'Microonde',
            'dishwasher' => 'Lavastoviglie', 'fridge' => 'Frigorifero', 'coffee' => 'Macchina caffè',
            'kettle' => 'Bollitore', 'washer' => 'Lavatrice', 'dryer' => 'Asciugatrice',
            'iron' => 'Ferro da stiro', 'hairdryer' => 'Asciugacapelli', 'bathtub' => 'Vasca da bagno',
            'parking' => 'Parcheggio', 'garage' => 'Garage', 'garden' => 'Giardino',
            'terrace' => 'Terrazza / Balcone', 'bbq' => 'Barbecue', 'pool' => 'Piscina',
            'hottub' => 'Vasca idromassaggio', 'ski' => 'Vicino piste da sci',
            'bikes' => 'Biciclette disponibili', 'playground' => 'Area giochi bambini',
            'sauna' => 'Sauna', 'hiking' => 'Sentieri escursionistici',
            'linens' => 'Biancheria inclusa', 'towels' => 'Asciugamani inclusi',
            'cleaning' => 'Pulizia finale inclusa', 'crib' => 'Culla disponibile',
            'highchair' => 'Seggiolone', 'safe' => 'Cassaforte',
        ];
    }

    private function get_amenity_svg( $key, $size = 18, $color = 'currentColor' ) {
        $path = self::$icon_paths[ $key ] ?? null;
        if ( ! $path ) {
            // Fallback: cerchio generico
            $path = '<circle cx="12" cy="12" r="3"/>';
        }
        return '<svg xmlns="http://www.w3.org/2000/svg" width="' . intval( $size ) . '" height="' . intval( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr( $color ) . '" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">' . $path . '</svg>';
    }

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        global $post;
        if ( ! $post || ! is_singular() ) {
            return '<div style="padding:24px;text-align:center;color:#9ca3af;background:#f9fafb;border-radius:8px">'
                 . '<p style="margin:0">Inserisci in un template single.</p></div>';
        }

        $pid = $post->ID;
        $pfx = rtrim( $s['meta_prefix'], '_' ) . '_';

        $amenities = get_post_meta( $pid, $pfx . 'amenities', true );
        if ( is_string( $amenities ) ) {
            $amenities = maybe_unserialize( $amenities );
        }
        if ( ! is_array( $amenities ) || empty( $amenities ) ) {
            return '';
        }

        $color  = get_post_meta( $pid, $pfx . 'color', true ) ?: '#6366F1';
        $accent = ! empty( $s['check_color'] ) ? $s['check_color'] : $color;

        $use_icons  = ! empty( $s['use_icons'] );
        $icon_color = ! empty( $s['icon_color'] ) ? $s['icon_color'] : $accent;
        $icon_size  = max( 14, min( 28, absint( $s['icon_size'] ?? 18 ) ) );

        $cols = absint( $s['columns'] ) ?: 4;
        $uid  = 'olo-samen-' . wp_rand( 10000, 99999 );

        ob_start();
        ?>
        <div class="<?php echo esc_attr( $uid ); ?>">
            <?php if ( ! empty( $s['show_title'] ) ) : ?>
                <h3 style="font-size:<?php echo absint( $s['title_size'] ); ?>px;font-weight:700;color:<?php echo esc_attr( $s['title_color'] ); ?>;margin:0 0 16px;font-style:italic">
                    <?php echo esc_html( $s['title_text'] ); ?>
                </h3>
            <?php endif; ?>
            <div style="display:grid;grid-template-columns:repeat(<?php echo $cols; ?>,1fr);gap:<?php echo absint( $s['gap_y'] ); ?>px <?php echo absint( $s['gap_x'] ); ?>px">
                <?php
                $amenity_labels = $this->get_amenity_labels();
                foreach ( $amenities as $key ) :
                    $label = $amenity_labels[ $key ] ?? ucfirst( str_replace( '_', ' ', $key ) );
                ?>
                <div style="display:flex;align-items:center;gap:8px;font-size:<?php echo absint( $s['text_size'] ); ?>px;color:<?php echo esc_attr( $s['text_color'] ); ?>;padding:4px 0">
                    <?php if ( $use_icons ) : ?>
                        <span style="color:<?php echo esc_attr( $icon_color ); ?>;flex-shrink:0;display:inline-flex"><?php echo $this->get_amenity_svg( $key, $icon_size, $icon_color ); ?></span>
                    <?php else : ?>
                        <span style="color:<?php echo esc_attr( $accent ); ?>;font-size:16px;flex-shrink:0">&#10003;</span>
                    <?php endif; ?>
                    <span><?php echo esc_html( $label ); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
