<?php
/**
 * Indice argomenti — l'elenco a lato di un manuale: gli argomenti e i sottoargomenti di un tipo di contenuto (quelli con
 * «Argomenti e sottoargomenti» nelle Impostazioni, o le Pagine), in ordine di lettura (Olobuild_Argomenti::albero()).
 * Il contenuto aperto è evidenziato e il suo ramo è aperto; gli altri rami si aprono con la freccia. Una casella cerca
 * fra i titoli. Sugli schermi piccoli l'indice sta chiuso sotto un pulsante (o aperto, a scelta). Per tenerlo in vista
 * mentre la pagina scorre si mette «Sticky» sulla sua colonna (lo sticky della colonna conosce la testata fissa): lì
 * l'indice, se è più alto della finestra, scorre da sé (frontend.css). Fuori da un contenuto del tipo
 * (nel builder, in una pagina qualunque) mostra l'albero intero.
 *
 * Interazione in assets/js/olo-argomenti.js (apri/chiudi, ricerca): senza JS l'indice resta leggibile e navigabile.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Argomenti_Tile extends Olobuild_Tile_Base {

    protected $type     = 'argomenti';
    protected $name     = 'Indice argomenti';
    protected $icon     = 'dashicons-list-view';
    protected $category = 'navigation';
    protected $defaults = [
        'fonte'             => '',
        'quali'             => 'tutti',
        'titolo'            => 'Argomenti',
        'cerca'             => true,
        'aperti'            => 'ramo',
        'telefono'          => 'chiuso',
        'text_color'        => '',
        'active_color'      => '',
        'active_bg'         => '',
        'line_color'        => '',
        'font_family'       => '',
        'font_size'         => 15,
        'font_weight'       => '400',
        'title_color'       => '',
        'title_size'        => 12,
        'title_weight'      => '700',
        'title_transform'   => 'uppercase',
        'item_radius'       => [ 'tl' => 8, 'tr' => 8, 'br' => 8, 'bl' => 8 ],
        'item_padding'      => [ 'top' => 6, 'right' => 10, 'bottom' => 6, 'left' => 10 ],
        'indent'            => 14,
        'gap'               => 2,
    ];

    public function get_controls() {
        return [];
    }

    /** Il tipo da mostrare: quello scelto, altrimenti quello del contenuto aperto (se ha argomenti), altrimenti le Pagine. */
    private function tipo( $s, $post ) {
        $scelto = sanitize_key( (string) ( $s['fonte'] ?? '' ) );
        if ( '' !== $scelto && Olobuild_Argomenti::ha_argomenti( $scelto ) ) {
            return $scelto;
        }
        if ( $post && Olobuild_Argomenti::ha_argomenti( $post->post_type ) ) {
            return $post->post_type;
        }
        // Nel builder (il template Singolo del tipo, senza un contenuto da guardare): il primo tipo con argomenti.
        foreach ( get_post_types( [ 'public' => true, 'hierarchical' => true ] ) as $t ) {
            if ( 'page' !== $t && 'attachment' !== $t ) {
                return $t;
            }
        }
        return 'page';
    }

    public function render( $settings ) {
        $s    = wp_parse_args( $settings, $this->defaults );
        // Il contenuto aperto solo in una pagina singola: in un archivio get_post() è il primo dell'elenco.
        $post = ( is_singular() || ! empty( $settings['_builder_mode'] ) ) ? get_post() : null;
        $tipo = $this->tipo( $s, $post );
        if ( ! class_exists( 'Olobuild_Argomenti' ) ) {
            return '';
        }
        $albero  = Olobuild_Argomenti::albero( $tipo );
        $attuale = ( $post && $post->post_type === $tipo ) ? (int) $post->ID : 0;
        $antenati = $attuale ? Olobuild_Argomenti::antenati( $post ) : [];

        // Quali argomenti: tutti, il solo volume del contenuto aperto (il ramo del suo antenato più alto), o i soli
        // sottoargomenti del contenuto aperto («In questa sezione»).
        $quali = in_array( $s['quali'], [ 'tutti', 'ramo', 'figli' ], true ) ? $s['quali'] : 'tutti';
        $radice = 0;
        if ( 'ramo' === $quali && $attuale ) {
            $radice = $antenati ? $antenati[0] : $attuale;
        } elseif ( 'figli' === $quali && $attuale ) {
            $radice = $attuale;
        }
        if ( $radice ) {
            $dentro = array_flip( Olobuild_Argomenti::ramo( $albero, $radice ) );
            $albero = array_values( array_filter( $albero, function ( $v ) use ( $dentro, $radice, $quali ) {
                return isset( $dentro[ $v['id'] ] ) && ( 'ramo' === $quali || $v['id'] !== $radice );
            } ) );
        }
        if ( ! $albero ) {
            return ! empty( $settings['_builder_mode'] )
                ? '<div class="olo-argomenti-vuoto">' . esc_html( olobuild_t( 'Ancora nessun argomento: scrivili nella Redazione.' ) ) . '</div>'
                : '';
        }

        $figli = [];
        foreach ( $albero as $v ) {
            $figli[ $v['genitore'] ][] = $v;
        }
        // La radice dell'albero mostrato: il genitore della prima voce.
        $cima        = $albero[0]['genitore'];
        $tutti_aperti = 'tutti' === $s['aperti'];
        $aperti      = array_flip( array_merge( $antenati, [ $attuale ] ) );

        $uid = 'olo-arg-' . substr( md5( wp_json_encode( $settings ) . $tipo . $attuale . wp_rand() ), 0, 8 );
        wp_enqueue_script( 'olo-argomenti', OLOBUILD_URL . 'assets/js/olo-argomenti.js', [], OLOBUILD_VERSION, true );

        $html = '<nav class="olo-argomenti ' . esc_attr( $uid )
            . ( 'chiuso' === $s['telefono'] ? ' olo-argomenti--telefono-chiuso' : '' ) . '" data-olo-argomenti aria-label="'
            . esc_attr( '' !== trim( (string) $s['titolo'] ) ? $s['titolo'] : olobuild_t( 'Argomenti' ) ) . '">';
        if ( 'chiuso' === $s['telefono'] ) {
            $qui   = $attuale ? html_entity_decode( apply_filters( 'the_title', $post->post_title, $attuale ), ENT_QUOTES, 'UTF-8' ) : '';
            $html .= '<button type="button" class="olo-arg-apri" aria-expanded="false" aria-controls="' . esc_attr( $uid ) . '-corpo">'
                . '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h10"/></svg>'
                . '<span class="olo-arg-apri-t">' . esc_html( '' !== trim( (string) $s['titolo'] ) ? $s['titolo'] : olobuild_t( 'Argomenti' ) ) . '</span>'
                . ( '' !== $qui ? '<span class="olo-arg-apri-qui">' . esc_html( $qui ) . '</span>' : '' )
                . '</button>';
        }
        $html .= '<div class="olo-arg-corpo" id="' . esc_attr( $uid ) . '-corpo">';
        if ( '' !== trim( (string) $s['titolo'] ) ) {
            $html .= '<p class="olo-arg-titolo">' . esc_html( $s['titolo'] ) . '</p>';
        }
        if ( ! empty( $s['cerca'] ) ) {
            $html .= '<div class="olo-arg-cerca"><svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/></svg>'
                . '<input type="search" placeholder="' . esc_attr( olobuild_t( 'Cerca negli argomenti' ) ) . '" aria-label="' . esc_attr( olobuild_t( 'Cerca negli argomenti' ) ) . '">'
                . '</div><p class="olo-arg-nessuno" hidden>' . esc_html( olobuild_t( 'Nessun argomento con queste parole.' ) ) . '</p>';
        }
        $html .= $this->lista( $figli, $cima, $attuale, $aperti, $tutti_aperti, 0 );
        $html .= '</div></nav>';

        return $this->stile( $s, $uid ) . $html;
    }

    /** Un livello dell'albero (e, dentro, i livelli sotto). */
    private function lista( $figli, $genitore, $attuale, $aperti, $tutti_aperti, $livello ) {
        if ( empty( $figli[ $genitore ] ) ) {
            return '';
        }
        $out = '<ul class="olo-arg-lista' . ( $livello ? ' olo-arg-sotto' : '' ) . '" role="list">';
        foreach ( $figli[ $genitore ] as $v ) {
            $ha_figli = ! empty( $figli[ $v['id'] ] );
            $aperta   = $ha_figli && ( $tutti_aperti || isset( $aperti[ $v['id'] ] ) );
            $attiva   = $v['id'] === $attuale;
            $classi   = 'olo-arg-voce' . ( $ha_figli ? ' has-figli' : '' ) . ( $aperta ? ' is-aperta' : '' ) . ( $attiva ? ' is-attiva' : '' )
                . ( ! $attiva && isset( $aperti[ $v['id'] ] ) ? ' is-ramo' : '' );
            $titolo   = '' !== $v['titolo'] ? $v['titolo'] : olobuild_t( '(senza titolo)' );
            $out     .= '<li class="' . $classi . '"><div class="olo-arg-riga">'
                . '<a href="' . esc_url( get_permalink( $v['post'] ) ) . '"' . ( $attiva ? ' aria-current="page"' : '' ) . '>' . esc_html( $titolo ) . '</a>';
            if ( $ha_figli ) {
                $out .= '<button type="button" class="olo-arg-freccia" aria-expanded="' . ( $aperta ? 'true' : 'false' ) . '" aria-label="'
                    /* translators: %s: titolo dell'argomento */
                    . esc_attr( sprintf( olobuild_t( 'Sottoargomenti di «%s»' ), $titolo ) ) . '">'
                    . '<svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg></button>';
            }
            $out .= '</div>' . ( $ha_figli ? $this->lista( $figli, $v['id'], $attuale, $aperti, $tutti_aperti, $livello + 1 ) : '' ) . '</li>';
        }
        return $out . '</ul>';
    }

    /** Lo stile della tile: colori dal tema, misure dai controlli (helper che restituiscono già l'unità). */
    private function stile( $s, $uid ) {
        $c       = '.' . $uid;
        $testo   = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text, #1d1d1f)';
        $attivo  = $this->safe_color_css( $s['active_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $fondo   = $this->safe_color_css( $s['active_bg'] ) ?: 'color-mix(in srgb, ' . $attivo . ' 9%, transparent)';
        $linea   = $this->safe_color_css( $s['line_color'] ) ?: 'var(--olo-color-border, #e5e5ea)';
        $tit_c   = $this->safe_color_css( $s['title_color'] ) ?: 'var(--olo-color-text-muted, #6e6e73)';
        $ff      = $this->resolve_font_family( (string) ( $s['font_family'] ?? '' ) );
        $ff_css  = ( $ff && 'inherit' !== $ff ) ? 'font-family:' . $ff . ';' : '';
        $fs      = max( 11, min( 24, intval( $s['font_size'] ) ?: 15 ) );
        $fw      = preg_match( '/^[1-9]00$/', (string) $s['font_weight'] ) ? $s['font_weight'] : '400';
        $ts      = max( 10, min( 24, intval( $s['title_size'] ) ?: 12 ) );
        $tw      = preg_match( '/^[1-9]00$/', (string) $s['title_weight'] ) ? $s['title_weight'] : '700';
        $tt      = in_array( $s['title_transform'], [ 'none', 'uppercase', 'lowercase', 'capitalize' ], true ) ? $s['title_transform'] : 'uppercase';
        $raggio  = $this->build_border_radius_css( $s['item_radius'] ?? null ) ?: '8px';
        $pad     = Olobuild_Tile_Utils::sides_css( Olobuild_Tile_Utils::spacing_sides( $s['item_padding'] ?? null, [], [ 6, 10, 6, 10 ] ) );
        $rientro = max( 0, min( 48, intval( $s['indent'] ) ) );
        $gap     = max( 0, min( 24, intval( $s['gap'] ) ) );

        $css  = $c . '{' . $ff_css . 'color:' . $testo . ';font-size:' . $fs . 'px;line-height:1.4;--olo-arg-attivo:' . $attivo . ';--olo-arg-linea:' . $linea . ';}';
        $css .= $c . ' .olo-arg-titolo{margin:0 0 10px;font-size:' . $ts . 'px;font-weight:' . $tw . ';text-transform:' . $tt . ';letter-spacing:' . ( 'uppercase' === $tt ? '.1em' : '0' ) . ';color:' . $tit_c . ';}';
        $css .= $c . ' .olo-arg-lista{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:' . $gap . 'px;}';
        $css .= $c . ' .olo-arg-sotto{margin:' . $gap . 'px 0 0 ' . $rientro . 'px;padding-left:8px;border-left:1px solid var(--olo-arg-linea);}';
        // color: il colore dei link del template (.olo-template a) pesa quanto .olo-arg-riga a: col codice della tile vince.
        $css .= $c . ' .olo-arg-riga a{color:inherit;border-radius:' . $raggio . ';padding:' . $pad . ';font-weight:' . $fw . ';}';
        $css .= $c . ' .olo-arg-voce.is-attiva > .olo-arg-riga a{color:' . $attivo . ';background:' . $fondo . ';font-weight:600;}';
        $css .= $c . ' .olo-arg-voce.is-ramo > .olo-arg-riga a{font-weight:600;}';
        return '<style>' . $css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- uid interno; colori da safe_color_css(), font da resolve_font_family(), misure intere o dagli helper
    }
}
