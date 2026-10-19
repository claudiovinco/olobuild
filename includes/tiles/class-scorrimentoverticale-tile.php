<?php
/**
 * Scorrimento verticale — colonne di voci (immagini, icone, testi, separatori) che scorrono senza fine,
 * ognuna verso l'alto o verso il basso (il «Vertical Marquee» di pixfort).
 *
 * Ogni colonna è un nastro con le sue voci ripetute due volte: il nastro sale (o scende) di metà della
 * sua altezza e ricomincia, e la seconda copia prende il posto della prima senza scatti. Tutto CSS:
 * niente script, va anche nelle tile pigre e nel canvas del builder. Se le voci sono poche per riempire
 * l'altezza scelta, si ripetono dentro la copia (stima per difetto delle loro altezze), e la durata
 * cresce con loro: la velocità resta quella della «Durata del ciclo».
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Scorrimentoverticale_Tile extends Olobuild_Tile_Base {

    protected $type     = 'scorrimentoverticale';
    protected $name     = 'Scorrimento verticale';
    protected $icon     = 'dashicons-sort';
    protected $category = 'media';
    protected $defaults = [
        'voci'                     => [
            [ 'tipo' => 'testo', 'testo' => 'Scorrimento verticale', 'colonna' => 'auto' ],
            [ 'tipo' => 'separatore', 'colonna' => 'auto' ],
            [ 'tipo' => 'testo', 'testo' => 'Novità', 'colonna' => 'auto' ],
        ],
        'colonne'                  => '3',
        'durata'                   => 24,
        'direzione_1'              => 'giu',
        'direzione_2'              => 'su',
        'direzione_3'              => 'giu',
        'direzione_4'              => 'su',
        'pausa_hover'              => true,
        'altezza'                  => 420,
        'gap'                      => 40,
        'gap_colonne'              => 24,
        'sfumatura_bordi'          => 18,
        'allineamento'             => 'center',
        'testo_font_family'        => '',
        'testo_font_size'          => 40,
        'testo_font_size_tablet'   => '',
        'testo_font_size_mobile'   => '',
        'testo_font_weight'        => '700',
        'testo_colore'             => '',
        'testo_sfumatura'          => false,
        'testo_colore_fine'        => '',
        'icona_dimensione'         => 44,
        'icona_riquadro'           => 104,
        'icona_sfondo'             => '',
        'icona_colore'             => '',
        'icona_raggio'             => 24,
        'immagine_altezza'         => 104,
        'immagine_raggio'          => 0,
        'separatore_colore'        => '',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-vmq-' . wp_rand( 100000, 999999 );
        $sel = '.' . $uid;

        $voci = is_array( $s['voci'] ) ? array_values( array_filter( $s['voci'], 'is_array' ) ) : [];
        if ( ! $voci ) {
            return ! empty( $s['_builder_mode'] )
                ? '<div class="olo-vmq-vuoto" style="padding:1.5em;text-align:center;opacity:.6">' . esc_html( olobuild_t( 'Aggiungi le voci da far scorrere' ) ) . '</div>'
                : '';
        }

        $n       = max( 1, min( 4, intval( $s['colonne'] ) ) );
        $durata  = max( 3, min( 200, floatval( $s['durata'] ) ) );
        $altezza = max( 120, min( 1600, intval( $s['altezza'] ) ) );
        $gap     = max( 0, min( 200, intval( $s['gap'] ) ) );
        $gap_col = max( 0, min( 200, intval( $s['gap_colonne'] ) ) );
        $bordi   = max( 0, min( 45, intval( $s['sfumatura_bordi'] ) ) );
        $allinea = [ 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' ][ (string) $s['allineamento'] ] ?? 'center';
        $fs      = max( 10, min( 200, floatval( $s['testo_font_size'] ) ) );
        $ico     = max( 12, min( 200, intval( $s['icona_dimensione'] ) ) );
        $riq     = max( $ico, min( 300, intval( $s['icona_riquadro'] ) ) );
        $img_h   = max( 16, min( 400, intval( $s['immagine_altezza'] ) ) );

        // Le voci nelle colonne: chi ne ha scelta una (che esiste) ci va, le altre a turno. Una voce
        // assegnata a una colonna tolta (es. la 4ª con 3 colonne) torna nel turno invece di sparire.
        $col   = array_fill( 0, $n, [] );
        $turno = 0;
        foreach ( $voci as $v ) {
            $c = isset( $v['colonna'] ) ? (string) $v['colonna'] : 'auto';
            if ( preg_match( '/^[1-4]$/', $c ) && (int) $c <= $n ) {
                $col[ (int) $c - 1 ][] = $v;
            } else {
                $col[ $turno % $n ][] = $v;
                $turno++;
            }
        }

        // L'altezza più grande fra computer e dispositivi: la copia delle voci deve superarla.
        $alt_disp = [];
        foreach ( [ 'tablet_landscape' => 1200, 'tablet' => 960, 'mobile_landscape' => 640, 'mobile' => 480 ] as $d => $px ) {
            $v = isset( $s[ 'altezza_' . $d ] ) ? trim( (string) $s[ 'altezza_' . $d ] ) : '';
            if ( '' !== $v ) {
                $alt_disp[ $px ] = max( 120, min( 1600, intval( $v ) ) );
            }
        }
        $alt_max = max( array_merge( [ $altezza ], array_values( $alt_disp ) ) );

        // Stima per difetto dell'altezza di una voce (un testo su una riga sola): con la stima bassa
        // le ripetizioni sono di più, mai di meno.
        $stima = function ( $v ) use ( $fs, $riq, $img_h, $gap ) {
            $t = $v['tipo'] ?? 'testo';
            $h = 'immagine' === $t ? $img_h : ( 'icona' === $t ? $riq : ( 'separatore' === $t ? 18 : $fs * 1.15 ) );
            return $h + $gap;
        };

        $colonne_html = '';
        $css_col      = '';
        foreach ( $col as $i => $lista ) {
            $dir = ( 'su' === ( $s[ 'direzione_' . ( $i + 1 ) ] ?? '' ) ) ? 'su' : 'giu';
            if ( ! $lista ) {
                $colonne_html .= '<div class="olo-vmq-col"></div>';
                continue;
            }
            $h_lista = 0;
            foreach ( $lista as $v ) {
                $h_lista += $stima( $v );
            }
            // ripetizioni dentro la copia: la copia deve superare l'altezza della tile di almeno una voce
            $volte = max( 1, min( 12, (int) ceil( ( $alt_max + $gap + 40 ) / max( 1, $h_lista ) ) ) );
            $copia = '';
            for ( $r = 0; $r < $volte; $r++ ) {
                foreach ( $lista as $v ) {
                    $copia .= $this->voce( $v, $s, false );
                }
            }
            $copia_nascosta = '';
            for ( $r = 0; $r < $volte; $r++ ) {
                foreach ( $lista as $v ) {
                    $copia_nascosta .= $this->voce( $v, $s, true );
                }
            }
            $colonne_html .= '<div class="olo-vmq-col olo-vmq-' . $dir . '" style="--olo-vmq-d:' . esc_attr( self::num( $durata * $volte ) ) . 's">'
                . '<div class="olo-vmq-nastro">'
                . '<div class="olo-vmq-copia">' . $copia . '</div>'
                . '<div class="olo-vmq-copia" aria-hidden="true">' . $copia_nascosta . '</div>'
                . '</div></div>';
        }

        // ── CSS ──
        $testo   = $this->safe_color_css( $s['testo_colore'] ) ?: 'var(--olo-color-text, #16263d)';
        $fine    = $this->safe_color_css( $s['testo_colore_fine'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $sfuma   = ! empty( $s['testo_sfumatura'] );
        $i_col   = $this->safe_color_css( $s['icona_colore'] ) ?: 'currentColor';
        $i_bg    = $this->safe_color_css( $s['icona_sfondo'] );
        $sep_col = $this->safe_color_css( $s['separatore_colore'] ) ?: 'var(--olo-color-text-muted, #94a3b8)';
        $i_rag   = Olobuild_Tile_Utils::border_radius( $s['icona_raggio'] );
        $img_rag = Olobuild_Tile_Utils::border_radius( $s['immagine_raggio'] );

        $css  = "@keyframes {$uid}-su{from{transform:translateY(0)}to{transform:translateY(-50%)}}";
        $css .= "{$sel}{display:grid;grid-template-columns:repeat({$n},minmax(0,1fr));column-gap:{$gap_col}px;height:{$altezza}px;overflow:hidden;";
        if ( $bordi > 0 ) {
            // i bordi in alto e in basso sfumano nel fondo della sezione, come nell'esempio di pixfort
            $maschera = "linear-gradient(to bottom,transparent 0,#000 {$bordi}%,#000 " . ( 100 - $bordi ) . '%,transparent 100%)';
            $css     .= "-webkit-mask-image:{$maschera};mask-image:{$maschera};";
        }
        $css .= '}';
        $css .= "{$sel} .olo-vmq-col{position:relative;min-width:0;overflow:hidden}";
        $css .= "{$sel} .olo-vmq-nastro{display:flex;flex-direction:column;animation:{$uid}-su var(--olo-vmq-d,24s) linear infinite;will-change:transform}";
        $css .= "{$sel} .olo-vmq-giu .olo-vmq-nastro{animation-direction:reverse}";
        // padding sotto la copia = gap: le due copie si danno il cambio alla stessa distanza delle voci
        $css .= "{$sel} .olo-vmq-copia{display:flex;flex-direction:column;align-items:{$allinea};gap:{$gap}px;padding-bottom:{$gap}px}";
        if ( ! empty( $s['pausa_hover'] ) ) {
            $css .= "{$sel}:hover .olo-vmq-nastro{animation-play-state:paused}";
        }
        // con la tastiera su un link la colonna si ferma, sempre: un link che scappa non si segue
        $css .= "{$sel}:focus-within .olo-vmq-nastro{animation-play-state:paused}";
        $css .= "@media (prefers-reduced-motion:reduce){{$sel} .olo-vmq-nastro{animation:none}}";
        $css .= "{$sel} .olo-vmq-voce{display:block;flex:none;max-width:100%;text-decoration:none;color:inherit}";
        $css .= "{$sel} a.olo-vmq-voce:focus-visible{outline:2px solid var(--olo-color-primary,#e1474f);outline-offset:4px;border-radius:.25em}";
        // testi
        $css .= "{$sel} .olo-vmq-testo{line-height:1.15;text-align:" . ( 'flex-start' === $allinea ? 'left' : ( 'flex-end' === $allinea ? 'right' : 'center' ) ) . ';overflow-wrap:break-word;-webkit-hyphens:auto;hyphens:auto;color:' . $testo . '}';
        if ( $sfuma ) {
            $css .= "{$sel} .olo-vmq-testo{background-image:linear-gradient(90deg,var(--olo-vmq-c," . $testo . "),{$fine});-webkit-background-clip:text;background-clip:text;color:transparent;-webkit-text-fill-color:transparent}";
        }
        $css .= $this->css_testo( $s, "{$sel} .olo-vmq-testo" );
        // icone
        $css .= "{$sel} .olo-vmq-icona{display:flex;align-items:center;justify-content:center;width:{$riq}px;height:{$riq}px;color:{$i_col}"
            . ( '' !== $i_bg ? ";background:{$i_bg}" : '' ) . ( '' !== $i_rag ? ";border-radius:{$i_rag}" : '' ) . '}';
        $css .= "{$sel} .olo-vmq-icona svg{width:{$ico}px;height:{$ico}px}";
        $css .= "{$sel} .olo-vmq-icona [uk-icon],{$sel} .olo-vmq-icona .uk-icon{line-height:0}";
        // immagini
        $css .= "{$sel} .olo-vmq-immagine{display:block;height:{$img_h}px;width:auto;max-width:100%;object-fit:contain" . ( '' !== $img_rag ? ";border-radius:{$img_rag}" : '' ) . '}';
        // separatore: tre punti
        $css .= "{$sel} .olo-vmq-sep{display:flex;gap:.6em;align-items:center;justify-content:center;height:18px}";
        $css .= "{$sel} .olo-vmq-sep i{display:block;width:10px;height:10px;border-radius:50%;background:{$sep_col};opacity:.55}";
        // per dispositivo: altezza e colonne
        $disp = [ 'tablet_landscape' => 1200, 'tablet' => 960, 'mobile_landscape' => 640, 'mobile' => 480 ];
        foreach ( $disp as $d => $px ) {
            $regole = '';
            if ( isset( $alt_disp[ $px ] ) ) {
                $regole .= "{$sel}{height:" . $alt_disp[ $px ] . 'px}';
            }
            $c = isset( $s[ 'colonne_' . $d ] ) ? trim( (string) $s[ 'colonne_' . $d ] ) : '';
            if ( '' !== $c ) {
                $k = max( 1, min( $n, intval( $c ) ) );
                $regole .= "{$sel}{grid-template-columns:repeat({$k},minmax(0,1fr))}";
                $regole .= $k < $n ? "{$sel} .olo-vmq-col:nth-child(n+" . ( $k + 1 ) . '){display:none}' : "{$sel} .olo-vmq-col{display:block}";
            }
            if ( '' !== $regole ) {
                $css .= "@media (max-width:{$px}px){{$regole}}";
            }
        }

        return '<style>' . $css . '</style>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS built from clamped integers, whitelisted enums, colours through safe_color_css(), radii through Olobuild_Tile_Utils::border_radius(), fonts through resolve_font_family(), internal uid
            . '<div class="olo-vmq ' . esc_attr( $uid ) . '">' . $colonne_html . '</div>';
    }

    /**
     * Una voce. Nella copia nascosta (aria-hidden) i link escono dal giro del tabulatore e le
     * immagini hanno l'alternativo vuoto: chi usa un lettore di schermo sente le voci una volta.
     */
    private function voce( $v, $s, $nascosta ) {
        $tipo = $v['tipo'] ?? 'testo';
        $c    = $this->safe_color_css( $v['colore'] ?? '' );
        $bg   = $this->safe_color_css( $v['sfondo'] ?? '' );
        switch ( $tipo ) {
            case 'immagine':
                $url = esc_url( (string) ( $v['immagine'] ?? '' ) );
                if ( '' === $url ) {
                    return '';
                }
                $alt  = $nascosta ? '' : (string) ( $v['alt'] ?? '' );
                $html = '<img class="olo-vmq-immagine" src="' . $url . '" alt="' . esc_attr( $alt ) . '" loading="lazy" decoding="async">';
                break;
            case 'icona':
                $icona = (string) ( $v['icona'] ?? '' );
                if ( '' === $icona ) {
                    return '';
                }
                $stile = ( '' !== $c ? 'color:' . $c . ';' : '' ) . ( '' !== $bg ? 'background:' . $bg . ';' : '' );
                $html  = '<span class="olo-vmq-icona"' . ( '' !== $stile ? ' style="' . esc_attr( $stile ) . '"' : '' ) . '>'
                    . $this->render_icon_html( $icona, max( 12, intval( $s['icona_dimensione'] ) ) / 20 ) . '</span>';
                break;
            case 'separatore':
                return '<span class="olo-vmq-voce olo-vmq-sep" aria-hidden="true"><i></i><i></i><i></i></span>';
            default:
                $testo = trim( (string) ( $v['testo'] ?? '' ) );
                if ( '' === $testo ) {
                    return '';
                }
                // il colore della voce: con la sfumatura è il punto di partenza del gradiente
                $stile = '' !== $c ? ( ! empty( $s['testo_sfumatura'] ) ? '--olo-vmq-c:' . $c : 'color:' . $c ) : '';
                $html  = '<span class="olo-vmq-testo"' . ( '' !== $stile ? ' style="' . esc_attr( $stile ) . '"' : '' ) . '>' . esc_html( $testo ) . '</span>';
        }
        $link = trim( (string) ( $v['link'] ?? '' ) );
        if ( '' !== $link ) {
            return '<a class="olo-vmq-voce" href="' . esc_url( $link ) . '"' . ( $nascosta ? ' tabindex="-1"' : '' ) . '>' . $html . '</a>';
        }
        return '<div class="olo-vmq-voce">' . $html . '</div>';
    }

    /** Il testo dal controllo tipografia: famiglia, misura (anche per tablet e telefono) e peso. */
    private function css_testo( $s, $selettore ) {
        $d   = '';
        $fam = $this->resolve_font_family( $s['testo_font_family'] ?? '' );
        $fs  = (float) ( $s['testo_font_size'] ?? 0 );
        $fw  = $this->font_weight_css( $s['testo_font_weight'] ?? '' );
        if ( '' !== $fam ) {
            $d .= 'font-family:' . $fam . ';';
        }
        if ( $fs > 0 ) {
            $d .= 'font-size:' . $fs . 'px;';
        }
        if ( $fw ) {
            $d .= 'font-weight:' . $fw . ';';
        }
        $css = '' !== $d ? "{$selettore}{{$d}}" : '';
        foreach ( [ 'tablet' => 960, 'mobile' => 480 ] as $disp => $px ) {
            $v = (float) ( $s[ 'testo_font_size_' . $disp ] ?? 0 );
            if ( $v > 0 ) {
                $css .= '@media (max-width:' . $px . 'px){' . $selettore . '{font-size:' . $v . 'px}}';
            }
        }
        return $css;
    }

    /** Un numero corto (al massimo due decimali, senza zeri in coda). */
    private static function num( $n ) {
        $t = rtrim( rtrim( number_format( round( (float) $n, 2 ), 2, '.', '' ), '0' ), '.' );
        return '' === $t ? '0' : $t;
    }
}
