<?php
/**
 * Tile Orario corsi — l'orario settimanale dei corsi (palestra, piscina, scuola di danza, studio di yoga) con i
 * filtri, il giorno di oggi in evidenza, l'ora attuale e i dettagli di ogni lezione.
 *
 * I corsi hanno un codice (id), un nome, una frase, la durata e un colore (vuoto = i colori del tema a rotazione).
 * Ogni lezione ha il giorno (1 = lunedì), l'ora d'inizio e di fine (senza fine: l'inizio più la durata del corso, o
 * un'ora), il corso (per codice o per nome: un corso che non c'è nasce dal testo scritto), l'istruttore, la sala, il
 * livello, i posti e una nota. Si mostrano solo i giorni che hanno lezioni.
 * Vista «griglia»: una colonna per giorno con l'asse delle ore (righe a ogni ora e, più leggere, a ogni mezz'ora;
 * «altezza_ora» px per ora), ogni lezione alta quanto dura; due lezioni sovrapposte nello stesso giorno si affiancano
 * (corsie calcolate qui, ricalcolate nel browser dopo ogni filtro sulle sole lezioni rimaste). La griglia si allarga
 * da sola se una lezione cade fuori da «Dalle»–«Alle». Vista «elenco»: le lezioni una sotto l'altra per giorno.
 * Sul telefono (sotto i 640 px) sempre un giorno alla volta: le schede dei giorni (role="tablist", frecce, Home ed
 * End; aperta oggi, o il primo giorno con lezioni dopo oggi) e sotto le lezioni del giorno; i filtri restano sopra.
 * Filtri: un pulsante per corso (aria-pressed) e «Tutti», i menu facoltativi del livello (un livello mostra anche le
 * lezioni «per tutti i livelli») e dell'istruttore. Le lezioni escluse spariscono in dissolvenza (subito con «riduci
 * movimento»), il conteggio sta in una regione aria-live, senza risultati compare «testo_vuoto».
 * Oggi e l'ora attuale vengono dall'orologio del browser: la colonna di oggi col colore di oggi, la linea dell'ora
 * nella griglia (aggiornata ogni minuto e al ritorno sulla scheda), «In corso» sulla lezione che si sta svolgendo.
 * Il clic (o Invio) su una lezione apre un <dialog> modale (top layer: non lo tocca il transform del template;
 * Esc, clic fuori, focus intrappolato e restituito alla lezione) col pulsante Prenota nello stile pulsanti del tema:
 * modello d'indirizzo con {corso} (codice del corso), {giorno} (1–7) e {ora} ('HH:MM'), valori codificati.
 * Nata per i temi della palestra (15 ott 2026). Config: src/config/elements/orariocorsi.js.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Orariocorsi_Tile extends Olobuild_Tile_Base {

    protected $type     = 'orariocorsi';
    protected $name     = 'Orario corsi';
    protected $icon     = 'dashicons-schedule';
    protected $category = 'interactive';

    protected $defaults = [
        // contenuto
        'corsi'                      => [
            [ 'id' => 'cycling', 'nome' => 'Indoor cycling', 'colore' => '', 'descrizione' => 'Pedalata a ritmo di musica, fra salite e sprint guidati: si suda e ci si diverte.', 'durata' => 45 ],
            [ 'id' => 'pilates', 'nome' => 'Pilates', 'colore' => '', 'descrizione' => 'Core, postura e respiro sul tappetino, con movimenti lenti e precisi.', 'durata' => 55 ],
            [ 'id' => 'yoga', 'nome' => 'Yoga flow', 'colore' => '', 'descrizione' => 'Sequenze fluide che uniscono forza, equilibrio e respiro.', 'durata' => 60 ],
            [ 'id' => 'functional', 'nome' => 'Functional training', 'colore' => '', 'descrizione' => 'Circuiti a corpo libero e con piccoli attrezzi, per forza e resistenza.', 'durata' => 45 ],
            [ 'id' => 'boxe', 'nome' => 'Boxe fit', 'colore' => '', 'descrizione' => 'I colpi della boxe al sacco e tanto allenamento, senza contatto.', 'durata' => 50 ],
        ],
        'lezioni'                    => [
            [ 'id' => 'l-1', 'giorno' => 1, 'inizio' => '07:00', 'fine' => '07:45', 'corso' => 'cycling', 'istruttore' => 'Marco', 'sala' => 'Sala Spin', 'livello' => 'intermedio', 'posti' => 20, 'nota' => 'Porta un asciugamano e una borraccia.' ],
            [ 'id' => 'l-2', 'giorno' => 1, 'inizio' => '12:30', 'fine' => '13:25', 'corso' => 'pilates', 'istruttore' => 'Giulia', 'sala' => 'Studio', 'livello' => 'base', 'posti' => 12, 'nota' => '' ],
            [ 'id' => 'l-3', 'giorno' => 1, 'inizio' => '18:00', 'fine' => '18:45', 'corso' => 'functional', 'istruttore' => 'Luca', 'sala' => 'Sala 1', 'livello' => 'tutti', 'posti' => 16, 'nota' => '' ],
            [ 'id' => 'l-4', 'giorno' => 1, 'inizio' => '18:30', 'fine' => '19:30', 'corso' => 'yoga', 'istruttore' => 'Sara', 'sala' => 'Sala 2', 'livello' => 'base', 'posti' => 14, 'nota' => '' ],
            [ 'id' => 'l-5', 'giorno' => 1, 'inizio' => '19:45', 'fine' => '20:30', 'corso' => 'cycling', 'istruttore' => 'Marco', 'sala' => 'Sala Spin', 'livello' => 'avanzato', 'posti' => 20, 'nota' => '' ],
            [ 'id' => 'l-6', 'giorno' => 2, 'inizio' => '07:00', 'fine' => '08:00', 'corso' => 'yoga', 'istruttore' => 'Sara', 'sala' => 'Studio', 'livello' => 'tutti', 'posti' => 14, 'nota' => '' ],
            [ 'id' => 'l-7', 'giorno' => 2, 'inizio' => '13:00', 'fine' => '13:30', 'corso' => 'functional', 'istruttore' => 'Luca', 'sala' => 'Sala 1', 'livello' => 'intermedio', 'posti' => 16, 'nota' => 'Mezz\'ora intensa, per la pausa pranzo.' ],
            [ 'id' => 'l-8', 'giorno' => 2, 'inizio' => '18:00', 'fine' => '18:50', 'corso' => 'boxe', 'istruttore' => 'Elena', 'sala' => 'Sala 2', 'livello' => 'base', 'posti' => 14, 'nota' => 'I guantoni te li prestiamo noi, alla reception.' ],
            [ 'id' => 'l-9', 'giorno' => 2, 'inizio' => '19:00', 'fine' => '19:55', 'corso' => 'pilates', 'istruttore' => 'Giulia', 'sala' => 'Studio', 'livello' => 'intermedio', 'posti' => 12, 'nota' => '' ],
            [ 'id' => 'l-10', 'giorno' => 3, 'inizio' => '07:00', 'fine' => '07:45', 'corso' => 'cycling', 'istruttore' => 'Marco', 'sala' => 'Sala Spin', 'livello' => 'tutti', 'posti' => 20, 'nota' => '' ],
            [ 'id' => 'l-11', 'giorno' => 3, 'inizio' => '12:30', 'fine' => '13:15', 'corso' => 'functional', 'istruttore' => 'Luca', 'sala' => 'Sala 1', 'livello' => 'avanzato', 'posti' => 16, 'nota' => '' ],
            [ 'id' => 'l-12', 'giorno' => 3, 'inizio' => '18:00', 'fine' => '19:00', 'corso' => 'yoga', 'istruttore' => 'Sara', 'sala' => 'Studio', 'livello' => 'intermedio', 'posti' => 14, 'nota' => '' ],
            [ 'id' => 'l-13', 'giorno' => 3, 'inizio' => '19:15', 'fine' => '20:05', 'corso' => 'boxe', 'istruttore' => 'Elena', 'sala' => 'Sala 2', 'livello' => 'intermedio', 'posti' => 14, 'nota' => '' ],
            [ 'id' => 'l-14', 'giorno' => 4, 'inizio' => '07:30', 'fine' => '08:25', 'corso' => 'pilates', 'istruttore' => 'Giulia', 'sala' => 'Studio', 'livello' => 'base', 'posti' => 12, 'nota' => '' ],
            [ 'id' => 'l-15', 'giorno' => 4, 'inizio' => '12:30', 'fine' => '13:15', 'corso' => 'cycling', 'istruttore' => 'Marco', 'sala' => 'Sala Spin', 'livello' => 'intermedio', 'posti' => 20, 'nota' => '' ],
            [ 'id' => 'l-16', 'giorno' => 4, 'inizio' => '18:00', 'fine' => '18:50', 'corso' => 'boxe', 'istruttore' => 'Elena', 'sala' => 'Sala 2', 'livello' => 'avanzato', 'posti' => 14, 'nota' => '' ],
            [ 'id' => 'l-17', 'giorno' => 4, 'inizio' => '19:00', 'fine' => '19:45', 'corso' => 'functional', 'istruttore' => 'Luca', 'sala' => 'Sala 1', 'livello' => 'tutti', 'posti' => 16, 'nota' => '' ],
            [ 'id' => 'l-18', 'giorno' => 5, 'inizio' => '07:00', 'fine' => '07:45', 'corso' => 'functional', 'istruttore' => 'Luca', 'sala' => 'Sala 1', 'livello' => 'intermedio', 'posti' => 16, 'nota' => '' ],
            [ 'id' => 'l-19', 'giorno' => 5, 'inizio' => '12:30', 'fine' => '13:30', 'corso' => 'yoga', 'istruttore' => 'Sara', 'sala' => 'Studio', 'livello' => 'tutti', 'posti' => 14, 'nota' => '' ],
            [ 'id' => 'l-20', 'giorno' => 5, 'inizio' => '18:00', 'fine' => '18:45', 'corso' => 'cycling', 'istruttore' => 'Marco', 'sala' => 'Sala Spin', 'livello' => 'avanzato', 'posti' => 20, 'nota' => '' ],
            [ 'id' => 'l-21', 'giorno' => 5, 'inizio' => '19:00', 'fine' => '19:55', 'corso' => 'pilates', 'istruttore' => 'Giulia', 'sala' => 'Studio', 'livello' => 'intermedio', 'posti' => 12, 'nota' => '' ],
            [ 'id' => 'l-22', 'giorno' => 6, 'inizio' => '09:30', 'fine' => '10:30', 'corso' => 'yoga', 'istruttore' => 'Sara', 'sala' => 'Studio', 'livello' => 'tutti', 'posti' => 14, 'nota' => 'Con la bella stagione si fa in giardino.' ],
            [ 'id' => 'l-23', 'giorno' => 6, 'inizio' => '10:00', 'fine' => '10:45', 'corso' => 'cycling', 'istruttore' => 'Marco', 'sala' => 'Sala Spin', 'livello' => 'tutti', 'posti' => 20, 'nota' => '' ],
            [ 'id' => 'l-24', 'giorno' => 6, 'inizio' => '11:00', 'fine' => '11:50', 'corso' => 'boxe', 'istruttore' => 'Elena', 'sala' => 'Sala 2', 'livello' => 'base', 'posti' => 14, 'nota' => '' ],
        ],
        'giorni'                     => 'Lunedì,Martedì,Mercoledì,Giovedì,Venerdì,Sabato,Domenica',
        'vista'                      => 'griglia',
        'ora_inizio'                 => '07:00',
        'ora_fine'                   => '22:00',
        'mostra_filtri'              => true,
        'etichetta_tutti'            => 'Tutti',
        'filtro_livello'             => false,
        'filtro_istruttore'          => false,
        'evidenzia_oggi'             => true,
        'mostra_adesso'              => true,
        'dettagli'                   => true,
        'testo_prenota'              => 'Prenota',
        'link_prenota'               => '',
        'testo_vuoto'                => 'Nessuna lezione con questi filtri.',
        // stile
        'colore_testo'               => '',
        'colore_tenue'               => '',
        'sfondo_griglia'             => '',
        'colore_linee'               => '',
        'sfondo_lezione'             => '',
        'colore_oggi'                => '',
        'colore_adesso'              => '',
        'ombra'                      => 'none',
        'ombra_h'                    => '0',
        'ombra_v'                    => '4',
        'ombra_blur'                 => '10',
        'ombra_spread'               => '0',
        'ombra_color'                => 'rgba(0,0,0,0.15)',
        'ombra_inset'                => false,
        'giorno_font_family'         => '',
        'giorno_font_size'           => 13,
        'giorno_font_size_tablet'    => '',
        'giorno_font_size_mobile'    => '',
        'giorno_font_weight'         => '600',
        'giorno_uppercase'           => true,
        'giorno_letter_spacing'      => 1.2,
        'giorno_color'               => '',
        'ora_font_family'            => '',
        'ora_font_size'              => 12,
        'ora_font_size_tablet'       => '',
        'ora_font_size_mobile'       => '',
        'ora_font_weight'            => '600',
        'ora_color'                  => '',
        'corso_font_family'          => '',
        'corso_font_size'            => 14,
        'corso_font_size_tablet'     => '',
        'corso_font_size_mobile'     => '',
        'corso_font_weight'          => '600',
        'corso_line_height'          => 1.25,
        'corso_color'                => '',
        'dettaglio_font_family'      => '',
        'dettaglio_font_size'        => 12,
        'dettaglio_font_size_tablet' => '',
        'dettaglio_font_size_mobile' => '',
        'dettaglio_font_weight'      => '400',
        'dettaglio_color'            => '',
        'raggio_lezione'             => [ 'tl' => 10, 'tr' => 10, 'br' => 10, 'bl' => 10 ],
        'altezza_ora'                => 72,
        'gap'                        => 8,
        'forma_filtri'               => 'pillola',
        'allineamento_filtri'        => 'left',
    ];

    /** I livelli ammessi, nell'ordine dei filtri. */
    const LIVELLI = [ 'base', 'intermedio', 'avanzato', 'tutti' ];

    /** Uid già dati in questa richiesta: lo stesso nodo reso due volte (un widget ripetuto) resta distinto. */
    private static $uid_usati = [];

    public function get_controls() {
        return [];
    }

    /** I nomi dei giorni nella lingua del sito (quando «giorni» non li scrive tutti), da lunedì. */
    private static function nomi_giorni() {
        return [
            1 => __( 'Lunedì', 'olobuilder' ),
            2 => __( 'Martedì', 'olobuilder' ),
            3 => __( 'Mercoledì', 'olobuilder' ),
            4 => __( 'Giovedì', 'olobuilder' ),
            5 => __( 'Venerdì', 'olobuilder' ),
            6 => __( 'Sabato', 'olobuilder' ),
            7 => __( 'Domenica', 'olobuilder' ),
        ];
    }

    /**
     * Le etichette dei livelli, tradotte. Al plurale, come nei calendari delle palestre («Livello: principianti»):
     * «Avanzato» è già nel catalogo col senso di «impostazioni avanzate» (de «Erweitert»), che per un corso non va.
     */
    private static function livelli() {
        return [
            'base'       => __( 'Principianti', 'olobuilder' ),
            'intermedio' => __( 'Intermedi', 'olobuilder' ),
            'avanzato'   => __( 'Avanzati', 'olobuilder' ),
            'tutti'      => __( 'Tutti i livelli', 'olobuilder' ),
        ];
    }

    /**
     * Il testo da usare su un fondo: '' se $testo lì si legge, altrimenti il chiaro o lo scuro della Palette
     * (Olobuild_Tile_Utils::testo_su_fondo). Sul primario e sul secondario del tema vale il colore che il tema abbina
     * a loro, anche quando i token non si possono risolvere qui (anteprime fuori dal sito).
     */
    private static function testo_per( $fondo, $testo ) {
        if ( preg_match( '/^var\(\s*--olo-color-(primary|secondary)\s*[,)]/', (string) $fondo, $m ) ) {
            return 'var(--olo-color-' . $m[1] . '-contrast, #ffffff)';
        }
        return Olobuild_Tile_Utils::testo_su_fondo( $fondo, $testo );
    }

    /** Un'ora 'HH:MM' (anche 'H.MM') in minuti dalla mezzanotte, o null. */
    private static function minuti( $v ) {
        $v = trim( (string) $v );
        if ( ! preg_match( '/^(\d{1,2})[:.](\d{2})$/', $v, $m ) ) {
            return null;
        }
        $t = (int) $m[1] * 60 + (int) $m[2];
        if ( (int) $m[2] > 59 || $t > 1440 ) {
            return null;
        }
        return $t;
    }

    /** Minuti dalla mezzanotte in 'HH:MM'. */
    private static function hhmm( $t ) {
        $t = max( 0, (int) $t );
        return sprintf( '%02d:%02d', intdiv( $t, 60 ), $t % 60 );
    }

    /** Un numero per il CSS: col punto, senza zeri inutili (le impostazioni locali non lo cambiano). */
    private static function num( $x ) {
        $s = number_format( (float) $x, 4, '.', '' );
        return rtrim( rtrim( $s, '0' ), '.' );
    }

    /** La chiave di un codice o di un nome: minuscole, senza accenti, solo lettere, cifre, «_» e «-». */
    private static function chiave( $v ) {
        $v = trim( (string) $v );
        if ( '' === $v ) {
            return '';
        }
        if ( function_exists( 'remove_accents' ) ) {
            $v = remove_accents( $v );
        }
        $v = function_exists( 'mb_strtolower' ) ? mb_strtolower( $v, 'UTF-8' ) : strtolower( $v );
        $v = (string) preg_replace( '/[^a-z0-9_]+/', '-', $v );
        return substr( trim( $v, '-' ), 0, 60 );
    }

    /** Le prime tre lettere del nome di un giorno, per le schede sul telefono (un nome corto resta intero). */
    private static function breve( $nome ) {
        $n   = trim( (string) $nome );
        $len = function_exists( 'mb_strlen' ) ? mb_strlen( $n, 'UTF-8' ) : strlen( $n );
        if ( $len <= 4 ) {
            return $n;
        }
        return function_exists( 'mb_substr' ) ? mb_substr( $n, 0, 3, 'UTF-8' ) : substr( $n, 0, 3 );
    }

    /**
     * Il modello d'indirizzo della prenotazione: http(s), relativo o «//…»; '' per gli altri schemi (javascript:,
     * data:…), con spazi o con caratteri che non stanno in un indirizzo. Le graffe dei segnaposto restano (esc_url le
     * toglierebbe): l'indirizzo lo compone il browser, che controlla di nuovo lo schema.
     */
    private static function modello( $v ) {
        if ( is_array( $v ) ) {
            $v = $v['url'] ?? '';
        }
        $v = trim( (string) $v );
        if ( '' === $v || preg_match( '/[\s<>"\'\\\\`]/', $v ) ) {
            return '';
        }
        if ( preg_match( '/^([a-z][a-z0-9+.\-]*):/i', $v, $m ) ) {
            if ( ! in_array( strtolower( $m[1] ), [ 'http', 'https' ], true ) ) {
                return '';
            }
        }
        return substr( $v, 0, 2000 );
    }

    /**
     * CSS di un testo dal controllo tipografia: famiglia, misura (anche per tablet e telefono), peso, maiuscolo,
     * spaziatura, interlinea e colore. Senza $con_misura niente misura e interlinea (i dettagli e le schede dei giorni
     * prendono solo il carattere); senza $con_colore niente colore (il riquadro dei dettagli ha il suo fondo).
     */
    private function css_testo( $s, $k, $selettore, $con_misura = true, $con_colore = true ) {
        $d   = '';
        $fam = $this->resolve_font_family( $s[ $k . '_font_family' ] ?? '' );
        $fs  = (float) ( $s[ $k . '_font_size' ] ?? 0 );
        $fw  = $this->font_weight_css( $s[ $k . '_font_weight' ] ?? '' );
        $ls  = $s[ $k . '_letter_spacing' ] ?? '';
        $lh  = $s[ $k . '_line_height' ] ?? '';
        $col = $con_colore ? $this->safe_color_css( $s[ $k . '_color' ] ?? '' ) : '';
        if ( '' !== $fam ) {
            $d .= 'font-family:' . $fam . ';';
        }
        if ( $con_misura && $fs > 0 ) {
            $d .= 'font-size:' . self::num( $fs ) . 'px;';
        }
        if ( $fw ) {
            $d .= 'font-weight:' . $fw . ';';
        }
        if ( array_key_exists( $k . '_uppercase', $s ) ) {
            $d .= 'text-transform:' . ( ! empty( $s[ $k . '_uppercase' ] ) ? 'uppercase' : 'none' ) . ';';
        }
        if ( is_numeric( $ls ) ) {
            $d .= 'letter-spacing:' . self::num( $ls ) . 'px;';
        }
        if ( $con_misura && is_numeric( $lh ) ) {
            $d .= 'line-height:' . self::num( $lh ) . ';';
        }
        if ( '' !== $col ) {
            $d .= 'color:' . $col . ';';
        }
        $css = '' !== $d ? "{$selettore}{{$d}}" : '';
        if ( $con_misura ) {
            foreach ( [ 'tablet' => 960, 'mobile' => 480 ] as $disp => $px ) {
                $v = (float) ( $s[ $k . '_font_size_' . $disp ] ?? 0 );
                if ( $v > 0 ) {
                    $css .= '@media (max-width:' . $px . 'px){' . $selettore . '{font-size:' . self::num( $v ) . 'px}}';
                }
            }
        }
        return $css;
    }

    public function render( $settings ) {
        $s        = wp_parse_args( $settings, $this->defaults );
        $uid      = 'ooc-' . $this->chiave_stabile( $settings );
        if ( isset( self::$uid_usati[ $uid ] ) ) {
            self::$uid_usati[ $uid ]++;
            $uid .= '-' . self::$uid_usati[ $uid ];
        } else {
            self::$uid_usati[ $uid ] = 1;
        }
        $sel      = '#' . $uid;
        $builder  = ! empty( $s['_builder_mode'] );
        $griglia  = 'elenco' !== (string) $s['vista'];
        $dettagli = ! empty( $s['dettagli'] );
        $oggi_on  = ! empty( $s['evidenzia_oggi'] );
        $ades_on  = ! empty( $s['mostra_adesso'] );

        // ── I nomi dei giorni: quelli scritti (da lunedì, separati da virgole), il resto nella lingua del sito ──
        $std     = self::nomi_giorni();
        $scritti = array_map( 'trim', explode( ',', (string) $s['giorni'] ) );
        $nomi    = [];
        for ( $g = 1; $g <= 7; $g++ ) {
            $n          = $scritti[ $g - 1 ] ?? '';
            $nomi[ $g ] = '' !== $n ? $n : $std[ $g ];
        }
        // Le schede del telefono: coi nomi predefiniti le abbreviazioni della lingua (Mo, Di… in tedesco), con nomi
        // scritti a mano le prime tre lettere.
        $brevi = [];
        if ( in_array( implode( ',', $scritti ), [ 'Lunedì,Martedì,Mercoledì,Giovedì,Venerdì,Sabato,Domenica', __( 'Lunedì,Martedì,Mercoledì,Giovedì,Venerdì,Sabato,Domenica', 'olobuilder' ) ], true ) ) {
            $b = array_map( 'trim', explode( ',', __( 'Lun,Mar,Mer,Gio,Ven,Sab,Dom', 'olobuilder' ) ) );
            if ( 7 === count( array_filter( $b, 'strlen' ) ) ) {
                $brevi = array_combine( range( 1, 7 ), $b );
            }
        }

        // ── I corsi, col colore scelto o uno del tema (sempre lo stesso per lo stesso posto nell'elenco) ──
        $tavolozza = [
            'var(--olo-color-primary)',
            'var(--olo-color-accent, #f4a23b)',
            'var(--olo-color-secondary, #16263d)',
            'color-mix(in srgb, var(--olo-color-primary) 50%, var(--olo-color-secondary, #16263d))',
            'color-mix(in srgb, var(--olo-color-accent, #f4a23b) 50%, var(--olo-color-secondary, #16263d))',
            'color-mix(in srgb, var(--olo-color-primary) 55%, var(--olo-color-accent, #f4a23b))',
        ];
        $nuovo = function ( $nome, $c, $k ) use ( $tavolozza ) {
            $col = $this->safe_color_css( $c['colore'] ?? '' );
            $col = '' !== $col ? $col : $tavolozza[ $k % count( $tavolozza ) ];
            return [
                'nome'   => $nome,
                'descr'  => trim( (string) ( $c['descrizione'] ?? '' ) ),
                'durata' => max( 0, min( 600, (int) ( $c['durata'] ?? 0 ) ) ),
                'colore' => $col,
                'su'     => self::testo_per( $col, 'var(--olo-color-light, #ffffff)' ) ?: 'var(--olo-color-light, #ffffff)',
            ];
        };
        $corsi = [];  // codice → corso
        $trova = [];  // chiave del codice o del nome → codice
        foreach ( array_values( (array) $s['corsi'] ) as $c ) {
            if ( ! is_array( $c ) ) {
                continue;
            }
            $nome = trim( (string) ( $c['nome'] ?? '' ) );
            $id   = trim( (string) ( $c['id'] ?? '' ) );
            // L'id che il builder dà a una voce nuova («ci-…») non è un codice da mettere in un indirizzo: vale il nome.
            $cod = preg_match( '/^ci-\d/', $id ) ? '' : self::chiave( $id );
            if ( '' === $cod ) {
                $cod = self::chiave( $nome );
            }
            if ( '' === $cod || isset( $corsi[ $cod ] ) ) {
                continue;
            }
            $corsi[ $cod ] = $nuovo( '' !== $nome ? $nome : $id, $c, count( $corsi ) );
            foreach ( [ self::chiave( $id ), self::chiave( $nome ), $cod ] as $kk ) {
                if ( '' !== $kk && ! isset( $trova[ $kk ] ) ) {
                    $trova[ $kk ] = $cod;
                }
            }
        }

        // ── Le lezioni valide, in ordine di giorno e d'ora ──
        $lezioni = [];
        foreach ( array_values( (array) $s['lezioni'] ) as $n => $l ) {
            if ( ! is_array( $l ) ) {
                continue;
            }
            $g   = (int) ( $l['giorno'] ?? 0 );
            $ini = self::minuti( $l['inizio'] ?? '' );
            $rif = trim( (string) ( $l['corso'] ?? '' ) );
            $kc  = self::chiave( $rif );
            if ( $g < 1 || $g > 7 || null === $ini || $ini >= 1440 || '' === $kc ) {
                continue;
            }
            if ( isset( $trova[ $kc ] ) ) {
                $cod = $trova[ $kc ];
            } else {
                // Un corso che non c'è nell'elenco nasce dal testo scritto nella lezione.
                $cod = $kc;
                if ( ! isset( $corsi[ $cod ] ) ) {
                    $corsi[ $cod ] = $nuovo( $rif, [], count( $corsi ) );
                }
                $trova[ $kc ] = $cod;
            }
            $fin = self::minuti( $l['fine'] ?? '' );
            if ( null === $fin || $fin <= $ini ) {
                $fin = $ini + ( $corsi[ $cod ]['durata'] > 0 ? $corsi[ $cod ]['durata'] : 60 );
            }
            $liv   = strtolower( trim( (string) ( $l['livello'] ?? '' ) ) );
            $posti = $l['posti'] ?? '';
            $lezioni[] = [
                'g'     => $g,
                'i'     => $ini,
                'f'     => min( 1440, $fin ),
                'c'     => $cod,
                'ist'   => trim( (string) ( $l['istruttore'] ?? '' ) ),
                'sala'  => trim( (string) ( $l['sala'] ?? '' ) ),
                'liv'   => in_array( $liv, self::LIVELLI, true ) ? $liv : '',
                'posti' => is_numeric( $posti ) ? max( 0, min( 9999, (int) $posti ) ) : 0,
                'nota'  => trim( (string) ( $l['nota'] ?? '' ) ),
                'n'     => $n,
                'col'   => 0,
                'cols'  => 1,
            ];
        }
        usort( $lezioni, static function ( $a, $b ) {
            foreach ( [ 'g', 'i', 'f', 'n' ] as $k ) {
                if ( $a[ $k ] !== $b[ $k ] ) {
                    return $a[ $k ] < $b[ $k ] ? -1 : 1;
                }
            }
            return 0;
        } );

        if ( ! $lezioni ) {
            if ( ! $builder ) {
                return '';
            }
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- selettore generato qui, CSS fisso
            return '<style>' . "{$sel} .ooc-nota{margin:0;padding:1rem 1.25rem;font-size:14px;line-height:1.5;opacity:.75;border:1px dashed color-mix(in srgb, currentColor 30%, transparent);border-radius:.75rem}" . '</style>'
                . '<div id="' . esc_attr( $uid ) . '" class="ooc ooc--vuota"><p class="ooc-nota">'
                . esc_html__( 'Aggiungi i corsi e le lezioni nel Contenuto: per ogni lezione il giorno, l\'ora e il corso.', 'olobuilder' )
                . '</p></div>';
        }

        // ── Per giorno, con le corsie: due lezioni che si sovrappongono si dividono la larghezza ──
        $per_giorno = [];
        foreach ( $lezioni as $k => $l ) {
            $per_giorno[ $l['g'] ][] = $k;
        }
        ksort( $per_giorno );
        foreach ( $per_giorno as $idx ) {
            $gruppo = [];
            $piste  = [];
            $fine_g = -1;
            foreach ( $idx as $k ) {
                $l = $lezioni[ $k ];
                if ( $gruppo && $l['i'] >= $fine_g ) {
                    foreach ( $gruppo as $x ) {
                        $lezioni[ $x ]['cols'] = count( $piste );
                    }
                    $gruppo = [];
                    $piste  = [];
                }
                $fine_g = $gruppo ? max( $fine_g, $l['f'] ) : $l['f'];
                $p      = 0;
                while ( isset( $piste[ $p ] ) && $piste[ $p ] > $l['i'] ) {
                    $p++;
                }
                $piste[ $p ]            = $l['f'];
                $lezioni[ $k ]['col']   = $p;
                $gruppo[]               = $k;
            }
            foreach ( $gruppo as $x ) {
                $lezioni[ $x ]['cols'] = count( $piste );
            }
        }
        $giorni = array_keys( $per_giorno );

        // ── Le ore della griglia: «Dalle»–«Alle», allargate se una lezione cade fuori, a ore intere ──
        $r_ini = self::minuti( $s['ora_inizio'] );
        $r_fin = self::minuti( $s['ora_fine'] );
        $r_ini = null === $r_ini ? 420 : $r_ini;
        $r_fin = null === $r_fin ? 1320 : $r_fin;
        $r_ini = min( $r_ini, min( array_column( $lezioni, 'i' ) ) );
        $r_fin = max( $r_fin, max( array_column( $lezioni, 'f' ) ) );
        $r_ini = intdiv( $r_ini, 60 ) * 60;
        $r_fin = min( 1440, (int) ceil( $r_fin / 60 ) * 60 );
        if ( $r_fin <= $r_ini ) {
            $r_fin = min( 1440, $r_ini + 60 );
        }
        $ore = (int) ( ( $r_fin - $r_ini ) / 60 );

        // ── Filtri: solo quelli che hanno qualcosa da scegliere ──
        $usati = [];
        $liv_u = [];
        $istr  = [];
        foreach ( $lezioni as $l ) {
            $usati[ $l['c'] ] = true;
            if ( '' !== $l['liv'] ) {
                $liv_u[ $l['liv'] ] = true;
            }
            $ki = self::chiave( $l['ist'] );
            if ( '' !== $ki && ! isset( $istr[ $ki ] ) ) {
                $istr[ $ki ] = $l['ist'];
            }
        }
        natcasesort( $istr );
        $filtri    = ! empty( $s['mostra_filtri'] );
        $con_chips = $filtri && count( $usati ) > 1;
        $con_liv   = $filtri && ! empty( $s['filtro_livello'] ) && count( $liv_u ) > 1;
        $con_ist   = $filtri && ! empty( $s['filtro_istruttore'] ) && count( $istr ) > 1;
        $centro    = 'center' === (string) $s['allineamento_filtri'];
        $tutti     = trim( (string) $s['etichetta_tutti'] );
        $tutti     = '' !== $tutti ? $tutti : __( 'Tutti', 'olobuilder' );
        $vuoto     = trim( (string) $s['testo_vuoto'] );
        $vuoto     = '' !== $vuoto ? $vuoto : __( 'Nessuna lezione con questi filtri.', 'olobuilder' );
        $link      = $dettagli ? self::modello( $s['link_prenota'] ) : '';
        $t_pren    = trim( (string) $s['testo_prenota'] );
        $t_pren    = '' !== $t_pren ? $t_pren : __( 'Prenota', 'olobuilder' );
        /* translators: %d: numero delle lezioni (una) */
        $t_uno = __( '%d lezione', 'olobuilder' );
        /* translators: %d: numero delle lezioni */
        $t_tanti = __( '%d lezioni', 'olobuilder' );

        // ── Colori: tutti dai token del tema, salvo quelli scelti ──
        $oggi_s  = $this->safe_color_css( $s['colore_oggi'] );
        $oggi    = '' !== $oggi_s ? $oggi_s : 'var(--olo-color-primary)';
        $su_oggi = '' !== $oggi_s
            ? ( self::testo_per( $oggi_s, 'var(--olo-color-light, #ffffff)' ) ?: 'var(--olo-color-light, #ffffff)' )
            : 'var(--olo-color-primary-contrast, #ffffff)';
        $ades    = $this->safe_color_css( $s['colore_adesso'] ) ?: 'var(--olo-color-accent, #f4a23b)';
        $su_ades = self::testo_per( $ades, 'var(--olo-color-light, #ffffff)' ) ?: 'var(--olo-color-light, #ffffff)';
        $testo_c = $this->safe_color_css( $s['colore_testo'] );
        $tenue   = $this->safe_color_css( $s['colore_tenue'] ) ?: 'color-mix(in srgb, currentColor 70%, transparent)';
        $sf_s    = $this->safe_color_css( $s['sfondo_griglia'] );
        $sfondo  = '' !== $sf_s ? $sf_s : 'color-mix(in srgb, currentColor 4%, transparent)';
        $linee   = $this->safe_color_css( $s['colore_linee'] ) ?: 'color-mix(in srgb, currentColor 12%, transparent)';
        $bl_s    = $this->safe_color_css( $s['sfondo_lezione'] );
        $testo_t = '' !== $testo_c ? $testo_c : 'var(--olo-color-text, #374151)';
        // Un fondo scelto e il testo no: il testo del tema se lì si legge, altrimenti il chiaro o lo scuro della
        // Palette. Mai il colore della sezione: su una sezione scura sarebbe chiaro su chiaro.
        $testo_g = '' !== $sf_s ? ( self::testo_per( $sf_s, $testo_t ) ?: ( '' !== $testo_c ? '' : $testo_t ) ) : '';
        $testo_l = '' !== $bl_s ? ( self::testo_per( $bl_s, $testo_t ) ?: ( '' !== $testo_c ? '' : $testo_t ) ) : '';
        $ombra   = Olobuild_Tile_Utils::shadow_value( $s, 'ombra' );
        $om      = 'none' !== $ombra ? $ombra : '0 0 0 0 transparent';

        // ── Forme e misure ──
        $raggio = Olobuild_Tile_Utils::border_radius( $s['raggio_lezione'] ) ?: '0';
        $r_max  = Olobuild_Tile_Utils::radius_int( $s['raggio_lezione'] );
        $rf     = $r_max > 0 ? $r_max + 4 : 0;            // i riquadri dei giorni: concentrici alle lezioni
        $rd     = $r_max > 0 ? min( 28, $r_max + 8 ) : 0; // il riquadro dei dettagli
        $rc     = 'quadrato' === (string) $s['forma_filtri'] ? '.375rem' : '5em';
        $h      = max( 56, min( 120, (int) $s['altezza_ora'] ) );
        $gap    = max( 0, min( 48, (int) $s['gap'] ) );
        $n_g    = count( $giorni );
        $schede = $n_g > 1; // con un giorno solo, sul telefono resta il suo nome al posto delle schede

        $classi = 'ooc ooc--' . ( $griglia ? 'griglia' : 'elenco' ) . ( $centro ? ' ooc--centro' : '' );
        $sr     = 'position:absolute;width:1px;height:1px;margin:-1px;padding:0;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0'; // solo per i lettori di schermo

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS da colori passati da safe_color_css() e dagli helper di Olobuild_Tile_Utils, numeri forzati, valori da elenchi chiusi e selettori generati qui
        echo '<style>';
        echo "{$sel}{--ooc-h:{$h}px;--ooc-ore:{$ore};--ooc-n:{$n_g};--ooc-gap:{$gap}px;--ooc-th:3rem;--ooc-r:{$raggio};--ooc-rmax:{$r_max}px;--ooc-rf:{$rf}px;--ooc-rd:{$rd}px;--ooc-rc:{$rc};--ooc-acc:var(--olo-color-primary);--ooc-su-acc:var(--olo-color-primary-contrast, #ffffff);--ooc-oggi:{$oggi};--ooc-su-oggi:{$su_oggi};--ooc-adesso:{$ades};--ooc-su-adesso:{$su_ades};--ooc-tenue:{$tenue};--ooc-sfondo:{$sfondo};--ooc-linee:{$linee};--ooc-ombra:{$om}" . ( '' !== $testo_c ? ";color:{$testo_c}" : '' ) . '}';
        echo "{$sel} [hidden]{display:none!important}";
        echo "{$sel} .ooc-sr{position:absolute!important;width:1px;height:1px;margin:-1px;padding:0;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}";

        // I filtri: i pulsanti dei corsi (col pallino del colore), i menu, il conteggio.
        echo "{$sel} .ooc-testa{display:flex;flex-wrap:wrap;align-items:center;gap:.75rem 1rem;margin:0 0 1.5rem}";
        echo "{$sel} .ooc-chips{display:flex;flex-wrap:wrap;gap:.5rem;min-width:0}";
        echo "{$sel} .ooc-chip{position:relative;display:inline-flex;align-items:center;gap:.5em;min-height:2.5rem;margin:0;padding:.5em 1.05em;border:1px solid color-mix(in srgb, currentColor 22%, transparent);border-radius:var(--ooc-rc);background:transparent;color:inherit;font:inherit;font-size:.875rem;font-weight:600;line-height:1.2;letter-spacing:normal;text-transform:none;text-shadow:none;white-space:nowrap;cursor:pointer;-webkit-appearance:none;appearance:none;box-shadow:none;transition:background-color .2s ease,border-color .2s ease,color .2s ease}";
        echo "{$sel} .ooc-chip::before{content:'';position:absolute;inset:-.25rem}";
        echo "{$sel} .ooc-chip:hover{background:transparent;border-color:var(--c, var(--ooc-acc));color:inherit}";
        echo "{$sel} .ooc-chip[aria-pressed=\"true\"]{background:var(--c, var(--ooc-acc));border-color:var(--c, var(--ooc-acc));color:var(--cs, var(--ooc-su-acc))}";
        echo "{$sel} .ooc-chip:focus{outline:none}";
        echo "{$sel} .ooc-chip:focus-visible{outline:2px solid var(--ooc-acc);outline-offset:2px}";
        echo "{$sel} .ooc-punto{flex:none;width:.5rem;height:.5rem;border-radius:50%;background:var(--c, currentColor)}";
        echo "{$sel} .ooc-chip[aria-pressed=\"true\"] .ooc-punto{background:currentColor}";
        echo "{$sel} .ooc-scelte{display:flex;flex-wrap:wrap;gap:.5rem}";
        echo "{$sel} .ooc-campo{position:relative;display:flex;min-width:0}";
        echo "{$sel} .ooc-campo::after{content:'';position:absolute;right:1.05em;top:50%;width:.4em;height:.4em;border-right:2px solid currentColor;border-bottom:2px solid currentColor;transform:translateY(-70%) rotate(45deg);opacity:.65;pointer-events:none}";
        echo "{$sel} .ooc-select{box-sizing:border-box;min-height:2.5rem;max-width:16rem;margin:0;padding:.5em 2.6em .5em 1.05em;border:1px solid color-mix(in srgb, currentColor 22%, transparent);border-radius:var(--ooc-rc);background:transparent;color:inherit;font:inherit;font-size:.875rem;font-weight:600;line-height:1.2;text-overflow:ellipsis;cursor:pointer;-webkit-appearance:none;appearance:none;box-shadow:none}";
        echo "{$sel} .ooc-select:hover{border-color:var(--ooc-acc)}";
        echo "{$sel} .ooc-select:focus{outline:none}";
        echo "{$sel} .ooc-select:focus-visible{outline:2px solid var(--ooc-acc);outline-offset:2px}";
        echo "{$sel} .ooc-select option{color:var(--olo-color-text, #1f2937);background:var(--olo-color-background, #ffffff)}";
        echo "{$sel} .ooc-conteggio{margin:0 0 0 auto;font-size:.875rem;line-height:1.3;color:var(--ooc-tenue);white-space:nowrap}";
        if ( $centro ) {
            echo "{$sel} .ooc-testa,{$sel} .ooc-chips,{$sel} .ooc-scelte{justify-content:center}";
            echo "{$sel} .ooc-conteggio{flex-basis:100%;margin:0;text-align:center}";
        }
        echo "{$sel} .ooc-schede{display:none}";

        // Le colonne dei giorni: un riquadro per giorno, il nome in testa.
        echo "{$sel} .ooc-orario{min-width:0}";
        echo "{$sel} .ooc-giorno{position:relative;min-width:0;display:flex;flex-direction:column;border-radius:var(--ooc-rf);background:var(--ooc-sfondo)" . ( '' !== $testo_g ? ";color:{$testo_g}" : '' ) . '}';
        echo "{$sel} .ooc-giorno-nome{box-sizing:border-box;display:flex;flex-direction:column;justify-content:center;gap:.2rem;height:var(--ooc-th);margin:0;padding:0 .75rem;border-bottom:1px solid var(--ooc-linee);border-radius:var(--ooc-rf) var(--ooc-rf) 0 0;font:inherit;font-size:13px;font-weight:600;line-height:1.15;letter-spacing:normal;text-transform:none;color:inherit;white-space:nowrap;overflow:hidden}";
        echo "{$sel} .ooc-giorno-testo{display:block;overflow:hidden;text-overflow:ellipsis}";
        echo "{$sel} .ooc-oggi-et{display:block;font-size:.625rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;line-height:1}";
        echo "{$sel} .ooc-lezioni{margin:0;padding:0;list-style:none}";
        echo "{$sel} .ooc-voce{margin:0;padding:0;list-style:none;transition:opacity .18s ease}";
        echo "{$sel} .ooc-voce::before{content:none}";
        echo "{$sel} .ooc-voce.is-fuori{opacity:0}";

        // La lezione: la fascia del corso a sinistra, la sua tinta leggera di fondo, ora, corso, istruttore e sala.
        echo "{$sel} .ooc-lez{box-sizing:border-box;position:relative;display:flex;flex-direction:column;align-items:flex-start;gap:.125rem;width:100%;min-width:0;min-height:0;margin:0;padding:.625rem .75rem .625rem 1rem;border:0;border-radius:var(--ooc-r);background:" . ( '' !== $bl_s ? $bl_s : 'color-mix(in srgb, var(--c) 14%, transparent)' ) . ';color:' . ( '' !== $testo_l ? $testo_l : 'inherit' ) . ";font:inherit;line-height:1.25;letter-spacing:normal;text-align:left;text-transform:none;text-shadow:none;overflow:hidden;-webkit-appearance:none;appearance:none;box-shadow:inset .1875rem 0 0 var(--c),var(--ooc-anello, 0 0 0 0 transparent),var(--ooc-ombra);transition:transform .2s ease,box-shadow .2s ease}";
        echo "{$sel} button.ooc-lez{cursor:pointer}";
        echo "{$sel} button.ooc-lez:hover{transform:translateY(-1px);box-shadow:inset .1875rem 0 0 var(--c),var(--ooc-anello, 0 0 0 0 transparent),0 .5rem 1.25rem -.625rem color-mix(in srgb, var(--olo-color-dark, #14161c) 45%, transparent)}";
        echo "{$sel} .ooc-lez:focus{outline:none}";
        echo "{$sel} .ooc-lez:focus-visible{outline:2px solid var(--ooc-acc);outline-offset:2px}";
        echo "{$sel} .ooc-voce.is-adesso{--ooc-anello:0 0 0 2px var(--ooc-adesso)}";
        echo "{$sel} .ooc-lez-riga{display:flex;align-items:center;gap:.375rem;width:100%;min-width:0}";
        echo "{$sel} .ooc-lez-ora{min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--ooc-tenue);font-size:12px;font-weight:600;font-variant-numeric:tabular-nums}";
        echo "{$sel} .ooc-lez-corso{display:block;max-width:100%;font-size:14px;font-weight:600;overflow-wrap:anywhere;-webkit-hyphens:auto;hyphens:auto}";
        echo "{$sel} .ooc-lez-meta{display:block;max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--ooc-tenue);font-size:12px}";
        echo "{$sel} .ooc-lez-stato{flex:none;display:inline-flex;align-items:center;gap:.3em;margin-left:auto;padding:.125rem .45rem;border-radius:5em;background:var(--ooc-adesso);color:var(--ooc-su-adesso);font-size:.625rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;line-height:1.3;white-space:nowrap}";
        echo "{$sel} .ooc-lez-stato::before{content:'';width:.375rem;height:.375rem;border-radius:50%;background:currentColor;animation:ooc-pulsa 1.6s ease-in-out infinite}";
        echo '@keyframes ooc-pulsa{50%{opacity:.3}}';
        echo "{$sel} .ooc-vuoto{margin:1rem 0 0;padding:1.5rem 1.25rem;border-radius:var(--ooc-rf);background:var(--ooc-sfondo);text-align:center;font-size:.9375rem;color:var(--ooc-tenue)}";

        // Desktop e tablet (da 640 px): le colonne affiancate; in griglia l'asse delle ore e le lezioni a misura.
        $largo = '';
        if ( $griglia ) {
            $largo .= "{$sel} .ooc-orario{display:grid;grid-template-columns:3.25rem repeat(var(--ooc-n),minmax(6.5rem,1fr));column-gap:var(--ooc-gap);overflow-x:auto;overflow-y:hidden;padding:.25rem 0 .75rem}";
            $largo .= "{$sel} .ooc-asse-testa{height:var(--ooc-th)}";
            $largo .= "{$sel} .ooc-asse-corpo{position:relative;height:calc(var(--ooc-ore) * var(--ooc-h))}";
            $largo .= "{$sel} .ooc-asse-ora{position:absolute;right:.5rem;top:calc(var(--t) * var(--ooc-h));transform:translateY(-50%);color:var(--ooc-tenue);font-size:12px;font-weight:600;line-height:1;font-variant-numeric:tabular-nums;white-space:nowrap}";
            $largo .= "{$sel} .ooc-giorno-nome{border-bottom:0}";
            $largo .= "{$sel} .ooc-lezioni{position:relative;height:calc(var(--ooc-ore) * var(--ooc-h));background-image:repeating-linear-gradient(to bottom,var(--ooc-linee) 0,var(--ooc-linee) 1px,transparent 1px,transparent var(--ooc-h)),repeating-linear-gradient(to bottom,transparent 0,transparent calc(var(--ooc-h) / 2),color-mix(in srgb, var(--ooc-linee) 45%, transparent) calc(var(--ooc-h) / 2),color-mix(in srgb, var(--ooc-linee) 45%, transparent) calc(var(--ooc-h) / 2 + 1px),transparent calc(var(--ooc-h) / 2 + 1px),transparent var(--ooc-h))}";
            // Ogni lezione è un contenitore con nome (ooc-voce): sotto i 6rem di larghezza (due lezioni affiancate su un
            // tablet) restano l'ora d'inizio e il corso; il resto passa ai lettori di schermo.
            $largo .= "{$sel} .ooc-voce{position:absolute;box-sizing:border-box;top:calc(var(--t) * var(--ooc-h));left:calc(var(--col) * 100% / var(--cols));width:calc(100% / var(--cols));height:calc(var(--d) * var(--ooc-h));padding:.125rem .25rem;container:ooc-voce / inline-size;transition:opacity .18s ease,left .2s ease,width .2s ease}";
            $largo .= "{$sel} .ooc-lez{height:100%;padding:.3125rem .5rem .3125rem .75rem}";
            // Il corso su una riga; due righe nelle lezioni lunghe. Nelle brevi istruttore e sala restano ai lettori di schermo.
            $largo .= "{$sel} .ooc-lez-corso{display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:1;overflow:hidden}";
            $largo .= "{$sel} .is-lunga .ooc-lez-corso{-webkit-line-clamp:2}";
            $largo .= "{$sel} .is-breve .ooc-lez-meta,{$sel} .is-mini .ooc-lez-meta,{$sel} .is-mini .ooc-lez-fine{{$sr}}";
            $largo .= "{$sel} .is-mini .ooc-lez{flex-direction:row;align-items:center;gap:.375rem;padding-top:0;padding-bottom:0}";
            $largo .= "{$sel} .is-mini .ooc-lez-riga{width:auto;flex:none}";
            $largo .= "{$sel} .is-mini .ooc-lez-corso{flex:1 1 auto;min-width:0}";
            $largo .= "{$sel} .ooc-adesso{position:absolute;left:0;right:0;top:calc(var(--ooc-th) + var(--t) * var(--ooc-h));z-index:3;height:2px;margin-top:-1px;background:var(--ooc-adesso);pointer-events:none}";
            $largo .= "{$sel} .ooc-adesso::before{content:'';position:absolute;left:-.3125rem;top:50%;width:.625rem;height:.625rem;border-radius:50%;background:inherit;transform:translateY(-50%)}";
        } else {
            $largo .= "{$sel} .ooc-orario{display:grid;grid-template-columns:repeat(var(--ooc-n),minmax(7.5rem,1fr));gap:var(--ooc-gap);overflow-x:auto;padding-bottom:.25rem}";
            $largo .= "{$sel} .ooc-lezioni{display:flex;flex-direction:column;gap:.5rem;padding:.625rem}";
        }
        if ( 0 === $gap ) {
            // Colonne attaccate: una linea fra un giorno e l'altro.
            $largo .= "{$sel} .ooc-giorno+.ooc-giorno{box-shadow:inset 1px 0 0 var(--ooc-linee)}";
        }
        echo '@media (min-width:640px){' . $largo . '}';
        if ( $griglia ) {
            echo '@container ooc-voce (max-width:6rem){'
                . "{$sel} .ooc-lez{padding-left:.5625rem;padding-right:.3125rem}"
                . "{$sel} .ooc-lez-fine,{$sel} .ooc-lez-meta{{$sr}}"
                . "{$sel} .ooc-voce:not(.is-breve):not(.is-mini) .ooc-lez-corso{-webkit-line-clamp:2}"
                . "{$sel} .ooc-lez-stato{font-size:0;padding:.25rem}"
                . '}';
        }
        // Oggi: il riquadro velato del colore di oggi, il nome su una fascia piena (vince sul colore dei giorni scelto
        // nella tipografia: il selettore è più specifico).
        echo "{$sel} .ooc-giorno.is-oggi{background:color-mix(in srgb, var(--ooc-oggi) 8%, var(--ooc-sfondo))}";
        echo "{$sel} .ooc-giorno.is-oggi .ooc-giorno-nome{background:var(--ooc-oggi);color:var(--ooc-su-oggi)}";

        // Telefono (sotto i 640 px): un giorno alla volta con le schede; filtri e schede scorrono in orizzontale.
        echo '@media (max-width:639px){'
            . "{$sel} .ooc-testa{display:grid;grid-template-columns:minmax(0,1fr);gap:.625rem;margin-bottom:1rem}"
            . "{$sel} .ooc-chips{flex-wrap:nowrap;overflow-x:auto;padding:.25rem;scrollbar-width:none}"
            . "{$sel} .ooc-chips::-webkit-scrollbar{display:none}"
            . "{$sel} .ooc-chip{flex:none;min-height:2.75rem}"
            . "{$sel} .ooc-scelte{display:grid;grid-template-columns:repeat(auto-fit,minmax(9rem,1fr))}"
            . "{$sel} .ooc-select{width:100%;max-width:none;min-height:2.75rem}"
            . "{$sel} .ooc-conteggio{margin:0}"
            . "{$sel}.ooc--centro .ooc-chips{justify-content:flex-start}"
            . "{$sel} .ooc-schede{position:relative;display:flex;gap:.25rem;margin:0 0 1rem;padding:.25rem;overflow-x:auto;scrollbar-width:none}"
            . "{$sel} .ooc-schede::-webkit-scrollbar{display:none}"
            . "{$sel} .ooc-scheda{flex:1 0 2.75rem;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.3rem;min-height:3.5rem;margin:0;padding:.375rem .25rem;border:1px solid color-mix(in srgb, currentColor 18%, transparent);border-radius:var(--ooc-rc);background:transparent;color:inherit;font:inherit;line-height:1;letter-spacing:normal;text-transform:none;text-shadow:none;cursor:pointer;-webkit-appearance:none;appearance:none;box-shadow:none;transition:background-color .2s ease,border-color .2s ease,color .2s ease,opacity .2s ease}"
            . "{$sel} .ooc-scheda:hover{background:transparent;border-color:var(--ooc-acc);color:inherit}"
            . "{$sel} .ooc-scheda[aria-selected=\"true\"]{background:var(--ooc-acc);border-color:var(--ooc-acc);color:var(--ooc-su-acc)}"
            . "{$sel} .ooc-scheda:focus{outline:none}"
            . "{$sel} .ooc-scheda:focus-visible{outline:2px solid var(--ooc-acc);outline-offset:2px}"
            . "{$sel} .ooc-scheda-breve{font-size:.8125rem;font-weight:600}"
            . "{$sel} .ooc-scheda-punto{width:.375rem;height:.375rem;border-radius:50%;background:transparent}"
            . "{$sel} .ooc-scheda.is-oggi .ooc-scheda-punto{background:var(--ooc-oggi)}"
            . "{$sel} .ooc-scheda.is-oggi[aria-selected=\"true\"] .ooc-scheda-punto{background:currentColor}"
            . "{$sel} .ooc-scheda.is-vuota:not([aria-selected=\"true\"]){opacity:.45}"
            . "{$sel} .ooc-orario{display:block}"
            . "{$sel} .ooc-asse,{$sel} .ooc-adesso{display:none}"
            . "{$sel} .ooc-giorno,{$sel} .ooc-giorno.is-oggi{background:transparent;border-radius:0}"
            . "{$sel} .ooc-giorno:not(.is-scelto){display:none}"
            . ( $schede ? "{$sel} .ooc-giorno-nome{{$sr}}" : "{$sel} .ooc-giorno-nome,{$sel} .ooc-giorno.is-oggi .ooc-giorno-nome{height:auto;padding:0 0 .75rem;border:0;border-radius:0;background:transparent}{$sel} .ooc-giorno.is-oggi .ooc-giorno-nome{color:var(--ooc-oggi)}" )
            . "{$sel} .ooc-lezioni{display:flex;flex-direction:column;gap:.625rem}"
            . "{$sel} .ooc-lez{padding:.875rem 1rem .875rem 1.25rem;gap:.25rem}"
            . '}';

        // I dettagli della lezione: un <dialog> modale sul fondo del tema (al centro; sul telefono sale dal basso).
        if ( $dettagli ) {
            echo "{$sel} .ooc-dialogo{box-sizing:border-box;width:min(30rem, calc(100% - 2rem));max-width:none;max-height:calc(100% - 2rem);margin:auto;padding:0;border:0;border-radius:var(--ooc-rd);background:var(--olo-color-background, #ffffff);color:var(--olo-color-text, #1f2937);box-shadow:0 1.5rem 4rem -1rem color-mix(in srgb, var(--olo-color-dark, #14161c) 55%, transparent);overflow:auto;overscroll-behavior:contain;opacity:0;transform:translateY(.75rem) scale(.98);transition:opacity .18s ease,transform .18s ease}";
            echo "{$sel} .ooc-dialogo.is-aperto{opacity:1;transform:none}";
            echo "{$sel} .ooc-dialogo::backdrop{background:color-mix(in srgb, var(--olo-color-dark, #14161c) 55%, transparent);-webkit-backdrop-filter:blur(3px);backdrop-filter:blur(3px)}";
            echo "{$sel} .ooc-dialogo[open]{display:block}";
            echo "{$sel} .ooc-d-corpo{position:relative;padding:1.75rem 1.75rem 1.5rem;border-top:.3125rem solid var(--c, var(--ooc-acc))}";
            echo "{$sel} .ooc-d-chiudi{position:absolute;top:.625rem;right:.625rem;display:inline-flex;align-items:center;justify-content:center;width:2.75rem;height:2.75rem;margin:0;padding:0;border:0;border-radius:50%;background:transparent;color:inherit;opacity:.7;cursor:pointer;-webkit-appearance:none;appearance:none;box-shadow:none;transition:opacity .2s ease,background-color .2s ease}";
            echo "{$sel} .ooc-d-chiudi:hover{opacity:1;background:color-mix(in srgb, currentColor 8%, transparent);color:inherit}";
            echo "{$sel} .ooc-d-chiudi:focus{outline:none}";
            echo "{$sel} .ooc-d-chiudi:focus-visible{opacity:1;outline:2px solid var(--ooc-acc);outline-offset:2px}";
            echo "{$sel} .ooc-d-chiudi svg,{$sel} .ooc-d-chiudi .olo-lucide-icon{width:1.25rem;height:1.25rem}";
            echo "{$sel} .ooc-d-quando{display:flex;align-items:center;gap:.5rem;margin:0 3rem .5rem 0;font-size:.8125rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;line-height:1.3;color:color-mix(in srgb, currentColor 68%, transparent);font-variant-numeric:tabular-nums}";
            echo "{$sel} .ooc-d-quando::before{content:'';flex:none;width:.5rem;height:.5rem;border-radius:50%;background:var(--c, var(--ooc-acc))}";
            echo "{$sel} .ooc-d-titolo{margin:0 2.5rem 0 0;font-size:1.625rem;line-height:1.15;letter-spacing:normal;text-transform:none;color:inherit;text-wrap:balance}";
            echo "{$sel} .ooc-d-stato{display:inline-flex;align-items:center;gap:.35em;margin:.75rem 0 0;padding:.25rem .625rem;border-radius:5em;background:var(--ooc-adesso);color:var(--ooc-su-adesso);font-size:.75rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;line-height:1.3}";
            echo "{$sel} .ooc-d-stato::before{content:'';width:.4rem;height:.4rem;border-radius:50%;background:currentColor;animation:ooc-pulsa 1.6s ease-in-out infinite}";
            echo "{$sel} .ooc-d-descr{margin:.875rem 0 0;font-size:.9375rem;line-height:1.6;opacity:.85;text-wrap:pretty}";
            echo "{$sel} .ooc-d-dati{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem 1.5rem;margin:1.25rem 0 0;padding:1.25rem 0 0;border-top:1px solid color-mix(in srgb, currentColor 12%, transparent)}";
            echo "{$sel} .ooc-d-dati>div{min-width:0}";
            echo "{$sel} .ooc-d-dati dt{margin:0 0 .25rem;font-size:.75rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;line-height:1.3;opacity:.62}";
            echo "{$sel} .ooc-d-dati dd{margin:0;font-size:.9375rem;font-weight:600;line-height:1.35;overflow-wrap:anywhere}";
            echo "{$sel} .ooc-d-nota{margin:1.25rem 0 0;padding:.75rem 1rem;border-radius:var(--ooc-rmax);background:color-mix(in srgb, var(--c, var(--ooc-acc)) 10%, transparent);font-size:.9375rem;line-height:1.5}";
            if ( '' !== $link ) {
                echo "{$sel} .ooc-d-prenota{box-sizing:border-box;display:flex;align-items:center;justify-content:center;width:100%;min-height:2.875rem;margin:1.5rem 0 0;padding:var(--olo-btn-padding, .75em 1.5em);border:0;border-radius:var(--olo-btn-border-radius, .5rem);background:var(--olo-color-primary);color:var(--olo-color-primary-contrast, #ffffff);font-size:var(--olo-btn-font-size, 1rem);font-weight:var(--olo-btn-font-weight, 600);line-height:1.2;text-transform:var(--olo-btn-text-transform, none);letter-spacing:var(--olo-btn-letter-spacing, normal);text-align:center;text-decoration:none;transition:filter .2s ease,transform .15s ease}";
                echo "{$sel} .ooc-d-prenota:hover{filter:brightness(var(--olo-btn-hover-brightness, 108%));color:var(--olo-color-primary-contrast, #ffffff);text-decoration:none}";
                echo "{$sel} .ooc-d-prenota:active{transform:translateY(1px)}";
                echo "{$sel} .ooc-d-prenota:focus{outline:none}";
                echo "{$sel} .ooc-d-prenota:focus-visible{outline:2px solid var(--ooc-acc);outline-offset:3px}";
            }
            echo '@media (max-width:639px){'
                . "{$sel} .ooc-dialogo{width:100%;max-height:88vh;margin:auto 0 0;border-radius:var(--ooc-rd) var(--ooc-rd) 0 0;transform:translateY(1.5rem)}"
                . "{$sel} .ooc-d-corpo{padding:1.5rem 1.25rem 1.25rem}"
                . '}';
        }
        // «Riduci movimento»: le transizioni e il pallino che pulsa si fermano; filtri, dettagli e ora restano.
        echo '@media (prefers-reduced-motion:reduce){' . "{$sel} .ooc-voce,{$sel} .ooc-lez,{$sel} .ooc-chip,{$sel} .ooc-scheda,{$sel} .ooc-dialogo,{$sel} .ooc-d-prenota{transition:none}{$sel} .ooc-lez-stato::before,{$sel} .ooc-d-stato::before{animation:none}" . '}';

        // La tipografia scelta: giorni, orari, corsi, istruttore e sala (il riquadro dei dettagli prende solo il carattere).
        echo $this->css_testo( $s, 'giorno', "{$sel} .ooc-giorno-nome" );
        echo $this->css_testo( $s, 'giorno', "{$sel} .ooc-scheda-breve", false, false );
        echo $this->css_testo( $s, 'ora', "{$sel} .ooc-lez-ora,{$sel} .ooc-asse-ora" );
        echo $this->css_testo( $s, 'corso', "{$sel} .ooc-lez-corso" );
        echo $this->css_testo( $s, 'dettaglio', "{$sel} .ooc-lez-meta" );
        if ( $dettagli ) {
            $fam_c = $this->resolve_font_family( $s['corso_font_family'] );
            $fam_d = $this->resolve_font_family( $s['dettaglio_font_family'] );
            if ( '' !== $fam_c ) {
                echo "{$sel} .ooc-d-titolo{font-family:{$fam_c}}";
            }
            if ( '' !== $fam_d ) {
                echo "{$sel} .ooc-d-descr,{$sel} .ooc-d-dati,{$sel} .ooc-d-nota{font-family:{$fam_d}}";
            }
        }
        echo '</style>';

        // ── I dati per il browser: filtri, ora attuale, dettagli ──
        $corsi_js = [];
        foreach ( $corsi as $cod => $c ) {
            $corsi_js[ $cod ] = [ 'nome' => $c['nome'], 'descr' => $c['descr'] ];
        }
        $lez_js = [];
        foreach ( $lezioni as $l ) {
            $lez_js[] = [ 'g' => $l['g'], 'i' => $l['i'], 'f' => $l['f'], 'c' => $l['c'], 'ist' => $l['ist'], 'sala' => $l['sala'], 'liv' => $l['liv'], 'posti' => $l['posti'], 'nota' => $l['nota'] ];
        }
        $cfg = [
            'griglia'  => $griglia,
            'oggi'     => $oggi_on,
            'adesso'   => $ades_on,
            'ini'      => $r_ini,
            'fin'      => $r_fin,
            'giorni'   => $nomi,
            'corsi'    => $corsi_js,
            'lezioni'  => $lez_js,
            'livelli'  => self::livelli(),
            'testi'    => [
                'uno'     => $t_uno,
                'tanti'   => $t_tanti,
                'inCorso' => __( 'In corso', 'olobuilder' ),
                /* translators: %d: minuti */
                'min'     => __( '%d min', 'olobuilder' ),
                /* translators: %d: ore */
                'ore'     => __( '%d h', 'olobuilder' ),
                /* translators: 1: ore, 2: minuti */
                'oreMin'  => __( '%1$d h %2$d min', 'olobuilder' ),
            ],
            'link'     => $link,
            'builder'  => $builder,
        ];
        $n_tot = count( $lezioni );

        echo '<div id="' . esc_attr( $uid ) . '" class="' . esc_attr( $classi ) . '" data-ooc="' . esc_attr( wp_json_encode( $cfg ) ) . '">';

        // ── I filtri ──
        if ( $con_chips || $con_liv || $con_ist ) {
            echo '<div class="ooc-testa">';
            if ( $con_chips ) {
                echo '<div class="ooc-chips" role="group" aria-label="' . esc_attr__( 'Corsi', 'olobuilder' ) . '">';
                echo '<button type="button" class="ooc-chip" data-c="" aria-pressed="true" data-olo-interactive>' . esc_html( $tutti ) . '</button>';
                foreach ( $corsi as $cod => $c ) {
                    if ( empty( $usati[ $cod ] ) ) {
                        continue;
                    }
                    echo '<button type="button" class="ooc-chip" data-c="' . esc_attr( $cod ) . '" aria-pressed="false" style="' . esc_attr( '--c:' . $c['colore'] . ';--cs:' . $c['su'] ) . '" data-olo-interactive>'
                        . '<span class="ooc-punto" aria-hidden="true"></span>' . esc_html( $c['nome'] ) . '</button>';
                }
                echo '</div>';
            }
            if ( $con_liv || $con_ist ) {
                echo '<div class="ooc-scelte">';
                if ( $con_liv ) {
                    echo '<div class="ooc-campo"><select class="ooc-select" id="' . esc_attr( $uid ) . '-liv" data-filtro="liv" aria-label="' . esc_attr__( 'Livello', 'olobuilder' ) . '" data-olo-interactive>';
                    echo '<option value="">' . esc_html__( 'Tutti i livelli', 'olobuilder' ) . '</option>';
                    foreach ( self::livelli() as $lv => $et ) {
                        if ( ! empty( $liv_u[ $lv ] ) ) {
                            echo '<option value="' . esc_attr( $lv ) . '">' . esc_html( 'tutti' === $lv ? __( 'Per tutti i livelli', 'olobuilder' ) : $et ) . '</option>';
                        }
                    }
                    echo '</select></div>';
                }
                if ( $con_ist ) {
                    echo '<div class="ooc-campo"><select class="ooc-select" id="' . esc_attr( $uid ) . '-ist" data-filtro="ist" aria-label="' . esc_attr__( 'Istruttore', 'olobuilder' ) . '" data-olo-interactive>';
                    echo '<option value="">' . esc_html__( 'Tutti gli istruttori', 'olobuilder' ) . '</option>';
                    foreach ( $istr as $ki => $nome ) {
                        echo '<option value="' . esc_attr( $ki ) . '">' . esc_html( $nome ) . '</option>';
                    }
                    echo '</select></div>';
                }
                echo '</div>';
            }
            echo '<p class="ooc-conteggio" aria-live="polite">' . esc_html( sprintf( 1 === $n_tot ? $t_uno : $t_tanti, $n_tot ) ) . '</p>';
            echo '</div>';
        }

        // ── Le schede dei giorni (solo sul telefono): aperta la prima, poi lo script apre oggi ──
        if ( $schede ) {
            echo '<div class="ooc-schede" role="tablist" aria-label="' . esc_attr__( 'Giorni della settimana', 'olobuilder' ) . '">';
            foreach ( $giorni as $j => $g ) {
                $on = 0 === $j;
                echo '<button type="button" role="tab" class="ooc-scheda" id="' . esc_attr( $uid . '-t' . $g ) . '" data-g="' . (int) $g . '" aria-controls="' . esc_attr( $uid . '-g' . $g ) . '" aria-selected="' . ( $on ? 'true' : 'false' ) . '" tabindex="' . ( $on ? '0' : '-1' ) . '" data-olo-interactive>'
                    . '<span class="ooc-scheda-breve" aria-hidden="true">' . esc_html( $brevi[ $g ] ?? self::breve( $nomi[ $g ] ) ) . '</span>'
                    . '<span class="ooc-sr">' . esc_html( $nomi[ $g ] ) . '</span>'
                    . '<span class="ooc-scheda-punto" aria-hidden="true"></span></button>';
            }
            echo '</div>';
        }

        // ── L'orario: l'asse delle ore (griglia) e una colonna per giorno ──
        echo '<div class="ooc-orario">';
        if ( $griglia ) {
            echo '<div class="ooc-asse" aria-hidden="true"><div class="ooc-asse-testa"></div><div class="ooc-asse-corpo">';
            for ( $t = 0; $t <= $ore; $t++ ) {
                echo '<span class="ooc-asse-ora" style="--t:' . (int) $t . '">' . esc_html( self::hhmm( $r_ini + 60 * $t ) ) . '</span>';
            }
            echo '</div></div>';
        }
        foreach ( $giorni as $j => $g ) {
            echo '<div class="ooc-giorno' . ( 0 === $j ? ' is-scelto' : '' ) . '" id="' . esc_attr( $uid . '-g' . $g ) . '" data-g="' . (int) $g . '">';
            echo '<h3 class="ooc-giorno-nome" id="' . esc_attr( $uid . '-h' . $g ) . '"><span class="ooc-oggi-et" hidden>' . esc_html__( 'Oggi', 'olobuilder' ) . '</span><span class="ooc-giorno-testo">' . esc_html( $nomi[ $g ] ) . '</span></h3>';
            if ( $griglia && $ades_on ) {
                echo '<div class="ooc-adesso" aria-hidden="true" hidden></div>';
            }
            echo '<ol class="ooc-lezioni" aria-labelledby="' . esc_attr( $uid . '-h' . $g ) . '">';
            foreach ( $per_giorno[ $g ] as $k ) {
                $l    = $lezioni[ $k ];
                $c    = $corsi[ $l['c'] ];
                $durh = ( $l['f'] - $l['i'] ) / 60;
                $px   = $durh * $h;
                $cls  = 'ooc-voce' . ( $griglia ? ( $px < 40 ? ' is-mini' : ( $px < 68 ? ' is-breve' : ( $px >= 92 ? ' is-lunga' : '' ) ) ) : '' );
                $vars = '--c:' . $c['colore'] . ';--t:' . self::num( ( $l['i'] - $r_ini ) / 60 ) . ';--d:' . self::num( $durh ) . ';--col:' . (int) $l['col'] . ';--cols:' . (int) $l['cols'];
                $meta = implode( ' · ', array_filter( [ $l['ist'], $l['sala'] ], 'strlen' ) );
                $ini  = self::hhmm( $l['i'] );
                $fin  = self::hhmm( $l['f'] );
                $dent = '<span class="ooc-sr">' . esc_html( $nomi[ $g ] ) . ', </span>'
                    . '<span class="ooc-lez-riga"><span class="ooc-lez-ora"><time datetime="' . esc_attr( $ini ) . '">' . esc_html( $ini ) . '</time><span class="ooc-lez-fine">–<time datetime="' . esc_attr( $fin ) . '">' . esc_html( $fin ) . '</time></span></span></span>'
                    . '<span class="ooc-lez-corso">' . esc_html( $c['nome'] ) . '</span>'
                    . ( '' !== $meta ? '<span class="ooc-lez-meta">' . esc_html( $meta ) . '</span>' : '' );
                echo '<li class="' . esc_attr( $cls ) . '" data-l="' . (int) $k . '" data-g="' . (int) $g . '" data-c="' . esc_attr( $l['c'] ) . '" data-liv="' . esc_attr( $l['liv'] ) . '" data-ist="' . esc_attr( self::chiave( $l['ist'] ) ) . '" style="' . esc_attr( $vars ) . '">';
                if ( $dettagli ) {
                    echo '<button type="button" class="ooc-lez" aria-haspopup="dialog" aria-controls="' . esc_attr( $uid ) . '-d" data-olo-interactive>' . $dent . '</button>';
                } else {
                    echo '<div class="ooc-lez">' . $dent . '</div>';
                }
                echo '</li>';
            }
            echo '</ol></div>';
        }
        echo '</div>';
        echo '<p class="ooc-vuoto" hidden>' . esc_html( $vuoto ) . '</p>';

        // ── Il riquadro dei dettagli: lo riempie lo script con la lezione scelta ──
        if ( $dettagli ) {
            echo '<dialog class="ooc-dialogo" id="' . esc_attr( $uid ) . '-d" aria-labelledby="' . esc_attr( $uid ) . '-dt" aria-modal="true" data-olo-interactive>';
            echo '<div class="ooc-d-corpo">';
            echo '<button type="button" class="ooc-d-chiudi" aria-label="' . esc_attr__( 'Chiudi', 'olobuilder' ) . '">' . $this->render_icon_html( 'x', 1, 'aria-hidden="true"' ) . '</button>';
            echo '<p class="ooc-d-quando"></p>';
            echo '<h2 class="ooc-d-titolo" id="' . esc_attr( $uid ) . '-dt"></h2>';
            echo '<p class="ooc-d-stato" hidden></p>';
            echo '<p class="ooc-d-descr" hidden></p>';
            echo '<dl class="ooc-d-dati">';
            // «Dove» e non «Sala»: nel catalogo «Sala» è quella del teatro (en Hall, de Saal), e con le sale chiamate
            // «Sala 1» si leggerebbe «Sala: Sala 1».
            foreach ( [ 'durata' => __( 'Durata', 'olobuilder' ), 'istruttore' => __( 'Istruttore', 'olobuilder' ), 'sala' => __( 'Dove', 'olobuilder' ), 'livello' => __( 'Livello', 'olobuilder' ), 'posti' => __( 'Posti', 'olobuilder' ) ] as $campo => $et ) {
                echo '<div data-campo="' . esc_attr( $campo ) . '"><dt>' . esc_html( $et ) . '</dt><dd></dd></div>';
            }
            echo '</dl>';
            echo '<p class="ooc-d-nota" hidden></p>';
            if ( '' !== $link ) {
                echo '<a class="ooc-d-prenota" href="#" hidden>' . esc_html( $t_pren ) . '</a>';
            }
            echo '</div></dialog>';
        }
        echo '</div>';

        // Lo script avvia ogni istanza della pagina una volta sola (anche le sezioni caricate dopo e l'anteprima).
        echo '<script>' . $this->script() . '[].forEach.call(document.querySelectorAll("[data-ooc]"),function(x){window.oloOrarioCorsi(x);});</script>';
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }

    /**
     * La funzione dell'orario: filtri e corsie, schede del telefono, oggi e ora attuale, dettagli. Va in OGNI istanza
     * (definita una volta sola, «if(!window…)»): una sezione caricata dopo può eseguire il suo script prima di quello
     * della prima istanza. Niente «&» nello script (wptexturize lo riscrive fra un «<» e un «>»): condizioni annidate e
     * la «&» da String.fromCharCode(38). Ogni istanza si avvia una volta (data-ooc-pronto); i timer si fermano quando
     * la tile esce dalla pagina (anteprima del builder che la rende di nuovo).
     */
    private function script() {
        return <<<'JS'
if(!window.oloOrarioCorsi){window.oloOrarioCorsi=function(el){
if(!el){return;}
if(el.getAttribute("data-ooc-pronto")==="1"){return;}
el.setAttribute("data-ooc-pronto","1");
var C={};try{C=JSON.parse(el.getAttribute("data-ooc")||"{}");}catch(e){}
var T=C.testi||{},L=C.lezioni||[],CO=C.corsi||{},E=String.fromCharCode(38);
var riduci=false;try{riduci=window.matchMedia("(prefers-reduced-motion: reduce)").matches;}catch(e){}
function tutti(s){return [].slice.call(el.querySelectorAll(s));}
function dato(n,k){return n.getAttribute("data-"+k)||"";}
function vivo(){return document.documentElement.contains(el);}
var voci=tutti(".ooc-voce"),colonne=tutti(".ooc-giorno"),schede=tutti(".ooc-scheda"),chips=tutti(".ooc-chip"),scelte=tutti(".ooc-select");
var orario=el.querySelector(".ooc-orario"),vuoto=el.querySelector(".ooc-vuoto"),conto=el.querySelector(".ooc-conteggio"),dlg=el.querySelector(".ooc-dialogo");
var F={c:"",liv:"",ist:""},mobile=false,scelto=0;
colonne.forEach(function(col){col._g=+dato(col,"g");col._voci=[];});
voci.forEach(function(v){v._l=L[+dato(v,"l")]||{};v._g=+dato(v,"g");v._vis=true;colonne.forEach(function(col){if(col._g===v._g){col._voci.push(v);}});});
function passa(v){
if(F.c){if(dato(v,"c")!==F.c){return false;}}
if(F.liv){var lv=dato(v,"liv");if(lv!==F.liv){if(F.liv==="tutti"){return false;}if(lv!=="tutti"){return false;}}}
if(F.ist){if(dato(v,"ist")!==F.ist){return false;}}
return true;
}
function corsie(lista){
var gruppo=[],piste=[],fineG=-1;
function chiudi(){gruppo.forEach(function(v){v.style.setProperty("--cols",String(piste.length||1));});gruppo=[];piste=[];}
lista.sort(function(a,b){return (a._l.i-b._l.i)||(a._l.f-b._l.f);});
lista.forEach(function(v){
var i=v._l.i,f=v._l.f,k;
if(gruppo.length){if(i>=fineG){chiudi();}}
fineG=gruppo.length?Math.max(fineG,f):f;
for(k=0;k<piste.length;k++){if(piste[k]<=i){break;}}
piste[k]=f;v.style.setProperty("--col",String(k));gruppo.push(v);
});
chiudi();
}
function applica(){
var n=0,perG={};
voci.forEach(function(v){
var ok=passa(v);
if(ok){n++;perG[v._g]=(perG[v._g]||0)+1;}
if(ok===v._vis){return;}
v._vis=ok;clearTimeout(v._t);
if(ok){v.removeAttribute("hidden");if(riduci){v.classList.remove("is-fuori");}else{v.classList.add("is-fuori");void v.offsetWidth;v.classList.remove("is-fuori");}}
else{v.classList.add("is-fuori");if(riduci){v.setAttribute("hidden","");}else{v._t=setTimeout(function(){v.setAttribute("hidden","");},190);}}
});
if(C.griglia){colonne.forEach(function(col){corsie(col._voci.filter(function(v){return v._vis;}));});}
var t=String(n===1?(T.uno||"%d"):(T.tanti||"%d")).replace("%d",n);
if(conto){if(conto.textContent!==t){conto.textContent=t;}}
schede.forEach(function(s){s.classList.toggle("is-vuota",!perG[+dato(s,"g")]);});
var nulla=mobile?!perG[scelto]:n===0;
if(vuoto){if(nulla){vuoto.removeAttribute("hidden");}else{vuoto.setAttribute("hidden","");}}
if(orario){if(nulla?!mobile:false){orario.setAttribute("hidden","");}else{orario.removeAttribute("hidden");}}
}
chips.forEach(function(ch){ch.addEventListener("click",function(){F.c=dato(ch,"c");chips.forEach(function(o){o.setAttribute("aria-pressed",o===ch?"true":"false");});applica();});});
scelte.forEach(function(se){se.addEventListener("change",function(){F[dato(se,"filtro")]=se.value;applica();});});
function centra(t){var w=t.parentNode;if(!w){return;}if(w.scrollWidth<=w.clientWidth){return;}w.scrollLeft=Math.max(0,t.offsetLeft-(w.clientWidth-t.offsetWidth)/2);}
function scegli(g,fuoco){
scelto=g;
schede.forEach(function(t){var on=+dato(t,"g")===g;t.setAttribute("aria-selected",on?"true":"false");t.tabIndex=on?0:-1;if(on){if(fuoco){t.focus();}centra(t);}});
colonne.forEach(function(col){col.classList.toggle("is-scelto",col._g===g);});
applica();
}
schede.forEach(function(t,i){
t.addEventListener("click",function(){scegli(+dato(t,"g"),false);});
t.addEventListener("keydown",function(e){var k=e.key,j=-1,N=schede.length;if(k==="ArrowRight"){j=(i+1)%N;}else if(k==="ArrowLeft"){j=(i-1+N)%N;}else if(k==="Home"){j=0;}else if(k==="End"){j=N-1;}if(j<0){return;}e.preventDefault();scegli(+dato(schede[j],"g"),true);});
});
var mq=null;try{mq=window.matchMedia("(max-width: 639px)");}catch(e){}
function modo(){
if(!vivo()){if(mq){if(mq.removeEventListener){mq.removeEventListener("change",modo);}else if(mq.removeListener){mq.removeListener(modo);}}return;}
mobile=mq?mq.matches:false;
colonne.forEach(function(col){
var tab=document.getElementById(el.id+"-t"+col._g);
if(mobile){if(tab){col.setAttribute("role","tabpanel");col.setAttribute("aria-labelledby",tab.id);if(!col.querySelector("button")){col.tabIndex=0;}}}
else{col.removeAttribute("role");col.removeAttribute("aria-labelledby");col.removeAttribute("tabindex");}
});
applica();
}
if(mq){if(mq.addEventListener){mq.addEventListener("change",modo);}else if(mq.addListener){mq.addListener(modo);}}
function adesso(){var d=new Date();return {g:(d.getDay()+6)%7+1,m:d.getHours()*60+d.getMinutes()};}
function segna(v,on){
var b=v.querySelector(".ooc-lez-stato");
if(on){v.classList.add("is-adesso");if(!b){var r=v.querySelector(".ooc-lez-riga");if(r){b=document.createElement("span");b.className="ooc-lez-stato";b.textContent=T.inCorso||"";r.appendChild(b);}}}
else{v.classList.remove("is-adesso");if(b){b.parentNode.removeChild(b);}}
}
function aggiorna(){
if(!vivo()){return false;}
var o=adesso();
if(C.oggi){
colonne.forEach(function(col){var on=col._g===o.g,et=col.querySelector(".ooc-oggi-et");col.classList.toggle("is-oggi",on);if(et){if(on){et.removeAttribute("hidden");}else{et.setAttribute("hidden","");}}});
schede.forEach(function(t){t.classList.toggle("is-oggi",+dato(t,"g")===o.g);});
}
if(C.adesso){
voci.forEach(function(v){var on=false;if(v._g===o.g){if(v._l.i<=o.m){if(o.m<v._l.f){on=true;}}}segna(v,on);});
colonne.forEach(function(col){var ln=col.querySelector(".ooc-adesso"),on=false;if(!ln){return;}if(col._g===o.g){if(o.m>=C.ini){if(o.m<=C.fin){on=true;}}}if(on){ln.style.setProperty("--t",((o.m-C.ini)/60).toFixed(4));ln.removeAttribute("hidden");}else{ln.setAttribute("hidden","");}});
}
return true;
}
function giro(){if(aggiorna()){setTimeout(giro,60000-(Date.now()%60000)+250);}}
function sveglia(){if(!aggiorna()){document.removeEventListener("visibilitychange",sveglia);}}
document.addEventListener("visibilitychange",sveglia);
if(dlg){
var chiudiB=dlg.querySelector(".ooc-d-chiudi"),pren=dlg.querySelector(".ooc-d-prenota"),giu=false,tc=0,ultimo=null;
var due=function(n){return (n<10?"0":"")+n;};
var hhmm=function(m){return due(Math.floor(m/60))+":"+due(m%60);};
var durata=function(m){var h=Math.floor(m/60),r=m%60;if(!h){return String(T.min||"%d min").replace("%d",r);}if(!r){return String(T.ore||"%d h").replace("%d",h);}return String(T.oreMin||"%1$d h %2$d min").replace("%1$d",h).replace("%2$d",r);};
var scrivi=function(s,val){var n=dlg.querySelector(s);if(!n){return;}n.textContent=val||"";if(val){n.removeAttribute("hidden");}else{n.setAttribute("hidden","");}};
var riga=function(k,val){var r=dlg.querySelector('[data-campo="'+k+'"]');if(!r){return;}var dd=r.querySelector("dd");if(dd){dd.textContent=val||"";}if(val){r.removeAttribute("hidden");}else{r.setAttribute("hidden","");}};
var sicuro=function(u){var a=document.createElement("a");a.href=u;var p=String(a.protocol||"").toLowerCase();return p==="http:"||p==="https:";};
var indirizzo=function(l){
var u=String(C.link||"");if(!u){return "";}
var val={corso:String(l.c||""),giorno:String(l.g||""),ora:hhmm(l.i||0)};
u=u.replace(/\{(corso|giorno|ora)\}|%7B(corso|giorno|ora)%7D/gi,function(x,a,b){return encodeURIComponent(val[String(a||b).toLowerCase()]);});
var p=u.split("?");if(p.length>2){u=p.shift()+"?"+p.join(E);}
return sicuro(u)?u:"";
};
var dopo=function(){clearTimeout(tc);dlg.classList.remove("is-aperto");if(ultimo){var u=ultimo;ultimo=null;try{u.focus();}catch(x){}}};
var fine=function(){if(!dlg.hasAttribute("open")){return;}if(typeof dlg.close==="function"){dlg.close();}else{dlg.removeAttribute("open");dopo();}};
var chiudi=function(){if(!dlg.hasAttribute("open")){return;}dlg.classList.remove("is-aperto");clearTimeout(tc);if(riduci){fine();}else{tc=setTimeout(fine,170);}};
var apri=function(v,b){
var l=v._l,co=CO[l.c]||{},g=(C.giorni||{})[l.g]||"",tit=dlg.querySelector(".ooc-d-titolo");
clearTimeout(tc);
dlg.style.setProperty("--c",v.style.getPropertyValue("--c"));
scrivi(".ooc-d-quando",(g?g+" · ":"")+hhmm(l.i)+"–"+hhmm(l.f));
if(tit){tit.textContent=co.nome||l.c||"";}
scrivi(".ooc-d-stato",v.classList.contains("is-adesso")?(T.inCorso||""):"");
scrivi(".ooc-d-descr",co.descr||"");
riga("durata",durata(l.f-l.i));riga("istruttore",l.ist);riga("sala",l.sala);
riga("livello",l.liv?((C.livelli||{})[l.liv]||l.liv):"");riga("posti",l.posti?String(l.posti):"");
scrivi(".ooc-d-nota",l.nota);
if(pren){var u=indirizzo(l);if(u){pren.setAttribute("href",u);pren.removeAttribute("hidden");}else{pren.setAttribute("hidden","");}}
ultimo=b;
if(!dlg.hasAttribute("open")){if(typeof dlg.showModal==="function"){try{dlg.showModal();}catch(x){dlg.setAttribute("open","");}}else{dlg.setAttribute("open","");}}
void dlg.offsetWidth;dlg.classList.add("is-aperto");
if(chiudiB){try{chiudiB.focus();}catch(x){}}
};
dlg.addEventListener("close",dopo);
dlg.addEventListener("cancel",function(e){e.preventDefault();chiudi();});
dlg.addEventListener("pointerdown",function(e){giu=e.target===dlg;});
dlg.addEventListener("click",function(e){if(e.target===dlg){if(giu){chiudi();}}giu=false;});
dlg.addEventListener("keydown",function(e){
if(e.key==="Escape"){if(typeof dlg.showModal!=="function"){e.preventDefault();chiudi();}return;}
if(e.key!=="Tab"){return;}
var f=[].slice.call(dlg.querySelectorAll("a[href],button,select,input,textarea,[tabindex]")).filter(function(x){return x.hasAttribute("hidden")?false:x.tabIndex>=0;});
if(!f.length){return;}
var a=f[0],z=f[f.length-1];
if(e.shiftKey){if(document.activeElement===a){e.preventDefault();z.focus();}}else{if(document.activeElement===z){e.preventDefault();a.focus();}}
});
if(chiudiB){chiudiB.addEventListener("click",chiudi);}
if(pren){pren.addEventListener("click",function(e){if(C.builder){e.preventDefault();}});}
voci.forEach(function(v){var b=v.querySelector("button.ooc-lez");if(b){b.addEventListener("click",function(){apri(v,b);});}});
}
var giorni=colonne.map(function(col){return col._g;});
var primo=function(g){for(var k=0;k<7;k++){var x=(g-1+k)%7+1;if(giorni.indexOf(x)>=0){return x;}}return giorni[0]||0;};
modo();
scegli(primo(adesso().g),false);
giro();
};}
JS;
    }
}
