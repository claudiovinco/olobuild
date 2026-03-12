<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Facebookpage_Tile extends Olo_Tile_Base {

    protected $type     = 'facebookpage';
    protected $name     = 'Facebook Page';
    protected $icon     = 'dashicons-facebook';
    protected $category = 'marketing';
    protected $defaults = [
        'page_url'        => '',
        'width'           => '340',
        'height'          => '500',
        'tabs'            => 'timeline',
        'show_cover'      => true,
        'show_facepile'   => true,
        'small_header'    => false,
        'adapt_container' => true,
        'language'        => 'it_IT',
        'alignment'       => 'center',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'page_url',        'type' => 'text',   'label' => 'URL pagina Facebook' ],
            [ 'key' => 'width',           'type' => 'range',  'label' => 'Larghezza (px)', 'min' => 180, 'max' => 500, 'step' => 10 ],
            [ 'key' => 'height',          'type' => 'range',  'label' => 'Altezza (px)', 'min' => 70, 'max' => 1000, 'step' => 10 ],
            [ 'key' => 'tabs',            'type' => 'text',   'label' => 'Tab (timeline,events,messages)' ],
            [ 'key' => 'show_cover',      'type' => 'toggle', 'label' => 'Mostra copertina' ],
            [ 'key' => 'show_facepile',   'type' => 'toggle', 'label' => 'Mostra facce amici' ],
            [ 'key' => 'small_header',    'type' => 'toggle', 'label' => 'Header compatto' ],
            [ 'key' => 'adapt_container', 'type' => 'toggle', 'label' => 'Adatta al contenitore' ],
            [ 'key' => 'language',        'type' => 'select', 'label' => 'Lingua SDK', 'options' => [ 'it_IT' => 'Italiano', 'en_US' => 'English', 'de_DE' => 'Deutsch', 'fr_FR' => 'Francais', 'es_ES' => 'Espanol' ] ],
            [ 'key' => 'alignment',       'type' => 'select', 'label' => 'Allineamento', 'options' => [ 'left' => 'Sinistra', 'center' => 'Centro', 'right' => 'Destra' ] ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $page_url        = trim( $s['page_url'] );
        $width           = absint( $s['width'] ) ?: 340;
        $height          = absint( $s['height'] ) ?: 500;
        $tabs            = sanitize_text_field( $s['tabs'] ?: 'timeline' );
        $hide_cover      = empty( $s['show_cover'] ) ? 'true' : 'false';
        $show_facepile   = ! empty( $s['show_facepile'] ) ? 'true' : 'false';
        $small_header    = ! empty( $s['small_header'] ) ? 'true' : 'false';
        $adapt_container = ! empty( $s['adapt_container'] ) ? 'true' : 'false';
        $language        = sanitize_text_field( $s['language'] ?: 'it_IT' );
        $alignment       = $s['alignment'] ?: 'center';

        $align_map = [ 'left' => 'left', 'center' => 'center', 'right' => 'right' ];
        $text_align = $align_map[ $alignment ] ?? 'center';

        $flex_map  = [ 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' ];
        $flex_just = $flex_map[ $alignment ] ?? 'center';

        ob_start();
        ?>
        <div class="olo-facebookpage" style="display:flex;justify-content:<?php echo esc_attr( $flex_just ); ?>;">
            <?php if ( empty( $page_url ) ) : ?>
                <div style="padding:40px 20px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);background:var(--olo-color-muted, #F3F4F6);border-radius:8px;width:<?php echo $width; ?>px;max-width:100%;">
                    <?php echo esc_html( function_exists( 'olo_t' ) ? olo_t( 'Inserisci URL pagina Facebook' ) : 'Inserisci URL pagina Facebook' ); ?>
                </div>
            <?php else : ?>
                <?php $this->enqueue_facebook_sdk( $language ); ?>
                <div id="fb-root"></div>
                <div
                    class="fb-page"
                    data-href="<?php echo esc_url( $page_url ); ?>"
                    data-tabs="<?php echo esc_attr( $tabs ); ?>"
                    data-width="<?php echo esc_attr( $width ); ?>"
                    data-height="<?php echo esc_attr( $height ); ?>"
                    data-small-header="<?php echo esc_attr( $small_header ); ?>"
                    data-adapt-container-width="<?php echo esc_attr( $adapt_container ); ?>"
                    data-hide-cover="<?php echo esc_attr( $hide_cover ); ?>"
                    data-show-facepile="<?php echo esc_attr( $show_facepile ); ?>"
                ></div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Enqueue Facebook SDK once per page via wp_enqueue_script.
     */
    private function enqueue_facebook_sdk( $language = 'it_IT' ) {
        $lang = preg_match( '/^[a-z]{2}_[A-Z]{2}$/', $language ) ? $language : 'it_IT';
        wp_enqueue_script( 'facebook-sdk', 'https://connect.facebook.net/' . $lang . '/sdk.js#xfbml=1&version=v19.0', [], null, true );
    }
}
