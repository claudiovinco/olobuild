<?php
/**
 * OLObuild — aggiornamenti da olotheme.com.
 *
 * Il plugin non sta su wordpress.org. L'intestazione «Update URI: https://olotheme.com/olobuild/» fa sì che
 * WordPress chieda gli aggiornamenti al filtro update_plugins_olotheme.com invece che a wordpress.org, e che un
 * plugin omonimo di wordpress.org non lo sostituisca. Il filtro chiede a OLO Store (olotheme.com) se c'è una
 * versione nuova: le informazioni arrivano sempre, il pacchetto solo con una licenza valida attivata su questo
 * sito. OLObuild Pro viene con OLObooking, quindi la chiave è quella di OLObooking (filtro
 * olobuild_update_license_key per darne un'altra). La chiave viaggia nel corpo della richiesta, mai nell'indirizzo.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Updater {

    const ENDPOINT = 'https://olotheme.com/wp-json/olo-store/v1/check';
    const SLUG     = 'olobuild';
    const CACHE    = 'olobuild_update_info';

    public static function init() {
        add_filter( 'update_plugins_olotheme.com', [ __CLASS__, 'update' ], 10, 4 );
        add_filter( 'plugins_api', [ __CLASS__, 'details' ], 20, 3 );
        add_action( 'in_plugin_update_message-' . self::basename(), [ __CLASS__, 'row_message' ], 10, 2 );
        add_action( 'upgrader_process_complete', [ __CLASS__, 'flush' ], 10, 0 );
        // La chiave di OLObooking cambia: la risposta ricordata non vale più.
        foreach ( [ 'add_option_olo_license_key', 'update_option_olo_license_key', 'delete_option_olo_license_key' ] as $hook ) {
            add_action( $hook, [ __CLASS__, 'flush' ], 10, 0 );
        }
    }

    public static function basename() {
        return plugin_basename( OLOBUILD_PATH . 'olobuild.php' );
    }

    /** Dimentica le informazioni ricordate (dopo un aggiornamento o un cambio di chiave). */
    public static function flush() {
        delete_transient( self::CACHE );
    }

    /** La chiave con cui chiedere il pacchetto: quella di OLObooking, se c'è. */
    private static function license_key() {
        $key = class_exists( 'Olo_License_Manager' ) && method_exists( 'Olo_License_Manager', 'get_license_key' ) ? (string) Olo_License_Manager::get_license_key() : '';
        return (string) apply_filters( 'olobuild_update_license_key', $key );
    }

    /** L'indirizzo del sito come lo scrive il server delle licenze: https, minuscolo, senza / finale. */
    private static function site_url() {
        $url = preg_replace( '#^http://#i', 'https://', untrailingslashit( home_url() ) );
        return strtolower( (string) $url );
    }

    /**
     * Le informazioni sulla versione pubblicata, ricordate 6 ore (un'ora se olotheme.com non risponde).
     * @return array version, package, requires, requires_php, tested, last_updated, sections, license… o [ 'error' => … ]
     */
    public static function info( $fresh = false ) {
        if ( ! $fresh ) {
            $hit = get_transient( self::CACHE );
            if ( is_array( $hit ) ) {
                return $hit;
            }
        }
        $resp = wp_remote_post( (string) apply_filters( 'olobuild_update_endpoint', self::ENDPOINT ), [
            'timeout' => 10,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
            'body'    => wp_json_encode( [
                'product'     => self::SLUG,
                'version'     => OLOBUILD_VERSION,
                'license_key' => self::license_key(),
                'site_url'    => self::site_url(),
                'wp_version'  => get_bloginfo( 'version' ),
                'php_version' => PHP_VERSION,
            ] ),
        ] );
        $code = is_wp_error( $resp ) ? 0 : (int) wp_remote_retrieve_response_code( $resp );
        $data = 200 === $code ? json_decode( (string) wp_remote_retrieve_body( $resp ), true ) : null;
        if ( ! is_array( $data ) || empty( $data['version'] ) ) {
            $data = [ 'error' => is_wp_error( $resp ) ? 'unreachable' : 'http_' . $code ];
            set_transient( self::CACHE, $data, HOUR_IN_SECONDS );
            return $data;
        }
        set_transient( self::CACHE, $data, 6 * HOUR_IN_SECONDS );
        return $data;
    }

    /** Il filtro di WordPress per i plugin con Update URI su olotheme.com. */
    public static function update( $update, $plugin_data, $plugin_file, $locales ) {
        if ( $plugin_file !== self::basename() ) {
            return $update;
        }
        // «Controlla di nuovo» in Bacheca › Aggiornamenti chiede una risposta fresca.
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- sola lettura, come fa WordPress stesso
        $force = isset( $_GET['force-check'] ) && current_user_can( 'update_plugins' );
        $info  = self::info( $force );
        if ( empty( $info['version'] ) ) {
            return $update;
        }
        return [
            'slug'         => self::SLUG,
            'version'      => (string) $info['version'],
            'url'          => (string) ( $info['homepage'] ?? 'https://olotheme.com/olobuild/' ),
            'package'      => (string) ( $info['package'] ?? '' ),
            'tested'       => (string) ( $info['tested'] ?? '' ),
            'requires'     => (string) ( $info['requires'] ?? '' ),
            'requires_php' => (string) ( $info['requires_php'] ?? '' ),
        ];
    }

    /** La finestra «Visualizza dettagli» della versione nuova. */
    public static function details( $result, $action, $args ) {
        if ( 'plugin_information' !== $action || ! is_object( $args ) || empty( $args->slug ) || self::SLUG !== $args->slug ) {
            return $result;
        }
        $info = self::info();
        if ( empty( $info['version'] ) ) {
            return $result;
        }
        return (object) [
            'name'          => (string) ( $info['name'] ?? 'OLObuild' ),
            'slug'          => self::SLUG,
            'version'       => (string) $info['version'],
            'author'        => '<a href="https://olotheme.com/">OLOtheme</a>',
            'homepage'      => (string) ( $info['homepage'] ?? 'https://olotheme.com/olobuild/' ),
            'requires'      => (string) ( $info['requires'] ?? '' ),
            'requires_php'  => (string) ( $info['requires_php'] ?? '' ),
            'tested'        => (string) ( $info['tested'] ?? '' ),
            'last_updated'  => (string) ( $info['last_updated'] ?? '' ),
            'sections'      => [
                'changelog' => wp_kses_post( (string) ( $info['sections']['changelog'] ?? '' ) ),
            ],
            'download_link' => (string) ( $info['package'] ?? '' ),
        ];
    }

    /** Sotto la riga del plugin, se la versione nuova c'è ma il pacchetto no. */
    public static function row_message( $plugin_data, $response ) {
        if ( is_object( $response ) && ! empty( $response->package ) ) {
            return;
        }
        if ( class_exists( 'Olo_License_Manager' ) ) {
            echo ' ' . esc_html__( 'Per aggiornare serve una licenza OLObooking valida su questo sito: inseriscila nel gestionale di OLO Booking, in Impostazioni › Pacchetti moduli.', 'olobuild' );
            return;
        }
        echo ' ' . esc_html__( 'Per aggiornare serve una licenza OLOtheme valida su questo sito.', 'olobuild' );
    }
}
