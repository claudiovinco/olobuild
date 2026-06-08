<?php
/**
 * Olo_Security_Config_Monitor — estende l'integrità dai file alla configurazione
 * e ai privilegi: confronta lo stato corrente con una baseline "buona".
 *
 * Sorveglia i vettori classici di compromissione che non lasciano traccia sui file:
 *   - siteurl / home cambiati (hijack / redirect malevolo)
 *   - users_can_register attivato + default_role privilegiato (privilege escalation)
 *   - admin_email cambiato (dirotta il recupero password)
 *   - nuovi amministratori non previsti
 *   - plugin attivati di recente
 *
 * La baseline (option olo_sec_config_baseline) è trust-on-first-use: l'admin la
 * "conferma" dopo aver verificato che lo stato attuale è legittimo.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Security_Config_Monitor {

    const OPT_BASE = 'olo_sec_config_baseline';

    /** Fotografia dello stato corrente di configurazione e privilegi. */
    public static function snapshot() {
        $admins = get_users( [ 'role' => 'administrator', 'fields' => [ 'user_login' ] ] );
        $admin_logins = array_map( function ( $u ) { return $u->user_login; }, $admins );
        sort( $admin_logins );

        $active = (array) get_option( 'active_plugins', [] );
        sort( $active );

        return [
            'siteurl'            => get_option( 'siteurl' ),
            'home'               => get_option( 'home' ),
            'admin_email'        => get_option( 'admin_email' ),
            'users_can_register' => (int) get_option( 'users_can_register' ),
            'default_role'       => get_option( 'default_role' ),
            'template'           => get_option( 'template' ),
            'stylesheet'         => get_option( 'stylesheet' ),
            'active_plugins'     => $active,
            'admins'             => $admin_logins,
        ];
    }

    public static function build_baseline() {
        update_option( self::OPT_BASE, [
            'created'  => time(),
            'snapshot' => self::snapshot(),
        ], false );
    }

    public static function get_baseline() {
        $b = get_option( self::OPT_BASE, [] );
        return is_array( $b ) ? $b : [];
    }

    /**
     * Confronta lo stato corrente con la baseline.
     *
     * @return array Finding: ['severity','label','path','detail'?]. ('path' = voce monitorata)
     */
    public static function scan() {
        $baseline = self::get_baseline();
        if ( empty( $baseline['snapshot'] ) ) {
            // Prima esecuzione: fotografa lo stato attuale come buono, niente allarme.
            self::build_baseline();
            return [];
        }

        $base = $baseline['snapshot'];
        $cur  = self::snapshot();
        $out  = [];

        if ( ( $cur['siteurl'] ?? '' ) !== ( $base['siteurl'] ?? '' ) ) {
            $out[] = self::f( 'high', __( 'URL del sito (siteurl) cambiato', 'olobuild' ), 'option: siteurl', $base['siteurl'] . ' → ' . $cur['siteurl'] );
        }
        if ( ( $cur['home'] ?? '' ) !== ( $base['home'] ?? '' ) ) {
            $out[] = self::f( 'high', __( 'URL home cambiato', 'olobuild' ), 'option: home', $base['home'] . ' → ' . $cur['home'] );
        }
        if ( ( $cur['admin_email'] ?? '' ) !== ( $base['admin_email'] ?? '' ) ) {
            $out[] = self::f( 'high', __( 'Email amministratore cambiata (possibile dirottamento recupero password)', 'olobuild' ), 'option: admin_email', $base['admin_email'] . ' → ' . $cur['admin_email'] );
        }
        if ( empty( $base['users_can_register'] ) && ! empty( $cur['users_can_register'] ) ) {
            $out[] = self::f( 'high', __( 'Registrazione utenti aperta (users_can_register attivato)', 'olobuild' ), 'option: users_can_register', '' );
        }
        if ( ( $cur['default_role'] ?? '' ) !== ( $base['default_role'] ?? '' ) ) {
            $risky = in_array( $cur['default_role'] ?? '', [ 'administrator', 'editor' ], true );
            $out[] = self::f( $risky ? 'high' : 'medium', __( 'Ruolo predefinito dei nuovi utenti cambiato', 'olobuild' ), 'option: default_role', ( $base['default_role'] ?? '?' ) . ' → ' . ( $cur['default_role'] ?? '?' ) );
        }
        if ( ( $cur['template'] ?? '' ) !== ( $base['template'] ?? '' ) || ( $cur['stylesheet'] ?? '' ) !== ( $base['stylesheet'] ?? '' ) ) {
            $out[] = self::f( 'medium', __( 'Tema attivo cambiato', 'olobuild' ), 'option: template', ( $base['stylesheet'] ?? '?' ) . ' → ' . ( $cur['stylesheet'] ?? '?' ) );
        }

        // Nuovi amministratori.
        $new_admins = array_diff( (array) ( $cur['admins'] ?? [] ), (array) ( $base['admins'] ?? [] ) );
        foreach ( $new_admins as $login ) {
            $out[] = self::f( 'high', __( 'Nuovo amministratore non presente nella baseline', 'olobuild' ), 'user: ' . $login, '' );
        }

        // Nuovi plugin attivi.
        $new_plugins = array_diff( (array) ( $cur['active_plugins'] ?? [] ), (array) ( $base['active_plugins'] ?? [] ) );
        foreach ( $new_plugins as $plugin ) {
            $out[] = self::f( 'medium', __( 'Plugin attivato dopo la baseline', 'olobuild' ), 'plugin: ' . $plugin, '' );
        }

        return $out;
    }

    private static function f( $severity, $label, $path, $detail ) {
        return [
            'severity' => $severity,
            'label'    => $label,
            'path'     => $path,
            'detail'   => $detail,
        ];
    }
}
