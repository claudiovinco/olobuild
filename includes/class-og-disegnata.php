<?php
/**
 * L'immagine di condivisione disegnata (1.4.1218, Configurazione › SEO › Open Graph).
 *
 * Per ogni contenuto senza un'immagine OG sua, una 1200×630 col titolo, il nome del sito e la foto in evidenza, nel
 * colore primario e nel carattere dei titoli del sito: è quella che WhatsApp, Facebook e LinkedIn mostrano quando
 * qualcuno condivide la pagina. Si disegna sul server (GD + FreeType) la prima volta che serve e si tiene in
 * uploads/olobuild-og; quando cambiano il titolo, la foto, lo stile, il colore o il carattere il nome del file cambia
 * e si ridisegna da sola (la vecchia si toglie).
 *
 * Il carattere: il TTF della famiglia dei titoli da Google Fonts (scaricato una volta), altrimenti un carattere del
 * server. Se manca anche quello, niente immagine disegnata: resta la foto in evidenza di sempre.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Og_Disegnata {

    const VERSIONE = '1';
    const W        = 1200;
    const H        = 630;
    const STILI    = [ 'foto', 'meta', 'colore' ];

    /** Caratteri del server da usare se quello dei titoli non si trova. */
    const FONT_DI_RISERVA = [
        '/usr/share/fonts/google-droid-sans-fonts/DroidSans-Bold.ttf',
        '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
        '/usr/share/fonts/dejavu/DejaVuSans-Bold.ttf',
        '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
        '/usr/share/fonts/google-droid-sans-fonts/DroidSansFallbackFull.ttf',
    ];

    public static function init() {
        add_action( 'rest_api_init', [ __CLASS__, 'rotte' ] );
    }

    public static function opzioni() {
        $o = class_exists( 'Olobuild_Seo_Settings' ) ? Olobuild_Seo_Settings::get( 'olo_seo_social' ) : get_option( 'olo_seo_social', [] );
        return is_array( $o ) ? $o : [];
    }

    public static function attiva() {
        $o = self::opzioni();
        return ! empty( $o['og_disegnata'] ) && function_exists( 'imagettftext' ) && function_exists( 'imagejpeg' );
    }

    /** L'indirizzo dell'immagine disegnata del contenuto ('' se non si può disegnare). */
    public static function url_per( $post ) {
        $post = get_post( $post );
        if ( ! $post ) {
            return '';
        }
        $o = self::opzioni();
        return self::file_per( 'c' . (int) $post->ID, self::dati( $post, self::stile( $o['og_stile'] ?? '' ) ) );
    }

    private static function stile( $s ) {
        return in_array( $s, self::STILI, true ) ? $s : 'foto';
    }

    /** Quello che finisce nell'immagine. Senza foto lo stile diventa «Solo colore». */
    private static function dati( $post, $stile ) {
        $titolo = $post ? trim( wp_strip_all_tags( html_entity_decode( get_the_title( $post ), ENT_QUOTES, 'UTF-8' ) ) ) : '';
        $sito   = trim( wp_strip_all_tags( html_entity_decode( get_bloginfo( 'name' ), ENT_QUOTES, 'UTF-8' ) ) );
        $foto   = '';
        $tid    = $post ? (int) get_post_thumbnail_id( $post ) : 0;
        if ( $tid ) {
            $foto = self::percorso_foto( $tid );
        }
        if ( '' === $foto ) {
            $stile = 'colore';
        }
        return [
            'stile'  => $stile,
            'titolo' => '' !== $titolo ? $titolo : $sito,
            'sito'   => $sito,
            'host'   => (string) wp_parse_url( home_url(), PHP_URL_HOST ),
            'foto'   => $foto,
            'colore' => self::colore(),
            'font'   => self::font(),
        ];
    }

    /** Il file in cache, disegnato se manca. */
    private static function file_per( $chiave, $dati ) {
        if ( '' === $dati['font'] ) {
            return '';
        }
        $up = wp_upload_dir();
        if ( ! empty( $up['error'] ) ) {
            return '';
        }
        $dir   = trailingslashit( $up['basedir'] ) . 'olobuild-og';
        $mtime = '' !== $dati['foto'] ? (string) @filemtime( $dati['foto'] ) : '';
        $nome  = $chiave . '-' . substr( md5( self::VERSIONE . wp_json_encode( $dati ) . $mtime ), 0, 12 ) . '.jpg';
        $file  = $dir . '/' . $nome;
        $url   = set_url_scheme( trailingslashit( $up['baseurl'] ) . 'olobuild-og/' . $nome );
        if ( file_exists( $file ) ) {
            return $url;
        }
        if ( ! wp_mkdir_p( $dir ) ) {
            return '';
        }
        foreach ( glob( $dir . '/' . $chiave . '-*.jpg' ) ?: [] as $vecchia ) {
            @unlink( $vecchia ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- la copia vecchia può essere già sparita
        }
        return self::disegna( $file, $dati ) ? $url : '';
    }

    /** La misura della foto più piccola che copre 1200×630, altrimenti l'originale. */
    private static function percorso_foto( $tid ) {
        $orig = get_attached_file( $tid );
        if ( ! $orig || ! file_exists( $orig ) ) {
            return '';
        }
        $meta  = wp_get_attachment_metadata( $tid );
        $scelta = '';
        $area   = PHP_INT_MAX;
        foreach ( ( is_array( $meta ) ? ( $meta['sizes'] ?? [] ) : [] ) as $m ) {
            $w = (int) ( $m['width'] ?? 0 );
            $h = (int) ( $m['height'] ?? 0 );
            if ( $w >= self::W && $h >= self::H && $w * $h < $area && ! empty( $m['file'] ) ) {
                $p = trailingslashit( dirname( $orig ) ) . $m['file'];
                if ( file_exists( $p ) ) {
                    $scelta = $p;
                    $area   = $w * $h;
                }
            }
        }
        if ( '' !== $scelta ) {
            return $scelta;
        }
        // l'originale, se non è enorme (la memoria del server): altrimenti «large»
        $w = is_array( $meta ) ? (int) ( $meta['width'] ?? 0 ) : 0;
        if ( $w && $w > 4200 ) {
            $large = image_get_intermediate_size( $tid, 'large' );
            if ( $large && ! empty( $large['path'] ) ) {
                $up = wp_upload_dir();
                $p  = trailingslashit( $up['basedir'] ) . $large['path'];
                return file_exists( $p ) ? $p : '';
            }
        }
        return $orig;
    }

    /** Il colore primario del sito (il global color «primary» vince, come nel CSS). */
    private static function colore() {
        $hex = '';
        foreach ( (array) get_option( 'olobuild_global_colors', [] ) as $gc ) {
            if ( is_array( $gc ) && 'primary' === ( $gc['id'] ?? '' ) && is_string( $gc['value'] ?? null ) ) {
                $hex = $gc['value'];
            }
        }
        if ( '' === $hex ) {
            $st  = get_option( 'olobuild_styles', [] );
            $hex = is_array( $st ) && is_string( $st['colors']['primary'] ?? null ) ? $st['colors']['primary'] : '';
        }
        $rgb = self::rgb( $hex );
        return $rgb ? $rgb : [ 225, 71, 79 ];
    }

    private static function rgb( $hex ) {
        $hex = ltrim( trim( (string) $hex ), '#' );
        if ( preg_match( '/^[0-9a-f]{3}$/i', $hex ) ) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if ( ! preg_match( '/^[0-9a-f]{6}/i', $hex ) ) {
            return null;
        }
        return [ hexdec( substr( $hex, 0, 2 ) ), hexdec( substr( $hex, 2, 2 ) ), hexdec( substr( $hex, 4, 2 ) ) ];
    }

    /** Il TTF dei titoli: da Google Fonts una volta sola, poi dalla cartella; se no un carattere del server. */
    private static function font() {
        $famiglia = '';
        $peso     = 700;
        if ( class_exists( 'Olobuild_Style_System' ) ) {
            $t   = Olobuild_Style_System::instance()->get_styles()['typography'] ?? [];
            $val = ! empty( $t['font_family_heading'] ) ? $t['font_family_heading'] : ( $t['font_family'] ?? '' );
            $fam = Olobuild_Style_System::famiglie_google( [ $val ] );
            $famiglia = $fam ? (string) $fam[0] : '';
            $p = (int) ( $t['font_weight_heading'] ?? 0 );
            if ( $p >= 100 && $p <= 900 ) {
                $peso = $p;
            }
        }
        // Nessun carattere scelto nello Stile globale (i temi lo mettono nelle loro pagine): Inter, leggibile e neutro.
        if ( '' === $famiglia ) {
            $famiglia = 'Inter';
        }
        if ( '' !== $famiglia ) {
            $up   = wp_upload_dir();
            $dir  = trailingslashit( $up['basedir'] ) . 'olobuild-og/caratteri';
            $file = $dir . '/' . sanitize_file_name( strtolower( $famiglia ) ) . '-' . $peso . '.ttf';
            if ( file_exists( $file ) ) {
                return $file;
            }
            $ko = 'olo_og_font_ko_' . md5( $famiglia . $peso );
            if ( ! get_transient( $ko ) ) {
                $nome = rawurlencode( $famiglia );
                foreach ( [ $nome . ':wght@' . $peso, $nome . ':wght@700', $nome ] as $q ) {
                    $css = wp_remote_get( 'https://fonts.googleapis.com/css2?family=' . str_replace( '%20', '+', $q ), [ 'timeout' => 8, 'user-agent' => 'Wget/1.21' ] );
                    if ( is_wp_error( $css ) || 200 !== (int) wp_remote_retrieve_response_code( $css ) ) {
                        continue;
                    }
                    if ( ! preg_match( '#url\((https://fonts\.gstatic\.com/[^)]+\.ttf)\)#', wp_remote_retrieve_body( $css ), $m ) ) {
                        continue;
                    }
                    $ttf = wp_remote_get( $m[1], [ 'timeout' => 12 ] );
                    $bin = is_wp_error( $ttf ) ? '' : wp_remote_retrieve_body( $ttf );
                    if ( strlen( $bin ) > 1000 && wp_mkdir_p( $dir ) && file_put_contents( $file, $bin ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
                        return $file;
                    }
                }
                set_transient( $ko, 1, DAY_IN_SECONDS );
            }
        }
        foreach ( self::FONT_DI_RISERVA as $f ) {
            if ( @is_readable( $f ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- open_basedir
                return $f;
            }
        }
        return '';
    }

    // ── il disegno ───────────────────────────────────────────────────────────────────────────────────

    private static function disegna( $file, $d ) {
        $W  = self::W;
        $H  = self::H;
        $M  = 72;
        $im = imagecreatetruecolor( $W, $H );
        if ( ! $im ) {
            return false;
        }
        imagealphablending( $im, true );
        list( $r, $g, $b ) = $d['colore'];
        $chiaro = self::luminanza( $d['colore'] ) > 0.6;
        $bianco = imagecolorallocate( $im, 255, 255, 255 );
        $scuro  = imagecolorallocate( $im, 17, 17, 22 );
        $prim   = imagecolorallocate( $im, $r, $g, $b );
        $foto   = '' !== $d['foto'] ? self::carica( $d['foto'] ) : null;
        $stile  = $foto ? $d['stile'] : 'colore';

        if ( 'foto' === $stile ) {
            self::copertina( $im, $foto, 0, 0, $W, $H );
            self::sfuma( $im, 0, 0, $W, 230, [ 8, 8, 12 ], 0.55, 0 );    // in alto: il nome del sito si legge su ogni foto
            self::sfuma( $im, 0, 200, $W, $H, [ 8, 8, 12 ], 0, 0.88 );   // in basso: il titolo
            self::etichetta( $im, $d, $M, $M, $bianco, $prim, 17 );
            self::titolo( $im, $d['titolo'], $d['font'], $M, $H - $M, $W - 2 * $M - 120, 'basso', $bianco, 3 );
        } elseif ( 'meta' === $stile ) {
            imagefilledrectangle( $im, 0, 0, (int) ( $W / 2 ) - 1, $H, $prim );
            self::copertina( $im, $foto, (int) ( $W / 2 ), 0, (int) ( $W / 2 ), $H );
            $testo = $chiaro ? $scuro : $bianco;
            self::etichetta( $im, $d, $M - 16, $M - 16, $testo, $testo, 15 );
            self::titolo( $im, $d['titolo'], $d['font'], $M - 16, (int) ( $H / 2 ) + 12, (int) ( $W / 2 ) - 2 * ( $M - 16 ), 'centro', $testo, 5 );
        } else {
            imagefilledrectangle( $im, 0, 0, $W, $H, $prim );
            // un cerchio grande appena più chiaro (o più scuro): il colore non resta piatto
            $c = self::mescola( $d['colore'], $chiaro ? [ 0, 0, 0 ] : [ 255, 255, 255 ], $chiaro ? 0.07 : 0.1 );
            imagefilledellipse( $im, (int) ( $W * 0.9 ), (int) ( $H * 0.08 ), 1020, 1020, imagecolorallocate( $im, $c[0], $c[1], $c[2] ) );
            $testo = $chiaro ? $scuro : $bianco;
            self::etichetta( $im, $d, $M, $M, $testo, $testo, 17 );
            self::titolo( $im, $d['titolo'], $d['font'], $M, (int) ( $H / 2 ) + 16, $W - 2 * $M - 160, 'centro', $testo, 3 );
            if ( '' !== $d['host'] ) {
                imagettftext( $im, 15, 0, $M, $H - $M + 10, $testo, $d['font'], $d['host'] );
            }
        }
        $ok = imagejpeg( $im, $file, 88 );
        imagedestroy( $im );
        if ( $foto ) {
            imagedestroy( $foto );
        }
        return $ok;
    }

    private static function carica( $path ) {
        $bin = @file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents,WordPress.PHP.NoSilencedErrors.Discouraged
        if ( ! $bin ) {
            return null;
        }
        $img = @imagecreatefromstring( $bin ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- formato che GD non legge: niente foto
        return $img ? $img : null;
    }

    /** La foto che riempie il riquadro; le verticali si tagliano più sotto che sopra (le teste restano). */
    private static function copertina( $dst, $src, $dx, $dy, $dw, $dh ) {
        $sw    = imagesx( $src );
        $sh    = imagesy( $src );
        $scala = max( $dw / $sw, $dh / $sh );
        $cw    = $dw / $scala;
        $ch    = $dh / $scala;
        $cx    = ( $sw - $cw ) / 2;
        $cy    = ( $sh - $ch ) * ( $sh > $sw * 0.9 ? 0.22 : 0.5 );
        imagecopyresampled( $dst, $src, (int) $dx, (int) $dy, (int) round( $cx ), (int) round( $cy ), (int) $dw, (int) $dh, (int) round( $cw ), (int) round( $ch ) );
    }

    /** Una sfumatura verticale di un colore, da un'opacità all'altra. */
    private static function sfuma( $im, $x0, $y0, $x1, $y1, $rgb, $a0, $a1 ) {
        $n = max( 1, $y1 - $y0 );
        for ( $y = $y0; $y < $y1; $y++ ) {
            $t = ( $y - $y0 ) / $n;
            $t = $t * $t * ( 3 - 2 * $t );
            $a = $a0 + ( $a1 - $a0 ) * $t;
            if ( $a <= 0.004 ) {
                continue;
            }
            $col = imagecolorallocatealpha( $im, $rgb[0], $rgb[1], $rgb[2], (int) round( 127 - min( 1, $a ) * 127 ) );
            imageline( $im, $x0, $y, $x1, $y, $col );
        }
    }

    /** Il nome del sito in alto, dopo una lineetta di colore. */
    private static function etichetta( $im, $d, $x, $y, $testo, $barra, $corpo ) {
        $nome = mb_strtoupper( mb_substr( $d['sito'], 0, 42 ) );
        if ( '' === $nome ) {
            return;
        }
        imagefilledrectangle( $im, $x, $y + 8, $x + 40, $y + 13, $barra );
        imagettftext( $im, $corpo, 0, $x + 56, $y + 20, $testo, $d['font'], $nome );
    }

    /**
     * Il titolo a capo sulla larghezza, nel corpo più grande che ci sta in $max righe (dal 62 al 32); se non ci sta
     * nemmeno nel più piccolo, l'ultima riga finisce coi puntini. 'basso' = l'ultima riga poggia su $y; 'centro' = il
     * blocco è centrato su $y.
     */
    private static function titolo( $im, $testo, $font, $x, $y, $larg, $dove, $colore, $max ) {
        $righe = [];
        $corpo = 62;
        for ( ; $corpo >= 32; $corpo -= 2 ) {
            $righe = self::a_capo( $testo, $font, $corpo, $larg );
            if ( count( $righe ) <= $max ) {
                break;
            }
        }
        if ( $corpo < 32 ) {
            $corpo = 32;
        }
        if ( count( $righe ) > $max ) {
            $righe = array_slice( $righe, 0, $max );
            $ult   = $righe[ $max - 1 ];
            while ( '' !== $ult && self::larghezza( $ult . '…', $font, $corpo ) > $larg ) {
                $ult = rtrim( mb_substr( $ult, 0, -1 ) );
            }
            $righe[ $max - 1 ] = $ult . '…';
        }
        $alt = (int) round( $corpo * 1.62 );   // GD misura in punti a 96 dpi: 1 punto ≈ 1,33 px
        $n   = count( $righe );
        $y0  = 'basso' === $dove ? $y - ( $n - 1 ) * $alt : (int) round( $y - ( $n * $alt ) / 2 + $alt * 0.74 );
        foreach ( $righe as $i => $riga ) {
            imagettftext( $im, $corpo, 0, $x, $y0 + $i * $alt, $colore, $font, $riga );
        }
    }

    private static function a_capo( $testo, $font, $corpo, $larg ) {
        $parole = preg_split( '/\s+/u', trim( $testo ) );
        $righe  = [];
        $riga   = '';
        foreach ( $parole as $p ) {
            $prova = '' === $riga ? $p : $riga . ' ' . $p;
            if ( '' !== $riga && self::larghezza( $prova, $font, $corpo ) > $larg ) {
                $righe[] = $riga;
                $riga    = $p;
            } else {
                $riga = $prova;
            }
        }
        if ( '' !== $riga ) {
            $righe[] = $riga;
        }
        return $righe;
    }

    private static function larghezza( $testo, $font, $corpo ) {
        $b = imagettfbbox( $corpo, 0, $font, $testo );
        return $b ? abs( $b[2] - $b[0] ) : 0;
    }

    private static function luminanza( $rgb ) {
        $l = [];
        foreach ( $rgb as $c ) {
            $c   = $c / 255;
            $l[] = $c <= 0.03928 ? $c / 12.92 : pow( ( $c + 0.055 ) / 1.055, 2.4 );
        }
        return 0.2126 * $l[0] + 0.7152 * $l[1] + 0.0722 * $l[2];
    }

    private static function mescola( $a, $b, $t ) {
        return [
            (int) round( $a[0] + ( $b[0] - $a[0] ) * $t ),
            (int) round( $a[1] + ( $b[1] - $a[1] ) * $t ),
            (int) round( $a[2] + ( $b[2] - $a[2] ) * $t ),
        ];
    }

    // ── l'anteprima nella scheda SEO ─────────────────────────────────────────────────────────────────

    public static function rotte() {
        register_rest_route( 'olobuild/v1', '/seo/og-anteprima', [
            'methods'             => 'GET',
            'callback'            => [ __CLASS__, 'anteprima' ],
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },
        ] );
    }

    /** L'immagine di prova sull'ultimo contenuto con la foto in evidenza (o sull'ultimo e basta), nello stile chiesto. */
    public static function anteprima( $req ) {
        if ( ! function_exists( 'imagettftext' ) ) {
            return new WP_Error( 'olo_og_gd', __( 'Il server non sa disegnare le immagini (manca GD con FreeType).', 'olobuilder' ), [ 'status' => 500 ] );
        }
        $stile = self::stile( (string) $req->get_param( 'stile' ) );
        $tipi  = array_values( array_diff( get_post_types( [ 'public' => true ] ), [ 'attachment' ] ) );
        $q     = get_posts( [ 'post_type' => $tipi, 'post_status' => 'publish', 'numberposts' => 1, 'meta_key' => '_thumbnail_id', 'orderby' => 'date', 'order' => 'DESC' ] ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
        if ( ! $q ) {
            $q = get_posts( [ 'post_type' => $tipi, 'post_status' => 'publish', 'numberposts' => 1 ] );
        }
        $post = $q ? $q[0] : null;
        $url  = self::file_per( 'anteprima-' . $stile, self::dati( $post, $stile ) );
        if ( '' === $url ) {
            return new WP_Error( 'olo_og_font', __( 'Nessun carattere disponibile per disegnare il titolo.', 'olobuilder' ), [ 'status' => 500 ] );
        }
        return rest_ensure_response( [
            'url'    => $url,
            'titolo' => $post ? get_the_title( $post ) : '',
        ] );
    }
}
