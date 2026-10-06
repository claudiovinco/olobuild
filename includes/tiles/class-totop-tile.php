<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Totop_Tile extends Olobuild_Tile_Base {

    protected $type     = 'totop';
    protected $name     = 'Torna su';
    protected $icon     = 'dashicons-arrow-up-alt';
    protected $category = 'navigation';
    protected $defaults = [
        'alignment' => 'right',
        'style'     => 'default',
        'smooth'    => true,
        'button_size' => 44,
        'icon_color'  => '',
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
            [ 'key' => 'alignment', 'type' => 'select', 'label' => 'Alignment', 'options' => [
                'left'   => 'Left',
                'center' => 'Center',
                'right'  => 'Right',
            ]],
            [ 'key' => 'style', 'type' => 'select', 'label' => 'Style', 'options' => [
                'default' => 'Default',
                'primary' => 'Primary',
            ]],
            [ 'key' => 'smooth', 'type' => 'toggle', 'label' => 'Smooth Scroll' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-tt-' . wp_rand( 10000, 99999 );

        $align_class = 'uk-text-' . esc_attr( $s['alignment'] );
        $scroll_attr = ! empty( $s['smooth'] ) ? ' uk-scroll' : '';

        // Il pulsante. Prima era l'icona `uk-totop` di UIkit: una freccina di 18×10 px grigia
        // (#999), senza misura né colore, e «Stile» non veniva letto. Ora un pulsante tondo con
        // la freccia del set Lucide: «Predefinito» leggero, nel colore del testo dove sta la tile
        // (resta leggibile anche su una sezione scura) su un disco velato dello stesso colore;
        // «Pieno» nel colore del sito. Misura e colore della freccia sono controlli.
        // Il disco del Predefinito è un ::before con currentColor e opacità: currentColor dentro
        // color-mix() in alcuni contesti vale nero.
        $style = ( $s['style'] ?? 'default' ) === 'primary' ? 'primary' : 'default';
        $size  = max( 28, min( 96, intval( $s['button_size'] ?? 44 ) ?: 44 ) );
        $icon  = $this->safe_color_css( $s['icon_color'] ?? '' );
        if ( $style === 'primary' ) {
            $base  = 'background-color:var(--olo-color-primary, #e1474f);color:' . ( $icon ?: 'var(--olo-color-primary-contrast, #ffffff)' ) . ';';
            $hover = 'background-color:color-mix(in srgb, var(--olo-color-primary, #e1474f) 85%, var(--olo-color-dark, #000000));transform:translateY(-2px);';
        } else {
            $base  = 'background-color:transparent;color:' . ( $icon ?: 'inherit' ) . ';';
            $hover = 'color:' . ( $icon ?: 'var(--olo-color-primary, #e1474f)' ) . ';';
        }

        // Bordo (con hover ed effetti) sul pulsante, come l'Ombra: sul contenitore, largo quanto
        // la colonna, disegnava un rettangolo attorno al disco. Diventa un anello (border-box: il
        // diametro resta la «Dimensione»). La transizione del bordo si AGGIUNGE a quella del
        // pulsante: con una regola a parte ne avrebbe preso il posto (vince una sola transition).
        $bordo   = $this->build_border_css( $s['border'] ?? [] );
        $bordo_h = $this->build_border_hover_props( $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $trans   = 'color .2s ease, background-color .2s ease, transform .2s ease' . ( $bordo_h['transition'] !== '' ? ', ' . $bordo_h['transition'] : '' );

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from the clamped integer $size, fixed token literals, the safe_color_css() whitelisted icon colour and the shared build_border_css()/build_border_hover_props() helpers (integer-forced widths); $uid is internally generated. ?>
        <style>
            .<?php echo $uid; ?> .olo-totop-btn { position: relative; box-sizing: border-box; display: inline-flex; align-items: center; justify-content: center; width: <?php echo $size; ?>px; height: <?php echo $size; ?>px; border-radius: 50%; text-decoration: none; vertical-align: middle; <?php echo $base . $bordo; ?> transition: <?php echo $trans; ?>; }
            .<?php echo $uid; ?> .olo-totop-btn svg { position: relative; width: 46%; height: 46%; display: block; }
            .<?php echo $uid; ?> .olo-totop-btn:hover { <?php echo $hover . $bordo_h['decls']; ?> }
            <?php if ( $style === 'default' ) : ?>
            .<?php echo $uid; ?> .olo-totop-btn::before { content: ""; position: absolute; inset: 0; border-radius: inherit; background-color: currentColor; opacity: .08; transition: opacity .2s ease; }
            .<?php echo $uid; ?> .olo-totop-btn:hover::before { opacity: .14; }
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-totop-btn:focus-visible { outline: none; box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent); }
            @media (prefers-reduced-motion: reduce) {
                .<?php echo $uid; ?> .olo-totop-btn, .<?php echo $uid; ?> .olo-totop-btn::before { transition: none; }
                .<?php echo $uid; ?> .olo-totop-btn:hover { transform: none; }
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-totop <?php echo esc_attr( $uid ); ?> <?php echo esc_attr( $align_class ); ?>">
            <a href="#" class="olo-totop-btn olo-totop--<?php echo esc_attr( $style ); ?>"<?php echo $scroll_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed internal string (' uk-scroll' or '') set by a boolean toggle above ?> aria-label="<?php echo esc_attr( olobuild_t( 'Torna su' ) ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="m18 15-6-6-6 6"/></svg></a>
        </div>
        <?php
        // Effetti del bordo (neon, gradiente) sul pulsante, dove sta il bordo.
        $border_effect_css = $this->build_border_effect_css( ".{$uid} .olo-totop-btn", $s['border'] ?? [], $s );
        if ( $border_effect_css ) {
            echo '<style>' . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_effect_css() from sanitized settings; $uid is internally generated
        }
        return ob_get_clean();
    }
}
