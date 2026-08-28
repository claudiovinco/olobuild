<?php
/**
 * Olobuild_Rest_Dashboard_Trait — endpoint bacheca: KPI, recenti, changelog, prefs, submissions, link search.
 *
 * Estratto verbatim da class-rest-api.php (dieta monoliti v1.4.390):
 * stessi metodi, stessa visibilita', zero cambi alle chiamate ($this/self
 * risolvono nella classe che usa il trait).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

trait Olobuild_Rest_Dashboard_Trait {
    /**
     * KPI strip: 4 metriche aggregate, cached 5 minuti via transient.
     */
    public function dashboard_kpis( $request ) {
        // Include locale in cache key — labels are translated via __() and the
        // result is cached, so each locale needs its own cached payload.
        $cache_key = 'olo_dashboard_kpis_' . determine_locale();
        $cached = get_transient( $cache_key );
        if ( $cached !== false ) {
            return rest_ensure_response( $cached );
        }

        global $wpdb;

        // KPI aggregati su tabelle custom del plugin ({prefix}olo_templates,
        // {prefix}olo_form_submissions, {prefix}olo_404_log) + core posts: nessun
        // equivalente WP_Query per le tabelle custom. I soli token interpolati sono nomi
        // tabella (da $wpdb->prefix / $wpdb->posts); ogni valore utente passa da prepare()
        // con %s. Le SHOW TABLES verificano l'esistenza delle tabelle opzionali. Risultato
        // cacheato a monte via transient ($cache_key, 5 min) → niente cache per-query.
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter

        // Pagine pubblicate
        $pages_published = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->posts}
             WHERE post_type = 'page' AND post_status = 'publish'"
        );
        $pages_recent = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts}
             WHERE post_type = 'page' AND post_status = 'publish'
             AND post_date_gmt >= %s",
            gmdate( 'Y-m-d H:i:s', strtotime( '-7 days' ) )
        ) );

        // Template Olobuild
        $tpl_table = $wpdb->prefix . 'olobuild_templates';
        $tpl_total = 0;
        $tpl_draft = 0;
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$tpl_table'" ) === $tpl_table ) {
            $tpl_total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $tpl_table WHERE status = 'published'" );
            $tpl_draft = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $tpl_table WHERE status = 'draft'" );
        }

        // Invii form ultimi 7gg (se la tabella esiste)
        $sub_table = $wpdb->prefix . 'olobuild_form_submissions';
        $form_7d = 0;
        $form_prev = 0;
        // La colonna della data si chiama submitted_at: con created_at MySQL
        // rispondeva «Unknown column» e i due conteggi restavano a zero, quindi
        // il cruscotto diceva sempre «nessun invio» e la variazione era 0%.
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$sub_table'" ) === $sub_table ) {
            $form_7d = (int) $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM $sub_table WHERE submitted_at >= %s",
                gmdate( 'Y-m-d H:i:s', strtotime( '-7 days' ) )
            ) );
            $form_prev = (int) $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM $sub_table WHERE submitted_at >= %s AND submitted_at < %s",
                gmdate( 'Y-m-d H:i:s', strtotime( '-14 days' ) ),
                gmdate( 'Y-m-d H:i:s', strtotime( '-7 days' ) )
            ) );
        }
        $form_delta_pct = $form_prev > 0 ? round( ( ( $form_7d - $form_prev ) / $form_prev ) * 100 ) : 0;

        // Avvisi: redirect 404 + revisioni in bozza + ecc.
        $alerts_404   = 0;
        $alerts_break = 0;
        /*
         * «404 non gestiti» = indirizzi finiti nel registro per i quali NON
         * esiste un redirect.
         *
         * La query cercava una colonna `handled` che in quella tabella non c'è
         * mai stata (le colonne sono url, hits, last_hit, referer, user_agent):
         * MySQL rifiutava tutto e l'avviso restava a zero anche con 500 righe
         * nel registro. Lo stato «gestito» non è un campo, è l'esistenza di un
         * redirect che parte da quell'indirizzo.
         *
         * ⚠️ wp_olo_404_log si chiama così davvero: è fuori dalla migrazione
         * del prefisso, non è un residuo da correggere.
         */
        $tools_404 = $wpdb->prefix . 'olo_404_log';
        $redirects = $wpdb->prefix . 'olobuild_redirects';
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$tools_404'" ) === $tools_404 ) {
            $alerts_404 = ( $wpdb->get_var( "SHOW TABLES LIKE '$redirects'" ) === $redirects )
                ? (int) $wpdb->get_var(
                    "SELECT COUNT(*) FROM $tools_404 l
                     LEFT JOIN $redirects r ON r.from_url = l.url
                     WHERE r.id IS NULL"
                )
                : (int) $wpdb->get_var( "SELECT COUNT(*) FROM $tools_404" );
        }
        // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $alerts_total = $alerts_404 + $tpl_draft;

        $kpis = [
            [
                'label' => __( 'Pagine pubblicate', 'olobuild' ),
                'value' => $pages_published,
                'delta' => $pages_recent > 0
                    /* translators: %d: pages published this week */
                    ? sprintf( _n( '+%d questa settimana', '+%d questa settimana', $pages_recent, 'olobuild' ), $pages_recent )
                    : __( 'nessuna nuova', 'olobuild' ),
                'trend' => $pages_recent > 0 ? 'up' : 'flat',
                'icon'  => 'fileText',
                'href'  => admin_url( 'edit.php?post_type=page' ),
            ],
            [
                'label' => __( 'Template attivi', 'olobuild' ),
                'value' => $tpl_total,
                'delta' => $tpl_draft > 0
                    /* translators: %d: number of templates in draft */
                    ? sprintf( _n( '%d in bozza', '%d in bozza', $tpl_draft, 'olobuild' ), $tpl_draft )
                    : __( 'tutti pubblicati', 'olobuild' ),
                'trend' => 'flat',
                'icon'  => 'template',
                'href'  => admin_url( 'admin.php?page=olobuilder-templates' ),
            ],
            [
                'label' => __( 'Invii form (7gg)', 'olobuild' ),
                'value' => $form_7d,
                'delta' => $form_prev > 0
                    ? sprintf( '%s%d%% vs scorsa', $form_delta_pct >= 0 ? '+' : '', $form_delta_pct )
                    : __( 'periodo iniziale', 'olobuild' ),
                'trend' => $form_delta_pct > 0 ? 'up' : ( $form_delta_pct < 0 ? 'warn' : 'flat' ),
                'icon'  => 'form',
                'href'  => admin_url( 'admin.php?page=olo-form-submissions' ),
            ],
            [
                'label' => __( 'Avvisi da risolvere', 'olobuild' ),
                'value' => $alerts_total,
                'delta' => $alerts_total > 0
                    ? sprintf( '%d 404 · %d bozze', $alerts_404, $tpl_draft )
                    : __( 'tutto a posto', 'olobuild' ),
                'trend' => $alerts_total > 0 ? 'warn' : 'up',
                'icon'  => 'alert',
                // La pagina standalone ?page=olo-redirects non è più registrata (v1.0.31):
                // la gestione vive in Configurazione → tab "Redirect & 404".
                'href'  => $alerts_404 > 0 ? admin_url( 'admin.php?page=olobuilder-settings&tab=redirects' ) : admin_url( 'admin.php?page=olobuilder-templates' ),
            ],
        ];

        set_transient( $cache_key, $kpis, 5 * MINUTE_IN_SECONDS );
        return rest_ensure_response( $kpis );
    }

    /**
     * Recent: ultime N modifiche tra pagine + template Olobuild.
     */
    public function dashboard_recent( $request ) {
        $limit = max( 1, min( 20, (int) $request->get_param( 'limit' ) ) );
        global $wpdb;
        $items = [];

        // Ultime pagine modificate. Thumbnail in ordine: template Olobuild associato →
        // featured image della pagina → gradient fallback.
        $page_query = new WP_Query( [
            'post_type'      => [ 'page', 'post' ],
            'post_status'    => [ 'publish', 'draft' ],
            'posts_per_page' => $limit,
            'orderby'        => 'modified',
            'order'          => 'DESC',
            'no_found_rows'  => true,
        ] );
        $tpl_table = $wpdb->prefix . 'olobuild_templates';
        // Tabella custom del plugin ({prefix}olo_templates); nessun equivalente WP_Query.
        // Solo il nome tabella (da $wpdb->prefix) è interpolato; i valori utente ($tpl_id,
        // $limit) passano da prepare() con %d. Lista "recenti" volatile → non cacheata.
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $tpl_table_exists = ( $wpdb->get_var( "SHOW TABLES LIKE '$tpl_table'" ) === $tpl_table );
        // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        foreach ( $page_query->posts as $p ) {
            $thumb_url = '';
            // 1. Template Olobuild associato
            $tpl_id = (int) get_post_meta( $p->ID, '_olo_template_id', true );
            if ( $tpl_id && $tpl_table_exists ) {
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- tabella custom {prefix}olo_templates; valore via prepare(%d), solo nome tabella interpolato.
                $thumb_url = (string) $wpdb->get_var( $wpdb->prepare(
                    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- query su tabella custom del plugin: nome tabella da $wpdb->prefix / colonna whitelist; tutti i valori utente passano da $wpdb->prepare
                    "SELECT thumbnail FROM $tpl_table WHERE id = %d", $tpl_id
                ) );
            }
            // 2. Featured image
            if ( ! $thumb_url ) {
                $thumb_id = get_post_thumbnail_id( $p->ID );
                $thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'medium' ) : '';
            }
            $items[] = [
                'id'         => 'p' . $p->ID,
                'title'      => $p->post_title ?: __( '(senza titolo)', 'olobuild' ),
                'type'       => $p->post_type === 'post' ? __( 'Articolo', 'olobuild' ) : __( 'Pagina', 'olobuild' ),
                'time'       => self::human_time_ago( $p->post_modified_gmt, $p->post_modified ),
                'time_iso'   => $p->post_modified_gmt,
                'thumb'      => $thumb_url,
                'thumb_grad' => self::get_color_gradient_for( $p->ID ),
                'status'     => $p->post_status === 'publish' ? 'live' : 'draft',
                // Card "Pagina" → editor WP (scelta deliberata: le card "Template"
                // qui sotto aprono il builder; due tipologie, due destinazioni).
                'href'       => admin_url( 'post.php?post=' . $p->ID . '&action=edit' ),
            ];
        }

        // Ultimi template Olobuild
        if ( $tpl_table_exists ) {
            // tabella custom {prefix}olo_templates; $limit via prepare(%d), solo nome tabella interpolato; lista recenti volatile.
            // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
            $tpls = $wpdb->get_results( $wpdb->prepare(
                "SELECT id, title, type, status, updated_at, thumbnail
                 FROM $tpl_table
                 ORDER BY updated_at DESC
                 LIMIT %d",
                $limit
            ), ARRAY_A );
            // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
            foreach ( $tpls as $t ) {
                $type_label = ucfirst( $t['type'] ?: 'template' );
                $items[] = [
                    'id'         => 't' . $t['id'],
                    'title'      => $t['title'] ?: __( '(senza titolo)', 'olobuild' ),
                    'type'       => __( 'Template', 'olobuild' ) . ' · ' . $type_label,
                    'time'       => self::human_time_ago( $t['updated_at'] ),
                    'time_iso'   => $t['updated_at'],
                    'thumb'      => $t['thumbnail'] ?: '',
                    'thumb_grad' => self::get_color_gradient_for( (int) $t['id'] + 1000 ),
                    'status'     => $t['status'] === 'published' ? 'live' : 'draft',
                    'href'       => admin_url( 'admin.php?page=olobuilder-templates&template_id=' . $t['id'] ),
                ];
            }
        }

        // Sort by time desc + cap to limit
        usort( $items, function( $a, $b ) {
            return strcmp( $b['time_iso'], $a['time_iso'] );
        } );
        $items = array_slice( $items, 0, $limit );

        return rest_ensure_response( $items );
    }

    /**
     * "X fa" robusto: le bozze WP possono avere datetime zero ('0000-00-00 00:00:00'),
     * che strtotime interpreta come anno 0 → "2028 anni fa". Qui: timestamp invalido,
     * negativo o futuro → fallback sulla seconda data, poi su "poco fa".
     *
     * @param string $mysql_gmt    Datetime MySQL preferito (GMT).
     * @param string $fallback_gmt Datetime di riserva (es. post_modified locale).
     * @return string
     */
    public static function human_time_ago( $mysql_gmt, $fallback_gmt = '' ) {
        $ts = $mysql_gmt ? strtotime( $mysql_gmt ) : false;
        if ( ! $ts || $ts <= 0 ) {
            $ts = $fallback_gmt ? strtotime( $fallback_gmt ) : false;
        }
        if ( ! $ts || $ts <= 0 || $ts > time() + MINUTE_IN_SECONDS ) {
            return __( 'poco fa', 'olobuild' );
        }
        return human_time_diff( $ts, time() ) . ' ' . __( 'fa', 'olobuild' );
    }

    /**
     * Genera un gradiente CSS deterministico in base all'ID per fallback thumb.
     */
    private static function get_color_gradient_for( $seed ) {
        $palettes = [
            [ '#a7d7f9', '#79b8e8' ],
            [ '#fde68a', '#f59e0b' ],
            [ '#bbf7d0', '#4a8c2a' ],
            [ '#fecaca', '#ef4444' ],
            [ '#e9d5ff', '#a855f7' ],
            [ '#cffafe', '#06b6d4' ],
            [ '#fed7aa', '#f97316' ],
            [ '#bfdbfe', '#3b82f6' ],
        ];
        $p = $palettes[ abs( crc32( (string) $seed ) ) % count( $palettes ) ];
        return 'linear-gradient(135deg,' . $p[0] . ',' . $p[1] . ')';
    }

    /**
     * Changelog: ultime N versioni del plugin (recent commit / readme).
     * Per ora lettura statica da array hardcoded — TODO: leggere da CHANGELOG.md.
     */
    public function dashboard_changelog( $request ) {
        $limit = max( 1, min( 10, (int) $request->get_param( 'limit' ) ) );

        // Lettura del CHANGELOG.md se esiste
        $changelog_file = OLOBUILD_PATH . 'CHANGELOG.md';
        $entries = [];
        if ( file_exists( $changelog_file ) ) {
            $entries = self::parse_changelog_md( $changelog_file, $limit );
        }

        // Fallback: ultima versione dall'header del plugin
        if ( empty( $entries ) ) {
            $entries = [ [
                'v'     => 'v' . OLOBUILD_VERSION,
                'date'  => date_i18n( 'j M', time() ),
                'tag'   => 'novità',
                'items' => [ __( 'Vedi changelog completo nel repository.', 'olobuild' ) ],
            ] ];
        }

        return rest_ensure_response( $entries );
    }

    private static function parse_changelog_md( $file, $limit ) {
        $content = @file_get_contents( $file );
        if ( ! $content ) return [];
        $lines = explode( "\n", $content );
        $entries = [];
        $current = null;
        foreach ( $lines as $line ) {
            // ## v3.34.6 — 2026-05-09 (novità)
            if ( preg_match( '/^##\s+(v[\d.]+)(?:\s*[—\-]\s*([\d-]+))?(?:\s*\(([^)]+)\))?/', $line, $m ) ) {
                if ( $current ) $entries[] = $current;
                if ( count( $entries ) >= $limit ) break;
                $current = [
                    'v'     => $m[1],
                    'date'  => ! empty( $m[2] ) ? date_i18n( 'j M', strtotime( $m[2] ) ) : '',
                    'tag'   => ! empty( $m[3] ) ? strtolower( trim( $m[3] ) ) : 'novità',
                    'items' => [],
                ];
            } elseif ( $current && preg_match( '/^[\-\*]\s+(.+)/', $line, $m ) ) {
                $current['items'][] = trim( $m[1] );
            }
        }
        if ( $current && count( $entries ) < $limit ) $entries[] = $current;
        return $entries;
    }

    /**
     * Preferenze utente per la dashboard (pin tile, rail collapsed, app mode).
     * Persiste in user-meta.
     */
    public function dashboard_get_prefs( $request ) {
        $user_id = get_current_user_id();
        $prefs = get_user_meta( $user_id, 'olo_dashboard_prefs', true );
        if ( ! is_array( $prefs ) ) $prefs = [];
        return rest_ensure_response( wp_parse_args( $prefs, [
            'pinned'      => [ 'tpl', 'cfg', 'media' ],
            'rail'        => 'expanded',
            'app_mode'    => false,
            'banners_off' => [],
        ] ) );
    }

    public function dashboard_set_prefs( $request ) {
        $user_id = get_current_user_id();
        $body = $request->get_json_params();
        if ( ! is_array( $body ) ) $body = [];

        $existing = get_user_meta( $user_id, 'olo_dashboard_prefs', true );
        if ( ! is_array( $existing ) ) $existing = [];

        // Merge solo dei campi noti
        $allowed = [ 'pinned', 'rail', 'app_mode', 'banners_off' ];
        foreach ( $allowed as $k ) {
            if ( array_key_exists( $k, $body ) ) {
                $existing[ $k ] = $body[ $k ];
            }
        }
        update_user_meta( $user_id, 'olo_dashboard_prefs', $existing );
        return rest_ensure_response( $existing );
    }

    /* ════════════════════════════════════════════════════════════════
       FORM SUBMISSIONS — list / detail / read / delete / bulk / stats
       ════════════════════════════════════════════════════════════════ */

    private function submissions_table() {
        global $wpdb;
        return $wpdb->prefix . 'olobuild_form_submissions';
    }

    /**
     * Lista submissions con filtri (status, form_name, q) + paginazione.
     */
    public function submissions_list( $request ) {
        global $wpdb;
        $tab = $this->submissions_table();
        // Tabella custom del plugin ({prefix}olo_form_submissions); SHOW TABLES verifica
        // l'esistenza, solo il nome tabella (da $wpdb->prefix) è interpolato.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$tab'" ) !== $tab ) {
            return rest_ensure_response( [ 'items' => [], 'total' => 0, 'page' => 1, 'per_page' => 30 ] );
        }

        $status    = $request->get_param( 'status' );
        $form_name = $request->get_param( 'form_name' );
        $q         = $request->get_param( 'q' );
        $page      = max( 1, (int) $request->get_param( 'page' ) );
        $per_page  = max( 1, min( 100, (int) $request->get_param( 'per_page' ) ) );
        $offset    = ( $page - 1 ) * $per_page;

        $where = [ '1=1' ];
        $params = [];
        if ( $status === 'unread' ) $where[] = 'read_status = 0';
        elseif ( $status === 'read' ) $where[] = 'read_status = 1';
        if ( $form_name ) {
            $where[] = 'form_name = %s';
            $params[] = $form_name;
        }
        if ( $q ) {
            $where[] = '(fields_data LIKE %s OR ip_address LIKE %s OR form_name LIKE %s)';
            $like = '%' . $wpdb->esc_like( $q ) . '%';
            $params[] = $like; $params[] = $like; $params[] = $like;
        }
        $where_sql = implode( ' AND ', $where );

        // Tabella custom del plugin ({prefix}olo_form_submissions); nessun equivalente WP_Query.
        // $tab è il nome tabella (da $wpdb->prefix); $where_sql contiene solo frammenti
        // con placeholder (%s/%d) — i valori utente passano sempre da $wpdb->prepare.
        // Risultato filtrato/paginato live → non cacheabile.
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $count_sql = "SELECT COUNT(*) FROM $tab WHERE $where_sql";
        $list_sql  = "SELECT id, form_name, fields_data, submitted_at, ip_address, read_status
                      FROM $tab WHERE $where_sql
                      ORDER BY submitted_at DESC LIMIT %d OFFSET %d";

        $total = (int) $wpdb->get_var( $params ? $wpdb->prepare( $count_sql, $params ) : $count_sql );
        $list_params = array_merge( $params, [ $per_page, $offset ] );
        $rows = $wpdb->get_results( $wpdb->prepare( $list_sql, $list_params ), ARRAY_A );
        // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, PluginCheck.Security.DirectDB.UnescapedDBParameter

        $items = array_map( [ $this, 'prepare_submission_summary' ], $rows ?: [] );

        return rest_ensure_response( [
            'items'    => $items,
            'total'    => $total,
            'page'     => $page,
            'per_page' => $per_page,
        ] );
    }

    /**
     * Stats aggregate per KPI strip + chip filters dinamici.
     */
    public function submissions_stats( $request ) {
        global $wpdb;
        $tab = $this->submissions_table();
        // Tabella custom del plugin ({prefix}olo_form_submissions); nessun equivalente WP_Query.
        // Solo il nome tabella (da $wpdb->prefix) è interpolato; i valori utente (date) passano
        // da prepare() con %s. Aggregato live → non cacheabile.
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$tab'" ) !== $tab ) {
            return rest_ensure_response( [
                'total' => 0, 'unread' => 0, 'read' => 0, 'last_7d' => 0,
                'forms' => [],
            ] );
        }

        $total   = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $tab" );
        $unread  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $tab WHERE read_status = 0" );
        $last_7d = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM $tab WHERE submitted_at >= %s",
            gmdate( 'Y-m-d H:i:s', strtotime( '-7 days' ) )
        ) );
        $prev_7d = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM $tab WHERE submitted_at >= %s AND submitted_at < %s",
            gmdate( 'Y-m-d H:i:s', strtotime( '-14 days' ) ),
            gmdate( 'Y-m-d H:i:s', strtotime( '-7 days' ) )
        ) );

        // Counter per form_name (top 10)
        $forms = $wpdb->get_results(
            "SELECT form_name, COUNT(*) AS n FROM $tab
             GROUP BY form_name ORDER BY n DESC LIMIT 10",
            ARRAY_A
        );
        // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $forms = array_map( function( $r ) {
            return [
                'name'  => $r['form_name'] ?: '(senza nome)',
                'count' => (int) $r['n'],
            ];
        }, $forms ?: [] );

        return rest_ensure_response( [
            'total'   => $total,
            'unread'  => $unread,
            'read'    => $total - $unread,
            'last_7d' => $last_7d,
            'prev_7d' => $prev_7d,
            'forms'   => $forms,
        ] );
    }

    /**
     * Dettaglio singola submission. Auto-mark as read alla GET.
     */
    public function submissions_get( $request ) {
        global $wpdb;
        $tab = $this->submissions_table();
        $id = (int) $request['id'];
        // Tabella custom del plugin ({prefix}olo_form_submissions); $id via prepare(%d),
        // solo nome tabella interpolato. Lettura singola live → non cacheata; update via API.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $row = $wpdb->get_row( $wpdb->prepare(
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- query su tabella custom del plugin: nome tabella da $wpdb->prefix / colonna whitelist; tutti i valori utente passano da $wpdb->prepare
            "SELECT * FROM $tab WHERE id = %d", $id
        ), ARRAY_A );
        if ( ! $row ) {
            return new WP_Error( 'not_found', __( 'Invio non trovato', 'olobuild' ), [ 'status' => 404 ] );
        }
        // Auto-mark read
        if ( ! $row['read_status'] ) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- tabella custom {prefix}olo_form_submissions; update via API $wpdb con format array.
            $wpdb->update( $tab, [ 'read_status' => 1 ], [ 'id' => $id ], [ '%d' ], [ '%d' ] );
            $row['read_status'] = 1;
        }
        $fields = json_decode( $row['fields_data'], true );
        if ( ! is_array( $fields ) ) $fields = [];
        return rest_ensure_response( [
            'id'           => (int) $row['id'],
            'form_name'    => $row['form_name'],
            'fields'       => $fields,
            'submitted_at' => $row['submitted_at'],
            'ip_address'   => $row['ip_address'],
            'user_agent'   => $row['user_agent'],
            'read_status'  => (int) $row['read_status'],
        ] );
    }

    /**
     * Toggle read status (POST con body {read: 0|1}, default toggle).
     */
    public function submissions_toggle_read( $request ) {
        global $wpdb;
        $tab = $this->submissions_table();
        $id = (int) $request['id'];
        $body = $request->get_json_params();
        // Tabella custom del plugin ({prefix}olo_form_submissions); $id via prepare(%d),
        // solo nome tabella interpolato. Lettura singola live → non cacheata; update via API.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $row = $wpdb->get_row( $wpdb->prepare(
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- query su tabella custom del plugin: nome tabella da $wpdb->prefix / colonna whitelist; tutti i valori utente passano da $wpdb->prepare
            "SELECT read_status FROM $tab WHERE id = %d", $id
        ), ARRAY_A );
        if ( ! $row ) {
            return new WP_Error( 'not_found', __( 'Invio non trovato', 'olobuild' ), [ 'status' => 404 ] );
        }
        $new = isset( $body['read'] ) ? (int) (bool) $body['read'] : ( $row['read_status'] ? 0 : 1 );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- tabella custom {prefix}olo_form_submissions; update via API $wpdb con format array.
        $wpdb->update( $tab, [ 'read_status' => $new ], [ 'id' => $id ], [ '%d' ], [ '%d' ] );
        return rest_ensure_response( [ 'id' => $id, 'read_status' => $new ] );
    }

    public function submissions_delete( $request ) {
        global $wpdb;
        $tab = $this->submissions_table();
        $id = (int) $request['id'];
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- tabella custom {prefix}olo_form_submissions; delete via API $wpdb con format array.
        $wpdb->delete( $tab, [ 'id' => $id ], [ '%d' ] );
        return rest_ensure_response( [ 'id' => $id, 'deleted' => true ] );
    }

    /**
     * Bulk action su molti id contemporaneamente.
     * Body: { action: 'delete'|'mark_read'|'mark_unread', ids: [1,2,3] }
     */
    public function submissions_bulk( $request ) {
        global $wpdb;
        $tab = $this->submissions_table();
        $body = $request->get_json_params();
        $action = $body['action'] ?? '';
        $ids = isset( $body['ids'] ) && is_array( $body['ids'] ) ? array_map( 'absint', $body['ids'] ) : [];
        $ids = array_filter( $ids );
        if ( empty( $ids ) ) {
            return new WP_Error( 'no_ids', __( 'Nessun ID selezionato', 'olobuild' ), [ 'status' => 400 ] );
        }
        // Tabella custom del plugin ({prefix}olo_form_submissions); $ids sono int (absint+filter)
        // passati come argomenti a prepare(); $placeholders è una sequenza di soli '%d'.
        // Interpolati solo nome tabella e i segnaposto generati internamente. Scrittura → niente cache.
        $placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, PluginCheck.Security.DirectDB.UnescapedDBParameter
        if ( $action === 'delete' ) {
            $wpdb->query( $wpdb->prepare( "DELETE FROM $tab WHERE id IN ($placeholders)", $ids ) );
        } elseif ( $action === 'mark_read' ) {
            $wpdb->query( $wpdb->prepare( "UPDATE $tab SET read_status = 1 WHERE id IN ($placeholders)", $ids ) );
        } elseif ( $action === 'mark_unread' ) {
            $wpdb->query( $wpdb->prepare( "UPDATE $tab SET read_status = 0 WHERE id IN ($placeholders)", $ids ) );
        // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, PluginCheck.Security.DirectDB.UnescapedDBParameter
        } else {
            return new WP_Error( 'bad_action', __( 'Azione non valida', 'olobuild' ), [ 'status' => 400 ] );
        }
        return rest_ensure_response( [ 'action' => $action, 'count' => count( $ids ) ] );
    }

    /**
     * Prepara il summary di una submission per la lista (preview campi top).
     */
    private function prepare_submission_summary( $row ) {
        $fields = json_decode( $row['fields_data'] ?? '{}', true );
        if ( ! is_array( $fields ) ) $fields = [];

        // Estrae i campi più rappresentativi: name, email, message → preview
        $name    = '';
        $email   = '';
        $preview = '';
        foreach ( $fields as $k => $v ) {
            $kl = strtolower( $k );
            if ( ! $name && in_array( $kl, [ 'name', 'nome', 'fullname', 'full_name' ], true ) ) {
                $name = is_array( $v ) ? implode( ' ', $v ) : $v;
            } elseif ( ! $email && ( $kl === 'email' || strpos( $kl, 'mail' ) !== false ) ) {
                $email = is_array( $v ) ? reset( $v ) : $v;
            } elseif ( ! $preview && in_array( $kl, [ 'message', 'messaggio', 'note', 'comment', 'comments', 'body', 'testo', 'description' ], true ) ) {
                $preview = is_array( $v ) ? implode( ' ', $v ) : $v;
            }
        }

        // Fallback: se non c'è preview, prendi il primo campo testuale lungo > 20
        if ( ! $preview ) {
            foreach ( $fields as $v ) {
                if ( is_string( $v ) && strlen( $v ) > 20 ) { $preview = $v; break; }
            }
        }

        return [
            'id'           => (int) $row['id'],
            'form_name'    => $row['form_name'] ?: '(senza nome)',
            'name'         => mb_strimwidth( (string) $name, 0, 60, '…' ),
            'email'        => $email,
            'preview'      => mb_strimwidth( wp_strip_all_tags( (string) $preview ), 0, 140, '…' ),
            'fields_count' => count( $fields ),
            'submitted_at' => $row['submitted_at'],
            'time_diff'    => self::human_time_ago( $row['submitted_at'] ),
            'ip_address'   => $row['ip_address'],
            'read_status'  => (int) $row['read_status'],
        ];
    }

    /**
     * Cerca contenuti linkabili del sito (pagine, post, CPT pubblici, tassonomie)
     * per popolare l'autocomplete del FieldLink nel builder.
     *
     * Senza query: restituisce le ultime N pagine pubblicate (lista iniziale utile).
     * Con query: cerca per titolo/slug su tutti i post type pubblici + termini tassonomie.
     */
    public function link_search( $request ) {
        $q        = trim( (string) $request->get_param( 'q' ) );
        $per_page = max( 1, min( 30, absint( $request->get_param( 'per_page' ) ) ?: 15 ) );
        $types    = trim( (string) $request->get_param( 'types' ) );

        // Post types ammessi: tutti i pubblici esclusi attachment + tipi interni Olobuild.
        $public_types = get_post_types( [ 'public' => true ], 'objects' );
        unset( $public_types['attachment'] );
        if ( isset( $public_types['olo_template'] ) ) unset( $public_types['olo_template'] );
        if ( isset( $public_types['olo_global_widget'] ) ) unset( $public_types['olo_global_widget'] );

        // Filtro opzionale per types=page,post,product
        if ( $types ) {
            $allowed = array_filter( array_map( 'sanitize_key', explode( ',', $types ) ) );
            $public_types = array_intersect_key( $public_types, array_flip( $allowed ) );
        }

        $post_type_keys = array_keys( $public_types );
        $results = [];

        // 1. Query post/page/CPT
        if ( ! empty( $post_type_keys ) ) {
            $args = [
                'post_type'        => $post_type_keys,
                'post_status'      => 'publish',
                'posts_per_page'   => $per_page,
                'no_found_rows'    => true,
                'orderby'          => $q ? 'relevance' : 'modified',
                'order'            => 'DESC',
            ];
            if ( $q !== '' ) {
                $args['s'] = $q;
            }
            $query = new WP_Query( $args );
            foreach ( $query->posts as $p ) {
                $pt_obj    = $public_types[ $p->post_type ] ?? null;
                $type_lbl  = $pt_obj ? ( $pt_obj->labels->singular_name ?: $pt_obj->label ) : $p->post_type;
                $thumb_id  = get_post_thumbnail_id( $p->ID );
                $thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'thumbnail' ) : '';
                $excerpt   = $p->post_excerpt ?: wp_strip_all_tags( $p->post_content );
                $excerpt   = mb_strimwidth( trim( preg_replace( '/\s+/', ' ', $excerpt ) ), 0, 110, '…' );

                $permalink = get_permalink( $p );
                $results[] = [
                    'id'           => (int) $p->ID,
                    'title'        => $p->post_title ?: __( '(senza titolo)', 'olobuild' ),
                    'url'          => $permalink,
                    'url_relative' => wp_make_link_relative( $permalink ),
                    'type'         => 'post',
                    'subtype'      => $p->post_type,
                    'type_label'   => $type_lbl,
                    'sublabel'     => $type_lbl,
                    'thumbnail'    => $thumb_url ?: '',
                    'excerpt'      => $excerpt,
                ];
            }
        }

        // 2. Tassonomie pubbliche (categorie, tag, custom): solo se c'è una query.
        if ( $q !== '' && count( $results ) < $per_page ) {
            $public_taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );
            $tax_keys = array_keys( $public_taxonomies );
            if ( ! empty( $tax_keys ) ) {
                $terms = get_terms( [
                    'taxonomy'   => $tax_keys,
                    'search'     => $q,
                    'number'     => $per_page - count( $results ),
                    'hide_empty' => false,
                ] );
                if ( ! is_wp_error( $terms ) ) {
                    foreach ( $terms as $term ) {
                        $tx_obj   = $public_taxonomies[ $term->taxonomy ] ?? null;
                        $type_lbl = $tx_obj ? ( $tx_obj->labels->singular_name ?: $tx_obj->label ) : $term->taxonomy;
                        $link     = get_term_link( $term );
                        if ( is_wp_error( $link ) ) continue;

                        $results[] = [
                            'id'           => (int) $term->term_id,
                            'title'        => $term->name,
                            'url'          => $link,
                            'url_relative' => wp_make_link_relative( $link ),
                            'type'         => 'term',
                            'subtype'      => $term->taxonomy,
                            'type_label'   => $type_lbl,
                            'sublabel'     => $type_lbl . ' · ' . ( $term->count ) . ' ' . __( 'voci', 'olobuild' ),
                            'thumbnail'    => '',
                            'excerpt'      => mb_strimwidth( wp_strip_all_tags( (string) $term->description ), 0, 110, '…' ),
                        ];
                    }
                }
            }
        }

        // 3. Sempre disponibili: scorciatoie semantiche (homepage, ecc.)
        if ( $q === '' || stripos( __( 'Homepage', 'olobuild' ), $q ) !== false || stripos( 'home', $q ) !== false ) {
            array_unshift( $results, [
                'id'           => 0,
                'title'        => __( 'Homepage', 'olobuild' ),
                'url'          => home_url( '/' ),
                'url_relative' => '/',
                'type'         => 'shortcut',
                'subtype'      => 'home',
                'type_label'   => __( 'Homepage', 'olobuild' ),
                'sublabel'     => home_url( '/' ),
                'thumbnail'    => '',
                'excerpt'      => '',
            ] );
        }

        return rest_ensure_response( [
            'query'   => $q,
            'count'   => count( $results ),
            'results' => $results,
        ] );
    }
}
