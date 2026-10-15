<?php
/**
 * Tile Configuratore — gruppi di opzioni con il totale che si aggiorna, il riepilogo e la richiesta: un computer
 * da comporre, un abbonamento con i servizi in più, un pacchetto di consulenza.
 *
 * Ogni gruppo è un fieldset con la legenda (titolo e, se c'è l'aiuto, la (i) che apre un popup) e le opzioni sono
 * controlli VERI: radio per «una scelta», checkbox per «più scelte», − / + con un campo numerico per «quantità»
 * (fra il minimo e il massimo dell'opzione), disegnati come schede (immagine, nome, descrizione, prezzo, badge) o
 * come righe compatte. Le opzioni stanno in un elenco solo e indicano il loro gruppo con l'id (o col titolo).
 * Le predefinite partono scelte; in un gruppo «una scelta» obbligatorio senza predefinita parte la prima opzione,
 * in uno facoltativo un secondo clic sulla scelta la toglie.
 * Totale = prezzo base + le differenze delle opzioni scelte (× quantità), nella lingua della pagina con la valuta e il
 * periodo («69 €/mese»): il numero corre al nuovo valore (subito con «riduci movimento») e un'area aria-live lo
 * annuncia. Gli indicatori sommano i punti delle opzioni scelte («giochi:40,lavoro:25»), fino al loro massimo.
 * Riepilogo: le scelte gruppo per gruppo e il totale; il pulsante segnala i gruppi obbligatori vuoti (messaggio e
 * fuoco sul gruppo), poi apre il collegamento ({configurazione} = il riepilogo in testo, {totale}) e/o scrive il
 * riepilogo nel campo del Modulo della pagina che ha quel nome (evento input), poi ci porta. «Ricomincia» torna
 * alle predefinite. Disposizione «lato»: il riepilogo accanto, fermo mentre si scorre; sotto i 960 px (o in una
 * colonna stretta) va sotto, e la sua testata col totale resta in fondo allo schermo finché la tile è in vista
 * (position:sticky: il contenitore del template ha un transform, e un position:fixed seguirebbe lui).
 * Più istanze nella stessa pagina; nessuna libreria. Config: src/config/elements/configuratore.js.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Configuratore_Tile extends Olobuild_Tile_Base {

    protected $type     = 'configuratore';
    protected $name     = 'Configuratore';
    protected $icon     = 'dashicons-calculator';
    protected $category = 'interactive';

    protected $defaults = [
        // contenuto
        'gruppi'                       => [
            [ 'id' => 'cpu', 'titolo' => 'Processore', 'tipo' => 'uno', 'obbligatorio' => true, 'aiuto' => 'Più core aiutano nel montaggio video e nel rendering; una frequenza alta fa girare meglio i giochi.' ],
            [ 'id' => 'ram', 'titolo' => 'Memoria', 'tipo' => 'uno', 'obbligatorio' => true, 'aiuto' => '' ],
            [ 'id' => 'servizi', 'titolo' => 'Servizi', 'tipo' => 'piu', 'obbligatorio' => false, 'aiuto' => '' ],
            [ 'id' => 'monitor', 'titolo' => 'Monitor', 'tipo' => 'quantita', 'obbligatorio' => false, 'aiuto' => 'Fino a tre monitor sulla stessa scheda video.' ],
        ],
        'opzioni'                      => [
            [ 'id' => 'cpu-6', 'gruppo' => 'cpu', 'nome' => '6 core · 4,6 GHz', 'descrizione' => 'Ufficio, studio e giochi in Full HD.', 'prezzo' => 0, 'immagine' => '', 'badge' => '', 'predefinita' => true, 'punti' => 'giochi:35,lavoro:30', 'min' => 0, 'max' => 10 ],
            [ 'id' => 'cpu-8', 'gruppo' => 'cpu', 'nome' => '8 core · 5,0 GHz', 'descrizione' => 'Giochi in QHD e montaggi leggeri.', 'prezzo' => 120, 'immagine' => '', 'badge' => 'Consigliato', 'predefinita' => false, 'punti' => 'giochi:50,lavoro:45', 'min' => 0, 'max' => 10 ],
            [ 'id' => 'cpu-16', 'gruppo' => 'cpu', 'nome' => '16 core · 5,4 GHz', 'descrizione' => 'Rendering 3D e video in 4K.', 'prezzo' => 380, 'immagine' => '', 'badge' => '', 'predefinita' => false, 'punti' => 'giochi:55,lavoro:65', 'min' => 0, 'max' => 10 ],
            [ 'id' => 'ram-16', 'gruppo' => 'ram', 'nome' => '16 GB', 'descrizione' => 'Dieci schede aperte senza rallentare.', 'prezzo' => 0, 'immagine' => '', 'badge' => '', 'predefinita' => true, 'punti' => 'giochi:15,lavoro:10', 'min' => 0, 'max' => 10 ],
            [ 'id' => 'ram-32', 'gruppo' => 'ram', 'nome' => '32 GB', 'descrizione' => 'Giochi recenti e fotoritocco.', 'prezzo' => 90, 'immagine' => '', 'badge' => '', 'predefinita' => false, 'punti' => 'giochi:25,lavoro:20', 'min' => 0, 'max' => 10 ],
            [ 'id' => 'ram-64', 'gruppo' => 'ram', 'nome' => '64 GB', 'descrizione' => 'Macchine virtuali e progetti pesanti.', 'prezzo' => 240, 'immagine' => '', 'badge' => '', 'predefinita' => false, 'punti' => 'giochi:25,lavoro:30', 'min' => 0, 'max' => 10 ],
            [ 'id' => 'srv-montaggio', 'gruppo' => 'servizi', 'nome' => 'Montaggio e collaudo', 'descrizione' => 'Lo assembliamo e lo proviamo per 24 ore.', 'prezzo' => 0, 'immagine' => '', 'badge' => '', 'predefinita' => true, 'punti' => '', 'min' => 0, 'max' => 10 ],
            [ 'id' => 'srv-sistema', 'gruppo' => 'servizi', 'nome' => 'Sistema installato', 'descrizione' => 'Sistema operativo, driver e aggiornamenti.', 'prezzo' => 49, 'immagine' => '', 'badge' => '', 'predefinita' => false, 'punti' => '', 'min' => 0, 'max' => 10 ],
            [ 'id' => 'srv-garanzia', 'gruppo' => 'servizi', 'nome' => 'Garanzia 3 anni', 'descrizione' => 'Ritiro e riconsegna a domicilio.', 'prezzo' => 79, 'immagine' => '', 'badge' => '', 'predefinita' => false, 'punti' => '', 'min' => 0, 'max' => 10 ],
            [ 'id' => 'mon-27', 'gruppo' => 'monitor', 'nome' => 'Monitor 27″ QHD', 'descrizione' => '165 Hz, pannello IPS.', 'prezzo' => 289, 'immagine' => '', 'badge' => '', 'predefinita' => false, 'punti' => 'giochi:10', 'min' => 0, 'max' => 3 ],
        ],
        'prezzo_base'                  => 899,
        'valuta'                       => '€',
        'testo_totale'                 => 'Totale',
        'nota_prezzo'                  => 'IVA inclusa',
        'periodo'                      => '',
        'mostra_differenze'            => true,
        'indicatori'                   => [
            [ 'id' => 'giochi', 'nome' => 'Giochi', 'massimo' => 100 ],
            [ 'id' => 'lavoro', 'nome' => 'Lavoro', 'massimo' => 100 ],
        ],
        'riepilogo'                    => true,
        'titolo_riepilogo'             => 'La tua configurazione',
        'testo_pulsante'               => 'Richiedi il preventivo',
        'link_azione'                  => '',
        'campo_modulo'                 => 'messaggio',
        'testo_azzera'                 => 'Ricomincia',
        // stile
        'vista_opzioni'                => 'schede',
        'disposizione'                 => 'lato',
        'sfondo_scheda'                => '',
        'bordo_scheda'                 => [],
        'colore_scelta'                => '',
        'sfondo_scelta'                => '',
        'colore_testo'                 => '',
        'colore_tenue'                 => '',
        'colore_prezzo'                => '',
        'sfondo_riepilogo'             => '',
        'sfondo_badge'                 => '',
        'colore_badge'                 => '',
        'colore_indicatori'            => '',
        'raggio_scheda'                => [ 'tl' => 12, 'tr' => 12, 'br' => 12, 'bl' => 12 ],
        'ombra'                        => 'none',
        'ombra_h'                      => '0',
        'ombra_v'                      => '4',
        'ombra_blur'                   => '10',
        'ombra_spread'                 => '0',
        'ombra_color'                  => 'rgba(0,0,0,0.15)',
        'ombra_inset'                  => false,
        'gruppo_font_family'           => 'heading',
        'gruppo_font_size'             => 20,
        'gruppo_font_size_tablet'      => '',
        'gruppo_font_size_mobile'      => '',
        'gruppo_font_weight'           => '600',
        'gruppo_color'                 => '',
        'opzione_font_family'          => '',
        'opzione_font_size'            => 16,
        'opzione_font_size_tablet'     => '',
        'opzione_font_size_mobile'     => '',
        'opzione_font_weight'          => '600',
        'opzione_color'                => '',
        'descrizione_font_family'      => '',
        'descrizione_font_size'        => 14,
        'descrizione_font_size_tablet' => '',
        'descrizione_font_size_mobile' => '',
        'descrizione_font_weight'      => '400',
        'descrizione_color'            => '',
        'prezzo_font_family'           => '',
        'prezzo_font_size'             => 14,
        'prezzo_font_size_tablet'      => '',
        'prezzo_font_size_mobile'      => '',
        'prezzo_font_weight'           => '600',
        'totale_font_family'           => 'heading',
        'totale_font_size'             => 34,
        'totale_font_size_tablet'      => '',
        'totale_font_size_mobile'      => '',
        'totale_font_weight'           => '700',
        'totale_color'                 => '',
        'proporzione_immagine'         => '3/2',
        'colonne'                      => 3,
        'gap'                          => 16,
    ];

    /** Uid già dati in questa richiesta: lo stesso nodo reso due volte (un widget ripetuto) resta distinto. */
    private static $uid_usati = [];

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

    /** Un id scritto a mano («CPU », «Velocità»): minuscolo, senza accenti, solo lettere, cifre, - e _. */
    private static function chiave( $v ) {
        $v = trim( (string) $v );
        if ( function_exists( 'remove_accents' ) ) {
            $v = remove_accents( $v );
        }
        $v = strtolower( $v );
        $v = (string) preg_replace( '/[^a-z0-9_-]+/', '-', $v );
        return substr( trim( $v, '-' ), 0, 60 );
    }

    /**
     * L'id di un gruppo o di un indicatore scritto da chi compone la tile; '' per quello che il builder dà da sé alle
     * voci nuove o duplicate («ci-1697…»): allora vale il titolo (o il nome).
     */
    private static function id_scritto( $v ) {
        $v = trim( (string) $v );
        return preg_match( '/^ci-\d{9,}/', $v ) ? '' : self::chiave( $v );
    }

    /** Il testo per confrontare un titolo: minuscolo, spazi ridotti. */
    private static function minuscolo( $v ) {
        $v = trim( (string) preg_replace( '/\s+/u', ' ', (string) $v ) );
        return function_exists( 'mb_strtolower' ) ? mb_strtolower( $v, 'UTF-8' ) : strtolower( $v );
    }

    /**
     * Un prezzo: numero, oppure testo scritto a mano: «1.299,90», «1,299.90», «-8,50», «€ 49». Con un separatore solo
     * seguito da tre cifre è quello delle migliaia (1.299 = 1299); 0 se non si legge.
     */
    private static function numero( $v ) {
        if ( is_int( $v ) || is_float( $v ) ) {
            return (float) $v;
        }
        $t = (string) preg_replace( '/[^0-9,.\-]/', '', str_replace( "\u{2212}", '-', (string) $v ) );
        if ( '' === $t || '-' === $t ) {
            return 0.0;
        }
        $vi = strrpos( $t, ',' );
        $pu = strrpos( $t, '.' );
        if ( false !== $vi && false !== $pu ) {
            // Tutti e due: l'ultimo è il separatore dei decimali.
            $t = $vi > $pu ? str_replace( [ '.', ',' ], [ '', '.' ], $t ) : str_replace( ',', '', $t );
        } elseif ( false !== $vi ) {
            $t = preg_match( '/^-?\d{1,3}(,\d{3})+$/', $t ) ? str_replace( ',', '', $t ) : str_replace( ',', '.', $t );
        } elseif ( false !== $pu && preg_match( '/^-?\d{1,3}(\.\d{3})+$/', $t ) ) {
            $t = str_replace( '.', '', $t );
        }
        return is_numeric( $t ) ? (float) $t : 0.0;
    }

    /** Un sì/no scritto nei dati di un tema: booleano, 1/0 o le parole «false», «no», «off» (= no). */
    private static function vero( $v ) {
        if ( is_bool( $v ) ) {
            return $v;
        }
        return ! in_array( strtolower( trim( (string) $v ) ), [ '', '0', 'false', 'no', 'off' ], true );
    }

    /** I punti di un'opzione per gli indicatori: «giochi:40, lavoro:25» (anche con = e ;) → [ id => valore ]. */
    private static function punti( $v ) {
        $out = [];
        if ( preg_match_all( '/([^:=,;]+?)\s*[:=]\s*(-?\d+(?:[.,]\d+)?)/u', (string) $v, $m, PREG_SET_ORDER ) ) {
            foreach ( $m as $x ) {
                $k = self::chiave( $x[1] );
                if ( '' !== $k ) {
                    $out[ $k ] = ( $out[ $k ] ?? 0 ) + (float) str_replace( ',', '.', $x[2] );
                }
            }
        }
        return $out;
    }

    /**
     * Il modello del collegamento: http(s), mailto:, tel:, relativo, «#…» o «//…»; '' per gli altri schemi
     * (javascript:, data:…) o con caratteri che non stanno in un indirizzo. Le graffe dei segnaposto restano
     * (esc_url le toglierebbe); gli spazi diventano %20 (un oggetto di mailto: scritto a mano).
     */
    private static function modello( $v ) {
        if ( is_array( $v ) ) {
            $v = $v['url'] ?? '';
        }
        $v = str_replace( ' ', '%20', trim( (string) $v ) );
        if ( '' === $v || preg_match( '/[\s<>"\'\\\\`]/', $v ) ) {
            return '';
        }
        if ( preg_match( '/^([a-z][a-z0-9+.\-]*):/i', $v, $m ) ) {
            if ( ! in_array( strtolower( $m[1] ), [ 'http', 'https', 'mailto', 'tel' ], true ) ) {
                return '';
            }
        }
        return substr( $v, 0, 2000 );
    }

    /** Il nome del campo di un Modulo: lettere, cifre, - e _ (come sanitize_key() del Form, che lo scrive minuscolo). */
    private static function nome_campo( $v ) {
        return substr( strtolower( (string) preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $v ) ), 0, 60 );
    }

    /** La proporzione «L/A» delle immagini (anche scritta «L:A»), o 3/2. */
    private static function proporzione( $v ) {
        $v = str_replace( [ ':', ' ' ], [ '/', '' ], trim( (string) $v ) );
        if ( preg_match( '/^(\d+(?:\.\d+)?)\/(\d+(?:\.\d+)?)$/', $v, $m ) ) {
            if ( (float) $m[1] > 0 ) {
                if ( (float) $m[2] > 0 ) {
                    return $v;
                }
            }
        }
        return '3/2';
    }

    /** La lingua della pagina, in due lettere, per il primo disegno dei prezzi (poi li riscrive il browser con Intl). */
    private static function lingua() {
        $l = function_exists( 'determine_locale' ) ? determine_locale() : ( function_exists( 'get_locale' ) ? get_locale() : 'it_IT' );
        return strtolower( substr( (string) $l, 0, 2 ) );
    }

    /**
     * Un prezzo come lo scrive Intl nel browser: «1.299 €», «€1,299», «1 299 €»; il segno meno vero (−) e, se
     * richiesto, il «+» delle differenze. Serve al primo disegno e a chi non ha JavaScript.
     */
    private static function soldi( $n, $dec, $valuta, $segno = false ) {
        $lg   = self::lingua();
        $mig  = '.';
        $deci = ',';
        if ( 'en' === $lg ) {
            $mig  = ',';
            $deci = '.';
        } elseif ( 'fr' === $lg ) {
            $mig = "\u{202F}";
        }
        $s = number_format( abs( (float) $n ), $dec, $deci, $mig );
        if ( '' !== $valuta ) {
            $s = 'en' === $lg ? $valuta . ( preg_match( '/^[A-Za-z]{2,}$/', $valuta ) ? "\u{00A0}" : '' ) . $s : $s . "\u{00A0}" . $valuta;
        }
        if ( $n < 0 ) {
            return "\u{2212}" . $s;
        }
        return $segno ? '+' . $s : $s;
    }

    /** Il colore se si legge sul fondo (4,5:1, o fondo non risolvibile), altrimenti ''. */
    private static function leggibile( $colore, $fondo ) {
        if ( '' === $colore ) {
            return '';
        }
        return '' === Olobuild_Tile_Utils::testo_su_fondo( $fondo, $colore ) ? $colore : '';
    }

    /**
     * CSS di un testo dal controllo tipografia: famiglia, misura (anche per tablet e telefono), peso e il colore già
     * risolto da chi chiama. Con $var la misura va in quella proprietà del selettore $radice invece che in font-size
     * (il totale la usa in due misure: nel riepilogo e, più piccola, nella barra).
     */
    private function css_testo( $s, $k, $selettore, $colore = '', $var = '', $radice = '' ) {
        $d   = '';
        $fam = $this->resolve_font_family( $s[ $k . '_font_family' ] ?? '' );
        $fs  = (float) ( $s[ $k . '_font_size' ] ?? 0 );
        $fw  = $this->font_weight_css( $s[ $k . '_font_weight' ] ?? '' );
        if ( '' !== $fam ) {
            $d .= 'font-family:' . $fam . ';';
        }
        if ( $fs > 0 && '' === $var ) {
            $d .= 'font-size:' . $fs . 'px;';
        }
        if ( $fw ) {
            $d .= 'font-weight:' . $fw . ';';
        }
        if ( '' !== $colore ) {
            $d .= 'color:' . $colore . ';';
        }
        $css = '' !== $d ? "{$selettore}{{$d}}" : '';
        if ( '' !== $var && $fs > 0 ) {
            $css .= "{$radice}{{$var}:{$fs}px}";
        }
        foreach ( [ 'tablet' => 960, 'mobile' => 480 ] as $disp => $px ) {
            $v = (float) ( $s[ $k . '_font_size_' . $disp ] ?? 0 );
            if ( $v > 0 ) {
                $css .= '@media (max-width:' . $px . 'px){' . ( '' !== $var ? "{$radice}{{$var}:{$v}px}" : "{$selettore}{font-size:{$v}px}" ) . '}';
            }
        }
        return $css;
    }

    public function render( $settings ) {
        $s       = wp_parse_args( $settings, $this->defaults );
        $uid     = 'ocfg-' . $this->chiave_stabile( $settings );
        if ( isset( self::$uid_usati[ $uid ] ) ) {
            self::$uid_usati[ $uid ]++;
            $uid .= '-' . self::$uid_usati[ $uid ];
        } else {
            self::$uid_usati[ $uid ] = 1;
        }
        $sel     = '#' . $uid;
        $builder = ! empty( $s['_builder_mode'] );

        // ── I gruppi: un titolo o un id; l'id scritto a mano diventa una chiave pulita (o il titolo, o «gN») ──
        $gruppi     = [];
        $per_id     = [];
        $per_titolo = [];
        foreach ( array_values( (array) $s['gruppi'] ) as $n => $g ) {
            if ( ! is_array( $g ) ) {
                continue;
            }
            $titolo = trim( (string) ( $g['titolo'] ?? '' ) );
            $id     = self::id_scritto( $g['id'] ?? '' );
            if ( '' === $id && '' === $titolo ) {
                continue;
            }
            if ( '' === $id ) {
                $id = self::chiave( $titolo );
            }
            if ( '' === $id || isset( $per_id[ $id ] ) ) {
                $id = ( '' === $id ? 'g' : $id . '-' ) . $n;
            }
            $tipo     = self::chiave( $g['tipo'] ?? '' ); // «più», «Quantità» → piu, quantita
            $tipo     = in_array( $tipo, [ 'uno', 'piu', 'quantita' ], true ) ? $tipo : 'uno';
            $gruppi[] = [
                'n'      => $n,
                'id'     => $id,
                'titolo' => $titolo,
                'tipo'   => $tipo,
                'obbl'   => ! array_key_exists( 'obbligatorio', $g ) || self::vero( $g['obbligatorio'] ),
                'aiuto'  => trim( (string) ( $g['aiuto'] ?? '' ) ),
                'opz'    => [],
            ];
            $per_id[ $id ] = count( $gruppi ) - 1;
            if ( '' !== $titolo ) {
                $per_titolo[ self::minuscolo( $titolo ) ] = count( $gruppi ) - 1;
            }
        }

        // ── Le opzioni: col nome, nel gruppo che indicano (per id, o per titolo; con un gruppo solo, anche senza) ──
        $senza  = 0;
        $prezzi = [ self::numero( $s['prezzo_base'] ) ];
        foreach ( array_values( (array) $s['opzioni'] ) as $n => $o ) {
            if ( ! is_array( $o ) ) {
                continue;
            }
            $nome = trim( (string) ( $o['nome'] ?? '' ) );
            if ( '' === $nome ) {
                continue;
            }
            $rif = trim( (string) ( $o['gruppo'] ?? '' ) );
            $gi  = null;
            if ( '' !== $rif ) {
                $k = self::chiave( $rif );
                if ( isset( $per_id[ $k ] ) ) {
                    $gi = $per_id[ $k ];
                } elseif ( isset( $per_titolo[ self::minuscolo( $rif ) ] ) ) {
                    $gi = $per_titolo[ self::minuscolo( $rif ) ];
                }
            } elseif ( 1 === count( $gruppi ) ) {
                $gi = 0;
            }
            if ( null === $gi ) {
                $senza++;
                continue;
            }
            $min = is_numeric( $o['min'] ?? '' ) ? max( 0, (int) $o['min'] ) : 0;
            $max = is_numeric( $o['max'] ?? '' ) ? (int) $o['max'] : 10;
            if ( $max < 1 ) {
                $max = max( 10, $min );
            }
            $max      = min( 999, max( $min, $max ) );
            $prezzo   = self::numero( $o['prezzo'] ?? 0 );
            $prezzi[] = $prezzo;

            $gruppi[ $gi ]['opz'][] = [
                'n'      => $n,
                'nome'   => $nome,
                'descr'  => trim( (string) ( $o['descrizione'] ?? '' ) ),
                'prezzo' => $prezzo,
                'img'    => esc_url_raw( self::url_di( $o['immagine'] ?? '' ) ),
                'badge'  => trim( (string) ( $o['badge'] ?? '' ) ),
                'pred'   => self::vero( $o['predefinita'] ?? false ),
                'punti'  => self::punti( $o['punti'] ?? '' ),
                'min'    => $min,
                'max'    => $max,
            ];
        }
        $vivi = array_values( array_filter( $gruppi, static function ( $g ) {
            return ! empty( $g['opz'] );
        } ) );
        if ( ! $vivi && ! $builder ) {
            return '';
        }

        // ── Le scelte iniziali: le predefinite (in «una scelta» una sola; obbligatorio senza predefinita = la prima) ──
        foreach ( $vivi as &$g ) {
            $preso = false;
            foreach ( $g['opz'] as &$o ) {
                if ( 'quantita' === $g['tipo'] ) {
                    $o['q'] = min( $o['max'], max( $o['min'], $o['pred'] ? max( $o['min'], 1 ) : $o['min'] ) );
                } elseif ( 'piu' === $g['tipo'] ) {
                    $o['q'] = $o['pred'] ? 1 : 0;
                } else {
                    $o['q'] = ( $o['pred'] && ! $preso ) ? 1 : 0;
                    $preso  = $preso || $o['pred'];
                }
            }
            unset( $o );
            if ( 'uno' === $g['tipo'] && ! $preso && $g['obbl'] ) {
                $g['opz'][0]['q'] = 1;
            }
        }
        unset( $g );

        // ── Prezzi: due decimali se un prezzo li ha, la valuta, il periodo («/mese» attaccato, «al mese» staccato) ──
        $dec = 0;
        foreach ( $prezzi as $p ) {
            if ( abs( $p - round( $p ) ) > 0.0001 ) {
                $dec = 2;
            }
        }
        $base    = round( self::numero( $s['prezzo_base'] ), $dec );
        $valuta  = trim( (string) $s['valuta'] );
        $periodo = trim( (string) $s['periodo'] );
        if ( '' !== $periodo && ! preg_match( '#^[/·\-]#u', $periodo ) ) {
            $periodo = ' ' . $periodo;
        }
        $diff   = ! empty( $s['mostra_differenze'] );
        $totale = $base;
        foreach ( $vivi as $g ) {
            foreach ( $g['opz'] as $o ) {
                $totale += $o['prezzo'] * $o['q'];
            }
        }
        $totale = round( $totale, $dec );

        // ── Indicatori: un nome o un id, il massimo (100 se manca) ──
        $indicatori = [];
        foreach ( (array) $s['indicatori'] as $d ) {
            if ( ! is_array( $d ) ) {
                continue;
            }
            $nome = trim( (string) ( $d['nome'] ?? '' ) );
            $id   = self::id_scritto( $d['id'] ?? '' );
            $id   = '' !== $id ? $id : self::chiave( $nome );
            if ( '' === $id ) {
                continue;
            }
            $mx = self::numero( $d['massimo'] ?? 0 );
            $v  = 0.0;
            foreach ( $vivi as $g ) {
                foreach ( $g['opz'] as $o ) {
                    if ( isset( $o['punti'][ $id ] ) ) {
                        $v += $o['punti'][ $id ] * $o['q'];
                    }
                }
            }
            $mx           = $mx > 0 ? $mx : 100.0;
            $indicatori[] = [ 'id' => $id, 'nome' => '' !== $nome ? $nome : $id, 'max' => $mx, 'v' => max( 0, min( $mx, $v ) ) ];
        }

        // ── Testi, richiesta, disposizione ──
        $con_lista  = ! empty( $s['riepilogo'] );
        $titolo_r   = trim( (string) $s['titolo_riepilogo'] );
        $testo_tot  = trim( (string) $s['testo_totale'] );
        $testo_tot  = '' !== $testo_tot ? $testo_tot : __( 'Totale', 'olobuilder' );
        $nota       = trim( (string) $s['nota_prezzo'] );
        $testo_p    = trim( (string) $s['testo_pulsante'] );
        $link       = self::modello( $s['link_azione'] );
        $campo      = self::nome_campo( $s['campo_modulo'] );
        $con_puls   = '' !== $testo_p && ( '' !== $link || '' !== $campo );
        $testo_az   = trim( (string) $s['testo_azzera'] );
        $righe      = 'righe' === (string) $s['vista_opzioni'];
        $a_lato     = 'sotto' !== (string) $s['disposizione'];
        $colonne    = max( 1, min( 4, (int) $s['colonne'] ) );
        $gap        = max( 0, min( 64, (int) $s['gap'] ) );
        $ratio      = self::proporzione( $s['proporzione_immagine'] );
        $titolo_rid = $uid . '-rt';

        // ── Colori: tutti dai token del tema, salvo quelli scelti ──
        $acc_s   = $this->safe_color_css( $s['colore_scelta'] );
        $acc     = '' !== $acc_s ? $acc_s : 'var(--olo-color-primary)';
        $su_acc  = '' !== $acc_s ? ( Olobuild_Tile_Utils::testo_su_fondo( $acc_s, 'var(--olo-color-light, #ffffff)' ) ?: 'var(--olo-color-light, #ffffff)' ) : 'var(--olo-color-primary-contrast, #ffffff)';
        $testo_c = $this->safe_color_css( $s['colore_testo'] );
        $tenue   = $this->safe_color_css( $s['colore_tenue'] ) ?: 'color-mix(in srgb, currentColor 64%, transparent)';
        $sf_sch  = $this->safe_color_css( $s['sfondo_scheda'] );
        // Le schede: trasparenti, seguono la sezione; con uno sfondo scelto il testo diventa esplicito e leggibile.
        $testo_sch = $testo_c;
        if ( '' !== $sf_sch ) {
            $base_t    = '' !== $testo_c ? $testo_c : 'var(--olo-color-text, #374151)';
            $testo_sch = Olobuild_Tile_Utils::testo_su_fondo( $sf_sch, $base_t ) ?: $base_t;
        }
        $sf_sce_s = $this->safe_color_css( $s['sfondo_scelta'] );
        $sf_sce   = '' !== $sf_sce_s ? $sf_sce_s : ( '' !== $sf_sch ? "color-mix(in srgb, var(--ocfg-acc) 8%, {$sf_sch})" : 'color-mix(in srgb, var(--ocfg-acc) 7%, transparent)' );
        $testo_sce = '';
        if ( '' !== $sf_sce_s ) {
            $base_t    = '' !== $testo_sch ? $testo_sch : 'var(--olo-color-text, #374151)';
            $testo_sce = Olobuild_Tile_Utils::testo_su_fondo( $sf_sce_s, $base_t );
        }
        // Il riepilogo è un pannello pieno (la sua testata scorre sopra le schede): il fondo del tema velato dal
        // testo, col testo del tema; con uno sfondo o un testo scelti, il testo che lì si legge.
        $pf_s = $this->safe_color_css( $s['sfondo_riepilogo'] );
        $pf   = '' !== $pf_s ? $pf_s : 'color-mix(in srgb, var(--olo-color-text, #374151) 4%, var(--olo-color-background, #ffffff))';
        $pt0  = '' !== $testo_c ? $testo_c : 'var(--olo-color-text, #374151)';
        $pt   = Olobuild_Tile_Utils::testo_su_fondo( $pf, $pt0 ) ?: $pt0;
        $bdg_s = $this->safe_color_css( $s['sfondo_badge'] );
        $bdg   = '' !== $bdg_s ? $bdg_s : 'var(--ocfg-acc)';
        $bdg_t = $this->safe_color_css( $s['colore_badge'] );
        if ( '' === $bdg_t ) {
            $bdg_t = '' !== $bdg_s ? ( Olobuild_Tile_Utils::testo_su_fondo( $bdg_s, 'var(--olo-color-light, #ffffff)' ) ?: 'var(--olo-color-light, #ffffff)' ) : 'var(--ocfg-su-acc)';
        }
        $ind_c = $this->safe_color_css( $s['colore_indicatori'] ) ?: 'var(--ocfg-acc)';
        $err   = 'var(--olo-color-danger, #b42318)';
        // Prezzo e totale: il colore scelto (colore_prezzo, o prezzo_color della tipografia) sulle schede; nel
        // riepilogo solo se lì si legge.
        $c_prezzo = $this->safe_color_css( $s['colore_prezzo'] ) ?: $this->safe_color_css( $s['prezzo_color'] ?? '' );
        $c_tot    = self::leggibile( $this->safe_color_css( $s['totale_color'] ), $pf );

        // Bordo delle schede: quello scelto (senza colore, il filo di sempre; a zero = nessun bordo).
        $filo  = 'color-mix(in srgb, currentColor 14%, transparent)';
        $bordo = 'border:1px solid var(--ocfg-bc);';
        $bc    = $filo;
        $zero  = false;
        $b     = $s['bordo_scheda'] ?? null;
        if ( is_array( $b ) && Olobuild_Tile_Utils::border_is_set( $b ) ) {
            $c_b = Olobuild_Tile_Utils::border_color( $b );
            if ( '' === $c_b ) {
                $b['color'] = $filo;
            } else {
                $bc = $c_b;
            }
            $bordo = Olobuild_Tile_Utils::border_css( $b );
            if ( '' === $bordo ) {
                $bordo = 'border:0;';
                $zero  = true;
            }
        } elseif ( is_string( $b ) && '' !== trim( $b ) ) {
            $c_b = Olobuild_Tile_Utils::border_color( $b );
            $bc  = '' !== $c_b ? $c_b : $filo;
        }
        $anello = $zero ? '0 0 0 2px var(--ocfg-acc)' : '0 0 0 1px var(--ocfg-acc)';

        // Un raggio per tutta la tile: schede, riepilogo, popup dell'aiuto (angolo per angolo: la barra usa i due alti).
        $rv = $s['raggio_scheda'];
        if ( is_array( $rv ) ) {
            $r4 = [ absint( $rv['tl'] ?? 0 ), absint( $rv['tr'] ?? 0 ), absint( $rv['br'] ?? 0 ), absint( $rv['bl'] ?? 0 ) ];
        } else {
            $r4 = array_fill( 0, 4, absint( $rv ) );
        }
        $ombra = Olobuild_Tile_Utils::shadow_value( $s, 'ombra' );
        $om    = 'none' !== $ombra ? $ombra : 'none';
        $om2   = 'none' !== $ombra ? ',' . $ombra : '';

        // Senza elenco, indicatori e pulsanti il riepilogo è solo il totale: impilato basta la sua barra.
        $solo_tot = ! $con_lista && ! $indicatori && ! $con_puls && '' === $testo_az;
        $classi   = 'ocfg ocfg--' . ( $a_lato ? 'lato' : 'sotto' ) . ' ocfg--' . ( $righe ? 'righe' : 'schede' ) . ( $solo_tot ? ' ocfg--solo-totale' : '' );

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS da colori passati da safe_color_css() e dagli helper di Olobuild_Tile_Utils, numeri forzati, proporzione da espressione regolare, valori da elenchi chiusi e selettori generati qui
        echo '<style>';
        echo "{$sel}{--ocfg-acc:{$acc};--ocfg-su-acc:{$su_acc};--ocfg-acc-bg:{$sf_sce};--ocfg-tenue:{$tenue};--ocfg-bc:{$bc};--ocfg-sf:" . ( '' !== $sf_sch ? $sf_sch : 'transparent' ) . ";--ocfg-rtl:{$r4[0]}px;--ocfg-rtr:{$r4[1]}px;--ocfg-rbr:{$r4[2]}px;--ocfg-rbl:{$r4[3]}px;--ocfg-r:var(--ocfg-rtl) var(--ocfg-rtr) var(--ocfg-rbr) var(--ocfg-rbl);--ocfg-gap:{$gap}px;--ocfg-om:{$om};--ocfg-pf:{$pf};--ocfg-pt:{$pt};--ocfg-plinea:color-mix(in srgb, var(--ocfg-pt) 11%, transparent);--ocfg-ptenue:color-mix(in srgb, var(--ocfg-pt) 64%, transparent);--ocfg-bdg:{$bdg};--ocfg-bdg-t:{$bdg_t};--ocfg-ind:{$ind_c};--ocfg-err:{$err};--ocfg-tfs:" . (float) $this->defaults['totale_font_size'] . 'px' . ( '' !== $testo_c ? ";color:{$testo_c}" : '' ) . '}';
        echo "{$sel} [hidden]{display:none!important}";
        // Le icone di UIkit (uk-icon) seguono il colore del testo anche dove il CSS di UIkit non le raggiunge.
        echo "{$sel} [uk-icon] svg{fill:currentColor}{$sel} [uk-icon] [fill*=\"#\"]{fill:currentColor}{$sel} [uk-icon] [stroke*=\"#\"]{stroke:currentColor}";
        echo "{$sel} .ocfg-sr{position:absolute!important;width:1px;height:1px;margin:-1px;padding:0;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}";

        // Disposizione: impilata (opzioni, testata del riepilogo che resta in fondo allo schermo, riepilogo); «lato»
        // da 960 px in su, se la tile non è in una colonna stretta, con il riepilogo accanto e fermo.
        echo "{$sel} .ocfg-corpo{display:flex;flex-direction:column;gap:2rem}";
        echo "{$sel} .ocfg-opzioni{display:flex;flex-direction:column;gap:2.5rem;min-width:0}";
        if ( $a_lato ) {
            echo '@media (min-width:960px){'
                . "{$sel}:not(.is-stretta) .ocfg-corpo{display:grid;grid-template-columns:minmax(0,1fr) minmax(16.5rem,22rem);align-items:start;gap:2rem clamp(1.5rem,3vw,3rem)}"
                . "{$sel}:not(.is-stretta) .ocfg-pannello{position:sticky;top:calc(var(--olo-sticky-top-offset, 0px) + 1.5rem);max-height:calc(100vh - var(--olo-sticky-top-offset, 0px) - 3rem);overflow-y:auto;overscroll-behavior:contain;border-radius:var(--ocfg-r)}"
                . "{$sel}:not(.is-stretta) .ocfg-barra{display:none}"
                . "{$sel}:not(.is-stretta) .ocfg-totale{display:flex}"
                . "{$sel}.ocfg--solo-totale:not(.is-stretta) .ocfg-pannello{display:flex}"
                . '}';
        } else {
            echo '@media (min-width:960px){'
                . "{$sel}:not(.is-stretta) .ocfg-pannello--doppio{display:grid;grid-template-columns:minmax(0,1.35fr) minmax(16rem,1fr);align-items:start;gap:1.25rem 2.5rem}"
                . '}';
        }

        // I gruppi: legenda (titolo e aiuto), errore, griglia delle opzioni.
        echo "{$sel} .ocfg-gruppo{position:relative;min-width:0;margin:0;padding:0;border:0}";
        echo "{$sel} .ocfg-legenda{float:left;width:100%;margin:0 0 1rem;padding:0;color:inherit}";
        echo "{$sel} .ocfg-gruppo > :not(legend){clear:both}";
        echo "{$sel} .ocfg-legenda-riga{display:flex;align-items:center;gap:.4rem;min-width:0}";
        echo "{$sel} .ocfg-gruppo-titolo{min-width:0;line-height:1.25;text-wrap:balance}";
        echo "{$sel} .is-manca .ocfg-gruppo-titolo{color:var(--ocfg-err)}";
        echo "{$sel} .ocfg-info{position:relative;display:inline-flex;align-items:center;justify-content:center;flex:none;width:1.75rem;height:1.75rem;margin:0;padding:0;border:0;border-radius:50%;background:transparent;color:var(--ocfg-tenue);font:inherit;line-height:1;cursor:pointer;-webkit-appearance:none;appearance:none;transition:color .2s ease,background-color .2s ease}";
        echo "{$sel} .ocfg-info::before{content:'';position:absolute;inset:-.5rem}";
        echo "{$sel} .ocfg-info:hover,{$sel} .ocfg-info[aria-expanded=\"true\"]{color:var(--ocfg-acc);background:color-mix(in srgb, var(--ocfg-acc) 10%, transparent)}";
        echo "{$sel} .ocfg-info:focus{outline:none}";
        echo "{$sel} .ocfg-info:focus-visible{outline:2px solid var(--ocfg-acc);outline-offset:2px}";
        echo "{$sel} .ocfg-info svg,{$sel} .ocfg-info .olo-lucide-icon{width:1.1rem;height:1.1rem}";
        echo "{$sel} .ocfg-aiuto{position:absolute;left:0;top:2.25rem;z-index:6;box-sizing:border-box;width:min(22rem,100%);margin:0;padding:.875rem 1rem;border-radius:min(var(--ocfg-rtl), .75rem);background:var(--olo-color-dark, #14161c);color:var(--olo-color-light, #ffffff);font-size:14px;font-weight:400;line-height:1.5;text-align:left;box-shadow:0 1rem 2.25rem -1rem color-mix(in srgb, var(--olo-color-dark, #14161c) 70%, transparent)}";
        echo "{$sel} .ocfg-errore{margin:-.25rem 0 .875rem;color:var(--ocfg-err);font-size:14px;font-weight:600;line-height:1.45}";
        echo "{$sel} .ocfg-griglia{--ocfg-n:{$colonne};display:grid;gap:var(--ocfg-gap);grid-template-columns:repeat(auto-fill,minmax(max(9.5rem,calc((100% - (var(--ocfg-n) - 1) * var(--ocfg-gap)) / var(--ocfg-n))),1fr))}";
        // Colonne: sul tablet al massimo due, sul telefono una (poi i valori scelti per i dispositivi, che vincono).
        echo '@media (max-width:959px){' . "{$sel} .ocfg-griglia{--ocfg-n:" . min( 2, $colonne ) . '}}';
        echo '@media (max-width:639px){' . "{$sel} .ocfg-griglia{--ocfg-n:1}" . '}';
        echo $this->css_per_dispositivo( $s, 'colonne', "{$sel} .ocfg-griglia", static function ( $v ) {
            return '--ocfg-n:' . max( 1, min( 4, absint( $v ) ) );
        } );
        echo "{$sel} .ocfg-vuoto{margin:0;padding:1rem;font-size:14px;line-height:1.5;opacity:.75;border:1px dashed color-mix(in srgb, currentColor 30%, transparent);border-radius:min(var(--ocfg-rtl), .75rem)}";

        // La scheda di un'opzione: il controllo vero resta dentro, invisibile ma raggiungibile; la scelta = bordo e
        // anello nel colore della scelta, fondo velato e il segno di spunta.
        echo "{$sel} .ocfg-opz{position:relative;isolation:isolate;display:flex;flex-direction:column;min-width:0;margin:0;{$bordo}border-radius:var(--ocfg-r);background:var(--ocfg-sf);" . ( '' !== $testo_sch ? "color:{$testo_sch};" : '' ) . 'box-shadow:var(--ocfg-om);overflow:hidden;cursor:pointer;-webkit-tap-highlight-color:transparent;transition:border-color .2s ease,box-shadow .2s ease,background-color .2s ease,transform .2s ease}';
        echo "{$sel} .ocfg-opz:hover{border-color:color-mix(in srgb, var(--ocfg-acc) 55%, var(--ocfg-bc))}";
        echo "{$sel}.ocfg--schede .ocfg-opz:hover{transform:translateY(-1px)}";
        echo "{$sel} .ocfg-opz.is-scelta{border-color:var(--ocfg-acc);background:var(--ocfg-acc-bg);box-shadow:{$anello}{$om2}" . ( '' !== $testo_sce ? ";color:{$testo_sce}" : '' ) . '}';
        echo "{$sel} .ocfg-opz.is-fuoco{outline:2px solid var(--ocfg-acc);outline-offset:3px}";
        echo "{$sel} .ocfg-opz--qta{cursor:default}";
        echo "{$sel} .ocfg-opz--qta:hover{transform:none}";
        echo "{$sel} .ocfg-input{position:absolute;right:1rem;top:1rem;z-index:0;width:1px;height:1px;margin:0;padding:0;opacity:0;pointer-events:none}";
        echo "{$sel} .ocfg-img{position:relative;display:block;aspect-ratio:{$ratio};overflow:hidden;background:color-mix(in srgb, currentColor 6%, transparent)}";
        echo "{$sel} .ocfg-img img{display:block;width:100%;height:100%;max-width:none;object-fit:cover;transition:transform .5s ease}";
        echo "{$sel}.ocfg--schede .ocfg-opz:hover .ocfg-img img{transform:scale(1.03)}";
        echo "{$sel} .ocfg-img--vuota{display:flex;align-items:center;justify-content:center;color:color-mix(in srgb, currentColor 35%, transparent)}";
        echo "{$sel} .ocfg-img--vuota svg,{$sel} .ocfg-img--vuota .olo-lucide-icon{width:1.75rem;height:1.75rem}";
        echo "{$sel} .ocfg-testo{display:flex;flex-direction:column;gap:.375rem;flex:1;min-width:0;padding:1rem 1.125rem 1.125rem}";
        echo "{$sel} .ocfg-testa{display:flex;flex-direction:column;align-items:flex-start;gap:.5rem;min-width:0;padding-right:2rem}";
        echo "{$sel} .ocfg-nome{display:block;line-height:1.3;text-wrap:balance}";
        echo "{$sel} .ocfg-descr{display:block;color:var(--ocfg-tenue);line-height:1.5;text-wrap:pretty}";
        echo "{$sel} .ocfg-piede{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:.5rem .75rem;margin-top:auto;padding-top:.5rem}";
        echo "{$sel} .ocfg-prezzo{font-variant-numeric:tabular-nums;white-space:nowrap" . ( '' !== $c_prezzo ? ";color:{$c_prezzo}" : '' ) . '}';
        echo "{$sel} .ocfg-prezzo.is-incluso{color:var(--ocfg-tenue);font-weight:500}";
        echo "{$sel} .ocfg-badge{display:inline-block;max-width:100%;padding:.3em .7em;border-radius:5em;background:var(--ocfg-bdg);color:var(--ocfg-bdg-t);font-size:12px;font-weight:600;line-height:1.2;letter-spacing:.02em;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;vertical-align:middle}";
        echo "{$sel} .ocfg-img .ocfg-badge{position:absolute;left:.75rem;top:.75rem;z-index:1;max-width:calc(100% - 4rem)}";
        echo "{$sel} .ocfg-segno{position:absolute;right:.875rem;top:.875rem;z-index:2;box-sizing:border-box;display:flex;align-items:center;justify-content:center;width:1.375rem;height:1.375rem;border:1.5px solid color-mix(in srgb, currentColor 32%, transparent);border-radius:50%;background:transparent;transition:background-color .2s ease,border-color .2s ease,color .2s ease}";
        echo "{$sel} .ocfg-segno > *{opacity:0;transition:opacity .2s ease}";
        echo "{$sel} .ocfg-gruppo--piu .ocfg-segno{border-radius:.375rem}";
        echo "{$sel} .ocfg-opz--img .ocfg-segno{background:color-mix(in srgb, var(--olo-color-background, #ffffff) 88%, transparent);border-color:color-mix(in srgb, var(--olo-color-text, #374151) 32%, transparent)}";
        echo "{$sel} .is-scelta .ocfg-segno{background:var(--ocfg-acc);border-color:var(--ocfg-acc);color:var(--ocfg-su-acc)}";
        echo "{$sel} .is-scelta .ocfg-segno > *{opacity:1}";
        echo "{$sel} .ocfg-segno svg,{$sel} .ocfg-segno .olo-lucide-icon{width:.875rem;height:.875rem}";
        echo "{$sel} .ocfg-segno svg [stroke]{stroke-width:2.4}";

        // Quantità: − / + attorno al numero.
        echo "{$sel} .ocfg-qta{display:inline-flex;align-items:center;gap:.25rem;flex:none}";
        echo "{$sel} .ocfg-passo{position:relative;display:inline-flex;align-items:center;justify-content:center;flex:none;width:2.25rem;height:2.25rem;margin:0;padding:0;border:1px solid var(--ocfg-bc);border-radius:50%;background:transparent;color:inherit;font:inherit;line-height:1;cursor:pointer;-webkit-appearance:none;appearance:none;transition:border-color .2s ease,color .2s ease,background-color .2s ease}";
        echo "{$sel} .ocfg-passo::before{content:'';position:absolute;inset:-.3rem}";
        echo "{$sel} .ocfg-passo:hover{border-color:var(--ocfg-acc);color:var(--ocfg-acc);background:transparent}";
        echo "{$sel} .ocfg-passo:disabled{opacity:.35;cursor:not-allowed;border-color:var(--ocfg-bc);color:inherit}";
        echo "{$sel} .ocfg-passo:focus{outline:none}";
        echo "{$sel} .ocfg-passo:focus-visible{outline:2px solid var(--ocfg-acc);outline-offset:2px}";
        echo "{$sel} .ocfg-passo svg,{$sel} .ocfg-passo .olo-lucide-icon{width:1rem;height:1rem}";
        echo "{$sel} .ocfg-passo svg rect{stroke:currentColor;stroke-width:.8}";
        echo "{$sel} .ocfg-num{box-sizing:border-box;width:2.75rem;height:2.25rem;margin:0;padding:0;border:0;border-radius:.375rem;background:transparent;color:inherit;font:inherit;font-size:16px;font-weight:600;font-variant-numeric:tabular-nums;line-height:1;text-align:center;box-shadow:none;-moz-appearance:textfield;appearance:textfield}";
        echo "{$sel} .ocfg-num::-webkit-outer-spin-button,{$sel} .ocfg-num::-webkit-inner-spin-button{-webkit-appearance:none;margin:0}";
        echo "{$sel} .ocfg-num:focus{outline:none}";
        echo "{$sel} .ocfg-num:focus-visible{outline:2px solid var(--ocfg-acc);outline-offset:1px}";

        // Righe compatte: il segno a sinistra, la miniatura, nome e descrizione, prezzo (o quantità) a destra.
        if ( $righe ) {
            echo "{$sel} .ocfg-griglia{grid-template-columns:minmax(0,1fr);gap:calc(var(--ocfg-gap) * .5)}";
            echo "{$sel} .ocfg-opz{flex-direction:row;align-items:center;gap:.875rem;padding:.875rem 1rem}";
            echo "{$sel} .ocfg-segno{position:static;flex:none;order:-1}";
            echo "{$sel} .ocfg-input{left:1rem;right:auto;top:50%}";
            echo "{$sel} .ocfg-img{flex:none;width:3.5rem;height:3.5rem;aspect-ratio:auto;border-radius:min(var(--ocfg-rtl), .5rem)}";
            echo "{$sel} .ocfg-testo{display:grid;grid-template-columns:minmax(0,1fr) auto;align-items:center;column-gap:1rem;row-gap:.15rem;padding:0}";
            echo "{$sel} .ocfg-testa{flex-direction:row;flex-wrap:wrap;align-items:center;gap:.25rem .5rem;padding:0}";
            echo "{$sel} .ocfg-testa .ocfg-badge{order:2}";
            echo "{$sel} .ocfg-descr{grid-column:1}";
            echo "{$sel} .ocfg-piede{grid-column:2;grid-row:1 / span 2;flex-wrap:nowrap;margin:0;padding:0}";
            echo "{$sel} .ocfg-opz--breve .ocfg-piede{grid-row:1}";
            echo '@media (max-width:479px){' . "{$sel} .ocfg-opz--qta .ocfg-testo{grid-template-columns:minmax(0,1fr)}{$sel} .ocfg-opz--qta .ocfg-piede{grid-column:1;grid-row:auto;padding-top:.5rem}" . '}';
        }

        // Il riepilogo: pannello pieno; la sua testata (barra col totale) scorre in fondo allo schermo finché lui non
        // arriva, poi ci si appoggia sopra. Nel layout «lato» largo la barra non c'è e il totale sta nel pannello.
        echo "{$sel} .ocfg-barra{position:sticky;bottom:0;z-index:5;box-sizing:border-box;display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-bottom:-2rem;padding:.875rem 1.25rem;border:1px solid var(--ocfg-plinea);border-bottom:0;border-radius:var(--ocfg-rtl) var(--ocfg-rtr) 0 0;background:var(--ocfg-pf);color:var(--ocfg-pt);box-shadow:0 -.75rem 1.75rem -1.25rem color-mix(in srgb, var(--olo-color-dark, #14161c) 55%, transparent);transition:box-shadow .25s ease}";
        echo "{$sel}.is-giunta .ocfg-barra{box-shadow:none}";
        echo "{$sel} .ocfg-barra-totale{display:flex;flex-wrap:wrap;align-items:baseline;gap:.15rem .75rem;min-width:0}";
        echo "{$sel} .ocfg-barra .ocfg-totale-valore{font-size:calc(var(--ocfg-tfs) * .7)}";
        echo "{$sel} .ocfg-vai{display:inline-flex;align-items:center;gap:.35rem;flex:none;margin:0;padding:.5rem .875rem;border:0;border-radius:5em;background:color-mix(in srgb, var(--ocfg-pt) 8%, transparent);color:inherit;font:inherit;font-size:14px;font-weight:600;line-height:1.2;cursor:pointer;-webkit-appearance:none;appearance:none;transition:background-color .2s ease}";
        echo "{$sel} .ocfg-vai:hover{background:color-mix(in srgb, var(--ocfg-pt) 14%, transparent);color:inherit}";
        echo "{$sel} .ocfg-vai:focus{outline:none}";
        echo "{$sel} .ocfg-vai:focus-visible{outline:2px solid var(--ocfg-acc);outline-offset:2px}";
        echo "{$sel} .ocfg-vai svg,{$sel} .ocfg-vai .olo-lucide-icon{width:1rem;height:1rem}";
        echo "{$sel}.is-giunta .ocfg-vai{visibility:hidden}";
        echo "{$sel}.ocfg--solo-totale .ocfg-pannello,{$sel}.ocfg--solo-totale .ocfg-vai{display:none}";
        echo "{$sel}.ocfg--solo-totale .ocfg-barra{margin-bottom:0;border-bottom:1px solid var(--ocfg-plinea);border-radius:var(--ocfg-r)}";
        echo "{$sel} .ocfg-pannello{box-sizing:border-box;display:flex;flex-direction:column;gap:1.5rem;min-width:0;padding:1.5rem;border:1px solid var(--ocfg-plinea);border-radius:0 0 var(--ocfg-rbr) var(--ocfg-rbl);background:var(--ocfg-pf);color:var(--ocfg-pt);box-shadow:var(--ocfg-om)}";
        echo "{$sel} .ocfg-colonna{display:flex;flex-direction:column;gap:1.25rem;min-width:0}";
        echo "{$sel} .ocfg-pannello-titolo{margin:0;color:inherit;font-size:18px;font-weight:600;line-height:1.3}";
        echo "{$sel} .ocfg-lista{margin:0;padding:0}";
        echo "{$sel} .ocfg-riga{padding:.75rem 0;border-bottom:1px solid var(--ocfg-plinea)}";
        echo "{$sel} .ocfg-riga:first-child{padding-top:0}";
        echo "{$sel} .ocfg-riga dt{margin:0 0 .2rem;color:var(--ocfg-ptenue);font-size:12px;font-weight:600;letter-spacing:.06em;line-height:1.3;text-transform:uppercase}";
        echo "{$sel} .ocfg-riga dd{display:flex;align-items:baseline;justify-content:space-between;gap:.75rem;margin:0;line-height:1.45}";
        echo "{$sel} .ocfg-riga dd + dd{margin-top:.2rem}";
        echo "{$sel} .ocfg-riga-nome{min-width:0}";
        echo "{$sel} .ocfg-riga-prezzo{flex:none;font-variant-numeric:tabular-nums;white-space:nowrap;font-weight:600}";
        echo "{$sel} .ocfg-riga.is-manca dd{color:var(--ocfg-err);font-weight:600}";
        echo "{$sel} .ocfg-riga-prezzo.is-incluso{color:var(--ocfg-ptenue);font-weight:500}";
        echo "{$sel} .ocfg-indicatori{display:flex;flex-direction:column;gap:.875rem}";
        echo "{$sel} .ocfg-ind-testa{display:flex;align-items:baseline;justify-content:space-between;gap:.75rem;margin:0 0 .4rem;font-size:14px;font-weight:600;line-height:1.3}";
        echo "{$sel} .ocfg-ind-val{color:var(--ocfg-ptenue);font-variant-numeric:tabular-nums}";
        echo "{$sel} .ocfg-ind-barra{position:relative;display:block;height:.5rem;border-radius:5em;background:var(--ocfg-plinea);overflow:hidden}";
        echo "{$sel} .ocfg-ind-pieno{position:absolute;left:0;top:0;bottom:0;border-radius:inherit;background:var(--ocfg-ind);transition:width .45s cubic-bezier(.2,.7,.2,1)}";
        echo "{$sel} .ocfg-totale{display:none;flex-wrap:wrap;align-items:baseline;justify-content:space-between;gap:.25rem 1rem;padding-top:.25rem}";
        echo "{$sel} .ocfg-totale-etichetta{font-size:14px;font-weight:600;line-height:1.3}";
        echo "{$sel} .ocfg-totale-valore{font-size:var(--ocfg-tfs);font-variant-numeric:tabular-nums;line-height:1.05;white-space:nowrap" . ( '' !== $c_tot ? ";color:{$c_tot}" : '' ) . '}';
        echo "{$sel} .ocfg-periodo{font-size:.5em;font-weight:500;opacity:.75}";
        echo "{$sel} .ocfg-nota{flex-basis:100%;margin:0;color:var(--ocfg-ptenue);font-size:13px;line-height:1.4}";
        echo "{$sel} .ocfg-azioni{display:flex;flex-direction:column;align-items:stretch;gap:.5rem}";
        echo "{$sel} .ocfg-pulsante{box-sizing:border-box;display:inline-flex;align-items:center;justify-content:center;gap:.55em;width:100%;min-height:3rem;margin:0;padding:var(--olo-btn-padding, .75rem 1.5rem);border:0;border-radius:var(--olo-btn-border-radius, .5rem);background:var(--olo-color-primary);color:var(--olo-color-primary-contrast, #ffffff);font:inherit;font-size:var(--olo-btn-font-size, 15px);font-weight:var(--olo-btn-font-weight, 600);line-height:1.15;text-align:center;text-transform:var(--olo-btn-text-transform, none);letter-spacing:var(--olo-btn-letter-spacing, normal);cursor:pointer;-webkit-appearance:none;appearance:none;transition:filter .2s ease,transform .15s ease}";
        echo "{$sel} .ocfg-pulsante:hover{filter:brightness(var(--olo-btn-hover-brightness, 92%));background:var(--olo-color-primary);color:var(--olo-color-primary-contrast, #ffffff)}";
        echo "{$sel} .ocfg-pulsante:active{transform:translateY(1px)}";
        echo "{$sel} .ocfg-pulsante:focus{outline:none}";
        echo "{$sel} .ocfg-pulsante:focus-visible{outline:2px solid var(--olo-color-primary);outline-offset:3px}";
        echo "{$sel} .ocfg-pulsante svg,{$sel} .ocfg-pulsante .olo-lucide-icon{width:1.1em;height:1.1em;flex:none}";
        echo "{$sel} .ocfg-azzera{align-self:center;display:inline-flex;align-items:center;gap:.4rem;margin:0;padding:.5rem .625rem;border:0;border-radius:.375rem;background:transparent;color:var(--ocfg-ptenue);font:inherit;font-size:14px;font-weight:500;line-height:1.2;cursor:pointer;-webkit-appearance:none;appearance:none;transition:color .2s ease}";
        echo "{$sel} .ocfg-azzera:hover{color:var(--ocfg-pt);background:transparent}";
        echo "{$sel} .ocfg-azzera:focus{outline:none}";
        echo "{$sel} .ocfg-azzera:focus-visible{outline:2px solid var(--ocfg-acc);outline-offset:2px}";
        echo "{$sel} .ocfg-azzera svg,{$sel} .ocfg-azzera .olo-lucide-icon{width:1em;height:1em}";
        echo "{$sel} .ocfg-stato{margin:0;color:var(--ocfg-err);font-size:14px;font-weight:600;line-height:1.45}";
        echo "{$sel} .ocfg-stato:empty{display:none}";
        echo "{$sel} .ocfg-stato.is-info{color:inherit;font-weight:500;opacity:.85;overflow-wrap:anywhere}";
        echo '@media (max-width:639px){' . "{$sel} .ocfg-pannello{padding:1.25rem 1rem}{$sel} .ocfg-barra{padding:.75rem 1rem}" . ( $righe ? "{$sel} .ocfg-opz{padding:.75rem .875rem}" : "{$sel} .ocfg-testo{padding:.875rem 1rem 1rem}" ) . '}';
        // «Riduci movimento»: le transizioni si spengono, il resto resta.
        echo '@media (prefers-reduced-motion:reduce){' . "{$sel} .ocfg-opz,{$sel} .ocfg-img img,{$sel} .ocfg-segno,{$sel} .ocfg-segno > *,{$sel} .ocfg-info,{$sel} .ocfg-passo,{$sel} .ocfg-pulsante,{$sel} .ocfg-ind-pieno,{$sel} .ocfg-barra,{$sel} .ocfg-vai,{$sel} .ocfg-azzera{transition:none}{$sel} .ocfg-opz:hover,{$sel} .ocfg-opz:hover .ocfg-img img{transform:none}" . '}';
        echo $this->css_testo( $s, 'gruppo', "{$sel} .ocfg-gruppo-titolo", $this->safe_color_css( $s['gruppo_color'] ) );
        echo $this->css_testo( $s, 'opzione', "{$sel} .ocfg-nome", $this->safe_color_css( $s['opzione_color'] ) );
        echo $this->css_testo( $s, 'descrizione', "{$sel} .ocfg-descr", $this->safe_color_css( $s['descrizione_color'] ) );
        echo $this->css_testo( $s, 'prezzo', "{$sel} .ocfg-prezzo,{$sel} .ocfg-riga-prezzo" );
        echo $this->css_testo( $s, 'totale', "{$sel} .ocfg-totale-valore", '', '--ocfg-tfs', $sel );
        if ( '' !== $c_prezzo ) {
            $c_prezzo_p = self::leggibile( $c_prezzo, $pf );
            if ( '' !== $c_prezzo_p ) {
                echo "{$sel} .ocfg-riga-prezzo{color:{$c_prezzo_p}}";
            }
        }
        echo '</style>';

        // Nel builder: cosa manca (gruppi vuoti, opzioni che non trovano il gruppo), detto dove si vede.
        $note = function () use ( $gruppi, $senza ) {
            $h = '';
            foreach ( $gruppi as $g ) {
                if ( empty( $g['opz'] ) ) {
                    /* translators: %s: l'id del gruppo */
                    $h .= '<p class="ocfg-vuoto"><strong>' . esc_html( '' !== $g['titolo'] ? $g['titolo'] : $g['id'] ) . '</strong> · ' . esc_html( sprintf( __( 'Nessuna opzione in questo gruppo: nel Contenuto aggiungine una con Gruppo «%s».', 'olobuilder' ), $g['id'] ) ) . '</p>';
                }
            }
            if ( $senza > 0 ) {
                /* translators: %d: quante opzioni non trovano il loro gruppo */
                $h .= '<p class="ocfg-vuoto">' . esc_html( sprintf( __( 'Opzioni senza gruppo: %d. Nel campo Gruppo scrivi l\'ID di un gruppo, per esempio cpu.', 'olobuilder' ), $senza ) ) . '</p>';
            }
            return $h;
        };

        if ( ! $vivi ) {
            echo '<div id="' . esc_attr( $uid ) . '" class="ocfg"><div class="ocfg-opzioni">';
            echo '<p class="ocfg-vuoto">' . esc_html__( 'Aggiungi nel Contenuto i gruppi e le loro opzioni: ogni opzione indica nel campo Gruppo l\'ID del suo gruppo.', 'olobuilder' ) . '</p>';
            echo $note();
            echo '</div></div>';
            return ob_get_clean();
        }

        // ── I dati per lo script (gli indici dei gruppi e delle opzioni sono quelli disegnati) ──
        $dati_g = [];
        $dati_o = [];
        foreach ( $vivi as $gi => $g ) {
            $dati_g[] = [ 't' => $g['titolo'], 'tipo' => $g['tipo'], 'obbl' => $g['obbl'] ];
            foreach ( $g['opz'] as $o ) {
                $dati_o[] = [ 'g' => $gi, 'n' => $o['nome'], 'p' => $o['prezzo'], 'pt' => (object) $o['punti'], 'min' => $o['min'], 'max' => $o['max'], 'd' => $o['q'] ];
            }
        }
        $dati_i = [];
        foreach ( $indicatori as $d ) {
            $dati_i[] = [ 'id' => $d['id'], 'max' => $d['max'] ];
        }
        $cfg = [
            'base'    => $base,
            'dec'     => $dec,
            'valuta'  => $valuta,
            'periodo' => $periodo,
            'diff'    => $diff,
            'g'       => $dati_g,
            'o'       => $dati_o,
            'ind'     => $dati_i,
            'link'    => $con_puls ? $link : '',
            'campo'   => $campo,
            'builder' => $builder,
            'tt'      => [
                'titolo'      => $con_lista ? $titolo_r : '',
                'totale'      => $testo_tot,
                'nota'        => $nota,
                'base'        => __( 'Prezzo base', 'olobuilder' ),
                'incluso'     => __( 'Incluso', 'olobuilder' ),
                /* translators: %s: il prezzo di un pezzo, per esempio «+289 €» */
                'cad'         => __( '%s cad.', 'olobuilder' ),
                'da'          => __( 'Da scegliere', 'olobuilder' ),
                /* translators: %s: titolo del gruppo di opzioni */
                'manca1'      => __( 'Scegli un\'opzione in «%s».', 'olobuilder' ),
                /* translators: %s: titolo del gruppo di opzioni */
                'mancaN'      => __( 'Scegli almeno un\'opzione in «%s».', 'olobuilder' ),
                'scritto'     => __( 'La configurazione è nel modulo: completa i tuoi dati e invia.', 'olobuilder' ),
                'ricominciato' => __( 'Configurazione riportata all\'inizio.', 'olobuilder' ),
            ],
        ];
        if ( $builder ) {
            /* translators: %s: l'indirizzo che si aprirebbe sul sito */
            $cfg['tt']['anteprima'] = __( 'Nel builder il collegamento non si apre: sul sito porterebbe a %s', 'olobuilder' );
            /* translators: %s: il nome del campo del Form Contatti */
            $cfg['tt']['modulo'] = __( 'Nella pagina non c\'è un Form Contatti con il campo «%s»: aggiungilo, oppure scrivi un collegamento nel Contenuto.', 'olobuilder' );
        }
        // La pagina ha un'intestazione fissa? Il riepilogo fermo si mette sotto (lo script del renderer ne misura l'altezza).
        if ( $a_lato && ! $builder && class_exists( 'Olobuild_Frontend_Renderer' ) && property_exists( 'Olobuild_Frontend_Renderer', 'needs_sticky_offset_script' ) ) {
            Olobuild_Frontend_Renderer::$needs_sticky_offset_script = true;
        }

        $segno  = $this->render_icon_html( 'check', 0.7, 'aria-hidden="true"' );
        $ed     = function ( $chiave ) use ( $builder ) {
            return $builder ? ' data-olo-editable="' . esc_attr( $chiave ) . '"' : '';
        };
        $prezzo = function ( $o, $quantita ) use ( $dec, $valuta ) {
            if ( abs( $o['prezzo'] ) < 0.00001 ) {
                return [ __( 'Incluso', 'olobuilder' ), true ];
            }
            $t = self::soldi( round( $o['prezzo'], $dec ), $dec, $valuta, true );
            /* translators: %s: il prezzo di un pezzo, per esempio «+289 €» */
            return [ $quantita ? sprintf( __( '%s cad.', 'olobuilder' ), $t ) : $t, false ];
        };

        echo '<div id="' . esc_attr( $uid ) . '" class="' . esc_attr( $classi ) . '" data-ocfg="' . esc_attr( wp_json_encode( $cfg ) ) . '">';
        echo '<div class="ocfg-corpo">';

        // ── Le opzioni, gruppo per gruppo ──
        echo '<div class="ocfg-opzioni">';
        $oi = 0;
        foreach ( $vivi as $gi => $g ) {
            $gid  = $uid . '-g' . $gi;
            $qta  = 'quantita' === $g['tipo'];
            $immg = false;
            foreach ( $g['opz'] as $o ) {
                if ( '' !== $o['img'] ) {
                    $immg = true;
                }
            }
            echo '<fieldset class="ocfg-gruppo ocfg-gruppo--' . esc_attr( $g['tipo'] ) . '" id="' . esc_attr( $gid ) . '" data-g="' . (int) $gi . '" aria-describedby="' . esc_attr( $gid ) . '-e">';
            echo '<legend class="ocfg-legenda"><span class="ocfg-legenda-riga">';
            echo '<span class="ocfg-gruppo-titolo"' . $ed( 'gruppi.' . (int) $g['n'] . '.titolo' ) . '>' . esc_html( $g['titolo'] ) . '</span>';
            if ( '' !== $g['aiuto'] ) {
                /* translators: %s: titolo del gruppo di opzioni */
                $aiuto_lbl = sprintf( __( 'Aiuto: %s', 'olobuilder' ), '' !== $g['titolo'] ? $g['titolo'] : $g['id'] );
                echo '<button type="button" class="ocfg-info" aria-expanded="false" aria-controls="' . esc_attr( $gid ) . '-a" aria-label="' . esc_attr( $aiuto_lbl ) . '" data-olo-interactive>' . $this->render_icon_html( 'info', 0.85, 'aria-hidden="true"' ) . '</button>';
            }
            echo '</span></legend>';
            if ( '' !== $g['aiuto'] ) {
                echo '<div class="ocfg-aiuto" id="' . esc_attr( $gid ) . '-a" hidden>' . nl2br( esc_html( $g['aiuto'] ) ) . '</div>';
            }
            echo '<p class="ocfg-errore" id="' . esc_attr( $gid ) . '-e" hidden></p>';
            echo '<div class="ocfg-griglia">';
            foreach ( $g['opz'] as $o ) {
                $oid    = $uid . '-o' . $oi;
                $scelta = $o['q'] > 0;
                $lbl    = $oid . '-n' . ( $diff ? ' ' . $oid . '-p' : '' );
                $descr  = trim( ( '' !== $o['badge'] ? $oid . '-b ' : '' ) . ( '' !== $o['descr'] ? $oid . '-d' : '' ) );
                $cl     = 'ocfg-opz' . ( $qta ? ' ocfg-opz--qta' : '' ) . ( $immg && ! $righe ? ' ocfg-opz--img' : '' ) . ( '' === $o['descr'] ? ' ocfg-opz--breve' : '' ) . ( $scelta ? ' is-scelta' : '' );
                $badge  = '' !== $o['badge'] ? '<span class="ocfg-badge" id="' . esc_attr( $oid ) . '-b">' . esc_html( $o['badge'] ) . '</span>' : '';
                $tag    = $qta ? 'div' : 'label';
                echo '<' . $tag . ' class="' . esc_attr( $cl ) . '" data-o="' . (int) $oi . '"' . ( $qta ? '' : ' data-olo-interactive' ) . '>';
                if ( ! $qta ) {
                    echo '<input class="ocfg-input" type="' . ( 'piu' === $g['tipo'] ? 'checkbox' : 'radio' ) . '" name="' . esc_attr( $gid ) . '" value="' . (int) $oi . '" autocomplete="off"'
                        . ( $scelta ? ' checked' : '' ) . ( 'uno' === $g['tipo'] && $g['obbl'] ? ' required' : '' )
                        . ' aria-labelledby="' . esc_attr( $lbl ) . '"' . ( '' !== $descr ? ' aria-describedby="' . esc_attr( $descr ) . '"' : '' ) . '>';
                }
                // L'immagine (con un segnaposto quando nel gruppo altre opzioni ce l'hanno); nelle schede il badge ci sta sopra.
                if ( $immg ) {
                    if ( '' !== $o['img'] ) {
                        echo '<span class="ocfg-img"><img src="' . esc_url( $o['img'] ) . '" alt="" loading="lazy" decoding="async">' . ( $righe ? '' : $badge ) . '</span>';
                    } else {
                        echo '<span class="ocfg-img ocfg-img--vuota" aria-hidden="true">' . $this->render_icon_html( 'image', 1, 'aria-hidden="true"' ) . '</span>';
                    }
                }
                echo '<span class="ocfg-testo">';
                $badge_qui = ( ! $immg || $righe || '' === $o['img'] ) ? $badge : '';
                echo '<span class="ocfg-testa">' . $badge_qui . '<span class="ocfg-nome" id="' . esc_attr( $oid ) . '-n">' . esc_html( $o['nome'] ) . '</span></span>';
                if ( '' !== $o['descr'] ) {
                    echo '<span class="ocfg-descr" id="' . esc_attr( $oid ) . '-d">' . esc_html( $o['descr'] ) . '</span>';
                }
                if ( $diff || $qta ) {
                    echo '<span class="ocfg-piede">';
                    if ( $diff ) {
                        list( $pt_txt, $incluso ) = $prezzo( $o, $qta );
                        echo '<span class="ocfg-prezzo' . ( $incluso ? ' is-incluso' : '' ) . '" id="' . esc_attr( $oid ) . '-p">' . esc_html( $pt_txt ) . '</span>';
                    }
                    if ( $qta ) {
                        /* translators: %s: nome dell'opzione */
                        $meno = sprintf( __( '%s: uno in meno', 'olobuilder' ), $o['nome'] );
                        /* translators: %s: nome dell'opzione */
                        $piu = sprintf( __( '%s: uno in più', 'olobuilder' ), $o['nome'] );
                        echo '<span class="ocfg-qta">';
                        echo '<button type="button" class="ocfg-passo ocfg-meno" aria-label="' . esc_attr( $meno ) . '" data-olo-interactive' . ( $o['q'] <= $o['min'] ? ' disabled' : '' ) . '>' . $this->render_icon_html( 'minus', 0.8, 'aria-hidden="true"' ) . '</button>';
                        echo '<input class="ocfg-num" type="number" inputmode="numeric" min="' . (int) $o['min'] . '" max="' . (int) $o['max'] . '" step="1" value="' . (int) $o['q'] . '" autocomplete="off"'
                            . ' aria-labelledby="' . esc_attr( $oid ) . '-n"' . ( '' !== trim( $descr . ( $diff ? ' ' . $oid . '-p' : '' ) ) ? ' aria-describedby="' . esc_attr( trim( $descr . ( $diff ? ' ' . $oid . '-p' : '' ) ) ) . '"' : '' ) . ' data-olo-interactive>';
                        echo '<button type="button" class="ocfg-passo ocfg-piu" aria-label="' . esc_attr( $piu ) . '" data-olo-interactive' . ( $o['q'] >= $o['max'] ? ' disabled' : '' ) . '>' . $this->render_icon_html( 'plus', 0.8, 'aria-hidden="true"' ) . '</button>';
                        echo '</span>';
                    }
                    echo '</span>';
                }
                echo '</span>';
                if ( ! $qta ) {
                    echo '<span class="ocfg-segno" aria-hidden="true">' . $segno . '</span>';
                }
                echo '</' . $tag . '>';
                $oi++;
            }
            echo '</div></fieldset>';
        }
        if ( $builder ) {
            echo $note();
        }
        echo '</div>';

        // ── La testata del riepilogo col totale (impilato) ──
        $tot_html = function ( $dove ) use ( $totale, $dec, $valuta, $periodo, $testo_tot, $nota, $ed ) {
            return '<span class="ocfg-totale-etichetta"' . ( 'pannello' === $dove ? $ed( 'testo_totale' ) : '' ) . '>' . esc_html( $testo_tot ) . '</span>'
                . '<span class="ocfg-totale-valore"><span class="ocfg-cifra">' . esc_html( self::soldi( $totale, $dec, $valuta ) ) . '</span>'
                . ( '' !== $periodo ? '<span class="ocfg-periodo">' . esc_html( $periodo ) . '</span>' : '' ) . '</span>'
                . ( '' !== $nota ? '<p class="ocfg-nota"' . ( 'pannello' === $dove ? $ed( 'nota_prezzo' ) : '' ) . '>' . esc_html( $nota ) . '</p>' : '' );
        };
        echo '<div class="ocfg-barra"><div class="ocfg-barra-totale">' . $tot_html( 'barra' ) . '</div>';
        echo '<button type="button" class="ocfg-vai" data-olo-interactive>' . esc_html__( 'Riepilogo', 'olobuilder' ) . $this->render_icon_html( 'chevron-down', 0.8, 'aria-hidden="true"' ) . '</button></div>';

        // ── Il riepilogo ──
        $titolo_vis = $con_lista && '' !== $titolo_r;
        echo '<div class="ocfg-pannello' . ( $con_lista ? ' ocfg-pannello--doppio' : '' ) . '"' . ( $titolo_vis ? ' role="region" aria-labelledby="' . esc_attr( $titolo_rid ) . '"' : '' ) . '>';
        if ( $con_lista ) {
            echo '<div class="ocfg-colonna">';
            if ( $titolo_vis ) {
                echo '<h3 class="ocfg-pannello-titolo" id="' . esc_attr( $titolo_rid ) . '"' . $ed( 'titolo_riepilogo' ) . '>' . esc_html( $titolo_r ) . '</h3>';
            }
            echo '<dl class="ocfg-lista">';
            if ( $diff && abs( $base ) > 0.00001 ) {
                echo '<div class="ocfg-riga ocfg-riga--base"><dt>' . esc_html__( 'Prezzo base', 'olobuilder' ) . '</dt><dd><span class="ocfg-riga-nome"></span><span class="ocfg-riga-prezzo">' . esc_html( self::soldi( $base, $dec, $valuta ) ) . '</span></dd></div>';
            }
            foreach ( $vivi as $gi => $g ) {
                $sc = array_values( array_filter( $g['opz'], static function ( $o ) {
                    return $o['q'] > 0;
                } ) );
                echo '<div class="ocfg-riga' . ( ! $sc && $g['obbl'] ? ' is-manca' : '' ) . '" data-g="' . (int) $gi . '"' . ( ! $sc && ! $g['obbl'] ? ' hidden' : '' ) . '><dt>' . esc_html( $g['titolo'] ) . '</dt>';
                if ( ! $sc && $g['obbl'] ) {
                    echo '<dd>' . esc_html__( 'Da scegliere', 'olobuilder' ) . '</dd>';
                }
                foreach ( $sc as $o ) {
                    $tot_o = round( $o['prezzo'] * $o['q'], $dec );
                    echo '<dd><span class="ocfg-riga-nome">' . esc_html( $o['nome'] . ( 'quantita' === $g['tipo'] ? ' × ' . $o['q'] : '' ) ) . '</span>'
                        . ( $diff ? '<span class="ocfg-riga-prezzo' . ( abs( $tot_o ) < 0.00001 ? ' is-incluso' : '' ) . '">' . esc_html( abs( $tot_o ) < 0.00001 ? __( 'Incluso', 'olobuilder' ) : self::soldi( $tot_o, $dec, $valuta, true ) ) . '</span>' : '' ) . '</dd>';
                }
                echo '</div>';
            }
            echo '</dl></div>';
        }
        echo '<div class="ocfg-colonna">';
        if ( $indicatori ) {
            echo '<div class="ocfg-indicatori">';
            foreach ( $indicatori as $j => $d ) {
                $vid = $uid . '-i' . $j;
                $vt  = round( $d['v'], 1 );
                $pc  = $d['max'] > 0 ? round( $d['v'] / $d['max'] * 100, 1 ) : 0;
                echo '<div class="ocfg-ind" data-i="' . (int) $j . '" role="meter" aria-valuemin="0" aria-valuemax="' . esc_attr( (string) $d['max'] ) . '" aria-valuenow="' . esc_attr( (string) $vt ) . '" aria-valuetext="' . esc_attr( $vt . ' / ' . $d['max'] ) . '" aria-labelledby="' . esc_attr( $vid ) . '">';
                echo '<span class="ocfg-ind-testa"><span id="' . esc_attr( $vid ) . '">' . esc_html( $d['nome'] ) . '</span><span class="ocfg-ind-val" aria-hidden="true">' . esc_html( (string) $vt ) . '</span></span>';
                echo '<span class="ocfg-ind-barra" aria-hidden="true"><span class="ocfg-ind-pieno" style="width:' . esc_attr( (string) $pc ) . '%"></span></span>';
                echo '</div>';
            }
            echo '</div>';
        }
        echo '<div class="ocfg-totale">' . $tot_html( 'pannello' ) . '</div>';
        if ( $con_puls || '' !== $testo_az ) {
            echo '<div class="ocfg-azioni">';
            if ( $con_puls ) {
                echo '<button type="button" class="ocfg-pulsante" data-olo-interactive><span' . $ed( 'testo_pulsante' ) . '>' . esc_html( $testo_p ) . '</span>' . $this->render_icon_html( 'arrow-right', 0.9, 'aria-hidden="true"' ) . '</button>';
            }
            if ( '' !== $testo_az ) {
                echo '<button type="button" class="ocfg-azzera" data-olo-interactive>' . $this->render_icon_html( 'rotate-ccw', 0.8, 'aria-hidden="true"' ) . '<span' . $ed( 'testo_azzera' ) . '>' . esc_html( $testo_az ) . '</span></button>';
            }
            echo '</div>';
        }
        echo '<p class="ocfg-stato" role="status"></p>';
        echo '<p class="ocfg-sr ocfg-vivo" aria-live="polite" aria-atomic="true"></p>';
        echo '</div></div>';
        echo '</div></div>';

        echo '<script>' . $this->script() . '[].forEach.call(document.querySelectorAll("[data-ocfg]"),function(x){window.oloConfiguratore(x);});</script>';
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }

    /**
     * La funzione del configuratore. Va in OGNI istanza (definita una volta sola, «if(!window…)»): una sezione caricata
     * dopo (<template> delle sezioni pigre) può eseguire il suo script prima di quello della prima istanza. Niente «&»
     * nello script (wptexturize lo riscrive fra un «<» e un «>»): condizioni annidate e la «&» da String.fromCharCode(38).
     * Ogni istanza si avvia una volta (data-ocfg-pronto), anche quando lo script rigira (anteprima del builder).
     */
    private function script() {
        return <<<'JS'
if(!window.oloConfiguratore){window.oloConfiguratore=function(el){
if(!el){return;}
if(el.getAttribute("data-ocfg-pronto")==="1"){return;}
el.setAttribute("data-ocfg-pronto","1");
var C={};try{C=JSON.parse(el.getAttribute("data-ocfg")||"{}");}catch(e){}
var G=C.g||[],O=C.o||[],IND=C.ind||[],T=C.tt||{};
var dec=Math.max(0,Math.min(2,+C.dec||0)),valuta=String(C.valuta||""),periodo=String(C.periodo||"");
var riduci=false;try{riduci=window.matchMedia("(prefers-reduced-motion: reduce)").matches;}catch(e){}
var lingua=String(document.documentElement.getAttribute("lang")||"it").replace("_","-");
var nf=null,nf1=null;
try{nf=new Intl.NumberFormat(lingua,{minimumFractionDigits:dec,maximumFractionDigits:dec,useGrouping:"always"});nf1=new Intl.NumberFormat(lingua,{maximumFractionDigits:1});}catch(e){try{nf=new Intl.NumberFormat(undefined,{minimumFractionDigits:dec,maximumFractionDigits:dec});nf1=new Intl.NumberFormat(undefined,{maximumFractionDigits:1});}catch(x){nf=null;nf1=null;}}
var prima=false,attacca=false;
try{
var parti=new Intl.NumberFormat(lingua,{style:"currency",currency:"EUR"}).formatToParts(1),ic=-1,ii=-1;
parti.forEach(function(p,i){if(p.type==="currency"){ic=i;}if(p.type==="integer"){if(ii<0){ii=i;}}});
if(ic>-1){if(ii>-1){prima=ic<ii;}}
attacca=prima;parti.forEach(function(p){if(p.type==="literal"){attacca=false;}});
}catch(e){}
var NB=String.fromCharCode(160),MENO=String.fromCharCode(8722),PER=String.fromCharCode(215);
function tondo(n){var f=Math.pow(10,dec);return Math.round(n*f)/f;}
function cifra(n){var v=Math.abs(n);return nf?nf.format(v):v.toFixed(dec);}
function uno(n){return nf1?nf1.format(n):String(n);}
function soldi(n,segno){
var s=cifra(n),t=s;
if(valuta){
if(prima){var sp=NB;if(attacca){if(!/^[A-Za-z]{2,}$/.test(valuta)){sp="";}}t=valuta+sp+s;}else{t=s+NB+valuta;}
}
if(n<0){return MENO+t;}
if(segno){return "+"+t;}
return t;
}
var V=[];
[].forEach.call(el.querySelectorAll(".ocfg-opz[data-o]"),function(c){var i=+c.getAttribute("data-o");V[i]={c:c,inp:c.querySelector(".ocfg-input"),num:c.querySelector(".ocfg-num"),meno:c.querySelector(".ocfg-meno"),piu:c.querySelector(".ocfg-piu")};});
function lim(o,n){return Math.max(+o.min||0,Math.min(+o.max||0,n));}
function qta(i){
var v=V[i],o=O[i];if(!v){return 0;}if(!o){return 0;}
if(v.num){var n=parseInt(v.num.value,10);if(isNaN(n)){n=0;}return lim(o,n);}
return v.inp?(v.inp.checked?1:0):0;
}
function totale(){var t=+C.base||0;O.forEach(function(o,i){var k=qta(i);if(k){t+=(+o.p||0)*k;}});return tondo(t);}
function scelteDi(gi){var r=[];O.forEach(function(o,i){if(o.g!==gi){return;}var k=qta(i);if(k>0){r.push({o:o,k:k});}});return r;}
function completo(gi){var g=G[gi]||{};if(!g.obbl){return true;}return scelteDi(gi).length>0;}
function gruppoEl(gi){return el.querySelector('.ocfg-gruppo[data-g="'+gi+'"]');}
var lista=el.querySelector(".ocfg-lista");
function aggiornaLista(){
if(!lista){return;}
[].forEach.call(lista.querySelectorAll(".ocfg-riga[data-g]"),function(r){
var gi=+r.getAttribute("data-g"),g=G[gi]||{},sc=scelteDi(gi);
[].slice.call(r.querySelectorAll("dd")).forEach(function(d){r.removeChild(d);});
r.classList.remove("is-manca");r.hidden=false;
if(!sc.length){
if(g.obbl){r.classList.add("is-manca");var d0=document.createElement("dd");d0.textContent=T.da||"";r.appendChild(d0);}else{r.hidden=true;}
return;
}
sc.forEach(function(s){
var d=document.createElement("dd"),n=document.createElement("span");
n.className="ocfg-riga-nome";n.textContent=s.o.n+(g.tipo==="quantita"?" "+PER+" "+s.k:"");d.appendChild(n);
if(C.diff){var p=document.createElement("span"),pz=tondo((+s.o.p||0)*s.k);p.className="ocfg-riga-prezzo"+(pz?"":" is-incluso");p.textContent=pz?soldi(pz,true):(T.incluso||"");d.appendChild(p);}
r.appendChild(d);
});
});
}
var indEl=[].slice.call(el.querySelectorAll(".ocfg-ind"));
function aggiornaInd(){
indEl.forEach(function(m){
var d=IND[+m.getAttribute("data-i")];if(!d){return;}
var s=0;O.forEach(function(o,i){var k=qta(i);if(!k){return;}var pt=o.pt||{};if(Object.prototype.hasOwnProperty.call(pt,d.id)){s+=(+pt[d.id]||0)*k;}});
var mx=+d.max||100,v=Math.round(Math.max(0,Math.min(mx,s))*10)/10,pc=mx?v/mx*100:0;
m.setAttribute("aria-valuenow",String(v));m.setAttribute("aria-valuetext",uno(v)+" / "+uno(mx));
var val=m.querySelector(".ocfg-ind-val");if(val){val.textContent=uno(v);}
var b=m.querySelector(".ocfg-ind-pieno");if(b){b.style.width=pc.toFixed(1)+"%";}
});
}
var cifre=[].slice.call(el.querySelectorAll(".ocfg-cifra")),mostrato=null,giro=0;
function scriviTot(v){cifre.forEach(function(c){c.textContent=soldi(v,false);});}
function animaTot(dest){
if(mostrato===null){mostrato=dest;scriviTot(dest);return;}
if(riduci){mostrato=dest;scriviTot(dest);return;}
if(mostrato===dest){scriviTot(dest);return;}
var da=mostrato,t0=0,mio=++giro;
function passo(ts){
if(mio!==giro){return;}
if(!t0){t0=ts;}
var k=Math.min(1,(ts-t0)/420),e=1-Math.pow(1-k,3),v=da+(dest-da)*e;
mostrato=k<1?v:dest;scriviTot(k<1?tondo(v):dest);
if(k<1){requestAnimationFrame(passo);}
}
requestAnimationFrame(passo);
}
[].forEach.call(el.querySelectorAll(".ocfg-opz[data-o]"),function(c){
var o=O[+c.getAttribute("data-o")],pe=c.querySelector(".ocfg-prezzo");if(!o){return;}if(!pe){return;}
var p=tondo(+o.p||0),t=p?soldi(p,true):(T.incluso||"");
if(p){if((G[o.g]||{}).tipo==="quantita"){t=String(T.cad||"%s").replace("%s",function(){return t;});}}
pe.textContent=t;
});
var rb=el.querySelector(".ocfg-riga--base .ocfg-riga-prezzo");if(rb){rb.textContent=soldi(+C.base||0,false);}
var vivo=el.querySelector(".ocfg-vivo"),tv=0;
function annuncia(t){if(!vivo){return;}clearTimeout(tv);tv=setTimeout(function(){vivo.textContent=t;},450);}
var stato=el.querySelector(".ocfg-stato");
function avvisa(t,info){if(!stato){return;}stato.textContent=t||"";if(info){stato.classList.add("is-info");}else{stato.classList.remove("is-info");}}
function segnala(gi,msg){
var fs=gruppoEl(gi);if(!fs){return;}var p=fs.querySelector(".ocfg-errore");
if(msg){fs.classList.add("is-manca");if(p){p.textContent=msg;p.hidden=false;}}else{fs.classList.remove("is-manca");if(p){p.textContent="";p.hidden=true;}}
}
function testoRiepilogo(){
var r=[],t=totale();
if(T.titolo){r.push(T.titolo);}
if(C.diff){if(+C.base){r.push((T.base||"")+": "+soldi(+C.base,false));}}
G.forEach(function(g,gi){
var sc=scelteDi(gi);if(!sc.length){return;}
r.push((g.t||"")+": "+sc.map(function(s){var x=s.o.n+(g.tipo==="quantita"?" "+PER+" "+s.k:"");if(C.diff){var pz=tondo((+s.o.p||0)*s.k);if(pz){x+=" ("+soldi(pz,true)+")";}}return x;}).join(", "));
});
r.push((T.totale||"")+": "+soldi(t,false)+periodo+(T.nota?" ("+T.nota+")":""));
return r.join("\n");
}
var toccato=false,ultimo="";
function campi(){
var out=[];if(!C.campo){return out;}
var n=String(C.campo);
[].forEach.call(document.querySelectorAll("form [name]"),function(f){
var nm=f.getAttribute("name")||"";if(nm!==n){if(nm!=="fields["+n+"]"){return;}}
var tp=String(f.getAttribute("type")||"").toLowerCase();
if(f.tagName==="TEXTAREA"){out.push(f);return;}
if(f.tagName==="INPUT"){if(tp===""||tp==="text"||tp==="hidden"||tp==="search"){out.push(f);}}
});
return out;
}
function scrivi(f,blocco,forza){
var v=String(f.value||""),nuovo=blocco;
if(v){if(v!==ultimo){
if(!forza){return false;}
var k=ultimo?v.indexOf(ultimo):-1;
nuovo=k>-1?v.slice(0,k)+blocco+v.slice(k+ultimo.length):blocco+"\n\n"+v;
}}
f.value=nuovo;
try{f.dispatchEvent(new Event("input",{bubbles:true}));f.dispatchEvent(new Event("change",{bubbles:true}));}catch(e){}
return true;
}
function sincronizza(forza){
if(!C.campo){return [];}
if(!forza){if(!toccato){return [];}}
var blocco=testoRiepilogo(),fatti=[];
campi().forEach(function(f){if(scrivi(f,blocco,forza)){fatti.push(f);}});
if(fatti.length){ultimo=blocco;}
return fatti;
}
function aggiorna(daUtente,pre){
V.forEach(function(v,i){
if(!v){return;}var k=qta(i),o=O[i]||{};
if(k>0){v.c.classList.add("is-scelta");}else{v.c.classList.remove("is-scelta");}
if(v.num){if(v.meno){v.meno.disabled=k<=(+o.min||0);}if(v.piu){v.piu.disabled=k>=(+o.max||0);}}
});
aggiornaLista();aggiornaInd();
var t=totale();animaTot(t);
if(!daUtente){return;}
toccato=true;pulsanteServe();
annuncia((pre?pre+" ":"")+(T.totale||"")+": "+soldi(t,false)+periodo);
G.forEach(function(g,gi){var fs=gruppoEl(gi);if(fs){if(fs.classList.contains("is-manca")){if(completo(gi)){segnala(gi,"");avvisa("",false);}}}});
sincronizza(false);
}
V.forEach(function(v,i){
if(!v){return;}
var o=O[i]||{},g=G[o.g]||{};
if(v.inp){
var inp=v.inp,c=v.c;
if(inp.type==="radio"){if(!g.obbl){
var segna=function(){inp.setAttribute("data-era",inp.checked?"1":"0");};
c.addEventListener("pointerdown",segna);
var spazio=function(e){return e.key===" "||e.key==="Spacebar";};
inp.addEventListener("keydown",function(e){if(spazio(e)){segna();}});
inp.addEventListener("keyup",function(e){if(!spazio(e)){return;}if(inp.getAttribute("data-era")!=="1"){return;}inp.removeAttribute("data-era");setTimeout(function(){inp.checked=false;aggiorna(true);},0);});
inp.addEventListener("click",function(){
var era=inp.getAttribute("data-era")==="1";inp.removeAttribute("data-era");
if(era){setTimeout(function(){inp.checked=false;aggiorna(true);},0);}
});
}}
inp.addEventListener("change",function(){aggiorna(true);});
inp.addEventListener("focus",function(){var fv=true;try{fv=inp.matches(":focus-visible");}catch(x){}if(fv){c.classList.add("is-fuoco");}});
inp.addEventListener("blur",function(){c.classList.remove("is-fuoco");});
}
if(v.num){
var num=v.num,metti=function(n){num.value=String(lim(o,n));aggiorna(true);};
if(v.meno){v.meno.addEventListener("click",function(){metti(qta(i)-1);});}
if(v.piu){v.piu.addEventListener("click",function(){metti(qta(i)+1);});}
num.addEventListener("input",function(){aggiorna(true);});
num.addEventListener("change",function(){metti(qta(i));});
}
});
var aperto=null;
function chiudi(){if(!aperto){return;}aperto.b.setAttribute("aria-expanded","false");aperto.p.hidden=true;aperto=null;}
[].forEach.call(el.querySelectorAll(".ocfg-info"),function(b){
var p=document.getElementById(b.getAttribute("aria-controls")||"");if(!p){return;}
b.addEventListener("click",function(e){
e.preventDefault();e.stopPropagation();
var era=aperto?aperto.b===b:false;chiudi();if(era){return;}
var lg=b.closest("legend");if(lg){p.style.top=(lg.offsetTop+lg.offsetHeight+6)+"px";}
p.hidden=false;b.setAttribute("aria-expanded","true");aperto={b:b,p:p};
});
});
document.addEventListener("click",function(e){if(!aperto){return;}if(aperto.p.contains(e.target)){return;}if(aperto.b.contains(e.target)){return;}chiudi();});
document.addEventListener("keydown",function(e){if(!aperto){return;}if(e.key==="Escape"||e.key==="Esc"){var b=aperto.b;chiudi();try{b.focus();}catch(x){}}});
function scorri(n,dove){try{n.scrollIntoView({behavior:riduci?"auto":"smooth",block:dove});}catch(x){try{n.scrollIntoView();}catch(y){}}}
function verifica(){
for(var gi=0;gi<G.length;gi++){
if(!completo(gi)){
var g=G[gi],msg=String(g.tipo==="uno"?(T.manca1||""):(T.mancaN||"")).replace("%s",function(){return g.t||"";});
segnala(gi,msg);avvisa(msg,false);
var fs=gruppoEl(gi);
if(fs){scorri(fs,"center");var primo=fs.querySelector(".ocfg-input, .ocfg-num");if(primo){try{primo.focus({preventScroll:true});}catch(x){}}}
return false;
}
}
return true;
}
function componi(modello,testo,tot){
var u=String(modello||""),a=encodeURIComponent(testo),b=encodeURIComponent(tot);
u=u.replace(/\{configurazione\}|%7Bconfigurazione%7D/gi,function(){return a;}).replace(/\{totale\}|%7Btotale%7D/gi,function(){return b;});
var p=u.split("?");if(p.length>2){u=p.shift()+"?"+p.join(String.fromCharCode(38));}
return u;
}
function agisci(){
chiudi();avvisa("",false);
if(!verifica()){return;}
var testo=testoRiepilogo(),tot=soldi(totale(),false)+periodo,fatti=sincronizza(true);
if(C.link){
var u=componi(C.link,testo,tot),a=document.createElement("a");a.href=u;
var pr=String(a.protocol||"").toLowerCase();
if(pr!=="http:"){if(pr!=="https:"){if(pr!=="mailto:"){if(pr!=="tel:"){return;}}}}
if(C.builder){avvisa(String(T.anteprima||"").replace("%s",function(){return u;}),true);return;}
if(pr==="mailto:"||pr==="tel:"){window.location.href=u;return;}
if(a.host!==window.location.host){window.open(u,"_blank","noopener");}else{window.location.assign(u);}
return;
}
if(fatti.length){
var f=fatti[0],box=f.closest?f.closest("form"):null;
avvisa(T.scritto||"",true);
scorri(box||f,"start");
if(f.type!=="hidden"){try{f.focus({preventScroll:true});}catch(x){}}
return;
}
if(C.builder){avvisa(String(T.modulo||"").replace("%s",function(){return C.campo;}),true);}
}
var puls=el.querySelector(".ocfg-pulsante");if(puls){puls.addEventListener("click",agisci);}
function pulsanteServe(){if(!puls){return;}if(C.link){return;}if(C.builder){return;}puls.hidden=campi().length===0;}
var azz=el.querySelector(".ocfg-azzera");
if(azz){azz.addEventListener("click",function(){
chiudi();
V.forEach(function(v,i){if(!v){return;}var d=+((O[i]||{}).d)||0;if(v.inp){v.inp.checked=d>0;}if(v.num){v.num.value=String(d);}});
G.forEach(function(g,gi){segnala(gi,"");});avvisa("",false);
aggiorna(true,T.ricominciato||"");
});}
var pan=el.querySelector(".ocfg-pannello"),vai=el.querySelector(".ocfg-vai"),barra=el.querySelector(".ocfg-barra");
if(vai){if(pan){vai.addEventListener("click",function(){
var y=pan.getBoundingClientRect().top+(window.pageYOffset||0)-(barra?barra.offsetHeight:0)-16;
try{window.scrollTo({top:y,behavior:riduci?"auto":"smooth"});}catch(x){window.scrollTo(0,y);}
});}}
if(barra){if(window.IntersectionObserver){try{new IntersectionObserver(function(es){es.forEach(function(e){if(!e.isIntersecting){return;}if(e.intersectionRatio<0.99){el.classList.remove("is-giunta");}else{el.classList.add("is-giunta");}});},{threshold:[0,0.99,1],rootMargin:"0px 0px -1px 0px"}).observe(barra);}catch(e){}}}
function misura(){var w=el.getBoundingClientRect().width;if(w){if(w<720){el.classList.add("is-stretta");}else{el.classList.remove("is-stretta");}}}
misura();pulsanteServe();
window.addEventListener("load",pulsanteServe);setTimeout(pulsanteServe,1500);
if(window.ResizeObserver){try{new ResizeObserver(function(){misura();}).observe(el);}catch(e){}}else{window.addEventListener("resize",misura);}
aggiorna(false);
};}
JS;
    }
}
