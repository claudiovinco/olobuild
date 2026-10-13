<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Lavagna a gesso — una lavagna scritta a gesso: occhiello, titolo, la data di oggi, le voci (piatto, descrizione,
 * prezzo) con i giorni della settimana in cui valgono, una nota e una firma, e disegnini a gesso che si tracciano
 * da soli. Le righe si scrivono una dopo l'altra quando la lavagna entra nello schermo; i disegni galleggiano a
 * profondità diverse scorrendo e la lavagna si inclina appena (parallasse di UIkit, solo trasformazioni); col
 * mouse (puntatore fine) si piega verso il cursore. Nata per il tema Osteria Fumo (13 ott 2026, «aggiungi
 * caratteristiche che ancora non sono nel sito»: nessuna tile faceva una lavagna), vale per bar, panetterie,
 * negozi, scuole, eventi.
 *
 * La data e le voci del giorno si calcolano nel fuso del sito e si ricalcolano nel browser: una pagina rimasta in
 * cache dal giorno prima mostra comunque le voci di oggi. Nel builder si vedono tutte, con i giorni accanto.
 * Il testo è vero testo (titolo, elenco, paragrafi): la grana del gesso è una maschera, i disegni sono decorativi.
 * Le scritte e i disegni non si muovono per chi ha chiesto al sistema di ridurre il movimento.
 */
class Olobuild_Chalkboard_Tile extends Olobuild_Tile_Base {

    protected $type     = 'chalkboard';
    protected $name     = 'Lavagna a gesso';
    protected $icon     = 'dashicons-welcome-write-blog';
    protected $category = 'marketing';
    protected $defaults = [
        // Contenuto
        'occhiello'          => 'Oggi in lavagna',
        'titolo'             => 'Fuori menù',
        'titolo_tag'         => 'h2',
        'data_mostra'        => true,
        'data_formato'       => 'giorno_data',
        'voci'               => [
            [ 'id' => 'olc-1', 'nome' => 'Zuppa di ceci e rosmarino', 'descrizione' => 'con l’olio nuovo', 'prezzo' => '9 €', 'giorni' => '', 'evidenza' => 'nessuna', 'etichetta' => '' ],
            [ 'id' => 'olc-2', 'nome' => 'Tagliatelle al ragù', 'descrizione' => 'tirate al mattarello stamattina', 'prezzo' => '14 €', 'giorni' => '', 'evidenza' => 'cerchio', 'etichetta' => '' ],
            [ 'id' => 'olc-3', 'nome' => 'Grigliata di pesce', 'descrizione' => 'solo il giovedì, finché c’è', 'prezzo' => '22 €', 'giorni' => '4', 'evidenza' => 'nessuna', 'etichetta' => '' ],
            [ 'id' => 'olc-4', 'nome' => 'Torta di riso', 'descrizione' => 'come la faceva la nonna', 'prezzo' => '6 €', 'giorni' => '', 'evidenza' => 'nessuna', 'etichetta' => 'novità!' ],
        ],
        'vuoto_testo'        => 'Oggi la lavagna è vuota: chiedi in sala.',
        'nota'               => '',
        'firma'              => '',
        'disegni'            => [
            [ 'id' => 'old-1', 'forma' => 'spiga', 'icona' => '', 'immagine' => '', 'x' => 90, 'y' => 18, 'misura' => 72, 'rotazione' => -12, 'profondita' => 70, 'colore' => '', 'opacita' => 85 ],
            [ 'id' => 'old-2', 'forma' => 'calice', 'icona' => '', 'immagine' => '', 'x' => 9, 'y' => 84, 'misura' => 60, 'rotazione' => 10, 'profondita' => 45, 'colore' => '', 'opacita' => 80 ],
            [ 'id' => 'old-3', 'forma' => 'stella', 'icona' => '', 'immagine' => '', 'x' => 90, 'y' => 86, 'misura' => 34, 'rotazione' => 0, 'profondita' => 25, 'colore' => '', 'opacita' => 75 ],
        ],
        'scrittura'          => 'scrive',
        'scrittura_velocita' => 'media',
        'disegni_tracciati'  => true,
        'parallasse'         => true,
        'intensita'          => 60,
        'mouse'              => true,
        // Stile — Aspetto
        'fondo'              => 'color-mix(in srgb, var(--olo-color-dark) 72%, var(--olo-color-secondary))',
        'texture'            => 'gesso',
        'polvere'            => 55,
        'aloni'              => 45,
        'cornice'            => 'legno',
        'cornice_colore'     => 'color-mix(in srgb, var(--olo-color-dark) 50%, var(--olo-color-accent))',
        'cornice_spessore'   => 18,
        'appesa'             => false,
        'inclinazione'       => -1.5,
        'shadow'             => 'xl',
        'shadow_h'           => '0',
        'shadow_v'           => '4',
        'shadow_blur'        => '10',
        'shadow_spread'      => '0',
        'shadow_color'       => 'rgba(0,0,0,0.15)',
        'shadow_inset'       => false,
        // Gesso
        'gesso_colore'       => 'var(--olo-color-light)',
        'accento_colore'     => 'var(--olo-color-accent)',
        'disegni_colore'     => '',
        // Testo
        'occhiello_font_family'    => 'Caveat',
        'occhiello_font_size'      => 24,
        'occhiello_font_weight'    => '700',
        'occhiello_uppercase'      => false,
        'occhiello_letter_spacing' => '',
        'occhiello_color'          => '',
        'titolo_font_family'       => 'Fredericka the Great',
        'titolo_font_size'         => 64,
        'titolo_font_size_tablet'  => 52,
        'titolo_font_size_mobile'  => 40,
        'titolo_font_weight'       => '400',
        'titolo_line_height'       => 1,
        'titolo_letter_spacing'    => '',
        'titolo_color'             => '',
        'data_font_family'         => 'Caveat',
        'data_font_size'           => 24,
        'data_font_weight'         => '400',
        'data_color'               => '',
        'voce_font_family'         => 'Caveat',
        'voce_font_size'           => 31,
        'voce_font_size_tablet'    => 29,
        'voce_font_size_mobile'    => 26,
        'voce_font_weight'         => '700',
        'voce_color'               => '',
        'descrizione_font_family'  => 'Caveat',
        'descrizione_font_size'    => 21,
        'descrizione_font_weight'  => '400',
        'descrizione_color'        => '',
        'prezzo_font_family'       => 'Caveat',
        'prezzo_font_size'         => 31,
        'prezzo_font_weight'       => '700',
        'prezzo_color'             => '',
        'nota_font_family'         => 'Caveat',
        'nota_font_size'           => 24,
        'nota_font_weight'         => '400',
        'nota_color'               => '',
        // Forma
        'larghezza'          => 820,
        'padding'            => [ 'top' => 56, 'right' => 56, 'bottom' => 56, 'left' => 56 ],
        'padding_tablet'     => [ 'top' => 44, 'right' => 40, 'bottom' => 44, 'left' => 40 ],
        'padding_mobile'     => [ 'top' => 32, 'right' => 22, 'bottom' => 32, 'left' => 22 ],
        'raggio'             => [ 'tl' => 6, 'tr' => 6, 'br' => 6, 'bl' => 6 ],
        // Disposizione
        'allineamento'       => 'center',
        'colonne'            => 1,
        'gap'                => 22,
        'puntini'            => true,
        'separatore'         => 'scarabocchio',
    ];

    const FORME = [ 'spiga', 'pesce', 'fiamma', 'foglia', 'grappolo', 'peperoncino', 'calice', 'bottiglia', 'tazza', 'pane', 'posate', 'cuore', 'stella', 'sole', 'freccia', 'scarabocchio', 'icona', 'immagine' ];

    /** I disegni a gesso, a tratto, in un riquadro 100×100. */
    const DISEGNI = [
        'spiga'        => [ 'M50 96C49 80 51 50 50 20', 'M50 22C46 16 46 9 50 3C54 9 54 16 50 22', 'M50 30C43 28 39 21 40 15C46 17 50 23 50 30', 'M50 30C57 28 61 21 60 15C54 17 50 23 50 30', 'M50 43C43 41 39 34 40 28C46 30 50 36 50 43', 'M50 43C57 41 61 34 60 28C54 30 50 36 50 43', 'M50 56C43 54 39 47 40 41C46 43 50 49 50 56', 'M50 56C57 54 61 47 60 41C54 43 50 49 50 56', 'M50 69C43 67 39 60 40 54C46 56 50 62 50 69', 'M50 69C57 67 61 60 60 54C54 56 50 62 50 69', 'M42 15L36 5', 'M58 15L64 5', 'M50 84C42 82 36 86 31 95' ],
        'pesce'        => [ 'M12 50C24 30 56 26 74 46C56 68 26 70 12 50Z', 'M74 46L90 32C88 42 88 56 90 66L74 52', 'M25.8 46.5a2.2 2.2 0 1 0 4.4 0a2.2 2.2 0 1 0 -4.4 0', 'M38 38C34 45 34 55 38 62', 'M48 44C51 47 51 52 48 55', 'M57 44C60 47 60 52 57 55' ],
        'fiamma'       => [ 'M50 94C27 92 18 73 26 55C30 64 35 66 38 62C32 44 40 25 54 8C54 26 66 34 72 48C76 58 78 67 74 77C70 88 62 94 50 94Z', 'M50 87C41 86 36 77 40 67C44 72 47 72 48 68C46 59 51 50 56 43C58 54 66 61 64 72C62 81 57 87 50 87Z' ],
        'foglia'       => [ 'M16 84C18 47 45 18 86 14C84 52 57 81 16 84Z', 'M16 84C38 62 60 40 82 18', 'M35 66L31 51', 'M47 54L45 37', 'M59 42L61 28', 'M35 66L51 69', 'M47 54L63 57', 'M59 42L73 43' ],
        'grappolo'     => [ 'M22 32a7 7 0 1 0 14 0a7 7 0 1 0 -14 0', 'M36 32a7 7 0 1 0 14 0a7 7 0 1 0 -14 0', 'M50 32a7 7 0 1 0 14 0a7 7 0 1 0 -14 0', 'M64 32a7 7 0 1 0 14 0a7 7 0 1 0 -14 0', 'M29 45a7 7 0 1 0 14 0a7 7 0 1 0 -14 0', 'M43 45a7 7 0 1 0 14 0a7 7 0 1 0 -14 0', 'M57 45a7 7 0 1 0 14 0a7 7 0 1 0 -14 0', 'M36 58a7 7 0 1 0 14 0a7 7 0 1 0 -14 0', 'M50 58a7 7 0 1 0 14 0a7 7 0 1 0 -14 0', 'M43 71a7 7 0 1 0 14 0a7 7 0 1 0 -14 0', 'M50 25C50 19 53 13 60 8', 'M51 20C44 9 30 9 24 16C31 20 41 22 51 20' ],
        'peperoncino'  => [ 'M33 24C22 28 20 46 28 60C36 74 54 86 84 92C74 82 64 70 57 56C50 42 47 30 43 25C40 22 36 22 33 24Z', 'M27 30C31 22 42 21 46 29', 'M37 23C36 15 40 9 48 7', 'M33 35C32 45 36 55 44 64' ],
        'calice'       => [ 'M32 12L68 12C70 35 62 50 50 52C38 50 30 35 32 12Z', 'M33 29C44 33 56 25 67 29', 'M50 52L50 83', 'M33 88C41 83 59 83 67 88' ],
        'bottiglia'    => [ 'M44 6L56 6L56 26C56 32 66 36 66 46L66 93L34 93L34 46C34 36 44 32 44 26Z', 'M34 56L66 56', 'M34 76L66 76', 'M44 14L56 14' ],
        'tazza'        => [ 'M22 46L70 46L66 80C64 86 60 88 54 88L38 88C32 88 28 86 26 80Z', 'M70 53C83 53 85 71 67 72', 'M12 93C30 97 62 97 82 93', 'M38 38C32 30 44 24 38 13', 'M53 38C47 30 59 24 53 11' ],
        'pane'         => [ 'M12 62C12 36 88 36 88 62C88 74 12 74 12 62Z', 'M32 45L39 58', 'M48 43L55 56', 'M64 45L71 58' ],
        'posate'       => [ 'M27 10L27 30', 'M35 10L35 30', 'M43 10L43 30', 'M27 30C27 40 43 40 43 30', 'M35 38L35 92', 'M64 10C75 22 75 44 66 52L66 92' ],
        'cuore'        => [ 'M50 86C20 64 10 44 22 28C32 16 46 20 50 32C54 20 68 16 78 28C90 44 80 64 50 86Z' ],
        'stella'       => [ 'M50 9L60 38L91 38L66 56L76 87L50 68L24 87L34 56L9 38L40 38Z' ],
        'sole'         => [ 'M32 50a18 18 0 1 0 36 0a18 18 0 1 0 -36 0', 'M76 50L88 50', 'M68.4 68.4L76.9 76.9', 'M50 76L50 88', 'M31.6 68.4L23.1 76.9', 'M24 50L12 50', 'M31.6 31.6L23.1 23.1', 'M50 24L50 12', 'M68.4 31.6L76.9 23.1' ],
        'freccia'      => [ 'M10 72C28 42 56 34 86 40', 'M71 29L87 40L73 53' ],
        'scarabocchio' => [ 'M20 52C18 27 80 21 83 47C86 74 27 81 22 57C19 44 39 34 62 36' ],
    ];

    /** Grana del gesso sulla lavagna: puntini chiari radi. */
    const POLVERE = "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='220' height='220'%3E%3Cfilter id='f'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='3' stitchTiles='stitch'/%3E%3CfeColorMatrix values='0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 1.5 -.58'/%3E%3C/filter%3E%3Crect width='220' height='220' filter='url(%23f)'/%3E%3C/svg%3E\")";
    /** Fibre della carta: puntini scuri radi. */
    const CARTA = "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='260' height='260'%3E%3Cfilter id='f'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.55' numOctaves='4' stitchTiles='stitch'/%3E%3CfeColorMatrix values='0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 1.1 -.42'/%3E%3C/filter%3E%3Crect width='260' height='260' filter='url(%23f)'/%3E%3C/svg%3E\")";
    /** Maschera del tratto di gesso: quasi pieno, con i buchi della grana. */
    const GRANA = "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160'%3E%3Cfilter id='g'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='1.1' numOctaves='2' stitchTiles='stitch'/%3E%3CfeColorMatrix values='0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 -2.6 2.25'/%3E%3C/filter%3E%3Crect width='160' height='160' filter='url(%23g)'/%3E%3C/svg%3E\")";

    public function get_controls() { return []; }

    /** Numeri pseudo-casuali ripetibili (stesso seme = stessa sequenza), in [0, 1). */
    private static function caso( $seme ) {
        $s = ( (int) $seme * 2654435761 + 1013904223 ) & 0x7fffffff;
        return static function () use ( &$s ) {
            $s = ( $s * 1103515245 + 12345 ) & 0x7fffffff;
            return $s / 2147483648;
        };
    }

    /** Attributo uk-parallax riferito alla lavagna intera (vuoto se non c'è movimento). */
    private function parallasse( array $parti ) {
        $parti = array_filter( $parti );
        if ( ! $parti ) {
            return '';
        }
        $parti[] = 'target: !.olo-chalk';
        $parti[] = 'easing: 0';
        $parti[] = 'media: (prefers-reduced-motion: no-preference)';
        return ' uk-parallax="' . esc_attr( implode( '; ', $parti ) ) . '"';
    }

    /** I giorni salvati ('1,4') → numeri ISO validi (1 lunedì … 7 domenica). */
    private static function giorni( $v ) {
        $out = [];
        foreach ( preg_split( '/[\s,|]+/', (string) $v ) as $g ) {
            $g = (int) $g;
            if ( $g >= 1 && $g <= 7 ) {
                $out[ $g ] = $g;
            }
        }
        ksort( $out );
        return array_values( $out );
    }

    /** Un tracciato a gesso (path con pathLength 1, per disegnarlo col tratteggio). */
    private static function tracciato( $d ) {
        return '<path pathLength="1" d="' . esc_attr( $d ) . '"/>';
    }

    public function render( $settings ) {
        $s       = wp_parse_args( $settings, $this->defaults );
        $uid     = 'olc-' . $this->chiave_stabile( $settings );
        $sel     = '#' . $uid;
        $builder = ! empty( $s['_builder_mode'] );
        $caso    = self::caso( crc32( $uid ) );

        // ── Colori e misure ─────────────────────────────────────────────────────────────────
        $fondo   = $this->safe_color_css( $s['fondo'] ) ?: 'var(--olo-color-dark)';
        $gesso   = $this->safe_color_css( $s['gesso_colore'] ) ?: 'var(--olo-color-light)';
        $accento = $this->safe_color_css( $s['accento_colore'] ) ?: 'var(--olo-color-accent)';
        $dis_col = $this->safe_color_css( $s['disegni_colore'] ) ?: "color-mix(in srgb, {$gesso} 88%, transparent)";
        $cornice = in_array( (string) $s['cornice'], [ 'legno', 'metallo', 'tinta', 'nessuna' ], true ) ? (string) $s['cornice'] : 'legno';
        $c_col   = $this->safe_color_css( $s['cornice_colore'] ) ?: 'color-mix(in srgb, var(--olo-color-dark) 50%, var(--olo-color-accent))';
        $spess   = 'nessuna' === $cornice ? 0 : max( 0, min( 60, absint( $s['cornice_spessore'] ) ) );
        $texture = in_array( (string) $s['texture'], [ 'gesso', 'carta', 'liscia' ], true ) ? (string) $s['texture'] : 'gesso';
        $polvere = max( 0, min( 100, absint( $s['polvere'] ) ) );
        $aloni   = max( 0, min( 100, absint( $s['aloni'] ) ) );
        $incl    = max( -12, min( 12, (float) $s['inclinazione'] ) );
        $largo   = max( 240, min( 1600, absint( $s['larghezza'] ) ) );
        $gap     = max( 0, min( 80, absint( $s['gap'] ) ) );
        $col     = max( 1, min( 3, absint( $s['colonne'] ) ) );
        $al      = 'left' === (string) $s['allineamento'] ? 'left' : 'center';
        $pad     = Olobuild_Tile_Utils::sides_css( Olobuild_Tile_Utils::spacing_sides( $s['padding'] ?? null, [], [ 56, 56, 56, 56 ] ) );
        $raggio  = Olobuild_Tile_Utils::border_radius( $s['raggio'] );
        $raggio  = ( '' === $raggio || preg_match( '/^(0(px)?\s*)+$/', trim( (string) $raggio ) ) ) ? '0' : $raggio;
        $ombra   = Olobuild_Tile_Utils::shadow_value( $s, 'shadow' );
        $mov     = ! empty( $s['parallasse'] );
        $int     = max( 0, min( 100, (float) $s['intensita'] ) ) / 100;
        $appesa  = ! empty( $s['appesa'] );

        // ── Caratteri: quelli scelti nella tipografia che non sono del sito si servono da qui (in locale) ──
        $parti_testo = [ 'occhiello', 'titolo', 'data', 'voce', 'descrizione', 'prezzo', 'nota' ];
        $famiglie    = [];
        foreach ( $parti_testo as $k ) {
            $famiglie[] = (string) ( $s[ $k . '_font_family' ] ?? '' );
        }
        $font_css = '';
        if ( class_exists( 'Olobuild_Font_Host' ) ) {
            if ( class_exists( 'Olobuild_Style_System' ) ) {
                $google = Olobuild_Style_System::famiglie_google( $famiglie );
                if ( $google ) {
                    static $serviti = [];
                    $chiave = implode( '|', $google );
                    if ( ! isset( $serviti[ $chiave ] ) ) {
                        $serviti[ $chiave ] = true;
                        $font_css           = Olobuild_Font_Host::get_font_face_css( $google, '400;700' );
                    }
                }
            }
        }

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS da colori passati da safe_color_css(), numeri forzati, costanti interne e selettori generati qui
        if ( '' !== $font_css ) {
            echo '<style>' . $font_css . '</style>';
        }
        echo '<style>';
        echo "{$sel}{--olc-gesso:{$gesso};--olc-accento:{$accento};--olc-disegni:{$dis_col};--olc-cornice:{$c_col};--olc-col:{$col};--olc-gap:{$gap}px;position:relative;" . ( $appesa ? 'padding-top:3.4rem;' : '' ) . '}';
        echo "{$sel} .olc-quadro{position:relative;margin:0 auto;max-width:{$largo}px;box-sizing:border-box;padding:{$spess}px;border-radius:{$raggio};transform:rotate({$incl}deg);" . ( 'none' !== $ombra ? "box-shadow:{$ombra};" : '' );
        if ( 'legno' === $cornice ) {
            echo 'background:linear-gradient(90deg, rgb(0 0 0 / .22), transparent 14%, rgb(255 255 255 / .07) 50%, transparent 86%, rgb(0 0 0 / .24)),repeating-linear-gradient(1.5deg, rgb(0 0 0 / .11) 0 2px, transparent 2px 8px),repeating-linear-gradient(-1deg, rgb(255 255 255 / .06) 0 1px, transparent 1px 13px),var(--olc-cornice);';
        } elseif ( 'metallo' === $cornice ) {
            echo 'background:linear-gradient(180deg, rgb(255 255 255 / .38), rgb(0 0 0 / .16) 48%, rgb(255 255 255 / .22)),var(--olc-cornice);';
        } elseif ( 'tinta' === $cornice ) {
            echo 'background:var(--olc-cornice);';
        }
        echo '}';
        echo $this->css_per_dispositivo( $s, 'larghezza', "{$sel} .olc-quadro", function ( $v ) { return 'max-width:' . max( 240, min( 1600, absint( $v ) ) ) . 'px'; } );
        if ( $spess > 0 ) {
            echo "{$sel} .olc-quadro::before{content:'';position:absolute;inset:0;border-radius:inherit;pointer-events:none;box-shadow:inset 0 0 0 1px rgb(0 0 0 / .3),inset 0 2px 0 rgb(255 255 255 / .14),inset 0 -2px 0 rgb(0 0 0 / .2)}";
        }
        // La lavagna: fondo, grana, aloni di gesso cancellato; incassata nella cornice.
        // Dentro la cornice gli angoli della lavagna restano appena smussati: il Raggio vale per il quadro.
        $r_in = $spess > 0 ? '3px' : $raggio;
        echo "{$sel} .olc-tavola{position:relative;overflow:hidden;isolation:isolate;background-color:{$fondo};border-radius:{$r_in};padding:{$pad};text-align:{$al};transform:perspective(1400px) rotateX(var(--olc-rx,0deg)) rotateY(var(--olc-ry,0deg));" . ( $spess > 0 ? 'box-shadow:inset 0 0 0 2px rgb(0 0 0 / .35),inset 0 8px 18px rgb(0 0 0 / .38);' : '' ) . '}';
        echo $this->css_per_dispositivo( $s, 'padding', "{$sel} .olc-tavola", function ( $v ) { return is_array( $v ) ? 'padding:' . Olobuild_Tile_Utils::sides_css( Olobuild_Tile_Utils::spacing_sides( $v, [], [ 56, 56, 56, 56 ] ) ) : ''; } );
        echo $this->css_per_dispositivo( $s, 'allineamento', "{$sel} .olc-tavola", function ( $v ) { return in_array( $v, [ 'left', 'center' ], true ) ? 'text-align:' . $v : ''; } );
        if ( 'gesso' === $texture ) {
            echo "{$sel} .olc-polvere{position:absolute;inset:0;z-index:-1;pointer-events:none;background-image:" . self::POLVERE . ';opacity:' . round( $polvere / 100 * 0.9, 3 ) . '}';
        } elseif ( 'carta' === $texture ) {
            echo "{$sel} .olc-polvere{position:absolute;inset:0;z-index:-1;pointer-events:none;background-image:" . self::CARTA . ';opacity:' . round( $polvere / 100 * 0.8, 3 ) . '}';
        }
        if ( $aloni > 0 ) {
            $macchie = [];
            for ( $i = 0; $i < 6; $i++ ) {
                $macchie[] = 'radial-gradient(ellipse ' . round( 14 + $caso() * 22 ) . '% ' . round( 8 + $caso() * 12 ) . '% at ' . round( 6 + $caso() * 88 ) . '% ' . round( 6 + $caso() * 88 ) . "%, color-mix(in srgb, {$gesso} " . round( $aloni * 0.16 ) . '%, transparent), transparent 70%)';
            }
            echo "{$sel} .olc-aloni{position:absolute;inset:-4%;z-index:-1;pointer-events:none;background:" . implode( ',', $macchie ) . ';filter:blur(10px)}';
        }
        // Le scritte: colore del gesso, un po' di polvere attorno, la grana come maschera.
        echo "{$sel} .olc-scritte{position:relative;color:{$gesso};text-shadow:0 0 1px color-mix(in srgb, {$gesso} 55%, transparent),0 0 12px color-mix(in srgb, {$gesso} 14%, transparent)}";
        if ( 'gesso' === $texture ) {
            echo "{$sel} .olc-scritte,{$sel} .olc-disegni{-webkit-mask-image:" . self::GRANA . ';mask-image:' . self::GRANA . ';-webkit-mask-size:160px 160px;mask-size:160px 160px}';
        }
        echo "{$sel} .olc-scritte > *{margin:0}";
        echo "{$sel} .olc-occhiello{color:{$accento};margin-bottom:.15em}";
        echo "{$sel} .olc-titolo{color:inherit;text-wrap:balance}";
        echo "{$sel} .olc-sotto{display:block;width:min(260px, 60%);height:16px;margin:.25em " . ( 'center' === $al ? 'auto' : '0' ) . ' .1em;color:' . $accento . '}';
        echo $this->css_per_dispositivo( $s, 'allineamento', "{$sel} .olc-sotto", function ( $v ) { return 'left' === $v ? 'margin-left:0;margin-right:0' : ( 'center' === $v ? 'margin-left:auto;margin-right:auto' : '' ); } );
        echo "{$sel} .olc-data{opacity:.92;margin-top:.1em}";
        echo "{$sel} .olc-voci{list-style:none;padding:0;margin:1.1em 0 0;display:grid;grid-template-columns:repeat(var(--olc-col), minmax(0, 1fr));gap:var(--olc-gap) calc(var(--olc-gap) * 2.4);text-align:inherit}";
        echo $this->css_per_dispositivo( $s, 'colonne', "{$sel} .olc-voci", function ( $v ) { return '--olc-col:' . max( 1, min( 3, absint( $v ) ) ); } );
        echo "{$sel} .olc-voce{position:relative;margin:0;padding:0}";
        echo "{$sel} .olc-voce[hidden]{display:none}";
        echo "{$sel} .olc-in{display:inline-block;max-width:100%}";
        echo "{$sel} .olc-riga1{display:inline-flex;align-items:baseline;flex-wrap:wrap;justify-content:center;gap:.15em .45em}";
        echo "{$sel} .olc-nome{position:relative}";
        echo "{$sel} .olc-prezzo{color:{$accento};white-space:nowrap}";
        echo "{$sel} .olc-puntini{display:none}";
        echo "{$sel} .olc-desc{display:block;color:color-mix(in srgb, {$gesso} 80%, transparent)}";
        echo "{$sel} .olc-etichetta{display:inline-block;color:{$accento};font-size:.62em;transform:rotate(-7deg) translateY(-.35em)}";
        echo "{$sel} .olc-stellina{display:inline-block;width:.8em;height:.8em;margin-right:.1em;color:{$accento};vertical-align:-.05em}";
        echo "{$sel} .olc-cerchio{position:absolute;left:-12%;top:-26%;width:124%;height:152%;color:{$accento};overflow:visible;pointer-events:none}";
        echo "{$sel} .olc-sottolinea{position:absolute;left:-2%;bottom:-.28em;width:104%;height:.42em;color:{$accento};overflow:visible;pointer-events:none}";
        echo "{$sel} .olc-cerchio path,{$sel} .olc-sottolinea path,{$sel} .olc-sotto path,{$sel} .olc-stellina path{fill:none;stroke:currentColor;stroke-width:2.4px;stroke-linecap:round;stroke-linejoin:round;vector-effect:non-scaling-stroke}";
        // Allineata a sinistra: nome, puntini e prezzo su una riga larga quanto la colonna.
        $sinistra = "{$sel} .olc-in{display:block}{$sel} .olc-riga1{display:flex;flex-wrap:nowrap;justify-content:flex-start}{$sel} .olc-nome{flex:0 1 auto;min-width:0}"
            . ( ! empty( $s['puntini'] ) ? "{$sel} .olc-puntini{display:block;flex:1 1 auto;min-width:1.2em;border-bottom:.12em dotted color-mix(in srgb, {$gesso} 50%, transparent);transform:translateY(-.32em)}" : "{$sel} .olc-prezzo{margin-left:auto}" );
        if ( 'left' === $al ) {
            echo $sinistra;
        }
        foreach ( [ 'tablet_landscape' => 1200, 'tablet' => 960, 'mobile_landscape' => 640, 'mobile' => 480 ] as $disp => $px ) {
            $v = (string) ( $s[ 'allineamento_' . $disp ] ?? '' );
            if ( 'left' === $v ) {
                echo '@media (max-width:' . $px . 'px){' . $sinistra . '}';
            } elseif ( 'center' === $v ) {
                echo '@media (max-width:' . $px . 'px){' . "{$sel} .olc-in{display:inline-block}{$sel} .olc-riga1{display:inline-flex;flex-wrap:wrap;justify-content:center}{$sel} .olc-puntini{display:none}}";
            }
        }
        echo "{$sel} .olc-vuoto{margin-top:1em;opacity:.85}";
        echo "{$sel} .olc-vuoto[hidden]{display:none}";
        echo "{$sel} .olc-nota{margin-top:1.3em}";
        echo "{$sel} .olc-firma{margin-top:.4em;text-align:right;color:{$accento}}";
        echo "{$sel} .olc-solo{display:inline-block;margin-left:.5em;padding:0 .45em;border:1px dashed color-mix(in srgb, {$gesso} 45%, transparent);border-radius:1em;font:600 11px/1.6 system-ui,sans-serif;letter-spacing:.04em;text-shadow:none;vertical-align:middle;opacity:.8}";
        echo "{$sel} .olc-altro{opacity:.42}";
        foreach ( [ 'occhiello', 'titolo', 'data', 'nota' ] as $k ) {
            echo $this->css_testo( $s, $k, "{$sel} .olc-{$k}" );
        }
        echo $this->css_testo( $s, 'nota', "{$sel} .olc-firma", false );
        echo $this->css_testo( $s, 'voce', "{$sel} .olc-voce" );
        echo $this->css_testo( $s, 'descrizione', "{$sel} .olc-desc" );
        echo $this->css_testo( $s, 'prezzo', "{$sel} .olc-prezzo" );
        echo $this->css_testo( $s, 'voce', "{$sel} .olc-vuoto", false );
        // Disegni
        echo "{$sel} .olc-disegni{position:absolute;inset:0;z-index:0;pointer-events:none}";
        echo "{$sel} .olc-disegno{position:absolute;display:block;color:var(--olc-disegni)}";
        echo "{$sel} .olc-disegno svg{display:block;width:100%;height:100%;overflow:visible}";
        echo "{$sel} .olc-disegno path{fill:none;stroke:currentColor;stroke-linecap:round;stroke-linejoin:round}";
        echo "{$sel} .olc-disegno img{display:block;width:100%;height:100%;object-fit:contain}";
        echo "{$sel} .olc-disegno .olo-lucide-icon,{$sel} .olc-disegno .olo-lucide-icon svg,{$sel} .olc-disegno [uk-icon] svg{width:100%;height:100%}";
        echo "{$sel} .olc-scritte{z-index:1}";
        // Appesa a un chiodo
        if ( $appesa ) {
            echo "{$sel} .olc-filo{position:absolute;left:14%;width:72%;top:-50px;height:52px;color:color-mix(in srgb, var(--olc-cornice) 55%, black);pointer-events:none}";
            echo "{$sel} .olc-filo path{fill:none;stroke:currentColor;stroke-width:2px}";
            echo "{$sel} .olc-chiodo{position:absolute;left:50%;top:-56px;width:12px;height:12px;transform:translateX(-50%);border-radius:50%;background:radial-gradient(circle at 35% 35%, rgb(255 255 255 / .7), color-mix(in srgb, var(--olc-cornice) 40%, #555) 55%);box-shadow:0 2px 3px rgb(0 0 0 / .4)}";
        }
        // Scrittura: le righe si scoprono da sinistra a destra (o compaiono), una dopo l'altra; i tratti si disegnano.
        $modo = in_array( (string) $s['scrittura'], [ 'scrive', 'compare', 'nessuna' ], true ) ? (string) $s['scrittura'] : 'scrive';
        if ( 'scrive' === $modo ) {
            echo "{$sel}.olc-pronta .olc-in{clip-path:inset(-60% 112% -60% -12%)}";
            echo "{$sel}.olc-via .olc-in{animation:olc-scrivi var(--olc-d, 900ms) cubic-bezier(.4,.1,.6,.95) var(--olc-r, 0ms) forwards}";
            echo '@keyframes olc-scrivi{to{clip-path:inset(-60% -12% -60% -12%)}}';
        } elseif ( 'compare' === $modo ) {
            echo "{$sel}.olc-pronta .olc-in{opacity:0;transform:translateY(.3em)}";
            echo "{$sel}.olc-via .olc-in{animation:olc-compari var(--olc-d, 520ms) ease var(--olc-r, 0ms) forwards}";
            echo '@keyframes olc-compari{to{opacity:1;transform:none}}';
        }
        if ( ! empty( $s['disegni_tracciati'] ) ) {
            echo "{$sel}.olc-pronta .olc-traccia path{stroke-dasharray:1 1;stroke-dashoffset:1}";
            echo "{$sel}.olc-via .olc-traccia path{animation:olc-traccia 1.1s cubic-bezier(.5,.1,.5,1) var(--olc-r, 0ms) forwards}";
            echo '@keyframes olc-traccia{to{stroke-dashoffset:0}}';
        }
        echo '@media (prefers-reduced-motion: reduce){' . "{$sel} .olc-in,{$sel} .olc-traccia path{clip-path:none !important;opacity:1 !important;transform:none !important;stroke-dashoffset:0 !important;animation:none !important}}";
        echo '</style>';
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped

        // ── Markup ──────────────────────────────────────────────────────────────────────────
        $giro_q = $mov ? round( 1.6 * $int, 2 ) : 0;
        $corsa  = $mov ? round( 3.5 * $int, 2 ) : 0;
        $plx_q  = $mov ? $this->parallasse( [
            $corsa > 0 ? "y: {$corsa}vh,-{$corsa}vh" : '',
            $giro_q > 0 ? 'rotate: ' . round( $incl - $giro_q, 2 ) . ',' . round( $incl + $giro_q, 2 ) : '',
        ] ) : '';

        echo '<div id="' . esc_attr( $uid ) . '" class="olo-chalk">';
        echo '<div class="olc-quadro"' . $plx_q . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributo da parallasse() con esc_attr()
        if ( $appesa ) {
            echo '<svg class="olc-filo" viewBox="0 0 100 52" preserveAspectRatio="none" aria-hidden="true" focusable="false"><path d="M0 52L50 4L100 52" vector-effect="non-scaling-stroke"/></svg><span class="olc-chiodo" aria-hidden="true"></span>';
        }
        echo '<div class="olc-tavola">';
        if ( 'liscia' !== $texture ) {
            echo '<div class="olc-polvere" aria-hidden="true"></div>';
        }
        if ( $aloni > 0 ) {
            echo '<div class="olc-aloni" aria-hidden="true"></div>';
        }

        // Disegni (dietro le scritte)
        $disegni = is_array( $s['disegni'] ) ? $s['disegni'] : [];
        if ( $disegni ) {
            echo '<div class="olc-disegni" aria-hidden="true">';
            foreach ( $disegni as $dg ) {
                if ( ! is_array( $dg ) ) {
                    continue;
                }
                $forma = in_array( (string) ( $dg['forma'] ?? '' ), self::FORME, true ) ? (string) $dg['forma'] : 'stella';
                $mm    = max( 12, min( 400, absint( $dg['misura'] ?? 60 ) ) );
                $x     = max( 0, min( 100, (float) ( $dg['x'] ?? 50 ) ) );
                $y     = max( 0, min( 100, (float) ( $dg['y'] ?? 50 ) ) );
                $rot   = max( -180, min( 180, (float) ( $dg['rotazione'] ?? 0 ) ) );
                $prof  = max( 0, min( 100, (float) ( $dg['profondita'] ?? 50 ) ) ) / 100;
                $op    = max( 0, min( 100, (float) ( $dg['opacita'] ?? 85 ) ) ) / 100;
                $c     = $this->safe_color_css( $dg['colore'] ?? '' );
                $stile = 'left:' . $x . '%;top:' . $y . '%;width:' . $mm . 'px;height:' . $mm . 'px;margin:-' . round( $mm / 2 ) . 'px 0 0 -' . round( $mm / 2 ) . 'px;transform:rotate(' . $rot . 'deg);opacity:' . $op . ';' . ( $c ? 'color:' . $c . ';' : '' );
                if ( 'icona' === $forma ) {
                    $dentro = $this->render_icon_html( (string) ( $dg['icona'] ?? '' ), max( 0.5, round( $mm / 20, 2 ) ) );
                } elseif ( 'immagine' === $forma ) {
                    $dentro = ! empty( $dg['immagine'] ) ? '<img src="' . esc_url( $dg['immagine'] ) . '" alt="" loading="lazy" decoding="async">' : '';
                } else {
                    $tratto = round( 2.6 * 100 / $mm, 2 );
                    $dentro = '<svg class="olc-traccia" viewBox="0 0 100 100" focusable="false" style="stroke-width:' . $tratto . '">' . implode( '', array_map( [ __CLASS__, 'tracciato' ], self::DISEGNI[ $forma ] ) ) . '</svg>';
                }
                if ( '' === $dentro ) {
                    continue;
                }
                $alza = round( 70 * $prof * $int );
                $giro = round( 8 * $prof * $int, 1 );
                $plx  = $mov ? $this->parallasse( [
                    $alza > 0 ? "y: {$alza}px,-{$alza}px" : '',
                    $giro > 0 ? 'rotate: ' . round( $rot - $giro, 1 ) . ',' . round( $rot + $giro, 1 ) : '',
                ] ) : '';
                $mano = ! empty( $s['mouse'] ) ? ' data-olc-mouse="' . round( 0.25 + $prof, 2 ) . '"' : '';
                echo '<span class="olc-disegno" style="' . esc_attr( $stile ) . '"' . $plx . '><span class="olc-mano"' . $mano . ' style="display:block;width:100%;height:100%">' . $dentro . '</span></span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- stile con esc_attr(), attributi da parallasse() e numeri forzati, svg con tracciati costanti passati da esc_attr(), icona da render_icon_html(), immagine con esc_url()
            }
            echo '</div>';
        }

        // Scritte
        $righe = static function ( $t ) {
            return max( 1, function_exists( 'mb_strlen' ) ? mb_strlen( wp_strip_all_tags( (string) $t ) ) : strlen( wp_strip_all_tags( (string) $t ) ) );
        };
        echo '<div class="olc-scritte">';
        $occ = trim( (string) $s['occhiello'] );
        if ( '' !== $occ ) {
            echo '<p class="olc-occhiello"><span class="olc-in" data-c="' . (int) $righe( $occ ) . '">' . esc_html( $occ ) . '</span></p>';
        }
        $tit = trim( (string) $s['titolo'] );
        $tag = in_array( (string) $s['titolo_tag'], [ 'h1', 'h2', 'h3', 'h4', 'p' ], true ) ? (string) $s['titolo_tag'] : 'h2';
        if ( '' !== $tit ) {
            echo '<' . $tag . ' class="olc-titolo"><span class="olc-in" data-c="' . (int) $righe( $tit ) . '">' . nl2br( esc_html( $tit ) ) . '</span></' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $tag da elenco fisso
            $sep = (string) $s['separatore'];
            if ( 'scarabocchio' === $sep ) {
                echo '<svg class="olc-sotto olc-traccia" viewBox="0 0 260 16" preserveAspectRatio="none" aria-hidden="true" focusable="false">' . self::tracciato( 'M4 10C40 4 70 13 104 8C138 3 160 12 196 7C220 4 240 9 256 6' ) . '</svg>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tracciato costante
            } elseif ( 'riga' === $sep ) {
                echo '<svg class="olc-sotto olc-traccia" viewBox="0 0 260 16" preserveAspectRatio="none" aria-hidden="true" focusable="false">' . self::tracciato( 'M4 9C70 7 160 10 256 8' ) . '</svg>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tracciato costante
            }
        }
        if ( ! empty( $s['data_mostra'] ) ) {
            $formati = [ 'giorno_data' => 'l j F', 'giorno' => 'l', 'data' => 'j F', 'breve' => 'd/m' ];
            $fmt     = $formati[ (string) $s['data_formato'] ] ?? 'l j F';
            $oggi_tx = wp_date( $fmt );
            echo '<p class="olc-data"><span class="olc-in" data-c="' . (int) $righe( $oggi_tx ) . '"><time datetime="' . esc_attr( wp_date( 'Y-m-d' ) ) . '">' . esc_html( $oggi_tx ) . '</time></span></p>';
        }

        $oggi  = (int) wp_date( 'N' );
        $sigle = [ 1 => __( 'lun', 'olobuild' ), 2 => __( 'mar', 'olobuild' ), 3 => __( 'mer', 'olobuild' ), 4 => __( 'gio', 'olobuild' ), 5 => __( 'ven', 'olobuild' ), 6 => __( 'sab', 'olobuild' ), 7 => __( 'dom', 'olobuild' ) ];
        $voci  = is_array( $s['voci'] ) ? $s['voci'] : [];
        $viste = 0;
        if ( $voci ) {
            echo '<ul class="olc-voci">';
            foreach ( $voci as $v ) {
                if ( ! is_array( $v ) ) {
                    continue;
                }
                $nome  = trim( (string) ( $v['nome'] ?? '' ) );
                $desc  = trim( (string) ( $v['descrizione'] ?? '' ) );
                $prez  = trim( (string) ( $v['prezzo'] ?? '' ) );
                $etic  = trim( (string) ( $v['etichetta'] ?? '' ) );
                if ( '' === $nome && '' === $desc && '' === $prez ) {
                    continue;
                }
                $gg    = self::giorni( $v['giorni'] ?? '' );
                $vale  = ! $gg || in_array( $oggi, $gg, true );
                $viste += $vale ? 1 : 0;
                $ev    = (string) ( $v['evidenza'] ?? 'nessuna' );
                $nascosta = ( ! $vale && ! $builder ) ? ' hidden' : '';
                echo '<li class="olc-voce' . ( ( ! $vale && $builder ) ? ' olc-altro' : '' ) . '" data-giorni="' . esc_attr( implode( ',', $gg ) ) . '"' . $nascosta . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributo fisso
                echo '<span class="olc-in" data-c="' . (int) $righe( $nome . ' ' . $prez . ' ' . $desc ) . '"><span class="olc-riga1">';
                if ( '' !== $nome ) {
                    echo '<span class="olc-nome">';
                    if ( 'stella' === $ev ) {
                        echo '<svg class="olc-stellina olc-traccia" viewBox="0 0 100 100" aria-hidden="true" focusable="false">' . self::tracciato( self::DISEGNI['stella'][0] ) . '</svg>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tracciato costante
                    }
                    echo esc_html( $nome );
                    if ( 'cerchio' === $ev ) {
                        echo '<svg class="olc-cerchio olc-traccia" viewBox="0 0 200 60" preserveAspectRatio="none" aria-hidden="true" focusable="false">' . self::tracciato( 'M26 40C8 22 60 6 128 8C184 10 206 26 186 42C166 56 90 58 46 52C20 48 10 34 34 20' ) . '</svg>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tracciato costante
                    } elseif ( 'sottolineato' === $ev ) {
                        echo '<svg class="olc-sottolinea olc-traccia" viewBox="0 0 100 12" preserveAspectRatio="none" aria-hidden="true" focusable="false">' . self::tracciato( 'M2 8C22 3 40 11 60 6C76 3 88 9 98 5' ) . '</svg>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tracciato costante
                    }
                    echo '</span>';
                }
                if ( '' !== $etic ) {
                    echo '<span class="olc-etichetta">' . esc_html( $etic ) . '</span>';
                }
                if ( '' !== $prez ) {
                    echo '<span class="olc-puntini" aria-hidden="true"></span><span class="olc-prezzo">' . esc_html( $prez ) . '</span>';
                }
                echo '</span>';
                if ( '' !== $desc ) {
                    echo '<span class="olc-desc">' . esc_html( $desc ) . '</span>';
                }
                echo '</span>';
                if ( $builder && $gg ) {
                    echo '<span class="olc-solo">' . esc_html( implode( ' · ', array_map( static function ( $g ) use ( $sigle ) { return $sigle[ $g ]; }, $gg ) ) ) . '</span>';
                }
                echo '</li>';
            }
            echo '</ul>';
        }
        $vuoto = trim( (string) $s['vuoto_testo'] );
        if ( '' !== $vuoto ) {
            echo '<p class="olc-vuoto"' . ( ( $viste > 0 || $builder ) ? ' hidden' : '' ) . '><span class="olc-in" data-c="' . (int) $righe( $vuoto ) . '">' . esc_html( $vuoto ) . '</span></p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributo fisso
        }
        $nota = trim( (string) $s['nota'] );
        if ( '' !== $nota ) {
            echo '<p class="olc-nota"><span class="olc-in" data-c="' . (int) $righe( $nota ) . '">' . nl2br( esc_html( $nota ) ) . '</span></p>';
        }
        $firma = trim( (string) $s['firma'] );
        if ( '' !== $firma ) {
            echo '<p class="olc-firma"><span class="olc-in" data-c="' . (int) $righe( $firma ) . '">' . esc_html( $firma ) . '</span></p>';
        }
        echo '</div>'; // .olc-scritte
        echo '</div></div></div>';

        // ── Comportamento: giorno e data nel fuso del sito (anche da una pagina in cache), scrittura, mouse ──
        $tz  = function_exists( 'wp_timezone_string' ) ? (string) wp_timezone_string() : '';
        $cfg = [
            'tz'        => ( '' !== $tz && false !== strpos( $tz, '/' ) ) ? $tz : ( 'UTC' === $tz ? 'UTC' : '' ),
            'off'       => (int) round( wp_timezone()->getOffset( new DateTime( 'now', wp_timezone() ) ) / 60 ),
            'formato'   => in_array( (string) $s['data_formato'], [ 'giorno_data', 'giorno', 'data', 'breve' ], true ) ? (string) $s['data_formato'] : 'giorno_data',
            'builder'   => $builder,
            'scrittura' => $modo,
            'ms'        => [ 'lenta' => 34, 'media' => 24, 'veloce' => 15 ][ (string) $s['scrittura_velocita'] ] ?? 24,
            'tracce'    => ! empty( $s['disegni_tracciati'] ),
            'mouse'     => ! empty( $s['mouse'] ),
        ];
        ?>
        <script>
        (function(){
            var el = document.getElementById(<?php echo wp_json_encode( $uid ); ?>);
            if (!el) { return; }
            if (el.getAttribute('data-olc-avviata')) { return; }
            el.setAttribute('data-olc-avviata', '1');
            var C = <?php echo wp_json_encode( $cfg ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON da wp_json_encode() di valori forzati e da elenchi fissi ?>;
            function oggi() {
                var d = new Date();
                try {
                    if (C.tz) {
                        var o = {};
                        new Intl.DateTimeFormat('en-US', { timeZone: C.tz, weekday: 'short', year: 'numeric', month: 'numeric', day: 'numeric' }).formatToParts(d).forEach(function (p) { o[p.type] = p.value; });
                        var w = { Mon: 1, Tue: 2, Wed: 3, Thu: 4, Fri: 5, Sat: 6, Sun: 7 }[o.weekday];
                        if (w) { return { w: w, y: +o.year, m: +o.month, g: +o.day }; }
                    }
                } catch (e) {}
                var s = new Date(d.getTime() + (C.off + d.getTimezoneOffset()) * 60000);
                return { w: s.getDay() === 0 ? 7 : s.getDay(), y: s.getFullYear(), m: s.getMonth() + 1, g: s.getDate() };
            }
            var g = oggi();
            if (!C.builder) {
                var voci = el.querySelectorAll('.olc-voce'), viste = 0;
                for (var i = 0; i < voci.length; i++) {
                    var gg = voci[i].getAttribute('data-giorni') || '';
                    var vale = gg === '' ? true : (',' + gg + ',').indexOf(',' + g.w + ',') !== -1;
                    voci[i].hidden = !vale;
                    if (vale) { viste++; }
                }
                var vuoto = el.querySelector('.olc-vuoto');
                if (vuoto) { vuoto.hidden = viste > 0; }
            }
            var t = el.querySelector('.olc-data time');
            if (t) {
                var opz = { giorno_data: { weekday: 'long', day: 'numeric', month: 'long' }, giorno: { weekday: 'long' }, data: { day: 'numeric', month: 'long' }, breve: { day: '2-digit', month: '2-digit' } }[C.formato];
                try {
                    opz.timeZone = 'UTC';
                    t.textContent = new Intl.DateTimeFormat(document.documentElement.lang || 'it', opz).format(new Date(Date.UTC(g.y, g.m - 1, g.g, 12)));
                    t.setAttribute('datetime', g.y + '-' + ('0' + g.m).slice(-2) + '-' + ('0' + g.g).slice(-2));
                } catch (e) {}
            }
            if (C.builder) { return; }
            if (window.matchMedia) {
                if (matchMedia('(prefers-reduced-motion: reduce)').matches) { return; }
            }
            var anima = C.scrittura !== 'nessuna';
            if (C.tracce) { anima = true; }
            if (anima) {
                var r = 0, righe = el.querySelectorAll('.olc-in');
                for (var k = 0; k < righe.length; k++) {
                    if (righe[k].closest('[hidden]')) { continue; }
                    var n = parseInt(righe[k].getAttribute('data-c'), 10) || 10;
                    var dur = C.scrittura === 'compare' ? 520 : Math.max(280, Math.min(1600, n * C.ms));
                    righe[k].style.setProperty('--olc-r', r + 'ms');
                    righe[k].style.setProperty('--olc-d', dur + 'ms');
                    righe[k].setAttribute('data-fine', r + dur);
                    if (C.scrittura === 'nessuna') { continue; }
                    r += C.scrittura === 'compare' ? 150 : Math.round(dur * 0.6 + 40);
                }
                var tracce = el.querySelectorAll('.olc-traccia'), dopo = Math.round(r * 0.2), q = 0;
                for (var z = 0; z < tracce.length; z++) {
                    if (tracce[z].closest('[hidden]')) { continue; }
                    var riga = tracce[z].closest('.olc-in');
                    if (!riga) {
                        var titolo = tracce[z].previousElementSibling;
                        if (titolo) { riga = titolo.querySelector('.olc-in'); }
                    }
                    var quando = riga ? (parseInt(riga.getAttribute('data-fine'), 10) || 0) : dopo + q * 420;
                    if (!riga) { q++; }
                    tracce[z].style.setProperty('--olc-r', quando + 'ms');
                }
                el.classList.add('olc-pronta');
                var via = function () { el.classList.add('olc-via'); };
                if ('IntersectionObserver' in window) {
                    var io = new IntersectionObserver(function (es) {
                        for (var e = 0; e < es.length; e++) {
                            if (es[e].isIntersecting) { via(); io.disconnect(); }
                        }
                    }, { threshold: 0.2 });
                    io.observe(el);
                } else { via(); }
            }
            if (!C.mouse) { return; }
            if (!window.matchMedia) { return; }
            if (!matchMedia('(hover: hover) and (pointer: fine)').matches) { return; }
            var tavola = el.querySelector('.olc-tavola'), mani = [].slice.call(el.querySelectorAll('[data-olc-mouse]'));
            var tx = 0, ty = 0, cx = 0, cy = 0, giro = 0;
            function passo() {
                cx += (tx - cx) * 0.09; cy += (ty - cy) * 0.09;
                tavola.style.setProperty('--olc-ry', (cx * 5).toFixed(2) + 'deg');
                tavola.style.setProperty('--olc-rx', (cy * -4).toFixed(2) + 'deg');
                mani.forEach(function (m) {
                    var f = parseFloat(m.getAttribute('data-olc-mouse')) || 0;
                    m.style.transform = 'translate3d(' + (cx * f * 14).toFixed(2) + 'px,' + (cy * f * 10).toFixed(2) + 'px,0)';
                });
                giro = (Math.abs(tx - cx) > 0.002 || Math.abs(ty - cy) > 0.002) ? requestAnimationFrame(passo) : 0;
            }
            el.addEventListener('pointermove', function (e) {
                var b = el.getBoundingClientRect();
                tx = (e.clientX - b.left) / b.width - 0.5;
                ty = (e.clientY - b.top) / b.height - 0.5;
                if (!giro) { giro = requestAnimationFrame(passo); }
            });
            el.addEventListener('pointerleave', function () { tx = 0; ty = 0; if (!giro) { giro = requestAnimationFrame(passo); } });
        })();
        </script>
        <?php
        return ob_get_clean();
    }

    /** CSS di un testo dal controllo tipografia (famiglia, misura per dispositivo, peso, maiuscolo, spaziatura, interlinea, colore). */
    private function css_testo( $s, $k, $selettore, $con_colore = true ) {
        $d   = '';
        $fam = $this->resolve_font_family( $s[ $k . '_font_family' ] ?? '' );
        if ( '' !== $fam ) {
            // I nomi semplici diventano «'Nome', cursive»: se il carattere non arriva resta una scrittura a mano.
            if ( false === strpos( $fam, ',' ) ) {
                if ( false === strpos( $fam, 'var(' ) ) {
                    $fam = "'" . trim( $fam, "'\" " ) . "', cursive";
                }
            }
        }
        $fs  = (float) ( $s[ $k . '_font_size' ] ?? 0 );
        $fw  = $this->font_weight_css( $s[ $k . '_font_weight' ] ?? '' );
        $ls  = $s[ $k . '_letter_spacing' ] ?? '';
        $lh  = $s[ $k . '_line_height' ] ?? '';
        $col = $con_colore ? $this->safe_color_css( $s[ $k . '_color' ] ?? '' ) : '';
        if ( $fam ) { $d .= "font-family:{$fam};"; }
        if ( $fs > 0 ) { $d .= 'font-size:' . $fs . 'px;'; }
        if ( $fw ) { $d .= "font-weight:{$fw};"; }
        if ( ! empty( $s[ $k . '_uppercase' ] ) ) { $d .= 'text-transform:uppercase;'; }
        if ( is_numeric( $ls ) ) { $d .= 'letter-spacing:' . (float) $ls . 'px;'; }
        $d .= is_numeric( $lh ) ? 'line-height:' . (float) $lh . ';' : 'line-height:1.15;';
        if ( $col ) { $d .= "color:{$col};"; }
        $css = "{$selettore}{{$d}}";
        foreach ( [ 'tablet' => 960, 'mobile' => 480 ] as $disp => $px ) {
            $v = (float) ( $s[ $k . '_font_size_' . $disp ] ?? 0 );
            if ( $v > 0 ) {
                $css .= '@media (max-width:' . $px . 'px){' . $selettore . '{font-size:' . $v . 'px}}';
            }
        }
        return $css;
    }
}
