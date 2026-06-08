<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Stat Strip — banda di statistiche (valore + etichetta) con divisori.
 * Generica e token-first: valore dal font heading del tema, etichetta in monospace.
 */
class Olo_StatStrip_Tile extends Olo_Tile_Base {

    protected $type     = 'statstrip';
    protected $name     = 'Stat Strip';
    protected $icon     = 'dashicons-chart-bar';
    protected $category = 'layout';
    protected $defaults = [
        'items' => [
            [ 'value' => '500+', 'label' => 'Progetti consegnati' ],
            [ 'value' => '12',   'label' => 'Anni di attività' ],
            [ 'value' => '98%',  'label' => 'Clienti soddisfatti' ],
            [ 'value' => '40M',  'label' => 'Utenti raggiunti' ],
        ],
        'columns'           => 4,
        'band_padding_y'    => 40,
        'show_dividers'     => true,
        'divider_color'     => '#d7d1c2',
        'band_border'       => true,
        'value_font_family' => 'heading',
        'value_color'       => '#18181a',
        'value_size'        => 48,
        'value_weight'      => '600',
        'label_color'       => '#8d8a82',
        'label_size'        => 13,
        'label_uppercase'   => false,
        'align'             => 'left',
        'mono_font_family'  => '',
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-statstrip-' . wp_rand( 10000, 99999 );

        $heading = "var(--olo-font-family-heading, 'DM Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
        $body    = "var(--olo-font-family, 'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
        $mono_fb = "ui-monospace,'SF Mono',Menlo,Consolas,monospace";
        $mono_name = trim( preg_replace( '/[^A-Za-z0-9 \-]/', '', (string) ( $s['mono_font_family'] ?? '' ) ) );
        $mono    = $mono_name !== '' ? "'" . $mono_name . "'," . $mono_fb : $mono_fb;
        $vfam    = [ 'heading' => $heading, 'body' => $body, 'mono' => $mono ][ $s['value_font_family'] ?? 'heading' ] ?? $heading;

        $cols    = max( 1, min( 6, absint( $s['columns'] ) ) );
        $pad_y   = max( 0, min( 100, absint( $s['band_padding_y'] ) ) );
        $v_size  = max( 20, min( 96, absint( $s['value_size'] ) ) );
        $v_wt    = preg_match( '/^\d+$/', (string) $s['value_weight'] ) ? $s['value_weight'] : '600';
        $l_size  = max( 10, min( 22, absint( $s['label_size'] ) ) );

        $line    = $this->safe_color_css( $s['divider_color'] ) ?: '#d7d1c2';
        $v_color = $this->safe_color_css( $s['value_color'] ) ?: '#18181a';
        $l_color = $this->safe_color_css( $s['label_color'] ) ?: '#8d8a82';

        $dividers = ! empty( $s['show_dividers'] );
        $band_brd = ! empty( $s['band_border'] );
        $upper    = ! empty( $s['label_uppercase'] );
        $align    = ( ( $s['align'] ?? 'left' ) === 'center' ) ? 'center' : 'left';

        $items = is_array( $s['items'] ) ? $s['items'] : [];

        $band_style = 'padding:' . $pad_y . 'px 0;';
        if ( $band_brd ) $band_style .= 'border-top:1px solid ' . $line . ';border-bottom:1px solid ' . $line . ';';
        $grid_style = 'display:grid;grid-template-columns:repeat(' . $cols . ',minmax(0,1fr));';
        $cell_style = 'display:flex;flex-direction:column;gap:8px;text-align:' . $align . ';padding:4px 24px;'
            . ( $align === 'center' ? 'align-items:center;' : '' );

        ob_start();
        ?>
        <div class="olo-statstrip <?php echo esc_attr( $uid ); ?>" style="<?php echo esc_attr( $band_style ); ?>">
            <div class="olo-statstrip__grid" style="<?php echo esc_attr( $grid_style ); ?>">
                <?php foreach ( $items as $idx => $it ) :
                    $value = $it['value'] ?? '';
                    $label = $it['label'] ?? '';
                ?>
                    <div class="olo-statstrip__cell" style="<?php echo esc_attr( $cell_style ); ?>">
                        <div class="olo-statstrip__value" style="font-family:<?php echo esc_attr( $vfam ); ?>;font-weight:<?php echo esc_attr( $v_wt ); ?>;font-size:<?php echo $v_size; ?>px;line-height:1;letter-spacing:-0.02em;color:<?php echo esc_attr( $v_color ); ?>;" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.value'; ?>"><?php echo esc_html( $value ); ?></div>
                        <div class="olo-statstrip__label" style="font-family:<?php echo esc_attr( $mono ); ?>;font-size:<?php echo $l_size; ?>px;color:<?php echo esc_attr( $l_color ); ?>;line-height:1.4;<?php echo $upper ? 'text-transform:uppercase;letter-spacing:0.06em;' : ''; ?>" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.label'; ?>"><?php echo esc_html( $label ); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <style>
            <?php if ( $dividers ) : ?>
            .<?php echo $uid; ?> .olo-statstrip__cell:not(:first-child) { border-left: 1px solid <?php echo $line; ?>; }
            <?php endif; ?>
            @media (max-width: 760px) {
                .<?php echo $uid; ?> .olo-statstrip__grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 28px 0; }
                <?php if ( $dividers ) : ?>.<?php echo $uid; ?> .olo-statstrip__cell:nth-child(odd) { border-left: 0; }<?php endif; ?>
            }
            @media (max-width: 420px) {
                .<?php echo $uid; ?> .olo-statstrip__grid { grid-template-columns: 1fr; }
                <?php if ( $dividers ) : ?>.<?php echo $uid; ?> .olo-statstrip__cell { border-left: 0 !important; }<?php endif; ?>
            }
        </style>
        <?php
        return ob_get_clean();
    }
}
