<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Process Steps — passi numerati, borderless di default (numero + titolo + testo).
 * Riproduce il pattern "ProcessSteps" dei blueprint OLOtheme (Sterling, Capital Row,
 * Meridian, Pulse, Cadence, Ledger «how it works», ecc.): griglia a N colonne, numero
 * grande accent, titolo, descrizione. Nessuna card/bordo salvo che richiesti.
 */
class Olo_Process_Steps_Tile extends Olo_Tile_Base {

    protected $type     = 'process-steps';
    protected $name     = 'Process Steps';
    protected $icon     = 'dashicons-editor-ol';
    protected $category = 'layout';
    protected $defaults = [
        'items' => [
            [ 'number' => '01', 'title' => 'Listen',  'description' => 'We start with your life, not your balance sheet.' ],
            [ 'number' => '02', 'title' => 'Plan',    'description' => 'A clear strategy, modelled and stress-tested.' ],
            [ 'number' => '03', 'title' => 'Invest',  'description' => 'Patient, diversified, low-cost where it counts.' ],
            [ 'number' => '04', 'title' => 'Review',  'description' => 'We meet regularly and adjust as life changes.' ],
        ],
        'columns'       => 4,
        'gap'           => 16,
        'auto_number'   => false,
        'number_style'  => 'plain',   // plain | circle | outline
        'number_color'  => 'var(--olo-color-primary, #e1474f)',
        'number_bg'     => '',
        'number_size'   => 40,
        'number_font'   => 'serif',
        'number_weight' => '500',
        'title_color'   => '',
        'title_size'    => 21,
        'title_weight'  => '600',
        'title_font'    => 'serif',
        'desc_color'    => '',
        'desc_size'     => 14,
        'align'         => 'left',
        'item_gap'      => 8,
        'card_bg'       => '',
        'card_border'   => '',
        'card_radius'   => [ 'tl' => 0, 'tr' => 0, 'br' => 0, 'bl' => 0, 'linked' => true ],
        'card_padding'  => 0,
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-psteps-' . wp_rand( 10000, 99999 );

        $serif = "var(--olo-font-family-heading, 'Playfair Display',Georgia,serif)";
        $sans  = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";
        // Valori legacy ('serif'/'sans-serif'/'mono') → stack storici della tile;
        // valori nuovi (type 'font-family') → CSS pronto via resolver condiviso.
        $legacy = [ 'serif' => $serif, 'sans-serif' => $sans, 'mono' => "ui-monospace,Menlo,Consolas,monospace" ];

        $cols    = max( 1, min( 6, absint( $s['columns'] ) ) );
        $gap     = max( 0, min( 80, absint( $s['gap'] ) ) );
        $align   = in_array( $s['align'], [ 'left', 'center', 'right' ], true ) ? $s['align'] : 'left';
        $nstyle  = in_array( $s['number_style'], [ 'plain', 'circle', 'outline' ], true ) ? $s['number_style'] : 'plain';
        $ncolor  = $this->safe_color_css( $s['number_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $nbg     = $this->safe_color_css( $s['number_bg'] ?? '' );
        $nsize   = max( 12, min( 96, absint( $s['number_size'] ) ) );
        $nfont   = $this->resolve_font_family( $s['number_font'], $legacy ) ?: $serif;
        $nweight = preg_match( '/^\d+$/', (string) $s['number_weight'] ) ? $s['number_weight'] : '500';
        $tcolor  = $this->safe_color_css( $s['title_color'] ?? '' ) ?: 'var(--olo-color-text, #111827)';
        $tsize   = max( 14, min( 48, absint( $s['title_size'] ) ) );
        $tweight = preg_match( '/^\d+$/', (string) $s['title_weight'] ) ? $s['title_weight'] : '600';
        $tfont   = $this->resolve_font_family( $s['title_font'], $legacy ) ?: $serif;
        $dcolor  = $this->safe_color_css( $s['desc_color'] ?? '' ) ?: 'var(--olo-color-text-muted, #6b7280)';
        $dsize   = max( 11, min( 22, absint( $s['desc_size'] ) ) );
        $igap    = max( 0, min( 40, absint( $s['item_gap'] ) ) );
        $cardbg  = $this->safe_color_css( $s['card_bg'] ?? '' );
        $cardbd  = $this->safe_color_css( $s['card_border'] ?? '' );
        $cardrad = $this->build_border_radius_css( $s['card_radius'] ?? [] );
        $cardpad = max( 0, min( 60, absint( $s['card_padding'] ) ) );
        $auto    = ! empty( $s['auto_number'] );
        $items   = is_array( $s['items'] ) ? $s['items'] : [];

        $circle      = ( $nstyle === 'circle' || $nstyle === 'outline' );
        $csize       = $nsize + 24;
        $align_items = $align === 'center' ? 'center' : ( $align === 'right' ? 'flex-end' : 'flex-start' );

        ob_start();
        ?>
        <div class="olo-psteps <?php echo esc_attr( $uid ); ?>" style="display:grid;grid-template-columns:repeat(<?php echo $cols; ?>,1fr);gap:<?php echo $gap; ?>px">
            <?php foreach ( $items as $i => $it ) :
                $num = $auto ? sprintf( '%02d', $i + 1 ) : ( $it['number'] ?? sprintf( '%02d', $i + 1 ) );
                $cardstyle  = 'display:flex;flex-direction:column;gap:' . $igap . 'px;text-align:' . $align . ';align-items:' . $align_items . ';';
                if ( $cardbg )  $cardstyle .= 'background:' . $cardbg . ';';
                if ( $cardbd )  $cardstyle .= 'border:1px solid ' . $cardbd . ';';
                if ( $cardrad ) $cardstyle .= 'border-radius:' . $cardrad . ';';
                $cardstyle .= 'padding:' . ( $cardpad ? $cardpad . 'px' : '0 12px' ) . ';';
            ?>
            <div class="olo-psteps__item" style="<?php echo esc_attr( $cardstyle ); ?>">
                <?php if ( $circle ) : ?>
                    <span style="width:<?php echo $csize; ?>px;height:<?php echo $csize; ?>px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-family:<?php echo esc_attr( $nfont ); ?>;font-weight:<?php echo esc_attr( $nweight ); ?>;font-size:<?php echo round( $nsize * 0.6 ); ?>px;line-height:1;color:<?php echo esc_attr( $ncolor ); ?>;<?php echo $nstyle === 'circle' ? 'background:' . esc_attr( $nbg ?: 'rgba(127,127,127,.12)' ) . ';' : 'border:1px solid ' . esc_attr( $nbg ?: $ncolor ) . ';'; ?>" data-olo-editable="items.<?php echo intval( $i ); ?>.number"><?php echo esc_html( $num ); ?></span>
                <?php else : ?>
                    <span style="font-family:<?php echo esc_attr( $nfont ); ?>;font-weight:<?php echo esc_attr( $nweight ); ?>;font-size:<?php echo $nsize; ?>px;line-height:1;color:<?php echo esc_attr( $ncolor ); ?>;display:block" data-olo-editable="items.<?php echo intval( $i ); ?>.number"><?php echo esc_html( $num ); ?></span>
                <?php endif; ?>
                <?php if ( ! empty( $it['title'] ) ) : ?>
                    <h3 style="font-family:<?php echo esc_attr( $tfont ); ?>;font-weight:<?php echo esc_attr( $tweight ); ?>;font-size:<?php echo $tsize; ?>px;line-height:1.2;color:<?php echo esc_attr( $tcolor ); ?>;margin:0" data-olo-editable="items.<?php echo intval( $i ); ?>.title"><?php echo esc_html( $it['title'] ); ?></h3>
                <?php endif; ?>
                <?php if ( ! empty( $it['description'] ) ) : ?>
                    <p style="font-family:<?php echo esc_attr( $sans ); ?>;font-size:<?php echo $dsize; ?>px;line-height:1.6;color:<?php echo esc_attr( $dcolor ); ?>;margin:0" data-olo-editable="items.<?php echo intval( $i ); ?>.description"><?php echo esc_html( $it['description'] ); ?></p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <style>
            @media (max-width:860px){ .<?php echo $uid; ?>{grid-template-columns:repeat(2,1fr) !important} }
            @media (max-width:480px){ .<?php echo $uid; ?>{grid-template-columns:1fr !important} }
        </style>
        <?php
        return ob_get_clean();
    }
}
