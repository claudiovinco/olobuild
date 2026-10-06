<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_TextBlock_Tile extends Olobuild_Tile_Base {

    protected $type     = 'text-block';
    protected $name     = 'Testo';
    protected $icon     = 'dashicons-editor-paragraph';
    protected $category = 'essential';
    protected $defaults = [
        'content'     => '<p>Scrivi qui il tuo testo.</p>',
        'text_color'  => '',
        'font_size'   => '',
        'line_height' => '',
        'max_width'   => '',
        'columns'     => 1,
        'column_gap'  => '30',
        'padding'      => '16',
        'tile_padding' => [ 'top' => 16, 'right' => 16, 'bottom' => 16, 'left' => 16 ],
        'tile_margin'  => [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 ],
        'border_radius'           => [ 'tl' => 0, 'tr' => 0, 'br' => 0, 'bl' => 0 ],
        'hover_border_radius'     => [ 'tl' => 0, 'tr' => 0, 'br' => 0, 'bl' => 0 ],
        'hover_radius_duration'   => 400,
        // Text effects
        'text_effect'             => 'none',
        'text_effect_target'      => 'content',
        'text_effect_speed'       => '50',
        'text_effect_delay'       => '0',
        'text_effect_loop'        => false,
        'text_effect_cursor'      => true,
        'text_effect_cursor_char' => '|',
        'text_effect_color'       => '',
        'text_effect_color_to'    => '',
        'text_effect_phrases'     => '',
        'text_effect_pause'       => '1500',
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
            [ 'key' => 'content',     'type' => 'editor', 'label' => 'Contenuto' ],
            [ 'key' => 'text_color',  'type' => 'color',  'label' => 'Colore testo' ],
            [ 'key' => 'font_size',   'type' => 'range',  'label' => 'Dimensione' ],
            [ 'key' => 'line_height', 'type' => 'select', 'label' => 'Interlinea' ],
            [ 'key' => 'max_width',   'type' => 'range',  'label' => 'Larghezza max' ],
            [ 'key' => 'padding',     'type' => 'range',  'label' => 'Padding' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        // Build inline style — padding e margini con FieldSpacing (oggetto {top,right,bottom,left})
        $style = '';

        // Padding: usa tile_padding (standard) oppure tb_padding/padding (legacy)
        $p = $s['tile_padding'] ?? $s['tb_padding'] ?? null;
        if ( is_array( $p ) ) {
            $style .= 'padding:' . intval( $p['top'] ?? 0 ) . 'px ' . intval( $p['right'] ?? 0 ) . 'px ' . intval( $p['bottom'] ?? 0 ) . 'px ' . intval( $p['left'] ?? 0 ) . 'px;';
        } else {
            $pad = absint( $s['padding'] ?? 16 );
            $style .= 'padding:' . $pad . 'px;';
        }

        // Margini: usa tile_margin (standard) oppure tb_margin (legacy)
        $m  = $s['tile_margin'] ?? $s['tb_margin'] ?? null;
        $mw = absint( $s['max_width'] ?? 0 );
        $mt = $mr = $mb = $ml = 0;
        if ( is_array( $m ) ) {
            $mt = intval( $m['top'] ?? 0 );
            $mr = intval( $m['right'] ?? 0 );
            $mb = intval( $m['bottom'] ?? 0 );
            $ml = intval( $m['left'] ?? 0 );
        }
        $ta = $s['text_align'] ?? '';
        if ( $mw > 0 && ! $mr && ! $ml && ( $ta === 'center' || $ta === 'right' ) ) {
            // «Larghezza max» con il testo centrato (o a destra) porta lì anche il blocco: senza
            // i margini automatici restava incollato a sinistra, con le righe centrate in una
            // colonna fuori asse. A sinistra o giustificato il blocco resta dov'era, allineato
            // al titolo sopra di lui. Margini laterali impostati a mano = scelta dell'utente.
            $style .= 'margin:' . $mt . 'px ' . ( $ta === 'center' ? 'auto' : '0' ) . ' ' . $mb . 'px auto;';
        } elseif ( $mt || $mr || $mb || $ml ) {
            $style .= "margin:{$mt}px {$mr}px {$mb}px {$ml}px;";
        }

        // Apply global typography preset if set — sanitize_key: il valore finisce
        // nei nomi delle custom property, e gli id dei set sono chiavi.
        $tp = sanitize_key( (string) ( $s['typography_preset'] ?? '' ) );
        if ( $tp ) {
            $style .= "font-family:var(--olo-font-{$tp}-family);";
            $style .= "font-weight:var(--olo-font-{$tp}-weight);";
            $style .= "text-transform:var(--olo-font-{$tp}-transform);";
            $style .= "line-height:var(--olo-font-{$tp}-line-height);";
            $style .= "letter-spacing:var(--olo-font-{$tp}-letter-spacing);";
        }

        $txt_clr = $this->safe_color_css( $s['text_color'] ?? '' );
        if ( $txt_clr ) {
            $style .= 'color:' . $txt_clr . ';';
        }

        $fs = absint( $s['font_size'] ?? 0 );
        if ( $fs > 0 ) {
            $style .= 'font-size:' . $fs . 'px;';
        }

        // Minimo garantito del controllo tipografia: famiglia e peso accanto a
        // corpo, interlinea e colore. Con uno stile tipografico collegato non si
        // scrivono: lo stile governa famiglia, peso e interlinea (sui paragrafi
        // arriva comunque, tramite la classe olo-typo-* sul wrapper), e
        // l'inspector quelle tre righe allora non le mostra.
        $ff = $tp ? '' : $this->resolve_font_family( (string) ( $s['font_family'] ?? '' ) );
        if ( $ff ) {
            $style .= 'font-family:' . $ff . ';';
        }
        $fw = $tp ? '' : $this->font_weight_css( $s['font_weight'] ?? '' );
        if ( $fw !== '' ) {
            $style .= 'font-weight:' . $fw . ';';
        }

        $lh = $tp ? '' : ( $s['line_height'] ?? '' );
        if ( is_numeric( $lh ) ) {
            $lh_val = (float) $lh;
            if ( $lh_val >= 0.5 && $lh_val <= 5 ) {
                $style .= 'line-height:' . rtrim( rtrim( sprintf( '%.2f', $lh_val ), '0' ), '.' ) . ';';
            }
        }

        if ( $mw > 0 ) {
            $style .= 'max-width:' . $mw . 'px;';
        }

        // Allineamento testo ($ta letto sopra, coi margini)
        if ( in_array( $ta, [ 'left', 'center', 'right', 'justify' ], true ) ) {
            $style .= 'text-align:' . $ta . ';';
        }

        // Multi-colonne (CSS columns): 1=single, 2-4=multi colonne con gap
        $cols = max( 1, min( 4, absint( $s['columns'] ?? 1 ) ) );
        if ( $cols > 1 ) {
            $col_gap = max( 0, min( 80, absint( $s['column_gap'] ?? 30 ) ) );
            $style .= 'column-count:' . $cols . ';';
            $style .= 'column-gap:' . $col_gap . 'px;';
        }

        // Border radius (4 angoli indipendenti via FieldBorderRadius)
        $br_css = $this->build_border_radius_css( $s['border_radius'] ?? [] );
        if ( $br_css ) {
            $style .= 'border-radius:' . $br_css . ';';
        }

        // Hover border-radius: se settato (anche con valori 0) genera transition + rule :hover
        $hover_br_raw = $s['hover_border_radius'] ?? '';
        $has_hover_br = is_array( $hover_br_raw ) && (
            ( isset( $hover_br_raw['tl'] ) && intval( $hover_br_raw['tl'] ) !== intval( $s['border_radius']['tl'] ?? 0 ) ) ||
            ( isset( $hover_br_raw['tr'] ) && intval( $hover_br_raw['tr'] ) !== intval( $s['border_radius']['tr'] ?? 0 ) ) ||
            ( isset( $hover_br_raw['br'] ) && intval( $hover_br_raw['br'] ) !== intval( $s['border_radius']['br'] ?? 0 ) ) ||
            ( isset( $hover_br_raw['bl'] ) && intval( $hover_br_raw['bl'] ) !== intval( $s['border_radius']['bl'] ?? 0 ) )
        );
        $hover_br_css = $has_hover_br ? $this->build_border_radius_css( $hover_br_raw ) : '';
        $br_duration  = max( 50, intval( $s['hover_radius_duration'] ?? 400 ) );
        if ( $hover_br_css ) {
            $style .= 'transition:border-radius ' . $br_duration . 'ms ease;';
        }

        // Content: supports both HTML (from RichTextEditor) and plain text (legacy).
        // Rileva HTML in QUALSIASI posizione (non solo all'inizio). Prima la regex
        // matchava solo '^\s*<' → un paragrafo "Testo <strong>...</strong>" veniva
        // trattato come plain text e i tag finivano escapati come testo letterale.
        $content_raw = $s['content'] ?? '';
        if ( preg_match( '/<[a-z!\/][^>]*>/i', $content_raw ) ) {
            $content = $this->safe_richtext_content( $content_raw );
        } else {
            $content = nl2br( esc_html( $content_raw ) );
        }

        list( $tb_cls, $tb_data ) = $this->tfx_attrs( $s, 'content', wp_strip_all_tags( $content_raw ) );
        // Glitch: le copie del motore (::before/::after) mostrano data-fx-text, testo piano. Sulla
        // radice di un testo a più paragrafi coprivano il primo con tutto il contenuto appiattito
        // in una riga, un doppione illeggibile. L'effetto va su ogni blocco: ogni copia combacia
        // con il suo paragrafo (o voce, titolo, citazione).
        $tb_glitch = $tb_cls !== '' && ( $s['text_effect'] ?? '' ) === 'glitch';
        if ( $tb_glitch ) {
            $content = $this->glitch_per_blocco( $content );
            $tb_cls  = '';
            $tb_data = '';
        }
        $tb_uid = 'olo-tb-' . wp_unique_id();

        ob_start();
        ?>
        <div class="olo-text-block <?php echo $tb_uid; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $tb_uid is internally generated; tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr) ?><?php echo $tb_cls; ?>" style="<?php echo esc_attr( $style ); ?>"<?php echo $tb_data; ?>>
            <?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rich text sanitized via safe_richtext_content() (wp_kses_post) above, or esc_html()+nl2br for plain text; glitch_per_blocco() only adds fixed-class spans with an esc_attr()'d data-fx-text ?>
        </div>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $tb_uid );
        if ( $tfx_css && $tb_glitch ) {
            // Glitch per blocco: le regole del motore («.uid .olo-tfx--glitch») valgono già per gli
            // span dei blocchi. Lo span è un blocco come il suo paragrafo, così la copia va a capo
            // negli stessi punti (e il testo si divide ancora fra le colonne); pre-line rende gli
            // a capo (<br>) che data-fx-text porta come ritorni.
            $tfx_css .= '.' . $tb_uid . ' .olo-tfx--glitch{display:block;}'
                . '.' . $tb_uid . ' .olo-tfx--glitch::before,.' . $tb_uid . ' .olo-tfx--glitch::after{white-space:pre-line;}';
            echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Text_Effects::css() from whitelisted effects and fixed token colors; $tb_uid is internally generated
        } elseif ( $tfx_css ) {
            // Qui l'effetto sta sulla radice della tile (quella con l'uid), mentre il motore scrive
            // regole per un discendente («.uid .olo-tfx--…»): gradient, wave, sottolineatura ed
            // evidenziatore non agivano. Le regole passano alla radice stessa, che resta un
            // blocco (l'inline-block del motore, pensato per titoli e parole, ignorerebbe
            // «Larghezza max» al centro e le colonne).
            $tfx_css = str_replace( '.' . $tb_uid . ' .olo-tfx', '.' . $tb_uid . '.olo-tfx', $tfx_css )
                . '.' . $tb_uid . '.olo-tfx{display:block;}';
            echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Text_Effects::css() from whitelisted effects, sanitized colors and integer timings; $tb_uid is internally generated
        }
        $this->tfx_print_script();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$tb_uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$tb_uid}", $s['border'] ?? [], $s );
        $hover_br_rule_css = $hover_br_css ? ".{$tb_uid}:hover{border-radius:{$hover_br_css};}" : '';
        if ( $border_css || $border_hover_css || $border_effect_css || $hover_br_rule_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$tb_uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $tb_uid is internally generated
            echo $border_hover_css . $border_effect_css . $hover_br_rule_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base border builders and build_border_radius_css() (intval-based) from sanitized settings
        }
        return ob_get_clean();
    }

    /**
     * Glitch per blocco: avvolge il contenuto di ogni blocco «foglia» (paragrafo, voce, titolo,
     * citazione… senza altri blocchi dentro) nello span dell'effetto, col suo testo in
     * data-fx-text. Un testo senza blocchi (testo semplice, solo a capo) diventa un solo span.
     *
     * @param string $html Contenuto già sanificato (safe_richtext_content o esc_html+nl2br).
     * @return string
     */
    protected function glitch_per_blocco( $html ) {
        $blocchi = 'p|h[1-6]|li|blockquote|dt|dd|figcaption|td|th';
        $interno = '(?:(?!<\/?(?:' . $blocchi . '|ul|ol|dl|div|table|figure|pre|section|article)\b).)*?';
        $n       = 0;
        $out     = preg_replace_callback(
            '#<(' . $blocchi . ')(\s[^>]*)?>(' . $interno . ')</\1\s*>#is',
            function ( $m ) {
                return '<' . $m[1] . $m[2] . '>' . $this->glitch_span( $m[3] ) . '</' . $m[1] . '>';
            },
            $html,
            -1,
            $n
        );
        if ( ! is_string( $out ) ) {
            return $html; // regex in errore (contenuto enorme): il testo resta senza glitch, intero
        }
        return $n > 0 ? $out : $this->glitch_span( $html );
    }

    /**
     * Lo span del glitch attorno al contenuto di un blocco. Il testo della copia ha gli spazi
     * compattati come nella pagina e gli a capo (<br>) come ritorni. La modifica in linea del
     * canvas salva l'HTML del paragrafo, span compreso: quello salvato si toglie e si rifà, così
     * la copia segue il testo nuovo; se resta uno span del glitch dentro, non si avvolge due volte.
     *
     * @param string $inner HTML interno del blocco.
     * @return string
     */
    protected function glitch_span( $inner ) {
        if ( preg_match( '#^\s*<span class="olo-tfx olo-tfx--glitch"[^>]*>(.*)</span>\s*$#is', $inner, $mm ) ) {
            // Si toglie solo se avvolge davvero tutto il blocco: gli span interni si chiudono
            // tutti, e nessuna chiusura viene prima della sua apertura.
            $prof = 0;
            preg_match_all( '#<(/?)span\b#i', $mm[1], $tag );
            foreach ( $tag[1] as $chiude ) {
                $prof += $chiude === '/' ? -1 : 1;
                if ( $prof < 0 ) {
                    break;
                }
            }
            if ( $prof === 0 ) {
                $inner = $mm[1];
            }
        }
        if ( strpos( $inner, 'olo-tfx--glitch' ) !== false ) {
            return $inner;
        }
        $testo = preg_replace( '/[ \t\r\n]+/', ' ', $inner );
        $testo = preg_replace( '#\s*<br\s*/?>\s*#i', "\n", $testo );
        $testo = trim( wp_strip_all_tags( $testo ) );
        if ( $testo === '' ) {
            return $inner;
        }
        return '<span class="olo-tfx olo-tfx--glitch" data-fx-text="' . esc_attr( $testo ) . '">' . $inner . '</span>';
    }
}
