<?php
/**
 * Tile Tour 360 — foto sferiche equirettangolari da guardare trascinando, con le scene collegate e i punti.
 *
 * Disegno in WebGL1 senza librerie: un triangolo che copre la tela e uno shader che per ogni pixel calcola la
 * direzione (direzione, inclinazione, campo visivo, rapporto) e campiona la foto equirettangolare
 * (u = 0.5 + atan(x,z)/2π con fract per la cucitura, v = 0.5 − asin(y)/π); texture NPOT con CLAMP_TO_EDGE e
 * LINEAR, senza mipmap. Il campo visivo è verticale, con l'orizzontale fermo a 120° nelle viste larghe (21:9).
 * La foto grande va ai computer; la foto leggera ai telefoni e alle schede con texture massima sotto 4096, e
 * quando la grande supera gl.MAX_TEXTURE_SIZE (senza foto leggera la grande si rimpicciolisce in una canvas 2D).
 * devicePixelRatio fino a 2; si disegna solo quando qualcosa cambia (trascinamento, inerzia, rotazione automatica,
 * dissolvenza) e mai fuori schermo (IntersectionObserver); le foto si caricano quando la tile si avvicina allo
 * schermo, poi si precaricano le scene collegate. Senza WebGL (o con una foto di un altro dominio senza CORS) la
 * foto fa da sfondo che scorre in orizzontale, con gli stessi gesti e gli stessi punti.
 * Gesti: trascinamento con inerzia (mouse e un dito, touch-action:none solo sulla tela), inclinazione fino a ±85°;
 * zoom con due dita, Ctrl + rotellina (la rotellina da sola fa scorrere la pagina), + e −, pulsanti; tastiera
 * sulla vista (tabindex 0, role="application", aria-label col titolo della scena e le istruzioni).
 * Punti: pulsanti sopra la tela proiettati a ogni fotogramma (trasparenti dietro e fuori dal campo, ma raggiungibili
 * col Tab: col fuoco la vista si gira verso di loro), etichetta al passaggio e al fuoco (sempre sui touch).
 * «Scena» → dissolvenza di 450 ms con un passo verso il punto (niente con «riduci movimento»), la scena nuova parte
 * dalla sua direzione; «Informazioni» → riquadro (Esc, clic fuori, ×; il fuoco torna al punto); «Collegamento» → <a>.
 * Nel builder: senza foto una vista d'esempio disegnata nel browser, il mirino al centro con direzione e inclinazione
 * e, con un clic sulla foto, quelle del punto cliccato (per piazzare i punti); senza scene un segnaposto.
 * Nata per i temi della serie 6 (15 ott 2026). Config: src/config/elements/tour360.js.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Tour360_Tile extends Olobuild_Tile_Base {

    protected $type     = 'tour360';
    protected $name     = 'Tour 360°';
    protected $icon     = 'dashicons-image-rotate';
    protected $category = 'media';

    /** Le icone dei punti quando il campo «Icona» è vuoto (Lucide: tratto uguale ai comandi della vista). */
    const ICONE = [
        'scena' => 'arrow-big-up',
        'info'  => 'plus',
        'link'  => 'arrow-up-right',
    ];

    protected $defaults = [
        // contenuto
        'scene'                      => [
            [ 'id' => 'ingresso', 'titolo' => 'Ingresso', 'immagine' => '', 'immagine_leggera' => '', 'anteprima' => '', 'yaw' => 0, 'pitch' => 0, 'descrizione' => 'La porta si apre su un corridoio luminoso col pavimento in rovere.' ],
            [ 'id' => 'soggiorno', 'titolo' => 'Soggiorno', 'immagine' => '', 'immagine_leggera' => '', 'anteprima' => '', 'yaw' => 0, 'pitch' => 0, 'descrizione' => 'Il divano guarda la vetrata sul giardino; la cucina è a vista, dietro la penisola.' ],
        ],
        'punti'                      => [
            [ 'id' => 'p-1', 'scena' => 'ingresso', 'yaw' => 32, 'pitch' => -6, 'tipo' => 'scena', 'destinazione' => 'soggiorno', 'titolo' => 'Vai al soggiorno', 'testo' => '', 'url' => '', 'icona' => '' ],
            [ 'id' => 'p-2', 'scena' => 'ingresso', 'yaw' => -58, 'pitch' => -18, 'tipo' => 'info', 'destinazione' => '', 'titolo' => 'Pavimento in rovere', 'testo' => 'Doghe larghe 20 cm, oliate a mano: scaldano la casa anche d\'inverno.', 'url' => '', 'icona' => '' ],
            [ 'id' => 'p-3', 'scena' => 'soggiorno', 'yaw' => 180, 'pitch' => -6, 'tipo' => 'scena', 'destinazione' => 'ingresso', 'titolo' => 'Torna all\'ingresso', 'testo' => '', 'url' => '', 'icona' => '' ],
            [ 'id' => 'p-4', 'scena' => 'soggiorno', 'yaw' => 64, 'pitch' => 8, 'tipo' => 'info', 'destinazione' => '', 'titolo' => 'Vetrata sul giardino', 'testo' => 'Tre metri di vetro basso emissivo, esposti a sud: luce piena fino al tramonto.', 'url' => '', 'icona' => '' ],
        ],
        'scena_iniziale'             => '',
        'fov'                        => 75,
        'zoom'                       => true,
        'mostra_titolo'              => true,
        'elenco_scene'               => true,
        'schermo_intero'             => true,
        'autorotazione'              => true,
        'velocita'                   => 3,
        'ripresa'                    => 6,
        'mostra_suggerimento'        => true,
        'testo_suggerimento'         => 'Trascina per guardarti intorno',
        'testo_suggerimento_touch'   => 'Scorri col dito per guardarti intorno',
        'testo_caricamento'          => 'Carico la vista…',
        // stile
        'proporzione'                => '16/9',
        'proporzione_mobile'         => '4/5',
        'raggio'                     => [ 'tl' => 14, 'tr' => 14, 'br' => 14, 'bl' => 14 ],
        'bordo'                      => [],
        'ombra'                      => 'none',
        'ombra_h'                    => '0',
        'ombra_v'                    => '4',
        'ombra_blur'                 => '10',
        'ombra_spread'               => '0',
        'ombra_color'                => 'rgba(0,0,0,0.15)',
        'ombra_inset'                => false,
        'stile_punto'                => 'anello',
        'colore_punto'               => '',
        'misura_punto'               => 40,
        'sfondo_etichetta'           => '',
        'colore_etichetta'           => '',
        'etichetta_font_family'      => '',
        'etichetta_font_size'        => 13,
        'etichetta_font_size_tablet' => '',
        'etichetta_font_size_mobile' => '',
        'etichetta_font_weight'      => '600',
        'etichetta_color'            => '',
        'sfondo_pannello'            => '',
        'colore_pannello'            => '',
        'titolo_font_family'         => 'heading',
        'titolo_font_size'           => 19,
        'titolo_font_size_tablet'    => '',
        'titolo_font_size_mobile'    => '',
        'titolo_font_weight'         => '600',
        'titolo_color'               => '',
        'testo_font_family'          => '',
        'testo_font_size'            => 15,
        'testo_font_size_tablet'     => '',
        'testo_font_size_mobile'     => '',
        'testo_font_weight'          => '400',
        'testo_line_height'          => 1.55,
        'testo_color'                => '',
        'sfondo_comandi'             => '',
        'colore_comandi'             => '',
    ];

    /** Uid già dati in questa richiesta: lo stesso nodo reso due volte (un widget ripetuto) resta distinto. */
    private static $uid_usati = [];

    public function get_controls() {
        return [];
    }

    /** Un valore d'immagine o d'indirizzo: stringa, oppure l'oggetto del selettore ({url}). */
    private static function url_di( $v ) {
        if ( is_array( $v ) ) {
            $v = $v['url'] ?? '';
        }
        return trim( (string) $v );
    }

    /** La proporzione «L/A» (anche scritta «L:A»), o quella data. */
    private static function proporzione( $v, $predefinita ) {
        $v = str_replace( [ ':', ' ' ], [ '/', '' ], trim( (string) $v ) );
        if ( preg_match( '/^(\d+(?:\.\d+)?)\/(\d+(?:\.\d+)?)$/', $v, $m ) ) {
            if ( (float) $m[1] > 0 ) {
                if ( (float) $m[2] > 0 ) {
                    return $v;
                }
            }
        }
        return $predefinita;
    }

    /** Una direzione in gradi, riportata fra −180 e 180. */
    private static function yaw( $v ) {
        $f = is_numeric( $v ) ? (float) $v : 0.0;
        $f = fmod( $f + 180.0, 360.0 );
        if ( $f < 0 ) {
            $f += 360.0;
        }
        return round( $f - 180.0, 2 );
    }

    /** Un'inclinazione in gradi, fra −$max e $max. */
    private static function pitch( $v, $max ) {
        $f = is_numeric( $v ) ? (float) $v : 0.0;
        return round( max( -$max, min( $max, $f ) ), 2 );
    }

    private static function minuscolo( $t ) {
        $t = trim( (string) $t );
        return function_exists( 'mb_strtolower' ) ? mb_strtolower( $t, 'UTF-8' ) : strtolower( $t );
    }

    /**
     * La scena indicata da un punto o da «Scena iniziale», fra TUTTE le scene del Contenuto (le posizioni sono
     * quelle dell'elenco dell'inspector): per id (i temi), per titolo, o per numero (1 = la prima). -1 se nessuna.
     */
    private static function trova_scena( $rif, $tutte ) {
        $rif = trim( (string) $rif );
        if ( '' === $rif ) {
            return -1;
        }
        foreach ( $tutte as $k => $sc ) {
            if ( '' !== $sc['id'] && $sc['id'] === $rif ) {
                return $k;
            }
        }
        $basso = self::minuscolo( $rif );
        foreach ( $tutte as $k => $sc ) {
            if ( '' !== $sc['titolo'] && self::minuscolo( $sc['titolo'] ) === $basso ) {
                return $k;
            }
        }
        if ( ctype_digit( $rif ) ) {
            $k = (int) $rif - 1;
            if ( isset( $tutte[ $k ] ) ) {
                return $k;
            }
        }
        return -1;
    }

    /**
     * Il testo di un punto «Informazioni». Senza tag: testo semplice (ogni carattere resta com'è, anche «<»), una
     * riga vuota = nuovo paragrafo, un a capo = <br>. Coi tag: solo quelli del testo semplice (p, br, grassetto,
     * corsivo, elenchi, link); senza paragrafi suoi, gli a capo valgono come sopra.
     */
    private static function testo_ricco( $t ) {
        $t = trim( str_replace( "\r", '', (string) $t ) );
        if ( '' === $t ) {
            return '';
        }
        $html = (bool) preg_match( '#</?[a-z][a-z0-9]*(?:\s[^<>]*)?/?>#i', $t );
        if ( $html ) {
            $t = trim(
                wp_kses(
                    $t,
                    [
                        'p'      => [],
                        'br'     => [],
                        'strong' => [],
                        'b'      => [],
                        'em'     => [],
                        'i'      => [],
                        'ul'     => [],
                        'ol'     => [],
                        'li'     => [],
                        'a'      => [ 'href' => true, 'target' => true, 'rel' => true ],
                    ]
                )
            );
            if ( preg_match( '#<(?:p|ul|ol)\b#i', $t ) ) {
                return $t;
            }
        }
        $out = '';
        foreach ( preg_split( '/\n\s*\n/', $t ) as $par ) {
            $par = trim( $par );
            if ( '' !== $par ) {
                $out .= '<p>' . nl2br( $html ? $par : esc_html( $par ), false ) . '</p>';
            }
        }
        return $out;
    }

    /** Un indirizzo di un altro sito (si apre in una nuova scheda)? Pagine del sito, mailto: e tel: no. */
    private static function esterno( $url ) {
        $host = wp_parse_url( $url, PHP_URL_HOST );
        if ( ! $host ) {
            return false;
        }
        $mio = wp_parse_url( home_url(), PHP_URL_HOST );
        return strtolower( (string) $host ) !== strtolower( (string) $mio );
    }

    /**
     * La miniatura di una foto della Libreria media del sito (misura «medium»: per una foto 2:1, 300 × 150), per
     * l'elenco delle scene quando mancano anteprima e foto leggera. Fuori dalla Libreria: '' (si vede il nome della
     * scena su un fondo neutro: scaricare la foto sferica intera per una miniatura costerebbe megabyte).
     */
    private static function miniatura_di( $url ) {
        if ( '' === $url ) {
            return '';
        }
        $up   = wp_get_upload_dir();
        $base = preg_replace( '#^https?:#i', '', (string) ( $up['baseurl'] ?? '' ) );
        if ( '' === $base || false === strpos( preg_replace( '#^https?:#i', '', $url ), $base ) ) {
            return '';
        }
        $id = attachment_url_to_postid( $url );
        if ( $id ) {
            $mini = wp_get_attachment_image_url( $id, 'medium' );
            if ( $mini ) {
                return $mini;
            }
        }
        return '';
    }

    /** Un'icona dei comandi o di un punto senza icona scelta: dal set Lucide, nel tratto dei comandi. */
    private function icona_ui( $nome ) {
        $lib = self::libreria_icone( 'lucide' );
        if ( isset( $lib[ $nome ] ) ) {
            return '<span class="olo-lucide-icon" aria-hidden="true">' . $lib[ $nome ] . '</span>';
        }
        return $this->render_icon_html( $nome, 1, 'aria-hidden="true"' );
    }

    /** L'icona di un punto: quella scelta (se una libreria la conosce), altrimenti quella del suo tipo. */
    private function icona_punto( $p ) {
        if ( '' !== $p['ico'] && '' !== $this->origine_icona( $p['ico'] ) ) {
            return $this->render_icon_html( $p['ico'], 1, 'aria-hidden="true"' );
        }
        return $this->icona_ui( self::ICONE[ $p['k'] ] );
    }

    /**
     * CSS di un testo dal controllo tipografia: famiglia, misura (anche per tablet e telefono), peso, interlinea e
     * colore (già risolto da chi chiama).
     */
    private function css_testo( $s, $k, $selettore, $colore = '' ) {
        $d   = '';
        $fam = $this->resolve_font_family( $s[ $k . '_font_family' ] ?? '' );
        $fs  = (float) ( $s[ $k . '_font_size' ] ?? 0 );
        $fw  = $this->font_weight_css( $s[ $k . '_font_weight' ] ?? '' );
        $lh  = $s[ $k . '_line_height' ] ?? '';
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
        if ( '' !== $colore ) {
            $d .= 'color:' . $colore . ';';
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
        // Per primo: l'id del nodo in resa (chiave_stabile) vale finché non si rende altro.
        $uid = 'o36-' . $this->chiave_stabile( $settings );
        if ( isset( self::$uid_usati[ $uid ] ) ) {
            self::$uid_usati[ $uid ]++;
            $uid .= '-' . self::$uid_usati[ $uid ];
        } else {
            self::$uid_usati[ $uid ] = 1;
        }
        $sel     = '#' . $uid;
        $s       = wp_parse_args( $settings, $this->defaults );
        $builder = ! empty( $s['_builder_mode'] );

        // ── Le scene: tutte (per i riferimenti), poi quelle che si possono mostrare ──
        $tutte = [];
        foreach ( array_values( (array) $s['scene'] ) as $n => $v ) {
            if ( ! is_array( $v ) ) {
                continue;
            }
            $tutte[] = [
                'n'      => $n,
                'id'     => substr( trim( (string) ( $v['id'] ?? '' ) ), 0, 80 ),
                'titolo' => trim( wp_strip_all_tags( (string) ( $v['titolo'] ?? '' ) ) ),
                'img'    => esc_url_raw( self::url_di( $v['immagine'] ?? '' ) ),
                'leg'    => esc_url_raw( self::url_di( $v['immagine_leggera'] ?? '' ) ),
                'ant'    => esc_url_raw( self::url_di( $v['anteprima'] ?? '' ) ),
                'yaw'    => self::yaw( $v['yaw'] ?? 0 ),
                'pitch'  => self::pitch( $v['pitch'] ?? 0, 85 ),
                'desc'   => trim( wp_strip_all_tags( (string) ( $v['descrizione'] ?? '' ) ) ),
            ];
        }
        // Sul sito una scena senza foto non c'è (né i punti che portano lì); nel builder la sostituisce una vista d'esempio.
        $scene = [];
        $posto = [];
        foreach ( $tutte as $k => $sc ) {
            $con_foto = '' !== $sc['img'] || '' !== $sc['leg'];
            if ( ! $con_foto && ! $builder ) {
                $posto[ $k ] = -1;
                continue;
            }
            $sc['demo']  = ! $con_foto;
            $posto[ $k ] = count( $scene );
            $scene[]     = $sc;
        }
        if ( ! $scene && ! $builder ) {
            return '';
        }
        $verso = function ( $rif ) use ( $tutte, $posto ) {
            $k = self::trova_scena( $rif, $tutte );
            return ( $k >= 0 && isset( $posto[ $k ] ) ) ? $posto[ $k ] : -1;
        };

        // ── I punti: ognuno nella sua scena; «Scena» solo verso un'altra scena che c'è ──
        $punti = [];
        foreach ( array_values( (array) $s['punti'] ) as $n => $v ) {
            if ( ! is_array( $v ) ) {
                continue;
            }
            $tipo = (string) ( $v['tipo'] ?? 'scena' );
            $tipo = array_key_exists( $tipo, self::ICONE ) ? $tipo : 'scena';
            $rif  = trim( (string) ( $v['scena'] ?? '' ) );
            $si   = '' === $rif ? ( $posto[0] ?? -1 ) : $verso( $rif );
            if ( $si < 0 ) {
                continue;
            }
            $dest = -1;
            if ( 'scena' === $tipo ) {
                $dest = $verso( $v['destinazione'] ?? '' );
                if ( $dest < 0 || $dest === $si ) {
                    continue;
                }
            }
            $tit = trim( wp_strip_all_tags( (string) ( $v['titolo'] ?? '' ) ) );
            $txt = 'info' === $tipo ? self::testo_ricco( $v['testo'] ?? '' ) : '';
            if ( 'info' === $tipo && '' === $tit && '' === $txt ) {
                continue;
            }
            $url = '';
            if ( 'link' === $tipo ) {
                $url = esc_url_raw( self::url_di( $v['url'] ?? '' ) );
                if ( '' === $url ) {
                    continue;
                }
            }
            $punti[] = [
                's'   => $si,
                'y'   => self::yaw( $v['yaw'] ?? 0 ),
                'p'   => self::pitch( $v['pitch'] ?? 0, 90 ),
                'k'   => $tipo,
                'd'   => $dest,
                'ti'  => $tit,
                'tx'  => $txt,
                'url' => $url,
                'ico' => sanitize_text_field( (string) ( $v['icona'] ?? '' ) ),
            ];
        }
        $ini = 0;
        if ( $scene && '' !== trim( (string) $s['scena_iniziale'] ) ) {
            $ini = max( 0, $verso( $s['scena_iniziale'] ) );
        }

        // ── Comportamento ──
        $fov       = max( 35, min( 100, (float) $s['fov'] ) );
        $zoom      = ! empty( $s['zoom'] );
        $auto      = ! empty( $s['autorotazione'] );
        $vel       = max( 0.5, min( 20, (float) $s['velocita'] ) );
        $rip       = max( 0, min( 60, (float) $s['ripresa'] ) );
        $piu_scene = count( $scene ) > 1;
        $elenco    = ! empty( $s['elenco_scene'] ) && $piu_scene;
        $con_tit   = ! empty( $s['mostra_titolo'] );
        $con_fs    = ! empty( $s['schermo_intero'] );
        $con_info  = false;
        foreach ( $punti as $p ) {
            if ( 'info' === $p['k'] ) {
                $con_info = true;
            }
        }
        $aiuto_m = trim( (string) $s['testo_suggerimento'] );
        $aiuto_t = trim( (string) $s['testo_suggerimento_touch'] );
        if ( '' === $aiuto_t ) {
            $aiuto_t = $aiuto_m;
        }
        if ( '' === $aiuto_m ) {
            $aiuto_m = $aiuto_t;
        }
        $con_aiuto = ! empty( $s['mostra_suggerimento'] ) && '' !== $aiuto_m;
        $caric     = trim( (string) $s['testo_caricamento'] );
        $caric     = '' !== $caric ? $caric : __( 'Carico la vista…', 'olobuilder' );

        // ── Stile: tutto dai token del tema, salvo ciò che è scelto ──
        $chiaro  = 'var(--olo-color-light, #ffffff)';
        $velo    = 'color-mix(in srgb, var(--olo-color-dark, #14161c) 72%, transparent)';
        $mp      = max( 28, min( 56, (int) $s['misura_punto'] ) );
        $stile_p = in_array( (string) $s['stile_punto'], [ 'anello', 'pieno', 'icona' ], true ) ? (string) $s['stile_punto'] : 'anello';
        $c_p_sc  = $this->safe_color_css( $s['colore_punto'] );
        $c_punto = '' !== $c_p_sc ? $c_p_sc : 'var(--olo-color-accent, var(--olo-color-primary))';
        $c_p_fg  = Olobuild_Tile_Utils::testo_su_fondo( $c_punto, $chiaro ) ?: $chiaro;
        $eb_sc   = $this->safe_color_css( $s['sfondo_etichetta'] );
        $eb      = '' !== $eb_sc ? $eb_sc : $velo;
        $ec      = $this->safe_color_css( $s['colore_etichetta'] ) ?: $this->safe_color_css( $s['etichetta_color'] );
        if ( '' === $ec ) {
            $ec = '' !== $eb_sc ? ( Olobuild_Tile_Utils::testo_su_fondo( $eb_sc, $chiaro ) ?: $chiaro ) : $chiaro;
        }
        $pb_sc = $this->safe_color_css( $s['sfondo_pannello'] );
        $pb    = '' !== $pb_sc ? $pb_sc : 'var(--olo-color-background, #ffffff)';
        $pc    = $this->safe_color_css( $s['colore_pannello'] );
        if ( '' === $pc ) {
            $pc = '' !== $pb_sc
                ? ( Olobuild_Tile_Utils::testo_su_fondo( $pb_sc, 'var(--olo-color-text, #374151)' ) ?: 'var(--olo-color-text, #374151)' )
                : 'var(--olo-color-text, #374151)';
        }
        $cb_sc = $this->safe_color_css( $s['sfondo_comandi'] );
        $cb    = '' !== $cb_sc ? $cb_sc : 'color-mix(in srgb, var(--olo-color-dark, #14161c) 62%, transparent)';
        $cc    = $this->safe_color_css( $s['colore_comandi'] );
        if ( '' === $cc ) {
            $cc = '' !== $cb_sc ? ( Olobuild_Tile_Utils::testo_su_fondo( $cb_sc, $chiaro ) ?: $chiaro ) : $chiaro;
        }
        $raggio = Olobuild_Tile_Utils::border_radius( $s['raggio'] ) ?: '0';
        $r_max  = Olobuild_Tile_Utils::radius_int( $s['raggio'] );
        $ri     = min( $r_max, 12 ); // riquadro e titolo della scena
        $rt     = min( $r_max, 8 );  // miniature
        $rp     = $r_max > 0 ? '5em' : '0';  // etichette e suggerimento: pillole, o angoli vivi con la vista
        $rc     = $r_max > 0 ? '50%' : '0';  // pulsanti e punti: tondi, o quadrati con la vista
        $ombra  = Olobuild_Tile_Utils::shadow_value( $s, 'ombra' );
        $bordo  = '';
        $b      = $s['bordo'] ?? null;
        if ( is_array( $b ) && Olobuild_Tile_Utils::border_is_set( $b ) ) {
            if ( '' === trim( (string) ( $b['color'] ?? '' ) ) ) {
                $b['color'] = 'var(--olo-color-border, #e5e7eb)';
            }
            $bordo = Olobuild_Tile_Utils::border_css( $b );
        }
        $ar  = self::proporzione( $s['proporzione'], '16/9' );
        $arm = self::proporzione( $s['proporzione_mobile'], '4/5' );

        $classi = 'o36 o36--' . $stile_p . ( $elenco ? ' o36--elenco' : '' ) . ( $builder ? ' o36--builder' : '' );

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS da colori passati da safe_color_css() e dagli helper di Olobuild_Tile_Utils, numeri forzati, proporzioni da espressione regolare, valori da elenchi chiusi e selettori generati qui; SVG delle icone dalle librerie del plugin (render_icon_html, libreria_icone)
        echo '<style>';
        echo "{$sel}{--o36-punto:{$c_punto};--o36-punto-fg:{$c_p_fg};--o36-mp:{$mp}px;--o36-r:{$raggio};--o36-ri:{$ri}px;--o36-rt:{$rt}px;--o36-rp:{$rp};--o36-rc:{$rc};--o36-eb:{$eb};--o36-ec:{$ec};--o36-pb:{$pb};--o36-pc:{$pc};--o36-cb:{$cb};--o36-cc:{$cc};--o36-fondo:color-mix(in srgb, var(--olo-color-dark, #14161c) 90%, var(--olo-color-light, #ffffff));position:relative}";
        echo "{$sel} [hidden]{display:none!important}";
        echo "{$sel} .o36-vista{position:relative;overflow:hidden;aspect-ratio:{$ar};border-radius:var(--o36-r);background:var(--o36-fondo);isolation:isolate;-webkit-user-select:none;user-select:none;-webkit-tap-highlight-color:transparent;{$bordo}" . ( 'none' !== $ombra ? "box-shadow:{$ombra}" : '' ) . '}';
        echo '@media (max-width:639px){' . "{$sel} .o36-vista{aspect-ratio:{$arm}}" . '}';
        // La tela: la sola zona che prende il gesto (touch-action:none), con l'anello del fuoco sopra il disegno.
        echo "{$sel} .o36-tela{position:absolute;inset:0;cursor:grab;touch-action:none;outline:none}";
        echo "{$sel} .o36-tela.is-presa{cursor:grabbing}";
        echo "{$sel} .o36-tela::after{content:'';position:absolute;inset:0;border-radius:inherit;pointer-events:none;box-shadow:inset 0 0 0 3px var(--o36-punto);opacity:0}";
        echo "{$sel} .o36-tela:focus-visible::after{opacity:1}";
        echo "{$sel} .o36-cv{position:absolute;inset:0;display:block;width:100%;height:100%}";
        echo "{$sel} .o36-piano{position:absolute;inset:0;display:none;background-repeat:repeat-x}";
        echo "{$sel}.is-piano .o36-piano{display:block}";
        echo "{$sel}.is-piano .o36-cv{display:none}";
        echo "{$sel} .o36-piano.is-entra{animation:o36-appare .4s ease}";
        // I punti: pulsanti sopra la tela, posati dallo script a ogni fotogramma.
        echo "{$sel} .o36-punti{position:absolute;inset:0;z-index:2;pointer-events:none}";
        echo "{$sel} .o36-punti.is-entra .o36-punto{animation:o36-appare .45s ease both}";
        echo "{$sel} .o36-punto,{$sel} .o36-punto:hover{background:transparent}";
        echo "{$sel} .o36-punto{position:absolute;left:0;top:0;display:block;width:var(--o36-mp);height:var(--o36-mp);margin:0;padding:0;border:0;border-radius:var(--o36-rc);color:inherit;font:inherit;line-height:1;text-decoration:none;cursor:pointer;pointer-events:auto;-webkit-appearance:none;appearance:none;box-shadow:none;transition:opacity .15s ease;will-change:transform}";
        echo "{$sel} .o36-punto::after{content:'';position:absolute;inset:-.5rem;border-radius:inherit}";
        echo "{$sel} .o36-punto.is-fuori{opacity:0;pointer-events:none}";
        echo "{$sel} .o36-punto:focus{outline:none}";
        echo "{$sel} .o36-punto:focus-visible{outline:3px solid var(--o36-punto);outline-offset:3px}";
        echo "{$sel} .o36-segno{position:relative;display:flex;align-items:center;justify-content:center;width:100%;height:100%;border-radius:inherit;transition:transform .2s ease}";
        echo "{$sel} .o36-punto:hover .o36-segno,{$sel} .o36-punto:focus-visible .o36-segno{transform:scale(1.08)}";
        echo "{$sel} .o36-segno>span{display:flex;width:46%;height:46%}";
        echo "{$sel} .o36-segno svg{display:block;width:100%;height:100%}";
        echo "{$sel} .o36-segno .olo-custom-icon{width:46%!important;height:46%!important}";
        if ( 'anello' === $stile_p ) {
            echo "{$sel} .o36-segno{background:color-mix(in srgb, var(--olo-color-dark, #14161c) 48%, transparent);color:{$chiaro};box-shadow:inset 0 0 0 2px var(--o36-punto),0 .375rem 1.125rem -.375rem color-mix(in srgb, var(--olo-color-dark, #14161c) 75%, transparent)}";
            echo "{$sel} .o36-segno::before{content:'';position:absolute;inset:0;border-radius:inherit;pointer-events:none;animation:o36-pulsa 2.6s ease-out infinite}";
        } elseif ( 'pieno' === $stile_p ) {
            echo "{$sel} .o36-segno{background:var(--o36-punto);color:var(--o36-punto-fg);box-shadow:0 0 0 3px color-mix(in srgb, var(--olo-color-light, #ffffff) 55%, transparent),0 .375rem 1.125rem -.375rem color-mix(in srgb, var(--olo-color-dark, #14161c) 75%, transparent)}";
        } else {
            echo "{$sel} .o36-segno{color:var(--o36-punto);filter:drop-shadow(0 1px 2px color-mix(in srgb, var(--olo-color-dark, #14161c) 80%, transparent))}";
            echo "{$sel} .o36-segno>span{width:78%;height:78%}";
            echo "{$sel} .o36-segno .olo-custom-icon{width:78%!important;height:78%!important}";
        }
        // L'etichetta del punto: al passaggio e al fuoco; sui touch sempre (non c'è passaggio).
        echo "{$sel} .o36-etichetta{position:absolute;left:50%;bottom:calc(100% + .5rem);z-index:1;box-sizing:border-box;max-width:16rem;padding:.4em .8em;border-radius:var(--o36-rp);background:var(--o36-eb);color:var(--o36-ec);line-height:1.3;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;pointer-events:none;opacity:0;transform:translate(-50%,.25rem);transition:opacity .2s ease,transform .2s ease;box-shadow:0 .25rem .875rem -.375rem color-mix(in srgb, var(--olo-color-dark, #14161c) 70%, transparent)}";
        echo "{$sel} .o36-punto--sotto .o36-etichetta{bottom:auto;top:calc(100% + .5rem);transform:translate(-50%,-.25rem)}";
        echo "{$sel} .o36-punto:hover .o36-etichetta,{$sel} .o36-punto:focus-visible .o36-etichetta{opacity:1;transform:translate(-50%,0)}";
        echo '@media (hover:none){' . "{$sel} .o36-etichetta{opacity:1;transform:translate(-50%,0)}" . '}';
        // Titolo della scena, comandi, suggerimento.
        echo "{$sel} .o36-testata{position:absolute;left:.75rem;top:.75rem;z-index:5;box-sizing:border-box;max-width:min(26rem,calc(100% - 5.25rem));padding:.55em .9em;border-radius:var(--o36-ri);background:var(--o36-eb);color:var(--o36-ec);pointer-events:none;-webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px)}";
        echo "{$sel} .o36-testata-titolo{margin:0;font-size:1.15em;line-height:1.25;text-wrap:balance}";
        echo "{$sel} .o36-conta{margin-left:.5em;font-size:.8em;font-weight:500;opacity:.72;font-variant-numeric:tabular-nums;white-space:nowrap}";
        echo "{$sel} .o36-testata-desc{margin:.2em 0 0;font-size:.92em;font-weight:400;line-height:1.4;opacity:.85;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}";
        echo "{$sel} .o36-comandi{position:absolute;right:.75rem;top:.75rem;z-index:5;display:flex;flex-direction:column;gap:.5rem}";
        echo "{$sel} .o36-btn,{$sel} .o36-btn:hover{background:var(--o36-cb)}";
        echo "{$sel} .o36-btn{display:flex;align-items:center;justify-content:center;width:2.75rem;height:2.75rem;margin:0;padding:0;border:0;border-radius:var(--o36-rc);color:var(--o36-cc);font:inherit;line-height:1;cursor:pointer;-webkit-appearance:none;appearance:none;-webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px);box-shadow:0 .25rem .875rem -.375rem color-mix(in srgb, var(--olo-color-dark, #14161c) 65%, transparent);transition:transform .15s ease}";
        echo "{$sel} .o36-btn:hover{transform:scale(1.06)}";
        echo "{$sel} .o36-btn:active{transform:scale(.96)}";
        echo "{$sel} .o36-btn:focus{outline:none}";
        echo "{$sel} .o36-btn:focus-visible{outline:2px solid var(--o36-cc);outline-offset:2px}";
        echo "{$sel} .o36-btn .olo-lucide-icon,{$sel} .o36-btn svg{display:flex;width:1.15rem;height:1.15rem}";
        echo "{$sel} .o36-ico-esci{display:none!important}";
        echo "{$sel}.is-schermo .o36-ico-apri{display:none!important}";
        echo "{$sel}.is-schermo .o36-ico-esci{display:flex!important}";
        echo '@media (hover:none){' . "{$sel} .o36-zoom{display:none}" . '}';
        if ( $con_aiuto ) {
            echo "{$sel} .o36-aiuto{position:absolute;left:50%;top:50%;z-index:5;box-sizing:border-box;display:inline-flex;align-items:center;gap:.55em;max-width:calc(100% - 2rem);padding:.6em 1.1em;border-radius:var(--o36-rp);background:var(--o36-eb);color:var(--o36-ec);font-size:1.05em;line-height:1.3;white-space:nowrap;pointer-events:none;-webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px);transform:translate(-50%,-50%);transition:opacity .4s ease,transform .4s ease}";
            echo "{$sel} .o36-aiuto>span:not(.olo-lucide-icon){min-width:0;overflow:hidden;text-overflow:ellipsis}";
            echo "{$sel} .o36-aiuto .olo-lucide-icon,{$sel} .o36-aiuto svg{display:flex;flex:none;width:1.25em;height:1.25em}";
            echo "{$sel}:not(.is-pronta) .o36-aiuto,{$sel}.is-usata .o36-aiuto{opacity:0;transform:translate(-50%,-30%)}";
            echo "{$sel} .o36-aiuto-tocco{display:none}";
            echo '@media (hover:none){' . "{$sel} .o36-aiuto-mouse{display:none}{$sel} .o36-aiuto-tocco{display:inline}" . '}';
        }
        // Il riquadro dei punti «Informazioni»: in basso a sinistra; sul telefono largo quanto la vista.
        if ( $con_info ) {
            echo "{$sel} .o36-pannello{position:absolute;left:.75rem;bottom:.75rem;z-index:6;box-sizing:border-box;width:min(22rem,calc(100% - 1.5rem));max-height:calc(100% - 1.5rem);overflow:auto;padding:1.25rem 1.25rem 1.375rem;border-radius:var(--o36-ri);background:var(--o36-pb);color:var(--o36-pc);box-shadow:0 1.125rem 3rem -1.125rem color-mix(in srgb, var(--olo-color-dark, #14161c) 75%, transparent);-webkit-user-select:text;user-select:text;cursor:auto;outline:none;animation:o36-entra .25s ease}";
            echo "{$sel} .o36-pannello-titolo{margin:0 2.5rem .5rem 0;padding:0;font-size:19px;line-height:1.25;color:inherit;text-transform:none;letter-spacing:normal;text-wrap:balance}";
            echo "{$sel} .o36-pannello-testo{font-size:15px;line-height:1.55;text-wrap:pretty}";
            echo "{$sel} .o36-pannello-testo>*{margin:0 0 .6em}";
            echo "{$sel} .o36-pannello-testo>*:last-child{margin-bottom:0}";
            echo "{$sel} .o36-pannello-testo ul,{$sel} .o36-pannello-testo ol{padding-left:1.25em}";
            echo "{$sel} .o36-pannello-testo a{color:inherit;text-decoration:underline;text-underline-offset:.2em}";
            echo "{$sel} .o36-pannello-testo a:focus-visible{outline:2px solid currentColor;outline-offset:2px}";
            echo "{$sel} .o36-chiudi,{$sel} .o36-chiudi:hover{background:transparent}";
            echo "{$sel} .o36-chiudi{position:absolute;right:.5rem;top:.5rem;display:flex;align-items:center;justify-content:center;width:2.75rem;height:2.75rem;margin:0;padding:0;border:0;border-radius:var(--o36-rc);color:inherit;cursor:pointer;opacity:.7;-webkit-appearance:none;appearance:none;box-shadow:none;transition:opacity .15s ease}";
            echo "{$sel} .o36-chiudi:hover{opacity:1}";
            echo "{$sel} .o36-chiudi:focus{outline:none}";
            echo "{$sel} .o36-chiudi:focus-visible{opacity:1;outline:2px solid currentColor;outline-offset:-.375rem}";
            echo "{$sel} .o36-chiudi .olo-lucide-icon,{$sel} .o36-chiudi svg{display:flex;width:1.25rem;height:1.25rem}";
            echo '@media (max-width:639px){' . "{$sel} .o36-pannello{left:.5rem;right:.5rem;bottom:.5rem;width:auto;max-height:72%;padding:1.125rem 1.125rem 1.25rem}" . '}';
        }
        // L'elenco delle scene: miniature in fondo alla vista, su un velo; al centro finché ci stanno, poi scorrono.
        if ( $elenco ) {
            echo "{$sel} .o36-scene{position:absolute;left:0;right:0;bottom:0;z-index:5;display:flex;gap:.625rem;padding:2.25rem .75rem .75rem;overflow-x:auto;overscroll-behavior-x:contain;scrollbar-width:none;background:linear-gradient(to top,color-mix(in srgb, var(--olo-color-dark, #14161c) 62%, transparent),transparent);pointer-events:none}";
            echo "{$sel} .o36-scene::-webkit-scrollbar{display:none}";
            echo "{$sel} .o36-scena,{$sel} .o36-scena:hover{background:transparent}";
            echo "{$sel} .o36-scena{flex:none;display:flex;flex-direction:column;gap:.375rem;width:6.5rem;margin:0;padding:0;border:0;color:var(--o36-ec);font:inherit;text-align:left;cursor:pointer;pointer-events:auto;touch-action:pan-x;-webkit-appearance:none;appearance:none;box-shadow:none}";
            echo "{$sel} .o36-scena:first-child{margin-left:auto}";
            echo "{$sel} .o36-scena:last-child{margin-right:auto}";
            echo "{$sel} .o36-scena-img{position:relative;display:flex;align-items:center;justify-content:center;aspect-ratio:16/10;overflow:hidden;border-radius:var(--o36-rt);background:var(--o36-eb);color:var(--o36-ec);font-size:1.4em;font-weight:700;box-shadow:inset 0 0 0 1px color-mix(in srgb, var(--olo-color-light, #ffffff) 28%, transparent),0 .25rem .75rem -.375rem color-mix(in srgb, var(--olo-color-dark, #14161c) 70%, transparent);transition:box-shadow .2s ease,transform .2s ease}";
            echo "{$sel} .o36-scena-img img{position:absolute;inset:0;display:block;width:100%;height:100%;max-width:none;object-fit:cover}";
            echo "{$sel} .o36-scena-titolo{align-self:flex-start;box-sizing:border-box;max-width:100%;padding:.2em .6em;border-radius:var(--o36-rp);background:var(--o36-eb);color:var(--o36-ec);font-size:.92em;line-height:1.25;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}";
            echo "{$sel} .o36-scena:hover .o36-scena-img{transform:translateY(-2px)}";
            echo "{$sel} .o36-scena[aria-current=\"true\"] .o36-scena-img{box-shadow:inset 0 0 0 1px color-mix(in srgb, var(--olo-color-light, #ffffff) 28%, transparent),0 0 0 2px var(--o36-punto),0 .25rem .75rem -.375rem color-mix(in srgb, var(--olo-color-dark, #14161c) 70%, transparent)}";
            echo "{$sel} .o36-scena:focus{outline:none}";
            echo "{$sel} .o36-scena:focus-visible .o36-scena-img{outline:2px solid var(--o36-ec);outline-offset:3px}";
            echo '@media (max-width:639px){' . "{$sel} .o36-scena{width:5rem}{$sel} .o36-scene{gap:.5rem;padding:1.75rem .5rem .5rem}" . '}';
        }
        // Caricamento ed errore: sopra un fondo neutro (velato, quando sotto c'è già una scena).
        echo "{$sel} .o36-carico,{$sel} .o36-errore{position:absolute;inset:0;z-index:4;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.75rem;padding:1.5rem;background:var(--o36-fondo);color:color-mix(in srgb, var(--olo-color-light, #ffffff) 88%, transparent);font-size:15px;line-height:1.45;text-align:center}";
        echo "{$sel} .o36-carico{transition:opacity .35s ease}";
        echo "{$sel} .o36-carico.is-velo{background:color-mix(in srgb, var(--olo-color-dark, #14161c) 45%, transparent)}";
        echo "{$sel} .o36-carico.is-via{opacity:0;pointer-events:none}";
        echo "{$sel} .o36-giro{display:flex;width:2rem;height:2rem;animation:o36-gira 1s linear infinite}";
        echo "{$sel} .o36-giro svg,{$sel} .o36-errore .olo-lucide-icon svg{display:block;width:100%;height:100%}";
        echo "{$sel} .o36-errore .olo-lucide-icon{display:flex;width:2.25rem;height:2.25rem;opacity:.8}";
        echo "{$sel} .o36-errore p{margin:0;max-width:28rem}";
        echo "{$sel} .o36-riprova,{$sel} .o36-riprova:hover{background:var(--olo-color-light, #ffffff)}";
        echo "{$sel} .o36-riprova{margin:0;padding:.7em 1.4em;border:0;border-radius:var(--o36-rp);color:var(--olo-color-dark, #14161c);font:inherit;font-weight:600;line-height:1.2;cursor:pointer;-webkit-appearance:none;appearance:none}";
        echo "{$sel} .o36-riprova:focus{outline:none}";
        echo "{$sel} .o36-riprova:focus-visible{outline:2px solid var(--olo-color-light, #ffffff);outline-offset:3px}";
        if ( $builder ) {
            // Il mirino e la lettura di direzione e inclinazione: per piazzare i punti dal Contenuto.
            echo "{$sel} .o36-mira{position:absolute;left:50%;top:50%;z-index:2;width:1.375rem;height:1.375rem;margin:-.6875rem 0 0 -.6875rem;pointer-events:none;opacity:.85;background:linear-gradient(var(--olo-color-light, #ffffff),var(--olo-color-light, #ffffff)) center/100% 2px no-repeat,linear-gradient(var(--olo-color-light, #ffffff),var(--olo-color-light, #ffffff)) center/2px 100% no-repeat;filter:drop-shadow(0 0 1px var(--olo-color-dark, #14161c))}";
            echo "{$sel} .o36-lettura{position:absolute;left:50%;top:calc(50% + 1.25rem);z-index:3;padding:.3em .75em;border-radius:var(--o36-rp);background:var(--o36-eb);color:var(--o36-ec);font:600 12px/1.3 var(--olo-font-family-mono, ui-monospace, monospace);white-space:nowrap;pointer-events:none;transform:translateX(-50%);opacity:0;transition:opacity .3s ease}";
            echo "{$sel}.is-usata .o36-lettura{opacity:1}";
            echo "{$sel} .o36-segnato{position:absolute;left:0;top:0;z-index:2;width:1.125rem;height:1.125rem;margin:-.5625rem 0 0 -.5625rem;border-radius:50%;box-shadow:0 0 0 2px var(--olo-color-light, #ffffff),0 0 0 4px var(--o36-punto);pointer-events:none}";
            echo "{$sel} .o36-nota{position:absolute;left:50%;bottom:.75rem;z-index:5;box-sizing:border-box;max-width:calc(100% - 2rem);margin:0;padding:.45em .9em;border-radius:var(--o36-rp);background:var(--o36-eb);color:var(--o36-ec);font-size:12px;font-weight:600;line-height:1.35;text-align:center;pointer-events:none;transform:translateX(-50%)}";
            echo "{$sel}.o36--elenco .o36-nota{bottom:7.25rem}";
            echo "{$sel} .o36-testata{pointer-events:auto}";
            echo "{$sel} .o36-vuota{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.75rem;aspect-ratio:{$ar};padding:1.5rem;border:1px dashed color-mix(in srgb, currentColor 30%, transparent);border-radius:var(--o36-r);font-size:14px;line-height:1.45;text-align:center;opacity:.75}";
            echo "{$sel} .o36-vuota .olo-lucide-icon{display:flex;width:2.25rem;height:2.25rem}";
            echo "{$sel} .o36-vuota svg{display:block;width:100%;height:100%}";
        }
        // Lo schermo intero: la vista prende tutto lo schermo, senza angoli né cornice.
        echo "{$sel}.is-schermo{display:flex;flex-direction:column;background:var(--o36-fondo)}";
        echo "{$sel}.is-schermo .o36-vista{flex:1 1 auto;height:100%;aspect-ratio:auto;border:0;border-radius:0;box-shadow:none}";
        echo '@keyframes o36-pulsa{0%{box-shadow:0 0 0 0 color-mix(in srgb, var(--o36-punto) 55%, transparent)}70%,100%{box-shadow:0 0 0 .75rem color-mix(in srgb, var(--o36-punto) 0%, transparent)}}';
        echo '@keyframes o36-gira{to{transform:rotate(360deg)}}';
        echo '@keyframes o36-appare{from{opacity:0}}';
        echo '@keyframes o36-entra{from{opacity:0;transform:translateY(.5rem)}}';
        // «Riduci movimento»: niente pulsazione né transizioni; la rotella del caricamento rallenta.
        echo '@media (prefers-reduced-motion:reduce){' . "{$sel} .o36-segno::before{animation:none}{$sel} .o36-punti.is-entra .o36-punto,{$sel} .o36-piano.is-entra,{$sel} .o36-pannello{animation:none}{$sel} .o36-punto,{$sel} .o36-segno,{$sel} .o36-etichetta,{$sel} .o36-aiuto,{$sel} .o36-carico,{$sel} .o36-btn,{$sel} .o36-scena-img,{$sel} .o36-chiudi,{$sel} .o36-lettura{transition:none}{$sel} .o36-giro{animation-duration:3s}" . '}';
        echo $this->css_testo( $s, 'etichetta', "{$sel} .o36-etichetta,{$sel} .o36-testata,{$sel} .o36-aiuto,{$sel} .o36-scene,{$sel} .o36-nota" );
        echo $this->css_testo( $s, 'titolo', "{$sel} .o36-pannello-titolo", $this->safe_color_css( $s['titolo_color'] ) );
        echo $this->css_testo( $s, 'testo', "{$sel} .o36-pannello-testo", $this->safe_color_css( $s['testo_color'] ) );
        echo '</style>';

        if ( ! $scene ) {
            echo '<div id="' . esc_attr( $uid ) . '" class="o36 o36--builder"><div class="o36-vuota">' . $this->icona_ui( 'rotate-3d' )
                . '<span>' . esc_html__( 'Aggiungi le scene nel Contenuto: per ognuna una foto sferica equirettangolare (2:1, per esempio 4096 × 2048).', 'olobuilder' ) . '</span></div></div>';
            return ob_get_clean();
        }

        // ── I dati per lo script ──
        $sc_js = [];
        foreach ( $scene as $i => $sc ) {
            $sc_js[] = [
                't'    => $sc['titolo'],
                'i'    => '' !== $sc['img'] ? $sc['img'] : $sc['leg'],
                'l'    => $sc['leg'],
                'y'    => $sc['yaw'],
                'p'    => $sc['pitch'],
                'd'    => $sc['desc'],
                'n'    => $sc['n'],
                'demo' => $sc['demo'],
            ];
        }
        $pt_js = [];
        foreach ( $punti as $p ) {
            $pt_js[] = [ 's' => $p['s'], 'y' => $p['y'], 'p' => $p['p'], 'k' => $p['k'], 'd' => $p['d'], 'ti' => $p['ti'], 'tx' => $p['tx'] ];
        }
        $cfg = [
            'scene'   => $sc_js,
            'punti'   => $pt_js,
            'ini'     => $ini,
            'fov'     => $fov,
            'zoom'    => $zoom,
            'auto'    => $auto,
            'vel'     => $vel,
            'rip'     => $rip,
            'mp'      => $mp,
            'builder' => $builder,
            'testi'   => [
                'aria'   => $zoom ? __( 'Vista a 360°: usa le frecce per guardarti intorno, + e − per ingrandire.', 'olobuilder' ) : __( 'Vista a 360°: usa le frecce per guardarti intorno.', 'olobuilder' ),
                'info'   => __( 'Informazioni', 'olobuilder' ),
                'fsApri' => __( 'Schermo intero', 'olobuilder' ),
                'fsEsci' => __( 'Esci dallo schermo intero', 'olobuilder' ),
                'dir'    => __( 'Direzione', 'olobuilder' ),
                'incl'   => __( 'Inclinazione', 'olobuilder' ),
            ],
        ];
        $s0     = $scene[ $ini ];
        $aria0  = ( '' !== $s0['titolo'] ? $s0['titolo'] . '. ' : '' ) . $cfg['testi']['aria'];
        $nomi   = [];
        foreach ( $scene as $i => $sc ) {
            /* translators: %d: numero della scena */
            $nomi[ $i ] = '' !== $sc['titolo'] ? $sc['titolo'] : sprintf( __( 'Scena %d', 'olobuilder' ), $i + 1 );
        }

        echo '<div id="' . esc_attr( $uid ) . '" class="' . esc_attr( $classi ) . '" data-o36="' . esc_attr( wp_json_encode( $cfg ) ) . '">';
        // data-olo-own-drag: nel builder il trascinamento dentro la vista gira la foto (la tile si sposta dalla barra).
        echo '<div class="o36-vista" data-olo-own-drag>';
        echo '<div class="o36-tela" tabindex="0" role="application" aria-label="' . esc_attr( $aria0 ) . '"' . ( $con_tit ? ' aria-describedby="' . esc_attr( $uid ) . '-d"' : '' ) . '>';
        echo '<canvas class="o36-cv" aria-hidden="true"></canvas><div class="o36-piano" aria-hidden="true"></div>';
        echo '</div>';

        // ── I punti di tutte le scene, nascosti: lo script mostra quelli della scena aperta, già al loro posto ──
        echo '<div class="o36-punti">';
        foreach ( $punti as $i => $p ) {
            $vis = $p['ti'];
            if ( 'scena' === $p['k'] && '' === $vis ) {
                $vis = $scene[ $p['d'] ]['titolo'];
            }
            $aria = $p['ti'];
            if ( '' === $aria ) {
                if ( 'scena' === $p['k'] ) {
                    /* translators: %s: titolo della scena di arrivo */
                    $aria = sprintf( __( 'Vai a: %s', 'olobuilder' ), $nomi[ $p['d'] ] );
                } elseif ( 'info' === $p['k'] ) {
                    $aria = __( 'Informazioni', 'olobuilder' );
                } else {
                    $aria = __( 'Apri il collegamento', 'olobuilder' );
                }
            }
            $dentro = '<span class="o36-segno" aria-hidden="true">' . $this->icona_punto( $p ) . '</span>'
                . ( '' !== $vis ? '<span class="o36-etichetta" aria-hidden="true">' . esc_html( $vis ) . '</span>' : '' );
            $comune = ' class="o36-punto o36-punto--' . $p['k'] . '" data-p="' . (int) $i . '" hidden';
            if ( 'link' === $p['k'] ) {
                $fuori = self::esterno( $p['url'] );
                if ( $fuori ) {
                    $aria .= ' ' . __( '(si apre in una nuova scheda)', 'olobuilder' );
                }
                echo '<a' . $comune . ' href="' . esc_url( $p['url'] ) . '"' . ( $fuori ? ' target="_blank" rel="noopener"' : '' ) . ' aria-label="' . esc_attr( $aria ) . '">' . $dentro . '</a>';
            } else {
                echo '<button type="button"' . $comune . ' aria-label="' . esc_attr( $aria ) . '"'
                    . ( 'info' === $p['k'] ? ' aria-haspopup="dialog" aria-expanded="false" aria-controls="' . esc_attr( $uid ) . '-p"' : '' )
                    . ' data-olo-interactive>' . $dentro . '</button>';
            }
        }
        echo '</div>';

        if ( $builder ) {
            echo '<span class="o36-mira" aria-hidden="true"></span><span class="o36-lettura" aria-hidden="true"></span><span class="o36-segnato" aria-hidden="true" hidden></span>';
        }
        if ( $con_tit ) {
            $vuota = '' === $s0['titolo'] && '' === $s0['desc'];
            echo '<div class="o36-testata"' . ( $vuota ? ' hidden' : '' ) . '>';
            echo '<p class="o36-testata-titolo" aria-live="polite"><span class="o36-tt"' . ( $builder ? ' data-olo-editable="scene.' . (int) $s0['n'] . '.titolo"' : '' ) . '>' . esc_html( $s0['titolo'] ) . '</span>'
                . '<span class="o36-conta">' . ( $piu_scene ? ( $ini + 1 ) . ' / ' . count( $scene ) : '' ) . '</span></p>';
            echo '<p class="o36-testata-desc" id="' . esc_attr( $uid ) . '-d"' . ( '' === $s0['desc'] ? ' hidden' : '' ) . '>' . esc_html( $s0['desc'] ) . '</p>';
            echo '</div>';
        }
        if ( $zoom || $con_fs ) {
            echo '<div class="o36-comandi">';
            if ( $con_fs ) {
                // Nascosto finché lo script non trova la Fullscreen API (sull'iPhone non c'è).
                echo '<button type="button" class="o36-btn o36-schermo" aria-label="' . esc_attr( $cfg['testi']['fsApri'] ) . '" hidden data-olo-interactive>'
                    . '<span class="o36-ico-apri">' . $this->icona_ui( 'maximize' ) . '</span><span class="o36-ico-esci">' . $this->icona_ui( 'minimize' ) . '</span></button>';
            }
            if ( $zoom ) {
                echo '<button type="button" class="o36-btn o36-zoom o36-piu" aria-label="' . esc_attr__( 'Aumenta zoom', 'olobuilder' ) . '" data-olo-interactive>' . $this->icona_ui( 'plus' ) . '</button>';
                echo '<button type="button" class="o36-btn o36-zoom o36-meno" aria-label="' . esc_attr__( 'Riduci zoom', 'olobuilder' ) . '" data-olo-interactive>' . $this->icona_ui( 'minus' ) . '</button>';
            }
            echo '</div>';
        }
        if ( $con_aiuto ) {
            echo '<div class="o36-aiuto" aria-hidden="true">' . $this->icona_ui( 'move-horizontal' )
                . '<span class="o36-aiuto-mouse">' . esc_html( $aiuto_m ) . '</span><span class="o36-aiuto-tocco">' . esc_html( $aiuto_t ) . '</span></div>';
        }
        if ( $con_info ) {
            echo '<div class="o36-pannello" id="' . esc_attr( $uid ) . '-p" role="dialog" aria-labelledby="' . esc_attr( $uid ) . '-pt" tabindex="-1" hidden>';
            echo '<button type="button" class="o36-chiudi" aria-label="' . esc_attr__( 'Chiudi', 'olobuilder' ) . '" data-olo-interactive>' . $this->icona_ui( 'x' ) . '</button>';
            echo '<h3 class="o36-pannello-titolo" id="' . esc_attr( $uid ) . '-pt"></h3><div class="o36-pannello-testo"></div>';
            echo '</div>';
        }
        if ( $elenco ) {
            echo '<nav class="o36-scene" aria-label="' . esc_attr__( 'Scene', 'olobuilder' ) . '">';
            foreach ( $scene as $i => $sc ) {
                $mini = '' !== $sc['ant'] ? $sc['ant'] : ( '' !== $sc['leg'] ? $sc['leg'] : self::miniatura_di( $sc['img'] ) );
                echo '<button type="button" class="o36-scena" data-s="' . (int) $i . '" aria-current="' . ( $i === $ini ? 'true' : 'false' ) . '" data-olo-interactive>'
                    . '<span class="o36-scena-img" aria-hidden="true">' . ( $i + 1 ) . ( '' !== $mini ? '<img alt="" data-src="' . esc_url( $mini ) . '" decoding="async">' : '' ) . '</span>'
                    . '<span class="o36-scena-titolo">' . esc_html( $nomi[ $i ] ) . '</span></button>';
            }
            echo '</nav>';
        }
        if ( $builder ) {
            echo '<p class="o36-nota" hidden>' . esc_html__( 'Foto d\'esempio: carica la foto sferica della scena nel Contenuto.', 'olobuilder' ) . '</p>';
        }
        echo '<div class="o36-carico" role="status"><span class="o36-giro" aria-hidden="true">' . $this->icona_ui( 'loader-circle' ) . '</span><span>' . esc_html( $caric ) . '</span></div>';
        echo '<div class="o36-errore" role="alert" hidden>' . $this->icona_ui( 'image-off' )
            . '<p>' . esc_html__( 'Non è stato possibile caricare questa vista.', 'olobuilder' ) . '</p>'
            . '<button type="button" class="o36-riprova" data-olo-interactive>' . esc_html__( 'Riprova', 'olobuilder' ) . '</button></div>';
        echo '</div></div>';
        echo '<script>' . $this->script() . '[].forEach.call(document.querySelectorAll("[data-o36]"),function(x){window.oloTour360(x);});</script>';
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }

    /**
     * Lo script della vista. Va in OGNI istanza (definito una volta sola, «if(!window…)»): una sezione caricata dopo
     * può eseguire il suo script prima di quello della prima istanza. Niente «&» nello script (wptexturize lo riscrive
     * fra un «<» e un «>»): condizioni annidate. Ogni istanza si avvia una volta (data-o36-pronto); a ogni avvio si
     * chiudono le istanze il cui nodo non è più nella pagina (l'anteprima del builder rigenera la tile): si fermano
     * fotogrammi e osservatori e si libera il contesto WebGL, che i browser concedono in numero limitato.
     */
    private function script() {
        return <<<'JS'
if(!window.oloTour360){window.oloTour360Istanze=[];window.oloTour360=function(el){
var I=window.oloTour360Istanze,q;
for(q=I.length-1;q>=0;q-=1){if(!document.documentElement.contains(I[q].el)){I[q].chiudi();I.splice(q,1);}}
if(!el){return;}
if(el.getAttribute("data-o36-pronto")==="1"){return;}
el.setAttribute("data-o36-pronto","1");
var C={};try{C=JSON.parse(el.getAttribute("data-o36")||"{}");}catch(e){C={};}
var SC=C.scene||[],PT=C.punti||[],T=C.testi||{};
var vista=el.querySelector(".o36-vista"),tela=el.querySelector(".o36-tela"),cv=el.querySelector(".o36-cv"),piano=el.querySelector(".o36-piano");
if(!SC.length){return;}if(!vista){return;}if(!tela){return;}
var D=Math.PI/180,FMIN=35,FMAX=100,MZ=(+C.mp||40)/2;
function mm(x){try{return window.matchMedia(x).matches;}catch(e){return false;}}
var riduci=mm("(prefers-reduced-motion: reduce)"),piccolo=mm("(max-width: 639px)")||mm("(pointer: coarse)");
var st={y:0,p:0,f:Math.max(FMIN,Math.min(FMAX,+C.fov||75))};
var cur=-1,giro=0,avviato=false,W=1,H=1,raf=0,tp=0,visibile=false,pronta=false,usata=false,chiuso=false;
var gl=null,pr=null,L={},t0=null,t1=null,maxT=0,modo="piano";
var ult=0,tRip=0,tPre=0,rot=0,vy=0,vp=0,mira=null,fade=null,tr=null,tocchi={},pz=null,gesto=0,sopra=null,foco=null,aperto=null,segno=null,errI=-1;
var cache={},ordine=[],io1=null,io2=null,ro=null;
var bott=[].slice.call(el.querySelectorAll(".o36-punto")),mini=[].slice.call(el.querySelectorAll(".o36-scena"));
var box=el.querySelector(".o36-punti"),carico=el.querySelector(".o36-carico"),errore=el.querySelector(".o36-errore"),riprova=el.querySelector(".o36-riprova");
var testata=el.querySelector(".o36-testata"),tt=el.querySelector(".o36-tt"),conta=el.querySelector(".o36-conta"),desc=el.querySelector(".o36-testata-desc");
var pan=el.querySelector(".o36-pannello"),pTit=el.querySelector(".o36-pannello-titolo"),pTxt=el.querySelector(".o36-pannello-testo"),pX=el.querySelector(".o36-chiudi");
var lettura=el.querySelector(".o36-lettura"),segnato=el.querySelector(".o36-segnato"),nota=el.querySelector(".o36-nota");
var fsB=el.querySelector(".o36-schermo"),zPiu=el.querySelector(".o36-piu"),zMeno=el.querySelector(".o36-meno");
function g180(a){a=(a+180)%360;if(a<0){a+=360;}return a-180;}
function fra(v,a,b){return Math.max(a,Math.min(b,v));}
function ora(){return window.performance?window.performance.now():Date.now();}
function fDi(f){var v=Math.tan(f*D/2),c=Math.tan(60*D)*H/W;return c<v?c:v;}
function vf(){return 2*Math.atan(fDi(st.f))/D;}
function limP(p){var l=85;if(modo==="piano"){l=Math.max(0,90-vf()/2);}return fra(p,-l,l);}
function limF(f){return fra(f,FMIN,FMAX);}
function vedi(n,s){if(!n){return;}if(s){n.removeAttribute("hidden");}else{n.setAttribute("hidden","");}}
var VS="attribute vec2 a;varying vec2 v;void main(){v=a;gl_Position=vec4(a,0.0,1.0);}";
var FS="#ifdef GL_FRAGMENT_PRECISION_HIGH\nprecision highp float;\n#else\nprecision mediump float;\n#endif\n"+
"uniform sampler2D t0;uniform sampler2D t1;uniform vec3 c0;uniform vec3 c1;uniform float r;uniform float m;varying vec2 v;"+
"vec2 uv(vec3 c){vec3 d=normalize(vec3(v.x*r*c.z,v.y*c.z,1.0));float a=cos(c.y);float b=sin(c.y);d=vec3(d.x,d.y*a+d.z*b,d.z*a-d.y*b);a=cos(c.x);b=sin(c.x);d=vec3(d.x*a+d.z*b,d.y,d.z*a-d.x*b);"+
"return vec2(fract(0.5+atan(d.x,d.z)*0.15915494),0.5-asin(clamp(d.y,-1.0,1.0))*0.31830989);}"+
"void main(){vec4 k=texture2D(t0,uv(c0));if(m<1.0){k=mix(texture2D(t1,uv(c1)),k,m);}gl_FragColor=vec4(k.rgb,1.0);}";
function sh(tipo,src){var s=gl.createShader(tipo);gl.shaderSource(s,src);gl.compileShader(s);if(!gl.getShaderParameter(s,gl.COMPILE_STATUS)){return null;}return s;}
function iniziaGL(){
if(!cv){return false;}
var o={alpha:false,antialias:false,depth:false,stencil:false,premultipliedAlpha:false,preserveDrawingBuffer:false};
try{gl=cv.getContext("webgl",o)||cv.getContext("experimental-webgl",o);}catch(e){gl=null;}
if(!gl){return false;}
var v=sh(gl.VERTEX_SHADER,VS),f=sh(gl.FRAGMENT_SHADER,FS);
if(!v){gl=null;return false;}if(!f){gl=null;return false;}
pr=gl.createProgram();gl.attachShader(pr,v);gl.attachShader(pr,f);gl.linkProgram(pr);
if(!gl.getProgramParameter(pr,gl.LINK_STATUS)){gl=null;return false;}
gl.useProgram(pr);
var b=gl.createBuffer();gl.bindBuffer(gl.ARRAY_BUFFER,b);gl.bufferData(gl.ARRAY_BUFFER,new Float32Array([-1,-1,3,-1,-1,3]),gl.STATIC_DRAW);
var a=gl.getAttribLocation(pr,"a");gl.enableVertexAttribArray(a);gl.vertexAttribPointer(a,2,gl.FLOAT,false,0,0);
["t0","t1","c0","c1","r","m"].forEach(function(n){L[n]=gl.getUniformLocation(pr,n);});
gl.uniform1i(L.t0,0);gl.uniform1i(L.t1,1);
maxT=gl.getParameter(gl.MAX_TEXTURE_SIZE)||2048;
return true;
}
function texDi(src){
var t=gl.createTexture();gl.bindTexture(gl.TEXTURE_2D,t);
gl.pixelStorei(gl.UNPACK_FLIP_Y_WEBGL,false);gl.pixelStorei(gl.UNPACK_PREMULTIPLY_ALPHA_WEBGL,false);
gl.texParameteri(gl.TEXTURE_2D,gl.TEXTURE_MIN_FILTER,gl.LINEAR);gl.texParameteri(gl.TEXTURE_2D,gl.TEXTURE_MAG_FILTER,gl.LINEAR);
gl.texParameteri(gl.TEXTURE_2D,gl.TEXTURE_WRAP_S,gl.CLAMP_TO_EDGE);gl.texParameteri(gl.TEXTURE_2D,gl.TEXTURE_WRAP_T,gl.CLAMP_TO_EDGE);
try{gl.texImage2D(gl.TEXTURE_2D,0,gl.RGB,gl.RGB,gl.UNSIGNED_BYTE,src);}catch(e){gl.deleteTexture(t);return null;}
if(gl.getError()!==gl.NO_ERROR){gl.deleteTexture(t);return null;}
return t;
}
function sorgente(c){
var im=c.img,w=im.naturalWidth||im.width,h=im.naturalHeight||im.height;
if(w<=maxT){if(h<=maxT){return im;}}
var k=Math.min(maxT/w,maxT/h),x=document.createElement("canvas");x.width=Math.floor(w*k);x.height=Math.floor(h*k);
x.getContext("2d").drawImage(im,0,0,x.width,x.height);return x;
}
function rgb(s){var m=String(s).match(/[\d.]+/g)||[];return [+(m[0]||0),+(m[1]||0),+(m[2]||0)];}
function mix(a,b,k){return "rgb("+Math.round(a[0]+(b[0]-a[0])*k)+","+Math.round(a[1]+(b[1]-a[1])*k)+","+Math.round(a[2]+(b[2]-a[2])*k)+")";}
function alfa(a,k){return "rgba("+a[0]+","+a[1]+","+a[2]+","+k+")";}
function colori(){
var sp=document.createElement("span"),K={},n={pr:"primary",ac:"accent",te:"text",fo:"background",sc:"dark"},r={pr:"rgb(225,71,79)",ac:"rgb(244,162,59)",te:"rgb(55,65,81)",fo:"rgb(255,255,255)",sc:"rgb(20,22,28)"},k;
sp.style.display="none";el.appendChild(sp);
for(k in n){if(Object.prototype.hasOwnProperty.call(n,k)){sp.style.color=r[k];sp.style.color="var(--olo-color-"+n[k]+", "+r[k]+")";K[k]=rgb(getComputedStyle(sp).color);}}
el.removeChild(sp);return K;
}
function demo(i){
var c=document.createElement("canvas"),x=null,K,tin,g,j,w=2048/24,h=1024/12;c.width=2048;c.height=1024;
try{x=c.getContext("2d");}catch(e){x=null;}if(!x){return null;}
K=colori();tin=i%2?K.ac:K.pr;
g=x.createLinearGradient(0,0,0,512);g.addColorStop(0,mix(K.sc,tin,0.3));g.addColorStop(1,mix(K.fo,tin,0.25));x.fillStyle=g;x.fillRect(0,0,2048,512);
g=x.createRadialGradient(1204,330,0,1204,330,300);g.addColorStop(0,alfa(rgb(mix(K.fo,tin,0.08)),0.75));g.addColorStop(1,alfa(K.fo,0));x.fillStyle=g;x.fillRect(0,0,2048,512);g=x.createLinearGradient(0,512,0,1024);g.addColorStop(0,mix(K.fo,K.te,0.16));g.addColorStop(1,mix(K.sc,K.te,0.35));x.fillStyle=g;x.fillRect(0,512,2048,512);
x.strokeStyle=alfa(K.te,0.2);x.lineWidth=2;
for(j=0;j<=24;j+=1){x.beginPath();x.moveTo(j*w,0);x.lineTo(j*w,1024);x.stroke();}
for(j=1;j<12;j+=1){x.beginPath();x.moveTo(0,j*h);x.lineTo(2048,j*h);x.stroke();}
x.fillStyle="rgb("+tin.join(",")+")";x.fillRect(0,508,2048,8);
x.font="700 150px system-ui, sans-serif";x.textAlign="center";x.textBaseline="alphabetic";x.fillStyle=alfa(K.fo,0.92);
for(j=0;j<=4;j+=1){x.fillText(String(i+1),j*512,468);}
return c;
}
function altra(u){try{var a=document.createElement("a");a.href=u;return a.host!==window.location.host;}catch(e){return false;}}
function urlDi(i){var s=SC[i];if(!s){return "";}if(s.l){if(piccolo){return s.l;}if(modo==="gl"){if(maxT<4096){return s.l;}}}return s.i||s.l||"";}
function ricorda(i){ordine=ordine.filter(function(x){return x!==i;});ordine.push(i);var j=0,k;while(ordine.length>4){if(j>=ordine.length){break;}k=ordine[j];if(k===cur){j+=1;}else{ordine.splice(j,1);delete cache[k];}}}
function finito(c){var l=c.cbs;c.cbs=[];l.forEach(function(f){f(c);});}
function prova(c,u,cors,s){
var im=new Image();c.img=im;c.u=u;
if(cors){im.crossOrigin="anonymous";}
im.decoding="async";
im.onload=function(){
var w=im.naturalWidth||0;
if(modo==="gl"){if(w>maxT){if(s.l){if(u!==s.l){prova(c,s.l,altra(s.l),s);return;}}}}
var vai=function(){c.ok=true;finito(c);};
if(im.decode){im.decode().then(vai,vai);}else{vai();}
};
im.onerror=function(){if(cors){c.tinta=true;prova(c,u,false,s);return;}c.ko=true;finito(c);};
im.src=u;
}
function carica(i,cb){
var s=SC[i],c=cache[i],u;if(!s){cb(null);return;}
if(c){if(c.ok){cb(c);return;}if(!c.ko){c.cbs.push(cb);return;}}
c={ok:false,ko:false,cbs:[cb],img:null,demo:null,tinta:false,u:""};cache[i]=c;ricorda(i);
if(s.demo){c.demo=demo(i);if(c.demo){c.ok=true;}else{c.ko=true;}finito(c);return;}
u=urlDi(i);if(!u){c.ko=true;finito(c);return;}
prova(c,u,altra(u),s);
}
function precarica(i){PT.forEach(function(p){if(p.s===i){if(p.k==="scena"){if(p.d>=0){carica(p.d,function(){});}}}});}
function passaPiano(){
modo="piano";el.classList.add("is-piano");fade=null;
if(gl){try{if(t0){gl.deleteTexture(t0);}if(t1){gl.deleteTexture(t1);}}catch(e){}}
t0=null;t1=null;
}
function sfondo(c){
if(!piano){return;}if(!c){return;}
var u=c.demo?(c.du||(c.du=c.demo.toDataURL("image/jpeg",0.85))):c.u;
piano.style.backgroundImage='url("'+String(u).replace(/["\\\n\r]/g,function(k){return encodeURIComponent(k);})+'")';
}
function mostraCarico(velo){if(!carico){return;}vedi(carico,true);carico.classList.remove("is-via");if(velo){carico.classList.add("is-velo");}else{carico.classList.remove("is-velo");}}
function nascondiCarico(){if(carico){carico.classList.add("is-via");}}
function miniature(){mini.forEach(function(m){var im=m.querySelector("img[data-src]");if(im){im.src=im.getAttribute("data-src");im.removeAttribute("data-src");}});}
function mostraErrore(i){errI=i;vedi(errore,true);miniature();}
function nascondiErrore(){vedi(errore,false);}
function aggiornaDOM(){
var s=SC[cur]||{};
bott.forEach(function(b){var p=PT[+b.getAttribute("data-p")],qui=false;if(p){qui=p.s===cur;}vedi(b,qui);b.classList.remove("is-fuori");});
if(box){if(!riduci){box.classList.remove("is-entra");void box.offsetWidth;box.classList.add("is-entra");}}
if(tt){tt.textContent=s.t||"";if(tt.hasAttribute("data-olo-editable")){tt.setAttribute("data-olo-editable","scene."+s.n+".titolo");}}
if(conta){conta.textContent=SC.length>1?(cur+1)+" / "+SC.length:"";}
if(desc){desc.textContent=s.d||"";vedi(desc,!!s.d);}
if(testata){vedi(testata,!!(s.t||s.d));}
tela.setAttribute("aria-label",(s.t?s.t+". ":"")+(T.aria||""));
mini.forEach(function(m){m.setAttribute("aria-current",+m.getAttribute("data-s")===cur?"true":"false");});
vedi(nota,!!s.demo);
segno=null;vedi(segnato,false);
}
function applica(i,c,op){
var vecchia=cur,dissolvi=!!op.dissolvi,nuova=null;
if(riduci){dissolvi=false;}if(vecchia<0){dissolvi=false;}
if(modo==="gl"){if(c.tinta){passaPiano();}}
if(modo==="gl"){
try{nuova=texDi(c.demo||sorgente(c));}catch(e){nuova=null;}
if(nuova){
if(t1){gl.deleteTexture(t1);t1=null;}
fade=null;if(!t0){dissolvi=false;}
if(dissolvi){t1=t0;fade={t:ora(),y:st.y,p:st.p,f:fDi(st.f),v:op.verso||null};}else if(t0){gl.deleteTexture(t0);}
t0=nuova;
}else{passaPiano();}
}
if(modo==="piano"){sfondo(c);if(dissolvi){if(piano){piano.classList.remove("is-entra");void piano.offsetWidth;piano.classList.add("is-entra");}}}
cur=i;chiudiPannello(false);
var s=SC[i];st.y=g180(+s.y||0);st.p=limP(+s.p||0);mira=null;vy=0;vp=0;
aggiornaDOM();nascondiCarico();nascondiErrore();
if(!pronta){pronta=true;el.classList.add("is-pronta");miniature();}
if(tPre){clearTimeout(tPre);}tPre=setTimeout(function(){tPre=0;if(!chiuso){precarica(i);}},500);
disegna(ora());richiedi();
}
function vai(i,op){
op=op||{};
if(i===cur){return;}if(!SC[i]){return;}
giro+=1;var mio=giro,c=cache[i];
nascondiErrore();
if(!c){mostraCarico(cur>=0);}else if(!c.ok){mostraCarico(cur>=0);}
carica(i,function(c2){
if(chiuso){return;}if(mio!==giro){return;}
var ok=false;if(c2){if(c2.ok){ok=true;}}
if(!ok){nascondiCarico();mostraErrore(i);return;}
applica(i,c2,op);
});
}
function richiedi(){if(raf){return;}if(chiuso){return;}if(!visibile){return;}raf=window.requestAnimationFrame(passo);}
function puoRuotare(now){
if(!C.auto){return false;}if(riduci){return false;}if(!pronta){return false;}if(fade){return false;}if(aperto){return false;}
if(sopra){return false;}if(foco){return false;}if(tr){return false;}if(pz){return false;}if(mira){return false;}if(document.hidden){return false;}
if(!ult){return true;}
var rip=+C.rip||0;if(rip<=0){return false;}
return now-ult>=rip*1000;
}
function passo(now){
raf=0;var dt=tp?Math.min(64,Math.max(0,now-tp)):16,ancora=false,e,k,dy;tp=now;
if(!tr){if(vy||vp){st.y=g180(st.y+vy*dt);st.p=limP(st.p+vp*dt);k=Math.exp(-dt/320);vy*=k;vp*=k;if(Math.abs(vy)<0.002){if(Math.abs(vp)<0.002){vy=0;vp=0;}}ancora=true;}}
if(mira){
e=riduci?1:1-Math.exp(-dt/90);dy=mira.dy*e;st.y=g180(st.y+dy);mira.dy-=dy;st.p=limP(st.p+(mira.p-st.p)*e);st.f=limF(st.f+(mira.f-st.f)*e);
if(Math.abs(mira.dy)<0.05){if(Math.abs(mira.p-st.p)<0.05){if(Math.abs(mira.f-st.f)<0.05){st.p=limP(mira.p);st.f=limF(mira.f);mira=null;}}}
ancora=true;
}
if(puoRuotare(now)){rot+=((+C.vel||3)-rot)*Math.min(1,dt/700);st.y=g180(st.y+rot*dt/1000);ancora=true;}else{rot=0;}
if(fade){ancora=true;}
disegna(now);
if(ancora){richiedi();}else{tp=0;}
}
function proietta(yw,pt){
var f=fDi(st.f),r=W/H;
if(modo!=="gl"){var k=H/vf();return {x:W/2+g180(yw-st.y)*k,y:H/2-(pt-st.p)*k};}
var cp=Math.cos(pt*D),wx=cp*Math.sin(yw*D),wy=Math.sin(pt*D),wz=cp*Math.cos(yw*D),a=st.y*D,b=st.p*D;
var x1=wx*Math.cos(a)-wz*Math.sin(a),z1=wx*Math.sin(a)+wz*Math.cos(a);
var y2=wy*Math.cos(b)-z1*Math.sin(b),z2=wy*Math.sin(b)+z1*Math.cos(b);
if(z2<0.02){return null;}
return {x:(x1/(z2*f*r)+1)/2*W,y:(1-y2/(z2*f))/2*H};
}
function direzione(cx,cy){
var f=fDi(st.f),r=W/H;
if(modo!=="gl"){var k=H/vf();return {y:g180(st.y+(cx-W/2)/k),p:fra(st.p-(cy-H/2)/k,-90,90)};}
var dx=(cx/W*2-1)*r*f,dy=(1-cy/H*2)*f,dz=1,n=Math.sqrt(dx*dx+dy*dy+1),a=st.y*D,b=st.p*D;dx/=n;dy/=n;dz/=n;
var y1=dy*Math.cos(b)+dz*Math.sin(b),z1=dz*Math.cos(b)-dy*Math.sin(b);
var x2=dx*Math.cos(a)+z1*Math.sin(a),z2=z1*Math.cos(a)-dx*Math.sin(a);
return {y:Math.atan2(x2,z2)/D,p:Math.asin(fra(y1,-1,1))/D};
}
function posa(){
bott.forEach(function(b){
if(b.hasAttribute("hidden")){return;}
var p=PT[+b.getAttribute("data-p")];if(!p){return;}
var q=proietta(p.y,p.p),dentro=false,m=MZ+8;
b._o36pieno=false;
if(q){if(q.x>-MZ){if(q.x<W+MZ){if(q.y>-MZ){if(q.y<H+MZ){dentro=true;}}}}}
if(!dentro){b.classList.add("is-fuori");return;}
if(q.x>=m){if(q.x<=W-m){if(q.y>=m){if(q.y<=H-m){b._o36pieno=true;}}}}
b.style.transform="translate3d("+(q.x-MZ).toFixed(1)+"px,"+(q.y-MZ).toFixed(1)+"px,0)";
b.classList.remove("is-fuori");
if(q.y<64){b.classList.add("o36-punto--sotto");}else{b.classList.remove("o36-punto--sotto");}
});
var q2=null;
if(segno){if(segnato){q2=proietta(segno.y,segno.p);if(q2){segnato.style.transform="translate3d("+q2.x.toFixed(1)+"px,"+q2.y.toFixed(1)+"px,0)";vedi(segnato,true);}else{vedi(segnato,false);}}}
if(lettura){var m=segno||st;lettura.textContent=(T.dir||"")+" "+Math.round(m.y)+"° · "+(T.incl||"")+" "+Math.round(m.p)+"°";if(q2){lettura.style.left=q2.x.toFixed(1)+"px";lettura.style.top=(q2.y+20).toFixed(1)+"px";}else{lettura.style.left="";lettura.style.top="";}}
}
function disegna(now){
if(modo==="gl"){
if(gl){if(t0){
var r=W/H,f=fDi(st.f),m=1,y1=st.y,p1=st.p,f1=f,f0=f,e,s2;
if(fade){
e=Math.min(1,Math.max(0,(now-fade.t)/450));s2=e*e*(3-2*e);m=s2;y1=fade.y;p1=fade.p;
if(fade.v){y1=fade.y+g180(fade.v.y-fade.y)*0.35*s2;p1=fade.p+(fade.v.p-fade.p)*0.35*s2;}
f1=fade.f*(1-0.28*s2);f0=f*(1+0.1*(1-s2));
if(e>=1){fade=null;m=1;f0=f;if(t1){gl.deleteTexture(t1);t1=null;}}
}
gl.viewport(0,0,cv.width,cv.height);
gl.uniform1f(L.r,r);gl.uniform1f(L.m,m);
gl.uniform3f(L.c0,st.y*D,st.p*D,f0);gl.uniform3f(L.c1,y1*D,p1*D,f1);
gl.activeTexture(gl.TEXTURE1);gl.bindTexture(gl.TEXTURE_2D,t1||t0);
gl.activeTexture(gl.TEXTURE0);gl.bindTexture(gl.TEXTURE_2D,t0);
gl.drawArrays(gl.TRIANGLES,0,3);
}}
}else if(piano){
st.p=limP(st.p);
var k=H/vf(),bw=360*k,bh=180*k;
piano.style.backgroundSize=bw.toFixed(1)+"px "+bh.toFixed(1)+"px";
piano.style.backgroundPosition=(W/2-(0.5+st.y/360)*bw).toFixed(1)+"px "+(H/2-(0.5-st.p/180)*bh).toFixed(1)+"px";
}
posa();
}
function misura(){
W=vista.clientWidth||1;H=vista.clientHeight||1;
if(cv){var d=Math.min(2,window.devicePixelRatio||1),w=Math.max(1,Math.round(W*d)),h=Math.max(1,Math.round(H*d));if(cv.width!==w){cv.width=w;}if(cv.height!==h){cv.height=h;}}
if(pronta){disegna(ora());}
}
function azione(){
ult=ora();rot=0;if(tRip){clearTimeout(tRip);tRip=0;}
var rip=+C.rip||0;if(C.auto){if(rip>0){tRip=setTimeout(function(){tRip=0;richiedi();},rip*1000+60);}}
}
function usa(){if(usata){return;}usata=true;el.classList.add("is-usata");}
function sposta(dy,dp,df){var m=mira||{dy:0,p:st.p,f:st.f};m.dy+=dy;m.p=limP(m.p+dp);m.f=limF(m.f+df);mira=m;richiedi();}
function guarda(p){mira={dy:g180(p.y-st.y),p:limP(p.p),f:st.f};azione();usa();richiedi();}
function daTastiera(n){try{return n.matches(":focus-visible");}catch(e){return true;}}
function nTocchi(){var n=0,k;for(k in tocchi){if(Object.prototype.hasOwnProperty.call(tocchi,k)){n+=1;}}return n;}
function distanza(){var a=[],k,dx,dy;for(k in tocchi){if(Object.prototype.hasOwnProperty.call(tocchi,k)){a.push(tocchi[k]);}}if(a.length<2){return 0;}dx=a[0].x-a[1].x;dy=a[0].y-a[1].y;return Math.sqrt(dx*dx+dy*dy);}
tela.addEventListener("pointerdown",function(e){
if(e.pointerType==="mouse"){if(e.button!==0){return;}}
if(!pronta){return;}
tocchi[e.pointerId]={x:e.clientX,y:e.clientY};
try{tela.setPointerCapture(e.pointerId);}catch(x){}
azione();vy=0;vp=0;mira=null;
if(nTocchi()>=2){tr=null;tela.classList.remove("is-presa");if(C.zoom){pz={d:distanza(),f:st.f};usa();}return;}
var rc=tela.getBoundingClientRect();
tr={id:e.pointerId,x:e.clientX,y:e.clientY,x0:e.clientX,y0:e.clientY,t:ora(),k:vf()/Math.max(1,rc.height),via:false};
tela.classList.add("is-presa");
});
tela.addEventListener("pointermove",function(e){
var p=tocchi[e.pointerId],t,dt,dx,dy,ax,ay,d;if(!p){return;}
p.x=e.clientX;p.y=e.clientY;
if(pz){d=distanza();if(d>0){if(pz.d>0){st.f=limF(pz.f*pz.d/d);richiedi();}}return;}
if(!tr){return;}if(e.pointerId!==tr.id){return;}
if(!tr.via){ax=e.clientX-tr.x0;ay=e.clientY-tr.y0;if(ax*ax+ay*ay<25){return;}tr.via=true;usa();segno=null;vedi(segnato,false);}
t=ora();dt=Math.max(1,t-tr.t);dx=e.clientX-tr.x;dy=e.clientY-tr.y;
st.y=g180(st.y-dx*tr.k);st.p=limP(st.p+dy*tr.k);
vy=0.75*(-dx*tr.k/dt)+0.25*vy;vp=0.75*(dy*tr.k/dt)+0.25*vp;
tr.x=e.clientX;tr.y=e.clientY;tr.t=t;
richiedi();
});
function tocco(e){
if(!C.builder){return;}
var r=tela.getBoundingClientRect();if(!r.width){return;}if(!r.height){return;}
segno=direzione((e.clientX-r.left)*W/r.width,(e.clientY-r.top)*H/r.height);usa();posa();
}
function rilascia(e){
if(!tocchi[e.pointerId]){return;}
delete tocchi[e.pointerId];
try{tela.releasePointerCapture(e.pointerId);}catch(x){}
if(pz){if(nTocchi()<2){pz=null;}tr=null;tela.classList.remove("is-presa");return;}
if(!tr){return;}if(e.pointerId!==tr.id){return;}
var t=tr;tr=null;tela.classList.remove("is-presa");
if(t.via){if(riduci){vy=0;vp=0;}else if(ora()-t.t>90){vy=0;vp=0;}vy=fra(vy,-0.6,0.6);vp=fra(vp,-0.6,0.6);}
else{vy=0;vp=0;if(e.type==="pointerup"){tocco(e);}}
azione();richiedi();
}
tela.addEventListener("pointerup",rilascia);
tela.addEventListener("pointercancel",rilascia);
tela.addEventListener("lostpointercapture",rilascia);
tela.addEventListener("keydown",function(e){
var k=e.key,g=e.shiftKey?30:10,ok=true,dy=0,dp=0,df=0;
if(k==="ArrowLeft"||k==="Left"){dy=-g;}else if(k==="ArrowRight"||k==="Right"){dy=g;}else if(k==="ArrowUp"||k==="Up"){dp=g;}else if(k==="ArrowDown"||k==="Down"){dp=-g;}
else if(k==="+"||k==="="||k==="Add"){if(C.zoom){df=-10;}else{ok=false;}}
else if(k==="-"||k==="_"||k==="Subtract"){if(C.zoom){df=10;}else{ok=false;}}
else{ok=false;}
if(!ok){return;}if(!pronta){return;}
e.preventDefault();azione();usa();sposta(dy,dp,df);
});
vista.addEventListener("wheel",function(e){
if(!C.zoom){return;}if(!e.ctrlKey){return;}if(!pronta){return;}
e.preventDefault();
var d=e.deltaY*(e.deltaMode===1?16:(e.deltaMode===2?400:1));
mira=null;st.f=limF(st.f*Math.exp(fra(d,-200,200)*0.0018));azione();usa();richiedi();
},{passive:false});
vista.addEventListener("gesturestart",function(e){if(!C.zoom){return;}e.preventDefault();if(pz){return;}gesto=st.f;azione();});
vista.addEventListener("gesturechange",function(e){if(!C.zoom){return;}e.preventDefault();if(pz){return;}if(gesto){st.f=limF(gesto/(e.scale||1));richiedi();}});
vista.addEventListener("gestureend",function(e){if(!C.zoom){return;}e.preventDefault();gesto=0;});
function apri(i,b){
var p=PT[i];if(!p){return;}if(!pan){return;}
if(aperto===b){chiudiPannello(true);return;}
chiudiPannello(false);
if(pTit){pTit.textContent=p.ti||T.info||"";}
if(pTxt){pTxt.innerHTML=p.tx||"";}
vedi(pan,true);aperto=b;b.setAttribute("aria-expanded","true");azione();usa();
try{pan.focus({preventScroll:true});}catch(x){pan.focus();}
document.addEventListener("pointerdown",fuori,true);
}
function chiudiPannello(rifoc){
if(!aperto){return;}
var b=aperto;aperto=null;vedi(pan,false);b.setAttribute("aria-expanded","false");
document.removeEventListener("pointerdown",fuori,true);
if(rifoc){try{b.focus({preventScroll:true});}catch(x){b.focus();}}
azione();richiedi();
}
function fuori(e){
if(!aperto){return;}if(pan.contains(e.target)){return;}if(aperto.contains(e.target)){return;}
var b=aperto;chiudiPannello(false);
setTimeout(function(){var a=document.activeElement;if(!a){a=document.body;}if(a===document.body){if(!b.hasAttribute("hidden")){try{b.focus({preventScroll:true});}catch(x){}}}},0);
}
if(pan){pan.addEventListener("keydown",function(e){if(e.key==="Escape"||e.key==="Esc"){e.preventDefault();e.stopPropagation();chiudiPannello(true);}});}
if(pX){pX.addEventListener("click",function(){chiudiPannello(true);});}
bott.forEach(function(b){
var i=+b.getAttribute("data-p"),p=PT[i];if(!p){return;}
b.addEventListener("pointerenter",function(){sopra=b;});
b.addEventListener("pointerleave",function(){if(sopra===b){sopra=null;richiedi();}});
b.addEventListener("focus",function(){foco=b;if(!b._o36pieno){if(daTastiera(b)){guarda(p);}}});
b.addEventListener("blur",function(){if(foco===b){foco=null;richiedi();}});
if(p.k==="scena"){b.addEventListener("click",function(e){e.preventDefault();var dentro=el.contains(document.activeElement);azione();usa();vai(p.d,{dissolvi:true,verso:{y:p.y,p:p.p}});if(dentro){try{tela.focus({preventScroll:true});}catch(x){}}});}
else if(p.k==="info"){b.addEventListener("click",function(e){e.preventDefault();apri(i,b);});}
});
mini.forEach(function(m){m.addEventListener("click",function(){azione();usa();vai(+m.getAttribute("data-s"),{dissolvi:true});});});
if(zPiu){zPiu.addEventListener("click",function(){if(!pronta){return;}azione();usa();sposta(0,0,-10);});}
if(zMeno){zMeno.addEventListener("click",function(){if(!pronta){return;}azione();usa();sposta(0,0,10);});}
if(riprova){riprova.addEventListener("click",function(){var k=errI;nascondiErrore();if(k<0){return;}delete cache[k];vai(k,{});});}
function inFS(){return (document.fullscreenElement||document.webkitFullscreenElement||null)===el;}
function cambioFS(){var s=inFS();if(s){el.classList.add("is-schermo");}else{el.classList.remove("is-schermo");}if(fsB){fsB.setAttribute("aria-label",s?(T.fsEsci||""):(T.fsApri||""));}misura();}
var puoFS=false;
if(el.requestFullscreen||el.webkitRequestFullscreen){if(document.fullscreenEnabled||document.webkitFullscreenEnabled){puoFS=true;}}
if(fsB){if(puoFS){vedi(fsB,true);fsB.addEventListener("click",function(){
var r;
if(inFS()){if(document.exitFullscreen){r=document.exitFullscreen();}else if(document.webkitExitFullscreen){document.webkitExitFullscreen();}}
else if(el.requestFullscreen){r=el.requestFullscreen();}else{el.webkitRequestFullscreen();}
if(r){if(r.catch){r.catch(function(){});}}
});}}
document.addEventListener("fullscreenchange",cambioFS);document.addEventListener("webkitfullscreenchange",cambioFS);
function torna(){if(!document.hidden){tp=0;richiedi();}}
document.addEventListener("visibilitychange",torna);
if(cv){cv.addEventListener("webglcontextlost",function(e){e.preventDefault();if(chiuso){return;}gl=null;t0=null;t1=null;passaPiano();if(cur>=0){sfondo(cache[cur]);}if(pronta){disegna(ora());}},false);}
function chiudi(){
chiuso=true;if(raf){window.cancelAnimationFrame(raf);raf=0;}if(tRip){clearTimeout(tRip);tRip=0;}if(tPre){clearTimeout(tPre);tPre=0;}
if(io1){io1.disconnect();}if(io2){io2.disconnect();}if(ro){ro.disconnect();}else{window.removeEventListener("resize",misura);}
document.removeEventListener("fullscreenchange",cambioFS);document.removeEventListener("webkitfullscreenchange",cambioFS);document.removeEventListener("visibilitychange",torna);
document.removeEventListener("pointerdown",fuori,true);
if(gl){try{var x=gl.getExtension("WEBGL_lose_context");if(x){x.loseContext();}}catch(e){}}
gl=null;t0=null;t1=null;cache={};
}
I.push({el:el,chiudi:chiudi});
function avvia(){if(avviato){return;}avviato=true;var k=+C.ini||0;if(!SC[k]){k=0;}vai(k,{});}
modo=iniziaGL()?"gl":"piano";if(modo==="piano"){el.classList.add("is-piano");}
misura();
if(window.ResizeObserver){ro=new ResizeObserver(function(){misura();});ro.observe(vista);}else{window.addEventListener("resize",misura);}
if(window.IntersectionObserver){
io1=new IntersectionObserver(function(en){var x=en[en.length-1];if(x){if(x.isIntersecting){io1.disconnect();avvia();}}},{rootMargin:"300px 0px"});io1.observe(vista);
io2=new IntersectionObserver(function(en){var x=en[en.length-1];if(x){visibile=x.isIntersecting;if(visibile){richiedi();}}});io2.observe(vista);
}else{visibile=true;avvia();}
};}
JS;
    }
}
