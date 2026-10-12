<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Form Submissions Dashboard — WP_List_Table implementation.
 *
 * Manages the wp_olo_form_submissions table and provides an admin UI
 * under the Olobuild menu to view, search, mark as read, delete, and export
 * form submissions.
 */
class Olobuild_Form_Submissions {

    /** @var string DB table name (set in init) */
    private static $table = '';

    /**
     * Hook into WordPress.
     */
    public static function init() {
        global $wpdb;
        self::$table = $wpdb->prefix . 'olobuild_form_submissions';

        // Screen options (per-page) only on our page
        add_action( 'load-olobuilder_page_olo-form-submissions', [ __CLASS__, 'screen_options' ] );
        add_filter( 'set-screen-option', [ __CLASS__, 'set_screen_option' ], 10, 3 );
        // Also newer hook name (WP 5.4.2+)
        add_filter( 'set_screen_option_olo_submissions_per_page', [ __CLASS__, 'set_screen_option' ], 10, 3 );

        // Posti a numero chiuso (1.4.685): colonna dell'ammissione sui siti già installati, cache svuotata
        // quando cambia il mese (i posti del mese ripartono), shortcode per mostrarli fuori dal modulo.
        add_action( 'init', [ __CLASS__, 'maybe_upgrade' ], 5 );
        add_action( 'init', [ __CLASS__, 'nuovo_mese' ], 20 );
        add_shortcode( 'olo_posti', [ __CLASS__, 'shortcode_posti' ] );
    }

    // =========================================================================
    // Database
    // =========================================================================

    /**
     * Create / update the form_submissions table via dbDelta.
     * Called on plugin activation.
     */
    public static function create_table() {
        global $wpdb;
        $table           = $wpdb->prefix . 'olobuild_form_submissions';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            form_name varchar(255) NOT NULL DEFAULT '',
            fields_data longtext NOT NULL,
            submitted_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            ip_address varchar(45) DEFAULT '',
            user_agent text,
            read_status tinyint(1) NOT NULL DEFAULT 0,
            admitted_at datetime NULL DEFAULT NULL,
            PRIMARY KEY (id),
            KEY form_name (form_name),
            KEY submitted_at (submitted_at),
            KEY read_status (read_status),
            KEY admitted_at (admitted_at)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
        update_option( 'olobuild_form_submissions_schema', self::SCHEMA, true );
    }

    /** Versione dello schema della tabella: 2 = colonna admitted_at (posti a numero chiuso, 1.4.685). */
    const SCHEMA = '2';

    /**
     * Opzione con le pagine allegate già aperte nel builder da Invii (1.4.701): «<id invio>:<nome del file>» → id
     * della bozza di template. Così «Apri nel builder» riapre la stessa bozza invece di crearne un'altra.
     */
    const PAGINE_APERTE = 'olobuild_form_pagine_aperte';

    /**
     * Porta la tabella allo schema corrente sui siti già installati: la tabella nasce all'attivazione, ma gli
     * aggiornamenti per scambio di cartella non riattivano il plugin. Una sola lettura di opzione quando è già fatto.
     * La colonna si aggiunge con un ALTER guardato da SHOW COLUMNS (dbDelta su una tabella esistente proverebbe a
     * ridefinire la chiave primaria scritta con uno spazio solo).
     */
    public static function maybe_upgrade() {
        if ( get_option( 'olobuild_form_submissions_schema' ) === self::SCHEMA ) {
            return;
        }
        global $wpdb;
        $table = $wpdb->prefix . 'olobuild_form_submissions';
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- migrazione della tabella custom del plugin, una volta sola (guardia di versione); solo il nome tabella da $wpdb->prefix è interpolato.
        if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) !== $table ) {
            self::create_table();
            return;
        }
        if ( ! $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM $table LIKE %s", 'admitted_at' ) ) ) {
            $wpdb->query( "ALTER TABLE $table ADD COLUMN admitted_at datetime NULL DEFAULT NULL, ADD KEY admitted_at (admitted_at)" );
        }
        // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        update_option( 'olobuild_form_submissions_schema', self::SCHEMA, true );
    }

    // =========================================================================
    // Posti a numero chiuso
    // =========================================================================

    /**
     * Quanti invii di un modulo sono stati ammessi, in tutto o nel mese corrente (ora del sito).
     *
     * @param string $form_name Nome del modulo.
     * @param bool   $solo_mese Solo quelli ammessi questo mese.
     * @return int
     */
    public static function admitted_for( $form_name, $solo_mese = false ) {
        global $wpdb;
        $table = $wpdb->prefix . 'olobuild_form_submissions';
        $nome  = sanitize_text_field( $form_name );
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- tabella custom del plugin; $table da $wpdb->prefix; i valori passano da prepare; conteggio che deve essere sempre fresco.
        if ( $solo_mese ) {
            $tz     = wp_timezone();
            $inizio = ( new DateTime( 'first day of this month midnight', $tz ) )->format( 'Y-m-d H:i:s' );
            $fine   = ( new DateTime( 'first day of next month midnight', $tz ) )->format( 'Y-m-d H:i:s' );
            $n      = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table WHERE form_name = %s AND admitted_at >= %s AND admitted_at < %s", $nome, $inizio, $fine ) );
        } else {
            $n = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table WHERE form_name = %s AND admitted_at IS NOT NULL", $nome ) );
        }
        // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        return (int) $n;
    }

    /**
     * Ammette un invio (o toglie l'ammissione). La cache delle pagine si svuota: il contatore dei posti è nella
     * pagina resa.
     *
     * @param int  $id      Invio.
     * @param bool $ammesso true = ammesso adesso, false = non più ammesso.
     * @return bool
     */
    public static function set_admitted( $id, $ammesso ) {
        global $wpdb;
        $table = $wpdb->prefix . 'olobuild_form_submissions';
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- tabella custom del plugin; $table da $wpdb->prefix; i valori passano da prepare; scrittura non cacheabile.
        $ok = $ammesso
            ? $wpdb->query( $wpdb->prepare( "UPDATE $table SET admitted_at = %s, read_status = 1 WHERE id = %d", current_time( 'mysql' ), absint( $id ) ) )
            : $wpdb->query( $wpdb->prepare( "UPDATE $table SET admitted_at = NULL WHERE id = %d", absint( $id ) ) );
        // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        if ( class_exists( 'Olobuild_FullPage_Cache' ) ) {
            Olobuild_FullPage_Cache::purge_all();
        }
        return false !== $ok;
    }

    /**
     * I posti di un modulo a numero chiuso.
     *
     * @return array{totale:int,al_mese:int,ammessi:int,ammessi_mese:int,restano:int,restano_mese:int}
     */
    public static function posti( $form_name, $totale, $al_mese = 0 ) {
        $totale       = absint( $totale );
        $al_mese      = absint( $al_mese );
        $ammessi      = self::admitted_for( $form_name );
        $ammessi_mese = $al_mese > 0 ? self::admitted_for( $form_name, true ) : 0;
        $restano      = max( 0, $totale - $ammessi );
        return [
            'totale'       => $totale,
            'al_mese'      => $al_mese,
            'ammessi'      => $ammessi,
            'ammessi_mese' => $ammessi_mese,
            'restano'      => $restano,
            'restano_mese' => $al_mese > 0 ? max( 0, min( $al_mese - $ammessi_mese, $restano ) ) : $restano,
        ];
    }

    /**
     * Il testo dei posti. Vuoto = il testo automatico (con o senza i posti del mese). Segnaposto: {mese} liberi
     * questo mese, {al_mese} posti al mese, {restano} liberi in tutto, {totale} posti in tutto. A posti del mese
     * esauriti (e posti ancora liberi in tutto) vale il testo del mese esaurito.
     */
    public static function testo_posti( array $p, $testo = '', $testo_mese_esaurito = '' ) {
        if ( $p['al_mese'] > 0 && $p['restano_mese'] <= 0 && $p['restano'] > 0 ) {
            $t = trim( (string) $testo_mese_esaurito );
            return '' !== $t ? $t : olobuild_t( 'I posti di questo mese sono assegnati: le richieste che arrivano ora valgono per il mese prossimo.' );
        }
        $t = trim( (string) $testo );
        if ( '' === $t ) {
            $t = $p['al_mese'] > 0
                ? olobuild_t( 'Posti liberi questo mese: {mese} su {al_mese} · in tutto: {restano} su {totale}' )
                : olobuild_t( 'Posti liberi: {restano} su {totale}' );
        }
        return strtr( $t, [
            '{mese}'    => number_format_i18n( $p['restano_mese'] ),
            '{al_mese}' => number_format_i18n( $p['al_mese'] ),
            '{restano}' => number_format_i18n( $p['restano'] ),
            '{totale}'  => number_format_i18n( $p['totale'] ),
        ] );
    }

    /**
     * Ricorda quali moduli hanno i posti (per la pagina Invii e lo shortcode). La tile Modulo la chiama quando si
     * rende: scrive solo se qualcosa è cambiato; un modulo che spegne i posti esce dall'elenco.
     */
    public static function registra_posti( $form_name, $totale, $al_mese ) {
        $nome = sanitize_text_field( $form_name );
        if ( '' === $nome ) {
            return;
        }
        $posti = get_option( 'olobuild_form_posti', [] );
        $posti = is_array( $posti ) ? $posti : [];
        $voce  = absint( $totale ) > 0 ? [ 'totale' => absint( $totale ), 'al_mese' => absint( $al_mese ) ] : null;
        if ( ( $posti[ $nome ] ?? null ) === $voce ) {
            return;
        }
        if ( $voce ) {
            $posti[ $nome ] = $voce;
        } else {
            unset( $posti[ $nome ] );
        }
        update_option( 'olobuild_form_posti', $posti, true );
    }

    /** I moduli con i posti: [ nome => [ totale, al_mese ] ]. */
    public static function moduli_a_posti() {
        $posti = get_option( 'olobuild_form_posti', [] );
        return is_array( $posti ) ? $posti : [];
    }

    /** Cambiato il mese, i posti del mese ripartono: la cache delle pagine col contatore si svuota una volta. */
    public static function nuovo_mese() {
        if ( ! self::moduli_a_posti() ) {
            return;
        }
        $mese = wp_date( 'Y-m' );
        if ( get_option( 'olobuild_form_posti_mese' ) === $mese ) {
            return;
        }
        update_option( 'olobuild_form_posti_mese', $mese, true );
        if ( class_exists( 'Olobuild_FullPage_Cache' ) ) {
            Olobuild_FullPage_Cache::purge_all();
        }
    }

    /**
     * [olo_posti modulo="OLOfounder"] — il testo dei posti di un modulo, per mostrarlo anche fuori dal modulo
     * (un'apertura, una fascia in home). testo="…" con gli stessi segnaposto del campo «Testo dei posti».
     */
    public static function shortcode_posti( $atts ) {
        $a     = shortcode_atts( [ 'modulo' => '', 'testo' => '' ], $atts, 'olo_posti' );
        $nome  = sanitize_text_field( $a['modulo'] );
        $posti = self::moduli_a_posti();
        if ( '' === $nome || empty( $posti[ $nome ] ) ) {
            return '';
        }
        $p = self::posti( $nome, $posti[ $nome ]['totale'], $posti[ $nome ]['al_mese'] );
        return '<span class="olo-posti">' . esc_html( self::testo_posti( $p, $a['testo'] ) ) . '</span>';
    }

    /**
     * Save a form submission.
     *
     * @param string $form_name  Identifier / name of the form.
     * @param array  $fields_data Associative array of submitted field values.
     * @param string $ip          Client IP address.
     * @return int|false Inserted row ID or false on failure.
     */
    public static function save_submission( $form_name, $fields_data, $ip = '' ) {
        global $wpdb;
        $table = $wpdb->prefix . 'olobuild_form_submissions';

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- tabella custom del plugin (olo_form_submissions); nessun equivalente WP_Query; scrittura non cacheabile.
        $result = $wpdb->insert( $table, [
            'form_name'   => sanitize_text_field( $form_name ),
            'fields_data' => wp_json_encode( $fields_data ),
            'submitted_at' => current_time( 'mysql' ),
            'ip_address'  => sanitize_text_field( $ip ),
            'user_agent'  => isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 0, 500 ) : '',
            'read_status' => 0,
        ], [ '%s', '%s', '%s', '%s', '%s', '%d' ] );

        return $result ? $wpdb->insert_id : false;
    }

    /**
     * Quanti invii ha ricevuto un modulo col suo «Nome del modulo» (Numero massimo di invii).
     *
     * @param string $form_name Nome del modulo.
     * @return int
     */
    public static function count_for( $form_name ) {
        global $wpdb;
        $table = $wpdb->prefix . 'olobuild_form_submissions';
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- tabella custom del plugin; $table da $wpdb->prefix; il nome passa da prepare; conteggio che deve essere sempre fresco.
        return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table WHERE form_name = %s", sanitize_text_field( $form_name ) ) );
    }

    /**
     * Mark a submission as read.
     */
    public static function mark_read( $id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'olobuild_form_submissions';
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- tabella custom del plugin (olo_form_submissions); nessun equivalente WP_Query; scrittura non cacheabile.
        return $wpdb->update( $table, [ 'read_status' => 1 ], [ 'id' => absint( $id ) ], [ '%d' ], [ '%d' ] );
    }

    /**
     * Mark a submission as unread.
     */
    public static function mark_unread( $id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'olobuild_form_submissions';
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- tabella custom del plugin (olo_form_submissions); nessun equivalente WP_Query; scrittura non cacheabile.
        return $wpdb->update( $table, [ 'read_status' => 0 ], [ 'id' => absint( $id ) ], [ '%d' ], [ '%d' ] );
    }

    /**
     * Delete a submission.
     */
    public static function delete_submission( $id ) {
        return self::delete_submissions( [ $id ] );
    }

    /**
     * Elimina degli invii e i loro file allegati, se nessun altro invio li usa. I file restano finché c'è l'invio
     * (la pulizia giornaliera li salta, Olobuild_Form_Handler::cleanup_old_uploads()) e se ne vanno con lui.
     *
     * @param int[] $ids Id degli invii.
     * @return int Invii eliminati.
     */
    public static function delete_submissions( array $ids ) {
        global $wpdb;
        $ids = array_values( array_filter( array_map( 'absint', $ids ) ) );
        if ( ! $ids ) {
            return 0;
        }
        $table        = $wpdb->prefix . 'olobuild_form_submissions';
        $placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, PluginCheck.Security.DirectDB.UnescapedDBParameter -- tabella custom del plugin; $placeholders è una sequenza di soli '%d' e gli id (interi) passano da prepare; lettura e scrittura live.
        $righe = $wpdb->get_col( $wpdb->prepare( "SELECT fields_data FROM $table WHERE id IN ($placeholders)", ...$ids ) );
        $tolti = (int) $wpdb->query( $wpdb->prepare( "DELETE FROM $table WHERE id IN ($placeholders)", ...$ids ) );
        // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, PluginCheck.Security.DirectDB.UnescapedDBParameter
        // Le pagine aperte nel builder restano (sono bozze di template, magari già toccate): si dimentica solo da
        // quale invio venivano.
        $aperte = get_option( self::PAGINE_APERTE, [] );
        if ( is_array( $aperte ) && $aperte ) {
            $prima = count( $aperte );
            foreach ( array_keys( $aperte ) as $chiave ) {
                if ( in_array( (int) strtok( (string) $chiave, ':' ), $ids, true ) ) {
                    unset( $aperte[ $chiave ] );
                }
            }
            if ( count( $aperte ) !== $prima ) {
                update_option( self::PAGINE_APERTE, $aperte, false );
            }
        }
        if ( ! class_exists( 'Olobuild_Form_Handler' ) ) {
            return $tolti;
        }
        $file = [];
        foreach ( (array) $righe as $r ) {
            foreach ( self::file_di( $r ) as $nome ) {
                $file[ $nome ] = true;
            }
        }
        if ( $file ) {
            $in_uso = self::file_in_uso();
            foreach ( array_keys( $file ) as $nome ) {
                $path = isset( $in_uso[ $nome ] ) ? '' : Olobuild_Form_Handler::percorso_scaricabile( $nome );
                if ( '' !== $path ) {
                    wp_delete_file( $path );
                }
            }
        }
        return $tolti;
    }

    /**
     * I file allegati a cui rimandano gli invii salvati (nome del file → true).
     *
     * @return array<string,bool>
     */
    public static function file_in_uso() {
        global $wpdb;
        $table = $wpdb->prefix . 'olobuild_form_submissions';
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- tabella custom del plugin, solo il nome tabella interpolato; LIKE su un testo fisso; serve fresco.
        $righe = $wpdb->get_col( "SELECT fields_data FROM $table WHERE fields_data LIKE '%olobuild-uploads%'" );
        $nomi  = [];
        foreach ( (array) $righe as $r ) {
            foreach ( self::file_di( $r ) as $nome ) {
                $nomi[ $nome ] = true;
            }
        }
        return $nomi;
    }

    /**
     * I nomi dei file allegati che compaiono nei dati di un invio (il JSON salvato).
     *
     * @param string $json fields_data.
     * @return string[]
     */
    private static function file_di( $json ) {
        $campi = json_decode( (string) $json, true );
        $nomi  = [];
        if ( ! is_array( $campi ) || ! class_exists( 'Olobuild_Form_Handler' ) ) {
            return $nomi;
        }
        array_walk_recursive( $campi, function ( $v ) use ( &$nomi ) {
            $nome = Olobuild_Form_Handler::nome_file_caricato( $v );
            if ( '' !== $nome ) {
                $nomi[] = $nome;
            }
        } );
        return $nomi;
    }

    /**
     * I campi di un invio con i file allegati al posto giusto: il link che li scarica dalla bacheca (la cartella è
     * chiusa all'accesso diretto dalla 1.4.700).
     *
     * @param array $campi Campi dell'invio.
     * @return array
     */
    public static function campi_con_link( array $campi ) {
        return class_exists( 'Olobuild_Form_Handler' ) ? array_map( [ 'Olobuild_Form_Handler', 'link_file' ], $campi ) : $campi;
    }

    /**
     * Un valore dell'invio in HTML per la pagina di dettaglio: i link (file allegati compresi) si aprono.
     *
     * @param mixed $valore Valore (già passato da campi_con_link()).
     * @return string
     */
    private static function valore_html( $valore ) {
        if ( is_array( $valore ) ) {
            return implode( ', ', array_map( [ __CLASS__, 'valore_html' ], $valore ) );
        }
        $valore = (string) $valore;
        if ( preg_match( '#^https?://\S+$#', $valore ) ) {
            $etichetta = $valore;
            $q         = [];
            parse_str( (string) wp_parse_url( $valore, PHP_URL_QUERY ), $q );
            if ( 'olobuild_form_file' === ( $q['action'] ?? '' ) && ! empty( $q['f'] ) && class_exists( 'Olobuild_Form_Handler' ) ) {
                /* translators: %s: nome del file allegato */
                $etichetta = sprintf( __( 'Scarica %s', 'olobuild' ), Olobuild_Form_Handler::nome_originale( $q['f'] ) );
            }
            return '<a href="' . esc_url( $valore ) . '" target="_blank" rel="noopener">' . esc_html( $etichetta ) . '</a>';
        }
        return nl2br( esc_html( $valore ) );
    }

    // =========================================================================
    // CSV Export
    // =========================================================================

    /**
     * Export submissions as CSV. Called from the admin page.
     */
    public static function export_csv() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Non autorizzato.' );
        }

        check_admin_referer( 'olo_export_submissions' );

        global $wpdb;
        $table     = $wpdb->prefix . 'olobuild_form_submissions';
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- nonce verificato sopra da check_admin_referer( 'olo_export_submissions' ) (riga ~131); valore unslashed e sanitizzato.
        $form_name = sanitize_text_field( wp_unslash( $_GET['form_name'] ?? '' ) );

        // $table proviene da $wpdb->prefix (tabella custom del plugin); il valore utente $form_name passa da $wpdb->prepare con placeholder %s.
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        if ( $form_name ) {
            $rows = $wpdb->get_results(
                $wpdb->prepare( "SELECT * FROM $table WHERE form_name = %s ORDER BY submitted_at DESC", $form_name ),
                ARRAY_A
            );
        } else {
            $rows = $wpdb->get_results( "SELECT * FROM $table ORDER BY submitted_at DESC", ARRAY_A );
        }
        // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter

        if ( empty( $rows ) ) {
            wp_die( 'Nessun invio trovato.' );
        }

        // Collect all field keys across submissions
        $all_keys    = [];
        $decoded_rows = [];
        foreach ( $rows as $row ) {
            $fields = json_decode( $row['fields_data'], true );
            if ( ! is_array( $fields ) ) {
                $fields = [];
            }
            // I file allegati col link che li scarica dalla bacheca (l'indirizzo diretto non si apre più)
            $fields = self::campi_con_link( $fields );
            $decoded_rows[] = [
                'id'          => $row['id'],
                'form_name'   => $row['form_name'],
                'fields'      => $fields,
                'ip_address'  => $row['ip_address'],
                'submitted_at' => $row['submitted_at'],
                'read_status' => $row['read_status'],
                'admitted_at' => $row['admitted_at'] ?? '',
            ];
            foreach ( array_keys( $fields ) as $k ) {
                if ( ! in_array( $k, $all_keys, true ) ) {
                    $all_keys[] = $k;
                }
            }
        }

        // Send CSV headers
        $filename = 'form-submissions-' . ( $form_name ? sanitize_file_name( $form_name ) : 'all' ) . '-' . gmdate( 'Y-m-d' ) . '.csv';
        header( 'Content-Type: text/csv; charset=UTF-8' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );

        $out = fopen( 'php://output', 'w' );
        // BOM for Excel UTF-8
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- CSV streamed to php://output (download), not a filesystem file
        fwrite( $out, "\xEF\xBB\xBF" );

        // Header row
        $header = array_merge( [ 'ID', 'Form' ], array_map( 'ucfirst', $all_keys ), [ 'IP', 'Data', 'Stato', 'Ammesso il' ] );
        fputcsv( $out, array_map( 'olobuild_csv_safe', $header ), ';' );

        // Data rows
        foreach ( $decoded_rows as $dr ) {
            $row_data = [ $dr['id'], $dr['form_name'] ];
            foreach ( $all_keys as $k ) {
                $val = $dr['fields'][ $k ] ?? '';
                if ( is_array( $val ) ) {
                    $val = implode( ', ', $val );
                }
                $row_data[] = $val;
            }
            $row_data[] = $dr['ip_address'];
            $row_data[] = $dr['submitted_at'];
            $row_data[] = $dr['read_status'] ? 'Letto' : 'Non letto';
            $row_data[] = (string) $dr['admitted_at'];
            // Anti CSV-injection: prefissa i valori che iniziano con =,+,-,@ (formula Excel/Sheets).
            fputcsv( $out, array_map( 'olobuild_csv_safe', $row_data ), ';' );
        }

        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- closing the php://output CSV stream
        fclose( $out );
        exit;
    }

    // =========================================================================
    // Screen options
    // =========================================================================

    public static function screen_options() {
        add_screen_option( 'per_page', [
            'label'   => 'Invii per pagina',
            'default' => 20,
            'option'  => 'olobuild_submissions_per_page',
        ] );
    }

    public static function set_screen_option( $status, $option, $value ) {
        if ( $option === 'olobuild_submissions_per_page' ) {
            return absint( $value );
        }
        return $status;
    }

    // =========================================================================
    // Admin page
    // =========================================================================

    /**
     * Render the admin page (cockpit redesign v3.40+).
     */
    public static function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Non autorizzato.' );
        }

        // Handle CSV export (before any HTML output)
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- semplice gate booleano di esistenza (valore non usato); il nonce è verificato in export_csv() da check_admin_referer( 'olo_export_submissions' ).
        if ( ! empty( $_GET['olo_export_csv'] ) ) {
            self::export_csv();
            return;
        }

        // Boot data (preload prima riga di KPI + items per evitare skeleton flash)
        $rest = new Olobuild_Rest_Api();
        $boot_stats = $rest->submissions_stats( null );
        $stats = is_wp_error( $boot_stats ) ? [] : $boot_stats->get_data();

        $export_url = add_query_arg( [
            'page'           => 'olo-form-submissions',
            'olo_export_csv' => '1',
            '_wpnonce'       => wp_create_nonce( 'olo_export_submissions' ),
        ], admin_url( 'admin.php' ) );

        // Chip filter di stato
        $chips = [
            [ 'id' => 'all',    'label' => __( 'Tutti', 'olobuild' ),     'count' => $stats['total']  ?? 0 ],
            [ 'id' => 'unread', 'label' => __( 'Non letti', 'olobuild' ), 'count' => $stats['unread'] ?? 0, 'dot_color' => '#ef4444' ],
            [ 'id' => 'read',   'label' => __( 'Letti', 'olobuild' ),    'count' => $stats['read']   ?? 0, 'dot_color' => '#22c55e' ],
        ];
        // Moduli a posti: il filtro degli ammessi
        if ( ! empty( $stats['posti'] ) ) {
            $chips[] = [ 'id' => 'admitted', 'label' => __( 'Ammessi', 'olobuild' ), 'count' => $stats['admitted'] ?? 0, 'dot_color' => '#15803d' ];
        }
        // Aggiungi chip per i form più frequenti
        foreach ( ( $stats['forms'] ?? [] ) as $f ) {
            $chips[] = [
                'id'    => 'form:' . $f['name'],
                'label' => $f['name'],
                'count' => $f['count'],
            ];
        }

        // Sub: contatore inline
        $sub = sprintf(
            /* translators: 1: total, 2: unread, 3: last 7 days */
            __( '%1$s invii totali · %2$s da leggere · %3$s ultimi 7gg', 'olobuild' ),
            '<b>' . number_format_i18n( $stats['total']   ?? 0 ) . '</b>',
            '<b>' . number_format_i18n( $stats['unread']  ?? 0 ) . '</b>',
            '<b>' . number_format_i18n( $stats['last_7d'] ?? 0 ) . '</b>'
        );
        // Posti a numero chiuso: ammessi del mese e in tutto, aggiornati dallo script dopo ogni ammissione.
        if ( ! empty( $stats['posti'] ) ) {
            $righe = [];
            foreach ( $stats['posti'] as $p ) {
                $righe[] = $p['al_mese'] > 0
                    /* translators: 1: nome del modulo, 2: ammessi questo mese, 3: posti al mese, 4: ammessi in tutto, 5: posti in tutto */
                    ? sprintf( __( '%1$s: questo mese %2$s su %3$s · in tutto %4$s su %5$s', 'olobuild' ), $p['name'], $p['ammessi_mese'], $p['al_mese'], $p['ammessi'], $p['totale'] )
                    /* translators: 1: nome del modulo, 2: ammessi, 3: posti in tutto */
                    : sprintf( __( '%1$s: ammessi %2$s su %3$s', 'olobuild' ), $p['name'], $p['ammessi'], $p['totale'] );
            }
            $sub .= '<br><span data-olo-posti>' . esc_html( implode( ' · ', $righe ) ) . '</span>';
        }

        // Actions: bottone Esporta CSV
        $actions = Olobuild_Builder::cockpit_button( [
            'label'   => __( 'Esporta CSV', 'olobuild' ),
            'variant' => 'sec',
            'href'    => $export_url,
            'icon'    => '<path d="M12 4v12M7 9l5-5 5 5M5 20h14"/>',
        ] );

        ?>
        <?php Olobuild_Builder::cockpit_shell_open( '<b>' . esc_html__( 'Invii Form', 'olobuild' ) . '</b>' ); ?>
        <main class="olo-cockpit-main olo-submissions-page">
            <?php
            // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- internal cockpit helpers: cockpit_page_head() escapes title (esc_html), sub (wp_kses_post) and pre-escaped actions internally; cockpit_toolbar() escapes all chip/search parts internally (esc_attr/esc_html/esc_url).
            echo Olobuild_Builder::cockpit_page_head( [
                'title'   => __( 'Invii Form', 'olobuild' ),
                'sub'     => $sub,
                'actions' => $actions,
            ] );
            echo Olobuild_Builder::cockpit_toolbar( [
                'chips'              => $chips,
                'active_chip'        => 'all',
                'search'             => true,
                'search_id'          => 'olo-sub-search',
                'search_placeholder' => __( 'Cerca per nome, email, contenuto…', 'olobuild' ),
            ] );
            // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
            ?>

            <div class="olo-sub-list" data-olo-submissions>
                <div class="olo-empty-state" data-olo-loading>
                    <div class="loader-spinner"></div>
                    <div><?php esc_html_e( 'Caricamento invii…', 'olobuild' ); ?></div>
                </div>
            </div>
        </main>

        <!-- Drawer dettaglio (overlay laterale destra) -->
        <div class="olo-sub-drawer-back" data-olo-drawer hidden>
            <aside class="olo-sub-drawer">
                <header class="olo-sub-drawer-head">
                    <div>
                        <div class="olo-sub-drawer-title" data-olo-drawer-title>—</div>
                        <div class="olo-sub-drawer-meta" data-olo-drawer-meta></div>
                    </div>
                    <button type="button" class="olo-btn olo-btn-ghost olo-btn-icon" data-olo-drawer-close title="<?php esc_attr_e( 'Chiudi', 'olobuild' ); ?>">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
                    </button>
                </header>
                <div class="olo-sub-drawer-body" data-olo-drawer-body>
                    <!-- popolato via JS -->
                </div>
                <footer class="olo-sub-drawer-foot">
                    <button type="button" class="olo-btn olo-btn-pri" data-olo-drawer-admit hidden>
                        <?php esc_html_e( 'Ammetti', 'olobuild' ); ?>
                    </button>
                    <button type="button" class="olo-btn olo-btn-sec" data-olo-drawer-toggle-read>
                        <?php esc_html_e( 'Segna come non letto', 'olobuild' ); ?>
                    </button>
                    <span style="flex:1"></span>
                    <button type="button" class="olo-btn olo-btn-danger" data-olo-drawer-delete>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2M6 6l1 14a2 2 0 002 2h6a2 2 0 002-2l1-14"/></svg>
                        <?php esc_html_e( 'Elimina', 'olobuild' ); ?>
                    </button>
                </footer>
            </aside>
        </div>

        <?php Olobuild_Builder::cockpit_shell_close(); ?>
        <?php
    }

    /**
     * Render detail view for a single submission.
     */
    private static function render_detail_view( $id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'olobuild_form_submissions';
        // $table da $wpdb->prefix (tabella custom del plugin); $id passa da $wpdb->prepare con placeholder %d.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter
        $row   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $id ), ARRAY_A );

        if ( ! $row ) {
            echo '<div class="olo-admin-wrap"><div class="olo-admin-header"><div class="olo-admin-header-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg></div><div><h1>Invio non trovato</h1></div></div><p><a href="' . esc_url( admin_url( 'admin.php?page=olo-form-submissions' ) ) . '">&larr; Torna alla lista</a></p></div>';
            return;
        }

        // Auto-mark as read
        if ( ! $row['read_status'] ) {
            self::mark_read( $id );
        }

        $fields = json_decode( $row['fields_data'], true );
        if ( ! is_array( $fields ) ) {
            $fields = [];
        }
        $fields = self::campi_con_link( $fields );

        $back_url = admin_url( 'admin.php?page=olo-form-submissions' );

        ?>
        <div class="olo-admin-wrap">
            <div class="olo-admin-header">
                <div class="olo-admin-header-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/><path d="M8 10h8"/><path d="M8 14h4"/></svg>
                </div>
                <div>
                    <h1>Invio #<?php echo esc_html( $row['id'] ); ?></h1>
                    <p>Dettaglio del messaggio ricevuto</p>
                </div>
                <div style="margin-left:auto">
                    <a href="<?php echo esc_url( $back_url ); ?>" class="olo-btn-reset" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                        Torna alla lista
                    </a>
                </div>
            </div>

            <div class="olo-card">
                <div class="olo-card-head">
                    <div class="olo-card-icon black">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    </div>
                    <div>
                        <h3>Informazioni invio</h3>
                        <p>Metadati del messaggio</p>
                    </div>
                </div>
                <div class="olo-card-body" style="padding:0">
                    <table class="olo-table">
                        <tbody>
                            <tr>
                                <th style="width:160px">ID</th>
                                <td><?php echo esc_html( $row['id'] ); ?></td>
                            </tr>
                            <tr>
                                <th>Form</th>
                                <td><code><?php echo esc_html( $row['form_name'] ?: '(senza nome)' ); ?></code></td>
                            </tr>
                            <tr>
                                <th>Data invio</th>
                                <td><?php echo esc_html( $row['submitted_at'] ); ?></td>
                            </tr>
                            <tr>
                                <th>IP</th>
                                <td><code style="font-size:12px"><?php echo esc_html( $row['ip_address'] ); ?></code></td>
                            </tr>
                            <tr>
                                <th>User Agent</th>
                                <td style="word-break:break-all"><?php echo esc_html( $row['user_agent'] ); ?></td>
                            </tr>
                            <tr>
                                <th>Stato</th>
                                <td><?php echo $row['read_status'] ? '<span class="olo-badge green">Letto</span>' : '<span class="olo-badge orange">Non letto</span>'; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="olo-card">
                <div class="olo-card-head">
                    <div class="olo-card-icon orange">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </div>
                    <div>
                        <h3>Campi compilati</h3>
                        <p>Dati inseriti dall'utente nel form</p>
                    </div>
                </div>
                <div class="olo-card-body" style="padding:0">
                    <table class="olo-table">
                        <thead>
                            <tr>
                                <th style="width:200px">Campo</th>
                                <th>Valore</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $fields as $key => $value ) : ?>
                                <tr>
                                    <td><strong><?php echo esc_html( ucfirst( str_replace( [ '_', '-' ], ' ', $key ) ) ); ?></strong></td>
                                    <td><?php
                                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- valore_html() esegue esc_html/esc_url su ogni parte.
                                        echo self::valore_html( $value );
                                    ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if ( empty( $fields ) ) : ?>
                                <tr><td colspan="2"><em>Nessun campo.</em></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Process single and bulk actions.
     */
    private static function process_actions() {
        global $wpdb;
        $table = $wpdb->prefix . 'olobuild_form_submissions';

        // Single delete
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- nonce verificato sotto da check_admin_referer; valori unslashed/sanitizzati.
        if ( ! empty( $_GET['action'] ) && sanitize_key( wp_unslash( $_GET['action'] ) ) === 'delete' && ! empty( $_GET['submission_id'] ) ) {
            $submission_id = absint( wp_unslash( $_GET['submission_id'] ) );
            check_admin_referer( 'olo_delete_submission_' . $submission_id );
            self::delete_submission( $submission_id );
            echo '<div class="notice notice-success is-dismissible"><p>Invio eliminato.</p></div>';
        }

        // Single mark read
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- nonce verificato sotto da check_admin_referer; valori unslashed/sanitizzati.
        if ( ! empty( $_GET['action'] ) && sanitize_key( wp_unslash( $_GET['action'] ) ) === 'mark_read' && ! empty( $_GET['submission_id'] ) ) {
            $submission_id = absint( wp_unslash( $_GET['submission_id'] ) );
            check_admin_referer( 'olo_read_submission_' . $submission_id );
            self::mark_read( $submission_id );
            echo '<div class="notice notice-success is-dismissible"><p>Segnato come letto.</p></div>';
        }

        // Bulk actions
        if ( ! empty( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'bulk-submissions' ) ) {
            $action = isset( $_POST['action'] ) ? sanitize_key( wp_unslash( $_POST['action'] ) ) : '';
            if ( $action === '-1' ) {
                $action = isset( $_POST['action2'] ) ? sanitize_key( wp_unslash( $_POST['action2'] ) ) : '';
            }
            // wp_unslash su array prima di mappare ogni elemento con absint.
            $ids = isset( $_POST['submission_ids'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['submission_ids'] ) ) : [];

            if ( ! empty( $ids ) ) {
                // $table da $wpdb->prefix (tabella custom del plugin); $placeholders è una stringa di soli segnaposto
                // %d (uno per ogni id), gli id (interi, già passati da absint) sono forniti come argomenti a $wpdb->prepare.
                // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, PluginCheck.Security.DirectDB.UnescapedDBParameter
                if ( $action === 'bulk_delete' ) {
                    // Con gli invii se ne vanno i loro file allegati
                    self::delete_submissions( $ids );
                    echo '<div class="notice notice-success is-dismissible"><p>' . count( $ids ) . ' invii eliminati.</p></div>';
                } elseif ( $action === 'mark_read' ) {
                    $placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
                    $wpdb->query( $wpdb->prepare( "UPDATE $table SET read_status = 1 WHERE id IN ($placeholders)", ...$ids ) );
                    echo '<div class="notice notice-success is-dismissible"><p>' . count( $ids ) . ' invii segnati come letti.</p></div>';
                } elseif ( $action === 'mark_unread' ) {
                    $placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
                    $wpdb->query( $wpdb->prepare( "UPDATE $table SET read_status = 0 WHERE id IN ($placeholders)", ...$ids ) );
                    echo '<div class="notice notice-success is-dismissible"><p>' . count( $ids ) . ' invii segnati come non letti.</p></div>';
                }
                // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, PluginCheck.Security.DirectDB.UnescapedDBParameter
            }
        }
    }
}


// =============================================================================
// WP_List_Table subclass — loaded only when needed
// =============================================================================

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Olobuild_Form_Submissions_List_Table extends WP_List_Table {

    public function __construct() {
        parent::__construct( [
            'singular' => 'submission',
            'plural'   => 'submissions',
            'ajax'     => false,
        ] );
    }

    /**
     * Column definitions.
     */
    public function get_columns() {
        return [
            'cb'           => '<input type="checkbox" />',
            'id'           => 'ID',
            'form_name'    => 'Form',
            'fields_data'  => 'Dati',
            'submitted_at' => 'Data invio',
            'ip_address'   => 'IP',
            'read_status'  => 'Stato',
        ];
    }

    /**
     * Sortable columns.
     */
    public function get_sortable_columns() {
        return [
            'id'           => [ 'id', true ],  // default sort desc
            'form_name'    => [ 'form_name', false ],
            'submitted_at' => [ 'submitted_at', false ],
            'read_status'  => [ 'read_status', false ],
        ];
    }

    /**
     * Bulk actions.
     */
    public function get_bulk_actions() {
        return [
            'mark_read'   => 'Segna come letto',
            'mark_unread' => 'Segna come non letto',
            'bulk_delete'  => 'Elimina',
        ];
    }

    /**
     * Checkbox column.
     */
    public function column_cb( $item ) {
        return '<input type="checkbox" name="submission_ids[]" value="' . absint( $item['id'] ) . '" />';
    }

    /**
     * ID column — link to detail view.
     */
    public function column_id( $item ) {
        $view_url = add_query_arg( [
            'page'    => 'olo-form-submissions',
            'view_id' => $item['id'],
        ], admin_url( 'admin.php' ) );

        $delete_url = wp_nonce_url(
            add_query_arg( [
                'page'          => 'olo-form-submissions',
                'action'        => 'delete',
                'submission_id' => $item['id'],
            ], admin_url( 'admin.php' ) ),
            'olo_delete_submission_' . $item['id']
        );

        $mark_read_url = wp_nonce_url(
            add_query_arg( [
                'page'          => 'olo-form-submissions',
                'action'        => 'mark_read',
                'submission_id' => $item['id'],
            ], admin_url( 'admin.php' ) ),
            'olo_read_submission_' . $item['id']
        );

        $actions = [
            'view'   => '<a href="' . esc_url( $view_url ) . '">Visualizza</a>',
            'delete' => '<a href="' . esc_url( $delete_url ) . '" class="submitdelete" onclick="return confirm(\'Eliminare questo invio?\')">Elimina</a>',
        ];

        if ( ! $item['read_status'] ) {
            $actions['mark_read'] = '<a href="' . esc_url( $mark_read_url ) . '">Segna letto</a>';
        }

        $style = $item['read_status'] ? '' : ' style="font-weight:700"';
        return '<strong' . $style . '><a href="' . esc_url( $view_url ) . '">#' . absint( $item['id'] ) . '</a></strong>' . $this->row_actions( $actions );
    }

    /**
     * Form name column.
     */
    public function column_form_name( $item ) {
        $name = $item['form_name'] ?: '(senza nome)';
        $filter_url = add_query_arg( [
            'page'      => 'olo-form-submissions',
            'form_name' => $item['form_name'],
        ], admin_url( 'admin.php' ) );
        return '<a href="' . esc_url( $filter_url ) . '"><code>' . esc_html( $name ) . '</code></a>';
    }

    /**
     * Fields data column — show truncated preview.
     */
    public function column_fields_data( $item ) {
        $fields = json_decode( $item['fields_data'], true );
        if ( ! is_array( $fields ) || empty( $fields ) ) {
            return '<em>—</em>';
        }

        $parts = [];
        $count = 0;
        foreach ( $fields as $k => $v ) {
            if ( $count >= 3 ) {
                $remaining = count( $fields ) - 3;
                $parts[] = '<span style="color:#888">+' . $remaining . ' campi</span>';
                break;
            }
            $label = esc_html( ucfirst( str_replace( [ '_', '-' ], ' ', $k ) ) );
            $val   = is_array( $v ) ? implode( ', ', $v ) : mb_strimwidth( (string) $v, 0, 80, '...' );
            $parts[] = '<strong>' . $label . ':</strong> ' . esc_html( $val );
            $count++;
        }

        return implode( '<br>', $parts );
    }

    /**
     * Date column.
     */
    public function column_submitted_at( $item ) {
        $timestamp = strtotime( $item['submitted_at'] );
        if ( ! $timestamp ) {
            return esc_html( $item['submitted_at'] );
        }
        $date_str = wp_date( 'd/m/Y', $timestamp );
        $time_str = wp_date( 'H:i', $timestamp );
        return esc_html( $date_str ) . '<br><span style="color:#888">' . esc_html( $time_str ) . '</span>';
    }

    /**
     * IP column.
     */
    public function column_ip_address( $item ) {
        return '<code style="font-size:12px">' . esc_html( $item['ip_address'] ) . '</code>';
    }

    /**
     * Read status column.
     */
    public function column_read_status( $item ) {
        if ( $item['read_status'] ) {
            return '<span style="color:#46b450" title="Letto">&#10003; Letto</span>';
        }
        return '<strong style="color:#d63638" title="Non letto">&#9679; Nuovo</strong>';
    }

    /**
     * Default fallback column.
     */
    public function column_default( $item, $column_name ) {
        return esc_html( $item[ $column_name ] ?? '' );
    }

    /**
     * Extra table navigation — form name filter dropdown.
     */
    protected function extra_tablenav( $which ) {
        if ( $which !== 'top' ) {
            return;
        }

        global $wpdb;
        $table     = $wpdb->prefix . 'olobuild_form_submissions';
        // $table da $wpdb->prefix (tabella custom del plugin); nessun valore utente nella query, nessun equivalente WP_Query; lista a basso volume non cacheata.
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
        $form_names = $wpdb->get_col( "SELECT DISTINCT form_name FROM `$table` WHERE form_name != '' ORDER BY form_name" );

        if ( empty( $form_names ) ) {
            return;
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per filtro list-table (nessuna modifica di stato); valore unslashed e sanitizzato.
        $current = sanitize_text_field( wp_unslash( $_REQUEST['form_name'] ?? '' ) );
        ?>
        <div class="alignleft actions">
            <select name="form_name">
                <option value="">Tutti i form</option>
                <?php foreach ( $form_names as $fn ) : ?>
                    <option value="<?php echo esc_attr( $fn ); ?>" <?php selected( $current, $fn ); ?>>
                        <?php echo esc_html( $fn ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php submit_button( 'Filtra', '', 'filter_action', false ); ?>
        </div>
        <?php
    }

    /**
     * Prepare items — query with sorting, search, filter, pagination.
     */
    public function prepare_items() {
        global $wpdb;
        $table = $wpdb->prefix . 'olobuild_form_submissions';

        // Columns
        $this->_column_headers = [
            $this->get_columns(),
            [],  // hidden
            $this->get_sortable_columns(),
        ];

        // Per page
        $per_page = $this->get_items_per_page( 'olobuild_submissions_per_page', 20 );
        $page_num = $this->get_pagenum();
        $offset   = ( $page_num - 1 ) * $per_page;

        // Build WHERE clause
        $where_parts  = [];
        $where_values = [];

        // Filter by form name
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per filtro list-table (nessuna modifica di stato); valore unslashed/sanitizzato e passato da $wpdb->prepare.
        $form_name = sanitize_text_field( wp_unslash( $_REQUEST['form_name'] ?? '' ) );
        if ( $form_name !== '' ) {
            $where_parts[]  = 'form_name = %s';
            $where_values[] = $form_name;
        }

        // Search
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per ricerca list-table (nessuna modifica di stato); valore unslashed/sanitizzato e passato da $wpdb->prepare.
        $search = sanitize_text_field( wp_unslash( $_REQUEST['s'] ?? '' ) );
        if ( $search !== '' ) {
            $like = '%' . $wpdb->esc_like( $search ) . '%';
            $where_parts[]  = '(form_name LIKE %s OR fields_data LIKE %s OR ip_address LIKE %s)';
            $where_values[] = $like;
            $where_values[] = $like;
            $where_values[] = $like;
        }

        $where_sql = '';
        if ( ! empty( $where_parts ) ) {
            $where_sql = 'WHERE ' . implode( ' AND ', $where_parts );
        }

        // Sorting
        $allowed_orderby = [ 'id', 'form_name', 'submitted_at', 'read_status' ];
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per ordinamento list-table (nessuna modifica di stato); valore unslashed e validato su whitelist sotto.
        $orderby = sanitize_sql_orderby( sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ?? 'id' ) ) );
        if ( ! in_array( $orderby, $allowed_orderby, true ) ) {
            $orderby = 'id';
        }
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per ordinamento list-table (nessuna modifica di stato); valore unslashed e validato su whitelist ASC/DESC sotto.
        $order = strtoupper( sanitize_text_field( wp_unslash( $_REQUEST['order'] ?? 'DESC' ) ) );
        if ( ! in_array( $order, [ 'ASC', 'DESC' ], true ) ) {
            $order = 'DESC';
        }

        // $table da $wpdb->prefix (tabella custom del plugin); $orderby (whitelist allowed_orderby) e $order (ASC/DESC)
        // validati sopra; $where_sql contiene solo frammenti con placeholder e i valori utente passano da $wpdb->prepare.
        // Nessun equivalente WP_Query su tabella custom; lista paginata non cacheata.
        // phpcs:disable WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        // Count total
        if ( ! empty( $where_values ) ) {
            // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- query su tabella custom del plugin: nome tabella da $wpdb->prefix / colonna whitelist; tutti i valori utente passano da $wpdb->prepare
            $total = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table $where_sql", ...$where_values ) );
        } else {
            $total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table" );
        }

        // Fetch rows
        $query = "SELECT * FROM $table $where_sql ORDER BY $orderby $order LIMIT %d OFFSET %d";
        $query_values = array_merge( $where_values, [ $per_page, $offset ] );
        $this->items = $wpdb->get_results( $wpdb->prepare( $query, ...$query_values ), ARRAY_A );
        // phpcs:enable WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

        // Pagination
        $this->set_pagination_args( [
            'total_items' => $total,
            'per_page'    => $per_page,
            'total_pages' => ceil( $total / $per_page ),
        ] );
    }

    /**
     * Message when no items are found.
     */
    public function no_items() {
        echo 'Nessun invio trovato.';
    }
}
