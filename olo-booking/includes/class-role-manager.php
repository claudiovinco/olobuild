<?php
/**
 * Olo Booking — Role Manager
 *
 * Gerarchia ruoli:
 *   administrator    → manage_options (tutto, incluso admin WP)
 *   olo_supervisor   → supervise_olo_services (tutte le strutture, no admin WP)
 *   olo_manager      → manage_olo_bookings (solo strutture assegnate)
 *
 * Permessi configurabili: salvati in option 'olo_permissions'.
 * L'amministratore ha SEMPRE tutti i permessi.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Role_Manager {

    /**
     * Registry dei permessi con defaults.
     * Chiave = id permesso, valore = [label, group, supervisor_default, manager_default]
     */
    private static $registry = [
        // ── Utenti ──
        'create_users' => [
            'label' => 'Creare nuovi utenti (gestori)',
            'group' => 'Utenti',
            'supervisor' => true,
            'manager'    => false,
        ],
        'delete_users' => [
            'label' => 'Eliminare utenti',
            'group' => 'Utenti',
            'supervisor' => false,
            'manager'    => false,
        ],
        'edit_user_profile' => [
            'label' => 'Modificare profilo utenti (nome, email, password)',
            'group' => 'Utenti',
            'supervisor' => false,
            'manager'    => false,
        ],
        'assign_services' => [
            'label' => 'Assegnare strutture ai gestori',
            'group' => 'Utenti',
            'supervisor' => true,
            'manager'    => false,
        ],
        // ── Prenotazioni ──
        'create_bookings' => [
            'label' => 'Creare prenotazioni manuali',
            'group' => 'Prenotazioni',
            'supervisor' => true,
            'manager'    => true,
        ],
        'edit_bookings' => [
            'label' => 'Modificare prenotazioni',
            'group' => 'Prenotazioni',
            'supervisor' => true,
            'manager'    => true,
        ],
        'delete_bookings' => [
            'label' => 'Eliminare prenotazioni',
            'group' => 'Prenotazioni',
            'supervisor' => true,
            'manager'    => false,
        ],
        'export_bookings' => [
            'label' => 'Esportare prenotazioni in CSV',
            'group' => 'Prenotazioni',
            'supervisor' => true,
            'manager'    => true,
        ],
        'import_bookings' => [
            'label' => 'Importare prenotazioni da CSV',
            'group' => 'Prenotazioni',
            'supervisor' => true,
            'manager'    => false,
        ],
        // ── Strutture ──
        'create_services' => [
            'label' => 'Creare nuove strutture',
            'group' => 'Strutture',
            'supervisor' => true,
            'manager'    => false,
        ],
        'edit_services' => [
            'label' => 'Modificare dettagli strutture',
            'group' => 'Strutture',
            'supervisor' => true,
            'manager'    => true,
        ],
        'manage_seasons' => [
            'label' => 'Gestire stagioni e tariffe',
            'group' => 'Strutture',
            'supervisor' => true,
            'manager'    => true,
        ],
        'manage_closures' => [
            'label' => 'Gestire chiusure',
            'group' => 'Strutture',
            'supervisor' => true,
            'manager'    => true,
        ],
        'manage_gallery' => [
            'label' => 'Gestire galleria immagini',
            'group' => 'Strutture',
            'supervisor' => true,
            'manager'    => true,
        ],
        // ── Viste ──
        'filter_service_type' => [
            'label' => 'Filtrare per tipo struttura (baite / servizi)',
            'group' => 'Viste',
            'supervisor' => true,
            'manager'    => false,
        ],
    ];

    /**
     * Create/update custom roles and capabilities.
     * Called on plugin activation.
     */
    public static function create_role() {
        // ── Gestore Struttura ──
        remove_role( 'olo_manager' );
        add_role( 'olo_manager', 'Gestore Struttura', [
            'read'                => true,
            'upload_files'        => true,
            'edit_posts'          => false,
            'delete_posts'        => false,
            'manage_olo_bookings' => true,
            'edit_olo_services'   => true,
        ] );

        // ── Supervisore Strutture ──
        remove_role( 'olo_supervisor' );
        add_role( 'olo_supervisor', 'Supervisore Strutture', [
            'read'                   => true,
            'upload_files'           => true,
            'edit_posts'             => false,
            'delete_posts'           => false,
            'manage_olo_bookings'    => true,
            'edit_olo_services'      => true,
            'supervise_olo_services' => true,
            'create_users'           => true,
        ] );

        // ── Administrator: add all custom caps ──
        $admin = get_role( 'administrator' );
        if ( $admin ) {
            $admin->add_cap( 'manage_olo_bookings' );
            $admin->add_cap( 'edit_olo_services' );
            $admin->add_cap( 'supervise_olo_services' );
            $admin->add_cap( 'manage_all_olo_bookings' );
        }
    }

    /* ─── Access Level ─── */

    public static function get_access_level( $user_id = null ) {
        if ( ! $user_id ) $user_id = get_current_user_id();
        if ( ! $user_id ) return 'none';

        if ( user_can( $user_id, 'manage_options' ) ) return 'admin';
        if ( user_can( $user_id, 'supervise_olo_services' ) ) return 'supervisor';
        if ( user_can( $user_id, 'manage_olo_bookings' ) ) return 'manager';
        return 'none';
    }

    /* ─── Configurable Permissions ─── */

    /**
     * Check if user has a specific permission.
     * Admin always returns true.
     */
    public static function can( $permission, $user_id = null ) {
        $level = self::get_access_level( $user_id );
        if ( $level === 'admin' ) return true;
        if ( $level === 'none' ) return false;

        // Check stored overrides
        $saved = get_option( 'olo_permissions', [] );
        if ( isset( $saved[ $level ][ $permission ] ) ) {
            return (bool) $saved[ $level ][ $permission ];
        }

        // Fall back to default
        if ( isset( self::$registry[ $permission ][ $level ] ) ) {
            return (bool) self::$registry[ $permission ][ $level ];
        }

        return false;
    }

    /**
     * Get the full permissions registry (for admin UI).
     */
    public static function get_registry() {
        return self::$registry;
    }

    /**
     * Get the resolved permissions for the current user (for frontend config).
     */
    public static function get_user_permissions( $user_id = null ) {
        $result = [];
        foreach ( array_keys( self::$registry ) as $perm ) {
            $result[ $perm ] = self::can( $perm, $user_id );
        }
        return $result;
    }

    /**
     * Get saved overrides.
     */
    public static function get_saved_permissions() {
        return get_option( 'olo_permissions', [] );
    }

    /**
     * Save permission overrides.
     * $data = [ 'supervisor' => [ 'perm' => bool, ... ], 'manager' => [ ... ] ]
     */
    public static function save_permissions( $data ) {
        $clean = [];
        foreach ( [ 'supervisor', 'manager' ] as $role ) {
            if ( ! isset( $data[ $role ] ) || ! is_array( $data[ $role ] ) ) continue;
            $clean[ $role ] = [];
            foreach ( array_keys( self::$registry ) as $perm ) {
                if ( isset( $data[ $role ][ $perm ] ) ) {
                    $clean[ $role ][ $perm ] = (bool) $data[ $role ][ $perm ];
                }
            }
        }
        update_option( 'olo_permissions', $clean );
    }

    /* ─── Service Access ─── */

    public static function can_manage_service( $service_id, $user_id = null ) {
        if ( ! $user_id ) $user_id = get_current_user_id();
        if ( ! $user_id ) return false;

        $level = self::get_access_level( $user_id );
        if ( $level === 'admin' || $level === 'supervisor' ) return true;

        if ( $level === 'manager' ) {
            $manager_id = get_post_meta( $service_id, '_olo_service_manager', true );
            return (int) $manager_id === (int) $user_id;
        }

        return false;
    }

    /**
     * Get service IDs accessible by user.
     * Returns null if admin or supervisor (= all services).
     * Returns array of IDs for manager.
     */
    public static function get_user_service_ids( $user_id = null ) {
        if ( ! $user_id ) $user_id = get_current_user_id();

        $level = self::get_access_level( $user_id );
        if ( $level === 'admin' || $level === 'supervisor' ) return null;

        return get_posts( [
            'post_type'      => 'olo_service',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => [
                [ 'key' => '_olo_service_manager', 'value' => $user_id ],
            ],
        ] );
    }

    public static function can_access_panel( $user_id = null ) {
        return self::get_access_level( $user_id ) !== 'none';
    }

    /**
     * @deprecated Use Olo_Role_Manager::can('filter_service_type') instead.
     */
    public static function can_filter_service_type( $user_id = null ) {
        return self::can( 'filter_service_type', $user_id );
    }
}
