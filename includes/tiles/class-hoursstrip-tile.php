<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Hours Strip — banda orari di apertura (giorno · orario · nota) con divisori.
 * Token-first: orario dal font heading del tema, giorno/nota in monospace.
 */
class Olobuild_HoursStrip_Tile extends Olobuild_Tile_Base {

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
        'divider_color'    => 'var(--olo-color-border, #d7d1c2)',
        'band_border'      => true,
        'day_color'        => 'var(--olo-color-text-soft, #8d8a82)',
        'day_size'         => 12,
        'time_font_family' => 'heading',
        'time_color'       => 'var(--olo-color-text, #18181a)',
        'time_size'        => 30,
        'time_weight'      => '500',
        'note_color'       => 'var(--olo-color-text-soft, #8d8a82)',
        'note_size'        => 13,
        'mono_font_family' => '',
        // Oggi in evidenza (16 nov 2026): spento nelle tile già salvate, acceso nella partenza del builder.
        'evidenzia_oggi'   => false,
        'scritta_oggi'     => 'Oggi',
        'colore_oggi'      => '',
    ];

    /** I giorni salvati ('1,2,3') → numeri ISO validi (1 lunedì … 7 domenica), come la Lavagna a gesso. */
    private static function giorni( $v ) {
        $out = [];
        foreach ( preg_split( '/[\s,|]+/', (string) $v ) as $g ) {
            $g = (int) $g;
            if ( $g >= 1 && $g <= 7 ) {
                $out[ $g ] = $g;
            }
        }
        ksort( $out );
        return array_values( $out );
    }

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-hoursstrip-' . wp_rand( 10000, 99999 );

        // Il ripiego dei titoli è quello di tutte le tile (resolve_font_family('heading')): senza un carattere dei titoli
        // scelto nel sito questa tile ripiegava su DM Sans mentre i titoli accanto andavano su Georgia (16 nov 2026).
        $heading = $this->resolve_font_family( 'heading' );
        $body    = "var(--olo-font-family, 'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
        // Vuoto = il mono del tema (--olo-font-family-mono), come dice l'inspector; il monospazio di sistema solo se il tema non ne ha uno.
        $mono_fb = "var(--olo-font-family-mono, ui-monospace,'SF Mono',Menlo,Consolas,monospace)";
        $mono_fam = $this->resolve_font_family( $s['mono_font_family'] ?? '' );
        // Nome font puro (legacy campo text) → wrap con lo stack mono di fallback storico.
        if ( $mono_fam !== '' && preg_match( '/^[A-Za-z0-9 \-]+$/', $mono_fam ) ) {
            $mono_fam = "'" . $mono_fam . "'," . $mono_fb;
        }
        $mono    = $mono_fam !== '' ? $mono_fam : $mono_fb;
        $tfam    = $this->resolve_font_family( $s['time_font_family'] ?? '', [ 'heading' => $heading, 'body' => $body, 'mono' => $mono ] ) ?: $heading;

        $cols   = max( 1, min( 6, absint( $s['columns'] ) ) );
        $pad_css = Olobuild_Tile_Utils::sides_css( Olobuild_Tile_Utils::spacing_sides(
            $s['band_padding'] ?? null,
            [ 'y' => $s['band_padding_y'] ?? null ],
            [ 36, 0, 36, 0 ]
        ) );
        $d_size = max( 10, min( 18, absint( $s['day_size'] ) ) );
        $t_size = max( 16, min( 56, absint( $s['time_size'] ) ) );
        $t_wt   = preg_match( '/^\d+$/', (string) $s['time_weight'] ) ? $s['time_weight'] : '500';
        $n_size = max( 10, min( 18, absint( $s['note_size'] ) ) );

        $line    = $this->safe_color_css( $s['divider_color'] ) ?: 'var(--olo-color-border, #d7d1c2)';
        $d_color = $this->safe_color_css( $s['day_color'] ) ?: 'var(--olo-color-text-muted, #8d8a82)';
        $t_color = $this->safe_color_css( $s['time_color'] ) ?: 'var(--olo-color-text, #18181a)';
        $n_color = $this->safe_color_css( $s['note_color'] ) ?: 'var(--olo-color-text-muted, #8d8a82)';

        $dividers = ! empty( $s['show_dividers'] );
        $band_brd = ! empty( $s['band_border'] );

        $items = is_array( $s['items'] ) ? $s['items'] : [];

        // Oggi in evidenza: la fascia i cui giorni comprendono oggi, nel fuso orario del sito (gli orari sono quelli del
        // negozio, non di chi guarda). Lo decide lo script qui sotto, così vale anche da una pagina in cache.
        $oggi_on  = ! empty( $s['evidenzia_oggi'] );
        $oggi_tx  = trim( (string) ( $s['scritta_oggi'] ?? '' ) );
        $oggi_col = $this->safe_color_css( (string) ( $s['colore_oggi'] ?? '' ) ) ?: 'var(--olo-color-primary, #e1474f)';
        $oggi_txc = Olobuild_Tile_Utils::testo_su_fondo( $oggi_col, 'var(--olo-color-primary-contrast, #ffffff)' ) ?: 'var(--olo-color-primary-contrast, #ffffff)';

        $band_style = 'padding:' . $pad_css . ';';
        if ( $band_brd ) $band_style .= 'border-top:1px solid ' . $line . ';border-bottom:1px solid ' . $line . ';';
        // Spazio fra le righe a ogni larghezza (prima solo nel ripiego sotto i 760 px: con le colonne scelte per
        // un dispositivo, o con più voci che colonne, le righe restavano incollate).
        $grid_style = 'display:grid;grid-template-columns:repeat(' . $cols . ',minmax(0,1fr));row-gap:26px;';
        // Cella come contenitore (olo-hs-cella): l'orario scende a gradini con la sua larghezza, mai oltre la dimensione
        // scelta, e il padding laterale cresce con la colonna. In una colonna a metà pagina «10 — 13» andava su tre righe
        // e «Chiuso» usciva dalla cella (16 nov 2026).
        $cell_style = 'display:flex;flex-direction:column;gap:7px;padding:4px clamp(8px, 8%, 24px);container-type:inline-size;container-name:olo-hs-cella;';
        $gradini    = '';
        foreach ( [ 200 => 52, 160 => 42, 130 => 34, 105 => 27, 80 => 21 ] as $largo => $px ) {
            if ( (int) $t_size > $px ) {
                $gradini .= '@container olo-hs-cella (max-width:' . (int) $largo . 'px){.' . $uid . ' .olo-hoursstrip__time{font-size:' . (int) $px . 'px !important;}}';
            }
        }

        // Colonne in vigore a ogni larghezza, nell'ordine in cui il CSS qui sotto le applica: quelle scelte per
        // dispositivo (css_per_dispositivo, la più stretta vince) e, dove non ce ne sono, i ripieghi fissi
        // (2 sotto i 760 px, 1 sotto i 420). Servono ai divisori: la prima cella di ogni riga non ne ha.
        $per_disp = [];
        foreach ( [ 'tablet_landscape' => 1200, 'tablet' => 960, 'mobile_landscape' => 640, 'mobile' => 480 ] as $disp => $px ) {
            $v = $s[ 'columns_' . $disp ] ?? '';
            if ( $v !== '' && $v !== null ) {
                $per_disp[ $px ] = max( 1, min( 6, absint( $v ) ) );
            }
        }
        $col_fasce = [];
        $scelto    = null;
        $prec      = $cols;
        foreach ( [ 1200, 960, 760, 640, 480, 420 ] as $px ) {
            if ( isset( $per_disp[ $px ] ) ) {
                $scelto = $per_disp[ $px ];
            }
            $n = ( null !== $scelto ) ? $scelto : ( $px <= 420 ? 1 : ( $px <= 760 ? 2 : $cols ) );
            if ( $n !== $prec ) {
                $col_fasce[ $px ] = $n;
                $prec             = $n;
            }
        }

        ob_start();
        ?>
        <div id="<?php echo esc_attr( $uid ); ?>" class="olo-hoursstrip <?php echo esc_attr( $uid ); ?>" style="<?php echo esc_attr( $band_style ); ?>">
            <div class="olo-hoursstrip__grid" style="<?php echo esc_attr( $grid_style ); ?>">
                <?php foreach ( $items as $idx => $it ) :
                    $day  = $it['day'] ?? '';
                    $time = $it['time'] ?? '';
                    $note = $it['note'] ?? '';
                    $sett = $oggi_on ? self::giorni( $it['settimana'] ?? '' ) : [];
                    // Il trattino resta attaccato a ciò che lo precede: se la riga non basta va a capo DOPO il trattino
                    // («10 —» / «13», «Lun —» / «Ven»), mai con il trattino da solo su una riga.
                    $lega = function ( $txt ) {
                        return preg_replace( '/\s+([\x{2014}\x{2013}])\s+/u', "\u{00A0}$1 ", (string) $txt );
                    };
                ?>
                    <div class="olo-hoursstrip__cell" style="<?php echo esc_attr( $cell_style ); ?>"<?php echo $sett ? ' data-settimana="' . esc_attr( implode( ',', $sett ) ) . '"' : ''; ?>>
                        <?php if ( $day !== '' || $sett ) : ?>
                            <div class="olo-hoursstrip__testa">
                                <?php if ( $day !== '' ) : ?>
                                <div class="olo-hoursstrip__day" style="font-family:<?php echo esc_attr( $mono ); ?>;font-size:<?php echo (int) $d_size; ?>px;text-transform:uppercase;letter-spacing:0.06em;color:<?php echo esc_attr( $d_color ); ?>;" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.day'; ?>"><?php echo esc_html( $lega( $day ) ); ?></div>
                                <?php endif; ?>
                                <?php if ( $sett && '' !== $oggi_tx ) : ?>
                                <span class="olo-hoursstrip__oggi" hidden><?php echo esc_html( $oggi_tx ); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <div class="olo-hoursstrip__time" style="font-family:<?php echo esc_attr( $tfam ); ?>;font-weight:<?php echo esc_attr( $t_wt ); ?>;font-size:<?php echo (int) $t_size; ?>px;line-height:1.05;letter-spacing:-0.01em;color:<?php echo esc_attr( $t_color ); ?>;" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.time'; ?>"><?php echo esc_html( $lega( $time ) ); ?></div>
                        <?php if ( $note !== '' ) : ?>
                            <div class="olo-hoursstrip__note" style="font-size:<?php echo (int) $n_size; ?>px;line-height:1.4;color:<?php echo esc_attr( $n_color ); ?>;" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.note'; ?>"><?php echo esc_html( $note ); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from the internally generated $uid, the safe_color_css() whitelisted divider colour and integer column counts/thresholds. ?>
        <style>
            <?php echo $gradini; ?>
            <?php if ( $oggi_on ) : ?>
            .<?php echo $uid; ?> .olo-hoursstrip__oggi { position: absolute; top: 0; left: clamp(8px, 8%, 24px); transform: translateY(-50%); display: inline-flex; align-items: center; padding: 0.15em 0.8em; border-radius: 99em; background: <?php echo $oggi_col; ?>; color: <?php echo $oggi_txc; ?>; font: 600 10px/1.5 <?php echo $mono; ?>; letter-spacing: 0.08em; text-transform: uppercase; white-space: nowrap; }
            .<?php echo $uid; ?> .olo-hoursstrip__oggi[hidden] { display: none; }
            <?php // La fascia di oggi: un fondo tenue arrotondato che sporge di poco sopra e sotto (margine negativo: la
                  // banda non cambia altezza) e niente divisori accanto, che taglierebbero il fondo. La scritta sta a
                  // cavallo del bordo superiore, allineata al testo: accanto al giorno, in una cella stretta andava a capo
                  // e abbassava l'orario rispetto alle altre fasce. ?>
            .<?php echo $uid; ?> .olo-hoursstrip__cell.is-oggi { position: relative; background: color-mix(in srgb, <?php echo $oggi_col; ?> 8%, transparent); border-radius: 0.75em; margin: -0.75em 0; padding-top: 1em !important; padding-bottom: 1em !important; border-left-color: transparent !important; }
            .<?php echo $uid; ?> .olo-hoursstrip__cell.is-oggi + .olo-hoursstrip__cell { border-left-color: transparent !important; }
            .<?php echo $uid; ?> .is-oggi .olo-hoursstrip__day { color: <?php echo $oggi_col; ?> !important; }
            <?php endif; ?>
            <?php if ( $dividers ) : ?>
            .<?php echo $uid; ?> .olo-hoursstrip__cell:not(:nth-child(<?php echo (int) $cols; ?>n+1)) { border-left: 1px solid <?php echo $line; ?>; }
            <?php endif; ?>
            <?php // !important: le colonne del desktop sono in linea e battevano queste regole (4 colonne schiacciate
                  // sul telefono). Le colonne scelte per dispositivo, stampate dopo, vincono comunque. ?>
            @media (max-width: 760px) {
                .<?php echo $uid; ?> .olo-hoursstrip__grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
            }
            @media (max-width: 420px) {
                .<?php echo $uid; ?> .olo-hoursstrip__grid { grid-template-columns: 1fr !important; }
            }
            <?php echo $this->css_per_dispositivo( $s, 'columns', '.' . $uid . ' .olo-hoursstrip__grid', $this->decl_colonne( 6 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- colonne per dispositivo (interi limitati) ?>
            <?php
            // Divisori dove cambiano le colonne in vigore: niente filo sulla prima cella di ogni riga, il filo
            // sulle altre. Prima valevano solo i ripieghi (dispari senza filo sotto i 760, nessuno sotto i 420),
            // anche quando le colonne scelte per il dispositivo erano altre: a 880 px con 2 colonne per il tablet
            // la terza cella, a inizio riga, teneva il filo a sinistra.
            if ( $dividers ) {
                foreach ( $col_fasce as $px => $n ) {
                    $cella = '.' . $uid . ' .olo-hoursstrip__cell';
                    echo '@media (max-width:' . (int) $px . 'px){'
                        . $cella . ':not(:nth-child(' . (int) $n . 'n+1)){border-left:1px solid ' . $line . '}'
                        . $cella . ':nth-child(' . (int) $n . 'n+1){border-left:0}}';
                }
            }
            ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php if ( $oggi_on ) :
            $tz  = function_exists( 'wp_timezone_string' ) ? (string) wp_timezone_string() : '';
            $cfg = [
                'tz'  => ( '' !== $tz && false !== strpos( $tz, '/' ) ) ? $tz : ( 'UTC' === $tz ? 'UTC' : '' ),
                'off' => (int) round( wp_timezone()->getOffset( new DateTime( 'now', wp_timezone() ) ) / 60 ),
            ];
            ?>
        <script>
        (function(){
            var el = document.getElementById(<?php echo wp_json_encode( $uid ); ?>);
            if (!el) { return; }
            var C = <?php echo wp_json_encode( $cfg ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON da wp_json_encode() del fuso del sito ?>;
            function giorno() {
                var d = new Date();
                try {
                    if (C.tz) {
                        var o = {};
                        new Intl.DateTimeFormat('en-US', { timeZone: C.tz, weekday: 'short' }).formatToParts(d).forEach(function (p) { o[p.type] = p.value; });
                        var w = { Mon: 1, Tue: 2, Wed: 3, Thu: 4, Fri: 5, Sat: 6, Sun: 7 }[o.weekday];
                        if (w) { return w; }
                    }
                } catch (e) {}
                var s = new Date(d.getTime() + (C.off + d.getTimezoneOffset()) * 60000);
                return s.getDay() === 0 ? 7 : s.getDay();
            }
            function segna() {
                var w = ',' + giorno() + ',';
                var celle = el.querySelectorAll('.olo-hoursstrip__cell');
                for (var i = 0; i < celle.length; i++) {
                    var gg = celle[i].getAttribute('data-settimana');
                    var oggi = gg ? (',' + gg + ',').indexOf(w) !== -1 : false;
                    celle[i].classList.toggle('is-oggi', oggi);
                    var b = celle[i].querySelector('.olo-hoursstrip__oggi');
                    if (b) { b.hidden = !oggi; }
                }
            }
            segna();
            // A mezzanotte la fascia cambia anche a pagina aperta; il controllo si ferma quando la tile non c'è più
            // (nel builder ogni modifica la ridisegna).
            var giro = setInterval(function () {
                if (!el.isConnected) { clearInterval(giro); return; }
                segna();
            }, 60000);
        })();
        </script>
        <?php endif; ?>
        <?php
        return ob_get_clean();
    }
}
