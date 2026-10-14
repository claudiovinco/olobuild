<?php
/**
 * Tile Scheda tecnica — la scheda di un prodotto per chi compra da un'azienda: codice, nome, etichetta, foto,
 * descrizione breve, le specifiche in tabella (a gruppi, col valore e l'unità allineati), i documenti da
 * scaricare senza moduli (formato e peso del file), le certificazioni del prodotto e il pulsante del preventivo.
 *
 * Il pulsante può portare il codice nel modulo: aggiunge ?prodotto=CODICE al collegamento, e nel Form Contatti
 * della pagina di arrivo un campo con «Valore dall'indirizzo» = prodotto si compila da solo. Sul sito la tile
 * aggiunge i dati Schema.org del prodotto (Product con le specifiche come PropertyValue).
 * Foto a sinistra, a destra o sopra; a fianco resta ferma mentre si scorrono le specifiche.
 * Nata per i temi industriali (14 ott 2026). Config: src/config/elements/schedatecnica.js.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Schedatecnica_Tile extends Olobuild_Tile_Base {

    protected $type     = 'schedatecnica';
    protected $name     = 'Scheda tecnica';
    protected $icon     = 'dashicons-media-spreadsheet';
    protected $category = 'marketing';

    protected $defaults = [
        // contenuto
        'codice'                  => 'LR-240',
        'nome'                    => 'Riempitrice rotativa LR 240',
        'descrizione'             => 'Per liquidi e creme da 50 a 1000 ml, in linea con tappatura ed etichettatura. Il cambio formato si fa senza attrezzi, in meno di un quarto d\'ora.',
        'immagine'                => '',
        'etichetta'               => 'Nuova serie',
        'titolo_specifiche'       => 'Specifiche',
        'specifiche'              => [
            [ 'id' => 'sp-1', 'gruppo' => 'Prestazioni', 'nome' => 'Velocità', 'valore' => '240', 'unita' => 'pezzi/min' ],
            [ 'id' => 'sp-2', 'gruppo' => 'Prestazioni', 'nome' => 'Formati', 'valore' => '50–1000', 'unita' => 'ml' ],
            [ 'id' => 'sp-3', 'gruppo' => 'Prestazioni', 'nome' => 'Precisione di dosaggio', 'valore' => '±0,5', 'unita' => '%' ],
            [ 'id' => 'sp-4', 'gruppo' => 'Dimensioni', 'nome' => 'Ingombro (L × P × H)', 'valore' => '3200 × 1800 × 2300', 'unita' => 'mm' ],
            [ 'id' => 'sp-5', 'gruppo' => 'Dimensioni', 'nome' => 'Peso', 'valore' => '3400', 'unita' => 'kg' ],
            [ 'id' => 'sp-6', 'gruppo' => 'Alimentazione', 'nome' => 'Potenza installata', 'valore' => '12', 'unita' => 'kW' ],
            [ 'id' => 'sp-7', 'gruppo' => 'Alimentazione', 'nome' => 'Aria compressa', 'valore' => '6', 'unita' => 'bar' ],
        ],
        'titolo_documenti'        => 'Documenti',
        'documenti'               => [
            [ 'id' => 'doc-1', 'nome' => 'Scheda tecnica', 'formato' => 'pdf', 'peso' => '1,2 MB', 'file' => '' ],
            [ 'id' => 'doc-2', 'nome' => 'Disegno di ingombro', 'formato' => 'dwg', 'peso' => '3,8 MB', 'file' => '' ],
            [ 'id' => 'doc-3', 'nome' => 'Modello 3D', 'formato' => 'step', 'peso' => '14 MB', 'file' => '' ],
        ],
        'certificazioni'          => 'CE, ISO 9001, GMP',
        'cta_testo'               => 'Richiedi un preventivo',
        'cta_url'                 => '',
        'cta_codice'              => true,
        'cta_parametro'           => 'prodotto',
        'dati_strutturati'        => true,
        // stile
        'accento'                 => '',
        'sfondo_scheda'           => '',
        'bordo_scheda'            => [],
        'shadow'                  => 'none',
        'colore_linee'            => '',
        'righe_alternate'         => false,
        'codice_font_family'      => '',
        'codice_font_size'        => 12,
        'codice_font_weight'      => '600',
        'codice_uppercase'        => true,
        'codice_letter_spacing'   => 2,
        'codice_color'            => '',
        'nome_tag'                => 'h2',
        'nome_font_family'        => 'heading',
        'nome_font_size'          => 36,
        'nome_font_size_tablet'   => '',
        'nome_font_size_mobile'   => '',
        'nome_font_weight'        => '600',
        'nome_line_height'        => 1.1,
        'nome_color'              => '',
        'descrizione_font_size'   => 16,
        'descrizione_line_height' => 1.6,
        'descrizione_color'       => '',
        'gruppo_font_size'        => 12,
        'gruppo_font_weight'      => '600',
        'gruppo_uppercase'        => true,
        'gruppo_letter_spacing'   => 1.5,
        'gruppo_color'            => '',
        'voce_font_size'          => 15,
        'voce_color'              => '',
        'valore_font_family'      => '',
        'valore_font_size'        => 15,
        'valore_font_weight'      => '600',
        'valore_color'            => '',
        'unita_font_size'         => 13,
        'unita_color'             => '',
        'documenti_font_size'     => 15,
        'documenti_font_weight'   => '500',
        'documenti_color'         => '',
        'pulsante_font_size'      => 15,
        'pulsante_font_weight'    => '600',
        'pulsante_uppercase'      => false,
        'pulsante_letter_spacing' => '',
        'raggio'                  => [ 'tl' => 14, 'tr' => 14, 'br' => 14, 'bl' => 14 ],
        'raggio_pulsante'         => [ 'tl' => 8, 'tr' => 8, 'br' => 8, 'bl' => 8 ],
        'padding_scheda'          => [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 ],
        'img_ratio'               => '4/3',
        'img_ratio_custom'        => '4/3',
        'img_fit'                 => 'contain',
        'img_object_position'     => 'center center',
        'foto_posizione'          => 'sinistra',
        'larghezza_foto'          => 42,
        'gap'                     => 48,
        'colonne_specifiche'      => 1,
    ];

    /** Icona di ogni formato (dal set Lucide del plugin). */
    const ICONE_FORMATO = [
        'pdf'   => 'file-text',
        'step'  => 'box',
        'dwg'   => 'drafting-compass',
        'dxf'   => 'drafting-compass',
        'zip'   => 'file-archive',
        'altro' => 'file',
    ];

    public function get_controls() {
        return [];
    }

    /** Un valore di collegamento: stringa, oppure l'oggetto del selettore ({url}). */
    private static function url_di( $v ) {
        if ( is_array( $v ) ) {
            $v = $v['url'] ?? '';
        }
        return trim( (string) $v );
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
        $s       = wp_parse_args( $settings, $this->defaults );
        $uid     = 'ostec-' . $this->chiave_stabile( $settings );
        $sel     = '#' . $uid;
        $builder = ! empty( $s['_builder_mode'] );

        $codice = trim( (string) $s['codice'] );
        $nome   = trim( (string) $s['nome'] );
        $etich  = trim( (string) $s['etichetta'] );
        $descr  = trim( (string) $s['descrizione'] );
        $img    = trim( (string) $s['immagine'] );
        $foto   = '' !== $img || $builder;

        // ── Specifiche a gruppi, nell'ordine in cui il gruppo compare la prima volta ──
        $gruppi = [];
        foreach ( (array) $s['specifiche'] as $r ) {
            if ( ! is_array( $r ) ) {
                continue;
            }
            $n = trim( (string) ( $r['nome'] ?? '' ) );
            $v = trim( (string) ( $r['valore'] ?? '' ) );
            if ( '' === $n && '' === $v ) {
                continue;
            }
            $g = trim( (string) ( $r['gruppo'] ?? '' ) );
            $gruppi[ $g ][] = [ 'nome' => $n, 'valore' => $v, 'unita' => trim( (string) ( $r['unita'] ?? '' ) ) ];
        }

        // ── Documenti: sul sito solo quelli col file ──
        $documenti = [];
        foreach ( (array) $s['documenti'] as $d ) {
            if ( ! is_array( $d ) ) {
                continue;
            }
            $file = self::url_di( $d['file'] ?? '' );
            $dn   = trim( (string) ( $d['nome'] ?? '' ) );
            if ( '' === $dn && '' === $file ) {
                continue;
            }
            if ( '' === $file && ! $builder ) {
                continue;
            }
            $fmt         = strtolower( trim( (string) ( $d['formato'] ?? 'pdf' ) ) );
            $fmt         = isset( self::ICONE_FORMATO[ $fmt ] ) ? $fmt : 'altro';
            $documenti[] = [ 'nome' => $dn, 'file' => $file, 'formato' => $fmt, 'peso' => trim( (string) ( $d['peso'] ?? '' ) ) ];
        }

        $cert = array_values( array_filter( array_map( 'trim', explode( ',', (string) $s['certificazioni'] ) ), 'strlen' ) );

        // ── Pulsante del preventivo, col codice nel collegamento ──
        $cta_testo = trim( (string) $s['cta_testo'] );
        $cta_url   = self::url_di( $s['cta_url'] );
        if ( '' !== $cta_url && '' !== $codice && ! empty( $s['cta_codice'] ) ) {
            $param = substr( (string) preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $s['cta_parametro'] ), 0, 40 );
            if ( '' !== $param ) {
                $cta_url = add_query_arg( $param, rawurlencode( $codice ), $cta_url );
            }
        }
        $con_cta = '' !== $cta_testo && ( '' !== $cta_url || $builder );

        if ( ! $builder && '' === $nome && ! $gruppi && ! $documenti ) {
            return '';
        }

        // ── Stile ──
        $accento  = $this->safe_color_css( $s['accento'] ) ?: 'var(--olo-color-primary)';
        $su_acc   = Olobuild_Tile_Utils::testo_su_fondo( $accento, 'var(--olo-color-light, #ffffff)' ) ?: 'var(--olo-color-light, #ffffff)';
        $sfondo   = $this->safe_color_css( $s['sfondo_scheda'] );
        $bordo    = Olobuild_Tile_Utils::border_css( $s['bordo_scheda'] ?? null );
        $ombra    = Olobuild_Tile_Utils::shadow_value( $s, 'shadow' );
        $raggio   = Olobuild_Tile_Utils::border_radius( $s['raggio'] ) ?: '0';
        $r_puls   = Olobuild_Tile_Utils::border_radius( $s['raggio_pulsante'] ) ?: '0';
        $pad      = Olobuild_Tile_Utils::sides_css( Olobuild_Tile_Utils::spacing_sides( $s['padding_scheda'] ?? null, [], [ 0, 0, 0, 0 ] ) );
        $linea    = $this->safe_color_css( $s['colore_linee'] ) ?: 'color-mix(in srgb, currentColor 14%, transparent)';
        $gap      = max( 0, min( 200, (int) $s['gap'] ) );
        $larg     = max( 20, min( 70, (int) $s['larghezza_foto'] ) );
        $pos      = in_array( (string) $s['foto_posizione'], [ 'sinistra', 'destra', 'sopra' ], true ) ? (string) $s['foto_posizione'] : 'sinistra';
        $col_spec = 2 === (int) $s['colonne_specifiche'] ? 2 : 1;
        $tag_n    = in_array( (string) $s['nome_tag'], [ 'h1', 'h2', 'h3', 'p' ], true ) ? (string) $s['nome_tag'] : 'h2';
        $tag_t    = 'h1' === $tag_n ? 'h2' : ( 'h2' === $tag_n ? 'h3' : 'h4' );
        $cornice  = Olobuild_Tile_Utils::image_frame( $s, 'img', [ 'ratio' => '4/3', 'fit' => 'contain' ] );
        $riquadro = '' !== $sfondo || '' !== $bordo || 'none' !== $ombra;

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS da colori passati da safe_color_css() e dagli helper di Olobuild_Tile_Utils, numeri forzati, costanti interne e selettori generati qui
        echo '<style>';
        echo "{$sel}{--ostec-acc:{$accento};--ostec-su-acc:{$su_acc};--ostec-linea:{$linea};--ostec-r:{$raggio};--ostec-rp:{$r_puls};--ostec-gap:{$gap}px;--ostec-foto:{$larg}%}";
        echo "{$sel} .ostec-scheda{display:grid;gap:var(--ostec-gap);align-items:start;padding:{$pad};" . ( $riquadro ? 'border-radius:var(--ostec-r);' : '' ) . ( '' !== $sfondo ? "background:{$sfondo};" : '' ) . $bordo . ( 'none' !== $ombra ? "box-shadow:{$ombra};" : '' ) . '}';
        if ( $foto && 'sopra' !== $pos ) {
            echo "{$sel} .ostec-scheda{grid-template-columns:" . ( 'destra' === $pos ? 'minmax(0,1fr) minmax(0,var(--ostec-foto))' : 'minmax(0,var(--ostec-foto)) minmax(0,1fr)' ) . '}';
            if ( 'destra' === $pos ) {
                echo "{$sel} .ostec-foto{order:2}";
            }
            echo "{$sel} .ostec-foto{position:sticky;top:1.5rem}";
            echo '@media (max-width:960px){' . "{$sel} .ostec-scheda{grid-template-columns:minmax(0,1fr)}{$sel} .ostec-foto{position:static;order:0}" . '}';
        }
        echo "{$sel} .ostec-foto{margin:0;overflow:hidden;border-radius:var(--ostec-r);background:color-mix(in srgb, currentColor 5%, transparent);" . ( '' !== $cornice['contenitore'] ? $cornice['contenitore'] : '' ) . '}';
        echo "{$sel} .ostec-foto img{display:block;width:100%;height:100%;" . ( '' !== $cornice['immagine'] ? $cornice['immagine'] : 'object-fit:contain;' ) . '}';
        echo "{$sel} .ostec-foto--vuota{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.6rem;min-height:14rem;font-size:14px;opacity:.6;border:1px dashed color-mix(in srgb, currentColor 30%, transparent)}";
        echo "{$sel} .ostec-foto--vuota .olo-lucide-icon,{$sel} .ostec-foto--vuota svg{width:2.4rem;height:2.4rem}";
        echo "{$sel} .ostec-corpo{min-width:0;display:flex;flex-direction:column;gap:1.75rem}";
        echo "{$sel} .ostec-testa{display:flex;flex-direction:column;gap:.75rem}";
        echo "{$sel} .ostec-sopra{margin:0;display:flex;flex-wrap:wrap;align-items:center;gap:.5rem 1rem}";
        echo "{$sel} .ostec-etichetta{background:color-mix(in srgb, var(--ostec-acc) 14%, transparent);color:var(--ostec-acc);padding:.3em .75em;border-radius:5em}";
        echo "{$sel} .ostec-nome{margin:0;text-wrap:balance}";
        echo "{$sel} .ostec-descr{margin:0;max-width:62ch;text-wrap:pretty;opacity:.9}";
        echo "{$sel} .ostec-cert{list-style:none;margin:0;padding:0;display:flex;flex-wrap:wrap;gap:.4rem}";
        echo "{$sel} .ostec-cert li{font-size:12px;font-weight:600;letter-spacing:.06em;padding:.3em .8em;border:1px solid var(--ostec-linea);border-radius:5em}";
        echo "{$sel} .ostec-cta{align-self:flex-start;display:inline-flex;align-items:center;gap:.6em;text-decoration:none!important;background:var(--ostec-acc);color:var(--ostec-su-acc)!important;border-radius:var(--ostec-rp);padding:.9em 1.5em;line-height:1.1;transition:transform .2s,filter .2s}";
        echo "{$sel} .ostec-cta:hover{transform:translateY(-1px);filter:brightness(1.08)}";
        echo "{$sel} .ostec-cta svg,{$sel} .ostec-cta .olo-lucide-icon{width:1.1em;height:1.1em;flex:none}";
        echo "{$sel} .ostec-cta:focus-visible,{$sel} .ostec-doc-link:focus-visible{outline:2px solid var(--ostec-acc);outline-offset:3px}";
        echo "{$sel} .ostec-tit{margin:0 0 .75rem}";
        if ( 2 === $col_spec ) {
            echo "{$sel} .ostec-gruppi{columns:2 16rem;column-gap:var(--ostec-gap)}";
        }
        echo "{$sel} .ostec-gruppo{break-inside:avoid;margin:0 0 1.25rem}";
        echo "{$sel} .ostec-gruppo:last-child{margin-bottom:0}";
        echo "{$sel} .ostec-gruppo-tit{margin:0 0 .25rem;opacity:.7}";
        echo "{$sel} .ostec-righe{margin:0}";
        echo "{$sel} .ostec-riga{display:flex;justify-content:space-between;align-items:baseline;gap:1rem;padding:.6rem 0;border-bottom:1px solid var(--ostec-linea)}";
        if ( ! empty( $s['righe_alternate'] ) ) {
            echo "{$sel} .ostec-riga{padding:.6rem .75rem;border-bottom:0}{$sel} .ostec-riga:nth-child(odd){background:color-mix(in srgb, currentColor 5%, transparent);border-radius:.35rem}";
        }
        echo "{$sel} .ostec-riga dt{margin:0;opacity:.8;min-width:0}";
        echo "{$sel} .ostec-riga dd{margin:0;text-align:right;font-variant-numeric:tabular-nums;white-space:nowrap}";
        echo "{$sel} .ostec-un{opacity:.65;margin-left:.3em}";
        echo "{$sel} .ostec-doc-lista{list-style:none;margin:0;padding:0;display:flex;flex-direction:column}";
        echo "{$sel} .ostec-doc-link{display:flex;align-items:center;gap:.85rem;padding:.75rem 0;border-bottom:1px solid var(--ostec-linea);color:inherit;text-decoration:none}";
        echo "{$sel} .ostec-doc-link:hover .ostec-doc-nome{color:var(--ostec-acc)}";
        echo "{$sel} .ostec-doc-ico{flex:none;display:flex;color:var(--ostec-acc)}";
        echo "{$sel} .ostec-doc-ico .olo-lucide-icon,{$sel} .ostec-doc-ico svg{width:1.35em;height:1.35em}";
        echo "{$sel} .ostec-doc-nome{flex:1;min-width:0;transition:color .2s}";
        echo "{$sel} .ostec-doc-info{flex:none;font-size:.8em;opacity:.65;font-variant-numeric:tabular-nums;white-space:nowrap}";
        echo "{$sel} .ostec-doc-giu{flex:none;display:flex;opacity:.6}";
        echo "{$sel} .ostec-doc-giu .olo-lucide-icon,{$sel} .ostec-doc-giu svg{width:1.1em;height:1.1em}";
        echo "{$sel} .ostec-doc--senza{opacity:.5}";
        echo "{$sel} .ostec-vuoto{margin:0;padding:1rem;font-size:14px;opacity:.7;border:1px dashed color-mix(in srgb, currentColor 30%, transparent);border-radius:var(--ostec-r)}";
        echo $this->css_testo( $s, 'codice', "{$sel} .ostec-codice,{$sel} .ostec-etichetta" );
        echo $this->css_testo( $s, 'nome', "{$sel} .ostec-nome" );
        echo $this->css_testo( $s, 'descrizione', "{$sel} .ostec-descr" );
        echo $this->css_testo( $s, 'gruppo', "{$sel} .ostec-tit,{$sel} .ostec-gruppo-tit" );
        echo $this->css_testo( $s, 'voce', "{$sel} .ostec-riga dt" );
        echo $this->css_testo( $s, 'valore', "{$sel} .ostec-val" );
        echo $this->css_testo( $s, 'unita', "{$sel} .ostec-un" );
        echo $this->css_testo( $s, 'documenti', "{$sel} .ostec-doc-link" );
        echo $this->css_testo( $s, 'pulsante', "{$sel} .ostec-cta" );
        echo '</style>';

        echo '<div id="' . esc_attr( $uid ) . '" class="ostec">';
        echo '<div class="ostec-scheda">';

        if ( $foto ) {
            if ( '' !== $img ) {
                echo '<figure class="ostec-foto"><img src="' . esc_url( $img ) . '" alt="' . esc_attr( '' !== $nome ? $nome : $codice ) . '" loading="lazy" decoding="async"></figure>';
            } else {
                echo '<div class="ostec-foto ostec-foto--vuota">' . $this->render_icon_html( 'image', 1.2, 'aria-hidden="true"' ) . '<span>' . esc_html__( 'La foto del prodotto', 'olobuilder' ) . '</span></div>';
            }
        }

        echo '<div class="ostec-corpo">';
        echo '<div class="ostec-testa">';
        if ( '' !== $codice || '' !== $etich ) {
            echo '<p class="ostec-sopra">' . ( '' !== $codice ? '<span class="ostec-codice">' . esc_html( $codice ) . '</span>' : '' ) . ( '' !== $etich ? '<span class="ostec-etichetta">' . esc_html( $etich ) . '</span>' : '' ) . '</p>';
        }
        if ( '' !== $nome ) {
            echo '<' . $tag_n . ' class="ostec-nome">' . esc_html( $nome ) . '</' . $tag_n . '>';
        }
        if ( '' !== $descr ) {
            echo '<p class="ostec-descr">' . nl2br( esc_html( $descr ) ) . '</p>';
        }
        if ( $cert ) {
            echo '<ul class="ostec-cert" aria-label="' . esc_attr__( 'Certificazioni', 'olobuilder' ) . '">' . implode( '', array_map( static function ( $c ) {
                return '<li>' . esc_html( $c ) . '</li>';
            }, $cert ) ) . '</ul>';
        }
        if ( $con_cta ) {
            echo '<a class="ostec-cta" href="' . esc_url( '' !== $cta_url ? $cta_url : '#' ) . '">' . esc_html( $cta_testo ) . $this->render_icon_html( 'arrow-right', 0.9, 'aria-hidden="true"' ) . '</a>';
        }
        echo '</div>';

        if ( $gruppi ) {
            echo '<section class="ostec-spec">';
            $ts = trim( (string) $s['titolo_specifiche'] );
            if ( '' !== $ts ) {
                echo '<' . $tag_t . ' class="ostec-tit">' . esc_html( $ts ) . '</' . $tag_t . '>';
            }
            echo '<div class="ostec-gruppi">';
            foreach ( $gruppi as $g => $righe ) {
                echo '<div class="ostec-gruppo">';
                if ( '' !== (string) $g ) {
                    echo '<p class="ostec-gruppo-tit">' . esc_html( (string) $g ) . '</p>';
                }
                echo '<dl class="ostec-righe">';
                foreach ( $righe as $r ) {
                    echo '<div class="ostec-riga"><dt>' . esc_html( $r['nome'] ) . '</dt><dd><span class="ostec-val">' . esc_html( $r['valore'] ) . '</span>' . ( '' !== $r['unita'] ? '<span class="ostec-un">' . esc_html( $r['unita'] ) . '</span>' : '' ) . '</dd></div>';
                }
                echo '</dl></div>';
            }
            echo '</div></section>';
        }

        if ( $documenti ) {
            echo '<section class="ostec-doc">';
            $td = trim( (string) $s['titolo_documenti'] );
            if ( '' !== $td ) {
                echo '<' . $tag_t . ' class="ostec-tit">' . esc_html( $td ) . '</' . $tag_t . '>';
            }
            echo '<ul class="ostec-doc-lista">';
            $giu = $this->render_icon_html( 'download', 0.9, 'aria-hidden="true"' );
            foreach ( $documenti as $d ) {
                $info  = strtoupper( 'altro' === $d['formato'] ? '' : $d['formato'] );
                $info .= ( '' !== $info && '' !== $d['peso'] ? ' · ' : '' ) . $d['peso'];
                $ico   = '<span class="ostec-doc-ico">' . $this->render_icon_html( self::ICONE_FORMATO[ $d['formato'] ], 1, 'aria-hidden="true"' ) . '</span>';
                $testo = $ico . '<span class="ostec-doc-nome">' . esc_html( '' !== $d['nome'] ? $d['nome'] : basename( (string) wp_parse_url( $d['file'], PHP_URL_PATH ) ) ) . '</span>'
                    . ( '' !== $info ? '<span class="ostec-doc-info">' . esc_html( $info ) . '</span>' : '' );
                if ( '' !== $d['file'] ) {
                    echo '<li><a class="ostec-doc-link" href="' . esc_url( $d['file'] ) . '" target="_blank" rel="noopener" download>' . $testo . '<span class="ostec-doc-giu">' . $giu . '</span></a></li>';
                } else {
                    echo '<li class="ostec-doc--senza"><span class="ostec-doc-link">' . $testo . '<span class="ostec-doc-info">' . esc_html__( 'manca il file', 'olobuilder' ) . '</span></span></li>';
                }
            }
            echo '</ul></section>';
        }

        if ( $builder && '' === $nome && ! $gruppi && ! $documenti ) {
            echo '<p class="ostec-vuoto">' . esc_html__( 'Scrivi il nome del prodotto e le specifiche nel Contenuto.', 'olobuilder' ) . '</p>';
        }
        echo '</div></div></div>';

        // ── Dati Schema.org del prodotto (solo sul sito) ──
        if ( ! $builder && ! empty( $s['dati_strutturati'] ) && '' !== $nome ) {
            $ld = [
                '@context' => 'https://schema.org',
                '@type'    => 'Product',
                'name'     => $nome,
                'brand'    => [ '@type' => 'Brand', 'name' => wp_strip_all_tags( get_bloginfo( 'name' ) ) ],
            ];
            if ( '' !== $codice ) {
                $ld['sku'] = $codice;
                $ld['mpn'] = $codice;
            }
            if ( '' !== $descr ) {
                $ld['description'] = $descr;
            }
            if ( '' !== $img ) {
                $ld['image'] = esc_url_raw( $img );
            }
            $props = [];
            foreach ( $gruppi as $righe ) {
                foreach ( $righe as $r ) {
                    if ( '' === $r['nome'] || '' === $r['valore'] ) {
                        continue;
                    }
                    $p = [ '@type' => 'PropertyValue', 'name' => $r['nome'], 'value' => $r['valore'] ];
                    if ( '' !== $r['unita'] ) {
                        $p['unitText'] = $r['unita'];
                    }
                    $props[] = $p;
                }
            }
            if ( $props ) {
                $ld['additionalProperty'] = $props;
            }
            echo '<script type="application/ld+json">' . wp_json_encode( $ld, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP ) . '</script>';
        }
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }
}
