<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Hero_Tile extends Olo_Tile_Base {

    protected $type     = 'hero';
    protected $name     = 'Hero';
    protected $icon     = 'dashicons-cover-image';
    protected $category = 'layout';
    protected $defaults = [
        'title'            => 'Welcome to Our Site',
        'subtitle'         => 'Discover something amazing',
        'background_color' => '#6366F1',
        'text_color'       => '#FFFFFF',
        'cta_text'         => 'Get Started',
        'cta_url'          => '#',
        'cta_bg_color'     => '#FFFFFF',
        'cta_text_color'   => '',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'title',            'type' => 'text',  'label' => 'Title' ],
            [ 'key' => 'subtitle',         'type' => 'text',  'label' => 'Subtitle' ],
            [ 'key' => 'background_color', 'type' => 'color', 'label' => 'Background Color' ],
            [ 'key' => 'text_color',       'type' => 'color', 'label' => 'Text Color' ],
            [ 'key' => 'cta_text',         'type' => 'text',  'label' => 'Button Text' ],
            [ 'key' => 'cta_url',          'type' => 'text',  'label' => 'Button URL' ],
            [ 'key' => 'cta_bg_color',     'type' => 'color', 'label' => 'Button Background' ],
            [ 'key' => 'cta_text_color',   'type' => 'color', 'label' => 'Button Text Color' ],
        ];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'mhero-' . wp_rand( 10000, 99999 );

        $bg  = $this->safe_color( $s['background_color'] );
        $fg  = $this->safe_color( $s['text_color'] );

        // CTA colors: button text defaults to background_color for contrast
        $cta_bg  = $this->safe_color( $s['cta_bg_color'] ) ?: '#FFFFFF';
        $cta_fg  = $this->safe_color( $s['cta_text_color'] ) ?: ( $bg ?: '#6366F1' );

        // Sanitize rich text from TipTap (strips <p>, converts rgb()→hex, wp_kses_post)
        $title    = $this->sanitize_richtext( $s['title'] );
        $subtitle = $this->sanitize_richtext( $s['subtitle'] );

        $hero_style = $this->build_style( [
            'background-color' => $bg,
            'color'            => $fg,
        ] );

        $h1_style = $this->build_style( [
            'margin-bottom' => '0.5em',
            'color'         => $fg,
        ] );

        $sub_style = $this->build_style( [
            'font-size'     => '1.25em',
            'margin-bottom' => '1.5em',
            'color'         => $fg,
        ] );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> .olo-hero-cta {
                display: inline-block;
                background: <?php echo $cta_bg; ?> !important;
                color: <?php echo $cta_fg; ?> !important;
                border-radius: 6px;
                padding: 12px 32px;
                font-weight: bold;
                text-decoration: none !important;
                transition: opacity .2s;
            }
            .<?php echo $uid; ?> .olo-hero-cta:hover {
                opacity: .85;
                color: <?php echo $cta_fg; ?> !important;
                text-decoration: none !important;
            }
        </style>
        <div class="olo-hero uk-section uk-section-large uk-text-center <?php echo esc_attr( $uid ); ?>" style="<?php echo esc_attr( $hero_style ); ?>">
            <div class="uk-container">
                <h1 class="uk-heading-medium" style="<?php echo esc_attr( $h1_style ); ?>"><?php echo $title; ?></h1>
                <div style="<?php echo esc_attr( $sub_style ); ?>"><?php echo $subtitle; ?></div>
                <?php if ( ! empty( $s['cta_text'] ) ) : ?>
                    <a href="<?php echo esc_url( $s['cta_url'] ); ?>" class="olo-hero-cta" style="background:<?php echo $cta_bg; ?>;color:<?php echo $cta_fg; ?>">
                        <?php echo wp_kses_post( $s['cta_text'] ); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
