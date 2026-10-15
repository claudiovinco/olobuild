<?php
/**
 * Olobuild Template Library
 *
 * Provides pre-built section templates that users can insert into their pages.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Olobuild_Template_Library {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get all available templates grouped by category.
     */
    public function get_templates() {
        // «Blocchi & Pagine» (ottobre 2026): i blocchi e le pagine intere stanno in due file nati dagli
        // esempi del catalogo delle tile. Foto, video e miniature stanno nella libreria remota (come gli
        // screenshot dei temi: il pacchetto resta leggero e le licenze delle foto non entrano nel
        // plugin): nei file i segnaposto, risolti qui. Foto e video si copiano nella Libreria media
        // del sito quando il blocco si inserisce (import_media()). Una lettura per richiesta: la
        // libreria pesa un megabyte e get_template() la chiede a ogni inserimento.
        static $cache = null;
        if ( null !== $cache ) {
            return $cache;
        }
        $templates = [];
        foreach ( [ 'template-library.json', 'template-library-pagine.json' ] as $nome ) {
            $file = OLOBUILD_PATH . 'assets/data/' . $nome;
            if ( ! file_exists( $file ) ) {
                continue;
            }
            $raw  = str_replace(
                [ '{{OLOBUILD_URL}}', '{{OLOBUILD_LIBRARY}}' ],
                [ OLOBUILD_URL, self::library_url() ],
                (string) file_get_contents( $file )
            );
            $data = json_decode( $raw, true );
            if ( is_array( $data ) && isset( $data['templates'] ) && is_array( $data['templates'] ) ) {
                $templates = array_merge( $templates, $data['templates'] );
            }
        }
        $cache = $templates;
        return $templates;
    }

    /**
     * La libreria remota di Olobuild (screenshot dei temi, foto e miniature dei blocchi). Stesso
     * filtro dell'importatore dei temi.
     */
    public static function library_url() {
        return rtrim( (string) apply_filters( 'olobuild_library_url', 'https://olotheme.com/olobuild-library' ), '/' );
    }

    /**
     * Soglia delle immagini grandi durante la copia dei media: un panorama equirettangolare (2:1, fino a
     * 8192 px) resta intero. WordPress riduce a 2560 px ogni immagine più larga, e il Tour 360 col
     * panorama da 4096 px perdeva metà della nitidezza; le altre immagini restano ridotte come prima.
     *
     * @param int|false $soglia Soglia di WordPress (2560).
     * @param array     $misure [ larghezza, altezza ].
     * @return int|false
     */
    public static function soglia_panorami( $soglia, $misure = [] ) {
        $w = (int) ( $misure[0] ?? 0 );
        $h = (int) ( $misure[1] ?? 0 );
        if ( $h > 0 && $w === 2 * $h && $w <= 8192 ) {
            return false;
        }
        return $soglia;
    }

    /**
     * Copia nella Libreria media del sito le foto della libreria remota usate da un blocco e
     * riscrive gli indirizzi: il sito non dipende più da olotheme.com. Una foto già copiata (meta
     * _olobuild_library_media) si riusa. Scarica solo da {library}/media/ e solo immagini e video MP4
     * (gli sfondi video dei blocchi, clip brevi con la copertina a parte); un file che non si copia
     * resta all'indirizzo remoto (il blocco si vede comunque).
     *
     * @return array [ 'content' => array, 'copiate' => int, 'riusate' => int, 'fallite' => int ]
     */
    public function import_media( $content ) {
        $esito = [ 'content' => $content, 'copiate' => 0, 'riusate' => 0, 'fallite' => 0 ];
        $lib   = self::library_url();
        if ( '' === $lib ) {
            return $esito;
        }
        $base = $lib . '/media/';
        $urls = [];
        $raccogli = function ( $v ) use ( &$raccogli, &$urls, $base ) {
            if ( is_array( $v ) ) {
                foreach ( $v as $x ) { $raccogli( $x ); }
            } elseif ( is_string( $v ) && false !== strpos( $v, $base ) ) {
                if ( preg_match_all( '#' . preg_quote( $base, '#' ) . '([A-Za-z0-9._-]+)#', $v, $m ) ) {
                    foreach ( $m[1] as $f ) { $urls[ $base . $f ] = $f; }
                }
            }
        };
        $raccogli( $content );
        if ( ! $urls ) {
            return $esito;
        }
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $mappa = [];
        add_filter( 'big_image_size_threshold', [ __CLASS__, 'soglia_panorami' ], 10, 2 );
        foreach ( array_slice( $urls, 0, 40, true ) as $url => $file ) {
            $gia = get_posts( [
                'post_type'      => 'attachment',
                'post_status'    => 'inherit',
                'posts_per_page' => 1,
                'fields'         => 'ids',
                'meta_key'       => '_olobuild_library_media',
                'meta_value'     => $file,
            ] );
            if ( $gia && wp_get_attachment_url( (int) $gia[0] ) ) {
                $mappa[ $url ] = wp_get_attachment_url( (int) $gia[0] );
                $esito['riusate']++;
                continue;
            }
            $tipo = wp_check_filetype( $file );
            if ( ! in_array( $tipo['type'] ?? '', [ 'image/jpeg', 'image/png', 'image/webp', 'image/gif', 'video/mp4' ], true ) ) {
                $esito['fallite']++;
                continue;
            }
            $tmp = download_url( $url, 'video/mp4' === $tipo['type'] ? 60 : 30 );
            if ( is_wp_error( $tmp ) ) {
                $esito['fallite']++;
                continue;
            }
            $aid = media_handle_sideload( [ 'name' => $file, 'tmp_name' => $tmp ], 0, preg_replace( '/\.[a-z0-9]+$/i', '', $file ) );
            if ( is_wp_error( $aid ) ) {
                @unlink( $tmp );
                $esito['fallite']++;
                continue;
            }
            update_post_meta( (int) $aid, '_olobuild_library_media', $file );
            $mappa[ $url ] = wp_get_attachment_url( (int) $aid );
            $esito['copiate']++;
        }
        remove_filter( 'big_image_size_threshold', [ __CLASS__, 'soglia_panorami' ], 10 );
        if ( $mappa ) {
            $sostituisci = function ( $v ) use ( &$sostituisci, $mappa ) {
                if ( is_array( $v ) ) {
                    foreach ( $v as $k => $x ) { $v[ $k ] = $sostituisci( $x ); }
                    return $v;
                }
                return is_string( $v ) ? strtr( $v, $mappa ) : $v;
            };
            $esito['content'] = $sostituisci( $content );
        }
        return $esito;
    }

    /** File che import_file_media() copia al massimo dagli altri siti in una richiesta (anche su più template). */
    const MEDIA_ESTERNI_MAX = 40;

    /** Secondi che import_file_media() dedica al massimo alle copie dagli altri siti in una richiesta. */
    const MEDIA_ESTERNI_SECONDI = 120;

    /** @var float Fin quando si copiano file dagli altri siti in questa richiesta (0 = non ancora cominciato). */
    private static $media_scadenza = 0.0;

    /** @var int File degli altri siti già tentati in questa richiesta. */
    private static $media_tentati = 0;

    /**
     * Foto e video di un template che arriva da un file (Importa del builder e dell'elenco, pacchetti, pagina
     * allegata a un invio). Fino alla 1.4.700 l'import salvava il file com'era: i segnaposto {{OLOBUILD_URL}} e
     * {{OLOBUILD_LIBRARY}} restavano tali (immagini rotte) e le foto restavano sul sito da cui la pagina veniva.
     *
     * - segnaposto → gli indirizzi di questo sito, come nei temi e nei Blocchi & Pagine;
     * - immagini del plugin prese da un altro sito (…/wp-content/plugins/olobuild/assets/…) → lo stesso file qui;
     * - foto e video della libreria remota → copiati come nei Blocchi & Pagine (import_media());
     * - con $esterni, foto e video caricati su un altro sito WordPress (…/wp-content/uploads/…) → copiati nella
     *   Libreria media. Un file già copiato si riusa (meta _olobuild_source_url).
     *
     * Dagli altri siti solo JPEG, PNG, WebP, GIF, AVIF, MP4 e WebM, al massimo MEDIA_ESTERNI_MAX file e
     * MEDIA_ESTERNI_SECONDI secondi per richiesta, ognuno non più grande di quanto il sito lascia caricare (e di
     * 200 MB). Un file che non si copia resta all'indirizzo di prima: la pagina si vede comunque. Le foto dei
     * servizi come Unsplash e Pexels (indirizzi senza wp-content/uploads) restano collegate: sono fatte per essere
     * usate così. Senza il permesso di caricare file si sistemano solo gli indirizzi.
     *
     * @param array $dati    Quello che si salva, es. [ 'content' => …, 'settings' => … ].
     * @param bool  $esterni Copiare anche i file degli altri siti (no per i file mandati da chiunque, gli Invii).
     * @return array [ 'dati' => array, 'copiate' => int, 'riusate' => int, 'fallite' => int ]
     */
    public function import_file_media( $dati, $esterni = true ) {
        $esito = [ 'dati' => $dati, 'copiate' => 0, 'riusate' => 0, 'fallite' => 0 ];
        if ( ! is_array( $dati ) ) {
            return $esito;
        }

        $lib     = self::library_url();
        $risolvi = function ( $v ) use ( &$risolvi, $lib ) {
            if ( is_array( $v ) ) {
                foreach ( $v as $k => $x ) {
                    $v[ $k ] = $risolvi( $x );
                }
                return $v;
            }
            if ( ! is_string( $v ) ) {
                return $v;
            }
            if ( false !== strpos( $v, '{{OLOBUILD_' ) ) {
                $v = str_replace( [ '{{OLOBUILD_URL}}', '{{OLOBUILD_LIBRARY}}' ], [ OLOBUILD_URL, $lib ], $v );
            }
            if ( false !== strpos( $v, '/wp-content/plugins/olobuild/assets/' ) ) {
                $v = (string) preg_replace_callback( '#https?://[^\s"\'<>()]+?/wp-content/plugins/olobuild/(assets/[^\s"\'<>()?\#]+)#', function ( $m ) {
                    return ( false === strpos( $m[1], '..' ) && file_exists( OLOBUILD_PATH . $m[1] ) ) ? OLOBUILD_URL . $m[1] : $m[0];
                }, $v );
            }
            return $v;
        };
        $esito['dati'] = $risolvi( $dati );

        if ( ! current_user_can( 'upload_files' ) ) {
            return $esito;
        }

        $libreria      = $this->import_media( $esito['dati'] );
        $esito['dati'] = $libreria['content'];
        foreach ( [ 'copiate', 'riusate', 'fallite' ] as $k ) {
            $esito[ $k ] += (int) $libreria[ $k ];
        }

        if ( $esterni ) {
            $altri         = $this->import_media_esterni( $esito['dati'] );
            $esito['dati'] = $altri['dati'];
            foreach ( [ 'copiate', 'riusate', 'fallite' ] as $k ) {
                $esito[ $k ] += (int) $altri[ $k ];
            }
        }
        return $esito;
    }

    /**
     * La parte «altri siti» di import_file_media(): copia nella Libreria media le foto e i video caricati su un
     * altro sito WordPress e riscrive gli indirizzi.
     *
     * @return array [ 'dati' => array, 'copiate' => int, 'riusate' => int, 'fallite' => int ]
     */
    private function import_media_esterni( $dati ) {
        $esito = [ 'dati' => $dati, 'copiate' => 0, 'riusate' => 0, 'fallite' => 0 ];
        $qui   = array_filter( array_unique( [
            strtolower( (string) wp_parse_url( home_url(), PHP_URL_HOST ) ),
            strtolower( (string) wp_parse_url( site_url(), PHP_URL_HOST ) ),
            strtolower( (string) wp_parse_url( (string) ( wp_get_upload_dir()['baseurl'] ?? '' ), PHP_URL_HOST ) ),
        ] ) );
        $urls     = [];
        $raccogli = function ( $v ) use ( &$raccogli, &$urls, $qui ) {
            if ( is_array( $v ) ) {
                foreach ( $v as $x ) {
                    $raccogli( $x );
                }
                return;
            }
            if ( ! is_string( $v ) || false === strpos( $v, '/wp-content/uploads/' ) ) {
                return;
            }
            if ( preg_match_all( '#https?://[^\s"\'<>()]+?/wp-content/uploads/[^\s"\'<>()?\#]+?\.(?:jpe?g|png|webp|gif|avif|mp4|webm)(?=[\s"\'<>()?\#&,;]|$)#i', $v, $m ) ) {
                foreach ( $m[0] as $u ) {
                    $host = strtolower( (string) wp_parse_url( $u, PHP_URL_HOST ) );
                    if ( '' !== $host && ! in_array( $host, $qui, true ) ) {
                        $urls[ $u ] = true;
                    }
                }
            }
        };
        $raccogli( $dati );
        if ( ! $urls ) {
            return $esito;
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        if ( ! self::$media_scadenza ) {
            self::$media_scadenza = microtime( true ) + self::MEDIA_ESTERNI_SECONDI;
            if ( function_exists( 'set_time_limit' ) ) {
                @set_time_limit( self::MEDIA_ESTERNI_SECONDI + 180 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- alcuni hosting lo vietano: si continua col limite del server
            }
        }
        $ammessi = [ 'image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/avif', 'video/mp4', 'video/webm' ];
        $massimo = (int) min( wp_max_upload_size(), 200 * MB_IN_BYTES );
        $mappa   = [];
        add_filter( 'big_image_size_threshold', [ __CLASS__, 'soglia_panorami' ], 10, 2 );
        foreach ( array_keys( $urls ) as $url ) {
            $gia = get_posts( [
                'post_type'      => 'attachment',
                'post_status'    => 'inherit',
                'posts_per_page' => 1,
                'fields'         => 'ids',
                'meta_key'       => '_olobuild_source_url', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_query_meta_key -- una ricerca per file copiato, solo durante un import
                'meta_value'     => $url, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_query_meta_value -- come sopra
            ] );
            if ( $gia && wp_get_attachment_url( (int) $gia[0] ) ) {
                $mappa[ $url ] = wp_get_attachment_url( (int) $gia[0] );
                $esito['riusate']++;
                continue;
            }
            $file = sanitize_file_name( wp_basename( (string) wp_parse_url( $url, PHP_URL_PATH ) ) );
            $tipo = wp_check_filetype( $file );
            if ( ! in_array( $tipo['type'] ?? '', $ammessi, true )
                || self::$media_tentati >= self::MEDIA_ESTERNI_MAX
                || microtime( true ) > self::$media_scadenza ) {
                $esito['fallite']++;
                continue;
            }
            self::$media_tentati++;
            $tmp = wp_tempnam( $file );
            if ( ! $tmp ) {
                $esito['fallite']++;
                continue;
            }
            $video = 0 === strpos( $tipo['type'], 'video/' );
            $r     = wp_safe_remote_get( $url, [
                'timeout'             => max( 5, min( $video ? 90 : 30, (int) ceil( self::$media_scadenza - microtime( true ) ) ) ),
                'stream'              => true,
                'filename'            => $tmp,
                'limit_response_size' => $massimo + 1,
            ] );
            clearstatcache( true, $tmp );
            $peso = file_exists( $tmp ) ? (int) filesize( $tmp ) : 0;
            if ( is_wp_error( $r ) || 200 !== (int) wp_remote_retrieve_response_code( $r ) || $peso < 1 || $peso > $massimo ) {
                wp_delete_file( $tmp );
                $esito['fallite']++;
                continue;
            }
            $aid = media_handle_sideload( [ 'name' => $file, 'tmp_name' => $tmp ], 0, preg_replace( '/\.[a-z0-9]+$/i', '', $file ) );
            if ( is_wp_error( $aid ) ) {
                wp_delete_file( $tmp );
                $esito['fallite']++;
                continue;
            }
            update_post_meta( (int) $aid, '_olobuild_source_url', $url );
            $mappa[ $url ] = wp_get_attachment_url( (int) $aid );
            $esito['copiate']++;
        }
        remove_filter( 'big_image_size_threshold', [ __CLASS__, 'soglia_panorami' ], 10 );
        if ( $mappa ) {
            $sostituisci = function ( $v ) use ( &$sostituisci, $mappa ) {
                if ( is_array( $v ) ) {
                    foreach ( $v as $k => $x ) {
                        $v[ $k ] = $sostituisci( $x );
                    }
                    return $v;
                }
                return is_string( $v ) ? strtr( $v, $mappa ) : $v;
            };
            $esito['dati'] = $sostituisci( $dati );
        }
        return $esito;
    }

    /**
     * Get templates filtered by category.
     */
    public function get_by_category( $category ) {
        $all = $this->get_templates();
        if ( empty( $category ) ) return $all;
        return array_values( array_filter( $all, function( $tpl ) use ( $category ) {
            return ( $tpl['category'] ?? '' ) === $category;
        } ) );
    }

    /**
     * Get a single template by ID.
     */
    public function get_template( $id ) {
        $all = $this->get_templates();
        foreach ( $all as $tpl ) {
            if ( ( $tpl['id'] ?? '' ) === $id ) {
                return $tpl;
            }
        }
        return null;
    }

    /**
     * Get list of all categories with counts.
     */
    public function get_categories() {
        $all = $this->get_templates();
        $cats = [];
        foreach ( $all as $tpl ) {
            $cat = $tpl['category'] ?? 'other';
            if ( ! isset( $cats[ $cat ] ) ) {
                $cats[ $cat ] = 0;
            }
            $cats[ $cat ]++;
        }
        return $cats;
    }

    /**
     * Save a section as a user template.
     *
     * @param string $description Descrizione breve, mostrata al passaggio del mouse nella
     *                            libreria e usata dalla ricerca (stessa chiave dei blocchi
     *                            di serie: preview_description). Facoltativa.
     */
    public function save_user_template( $name, $category, $content, $description = '' ) {
        $templates = get_option( 'olobuild_user_templates', [] );
        if ( ! is_array( $templates ) ) $templates = [];

        $id = 'user-' . wp_generate_password( 8, false );
        $templates[] = [
            'id'                  => $id,
            'name'                => sanitize_text_field( $name ),
            'category'            => sanitize_text_field( $category ),
            'preview_description' => sanitize_text_field( $description ),
            'content'             => $content,
            'created_at'          => current_time( 'mysql' ),
            'is_user'             => true,
        ];

        update_option( 'olobuild_user_templates', $templates, false );
        return $id;
    }

    /**
     * Delete a user template.
     */
    public function delete_user_template( $id ) {
        $templates = get_option( 'olobuild_user_templates', [] );
        if ( ! is_array( $templates ) ) return false;

        $found = false;
        $templates = array_values( array_filter( $templates, function( $tpl ) use ( $id, &$found ) {
            if ( ( $tpl['id'] ?? '' ) === $id ) {
                $found = true;
                return false;
            }
            return true;
        } ) );

        if ( $found ) {
            update_option( 'olobuild_user_templates', $templates, false );
        }
        return $found;
    }

    /**
     * Get all templates (built-in + user) merged.
     */
    public function get_all_templates() {
        $builtin = $this->get_templates();
        $user    = get_option( 'olobuild_user_templates', [] );
        if ( ! is_array( $user ) ) $user = [];
        return array_merge( $builtin, $user );
    }
}
