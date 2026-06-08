<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Configurazione centralizzata della sandbox.
 * Modificare qui per cambiare durata sessione, whitelist tile, ecc.
 */
class Olo_Sandbox_Config {

    /** Nome cookie persistente del visitatore. */
    const COOKIE = 'olo_sandbox_sid';

    /** Durata cookie (giorni). */
    const COOKIE_DAYS = 30;

    /** Login utente WP "guest" condiviso da tutti i visitatori sandbox. */
    const GUEST_LOGIN = 'olo-sandbox-guest';

    /** Role custom assegnato al guest. */
    const GUEST_ROLE = 'olo_sandbox';

    /** Tabella sessioni (senza prefix). */
    const SESSIONS_TABLE = 'olo_sandbox_sessions';

    /** Option che contiene l'ID template "master" da clonare. */
    const MASTER_OPTION = 'olo_sandbox_master_id';

    /** TTL inattività sessione (secondi). Dopo questo: clone rigenerato. */
    public static function session_ttl() {
        return 12 * HOUR_IN_SECONDS;
    }

    /**
     * Whitelist tile permessi (per type esatto).
     * Solo i tile in questo elenco compaiono nella sidebar del builder.
     * I tile esistenti nel template master ma non in whitelist restano visibili
     * ma non possono essere aggiunti di nuovo (limitazione UX, non security).
     */
    public static function allowed_tiles() {
        // 28 tile esposti al guest (solo type esistenti in Olo_Tile_Manager).
        // 'separator' e 'logislider' NON sono qui: non esistono come tile registrati.
        return apply_filters( 'olo_sandbox_allowed_tiles', [
            // Layout/struttura
            'spacer', 'divider',
            // Contenuto base
            'headline', 'text-block', 'button', 'image', 'icon', 'iconbox',
            // Liste e raggruppamenti
            'list', 'iconlist', 'desclist',
            // Media
            'gallery', 'lightbox', 'imgcompare', 'video',
            // Layout avanzati
            'hero', 'accordion', 'panel', 'icontabs', 'flipcard',
            'carousel', 'marquee',
            // Animati / interattivi
            'counter', 'countercircle', 'countdown', 'hotspot',
            'alert', 'breadcrumbs',
        ] );
    }

    /**
     * Endpoint REST Olobuild bloccati per la sandbox.
     * Coprono: create/delete templates, global elements, font upload, role manager.
     */
    public static function blocked_routes() {
        // Solo le MUTAZIONI distruttive. Tutti i GET passano (anche export e
        // revisions) — la dashboard Olobuild può aver bisogno di prefetch.
        return [
            // Templates: niente create/delete/duplicate/import
            '#^/olo/v1/templates$#'                  => [ 'POST' ],
            '#^/olo/v1/templates/\d+$#'              => [ 'DELETE' ],
            '#^/olo/v1/templates/\d+/duplicate$#'    => [ 'POST' ],
            '#^/olo/v1/templates/import$#'           => [ 'POST' ],
            // Header/Footer/404: nessuna modifica
            '#^/olo/v1/header/activate$#'            => [ 'PUT', 'DELETE' ],
            '#^/olo/v1/footer/activate$#'            => [ 'PUT', 'DELETE' ],
            // Stili globali / global widgets: read-only
            '#^/olo/v1/styles$#'                     => [ 'PUT' ],
            '#^/olo/v1/global-colors$#'              => [ 'PUT' ],
            '#^/olo/v1/global-typography$#'          => [ 'PUT' ],
            '#^/olo/v1/global-widgets$#'             => [ 'POST' ],
            '#^/olo/v1/global-widgets/\d+$#'         => [ 'PUT', 'DELETE' ],
            // Font/icons custom: solo per admin
            '#^/olo/v1/fonts$#'                      => [ 'POST' ],
            '#^/olo/v1/custom-icons$#'               => [ 'POST', 'DELETE' ],
            // Role manager
            '#^/olo/v1/role-manager$#'               => [ 'POST' ],
            '#^/olo/v1/role-restrictions$#'          => [ 'POST' ],
        ];
    }

    /**
     * Capability che il guest sandbox ottiene dinamicamente (solo per request
     * verso pagine/REST di Olobuild).
     */
    public static function granted_caps() {
        return [
            'read', 'edit_pages', 'edit_others_pages', 'edit_published_pages',
            'publish_pages', 'read_private_pages', 'manage_options',
            'edit_theme_options', 'upload_files',
        ];
    }
}
