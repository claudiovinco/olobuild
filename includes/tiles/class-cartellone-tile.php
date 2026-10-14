<?php
/**
 * Tile Cartellone — il programma di una stagione (teatro, concerti, rassegne, corsi).
 *
 * Ogni voce è un appuntamento con data e ora (e, se dura più giorni, la data di fine), genere,
 * compagnia, luogo, durata, prezzo, stato (ultimi posti, esaurito…), etichette (sopratitoli,
 * prima nazionale…), l'incontro col pubblico che lo accompagna, una foto e i link.
 *
 * Tre viste: elenco (con i mesi), schede, o il solo PROSSIMO appuntamento col conto alla rovescia.
 * Filtri per genere e per mese e ricerca, nel browser. Gli appuntamenti passati si nascondono (o si
 * attenuano) nel browser, nel fuso del sito: la pagina resta giusta anche servita dalla cache.
 * «Aggiungi al calendario» scarica il file .ics dell'appuntamento. Nel builder si vedono tutti, i
 * passati attenuati. Con «Appuntamenti da: Spettacoli del sito» le voci sono gli spettacoli del menu
 * Spettacoli (Olobuild_Spettacoli), gli stessi in ogni copia della tile; nelle demo dei temi restano le voci.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Cartellone_Tile extends Olobuild_Tile_Base {

    protected $type     = 'cartellone';
    protected $name     = 'Cartellone';
    protected $icon     = 'dashicons-tickets-alt';
    protected $category = 'interactive';

    protected $defaults = [
        // contenuto
        'fonte'             => 'voci',
        'vista'             => 'lista',
        'raggruppa'         => true,
        'ordina'            => true,
        'limite'            => 0,
        'passati'           => 'nascondi',
        'filtri_generi'     => true,
        'filtri_mesi'       => false,
        'ricerca'           => false,
        'solo_genere'       => '',
        'escludi_generi'    => '',
        'immagini'          => true,
        'testo_tutti'       => 'Tutti',
        'testo_biglietti'   => 'Biglietti',
        'testo_info'        => 'Scopri',
        'calendario'        => true,
        'testo_calendario'  => 'Aggiungi al calendario',
        'luogo_evento'      => '',
        'conto'             => true,
        'testo_conto'       => 'Si apre il sipario tra',
        'occhiello_prossimo' => 'Prossimo spettacolo',
        'vuoto_testo'       => 'Nessun appuntamento in programma: il nuovo cartellone arriva presto.',
        'nessun_risultato'  => 'Nessun appuntamento per questa scelta.',
        'generi'            => [
            [ 'id' => 'gen-1', 'nome' => 'Prosa', 'colore' => '' ],
            [ 'id' => 'gen-2', 'nome' => 'Musica', 'colore' => '' ],
            [ 'id' => 'gen-3', 'nome' => 'Incontri', 'colore' => '' ],
        ],
        'voci'              => [
            [ 'id' => 'voce-1', 'titolo' => 'La locandiera', 'sottotitolo' => 'di Carlo Goldoni · Compagnia dell\'Arco', 'genere' => 'Prosa', 'data' => '2026-11-14', 'ora' => '20:30', 'data_fine' => '', 'orari' => '', 'luogo' => 'Sala grande', 'durata' => '2 h 10 min con intervallo', 'prezzo' => 'da 14 €', 'stato' => '', 'etichette' => '', 'descrizione' => '', 'incontro' => '', 'immagine' => '', 'link_biglietti' => '#', 'link_info' => '' ],
            [ 'id' => 'voce-2', 'titolo' => 'Le quattro stagioni', 'sottotitolo' => 'Orchestra da camera della città', 'genere' => 'Musica', 'data' => '2026-11-28', 'ora' => '21:00', 'data_fine' => '', 'orari' => '', 'luogo' => 'Sala grande', 'durata' => '1 h 20 min', 'prezzo' => 'da 12 €', 'stato' => 'ultimi', 'etichette' => '', 'descrizione' => '', 'incontro' => '', 'immagine' => '', 'link_biglietti' => '#', 'link_info' => '' ],
            [ 'id' => 'voce-3', 'titolo' => 'Prima del sipario', 'sottotitolo' => 'Conversazione con il regista', 'genere' => 'Incontri', 'data' => '2026-12-05', 'ora' => '18:00', 'data_fine' => '', 'orari' => '', 'luogo' => 'Foyer', 'durata' => '1 h', 'prezzo' => '', 'stato' => 'gratuito', 'etichette' => '', 'descrizione' => '', 'incontro' => '', 'immagine' => '', 'link_biglietti' => '', 'link_info' => '' ],
        ],

        // stile
        'accento'           => '',
        'sfondo_voce'       => '',
        'bordo_voce'        => [],
        'linea_colore'      => '',
        'raggio'            => [ 'tl' => 14, 'tr' => 14, 'br' => 14, 'bl' => 14 ],
        'raggio_pulsanti'   => [ 'tl' => 999, 'tr' => 999, 'br' => 999, 'bl' => 999 ],
        'padding_voce'      => [ 'top' => 28, 'right' => 0, 'bottom' => 28, 'left' => 0 ],
        'gap'               => 28,
        'colonne'           => 3,
        'immagine_lato'     => 'destra',
        'larghezza_data'    => 120,
        'img_ratio'         => '4/3',
        'img_object_position' => 'center center',
        'shadow'            => 'none',
        'filtri_aspetto'    => 'pillole',
        'giorno_font_family'    => 'heading',
        'giorno_font_size'      => 56,
        'giorno_font_weight'    => '400',
        'giorno_color'          => '',
        'mese_font_size'        => 12,
        'mese_font_weight'      => '600',
        'mese_uppercase'        => true,
        'mese_letter_spacing'   => 2,
        'mese_color'            => '',
        'genere_font_size'      => 11,
        'genere_font_weight'    => '600',
        'genere_uppercase'      => true,
        'genere_letter_spacing' => 2,
        'titolo_tag'            => 'h3',
        'titolo_font_family'    => 'heading',
        'titolo_font_size'      => 30,
        'titolo_font_size_tablet' => '',
        'titolo_font_size_mobile' => '',
        'titolo_font_weight'    => '400',
        'titolo_line_height'    => 1.1,
        'titolo_letter_spacing' => '',
        'titolo_italic'         => false,
        'titolo_color'          => '',
        'sottotitolo_font_family' => '',
        'sottotitolo_font_size' => 16,
        'sottotitolo_italic'    => true,
        'sottotitolo_color'     => '',
        'meta_font_size'        => 14,
        'meta_color'            => '',
        'descrizione_font_size' => 15,
        'descrizione_line_height' => 1.6,
        'descrizione_color'     => '',
        'pulsante_font_size'    => 13,
        'pulsante_font_weight'  => '600',
        'pulsante_uppercase'    => true,
        'pulsante_letter_spacing' => 1.5,
        'conto_font_family'     => 'heading',
        'conto_font_size'       => 48,
        'conto_color'           => '',
    ];

    const STATI = [ 'ultimi', 'esaurito', 'annullato', 'gratuito', 'prenotazione', 'rinviato' ];

    public function get_controls() {
        return [];
    }

    /** Etichette degli stati, tradotte. */
    private static function stati() {
        return [
            'ultimi'       => __( 'Ultimi posti', 'olobuilder' ),
            'esaurito'     => __( 'Esaurito', 'olobuilder' ),
            'annullato'    => __( 'Annullato', 'olobuilder' ),
            'gratuito'     => __( 'Ingresso libero', 'olobuilder' ),
            'prenotazione' => __( 'Su prenotazione', 'olobuilder' ),
            'rinviato'     => __( 'Rinviato', 'olobuilder' ),
        ];
    }

    /** Timestamp di data + ora nel fuso del sito (null se la data non è valida). */
    private static function istante( $data, $ora ) {
        $data = trim( (string) $data );
        if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $data ) ) {
            return null;
        }
        $ora = trim( (string) $ora );
        if ( ! preg_match( '/^\d{1,2}:\d{2}$/', $ora ) ) {
            $ora = '00:00';
        }
        $d = DateTime::createFromFormat( 'Y-m-d H:i', $data . ' ' . $ora, wp_timezone() );
        return $d ? $d->getTimestamp() : null;
    }

    /**
     * Data a parole nella lingua del sito. Nelle lingue in cui mesi e giorni si scrivono minuscoli
     * (italiano, spagnolo, francese…) i nomi tornano minuscoli: WordPress in italiano dà «Novembre»,
     * e in una frase si legge «sabato 7 novembre». $maiuscola = prima lettera maiuscola (inizio di riga).
     */
    private static function data_a_parole( $formato, $ts, $maiuscola = false ) {
        $t    = wp_date( $formato, $ts );
        $loc  = strtolower( substr( (string) determine_locale(), 0, 2 ) );
        if ( in_array( $loc, [ 'it', 'es', 'fr', 'pt', 'ca', 'ro', 'nl', 'pl', 'cs', 'sk', 'sv', 'da', 'nb', 'nn', 'fi', 'hu', 'hr', 'sl', 'gl', 'eu' ], true ) ) {
            $t = function_exists( 'mb_strtolower' ) ? mb_strtolower( $t, 'UTF-8' ) : strtolower( $t );
        }
        if ( $maiuscola && function_exists( 'mb_substr' ) ) {
            $t = mb_strtoupper( mb_substr( $t, 0, 1, 'UTF-8' ), 'UTF-8' ) . mb_substr( $t, 1, null, 'UTF-8' );
        }
        return $t;
    }

    /** Minuti di una durata scritta a parole («2 h 10 min», «90 min», «1h30»); 0 se non si capisce. */
    private static function minuti( $testo ) {
        $t = strtolower( (string) $testo );
        $h = 0;
        $m = 0;
        if ( preg_match( '/(\d+(?:[.,]\d+)?)\s*(?:h|ore|ora)\b/u', $t, $x ) ) {
            $h = (float) str_replace( ',', '.', $x[1] );
        }
        if ( preg_match( '/(\d+)\s*(?:min|m\b|\')/u', $t, $x ) ) {
            $m = (int) $x[1];
        } elseif ( preg_match( '/\d\s*h\s*(\d{2})\b/u', $t, $x ) ) {
            $m = (int) $x[1];
        }
        return (int) round( $h * 60 + $m );
    }

    /** CSS di un testo dal controllo tipografia (famiglia, misura per dispositivo, peso, maiuscolo, spaziatura, interlinea, corsivo, colore). */
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
        if ( array_key_exists( $k . '_italic', $s ) ) {
            $d .= 'font-style:' . ( ! empty( $s[ $k . '_italic' ] ) ? 'italic' : 'normal' ) . ';';
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
        $uid     = 'ocrt-' . $this->chiave_stabile( $settings );
        $sel     = '#' . $uid;
        $builder = ! empty( $s['_builder_mode'] );
        $vista   = in_array( (string) $s['vista'], [ 'lista', 'schede', 'prossimo' ], true ) ? (string) $s['vista'] : 'lista';
        $passati = in_array( (string) $s['passati'], [ 'nascondi', 'attenua', 'mostra' ], true ) ? (string) $s['passati'] : 'nascondi';
        $immag   = ! empty( $s['immagini'] );
        $stati   = self::stati();
        $ora_ts  = time();

        // ── Generi: l'ordine dei filtri e i colori (dal tema, uno dopo l'altro, se non scelti) ──
        $tavolozza = [
            'var(--olo-color-primary)', 'var(--olo-color-accent)', 'var(--olo-color-secondary)',
            'color-mix(in srgb, var(--olo-color-primary) 55%, var(--olo-color-accent))',
            'color-mix(in srgb, var(--olo-color-secondary) 55%, var(--olo-color-accent))',
            'color-mix(in srgb, var(--olo-color-primary) 50%, var(--olo-color-secondary))',
        ];
        $generi = [];
        foreach ( (array) $s['generi'] as $g ) {
            $nome = trim( (string) ( $g['nome'] ?? '' ) );
            if ( '' === $nome ) {
                continue;
            }
            $slug = sanitize_title( $nome );
            if ( isset( $generi[ $slug ] ) ) {
                continue;
            }
            $generi[ $slug ] = [ 'nome' => $nome, 'colore' => $this->safe_color_css( $g['colore'] ?? '' ) ];
        }

        // ── Voci: quelle della tile o gli spettacoli del sito (nelle demo dei temi le voci del tema) ──
        if ( 'spettacoli' === (string) $s['fonte'] && class_exists( 'Olobuild_Spettacoli' ) ) {
            $s['voci'] = Olobuild_Spettacoli::voci_per_cartellone( (array) $s['voci'] );
        }

        // ── Voci: valide, ordinate per data, con quanto serve al browser ──
        $solo    = sanitize_title( (string) $s['solo_genere'] );
        $escludi = array_filter( array_map( 'sanitize_title', explode( ',', (string) $s['escludi_generi'] ) ) );
        $voci    = [];
        foreach ( (array) $s['voci'] as $i => $v ) {
            $tit = trim( (string) ( $v['titolo'] ?? '' ) );
            $ts  = self::istante( $v['data'] ?? '', $v['ora'] ?? '' );
            if ( '' === $tit || null === $ts ) {
                continue;
            }
            $gen  = trim( (string) ( $v['genere'] ?? '' ) );
            $gslg = '' !== $gen ? sanitize_title( $gen ) : '';
            if ( '' !== $solo && $gslg !== $solo ) {
                continue;
            }
            if ( '' !== $gslg && in_array( $gslg, $escludi, true ) ) {
                continue;
            }
            if ( '' !== $gslg && ! isset( $generi[ $gslg ] ) ) {
                $generi[ $gslg ] = [ 'nome' => $gen, 'colore' => '' ];
            }
            $fine_d = self::istante( $v['data_fine'] ?? '', '23:59' );
            $durata = self::minuti( $v['durata'] ?? '' );
            // fine dell'appuntamento: l'ultimo giorno della tenitura, o l'inizio più la durata (3 ore se non c'è)
            $fine   = ( null !== $fine_d && $fine_d > $ts ) ? $fine_d : $ts + 60 * ( $durata > 0 ? $durata : 180 );
            $voci[] = [ 'v' => $v, 'ts' => $ts, 'fine' => $fine, 'gen' => $gen, 'gslg' => $gslg, 'ord' => $i, 'durata' => $durata ];
        }
        if ( ! empty( $s['ordina'] ) ) {
            usort( $voci, static function ( $a, $b ) {
                return $a['ts'] === $b['ts'] ? $a['ord'] - $b['ord'] : $a['ts'] - $b['ts'];
            } );
        }
        $k = 0;
        foreach ( $generi as $slug => $g ) {
            if ( '' === $g['colore'] ) {
                $generi[ $slug ]['colore'] = $tavolozza[ $k % count( $tavolozza ) ];
            }
            $k++;
        }
        // nei filtri solo i generi che hanno almeno una voce
        $usati = [];
        foreach ( $voci as $x ) {
            if ( '' !== $x['gslg'] ) {
                $usati[ $x['gslg'] ] = true;
            }
        }

        // ── Colori e misure ──
        $accento = $this->safe_color_css( $s['accento'] ) ?: 'var(--olo-color-primary)';
        $su_acc  = Olobuild_Tile_Utils::testo_su_fondo( $accento, 'var(--olo-color-light, #ffffff)' ) ?: 'var(--olo-color-light, #ffffff)';
        $sfondo  = $this->safe_color_css( $s['sfondo_voce'] );
        $linea   = $this->safe_color_css( $s['linea_colore'] ) ?: 'color-mix(in srgb, currentColor 16%, transparent)';
        $bordo   = Olobuild_Tile_Utils::border_css( $s['bordo_voce'] ?? null );
        $raggio  = Olobuild_Tile_Utils::border_radius( $s['raggio'] ) ?: '0';
        $r_puls  = Olobuild_Tile_Utils::border_radius( $s['raggio_pulsanti'] ) ?: '999px';
        $pad     = Olobuild_Tile_Utils::sides_css( Olobuild_Tile_Utils::spacing_sides( $s['padding_voce'] ?? null, [], [ 28, 0, 28, 0 ] ) );
        $gap     = max( 0, min( 96, absint( $s['gap'] ) ) );
        $col     = max( 1, min( 4, absint( $s['colonne'] ) ) );
        $lato    = in_array( (string) $s['immagine_lato'], [ 'destra', 'sinistra' ], true ) ? (string) $s['immagine_lato'] : 'destra';
        $l_data  = max( 64, min( 220, absint( $s['larghezza_data'] ) ) );
        $ombra   = Olobuild_Tile_Utils::shadow_value( $s, 'shadow' );
        $cornice = Olobuild_Tile_Utils::image_frame( $s, 'img', [ 'ratio' => '4/3', 'fit' => 'cover' ] );
        $img_ar  = '' !== $cornice['contenitore'] ? $cornice['contenitore'] : 'aspect-ratio:4/3;';
        $img_fit = '' !== $cornice['immagine'] ? $cornice['immagine'] : 'object-fit:cover;';
        $filtri  = 'sottolineati' === (string) $s['filtri_aspetto'] ? 'sottolineati' : 'pillole';
        $tag_t   = in_array( (string) $s['titolo_tag'], [ 'h2', 'h3', 'h4', 'p' ], true ) ? (string) $s['titolo_tag'] : 'h3';

        // ── Dati per lo script ──
        $cfg     = [
            'passati' => $builder ? 'attenua' : $passati,
            'limite'  => max( 0, min( 60, absint( $s['limite'] ) ) ),
            'vista'   => $vista,
            'uno'     => __( '%d appuntamento', 'olobuilder' ),
            'tanti'   => __( '%d appuntamenti', 'olobuilder' ),
            'luogo'   => (string) $s['luogo_evento'],
            'unita'   => [ __( 'giorni', 'olobuilder' ), __( 'ore', 'olobuilder' ), __( 'minuti', 'olobuilder' ), __( 'secondi', 'olobuilder' ) ],
            'ora'     => __( 'In scena adesso', 'olobuilder' ),
            'builder' => $builder,
        ];

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS da colori passati da safe_color_css() e dagli helper di Olobuild_Tile_Utils, numeri forzati, costanti interne e selettori generati qui
        echo '<style>';
        echo "{$sel}{--ocrt-acc:{$accento};--ocrt-su-acc:{$su_acc};--ocrt-linea:{$linea};--ocrt-r:{$raggio};--ocrt-rp:{$r_puls};--ocrt-gap:{$gap}px;--ocrt-col:{$col};--ocrt-ld:{$l_data}px;position:relative}";
        // filtri
        echo "{$sel} .ocrt-filtri{display:flex;flex-wrap:wrap;align-items:center;gap:.75rem 1.5rem;margin:0 0 1.75rem}";
        echo "{$sel} .ocrt-chips{display:flex;flex-wrap:wrap;gap:.5rem}";
        if ( 'pillole' === $filtri ) {
            echo "{$sel} .ocrt-chip{font:inherit;font-size:.82rem;font-weight:600;letter-spacing:.06em;cursor:pointer;color:inherit;background:transparent;border:1px solid color-mix(in srgb, currentColor 26%, transparent);border-radius:5em;padding:.5em 1.15em;transition:background-color .25s,border-color .25s,color .25s}";
            echo "{$sel} .ocrt-chip:hover{border-color:var(--g,var(--ocrt-acc))}";
            echo "{$sel} .ocrt-chip.is-on{background:var(--g,var(--ocrt-acc));border-color:var(--g,var(--ocrt-acc));color:var(--gs,var(--ocrt-su-acc))}";
        } else {
            echo "{$sel} .ocrt-chip{font:inherit;font-size:.82rem;font-weight:600;letter-spacing:.12em;text-transform:uppercase;cursor:pointer;color:inherit;background:transparent;border:0;border-bottom:2px solid transparent;padding:.4em 0;opacity:.7;transition:opacity .25s,border-color .25s}";
            echo "{$sel} .ocrt-chips{gap:.5rem 1.4rem}";
            echo "{$sel} .ocrt-chip:hover{opacity:1}";
            echo "{$sel} .ocrt-chip.is-on{opacity:1;border-bottom-color:var(--g,var(--ocrt-acc))}";
        }
        echo "{$sel} .ocrt-chip:focus-visible,{$sel} .ocrt-cerca:focus-visible,{$sel} .ocrt-btn:focus-visible,{$sel} .ocrt-ics:focus-visible,{$sel} .ocrt-info:focus-visible{outline:2px solid var(--ocrt-acc);outline-offset:3px}";
        echo "{$sel} .ocrt-cerca{font:inherit;font-size:.92rem;color:inherit;background:transparent;border:1px solid color-mix(in srgb, currentColor 26%, transparent);border-radius:5em;padding:.55em 1.1em;min-width:min(16rem,100%)}";
        echo "{$sel} .ocrt-conteggio{margin:0 0 .5rem;font-size:.85rem;opacity:.7}";
        echo "{$sel} .ocrt-vuoto,{$sel} .ocrt-nessuno{margin:1rem 0;font-size:1.05rem;opacity:.8}";
        echo "{$sel} .ocrt-mese{margin:2.4rem 0 .4rem;padding-bottom:.6rem;border-bottom:1px solid var(--ocrt-linea);font-size:.8rem;font-weight:600;letter-spacing:.24em;text-transform:uppercase;color:var(--ocrt-acc)}";
        echo "{$sel} .ocrt-mese:first-child{margin-top:0}";
        // pezzi comuni
        echo "{$sel} .ocrt-sopra{display:flex;flex-wrap:wrap;align-items:center;gap:.4rem .8rem;margin:0 0 .5rem}";
        echo "{$sel} .ocrt-genere{color:var(--g,var(--ocrt-acc));display:inline-flex;align-items:center;gap:.45em}";
        echo "{$sel} .ocrt-genere::before{content:'';width:.5em;height:.5em;border-radius:50%;background:currentColor}";
        echo "{$sel} .ocrt-stato{font-size:.72rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;padding:.3em .75em;border-radius:5em;background:color-mix(in srgb, var(--ocrt-acc) 14%, transparent);color:var(--ocrt-acc)}";
        echo "{$sel} .ocrt-stato--esaurito,{$sel} .ocrt-stato--annullato{background:color-mix(in srgb, currentColor 10%, transparent);color:inherit;opacity:.75}";
        echo "{$sel} .ocrt-stato--gratuito{background:color-mix(in srgb, var(--olo-color-secondary) 14%, transparent);color:var(--olo-color-secondary)}";
        echo "{$sel} .ocrt-titolo{margin:0}";
        echo "{$sel} .ocrt-sotto{margin:.35rem 0 0;opacity:.85}";
        echo "{$sel} .ocrt-meta{margin:.6rem 0 0;opacity:.75}";
        echo "{$sel} .ocrt-desc{margin:.7rem 0 0;max-width:62ch}";
        echo "{$sel} .ocrt-etichette{list-style:none;margin:.8rem 0 0;padding:0;display:flex;flex-wrap:wrap;gap:.4rem}";
        echo "{$sel} .ocrt-etichette li{font-size:.75rem;padding:.25em .7em;border:1px solid color-mix(in srgb, currentColor 22%, transparent);border-radius:5em}";
        echo "{$sel} .ocrt-incontro{display:flex;align-items:flex-start;gap:.5rem;margin:.8rem 0 0;font-size:.9rem;color:var(--ocrt-acc)}";
        echo "{$sel} .ocrt-incontro svg{flex:none;width:1.1em;height:1.1em;margin-top:.15em}";
        echo "{$sel} .ocrt-azioni{display:flex;flex-wrap:wrap;align-items:center;gap:.6rem 1rem;margin-top:1.1rem}";
        echo "{$sel} .ocrt-btn{display:inline-flex;align-items:center;justify-content:center;text-decoration:none!important;background:var(--ocrt-acc);color:var(--ocrt-su-acc)!important;border-radius:var(--ocrt-rp);padding:.85em 1.6em;line-height:1;transition:transform .25s,filter .25s}";
        echo "{$sel} .ocrt-btn:hover{transform:translateY(-1px);filter:brightness(1.08)}";
        echo "{$sel} .ocrt-btn[aria-disabled=true]{background:color-mix(in srgb, currentColor 12%, transparent);color:inherit!important;cursor:default;transform:none;filter:none;opacity:.7}";
        echo "{$sel} .ocrt-ics{display:inline-flex;align-items:center;justify-content:center;width:2.6em;height:2.6em;padding:0;cursor:pointer;color:inherit;background:transparent;border:1px solid color-mix(in srgb, currentColor 26%, transparent);border-radius:var(--ocrt-rp);transition:border-color .25s,color .25s}";
        echo "{$sel} .ocrt-ics:hover{border-color:var(--ocrt-acc);color:var(--ocrt-acc)}";
        echo "{$sel} .ocrt-ics svg{width:1.15em;height:1.15em}";
        echo "{$sel} .ocrt-info{color:inherit;text-decoration:none;border-bottom:1px solid color-mix(in srgb, currentColor 35%, transparent);padding-bottom:.15em;transition:border-color .25s,color .25s}";
        echo "{$sel} .ocrt-info:hover{color:var(--ocrt-acc);border-color:var(--ocrt-acc)}";
        echo "{$sel} .ocrt-img{margin:0;overflow:hidden;border-radius:var(--ocrt-r);{$img_ar}background:color-mix(in srgb, currentColor 8%, transparent)}";
        echo "{$sel} .ocrt-img img{display:block;width:100%;height:100%;{$img_fit}transition:transform .8s cubic-bezier(.2,.7,.2,1)}";
        echo "{$sel} .ocrt-voce:hover .ocrt-img img{transform:scale(1.04)}";
        echo "{$sel} .ocrt-passato{opacity:.45}";
        echo "{$sel} .ocrt-passato .ocrt-btn{display:none}";
        echo "{$sel} [hidden]{display:none!important}";

        if ( 'lista' === $vista ) {
            $colonne = 'var(--ocrt-ld) minmax(0,1fr)' . ( $immag ? ' minmax(11rem,17rem)' : '' );
            echo "{$sel} .ocrt-voce{display:grid;grid-template-columns:{$colonne};gap:1.2rem 2.2rem;align-items:start;padding:{$pad};border-bottom:1px solid var(--ocrt-linea);" . ( $sfondo ? "background:{$sfondo};" : '' ) . ( $bordo ? "border:{$bordo};border-radius:var(--ocrt-r);margin-bottom:var(--ocrt-gap);" : '' ) . ( 'none' !== $ombra ? "box-shadow:{$ombra};" : '' ) . 'transition:background-color .3s}';
            if ( $sfondo || $bordo || 'none' !== $ombra ) {
                // righe a riquadro: arrotondate e staccate, senza la linea sotto
                echo "{$sel} .ocrt-voce{border-radius:var(--ocrt-r);margin-bottom:var(--ocrt-gap)" . ( $bordo ? '' : ';border-bottom:0' ) . '}';
            }
            if ( $immag && 'sinistra' === $lato ) {
                echo "{$sel} .ocrt-voce{grid-template-columns:var(--ocrt-ld) minmax(11rem,17rem) minmax(0,1fr)}{$sel} .ocrt-lato{grid-column:2;grid-row:1}{$sel} .ocrt-corpo{grid-column:3;grid-row:1}";
            }
            echo "{$sel} .ocrt-data{display:flex;flex-direction:column;align-items:flex-start;gap:.15rem;padding-top:.2rem}";
            echo "{$sel} .ocrt-gg{line-height:.9}";
            echo "{$sel} .ocrt-lato{display:flex;flex-direction:column;gap:1rem}";
            echo "{$sel} .ocrt-lato .ocrt-azioni{margin-top:0}";
            echo '@media (max-width:960px){' . "{$sel} .ocrt-voce{grid-template-columns:var(--ocrt-ld) minmax(0,1fr)}{$sel} .ocrt-lato{grid-column:2;grid-row:auto}{$sel} .ocrt-corpo{grid-column:2;grid-row:1}{$sel} .ocrt-img{max-width:22rem}" . '}';
            echo '@media (max-width:640px){' . "{$sel} .ocrt-voce{grid-template-columns:1fr;gap:.9rem}{$sel} .ocrt-data{flex-direction:row;align-items:baseline;gap:.6rem;flex-wrap:wrap}{$sel} .ocrt-lato,{$sel} .ocrt-corpo{grid-column:1;grid-row:auto}{$sel} .ocrt-img{max-width:none}" . '}';
        } elseif ( 'schede' === $vista ) {
            echo "{$sel} .ocrt-elenco{display:grid;grid-template-columns:repeat(var(--ocrt-col),minmax(0,1fr));gap:var(--ocrt-gap)}";
            echo "{$sel} .ocrt-mese{grid-column:1/-1}";
            echo "{$sel} .ocrt-voce{display:flex;flex-direction:column;overflow:hidden;border-radius:var(--ocrt-r);" . ( $sfondo ? "background:{$sfondo};" : 'background:color-mix(in srgb, currentColor 4%, transparent);' ) . ( $bordo ? "border:{$bordo};" : '' ) . ( 'none' !== $ombra ? "box-shadow:{$ombra};" : '' ) . '}';
            echo "{$sel} .ocrt-voce .ocrt-img{border-radius:0;position:relative}";
            echo "{$sel} .ocrt-scheda-data{position:absolute;left:1rem;top:1rem;z-index:1;display:flex;flex-direction:column;align-items:center;min-width:4.2rem;padding:.55rem .6rem .5rem;border-radius:.6rem;background:var(--olo-color-light, #fff);color:var(--olo-color-dark, #111);box-shadow:0 .4rem 1.2rem rgba(0,0,0,.18)}";
            echo "{$sel} .ocrt-scheda-data .ocrt-gg{font-size:2rem;line-height:1}";
            echo "{$sel} .ocrt-corpo{display:flex;flex-direction:column;flex:1;padding:{$pad}}";
            echo "{$sel} .ocrt-corpo .ocrt-azioni{margin-top:auto;padding-top:1.1rem}";
            echo "{$sel} .ocrt-quando{margin:0 0 .45rem;font-size:.85rem;opacity:.75}";
            echo '@media (max-width:960px){' . "{$sel} .ocrt-elenco{grid-template-columns:repeat(min(2,var(--ocrt-col)),minmax(0,1fr))}" . '}';
            echo '@media (max-width:640px){' . "{$sel} .ocrt-elenco{grid-template-columns:1fr}" . '}';
            echo $this->css_per_dispositivo( $s, 'colonne', "{$sel} .ocrt-elenco", $this->decl_colonne( 4 ) );
        } else {
            echo "{$sel} .ocrt-pross{display:grid;grid-template-columns:" . ( $immag ? 'minmax(0,1.05fr) minmax(0,1fr)' : 'minmax(0,1fr)' ) . ";gap:clamp(1.5rem,4vw,4rem);align-items:center;" . ( $sfondo ? "background:{$sfondo};padding:clamp(1.25rem,3vw,2.5rem);border-radius:var(--ocrt-r);" : '' ) . ( $bordo ? "border:{$bordo};" : '' ) . ( 'none' !== $ombra ? "box-shadow:{$ombra};" : '' ) . '}';
            echo "{$sel} .ocrt-pross .ocrt-img{aspect-ratio:4/5}";
            echo "{$sel} .ocrt-occ{display:block;margin:0 0 1rem;font-size:.8rem;font-weight:600;letter-spacing:.28em;text-transform:uppercase;color:var(--ocrt-acc)}";
            echo "{$sel} .ocrt-quando{margin:0 0 .9rem;font-size:1.05rem;letter-spacing:.04em}";
            echo "{$sel} .ocrt-pross .ocrt-titolo{font-size:clamp(2rem,4.4vw,3.6rem);line-height:1.04}";
            echo "{$sel} .ocrt-conto{margin:1.6rem 0 0}";
            echo "{$sel} .ocrt-conto-et{display:block;margin:0 0 .6rem;font-size:.8rem;letter-spacing:.2em;text-transform:uppercase;opacity:.75}";
            echo "{$sel} .ocrt-cifre{display:flex;gap:clamp(.6rem,2vw,1.4rem)}";
            echo "{$sel} .ocrt-cifre span{display:flex;flex-direction:column;align-items:center;min-width:3.4rem}";
            echo "{$sel} .ocrt-cifre b{font-weight:400;line-height:1;font-variant-numeric:tabular-nums}";
            echo "{$sel} .ocrt-cifre i{font-style:normal;font-size:.72rem;letter-spacing:.14em;text-transform:uppercase;opacity:.7;margin-top:.35rem}";
            echo "{$sel} .ocrt-adesso{font-size:1.2rem;color:var(--ocrt-acc)}";
            echo '@media (max-width:960px){' . "{$sel} .ocrt-pross{grid-template-columns:1fr}{$sel} .ocrt-pross .ocrt-img{aspect-ratio:16/10}" . '}';
        }
        // tipografia
        echo $this->css_testo( $s, 'giorno', "{$sel} .ocrt-gg" );
        echo $this->css_testo( $s, 'mese', "{$sel} .ocrt-mm,{$sel} .ocrt-sett,{$sel} .ocrt-ora" );
        echo $this->css_testo( $s, 'genere', "{$sel} .ocrt-genere" );
        echo $this->css_testo( $s, 'titolo', "{$sel} .ocrt-titolo" );
        echo $this->css_testo( $s, 'sottotitolo', "{$sel} .ocrt-sotto" );
        echo $this->css_testo( $s, 'meta', "{$sel} .ocrt-meta" );
        echo $this->css_testo( $s, 'descrizione', "{$sel} .ocrt-desc" );
        echo $this->css_testo( $s, 'pulsante', "{$sel} .ocrt-btn" );
        echo $this->css_testo( $s, 'conto', "{$sel} .ocrt-cifre b" );
        if ( 'schede' === $vista ) {
            echo "{$sel} .ocrt-scheda-data .ocrt-gg{font-size:2rem}{$sel} .ocrt-scheda-data .ocrt-mm{color:inherit}";
        }
        echo '</style>';
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped

        $ics_svg  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="3.5" y="5" width="17" height="15" rx="2"/><path d="M3.5 9.5h17M8 3v4M16 3v4M12 12.5v5M9.5 15h5"/></svg>';
        $voce_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M4 5h16v10H9l-5 4z"/><path d="M8 9h8M8 12h5"/></svg>';

        // una voce, nelle tre viste
        $voce = function ( $x ) use ( $s, $vista, $immag, $generi, $stati, $tag_t, $ics_svg, $voce_svg, $ora_ts ) {
            $v      = $x['v'];
            $ts     = $x['ts'];
            $g      = '' !== $x['gslg'] ? $generi[ $x['gslg'] ] : null;
            $stato  = in_array( (string) ( $v['stato'] ?? '' ), self::STATI, true ) ? (string) $v['stato'] : '';
            $chiuso = in_array( $stato, [ 'esaurito', 'annullato' ], true );
            $tit    = (string) $v['titolo'];
            $sotto  = trim( (string) ( $v['sottotitolo'] ?? '' ) );
            $ora    = trim( (string) ( $v['ora'] ?? '' ) );
            $fine_d = self::istante( $v['data_fine'] ?? '', '12:00' );
            $orari  = trim( (string) ( $v['orari'] ?? '' ) );
            $meta   = array_filter( array_map( 'trim', [ (string) ( $v['durata'] ?? '' ), (string) ( $v['luogo'] ?? '' ), (string) ( $v['prezzo'] ?? '' ) ] ), 'strlen' );
            $etich  = array_filter( array_map( 'trim', explode( ',', (string) ( $v['etichette'] ?? '' ) ) ), 'strlen' );
            $img    = trim( (string) ( $v['immagine'] ?? '' ) );
            $l_big  = trim( (string) ( $v['link_biglietti'] ?? '' ) );
            $l_inf  = trim( (string) ( $v['link_info'] ?? '' ) );
            $cerca  = strtolower( remove_accents( $tit . ' ' . $sotto . ' ' . ( $g ? $g['nome'] : '' ) . ' ' . (string) ( $v['luogo'] ?? '' ) ) );
            $quando = ( null !== $fine_d && $fine_d > $ts )
                /* translators: 1: first date, 2: last date */
                ? sprintf( __( 'dal %1$s al %2$s', 'olobuilder' ), self::data_a_parole( 'j F', $ts ), self::data_a_parole( 'j F', $fine_d ) )
                : self::data_a_parole( 'l j F', $ts, 'prossimo' === $vista );
            if ( '' !== $orari ) {
                $quando .= ' · ' . $orari;
            } elseif ( '' !== $ora ) {
                /* translators: %s: time */
                $quando .= ' · ' . sprintf( __( 'ore %s', 'olobuilder' ), $ora );
            }
            $passato = $x['fine'] < $ora_ts;
            $attr    = ' data-ts="' . (int) $ts . '" data-fine="' . (int) $x['fine'] . '" data-durata="' . (int) $x['durata'] . '"'
                . ' data-g="' . esc_attr( $x['gslg'] ) . '" data-mese="' . esc_attr( wp_date( 'Y-m', $ts ) ) . '" data-cerca="' . esc_attr( $cerca ) . '"'
                . ' data-titolo="' . esc_attr( $tit ) . '" data-luogo="' . esc_attr( (string) ( $v['luogo'] ?? '' ) ) . '"'
                . ( $g ? ' style="--g:' . esc_attr( $g['colore'] ) . '"' : '' );
            $cls     = 'ocrt-voce' . ( $passato ? ' ocrt-passato' : '' );

            $data_html = '<span class="ocrt-gg">' . esc_html( wp_date( 'j', $ts ) ) . '</span><span class="ocrt-mm">' . esc_html( self::data_a_parole( 'M', $ts ) ) . '</span>';
            $sopra = '';
            if ( $g || '' !== $stato ) {
                $sopra = '<div class="ocrt-sopra">' . ( $g ? '<span class="ocrt-genere">' . esc_html( $g['nome'] ) . '</span>' : '' )
                    . ( '' !== $stato ? '<span class="ocrt-stato ocrt-stato--' . esc_attr( $stato ) . '">' . esc_html( $stati[ $stato ] ) . '</span>' : '' ) . '</div>';
            }
            $testo = '<' . $tag_t . ' class="ocrt-titolo">' . esc_html( $tit ) . '</' . $tag_t . '>'
                . ( '' !== $sotto ? '<p class="ocrt-sotto">' . esc_html( $sotto ) . '</p>' : '' );
            $dett  = ( $meta ? '<p class="ocrt-meta">' . esc_html( implode( ' · ', $meta ) ) . '</p>' : '' )
                . ( '' !== trim( (string) ( $v['descrizione'] ?? '' ) ) ? '<p class="ocrt-desc">' . esc_html( (string) $v['descrizione'] ) . '</p>' : '' )
                . ( $etich ? '<ul class="ocrt-etichette">' . implode( '', array_map( static function ( $e ) { return '<li>' . esc_html( $e ) . '</li>'; }, $etich ) ) . '</ul>' : '' )
                . ( '' !== trim( (string) ( $v['incontro'] ?? '' ) ) ? '<p class="ocrt-incontro">' . $voce_svg . '<span>' . esc_html( (string) $v['incontro'] ) . '</span></p>' : '' );
            $azioni = '';
            if ( '' !== $l_big || $chiuso ) {
                $azioni .= $chiuso
                    ? '<span class="ocrt-btn" aria-disabled="true">' . esc_html( $stati[ $stato ] ) . '</span>'
                    : '<a class="ocrt-btn" href="' . esc_url( $l_big ) . '">' . esc_html( (string) $s['testo_biglietti'] ) . '</a>';
            }
            if ( ! empty( $s['calendario'] ) && 'annullato' !== $stato ) {
                $azioni .= '<button type="button" class="ocrt-ics" aria-label="' . esc_attr( (string) $s['testo_calendario'] . ': ' . $tit ) . '" title="' . esc_attr( (string) $s['testo_calendario'] ) . '">' . $ics_svg . '</button>';
            }
            if ( '' !== $l_inf ) {
                $azioni .= '<a class="ocrt-info" href="' . esc_url( $l_inf ) . '">' . esc_html( (string) $s['testo_info'] ) . '</a>';
            }
            $azioni   = '' !== $azioni ? '<div class="ocrt-azioni">' . $azioni . '</div>' : '';
            $img_html = ( $immag && '' !== $img ) ? '<img src="' . esc_url( $img ) . '" alt="" loading="lazy" decoding="async">' : '';

            if ( 'lista' === $vista ) {
                $h  = '<article class="' . $cls . '"' . $attr . '>';
                $h .= '<div class="ocrt-data">' . $data_html . '<span class="ocrt-sett">' . esc_html( self::data_a_parole( 'D', $ts ) ) . '</span>'
                    . ( '' !== $ora ? '<span class="ocrt-ora">' . esc_html( $ora ) . '</span>' : '' ) . '</div>';
                $h .= '<div class="ocrt-corpo">' . $sopra . $testo . $dett . ( $immag ? '' : $azioni ) . '</div>';
                if ( $immag ) {
                    $h .= '<div class="ocrt-lato">' . ( '' !== $img_html ? '<figure class="ocrt-img">' . $img_html . '</figure>' : '' ) . $azioni . '</div>';
                }
                return $h . '</article>';
            }
            if ( 'schede' === $vista ) {
                $h  = '<article class="' . $cls . '"' . $attr . '>';
                if ( $immag ) {
                    $h .= '<figure class="ocrt-img">' . $img_html . '<span class="ocrt-scheda-data">' . $data_html . '</span></figure>';
                }
                $h .= '<div class="ocrt-corpo">' . $sopra . '<p class="ocrt-quando">' . esc_html( $quando ) . '</p>' . $testo . $dett . $azioni . '</div>';
                return $h . '</article>';
            }
            // prossimo
            $h  = '<article class="' . $cls . ' ocrt-pross"' . $attr . '>';
            if ( $immag ) {
                $h .= '<figure class="ocrt-img">' . $img_html . '</figure>';
            }
            $h .= '<div class="ocrt-corpo"><span class="ocrt-occ">' . esc_html( (string) $s['occhiello_prossimo'] ) . '</span>'
                . '<p class="ocrt-quando">' . esc_html( $quando ) . '</p>' . $sopra . $testo . $dett;
            if ( ! empty( $s['conto'] ) ) {
                $h .= '<div class="ocrt-conto" data-conto><span class="ocrt-conto-et">' . esc_html( (string) $s['testo_conto'] ) . '</span><div class="ocrt-cifre" aria-hidden="true"></div></div>';
            }
            return $h . $azioni . '</div></article>';
        };

        // ── Markup ──
        $h = '<div id="' . esc_attr( $uid ) . '" class="olo-cartellone ocrt ocrt--' . esc_attr( $vista ) . '" data-ocrt="' . esc_attr( wp_json_encode( $cfg ) ) . '">';
        if ( 'prossimo' !== $vista ) {
            $chips = '';
            if ( ! empty( $s['filtri_generi'] ) && '' === $solo && count( $usati ) > 1 ) {
                $chips .= '<div class="ocrt-chips" role="group" aria-label="' . esc_attr__( 'Genere', 'olobuilder' ) . '"><button type="button" class="ocrt-chip is-on" data-g="" aria-pressed="true">' . esc_html( (string) $s['testo_tutti'] ) . '</button>';
                foreach ( $generi as $slug => $g ) {
                    if ( empty( $usati[ $slug ] ) ) {
                        continue;
                    }
                    $su_g   = Olobuild_Tile_Utils::testo_su_fondo( $g['colore'], 'var(--olo-color-light, #ffffff)' ) ?: 'var(--olo-color-light, #ffffff)';
                    $chips .= '<button type="button" class="ocrt-chip" data-g="' . esc_attr( $slug ) . '" aria-pressed="false" style="--g:' . esc_attr( $g['colore'] ) . ';--gs:' . esc_attr( $su_g ) . '">' . esc_html( $g['nome'] ) . '</button>';
                }
                $chips .= '</div>';
            }
            if ( ! empty( $s['filtri_mesi'] ) ) {
                $mesi = [];
                foreach ( $voci as $x ) {
                    $mesi[ wp_date( 'Y-m', $x['ts'] ) ] = wp_date( 'F', $x['ts'] );
                }
                if ( count( $mesi ) > 1 ) {
                    $chips .= '<div class="ocrt-chips ocrt-chips--mesi" role="group" aria-label="' . esc_attr__( 'Mese', 'olobuilder' ) . '"><button type="button" class="ocrt-chip is-on" data-m="" aria-pressed="true">' . esc_html( (string) $s['testo_tutti'] ) . '</button>';
                    foreach ( $mesi as $m => $nome ) {
                        $chips .= '<button type="button" class="ocrt-chip" data-m="' . esc_attr( $m ) . '" aria-pressed="false">' . esc_html( $nome ) . '</button>';
                    }
                    $chips .= '</div>';
                }
            }
            if ( ! empty( $s['ricerca'] ) ) {
                $chips .= '<input type="search" class="ocrt-cerca" placeholder="' . esc_attr__( 'Cerca uno spettacolo', 'olobuilder' ) . '" aria-label="' . esc_attr__( 'Cerca uno spettacolo', 'olobuilder' ) . '">';
            }
            if ( '' !== $chips ) {
                $h .= '<div class="ocrt-filtri">' . $chips . '</div><p class="ocrt-conteggio" aria-live="polite"></p>';
            }
        }
        $h    .= '<div class="ocrt-elenco">';
        $mese  = '';
        $primo = true;
        foreach ( $voci as $x ) {
            if ( 'prossimo' === $vista ) {
                // il prossimo lo sceglie il server (e lo riconferma il browser, nel fuso del sito); gli altri restano pronti
                $h    .= str_replace( '<article class="', '<article ' . ( $primo && $x['fine'] >= $ora_ts ? '' : 'hidden ' ) . 'class="', $voce( $x ) );
                $primo = $primo && $x['fine'] < $ora_ts;
                continue;
            }
            if ( ! empty( $s['raggruppa'] ) ) {
                $m = wp_date( 'Y-m', $x['ts'] );
                if ( $m !== $mese ) {
                    $mese = $m;
                    $h   .= '<p class="ocrt-mese" data-mese="' . esc_attr( $m ) . '">' . esc_html( wp_date( 'F Y', $x['ts'] ) ) . '</p>';
                }
            }
            $h .= $voce( $x );
        }
        $h .= '</div>';
        $h .= '<p class="ocrt-vuoto"' . ( $voci ? ' hidden' : '' ) . '>' . esc_html( (string) $s['vuoto_testo'] ) . '</p>';
        if ( 'prossimo' !== $vista ) {
            $h .= '<p class="ocrt-nessuno" hidden>' . esc_html( (string) $s['nessun_risultato'] ) . '</p>';
        }
        $h .= '</div>';
        $h .= $this->script( $uid );
        return $h;
    }

    /** Lo script: la funzione una volta per pagina, poi una chiamata per cartellone. Niente && (wptexturize). */
    private function script( $uid ) {
        static $funzione = false;
        $js = '';
        if ( ! $funzione ) {
            $funzione = true;
            $js      .= <<<'JS'
if(!window.oloCartellone){window.oloCartellone=function(el){
if(!el){return;}if(el.getAttribute("data-ocrt-vivo")){return;}el.setAttribute("data-ocrt-vivo","1");
var C={};try{C=JSON.parse(el.getAttribute("data-ocrt")||"{}");}catch(e){}
var voci=[].slice.call(el.querySelectorAll(".ocrt-voce"));
voci.forEach(function(v){v._i=(+v.getAttribute("data-ts")||0)*1000;var f=+v.getAttribute("data-fine");v._f=f?f*1000:v._i+10800000;});
var adesso=Date.now();
voci.forEach(function(v){v._p=v._f<adesso;if(v._p){v.classList.add("ocrt-passato");}else{v.classList.remove("ocrt-passato");}});
if(C.vista==="prossimo"){
var p=null;voci.forEach(function(v){if(!p){if(!v._p){p=v;}}});
voci.forEach(function(v){if(v===p){v.removeAttribute("hidden");}else{v.setAttribute("hidden","");}});
var vu=el.querySelector(".ocrt-vuoto");if(vu){if(p){vu.setAttribute("hidden","");}else{vu.removeAttribute("hidden");}}
var box=p?p.querySelector("[data-conto] .ocrt-cifre"):null;
if(box){var u=C.unita||["giorni","ore","minuti","secondi"];box.innerHTML=u.map(function(n){return "<span><b>00</b><i>"+n+"</i></span>";}).join("");var b=box.querySelectorAll("b");
var giro=function(){var d=p._i-Date.now();if(d<=0){var c=p.querySelector("[data-conto]");if(c){c.innerHTML="<span class=\"ocrt-adesso\">"+(C.ora||"")+"</span>";}return;}var x=[Math.floor(d/864e5),Math.floor(d/36e5)%24,Math.floor(d/6e4)%60,Math.floor(d/1e3)%60];for(var i=0;i<4;i++){b[i].textContent=(x[i]<10?"0":"")+x[i];}setTimeout(giro,1000-(Date.now()%1000));};giro();}
}else{
var g="",m="",q="";var chi=el.querySelectorAll(".ocrt-chip");var cnt=el.querySelector(".ocrt-conteggio");var nes=el.querySelector(".ocrt-nessuno");var vu2=el.querySelector(".ocrt-vuoto");
var applica=function(){var n=0;var lim=+C.limite||0;voci.forEach(function(v){var ok=true;if(v._p){if(C.passati==="nascondi"){ok=false;}}if(g){if(v.getAttribute("data-g")!==g){ok=false;}}if(m){if(v.getAttribute("data-mese")!==m){ok=false;}}if(q){if((v.getAttribute("data-cerca")||"").indexOf(q)<0){ok=false;}}if(ok){if(lim){if(n>=lim){ok=false;}}}if(ok){n++;v.removeAttribute("hidden");}else{v.setAttribute("hidden","");}});
[].forEach.call(el.querySelectorAll(".ocrt-mese"),function(h){var vis=false;var s=h.nextElementSibling;while(s){if(s.classList.contains("ocrt-mese")){break;}if(!s.hasAttribute("hidden")){vis=true;}s=s.nextElementSibling;}if(vis){h.removeAttribute("hidden");}else{h.setAttribute("hidden","");}});
if(cnt){cnt.textContent=(n===1?C.uno:C.tanti||"%d").replace("%d",n);}
var filtrato=g!==""||m!==""||q!=="";if(nes){if(n===0){if(filtrato){nes.removeAttribute("hidden");}else{nes.setAttribute("hidden","");}}else{nes.setAttribute("hidden","");}}
if(vu2){if(n===0){if(!filtrato){vu2.removeAttribute("hidden");}}else{vu2.setAttribute("hidden","");}}};
[].forEach.call(chi,function(c){c.addEventListener("click",function(){var mese=c.hasAttribute("data-m");var val=mese?c.getAttribute("data-m"):c.getAttribute("data-g");if(mese){m=val;}else{g=val;}[].forEach.call(c.parentNode.querySelectorAll(".ocrt-chip"),function(o){var on=o===c;o.classList.toggle("is-on",on);o.setAttribute("aria-pressed",on?"true":"false");});applica();});});
var cerca=el.querySelector(".ocrt-cerca");if(cerca){var t=0;cerca.addEventListener("input",function(){clearTimeout(t);t=setTimeout(function(){q=(cerca.value||"").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g,"").trim();applica();},150);});}
[].forEach.call(el.querySelectorAll(".ocrt-chip[data-m]"),function(c){var mm=c.getAttribute("data-m");if(!mm){return;}if(C.passati!=="nascondi"){return;}var vivo=voci.some(function(v){return !v._p ? v.getAttribute("data-mese")===mm : false;});if(!vivo){c.setAttribute("hidden","");}});
applica();}
var due=function(n){return (n<10?"0":"")+n;};
var fmt=function(ms){var d=new Date(ms);return d.getUTCFullYear()+due(d.getUTCMonth()+1)+due(d.getUTCDate())+"T"+due(d.getUTCHours())+due(d.getUTCMinutes())+"00Z";};
var esc=function(s){return String(s||"").replace(/\\/g,"\\\\").replace(/;/g,"\\;").replace(/,/g,"\\,").replace(/\n/g,"\\n");};
[].forEach.call(el.querySelectorAll(".ocrt-ics"),function(b){b.addEventListener("click",function(){var v=b.closest(".ocrt-voce");if(!v){return;}var ini=v._i;var dur=+v.getAttribute("data-durata")||120;var fine=ini+dur*60000;var tit=v.getAttribute("data-titolo")||"";var luogo=[v.getAttribute("data-luogo")||"",C.luogo||""].filter(function(x){return x;}).join(", ");
var ics=["BEGIN:VCALENDAR","VERSION:2.0","PRODID:-//Olobuild//Cartellone//IT","CALSCALE:GREGORIAN","BEGIN:VEVENT","UID:"+ini+"-"+Math.random().toString(36).slice(2)+"@olobuild","DTSTAMP:"+fmt(Date.now()),"DTSTART:"+fmt(ini),"DTEND:"+fmt(fine),"SUMMARY:"+esc(tit),"LOCATION:"+esc(luogo),"URL:"+esc(location.href),"END:VEVENT","END:VCALENDAR"].join("\r\n");
var a=document.createElement("a");a.href=URL.createObjectURL(new Blob([ics],{type:"text/calendar;charset=utf-8"}));a.download=(tit.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g,"").replace(/[^a-z0-9]+/g,"-").replace(/^-|-$/g,"")||"evento")+".ics";document.body.appendChild(a);a.click();setTimeout(function(){URL.revokeObjectURL(a.href);a.remove();},800);});});
};}
JS;
        }
        $js .= '(function(){var e=document.getElementById(' . wp_json_encode( $uid ) . ');if(window.oloCartellone){window.oloCartellone(e);}})();';
        return '<script>' . $js . '</script>';
    }
}
