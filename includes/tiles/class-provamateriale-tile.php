<?php
/**
 * Tile Prova materiale — la foto di un materiale in alta risoluzione con la LENTE che ingrandisce dove passa il
 * puntatore, e i campioni per cambiare finitura o colore (piastrelle, marmi, tessuti, legni, vernici).
 *
 * La lente è un cerchio (o un quadrato) che segue il puntatore e mostra la stessa foto ingrandita: sfondo = la foto
 * grande, misura = la foto com'è mostrata (ritaglio «cover» compreso) × l'ingrandimento. Vicino ai bordi la lente
 * resta dentro la foto, e il punto ingrandito arriva comunque fino all'orlo. Sul telefono un tocco la accende nel
 * punto toccato, trascinando si sposta (touch-action:none sulla foto SOLO mentre è accesa: a lente spenta la pagina
 * scorre), un tocco senza trascinare la spegne. Da tastiera la foto si raggiunge col Tab: Invio o spazio accendono
 * la lente al centro, le frecce la spostano (con Maiuscolo a passi lunghi), Esc la spegne.
 * I campioni sono pulsanti (aria-pressed): il cambio passa in dissolvenza (300 ms, subito con «riduci movimento»)
 * e aggiorna foto, testo alternativo e testi; la foto grande si carica al passaggio o al fuoco sul campione.
 * Nata per i temi industriali della fase 3 (14 ott 2026). Config: src/config/elements/provamateriale.js.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Provamateriale_Tile extends Olobuild_Tile_Base {

    protected $type     = 'provamateriale';
    protected $name     = 'Prova materiale';
    protected $icon     = 'dashicons-search';
    protected $category = 'media';

    protected $defaults = [
        // contenuto
        'voci'                       => [
            [ 'id' => 'pm-1', 'nome' => 'Smalto bianco', 'codice' => 'SM-01', 'testo' => 'Smalto lucido steso a mano: la luce scivola sulle curve senza riflessi duri.', 'immagine' => '', 'miniatura' => '', 'colore' => '', 'alt' => '' ],
            [ 'id' => 'pm-2', 'nome' => 'Blu cobalto', 'codice' => 'BC-07', 'testo' => 'Porcellana blu col decoro dipinto a mano, cotta due volte.', 'immagine' => '', 'miniatura' => '', 'colore' => '', 'alt' => '' ],
            [ 'id' => 'pm-3', 'nome' => 'Rovere naturale', 'codice' => 'RV-12', 'testo' => 'Rovere spazzolato e oliato: la venatura resta in rilievo sotto le dita.', 'immagine' => '', 'miniatura' => '', 'colore' => '', 'alt' => '' ],
        ],
        'iniziale'                   => 0,
        'lente'                      => true,
        'lente_zoom'                 => 2.5,
        'mostra_suggerimento'        => true,
        'testo_suggerimento'         => 'Passa sopra la foto per vedere i dettagli',
        'testo_suggerimento_touch'   => 'Tocca la foto per ingrandirla',
        'mostra_testi'               => true,
        'etichetta_campioni'         => 'Finitura',
        // stile
        'accento'                    => '',
        'colore_testo'               => '',
        'colore_tenue'               => '',
        'sfondo_pannello'            => '',
        'ombra'                      => 'none',
        'ombra_h'                    => '0',
        'ombra_v'                    => '4',
        'ombra_blur'                 => '10',
        'ombra_spread'               => '0',
        'ombra_color'                => 'rgba(0,0,0,0.15)',
        'ombra_inset'                => false,
        'etichetta_font_family'      => '',
        'etichetta_font_size'        => 13,
        'etichetta_font_size_tablet' => '',
        'etichetta_font_size_mobile' => '',
        'etichetta_font_weight'      => '600',
        'etichetta_uppercase'        => true,
        'etichetta_letter_spacing'   => 1.2,
        'etichetta_color'            => '',
        'nome_tag'                   => 'h3',
        'nome_font_family'           => 'heading',
        'nome_font_size'             => 26,
        'nome_font_size_tablet'      => '',
        'nome_font_size_mobile'      => '',
        'nome_font_weight'           => '600',
        'nome_line_height'           => 1.15,
        'nome_color'                 => '',
        'codice_font_family'         => '',
        'codice_font_size'           => 13,
        'codice_font_size_tablet'    => '',
        'codice_font_size_mobile'    => '',
        'codice_font_weight'         => '600',
        'codice_uppercase'           => true,
        'codice_letter_spacing'      => 1.5,
        'codice_color'               => '',
        'testo_font_family'          => '',
        'testo_font_size'            => 15,
        'testo_font_size_tablet'     => '',
        'testo_font_size_mobile'     => '',
        'testo_font_weight'          => '400',
        'testo_line_height'          => 1.6,
        'testo_color'                => '',
        'raggio_immagine'            => [ 'tl' => 14, 'tr' => 14, 'br' => 14, 'bl' => 14 ],
        'proporzione'                => '4/3',
        'lente_forma'                => 'cerchio',
        'lente_misura'               => 180,
        'campione_forma'             => 'pallino',
        'campione_misura'            => 44,
        'disposizione'               => 'sotto',
        'lato'                       => 'destra',
        'larghezza_immagine'         => 62,
        'gap'                        => 32,
        'allineamento'               => 'left',
    ];

    public function get_controls() {
        return [];
    }

    /** Un valore d'immagine: stringa, oppure l'oggetto del selettore ({url}). */
    private static function url_di( $v ) {
        if ( is_array( $v ) ) {
            $v = $v['url'] ?? '';
        }
        return trim( (string) $v );
    }

    /** La proporzione «L/A» della foto (anche scritta «L:A»), o 4/3. */
    private static function proporzione( $v ) {
        $v = str_replace( [ ':', ' ' ], [ '/', '' ], trim( (string) $v ) );
        if ( preg_match( '/^(\d+(?:\.\d+)?)\/(\d+(?:\.\d+)?)$/', $v, $m ) ) {
            if ( (float) $m[1] > 0 ) {
                if ( (float) $m[2] > 0 ) {
                    return $v;
                }
            }
        }
        return '4/3';
    }

    /**
     * La miniatura di una foto della Libreria media del sito (la misura «thumbnail» di WordPress): il campione non
     * deve scaricare la foto grande, che la lente vuole in alta risoluzione. Fuori dalla Libreria (la libreria dei
     * temi, un altro dominio) resta la foto, caricata solo quando serve (loading="lazy").
     */
    private static function miniatura_di( $url ) {
        if ( '' === $url ) {
            return '';
        }
        $up   = wp_get_upload_dir();
        $base = preg_replace( '#^https?:#i', '', (string) ( $up['baseurl'] ?? '' ) );
        if ( '' === $base || false === strpos( preg_replace( '#^https?:#i', '', $url ), $base ) ) {
            return $url;
        }
        $id = attachment_url_to_postid( $url );
        if ( $id ) {
            $mini = wp_get_attachment_image_url( $id, 'thumbnail' );
            if ( $mini ) {
                return $mini;
            }
        }
        return $url;
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
        $uid     = 'opm-' . $this->chiave_stabile( $settings );
        $sel     = '#' . $uid;
        $builder = ! empty( $s['_builder_mode'] );

        // ── Le voci: un nome o una foto. «iniziale» conta le voci come sono salvate (da 0) ──
        $voci = [];
        $ini  = max( 0, (int) $s['iniziale'] );
        $cur  = 0;
        foreach ( array_values( (array) $s['voci'] ) as $n => $v ) {
            if ( ! is_array( $v ) ) {
                continue;
            }
            $nome = trim( (string) ( $v['nome'] ?? '' ) );
            $img  = esc_url_raw( self::url_di( $v['immagine'] ?? '' ) );
            if ( '' === $nome && '' === $img ) {
                continue;
            }
            if ( $n === $ini ) {
                $cur = count( $voci );
            }
            $alt    = trim( (string) ( $v['alt'] ?? '' ) );
            $voci[] = [
                'n'      => $n,
                'nome'   => $nome,
                'codice' => trim( (string) ( $v['codice'] ?? '' ) ),
                'testo'  => trim( (string) ( $v['testo'] ?? '' ) ),
                'img'    => $img,
                'mini'   => esc_url_raw( self::url_di( $v['miniatura'] ?? '' ) ),
                'colore' => $this->safe_color_css( $v['colore'] ?? '' ),
                'alt'    => '' !== $alt ? $alt : $nome,
            ];
        }
        if ( ! $voci && ! $builder ) {
            return '';
        }

        // ── Lente, campioni, disposizione ──
        $lente    = ! empty( $s['lente'] );
        $zoom     = max( 1.5, min( 6, (float) $s['lente_zoom'] ) );
        $misura   = max( 100, min( 320, (int) $s['lente_misura'] ) );
        $quadrata = 'quadrato' === (string) $s['lente_forma'];
        $forma    = in_array( (string) $s['campione_forma'], [ 'pallino', 'quadretto', 'pillola' ], true ) ? (string) $s['campione_forma'] : 'pallino';
        $sw       = max( 28, min( 72, (int) $s['campione_misura'] ) );
        $a_lato   = 'lato' === (string) $s['disposizione'];
        $a_sin    = 'sinistra' === (string) $s['lato'];
        $larg     = max( 45, min( 75, (int) $s['larghezza_immagine'] ) );
        $gap      = max( 0, min( 160, (int) $s['gap'] ) );
        $centro   = 'center' === (string) $s['allineamento'];
        $ratio    = self::proporzione( $s['proporzione'] );
        $tag_n    = in_array( (string) $s['nome_tag'], [ 'h2', 'h3', 'h4', 'h5', 'p', 'div' ], true ) ? (string) $s['nome_tag'] : 'h3';

        // ── Colori: tutti dai token del tema, salvo quelli scelti ──
        $accento  = $this->safe_color_css( $s['accento'] ) ?: 'var(--olo-color-primary)';
        $tenue    = $this->safe_color_css( $s['colore_tenue'] ) ?: 'color-mix(in srgb, currentColor 62%, transparent)';
        $sfondo_p = $this->safe_color_css( $s['sfondo_pannello'] );
        $testo_c  = $this->safe_color_css( $s['colore_testo'] );
        if ( '' === $testo_c && '' !== $sfondo_p ) {
            // Un pannello con lo sfondo scelto e il testo no: il testo del tema se lì si legge, altrimenti il chiaro
            // o lo scuro della Palette. Mai il colore della sezione: su una sezione scura sarebbe chiaro su chiaro.
            $testo_c = Olobuild_Tile_Utils::testo_su_fondo( $sfondo_p, 'var(--olo-color-text, #374151)' ) ?: 'var(--olo-color-text, #374151)';
        }
        $ombra  = Olobuild_Tile_Utils::shadow_value( $s, 'ombra' );
        $raggio = Olobuild_Tile_Utils::border_radius( $s['raggio_immagine'] ) ?: '0';
        $r_max  = Olobuild_Tile_Utils::radius_int( $s['raggio_immagine'] );
        $rq     = min( $r_max, (int) round( $sw / 4 ) ); // quadretto: lo stesso raggio della foto, in piccolo
        $rl     = min( $r_max, 16 );                     // lente quadrata

        // ── Suggerimento sulla foto (un testo per il mouse, uno per il tocco) ──
        $aiuto_m = trim( (string) $s['testo_suggerimento'] );
        $aiuto_t = trim( (string) $s['testo_suggerimento_touch'] );
        if ( '' === $aiuto_t ) {
            $aiuto_t = $aiuto_m;
        }
        if ( '' === $aiuto_m ) {
            $aiuto_m = $aiuto_t;
        }
        $con_aiuto    = $lente && ! empty( $s['mostra_suggerimento'] ) && '' !== $aiuto_m;
        $con_campioni = count( $voci ) > 1;
        $con_testi    = ! empty( $s['mostra_testi'] ) && $voci;
        $etichetta    = trim( (string) $s['etichetta_campioni'] );

        $classi = 'opm opm--' . ( $a_lato ? 'lato' : 'sotto' ) . ' opm--' . $forma
            . ( $a_lato && $a_sin ? ' opm--a-sinistra' : '' )
            . ( $centro ? ' opm--centro' : '' )
            . ( $quadrata ? ' opm--lente-quadrata' : '' );

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS da colori passati da safe_color_css() e dagli helper di Olobuild_Tile_Utils, numeri forzati, proporzione da espressione regolare, costanti interne e selettori generati qui
        echo '<style>';
        echo "{$sel}{--opm-acc:{$accento};--opm-tenue:{$tenue};--opm-r:{$raggio};--opm-rq:{$rq}px;--opm-rl:{$rl}px;--opm-sw:{$sw}px;--opm-gap:{$gap}px;--opm-larg:{$larg}%;--opm-l:{$misura}px}";
        echo "{$sel} [hidden]{display:none!important}";
        echo "{$sel} .opm-corpo{display:flex;flex-direction:column;gap:var(--opm-gap)}";
        if ( $a_lato ) {
            echo "{$sel} .opm-corpo{display:grid;align-items:center;grid-template-columns:" . ( $a_sin ? 'minmax(0,1fr) minmax(0,var(--opm-larg))' : 'minmax(0,var(--opm-larg)) minmax(0,1fr)' ) . '}';
            if ( $a_sin ) {
                echo "{$sel} .opm-pannello{order:-1}";
            }
            // Sul telefono i campioni tornano sempre sotto la foto.
            echo '@media (max-width:639px){' . "{$sel} .opm-corpo{display:flex;flex-direction:column;align-items:stretch}{$sel} .opm-pannello{order:0}" . '}';
        }

        // La foto: la proporzione scelta, ritaglio «cover», il segnaposto quando manca.
        echo "{$sel} .opm-foto{position:relative;overflow:hidden;aspect-ratio:{$ratio};border-radius:var(--opm-r);background:color-mix(in srgb, currentColor 6%, transparent);isolation:isolate;touch-action:manipulation;-webkit-user-select:none;user-select:none;-webkit-tap-highlight-color:transparent" . ( 'none' !== $ombra ? ";box-shadow:{$ombra}" : '' ) . '}';
        echo "{$sel} .opm-foto[tabindex]{cursor:crosshair}";
        echo "{$sel} .opm-foto:focus{outline:none}";
        echo "{$sel} .opm-foto:focus-visible{outline:2px solid var(--opm-acc);outline-offset:3px}";
        echo "{$sel}.is-lente .opm-foto{touch-action:none}";
        echo "{$sel} .opm-img{position:absolute;inset:0;display:block;width:100%;height:100%;max-width:none;object-fit:cover;object-position:center;-webkit-user-drag:none;-webkit-touch-callout:none;pointer-events:none}";
        echo "{$sel} .opm-img--entra{opacity:0;transition:opacity .3s ease}";
        echo "{$sel} .opm-img--entra.is-dentro{opacity:1}";
        echo "{$sel} .opm-vuota{position:absolute;inset:0;display:none;flex-direction:column;align-items:center;justify-content:center;gap:.5rem;font-size:14px;line-height:1.4;text-align:center;color:color-mix(in srgb, currentColor 60%, transparent);border:1px dashed color-mix(in srgb, currentColor 28%, transparent);border-radius:inherit}";
        echo "{$sel} .opm-vuota svg,{$sel} .opm-vuota .olo-lucide-icon{width:2.25rem;height:2.25rem}";
        echo "{$sel} .is-vuota .opm-vuota{display:flex}";
        echo "{$sel} .is-vuota .opm-img,{$sel} .is-vuota .opm-lente,{$sel} .is-vuota .opm-aiuto{display:none}";

        // La lente: la foto grande come sfondo, l'anello nel colore accento (dentro, così il bordo della foto non lo taglia).
        if ( $lente ) {
            echo "{$sel} .opm-lente{position:absolute;left:0;top:0;z-index:2;width:var(--opm-l);height:var(--opm-l);border-radius:50%;pointer-events:none;opacity:0;background-color:var(--olo-color-background, transparent);background-repeat:no-repeat;box-shadow:inset 0 0 0 2px var(--opm-acc),inset 0 0 0 3px color-mix(in srgb, var(--olo-color-light, #ffffff) 45%, transparent),0 14px 34px -12px color-mix(in srgb, var(--olo-color-dark, #14161c) 60%, transparent);transition:opacity .15s ease;will-change:transform}";
            echo "{$sel}.opm--lente-quadrata .opm-lente{border-radius:var(--opm-rl)}";
            echo "{$sel}.is-lente .opm-lente{opacity:1}";
            echo "{$sel} .opm-sr{position:absolute;width:1px;height:1px;margin:-1px;padding:0;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}";
        }
        if ( $con_aiuto ) {
            echo "{$sel} .opm-aiuto{position:absolute;left:50%;bottom:.875rem;z-index:3;transform:translateX(-50%);display:inline-flex;align-items:center;gap:.45em;max-width:calc(100% - 1.5rem);padding:.45em .95em;border-radius:5em;background:color-mix(in srgb, var(--olo-color-dark, #14161c) 72%, transparent);color:var(--olo-color-light, #ffffff);font-size:12px;font-weight:600;line-height:1.3;white-space:nowrap;pointer-events:none;-webkit-backdrop-filter:blur(6px);backdrop-filter:blur(6px);transition:opacity .35s ease}";
            echo "{$sel} .opm-aiuto>span{min-width:0;overflow:hidden;text-overflow:ellipsis}";
            echo "{$sel} .opm-aiuto svg,{$sel} .opm-aiuto .olo-lucide-icon{width:1.15em;height:1.15em;flex:none}";
            echo "{$sel}.is-usata .opm-aiuto{opacity:0}";
            echo "{$sel} .opm-aiuto-tocco{display:none}";
            echo '@media (hover:none){' . "{$sel} .opm-aiuto-mouse{display:none}{$sel} .opm-aiuto-tocco{display:inline}" . '}';
        }

        // Il pannello: etichetta, campioni, nome, codice, testo.
        echo "{$sel} .opm-pannello{min-width:0;display:flex;flex-direction:column;gap:1.25rem" . ( '' !== $testo_c ? ";color:{$testo_c}" : '' ) . ( '' !== $sfondo_p ? ";background:{$sfondo_p};padding:1.5rem;border-radius:var(--opm-r)" . ( 'none' !== $ombra ? ";box-shadow:{$ombra}" : '' ) : '' ) . '}';
        echo "{$sel} .opm-scelta{display:flex;flex-direction:column;gap:.75rem}";
        echo "{$sel} .opm-etichetta{margin:0;color:var(--opm-tenue);line-height:1.3}";
        echo "{$sel} .opm-campioni{display:flex;flex-wrap:wrap;gap:.625rem;margin:0}";
        echo "{$sel} .opm-campione,{$sel} .opm-campione:hover{background:transparent}";
        echo "{$sel} .opm-campione{position:relative;display:inline-flex;align-items:center;gap:.6em;margin:0;padding:.1875rem;border:2px solid transparent;border-radius:50%;color:inherit;font:inherit;line-height:1.2;text-align:left;cursor:pointer;-webkit-appearance:none;appearance:none;box-shadow:none;transition:border-color .2s ease,transform .2s ease}";
        echo "{$sel} .opm-campione::before{content:'';position:absolute;inset:-.25rem}";
        echo "{$sel} .opm-campione:hover{transform:translateY(-1px)}";
        echo "{$sel} .opm-campione[aria-pressed=\"true\"]{border-color:var(--opm-acc)}";
        echo "{$sel} .opm-campione:focus{outline:none}";
        echo "{$sel} .opm-campione:focus-visible{outline:2px solid var(--opm-acc);outline-offset:2px}";
        echo "{$sel} .opm-vista{position:relative;display:block;flex:none;width:var(--opm-sw);height:var(--opm-sw);border-radius:inherit;overflow:hidden;background:color-mix(in srgb, currentColor 10%, transparent)}";
        echo "{$sel} .opm-vista::after{content:'';position:absolute;inset:0;border-radius:inherit;box-shadow:inset 0 0 0 1px color-mix(in srgb, currentColor 16%, transparent)}";
        echo "{$sel} .opm-vista img{display:block;width:100%;height:100%;max-width:none;object-fit:cover}";
        if ( 'quadretto' === $forma ) {
            echo "{$sel} .opm-campione{border-radius:" . ( $rq > 0 ? 'calc(var(--opm-rq) + .3125rem)' : '0' ) . '}'; // foto ad angoli vivi = campioni ad angoli vivi, anello compreso
            echo "{$sel} .opm-vista{border-radius:var(--opm-rq)}";
        } elseif ( 'pillola' === $forma ) {
            echo "{$sel} .opm-campione{border-radius:5em;padding:.1875rem 1rem .1875rem .1875rem;border:1px solid color-mix(in srgb, currentColor 18%, transparent)}";
            echo "{$sel} .opm-campione[aria-pressed=\"true\"]{border-color:var(--opm-acc);box-shadow:inset 0 0 0 1px var(--opm-acc)}";
            echo "{$sel} .opm-vista{width:calc(var(--opm-sw) - .5rem);height:calc(var(--opm-sw) - .5rem);border-radius:50%}";
            echo "{$sel} .opm-campione-nome{font-size:14px;font-weight:500;white-space:nowrap}";
        }
        echo "{$sel} .opm-testi{display:flex;flex-direction:column;gap:.4rem}";
        echo "{$sel} .opm-nome{margin:0;text-wrap:balance}";
        echo "{$sel} .opm-codice{margin:0;color:var(--opm-tenue);font-variant-numeric:tabular-nums}";
        echo "{$sel} .opm-testo{margin:.25rem 0 0;max-width:62ch;opacity:.88;text-wrap:pretty}";
        if ( $centro ) {
            echo "{$sel} .opm-pannello{align-items:center;text-align:center}";
            echo "{$sel} .opm-scelta,{$sel} .opm-testi{align-items:center}";
            echo "{$sel} .opm-campioni{justify-content:center}";
        }
        if ( $builder ) {
            echo "{$sel} .opm-nota{margin:0;padding:1rem;font-size:14px;opacity:.7;border:1px dashed color-mix(in srgb, currentColor 30%, transparent);border-radius:var(--opm-r)}";
        }
        // «Riduci movimento»: le transizioni si spengono, la lente resta.
        echo '@media (prefers-reduced-motion:reduce){' . "{$sel} .opm-lente,{$sel} .opm-img--entra,{$sel} .opm-aiuto,{$sel} .opm-campione{transition:none}" . '}';
        echo $this->css_testo( $s, 'etichetta', "{$sel} .opm-etichetta" );
        echo $this->css_testo( $s, 'nome', "{$sel} .opm-nome" );
        echo $this->css_testo( $s, 'codice', "{$sel} .opm-codice" );
        echo $this->css_testo( $s, 'testo', "{$sel} .opm-testo" );
        echo '</style>';

        if ( ! $voci ) {
            echo '<div id="' . esc_attr( $uid ) . '" class="opm"><p class="opm-nota">' . esc_html__( 'Aggiungi i campioni nel Contenuto: per ognuno la foto, il nome e il codice.', 'olobuilder' ) . '</p></div>';
            return ob_get_clean();
        }

        $v0   = $voci[ $cur ];
        $dati = [];
        foreach ( $voci as $v ) {
            $dati[] = [ 'n' => $v['n'], 'img' => $v['img'], 'alt' => $v['alt'], 'nome' => $v['nome'], 'codice' => $v['codice'], 'testo' => $v['testo'] ];
        }
        $cfg = [ 'voci' => $dati, 'iniziale' => $cur, 'zoom' => $zoom, 'misura' => $misura ];

        echo '<div id="' . esc_attr( $uid ) . '" class="' . esc_attr( $classi ) . '" data-opm="' . esc_attr( wp_json_encode( $cfg ) ) . '">';
        echo '<div class="opm-corpo">';

        // ── La foto ──
        $vuota = '' === $v0['img'];
        $attr  = ' class="opm-foto' . ( $vuota ? ' is-vuota' : '' ) . '"';
        if ( $lente ) {
            // La foto si raggiunge da tastiera: role="img" col testo alternativo, le istruzioni per la lente a parte.
            $attr .= ' tabindex="0" role="img" aria-label="' . esc_attr( $v0['alt'] ) . '" aria-describedby="' . esc_attr( $uid ) . '-aiuto" data-olo-own-drag';
        }
        echo '<div' . $attr . '>';
        if ( ! $vuota ) {
            echo '<img class="opm-img" src="' . esc_url( $v0['img'] ) . '" alt="' . esc_attr( $lente ? '' : $v0['alt'] ) . '" draggable="false" loading="lazy" decoding="async">';
        }
        echo '<span class="opm-vuota" aria-hidden="true">' . $this->render_icon_html( 'image', 1.6, 'aria-hidden="true"' )
            . ( $builder ? '<span>' . esc_html__( 'Carica la foto del campione nel Contenuto', 'olobuilder' ) . '</span>' : '' ) . '</span>';
        if ( $lente ) {
            echo '<span class="opm-lente" aria-hidden="true"></span>';
            echo '<span class="opm-sr" id="' . esc_attr( $uid ) . '-aiuto">' . esc_html__( 'Invio accende la lente d\'ingrandimento, le frecce la spostano, Esc la chiude.', 'olobuilder' ) . '</span>';
        }
        if ( $con_aiuto ) {
            echo '<span class="opm-aiuto" aria-hidden="true">' . $this->render_icon_html( 'zoom-in', 0.7, 'aria-hidden="true"' )
                . '<span class="opm-aiuto-mouse">' . esc_html( $aiuto_m ) . '</span><span class="opm-aiuto-tocco">' . esc_html( $aiuto_t ) . '</span></span>';
        }
        echo '</div>';

        // ── Il pannello ──
        if ( $con_campioni || $con_testi ) {
            echo '<div class="opm-pannello">';
            if ( $con_campioni ) {
                echo '<div class="opm-scelta">';
                if ( '' !== $etichetta ) {
                    echo '<p class="opm-etichetta" id="' . esc_attr( $uid ) . '-et"' . ( $builder ? ' data-olo-editable="etichetta_campioni"' : '' ) . '>' . esc_html( $etichetta ) . '</p>';
                    echo '<div class="opm-campioni" role="group" aria-labelledby="' . esc_attr( $uid ) . '-et">';
                } else {
                    echo '<div class="opm-campioni" role="group" aria-label="' . esc_attr__( 'Campioni', 'olobuilder' ) . '">';
                }
                foreach ( $voci as $i => $v ) {
                    /* translators: %d: numero del campione */
                    $nome_vis = '' !== $v['nome'] ? $v['nome'] : sprintf( __( 'Campione %d', 'olobuilder' ), $i + 1 );
                    $aria     = $nome_vis . ( '' !== $v['codice'] ? ' · ' . $v['codice'] : '' );
                    $vista    = '';
                    $tinta    = '';
                    if ( '' !== $v['mini'] ) {
                        $vista = '<img src="' . esc_url( $v['mini'] ) . '" alt="" loading="lazy" decoding="async">';
                    } elseif ( '' !== $v['colore'] ) {
                        $tinta = ' style="background:' . esc_attr( $v['colore'] ) . '"';
                    } elseif ( '' !== $v['img'] ) {
                        $vista = '<img src="' . esc_url( self::miniatura_di( $v['img'] ) ) . '" alt="" loading="lazy" decoding="async">';
                    }
                    echo '<button type="button" class="opm-campione" data-i="' . (int) $i . '" aria-pressed="' . ( $i === $cur ? 'true' : 'false' ) . '" aria-label="' . esc_attr( $aria ) . '"'
                        . ( 'pillola' !== $forma ? ' title="' . esc_attr( $nome_vis ) . '"' : '' ) . ' data-olo-interactive>'
                        . '<span class="opm-vista"' . $tinta . '>' . $vista . '</span>'
                        . ( 'pillola' === $forma ? '<span class="opm-campione-nome">' . esc_html( $nome_vis ) . '</span>' : '' )
                        . '</button>';
                }
                echo '</div></div>';
            }
            if ( $con_testi ) {
                // Nel builder i testi si modificano in linea sulla voce mostrata (voci.N.campo): lo script segue il cambio.
                $in_linea = function ( $campo ) use ( $builder, $v0 ) {
                    return $builder ? ' data-olo-editable="voci.' . (int) $v0['n'] . '.' . $campo . '"' : '';
                };
                echo '<div class="opm-testi" aria-live="polite">';
                echo '<' . $tag_n . ' class="opm-nome"' . $in_linea( 'nome' ) . ( '' === $v0['nome'] ? ' hidden' : '' ) . '>' . esc_html( $v0['nome'] ) . '</' . $tag_n . '>';
                echo '<p class="opm-codice"' . $in_linea( 'codice' ) . ( '' === $v0['codice'] ? ' hidden' : '' ) . '>' . esc_html( $v0['codice'] ) . '</p>';
                echo '<p class="opm-testo"' . $in_linea( 'testo' ) . ( '' === $v0['testo'] ? ' hidden' : '' ) . '>' . esc_html( $v0['testo'] ) . '</p>';
                echo '</div>';
            }
            echo '</div>';
        }
        echo '</div></div>';

        // Lo script serve alla lente e al cambio di campione: avvia ogni istanza della pagina una volta sola.
        if ( $lente || $con_campioni ) {
            echo '<script>' . $this->script() . '[].forEach.call(document.querySelectorAll("[data-opm]"),function(x){window.oloProvaMateriale(x);});</script>';
        }
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }

    /**
     * La funzione della lente e dei campioni. Va in OGNI istanza (definita una volta sola, «if(!window…)»): una
     * sezione caricata dopo (<template> delle sezioni pigre) può eseguire il suo script prima di quello della prima
     * istanza, e senza la funzione la tile resterebbe ferma. Niente «&» nello script (wptexturize lo riscrive fra un
     * «<» e un «>»): condizioni annidate. Ogni istanza si avvia una volta (data-opm-pronto), anche quando lo script
     * rigira (anteprima del builder, sezioni caricate dopo).
     */
    private function script() {
        return <<<'JS'
if(!window.oloProvaMateriale){window.oloProvaMateriale=function(el){
if(!el){return;}
if(el.getAttribute("data-opm-pronto")==="1"){return;}
el.setAttribute("data-opm-pronto","1");
var C={};try{C=JSON.parse(el.getAttribute("data-opm")||"{}");}catch(e){}
var voci=C.voci||[],cur=+C.iniziale||0,zoom=+C.zoom||2.5,L=+C.misura||180;
zoom=Math.max(1.5,Math.min(6,zoom));L=Math.max(100,Math.min(320,L));
var foto=el.querySelector(".opm-foto");if(!foto){return;}
var lente=foto.querySelector(".opm-lente"),img=foto.querySelector(".opm-img"),conRuolo=foto.hasAttribute("role");
var riduci=false;try{riduci=window.matchMedia("(prefers-reduced-motion: reduce)").matches;}catch(e){}
var acceso=false,px=0,py=0,tocco=null,giro=0,viste={};
function fra(v,a,b){return Math.max(a,Math.min(b,v));}
function url(u){return 'url("'+String(u).replace(/["\\\n\r]/g,function(c){return encodeURIComponent(c);})+'")';}
function misure(){
if(!img){return null;}
var nw=img.naturalWidth,nh=img.naturalHeight;if(!nw){return null;}if(!nh){return null;}
var r=foto.getBoundingClientRect(),W=r.width,H=r.height;if(!W){return null;}if(!H){return null;}
var k=Math.max(W/nw,H/nh),rw=nw*k,rh=nh*k;
return {W:W,H:H,rw:rw,rh:rh,ox:(W-rw)/2,oy:(H-rh)/2};
}
function posa(x,y){
var m=misure();if(!m){return false;}
var Le=Math.min(L,m.W,m.H),h=Le/2,hz=h/zoom;
px=fra(x,0,m.W);py=fra(y,0,m.H);
var lx=fra(px,h,m.W-h),ly=fra(py,h,m.H-h),cx=fra(px,hz,m.W-hz),cy=fra(py,hz,m.H-hz),st=lente.style;
st.width=Le+"px";st.height=Le+"px";
st.transform="translate3d("+(lx-h)+"px,"+(ly-h)+"px,0)";
st.backgroundSize=(m.rw*zoom)+"px "+(m.rh*zoom)+"px";
st.backgroundPosition=(h-(cx-m.ox)*zoom)+"px "+(h-(cy-m.oy)*zoom)+"px";
return true;
}
function accendi(x,y){
if(!lente){return;}if(foto.classList.contains("is-vuota")){return;}
if(!posa(x,y)){return;}
acceso=true;el.classList.add("is-lente");el.classList.add("is-usata");
}
function spegni(){acceso=false;tocco=null;el.classList.remove("is-lente");}
function punto(e){var r=foto.getBoundingClientRect();return {x:e.clientX-r.left,y:e.clientY-r.top};}
if(voci[cur]){if(voci[cur].img){viste[voci[cur].img]=1;}}
if(lente){
lente.style.backgroundImage=voci[cur]?(voci[cur].img?url(voci[cur].img):"none"):"none";
foto.addEventListener("pointerenter",function(e){if(e.pointerType==="touch"){return;}var p=punto(e);accendi(p.x,p.y);});
foto.addEventListener("pointermove",function(e){
var p=punto(e);
if(e.pointerType==="touch"){
if(!tocco){return;}if(e.pointerId!==tocco.id){return;}
if(!tocco.via){var dx=p.x-tocco.x,dy=p.y-tocco.y;if(dx*dx+dy*dy<64){return;}tocco.via=true;}
if(acceso){posa(p.x,p.y);}
return;
}
if(acceso){posa(p.x,p.y);}else{accendi(p.x,p.y);}
});
foto.addEventListener("pointerleave",function(e){if(e.pointerType==="touch"){return;}spegni();});
foto.addEventListener("pointerdown",function(e){
if(e.pointerType!=="touch"){return;}
var p=punto(e);tocco={id:e.pointerId,x:p.x,y:p.y,via:false,era:acceso};
if(acceso){try{foto.setPointerCapture(e.pointerId);}catch(x){}}
});
foto.addEventListener("pointerup",function(e){
if(e.pointerType!=="touch"){return;}
var t=tocco;tocco=null;if(!t){return;}if(t.via){return;}
if(t.era){spegni();return;}
var p=punto(e);accendi(p.x,p.y);
});
foto.addEventListener("pointercancel",function(){tocco=null;});
document.addEventListener("pointerdown",function(e){if(!acceso){return;}if(e.pointerType!=="touch"){return;}if(foto.contains(e.target)){return;}spegni();},true);
foto.addEventListener("keydown",function(e){
var k=e.key,m,d,x,y;
if(k==="Enter"||k===" "||k==="Spacebar"){e.preventDefault();if(acceso){spegni();return;}m=misure();if(m){accendi(m.W/2,m.H/2);}return;}
if(k==="Escape"||k==="Esc"){if(acceso){e.preventDefault();spegni();}return;}
if(!acceso){return;}
m=misure();if(!m){return;}
d=Math.max(6,Math.round(Math.min(m.W,m.H)*(e.shiftKey?0.15:0.05)));x=px;y=py;
if(k==="ArrowLeft"){x-=d;}else if(k==="ArrowRight"){x+=d;}else if(k==="ArrowUp"){y-=d;}else if(k==="ArrowDown"){y+=d;}else{return;}
e.preventDefault();posa(x,y);
});
foto.addEventListener("blur",function(){if(acceso){spegni();}});
window.addEventListener("resize",function(){if(acceso){posa(px,py);}});
}
var bott=[].slice.call(el.querySelectorAll(".opm-campione"));
function precarica(i){var v=voci[i];if(!v){return;}if(!v.img){return;}if(viste[v.img]){return;}viste[v.img]=1;var im=new Image();im.decoding="async";im.src=v.img;}
function scrivi(campo,v){var n=el.querySelector(".opm-"+campo);if(!n){return;}var val=v[campo]||"";n.textContent=val;if(val){n.removeAttribute("hidden");}else{n.setAttribute("hidden","");}if(n.hasAttribute("data-olo-editable")){n.setAttribute("data-olo-editable","voci."+v.n+"."+campo);}}
function cambia(u,alt){
spegni();
var mio=++giro;
if(lente){lente.style.backgroundImage=u?url(u):"none";}
if(!u){foto.classList.add("is-vuota");return;}
var nuova=document.createElement("img");
nuova.className="opm-img";nuova.alt=conRuolo?"":alt;nuova.setAttribute("draggable","false");nuova.decoding="async";
var vai=function(){
if(mio!==giro){return;}
var vecchie=[].slice.call(foto.querySelectorAll(".opm-img"));
foto.classList.remove("is-vuota");
foto.insertBefore(nuova,foto.querySelector(".opm-vuota"));
img=nuova;
if(riduci){vecchie.forEach(function(x){if(x.parentNode){x.parentNode.removeChild(x);}});return;}
nuova.classList.add("opm-img--entra");
requestAnimationFrame(function(){requestAnimationFrame(function(){nuova.classList.add("is-dentro");});});
setTimeout(function(){
vecchie.forEach(function(x){if(x.parentNode){x.parentNode.removeChild(x);}});
nuova.classList.remove("opm-img--entra");nuova.classList.remove("is-dentro");
},340);
};
nuova.src=u;
if(nuova.decode){nuova.decode().then(vai,vai);}else{nuova.onload=vai;nuova.onerror=vai;}
}
function scegli(i){
var v=voci[i];if(!v){return;}if(i===cur){return;}
cur=i;
bott.forEach(function(b){b.setAttribute("aria-pressed",+b.getAttribute("data-i")===i?"true":"false");});
scrivi("nome",v);scrivi("codice",v);scrivi("testo",v);
if(conRuolo){foto.setAttribute("aria-label",v.alt||v.nome||"");}
cambia(v.img,v.alt||v.nome||"");
}
bott.forEach(function(b){
var i=+b.getAttribute("data-i");
b.addEventListener("click",function(){scegli(i);});
b.addEventListener("pointerenter",function(){precarica(i);});
b.addEventListener("focus",function(){precarica(i);});
});
};}
JS;
    }
}
