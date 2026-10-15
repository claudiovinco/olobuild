<?php
/**
 * Tile Lucchetto — un lucchetto a combinazione da aprire risolvendo un enigma: rivela un messaggio, un codice da
 * copiare e un pulsante.
 *
 * Le ruote (da 3 a 6, quanti i simboli della combinazione) sono spinbutton: frecce sopra e sotto, tasti freccia,
 * Pagina su/giù, Inizio/Fine, destra e sinistra per cambiare ruota, Invio per aprire; scrivere un simbolo lo imposta e
 * passa alla ruota dopo. La rotellina gira la ruota solo se il puntatore ci è arrivato muovendosi (non se la pagina le
 * scorre sotto), e la ruota si trascina in verticale col dito e col mouse. I simboli scorrono come un rullo (striscia
 * ripetuta, translateY con una transizione breve; subito con «riduci movimento») e si vede un accenno di quelli sopra e
 * sotto. Il lucchetto è disegnato (corpo in CSS, gancio in SVG: ottone, acciaio o minimal) o sostituito da un'icona.
 *
 * La combinazione NON è nell'HTML: c'è l'impronta FNV-1a a 32 bit della combinazione normalizzata (maiuscole, simboli
 * uniti con «|») seguita da un sale casuale per istanza; lo script calcola quella del tentativo allo stesso modo. Anche
 * il premio (messaggio, codice, pulsante) è cifrato con la combinazione (XOR con una sequenza FNV-1a che parte da
 * FNV-1a(combinazione#chiave della tile)): nel sorgente non si legge, e una combinazione che per caso desse la stessa
 * impronta non lo decifra. «Ricorda» salva nel browser quella seconda impronta: basta a riaprire, non è la combinazione.
 * Sbagliata: scossa e messaggio in aria-live (dopo N errori si apre l'indizio); giusta: il gancio si alza e ruota, le
 * ruote si bloccano (aria-disabled) e il fuoco va al premio. Le ruote si stringono da sole dove lo spazio non basta.
 * Nata per i temi delle escape room virtuali (15 ott 2026). Config: src/config/elements/lucchetto.js.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Lucchetto_Tile extends Olobuild_Tile_Base {

    protected $type     = 'lucchetto';
    protected $name     = 'Lucchetto';
    protected $icon     = 'dashicons-lock';
    protected $category = 'interactive';

    protected $defaults = [
        // contenuto
        'enigma'                     => '<p><strong>Quattro cifre</strong> aprono il lucchetto, in quest\'ordine:</p><p>i colli su cui sorge Roma, i lati di un triangolo, le lune della Terra, le zampe di un gatto.</p>',
        'indizio'                    => 'Sommate fanno 15, e solo l\'ultima è pari.',
        'testo_indizio'              => 'Mostra un indizio',
        'combinazione'               => '7314',
        'simboli'                    => 'cifre',
        'simboli_personalizzati'     => 'I,II,III,IV,V',
        'testo_pulsante'             => 'Apri',
        'messaggio_errore'           => 'Non si apre. Riprova.',
        'messaggio_successo'         => 'Aperto! Il codice qui sotto vale uno sconto del 10% sulla prossima prenotazione.',
        'codice_premio'              => 'APRITI10',
        'testo_copia'                => 'Copia il codice',
        'testo_copiato'              => 'Copiato',
        'pulsante_premio_testo'      => '',
        'pulsante_premio_url'        => '',
        'tentativi_indizio'          => 3,
        'mostra_tentativi'           => false,
        'ricorda'                    => false,
        'mostra_lucchetto'           => true,
        'icona_lucchetto'            => '',
        // stile
        'stile'                      => 'ottone',
        'colore_corpo'               => '',
        'colore_ruota'               => '',
        'colore_simbolo'             => '',
        'colore_accento'             => '',
        'colore_successo'            => '',
        'colore_errore'              => '',
        'sfondo_pannello'            => '',
        'raggio'                     => [ 'tl' => 16, 'tr' => 16, 'br' => 16, 'bl' => 16 ],
        'ombra'                      => 'none',
        'ombra_h'                    => '0',
        'ombra_v'                    => '4',
        'ombra_blur'                 => '10',
        'ombra_spread'               => '0',
        'ombra_color'                => 'rgba(0,0,0,0.15)',
        'ombra_inset'                => false,
        'enigma_font_family'         => '',
        'enigma_font_size'           => 18,
        'enigma_font_size_tablet'    => '',
        'enigma_font_size_mobile'    => '',
        'enigma_font_weight'         => '400',
        'enigma_line_height'         => 1.6,
        'enigma_color'               => '',
        'simbolo_font_family'        => '',
        'simbolo_font_size'          => '',
        'simbolo_font_size_tablet'   => '',
        'simbolo_font_size_mobile'   => '',
        'simbolo_font_weight'        => '700',
        'simbolo_color'              => '',
        'messaggio_font_family'      => '',
        'messaggio_font_size'        => 17,
        'messaggio_font_size_tablet' => '',
        'messaggio_font_size_mobile' => '',
        'messaggio_font_weight'      => '500',
        'messaggio_color'            => '',
        'misura_ruota'               => 60,
        'gap'                        => 32,
        'allineamento'               => 'center',
    ];

    /** Le uid già usate in questa richiesta: la stessa tile resa due volte nella pagina riceve un suffisso. */
    private static $uid_usate = [];

    public function get_controls() {
        return [];
    }

    /** Maiuscolo anche fuori dall'ASCII (romani, greco, accenti), come lo confronta il tentativo. */
    private static function maiuscolo( $v ) {
        $v = (string) $v;
        return function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $v, 'UTF-8' ) : strtoupper( $v );
    }

    /** I caratteri di una stringa UTF-8 (un segno non ASCII è UN simbolo, non due byte). */
    private static function caratteri( $v ) {
        $c = preg_split( '//u', (string) $v, -1, PREG_SPLIT_NO_EMPTY );
        return is_array( $c ) ? $c : str_split( (string) $v );
    }

    /** Un testo accorciato a $max caratteri (UTF-8). */
    private static function taglia( $v, $max ) {
        $v = (string) $v;
        return function_exists( 'mb_substr' ) ? mb_substr( $v, 0, $max, 'UTF-8' ) : substr( $v, 0, $max );
    }

    /** Un valore di collegamento: stringa, oppure l'oggetto del selettore ({url}). */
    private static function url_di( $v ) {
        if ( is_array( $v ) ) {
            $v = $v['url'] ?? '';
        }
        return trim( (string) $v );
    }

    /**
     * I simboli delle ruote: [ mostrati, normalizzati, ripiego ]. Cifre 0–9, lettere A–Z, oppure l'elenco scritto
     * (separato da virgole, punti e virgola o a capo; doppioni tolti; al massimo 40 simboli di 8 caratteri). Meno di due
     * simboli personalizzati: le cifre, con ripiego = true (il builder lo dice).
     */
    private static function simboli( $tipo, $elenco ) {
        if ( 'lettere' === $tipo ) {
            $l = range( 'A', 'Z' );
            return [ $l, $l, false ];
        }
        if ( 'personalizzati' === $tipo ) {
            $mostrati = [];
            $norme    = [];
            $parti    = preg_split( '/[,;\r\n]+/u', (string) $elenco );
            foreach ( is_array( $parti ) ? $parti : [] as $x ) {
                $x = self::taglia( trim( strip_tags( $x ) ), 8 );
                if ( '' === $x ) {
                    continue;
                }
                $k = self::maiuscolo( $x );
                if ( in_array( $k, $norme, true ) ) {
                    continue;
                }
                $mostrati[] = $x;
                $norme[]    = $k;
                if ( count( $norme ) >= 40 ) {
                    break;
                }
            }
            if ( count( $norme ) >= 2 ) {
                return [ $mostrati, $norme, false ];
            }
        }
        $c = array_map( 'strval', range( 0, 9 ) );
        return [ $c, $c, 'personalizzati' === $tipo ];
    }

    /**
     * I simboli della combinazione, già normalizzati. Con separatori (spazi, virgole, «|», «;», «/», «·», «-» quando
     * nessun simbolo li contiene) si divide lì; senza, simbolo per simbolo quando sono tutti di un carattere,
     * altrimenti cercando il simbolo più lungo che combacia («IIIIV» con I…V = III, IV).
     */
    private static function gettoni( $raw, $norme ) {
        $raw = self::maiuscolo( trim( strip_tags( (string) $raw ) ) );
        if ( '' === $raw ) {
            return [];
        }
        $singoli = true;
        $trattino = false;
        foreach ( $norme as $k ) {
            if ( 1 !== count( self::caratteri( $k ) ) ) {
                $singoli = false;
            }
            if ( false !== strpos( $k, '-' ) ) {
                $trattino = true;
            }
        }
        $sep = $trattino ? '/[\s,;|\/·]+/u' : '/[\s,;|\/·\-]+/u';
        if ( preg_match( $sep, $raw ) ) {
            $out   = [];
            $parti = preg_split( $sep, $raw, -1, PREG_SPLIT_NO_EMPTY );
            foreach ( is_array( $parti ) ? $parti : [] as $p ) {
                if ( $singoli ) {
                    foreach ( self::caratteri( $p ) as $c ) {
                        $out[] = $c;
                    }
                } else {
                    $out[] = $p;
                }
            }
            return $out;
        }
        if ( $singoli ) {
            return self::caratteri( $raw );
        }
        $ordinati = $norme;
        usort(
            $ordinati,
            function ( $a, $b ) {
                return strlen( $b ) - strlen( $a );
            }
        );
        $out = [];
        $pos = 0;
        $tot = strlen( $raw );
        while ( $pos < $tot ) {
            $preso = '';
            foreach ( $ordinati as $k ) {
                if ( '' !== $k ) {
                    if ( substr( $raw, $pos, strlen( $k ) ) === $k ) {
                        $preso = $k;
                        break;
                    }
                }
            }
            if ( '' === $preso ) {
                $out[] = substr( $raw, $pos ); // il resto non combacia: resta un gettone che le ruote non hanno
                break;
            }
            $out[] = $preso;
            $pos  += strlen( $preso );
        }
        return $out;
    }

    /**
     * FNV-1a a 32 bit sui byte UTF-8 di una stringa. Il prodotto per 16777619 (= 2^24 + 403) è scomposto perché resti
     * esatto con gli interi di PHP; il gemello nello script usa Math.imul.
     */
    private static function fnv( $str ) {
        $h = 2166136261;
        $n = strlen( $str );
        for ( $i = 0; $i < $n; $i++ ) {
            $h ^= ord( $str[ $i ] );
            $h  = ( ( ( $h << 24 ) & 0xffffffff ) + $h * 403 ) & 0xffffffff;
        }
        return $h;
    }

    /**
     * Il premio cifrato: ogni byte in XOR con la sequenza FNV-1a(«chiave esadecimale:blocco»), 4 byte per blocco dal
     * più alto; base64. Lo script lo decifra con la chiave calcolata dal tentativo giusto.
     */
    private static function cifra( $testo, $k ) {
        $kh  = sprintf( '%08x', $k );
        $out = '';
        $n   = strlen( $testo );
        for ( $i = 0; $i < $n; $i += 4 ) {
            $ks = self::fnv( $kh . ':' . ( $i >> 2 ) );
            for ( $j = 0; $j < 4; $j++ ) {
                if ( $i + $j >= $n ) {
                    break;
                }
                $out .= chr( ord( $testo[ $i + $j ] ) ^ ( ( $ks >> ( 24 - 8 * $j ) ) & 255 ) );
            }
        }
        return base64_encode( $out );
    }

    /** CSS di un testo dal controllo tipografia (famiglia, misura per dispositivo, peso, interlinea, colore). */
    private function css_testo( $s, $k, $selettore ) {
        $d   = '';
        $fam = $this->resolve_font_family( $s[ $k . '_font_family' ] ?? '' );
        $fs  = (float) ( $s[ $k . '_font_size' ] ?? 0 );
        $fw  = $this->font_weight_css( $s[ $k . '_font_weight' ] ?? '' );
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
        $chiave  = $this->chiave_stabile( $settings );
        $uid     = 'olk-' . $chiave;
        $base    = $uid;
        $copia_n = 2;
        while ( isset( self::$uid_usate[ $uid ] ) ) {
            $uid = $base . '-' . $copia_n++;
        }
        self::$uid_usate[ $uid ] = true;
        $sel     = '#' . $uid;
        $builder = ! empty( $s['_builder_mode'] );

        // ── Simboli e combinazione ──
        $tipo_simboli           = in_array( (string) $s['simboli'], [ 'cifre', 'lettere', 'personalizzati' ], true ) ? (string) $s['simboli'] : 'cifre';
        list( $mostrati, $norme, $pochi_simboli ) = self::simboli( $tipo_simboli, $s['simboli_personalizzati'] );
        $gettoni                = self::gettoni( $s['combinazione'], $norme );
        $indici                 = [];
        $estranei               = [];
        foreach ( $gettoni as $g ) {
            $i = array_search( $g, $norme, true );
            if ( false === $i ) {
                $estranei[] = $g;
            } else {
                $indici[] = (int) $i;
            }
        }
        $quanti = count( $gettoni );
        $valida = $quanti >= 3 && $quanti <= 6 && ! $estranei;
        if ( ! $valida && ! $builder ) {
            return ''; // senza una combinazione giusta il lucchetto non si aprirebbe mai: sul sito non si mostra
        }
        $n_ruote = $valida ? $quanti : max( 3, min( 6, $quanti ? $quanti : 4 ) );

        // ── Testi ──
        $enigma_raw = trim( (string) $s['enigma'] );
        if ( preg_match( '/<[a-z!\/][^>]*>/i', $enigma_raw ) ) {
            $enigma = wp_kses(
                $enigma_raw,
                [
                    'p'      => [],
                    'br'     => [],
                    'strong' => [],
                    'b'      => [],
                    'em'     => [],
                    'i'      => [],
                    'u'      => [],
                    's'      => [],
                    'sub'    => [],
                    'sup'    => [],
                    'mark'   => [],
                ]
            );
        } else {
            $enigma = nl2br( esc_html( $enigma_raw ) );
        }
        $indizio   = trim( strip_tags( (string) $s['indizio'] ) );
        $t_indizio = trim( (string) $s['testo_indizio'] );
        $t_indizio = '' !== $t_indizio ? $t_indizio : __( 'Mostra un indizio', 'olobuilder' );
        $t_apri    = trim( (string) $s['testo_pulsante'] );
        $t_apri    = '' !== $t_apri ? $t_apri : __( 'Apri', 'olobuilder' );
        $errore    = trim( (string) $s['messaggio_errore'] );
        $errore    = '' !== $errore ? $errore : __( 'Non si apre. Riprova.', 'olobuilder' );
        $codice    = self::taglia( trim( strip_tags( (string) $s['codice_premio'] ) ), 60 );
        $t_copia   = trim( (string) $s['testo_copia'] );
        $t_copia   = '' !== $t_copia ? $t_copia : __( 'Copia il codice', 'olobuilder' );
        $t_copiato = trim( (string) $s['testo_copiato'] );
        $t_copiato = '' !== $t_copiato ? $t_copiato : __( 'Copiato', 'olobuilder' );
        $btn_testo = trim( strip_tags( (string) $s['pulsante_premio_testo'] ) );
        $btn_url   = esc_url_raw( self::url_di( $s['pulsante_premio_url'] ) );
        $con_btn   = '' !== $btn_testo && '' !== $btn_url;
        $msg       = self::taglia( trim( strip_tags( (string) $s['messaggio_successo'] ) ), 1200 );
        if ( '' === $msg && '' === $codice && ! $con_btn ) {
            $msg = __( 'Il lucchetto è aperto.', 'olobuilder' ); // il premio non resta mai vuoto: il fuoco va lì
        }
        $dopo      = '' !== $indizio ? max( 0, min( 10, (int) $s['tentativi_indizio'] ) ) : 0;
        $conta     = ! empty( $s['mostra_tentativi'] );
        // «Ricorda»: non nel builder, dove si deve poter riprovare a ogni anteprima.
        $ricorda   = ! empty( $s['ricorda'] ) && ! $builder;

        // ── Impronta, premio cifrato, sale ──
        $impronta = '';
        $cifrato  = '';
        $sale     = sprintf( '%08x', (int) wp_rand( 0, 4294967295 ) );
        if ( $valida ) {
            $norm_str = implode( '|', $gettoni );
            $impronta = sprintf( '%08x', self::fnv( $norm_str . $sale ) );
            $premio   = [ 'ok' => 1, 'm' => $msg, 'i' => $indici ];
            if ( '' !== $codice ) {
                $premio['c'] = $codice;
            }
            if ( $con_btn ) {
                $premio['t'] = $btn_testo;
                $premio['u'] = $btn_url;
            }
            $cifrato = self::cifra( (string) wp_json_encode( $premio, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ), self::fnv( $norm_str . '#' . $chiave ) );
        }

        // ── Disegno: lucchetto, icona o solo le ruote ──
        $mostra   = ! empty( $s['mostra_lucchetto'] );
        $icona    = $mostra ? sanitize_text_field( (string) $s['icona_lucchetto'] ) : '';
        $disegno  = $mostra && '' === $icona;
        $stile    = in_array( (string) $s['stile'], [ 'ottone', 'acciaio', 'minimal' ], true ) ? (string) $s['stile'] : 'ottone';
        $metallo  = 'minimal' !== $stile;
        $centro   = 'left' !== (string) $s['allineamento'];
        $misura   = max( 44, min( 88, (int) $s['misura_ruota'] ) );
        $gap      = max( 0, min( 120, (int) $s['gap'] ) );
        // Larghezza del corpo in «ruote»: N ruote, N−1 spazi da .14, i lati da .28 e un margine per l'ombra.
        $unita    = round( $n_ruote + 0.14 * ( $n_ruote - 1 ) + 0.56 + 0.12, 3 );
        // Il gancio: largo G ruote (più stretto del corpo), alto 2,2 ruote, spessore .28 ruote; affonda nel corpo.
        $g_larg   = round( max( 2.3, min( 3.4, 0.56 * $unita ) ), 3 );
        $vb_alto  = round( 100 * 2.2 / $g_larg, 2 );
        $spess    = round( 28 / $g_larg, 2 );
        $x1       = round( $spess / 2 + 0.6, 2 );
        $x2       = round( 100 - $spess / 2 - 0.6, 2 );
        $arco     = round( ( $x2 - $x1 ) / 2, 2 );
        $cy       = round( $spess / 2 + 0.6 + $arco, 2 );
        $tracciato = "M{$x1} {$vb_alto}V{$cy}A{$arco} {$arco} 0 0 1 {$x2} {$cy}V{$vb_alto}";
        // I simboli lunghi (personalizzati) si rimpiccioliscono per stare nella ruota.
        $piu_lungo = 1;
        foreach ( $mostrati as $m ) {
            $piu_lungo = max( $piu_lungo, count( self::caratteri( $m ) ) );
        }
        $rapporto = round( min( 0.52, 1.36 / $piu_lungo ), 3 );
        $tetto    = round( $rapporto * 1.3, 3 );

        // ── Colori: tutti dai token del tema, salvo quelli scelti ──
        // Scuro e chiaro della Palette: definiti una volta sulla radice (--olk-k, --olk-l) e ripresi da lì.
        $scuro   = 'var(--olk-k)';
        $chiaro  = 'var(--olk-l)';
        $c_corpo = $this->safe_color_css( $s['colore_corpo'] );
        $corpo_s = '' !== $c_corpo;
        if ( ! $corpo_s ) {
            // Ottone: il caldo del tema (il colore «avviso», un ambra) schiarito e poi scurito, verso l'oro brunito.
            $c_corpo = 'ottone' === $stile
                ? 'color-mix(in srgb, color-mix(in srgb, var(--olo-color-warning, #f59e0b) 70%, ' . $chiaro . ') 80%, ' . $scuro . ')'
                : ( 'acciaio' === $stile ? 'color-mix(in srgb, ' . $scuro . ' 40%, ' . $chiaro . ')' : 'var(--olo-color-primary)' );
        }
        $c_ruota = $this->safe_color_css( $s['colore_ruota'] );
        $ruota_s = '' !== $c_ruota;
        if ( ! $ruota_s ) {
            $c_ruota = $metallo ? 'color-mix(in srgb, ' . $scuro . ' 86%, var(--olk-c))' : 'var(--olo-color-background, #ffffff)';
        }
        $sim_def = $metallo ? 'color-mix(in srgb, ' . $chiaro . ' 92%, var(--olk-c))' : 'var(--olo-color-text, #1f2937)';
        $c_sim   = $this->safe_color_css( $s['colore_simbolo'] ) ?: $this->safe_color_css( $s['simbolo_color'] );
        if ( '' === $c_sim ) {
            $c_sim = $ruota_s ? ( Olobuild_Tile_Utils::testo_su_fondo( $c_ruota, $sim_def ) ?: $sim_def ) : $sim_def;
        }
        if ( $disegno ) {
            if ( $metallo ) {
                $c_freccia = 'color-mix(in srgb, var(--olk-c) 36%, ' . $scuro . ')';
            } else {
                $c_freccia = $corpo_s
                    ? ( Olobuild_Tile_Utils::testo_su_fondo( $c_corpo, 'var(--olo-color-primary-contrast, #ffffff)' ) ?: 'var(--olo-color-primary-contrast, #ffffff)' )
                    : 'var(--olo-color-primary-contrast, #ffffff)';
            }
        } else {
            $c_freccia = 'color-mix(in srgb, currentColor 72%, transparent)';
        }
        $acc_s  = $this->safe_color_css( $s['colore_accento'] );
        $acc    = '' !== $acc_s ? $acc_s : 'var(--olo-color-primary)';
        $su_acc = '' !== $acc_s
            ? ( Olobuild_Tile_Utils::testo_su_fondo( $acc_s, 'var(--olo-color-light, #ffffff)' ) ?: 'var(--olo-color-light, #ffffff)' )
            : 'var(--olo-color-primary-contrast, #ffffff)';
        $ok     = $this->safe_color_css( $s['colore_successo'] ) ?: 'var(--olo-color-success, #15803d)';
        $err    = $this->safe_color_css( $s['colore_errore'] ) ?: 'var(--olo-color-danger, #b42318)';
        $sfondo = $this->safe_color_css( $s['sfondo_pannello'] );
        $testo  = '' !== $sfondo ? ( Olobuild_Tile_Utils::testo_su_fondo( $sfondo, 'var(--olo-color-text, #374151)' ) ?: 'var(--olo-color-text, #374151)' ) : '';
        $ombra  = Olobuild_Tile_Utils::shadow_value( $s, 'ombra' );
        $raggio = Olobuild_Tile_Utils::border_radius( $s['raggio'] ) ?: '0';
        $r_max  = Olobuild_Tile_Utils::radius_int( $s['raggio'] );
        $r_rs   = round( $r_max / 2, 1 ); // le ruote: metà del raggio, mai più di .12 ruote
        $r_rp   = min( $r_max, 14 );      // indizio, premio, codice: lo stesso raggio, contenuto

        if ( $metallo ) {
            $incavo = 'inset 0 .2rem .45rem color-mix(in srgb, ' . $scuro . ' 78%, transparent),inset 0 -1px 0 color-mix(in srgb, ' . $chiaro . ' 14%, transparent),0 1px 0 color-mix(in srgb, ' . $chiaro . ' 50%, transparent)';
        } else {
            $incavo = 'inset 0 0 0 1px color-mix(in srgb, var(--olk-sim) 16%, transparent)';
        }
        if ( ! $disegno && '' === $sfondo && 'none' !== $ombra ) {
            $incavo .= ',' . $ombra; // senza disegno e senza pannello l'ombra va sotto le ruote
        }

        $classi = 'olk olk--' . $stile . ( $centro ? ' olk--centro' : ' olk--sinistra' ) . ( $disegno ? ' olk--disegno' : ' olk--nudo' ) . ( '' !== $icona ? ' olk--icona' : '' );

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS da colori passati da safe_color_css() e dagli helper di Olobuild_Tile_Utils, numeri forzati e arrotondati qui, valori da elenchi chiusi e selettori generati qui
        echo '<style>';
        echo "{$sel}{--olk-k:var(--olo-color-dark, #14161c);--olk-l:var(--olo-color-light, #ffffff);--olk-m:{$misura}px;--olk-u:{$unita};--olk-w:min(var(--olk-m),calc((100vw - 3rem) / var(--olk-u)));--olk-h:calc(var(--olk-w) * .74);--olk-fs:calc(var(--olk-w) * {$rapporto});--olk-gap:{$gap}px;--olk-r:{$raggio};--olk-rs:min({$r_rs}px,calc(var(--olk-w) * .12));--olk-rp:{$r_rp}px;--olk-c:{$c_corpo};--olk-ruota:{$c_ruota};--olk-sim:{$c_sim};--olk-freccia:{$c_freccia};--olk-acc:{$acc};--olk-su-acc:{$su_acc};--olk-ok:{$ok};--olk-err:{$err};--olk-incavo:{$incavo}}";
        echo "{$sel} [hidden]{display:none!important}";
        echo "{$sel} .olk-sr{position:absolute!important;width:1px;height:1px;margin:-1px;padding:0;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}";
        echo "{$sel} .olk-pannello{box-sizing:border-box;display:flex;flex-direction:column;align-items:" . ( $centro ? 'center' : 'flex-start' ) . ';gap:var(--olk-gap);min-width:0;text-align:' . ( $centro ? 'center' : 'left' )
            . ( '' !== $sfondo ? ";background:{$sfondo};color:{$testo};padding:clamp(1.25rem, 4vw, 2.75rem);border-radius:var(--olk-r)" . ( 'none' !== $ombra ? ";box-shadow:{$ombra}" : '' ) : '' ) . '}';
        echo "{$sel} .olk-enigma{max-width:40rem;margin:0;text-wrap:pretty}";
        echo "{$sel} .olk-enigma p{margin:0 0 .65em}";
        echo "{$sel} .olk-enigma p:last-child{margin-bottom:0}";

        // Il lucchetto: il gancio (SVG) dietro al corpo, che contiene le ruote. Lo spazio sopra serve al gancio aperto.
        echo "{$sel} .olk-lucchetto{position:relative;display:flex;flex-direction:column;align-items:center;max-width:100%}";
        echo "{$sel}.olk--disegno .olk-lucchetto{padding-top:calc(var(--olk-w) * .7)}";
        echo "{$sel} .olk-gancio{position:relative;z-index:0;display:block;flex:none;width:calc(var(--olk-w) * {$g_larg});height:calc(var(--olk-w) * 2.2);margin-bottom:calc(var(--olk-w) * -.42);overflow:visible;transform-origin:{$x1}% 100%}";
        echo "{$sel} .olk-gancio path{fill:none;stroke-linecap:butt}";
        if ( $metallo ) {
            echo "{$sel} .olk-g1{stroke:color-mix(in srgb, {$scuro} 64%, {$chiaro})}";
            echo "{$sel} .olk-g2{stroke:color-mix(in srgb, {$scuro} 28%, {$chiaro})}";
            echo "{$sel} .olk-g3{stroke:color-mix(in srgb, {$chiaro} 90%, {$scuro})}";
        } else {
            echo "{$sel} .olk-g1{stroke:color-mix(in srgb, currentColor 58%, transparent)}";
            echo "{$sel} .olk-g2,{$sel} .olk-g3{display:none}";
        }
        echo "{$sel} .olk-icona{display:flex;margin-bottom:calc(var(--olk-w) * .25);color:var(--olk-c);line-height:0}";
        echo "{$sel} .olk-icona svg{width:calc(var(--olk-w) * 1.4);height:calc(var(--olk-w) * 1.4)}";
        echo "{$sel} .olk-corpo{position:relative;z-index:1;display:flex;padding:calc(var(--olk-w) * .18) calc(var(--olk-w) * .28);border-radius:var(--olk-r)}";
        echo "{$sel} .olk-corpo::after{content:'';position:absolute;inset:0;border-radius:inherit;pointer-events:none;opacity:0}";
        $ombra_corpo = ( '' === $sfondo && 'none' !== $ombra ) ? ',' . $ombra : '';
        if ( $disegno ) {
            if ( 'ottone' === $stile ) {
                echo "{$sel} .olk-corpo{background:linear-gradient(104deg, transparent 22%, color-mix(in srgb, {$chiaro} 30%, transparent) 34%, transparent 46%),linear-gradient(180deg, color-mix(in srgb, var(--olk-c) 55%, {$chiaro}) 0%, var(--olk-c) 32%, color-mix(in srgb, var(--olk-c) 72%, {$scuro}) 100%);box-shadow:inset 0 1px 0 color-mix(in srgb, {$chiaro} 60%, transparent),inset 0 -.25rem .5rem color-mix(in srgb, {$scuro} 30%, transparent),0 .9rem 1.6rem -.9rem color-mix(in srgb, {$scuro} 75%, transparent){$ombra_corpo}}";
            } elseif ( 'acciaio' === $stile ) {
                echo "{$sel} .olk-corpo{background:repeating-linear-gradient(90deg, color-mix(in srgb, {$chiaro} 10%, transparent) 0 1px, transparent 1px 3px),linear-gradient(104deg, transparent 20%, color-mix(in srgb, {$chiaro} 34%, transparent) 33%, transparent 46%),linear-gradient(180deg, color-mix(in srgb, var(--olk-c) 50%, {$chiaro}) 0%, var(--olk-c) 42%, color-mix(in srgb, var(--olk-c) 70%, {$scuro}) 100%);box-shadow:inset 0 1px 0 color-mix(in srgb, {$chiaro} 70%, transparent),inset 0 -.25rem .5rem color-mix(in srgb, {$scuro} 24%, transparent),0 .9rem 1.6rem -.9rem color-mix(in srgb, {$scuro} 70%, transparent){$ombra_corpo}}";
            } else {
                echo "{$sel} .olk-corpo{background:var(--olk-c)" . ( '' !== $ombra_corpo ? ';box-shadow:' . substr( $ombra_corpo, 1 ) : '' ) . '}';
            }
        } else {
            echo "{$sel} .olk-corpo{padding:0;background:none}";
        }

        // Le ruote: colonna = freccia su, finestra col rullo, freccia giù.
        echo "{$sel} .olk-ruote{display:flex;gap:calc(var(--olk-w) * .14);margin:0}";
        echo "{$sel} .olk-col{display:flex;flex-direction:column;align-items:center;flex:none;width:var(--olk-w)}";
        echo "{$sel} .olk-freccia,{$sel} .olk-freccia:hover{background:transparent}";
        echo "{$sel} .olk-freccia{display:flex;align-items:center;justify-content:center;box-sizing:border-box;width:100%;height:calc(var(--olk-w) * .46);min-height:1.5rem;margin:0;padding:0;border:0;border-radius:var(--olk-rs);color:var(--olk-freccia);font:inherit;line-height:0;cursor:pointer;-webkit-appearance:none;appearance:none;box-shadow:none;touch-action:manipulation;-webkit-user-select:none;user-select:none;-webkit-touch-callout:none;-webkit-tap-highlight-color:transparent;transition:background-color .15s ease,transform .1s ease}";
        echo "{$sel} .olk-freccia svg{display:block;width:calc(var(--olk-w) * .4);height:calc(var(--olk-w) * .4);fill:none;stroke:currentColor;stroke-width:2.6;stroke-linecap:round;stroke-linejoin:round}";
        echo "{$sel} .olk-freccia:hover{background:color-mix(in srgb, currentColor 14%, transparent);color:var(--olk-freccia)}";
        echo "{$sel} .olk-freccia:active{transform:scale(.9)}";
        echo "{$sel} .olk-freccia:focus{outline:none}";
        echo "{$sel} .olk-freccia:focus-visible{outline:2px solid var(--olk-acc);outline-offset:1px}";
        echo "{$sel} .olk-freccia:disabled{opacity:.3;cursor:default;pointer-events:none}";
        echo "{$sel} .olk-ruota{position:relative;box-sizing:border-box;width:100%;height:calc(var(--olk-h) * 2);overflow:hidden;border-radius:var(--olk-rs);box-shadow:var(--olk-incavo);cursor:ns-resize;touch-action:none;-webkit-user-select:none;user-select:none;-webkit-tap-highlight-color:transparent;outline:none;transition:box-shadow .2s ease}";
        if ( $metallo ) {
            echo "{$sel} .olk-ruota{background:linear-gradient(180deg, color-mix(in srgb, var(--olk-ruota) 70%, {$scuro}) 0%, var(--olk-ruota) 24%, color-mix(in srgb, var(--olk-ruota) 86%, {$chiaro}) 50%, var(--olk-ruota) 76%, color-mix(in srgb, var(--olk-ruota) 70%, {$scuro}) 100%)}";
            echo "{$sel} .olk-ruota::after{content:'';position:absolute;inset:0;z-index:1;border-radius:inherit;pointer-events:none;background:linear-gradient(180deg, color-mix(in srgb, {$scuro} 58%, transparent) 0%, color-mix(in srgb, {$scuro} 12%, transparent) 30%, transparent 44%, transparent 56%, color-mix(in srgb, {$scuro} 12%, transparent) 70%, color-mix(in srgb, {$scuro} 58%, transparent) 100%)}";
            echo "{$sel} .olk-cella{text-shadow:0 -1px 0 color-mix(in srgb, {$scuro} 70%, transparent)}";
        } else {
            echo "{$sel} .olk-ruota{background:var(--olk-ruota)}";
            echo "{$sel} .olk-ruota::after{content:'';position:absolute;inset:0;z-index:1;border-radius:inherit;pointer-events:none;background:linear-gradient(180deg, color-mix(in srgb, var(--olk-ruota) 82%, transparent) 0%, color-mix(in srgb, var(--olk-ruota) 0%, transparent) 34%, color-mix(in srgb, var(--olk-ruota) 0%, transparent) 66%, color-mix(in srgb, var(--olk-ruota) 82%, transparent) 100%)}";
        }
        echo "{$sel} .olk-ruota:focus-visible{box-shadow:var(--olk-incavo),0 0 0 2px var(--olo-color-background, #ffffff),0 0 0 4px var(--olk-acc)}";
        echo "{$sel} .olk-ruota.is-trascina{cursor:grabbing}";
        echo "{$sel} .olk-rullo{position:absolute;left:0;right:0;top:0;display:flex;flex-direction:column;transform:translateY(calc(var(--olk-h) * (.5 - var(--p, 0))));transition:transform .2s cubic-bezier(.25,.8,.3,1);will-change:transform}";
        echo "{$sel} .olk-cella{display:flex;align-items:center;justify-content:center;flex:none;height:var(--olk-h);overflow:hidden;font-size:var(--olk-fs);font-weight:700;line-height:1;color:var(--olk-sim);font-variant-numeric:tabular-nums lining-nums;letter-spacing:-.01em;white-space:nowrap}";

        // Errore (breve) e apertura.
        echo "{$sel}.is-errore .olk-ruota{box-shadow:var(--olk-incavo),inset 0 0 0 2px var(--olk-err)}";
        echo "{$sel}.is-errore .olk-lucchetto{animation:olk-scossa .45s cubic-bezier(.36,.07,.19,.97)}";
        echo "{$sel}.is-aperto .olk-ruota{box-shadow:var(--olk-incavo),inset 0 0 0 2px var(--olk-ok);cursor:default}";
        echo "{$sel}.is-aprendo .olk-gancio{animation:olk-apri .6s cubic-bezier(.3,.7,.3,1) forwards}";
        echo "{$sel}.is-aperto-subito .olk-gancio{transform:translateY(-13%) rotate(-15deg)}";
        echo "{$sel}.is-aprendo .olk-corpo::after{animation:olk-anello .9s ease-out .2s}";
        echo "{$sel}.is-aprendo .olk-icona{animation:olk-pop .55s cubic-bezier(.3,1.6,.5,1)}";
        echo "{$sel}.is-aperto .olk-icona{color:var(--olk-ok)}";
        echo "{$sel}.is-fatto .olk-azioni,{$sel}.is-fatto .olk-stato,{$sel}.is-fatto .olk-tentativi,{$sel}.is-fatto .olk-indizio{display:none}";
        echo '@keyframes olk-scossa{0%,100%{transform:translateX(0)}20%{transform:translateX(-.55rem)}40%{transform:translateX(.45rem)}60%{transform:translateX(-.3rem)}80%{transform:translateX(.15rem)}}';
        echo '@keyframes olk-apri{0%{transform:none}40%{transform:translateY(-13%)}100%{transform:translateY(-13%) rotate(-15deg)}}';
        echo '@keyframes olk-anello{0%{opacity:1;box-shadow:0 0 0 0 color-mix(in srgb, var(--olk-ok) 65%, transparent)}100%{opacity:0;box-shadow:0 0 0 1.25rem color-mix(in srgb, var(--olk-ok) 0%, transparent)}}';
        echo '@keyframes olk-pop{0%{transform:scale(1)}40%{transform:scale(1.18)}100%{transform:scale(1)}}';
        echo '@keyframes olk-entra{from{opacity:0;transform:translateY(.75rem)}to{opacity:1;transform:none}}';
        echo '@keyframes olk-appare{from{opacity:0}to{opacity:1}}';

        // Pulsanti, stato, indizio.
        echo "{$sel} .olk-comandi{display:flex;flex-direction:column;align-items:" . ( $centro ? 'center' : 'flex-start' ) . ';gap:.75rem;width:100%}';
        echo "{$sel} .olk-azioni{display:flex;flex-wrap:wrap;align-items:center;justify-content:" . ( $centro ? 'center' : 'flex-start' ) . ';gap:.75rem 1.25rem}';
        echo "{$sel} .olk-apri,{$sel} .olk-link{box-sizing:border-box;display:inline-flex;align-items:center;justify-content:center;gap:.5em;min-height:2.75rem;margin:0;padding:var(--olo-btn-padding, .8em 1.75em);border:0;border-radius:var(--olo-btn-border-radius, .5rem);background:var(--olk-acc);color:var(--olk-su-acc);font:inherit;font-size:var(--olo-btn-font-size, 1rem);font-weight:var(--olo-btn-font-weight, 600);line-height:1.1;text-transform:var(--olo-btn-text-transform, none);letter-spacing:var(--olo-btn-letter-spacing, normal);text-decoration:none;cursor:pointer;-webkit-appearance:none;appearance:none;box-shadow:none;transition:filter .2s ease,transform .15s ease}";
        echo "{$sel} .olk-apri:hover,{$sel} .olk-link:hover{background:var(--olk-acc);color:var(--olk-su-acc);filter:brightness(var(--olo-btn-hover-brightness, 108%));transform:translateY(-1px)}";
        echo "{$sel} .olk-apri:active,{$sel} .olk-link:active{transform:translateY(1px)}";
        echo "{$sel} .olk-indizio-btn,{$sel} .olk-indizio-btn:hover{background:transparent}";
        echo "{$sel} .olk-indizio-btn{display:inline-flex;align-items:center;min-height:2.75rem;margin:0;padding:0 .25rem;border:0;color:inherit;font:inherit;font-size:.9375rem;font-weight:500;line-height:1.3;text-transform:none;letter-spacing:normal;text-decoration:underline;text-decoration-thickness:1px;text-underline-offset:.25em;cursor:pointer;opacity:.85;-webkit-appearance:none;appearance:none;box-shadow:none}";
        echo "{$sel} .olk-indizio-btn:hover{opacity:1;color:inherit}";
        echo "{$sel} .olk-apri:focus,{$sel} .olk-link:focus,{$sel} .olk-indizio-btn:focus,{$sel} .olk-copia:focus{outline:none}";
        echo "{$sel} .olk-apri:focus-visible,{$sel} .olk-link:focus-visible,{$sel} .olk-indizio-btn:focus-visible,{$sel} .olk-copia:focus-visible{outline:2px solid var(--olk-acc);outline-offset:3px}";
        echo "{$sel} .olk-stato{margin:0;min-height:1.45em;font-size:15px;font-weight:600;line-height:1.45}";
        echo "{$sel} .olk-stato.is-errore{color:color-mix(in srgb, var(--olk-err) 82%, currentColor)}";
        echo "{$sel} .olk-tentativi{margin:0;font-size:13px;font-weight:600;line-height:1.3;letter-spacing:.06em;text-transform:uppercase;opacity:.75;font-variant-numeric:tabular-nums}";
        echo "{$sel} .olk-indizio{box-sizing:border-box;width:100%;max-width:36rem;margin:0;padding:.875rem 1.125rem;border-radius:var(--olk-rp);background:color-mix(in srgb, currentColor 6%, transparent);box-shadow:inset 3px 0 0 var(--olk-acc);font-size:.9375rem;line-height:1.55;text-wrap:pretty}";
        echo "{$sel} .olk-indizio-et{display:block;margin:0 0 .25rem;font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;opacity:.7}";
        echo "{$sel} .olk-indizio p{margin:0}";

        // Il premio.
        echo "{$sel} .olk-premio{box-sizing:border-box;display:flex;flex-direction:column;align-items:" . ( $centro ? 'center' : 'flex-start' ) . ";gap:1rem;width:100%;max-width:36rem;margin:0;padding:clamp(1.125rem, 3vw, 1.75rem);border:1px solid color-mix(in srgb, var(--olk-ok) 36%, transparent);border-radius:var(--olk-rp);background:color-mix(in srgb, var(--olk-ok) 8%, transparent);animation:olk-entra .5s cubic-bezier(.2,.8,.3,1) .3s both}";
        echo "{$sel}.is-aperto-subito .olk-premio{animation:none}";
        echo "{$sel} .olk-premio:focus{outline:none}";
        echo "{$sel} .olk-premio:focus-visible{outline:2px solid var(--olk-ok);outline-offset:3px}";
        echo "{$sel} .olk-msg{margin:0;max-width:34rem;font-size:17px;font-weight:500;line-height:1.5;text-wrap:pretty}";
        echo "{$sel} .olk-codice-riga{display:flex;flex-wrap:wrap;align-items:center;justify-content:" . ( $centro ? 'center' : 'flex-start' ) . ';gap:.75rem}';
        echo "{$sel} .olk-codice{display:inline-block;max-width:100%;padding:.45rem 1rem;border:2px dashed color-mix(in srgb, currentColor 42%, transparent);border-radius:calc(var(--olk-rp) * .6);font-family:var(--olo-font-family-mono, ui-monospace, SFMono-Regular, Menlo, Consolas, monospace);font-size:1.25rem;font-weight:700;line-height:1.2;letter-spacing:.08em;overflow-wrap:anywhere;-webkit-user-select:all;user-select:all}";
        echo "{$sel} .olk-copia,{$sel} .olk-copia:hover{background:transparent}";
        echo "{$sel} .olk-copia{display:inline-flex;align-items:center;gap:.45em;min-height:2.75rem;margin:0;padding:.5rem 1rem;border:1.5px solid color-mix(in srgb, currentColor 32%, transparent);border-radius:var(--olo-btn-border-radius, .5rem);color:inherit;font:inherit;font-size:.9375rem;font-weight:600;line-height:1.2;text-transform:none;letter-spacing:normal;cursor:pointer;-webkit-appearance:none;appearance:none;box-shadow:none;transition:background-color .2s ease}";
        echo "{$sel} .olk-copia:hover{background:color-mix(in srgb, currentColor 8%, transparent);color:inherit}";
        echo "{$sel} .olk-copia svg{display:block;width:1.05em;height:1.05em;flex:none;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}";
        if ( $builder ) {
            echo "{$sel} .olk-note{display:flex;flex-direction:column;gap:.5rem;width:100%;max-width:36rem}";
            echo "{$sel} .olk-nota{box-sizing:border-box;margin:0;padding:.75rem 1rem;font-size:14px;line-height:1.45;text-align:left;opacity:.8;border:1px dashed color-mix(in srgb, currentColor 35%, transparent);border-radius:.5rem}";
        }
        // «Riduci movimento»: niente scosse né rullo animato; il gancio è subito aperto, il premio compare in dissolvenza.
        echo '@media (prefers-reduced-motion:reduce){' . "{$sel} .olk-rullo,{$sel} .olk-freccia,{$sel} .olk-ruota,{$sel} .olk-apri,{$sel} .olk-link,{$sel} .olk-copia{transition:none}{$sel}.is-errore .olk-lucchetto{animation:none}{$sel}.is-aprendo .olk-gancio{animation:none;transform:translateY(-13%) rotate(-15deg)}{$sel}.is-aprendo .olk-icona{animation:none}{$sel} .olk-premio{animation-name:olk-appare}" . '}';
        // Simboli: famiglia e peso dal controllo tipografia; la misura segue la ruota (un valore scelto fa da tetto).
        $fam_sim = $this->resolve_font_family( $s['simbolo_font_family'] );
        $fw_sim  = $this->font_weight_css( $s['simbolo_font_weight'] );
        if ( '' !== $fam_sim || '' !== $fw_sim ) {
            echo "{$sel} .olk-cella{" . ( '' !== $fam_sim ? 'font-family:' . $fam_sim . ';' : '' ) . ( '' !== $fw_sim ? 'font-weight:' . $fw_sim . ';' : '' ) . '}';
        }
        $fs_sim = (float) $s['simbolo_font_size'];
        if ( $fs_sim > 0 ) {
            echo "{$sel}{--olk-fs:min({$fs_sim}px,calc(var(--olk-w) * {$tetto}))}";
        }
        foreach ( [ 'tablet' => 960, 'mobile' => 480 ] as $disp => $px ) {
            $v = (float) ( $s[ 'simbolo_font_size_' . $disp ] ?? 0 );
            if ( $v > 0 ) {
                echo '@media (max-width:' . $px . 'px){' . "{$sel}{--olk-fs:min({$v}px,calc(var(--olk-w) * {$tetto}))}" . '}';
            }
        }
        echo $this->css_testo( $s, 'enigma', "{$sel} .olk-enigma" );
        echo $this->css_testo( $s, 'messaggio', "{$sel} .olk-msg" );
        echo '</style>';

        $cfg = [
            'simboli'   => array_values( $mostrati ),
            'impronta'  => $impronta,
            'sale'      => $sale,
            'chiave'    => $chiave,
            'premio'    => $cifrato,
            'misura'    => $misura,
            'unita'     => $unita,
            'dopo'      => $dopo,
            'ricorda'   => $ricorda,
            'locale'    => 'olo_lucchetto_' . $chiave,
            'errore'    => $errore,
            'copiato'   => $t_copiato,
            /* translators: %d: numero dei tentativi sbagliati */
            'tentativi' => __( 'Tentativi: %d', 'olobuilder' ),
            'ecco'      => __( 'Ecco un indizio.', 'olobuilder' ),
        ];
        if ( $norme !== $mostrati ) {
            $cfg['norme'] = array_values( $norme ); // solo i personalizzati scritti in minuscolo: per il confronto
        }

        $su   = '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 15l6-6 6 6"/></svg>';
        $giu  = '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 9l6 6 6-6"/></svg>';
        $in_b = function ( $chiave_campo ) use ( $builder ) {
            return $builder ? ' data-olo-editable="' . $chiave_campo . '"' : '';
        };

        echo '<div id="' . esc_attr( $uid ) . '" class="' . esc_attr( $classi ) . '" data-olk="' . esc_attr( wp_json_encode( $cfg ) ) . '">';
        echo '<div class="olk-pannello">';
        if ( '' !== trim( strip_tags( $enigma ) ) ) {
            echo '<div class="olk-enigma">' . $enigma . '</div>';
        }

        // ── Il lucchetto ──
        echo '<div class="olk-lucchetto">';
        if ( $disegno ) {
            echo '<svg class="olk-gancio" viewBox="0 0 100 ' . $vb_alto . '" preserveAspectRatio="none" aria-hidden="true" focusable="false">'
                . '<path class="olk-g1" d="' . $tracciato . '" stroke-width="' . $spess . '"/>'
                . '<path class="olk-g2" d="' . $tracciato . '" stroke-width="' . round( $spess * 0.7, 2 ) . '"/>'
                . '<path class="olk-g3" d="' . $tracciato . '" stroke-width="' . round( $spess * 0.2, 2 ) . '" transform="translate(' . round( -$spess * 0.16, 2 ) . ' ' . round( -$spess * 0.12, 2 ) . ')"/>'
                . '</svg>';
        } elseif ( '' !== $icona ) {
            echo '<span class="olk-icona" aria-hidden="true">' . $this->render_icon_html( $icona, 3, 'aria-hidden="true"' ) . '</span>';
        }
        echo '<div class="olk-corpo">';
        echo '<div class="olk-ruote" role="group" aria-label="' . esc_attr__( 'Combinazione', 'olobuilder' ) . '" data-olo-own-drag>';
        for ( $r = 0; $r < $n_ruote; $r++ ) {
            /* translators: 1: numero della ruota, 2: numero delle ruote */
            $et = sprintf( __( 'Ruota %1$d di %2$d', 'olobuilder' ), $r + 1, $n_ruote );
            echo '<div class="olk-col">';
            echo '<button type="button" class="olk-freccia olk-su" tabindex="-1" aria-hidden="true" data-olo-interactive>' . $su . '</button>';
            echo '<div class="olk-ruota" role="spinbutton" tabindex="0" aria-label="' . esc_attr( $et ) . '" aria-valuemin="0" aria-valuemax="' . ( count( $mostrati ) - 1 ) . '" aria-valuenow="0" aria-valuetext="' . esc_attr( $mostrati[0] ) . '" aria-describedby="' . esc_attr( $uid ) . '-aiuto" data-olo-interactive>'
                . '<span class="olk-rullo" aria-hidden="true"><span class="olk-cella">' . esc_html( $mostrati[0] ) . '</span></span></div>';
            echo '<button type="button" class="olk-freccia olk-giu" tabindex="-1" aria-hidden="true" data-olo-interactive>' . $giu . '</button>';
            echo '</div>';
        }
        echo '</div></div></div>';
        echo '<p class="olk-sr" id="' . esc_attr( $uid ) . '-aiuto">' . esc_html__( 'Frecce su e giù per cambiare il simbolo, destra e sinistra per passare a un\'altra ruota, Invio per aprire. Puoi anche scrivere il simbolo.', 'olobuilder' ) . '</p>';

        // ── Pulsanti, stato, indizio ──
        echo '<div class="olk-comandi">';
        echo '<div class="olk-azioni">';
        echo '<button type="button" class="olk-apri" data-olo-interactive><span' . $in_b( 'testo_pulsante' ) . '>' . esc_html( $t_apri ) . '</span></button>';
        if ( '' !== $indizio ) {
            echo '<button type="button" class="olk-indizio-btn" aria-expanded="false" aria-controls="' . esc_attr( $uid ) . '-indizio" data-olo-interactive><span' . $in_b( 'testo_indizio' ) . '>' . esc_html( $t_indizio ) . '</span></button>';
        }
        echo '</div>';
        echo '<p class="olk-stato" role="status" aria-live="polite"></p>';
        if ( $conta ) {
            echo '<p class="olk-tentativi" aria-hidden="true" hidden></p>';
        }
        if ( '' !== $indizio ) {
            echo '<div class="olk-indizio" id="' . esc_attr( $uid ) . '-indizio" hidden><span class="olk-indizio-et">' . esc_html__( 'Indizio', 'olobuilder' ) . '</span><p>' . nl2br( esc_html( $indizio ) ) . '</p></div>';
        }
        echo '</div>';

        // ── Il premio (riempito dallo script con il testo decifrato) ──
        echo '<div class="olk-premio" id="' . esc_attr( $uid ) . '-premio" role="group" aria-labelledby="' . esc_attr( $uid ) . '-msg" tabindex="-1" hidden>';
        echo '<p class="olk-msg" id="' . esc_attr( $uid ) . '-msg"></p>';
        if ( '' !== $codice ) {
            echo '<div class="olk-codice-riga" hidden><span class="olk-codice"></span>'
                . '<button type="button" class="olk-copia" data-olo-interactive><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v1"/></svg><span class="olk-copia-testo">' . esc_html( $t_copia ) . '</span></button></div>';
        }
        if ( $con_btn ) {
            echo '<a class="olk-link" href="#" hidden data-olo-interactive></a>';
        }
        echo '<span class="olk-sr olk-eco" aria-live="polite"></span>';
        echo '</div>';

        // ── Note del builder ──
        if ( $builder ) {
            $nota = '';
            if ( $pochi_simboli ) {
                $nota .= '<p class="olk-nota">' . esc_html__( 'Scrivi almeno due simboli personalizzati, separati da una virgola: per ora le ruote mostrano le cifre.', 'olobuilder' ) . '</p>';
            }
            if ( ! $quanti ) {
                $nota .= '<p class="olk-nota">' . esc_html__( 'Scrivi la combinazione nel Contenuto, da 3 a 6 simboli: finché manca, sul sito il lucchetto non si vede.', 'olobuilder' ) . '</p>';
            } elseif ( $estranei ) {
                /* translators: %s: i simboli della combinazione che le ruote non hanno */
                $nota .= '<p class="olk-nota">' . esc_html( sprintf( __( 'Nella combinazione ci sono simboli che le ruote non hanno: %s. Finché restano, sul sito il lucchetto non si vede.', 'olobuilder' ), implode( ', ', $estranei ) ) ) . '</p>';
            } elseif ( ! $valida ) {
                /* translators: %d: quanti simboli ha la combinazione */
                $nota .= '<p class="olk-nota">' . esc_html( sprintf( __( 'La combinazione deve avere da 3 a 6 simboli, una ruota per simbolo: ora ne ha %d. Finché è così, sul sito il lucchetto non si vede.', 'olobuilder' ), $quanti ) ) . '</p>';
            } else {
                $soluzione = [];
                foreach ( $indici as $i ) {
                    $soluzione[] = $mostrati[ $i ];
                }
                /* translators: %s: la combinazione, coi simboli separati da spazi */
                $nota .= '<p class="olk-nota">' . esc_html( sprintf( __( 'Nel builder la combinazione è %s: provala qui. Sul sito questa nota non c\'è.', 'olobuilder' ), implode( ' ', $soluzione ) ) ) . '</p>';
            }
            echo '<div class="olk-note">' . $nota . '</div>';
        }
        echo '</div></div>';

        // Lo script avvia ogni istanza della pagina una volta sola.
        echo '<script>' . $this->script() . '[].forEach.call(document.querySelectorAll("[data-olk]"),function(x){window.oloLucchetto(x);});</script>';
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }

    /**
     * Le ruote, la prova della combinazione e il premio. Va in OGNI istanza (definita una volta sola, «if(!window…)»):
     * una sezione caricata dopo può eseguire il suo script prima di quello della prima istanza. Niente «&» nello
     * script (wptexturize lo riscrive fra un «<» e un «>»): condizioni annidate, «% 256» al posto della maschera di
     * bit. Ogni istanza si avvia una volta (data-olk-pronto), anche quando lo script rigira (anteprima del builder).
     */
    private function script() {
        return <<<'JS'
if(!window.oloLucchetto){window.oloLucchetto=function(el){
if(!el){return;}
if(el.getAttribute("data-olk-pronto")==="1"){return;}
el.setAttribute("data-olk-pronto","1");
var C={};try{C=JSON.parse(el.getAttribute("data-olk")||"{}");}catch(e){}
var S=C.simboli||[],N=C.norme||S,n=S.length;if(!n){return;}
var riduci=false;try{riduci=window.matchMedia("(prefers-reduced-motion: reduce)").matches;}catch(e){}
var pannello=el.querySelector(".olk-pannello"),stato=el.querySelector(".olk-stato"),conta=el.querySelector(".olk-tentativi"),apriBtn=el.querySelector(".olk-apri"),indBtn=el.querySelector(".olk-indizio-btn"),indBox=el.querySelector(".olk-indizio"),premio=el.querySelector(".olk-premio"),eco=el.querySelector(".olk-eco"),copia=el.querySelector(".olk-copia");
var copie=n<8?5:3,meta=Math.floor(copie/2),grande=Math.max(2,Math.min(5,Math.round(n/3)));
var ruote=[],aperto=false,errori=0,tErr=0,tAnn=0,scorso=0;
function mod(a,b){return ((a%b)+b)%b;}
function utf8(s){s=String(s);if(window.TextEncoder){return new TextEncoder().encode(s);}var b=unescape(encodeURIComponent(s)),a=[],i;for(i=0;i<b.length;i++){a.push(b.charCodeAt(i));}return a;}
function fnv(s){var b=utf8(s),h=0x811c9dc5,i;for(i=0;i<b.length;i++){h^=b[i];h=Math.imul(h,16777619)>>>0;}return h>>>0;}
function hex(h){return ("0000000"+(h>>>0).toString(16)).slice(-8);}
function decifra(b64,k){
var bin="",i,ks=0,kh=hex(k),out,txt="",o=null;
try{bin=atob(String(b64||""));}catch(e){return null;}
if(!bin){return null;}
out=new Uint8Array(bin.length);
for(i=0;i<bin.length;i++){if(i%4===0){ks=fnv(kh+":"+(i/4));}out[i]=bin.charCodeAt(i)^((ks>>>(24-8*(i%4)))%256);}
try{if(window.TextDecoder){txt=new TextDecoder("utf-8",{fatal:true}).decode(out);}else{var s="";for(i=0;i<out.length;i++){s+=String.fromCharCode(out[i]);}txt=decodeURIComponent(escape(s));}}catch(e){return null;}
try{o=JSON.parse(txt);}catch(e){return null;}
if(!o){return null;}if(o.ok!==1){return null;}
return o;
}
function posa(w,anim){
if(riduci){anim=false;}
if(!anim){w.rullo.style.transition="none";}
w.rullo.style.setProperty("--p",String(w.p));
if(!anim){void w.rullo.offsetHeight;w.rullo.style.transition="";}
}
function aria(w){w.el.setAttribute("aria-valuenow",String(w.v));w.el.setAttribute("aria-valuetext",String(S[w.v]));}
function calma(){
el.classList.remove("is-errore");
if(stato){if(stato.classList.contains("is-errore")){stato.textContent="";stato.classList.remove("is-errore");}}
}
function gira(w,d){
if(aperto){return;}if(!d){return;}
var q=meta*n+w.v;if(q!==w.p){w.p=q;posa(w,false);}
w.v=mod(w.v+d,n);w.p+=d;posa(w,true);aria(w);calma();
}
function vaiA(w,j){var d=j-w.v;if(d>n/2){d-=n;}if(d<-n/2){d+=n;}gira(w,d);}
function fuoco(j){if(j<0){return;}if(j>=ruote.length){return;}try{ruote[j].el.focus();}catch(e){}}
function annuncia(t,extra,cls){
if(!stato){return;}
clearTimeout(tAnn);stato.textContent="";stato.className="olk-stato"+(cls?" "+cls:"");
tAnn=setTimeout(function(){stato.textContent=t;if(extra){var sp=document.createElement("span");sp.className="olk-sr";sp.textContent=" "+extra;stato.appendChild(sp);}},40);
}
function indizio(v){if(!indBox){return;}indBox.hidden=!v;if(indBtn){indBtn.setAttribute("aria-expanded",v?"true":"false");}}
function sicuro(u){
u=String(u||"").replace(/[\u0000- \u007f]/g,"");if(!u){return "";}
var m=/^([a-z][a-z0-9+.\-]*):/i.exec(u),p;
if(m){p=m[1].toLowerCase();if(p!=="http"){if(p!=="https"){if(p!=="mailto"){if(p!=="tel"){return "";}}}}}
return u;
}
function riempi(d){
var pieno=false,m=el.querySelector(".olk-msg"),riga=el.querySelector(".olk-codice-riga"),cod=el.querySelector(".olk-codice"),a=el.querySelector(".olk-link"),righe,i,u;
if(m){m.textContent="";righe=String(d.m||"").split(/\r?\n/);for(i=0;i<righe.length;i++){if(i){m.appendChild(document.createElement("br"));}m.appendChild(document.createTextNode(righe[i]));}m.hidden=!d.m;if(d.m){pieno=true;}}
if(riga){if(d.c){if(cod){cod.textContent=String(d.c);}riga.hidden=false;pieno=true;}else{riga.hidden=true;}}
if(a){u=d.u?sicuro(String(d.u)):"";if(d.t){if(u){a.textContent=String(d.t);a.setAttribute("href",u);a.hidden=false;pieno=true;}}}
return pieno;
}
function apri(d,k,subito){
aperto=true;
var ind=d.i||[],i,w,v;
for(i=0;i<ruote.length;i++){
w=ruote[i];v=+ind[i];
if(subito){if(v>=0){if(v<n){w.v=v;w.p=meta*n+v;posa(w,false);aria(w);}}}
w.el.setAttribute("aria-disabled","true");if(w.su){w.su.disabled=true;}if(w.giu){w.giu.disabled=true;}
}
clearTimeout(tErr);clearTimeout(tAnn);el.classList.remove("is-errore");
if(stato){stato.textContent="";stato.className="olk-stato";}
el.classList.add(subito?"is-aperto-subito":"is-aprendo");el.classList.add("is-aperto");
var pieno=riempi(d);
if(premio){if(pieno){premio.hidden=false;}}
if(subito){el.classList.add("is-fatto");return;}
setTimeout(function(){
el.classList.add("is-fatto");
if(premio){if(pieno){try{premio.focus();}catch(x){}}}
},450);
if(C.ricorda){try{localStorage.setItem(C.locale,hex(k));}catch(x){}}
}
function sbaglia(){
errori++;
var extra="",auto=false,msg=String(C.errore||"");
if(conta){conta.textContent=String(C.tentativi||"").replace("%d",String(errori));conta.hidden=false;extra=conta.textContent;}
if(C.dopo>0){if(errori>=C.dopo){if(indBox){if(indBox.hidden){indizio(true);auto=true;}}}}
if(auto){msg=msg+(msg?" ":"")+String(C.ecco||"");}
annuncia(msg,extra,"is-errore");
el.classList.remove("is-errore");void el.offsetWidth;el.classList.add("is-errore");
clearTimeout(tErr);tErr=setTimeout(function(){el.classList.remove("is-errore");},650);
}
function prova(){
if(aperto){return;}
var t=[],i,tent,k=0,dati=null;
for(i=0;i<ruote.length;i++){t.push(N[ruote[i].v]);}
tent=t.join("|");
if(C.impronta){if(hex(fnv(tent+C.sale))===C.impronta){k=fnv(tent+"#"+C.chiave);dati=decifra(C.premio,k);}}
if(dati){apri(dati,k,false);}else{sbaglia();}
}
[].forEach.call(el.querySelectorAll(".olk-col"),function(col){
var r=col.querySelector(".olk-ruota"),rullo=col.querySelector(".olk-rullo");
if(!r){return;}if(!rullo){return;}
var c,j,cel;
rullo.textContent="";
for(c=0;c<copie;c++){for(j=0;j<n;j++){cel=document.createElement("span");cel.className="olk-cella";cel.textContent=String(S[j]);rullo.appendChild(cel);}}
var w={i:ruote.length,el:r,rullo:rullo,v:0,p:meta*n,su:col.querySelector(".olk-su"),giu:col.querySelector(".olk-giu")};
ruote.push(w);posa(w,false);aria(w);
r.addEventListener("keydown",function(e){
if(e.ctrlKey||e.metaKey||e.altKey){return;}
var k=e.key,x,t,q,primo=-1,quanti=0;
if(k==="ArrowUp"){e.preventDefault();gira(w,1);return;}
if(k==="ArrowDown"){e.preventDefault();gira(w,-1);return;}
if(k==="PageUp"){e.preventDefault();gira(w,grande);return;}
if(k==="PageDown"){e.preventDefault();gira(w,-grande);return;}
if(k==="Home"){e.preventDefault();vaiA(w,0);return;}
if(k==="End"){e.preventDefault();vaiA(w,n-1);return;}
if(k==="ArrowRight"){e.preventDefault();fuoco(w.i+1);return;}
if(k==="ArrowLeft"){e.preventDefault();fuoco(w.i-1);return;}
if(k==="Enter"){e.preventDefault();prova();return;}
if(k==="Backspace"){e.preventDefault();e.stopPropagation();fuoco(w.i-1);return;}
if(String(k).length!==1){return;}
if(aperto){return;}
t=String(k).toUpperCase();
for(q=1;q<=n;q++){x=mod(w.v+q,n);if(String(N[x]).indexOf(t)===0){quanti++;if(primo<0){primo=x;}}}
if(primo<0){return;}
e.preventDefault();
vaiA(w,primo);
if(quanti===1){fuoco(w.i+1);}
});
var acc=0,mosso=0,drag=null;
r.addEventListener("wheel",function(e){
if(aperto){return;}
if(scorso>mosso){return;}
e.preventDefault();
var dy=e.deltaY;if(e.deltaMode===1){dy*=16;}if(e.deltaMode===2){dy*=120;}
acc+=dy;
if(Math.abs(acc)>=40){gira(w,acc>0?1:-1);acc=0;}
},{passive:false});
r.addEventListener("pointerdown",function(e){
if(aperto){return;}
if(e.pointerType==="mouse"){if(e.button!==0){return;}}
var q=meta*n+w.v,f=w.rullo.firstChild,h=f?f.getBoundingClientRect().height:0;
if(!h){return;}
if(q!==w.p){w.p=q;posa(w,false);}
drag={id:e.pointerId,y:e.clientY,p:w.p,h:h,via:false};
try{r.setPointerCapture(e.pointerId);}catch(x){}
});
r.addEventListener("pointermove",function(e){
if(e.pointerType==="mouse"){mosso=Date.now();}
if(!drag){return;}if(e.pointerId!==drag.id){return;}
var dy=e.clientY-drag.y,p,v;
if(!drag.via){if(Math.abs(dy)<5){return;}drag.via=true;r.classList.add("is-trascina");w.rullo.style.transition="none";calma();}
e.preventDefault();
p=Math.max(1,Math.min(copie*n-2,drag.p-dy/drag.h));
w.p=p;w.rullo.style.setProperty("--p",String(p));
v=mod(Math.round(p),n);if(v!==w.v){w.v=v;aria(w);}
});
function lascia(e){
if(!drag){return;}if(e.pointerId!==drag.id){return;}
var d=drag;drag=null;r.classList.remove("is-trascina");
if(!d.via){return;}
w.rullo.style.transition="";
w.p=Math.round(w.p);w.v=mod(w.p,n);posa(w,true);aria(w);
}
r.addEventListener("pointerup",lascia);r.addEventListener("pointercancel",lascia);r.addEventListener("lostpointercapture",lascia);
function freccia(b,d){
if(!b){return;}
var t1=0,t2=0,daPunt=false;
function stop(){clearTimeout(t1);clearInterval(t2);}
b.addEventListener("pointerdown",function(e){
if(e.pointerType==="mouse"){if(e.button!==0){return;}}
e.preventDefault();daPunt=true;stop();gira(w,d);
t1=setTimeout(function(){t2=setInterval(function(){gira(w,d);},110);},450);
});
b.addEventListener("pointerup",stop);b.addEventListener("pointerleave",stop);b.addEventListener("pointercancel",stop);
b.addEventListener("click",function(e){e.preventDefault();if(daPunt){daPunt=false;return;}gira(w,d);});
}
freccia(w.su,1);freccia(w.giu,-1);
});
if(!ruote.length){return;}
window.addEventListener("scroll",function(){scorso=Date.now();},{passive:true});
if(apriBtn){apriBtn.addEventListener("click",function(){prova();});}
if(indBtn){indBtn.addEventListener("click",function(){indizio(indBox?indBox.hidden:false);});}
if(copia){
var tc=copia.querySelector(".olk-copia-testo")||copia,orig=tc.textContent,tC=0;
copia.addEventListener("click",function(){
var cod=el.querySelector(".olk-codice"),v=cod?cod.textContent:"";
if(!v){return;}
function fatto(){tc.textContent=String(C.copiato||orig);if(eco){eco.textContent="";eco.textContent=String(C.copiato||"");}clearTimeout(tC);tC=setTimeout(function(){tc.textContent=orig;if(eco){eco.textContent="";}},2000);}
function ripiego(){if(!cod){return;}try{var rg=document.createRange();rg.selectNodeContents(cod);var sl=window.getSelection();sl.removeAllRanges();sl.addRange(rg);if(document.execCommand("copy")){fatto();}}catch(x){}}
if(navigator.clipboard){if(navigator.clipboard.writeText){navigator.clipboard.writeText(v).then(fatto,ripiego);return;}}
ripiego();
});
}
function adatta(){
if(!pannello){return;}
var cs=getComputedStyle(pannello),lw=pannello.clientWidth-(parseFloat(cs.paddingLeft)||0)-(parseFloat(cs.paddingRight)||0),m=+C.misura||60,u=+C.unita||5;
if(!(lw>0)){return;}
el.style.setProperty("--olk-w",Math.max(24,Math.floor(Math.min(m,lw/u)))+"px");
}
adatta();
if(window.ResizeObserver){try{new ResizeObserver(function(){adatta();}).observe(pannello);}catch(x){window.addEventListener("resize",adatta);}}else{window.addEventListener("resize",adatta);}
if(C.ricorda){var sv=null;try{sv=localStorage.getItem(C.locale);}catch(x){sv=null;}if(sv){if(/^[0-9a-f]{8}$/.test(sv)){var d0=decifra(C.premio,parseInt(sv,16));if(d0){apri(d0,0,true);}}}}
};}
JS;
    }
}
