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
        $file = OLOBUILD_PATH . 'assets/data/template-library.json';
        if ( ! file_exists( $file ) ) {
            return [];
        }
        $json = file_get_contents( $file );
        $data = json_decode( $json, true );
        if ( ! is_array( $data ) ) {
            return [];
        }
        // Support both flat array and {version, templates} wrapper
        $templates = $data;
        if ( isset( $data['templates'] ) && is_array( $data['templates'] ) ) {
            $templates = $data['templates'];
        }

        // Blocchi nati dagli esempi del catalogo delle tile. Foto e miniature stanno nella libreria
        // remota (come gli screenshot dei temi: il pacchetto resta leggero e le licenze delle foto non
        // entrano nel plugin): nel file i segnaposto, risolti qui. Le foto si copiano nella Libreria
        // media del sito quando il blocco si inserisce (import_media()).
        $catalogo = OLOBUILD_PATH . 'assets/data/template-library-catalogo.json';
        if ( file_exists( $catalogo ) ) {
            $raw  = str_replace(
                [ '{{OLOBUILD_URL}}', '{{OLOBUILD_LIBRARY}}' ],
                [ OLOBUILD_URL, self::library_url() ],
                (string) file_get_contents( $catalogo )
            );
            $extra = json_decode( $raw, true );
            if ( is_array( $extra ) && isset( $extra['templates'] ) && is_array( $extra['templates'] ) ) {
                $templates = array_merge( $templates, $extra['templates'] );
            }
        }

        // Load additional page templates from separate files
        $pages_dir = OLOBUILD_PATH . 'assets/data/page-templates/';
        if ( is_dir( $pages_dir ) ) {
            foreach ( glob( $pages_dir . '*.json' ) as $page_file ) {
                $page_json = file_get_contents( $page_file );
                $page_data = json_decode( $page_json, true );
                if ( is_array( $page_data ) && ! empty( $page_data['id'] ) ) {
                    $templates[] = $page_data;
                }
            }
        }

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
     * Copia nella Libreria media del sito le foto della libreria remota usate da un blocco e
     * riscrive gli indirizzi: il sito non dipende più da olotheme.com. Una foto già copiata (meta
     * _olobuild_library_media) si riusa. Scarica solo da {library}/media/ e solo immagini; una foto
     * che non si copia resta all'indirizzo remoto (il blocco si vede comunque).
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
            if ( ! in_array( $tipo['type'] ?? '', [ 'image/jpeg', 'image/png', 'image/webp', 'image/gif' ], true ) ) {
                $esito['fallite']++;
                continue;
            }
            $tmp = download_url( $url, 30 );
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
