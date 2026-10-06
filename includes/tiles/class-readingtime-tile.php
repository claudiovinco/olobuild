<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Readingtime_Tile extends Olobuild_Tile_Base {

    protected $type     = 'readingtime';
    protected $name     = 'Tempo di Lettura';
    protected $icon     = 'dashicons-clock';
    protected $category = 'dynamic';
    protected $defaults = [
        'words_per_minute' => 200,
        'format'           => 'full',
        'prefix'           => 'Tempo di lettura:',
        'suffix'           => 'min',
        'icon'             => 'clock',
        'show_icon'        => true,
        'text_color'       => '',
        'icon_color'       => '',
        'font_size'        => '',
        'font_weight'      => '',
        'text_align'       => 'left',
        'border_width'     => '0',
        'border_color'     => '',
        'border_radius'    => '0',
        'box_shadow'       => '',
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
        return [];
    }

    /**
     * Tipi di nodo i cui settings non sono testo da leggere: i contenitori (solo stile) e le
     * tile che il tempo di lettura lo mostrano (il loro «Tempo di lettura:» non va contato).
     */
    const NODI_SENZA_TESTO = [ 'section', 'row', 'column', 'readingtime', 'postmeta' ];

    /**
     * Chiavi dei settings che non portano testo: colori, misure, media, link, codice, enum di
     * stile. Si confronta ogni pezzo del nome (title_color → «color»), non una sottostringa.
     */
    const CHIAVI_NON_TESTO = '/^_|(?:^|_)(?:colou?r|bg|background|shadow|gradient|font|family|weight|url|href|link|src|image|img|video|audio|poster|icon|svg|class|css|style|anim|animation|transition|transform|filter|mask|clip|path|code|html|script|embed|shortcode|json|id|slug|format|preset|variant|layout|align|position|size|width|height|radius|border|padding|margin|gap|easing|unit|target|rel|mode|source|endpoint|query|selector|lang|effect|aria|alt|placeholder|tag|ratio|fit|focal|overlay|opacity|blend|cursor|columns|speed|duration|delay|separator|prefix|suffix|date|type)(?:_|$)/i';

    /**
     * Minuti di lettura di un post: le parole dell'editor PIÙ quelle delle tile del template
     * Olobuild collegato alla pagina (_olo_template_id). Prima si contava solo post_content, che
     * su una pagina fatta col builder è vuoto o un segnaposto: il tempo diceva sempre «1 min»
     * anche su un articolo lungo. Le tile si leggono dai loro settings senza renderle (questa
     * tile sta dentro lo stesso template). Usata anche da Post Meta («Mostra tempo di lettura»).
     *
     * @param WP_Post|int $post
     * @param int         $wpm  parole al minuto
     * @return int minuti, almeno 1
     */
    public static function minuti_di_lettura( $post, $wpm = 200 ) {
        $wpm = max( 1, (int) $wpm );
        return max( 1, (int) ceil( self::parole_del_post( $post ) / $wpm ) );
    }

    /**
     * Parole del post (editor + template collegato), contate una volta per richiesta.
     *
     * @param WP_Post|int $post
     * @return int
     */
    public static function parole_del_post( $post ) {
        static $memo = [];
        $post = get_post( $post );
        if ( ! $post ) {
            return 0;
        }
        if ( isset( $memo[ $post->ID ] ) ) {
            return $memo[ $post->ID ];
        }
        // Tutto il post_content, non get_the_content(): quella dà solo la pagina corrente di
        // un articolo diviso con <!--nextpage-->. Gli shortcode non sono parole.
        $contenuto = (string) $post->post_content;
        $parole    = self::conta_parole( strip_shortcodes( $contenuto ) );
        // Il template collegato alla pagina e quelli inseriti con lo shortcode [olobuild_template id=…].
        $tpl_ids = [ (int) get_post_meta( $post->ID, '_olo_template_id', true ) ];
        if ( preg_match_all( '/\[(?:olobuild|olo|mosaic)_template\b[^\]]*?\bid\s*=\s*["\']?(\d+)/i', $contenuto, $m ) ) {
            $tpl_ids = array_merge( $tpl_ids, array_map( 'intval', $m[1] ) );
        }
        $tpl_ids = array_unique( array_filter( $tpl_ids ) );
        if ( $tpl_ids && class_exists( 'Olobuild_Database' ) ) {
            foreach ( $tpl_ids as $tpl_id ) {
                $tpl = Olobuild_Database::instance()->get_template( $tpl_id );
                if ( is_array( $tpl ) && ! empty( $tpl['content'] ) && is_array( $tpl['content'] ) ) {
                    $parole += self::parole_dei_nodi( $tpl['content'], 0 );
                }
            }
        }
        $memo[ $post->ID ] = $parole;
        return $parole;
    }

    /**
     * Parole di un testo (anche HTML). Si contano le sequenze di lettere e cifre, con apostrofo
     * e trattino interni: str_word_count() spezzava le parole accentate («perché», «città»)
     * e non contava i numeri.
     */
    private static function conta_parole( $testo ) {
        $testo = html_entity_decode( wp_strip_all_tags( (string) $testo ), ENT_QUOTES, 'UTF-8' );
        if ( trim( $testo ) === '' ) {
            return 0;
        }
        $n = preg_match_all( '/[\p{L}\p{N}]+(?:[\'’-][\p{L}\p{N}]+)*/u', $testo );
        if ( false === $n ) {
            // UTF-8 non valido: meglio un conteggio approssimato che zero.
            $n = str_word_count( $testo );
        }
        return (int) $n;
    }

    /**
     * Somma le parole dei nodi del template (sezioni → righe → colonne → tile, a ogni profondità).
     */
    private static function parole_dei_nodi( $nodi, $profondita ) {
        if ( ! is_array( $nodi ) || $profondita > 40 ) {
            return 0;
        }
        $n = 0;
        foreach ( $nodi as $nodo ) {
            if ( ! is_array( $nodo ) ) {
                continue;
            }
            $tipo = (string) ( $nodo['type'] ?? '' );
            if ( ! in_array( $tipo, self::NODI_SENZA_TESTO, true ) && ! empty( $nodo['settings'] ) && is_array( $nodo['settings'] ) ) {
                $n += self::parole_dei_valori( $nodo['settings'], 0 );
            }
            if ( ! empty( $nodo['children'] ) ) {
                $n += self::parole_dei_nodi( $nodo['children'], $profondita + 1 );
            }
        }
        return $n;
    }

    /**
     * Parole dei settings di una tile, voci dei ripetitori comprese: solo i valori che sono
     * testo (vedi e_testo()) sotto chiavi che non sono di stile, media o codice.
     */
    private static function parole_dei_valori( $valori, $profondita ) {
        if ( $profondita > 6 ) {
            return 0;
        }
        $n = 0;
        foreach ( $valori as $chiave => $valore ) {
            if ( is_string( $chiave ) && preg_match( self::CHIAVI_NON_TESTO, $chiave ) ) {
                continue;
            }
            if ( is_array( $valore ) ) {
                $n += self::parole_dei_valori( $valore, $profondita + 1 );
            } elseif ( is_string( $valore ) && self::e_testo( $valore ) ) {
                $n += self::conta_parole( $valore );
            }
        }
        return $n;
    }

    /**
     * Un valore è testo da leggere se, tolti i tag, ha almeno due parole e non è un valore CSS
     * («0 4px 12px», «center center», un gradiente, un var()) né un indirizzo. Le singole parole
     * restano fuori di proposito: sono quasi sempre enum («solid», «cover», «left»).
     */
    private static function e_testo( $valore ) {
        $t = trim( html_entity_decode( wp_strip_all_tags( $valore ), ENT_QUOTES, 'UTF-8' ) );
        if ( $t === '' || ! preg_match( '/\p{L}/u', $t ) || ! preg_match( '/\s/u', $t ) ) {
            return false;
        }
        if ( preg_match( '/[{}]|(?:var|rgba?|hsla?|calc|clamp|url|color-mix|(?:repeating-)?(?:linear|radial|conic)-gradient)\s*\(|^(?:https?:)?\/\//i', $t ) ) {
            return false;
        }
        $pezzi = preg_split( '/[\s,\/]+/u', $t, -1, PREG_SPLIT_NO_EMPTY );
        foreach ( (array) $pezzi as $p ) {
            if ( ! preg_match( '/^(?:-?[\d.]+(?:px|r?em|%|fr|vh|vw|deg|m?s)?|center|left|right|top|bottom|auto|none|solid|dashed|dotted|inherit|normal|cover|contain)$/i', $p ) ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Il suffisso al singolare quando i minuti sono 1: col suffisso «minuti» usciva «1 minuti».
     */
    private static function suffisso_per( $suffix, $minutes ) {
        if ( 1 !== (int) $minutes ) {
            return $suffix;
        }
        $singolari = [ 'minuti' => 'minuto', 'minutes' => 'minute', 'minuten' => 'minute', 'minutos' => 'minuto', 'mins' => 'min' ];
        $chiave    = strtolower( trim( (string) $suffix ) );
        if ( ! isset( $singolari[ $chiave ] ) ) {
            return $suffix;
        }
        $s = $singolari[ $chiave ];
        return ctype_upper( substr( trim( (string) $suffix ), 0, 1 ) ) ? ucfirst( $s ) : $s;
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-rt-' . wp_rand( 10000, 99999 );

        $wpm    = max( 50, min( 500, absint( $s['words_per_minute'] ) ) ) ?: 200;
        $format = in_array( $s['format'], [ 'full', 'short', 'minutes_only' ], true ) ? $s['format'] : 'full';
        $prefix = esc_html( $s['prefix'] );
        $align  = in_array( $s['text_align'], [ 'left', 'center', 'right' ], true ) ? $s['text_align'] : 'left';

        $show_icon  = filter_var( $s['show_icon'], FILTER_VALIDATE_BOOLEAN );
        $text_color = $this->safe_color_css( $s['text_color'] );
        $icon_color = $this->safe_color_css( $s['icon_color'] );
        // Il controllo Tipografia salva la dimensione come numero (16): scritta così, senza
        // unità, la dichiarazione era scartata e il testo restava della misura ereditata.
        $font_size  = esc_attr( Olobuild_Tile_Utils::css_len( $s['font_size'] ) );
        $font_weight = esc_attr( $s['font_weight'] );

        // Calcolo tempo lettura: editor + tile del template della pagina (vedi minuti_di_lettura()).
        $minutes = 0;
        $post = get_post();
        if ( $post ) {
            $minutes = self::minuti_di_lettura( $post, $wpm );
        }
        $suffix = esc_html( self::suffisso_per( $s['suffix'], $minutes ) );

        // Icona scelta nell'inspector: prima si disegnava sempre l'orologio. «clock» (il default)
        // resta l'SVG di sempre, così le tile salvate non cambiano aspetto.
        $icon_name = trim( (string) ( $s['icon'] ?? '' ) );
        $icon_html = '';
        if ( $show_icon && $icon_name !== '' && $icon_name !== 'clock' ) {
            $icon_html = $this->render_icon_html( $icon_name );
        }

        // Formatta testo
        if ( ! $post ) {
            $display_text = '&mdash;';
        } else {
            switch ( $format ) {
                case 'minutes_only':
                    $display_text = (string) $minutes;
                    break;
                case 'short':
                    $display_text = $minutes . ' ' . $suffix . ' ' . olobuild_t( 'di lettura' );
                    break;
                case 'full':
                default:
                    $display_text = $prefix . ' ' . $minutes . ' ' . $suffix;
                    break;
            }
        }

        // Stili
        $bw = intval( $s['border_width'] );
        $bc = $this->safe_color_css( $s['border_color'] ) ?: 'var(--olo-color-border, #e5e7eb)';
        $br = esc_attr( $s['border_radius'] );

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: the internally generated $uid, in_array() whitelisted align (and fixed-literal ternary), safe_color_css() colours, esc_attr()'d size/weight/radius and the intval()'d border width.
        ?>
        <style>
            .<?php echo $uid; ?> {
                display: flex;
                align-items: center;
                gap: 8px;
                text-align: <?php echo $align; ?>;
                justify-content: <?php echo $align === 'center' ? 'center' : ( $align === 'right' ? 'flex-end' : 'flex-start' ); ?>;
                padding: 8px 12px;
                <?php if ( $text_color ) : ?>color: <?php echo $text_color; ?>;<?php endif; ?>
                <?php if ( $font_size ) : ?>font-size: <?php echo $font_size; ?>;<?php endif; ?>
                <?php if ( $font_weight ) : ?>font-weight: <?php echo $font_weight; ?>;<?php endif; ?>
                <?php if ( $bw > 0 ) : ?>border: <?php echo (int) $bw; ?>px solid <?php echo $bc; ?>;<?php endif; ?>
                <?php if ( $br ) : ?>border-radius: <?php echo $br; ?>px;<?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-rt-icon svg {
                width: 1em;
                height: 1em;
                vertical-align: -0.125em;
                <?php if ( $icon_color ) : ?>color: <?php echo $icon_color; ?>;<?php endif; ?>
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-readingtime <?php echo esc_attr( $uid ); ?>">
            <?php if ( $show_icon && $icon_html !== '' ) : ?>
                <span class="olo-rt-icon" aria-hidden="true"><?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup from Olobuild_Tile_Base::render_icon_html(): esc_attr()'d icon name, sanitized custom SVG or the bundled Lucide SVG ?></span>
            <?php elseif ( $show_icon ) : ?>
                <span class="olo-rt-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </span>
            <?php endif; ?>
            <span class="olo-rt-text"><?php echo $display_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- assembled above from esc_html()'d prefix/suffix, an integer minute count, a translated fixed literal or the '&mdash;' entity ?></span>
        </div>
        <?php
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }
}
