<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Schedule — timetable settimanale (giorni × fasce orarie). Statico.
 * Celle evidenziabili (prefisso "!" nelle stringhe, o {text,on} negli array).
 * Render == Vue (ScheduleTile.vue).
 */
class Olo_Schedule_Tile extends Olo_Tile_Base {

    protected $type     = 'schedule';
    protected $name     = 'Schedule';
    protected $icon     = 'dashicons-calendar-alt';
    protected $category = 'layout';
    protected $defaults = [
        'eyebrow'      => '',
        'heading'      => '',
        'days'         => 'Mon, Tue, Wed, Thu, Fri',
        'corner_label' => '',
        'rows'         => [],
        'zone_accent'  => '',
        'zone_on'      => '#ffffff',
        'cell_bg'      => '',
        'card_border'  => '',
        'head_color'   => '',
        'align'        => 'left',
    ];

    public function get_controls() { return []; }

    private function norm_cells( $cells ) {
        $out = [];
        if ( is_array( $cells ) ) {
            foreach ( $cells as $c ) {
                if ( is_array( $c ) ) {
                    $out[] = [ 'text' => (string) ( $c['text'] ?? '' ), 'on' => ! empty( $c['on'] ) ];
                } else {
                    $t = trim( (string) $c );
                    $on = ( strlen( $t ) > 0 && $t[0] === '!' );
                    $out[] = [ 'text' => $on ? ltrim( substr( $t, 1 ) ) : $t, 'on' => $on ];
                }
            }
        } else {
            foreach ( explode( '|', (string) $cells ) as $c ) {
                $t = trim( $c );
                $on = ( strlen( $t ) > 0 && $t[0] === '!' );
                $out[] = [ 'text' => $on ? ltrim( substr( $t, 1 ) ) : $t, 'on' => $on ];
            }
        }
        return $out;
    }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'osc-' . wp_rand( 10000, 99999 );

        $accent  = $this->safe_color_css( $s['zone_accent'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $on      = $this->safe_color_css( $s['zone_on'] ?? '' ) ?: '#ffffff';
        $cellbg  = $this->safe_color_css( $s['cell_bg'] ?? '' ) ?: 'var(--olo-color-surface, #ffffff)';
        $line    = $this->safe_color_css( $s['card_border'] ?? '' ) ?: 'var(--olo-color-border, #e5e7eb)';
        $headc   = $this->safe_color_css( $s['head_color'] ?? '' ) ?: 'var(--olo-color-text-muted, #6b7280)';
        $center  = ( ( $s['align'] ?? 'left' ) === 'center' );
        $serif   = "var(--olo-font-family-heading, 'Playfair Display',Georgia,serif)";
        $sans    = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";

        $days = array_values( array_filter( array_map( 'trim', explode( ',', (string) ( $s['days'] ?? '' ) ) ), function ( $d ) { return $d !== ''; } ) );
        $nd   = count( $days );
        if ( $nd === 0 ) return '';
        $rows = is_array( $s['rows'] ) ? array_values( $s['rows'] ) : [];

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?>{ font-family:<?php echo $sans; ?>; <?php if ( $center ) echo 'text-align:center;'; ?> }
            .<?php echo $uid; ?> .osc-eyebrow{font-size:12px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:<?php echo $accent; ?>;display:block;margin-bottom:10px;}
            .<?php echo $uid; ?> .osc-h{font-family:<?php echo $serif; ?>;font-size:clamp(26px,3.6vw,42px);line-height:1.12;margin:0 0 22px;color:var(--olo-color-text,#111827);}
            .<?php echo $uid; ?> .osc-grid{display:grid;grid-template-columns:minmax(56px,auto) repeat(<?php echo $nd; ?>,1fr);border:1px solid <?php echo $line; ?>;border-radius:14px;overflow:hidden;background:<?php echo $line; ?>;gap:1px;text-align:left;}
            .<?php echo $uid; ?> .osc-cell{background:<?php echo $cellbg; ?>;padding:14px 12px;font-size:13.5px;min-height:30px;display:flex;align-items:center;}
            .<?php echo $uid; ?> .osc-head{font-weight:700;font-size:11.5px;letter-spacing:.06em;text-transform:uppercase;color:<?php echo $headc; ?>;}
            .<?php echo $uid; ?> .osc-time{font-weight:700;color:<?php echo $headc; ?>;font-size:12.5px;font-variant-numeric:tabular-nums;}
            .<?php echo $uid; ?> .osc-cell.on{background:<?php echo $accent; ?>;color:<?php echo $on; ?>;font-weight:600;}
            .<?php echo $uid; ?> .osc-cell.empty{opacity:.4;}
        </style>
        <div class="olo-schedule <?php echo esc_attr( $uid ); ?>">
            <?php if ( $s['eyebrow'] !== '' ) : ?><span class="osc-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span><?php endif; ?>
            <?php if ( $s['heading'] !== '' ) : ?><h2 class="osc-h"><?php echo esc_html( $s['heading'] ); ?></h2><?php endif; ?>
            <div class="osc-grid" role="table">
                <div class="osc-cell osc-head"><?php echo esc_html( $s['corner_label'] ?? '' ); ?></div>
                <?php foreach ( $days as $d ) : ?><div class="osc-cell osc-head"><?php echo esc_html( $d ); ?></div><?php endforeach; ?>
                <?php foreach ( $rows as $r ) :
                    $cells = $this->norm_cells( $r['cells'] ?? [] );
                ?>
                    <div class="osc-cell osc-time"><?php echo esc_html( $r['time'] ?? '' ); ?></div>
                    <?php for ( $i = 0; $i < $nd; $i++ ) :
                        $c = $cells[ $i ] ?? [ 'text' => '', 'on' => false ];
                        $cls = 'osc-cell' . ( $c['on'] ? ' on' : '' ) . ( $c['text'] === '' ? ' empty' : '' );
                    ?>
                        <div class="<?php echo $cls; ?>"><?php echo $c['text'] === '' ? '·' : esc_html( $c['text'] ); ?></div>
                    <?php endfor; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
