<?php
/**
 * Tile Calendario soggiorni — il calendario della disponibilità di una casa (baita, appartamento, casa al mare) con
 * i prezzi, la scelta delle date e il riepilogo del soggiorno.
 *
 * Il calendario lo costruisce il browser: «oggi» (nel fuso del sito, se c'è), le date relative dei periodi occupati
 * ('+3' = fra tre giorni: le demo restano credibili), i nomi di mesi e giorni con Intl.DateTimeFormat nella lingua
 * della pagina, la settimana dal lunedì. Ogni giorno è una cella di una griglia role="grid" (frecce, Inizio/Fine,
 * Pag su/giù, Invio o spazio per scegliere, Esc per ricominciare; aria-label con la data intera, lo stato e il prezzo).
 * Una notte è occupata da «dal» fino al giorno prima di «al»: il giorno di partenza resta libero (cambio turno), e
 * il primo giorno occupato può essere il giorno di partenza di chi arriva prima. Il prezzo di una notte viene dalla
 * stagione che contiene quel giorno (MM-GG, anche a cavallo d'anno) o dal prezzo di base; il soggiorno minimo è quello
 * della notte d'arrivo. Il riepilogo somma le notti per stagione, le pulizie e l'imposta (ospiti × notti × imposta),
 * scrive le date nei campi di un Form Contatti della pagina (campi_modulo) e prepara il collegamento della richiesta.
 * Con «Date dall'indirizzo» legge ?arrivo=&partenza=&ospiti= e preseleziona. Il pulsante segue lo stile pulsanti del
 * tema. Nato per i temi della serie «6 temi» (15 ott 2026). Config: src/config/elements/soggiorni.js.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Soggiorni_Tile extends Olobuild_Tile_Base {

    protected $type     = 'soggiorni';
    protected $name     = 'Calendario soggiorni';
    protected $icon     = 'dashicons-calendar-alt';
    protected $category = 'interactive';

    protected $defaults = [
        // contenuto
        'occupati'                    => [
            [ 'id' => 'occ-1', 'dal' => '+3', 'al' => '+7', 'nota' => '' ],
            [ 'id' => 'occ-2', 'dal' => '+11', 'al' => '+14', 'nota' => '' ],
            [ 'id' => 'occ-3', 'dal' => '+20', 'al' => '+27', 'nota' => '' ],
            [ 'id' => 'occ-4', 'dal' => '+41', 'al' => '+46', 'nota' => '' ],
        ],
        'prezzo_notte'                => 120,
        'stagioni'                    => [
            [ 'id' => 'st-1', 'nome' => 'Alta stagione', 'dal' => '07-01', 'al' => '08-31', 'prezzo_notte' => 180, 'minimo_notti' => 7 ],
            [ 'id' => 'st-2', 'nome' => 'Feste di fine anno', 'dal' => '12-20', 'al' => '01-06', 'prezzo_notte' => 190, 'minimo_notti' => 4 ],
        ],
        'minimo_notti'                => 2,
        'ospiti_max'                  => 6,
        'ospiti_iniziali'             => 2,
        'pulizie'                     => 60,
        'tassa_soggiorno'             => 2,
        'etichetta_tassa'             => 'Imposta di soggiorno',
        'valuta'                      => '€',
        'mostra_prezzi'               => true,
        'mesi_visibili'               => 2,
        'mesi_avanti'                 => 12,
        'testo_pulsante'              => 'Richiedi questo soggiorno',
        'link_richiesta'              => '',
        'campi_modulo'                => 'arrivo,partenza,ospiti',
        'parametri'                   => true,
        'nota'                        => '',
        'testo_scegli_arrivo'         => 'Scegli il giorno di arrivo',
        'testo_scegli_partenza'       => 'Ora scegli il giorno di partenza',
        'testo_minimo'                => 'In questo periodo il soggiorno minimo è di {n} notti',
        'testo_occupato'              => 'Nelle date scelte la casa è già occupata',
        'testo_aggiungi'              => 'Aggiungi data',
        'testo_vuoto'                 => 'Scegli le date per vedere il prezzo del soggiorno.',
        'testo_azzera'                => 'Cancella le date',
        'legenda'                     => true,
        'testo_legenda_libero'        => 'Libero',
        'testo_legenda_occupato'      => 'Occupato',
        'testo_legenda_scelte'        => 'Le tue date',
        // stile
        'colore_testo'                => '',
        'colore_tenue'                => '',
        'colore_scelto'               => '',
        'sfondo_intervallo'           => '',
        'sfondo_libero'               => '',
        'sfondo_occupato'             => '',
        'colore_oggi'                 => '',
        'raggio_giorno'               => [ 'tl' => 10, 'tr' => 10, 'br' => 10, 'bl' => 10 ],
        'sfondo_pannello'             => '',
        'bordo_pannello'              => [ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1, 'linked' => true, 'style' => 'solid', 'color' => '' ],
        'raggio_pannello'             => [ 'tl' => 16, 'tr' => 16, 'br' => 16, 'bl' => 16 ],
        'padding_pannello'            => [ 'top' => 28, 'right' => 28, 'bottom' => 28, 'left' => 28 ],
        'ombra'                       => 'md',
        'ombra_h'                     => '0',
        'ombra_v'                     => '4',
        'ombra_blur'                  => '10',
        'ombra_spread'                => '0',
        'ombra_color'                 => 'rgba(0,0,0,0.15)',
        'ombra_inset'                 => false,
        'mese_font_family'            => 'heading',
        'mese_font_size'              => 17,
        'mese_font_size_tablet'       => '',
        'mese_font_size_mobile'       => '',
        'mese_font_weight'            => '600',
        'mese_color'                  => '',
        'settimana_font_family'       => '',
        'settimana_font_size'         => 12,
        'settimana_font_size_tablet'  => '',
        'settimana_font_size_mobile'  => '',
        'settimana_font_weight'       => '600',
        'settimana_uppercase'         => true,
        'settimana_letter_spacing'    => 0.6,
        'settimana_color'             => '',
        'giorno_font_family'          => '',
        'giorno_font_size'            => 15,
        'giorno_font_size_tablet'     => '',
        'giorno_font_size_mobile'     => '',
        'giorno_font_weight'          => '500',
        'giorno_color'                => '',
        'prezzo_font_family'          => '',
        'prezzo_font_size'            => 12,
        'prezzo_font_size_tablet'     => '',
        'prezzo_font_size_mobile'     => '',
        'prezzo_font_weight'          => '500',
        'prezzo_color'                => '',
        'etichetta_font_family'       => '',
        'etichetta_font_size'         => 12,
        'etichetta_font_size_tablet'  => '',
        'etichetta_font_size_mobile'  => '',
        'etichetta_font_weight'       => '600',
        'etichetta_uppercase'         => true,
        'etichetta_letter_spacing'    => 0.6,
        'etichetta_color'             => '',
        'testo_font_family'           => '',
        'testo_font_size'             => 15,
        'testo_font_size_tablet'      => '',
        'testo_font_size_mobile'      => '',
        'testo_font_weight'           => '400',
        'testo_line_height'           => 1.5,
        'testo_color'                 => '',
        'totale_font_family'          => 'heading',
        'totale_font_size'            => 28,
        'totale_font_size_tablet'     => '',
        'totale_font_size_mobile'     => '',
        'totale_font_weight'          => '600',
        'totale_color'                => '',
        'disposizione'                => 'lato',
        'gap'                         => 40,
        'allineamento'                => 'left',
    ];

    /** Uid già dati in questa richiesta: lo stesso nodo reso due volte (un widget ripetuto) resta distinto. */
    private static $uid_usati = [];

    public function get_controls() {
        return [];
    }

    /** Un testo semplice: '' per array e oggetti (niente «Array to string conversion»). */
    private static function testo( $v ) {
        return is_scalar( $v ) ? trim( (string) $v ) : '';
    }

    /** I primi $n caratteri (anche multibyte: il simbolo € è di tre byte). */
    private static function corto( $t, $n ) {
        return function_exists( 'mb_substr' ) ? mb_substr( $t, 0, $n ) : substr( $t, 0, $n );
    }

    /** Un numero fra $min e $max (anche scritto con la virgola: «2,5»); fuori o vuoto = il limite o $min. */
    private static function numero( $v, $min, $max ) {
        $v = str_replace( ',', '.', self::testo( $v ) );
        if ( '' === $v || ! is_numeric( $v ) ) {
            return (float) $min;
        }
        return (float) max( $min, min( $max, (float) $v ) );
    }

    /**
     * Una data di un periodo occupato: 'AAAA-MM-GG' (anche «GG/MM/AAAA», come la scrive chi ha un calendario
     * italiano) oppure relativa a oggi, '+N' / '-N' giorni (la calcola il browser). '' se non è una data.
     */
    private static function data_occupata( $v ) {
        $v = self::testo( $v );
        if ( preg_match( '/^[+-]?\d{1,4}$/', $v ) ) {
            return sprintf( '%+d', (int) $v );
        }
        if ( preg_match( '/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $v, $m ) ) {
            $a = (int) $m[1];
            $me = (int) $m[2];
            $g = (int) $m[3];
        } elseif ( preg_match( '#^(\d{1,2})[/.](\d{1,2})[/.](\d{4})$#', $v, $m ) ) {
            $a = (int) $m[3];
            $me = (int) $m[2];
            $g = (int) $m[1];
        } else {
            return '';
        }
        return checkdate( $me, $g, $a ) ? sprintf( '%04d-%02d-%02d', $a, $me, $g ) : '';
    }

    /** Il giorno di una stagione, 'MM-GG' (anche «GG/MM» o una data intera, di cui conta mese e giorno); '' se no. */
    private static function mese_giorno( $v ) {
        $v = self::testo( $v );
        if ( preg_match( '/^(?:\d{4}-)?(\d{1,2})-(\d{1,2})$/', $v, $m ) ) {
            $me = (int) $m[1];
            $g = (int) $m[2];
        } elseif ( preg_match( '#^(\d{1,2})/(\d{1,2})$#', $v, $m ) ) {
            $me = (int) $m[2];
            $g = (int) $m[1];
        } else {
            return '';
        }
        if ( $me < 1 || $me > 12 || $g < 1 || $g > 31 ) {
            return '';
        }
        return sprintf( '%02d-%02d', $me, $g );
    }

    /**
     * Il modello del collegamento della richiesta: http(s), mailto:, tel:, relativo, «//…» o «#ancora»; '' per gli
     * altri schemi (javascript:, data:…) e coi caratteri che non stanno in un indirizzo. Le graffe dei segnaposto
     * restano (esc_url le toglierebbe): il browser li sostituisce coi valori codificati e ricontrolla lo schema.
     */
    private static function modello( $v ) {
        if ( is_array( $v ) ) {
            $v = $v['url'] ?? '';
        }
        $v = self::testo( $v );
        if ( '' === $v || preg_match( '/[<>"\\\\`\x00-\x1f]/', $v ) ) {
            return '';
        }
        if ( preg_match( '/^([a-z][a-z0-9+.\-]*):/i', $v, $m ) ) {
            if ( ! in_array( strtolower( $m[1] ), [ 'http', 'https', 'mailto', 'tel' ], true ) ) {
                return '';
            }
        }
        return substr( $v, 0, 2000 );
    }

    /** Il nome di un campo del Form Contatti come lo salva il modulo (sanitize_key: minuscole, cifre, - e _). */
    private static function nome_campo( $v ) {
        return substr( (string) preg_replace( '/[^a-z0-9_-]/', '', strtolower( self::testo( $v ) ) ), 0, 40 );
    }

    /** CSS di un testo dal controllo tipografia (famiglia, misura per dispositivo, peso, maiuscolo, spaziatura, interlinea, colore). */
    private function css_testo( $s, $k, $selettore ) {
        $d   = '';
        $fam = $this->resolve_font_family( self::testo( $s[ $k . '_font_family' ] ?? '' ) );
        $fs  = (float) self::numero( $s[ $k . '_font_size' ] ?? 0, 0, 200 );
        $fw  = $this->font_weight_css( self::testo( $s[ $k . '_font_weight' ] ?? '' ) );
        $ls  = self::testo( $s[ $k . '_letter_spacing' ] ?? '' );
        $lh  = self::testo( $s[ $k . '_line_height' ] ?? '' );
        $col = $this->safe_color_css( self::testo( $s[ $k . '_color' ] ?? '' ) );
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
            $v = (float) self::numero( $s[ $k . '_font_size_' . $disp ] ?? 0, 0, 200 );
            if ( $v > 0 ) {
                $css .= '@media (max-width:' . $px . 'px){' . $selettore . '{font-size:' . $v . 'px}}';
            }
        }
        return $css;
    }

    public function render( $settings ) {
        $s       = wp_parse_args( is_array( $settings ) ? $settings : [], $this->defaults );
        $uid     = 'osg-' . $this->chiave_stabile( $settings );
        if ( isset( self::$uid_usati[ $uid ] ) ) {
            self::$uid_usati[ $uid ]++;
            $uid .= '-' . self::$uid_usati[ $uid ];
        } else {
            self::$uid_usati[ $uid ] = 1;
        }
        $sel     = '#' . $uid;
        $builder = ! empty( $s['_builder_mode'] );

        // ── Periodi occupati: [dal, al] come li calcola il browser (date o '+N'); «al» vuoto = una notte ──
        $occupati = [];
        foreach ( is_array( $s['occupati'] ) ? $s['occupati'] : [] as $o ) {
            if ( ! is_array( $o ) ) {
                continue;
            }
            $dal = self::data_occupata( $o['dal'] ?? '' );
            if ( '' === $dal ) {
                continue;
            }
            // La nota resta all'inspector (da dove arriva la prenotazione, chi è): sul sito non si scrive.
            $occupati[] = [ $dal, self::data_occupata( $o['al'] ?? '' ) ];
            if ( count( $occupati ) >= 500 ) {
                break;
            }
        }

        // ── Stagioni: [nome, dal, al, prezzo, minimo]; prezzo 0 = quello di base, minimo 0 = quello di base ──
        $stagioni   = [];
        $con_prezzi = false;
        foreach ( is_array( $s['stagioni'] ) ? $s['stagioni'] : [] as $v ) {
            if ( ! is_array( $v ) ) {
                continue;
            }
            $dal = self::mese_giorno( $v['dal'] ?? '' );
            $al  = self::mese_giorno( $v['al'] ?? '' );
            if ( '' === $dal || '' === $al ) {
                continue;
            }
            $p = self::numero( $v['prezzo_notte'] ?? 0, 0, 1000000 );
            if ( $p > 0 ) {
                $con_prezzi = true;
            }
            $stagioni[] = [ self::corto( self::testo( $v['nome'] ?? '' ), 80 ), $dal, $al, $p, (int) self::numero( $v['minimo_notti'] ?? 0, 0, 90 ) ];
            if ( count( $stagioni ) >= 60 ) {
                break;
            }
        }

        // ── Numeri e regole ──
        $base    = self::numero( $s['prezzo_notte'], 0, 1000000 );
        if ( $base > 0 ) {
            $con_prezzi = true;
        }
        $minimo  = max( 1, (int) self::numero( $s['minimo_notti'], 1, 90 ) );
        $osp_max = max( 1, (int) self::numero( $s['ospiti_max'], 1, 50 ) );
        $osp_ini = max( 1, min( $osp_max, (int) self::numero( $s['ospiti_iniziali'], 1, 50 ) ) );
        $pulizie = self::numero( $s['pulizie'], 0, 1000000 );
        $tassa   = self::numero( $s['tassa_soggiorno'], 0, 10000 );
        $vis     = (int) self::numero( $s['mesi_visibili'], 1, 2 );
        $avanti  = (int) self::numero( $s['mesi_avanti'], 1, 36 );
        $nm      = min( $vis, $avanti );
        $mostra  = ! empty( $s['mostra_prezzi'] );
        $valuta  = self::corto( sanitize_text_field( self::testo( $s['valuta'] ) ), 8 );
        $link    = self::modello( $s['link_richiesta'] );
        $campi   = [];
        foreach ( array_slice( explode( ',', self::testo( $s['campi_modulo'] ) ), 0, 3 ) as $c ) {
            $campi[] = self::nome_campo( $c );
        }

        // ── Testi (vuoti = quelli di sempre, nella lingua del sito) ──
        $testi = [
            'arrivo'   => self::testo( $s['testo_scegli_arrivo'] ) ?: __( 'Scegli il giorno di arrivo', 'olobuilder' ),
            'partenza' => self::testo( $s['testo_scegli_partenza'] ) ?: __( 'Ora scegli il giorno di partenza', 'olobuilder' ),
            'minimo'   => self::testo( $s['testo_minimo'] ) ?: __( 'In questo periodo il soggiorno minimo è di {n} notti', 'olobuilder' ),
            'occupato' => self::testo( $s['testo_occupato'] ) ?: __( 'Nelle date scelte la casa è già occupata', 'olobuilder' ),
        ];
        $testo_p   = self::testo( $s['testo_pulsante'] ) ?: __( 'Richiedi questo soggiorno', 'olobuilder' );
        // Riepilogo e legenda: anche questi si scrivono (un tema che dà del voi resta coerente); vuoti = quelli di sempre.
        $t_agg     = self::testo( $s['testo_aggiungi'] ) ?: __( 'Aggiungi data', 'olobuilder' );
        $t_vuoto   = self::testo( $s['testo_vuoto'] ) ?: __( 'Scegli le date per vedere il prezzo del soggiorno.', 'olobuilder' );
        $t_azzera  = self::testo( $s['testo_azzera'] ) ?: __( 'Cancella le date', 'olobuilder' );
        $leg_lib   = self::testo( $s['testo_legenda_libero'] ) ?: __( 'Libero', 'olobuilder' );
        $leg_occ   = self::testo( $s['testo_legenda_occupato'] ) ?: __( 'Occupato', 'olobuilder' );
        $leg_sc    = self::testo( $s['testo_legenda_scelte'] ) ?: __( 'Le tue date', 'olobuilder' );
        $et_tassa  = self::testo( $s['etichetta_tassa'] ) ?: __( 'Imposta di soggiorno', 'olobuilder' );
        $nota      = self::testo( $s['nota'] );
        $ospiti_n  = str_replace( '{n}', (string) $osp_ini, __( '{n} ospiti', 'olobuilder' ) );
        if ( 1 === $osp_ini ) {
            $ospiti_n = __( '1 ospite', 'olobuilder' );
        }

        // ── Fuso del sito: «oggi» è quello della casa, non di chi guarda da un altro fuso ──
        $tz  = function_exists( 'wp_timezone_string' ) ? (string) wp_timezone_string() : '';
        $off = null;
        if ( function_exists( 'wp_timezone' ) ) {
            $off = (int) round( wp_timezone()->getOffset( new DateTime( 'now', wp_timezone() ) ) / 60 );
        }

        // ── Colori: tutti dai token del tema, salvo quelli scelti ──
        $c_testo  = $this->safe_color_css( self::testo( $s['colore_testo'] ) );
        $c_tenue  = $this->safe_color_css( self::testo( $s['colore_tenue'] ) ) ?: 'color-mix(in srgb, currentColor 72%, transparent)'; // 72%: il tenue resta leggibile (AA) anche a 12 px
        $sc_s     = $this->safe_color_css( self::testo( $s['colore_scelto'] ) );
        $c_scelto = '' !== $sc_s ? $sc_s : 'var(--olo-color-primary)';
        $c_sc_t   = '' !== $sc_s
            ? ( Olobuild_Tile_Utils::testo_su_fondo( $sc_s, 'var(--olo-color-light, #ffffff)' ) ?: 'var(--olo-color-light, #ffffff)' )
            : 'var(--olo-color-primary-contrast, #ffffff)';
        $c_int    = $this->safe_color_css( self::testo( $s['sfondo_intervallo'] ) ) ?: 'color-mix(in srgb, var(--osg-sc) 15%, transparent)';
        $c_lib    = $this->safe_color_css( self::testo( $s['sfondo_libero'] ) ) ?: 'color-mix(in srgb, currentColor 6%, transparent)';
        $c_occ    = $this->safe_color_css( self::testo( $s['sfondo_occupato'] ) ) ?: 'transparent';
        $c_oggi   = $this->safe_color_css( self::testo( $s['colore_oggi'] ) ) ?: 'currentColor';
        $p_bg_s   = $this->safe_color_css( self::testo( $s['sfondo_pannello'] ) );
        $p_trasp  = 'transparent' === strtolower( $p_bg_s );
        $p_bg     = '' !== $p_bg_s ? $p_bg_s : 'var(--olo-color-background, #ffffff)';
        $p_txt    = $c_testo;
        if ( '' === $p_txt ) {
            if ( $p_trasp ) {
                $p_txt = 'inherit';
            } elseif ( '' !== $p_bg_s ) {
                // Un riquadro con lo sfondo scelto e il testo no: il testo del tema se lì si legge, altrimenti il
                // chiaro o lo scuro della Palette. Mai il colore della sezione: su una sezione scura sarebbe chiaro su chiaro.
                $p_txt = Olobuild_Tile_Utils::testo_su_fondo( $p_bg_s, 'var(--olo-color-text, #374151)' ) ?: 'var(--olo-color-text, #374151)';
            } else {
                $p_txt = 'var(--olo-color-text, #374151)';
            }
        }

        // Bordo del riepilogo: quello scelto (senza colore, il colore dei bordi del tema; lati a zero = nessun bordo).
        // Il predefinito è 1 px senza colore: scegliere solo il colore non toglie il bordo.
        $bordo = 'border:1px solid var(--olo-color-border, #e5e7eb);';
        $b     = $s['bordo_pannello'] ?? null;
        if ( is_array( $b ) && Olobuild_Tile_Utils::border_is_set( $b ) ) {
            if ( '' === self::testo( $b['color'] ?? '' ) ) {
                $b['color'] = 'var(--olo-color-border, #e5e7eb)';
            }
            $bordo = Olobuild_Tile_Utils::border_css( $b ) ?: 'border:0;';
        } elseif ( is_array( $b ) && isset( $b['top'], $b['right'], $b['bottom'], $b['left'] ) ) {
            $bordo = 'border:0;';
        } elseif ( is_string( $b ) && '' !== trim( $b ) ) {
            $c_b = Olobuild_Tile_Utils::border_color( $b );
            if ( '' !== $c_b ) {
                $bordo = 'border:1px solid ' . $c_b . ';';
            }
        }

        $r_giorno = Olobuild_Tile_Utils::border_radius( $s['raggio_giorno'] ) ?: '0';
        $r_pann   = Olobuild_Tile_Utils::border_radius( $s['raggio_pannello'] ) ?: '0';
        $r_cam    = min( 6, (int) round( Olobuild_Tile_Utils::radius_int( $s['raggio_giorno'] ) / 3 ) );   // campioni della legenda: lo stesso raggio, in piccolo
        $r_campi  = min( 12, Olobuild_Tile_Utils::radius_int( $s['raggio_pannello'] ) );                  // riquadro di date e ospiti dentro il pannello
        $pad      = Olobuild_Tile_Utils::sides_css( Olobuild_Tile_Utils::spacing_sides( $s['padding_pannello'] ?? null, [], [ 28, 28, 28, 28 ] ) );
        // L'ombra: le chiavi passano ripulite (un valore strano non deve rompere il CSS né la resa).
        $om_k     = self::testo( $s['ombra'] );
        $om_s     = [
            'ombra'        => in_array( $om_k, [ 'none', 'sm', 'md', 'lg', 'xl', 'custom' ], true ) ? $om_k : 'none',
            'ombra_h'      => (int) self::numero( $s['ombra_h'], -200, 200 ),
            'ombra_v'      => (int) self::numero( $s['ombra_v'], -200, 200 ),
            'ombra_blur'   => (int) self::numero( $s['ombra_blur'], 0, 400 ),
            'ombra_spread' => (int) self::numero( $s['ombra_spread'], -200, 200 ),
            'ombra_color'  => $this->safe_color_css( self::testo( $s['ombra_color'] ) ) ?: 'rgba(0,0,0,0.15)',
            'ombra_inset'  => ! empty( $s['ombra_inset'] ),
        ];
        $ombra    = $p_trasp ? 'none' : Olobuild_Tile_Utils::shadow_value( $om_s, 'ombra' );
        $gap      = (int) self::numero( $s['gap'], 0, 160 );
        $lato     = 'sotto' !== self::testo( $s['disposizione'] );
        $centro   = 'center' === self::testo( $s['allineamento'] );
        $f_tot    = $this->resolve_font_family( self::testo( $s['totale_font_family'] ) );

        $classi = 'osg osg--' . ( $lato ? 'lato' : 'sotto' ) . ( $centro ? ' osg--centro' : '' );

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS da colori passati da safe_color_css() e dagli helper di Olobuild_Tile_Utils, numeri forzati, valori da elenchi chiusi e selettori generati qui; markup con esc_html/esc_attr; icone da render_icon_html()
        echo '<style>';
        echo "{$sel}{--osg-tenue:{$c_tenue};--osg-lib:{$c_lib};--osg-occ:{$c_occ};--osg-righe:color-mix(in srgb, currentColor 17%, transparent);--osg-sc:{$c_scelto};--osg-sct:{$c_sc_t};--osg-int:{$c_int};--osg-oggi:{$c_oggi};--osg-rg:{$r_giorno};--osg-rp:{$r_pann};--osg-gap:{$gap}px;--osg-nm:{$nm};--osg-mx:calc(var(--osg-nm) * 25rem + (var(--osg-nm) - 1) * 2rem);--osg-linea:color-mix(in srgb, currentColor 14%, transparent)"
            . ( '' !== $f_tot ? ";--osg-ft:{$f_tot}" : '' ) . ( '' !== $c_testo ? ";color:{$c_testo}" : '' ) . '}';
        echo "{$sel} [hidden]{display:none!important}";
        echo "{$sel} .osg-sr{position:absolute!important;width:1px;height:1px;margin:-1px;padding:0;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}";

        // Disposizione: calendario e riepilogo affiancati finché ci stanno (44rem, due mesi con tessere da 44 px, + 21rem),
        // poi il riepilogo va sotto, largo quanto i mesi.
        echo "{$sel} .osg-corpo{display:flex;flex-wrap:wrap;align-items:flex-start;gap:var(--osg-gap)}";
        if ( $lato ) {
            echo "{$sel} .osg-corpo{max-width:calc(var(--osg-mx) + var(--osg-gap) + 21rem)}";
            echo "{$sel} .osg-cal{flex:999 1 min(44rem, var(--osg-mx));min-width:0}";
            echo "{$sel} .osg-pannello{flex:1 1 21rem;max-width:var(--osg-mx);position:sticky;top:calc(var(--olo-sticky-top-offset, 0px) + 1.5rem)}";
        } else {
            echo "{$sel} .osg-corpo{max-width:var(--osg-mx)}";
            echo "{$sel} .osg-cal,{$sel} .osg-pannello{flex:1 1 100%;min-width:0}";
        }
        if ( $centro ) {
            echo "{$sel} .osg-corpo,{$sel} .osg-mesi,{$sel} .osg-pannello{margin-left:auto;margin-right:auto}";
            echo "{$sel} .osg-stato{text-align:center}";
            echo "{$sel} .osg-legenda{justify-content:center}";
        }

        // I mesi: affiancati finché ognuno ha almeno 18rem, poi uno sotto l'altro; sul telefono uno solo.
        echo "{$sel} .osg-mesi{position:relative;max-width:var(--osg-mx)}";
        echo "{$sel} .osg-fogli{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,18rem),1fr));column-gap:2rem;row-gap:1.75rem;min-height:18rem}";
        echo "{$sel} .osg-mese{min-width:0}";
        echo "{$sel} .osg-mese-testa{display:flex;align-items:center;justify-content:center;min-height:2.5rem;margin:0 0 .75rem;padding:0 2.75rem}";
        echo "{$sel} .osg-mese-titolo{margin:0;line-height:1.2;text-align:center;text-wrap:balance}";
        echo "{$sel} .osg-nav,{$sel} .osg-nav:hover{background:transparent}";
        echo "{$sel} .osg-nav{position:absolute;top:0;z-index:1;box-sizing:border-box;display:inline-flex;align-items:center;justify-content:center;width:2.5rem;height:2.5rem;min-height:0;margin:0;padding:0;border:1px solid var(--osg-linea);border-radius:50%;color:inherit;font:inherit;line-height:1;cursor:pointer;-webkit-appearance:none;appearance:none;box-shadow:none;transition:background-color .15s ease,border-color .15s ease}";
        echo "{$sel} .osg-nav::before,{$sel} .osg-btn::before{content:'';position:absolute;inset:-.25rem;border-radius:50%}"; // area di tocco da 44 px e più
        echo "{$sel} .osg-prec{left:0}";
        echo "{$sel} .osg-succ{right:0}";
        echo "{$sel} .osg-nav:hover{background:color-mix(in srgb, currentColor 7%, transparent);border-color:color-mix(in srgb, currentColor 30%, transparent)}";
        echo "{$sel} .osg-nav[aria-disabled=\"true\"]{opacity:.32;cursor:default;background:transparent;border-color:var(--osg-linea)}";
        echo "{$sel} .osg-nav:focus{outline:none}";
        echo "{$sel} .osg-nav:focus-visible{outline:2px solid var(--osg-sc);outline-offset:2px}";
        echo "{$sel} .osg-nav svg,{$sel} .osg-nav .olo-lucide-icon{width:1.1rem;height:1.1rem}";

        // La griglia dei giorni: una tessera quadrata per giorno, il numero e il prezzo della notte.
        echo "{$sel} .osg-griglia{width:100%;margin:0;border:0;border-collapse:collapse;border-spacing:0;table-layout:fixed;background:transparent}";
        echo "{$sel} .osg-griglia tr{background:transparent;border:0}";
        echo "{$sel} .osg-griglia th{padding:0 0 .5rem;border:0;background:transparent;color:var(--osg-tenue);text-align:center;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:clip}";
        echo "{$sel} .osg-griglia td{padding:.125rem;border:0;background:transparent;text-align:center;vertical-align:middle;outline:none;-webkit-tap-highlight-color:transparent}";
        echo "{$sel} .osg-griglia td[data-n]{cursor:pointer}";
        echo "{$sel} .osg-griglia td[aria-disabled=\"true\"]{cursor:default}";
        echo "{$sel} .osg-g{position:relative;box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.125rem;width:100%;aspect-ratio:1/1;border-radius:var(--osg-rg);background-color:var(--osg-lib);line-height:1.1;transition:background-color .15s ease,color .15s ease,box-shadow .15s ease,opacity .15s ease}";
        echo "{$sel} .osg-n{font-variant-numeric:tabular-nums}";
        echo "{$sel} .osg-pr{max-width:calc(100% - .25rem);overflow:hidden;color:var(--osg-tenue);font-variant-numeric:tabular-nums;white-space:nowrap;text-overflow:clip}";
        echo "{$sel} .no-pr .osg-pr{visibility:hidden}";
        echo "{$sel}.is-stretto .osg-pv{display:none}"; // tessere strette: il prezzo senza il simbolo della valuta (lo dice il riepilogo)
        echo "{$sel} .is-oggi .osg-g{box-shadow:inset 0 0 0 1.5px var(--osg-oggi)}";
        echo "{$sel} td.is-libero:hover .osg-g,{$sel} td.is-uscita:hover .osg-g{box-shadow:inset 0 0 0 1.5px color-mix(in srgb, currentColor 42%, transparent)}";
        echo "{$sel} .is-passato .osg-g,{$sel} .is-fuori .osg-g{background:transparent;opacity:.36}";
        echo "{$sel} .is-occupato .osg-g,{$sel} .is-uscita .osg-g{background-color:var(--osg-occ);background-image:repeating-linear-gradient(135deg,var(--osg-righe) 0 1px,transparent 1px 6px)}";
        echo "{$sel} .is-occupato .osg-n{text-decoration:line-through;text-decoration-thickness:1px;color:var(--osg-tenue)}";
        echo "{$sel} .is-uscita .osg-g{background-color:var(--osg-lib)}";
        echo "{$sel} .is-corto .osg-n{color:var(--osg-tenue)}";
        echo "{$sel} .is-oltre .osg-g{opacity:.4}";
        echo "{$sel} .is-dentro .osg-g,{$sel} .is-anteprima .osg-g{background:var(--osg-int);box-shadow:none}";
        echo "{$sel} td.is-anteprima-fine .osg-g{background:var(--osg-int);box-shadow:inset 0 0 0 2px var(--osg-sc)}";
        echo "{$sel} .is-arrivo .osg-g,{$sel} .is-partenza .osg-g{background:var(--osg-sc);box-shadow:none}";
        echo "{$sel} .is-arrivo .osg-n,{$sel} .is-partenza .osg-n,{$sel} .is-arrivo .osg-pr,{$sel} .is-partenza .osg-pr{color:var(--osg-sct)}";
        echo "{$sel} .is-arrivo .osg-pr{opacity:.85}";
        echo "{$sel} .osg-griglia td:focus-visible .osg-g{outline:2px solid var(--osg-sc);outline-offset:2px}";

        // Stato (inviti e avvisi) e legenda.
        echo "{$sel} .osg-stato{margin:1rem 0 0;min-height:1.5em;line-height:1.45}";
        echo "{$sel} .osg-stato.is-avviso{color:var(--olo-color-danger, #b42318);font-weight:500}";
        echo "{$sel} .osg-conta{color:var(--osg-tenue);font-weight:400}";
        echo "{$sel} .osg-conta:not(:empty)::before{content:'·';margin:0 .45em}";
        echo "{$sel} .osg-legenda{display:flex;flex-wrap:wrap;align-items:center;gap:.5rem 1.25rem;margin:.875rem 0 0;padding:0;list-style:none}";
        echo "{$sel} .osg-legenda li{display:inline-flex;align-items:center;gap:.5rem;margin:0;padding:0;color:var(--osg-tenue)}";
        echo "{$sel} .osg-cam{flex:none;width:1rem;height:1rem;border-radius:{$r_cam}px;background:var(--osg-lib)}";
        echo "{$sel} .osg-cam--occupato{background-color:var(--osg-occ);background-image:repeating-linear-gradient(135deg,var(--osg-righe) 0 1px,transparent 1px 4px);box-shadow:inset 0 0 0 1px var(--osg-linea)}";
        echo "{$sel} .osg-cam--scelto{background:var(--osg-sc)}";

        // Il riepilogo: un riquadro; quando è largo (sotto il calendario) i blocchi vanno su due colonne.
        echo "{$sel} .osg-pannello{box-sizing:border-box;min-width:0;padding:{$pad};border-radius:var(--osg-rp);background:{$p_bg};color:{$p_txt};{$bordo}" . ( 'none' !== $ombra ? "box-shadow:{$ombra}" : '' ) . '}';
        echo "{$sel} .osg-p-dentro{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,17rem),1fr));gap:1.5rem 2.5rem;align-items:start}";
        echo "{$sel} .osg-p-a,{$sel} .osg-p-b{min-width:0;display:flex;flex-direction:column;gap:1rem}";
        echo "{$sel} .osg-da{margin:0;line-height:1.2}";
        echo "{$sel} .osg-da span{color:var(--osg-tenue)}";
        echo "{$sel} .osg-da strong{font-family:var(--osg-ft, inherit);font-size:1.5em;font-weight:600;color:inherit}";
        echo "{$sel} .osg-campi{border:1px solid var(--osg-linea);border-radius:{$r_campi}px;overflow:hidden}";
        echo "{$sel} .osg-date{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr)}";
        echo "{$sel} .osg-data{min-width:0;padding:.75rem 1rem}";
        echo "{$sel} .osg-data+.osg-data{border-left:1px solid var(--osg-linea)}";
        echo "{$sel} .osg-etichetta{display:block;margin:0;color:var(--osg-tenue);line-height:1.3}";
        echo "{$sel} .osg-valore{display:block;margin-top:.25rem;font-weight:500;line-height:1.3;overflow-wrap:anywhere}";
        echo "{$sel} .osg-valore.is-vuoto{font-weight:400;color:var(--osg-tenue)}";
        echo "{$sel} .osg-ospiti{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.625rem 1rem;border-top:1px solid var(--osg-linea)}";
        echo "{$sel} .osg-passo{display:inline-flex;align-items:center;gap:.75rem}";
        echo "{$sel} .osg-btn,{$sel} .osg-btn:hover{background:transparent}";
        echo "{$sel} .osg-btn{position:relative;box-sizing:border-box;display:inline-flex;align-items:center;justify-content:center;width:2.25rem;height:2.25rem;min-height:0;margin:0;padding:0;border:1px solid color-mix(in srgb, currentColor 22%, transparent);border-radius:50%;color:inherit;font:inherit;line-height:1;cursor:pointer;-webkit-appearance:none;appearance:none;box-shadow:none;transition:background-color .15s ease,border-color .15s ease}";
        echo "{$sel} .osg-btn:hover{background:color-mix(in srgb, currentColor 7%, transparent);border-color:color-mix(in srgb, currentColor 40%, transparent)}";
        echo "{$sel} .osg-btn[aria-disabled=\"true\"]{opacity:.32;cursor:default;background:transparent}";
        echo "{$sel} .osg-btn:focus{outline:none}";
        echo "{$sel} .osg-btn:focus-visible{outline:2px solid var(--osg-sc);outline-offset:2px}";
        echo "{$sel} .osg-btn svg,{$sel} .osg-btn .olo-lucide-icon{width:1rem;height:1rem}";
        echo "{$sel} .osg-num{min-width:1.5ch;text-align:center;font-weight:600;font-variant-numeric:tabular-nums}";
        echo "{$sel} .osg-vuoto{margin:0;color:var(--osg-tenue)}";
        echo "{$sel} .osg-conto{min-width:0}";
        echo "{$sel} .osg-righe{list-style:none;margin:0;padding:0}";
        echo "{$sel} .osg-riga{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;margin:0;padding:.375rem 0}";
        echo "{$sel} .osg-voce{display:flex;flex-direction:column;min-width:0}";
        echo "{$sel} .osg-voce-d{font-size:.85em;color:var(--osg-tenue)}";
        echo "{$sel} .osg-importo{white-space:nowrap;font-variant-numeric:tabular-nums}";
        echo "{$sel} .osg-totale{display:flex;align-items:baseline;justify-content:space-between;gap:1rem;margin:.5rem 0 0;padding:.875rem 0 0;border-top:1px solid var(--osg-linea)}";
        echo "{$sel} .osg-cifra{line-height:1.1;font-variant-numeric:tabular-nums;white-space:nowrap}";
        // Il pulsante: lo stile pulsanti del tema (--olo-btn-*), primario e il suo contrasto.
        echo "{$sel} .osg-cta{box-sizing:border-box;display:flex;align-items:center;justify-content:center;gap:.5em;width:100%;min-height:3.25rem;margin:0;padding:var(--olo-btn-padding, .85em 1.5em);border:0;border-radius:var(--olo-btn-border-radius, .5rem);background:var(--olo-color-primary);color:var(--olo-color-primary-contrast, #ffffff);font-size:var(--olo-btn-font-size, 1rem);font-weight:var(--olo-btn-font-weight, 600);line-height:1.2;text-align:center;text-decoration:none;text-transform:var(--olo-btn-text-transform, none);letter-spacing:var(--olo-btn-letter-spacing, normal);cursor:pointer;transition:filter .15s ease,opacity .15s ease}";
        echo "{$sel} .osg-cta:hover,{$sel} .osg-cta:focus{color:var(--olo-color-primary-contrast, #ffffff);text-decoration:none}";
        echo "{$sel} .osg-cta:hover{filter:brightness(var(--olo-btn-hover-brightness, 92%))}";
        echo "{$sel} .osg-cta[aria-disabled=\"true\"]{opacity:.45;cursor:not-allowed;filter:none}";
        echo "{$sel} .osg-cta:focus{outline:none}";
        echo "{$sel} .osg-cta:focus-visible{outline:2px solid var(--olo-color-primary);outline-offset:3px}";
        echo "{$sel} .osg-nota{margin:0;font-size:.875em;line-height:1.5;color:var(--osg-tenue);text-align:center;text-wrap:pretty}";
        echo "{$sel} .osg-azzera,{$sel} .osg-azzera:hover{background:transparent}";
        echo "{$sel} .osg-azzera::before{content:'';position:absolute;inset:-.5rem -.25rem}";
        echo "{$sel} .osg-azzera{position:relative;align-self:center;min-height:0;margin:0;padding:.25rem .5rem;border:0;border-radius:.25rem;color:inherit;font:inherit;font-size:.875em;line-height:1.4;text-decoration:underline;text-underline-offset:.2em;cursor:pointer;-webkit-appearance:none;appearance:none;box-shadow:none}";
        echo "{$sel} .osg-azzera:focus{outline:none}";
        echo "{$sel} .osg-azzera:focus-visible{outline:2px solid var(--osg-sc);outline-offset:2px}";
        if ( $builder ) {
            echo "{$sel} .osg-avviso{margin:0;padding:.75rem 1rem;font-size:14px;line-height:1.45;opacity:.75;border:1px dashed color-mix(in srgb, currentColor 30%, transparent);border-radius:.5rem}";
        }
        // Sul telefono un mese solo (lo script ne costruisce uno; questo vale anche prima che parta).
        echo '@media (max-width:639px){' . "{$sel} .osg-mese+.osg-mese{display:none}" . '}';
        // «Riduci movimento»: le transizioni si spengono, tutto il resto resta.
        echo '@media (prefers-reduced-motion:reduce){' . "{$sel} .osg-g,{$sel} .osg-nav,{$sel} .osg-btn,{$sel} .osg-cta{transition:none}" . '}';
        echo $this->css_testo( $s, 'mese', "{$sel} .osg-mese-titolo" );
        echo $this->css_testo( $s, 'settimana', "{$sel} .osg-griglia th" );
        echo $this->css_testo( $s, 'giorno', "{$sel} .osg-n" );
        echo $this->css_testo( $s, 'prezzo', "{$sel} .osg-pr" );
        // Etichette e testo hanno il colore del riepilogo; la legenda e lo stato stanno sul fondo della sezione: lì
        // vale tutto il resto (famiglia, misura, peso), ma il colore resta quello del calendario.
        echo $this->css_testo( $s, 'etichetta', "{$sel} .osg-etichetta" );
        echo $this->css_testo( array_merge( $s, [ 'etichetta_color' => '' ] ), 'etichetta', "{$sel} .osg-legenda li" );
        echo $this->css_testo( $s, 'testo', "{$sel} .osg-pannello" );
        echo $this->css_testo( array_merge( $s, [ 'testo_color' => '' ] ), 'testo', "{$sel} .osg-stato" );
        echo $this->css_testo( $s, 'totale', "{$sel} .osg-cifra" );
        echo '</style>';

        $cfg = [
            'occ'     => $occupati,
            'st'      => $stagioni,
            'base'    => $base,
            'min'     => $minimo,
            'ospMax'  => $osp_max,
            'osp'     => $osp_ini,
            'pul'     => $pulizie,
            'tassa'   => $tassa,
            'etTassa' => $et_tassa,
            'val'     => $valuta,
            'prezzi'  => $mostra,
            'vis'     => $vis,
            'avanti'  => $avanti,
            'link'    => $link,
            'campi'   => $campi,
            'param'   => ! empty( $s['parametri'] ),
            'tz'      => ( '' !== $tz && ( false !== strpos( $tz, '/' ) || 'UTC' === $tz ) ) ? $tz : '',
            'off'     => $off,
            'builder' => $builder,
            'testi'   => $testi,
            't'       => [
                'n1'       => __( '1 notte', 'olobuilder' ),
                'nN'       => __( '{n} notti', 'olobuilder' ),
                'o1'       => __( '1 ospite', 'olobuilder' ),
                'oN'       => __( '{n} ospiti', 'olobuilder' ),
                'notte'    => __( '{prezzo} a notte', 'olobuilder' ),
                'da'       => __( 'da {prezzo} a notte', 'olobuilder' ),
                'libero'   => __( 'giorno libero', 'olobuilder' ),
                'occupato' => __( 'giorno occupato', 'olobuilder' ),
                'nd'       => __( 'non disponibile', 'olobuilder' ),
                'arrivo'   => __( 'giorno di arrivo', 'olobuilder' ),
                'partenza' => __( 'giorno di partenza', 'olobuilder' ),
                'dentro'   => __( 'notte del soggiorno', 'olobuilder' ),
                'uscita'   => __( 'solo partenza', 'olobuilder' ),
                'pulizie'  => __( 'Pulizie finali', 'olobuilder' ),
                'aggiungi' => $t_agg,
            ],
        ];

        echo '<div id="' . esc_attr( $uid ) . '" class="' . esc_attr( $classi ) . '" data-osg="' . esc_attr( wp_json_encode( $cfg ) ) . '">';
        echo '<div class="osg-corpo">';

        // ── Il calendario (i mesi li costruisce lo script) ──
        echo '<div class="osg-cal" data-olo-interactive>';
        echo '<div class="osg-mesi" data-olo-own-drag>';
        echo '<button type="button" class="osg-nav osg-prec" aria-label="' . esc_attr__( 'Mese precedente', 'olobuilder' ) . '" aria-controls="' . esc_attr( $uid ) . '-m" aria-disabled="true">' . $this->render_icon_html( 'chevron-left', 0.9, 'aria-hidden="true"' ) . '</button>';
        echo '<button type="button" class="osg-nav osg-succ" aria-label="' . esc_attr__( 'Mese successivo', 'olobuilder' ) . '" aria-controls="' . esc_attr( $uid ) . '-m"' . ( $avanti <= $nm ? ' aria-disabled="true"' : '' ) . '>' . $this->render_icon_html( 'chevron-right', 0.9, 'aria-hidden="true"' ) . '</button>';
        echo '<div class="osg-fogli" id="' . esc_attr( $uid ) . '-m"></div>';
        echo '</div>';
        echo '<p class="osg-stato" role="status"><span class="osg-stato-t">' . esc_html( $testi['arrivo'] ) . '</span><span class="osg-conta" aria-hidden="true"></span></p>';
        if ( ! empty( $s['legenda'] ) ) {
            echo '<ul class="osg-legenda" aria-label="' . esc_attr__( 'Legenda', 'olobuilder' ) . '">';
            echo '<li><span class="osg-cam" aria-hidden="true"></span>' . esc_html( $leg_lib ) . '</li>';
            echo '<li><span class="osg-cam osg-cam--occupato" aria-hidden="true"></span>' . esc_html( $leg_occ ) . '</li>';
            echo '<li><span class="osg-cam osg-cam--scelto" aria-hidden="true"></span>' . esc_html( $leg_sc ) . '</li>';
            echo '</ul>';
        }
        echo '<p class="osg-sr osg-annuncio" aria-live="polite"></p>';
        echo '</div>';

        // ── Il riepilogo ──
        echo '<div class="osg-pannello">';
        echo '<div class="osg-p-dentro">';
        echo '<div class="osg-p-a">';
        if ( $con_prezzi && $mostra ) {
            echo '<p class="osg-da" hidden></p>';
        }
        echo '<div class="osg-campi">';
        echo '<div class="osg-date">';
        echo '<div class="osg-data"><span class="osg-etichetta">' . esc_html__( 'Arrivo', 'olobuilder' ) . '</span><span class="osg-valore is-vuoto" data-osg-v="arrivo">' . esc_html( $t_agg ) . '</span></div>';
        echo '<div class="osg-data"><span class="osg-etichetta">' . esc_html__( 'Partenza', 'olobuilder' ) . '</span><span class="osg-valore is-vuoto" data-osg-v="partenza">' . esc_html( $t_agg ) . '</span></div>';
        echo '</div>';
        echo '<div class="osg-ospiti"><span class="osg-etichetta" id="' . esc_attr( $uid ) . '-o">' . esc_html__( 'Ospiti', 'olobuilder' ) . '</span>';
        echo '<div class="osg-passo" role="group" aria-labelledby="' . esc_attr( $uid ) . '-o" data-olo-interactive>';
        echo '<button type="button" class="osg-btn osg-meno" aria-label="' . esc_attr__( 'Togli un ospite', 'olobuilder' ) . '"' . ( $osp_ini <= 1 ? ' aria-disabled="true"' : '' ) . '>' . $this->render_icon_html( 'minus', 0.8, 'aria-hidden="true"' ) . '</button>';
        echo '<output class="osg-num" aria-live="polite"><span class="osg-num-v" aria-hidden="true">' . (int) $osp_ini . '</span><span class="osg-sr osg-num-sr">' . esc_html( $ospiti_n ) . '</span></output>';
        echo '<button type="button" class="osg-btn osg-piu" aria-label="' . esc_attr__( 'Aggiungi un ospite', 'olobuilder' ) . '"' . ( $osp_ini >= $osp_max ? ' aria-disabled="true"' : '' ) . '>' . $this->render_icon_html( 'plus', 0.8, 'aria-hidden="true"' ) . '</button>';
        echo '</div></div>';
        echo '</div>';
        echo '</div>';

        echo '<div class="osg-p-b">';
        if ( $con_prezzi ) {
            echo '<p class="osg-vuoto">' . esc_html( $t_vuoto ) . '</p>';
            echo '<div class="osg-conto" hidden><ul class="osg-righe"></ul><p class="osg-totale"><span class="osg-etichetta">' . esc_html__( 'Totale', 'olobuilder' ) . '</span><span class="osg-cifra" aria-live="polite"></span></p></div>';
        }
        if ( '' !== $link ) {
            // Nel builder il collegamento non si apre (il pulsante non è interattivo: il clic seleziona la tile).
            echo '<a class="osg-cta" role="link" tabindex="0" aria-disabled="true"' . ( $builder ? '' : ' data-olo-interactive' ) . '><span' . ( $builder ? ' data-olo-editable="testo_pulsante"' : '' ) . '>' . esc_html( $testo_p ) . '</span></a>';
        } elseif ( $builder ) {
            echo '<p class="osg-avviso">' . esc_html__( 'Il pulsante della richiesta compare quando scrivi nel Contenuto il collegamento della richiesta.', 'olobuilder' ) . '</p>';
        }
        if ( '' !== $nota ) {
            echo '<p class="osg-nota"' . ( $builder ? ' data-olo-editable="nota"' : '' ) . '>' . esc_html( $nota ) . '</p>';
        }
        echo '<button type="button" class="osg-azzera" hidden data-olo-interactive>' . esc_html( $t_azzera ) . '</button>';
        echo '</div>';
        echo '</div></div>';

        echo '</div></div>';
        echo '<script>' . $this->script() . '[].forEach.call(document.querySelectorAll("[data-osg]"),function(x){window.oloSoggiorni(x);});</script>';
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }

    /**
     * La funzione del calendario. Va in OGNI istanza (definita una volta sola, «if(!window…)»): una sezione caricata
     * dopo (<template> delle sezioni pigre) può eseguire il suo script prima di quello della prima istanza. Niente
     * «&» nello script (wptexturize lo riscrive fra un «<» e un «>»): condizioni annidate e la «&» delle query da
     * String.fromCharCode(38). Ogni istanza si avvia una volta (data-osg-pronto), anche quando lo script rigira
     * (anteprima del builder, sezioni caricate dopo). I giorni sono numeri (giorni dal 1970, in UTC): niente ora legale.
     * L'intervallo scelto («3–8 novembre 2026») viene da formatRangeToParts: in it-IT Chrome dà i giorni a due cifre
     * («03–08 novembre») anche con day:'numeric', e lo zero davanti si toglie dalle sole parti «day».
     */
    private function script() {
        return <<<'JS'
if(!window.oloSoggiorni){window.oloSoggiorni=function(el){
if(!el){return;}
if(el.getAttribute("data-osg-pronto")==="1"){return;}
el.setAttribute("data-osg-pronto","1");
var C={};try{C=JSON.parse(el.getAttribute("data-osg")||"{}");}catch(e){C={};}
var T=C.t||{},X=C.testi||{},G=86400000,uid=el.id||"osg",E=String.fromCharCode(38);
var lingua=String(document.documentElement.lang||"it").replace("_","-");
function fmt(o){try{return new Intl.DateTimeFormat(lingua,o);}catch(e){try{return new Intl.DateTimeFormat("it",o);}catch(e2){return null;}}}
function nf(o){try{return new Intl.NumberFormat(lingua,o);}catch(e){return new Intl.NumberFormat("it",o);}}
var fMese=fmt({month:"long",year:"numeric",timeZone:"UTC"}),fSett=fmt({weekday:"short",timeZone:"UTC"}),fSettL=fmt({weekday:"long",timeZone:"UTC"}),
fLunga=fmt({weekday:"long",day:"numeric",month:"long",year:"numeric",timeZone:"UTC"}),fBreve=fmt({weekday:"short",day:"numeric",month:"short",timeZone:"UTC"}),
fBreveA=fmt({weekday:"short",day:"numeric",month:"short",year:"numeric",timeZone:"UTC"}),fArco=fmt({day:"numeric",month:"long",year:"numeric",timeZone:"UTC"});
var n0=nf({maximumFractionDigits:0}),n2=nf({minimumFractionDigits:2,maximumFractionDigits:2});
function dt(n){return new Date(n*G);}
function iso(n){return dt(n).toISOString().slice(0,10);}
function f(fm,n){if(!fm){return iso(n);}try{return fm.format(dt(n));}catch(e){return iso(n);}}
function daIso(s){var m=/^(\d{4})-(\d{2})-(\d{2})$/.exec(String(s||""));if(!m){return NaN;}var n=Math.round(Date.UTC(+m[1],+m[2]-1,+m[3])/G);if(iso(n)!==m[0]){return NaN;}return n;}
function sett(n){return (dt(n).getUTCDay()+6)%7;}
function meseDi(n){var d=dt(n);return d.getUTCFullYear()*12+d.getUTCMonth();}
function inizio(mi){return Math.round(Date.UTC(Math.floor(mi/12),mi%12,1)/G);}
function prima(s){s=String(s||"");return s.charAt(0).toUpperCase()+s.slice(1);}
function oggi(){
var d=new Date(),o={};
if(C.tz){try{new Intl.DateTimeFormat("en-US",{timeZone:C.tz,year:"numeric",month:"numeric",day:"numeric"}).formatToParts(d).forEach(function(p){o[p.type]=p.value;});if(o.year){return Math.round(Date.UTC(+o.year,+o.month-1,+o.day)/G);}}catch(e){}}
if(typeof C.off==="number"){var s=new Date(d.getTime()+C.off*60000);return Math.round(Date.UTC(s.getUTCFullYear(),s.getUTCMonth(),s.getUTCDate())/G);}
return Math.round(Date.UTC(d.getFullYear(),d.getMonth(),d.getDate())/G);
}
var val=String(C.val||""),davanti=/^en\b/i.test(lingua);
function soldi(v){var s=(Math.round(v*100)%100===0?n0:n2).format(v);if(!val){return s;}return davanti?val+(val.length>1?" ":"")+s:s+" "+val;}
var oggiN=oggi(),primoMin=meseDi(oggiN),avanti=Math.max(1,Math.min(36,parseInt(C.avanti,10)||12)),ultimoMi=primoMin+avanti-1,ultimo=inizio(ultimoMi+1)-1;
var base=Math.max(0,+C.base||0),minG=Math.max(1,parseInt(C.min,10)||1),ospMax=Math.max(1,parseInt(C.ospMax,10)||1),pul=Math.max(0,+C.pul||0),tassa=Math.max(0,+C.tassa||0);
var mostraPrezzi=!!C.prezzi,campi=C.campi||[];
function rel(s){s=String(s||"").trim();if(/^[+-]?\d{1,4}$/.test(s)){return oggiN+parseInt(s,10);}return daIso(s);}
var occ={};
(C.occ||[]).forEach(function(o){
if(!o){return;}var a=rel(o[0]),b=rel(o[1]),x;
if(isNaN(a)){return;}if(isNaN(b)){b=a+1;}if(b<a){x=a;a=b;b=x;}if(b===a){b=a+1;}
a=Math.max(a,oggiN-1);b=Math.min(b,ultimo+1);
for(x=a;x<b;x++){occ[x]=1;}
});
var ST=[];
(C.st||[]).forEach(function(s){if(!s){return;}ST.push({nome:String(s[0]||""),dal:String(s[1]||""),al:String(s[2]||""),p:Math.max(0,+s[3]||0),min:Math.max(0,parseInt(s[4],10)||0)});});
function stagione(n){var k=iso(n).slice(5),i,s;for(i=0;i<ST.length;i++){s=ST[i];if(s.dal<=s.al){if(k>=s.dal){if(k<=s.al){return i;}}}else{if(k>=s.dal){return i;}if(k<=s.al){return i;}}}return -1;}
function prezzo(n){var i=stagione(n);if(i>=0){if(ST[i].p>0){return ST[i].p;}}return base;}
function minimo(n){var i=stagione(n);if(i>=0){if(ST[i].min>0){return ST[i].min;}}return minG;}
var conPrezzi=base>0;ST.forEach(function(s){if(s.p>0){conPrezzi=true;}});
function maxPart(a){for(var n=a+1;n<ultimo;n++){if(occ[n]){return n;}}return ultimo;}
function arrivoOk(n){if(n<oggiN){return false;}if(n>=ultimo){return false;}if(occ[n]){return false;}return maxPart(n)-n>=minimo(n);}
function notti(k){return k===1?String(T.n1||"1"):String(T.nN||"{n}").replace("{n}",k);}
function ospitiT(k){return k===1?String(T.o1||"1"):String(T.oN||"{n}").replace("{n}",k);}
function minimoTesto(k){return String(X.minimo||"").replace(/\{n\}/g,k);}
function q(s){return el.querySelector(s);}
var fogli=q(".osg-fogli"),prec=q(".osg-prec"),succ=q(".osg-succ"),statoEl=q(".osg-stato-t"),contaEl=q(".osg-conta"),annuncio=q(".osg-annuncio"),
pDa=q(".osg-da"),vArr=q('[data-osg-v="arrivo"]'),vPar=q('[data-osg-v="partenza"]'),vuoto=q(".osg-vuoto"),conto=q(".osg-conto"),righe=q(".osg-righe"),cifra=q(".osg-cifra"),
cta=q(".osg-cta"),azz=q(".osg-azzera"),meno=q(".osg-meno"),piu=q(".osg-piu"),numV=q(".osg-num-v"),numSr=q(".osg-num-sr");
if(!fogli){return;}
var mq=null;try{mq=window.matchMedia("(max-width: 639px)");}catch(e){mq=null;}
var visC=Math.max(1,Math.min(2,parseInt(C.vis,10)||1));
function quanti(){var v=visC;if(mq){if(mq.matches){v=1;}}return Math.min(v,avanti);}
var vis=quanti(),S={a:null,p:null,h:null,osp:Math.max(1,Math.min(ospMax,parseInt(C.osp,10)||1)),primo:primoMin,foc:null},celle={};
var settN=[],settL=[];(function(){for(var i=0;i<7;i++){settN.push(f(fSett,4+i));settL.push(f(fSettL,4+i));}})();
function crea(tag,cls){var x=document.createElement(tag);if(cls){x.className=cls;}return x;}
function costruisci(){
vis=quanti();
if(S.primo>ultimoMi-vis+1){S.primo=ultimoMi-vis+1;}
if(S.primo<primoMin){S.primo=primoMin;}
el.style.setProperty("--osg-nm",vis);
fogli.textContent="";celle={};
var righeN=4,k,m0;
for(k=0;k<vis;k++){m0=inizio(S.primo+k);righeN=Math.max(righeN,Math.ceil((sett(m0)+inizio(S.primo+k+1)-m0)/7));}
for(k=0;k<vis;k++){
var mi=S.primo+k,d0=inizio(mi),dn=inizio(mi+1),mese=crea("div","osg-mese"),testa=crea("div","osg-mese-testa"),tit=crea("div","osg-mese-titolo");
tit.id=uid+"-t"+k;tit.textContent=prima(f(fMese,d0));testa.appendChild(tit);mese.appendChild(testa);
var tab=crea("table","osg-griglia"),th=crea("thead"),tr=crea("tr"),tb=crea("tbody"),i,c,g=d0-sett(d0),r;
tab.setAttribute("role","grid");tab.setAttribute("aria-labelledby",tit.id);tab.setAttribute("aria-multiselectable","true");
for(i=0;i<7;i++){c=crea("th");c.setAttribute("scope","col");c.setAttribute("abbr",settL[i]);c.textContent=settN[i];tr.appendChild(c);}
th.appendChild(tr);tab.appendChild(th);
for(r=0;r<righeN;r++){
tr=crea("tr");
for(i=0;i<7;i++){
c=crea("td");
if(g>=d0){if(g<dn){
c.setAttribute("role","gridcell");c.setAttribute("data-n",g);c.tabIndex=-1;
var t=crea("span","osg-g"),nu=crea("span","osg-n");t.setAttribute("aria-hidden","true");nu.textContent=dt(g).getUTCDate();t.appendChild(nu);
if(conPrezzi){if(mostraPrezzi){var pz=prezzo(g),pr=crea("span","osg-pr");if(pz>0){cifraCella(pr,pz);}t.appendChild(pr);}}
c.appendChild(t);celle[g]=c;
}}
tr.appendChild(c);g++;
}
tb.appendChild(tr);
}
tab.appendChild(tb);mese.appendChild(tab);fogli.appendChild(mese);
}
dipingi();naviga();stretto();
}
function cifraCella(x,v){
var s=(Math.round(v*100)%100===0?n0:n2).format(v),u;
if(!val){x.textContent=s;return;}
u=crea("span","osg-pv");
if(davanti){u.textContent=val+(val.length>1?" ":"");x.appendChild(u);x.appendChild(document.createTextNode(s));}
else{u.textContent=" "+val;x.appendChild(document.createTextNode(s));x.appendChild(u);}
}
function stretto(){
el.classList.remove("is-stretto");
var p=fogli.querySelectorAll(".osg-pr"),i;
for(i=0;i<p.length;i++){if(p[i].scrollWidth>p[i].clientWidth){el.classList.add("is-stretto");return;}}
}
function stato(n,mp,mn,ant){
var r={c:[],l:"",s:false,d:false,pr:false,t:""},a=S.a,p=S.p,occupato=occ[n]===1;
if(n===oggiN){r.c.push("is-oggi");}
if(n<oggiN){r.c.push("is-passato");r.d=true;r.l=T.nd;return r;}
if(n>ultimo){r.c.push("is-fuori");r.d=true;r.l=T.nd;return r;}
if(a!==null){
if(n===a){r.c.push("is-arrivo");r.s=true;r.l=T.arrivo;r.pr=true;return r;}
if(p!==null){
if(n===p){r.c.push("is-partenza");r.s=true;r.l=T.partenza;return r;}
if(n>a){if(n<p){r.c.push("is-dentro");r.s=true;r.l=T.dentro;r.pr=true;return r;}}
}else if(n>a){
if(n>mp){r.c.push("is-oltre");if(occupato){r.c.push("is-occupato");}r.l=occupato?T.occupato:T.nd;r.d=true;return r;}
if(n-a<mn){r.c.push("is-libero");r.c.push("is-corto");r.l=T.libero;r.pr=true;r.t=minimoTesto(mn);if(ant){if(n<S.h){r.c.push("is-anteprima");}}return r;}
if(occupato){r.c.push("is-uscita");r.l=T.uscita;}else{r.c.push("is-libero");r.l=T.libero;r.pr=true;}
if(ant){if(n<S.h){r.c.push("is-anteprima");}else if(n===S.h){r.c.push("is-anteprima-fine");}}
return r;
}
}
if(occupato){r.c.push("is-occupato");r.l=T.occupato;r.d=true;return r;}
r.c.push("is-libero");r.l=T.libero;r.pr=true;
if(!arrivoOk(n)){r.c.push("is-corto");r.t=minimoTesto(minimo(n));}
return r;
}
function fuoco(){
if(S.foc!==null){if(celle[S.foc]){return S.foc;}}
if(S.a!==null){if(celle[S.a]){return S.a;}}
if(celle[oggiN]){return oggiN;}
var ks=Object.keys(celle),i,n;
for(i=0;i<ks.length;i++){n=+ks[i];if(n>=oggiN){if(!occ[n]){return n;}}}
return ks.length?+ks[0]:null;
}
function dipingi(){
var mp=0,mn=0,ant=false,h=S.h;
if(S.a!==null){if(S.p===null){mp=maxPart(S.a);mn=minimo(S.a);if(h!==null){if(h>S.a){if(h<=mp){if(h-S.a>=mn){ant=true;}}}}}}
var fo=fuoco();
Object.keys(celle).forEach(function(k){
var n=+k,td=celle[k],r=stato(n,mp,mn,ant),pz=prezzo(n),l=prima(f(fLunga,n))+", "+r.l,mostra=false;
if(r.pr){if(conPrezzi){if(pz>0){mostra=true;l+=", "+String(T.notte||"{prezzo}").replace("{prezzo}",soldi(pz));}}}
if(!mostra){r.c.push("no-pr");}
if(r.t){l+=". "+r.t;}
td.className=r.c.join(" ");
td.setAttribute("aria-label",l);
td.setAttribute("aria-selected",r.s?"true":"false");
if(r.d){td.setAttribute("aria-disabled","true");}else{td.removeAttribute("aria-disabled");}
if(r.t){td.setAttribute("title",r.t);}else{td.removeAttribute("title");}
if(n===oggiN){td.setAttribute("aria-current","date");}
td.tabIndex=n===fo?0:-1;
});
S.foc=fo;
}
function naviga(){
if(prec){prec.setAttribute("aria-disabled",S.primo<=primoMin?"true":"false");}
if(succ){succ.setAttribute("aria-disabled",S.primo+vis-1>=ultimoMi?"true":"false");}
}
function sposta(d){
var nuovo=Math.max(primoMin,Math.min(ultimoMi-vis+1,S.primo+d));
if(nuovo===S.primo){return false;}
S.primo=nuovo;S.foc=null;costruisci();
if(annuncio){var t=[],k;for(k=0;k<vis;k++){t.push(prima(f(fMese,inizio(S.primo+k))));}annuncio.textContent=t.join(", ");}
return true;
}
function vaiA(n){
var lo=inizio(primoMin),mi;
if(n<lo){n=lo;}if(n>ultimo){n=ultimo;}
mi=meseDi(n);
if(mi<S.primo){S.primo=mi;S.foc=n;costruisci();}else if(mi>S.primo+vis-1){S.primo=mi-vis+1;S.foc=n;costruisci();}
if(S.a!==null){if(S.p===null){S.h=n;}}
S.foc=n;dipingi();conta();
var td=celle[n];if(td){try{td.focus();}catch(e){}}
}
function avvisa(t,err){if(!statoEl){return;}statoEl.textContent=t||"";var p=statoEl.parentNode;if(p){if(err){p.classList.add("is-avviso");}else{p.classList.remove("is-avviso");}}}
function conta(){
if(!contaEl){return;}var t="";
if(S.a!==null){if(S.p===null){if(S.h!==null){if(S.h>S.a){if(S.h<=maxPart(S.a)){t=notti(S.h-S.a);}}}}}
contaEl.textContent=t;
}
function arco(a,p){
if(fArco){
if(fArco.formatRangeToParts){try{return fArco.formatRangeToParts(dt(a),dt(p)).map(function(x){return x.type==="day"?String(x.value).replace(/^0(?=\d)/,""):x.value;}).join("");}catch(e){}}
if(fArco.formatRange){try{return String(fArco.formatRange(dt(a),dt(p))).replace(/(^|[^\d])0(\d)(?!\d)/g,"$1$2");}catch(e){}}
}
return f(fArco,a)+" – "+f(fArco,p);
}
function dataBreve(n){return prima(f(dt(n).getUTCFullYear()===dt(oggiN).getUTCFullYear()?fBreve:fBreveA,n));}
function fatto(){if(S.a===null){return false;}return S.p!==null;}
function riga(t,d,v){
if(!righe){return;}
var li=crea("li","osg-riga"),s=crea("span","osg-voce"),m=crea("span","osg-voce-t"),im=crea("span","osg-importo"),x;
m.textContent=t;s.appendChild(m);if(d){x=crea("span","osg-voce-d");x.textContent=d;s.appendChild(x);}
im.textContent=v;li.appendChild(s);li.appendChild(im);righe.appendChild(li);
}
function collegamento(tot){
var u=String(C.link||"");if(!u){return "";}
var v={arrivo:iso(S.a),partenza:iso(S.p),notti:String(S.p-S.a),ospiti:String(S.osp),totale:tot===null?"":String(tot)};
u=u.replace(/\{(arrivo|partenza|notti|ospiti|totale)\}|%7B(arrivo|partenza|notti|ospiti|totale)%7D/gi,function(m0,x,y){return encodeURIComponent(v[String(x||y).toLowerCase()]);});
u=u.replace(/ /g,"%20");
var pz=u.split("?");if(pz.length>2){u=pz.shift()+"?"+pz.join(E);}
var sch=/^([a-z][a-z0-9+.\-]*):/i.exec(u),pr;
if(sch){pr=sch[1].toLowerCase();if(pr!=="http"){if(pr!=="https"){if(pr!=="mailto"){if(pr!=="tel"){return "";}}}}}
return u;
}
function pannello(){
var ok=fatto(),tot=0,valido=ok,gr=[],ix={},n,pz,si,ch,k;
if(vArr){vArr.textContent=S.a!==null?dataBreve(S.a):String(T.aggiungi||"");vArr.classList.toggle("is-vuoto",S.a===null);}
if(vPar){vPar.textContent=S.p!==null?dataBreve(S.p):String(T.aggiungi||"");vPar.classList.toggle("is-vuoto",S.p===null);}
if(righe){righe.textContent="";}
if(ok){
if(!conPrezzi){valido=false;}
for(n=S.a;n<S.p;n++){
pz=prezzo(n);if(!(pz>0)){valido=false;}
si=stagione(n);if(si>=0){if(!(ST[si].p>0)){si=-1;}}
ch=si+"|"+pz;if(ix[ch]===undefined){ix[ch]=gr.length;gr.push({si:si,p:pz,k:0});}gr[ix[ch]].k++;
}
if(valido){
k=S.p-S.a;
gr.forEach(function(x){var sub=x.k*x.p;tot+=sub;riga(notti(x.k)+" × "+soldi(x.p),x.si>=0?ST[x.si].nome:"",soldi(sub));});
if(pul>0){tot+=pul;riga(String(T.pulizie||""),"",soldi(pul));}
if(tassa>0){var tx=tassa*S.osp*k;tot+=tx;riga(String(C.etTassa||""),ospitiT(S.osp)+" × "+notti(k)+" × "+soldi(tassa),soldi(tx));}
tot=Math.round(tot*100)/100;
}
}
if(vuoto){vuoto.hidden=ok;}
if(conto){conto.hidden=!valido;}
if(cifra){cifra.textContent=valido?soldi(tot):"";}
if(azz){azz.hidden=S.a===null;}
if(cta){
var u=ok?collegamento(valido?tot:null):"";
if(u){cta.setAttribute("href",u);cta.removeAttribute("aria-disabled");}else{cta.removeAttribute("href");cta.setAttribute("aria-disabled","true");}
}
}
function evento(x){["input","change"].forEach(function(t){var e;try{e=new Event(t,{bubbles:true});}catch(z){e=document.createEvent("Event");e.initEvent(t,true,true);}x.dispatchEvent(e);});}
function nodi(nome){if(!nome){return [];}try{return [].slice.call(document.querySelectorAll('[name="fields['+nome+']"],[name="'+nome+'"]'));}catch(e){return [];}}
function metti(x,vIso,vTesto){
var t=String(x.type||"").toLowerCase(),tag=String(x.tagName||"").toLowerCase(),i,o,trovato=null;
if(el.contains(x)){return;}
if(["checkbox","file","button","submit","reset","image","datetime-local","month","week","time","range","color"].indexOf(t)>=0){return;}
if(t==="radio"){if(String(x.value)!==String(vIso)){return;}x.checked=true;evento(x);return;}
if(tag==="select"){
for(i=0;i<x.options.length;i++){o=x.options[i];if(String(o.value)===String(vIso)){trovato=o;break;}}
if(!trovato){for(i=0;i<x.options.length;i++){o=x.options[i];if(parseInt(o.value||o.text,10)===parseInt(vIso,10)){trovato=o;break;}}}
if(!trovato){return;}x.value=trovato.value;evento(x);return;
}
x.value=t==="date"?vIso:(t==="number"?vIso:vTesto);evento(x);
}
function modulo(){
if(!fatto()){return;}
var a=S.a,p=S.p;
nodi(campi[0]).forEach(function(x){metti(x,iso(a),prima(f(fLunga,a)));});
nodi(campi[1]).forEach(function(x){metti(x,iso(p),prima(f(fLunga,p)));});
nodi(campi[2]).forEach(function(x){metti(x,String(S.osp),String(S.osp));});
}
function completa(scrivi){
avvisa(notti(S.p-S.a)+" · "+arco(S.a,S.p),false);pannello();if(scrivi){modulo();}
}
function azzera(rimetti){
S.a=null;S.p=null;S.h=null;dipingi();conta();avvisa(X.arrivo,false);pannello();
if(rimetti){var td=celle[S.foc];if(td){try{td.focus();}catch(e){}}}
}
function scegli(n){
if(n<oggiN){return;}if(n>ultimo){return;}
var a=S.a,mp,mn;
if(a!==null){if(S.p===null){
if(n===a){azzera(false);return;}
if(n>a){
mp=maxPart(a);mn=minimo(a);
if(n>mp){avvisa(X.occupato,true);return;}
if(n-a<mn){avvisa(minimoTesto(mn),true);return;}
S.p=n;S.h=null;dipingi();conta();completa(true);return;
}
}}
if(occ[n]){avvisa(X.occupato,true);return;}
if(!arrivoOk(n)){avvisa(minimoTesto(minimo(n)),true);return;}
S.a=n;S.p=null;S.h=null;dipingi();conta();avvisa(X.partenza,false);pannello();
}
function ospiti(){
if(numV){numV.textContent=String(S.osp);}
if(numSr){numSr.textContent=ospitiT(S.osp);}
if(meno){meno.setAttribute("aria-disabled",S.osp<=1?"true":"false");}
if(piu){piu.setAttribute("aria-disabled",S.osp>=ospMax?"true":"false");}
}
fogli.addEventListener("click",function(e){var td=e.target.closest?e.target.closest("td[data-n]"):null;if(!td){return;}var n=+td.getAttribute("data-n");S.foc=n;scegli(n);});
fogli.addEventListener("pointerover",function(e){
if(S.a===null){return;}if(S.p!==null){return;}
var td=e.target.closest?e.target.closest("td[data-n]"):null;if(!td){return;}
var n=+td.getAttribute("data-n");if(n!==S.h){S.h=n;dipingi();conta();}
});
fogli.addEventListener("pointerleave",function(){if(S.h!==null){S.h=null;dipingi();conta();}});
fogli.addEventListener("keydown",function(e){
var td=e.target.closest?e.target.closest("td[data-n]"):null;if(!td){return;}
var n=+td.getAttribute("data-n"),k=e.key,t=null,d,mi,dd;
if(k==="ArrowLeft"){t=n-1;}else if(k==="ArrowRight"){t=n+1;}else if(k==="ArrowUp"){t=n-7;}else if(k==="ArrowDown"){t=n+7;}
else if(k==="Home"){t=n-sett(n);}else if(k==="End"){t=n+6-sett(n);}
else if(k==="PageUp"||k==="PageDown"){d=dt(n);mi=meseDi(n)+(k==="PageUp"?-1:1);dd=inizio(mi+1)-inizio(mi);t=inizio(mi)+Math.min(d.getUTCDate(),dd)-1;}
else if(k==="Enter"||k===" "||k==="Spacebar"){e.preventDefault();e.stopPropagation();S.foc=n;scegli(n);return;}
else if(k==="Escape"||k==="Esc"){if(S.a!==null){e.preventDefault();e.stopPropagation();azzera(true);}return;}
else{return;}
e.preventDefault();e.stopPropagation();vaiA(t);
});
if(prec){prec.addEventListener("click",function(){sposta(-1);});}
if(succ){succ.addEventListener("click",function(){sposta(1);});}
if(meno){meno.addEventListener("click",function(){if(S.osp>1){S.osp--;ospiti();pannello();modulo();}});}
if(piu){piu.addEventListener("click",function(){if(S.osp<ospMax){S.osp++;ospiti();pannello();modulo();}});}
if(azz){azz.addEventListener("click",function(){azzera(true);});}
if(cta){
cta.addEventListener("click",function(e){
if(cta.getAttribute("aria-disabled")!=="true"){return;}
e.preventDefault();avvisa(S.a===null?X.arrivo:X.partenza,false);
var td=celle[S.foc];if(td){try{td.focus();}catch(x){}}
});
cta.addEventListener("keydown",function(e){if(e.key===" "){e.preventDefault();cta.click();}else if(e.key==="Enter"){if(!cta.getAttribute("href")){e.preventDefault();cta.click();}}});
}
if(window.ResizeObserver){try{new ResizeObserver(function(){stretto();}).observe(fogli);}catch(e){}}else{window.addEventListener("resize",stretto);}
if(mq){var cambia=function(){if(quanti()!==vis){costruisci();}};if(mq.addEventListener){mq.addEventListener("change",cambia);}else if(mq.addListener){mq.addListener(cambia);}}
if(pDa){if(conPrezzi){
var pos=[],mini,diversi=base<=0,tpl,parti,b;
if(base>0){pos.push(base);}ST.forEach(function(s){if(s.p>0){pos.push(s.p);}});
mini=Math.min.apply(null,pos);pos.forEach(function(x){if(x!==mini){diversi=true;}});
tpl=String((diversi?T.da:T.notte)||"{prezzo}");parti=tpl.split("{prezzo}");
pDa.textContent="";
if(parti[0]){b=crea("span");b.textContent=parti[0];pDa.appendChild(b);}
b=crea("strong");b.textContent=soldi(mini);pDa.appendChild(b);
if(parti.length>1){if(parti[1]){b=crea("span");b.textContent=parti[1];pDa.appendChild(b);}}
pDa.hidden=false;
}}
if(C.param){
var qs=null;try{qs=new URLSearchParams(window.location.search);}catch(e){qs=null;}
if(qs){
var po=parseInt(qs.get("ospiti")||"",10),pa=daIso(qs.get("arrivo")||""),pp=daIso(qs.get("partenza")||"");
if(po>=1){S.osp=Math.min(ospMax,po);}
if(!isNaN(pa)){if(arrivoOk(pa)){
S.a=pa;S.primo=Math.min(meseDi(pa),ultimoMi);
if(!isNaN(pp)){if(pp>pa){if(pp<=maxPart(pa)){if(pp-pa>=minimo(pa)){S.p=pp;}}}}
}}
}
}
costruisci();ospiti();
if(S.p!==null){completa(true);}else if(S.a!==null){avvisa(X.partenza,false);pannello();}else{avvisa(X.arrivo,false);pannello();}
};}
JS;
    }
}
