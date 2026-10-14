<?php
/**
 * Tile Certificazioni — le certificazioni di un'azienda dove il cliente le cerca (pagina Qualità, scheda di
 * un prodotto, fascia sotto l'apertura): norma, titolo, ente, numero del certificato, campo di applicazione,
 * rilascio e scadenza, il simbolo o il logo, il PDF del certificato.
 *
 * La scadenza si controlla da sola: dal giorno dopo la data, sul sito la certificazione sparisce, oppure resta
 * con «In rinnovo» al posto della data e senza il PDF. Lo decide anche il browser, nel fuso del sito: una
 * pagina servita dalla cache resta giusta. Nel builder si vedono tutte, le scadute attenuate con la data.
 * Tre viste: schede (griglia), fascia (riga compatta) ed elenco (una riga per certificazione).
 * Nata per i temi industriali (14 ott 2026). Config: src/config/elements/certificazioni.js.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Certificazioni_Tile extends Olobuild_Tile_Base {

    protected $type     = 'certificazioni';
    protected $name     = 'Certificazioni';
    protected $icon     = 'dashicons-awards';
    protected $category = 'marketing';

    protected $defaults = [
        // contenuto
        'voci'                     => [
            [ 'id' => 'cert-1', 'norma' => 'ISO 9001:2015', 'titolo' => 'Sistema di gestione per la qualità', 'ente' => 'Organismo di certificazione accreditato', 'numero' => 'N. 24-QMS-0381', 'ambito' => 'Progettazione e lavorazione meccanica di precisione per conto terzi.', 'rilascio' => '2024-03-12', 'scadenza' => '2027-03-11', 'simbolo' => 'award', 'logo' => '', 'documento' => '' ],
            [ 'id' => 'cert-2', 'norma' => 'ISO 14001:2015', 'titolo' => 'Sistema di gestione ambientale', 'ente' => 'Organismo di certificazione accreditato', 'numero' => 'N. 24-EMS-0112', 'ambito' => 'Scarti di lavorazione, consumi di energia ed emissioni dello stabilimento.', 'rilascio' => '2024-05-20', 'scadenza' => '2027-05-19', 'simbolo' => 'leaf', 'logo' => '', 'documento' => '' ],
            [ 'id' => 'cert-3', 'norma' => 'ISO 45001:2018', 'titolo' => 'Salute e sicurezza sul lavoro', 'ente' => 'Organismo di certificazione accreditato', 'numero' => 'N. 25-OHS-0057', 'ambito' => 'Reparti di produzione, magazzino e uffici tecnici.', 'rilascio' => '2025-01-15', 'scadenza' => '2028-01-14', 'simbolo' => 'shield-check', 'logo' => '', 'documento' => '' ],
        ],
        'mostra_ente'              => true,
        'mostra_numero'            => true,
        'mostra_ambito'            => true,
        'mostra_rilascio'          => false,
        'mostra_validita'          => true,
        'mostra_documento'         => true,
        'scadute'                  => 'nascondi',
        'formato_data'             => 'esteso',
        'testo_valido'             => 'Valido fino al',
        'testo_rilascio'           => 'Rilasciato il',
        'testo_rinnovo'            => 'In rinnovo',
        'testo_documento'          => 'Certificato in PDF',
        // stile
        'vista'                    => 'schede',
        'accento'                  => '',
        'colore_validita'          => '',
        'sfondo_voce'              => '',
        'bordo_voce'               => [],
        'shadow'                   => 'none',
        'norma_tag'                => 'h3',
        'norma_font_family'        => 'heading',
        'norma_font_size'          => 22,
        'norma_font_size_tablet'   => '',
        'norma_font_size_mobile'   => '',
        'norma_font_weight'        => '600',
        'norma_letter_spacing'     => '',
        'norma_color'              => '',
        'titolo_font_family'       => '',
        'titolo_font_size'         => 16,
        'titolo_font_weight'       => '500',
        'titolo_line_height'       => 1.35,
        'titolo_color'             => '',
        'dettagli_font_size'       => 13,
        'dettagli_color'           => '',
        'ambito_font_size'         => 14,
        'ambito_line_height'       => 1.6,
        'ambito_color'             => '',
        'documento_font_size'      => 14,
        'documento_font_weight'    => '600',
        'documento_uppercase'      => false,
        'documento_letter_spacing' => '',
        'raggio'                   => [ 'tl' => 14, 'tr' => 14, 'br' => 14, 'bl' => 14 ],
        'padding_voce'             => [ 'top' => 28, 'right' => 28, 'bottom' => 28, 'left' => 28 ],
        'dimensione_simbolo'       => 40,
        'altezza_logo'             => 48,
        'colonne'                  => 3,
        'gap'                      => 24,
        'allineamento'             => 'sinistra',
    ];

    public function get_controls() {
        return [];
    }

    /** Una data AAAA-MM-GG valida, o ''. */
    private static function giorno( $v ) {
        $v = trim( (string) $v );
        return preg_match( '/^\d{4}-\d{2}-\d{2}$/', $v ) ? $v : '';
    }

    /** La data come la legge il visitatore: «11 marzo 2027» (nella lingua del sito) o «11/03/2027». */
    private static function data_leggibile( $ymd, $formato ) {
        $ts = strtotime( $ymd . ' 12:00:00 UTC' );
        if ( false === $ts ) {
            return '';
        }
        $utc = new DateTimeZone( 'UTC' );
        return 'numeri' === $formato ? wp_date( 'd/m/Y', $ts, $utc ) : wp_date( 'j F Y', $ts, $utc );
    }

    /** CSS di un testo dal controllo tipografia (famiglia, misura per dispositivo, peso, maiuscolo, spaziatura, interlinea, colore). */
    private function css_testo( $s, $k, $selettore ) {
        $d   = '';
        $fam = $this->resolve_font_family( $s[ $k . '_font_family' ] ?? '' );
        $fs  = (float) ( $s[ $k . '_font_size' ] ?? 0 );
        $fw  = $this->font_weight_css( $s[ $k . '_font_weight' ] ?? '' );
        $ls  = $s[ $k . '_letter_spacing' ] ?? '';
        $lh  = $s[ $k . '_line_height' ] ?? '';
        $col = $this->safe_color_css( $s[ $k . '_color' ] ?? '' );
        if ( '' !== $fam ) {
            $d .= 'font-family:' . $fam . ';';
        }
        if ( $fs > 0 ) {
            $d .= 'font-size:' . $fs . 'px;';
        }
        if ( $fw ) {
            $d .= 'font-weight:' . $fw . ';';
        }
        if ( array_key_exists( $k . '_uppercase', $s ) ) {
            $d .= 'text-transform:' . ( ! empty( $s[ $k . '_uppercase' ] ) ? 'uppercase' : 'none' ) . ';';
        }
        if ( is_numeric( $ls ) ) {
            $d .= 'letter-spacing:' . (float) $ls . 'px;';
        }
        if ( is_numeric( $lh ) ) {
            $d .= 'line-height:' . (float) $lh . ';';
        }
        if ( $col ) {
            $d .= 'color:' . $col . ';';
        }
        $css = '' !== $d ? "{$selettore}{{$d}}" : '';
        foreach ( [ 'tablet' => 960, 'mobile' => 480 ] as $disp => $px ) {
            $v = (float) ( $s[ $k . '_font_size_' . $disp ] ?? 0 );
            if ( $v > 0 ) {
                $css .= '@media (max-width:' . $px . 'px){' . $selettore . '{font-size:' . $v . 'px}}';
            }
        }
        return $css;
    }

    public function render( $settings ) {
        $s        = wp_parse_args( $settings, $this->defaults );
        $uid      = 'ocert-' . $this->chiave_stabile( $settings );
        $sel      = '#' . $uid;
        $builder  = ! empty( $s['_builder_mode'] );
        $vista    = in_array( (string) $s['vista'], [ 'schede', 'fascia', 'elenco' ], true ) ? (string) $s['vista'] : 'schede';
        $fascia   = 'fascia' === $vista;
        $modo     = 'rinnovo' === (string) $s['scadute'] ? 'rinnovo' : 'nascondi';
        $formato  = 'numeri' === (string) $s['formato_data'] ? 'numeri' : 'esteso';
        $oggi     = wp_date( 'Y-m-d' );
        $mostra   = [
            'ente'      => ! $fascia && ! empty( $s['mostra_ente'] ),
            'numero'    => ! $fascia && ! empty( $s['mostra_numero'] ),
            'ambito'    => ! $fascia && ! empty( $s['mostra_ambito'] ),
            'rilascio'  => ! $fascia && ! empty( $s['mostra_rilascio'] ),
            'validita'  => ! $fascia && ! empty( $s['mostra_validita'] ),
            'documento' => ! empty( $s['mostra_documento'] ),
        ];

        // ── Le voci: almeno una norma o un titolo; la scadenza decide se oggi è valida ──
        $voci = [];
        foreach ( (array) $s['voci'] as $v ) {
            if ( ! is_array( $v ) ) {
                continue;
            }
            $norma  = trim( (string) ( $v['norma'] ?? '' ) );
            $titolo = trim( (string) ( $v['titolo'] ?? '' ) );
            if ( '' === $norma && '' === $titolo ) {
                continue;
            }
            $scad   = self::giorno( $v['scadenza'] ?? '' );
            $voci[] = [
                'norma'     => $norma,
                'titolo'    => $titolo,
                'ente'      => trim( (string) ( $v['ente'] ?? '' ) ),
                'numero'    => trim( (string) ( $v['numero'] ?? '' ) ),
                'ambito'    => trim( (string) ( $v['ambito'] ?? '' ) ),
                'rilascio'  => self::giorno( $v['rilascio'] ?? '' ),
                'scadenza'  => $scad,
                'scaduta'   => '' !== $scad && $scad < $oggi,
                'simbolo'   => trim( (string) ( $v['simbolo'] ?? '' ) ),
                'logo'      => trim( (string) ( $v['logo'] ?? '' ) ),
                'documento' => trim( (string) ( $v['documento'] ?? '' ) ),
            ];
        }
        $valide = count( array_filter( $voci, static function ( $x ) {
            return ! $x['scaduta'];
        } ) );
        if ( ! $builder ) {
            // sul sito: niente da mostrare se non ci sono voci, o se sono tutte scadute e si nascondono
            if ( ! $voci ) {
                return '';
            }
            if ( 'nascondi' === $modo && 0 === $valide ) {
                return '';
            }
        }

        // ── Stile ──
        $accento = $this->safe_color_css( $s['accento'] ) ?: 'var(--olo-color-primary)';
        $valido  = $this->safe_color_css( $s['colore_validita'] ) ?: 'var(--olo-color-success, var(--olo-color-primary))';
        $sfondo  = $this->safe_color_css( $s['sfondo_voce'] ) ?: 'color-mix(in srgb, currentColor 4%, transparent)';
        $bordo   = Olobuild_Tile_Utils::border_css( $s['bordo_voce'] ?? null ) ?: 'border:1px solid color-mix(in srgb, currentColor 12%, transparent);';
        $raggio  = Olobuild_Tile_Utils::border_radius( $s['raggio'] ) ?: '0';
        $pad     = Olobuild_Tile_Utils::sides_css( Olobuild_Tile_Utils::spacing_sides( $s['padding_voce'] ?? null, [], [ 28, 28, 28, 28 ] ) );
        $ombra   = Olobuild_Tile_Utils::shadow_value( $s, 'shadow' );
        $gap     = max( 0, min( 160, (int) $s['gap'] ) );
        $colonne = max( 1, min( 6, (int) $s['colonne'] ) );
        $simbolo = max( 12, min( 160, (int) $s['dimensione_simbolo'] ) );
        $logo_h  = max( 12, min( 200, (int) $s['altezza_logo'] ) );
        $centro  = 'centro' === (string) $s['allineamento'] && 'elenco' !== $vista;
        $tag_n   = in_array( (string) $s['norma_tag'], [ 'h2', 'h3', 'h4', 'p' ], true ) ? (string) $s['norma_tag'] : 'h3';
        $riquadro = "background:{$sfondo};{$bordo}border-radius:var(--ocert-r);" . ( 'none' !== $ombra ? "box-shadow:{$ombra};" : '' );

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS da colori passati da safe_color_css() e dagli helper di Olobuild_Tile_Utils, numeri forzati, costanti interne e selettori generati qui
        echo '<style>';
        echo "{$sel}{--ocert-acc:{$accento};--ocert-ok:{$valido};--ocert-r:{$raggio};--ocert-gap:{$gap}px;--ocert-simb:{$simbolo}px;--ocert-logo:{$logo_h}px}";
        echo "{$sel} .ocert-lista{list-style:none;margin:0;padding:0}";
        echo "{$sel} .ocert-voce{min-width:0;margin:0}";
        echo "{$sel} .ocert-segno{display:flex;align-items:center;color:var(--ocert-acc);line-height:0}";
        echo "{$sel} .ocert-segno svg,{$sel} .ocert-segno .olo-lucide-icon,{$sel} .ocert-segno [uk-icon]{width:var(--ocert-simb);height:var(--ocert-simb)}";
        echo "{$sel} .ocert-segno .olo-lucide-icon svg,{$sel} .ocert-segno [uk-icon] svg{width:100%;height:100%}";
        echo "{$sel} .ocert-segno img{display:block;height:var(--ocert-logo);width:auto;max-width:100%;object-fit:contain}";
        echo "{$sel} .ocert-norma{margin:0;line-height:1.15;text-wrap:balance}";
        echo "{$sel} .ocert-titolo{margin:.3rem 0 0;text-wrap:balance}";
        echo "{$sel} .ocert-ambito{margin:.75rem 0 0;opacity:.85;max-width:62ch;text-wrap:pretty}";
        echo "{$sel} .ocert-meta{margin:.85rem 0 0;display:flex;flex-wrap:wrap;gap:.2rem 1rem;opacity:.75}";
        echo "{$sel} .ocert-date{margin:.85rem 0 0;display:flex;flex-wrap:wrap;align-items:center;gap:.35rem 1.1rem}";
        // Testo e data in UN elemento, così vanno a capo come una frase; il pallino (senza linea di base) si
        // allinea col fondo alla linea di base della prima riga, cioè a metà delle minuscole.
        echo "{$sel} .ocert-scade{display:inline-flex;align-items:baseline;gap:.55em}";
        // La data non si spezza: «15 Settembre» da una parte e «2027» da sola sotto.
        echo "{$sel} .ocert-date time{white-space:nowrap}";
        echo "{$sel} .ocert-rinnovo{display:inline-flex;align-items:center;gap:.55em}";
        echo "{$sel} .ocert-scade::before{content:'';flex:none;width:.55em;height:.55em;border-radius:50%;background:var(--ocert-ok);box-shadow:0 0 0 3px color-mix(in srgb, var(--ocert-ok) 22%, transparent)}";
        echo "{$sel} .ocert-rinnovo{display:none;font-weight:600}";
        echo "{$sel} .ocert-rinnovo::before{content:'';flex:none;width:.55em;height:.55em;border-radius:50%;background:currentColor;opacity:.45}";
        echo "{$sel} .is-scaduta .ocert-scade,{$sel} .is-scaduta .ocert-doc{display:none}";
        if ( 'rinnovo' === $modo ) {
            echo "{$sel} .is-scaduta .ocert-rinnovo{display:inline-flex}";
        }
        echo "{$sel} .ocert-doc{display:inline-flex;align-items:center;gap:.5em;margin-top:1.1rem;color:var(--ocert-acc);text-decoration:none;border-bottom:1px solid color-mix(in srgb, var(--ocert-acc) 40%, transparent);padding-bottom:.15em;transition:border-color .25s}";
        echo "{$sel} .ocert-doc:hover{border-color:var(--ocert-acc)}";
        echo "{$sel} .ocert-doc .olo-lucide-icon,{$sel} .ocert-doc svg{width:1.1em;height:1.1em;flex:none}";
        echo "{$sel} .ocert-doc:focus-visible,{$sel} .ocert-collegamento:focus-visible{outline:2px solid var(--ocert-acc);outline-offset:3px}";
        echo "{$sel} .ocert-nota{display:none}";
        if ( $builder ) {
            echo "{$sel} .is-scaduta{opacity:.5}";
            echo "{$sel} .is-scaduta .ocert-nota{display:inline-flex;margin-top:.6rem;font-size:12px;font-weight:600;letter-spacing:.04em;padding:.25em .7em;border-radius:5em;background:color-mix(in srgb, currentColor 10%, transparent)}";
            echo "{$sel} .ocert-vuoto{margin:0;padding:1rem;font-size:14px;opacity:.7;border:1px dashed color-mix(in srgb, currentColor 30%, transparent);border-radius:var(--ocert-r)}";
        }
        echo "{$sel} [hidden]{display:none!important}";

        if ( 'schede' === $vista ) {
            echo "{$sel} .ocert-lista{display:grid;grid-template-columns:repeat({$colonne},minmax(0,1fr));gap:var(--ocert-gap)}";
            echo "{$sel} .ocert-voce{display:flex;flex-direction:column;{$riquadro}padding:{$pad}" . ( $centro ? ';text-align:center;align-items:center' : '' ) . '}';
            echo "{$sel} .ocert-segno{margin:0 0 1.1rem}";
            echo "{$sel} .ocert-doc{margin-top:auto;padding-top:1.1rem;border-bottom:0}";
            echo "{$sel} .ocert-doc span{border-bottom:1px solid color-mix(in srgb, var(--ocert-acc) 40%, transparent);padding-bottom:.15em;transition:border-color .25s}";
            echo "{$sel} .ocert-doc:hover span{border-color:var(--ocert-acc)}";
            if ( $centro ) {
                echo "{$sel} .ocert-meta,{$sel} .ocert-date{justify-content:center}";
            }
            if ( $colonne > 2 ) {
                echo '@media (max-width:960px){' . "{$sel} .ocert-lista{grid-template-columns:repeat(2,minmax(0,1fr))}" . '}';
            }
            if ( $colonne > 1 ) {
                echo '@media (max-width:640px){' . "{$sel} .ocert-lista{grid-template-columns:minmax(0,1fr)}" . '}';
            }
            echo $this->css_per_dispositivo( $s, 'colonne', "{$sel} .ocert-lista", $this->decl_colonne( 6 ) );
        } elseif ( 'fascia' === $vista ) {
            echo "{$sel} .ocert-lista{display:flex;flex-wrap:wrap;align-items:center;gap:var(--ocert-gap) calc(var(--ocert-gap) * 1.5)" . ( $centro ? ';justify-content:center' : '' ) . '}';
            echo "{$sel} .ocert-voce,{$sel} .ocert-collegamento{display:flex;align-items:center;gap:.85rem}";
            echo "{$sel} .ocert-collegamento{color:inherit;text-decoration:none}";
            echo "{$sel} .ocert-collegamento:hover .ocert-norma{color:var(--ocert-acc)}";
            echo "{$sel} .ocert-titolo{margin:.15rem 0 0;opacity:.8}";
        } else {
            echo "{$sel} .ocert-lista{display:flex;flex-direction:column;gap:var(--ocert-gap)}";
            echo "{$sel} .ocert-voce{display:grid;grid-template-columns:auto minmax(0,1.6fr) minmax(0,1fr) auto;gap:1rem 2rem;align-items:center;{$riquadro}padding:{$pad}}";
            echo "{$sel} .ocert-voce .ocert-meta,{$sel} .ocert-voce .ocert-date{margin:0;flex-direction:column;align-items:flex-start;gap:.25rem}";
            echo "{$sel} .ocert-voce .ocert-doc{margin:0;white-space:nowrap}";
            echo '@media (max-width:960px){' . "{$sel} .ocert-voce{grid-template-columns:auto minmax(0,1fr)}{$sel} .ocert-voce>.ocert-dati,{$sel} .ocert-voce>.ocert-azione{grid-column:2}" . '}';
            echo '@media (max-width:480px){' . "{$sel} .ocert-voce{grid-template-columns:minmax(0,1fr)}{$sel} .ocert-voce>*{grid-column:1!important}" . '}';
        }
        echo $this->css_testo( $s, 'norma', "{$sel} .ocert-norma" );
        echo $this->css_testo( $s, 'titolo', "{$sel} .ocert-titolo" );
        echo $this->css_testo( $s, 'dettagli', "{$sel} .ocert-meta,{$sel} .ocert-date,{$sel} .ocert-nota" );
        echo $this->css_testo( $s, 'ambito', "{$sel} .ocert-ambito" );
        echo $this->css_testo( $s, 'documento', "{$sel} .ocert-doc" );
        echo '</style>';

        $cfg = [
            'modo'    => $modo,
            'tz'      => wp_timezone_string(),
            'builder' => $builder,
        ];
        echo '<div id="' . esc_attr( $uid ) . '" class="ocert ocert--' . esc_attr( $vista ) . '" data-ocert="' . esc_attr( wp_json_encode( $cfg ) ) . '">';
        if ( ! $voci ) {
            echo '<p class="ocert-vuoto">' . esc_html__( 'Aggiungi le certificazioni nel Contenuto.', 'olobuilder' ) . '</p></div>';
            return ob_get_clean();
        }
        if ( $builder && 0 === $valide && 'nascondi' === $modo ) {
            echo '<p class="ocert-vuoto">' . esc_html__( 'Sono tutte scadute: sul sito la tile non si vede.', 'olobuilder' ) . '</p>';
        }

        $doc_icona = $this->render_icon_html( 'file-down', 0.9, 'aria-hidden="true"' );
        echo '<ul class="ocert-lista" role="list">';
        foreach ( $voci as $v ) {
            $segno = '';
            if ( '' !== $v['logo'] ) {
                $segno = '<img src="' . esc_url( $v['logo'] ) . '" alt="' . esc_attr( '' !== $v['norma'] ? $v['norma'] : $v['titolo'] ) . '" loading="lazy" decoding="async">';
            } elseif ( '' !== $v['simbolo'] ) {
                $segno = $this->render_icon_html( $v['simbolo'], max( 0.6, round( $simbolo / 20, 2 ) ), 'aria-hidden="true"' );
            }
            $segno_html = '' !== $segno ? '<div class="ocert-segno">' . $segno . '</div>' : '';
            $norma      = '' !== $v['norma'] ? '<' . $tag_n . ' class="ocert-norma">' . esc_html( $v['norma'] ) . '</' . $tag_n . '>' : '';
            $titolo     = '' !== $v['titolo'] ? '<p class="ocert-titolo">' . esc_html( $v['titolo'] ) . '</p>' : '';
            $classe     = 'ocert-voce' . ( $v['scaduta'] ? ' is-scaduta' : '' );
            $nascosta   = ( $v['scaduta'] && 'nascondi' === $modo && ! $builder ) ? ' hidden' : '';
            $attr       = ' class="' . $classe . '"' . ( '' !== $v['scadenza'] ? ' data-scade="' . esc_attr( $v['scadenza'] ) . '"' : '' ) . $nascosta;
            $ha_doc     = $mostra['documento'] && '' !== $v['documento'];

            if ( $fascia ) {
                $dentro = $segno_html . '<div class="ocert-testo">' . $norma . $titolo . '</div>';
                if ( $ha_doc ) {
                    /* translators: %s: nome della certificazione (es. ISO 9001:2015) */
                    $aria   = sprintf( __( 'Certificato %s in PDF', 'olobuilder' ), '' !== $v['norma'] ? $v['norma'] : $v['titolo'] );
                    $dentro = '<a class="ocert-collegamento" href="' . esc_url( $v['documento'] ) . '" target="_blank" rel="noopener" aria-label="' . esc_attr( $aria ) . '">' . $dentro . '</a>';
                }
                echo '<li' . $attr . '>' . $dentro . '</li>';
                continue;
            }

            $meta = [];
            if ( $mostra['ente'] && '' !== $v['ente'] ) {
                $meta[] = '<span>' . esc_html( $v['ente'] ) . '</span>';
            }
            if ( $mostra['numero'] && '' !== $v['numero'] ) {
                $meta[] = '<span>' . esc_html( $v['numero'] ) . '</span>';
            }
            $date = '';
            if ( $mostra['rilascio'] && '' !== $v['rilascio'] ) {
                $date .= '<span class="ocert-rilascio">' . esc_html( trim( (string) $s['testo_rilascio'] ) ) . ' <time datetime="' . esc_attr( $v['rilascio'] ) . '">' . esc_html( self::data_leggibile( $v['rilascio'], $formato ) ) . '</time></span>';
            }
            if ( $mostra['validita'] && '' !== $v['scadenza'] ) {
                $date .= '<span class="ocert-scade"><span>' . esc_html( trim( (string) $s['testo_valido'] ) ) . ' <time datetime="' . esc_attr( $v['scadenza'] ) . '">' . esc_html( self::data_leggibile( $v['scadenza'], $formato ) ) . '</time></span></span>';
            }
            if ( 'rinnovo' === $modo && '' !== $v['scadenza'] ) {
                $date .= '<span class="ocert-rinnovo">' . esc_html( trim( (string) $s['testo_rinnovo'] ) ) . '</span>';
            }
            $nota = '';
            if ( $builder && $v['scaduta'] ) {
                /* translators: %s: data di scadenza */
                $nota = '<span class="ocert-nota">' . esc_html( sprintf( __( 'Scaduta il %s', 'olobuilder' ), self::data_leggibile( $v['scadenza'], $formato ) ) ) . '</span>';
            }
            $doc = '';
            if ( $ha_doc ) {
                $testo_doc = trim( (string) $s['testo_documento'] );
                /* translators: %s: nome della certificazione (es. ISO 9001:2015) */
                $aria = sprintf( __( 'Certificato %s in PDF', 'olobuilder' ), '' !== $v['norma'] ? $v['norma'] : $v['titolo'] );
                $doc  = '<a class="ocert-doc" href="' . esc_url( $v['documento'] ) . '" target="_blank" rel="noopener" aria-label="' . esc_attr( $aria ) . '">' . $doc_icona . '<span>' . esc_html( '' !== $testo_doc ? $testo_doc : __( 'Certificato in PDF', 'olobuilder' ) ) . '</span></a>';
            }
            $ambito   = ( $mostra['ambito'] && '' !== $v['ambito'] ) ? '<p class="ocert-ambito">' . esc_html( $v['ambito'] ) . '</p>' : '';
            $meta_htm = $meta ? '<p class="ocert-meta">' . implode( '', $meta ) . '</p>' : '';
            $date_htm = '' !== $date ? '<p class="ocert-date">' . $date . '</p>' : '';

            if ( 'elenco' === $vista ) {
                echo '<li' . $attr . '>'
                    . ( '' !== $segno_html ? $segno_html : '<div class="ocert-segno"></div>' )
                    . '<div class="ocert-testo">' . $norma . $titolo . $ambito . $nota . '</div>'
                    . '<div class="ocert-dati">' . $meta_htm . $date_htm . '</div>'
                    . '<div class="ocert-azione">' . $doc . '</div>'
                    . '</li>';
            } else {
                echo '<li' . $attr . '>' . $segno_html . $norma . $titolo . $ambito . $meta_htm . $date_htm . $nota . $doc . '</li>';
            }
        }
        echo '</ul>';
        echo '</div>';

        // Il browser ricontrolla le scadenze (pagine in cache): solo sul sito, solo se c'è una data.
        if ( ! $builder && array_filter( array_column( $voci, 'scadenza' ) ) ) {
            echo '<script>' . $this->script() . 'window.oloCertificazioni(document.getElementById(' . wp_json_encode( $uid ) . '));</script>';
        }
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }

    /** La funzione che ricontrolla le scadenze nel browser, una volta per pagina. Niente && (wptexturize). */
    private function script() {
        static $fatto = false;
        if ( $fatto ) {
            return '';
        }
        $fatto = true;
        return <<<'JS'
if(!window.oloCertificazioni){window.oloCertificazioni=function(el){
if(!el){return;}
var C={};try{C=JSON.parse(el.getAttribute("data-ocert")||"{}");}catch(e){}
var due=function(n){return(n<10?"0":"")+n;};
var d=new Date();var oggi=d.getFullYear()+"-"+due(d.getMonth()+1)+"-"+due(d.getDate());
if(C.tz){try{oggi=new Intl.DateTimeFormat("en-CA",{timeZone:C.tz,year:"numeric",month:"2-digit",day:"2-digit"}).format(d);}catch(e){}}
var voci=[].slice.call(el.querySelectorAll(".ocert-voce"));var viste=0;
voci.forEach(function(v){var s=v.getAttribute("data-scade")||"";var sc=false;if(s){if(s<oggi){sc=true;}}
if(sc){v.classList.add("is-scaduta");}else{v.classList.remove("is-scaduta");}
if(C.modo==="nascondi"){if(sc){v.setAttribute("hidden","");}else{v.removeAttribute("hidden");}}
if(!v.hasAttribute("hidden")){viste++;}});
if(viste===0){el.setAttribute("hidden","");}else{el.removeAttribute("hidden");}
};}
JS;
    }
}
