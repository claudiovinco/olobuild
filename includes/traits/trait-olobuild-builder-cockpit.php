<?php
/**
 * Olobuild_Builder_Cockpit_Trait — chrome cockpit wp-admin: shell, sidebar, toolbar, card, dashboard.
 *
 * Estratto verbatim da class-olo-builder.php (dieta monoliti v1.4.390):
 * stessi metodi, stessa visibilita', zero cambi alle chiamate ($this/self
 * risolvono nella classe che usa il trait).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

trait Olobuild_Builder_Cockpit_Trait {
    /**
     * Open the shared admin page shell: top bar + sidebar + content area.
     */
    /**
     * @deprecated 3.39.0 Usare cockpit_shell_open() — questo è mantenuto per
     * backward compat con eventuali plugin/tema che lo chiamano.
     */
    public static function page_shell_open( $page_title = '', $extra_class = '' ) {
        $logo_url  = OLOBUILD_URL . 'assets/img/olobuild-logo-200-v2.png';
        $white_url = apply_filters( 'olobuild_brand_logo_url', OLOBUILD_URL . 'assets/img/olobuild-logo-200-white.png' );
        $cls = 'olo-admin-wrap' . ( $extra_class ? ' ' . esc_attr( $extra_class ) : '' );
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per evidenziare la voce di menu corrente nella sidebar admin; nessuna modifica di stato; valore sanitizzato via sanitize_key.
        $current   = sanitize_key( wp_unslash( $_GET['page'] ?? '' ) );
        ?>
        <div class="olo-shell">
            <!-- Top bar -->
            <div class="olo-shell-topbar">
                <div class="olo-shell-topbar-brand">
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=olobuild' ) ); ?>">
                        <img src="<?php echo esc_url( $white_url ); ?>" alt="<?php echo esc_attr( apply_filters( 'olobuild_brand_name', 'Olobuild' ) ); ?>" />
                    </a>
                    <span class="olo-shell-topbar-label">website builder</span>
                </div>
                <div class="olo-shell-topbar-actions">
                    <span class="olo-shell-topbar-version">v<?php echo esc_html( OLOBUILD_VERSION ); ?></span>
                </div>
            </div>

            <div class="olo-shell-body">
                <!-- Sidebar -->
                <?php self::render_sidebar( $current ); ?>

                <!-- Content -->
                <div class="olo-shell-content">
                    <?php if ( $page_title ) : ?>
                        <h1 class="olo-shell-page-title"><?php echo esc_html( $page_title ); ?></h1>
                    <?php endif; ?>
                    <div class="<?php echo esc_attr( $cls ); ?>">
        <?php
    }

    /**
     * Close the shared admin page shell.
     */
    public static function page_shell_close() {
        ?>
                    </div><!-- .olo-admin-wrap -->
                </div><!-- .olo-shell-content -->
            </div><!-- .olo-shell-body -->
        </div><!-- .olo-shell -->
        <?php
    }

    /**
     * Render the Olobuild sidebar navigation.
     */
    private static function render_sidebar( $current_page ) {
        $base = admin_url( 'admin.php?page=' );

        $menu = [
            [ 'slug' => 'olobuild',           'label' => __( 'Avvio Rapido', 'olobuild' ),  'icon' => 'rocket' ],
            [ 'slug' => 'olobuilder-settings',   'label' => __( 'Impostazioni', 'olobuild' ),  'icon' => 'gear' ],
            [ 'slug' => 'olo-tools',             'label' => __( 'Strumenti', 'olobuild' ),     'icon' => 'wrench' ],
            [ 'slug' => 'olobuilder-settings', 'label' => __( 'Permessi & Ruoli', 'olobuild' ), 'icon' => 'users', 'tab' => 'permessi' ],
            [ 'slug' => 'olo-form-submissions',  'label' => __( 'Submissions', 'olobuild' ),   'icon' => 'email' ],
            [
                'group' => __( 'Template', 'olobuild' ),
                'icon'  => 'layout',
                'items' => [
                    [ 'slug' => 'olobuilder-templates', 'label' => __( 'Template Salvati', 'olobuild' ) ],
                    [ 'slug' => 'olo-import-export',    'label' => __( 'Template Website', 'olobuild' ) ],
                    [ 'slug' => 'olobuilder-settings',  'label' => __( 'Popup globali', 'olobuild' ), 'tab' => 'popups' ],
                ],
            ],
            [
                'group' => __( 'Marketing', 'olobuild' ),
                'icon'  => 'chart',
                'items' => [
                    [ 'slug' => 'olobuilder-settings',   'label' => __( 'Analytics & Tracking', 'olobuild' ), 'tab' => 'analytics' ],
                    [ 'slug' => 'olobuilder-settings',   'label' => __( 'Cookie Consent', 'olobuild' ),       'tab' => 'cookie' ],
                    [ 'slug' => 'olobuilder-settings',   'label' => __( 'SEO', 'olobuild' ),                  'tab' => 'seo' ],
                    [ 'slug' => 'olobuilder-settings',   'label' => __( 'Redirect & 404', 'olobuild' ),       'tab' => 'redirects' ],
                ],
            ],
            [
                'group' => __( 'Personalizzazione', 'olobuild' ),
                'icon'  => 'palette',
                'items' => [
                    [ 'slug' => 'olo-media-search',      'label' => __( 'Ricerca Media', 'olobuild' ) ],
                    [ 'slug' => 'olobuilder-settings',   'label' => __( 'Performance', 'olobuild' ), 'tab' => 'performance' ],
                    [ 'slug' => 'olobuilder-settings',   'label' => __( 'WooCommerce', 'olobuild' ), 'tab' => 'wootemplates' ],
                ],
            ],
            [
                'group' => __( 'Sistema', 'olobuild' ),
                'icon'  => 'settings',
                'items' => [
                    [ 'slug' => 'olobuilder-settings', 'label' => __( 'White Label', 'olobuild' ), 'tab' => 'whitelabel' ],
                ],
            ],
        ];

        $icons = [
            'rocket'   => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 00-2.91-.09z"/><path d="M12 15l-3-3a22 22 0 012-3.95A12.88 12.88 0 0122 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 01-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/></svg>',
            'gear'     => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 01-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>',
            'wrench'   => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>',
            'users'    => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>',
            'email'    => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
            'layout'   => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>',
            'chart'    => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>',
            'palette'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.93 0 1.5-.63 1.5-1.36 0-.35-.14-.69-.38-.96-.22-.25-.34-.54-.34-.9 0-.74.6-1.28 1.34-1.28H16c3.31 0 6-2.69 6-6 0-5.52-4.48-10-10-10z"/><circle cx="7.5" cy="11.5" r="1.5" fill="currentColor"/><circle cx="10.5" cy="7.5" r="1.5" fill="currentColor"/><circle cx="14.5" cy="7.5" r="1.5" fill="currentColor"/><circle cx="17.5" cy="11.5" r="1.5" fill="currentColor"/></svg>',
            'settings' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
        ];

        // Determine which group should be open
        $group_slugs = [];
        foreach ( $menu as $item ) {
            if ( isset( $item['group'] ) ) {
                foreach ( $item['items'] as $sub ) {
                    $group_slugs[ $sub['slug'] ] = $item['group'];
                }
            }
        }
        $active_group = $group_slugs[ $current_page ] ?? '';

        ?>
        <nav class="olo-shell-sidebar">
            <div class="olo-shell-sidebar-head">
                <span class="olo-shell-sidebar-title">Editor</span>
            </div>
            <ul class="olo-shell-nav">
                <?php foreach ( $menu as $item ) : ?>
                    <?php if ( isset( $item['group'] ) ) :
                        $is_open = $active_group === $item['group'];
                        $group_id = 'olo-nav-' . sanitize_key( $item['group'] );
                    ?>
                        <li class="olo-shell-nav-group <?php echo $is_open ? 'open' : ''; ?>">
                            <button class="olo-shell-nav-group-btn" onclick="this.parentElement.classList.toggle('open')" type="button">
                                <span class="olo-shell-nav-icon"><?php echo $icons[ $item['icon'] ] ?? ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup from the hardcoded internal $icons map above. ?></span>
                                <span><?php echo esc_html( $item['group'] ); ?></span>
                                <svg class="olo-shell-nav-arrow" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                            </button>
                            <ul class="olo-shell-nav-sub">
                                <?php foreach ( $item['items'] as $sub ) :
                                    $sub_href = $base . $sub['slug'];
                                    if ( ! empty( $sub['tab'] ) ) { $sub_href .= '&tab=' . rawurlencode( $sub['tab'] ); }
                                ?>
                                    <li>
                                        <a href="<?php echo esc_url( $sub_href ); ?>"
                                           class="<?php echo $current_page === $sub['slug'] ? 'active' : ''; ?>">
                                            <?php echo esc_html( $sub['label'] ); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php else :
                        $item_href = $base . $item['slug'];
                        if ( ! empty( $item['tab'] ) ) { $item_href .= '&tab=' . rawurlencode( $item['tab'] ); }
                    ?>
                        <li>
                            <a href="<?php echo esc_url( $item_href ); ?>"
                               class="olo-shell-nav-item <?php echo $current_page === $item['slug'] ? 'active' : ''; ?>">
                                <span class="olo-shell-nav-icon"><?php echo $icons[ $item['icon'] ] ?? ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup from the hardcoded internal $icons map above. ?></span>
                                <span><?php echo esc_html( $item['label'] ); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </nav>
        <?php
    }

    /**
     * Tile "Gestione" della dashboard cockpit.
     * Lo stesso array è riutilizzato per costruire l'indice di ricerca.
     */
    public static function dashboard_manage_tiles() {
        return [
            [
                'id'    => 'tpl',
                'label' => __( 'Blocchi & Pagine', 'olobuild' ),
                'hint'  => __( 'Crea e modifica i tuoi template', 'olobuild' ),
                'icon'  => 'template',
                'color' => '#f97316',
                'href'  => admin_url( 'admin.php?page=olobuilder-templates' ),
            ],
            [
                'id'    => 'cfg',
                'label' => __( 'Configurazione', 'olobuild' ),
                'hint'  => __( 'Stili, colori, tipografia e API', 'olobuild' ),
                'icon'  => 'sliders',
                'color' => '#1f2937',
                'href'  => admin_url( 'admin.php?page=olobuilder-settings' ),
            ],
            [
                'id'    => 'media',
                'label' => __( 'Ricerca Media', 'olobuild' ),
                'hint'  => __( 'Foto, video e audio stock', 'olobuild' ),
                'icon'  => 'image',
                'color' => '#a855f7',
                'href'  => admin_url( 'admin.php?page=olo-media-search' ),
            ],
            [
                'id'    => 'form',
                'label' => __( 'Invii Form', 'olobuild' ),
                'hint'  => __( 'Visualizza i messaggi ricevuti', 'olobuild' ),
                'icon'  => 'form',
                'color' => '#10b981',
                'href'  => admin_url( 'admin.php?page=olo-form-submissions' ),
            ],
            [
                'id'    => 'an',
                'label' => __( 'Analytics', 'olobuild' ),
                'hint'  => __( 'Tracking e statistiche', 'olobuild' ),
                'icon'  => 'chart',
                'color' => '#3b82f6',
                'href'  => admin_url( 'admin.php?page=olobuilder-settings&tab=analytics' ),
            ],
            [
                'id'    => 'cc',
                'label' => __( 'Cookie Consent', 'olobuild' ),
                'hint'  => __( 'Banner GDPR e consenso', 'olobuild' ),
                'icon'  => 'cookie',
                'color' => '#f59e0b',
                'href'  => admin_url( 'admin.php?page=olobuilder-settings&tab=cookie' ),
            ],
            [
                'id'    => 'seo',
                'label' => __( 'SEO', 'olobuild' ),
                'hint'  => __( 'Meta tag, Open Graph e sitemap', 'olobuild' ),
                'icon'  => 'search',
                'color' => '#06b6d4',
                'href'  => admin_url( 'admin.php?page=olobuilder-settings&tab=seo' ),
            ],
            [
                'id'    => '404',
                'label' => __( 'Redirect & 404', 'olobuild' ),
                'hint'  => __( 'Gestisci redirect e pagine 404', 'olobuild' ),
                'icon'  => 'redirect',
                'color' => '#ef4444',
                'href'  => admin_url( 'admin.php?page=olobuilder-settings&tab=redirects' ),
            ],
            [
                'id'    => 'perf',
                'label' => __( 'Performance', 'olobuild' ),
                'hint'  => __( 'Cache, lazy load, ottimizzazione', 'olobuild' ),
                'icon'  => 'zap',
                'color' => '#eab308',
                'href'  => admin_url( 'admin.php?page=olobuilder-settings&tab=performance' ),
            ],
            [
                'id'    => 'tools',
                'label' => __( 'Strumenti', 'olobuild' ),
                'hint'  => __( 'Cache, manutenzione, URL, versioni', 'olobuild' ),
                'icon'  => 'wrench',
                'color' => '#64748b',
                'href'  => admin_url( 'admin.php?page=olo-tools' ),
            ],
            [
                'id'    => 'woo',
                'label' => __( 'WooCommerce', 'olobuild' ),
                'hint'  => __( 'Template per prodotti e shop', 'olobuild' ),
                'icon'  => 'cart',
                'color' => '#7e22ce',
                'href'  => admin_url( 'admin.php?page=olobuilder-settings&tab=wootemplates' ),
            ],
            [
                'id'    => 'pop',
                'label' => __( 'Popup Globali', 'olobuild' ),
                'hint'  => __( 'Banner e modali riusabili', 'olobuild' ),
                'icon'  => 'modal',
                'color' => '#0ea5e9',
                'href'  => admin_url( 'admin.php?page=olobuilder-settings&tab=popups' ),
            ],
        ];
    }

    /**
     * Voci "Sistema" — chip rounded raramente usate.
     */
    public static function dashboard_system_chips() {
        return [
            [ 'id' => 'wl',   'label' => __( 'White Label', 'olobuild' ),       'icon' => 'tag',     'href' => admin_url( 'admin.php?page=olobuilder-settings&tab=whitelabel' ) ],
            [ 'id' => 'imp',  'label' => __( 'Import/Export', 'olobuild' ),     'icon' => 'upload',  'href' => admin_url( 'admin.php?page=olo-import-export' ) ],
            [ 'id' => 'perm', 'label' => __( 'Permessi & Ruoli', 'olobuild' ),  'icon' => 'users',   'href' => admin_url( 'admin.php?page=olobuilder-settings&tab=permessi' ) ],
            [ 'id' => 'subs', 'label' => __( 'Submissions', 'olobuild' ),       'icon' => 'inbox',   'href' => admin_url( 'admin.php?page=olo-form-submissions' ) ],
            [ 'id' => 'log',  'label' => __( 'Diagnostica', 'olobuild' ),       'icon' => 'history', 'href' => admin_url( 'tools.php?page=olo-diagnostics' ) ],
            [ 'id' => 'lic',  'label' => __( 'Licenza', 'olobuild' ),           'icon' => 'key',     'href' => admin_url( 'admin.php?page=olobuilder-settings' ) ],
        ];
    }

    /* La palette ⌘K è globale dalla 1.4.392: vive in assets/js/olo-palette.js
       (voci menu nel localize, pagine+template via REST dashboard/palette,
       campi Configurazione dal JSON generato a build time). Il vecchio
       dashboard_search_index() è stato assorbito da quelle tre fonti. */

    /**
     * Dato per l'hero contestuale: prima la pagina più recentemente modificata,
     * altrimenti il template più recente. Include URL builder Olobuild + thumbnail.
     */
    private static function dashboard_hero_data() {
        global $wpdb;
        $tpl_table = $wpdb->prefix . 'olobuild_templates';
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Tabella custom del plugin ($wpdb->prefix . 'olobuild_templates'); nessun equivalente WP_Query. Interpolato solo il nome tabella da $wpdb->prefix (nessun valore utente); check di esistenza tabella non cacheabile.
        $tpl_table_exists = ( $wpdb->get_var( "SHOW TABLES LIKE '$tpl_table'" ) === $tpl_table );

        $pages = get_posts( [
            'post_type'      => [ 'page', 'post' ],
            'post_status'    => [ 'publish', 'draft' ],
            'posts_per_page' => 1,
            'orderby'        => 'modified',
            'order'          => 'DESC',
        ] );
        if ( ! empty( $pages ) ) {
            $p = $pages[0];
            $thumbnail = '';
            $tpl_id = (int) get_post_meta( $p->ID, '_olo_template_id', true );
            if ( $tpl_id && $tpl_table_exists ) {
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Tabella custom del plugin ($wpdb->prefix . 'olobuild_templates'); nessun equivalente WP_Query. Interpolato solo il nome tabella da $wpdb->prefix; il valore utente $tpl_id passa da $wpdb->prepare con %d; risultato (thumbnail) non cacheabile.
                $thumbnail = (string) $wpdb->get_var( $wpdb->prepare(
                    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- query su tabella custom del plugin: nome tabella da $wpdb->prefix / colonna whitelist; tutti i valori utente passano da $wpdb->prepare
                    "SELECT thumbnail FROM $tpl_table WHERE id = %d", $tpl_id
                ) );
            }
            // Fallback: featured image
            if ( ! $thumbnail ) {
                $thumb_id = get_post_thumbnail_id( $p->ID );
                if ( $thumb_id ) $thumbnail = wp_get_attachment_image_url( $thumb_id, 'large' );
            }

            $edit_url = class_exists( 'Olobuild_Page_Integration' )
                ? Olobuild_Page_Integration::get_builder_url( $p->ID )
                : admin_url( 'admin.php?page=olobuilder-templates' );

            return [
                'title'     => $p->post_title ?: __( '(senza titolo)', 'olobuild' ),
                'sub'       => sprintf(
                    /* translators: 1: human time diff, 2: status word */
                    __( 'Hai modificato questa pagina %1$s. La pagina è %2$s.', 'olobuild' ),
                    '<b>' . Olobuild_Rest_Api::human_time_ago( $p->post_modified_gmt, $p->post_modified ) . '</b>',
                    $p->post_status === 'publish' ? __( 'pubblicata', 'olobuild' ) : __( 'in bozza', 'olobuild' )
                ),
                'edit'      => $edit_url,
                'view'      => get_permalink( $p->ID ),
                'is_page'   => true,
                'thumbnail' => $thumbnail,
                'status'    => $p->post_status,
            ];
        }
        return [
            'title'     => __( 'Inizia a costruire il tuo sito', 'olobuild' ),
            'sub'       => __( 'Crea la prima pagina o sfoglia i template per partire al volo.', 'olobuild' ),
            'edit'      => admin_url( 'admin.php?page=olobuilder-templates' ),
            'view'      => home_url( '/' ),
            'is_page'   => false,
            'thumbnail' => '',
            'status'    => '',
        ];
    }

    /**
     * Apre lo "shell cockpit" condiviso (appback strip + topbar) — usato sia
     * dalla dashboard che dalle altre pagine top-level Olobuild.
     *
     * Dopo questa chiamata, l'output è dentro `.olo-cockpit-wrap`. Chiudere
     * con `cockpit_shell_close()`. Il blocco intermedio è libero (la pagina
     * gestisce il suo layout: dashboard usa `.olo-cockpit-grid` con rail,
     * la lista template usa direttamente `.olo-cockpit-main`).
     *
     * @param string $crumb_html  HTML del breadcrumb dopo "Olobuild · ".
     */
    /**
     * Le 4 aree della shell (restyling Fase 2): tutto il sistema in una riga.
     *
     * Ogni area ha la sua sub-nav con le destinazioni REALI di oggi: le rotte
     * wp-admin non cambiano, cambia dove le funzioni COMPAIONO. Le voci che
     * puntano a `olobuilder-settings&tab=X` sono le schede della Configurazione
     * traslocate qui (Fase 3): il deep-link ?tab= le apre come sempre.
     */
    public static function cockpit_areas() {
        return [
            'costruisci' => [
                'label'  => __( 'Costruisci', 'olobuild' ),
                'icon'   => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>',
                'url'    => admin_url( 'admin.php?page=olobuilder-templates' ),
                /*
                 * REGOLA delle sub-nav (feedback utente): ogni voce apre SOLO
                 * pagine dentro la shell (stessa topbar + stessa sub-nav), MAI
                 * la console o pagine WP nude — cambiare menu a metà strada fa
                 * perdere la rotta. 'tab' resta accanto a 'screen' dove esiste
                 * anche la scheda console: serve alla console per evidenziare
                 * la voce quando la scheda è aperta come ospite (via ricerca).
                 */
                'subnav' => [
                    [ 'label' => __( 'Blocchi & Pagine', 'olobuild' ), 'url' => admin_url( 'admin.php?page=olobuilder-templates' ), 'screen' => 'olobuild_page_olobuilder-templates' ],
                    [ 'label' => __( 'Popup', 'olobuild' ), 'url' => admin_url( 'admin.php?page=olo-global-popups' ), 'screen' => 'olobuild_page_olo-global-popups', 'tab' => 'popups' ],
                    [ 'label' => __( 'Importa / Esporta', 'olobuild' ), 'url' => admin_url( 'admin.php?page=olo-import-export' ), 'screen' => 'olobuild_page_olo-import-export' ],
                ],
            ],
            'media' => [
                'label'  => __( 'Media', 'olobuild' ),
                'icon'   => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-5-5L5 21"/></svg>',
                'url'    => admin_url( 'admin.php?page=olo-media-search' ),
                // Libreria WordPress (upload.php) e Chiavi provider (console)
                // RIMOSSE: portavano fuori dalla shell. Le chiavi provider
                // restano una scheda visibile della Configurazione.
                'subnav' => [
                    [ 'label' => __( 'Ricerca media', 'olobuild' ), 'url' => admin_url( 'admin.php?page=olo-media-search' ), 'screen' => 'olobuild_page_olo-media-search' ],
                ],
            ],
            'raccolta' => [
                'label'  => __( 'Raccolta', 'olobuild' ),
                'icon'   => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 6l-10 7L2 6"/><rect x="2" y="4" width="20" height="16" rx="2"/></svg>',
                'url'    => admin_url( 'admin.php?page=olo-form-submissions' ),
                'subnav' => [
                    [ 'label' => __( 'Invii form', 'olobuild' ), 'url' => admin_url( 'admin.php?page=olo-form-submissions' ), 'screen' => 'olobuild_page_olo-form-submissions' ],
                    [ 'label' => __( 'Newsletter', 'olobuild' ), 'url' => admin_url( 'admin.php?page=olo-newsletter' ), 'screen' => 'olobuild_page_olo-newsletter' ],
                    [ 'label' => __( 'Tracking & Analytics', 'olobuild' ), 'url' => admin_url( 'admin.php?page=olo-analytics' ), 'screen' => 'olobuild_page_olo-analytics', 'tab' => 'analytics' ],
                ],
            ],
            'sistema' => [
                'label'  => __( 'Sistema', 'olobuild' ),
                'icon'   => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M12 5V3M12 21v-2M5 12H3M21 12h-2M6.3 6.3 4.9 4.9M19.1 19.1l-1.4-1.4M6.3 17.7l-1.4 1.4M19.1 4.9l-1.4 1.4"/></svg>',
                'url'    => admin_url( 'admin.php?page=olobuilder-settings' ),
                // Diagnostica RIMOSSA dalla sub-nav: è una pagina di debug
                // volutamente nuda sotto Strumenti WP (deve funzionare anche
                // quando la shell è rotta) — resta raggiungibile da lì e dalla palette.
                'subnav' => [
                    [ 'label' => __( 'Configurazione', 'olobuild' ), 'url' => admin_url( 'admin.php?page=olobuilder-settings' ), 'screen' => 'olobuild_page_olobuilder-settings' ],
                    [ 'label' => __( 'Strumenti', 'olobuild' ), 'url' => admin_url( 'admin.php?page=olo-tools' ), 'screen' => 'olobuild_page_olo-tools' ],
                ],
            ],
        ];
    }

    /**
     * L'area della shell a cui appartiene la schermata corrente ('' = Dashboard
     * o pagina fuori mappa: nessuna area evidenziata, niente sub-nav).
     *
     * La Configurazione è area Sistema TRANNE per le schede traslocate
     * (popups/stockmedia/analytics), che rispondono alla loro nuova casa.
     */
    public static function cockpit_current_area() {
        $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
        $id  = $screen ? $screen->id : '';
        $tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- sola lettura per evidenziare la nav.

        if ( 'olobuild_page_olobuilder-settings' === $id ) {
            $moved = [ 'popups' => 'costruisci', 'analytics' => 'raccolta' ];
            return $moved[ $tab ] ?? 'sistema';
        }

        $map = [
            'olobuild_page_olobuilder-templates' => 'costruisci',
            'olobuild_page_olo-import-export'    => 'costruisci',
            'olobuild_page_olo-global-popups'    => 'costruisci',
            'olobuild_page_olo-woo-templates'    => 'costruisci',
            'olobuild_page_olo-media-search'     => 'media',
            'olobuild_page_olo-form-submissions' => 'raccolta',
            'olobuild_page_olo-newsletter'       => 'raccolta',
            'olobuild_page_olo-analytics'        => 'raccolta',
            'olobuild_page_olo-tools'            => 'sistema',
            'olobuild_page_olo-cookie-consent'   => 'sistema',
            'olobuild_page_olo-role-manager'     => 'sistema',
            'olobuild_page_olo-seo'              => 'sistema',
            'olobuild_page_olo-redirects'        => 'sistema',
            'olobuild_page_olo-performance'      => 'sistema',
            'olobuild_page_olo-white-label'      => 'sistema',
            'tools_page_olo-diagnostics'         => 'sistema',
            'settings_page_olo-setup'            => 'sistema',
        ];
        return $map[ $id ] ?? '';
    }

    public static function cockpit_shell_open( $crumb_html = '' ) {
        $user = wp_get_current_user();
        $initials = strtoupper( substr( $user->first_name ?: $user->display_name ?: 'U', 0, 2 ) );
        $site_host = wp_parse_url( home_url(), PHP_URL_HOST );

        $user_prefs = get_user_meta( $user->ID, 'olo_dashboard_prefs', true );
        if ( ! is_array( $user_prefs ) ) $user_prefs = [];
        $app_mode = array_key_exists( 'app_mode', $user_prefs ) ? (bool) $user_prefs['app_mode'] : false;
        ?>
        <div class="olo-cockpit-wrap">
            <?php if ( $app_mode ) : ?>
            <div class="olo-cockpit-appback">
                <a href="<?php echo esc_url( admin_url( 'index.php' ) ); ?>">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    <?php esc_html_e( 'Torna a WordPress', 'olobuild' ); ?>
                </a>
                <span style="opacity:.4">|</span>
                <span><?php echo esc_html( $site_host ); ?></span>
                <span class="spc"></span>
                <span class="pill-app"><?php esc_html_e( 'App mode', 'olobuild' ); ?></span>
            </div>
            <?php endif; ?>

            <?php
            /*
             * Restyling Fase 2: al posto del breadcrumb, le 4 aree del sistema.
             * $crumb_html resta in firma per i caller esistenti ma non si stampa
             * più: la sub-nav dell'area dice già dove sei.
             */
            $olo_areas        = self::cockpit_areas();
            $olo_current_area = self::cockpit_current_area();
            $olo_screen_obj   = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
            $olo_screen_id    = $olo_screen_obj ? $olo_screen_obj->id : '';
            $olo_current_tab  = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- sola lettura per evidenziare la nav.
            ?>
            <div class="olo-cockpit-topbar">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=olobuild' ) ); ?>" style="display:inline-flex;align-items:center;text-decoration:none;">
                    <img class="logo" src="<?php echo esc_url( OLOBUILD_URL . 'assets/img/olobuild-horizontal.png' ); ?>" alt="Olobuild" />
                </a>
                <span class="ver">v<?php echo esc_html( OLOBUILD_VERSION ); ?></span>
                <nav class="areas" aria-label="<?php esc_attr_e( 'Aree di Olobuild', 'olobuild' ); ?>">
                    <?php foreach ( $olo_areas as $area_id => $area ) : ?>
                    <a class="olo-area-tab<?php echo $area_id === $olo_current_area ? ' is-active' : ''; ?>"
                       href="<?php echo esc_url( $area['url'] ); ?>"
                       <?php echo $area_id === $olo_current_area ? 'aria-current="true"' : ''; ?>>
                        <?php echo $area['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG from the hardcoded cockpit_areas() map. ?>
                        <span><?php echo esc_html( $area['label'] ); ?></span>
                    </a>
                    <?php endforeach; ?>
                </nav>
                <span class="spc"></span>
                <?php
                /*
                 * ⚠️ IL COMANDO STA QUI, E NON PIU' SUL NUMERO DI VERSIONE.
                 * Prima l'interruttore della modalita' app era il pulsante che
                 * scrive `v1.4.379`: un comando che nessuno cerca li', e che
                 * per giunta funzionava solo nella dashboard, perche' la sua
                 * JS si carica soltanto la'. Adesso e' un pulsante che dice
                 * cosa fa, e lo dice anche a un lettore di schermo
                 * (`aria-pressed`).
                 */
                ?>
                <button type="button" class="ico-btn" data-olo-app-mode-toggle
                        aria-pressed="<?php echo $app_mode ? 'true' : 'false'; ?>"
                        title="<?php esc_attr_e( 'Stringi il menu di WordPress', 'olobuild' ); ?>"
                        aria-label="<?php esc_attr_e( 'Stringi il menu di WordPress', 'olobuild' ); ?>">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M9 4v16"/></svg>
                </button>
                <div class="search-mini" data-olo-palette-trigger>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" style="color:var(--olo-text-muted)"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="text" placeholder="<?php esc_attr_e( 'Cerca pagine, template, impostazioni…', 'olobuild' ); ?>" readonly/>
                    <kbd>Ctrl K</kbd>
                </div>
                <a class="ico-btn" href="<?php echo esc_url( admin_url( 'admin.php?page=olo-form-submissions' ) ); ?>" title="<?php esc_attr_e( 'Notifiche', 'olobuild' ); ?>">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 16V11a6 6 0 0112 0v5l2 2H4l2-2zM10 20a2 2 0 004 0"/></svg>
                </a>
                <a class="ico-btn" href="https://olotheme.com/docs" target="_blank" rel="noopener" title="<?php esc_attr_e( 'Documentazione', 'olobuild' ); ?>">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 015 0c0 1.5-2.5 2-2.5 4M12 17h.01"/></svg>
                </a>
                <a class="ico-btn" href="<?php echo esc_url( get_edit_user_link() ); ?>" title="<?php esc_attr_e( 'Profilo', 'olobuild' ); ?>">
                    <span class="av"><?php echo esc_html( $initials ); ?></span>
                </a>
            </div>
            <?php if ( $olo_current_area && ! empty( $olo_areas[ $olo_current_area ]['subnav'] ) ) : ?>
            <nav class="olo-cockpit-subnav" aria-label="<?php esc_attr_e( 'Sezioni dell\'area', 'olobuild' ); ?>">
                <?php foreach ( $olo_areas[ $olo_current_area ]['subnav'] as $it ) :
                    $it_active = ( isset( $it['screen'] ) && $it['screen'] === $olo_screen_id && ( 'olobuild_page_olobuilder-settings' !== $olo_screen_id || '' === $olo_current_tab || ! in_array( $olo_current_tab, [ 'popups', 'analytics' ], true ) ) )
                        || ( isset( $it['tab'] ) && 'olobuild_page_olobuilder-settings' === $olo_screen_id && $it['tab'] === $olo_current_tab );
                    ?>
                <a href="<?php echo esc_url( $it['url'] ); ?>" class="<?php echo $it_active ? 'is-active' : ''; ?>" <?php echo $it_active ? 'aria-current="page"' : ''; ?>><?php echo esc_html( $it['label'] ); ?></a>
                <?php endforeach; ?>
            </nav>
            <?php endif; ?>
        <?php
        self::stampa_interruttore_app_mode();
    }

    /**
     * L'INTERRUTTORE DELLA MODALITA' APP, che vive dove vive la barra.
     *
     * ⚠️ NON STA IN `dashboard.js`, e prima ci stava. Quel file si accoda
     * soltanto su `toplevel_page_olobuild`, quindi su ogni altra pagina di
     * Olobuild il pulsante c'era, aveva il suo suggerimento, e non faceva
     * niente: un comando morto e' peggio di un comando assente, perche' chi
     * lo preme conclude che il prodotto non funziona.
     *
     * Qui invece viene stampato insieme alla barra: dove c'e' la barra c'e'
     * il comando, e non ci sono due elenchi da tenere allineati.
     */
    private static function stampa_interruttore_app_mode() {
        $rotta  = esc_url_raw( rest_url( 'olobuild/v1/dashboard/prefs' ) );
        $chiave = wp_create_nonce( 'wp_rest' );
        ?>
        <script>
        ( function () {
            var b = document.querySelector( '[data-olo-app-mode-toggle]' );
            if ( ! b ) { return; }
            b.addEventListener( 'click', function () {
                var acceso = ! document.body.classList.contains( 'olobuild-app-mode' );
                document.body.classList.toggle( 'olobuild-app-mode', acceso );
                b.setAttribute( 'aria-pressed', acceso ? 'true' : 'false' );

                /* La striscia «Torna a WordPress» esiste solo in modalita' app. */
                var striscia = document.querySelector( '.olo-cockpit-appback' );
                if ( striscia ) { striscia.style.display = acceso ? '' : 'none'; }

                fetch( <?php echo wp_json_encode( $rotta ); ?>, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': <?php echo wp_json_encode( $chiave ); ?> },
                    body: JSON.stringify( { app_mode: acceso } )
                } ).catch( function () {} );
            } );
        } )();
        </script>
        <?php
    }

    /**
     * Chiude lo shell cockpit (chiusura `.olo-cockpit-wrap`).
     */
    public static function cockpit_shell_close() {
        ?>
        </div><!-- .olo-cockpit-wrap -->
        <?php
    }

    /* ════════════════════════════════════════════════════════════════
       TOOLKIT — componenti shared per pagine cockpit (Fase 2)
       Helper PHP che emettono il markup standard dei pattern comuni.
       Vanno usati DENTRO `<main class="olo-cockpit-main">`.
       Vedono CSS classes in dashboard.css sezione TOOLKIT.
       ════════════════════════════════════════════════════════════════ */

    /**
     * Page head: titolo H1 + sottotitolo meta + bottoni a destra.
     *
     * @param array $args {
     *   @type string $title    Titolo H1 (richiesto).
     *   @type string $sub      HTML del sottotitolo (b/a/spans permessi).
     *   @type string $actions  HTML dei bottoni a destra (usa cockpit_button()).
     * }
     *
     * @example
     *   echo Olobuild_Builder::cockpit_page_head([
     *     'title'   => __('Strumenti', 'olobuild'),
     *     'sub'     => sprintf(__('Cache: %s · DB: %s'), '<b>2.4 MB</b>', '<b>OK</b>'),
     *     'actions' => Olobuild_Builder::cockpit_button(['label' => 'Nuovo', 'variant' => 'pri']),
     *   ]);
     */
    public static function cockpit_page_head( $args = [] ) {
        $args = wp_parse_args( $args, [ 'title' => '', 'sub' => '', 'actions' => '' ] );
        ob_start();
        ?>
        <header class="olo-page-head">
            <div class="titles">
                <h1><?php echo esc_html( $args['title'] ); ?></h1>
                <?php if ( $args['sub'] ) : ?>
                    <div class="sub"><?php echo wp_kses_post( $args['sub'] ); ?></div>
                <?php endif; ?>
            </div>
            <?php if ( $args['actions'] ) : ?>
                <div class="actions"><?php echo $args['actions']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- pre-built markup from cockpit_button(), which escapes all parts internally. ?></div>
            <?php endif; ?>
        </header>
        <?php
        return ob_get_clean();
    }

    /**
     * Sub-nav tabs (orizzontali) sotto il page head.
     *
     * @param array  $items   Array di tabs: [{slug, label, count?, href}].
     * @param string $active  Slug del tab attivo.
     *
     * @example
     *   echo Olobuild_Builder::cockpit_subnav([
     *     ['slug' => 'general', 'label' => 'Generale', 'href' => admin_url('admin.php?page=olo-seo')],
     *     ['slug' => 'meta',    'label' => 'Meta tag', 'href' => admin_url('admin.php?page=olo-seo&tab=meta'), 'count' => 12],
     *   ], 'general');
     */
    public static function cockpit_subnav( $items, $active = '' ) {
        if ( empty( $items ) || ! is_array( $items ) ) return '';
        ob_start();
        ?>
        <nav class="olo-subnav">
            <?php foreach ( $items as $it ) :
                $slug = $it['slug'] ?? '';
                $href = $it['href'] ?? '#';
                $label = $it['label'] ?? '';
                $count = $it['count'] ?? null;
                $is_active = ( $slug === $active );
            ?>
                <a href="<?php echo esc_url( $href ); ?>" class="<?php echo $is_active ? 'active' : ''; ?>">
                    <?php echo esc_html( $label ); ?>
                    <?php if ( $count !== null ) : ?>
                        <span class="num"><?php echo (int) $count; ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <?php
        return ob_get_clean();
    }

    /**
     * Toolbar sticky: chip filtri + search + sort + view toggle + content libero.
     *
     * @param array $args {
     *   @type array  $chips        Lista chip: [{id, label, count?, dot_color?, href?}]
     *   @type string $active_chip  ID del chip attivo.
     *   @type bool   $search       Mostra input search (default false).
     *   @type string $search_id    ID dell'input search (per binding JS).
     *   @type string $search_placeholder
     *   @type string $extra        HTML aggiuntivo dopo gli spacer (es. sort, view toggle).
     * }
     *
     * @example
     *   echo Olobuild_Builder::cockpit_toolbar([
     *     'chips' => [
     *       ['id'=>'all', 'label'=>'Tutti', 'count'=>128],
     *       ['id'=>'unread', 'label'=>'Non letti', 'count'=>12, 'dot_color'=>'#ef4444'],
     *     ],
     *     'active_chip' => 'all',
     *     'search'      => true,
     *     'search_placeholder' => 'Cerca…',
     *   ]);
     */
    public static function cockpit_toolbar( $args = [] ) {
        $args = wp_parse_args( $args, [
            'chips'              => [],
            'active_chip'        => '',
            'search'             => false,
            'search_id'          => '',
            'search_placeholder' => __( 'Cerca…', 'olobuild' ),
            'extra'              => '',
        ] );
        ob_start();
        ?>
        <div class="olo-toolbar">
            <?php if ( ! empty( $args['chips'] ) ) : ?>
                <div class="chips">
                    <?php foreach ( $args['chips'] as $c ) :
                        $id    = $c['id']    ?? '';
                        $label = $c['label'] ?? '';
                        $count = $c['count'] ?? null;
                        $dot   = $c['dot_color'] ?? '';
                        $href  = $c['href']  ?? '';
                        $on    = ( $id === $args['active_chip'] );
                        $tag   = $href ? 'a' : 'button';
                        $extra_attr = $href ? ' href="' . esc_url( $href ) . '"' : ' type="button"';
                    ?>
                        <<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $tag is 'a' or 'button' from the fixed ternary above. ?> class="olo-chip <?php echo $on ? 'on' : ''; ?>"
                            data-chip-id="<?php echo esc_attr( $id ); ?>"<?php echo $extra_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attribute built above from esc_url() or the fixed literal type="button". ?>>
                            <?php if ( $dot ) : ?>
                                <span class="dot" style="background: <?php echo esc_attr( $dot ); ?>"></span>
                            <?php endif; ?>
                            <?php echo esc_html( $label ); ?>
                            <?php if ( $count !== null ) : ?>
                                <span class="num"><?php echo (int) $count; ?></span>
                            <?php endif; ?>
                        </<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $tag is 'a' or 'button' from the fixed ternary above. ?>>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <span class="spc"></span>
            <?php if ( $args['search'] ) : ?>
                <div class="olo-search">
                    <span class="ic"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg></span>
                    <input type="search" id="<?php echo esc_attr( $args['search_id'] ); ?>"
                        placeholder="<?php echo esc_attr( $args['search_placeholder'] ); ?>" autocomplete="off" />
                </div>
            <?php endif; ?>
            <?php if ( $args['extra'] ) : ?>
                <?php echo $args['extra']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- pre-built markup provided by internal callers (escaped at construction). ?>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Bottone cockpit standard (atomico).
     *
     * @param array $args {
     *   @type string $label    Testo del bottone (richiesto).
     *   @type string $variant  'pri' | 'sec' | 'danger' | 'ghost'. Default 'sec'.
     *   @type string $size     '' | 'sm'. Default ''.
     *   @type string $icon     SVG path inline (24×24 viewBox, stroke-width 1.7).
     *   @type string $href     Se presente, emette <a> invece di <button>.
     *   @type string $type     button|submit. Default 'button'.
     *   @type string $title    Tooltip.
     *   @type bool   $disabled Default false.
     *   @type array  $attrs    Attributi extra (data-*, onclick, ecc.) come [k=>v].
     * }
     *
     * @example
     *   echo Olobuild_Builder::cockpit_button([
     *     'label' => 'Salva', 'variant' => 'pri', 'type' => 'submit',
     *     'icon' => '<path d="M19 21H5..." />',
     *   ]);
     */
    public static function cockpit_button( $args = [] ) {
        $args = wp_parse_args( $args, [
            'label'    => '',
            'variant'  => 'sec',
            'size'     => '',
            'icon'     => '',
            'href'     => '',
            'type'     => 'button',
            'title'    => '',
            'disabled' => false,
            'attrs'    => [],
        ] );
        $cls = 'olo-btn olo-btn-' . sanitize_html_class( $args['variant'] );
        if ( $args['size'] ) $cls .= ' olo-btn-' . sanitize_html_class( $args['size'] );
        if ( ! $args['label'] && $args['icon'] ) $cls .= ' olo-btn-icon';

        $attrs_str = '';
        foreach ( $args['attrs'] as $k => $v ) {
            $attrs_str .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
        }
        if ( $args['title'] ) $attrs_str .= ' title="' . esc_attr( $args['title'] ) . '"';

        $svg = $args['icon']
            ? '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">' . $args['icon'] . '</svg>'
            : '';

        if ( $args['href'] ) {
            return sprintf(
                '<a class="%s" href="%s"%s>%s%s</a>',
                esc_attr( $cls ), esc_url( $args['href'] ), $attrs_str,
                $svg, esc_html( $args['label'] )
            );
        }
        return sprintf(
            '<button class="%s" type="%s"%s%s>%s%s</button>',
            esc_attr( $cls ),
            esc_attr( $args['type'] ),
            $args['disabled'] ? ' disabled' : '',
            $attrs_str,
            $svg, esc_html( $args['label'] )
        );
    }

    /**
     * Card grid o list per liste di entità (Submissions, Popups, Media, ecc).
     *
     * @param array $args {
     *   @type array  $items    Array di items, ognuno con shape libera; passato al renderer.
     *   @type string $layout   'grid' | 'list'. Default 'grid'.
     *   @type callable $render Callback `function($item) => string` che ritorna l'HTML
     *                          della card. Riceve l'intero $item.
     *   @type string $empty_title    Titolo dell'empty state.
     *   @type string $empty_message  Messaggio dell'empty state.
     *   @type string $empty_actions  HTML dei bottoni dell'empty state.
     * }
     *
     * @example
     *   echo Olobuild_Builder::cockpit_card_grid([
     *     'items'  => $submissions,
     *     'layout' => 'grid',
     *     'render' => function($s) {
     *         return '<a class="olo-card" href="#' . $s['id'] . '">' .
     *             '<div class="head"><div class="lab"><div class="t">' . esc_html($s['from']) . '</div></div></div>' .
     *             '<div class="body">' . esc_html($s['preview']) . '</div>' .
     *         '</a>';
     *     },
     *     'empty_title'   => 'Nessun invio ancora',
     *     'empty_message' => 'I messaggi dai form compariranno qui.',
     *   ]);
     */
    public static function cockpit_card_grid( $args = [] ) {
        $args = wp_parse_args( $args, [
            'items'         => [],
            'layout'        => 'grid',
            'render'        => null,
            'empty_title'   => __( 'Nessun elemento', 'olobuild' ),
            'empty_message' => '',
            'empty_actions' => '',
        ] );

        if ( empty( $args['items'] ) ) {
            ob_start();
            ?>
            <div class="olo-empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" style="opacity:.3"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
                <h3><?php echo esc_html( $args['empty_title'] ); ?></h3>
                <?php if ( $args['empty_message'] ) : ?>
                    <p><?php echo esc_html( $args['empty_message'] ); ?></p>
                <?php endif; ?>
                <?php if ( $args['empty_actions'] ) : ?>
                    <div><?php echo $args['empty_actions']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- pre-built markup from cockpit_button(), which escapes all parts internally. ?></div>
                <?php endif; ?>
            </div>
            <?php
            return ob_get_clean();
        }

        $cls = $args['layout'] === 'list' ? 'olo-data-list' : 'olo-data-grid';
        ob_start();
        ?>
        <div class="<?php echo esc_attr( $cls ); ?>">
            <?php
            if ( is_callable( $args['render'] ) ) {
                foreach ( $args['items'] as $item ) {
                    echo call_user_func( $args['render'], $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- card HTML returned by the caller-supplied renderer, which escapes its own dynamic parts.
                }
            }
            ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_dashboard_page() {
        $manage = self::dashboard_manage_tiles();
        $system = self::dashboard_system_chips();
        $hero   = self::dashboard_hero_data();

        $user = wp_get_current_user();
        $first_name = $user->first_name ?: $user->display_name ?: __( 'Utente', 'olobuild' );
        $site_host = wp_parse_url( home_url(), PHP_URL_HOST );

        // Stato "aggiornamento disponibile" (placeholder — TODO collegare a UpdateChecker)
        $update_available = false;
        $update_info = [];

        $user_prefs = get_user_meta( $user->ID, 'olo_dashboard_prefs', true );
        if ( ! is_array( $user_prefs ) ) $user_prefs = [];
        $rail_collapsed = isset( $user_prefs['rail'] ) && $user_prefs['rail'] === 'collapsed';

        self::cockpit_shell_open( '<b>' . esc_html__( 'Dashboard', 'olobuild' ) . '</b>' );
        ?>
            <div class="olo-cockpit-grid<?php echo $rail_collapsed ? ' collapsed' : ''; ?>">
                <main class="olo-cockpit-main">

                    <?php if ( $update_available ) : ?>
                    <div class="olo-banner" data-banner="update-<?php echo esc_attr( $update_info['version'] ?? '0' ); ?>">
                        <span class="ic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4l9 16H3z"/><path d="M12 11v4M12 18h.01"/></svg></span>
                        <span><b><?php /* translators: %s: version number */ printf( esc_html__( 'Aggiornamento disponibile · v%s', 'olobuild' ), esc_html( $update_info['version'] ?? '' ) ); ?></b> — <?php echo esc_html( $update_info['note'] ?? '' ); ?></span>
                        <span class="spc"></span>
                        <a class="act" href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>"><?php esc_html_e( 'Aggiorna ora', 'olobuild' ); ?></a>
                        <button type="button" class="x" title="<?php esc_attr_e( 'Chiudi', 'olobuild' ); ?>"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l12 12M18 6L6 18"/></svg></button>
                    </div>
                    <?php endif; ?>

                    <!-- Hero contestuale -->
                    <section class="olo-hero">
                        <div class="olo-hero-l">
                            <div class="greet"><?php
                                /* translators: %s = first name */
                                printf( esc_html__( 'Ciao %s, buon lavoro', 'olobuild' ), esc_html( $first_name ) );
                            ?></div>
                            <h1><?php
                                if ( $hero['is_page'] ) {
                                    /* translators: %s = page title */
                                    printf( esc_html__( 'Continua su %s', 'olobuild' ), '<b>' . esc_html( $hero['title'] ) . '</b>' );
                                } else {
                                    echo '<b>' . esc_html( $hero['title'] ) . '</b>';
                                }
                            ?></h1>
                            <p class="sub"><?php echo wp_kses( $hero['sub'], [ 'b' => [], 'strong' => [] ] ); ?></p>
                            <div class="meta-row">
                                <span class="item"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 010 18M12 3a14 14 0 000 18"/></svg> <?php echo esc_html( $site_host ); ?></span>
                                <span class="item"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h4"/></svg> <b><?php echo esc_html( wp_count_posts( 'page' )->publish ); ?></b> <?php esc_html_e( 'pagine', 'olobuild' ); ?></span>
                                <span class="item"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 19l3-3a4 4 0 015.7 0l.3.3 4-9-9 4 .3.3a4 4 0 010 5.7L5 19zM4 14a3 3 0 00-1 6 3 3 0 006-1"/></svg> v<?php echo esc_html( OLOBUILD_VERSION ); ?></span>
                            </div>
                            <div class="ctas">
                                <a class="pri" href="<?php echo esc_url( $hero['edit'] ); ?>">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20l4-1 11-11-3-3L5 16l-1 4z"/></svg>
                                    <?php echo $hero['is_page'] ? esc_html__( 'Apri editor', 'olobuild' ) : esc_html__( 'Vai ai template', 'olobuild' ); ?>
                                </a>
                                <a class="sec" href="<?php echo esc_url( $hero['view'] ); ?>" target="_blank" rel="noopener">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M14 4h6v6M20 4l-8 8M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4"/></svg>
                                    <?php esc_html_e( 'Vedi sito live', 'olobuild' ); ?>
                                </a>
                                <button type="button" class="sec" data-olo-new-page title="<?php esc_attr_e( 'Crea un nuovo template e la pagina WP collegata', 'olobuild' ); ?>">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                                    <?php esc_html_e( 'Nuova pagina', 'olobuild' ); ?>
                                </button>
                            </div>
                        </div>
                        <div class="olo-hero-r">
                            <?php if ( $hero['status'] === 'publish' ) : ?>
                                <span class="live-pill">live</span>
                            <?php endif; ?>
                            <?php
                            // URL nella browser bar: full URL della pagina (host + path),
                            // troncato a metà se troppo lungo per stare nella barra.
                            $hero_url_display = $site_host;
                            if ( ! empty( $hero['view'] ) ) {
                                $parts = wp_parse_url( $hero['view'] );
                                $hero_url_display = ( $parts['host'] ?? $site_host ) . ( $parts['path'] ?? '' );
                                $hero_url_display = rtrim( $hero_url_display, '/' );
                            }
                            ?>
                            <div class="browser">
                                <div class="br-bar">
                                    <span class="dot r"></span><span class="dot y"></span><span class="dot g"></span>
                                    <span class="url" title="<?php echo esc_attr( $hero['view'] ?? '' ); ?>"><?php echo esc_html( $hero_url_display ); ?></span>
                                </div>
                                <?php if ( ! empty( $hero['thumbnail'] ) ) : ?>
                                    <a class="br-screenshot" href="<?php echo esc_url( $hero['view'] ); ?>" target="_blank" rel="noopener" title="<?php esc_attr_e( 'Apri pagina live', 'olobuild' ); ?>">
                                        <img src="<?php echo esc_url( $hero['thumbnail'] ); ?>" alt="<?php echo esc_attr( $hero['title'] ); ?>" loading="lazy" />
                                    </a>
                                <?php else : ?>
                                    <div class="br-body">
                                        <div class="nav">
                                            <span class="lo"><?php echo esc_html( strtoupper( substr( get_bloginfo( 'name' ), 0, 7 ) ) ); ?></span>
                                            <span><?php esc_html_e( 'Home', 'olobuild' ); ?></span>
                                            <span><?php esc_html_e( 'Servizi', 'olobuild' ); ?></span>
                                            <span><?php esc_html_e( 'Contatti', 'olobuild' ); ?></span>
                                        </div>
                                        <h2><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h2>
                                        <p><?php echo esc_html( wp_trim_words( get_bloginfo( 'description' ) ?: __( 'Il tuo sito WordPress costruito con Olobuild.', 'olobuild' ), 12 ) ); ?></p>
                                        <span class="btn"><?php esc_html_e( 'Scopri di più', 'olobuild' ); ?> →</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </section>

                    <!-- KPI strip (popolato da JS / boot data) -->
                    <section class="olo-kpi-strip" aria-label="<?php esc_attr_e( 'Indicatori chiave', 'olobuild' ); ?>"></section>

                    <!-- Recent strip -->
                    <section>
                        <div class="olo-sec-h">
                            <h2><?php esc_html_e( 'Continua dove avevi lasciato', 'olobuild' ); ?></h2>
                            <span class="hint"><?php esc_html_e( 'Le tue ultime modifiche', 'olobuild' ); ?></span>
                            <span class="spc"></span>
                            <a class="more" href="<?php echo esc_url( admin_url( 'edit.php?post_type=page&orderby=modified' ) ); ?>">
                                <?php esc_html_e( 'Vedi tutto', 'olobuild' ); ?>
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </a>
                        </div>
                        <div class="olo-recent-strip"></div>
                    </section>

                    <!-- Quick actions -->
                    <section>
                        <div class="olo-sec-h">
                            <h2><?php esc_html_e( 'Azioni rapide', 'olobuild' ); ?></h2>
                            <span class="hint"><?php esc_html_e( 'Ciò che fai più spesso', 'olobuild' ); ?></span>
                        </div>
                        <div class="olo-quick-row">
                            <button type="button" class="olo-quick-card tone-primary" data-olo-new-page>
                                <div class="ic-box"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg></div>
                                <div class="lab"><span class="t"><?php esc_html_e( 'Crea pagina', 'olobuild' ); ?></span><span class="h"><?php esc_html_e( 'Nuovo template + pagina WP', 'olobuild' ); ?></span></div>
                                <span class="arr"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
                            </button>
                            <a class="olo-quick-card tone-info" href="<?php echo esc_url( $hero['edit'] ); ?>">
                                <div class="ic-box"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20l4-1 11-11-3-3L5 16l-1 4z"/></svg></div>
                                <div class="lab"><span class="t"><?php esc_html_e( 'Apri editor', 'olobuild' ); ?></span><span class="h"><?php echo esc_html( wp_trim_words( $hero['title'], 4 ) ); ?></span></div>
                                <span class="arr"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
                            </a>
                            <a class="olo-quick-card tone-purple" href="<?php echo esc_url( admin_url( 'admin.php?page=olobuilder-templates' ) ); ?>">
                                <div class="ic-box"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg></div>
                                <div class="lab"><span class="t"><?php esc_html_e( 'Sfoglia template', 'olobuild' ); ?></span><span class="h"><?php esc_html_e( 'Pronti all\'uso', 'olobuild' ); ?></span></div>
                                <span class="arr"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
                            </a>
                            <a class="olo-quick-card tone-neutral" href="<?php echo esc_url( admin_url( 'admin.php?page=olo-import-export' ) ); ?>">
                                <div class="ic-box"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v12M7 9l5-5 5 5M5 20h14"/></svg></div>
                                <div class="lab"><span class="t"><?php esc_html_e( 'Importa', 'olobuild' ); ?></span><span class="h"><?php esc_html_e( 'Pagina, sito, JSON', 'olobuild' ); ?></span></div>
                                <span class="arr"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
                            </a>
                        </div>
                    </section>

                    <!-- Manage tile grid -->
                    <section>
                        <div class="olo-sec-h">
                            <h2><?php esc_html_e( 'Gestione', 'olobuild' ); ?></h2>
                            <span class="hint"><?php esc_html_e( 'Configurazione e contenuti del sito', 'olobuild' ); ?></span>
                        </div>
                        <div class="olo-manage-grid">
                            <?php foreach ( $manage as $i => $t ) : ?>
                            <a class="olo-manage-tile" href="<?php echo esc_url( $t['href'] ); ?>" data-id="<?php echo esc_attr( $t['id'] ); ?>" data-order="<?php echo esc_attr( $i ); ?>">
                                <div class="ic-sq"><?php // Il colore per-tile ($t['color']) resta nei dati ma non si applica più: icone neutre, accensione rossa in hover (CSS). ?><?php echo self::dashboard_svg( $t['icon'], 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG from the hardcoded dashboard_svg() map, size is int-cast. ?></div>
                                <div class="lab">
                                    <span class="t"><?php echo esc_html( $t['label'] ); ?></span>
                                    <span class="h"><?php echo esc_html( $t['hint'] ); ?></span>
                                </div>
                                <button type="button" class="pin" title="<?php esc_attr_e( 'Aggiungi ai preferiti', 'olobuild' ); ?>">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3l7 7-3 3 1 5-5-1-7 7v-4l-3-3 7-7-1-5z"/></svg>
                                </button>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <!-- System chips -->
                    <section>
                        <div class="olo-sec-h">
                            <h2><?php esc_html_e( 'Sistema', 'olobuild' ); ?></h2>
                            <span class="hint"><?php esc_html_e( 'Configurazione tecnica · raramente', 'olobuild' ); ?></span>
                        </div>
                        <div class="olo-system-row">
                            <?php foreach ( $system as $s ) : ?>
                            <a class="olo-system-chip" href="<?php echo esc_url( $s['href'] ); ?>">
                                <span class="ic"><?php echo self::dashboard_svg( $s['icon'], 13 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG from the hardcoded dashboard_svg() map, size is int-cast. ?></span>
                                <?php echo esc_html( $s['label'] ); ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </section>

                </main>

                <!-- Right rail -->
                <aside class="olo-rail">
                    <div class="rail-head">
                        <h2><?php esc_html_e( 'Centro risorse', 'olobuild' ); ?></h2>
                        <button type="button" class="toggle" title="<?php esc_attr_e( 'Comprimi pannello', 'olobuild' ); ?>">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l-6 6 6 6M15 6l6 6-6 6"/></svg>
                        </button>
                    </div>
                    <div class="rail-mini">
                        <button type="button" title="<?php esc_attr_e( 'Cosa c\'è di nuovo', 'olobuild' ); ?>">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 19l3-3a4 4 0 015.7 0l.3.3 4-9-9 4 .3.3a4 4 0 010 5.7L5 19zM4 14a3 3 0 00-1 6 3 3 0 006-1"/></svg>
                            <span class="dot-new"></span>
                        </button>
                        <button type="button" title="<?php esc_attr_e( 'Tutorial', 'olobuild' ); ?>">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M7 5l12 7-12 7z"/></svg>
                        </button>
                        <button type="button" title="<?php esc_attr_e( 'Documentazione', 'olobuild' ); ?>">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 015 0c0 1.5-2.5 2-2.5 4M12 17h.01"/></svg>
                        </button>
                        <button type="button" title="<?php esc_attr_e( 'Notifiche', 'olobuild' ); ?>">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 16V11a6 6 0 0112 0v5l2 2H4l2-2zM10 20a2 2 0 004 0"/></svg>
                        </button>
                    </div>
                    <div class="rail-body">
                        <div class="olo-rail-section">
                            <h3>
                                <?php esc_html_e( 'Cosa c\'è di nuovo', 'olobuild' ); ?>
                                <span class="pill">v<?php echo esc_html( OLOBUILD_VERSION ); ?></span>
                            </h3>
                            <div data-olo-changelog></div>
                        </div>
                        <div class="olo-rail-section">
                            <h3><?php esc_html_e( 'Impara Olobuild', 'olobuild' ); ?></h3>
                            <?php /* Niente emoji né gradient: icone SVG stroke su fondo neutro, la firma dei prodotti OLO. */ ?>
                            <a class="olo-learn-card" href="https://olotheme.com/docs/onboarding" target="_blank" rel="noopener">
                                <div class="th" style="background: var(--olo-bg-muted); color: var(--olo-text-soft);"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M15.5 8.5l-2 5-5 2 2-5z"/></svg></div>
                                <div class="info"><span class="t"><?php esc_html_e( 'Onboarding 60 secondi', 'olobuild' ); ?></span><span class="d"><svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor"><path d="M7 5l12 7-12 7z"/></svg> 1:02</span></div>
                            </a>
                            <a class="olo-learn-card" href="https://olotheme.com/docs/templates" target="_blank" rel="noopener">
                                <div class="th" style="background: var(--olo-bg-muted); color: var(--olo-text-soft);"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg></div>
                                <div class="info"><span class="t"><?php esc_html_e( 'Template come pro', 'olobuild' ); ?></span><span class="d"><svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor"><path d="M7 5l12 7-12 7z"/></svg> 4:18</span></div>
                            </a>
                            <a class="olo-learn-card" href="https://olotheme.com/docs/seo" target="_blank" rel="noopener">
                                <div class="th" style="background: var(--olo-bg-muted); color: var(--olo-text-soft);"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg></div>
                                <div class="info"><span class="t"><?php esc_html_e( 'SEO e Open Graph', 'olobuild' ); ?></span><span class="d"><svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor"><path d="M7 5l12 7-12 7z"/></svg> 3:45</span></div>
                            </a>
                            <a class="olo-learn-card" href="https://olotheme.com/docs/performance" target="_blank" rel="noopener">
                                <div class="th" style="background: var(--olo-bg-muted); color: var(--olo-text-soft);"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 14 8 10"/><circle cx="12" cy="14" r="9"/><path d="M3 14a9 9 0 0 1 18 0"/></svg></div>
                                <div class="info"><span class="t"><?php esc_html_e( 'Performance: punteggio 100', 'olobuild' ); ?></span><span class="d"><svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor"><path d="M7 5l12 7-12 7z"/></svg> 5:30</span></div>
                            </a>
                        </div>
                        <div class="olo-rail-section">
                            <h3><?php esc_html_e( 'Aiuto & supporto', 'olobuild' ); ?></h3>
                            <div class="olo-help-row">
                                <a href="https://olotheme.com/docs" target="_blank" rel="noopener">
                                    <span class="ic"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 015 0c0 1.5-2.5 2-2.5 4M12 17h.01"/></svg></span>
                                    <?php esc_html_e( 'Documentazione', 'olobuild' ); ?>
                                </a>
                                <a href="https://olotheme.com/support" target="_blank" rel="noopener">
                                    <span class="ic"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></span>
                                    <?php esc_html_e( 'Apri ticket', 'olobuild' ); ?>
                                </a>
                                <a href="https://olotheme.com/community" target="_blank" rel="noopener">
                                    <span class="ic"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0112 0"/><circle cx="17" cy="9" r="2.5"/><path d="M14 20a5 5 0 017-4.5"/></svg></span>
                                    <?php esc_html_e( 'Community', 'olobuild' ); ?>
                                </a>
                                <a href="https://olotheme.com/roadmap" target="_blank" rel="noopener">
                                    <span class="ic"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M14 4h6v6M20 4l-8 8M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4"/></svg></span>
                                    <?php esc_html_e( 'Roadmap', 'olobuild' ); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        <?php
        self::cockpit_shell_close();
    }

    /**
     * Mappa nome icona → SVG inline (subset usato dalla dashboard cockpit).
     */
    public static function dashboard_svg( $name, $size = 18 ) {
        $paths = [
            'template' => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>',
            'sliders'  => '<line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/>',
            'image'    => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/>',
            'form'     => '<path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>',
            'chart'    => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
            'cookie'   => '<circle cx="12" cy="12" r="9"/><circle cx="9" cy="9" r="1"/><circle cx="15" cy="11" r="1"/><circle cx="11" cy="15" r="1"/>',
            'search'   => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>',
            'redirect' => '<path d="M3 9l4-4 4 4M7 5v9a4 4 0 004 4h7M21 15l-4 4-4-4"/>',
            'zap'      => '<path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>',
            'wrench'   => '<path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/>',
            'cart'     => '<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/>',
            'modal'    => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/>',
            'tag'      => '<path d="M3 12V4h8l10 10-8 8z"/><circle cx="7.5" cy="7.5" r="1"/>',
            'upload'   => '<path d="M12 4v12M7 9l5-5 5 5M5 20h14"/>',
            'users'    => '<circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0112 0"/><circle cx="17" cy="9" r="2.5"/><path d="M14 20a5 5 0 017-4.5"/>',
            'inbox'    => '<path d="M3 12l3-7h12l3 7v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6zM3 12h5l1 2h6l1-2h5"/>',
            'history'  => '<path d="M3 3v5h5"/><path d="M3.05 13A9 9 0 106 5.3L3 8"/><path d="M12 7v5l4 2"/>',
            'key'      => '<circle cx="8" cy="15" r="4"/><path d="M11 12l9-9 2 2-2 2 2 2-2 2-3-3"/>',
        ];
        $path = $paths[ $name ] ?? '';
        return '<svg width="' . (int) $size . '" height="' . (int) $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">' . $path . '</svg>';
    }
}
