<?php
/**
 * Olobuild_Prefix_Migration — migrazione una-tantum del prefisso dati olo_ → olobuild_.
 *
 * Dalla v1.4.301 il plugin usa il prefisso olobuild_ (≥4 char) per conformità
 * wordpress.org. Le OPZIONI e le TABELLE custom esistenti nel DB usano ancora il
 * vecchio prefisso olo_: questa migrazione le rinomina UNA VOLTA, in modo idempotente,
 * PRIMA che il codice (che ora legge i nomi olobuild_) provi a leggerle.
 *
 * Scope: SOLO le opzioni/tabelle di OLObuild (allowlist esplicita + 3 famiglie dinamiche).
 * NON tocca le opzioni dei plugin fratelli (olo_book_*, olo_sec_*, olo_lang_*, ecc.),
 * né le meta-key _olo_* (prefisso condiviso nell'ecosistema), né i transient/cookie/nonce.
 *
 * Gate: option `olobuild_prefix_migrated`. Una volta migrato, il costo a regime è
 * un singolo get_option() autoloaded.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Prefix_Migration {

    const FLAG = 'olobuild_prefix_migrated';

    /** Opzioni olo_* di olobuild con nome ESATTO. */
    private static $opt_exact = [
        'olo_active_404', 'olo_active_archive', 'olo_active_footer', 'olo_active_header',
        'olo_active_preset_id', 'olo_active_search', 'olo_activecampaign_key', 'olo_activecampaign_url',
        'olo_ai_anthropic_key', 'olo_ai_image_model', 'olo_ai_model', 'olo_ai_openai_key',
        'olo_ai_usage', 'olo_allowed_form_recipients', 'olo_analytics', 'olo_analytics_group',
        'olo_breakpoints_advanced', 'olo_breakpoints_enabled', 'olo_breakpoints_v2', 'olo_brevo_key',
        'olo_builder_roles', 'olo_coming_soon_template_id', 'olo_content_only_roles', 'olo_convertkit_key',
        'olo_cookie_consent_enabled', 'olo_cookie_group', 'olo_cookie_settings', 'olo_critical_css_enabled',
        'olo_cursor_hud', 'olo_custom_code_body', 'olo_custom_code_footer', 'olo_custom_code_head',
        'olo_custom_fonts', 'olo_custom_icons', 'olo_dark_settings', 'olo_debug_bar',
        'olo_design_only_roles', 'olo_design_preset_behavior', 'olo_design_preset_snapshots', 'olo_design_presets',
        'olo_fb_pixel_id', 'olo_freesound_api_key', 'olo_ga_measurement_id', 'olo_global_colors',
        'olo_global_popups', 'olo_global_typography', 'olo_gtm_container_id', 'olo_magnetic_cursor',
        'olo_mailchimp_api_key', 'olo_maintenance_bypass_roles', 'olo_maintenance_bypass_secret', 'olo_maintenance_mode',
        'olo_maintenance_template_id', 'olo_neutral_mode', 'olo_neutral_tint', 'olo_neutrals',
        'olo_newsletter_db_version', 'olo_palette', 'olo_performance', 'olo_performance_group',
        'olo_pexels_api_key', 'olo_pixabay_api_key', 'olo_recaptcha_secret_key', 'olo_recaptcha_site_key',
        'olo_role_restrictions', 'olo_safe_mode', 'olo_seo_advanced', 'olo_seo_group',
        'olo_seo_redirects_db', 'olo_settings_group', 'olo_settings_last_saved', 'olo_setup_complete',
        'olo_styles', 'olo_template_conditions', 'olo_unsplash_api_key', 'olo_user_templates',
        'olo_welcome_dismissed', 'olo_white_label',
    ];

    /** Famiglie dinamiche (un'opzione per CPT/tassonomia/template Woo). */
    private static $opt_like = [
        'olo_active_single_',
        'olo_active_archive_',
        'olo_woo_tpl_',
    ];

    /** Tabelle custom (senza prefix). */
    private static $tables = [
        'olo_templates', 'olo_revisions', 'olo_form_submissions', 'olo_submissions',
        'olo_newsletter', 'olo_redirects', 'olo_ab_tests', 'olo_global_widgets',
    ];

    /**
     * Esegue la migrazione una sola volta. Va chiamata MOLTO presto (prima che
     * qualunque classe legga le opzioni con il nuovo prefisso).
     */
    public static function maybe_migrate() {
        if ( get_option( self::FLAG ) ) {
            return;
        }

        global $wpdb;

        // 1) Tabelle: RENAME se la vecchia esiste e la nuova no.
        foreach ( self::$tables as $old ) {
            $old_t = $wpdb->prefix . $old;
            $new_t = $wpdb->prefix . 'olobuild_' . substr( $old, 4 );
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- migrazione schema: nomi tabella derivati da $wpdb->prefix + allowlist hard-coded (nessun input utente); SHOW TABLES/RENAME non sono preparabili con placeholder.
            $exists_old = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $old_t ) );
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- vedi sopra
            $exists_new = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $new_t ) );
            if ( $exists_old && ! $exists_new ) {
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- RENAME TABLE con identificatori dall'allowlist; non parametrizzabile.
                $wpdb->query( "RENAME TABLE `{$old_t}` TO `{$new_t}`" );
            }
        }

        // 2) Opzioni con nome esatto.
        foreach ( self::$opt_exact as $old ) {
            self::rename_option( $old, 'olobuild_' . substr( $old, 4 ) );
        }

        // 3) Opzioni dinamiche (LIKE). Solo prefissi di OLObuild.
        foreach ( self::$opt_like as $prefix ) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- lettura una-tantum dei nomi opzione per la migrazione; nessuna cache applicabile.
            $rows = $wpdb->get_col( $wpdb->prepare(
                "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
                $wpdb->esc_like( $prefix ) . '%'
            ) );
            foreach ( (array) $rows as $name ) {
                self::rename_option( $name, 'olobuild_' . substr( $name, 4 ) );
            }
        }

        update_option( self::FLAG, OLOBUILD_VERSION, true );
    }

    /**
     * Rinomina una option preservando il valore e l'autoload. No-op se la vecchia
     * non esiste; se la nuova esiste già, elimina solo la vecchia (idempotenza).
     */
    private static function rename_option( $old, $new ) {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- migrazione: lettura puntuale del nome opzione, nessuna cache.
        $row = $wpdb->get_row( $wpdb->prepare(
            "SELECT option_id, autoload FROM {$wpdb->options} WHERE option_name = %s",
            $old
        ) );
        if ( ! $row ) {
            return;
        }
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- migrazione.
        $exists_new = $wpdb->get_var( $wpdb->prepare(
            "SELECT option_id FROM {$wpdb->options} WHERE option_name = %s",
            $new
        ) );
        if ( $exists_new ) {
            // La nuova esiste già (migrazione parziale precedente): elimina la vecchia.
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- migrazione una-tantum della tabella wp_options; nessuna API alternativa per rinominare option_name preservando l'autoload.
            $wpdb->delete( $wpdb->options, [ 'option_name' => $old ] );
        } else {
            // UPDATE diretto del nome: conserva valore + colonna autoload.
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- migrazione una-tantum: rinomina option_name via UPDATE diretto per conservare valore e autoload.
            $wpdb->update( $wpdb->options, [ 'option_name' => $new ], [ 'option_name' => $old ] );
        }
        wp_cache_delete( $old, 'options' );
        wp_cache_delete( $new, 'options' );
        wp_cache_delete( 'alloptions', 'options' );
    }
}
