<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Ricerca con filtri (tipo `tripfinder`) — barra di ricerca: N campi + pulsante.
 * Nata dai blueprint OLOthemes (TripFinder/BookingBar) come solo disegno; dalla 1.4.505
 * cerca davvero: è un form GET. Ogni campo dice cosa filtra (`filter`):
 *   text      → testo libero, parametro `s` (la ricerca di WordPress)
 *   category  → `category_name` · tag → `tag` · taxonomy → la query var della tassonomia
 *   post_type → `post_type`
 *   ''        → un parametro col nome scelto (`param`, vuoto = l'etichetta in minuscolo):
 *               lo legge la pagina di destinazione (contenuti dinamici, condizioni).
 * Le opzioni si scrivono a mano («Etichetta|valore») o vengono dal sito (`auto_options`:
 * termini della tassonomia, tipi di contenuto). La voce uguale al valore predefinito
 * non filtra. `button_url` vuoto o «#» = risultati della ricerca del sito.
 * I campi salvati prima (senza `filter`) diventano parametri col nome dell'etichetta.
 * Select nativi, nessun JS.
 */
class Olobuild_TripFinder_Tile extends Olobuild_Tile_Base {

    protected $type     = 'tripfinder';
    protected $name     = 'Ricerca con filtri';
    protected $icon     = 'dashicons-search';
    protected $category = 'interactive';
    protected $defaults = [
        'fields' => [
            [ 'label' => 'Cosa cerchi', 'filter' => 'text', 'value' => 'Scrivi una parola', 'options' => '' ],
            [ 'label' => 'Categoria', 'filter' => 'category', 'auto_options' => true, 'value' => 'Tutte le categorie', 'options' => '' ],
            [ 'label' => 'Tipo', 'filter' => 'post_type', 'value' => 'Tutto', 'options' => "Tutto\nArticoli|post\nPagine|page" ],
        ],
        'button_text'  => 'Cerca',
        'button_url'   => '',
        'accent'       => '',
        'accent_on'    => 'var(--olo-color-surface, #ffffff)',
        'bar_bg'       => '',
        'field_bg'     => '',
        'field_border' => '',
        'label_color'  => '',
        'value_color'  => '',
        'radius'       => 14,

        // SPAZIATURA additiva — default = padding storico (barra 8px, campi 10/16).
        'bar_padding'   => [ 'top' => 8, 'right' => 8, 'bottom' => 8, 'left' => 8 ],
        'field_padding' => [ 'top' => 10, 'right' => 16, 'bottom' => 10, 'left' => 16 ],

        // FORMA additiva — raggio per-angolo override. Tutto 0 → usa `radius` (no-op).
        'radius_corners' => [ 'tl' => 0, 'tr' => 0, 'br' => 0, 'bl' => 0 ],

        // KIT standard OLObuild — sfondo completo + ombra + bordo sul contenitore.
        // Default no-op: bg none / shadow none / border 0 → render invariato.
        'bg'                      => [ 'type' => 'none' ],
        'shadow'                  => 'none',
        'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'otf-' . wp_rand( 10000, 99999 );

        $accent = $this->safe_color_css( $s['accent'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $on     = $this->safe_color_css( $s['accent_on'] ?? '' ) ?: '#ffffff';
        $barbg  = $this->safe_color_css( $s['bar_bg'] ?? '' ) ?: 'var(--olo-color-surface, #ffffff)';
        $fbg    = $this->safe_color_css( $s['field_bg'] ?? '' ) ?: 'transparent';
        $fbd    = Olobuild_Tile_Utils::border_color( $s['field_border'] ?? null, 'var(--olo-color-border, #e5e7eb)' );
        $lab    = $this->safe_color_css( $s['label_color'] ?? '' ) ?: 'var(--olo-color-text-muted, #6b7280)';
        $val    = $this->safe_color_css( $s['value_color'] ?? '' ) ?: 'var(--olo-color-text, #111827)';
        $rad    = Olobuild_Tile_Utils::border_radius( $s['radius'] ?? 14 ) ?: '0';
        $sans   = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";

        // FORMA: raggio per-angolo override. '' (tutti 0) → usa $rad uniforme storico.
        $rad_corners = $this->build_border_radius_css( $s['radius_corners'] ?? [] );
        $rad_eff     = ( $rad_corners !== '' ) ? $rad_corners : $rad;

        // SPAZIATURA: padding barra/campi. Default = valori storici → render invariato.
        $bar_pad   = $this->tf_pad_css( $s['bar_padding'] ?? [], [ 8, 8, 8, 8 ] );
        $field_pad = $this->tf_pad_css( $s['field_padding'] ?? [], [ 10, 16, 10, 16 ] );

        $fields = is_array( $s['fields'] ) ? array_values( $s['fields'] ) : [];
        if ( empty( $fields ) ) return '';

        // ── KIT standard OLObuild: sfondo completo + ombra + bordo sul contenitore ──
        // Sfondo completo (override SOLO se valorizzato → default invariato).
        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olobuild_CSS_Builder' ) ) {
            $bg_decl = ( new Olobuild_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        // Ombra (preset/custom). '' se none.
        $shadow_css = $this->build_shadow_decl( $s );
        // Bordo (come la coda di particlefx render).
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );

        // Decorazioni inline per la regola del contenitore .$uid (no-op coi default).
        $box_decl = '';
        if ( $bg_decl )    { $box_decl .= $bg_decl . ';'; }
        if ( $border_css ) { $box_decl .= $border_css; }
        if ( $shadow_css ) { $box_decl .= 'box-shadow:' . $shadow_css . ';'; }
        // position:relative serve agli effetti bordo (come in particlefx).
        if ( $box_decl || $border_effect_css ) { $box_decl .= 'position:relative;'; }

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: every colour via the safe_color_css() whitelist (with fixed var() fallbacks), radius/padding via intval() helpers, box decorations via the Olobuild_CSS_Builder/Olobuild_Tile_Base shared helpers (sanitized internally), fixed font-stack literal; $uid is internally generated. ?>
        <style>
            <?php echo Olobuild_CSS_Builder::pattern_layer_css( $bg_decl, '.' . $uid ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- regole fisse di pattern_layer_css(): il selettore è l'uid della tile, i valori sono variabili CSS ?>
            .<?php echo $uid; ?>{font-family:<?php echo $sans; ?>;<?php echo $box_decl; ?>}
            .<?php echo $uid; ?> .otf-bar{display:flex;flex-wrap:wrap;align-items:stretch;gap:0;background:<?php echo $barbg; ?>;<?php echo esc_attr( Olobuild_Tile_Utils::border_css( $s['field_border'] ?? null, [ 'width' => 1, 'color' => $fbd ] ) ); ?>border-radius:<?php echo $rad_eff; ?>;padding:<?php echo $bar_pad; ?>;box-shadow:0 18px 50px -28px rgba(0,0,0,.35);transition:box-shadow .2s;}
            .<?php echo $uid; ?> .otf-f{flex:1 1 160px;display:flex;flex-direction:column;gap:4px;padding:<?php echo $field_pad; ?>;background:<?php echo $fbg; ?>;border-left:1px solid <?php echo $fbd; ?>;min-width:0;}
            .<?php echo $uid; ?> .otf-f:first-child{border-left:0;}
            .<?php echo $uid; ?> .otf-lab{font-size:11px;font-weight:600;letter-spacing:.12em;text-transform:uppercase;color:<?php echo $lab; ?>;}
            .<?php echo $uid; ?> .otf-sel{font-family:<?php echo $sans; ?>;font-size:15px;font-weight:600;color:<?php echo $val; ?>;background:transparent;border:0;padding:2px 0;cursor:pointer;width:100%;appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23999' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right center;padding-right:18px;}
            <?php // Il focus si vede sulla barra intera (alone col suo raggio) e sull'etichetta del campo attivo:
            // il contorno sul campo, trasparente e senza bordo, era un rettangolo dentro il segmento. ?>
            .<?php echo $uid; ?> .otf-bar:focus-within{box-shadow:0 0 0 3px color-mix(in srgb, <?php echo $accent; ?> 30%, transparent),0 18px 50px -28px rgba(0,0,0,.35);}
            .<?php echo $uid; ?> .otf-f:focus-within .otf-lab{color:<?php echo $accent; ?>;}
            .<?php echo $uid; ?> .otf-txt{background-image:none;padding-right:0;cursor:text;}
            .<?php echo $uid; ?> .otf-txt::placeholder{color:<?php echo $val; ?>;opacity:.5;}
            .<?php echo $uid; ?> .otf-btn{font-family:inherit;flex:0 0 auto;display:inline-flex;align-items:center;justify-content:center;gap:8px;margin-left:8px;padding:0 26px;background:<?php echo $accent; ?>;color:<?php echo $on; ?>;font-weight:700;font-size:14px;letter-spacing:.02em;border:0;border-radius:<?php echo $rad_eff; ?>;text-decoration:none;cursor:pointer;transition:transform .18s,filter .18s;}
            .<?php echo $uid; ?> .otf-btn:hover{transform:translateY(-1px);filter:brightness(1.05);}
            .<?php echo $uid; ?> .otf-btn:focus-visible{outline:2px solid <?php echo $accent; ?>;outline-offset:3px;}
            .<?php echo $uid; ?> .otf-btn svg{width:16px;height:16px;}
            @media (max-width:680px){.<?php echo $uid; ?> .otf-f{flex:1 1 100%;border-left:0;border-top:1px solid <?php echo $fbd; ?>;}.<?php echo $uid; ?> .otf-f:first-child{border-top:0;}.<?php echo $uid; ?> .otf-btn{flex:1 1 100%;margin:8px 0 0;padding:14px 26px;}}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php
        // Dove va la ricerca: vuoto o «#» = risultati della ricerca del sito.
        $dest      = trim( (string) ( $s['button_url'] ?? '' ) );
        $al_sito   = ( '' === $dest || '#' === $dest );
        $action    = $al_sito ? home_url( '/' ) : $dest;
        // Un form GET sostituisce la query dell'indirizzo di destinazione: i suoi
        // parametri viaggiano come campi nascosti.
        $nascosti  = [];
        $query_url = (string) wp_parse_url( $action, PHP_URL_QUERY );
        if ( '' !== $query_url ) {
            wp_parse_str( $query_url, $nascosti );
        }
        $ha_testo = false;
        foreach ( $fields as $f ) {
            if ( 'text' === ( $f['filter'] ?? '' ) ) { $ha_testo = true; }
        }
        // Nel canvas del builder il form non deve portare via l'anteprima.
        $blocca = ! empty( $s['_builder_mode'] ) ? ' onsubmit="return false"' : '';
        ?>
        <form class="olo-tripfinder <?php echo esc_attr( $uid ); ?>" role="search" method="get" action="<?php echo esc_url( $action ); ?>"<?php echo $blocca; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed literal ?>>
            <?php foreach ( $nascosti as $nk => $nv ) : if ( ! is_scalar( $nv ) ) continue; ?>
                <input type="hidden" name="<?php echo esc_attr( $nk ); ?>" value="<?php echo esc_attr( $nv ); ?>">
            <?php endforeach; ?>
            <?php if ( $al_sito && ! $ha_testo ) : // senza `s` WordPress mostrerebbe un archivio, non i risultati ?>
                <input type="hidden" name="s" value="">
            <?php endif; ?>
            <div class="otf-bar olo-casella">
                <?php foreach ( $fields as $f ) :
                    $flabel = isset( $f['label'] ) ? (string) $f['label'] : '';
                    $fval   = isset( $f['value'] ) ? trim( (string) $f['value'] ) : '';
                    $filtro = $this->tf_filtro( $f );
                    $nome   = $this->tf_nome( $f, $filtro );
                    $ora    = $this->tf_valore_attuale( $nome );
                ?>
                    <label class="otf-f">
                        <span class="otf-lab"><?php echo esc_html( $flabel ); ?></span>
                        <?php if ( 'text' === $filtro ) : ?>
                            <input class="otf-sel otf-txt" type="search" name="s" value="<?php echo esc_attr( $ora ); ?>" placeholder="<?php echo esc_attr( $fval ); ?>" aria-label="<?php echo esc_attr( $flabel ); ?>">
                        <?php else :
                            $opts = $this->tf_opzioni( $f, $filtro, $fval );
                            // Scelta già fatta (pagina dei risultati) o, se non c'è, la voce iniziale.
                            $scelta = null;
                            if ( '' !== $ora ) {
                                foreach ( $opts as $i => $o ) { if ( $o[1] === $ora ) { $scelta = $i; break; } }
                            }
                            if ( null === $scelta ) {
                                foreach ( $opts as $i => $o ) { if ( $o[0] === $fval ) { $scelta = $i; break; } }
                            }
                        ?>
                            <select class="otf-sel"<?php echo '' !== $nome ? ' name="' . esc_attr( $nome ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- name esc_attr()'d inline ?> aria-label="<?php echo esc_attr( $flabel ); ?>">
                                <?php foreach ( $opts as $i => $o ) : ?>
                                    <option value="<?php echo esc_attr( $o[1] ); ?>"<?php echo ( $i === $scelta ) ? ' selected' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed ' selected'/'' literal from the ternary ?>><?php echo esc_html( $o[0] ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </label>
                <?php endforeach; ?>
                <button class="otf-btn" type="submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                    <?php echo esc_html( $s['button_text'] ?: olobuild_t( 'Cerca' ) ); ?>
                </button>
            </div>
        </form>
        <?php
        // ── Sistema bordi standard: hover + effetto (come particlefx) ──────
        if ( $border_hover_css || $border_effect_css ) {
            echo '<style>' . $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }

    /** Cosa filtra un campo. Sconosciuto o assente (campi salvati prima della 1.4.505) = parametro. */
    private function tf_filtro( $f ) {
        $filtro = (string) ( $f['filter'] ?? '' );
        return in_array( $filtro, [ 'text', 'category', 'tag', 'taxonomy', 'post_type' ], true ) ? $filtro : '';
    }

    /** Il nome del parametro che il campo invia. '' = il campo non invia niente (tassonomia inesistente). */
    private function tf_nome( $f, $filtro ) {
        switch ( $filtro ) {
            case 'text':      return 's';
            case 'category':  return 'category_name';
            case 'tag':       return 'tag';
            case 'post_type': return 'post_type';
            case 'taxonomy':
                $tax = get_taxonomy( sanitize_key( (string) ( $f['taxonomy'] ?? '' ) ) );
                return ( $tax && ! empty( $tax->query_var ) ) ? (string) $tax->query_var : '';
        }
        $param = preg_replace( '/[^A-Za-z0-9_\-]/', '', (string) ( $f['param'] ?? '' ) );
        return '' !== $param ? $param : sanitize_title( (string) ( $f['label'] ?? '' ) );
    }

    /** Il valore già scelto (nella pagina dei risultati il form ripropone le scelte). */
    private function tf_valore_attuale( $nome ) {
        if ( '' === $nome ) {
            return '';
        }
        if ( 's' === $nome ) {
            return get_search_query( false );
        }
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only del parametro GET che questo stesso form invia, per riproporre la scelta; nessuna modifica di stato; valore sanitizzato.
        return isset( $_GET[ $nome ] ) && is_scalar( $_GET[ $nome ] ) ? sanitize_text_field( wp_unslash( $_GET[ $nome ] ) ) : '';
    }

    /**
     * Le opzioni di un campo a scelta: [ [ etichetta, valore ], … ].
     * Dal sito (`auto_options`): la voce iniziale che non filtra + termini o tipi di contenuto.
     * A mano: una per riga, «Etichetta|valore»; senza valore, per categorie, tag e
     * tassonomie lo slug dell'etichetta, per il resto l'etichetta. La voce uguale al
     * valore predefinito non filtra (valore '').
     */
    private function tf_opzioni( $f, $filtro, $iniziale ) {
        $out = [];
        if ( ! empty( $f['auto_options'] ) && in_array( $filtro, [ 'category', 'tag', 'taxonomy', 'post_type' ], true ) ) {
            $out[] = [ '' !== $iniziale ? $iniziale : olobuild_t( 'Tutti' ), '' ];
            if ( 'post_type' === $filtro ) {
                foreach ( get_post_types( [ 'public' => true, 'exclude_from_search' => false ], 'objects' ) as $pt ) {
                    if ( 'attachment' === $pt->name ) { continue; }
                    $out[] = [ (string) $pt->labels->name, (string) $pt->name ];
                }
                return $out;
            }
            $tax = 'category' === $filtro ? 'category' : ( 'tag' === $filtro ? 'post_tag' : sanitize_key( (string) ( $f['taxonomy'] ?? '' ) ) );
            if ( taxonomy_exists( $tax ) ) {
                $termini = get_terms( [ 'taxonomy' => $tax, 'hide_empty' => true, 'orderby' => 'name', 'number' => 200 ] );
                if ( is_array( $termini ) ) {
                    foreach ( $termini as $term ) { $out[] = [ (string) $term->name, (string) $term->slug ]; }
                }
            }
            return $out;
        }
        $slug = in_array( $filtro, [ 'category', 'tag', 'taxonomy' ], true );
        foreach ( preg_split( '/\r\n|\r|\n/', (string) ( $f['options'] ?? '' ) ) as $riga ) {
            $riga = trim( $riga );
            if ( '' === $riga ) { continue; }
            if ( false !== strpos( $riga, '|' ) ) {
                list( $et, $va ) = array_map( 'trim', explode( '|', $riga, 2 ) );
                $out[] = [ $et, $va ];
                continue;
            }
            if ( $riga === $iniziale ) {
                $out[] = [ $riga, '' ];
                continue;
            }
            $out[] = [ $riga, $slug ? sanitize_title( $riga ) : ( 'post_type' === $filtro ? sanitize_key( $riga ) : $riga ) ];
        }
        return $out;
    }

    /**
     * Spaziatura → stringa CSS "Tpx Rpx Bpx Lpx". Il setting è un oggetto
     * { top, right, bottom, left }. Se assente/non valido usa il fallback storico
     * (così il render coi default resta invariato). Additivo e no-op sui default.
     *
     * @param mixed $pad      Oggetto spacing { top, right, bottom, left }.
     * @param array $fallback [top, right, bottom, left] storici.
     * @return string CSS shorthand del padding.
     */
    private function tf_pad_css( $pad, $fallback ) {
        $top    = isset( $pad['top'] )    ? intval( $pad['top'] )    : $fallback[0];
        $right  = isset( $pad['right'] )  ? intval( $pad['right'] )  : $fallback[1];
        $bottom = isset( $pad['bottom'] ) ? intval( $pad['bottom'] ) : $fallback[2];
        $left   = isset( $pad['left'] )   ? intval( $pad['left'] )   : $fallback[3];
        return "{$top}px {$right}px {$bottom}px {$left}px";
    }

    /**
     * Restituisce la dichiarazione box-shadow (valore, senza "box-shadow:")
     * dal setting shadow (preset sm/md/lg/xl o custom). '' se none.
     * Copiato dal pattern standard OLObuild (cfr. Olobuild_Particlefx_Tile).
     */
    private function build_shadow_decl( $s ) {
        $preset = $s['shadow'] ?? 'none';
        if ( $preset === 'none' || $preset === '' ) {
            return '';
        }
        if ( $preset === 'custom' ) {
            $h      = intval( $s['shadow_h'] ?? 0 );
            $v      = intval( $s['shadow_v'] ?? 4 );
            $blur   = max( 0, intval( $s['shadow_blur'] ?? 10 ) );
            $spread = intval( $s['shadow_spread'] ?? 0 );
            $color  = $this->safe_color_css( $s['shadow_color'] ?? '' ) ?: 'rgba(0,0,0,0.15)';
            $inset  = ! empty( $s['shadow_inset'] ) ? 'inset ' : '';
            return "{$inset}{$h}px {$v}px {$blur}px {$spread}px {$color}";
        }
        $map = [
            'sm' => '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
            'md' => '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
            'lg' => '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
            'xl' => '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
        ];
        return $map[ $preset ] ?? '';
    }
}
