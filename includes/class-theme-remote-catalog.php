<?php
/**
 * Catalog remoto dei temi: temi che non vivono dentro il plugin (perché portati
 * da plugin esterni come OLObooking, o perché in attesa di installazione del
 * plugin che li fornisce). Il file canonico è un JSON pubblico su
 * olotheme.com/olobuild-library/themes.json, aggiornato alla release dei plugin
 * che portano temi.
 *
 * Il formato di una entry è lo stesso dei theme.json locali (id, name,
 * description, author, version, preview_v, preview_mobile_v, category, zone,
 * tags, styles) + due campi:
 *   - requires_plugin : {slug, label, check, purchase_url, module?} (vedi get_themes)
 *   - catalog_url     : link alla pagina del tema sul catalog (opzionale)
 *
 * I temi remoti NON portano template: sono solo "schede" nel selettore. Chi
 * clicca «Scopri» va al catalog o alla pagina del plugin che li fornisce.
 * Quando il plugin è installato, l'hook olobuild_external_theme_dirs porta la
 * cartella del tema in locale e la dedup lascia vincere quella (importabile).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Olobuild_Theme_Remote_Catalog {

    const TRANSIENT   = 'olobuild_theme_remote_catalog';
    const CACHE_HOURS = 24;
    const DEFAULT_URL = 'https://olotheme.com/olobuild-library/themes.json';

    /**
     * Ritorna la lista dei temi dal catalog remoto. Array vuoto in caso di
     * errore (rete, DNS, 404, JSON malformato): mai una fatal, mai una card
     * con dati corrotti → il selettore cade con grazia sui temi locali.
     *
     * @param bool $force_refresh salta la cache (debug / dopo deploy).
     * @return array lista di temi (ciascuno un array associativo).
     */
    public static function fetch( $force_refresh = false ) {
        if ( ! $force_refresh ) {
            $cached = get_transient( self::TRANSIENT );
            if ( is_array( $cached ) ) return $cached;
        }

        $url = (string) apply_filters( 'olobuild_theme_remote_catalog_url', self::DEFAULT_URL );
        if ( '' === $url ) {
            set_transient( self::TRANSIENT, [], HOUR_IN_SECONDS * self::CACHE_HOURS );
            return [];
        }

        $response = wp_remote_get( $url, [
            'timeout'    => 6,
            'user-agent' => 'Olobuild/' . ( defined( 'OLOBUILD_VERSION' ) ? OLOBUILD_VERSION : '0' ),
            'headers'    => [ 'Accept' => 'application/json' ],
        ] );

        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
            // Cache negativa breve (1h): un endpoint 500 lampeggiante non martella il server.
            set_transient( self::TRANSIENT, [], HOUR_IN_SECONDS );
            return [];
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );
        if ( ! is_array( $data ) ) {
            set_transient( self::TRANSIENT, [], HOUR_IN_SECONDS );
            return [];
        }

        // Il JSON può essere o un array di temi, o un oggetto { themes: [...] }.
        $themes = isset( $data['themes'] ) && is_array( $data['themes'] ) ? $data['themes'] : $data;
        $themes = array_values( array_filter( $themes, function ( $t ) {
            return is_array( $t ) && ! empty( $t['id'] );
        } ) );

        set_transient( self::TRANSIENT, $themes, HOUR_IN_SECONDS * self::CACHE_HOURS );
        return $themes;
    }

    /** Invalida la cache (hook: dopo un aggiornamento plugin, da cron, da pulsante Impostazioni). */
    public static function flush() {
        delete_transient( self::TRANSIENT );
    }
}
