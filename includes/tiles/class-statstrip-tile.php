<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Stat Strip — banda di statistiche (valore + etichetta) con divisori.
 * Generica e token-first: valore dal font heading del tema, etichetta in monospace.
 */
class Olobuild_StatStrip_Tile extends Olobuild_Tile_Base {

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
        'divider_color'     => 'var(--olo-color-border, #d7d1c2)',
        'band_border'       => true,
        'value_font_family' => 'heading',
        'value_color'       => 'var(--olo-color-text, #18181a)',
        'value_size'        => 48,
        'value_weight'      => '600',
        'label_color'       => 'var(--olo-color-text-soft, #8d8a82)',
        'label_size'        => 13,
        'label_uppercase'   => false,
        'label_weight'      => '',
        'align'             => 'left',
        'mono_font_family'  => '',
        // Forma (2 nov 2026): 'banda' è quella di sempre; riga, elenco, schede ed evidenza per mettere i numeri dentro
        // un testo, in una colonna o in modo meno ingombrante.
        'forma'             => 'banda',
    ];

    const FORME = [ 'banda', 'riga', 'elenco', 'schede', 'evidenza' ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-statstrip-' . wp_rand( 10000, 99999 );

        $heading = "var(--olo-font-family-heading, 'DM Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
        $body    = "var(--olo-font-family, 'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
        // Vuoto = il mono del tema (--olo-font-family-mono), come dice l'inspector; il monospazio di sistema solo se il tema non ne ha uno.
        $mono_fb = "var(--olo-font-family-mono, ui-monospace,'SF Mono',Menlo,Consolas,monospace)";
        $mono_fam = $this->resolve_font_family( $s['mono_font_family'] ?? '' );
        // Nome font puro (legacy campo text) → wrap con lo stack mono di fallback storico.
        if ( $mono_fam !== '' && preg_match( '/^[A-Za-z0-9 \-]+$/', $mono_fam ) ) {
            $mono_fam = "'" . $mono_fam . "'," . $mono_fb;
        }
        $mono    = $mono_fam !== '' ? $mono_fam : $mono_fb;
        $vfam    = $this->resolve_font_family( $s['value_font_family'] ?? '', [ 'heading' => $heading, 'body' => $body, 'mono' => $mono ] ) ?: $heading;

        $cols    = max( 1, min( 6, absint( $s['columns'] ) ) );
        $pad_css = Olobuild_Tile_Utils::sides_css( Olobuild_Tile_Utils::spacing_sides(
            $s['band_padding'] ?? null,
            [ 'y' => $s['band_padding_y'] ?? null ],
            [ 40, 0, 40, 0 ]
        ) );
        $v_size  = max( 20, min( 96, absint( $s['value_size'] ) ) );
        $v_wt    = preg_match( '/^\d+$/', (string) $s['value_weight'] ) ? $s['value_weight'] : '600';
        // Stile tipografico: vale per il VALORE, non per l'etichetta (che resta in
        // mono). Famiglia, peso, interlinea, spaziatura e maiuscole dal set; quelli
        // della tile restano come riserva se il set viene cancellato. Il wrapper
        // non riceve la classe olo-typo-* (tile_stile_tipografico_proprio()).
        $tp      = sanitize_key( (string) ( $s['typography_preset'] ?? '' ) );
        // Due pezzi, per tenere l'ordine storico delle dichiarazioni (la dimensione sta nel mezzo).
        $v_head  = 'font-family:' . $vfam . ';font-weight:' . $v_wt . ';';
        $v_tail  = 'line-height:1;letter-spacing:-0.02em;';
        if ( $tp !== '' ) {
            $v_head = "font-family:var(--olo-font-{$tp}-family, {$vfam});font-weight:var(--olo-font-{$tp}-weight, {$v_wt});";
            $v_tail = "line-height:var(--olo-font-{$tp}-line-height, 1);letter-spacing:var(--olo-font-{$tp}-letter-spacing, -0.02em);"
                . "text-transform:var(--olo-font-{$tp}-transform, none);";
        }
        // Minimo garantito: il peso dell'etichetta (prima ereditava quello della pagina).
        $l_wt    = $this->font_weight_css( $s['label_weight'] ?? '' );
        $l_size  = max( 10, min( 22, absint( $s['label_size'] ) ) );

        $line    = $this->safe_color_css( $s['divider_color'] ) ?: '#d7d1c2';
        $v_color = $this->safe_color_css( $s['value_color'] ) ?: '#18181a';
        $l_color = $this->safe_color_css( $s['label_color'] ) ?: '#8d8a82';

        $dividers = ! empty( $s['show_dividers'] );
        $band_brd = ! empty( $s['band_border'] );
        $upper    = ! empty( $s['label_uppercase'] );
        $align    = ( ( $s['align'] ?? 'left' ) === 'center' ) ? 'center' : 'left';

        $items = is_array( $s['items'] ) ? $s['items'] : [];
        $forma = in_array( $s['forma'] ?? 'banda', self::FORME, true ) ? $s['forma'] : 'banda';
        if ( 'banda' !== $forma ) {
            return $this->render_forma( $forma, $s, $items, compact( 'uid', 'pad_css', 'v_head', 'v_tail', 'v_size', 'v_color', 'l_wt', 'l_size', 'l_color', 'line', 'dividers', 'band_brd', 'upper', 'align', 'mono', 'cols' ) );
        }

        $band_style = 'padding:' . $pad_css . ';';
        if ( $band_brd ) $band_style .= 'border-top:1px solid ' . $line . ';border-bottom:1px solid ' . $line . ';';
        // Spazio fra le righe: con più voci che colonne la seconda riga stava incollata alla prima.
        // Cresce col valore (48 px → 29 px); con una riga sola non cambia niente. Va nello <style>,
        // non in linea: in linea batterebbe lo spazio fra le righe del telefono (media query qui sotto).
        $row_gap    = max( 16, (int) round( $v_size * 0.6 ) );
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
                        <div class="olo-statstrip__value" style="<?php echo esc_attr( $v_head ); ?>font-size:<?php echo (int) $v_size; ?>px;<?php echo esc_attr( $v_tail ); ?>color:<?php echo esc_attr( $v_color ); ?>;" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.value'; ?>"><?php echo esc_html( $value ); ?></div>
                        <div class="olo-statstrip__label" style="font-family:<?php echo esc_attr( $mono ); ?>;<?php echo $l_wt !== '' ? 'font-weight:' . esc_attr( $l_wt ) . ';' : ''; ?>font-size:<?php echo (int) $l_size; ?>px;color:<?php echo esc_attr( $l_color ); ?>;line-height:1.4;<?php echo $upper ? 'text-transform:uppercase;letter-spacing:0.06em;' : ''; ?>" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.label'; ?>"><?php echo esc_html( $label ); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist for $line, the (int) cast $row_gap and the internally generated 'olo-statstrip-' . wp_rand() $uid. ?>
        <style>
            .<?php echo $uid; ?> .olo-statstrip__grid { row-gap: <?php echo (int) $row_gap; ?>px; }
            <?php
            // Divisore a sinistra di OGNI cella, spostato di 1 px fuori dalla sua colonna, e la
            // griglia che taglia ciò che esce dai suoi lati: il divisore della prima colonna di
            // ogni riga cade fuori e sparisce, qualunque sia il numero di colonne (desktop, ripieghi
            // del telefono, colonne per dispositivo). Prima `:not(:first-child)` lo toglieva solo
            // alla prima cella: dalla seconda riga in poi ogni riga cominciava con una linea.
            if ( $dividers ) : ?>
            .<?php echo $uid; ?> .olo-statstrip__grid { overflow-x: clip; }
            .<?php echo $uid; ?> .olo-statstrip__cell { border-left: 1px solid <?php echo $line; ?>; margin-left: -1px; }
            <?php endif; ?>
            <?php // !important: il grid-template-columns arriva inline su __grid, senza non vincerebbero mai. ?>
            @media (max-width: 760px) {
                .<?php echo $uid; ?> .olo-statstrip__grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; gap: 28px 0; }
            }
            @media (max-width: 420px) {
                .<?php echo $uid; ?> .olo-statstrip__grid { grid-template-columns: 1fr !important; }
            }
            <?php echo $this->css_per_dispositivo( $s, 'columns', '.' . $uid . ' .olo-statstrip__grid', $this->decl_colonne( 6 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- colonne per dispositivo (interi limitati) ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php
        return ob_get_clean();
    }

    /**
     * Le forme diverse dalla banda. Stessi valori, stesse etichette, stessa tipografia; cambia la disposizione:
     * riga = in fila nel testo, a capo quando non ci sta · elenco = uno sotto l'altro, numero ed etichetta affiancati,
     * coi filetti se «Divisori» · schede = ogni numero in un riquadro tenue · evidenza = il primo grande, gli altri in
     * una riga piccola sotto.
     *
     * @param string $forma Una di FORME tranne 'banda'.
     * @param array  $s     Impostazioni.
     * @param array  $items Voci.
     * @param array  $v     Valori già calcolati e ripuliti da render().
     * @return string
     */
    private function render_forma( $forma, $s, $items, $v ) {
        $uid    = $v['uid'];
        $center = 'center' === $v['align'];
        $etich  = 'font-family:' . $v['mono'] . ';' . ( '' !== $v['l_wt'] ? 'font-weight:' . $v['l_wt'] . ';' : '' )
            . 'font-size:' . (int) $v['l_size'] . 'px;color:' . $v['l_color'] . ';line-height:1.4;'
            . ( $v['upper'] ? 'text-transform:uppercase;letter-spacing:0.06em;' : '' );
        $valore = function ( $px ) use ( $v ) {
            return $v['v_head'] . 'font-size:' . (int) $px . 'px;' . $v['v_tail'] . 'color:' . $v['v_color'] . ';';
        };
        $bordo  = $v['band_brd'] ? 'border-top:1px solid ' . $v['line'] . ';border-bottom:1px solid ' . $v['line'] . ';' : '';
        $cella  = function ( $idx, $it, $px, $classe = '' ) use ( $valore, $etich ) {
            return '<div class="olo-statstrip__cell' . $classe . '">'
                . '<div class="olo-statstrip__value" style="' . esc_attr( $valore( $px ) ) . '" data-olo-editable="items.' . (int) $idx . '.value">' . esc_html( $it['value'] ?? '' ) . '</div>'
                . '<div class="olo-statstrip__label" style="' . esc_attr( $etich ) . '" data-olo-editable="items.' . (int) $idx . '.label">' . esc_html( $it['label'] ?? '' ) . '</div>'
                . '</div>';
        };
        $html = '';
        $css  = '';
        $sel  = '.' . $uid;
        if ( 'riga' === $forma ) {
            foreach ( $items as $idx => $it ) {
                $html .= $cella( $idx, $it, $v['v_size'] );
            }
            $css = $sel . ' .olo-statstrip__grid{display:flex;flex-wrap:wrap;align-items:baseline;gap:.75em 2.2em;' . ( $center ? 'justify-content:center;' : '' ) . '}'
                . $sel . ' .olo-statstrip__cell{display:inline-flex;align-items:baseline;gap:.55em;}';
        } elseif ( 'elenco' === $forma ) {
            foreach ( $items as $idx => $it ) {
                $html .= $cella( $idx, $it, $v['v_size'] );
            }
            $css = $sel . ' .olo-statstrip__grid{display:flex;flex-direction:column;}'
                . $sel . ' .olo-statstrip__cell{display:grid;grid-template-columns:minmax(4.2em,auto) 1fr;gap:1.1em;align-items:baseline;padding:.85em 0;' . ( $center ? 'text-align:left;' : '' ) . '}'
                . ( $v['dividers'] ? $sel . ' .olo-statstrip__cell+.olo-statstrip__cell{border-top:1px solid ' . $v['line'] . ';}' : '' );
        } elseif ( 'schede' === $forma ) {
            foreach ( $items as $idx => $it ) {
                $html .= $cella( $idx, $it, $v['v_size'] );
            }
            $css = $sel . ' .olo-statstrip__grid{display:grid;grid-template-columns:repeat(' . (int) $v['cols'] . ',minmax(0,1fr));gap:1rem;}'
                . $sel . ' .olo-statstrip__cell{display:flex;flex-direction:column;gap:.6em;padding:1.4em 1.3em;border-radius:1rem;'
                . 'background:color-mix(in srgb, ' . $v['v_color'] . ' 6%, transparent);' . ( $center ? 'align-items:center;text-align:center;' : '' ) . '}'
                . '@media (max-width: 959px){' . $sel . ' .olo-statstrip__grid{grid-template-columns:repeat(2,minmax(0,1fr)) !important;}}'
                . $this->css_per_dispositivo( $s, 'columns', $sel . ' .olo-statstrip__grid', $this->decl_colonne( 6 ) );
        } else { // evidenza
            $primo = array_slice( $items, 0, 1, true );
            $altri = array_slice( $items, 1, null, true );
            foreach ( $primo as $idx => $it ) {
                $html .= $cella( $idx, $it, min( 160, (int) round( $v['v_size'] * 1.7 ) ), ' olo-statstrip__cell--primo' );
            }
            if ( $altri ) {
                $html .= '<div class="olo-statstrip__altri">';
                foreach ( $altri as $idx => $it ) {
                    $html .= $cella( $idx, $it, max( 18, (int) round( $v['v_size'] * 0.5 ) ) );
                }
                $html .= '</div>';
            }
            $css = $sel . ' .olo-statstrip__grid{display:flex;flex-direction:column;gap:1.4em;' . ( $center ? 'align-items:center;text-align:center;' : '' ) . '}'
                . $sel . ' .olo-statstrip__cell--primo{display:flex;flex-direction:column;gap:.5em;' . ( $center ? 'align-items:center;' : '' ) . '}'
                . $sel . ' .olo-statstrip__altri{display:flex;flex-wrap:wrap;gap:.8em 2.2em;padding-top:1.1em;border-top:1px solid ' . $v['line'] . ';' . ( $center ? 'justify-content:center;' : '' ) . '}'
                . $sel . ' .olo-statstrip__altri .olo-statstrip__cell{display:inline-flex;align-items:baseline;gap:.5em;}';
        }
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- $html escaped piece by piece above; $css built from safe_color_css() colours, int casts, the internal $uid and css_per_dispositivo()
        return '<div class="olo-statstrip olo-statstrip--' . esc_attr( $forma ) . ' ' . esc_attr( $uid ) . '" style="' . esc_attr( 'padding:' . $v['pad_css'] . ';' . $bordo ) . '">'
            . '<div class="olo-statstrip__grid">' . $html . '</div></div><style>' . $css . '</style>';
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}
