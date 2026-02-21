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
        'text_color'    => '#374151',
        'text_size'     => 14,
    ];

    private $amenity_labels = [
        'wifi'       => 'Wi-Fi',
        'heating'    => 'Riscaldamento',
        'aircon'     => 'Aria condizionata',
        'fireplace'  => 'Camino / Stufa',
        'tv'         => 'TV',
        'pets'       => 'Animali ammessi',
        'smoking'    => 'Fumatori',
        'elevator'   => 'Ascensore',
        'accessible' => 'Accesso disabili',
        'kitchen'    => 'Cucina attrezzata',
        'oven'       => 'Forno',
        'microwave'  => 'Microonde',
        'dishwasher' => 'Lavastoviglie',
        'fridge'     => 'Frigorifero',
        'coffee'     => 'Macchina caffè',
        'kettle'     => 'Bollitore',
        'washer'     => 'Lavatrice',
        'dryer'      => 'Asciugatrice',
        'iron'       => 'Ferro da stiro',
        'hairdryer'  => 'Asciugacapelli',
        'bathtub'    => 'Vasca da bagno',
        'parking'    => 'Parcheggio',
        'garage'     => 'Garage',
        'garden'     => 'Giardino',
        'terrace'    => 'Terrazza / Balcone',
        'bbq'        => 'Barbecue',
        'pool'       => 'Piscina',
        'hottub'     => 'Vasca idromassaggio',
        'ski'        => 'Vicino piste da sci',
        'bikes'      => 'Biciclette disponibili',
        'playground' => 'Area giochi bambini',
        'sauna'      => 'Sauna',
        'hiking'     => 'Sentieri escursionistici',
        'linens'     => 'Biancheria inclusa',
        'towels'     => 'Asciugamani inclusi',
        'cleaning'   => 'Pulizia finale inclusa',
        'crib'       => 'Culla disponibile',
        'highchair'  => 'Seggiolone',
        'safe'       => 'Cassaforte',
    ];

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
                <?php foreach ( $amenities as $key ) :
                    $label = $this->amenity_labels[ $key ] ?? ucfirst( str_replace( '_', ' ', $key ) );
                ?>
                <div style="display:flex;align-items:center;gap:8px;font-size:<?php echo absint( $s['text_size'] ); ?>px;color:<?php echo esc_attr( $s['text_color'] ); ?>;padding:4px 0">
                    <span style="color:<?php echo esc_attr( $accent ); ?>;font-size:16px;flex-shrink:0">&#10003;</span>
                    <span><?php echo esc_html( $label ); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
