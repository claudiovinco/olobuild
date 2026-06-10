<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Hours Strip — banda orari di apertura (giorno · orario · nota) con divisori.
 * Token-first: orario dal font heading del tema, giorno/nota in monospace.
 */
class Olo_HoursStrip_Tile extends Olo_Tile_Base {

    protected $type     = 'hoursstrip';
    protected $name     = 'Hours Strip';
    protected $icon     = 'dashicons-clock';
    protected $category = 'layout';
    protected $defaults = [
        'items' => [
            [ 'day' => 'Lun — Gio', 'time' => '12 — 23', 'note' => 'Cucina fino alle 22' ],
            [ 'day' => 'Ven — Sab', 'time' => '12 — 24', 'note' => 'Aperitivo dalle 18' ],
            [ 'day' => 'Domenica',  'time' => '12 — 16', 'note' => 'Solo pranzo' ],
            [ 'day' => 'Martedì',   'time' => 'Chiuso',  'note' => 'Riposo settimanale' ],
        ],
        'columns'          => 4,
        'band_padding_y'   => 36,
        'show_dividers'    => true,
        'divider_color'    => '#d7d1c2',
        'band_border'      => true,
        'day_color'        => '#8d8a82',
        'day_size'         => 12,
        'time_font_family' => 'heading',
        'time_color'       => '#18181a',
        'time_size'        => 30,
        'time_weight'      => '500',
        'note_color'       => '#8d8a82',
        'note_size'        => 13,
        'mono_font_family' => '',
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-hoursstrip-' . wp_rand( 10000, 99999 );

        $heading = "var(--olo-font-family-heading, 'DM Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
        $body    = "var(--olo-font-family, 'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
        $mono_fb = "ui-monospace,'SF Mono',Menlo,Consolas,monospace";
        $mono_fam = $this->resolve_font_family( $s['mono_font_family'] ?? '' );
        // Nome font puro (legacy campo text) → wrap con lo stack mono di fallback storico.
        if ( $mono_fam !== '' && preg_match( '/^[A-Za-z0-9 \-]+$/', $mono_fam ) ) {
            $mono_fam = "'" . $mono_fam . "'," . $mono_fb;
        }
        $mono    = $mono_fam !== '' ? $mono_fam : $mono_fb;
        $tfam    = $this->resolve_font_family( $s['time_font_family'] ?? '', [ 'heading' => $heading, 'body' => $body, 'mono' => $mono ] ) ?: $heading;

        $cols   = max( 1, min( 6, absint( $s['columns'] ) ) );
        $pad_y  = max( 0, min( 100, absint( $s['band_padding_y'] ) ) );
        $d_size = max( 10, min( 18, absint( $s['day_size'] ) ) );
        $t_size = max( 16, min( 56, absint( $s['time_size'] ) ) );
        $t_wt   = preg_match( '/^\d+$/', (string) $s['time_weight'] ) ? $s['time_weight'] : '500';
        $n_size = max( 10, min( 18, absint( $s['note_size'] ) ) );

        $line    = $this->safe_color_css( $s['divider_color'] ) ?: '#d7d1c2';
        $d_color = $this->safe_color_css( $s['day_color'] ) ?: '#8d8a82';
        $t_color = $this->safe_color_css( $s['time_color'] ) ?: '#18181a';
        $n_color = $this->safe_color_css( $s['note_color'] ) ?: '#8d8a82';

        $dividers = ! empty( $s['show_dividers'] );
        $band_brd = ! empty( $s['band_border'] );

        $items = is_array( $s['items'] ) ? $s['items'] : [];

        $band_style = 'padding:' . $pad_y . 'px 0;';
        if ( $band_brd ) $band_style .= 'border-top:1px solid ' . $line . ';border-bottom:1px solid ' . $line . ';';
        $grid_style = 'display:grid;grid-template-columns:repeat(' . $cols . ',minmax(0,1fr));';
        $cell_style = 'display:flex;flex-direction:column;gap:7px;padding:4px 24px;';

        ob_start();
        ?>
        <div class="olo-hoursstrip <?php echo esc_attr( $uid ); ?>" style="<?php echo esc_attr( $band_style ); ?>">
            <div class="olo-hoursstrip__grid" style="<?php echo esc_attr( $grid_style ); ?>">
                <?php foreach ( $items as $idx => $it ) :
                    $day  = $it['day'] ?? '';
                    $time = $it['time'] ?? '';
                    $note = $it['note'] ?? '';
                ?>
                    <div class="olo-hoursstrip__cell" style="<?php echo esc_attr( $cell_style ); ?>">
                        <?php if ( $day !== '' ) : ?>
                            <div class="olo-hoursstrip__day" style="font-family:<?php echo esc_attr( $mono ); ?>;font-size:<?php echo $d_size; ?>px;text-transform:uppercase;letter-spacing:0.06em;color:<?php echo esc_attr( $d_color ); ?>;" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.day'; ?>"><?php echo esc_html( $day ); ?></div>
                        <?php endif; ?>
                        <div class="olo-hoursstrip__time" style="font-family:<?php echo esc_attr( $tfam ); ?>;font-weight:<?php echo esc_attr( $t_wt ); ?>;font-size:<?php echo $t_size; ?>px;line-height:1.05;letter-spacing:-0.01em;color:<?php echo esc_attr( $t_color ); ?>;" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.time'; ?>"><?php echo esc_html( $time ); ?></div>
                        <?php if ( $note !== '' ) : ?>
                            <div class="olo-hoursstrip__note" style="font-size:<?php echo $n_size; ?>px;line-height:1.4;color:<?php echo esc_attr( $n_color ); ?>;" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.note'; ?>"><?php echo esc_html( $note ); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <style>
            <?php if ( $dividers ) : ?>
            .<?php echo $uid; ?> .olo-hoursstrip__cell:not(:first-child) { border-left: 1px solid <?php echo $line; ?>; }
            <?php endif; ?>
            @media (max-width: 760px) {
                .<?php echo $uid; ?> .olo-hoursstrip__grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 26px 0; }
                <?php if ( $dividers ) : ?>.<?php echo $uid; ?> .olo-hoursstrip__cell:nth-child(odd) { border-left: 0; }<?php endif; ?>
            }
            @media (max-width: 420px) {
                .<?php echo $uid; ?> .olo-hoursstrip__grid { grid-template-columns: 1fr; }
                <?php if ( $dividers ) : ?>.<?php echo $uid; ?> .olo-hoursstrip__cell { border-left: 0 !important; }<?php endif; ?>
            }
        </style>
        <?php
        return ob_get_clean();
    }
}
