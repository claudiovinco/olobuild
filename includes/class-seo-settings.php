<?php
/**
 * Olobuild_Seo_Settings — Pagina impostazioni SEO + Meta box per post/pagine.
 *
 * Ispirato a Rank Math / Yoast: titoli, meta, social, schema, sitemap, robots.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Seo_Settings {

    private static $instance = null;

    /** Option keys */
    const OPT_GENERAL   = 'olo_seo_general';
    const OPT_TITLES    = 'olo_seo_titles';
    const OPT_SOCIAL    = 'olo_seo_social';
    const OPT_LOCAL     = 'olo_seo_local_business';
    const OPT_WEBMASTER = 'olo_seo_webmaster';
    const OPT_ADVANCED  = 'olobuild_seo_advanced';
    const OPT_SITEMAP   = 'olo_seo_sitemap';

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init() {
        add_action( 'admin_menu', [ $this, 'add_menu' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );

        // Meta box per post/pagine
        add_action( 'add_meta_boxes', [ $this, 'add_seo_meta_box' ] );
        add_action( 'save_post', [ $this, 'save_seo_meta_box' ], 10, 2 );

        // Colonna SEO nella lista post/pagine
        add_filter( 'manage_posts_columns', [ $this, 'add_seo_column' ] );
        add_filter( 'manage_pages_columns', [ $this, 'add_seo_column' ] );
        add_action( 'manage_posts_custom_column', [ $this, 'render_seo_column' ], 10, 2 );
        add_action( 'manage_pages_custom_column', [ $this, 'render_seo_column' ], 10, 2 );
    }

    /* ═══════════════════════════════════════════════════
     * MENU & PAGINA ADMIN
     * ═══════════════════════════════════════════════════ */

    public function add_menu() {
        // v1.0.30 — pagina migrata in ?page=olobuilder-settings&tab=seo
        // Submenu rimosso: i campi vivono ora in Configurazione → SEO globale.
        // La classe resta attiva per i filter del frontend (title, meta, sitemap, schema.org).
    }

    public function enqueue_admin_assets( $hook ) {
        // Solo nella pagina SEO e negli editor
        if ( $hook === 'olobuild_page_olo-seo' ) {
            wp_enqueue_media();
            wp_enqueue_style( 'olo-seo-admin', OLOBUILD_URL . 'assets/css/seo-admin.css', [], OLOBUILD_VERSION );
        }

        // Meta box JS ovunque ci sia l'editor
        $screen = get_current_screen();
        if ( $screen && ( $screen->base === 'post' || $screen->base === 'page' ) ) {
            wp_enqueue_media();
        }
    }

    public function register_settings() {
        $options = [
            self::OPT_GENERAL,
            self::OPT_TITLES,
            self::OPT_SOCIAL,
            self::OPT_LOCAL,
            self::OPT_WEBMASTER,
            self::OPT_ADVANCED,
            self::OPT_SITEMAP,
        ];

        foreach ( $options as $opt ) {
            register_setting( 'olobuild_seo_group', $opt, [
                'type'              => 'array',
                'sanitize_callback' => [ $this, 'sanitize_array_option' ],
                'default'           => [],
            ] );
        }
    }

    public function sanitize_array_option( $value ) {
        if ( ! is_array( $value ) ) {
            return [];
        }
        return $this->deep_sanitize( $value );
    }

    private function deep_sanitize( $arr ) {
        $clean = [];
        foreach ( $arr as $k => $v ) {
            $k = sanitize_text_field( $k );
            if ( is_array( $v ) ) {
                $clean[ $k ] = $this->deep_sanitize( $v );
            } else {
                // Permetti HTML nei campi di verifica webmaster (meta tag)
                if ( str_contains( $k, 'verification' ) ) {
                    $clean[ $k ] = sanitize_text_field( $v );
                } else {
                    $clean[ $k ] = sanitize_text_field( $v );
                }
            }
        }
        return $clean;
    }

    /* ─── Helpers per opzioni ─── */

    /** Tipi di chi pubblica il sito offerti dalla scheda SEO globale. */
    const KG_TYPES = [ 'Organization', 'Person', 'LocalBusiness', 'Hotel', 'Restaurant', 'Store', 'ProfessionalService' ];

    public static function get( $option_key, $field = null, $default = '' ) {
        $opts = get_option( $option_key, [] );
        $opts = self::normalizza( $option_key, is_array( $opts ) ? $opts : [] );
        if ( $field === null ) {
            return $opts;
        }
        return $opts[ $field ] ?? $default;
    }

    /**
     * Le chiavi che la scheda «SEO globale» ha scritto fino alla 1.4.501 erano
     * diverse da quelle che Olobuild_Seo_Head legge (og_image, twitter_handle,
     * card_type, advanced.schema.type): i controlli non agivano. Qui diventano
     * quelle giuste, in lettura (siti già salvati) e in scrittura.
     */
    public static function normalizza( $option_key, array $o ) {
        if ( self::OPT_SOCIAL === $option_key ) {
            foreach ( [ 'og_image' => 'og_default_image', 'twitter_handle' => 'twitter_user', 'card_type' => 'twitter_card_type' ] as $da => $a ) {
                if ( ! array_key_exists( $da, $o ) ) {
                    continue;
                }
                if ( empty( $o[ $a ] ) && '' !== (string) $o[ $da ] ) {
                    $o[ $a ] = ( 'card_type' === $da && 'summary' !== $o[ $da ] ) ? 'summary_large_image' : $o[ $da ];
                }
                unset( $o[ $da ] );
            }
            if ( isset( $o['twitter_user'] ) ) {
                $o['twitter_user'] = ltrim( sanitize_text_field( $o['twitter_user'] ), '@' );
            }
            if ( isset( $o['og_default_image'] ) ) {
                $o['og_default_image'] = esc_url_raw( $o['og_default_image'] );
            }
            if ( isset( $o['twitter_card_type'] ) && ! in_array( $o['twitter_card_type'], [ 'summary', 'summary_large_image' ], true ) ) {
                unset( $o['twitter_card_type'] );
            }
            // L'immagine disegnata (1.4.1218)
            if ( isset( $o['og_disegnata'] ) ) {
                $o['og_disegnata'] = ! empty( $o['og_disegnata'] );
            }
            if ( isset( $o['og_stile'] ) && ! in_array( $o['og_stile'], [ 'foto', 'meta', 'colore' ], true ) ) {
                $o['og_stile'] = 'foto';
            }
        }
        if ( self::OPT_TITLES === $option_key ) {
            // «Lingua sito» (la lingua è quella di WordPress e di OLOlang) e «Robots
            // default» (è blog_public, Impostazioni › Lettura) non stanno più qui.
            unset( $o['language'], $o['robots'] );
            if ( empty( $o['kg_type'] ) ) {
                $adv = get_option( self::OPT_ADVANCED, [] );
                if ( is_array( $adv ) && ! empty( $adv['schema']['type'] ) ) {
                    $o['kg_type'] = $adv['schema']['type'];
                }
            }
            if ( isset( $o['kg_type'] ) && ! in_array( $o['kg_type'], self::KG_TYPES, true ) ) {
                $o['kg_type'] = 'Organization';
            }
            if ( isset( $o['description'] ) ) {
                $o['description'] = mb_substr( sanitize_textarea_field( $o['description'] ), 0, 160 );
            }
            if ( isset( $o['pattern'] ) ) {
                $o['pattern'] = sanitize_text_field( $o['pattern'] );
            }
            if ( isset( $o['separator'] ) ) {
                $o['separator'] = sanitize_text_field( $o['separator'] );
            }
        }
        return $o;
    }

    /* ═══════════════════════════════════════════════════
     * RENDER PAGINA SETTINGS
     * ═══════════════════════════════════════════════════ */

    public function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $tabs = [
            'titles'    => 'Titoli &amp; Meta',
            'social'    => 'Social',
            'schema'    => 'Schema / Local SEO',
            'webmaster' => 'Webmaster Tools',
            'sitemap'   => 'Sitemap',
            'advanced'  => 'Avanzate',
        ];

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per il routing del tab nella pagina admin (sotto current_user_can manage_options); nessuna modifica di stato; valore sanitizzato e validato contro la whitelist $tabs.
        $active_tab = sanitize_text_field( wp_unslash( $_GET['tab'] ?? 'titles' ) );
        if ( ! array_key_exists( $active_tab, $tabs ) ) {
            $active_tab = 'titles';
        }

        $subnav = [];
        foreach ( $tabs as $slug => $label ) {
            $subnav[] = [ 'slug' => $slug, 'label' => wp_kses_post( $label ), 'href' => admin_url( 'admin.php?page=olo-seo&tab=' . $slug ) ];
        }

        ?>
        <?php Olobuild_Builder::cockpit_shell_open( '<b>' . esc_html__( 'SEO', 'olobuild' ) . '</b>' ); ?>
        <main class="olo-cockpit-main olo-cockpit-legacy olo-seo-wrap">
            <?php
            // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML generated by Olobuild_Builder::cockpit_page_head(); dynamic values escaped internally (esc_html/wp_kses_post)
            echo Olobuild_Builder::cockpit_page_head( [
                'title' => __( 'SEO', 'olobuild' ),
                'sub'   => __( 'Title, meta description, Open Graph, schema.org, sitemap XML e webmaster tools.', 'olobuild' ),
            ] );
            // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML generated by Olobuild_Builder::cockpit_subnav(); dynamic values escaped internally (esc_url/esc_html)
            echo Olobuild_Builder::cockpit_subnav( $subnav, $active_tab );
            ?>

            <form method="post" action="options.php" class="olo-seo-form" style="margin-top:16px">
                <?php
                settings_fields( 'olobuild_seo_group' );
                $this->{'render_tab_' . $active_tab}();
                ?>
                <div style="margin-top:24px;">
                    <?php
                    // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML generated by Olobuild_Builder::cockpit_button(); dynamic values escaped internally (esc_attr/esc_html)
                    echo Olobuild_Builder::cockpit_button( [
                        'label'   => __( 'Salva impostazioni', 'olobuild' ),
                        'variant' => 'pri',
                        'type'    => 'submit',
                        'icon'    => '<path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>',
                    ] );
                    // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
                    ?>
                </div>
            </form>
        </main>
        <?php Olobuild_Builder::cockpit_shell_close(); ?>
        <?php
    }

    private function tab_icon( $slug ) {
        $icons = [
            'titles'    => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px;"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>',
            'social'    => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px;"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>',
            'schema'    => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px;"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
            'webmaster' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px;"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>',
            'sitemap'   => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px;"><rect x="9" y="2" width="6" height="6" rx="1"/><rect x="2" y="16" width="6" height="6" rx="1"/><rect x="16" y="16" width="6" height="6" rx="1"/><path d="M12 8v4"/><path d="M5 16v-2a2 2 0 012-2h10a2 2 0 012 2v2"/></svg>',
            'advanced'  => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px;"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>',
        ];
        return $icons[ $slug ] ?? '';
    }

    /* ─── Tab: Titoli & Meta ─── */

    private function render_tab_titles() {
        $opts = self::get( self::OPT_TITLES );
        $sep  = $opts['separator'] ?? '-';

        $separators = [ '-', '–', '—', '|', '·', '/', '»', '«', ':', '•' ];
        ?>
        <!-- Formato Titoli -->
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </div>
                <div>
                    <h3>Formato Titoli</h3>
                    <p>Definisci il formato predefinito dei titoli per ogni tipo di contenuto. Variabili: <code>%title%</code>, <code>%sitename%</code>, <code>%sep%</code>, <code>%tagline%</code>, <code>%category%</code>, <code>%tag%</code>, <code>%search_query%</code>, <code>%page%</code>, <code>%date%</code>, <code>%author%</code></p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Separatore</label>
                        <div class="olo-field-hint">Il carattere usato per separare titolo e nome sito.</div>
                    </div>
                    <div class="olo-field-input-wrap">
                        <div class="olo-seo-separator-picker">
                            <?php foreach ( $separators as $s ) : ?>
                                <label class="olo-seo-sep-option <?php echo $sep === $s ? 'active' : ''; ?>">
                                    <input type="radio" name="<?php echo esc_attr( self::OPT_TITLES ); ?>[separator]" value="<?php echo esc_attr( $s ); ?>" <?php checked( $sep, $s ); ?>>
                                    <span><?php echo esc_html( $s ); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php
                $title_fields = [
                    'homepage_title'  => [ 'Homepage — Titolo',      '%sitename% %sep% %tagline%' ],
                    'homepage_desc'   => [ 'Homepage — Descrizione', '' ],
                    'post_title'      => [ 'Articoli — Titolo',      '%title% %sep% %sitename%' ],
                    'post_desc'       => [ 'Articoli — Descrizione', '%excerpt%' ],
                    'page_title'      => [ 'Pagine — Titolo',        '%title% %sep% %sitename%' ],
                    'page_desc'       => [ 'Pagine — Descrizione',   '' ],
                    'category_title'  => [ 'Categorie — Titolo',     '%category% %sep% %sitename%' ],
                    'category_desc'   => [ 'Categorie — Descrizione', '%category_description%' ],
                    'tag_title'       => [ 'Tag — Titolo',            '%tag% %sep% %sitename%' ],
                    'author_title'    => [ 'Autore — Titolo',         '%author% %sep% %sitename%' ],
                    'search_title'    => [ 'Ricerca — Titolo',        'Risultati per "%search_query%" %sep% %sitename%' ],
                    '404_title'       => [ '404 — Titolo',            'Pagina non trovata %sep% %sitename%' ],
                ];

                foreach ( $title_fields as $key => $info ) :
                    $val = $opts[ $key ] ?? $info[1];
                    $is_desc = str_contains( $key, '_desc' );
                    ?>
                    <div class="olo-field-row">
                        <div class="olo-field-info">
                            <label><?php echo esc_html( $info[0] ); ?></label>
                        </div>
                        <div class="olo-field-input-wrap">
                            <?php if ( $is_desc ) : ?>
                                <textarea name="<?php echo esc_attr( self::OPT_TITLES ); ?>[<?php echo esc_attr( $key ); ?>]" class="olo-field-input" rows="2" placeholder="<?php echo esc_attr( $info[1] ); ?>"><?php echo esc_textarea( $val ); ?></textarea>
                            <?php else : ?>
                                <input type="text" name="<?php echo esc_attr( self::OPT_TITLES ); ?>[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $val ); ?>" class="olo-field-input" placeholder="<?php echo esc_attr( $info[1] ); ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Knowledge Graph -->
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon orange">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                </div>
                <div>
                    <h3>Knowledge Graph</h3>
                    <p>Queste informazioni vengono usate per il markup JSON-LD del Knowledge Graph di Google.</p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Tipo entità</label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <select name="<?php echo esc_attr( self::OPT_TITLES ); ?>[kg_type]" class="olo-field-input">
                            <option value="Organization" <?php selected( $opts['kg_type'] ?? 'Organization', 'Organization' ); ?>>Organizzazione</option>
                            <option value="Person" <?php selected( $opts['kg_type'] ?? '', 'Person' ); ?>>Persona</option>
                        </select>
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Nome</label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="text" name="<?php echo esc_attr( self::OPT_TITLES ); ?>[kg_name]" value="<?php echo esc_attr( $opts['kg_name'] ?? get_bloginfo( 'name' ) ); ?>" class="olo-field-input">
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Logo / Foto (URL)</label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="text" id="olo-seo-kg-logo" name="<?php echo esc_attr( self::OPT_TITLES ); ?>[kg_logo]" value="<?php echo esc_attr( $opts['kg_logo'] ?? '' ); ?>" class="olo-field-input">
                        <button type="button" class="button olo-seo-upload-btn" data-target="olo-seo-kg-logo" style="margin-top:6px;">Seleziona</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /* ─── Tab: Social ─── */

    private function render_tab_social() {
        $opts = self::get( self::OPT_SOCIAL );

        $social_icons = [
            'facebook_url'   => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>',
            'twitter_user'   => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg>',
            'instagram_url'  => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>',
            'linkedin_url'   => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-4 0v7h-4v-7a6 6 0 016-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>',
            'youtube_url'    => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 00-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 00-1.94 2A29 29 0 001 11.75a29 29 0 00.46 5.33A2.78 2.78 0 003.4 19.1c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 001.94-2 29 29 0 00.46-5.25 29 29 0 00-.46-5.43z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/></svg>',
            'pinterest_url'  => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="17" x2="12" y2="22"/><path d="M5 12V8a7 7 0 0114 0v4"/><circle cx="12" cy="12" r="3"/></svg>',
            'tiktok_url'     => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12a4 4 0 104 4V4a5 5 0 005 5"/></svg>',
        ];
        ?>
        <!-- Profili Social -->
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                </div>
                <div>
                    <h3>Profili Social</h3>
                    <p>Usati nel markup JSON-LD <code>sameAs</code> per collegare il sito ai profili social.</p>
                </div>
            </div>
            <div class="olo-card-body">
                <?php
                $social_fields = [
                    'facebook_url'   => [ 'Facebook', 'https://facebook.com/...' ],
                    'twitter_user'   => [ 'X (Twitter)', '@username' ],
                    'instagram_url'  => [ 'Instagram', 'https://instagram.com/...' ],
                    'linkedin_url'   => [ 'LinkedIn', 'https://linkedin.com/...' ],
                    'youtube_url'    => [ 'YouTube', 'https://youtube.com/...' ],
                    'pinterest_url'  => [ 'Pinterest', 'https://pinterest.com/...' ],
                    'tiktok_url'     => [ 'TikTok', 'https://tiktok.com/@...' ],
                ];
                foreach ( $social_fields as $key => $info ) :
                    ?>
                    <div class="olo-field-row">
                        <div class="olo-field-info">
                            <label><?php echo $social_icons[ $key ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG from the hardcoded $social_icons map above ?> <?php echo esc_html( $info[0] ); ?></label>
                        </div>
                        <div class="olo-field-input-wrap">
                            <input type="text" name="<?php echo esc_attr( self::OPT_SOCIAL ); ?>[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $opts[ $key ] ?? '' ); ?>" class="olo-field-input" placeholder="<?php echo esc_attr( $info[1] ); ?>">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Open Graph -->
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon orange">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                </div>
                <div>
                    <h3>Open Graph — Impostazioni Predefinite</h3>
                    <p>Configurazione per la condivisione su Facebook, Twitter e altri social network.</p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Immagine OG predefinita</label>
                        <div class="olo-field-hint">Usata quando un post/pagina non ha immagine in evidenza. Dimensione consigliata: 1200x630px.</div>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="text" id="olo-seo-og-default" name="<?php echo esc_attr( self::OPT_SOCIAL ); ?>[og_default_image]" value="<?php echo esc_attr( $opts['og_default_image'] ?? '' ); ?>" class="olo-field-input">
                        <button type="button" class="button olo-seo-upload-btn" data-target="olo-seo-og-default" style="margin-top:6px;">Seleziona</button>
                        <?php if ( ! empty( $opts['og_default_image'] ) ) : ?>
                            <br><img src="<?php echo esc_url( $opts['og_default_image'] ); ?>" style="max-width:200px;margin-top:8px;border-radius:4px;">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Facebook App ID</label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="text" name="<?php echo esc_attr( self::OPT_SOCIAL ); ?>[fb_app_id]" value="<?php echo esc_attr( $opts['fb_app_id'] ?? '' ); ?>" class="olo-field-input" placeholder="Es. 123456789012345">
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Twitter Card Type</label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <select name="<?php echo esc_attr( self::OPT_SOCIAL ); ?>[twitter_card_type]" class="olo-field-input">
                            <option value="summary_large_image" <?php selected( $opts['twitter_card_type'] ?? 'summary_large_image', 'summary_large_image' ); ?>>Summary con immagine grande</option>
                            <option value="summary" <?php selected( $opts['twitter_card_type'] ?? '', 'summary' ); ?>>Summary</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /* ─── Tab: Schema / Local SEO ─── */

    private function render_tab_schema() {
        $opts = self::get( self::OPT_LOCAL );
        ?>
        <!-- Local Business / Organizzazione -->
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <div>
                    <h3>Local Business / Organizzazione</h3>
                    <p>Schema markup LocalBusiness per Google. Se il nome è vuoto, questo schema non viene generato.</p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Tipo attività</label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <select name="<?php echo esc_attr( self::OPT_LOCAL ); ?>[type]" class="olo-field-input">
                            <?php
                            $types = [
                                'LocalBusiness'       => 'Attività locale (generico)',
                                'Restaurant'          => 'Ristorante',
                                'Hotel'               => 'Hotel',
                                'BarOrPub'            => 'Bar / Pub',
                                'CafeOrCoffeeShop'    => 'Caffè / Coffee Shop',
                                'Store'               => 'Negozio (generico)',
                                'ClothingStore'       => 'Negozio abbigliamento',
                                'GroceryStore'        => 'Alimentari / Supermercato',
                                'SportingGoodsStore'  => 'Negozio articoli sportivi',
                                'AutoRepair'          => 'Officina auto',
                                'BeautySalon'         => 'Salone di bellezza',
                                'Dentist'             => 'Dentista',
                                'MedicalClinic'       => 'Clinica medica',
                                'Pharmacy'            => 'Farmacia',
                                'LegalService'        => 'Studio legale',
                                'AccountingService'   => 'Commercialista',
                                'RealEstateAgent'     => 'Agenzia immobiliare',
                                'TravelAgency'        => 'Agenzia viaggi',
                                'FinancialService'    => 'Servizi finanziari',
                                'InsuranceAgency'     => 'Agenzia assicurazioni',
                                'HealthClub'          => 'Palestra / Centro fitness',
                                'DaySpa'              => 'Spa / Centro benessere',
                                'Campground'          => 'Campeggio',
                                'BedAndBreakfast'     => 'Bed & Breakfast',
                                'Hostel'              => 'Ostello',
                                'LodgingBusiness'     => 'Struttura ricettiva (generico)',
                                'ProfessionalService' => 'Servizio professionale',
                                'EducationalOrganization' => 'Ente educativo',
                                'GovernmentOffice'    => 'Ufficio pubblico',
                            ];
                            foreach ( $types as $val => $label ) :
                                ?>
                                <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $opts['type'] ?? 'LocalBusiness', $val ); ?>><?php echo esc_html( $label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Nome attività</label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="text" name="<?php echo esc_attr( self::OPT_LOCAL ); ?>[name]" value="<?php echo esc_attr( $opts['name'] ?? '' ); ?>" class="olo-field-input">
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Descrizione</label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <textarea name="<?php echo esc_attr( self::OPT_LOCAL ); ?>[description]" class="olo-field-input" rows="2"><?php echo esc_textarea( $opts['description'] ?? '' ); ?></textarea>
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Via / Indirizzo</label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="text" name="<?php echo esc_attr( self::OPT_LOCAL ); ?>[address][street]" value="<?php echo esc_attr( $opts['address']['street'] ?? '' ); ?>" class="olo-field-input">
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Città</label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="text" name="<?php echo esc_attr( self::OPT_LOCAL ); ?>[address][city]" value="<?php echo esc_attr( $opts['address']['city'] ?? '' ); ?>" class="olo-field-input">
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Provincia / Stato</label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="text" name="<?php echo esc_attr( self::OPT_LOCAL ); ?>[address][state]" value="<?php echo esc_attr( $opts['address']['state'] ?? '' ); ?>" class="olo-field-input">
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>CAP</label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="text" name="<?php echo esc_attr( self::OPT_LOCAL ); ?>[address][zip]" value="<?php echo esc_attr( $opts['address']['zip'] ?? '' ); ?>" class="olo-field-input" style="max-width:120px;">
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Paese</label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="text" name="<?php echo esc_attr( self::OPT_LOCAL ); ?>[address][country]" value="<?php echo esc_attr( $opts['address']['country'] ?? 'IT' ); ?>" class="olo-field-input" style="max-width:80px;" placeholder="IT">
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Telefono</label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="text" name="<?php echo esc_attr( self::OPT_LOCAL ); ?>[phone]" value="<?php echo esc_attr( $opts['phone'] ?? '' ); ?>" class="olo-field-input" placeholder="+39 ...">
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Email</label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="email" name="<?php echo esc_attr( self::OPT_LOCAL ); ?>[email]" value="<?php echo esc_attr( $opts['email'] ?? '' ); ?>" class="olo-field-input">
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Coordinate GPS</label>
                    </div>
                    <div class="olo-field-input-wrap" style="display:flex;gap:8px;align-items:center;">
                        <input type="text" name="<?php echo esc_attr( self::OPT_LOCAL ); ?>[geo_lat]" value="<?php echo esc_attr( $opts['geo_lat'] ?? '' ); ?>" class="olo-field-input" style="max-width:140px;" placeholder="Lat">
                        <span style="color:#999;">&mdash;</span>
                        <input type="text" name="<?php echo esc_attr( self::OPT_LOCAL ); ?>[geo_lng]" value="<?php echo esc_attr( $opts['geo_lng'] ?? '' ); ?>" class="olo-field-input" style="max-width:140px;" placeholder="Lng">
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Fascia di prezzo</label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <select name="<?php echo esc_attr( self::OPT_LOCAL ); ?>[price_range]" class="olo-field-input">
                            <option value="" <?php selected( $opts['price_range'] ?? '', '' ); ?>>— Non specificato —</option>
                            <option value="$" <?php selected( $opts['price_range'] ?? '', '$' ); ?>>$ — Economico</option>
                            <option value="$$" <?php selected( $opts['price_range'] ?? '', '$$' ); ?>>$$ — Moderato</option>
                            <option value="$$$" <?php selected( $opts['price_range'] ?? '', '$$$' ); ?>>$$$ — Costoso</option>
                            <option value="$$$$" <?php selected( $opts['price_range'] ?? '', '$$$$' ); ?>>$$$$ — Lusso</option>
                        </select>
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Immagine attività (URL)</label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="text" id="olo-seo-biz-image" name="<?php echo esc_attr( self::OPT_LOCAL ); ?>[image]" value="<?php echo esc_attr( $opts['image'] ?? '' ); ?>" class="olo-field-input">
                        <button type="button" class="button olo-seo-upload-btn" data-target="olo-seo-biz-image" style="margin-top:6px;">Seleziona</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orari di apertura -->
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon orange">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                    <h3>Orari di apertura</h3>
                    <p>Inserisci gli orari nel formato <code>HH:MM</code>. Lascia vuoto per i giorni di chiusura.</p>
                </div>
            </div>
            <div class="olo-card-body">
                <?php
                $days = [
                    'monday'    => 'Lunedì',
                    'tuesday'   => 'Martedì',
                    'wednesday' => 'Mercoledì',
                    'thursday'  => 'Giovedì',
                    'friday'    => 'Venerdì',
                    'saturday'  => 'Sabato',
                    'sunday'    => 'Domenica',
                ];
                $hours = $opts['hours'] ?? [];
                foreach ( $days as $key => $label ) :
                    $open  = $hours[ $key ]['open'] ?? '';
                    $close = $hours[ $key ]['close'] ?? '';
                    ?>
                    <div class="olo-field-row">
                        <div class="olo-field-info">
                            <label><?php echo esc_html( $label ); ?></label>
                        </div>
                        <div class="olo-field-input-wrap" style="display:flex;gap:8px;align-items:center;">
                            <input type="text" name="<?php echo esc_attr( self::OPT_LOCAL ); ?>[hours][<?php echo esc_attr( $key ); ?>][open]" value="<?php echo esc_attr( $open ); ?>" class="olo-field-input" style="max-width:100px;" placeholder="09:00">
                            <span style="color:#999;">&mdash;</span>
                            <input type="text" name="<?php echo esc_attr( self::OPT_LOCAL ); ?>[hours][<?php echo esc_attr( $key ); ?>][close]" value="<?php echo esc_attr( $close ); ?>" class="olo-field-input" style="max-width:100px;" placeholder="18:00">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /* ─── Tab: Webmaster Tools ─── */

    private function render_tab_webmaster() {
        $opts = self::get( self::OPT_WEBMASTER );
        ?>
        <!-- Verifica Motori di Ricerca -->
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
                </div>
                <div>
                    <h3>Verifica Motori di Ricerca</h3>
                    <p>Inserisci il codice di verifica (solo il valore del <code>content</code>, non l'intero meta tag).</p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Google Search Console</label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="text" name="<?php echo esc_attr( self::OPT_WEBMASTER ); ?>[google]" value="<?php echo esc_attr( $opts['google'] ?? '' ); ?>" class="olo-field-input" placeholder="Es. 1a2b3c4d5e...">
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Bing Webmaster Tools</label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="text" name="<?php echo esc_attr( self::OPT_WEBMASTER ); ?>[bing]" value="<?php echo esc_attr( $opts['bing'] ?? '' ); ?>" class="olo-field-input">
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Pinterest</label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="text" name="<?php echo esc_attr( self::OPT_WEBMASTER ); ?>[pinterest]" value="<?php echo esc_attr( $opts['pinterest'] ?? '' ); ?>" class="olo-field-input">
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Yandex</label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="text" name="<?php echo esc_attr( self::OPT_WEBMASTER ); ?>[yandex]" value="<?php echo esc_attr( $opts['yandex'] ?? '' ); ?>" class="olo-field-input">
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /* ─── Tab: Sitemap ─── */

    private function render_tab_sitemap() {
        $opts = self::get( self::OPT_SITEMAP );
        $public_pts = get_post_types( [ 'public' => true ], 'objects' );
        $public_tax = get_taxonomies( [ 'public' => true ], 'objects' );
        ?>
        <!-- Sitemap XML -->
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="2" width="6" height="6" rx="1"/><rect x="2" y="16" width="6" height="6" rx="1"/><rect x="16" y="16" width="6" height="6" rx="1"/><path d="M12 8v4"/><path d="M5 16v-2a2 2 0 012-2h10a2 2 0 012 2v2"/></svg>
                </div>
                <div>
                    <h3>Sitemap XML</h3>
                    <p>URL della sitemap: <a href="<?php echo esc_url( home_url( '/?olo_sitemap=1' ) ); ?>" target="_blank"><code><?php echo esc_html( home_url( '/?olo_sitemap=1' ) ); ?></code></a></p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Sitemap attiva</label>
                        <div class="olo-field-hint">Genera la sitemap XML per i motori di ricerca.</div>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo esc_attr( self::OPT_SITEMAP ); ?>[enabled]" value="1" <?php checked( $opts['enabled'] ?? '1', '1' ); ?>>
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Numero max URL per sitemap</label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="number" name="<?php echo esc_attr( self::OPT_SITEMAP ); ?>[max_urls]" value="<?php echo esc_attr( $opts['max_urls'] ?? 1000 ); ?>" class="olo-field-input" style="max-width:120px;" min="100" max="50000">
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Includi immagini</label>
                        <div class="olo-field-hint">Aggiungi tag <code>&lt;image:image&gt;</code> nella sitemap.</div>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo esc_attr( self::OPT_SITEMAP ); ?>[include_images]" value="1" <?php checked( $opts['include_images'] ?? '1', '1' ); ?>>
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Post Type nella Sitemap -->
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon orange">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </div>
                <div>
                    <h3>Post Type nella Sitemap</h3>
                    <p>Seleziona quali tipi di contenuto includere nella sitemap.</p>
                </div>
            </div>
            <div class="olo-card-body">
                <?php foreach ( $public_pts as $pt ) : ?>
                    <div class="olo-field-row">
                        <div class="olo-field-info">
                            <label><?php echo esc_html( $pt->labels->name ); ?> <code><?php echo esc_html( $pt->name ); ?></code></label>
                        </div>
                        <label class="olo-toggle">
                            <input type="checkbox" name="<?php echo esc_attr( self::OPT_SITEMAP ); ?>[pt][<?php echo esc_attr( $pt->name ); ?>]" value="1" <?php checked( $opts['pt'][ $pt->name ] ?? '1', '1' ); ?>>
                            <span class="olo-toggle-slider"></span>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Tassonomie nella Sitemap -->
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon orange">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                </div>
                <div>
                    <h3>Tassonomie nella Sitemap</h3>
                    <p>Seleziona quali tassonomie includere nella sitemap.</p>
                </div>
            </div>
            <div class="olo-card-body">
                <?php foreach ( $public_tax as $tax ) : ?>
                    <div class="olo-field-row">
                        <div class="olo-field-info">
                            <label><?php echo esc_html( $tax->labels->name ); ?> <code><?php echo esc_html( $tax->name ); ?></code></label>
                        </div>
                        <label class="olo-toggle">
                            <input type="checkbox" name="<?php echo esc_attr( self::OPT_SITEMAP ); ?>[tax][<?php echo esc_attr( $tax->name ); ?>]" value="1" <?php checked( $opts['tax'][ $tax->name ] ?? '1', '1' ); ?>>
                            <span class="olo-toggle-slider"></span>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /* ─── Tab: Avanzate ─── */

    private function render_tab_advanced() {
        $opts = self::get( self::OPT_ADVANCED );
        $public_pts = get_post_types( [ 'public' => true ], 'objects' );
        ?>
        <!-- Robots Meta -->
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                </div>
                <div>
                    <h3>Robots Meta</h3>
                    <p>Controlla le direttive robots globali. Queste possono essere sovrascritte per singolo post/pagina.</p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Noindex — Categorie</label>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo esc_attr( self::OPT_ADVANCED ); ?>[noindex_categories]" value="1" <?php checked( $opts['noindex_categories'] ?? '', '1' ); ?>>
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Noindex — Tag</label>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo esc_attr( self::OPT_ADVANCED ); ?>[noindex_tags]" value="1" <?php checked( $opts['noindex_tags'] ?? '', '1' ); ?>>
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Noindex — Archivi autore</label>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo esc_attr( self::OPT_ADVANCED ); ?>[noindex_author]" value="1" <?php checked( $opts['noindex_author'] ?? '', '1' ); ?>>
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Noindex — Archivi per data</label>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo esc_attr( self::OPT_ADVANCED ); ?>[noindex_date]" value="1" <?php checked( $opts['noindex_date'] ?? '', '1' ); ?>>
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Noindex — Pagine di ricerca</label>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo esc_attr( self::OPT_ADVANCED ); ?>[noindex_search]" value="1" <?php checked( $opts['noindex_search'] ?? '1', '1' ); ?>>
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <?php foreach ( $public_pts as $pt ) : ?>
                    <div class="olo-field-row">
                        <div class="olo-field-info">
                            <label>Noindex — <?php echo esc_html( $pt->labels->name ); ?></label>
                        </div>
                        <label class="olo-toggle">
                            <input type="checkbox" name="<?php echo esc_attr( self::OPT_ADVANCED ); ?>[noindex_pt][<?php echo esc_attr( $pt->name ); ?>]" value="1" <?php checked( $opts['noindex_pt'][ $pt->name ] ?? '', '1' ); ?>>
                            <span class="olo-toggle-slider"></span>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Link -->
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon orange">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
                </div>
                <div>
                    <h3>Link</h3>
                    <p>Gestione attributi per i link esterni nel contenuto.</p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Nofollow link esterni</label>
                        <div class="olo-field-hint">Aggiungi <code>rel="nofollow"</code> ai link esterni nel contenuto.</div>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo esc_attr( self::OPT_ADVANCED ); ?>[nofollow_external]" value="1" <?php checked( $opts['nofollow_external'] ?? '', '1' ); ?>>
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Apri in nuova tab</label>
                        <div class="olo-field-hint">Aggiungi <code>target="_blank"</code> ai link esterni.</div>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo esc_attr( self::OPT_ADVANCED ); ?>[external_new_tab]" value="1" <?php checked( $opts['external_new_tab'] ?? '', '1' ); ?>>
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Pulizia <head> -->
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                </div>
                <div>
                    <h3>Pulizia &lt;head&gt;</h3>
                    <p>Rimuovi tag non necessari dall'header HTML per un codice più pulito.</p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Shortlink <code>rel="shortlink"</code></label>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo esc_attr( self::OPT_ADVANCED ); ?>[remove_shortlink]" value="1" <?php checked( $opts['remove_shortlink'] ?? '', '1' ); ?>>
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>RSD link <code>EditURI</code></label>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo esc_attr( self::OPT_ADVANCED ); ?>[remove_rsd]" value="1" <?php checked( $opts['remove_rsd'] ?? '', '1' ); ?>>
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Windows Live Writer link</label>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo esc_attr( self::OPT_ADVANCED ); ?>[remove_wlw]" value="1" <?php checked( $opts['remove_wlw'] ?? '', '1' ); ?>>
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Meta <code>generator</code> (versione WP)</label>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo esc_attr( self::OPT_ADVANCED ); ?>[remove_generator]" value="1" <?php checked( $opts['remove_generator'] ?? '', '1' ); ?>>
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Feed RSS extra (commenti, categorie)</label>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo esc_attr( self::OPT_ADVANCED ); ?>[remove_feed_links]" value="1" <?php checked( $opts['remove_feed_links'] ?? '', '1' ); ?>>
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Robots.txt personalizzato -->
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon orange">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div>
                    <h3>Robots.txt personalizzato</h3>
                    <p>Aggiungi righe personalizzate al file <code>robots.txt</code> virtuale di WordPress. Una regola per riga.</p>
                </div>
            </div>
            <div class="olo-card-body">
                <textarea name="<?php echo esc_attr( self::OPT_ADVANCED ); ?>[robots_txt]" class="olo-field-input" rows="8" style="min-height:120px;font-family:monospace;" placeholder="User-agent: *&#10;Disallow: /wp-admin/&#10;Allow: /wp-admin/admin-ajax.php"><?php echo esc_textarea( $opts['robots_txt'] ?? '' ); ?></textarea>
            </div>
        </div>
        <?php
    }

    /* ═══════════════════════════════════════════════════
     * META BOX PER POST/PAGINE
     * ═══════════════════════════════════════════════════ */

    public function add_seo_meta_box() {
        $post_types = get_post_types( [ 'public' => true ], 'names' );
        foreach ( $post_types as $pt ) {
            add_meta_box(
                'olo-seo-meta-box',
                'Olobuild SEO',
                [ $this, 'render_seo_meta_box' ],
                $pt,
                'normal',
                'high'
            );
        }
    }

    public function render_seo_meta_box( $post ) {
        wp_nonce_field( 'olo_seo_meta_box', 'olo_seo_nonce' );

        $seo_title    = get_post_meta( $post->ID, '_olo_seo_title', true );
        $seo_desc     = get_post_meta( $post->ID, '_olo_seo_description', true );
        $seo_keywords = get_post_meta( $post->ID, '_olo_seo_focus_keyword', true );
        $seo_canonical = get_post_meta( $post->ID, '_olo_seo_canonical', true );
        $seo_noindex  = get_post_meta( $post->ID, '_olo_seo_noindex', true );
        $seo_nofollow = get_post_meta( $post->ID, '_olo_seo_nofollow', true );
        $seo_og_title = get_post_meta( $post->ID, '_olo_seo_og_title', true );
        $seo_og_desc  = get_post_meta( $post->ID, '_olo_seo_og_description', true );
        $seo_og_image = get_post_meta( $post->ID, '_olo_seo_og_image', true );
        $seo_tw_title = get_post_meta( $post->ID, '_olo_seo_tw_title', true );
        $seo_tw_desc  = get_post_meta( $post->ID, '_olo_seo_tw_description', true );
        $seo_schema   = get_post_meta( $post->ID, '_olo_seo_schema_type', true );

        $site_name    = get_bloginfo( 'name' );
        $post_title   = $post->post_title ?: 'Titolo pagina';
        $post_url     = get_permalink( $post );
        $display_title = $seo_title ?: $post_title . ' - ' . $site_name;
        $display_desc  = $seo_desc ?: wp_trim_words( wp_strip_all_tags( $post->post_excerpt ?: $post->post_content ), 25, '...' );
        $display_url   = str_replace( [ 'http://', 'https://' ], '', $post_url );
        ?>
        <style>
            .olo-seo-metabox { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
            .olo-seo-metabox .olo-seo-tabs-nav { display: flex; gap: 0; border-bottom: 2px solid #e0e0e0; margin-bottom: 16px; }
            .olo-seo-metabox .olo-seo-tab-btn { padding: 10px 18px; border: none; background: none; cursor: pointer; font-size: 13px; font-weight: 500; color: #666; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all 0.2s; }
            .olo-seo-metabox .olo-seo-tab-btn:hover { color: #333; }
            .olo-seo-metabox .olo-seo-tab-btn.active { color: #e1474f; border-bottom-color: #e1474f; }
            /* Focus rosso Olobuild al posto del blu di wp-admin sui campi del metabox. */
            .olo-seo-metabox input:focus, .olo-seo-metabox textarea:focus, .olo-seo-metabox select:focus { border-color: #e1474f; box-shadow: 0 0 0 1px #e1474f; outline: 2px solid transparent; }
            .olo-seo-metabox .olo-seo-tab-panel { display: none; }
            .olo-seo-metabox .olo-seo-tab-panel.active { display: block; }

            .olo-seo-preview { background: #fff; border: 1px solid #dadce0; border-radius: 8px; padding: 16px; max-width: 600px; margin-bottom: 16px; }
            .olo-seo-preview-title { color: #1a0dab; font-size: 18px; line-height: 1.3; font-family: Arial, sans-serif; margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .olo-seo-preview-url { color: #006621; font-size: 13px; line-height: 1.4; margin: 2px 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .olo-seo-preview-desc { color: #545454; font-size: 13px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

            .olo-seo-field { margin-bottom: 16px; }
            .olo-seo-field label { display: block; font-weight: 600; font-size: 12px; text-transform: uppercase; color: #666; margin-bottom: 4px; letter-spacing: 0.5px; }
            .olo-seo-field input[type="text"],
            .olo-seo-field textarea { width: 100%; }
            .olo-seo-field .char-count { float: right; font-size: 11px; color: #999; margin-top: 2px; }
            .olo-seo-field .char-count.warning { color: #d63638; font-weight: 600; }
            .olo-seo-field .char-count.good { color: #00a32a; }

            .olo-seo-keyword-score { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 500; margin-top: 8px; }
            .olo-seo-keyword-score.good { background: #d4edda; color: #155724; }
            .olo-seo-keyword-score.ok { background: #fff3cd; color: #856404; }
            .olo-seo-keyword-score.bad { background: #f8d7da; color: #721c24; }

            .olo-seo-checks { margin-top: 12px; padding: 0; list-style: none; }
            .olo-seo-checks li { padding: 6px 0; font-size: 13px; display: flex; align-items: flex-start; gap: 8px; }
            .olo-seo-checks .dashicons { font-size: 16px; width: 16px; height: 16px; line-height: 16px; margin-top: 1px; }
            .olo-seo-checks .pass .dashicons { color: #00a32a; }
            .olo-seo-checks .warn .dashicons { color: #dba617; }
            .olo-seo-checks .fail .dashicons { color: #d63638; }
            .olo-seo-checks .info .dashicons { color: #2271b1; }
            .olo-seo-checks .olo-seo-riga { flex: 1 1 auto; min-width: 0; }
            .olo-seo-aiuto { display: block; color: #646970; font-size: 12px; margin-top: 2px; }
            .olo-seo-titoli { margin-top: 12px; font-size: 13px; }
            .olo-seo-titoli summary { cursor: pointer; font-weight: 600; }
            .olo-seo-titoli ul { margin: 8px 0 0; }
            .olo-seo-titoli li { margin: 0 0 4px; }
            .olo-seo-titoli code { font-size: 11px; margin-right: 6px; }
            .olo-seo-titoli .problema { color: #b45309; font-size: 12px; margin-left: 6px; }
            .olo-seo-schema-campi { border-left: 3px solid #e1474f; padding-left: 12px; margin: 0 0 16px; }
            .olo-seo-schema-campi input, .olo-seo-schema-campi select { width: 100%; max-width: 420px; }

            .olo-seo-social-preview { background: #f0f2f5; border-radius: 8px; overflow: hidden; max-width: 500px; margin-bottom: 16px; border: 1px solid #ddd; }
            .olo-seo-social-preview-img { width: 100%; height: 200px; background: #e4e6eb; display: flex; align-items: center; justify-content: center; color: #999; font-size: 13px; background-size: cover; background-position: center; }
            .olo-seo-social-preview-body { padding: 12px; }
            .olo-seo-social-preview-domain { font-size: 11px; color: #606770; text-transform: uppercase; }
            .olo-seo-social-preview-title { font-size: 15px; font-weight: 600; color: #1d2129; margin: 4px 0; }
            .olo-seo-social-preview-desc { font-size: 13px; color: #606770; }

            .olo-seo-inline-checks { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px; }
            .olo-seo-check-pill { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 12px; font-size: 11px; }
        </style>

        <div class="olo-seo-metabox">
            <div class="olo-seo-tabs-nav">
                <button type="button" class="olo-seo-tab-btn active" data-tab="seo">
                    <span class="dashicons dashicons-search" style="font-size:14px;vertical-align:middle;margin-right:4px;"></span> SEO
                </button>
                <button type="button" class="olo-seo-tab-btn" data-tab="social">
                    <span class="dashicons dashicons-share" style="font-size:14px;vertical-align:middle;margin-right:4px;"></span> Social
                </button>
                <button type="button" class="olo-seo-tab-btn" data-tab="advanced-mb">
                    <span class="dashicons dashicons-admin-generic" style="font-size:14px;vertical-align:middle;margin-right:4px;"></span> Avanzate
                </button>
            </div>

            <!-- TAB: SEO -->
            <div class="olo-seo-tab-panel active" id="olo-seo-tab-seo">
                <!-- Preview toggle Desktop / Mobile -->
                <div style="display:flex;gap:4px;margin-bottom:8px;">
                    <button type="button" class="button button-small olo-seo-device-btn active" data-device="desktop" onclick="oloSeoToggleDevice('desktop')">
                        <span class="dashicons dashicons-desktop" style="font-size:14px;vertical-align:middle;"></span> Desktop
                    </button>
                    <button type="button" class="button button-small olo-seo-device-btn" data-device="mobile" onclick="oloSeoToggleDevice('mobile')">
                        <span class="dashicons dashicons-smartphone" style="font-size:14px;vertical-align:middle;"></span> Mobile
                    </button>
                </div>

                <!-- Desktop preview -->
                <div class="olo-seo-preview" id="olo-seo-google-preview">
                    <div class="olo-seo-preview-title" id="olo-seo-prev-title"><?php echo esc_html( $display_title ); ?></div>
                    <div class="olo-seo-preview-url" id="olo-seo-prev-url"><?php echo esc_html( $display_url ); ?></div>
                    <div class="olo-seo-preview-desc" id="olo-seo-prev-desc"><?php echo esc_html( $display_desc ); ?></div>
                </div>

                <!-- Mobile preview -->
                <div class="olo-seo-preview olo-seo-mobile-preview" id="olo-seo-google-preview-mobile" style="display:none;max-width:360px;border-radius:12px;padding:12px;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                        <div style="width:26px;height:26px;border-radius:50%;background:#e4e6eb;"></div>
                        <div>
                            <div style="font-size:12px;color:#202124;font-weight:500;"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></div>
                            <div style="font-size:11px;color:#70757a;" id="olo-seo-prev-url-mobile"><?php echo esc_html( $display_url ); ?></div>
                        </div>
                    </div>
                    <div style="font-size:16px;color:#1a0dab;line-height:1.3;margin-bottom:4px;" id="olo-seo-prev-title-mobile"><?php echo esc_html( $display_title ); ?></div>
                    <div style="font-size:12px;color:#4d5156;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;" id="olo-seo-prev-desc-mobile"><?php echo esc_html( $display_desc ); ?></div>
                </div>

                <div class="olo-seo-field">
                    <label>Focus Keyword</label>
                    <input type="text" name="olo_seo_focus_keyword" id="olo-seo-keyword" value="<?php echo esc_attr( $seo_keywords ); ?>" placeholder="Es. page builder wordpress">
                    <div id="olo-seo-keyword-analysis"></div>
                </div>

                <div class="olo-seo-field">
                    <label>SEO Title <span class="char-count" id="olo-seo-title-count">0/60</span></label>
                    <input type="text" name="olo_seo_title" id="olo-seo-title" value="<?php echo esc_attr( $seo_title ); ?>" placeholder="<?php echo esc_attr( $post_title . ' - ' . $site_name ); ?>">
                </div>

                <div class="olo-seo-field">
                    <label>Meta Description <span class="char-count" id="olo-seo-desc-count">0/160</span></label>
                    <textarea name="olo_seo_description" id="olo-seo-desc" rows="3" placeholder="Descrivi questa pagina in 120-160 caratteri..."><?php echo esc_textarea( $seo_desc ); ?></textarea>
                </div>

                <div id="olo-seo-checklist"></div>
            </div>

            <!-- TAB: Social -->
            <div class="olo-seo-tab-panel" id="olo-seo-tab-social">
                <h3 style="margin-top:0;">Facebook / Open Graph</h3>
                <div class="olo-seo-social-preview" id="olo-seo-og-preview">
                    <div class="olo-seo-social-preview-img" id="olo-seo-og-prev-img">Nessuna immagine</div>
                    <div class="olo-seo-social-preview-body">
                        <div class="olo-seo-social-preview-domain"><?php echo esc_html( wp_parse_url( home_url(), PHP_URL_HOST ) ); ?></div>
                        <div class="olo-seo-social-preview-title" id="olo-seo-og-prev-title"><?php echo esc_html( $seo_og_title ?: $display_title ); ?></div>
                        <div class="olo-seo-social-preview-desc" id="olo-seo-og-prev-desc"><?php echo esc_html( $seo_og_desc ?: $display_desc ); ?></div>
                    </div>
                </div>

                <div class="olo-seo-field">
                    <label>OG Title</label>
                    <input type="text" name="olo_seo_og_title" id="olo-seo-og-title" value="<?php echo esc_attr( $seo_og_title ); ?>" placeholder="Usa il titolo SEO se vuoto">
                </div>
                <div class="olo-seo-field">
                    <label>OG Description</label>
                    <textarea name="olo_seo_og_description" id="olo-seo-og-desc" rows="2" placeholder="Usa la meta description se vuoto"><?php echo esc_textarea( $seo_og_desc ); ?></textarea>
                </div>
                <div class="olo-seo-field">
                    <label>OG Image</label>
                    <input type="text" name="olo_seo_og_image" id="olo-seo-og-image" value="<?php echo esc_attr( $seo_og_image ); ?>" class="regular-text">
                    <button type="button" class="button olo-seo-upload-btn" data-target="olo-seo-og-image">Seleziona immagine</button>
                </div>

                <hr style="margin:24px 0;">
                <h3>Twitter / X</h3>
                <div class="olo-seo-field">
                    <label>Twitter Title</label>
                    <input type="text" name="olo_seo_tw_title" value="<?php echo esc_attr( $seo_tw_title ); ?>" placeholder="Usa OG Title se vuoto">
                </div>
                <div class="olo-seo-field">
                    <label>Twitter Description</label>
                    <textarea name="olo_seo_tw_description" rows="2" placeholder="Usa OG Description se vuoto"><?php echo esc_textarea( $seo_tw_desc ); ?></textarea>
                </div>
            </div>

            <!-- TAB: Avanzate -->
            <div class="olo-seo-tab-panel" id="olo-seo-tab-advanced-mb">
                <div class="olo-seo-field">
                    <label>Canonical URL</label>
                    <input type="text" name="olo_seo_canonical" value="<?php echo esc_attr( $seo_canonical ); ?>" placeholder="<?php echo esc_attr( $post_url ); ?>">
                    <p class="description">Lascia vuoto per usare l'URL predefinito.</p>
                </div>

                <div class="olo-seo-field">
                    <label>Robots</label>
                    <label style="font-weight:normal;text-transform:none;display:inline-flex;align-items:center;gap:6px;margin-right:16px;">
                        <input type="checkbox" name="olo_seo_noindex" value="1" <?php checked( $seo_noindex, '1' ); ?>>
                        <code>noindex</code> — non indicizzare questa pagina
                    </label>
                    <label style="font-weight:normal;text-transform:none;display:inline-flex;align-items:center;gap:6px;">
                        <input type="checkbox" name="olo_seo_nofollow" value="1" <?php checked( $seo_nofollow, '1' ); ?>>
                        <code>nofollow</code> — non seguire i link
                    </label>
                </div>

                <div class="olo-seo-field">
                    <label>Schema Type</label>
                    <select name="olo_seo_schema_type" id="olo-seo-schema-type">
                        <option value="" <?php selected( $seo_schema, '' ); ?>>Automatico (predefinito)</option>
                        <option value="Article" <?php selected( $seo_schema, 'Article' ); ?>>Article</option>
                        <option value="BlogPosting" <?php selected( $seo_schema, 'BlogPosting' ); ?>>BlogPosting</option>
                        <option value="NewsArticle" <?php selected( $seo_schema, 'NewsArticle' ); ?>>NewsArticle</option>
                        <option value="WebPage" <?php selected( $seo_schema, 'WebPage' ); ?>>WebPage</option>
                        <option value="FAQPage" <?php selected( $seo_schema, 'FAQPage' ); ?>>FAQPage</option>
                        <option value="HowTo" <?php selected( $seo_schema, 'HowTo' ); ?>>HowTo</option>
                        <option value="Product" <?php selected( $seo_schema, 'Product' ); ?>>Product</option>
                        <option value="SoftwareApplication" <?php selected( $seo_schema, 'SoftwareApplication' ); ?>>SoftwareApplication</option>
                        <option value="Event" <?php selected( $seo_schema, 'Event' ); ?>>Event</option>
                        <option value="Recipe" <?php selected( $seo_schema, 'Recipe' ); ?>>Recipe</option>
                        <option value="LocalBusiness" <?php selected( $seo_schema, 'LocalBusiness' ); ?>>LocalBusiness</option>
                        <option value="none" <?php selected( $seo_schema, 'none' ); ?>>Nessuno schema</option>
                    </select>
                </div>

                <?php
                // I campi del tipo scelto (SoftwareApplication, Event), dalla fonte unica Olobuild_Seo_Head::campi_schema():
                // gli stessi della scheda Schema del builder. Si inviano solo quelli del tipo scelto.
                $campi_schema = class_exists( 'Olobuild_Seo_Head' ) ? Olobuild_Seo_Head::campi_schema() : [];
                $dati_schema  = get_post_meta( $post->ID, '_olo_seo_schema_data', true );
                $dati_schema  = is_array( $dati_schema ) ? $dati_schema : [];
                $tipi_input   = [ 'datetime' => 'datetime-local', 'number' => 'number', 'url' => 'url' ];
                foreach ( $campi_schema as $tipo_schema => $campi ) :
                    $attivo = ( $seo_schema === $tipo_schema );
                    ?>
                    <div class="olo-seo-schema-campi" data-tipo="<?php echo esc_attr( $tipo_schema ); ?>"<?php echo $attivo ? '' : ' hidden'; ?>>
                        <?php foreach ( $campi as $c ) :
                            $id_campo = 'olo-seo-sd-' . $c['key'];
                            $valore   = (string) ( $dati_schema[ $c['key'] ] ?? ( $c['default'] ?? '' ) );
                            $nome     = 'olo_seo_schema_data[' . $c['key'] . ']';
                            ?>
                            <div class="olo-seo-field"<?php if ( ! empty( $c['mostra_se'] ) ) : ?> data-mostra-se="<?php echo esc_attr( wp_json_encode( $c['mostra_se'] ) ); ?>"<?php endif; ?>>
                                <label for="<?php echo esc_attr( $id_campo ); ?>"><?php echo esc_html( $c['label'] ); ?></label>
                                <?php if ( 'select' === $c['type'] ) : ?>
                                    <select id="<?php echo esc_attr( $id_campo ); ?>" name="<?php echo esc_attr( $nome ); ?>" data-chiave="<?php echo esc_attr( $c['key'] ); ?>"<?php disabled( ! $attivo ); ?>>
                                        <?php foreach ( $c['options'] as $v => $l ) : ?>
                                            <option value="<?php echo esc_attr( $v ); ?>" <?php selected( $valore, (string) $v ); ?>><?php echo esc_html( $l ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else : ?>
                                    <input type="<?php echo esc_attr( $tipi_input[ $c['type'] ] ?? 'text' ); ?>" id="<?php echo esc_attr( $id_campo ); ?>" name="<?php echo esc_attr( $nome ); ?>" value="<?php echo esc_attr( $valore ); ?>" placeholder="<?php echo esc_attr( $c['placeholder'] ?? '' ); ?>" data-chiave="<?php echo esc_attr( $c['key'] ); ?>"<?php if ( 'number' === $c['type'] ) : ?> min="0" step="any"<?php endif; ?><?php if ( ! empty( $c['dopo'] ) ) : ?> data-dopo="<?php echo esc_attr( $c['dopo'] ); ?>"<?php endif; ?><?php disabled( ! $attivo ); ?>>
                                <?php endif; ?>
                                <?php if ( ! empty( $c['aiuto'] ) ) : ?>
                                    <p class="description"><?php echo esc_html( $c['aiuto'] ); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>

                <!-- FAQ Schema Builder -->
                <div class="olo-seo-field">
                    <label>FAQ Schema (domande e risposte)</label>
                    <p class="description" style="margin-top:0;">Domande e risposte che diventano dati strutturati di tipo FAQ, letti da motori e assistenti IA. Google mostra le FAQ arricchite nei risultati solo per siti governativi e sanitari.</p>
                    <div id="olo-seo-faq-list">
                        <?php
                        $faq_items = get_post_meta( $post->ID, '_olo_seo_faq', true );
                        if ( ! is_array( $faq_items ) ) $faq_items = [];
                        foreach ( $faq_items as $i => $faq ) :
                        ?>
                            <div class="olo-seo-faq-item" style="background:#f9f9f9;border:1px solid #ddd;border-radius:6px;padding:12px;margin-bottom:8px;position:relative;">
                                <button type="button" onclick="this.parentElement.remove()" style="position:absolute;top:8px;right:8px;background:none;border:none;cursor:pointer;color:#d63638;font-size:16px;">&times;</button>
                                <div style="margin-bottom:8px;">
                                    <label style="font-size:11px;font-weight:600;color:#666;text-transform:uppercase;display:block;margin-bottom:2px;">Domanda</label>
                                    <input type="text" name="olo_seo_faq[<?php echo esc_attr( $i ); ?>][q]" value="<?php echo esc_attr( $faq['q'] ?? '' ); ?>" style="width:100%;">
                                </div>
                                <div>
                                    <label style="font-size:11px;font-weight:600;color:#666;text-transform:uppercase;display:block;margin-bottom:2px;">Risposta</label>
                                    <textarea name="olo_seo_faq[<?php echo esc_attr( $i ); ?>][a]" rows="2" style="width:100%;"><?php echo esc_textarea( $faq['a'] ?? '' ); ?></textarea>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="button" onclick="oloSeoAddFaq()">+ Aggiungi FAQ</button>
                </div>

                <!-- Breadcrumb Shortcode info -->
                <div class="olo-seo-field" style="margin-top:16px;">
                    <label>Breadcrumb Shortcode</label>
                    <p class="description">Usa <code>[olo_breadcrumb]</code> per inserire breadcrumb SEO-friendly con markup JSON-LD. Parametri opzionali: <code>separator="&raquo;"</code>, <code>class="my-breadcrumb"</code></p>
                </div>
            </div>
        </div>

        <script>
        /* FAQ builder */
        var oloFaqIndex = <?php echo (int) count( $faq_items ); ?>;
        function oloSeoAddFaq() {
            var list = document.getElementById('olo-seo-faq-list');
            var html = '<div class="olo-seo-faq-item" style="background:#f9f9f9;border:1px solid #ddd;border-radius:6px;padding:12px;margin-bottom:8px;position:relative;">' +
                '<button type="button" onclick="this.parentElement.remove()" style="position:absolute;top:8px;right:8px;background:none;border:none;cursor:pointer;color:#d63638;font-size:16px;">&times;</button>' +
                '<div style="margin-bottom:8px;"><label style="font-size:11px;font-weight:600;color:#666;text-transform:uppercase;display:block;margin-bottom:2px;">Domanda</label>' +
                '<input type="text" name="olo_seo_faq[' + oloFaqIndex + '][q]" style="width:100%;" placeholder="Es. Come funziona...?"></div>' +
                '<div><label style="font-size:11px;font-weight:600;color:#666;text-transform:uppercase;display:block;margin-bottom:2px;">Risposta</label>' +
                '<textarea name="olo_seo_faq[' + oloFaqIndex + '][a]" rows="2" style="width:100%;" placeholder="Spiegazione..."></textarea></div></div>';
            list.insertAdjacentHTML('beforeend', html);
            oloFaqIndex++;
        }

        /* Device toggle (desktop/mobile preview) */
        function oloSeoToggleDevice(device) {
            document.querySelectorAll('.olo-seo-device-btn').forEach(function(b){ b.classList.remove('active'); });
            document.querySelector('.olo-seo-device-btn[data-device="' + device + '"]').classList.add('active');
            document.getElementById('olo-seo-google-preview').style.display = device === 'desktop' ? '' : 'none';
            document.getElementById('olo-seo-google-preview-mobile').style.display = device === 'mobile' ? '' : 'none';
        }
        (function(){
            /* Tab switching */
            var btns = document.querySelectorAll('.olo-seo-metabox .olo-seo-tab-btn');
            btns.forEach(function(btn){
                btn.addEventListener('click', function(e){
                    e.preventDefault();
                    btns.forEach(function(b){ b.classList.remove('active'); });
                    btn.classList.add('active');
                    document.querySelectorAll('.olo-seo-metabox .olo-seo-tab-panel').forEach(function(p){ p.classList.remove('active'); });
                    document.getElementById('olo-seo-tab-' + btn.dataset.tab).classList.add('active');
                });
            });

            /* Google preview + char counters */
            var titleInput = document.getElementById('olo-seo-title');
            var descInput  = document.getElementById('olo-seo-desc');
            var titleCount = document.getElementById('olo-seo-title-count');
            var descCount  = document.getElementById('olo-seo-desc-count');
            var prevTitle  = document.getElementById('olo-seo-prev-title');
            var prevDesc   = document.getElementById('olo-seo-prev-desc');
            var keywordInput = document.getElementById('olo-seo-keyword');

            var defaultTitle = <?php echo wp_json_encode( $post_title . ' - ' . $site_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON for inline script generated by wp_json_encode() ?>;
            var defaultDesc  = <?php echo wp_json_encode( $display_desc ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON for inline script generated by wp_json_encode() ?>;

            function updatePreview() {
                var t = titleInput.value || defaultTitle;
                var d = descInput.value || defaultDesc;
                prevTitle.textContent = t;
                prevDesc.textContent = d;

                // Sync mobile preview
                var mTitle = document.getElementById('olo-seo-prev-title-mobile');
                var mDesc = document.getElementById('olo-seo-prev-desc-mobile');
                if (mTitle) mTitle.textContent = t;
                if (mDesc) mDesc.textContent = d;

                var tl = titleInput.value.length;
                titleCount.textContent = tl + '/60';
                titleCount.className = 'char-count' + (tl > 60 ? ' warning' : (tl >= 30 ? ' good' : ''));

                var dl = descInput.value.length;
                descCount.textContent = dl + '/160';
                descCount.className = 'char-count' + (dl > 160 ? ' warning' : (dl >= 120 ? ' good' : ''));

                runAnalysis();
            }

            /* Analisi della pagina: la fa il server sulla pagina resa dal PHP (Olobuild_Seo_Analisi), la stessa del
               builder. Prima si leggeva il contenuto dell'editor classico, vuoto nelle pagine fatte col builder e
               nell'editor a blocchi. Si mandano i campi SEO di questo riquadro anche se non ancora salvati. */
            var oloAnalisi = {
                url: <?php echo wp_json_encode( rest_url( 'olobuild/v1/seo/analisi' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON for inline script generated by wp_json_encode() ?>,
                nonce: <?php echo wp_json_encode( wp_create_nonce( 'wp_rest' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON for inline script generated by wp_json_encode() ?>,
                postId: <?php echo (int) $post->ID; ?>,
                testi: <?php echo wp_json_encode( [ // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON for inline script generated by wp_json_encode()
                    'correggere' => __( 'da correggere', 'olobuild' ),
                    'migliorare' => __( 'da migliorare', 'olobuild' ),
                    'posto'      => __( 'Tutto a posto', 'olobuild' ),
                    'superati'   => __( 'superati', 'olobuild' ),
                    'analisi'    => __( 'Analisi della pagina…', 'olobuild' ),
                    'fallita'    => __( 'Analisi non riuscita', 'olobuild' ),
                    'salvata'    => __( 'Si analizza la pagina salvata: dopo aver cambiato il contenuto, aggiorna la pagina.', 'olobuild' ),
                    'titoli'     => __( 'Titoli della pagina', 'olobuild' ),
                    'salto'      => __( 'salta un livello', 'olobuild' ),
                    'vuoto'      => __( 'vuoto', 'olobuild' ),
                    'nessuno'    => __( 'Nessun titolo nella pagina.', 'olobuild' ),
                ] ); ?>,
                timer: null,
                richiesta: 0
            };
            function oloSeoEsc(s) { var d = document.createElement('div'); d.textContent = s == null ? '' : String(s); return d.innerHTML; }
            function oloSeoCampo(nome) {
                var el = document.querySelector('.olo-seo-metabox [name="' + nome + '"]');
                if (!el) return '';
                return el.type === 'checkbox' ? el.checked : el.value;
            }
            function oloSeoDatiSchema() {
                var out = {};
                document.querySelectorAll('.olo-seo-metabox [name^="olo_seo_schema_data["]').forEach(function (el) {
                    if (!el.disabled) out[el.name.slice(20, -1)] = el.value;
                });
                return out;
            }

            function runAnalysis() {
                clearTimeout(oloAnalisi.timer);
                oloAnalisi.timer = setTimeout(eseguiAnalisi, 700);
            }
            function eseguiAnalisi() {
                var n = ++oloAnalisi.richiesta;
                var lista = document.getElementById('olo-seo-checklist');
                if (!lista.innerHTML) lista.innerHTML = '<p class="description">' + oloSeoEsc(oloAnalisi.testi.analisi) + '</p>';
                var corpo = {
                    post_id: oloAnalisi.postId,
                    seo: {
                        title: titleInput.value,
                        description: descInput.value,
                        focus_keyword: keywordInput.value,
                        noindex: !!oloSeoCampo('olo_seo_noindex'),
                        canonical: oloSeoCampo('olo_seo_canonical'),
                        og_image: oloSeoCampo('olo_seo_og_image'),
                        schema_type: oloSeoCampo('olo_seo_schema_type'),
                        schema_data: oloSeoDatiSchema()
                    }
                };
                fetch(oloAnalisi.url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': oloAnalisi.nonce },
                    body: JSON.stringify(corpo)
                }).then(function (r) {
                    return r.json().then(function (j) { return { ok: r.ok, j: j }; });
                }).then(function (res) {
                    if (n !== oloAnalisi.richiesta) return;
                    if (!res.ok) throw new Error((res.j && res.j.message) || 'HTTP');
                    disegnaAnalisi(res.j);
                }).catch(function (e) {
                    if (n !== oloAnalisi.richiesta) return;
                    lista.innerHTML = '<p class="description">' + oloSeoEsc(oloAnalisi.testi.fallita + ': ' + (e && e.message ? e.message : e)) + '</p>';
                });
            }
            function disegnaAnalisi(a) {
                var T = oloAnalisi.testi;
                var r = a.riepilogo || {};
                var pezzi = [];
                if (r.errore) pezzi.push(r.errore + ' ' + T.correggere);
                if (r.attenzione) pezzi.push(r.attenzione + ' ' + T.migliorare);
                if (!pezzi.length) pezzi.push(T.posto);
                var cls = r.errore ? 'bad' : (r.attenzione ? 'ok' : 'good');
                document.getElementById('olo-seo-keyword-analysis').innerHTML =
                    '<span class="olo-seo-keyword-score ' + cls + '">' + oloSeoEsc(pezzi.join(' · ')) + '</span>' +
                    ' <span class="description">' + oloSeoEsc((r.ok || 0) + ' ' + T.superati) + '</span>';

                var ordine = { errore: 0, attenzione: 1, info: 2, ok: 3 };
                var classi = { errore: 'fail', attenzione: 'warn', info: 'info', ok: 'pass' };
                var icone = { errore: 'dismiss', attenzione: 'warning', info: 'info-outline', ok: 'yes-alt' };
                var controlli = (a.controlli || []).slice().sort(function (x, y) { return ordine[x.esito] - ordine[y.esito]; });
                var html = '<p class="description">' + oloSeoEsc(T.salvata) + '</p><ul class="olo-seo-checks">';
                controlli.forEach(function (c) {
                    html += '<li class="' + classi[c.esito] + '"><span class="dashicons dashicons-' + icone[c.esito] + '"></span><span class="olo-seo-riga">' + oloSeoEsc(c.testo) +
                        (c.aiuto && c.esito !== 'ok' ? '<span class="olo-seo-aiuto">' + oloSeoEsc(c.aiuto) + '</span>' : '') + '</span></li>';
                });
                html += '</ul>';

                var titoli = a.titoli || [];
                html += '<details class="olo-seo-titoli"><summary>' + oloSeoEsc(T.titoli + ' (' + titoli.length + ')') + '</summary>';
                if (!titoli.length) {
                    html += '<p class="description">' + oloSeoEsc(T.nessuno) + '</p>';
                } else {
                    html += '<ul>';
                    titoli.forEach(function (t) {
                        html += '<li style="padding-left:' + ((t.livello - 1) * 14) + 'px"><code>H' + t.livello + '</code>' + oloSeoEsc(t.testo || '—') +
                            (t.problema ? '<span class="problema">' + oloSeoEsc(T[t.problema] || t.problema) + '</span>' : '') + '</li>';
                    });
                    html += '</ul>';
                }
                html += '</details>';
                document.getElementById('olo-seo-checklist').innerHTML = html;
            }

            /* Campi del tipo di dati strutturati: si vedono e si inviano solo quelli del tipo scelto. */
            var tipoSchema = document.getElementById('olo-seo-schema-type');
            function aggiornaCampiSchema() {
                var tipo = tipoSchema ? tipoSchema.value : '';
                document.querySelectorAll('.olo-seo-schema-campi').forEach(function (g) {
                    var attivo = g.dataset.tipo === tipo;
                    g.hidden = !attivo;
                    g.querySelectorAll('input, select').forEach(function (el) { el.disabled = !attivo; });
                    g.querySelectorAll('[data-mostra-se]').forEach(function (f) {
                        var cond = {};
                        try { cond = JSON.parse(f.dataset.mostraSe) || {}; } catch (e) {}
                        var visibile = Object.keys(cond).every(function (k) {
                            var el = g.querySelector('[data-chiave="' + k + '"]');
                            return cond[k].indexOf(el ? el.value : '') !== -1;
                        });
                        f.style.display = visibile ? '' : 'none';
                    });
                    // La fine non prima dell'inizio.
                    g.querySelectorAll('[data-dopo]').forEach(function (el) {
                        var da = g.querySelector('[data-chiave="' + el.dataset.dopo + '"]');
                        el.min = da ? da.value : '';
                    });
                });
            }
            if (tipoSchema) tipoSchema.addEventListener('change', function () { aggiornaCampiSchema(); runAnalysis(); });
            document.querySelectorAll('.olo-seo-schema-campi input, .olo-seo-schema-campi select, .olo-seo-metabox [name="olo_seo_noindex"], .olo-seo-metabox [name="olo_seo_canonical"], .olo-seo-metabox [name="olo_seo_og_image"]').forEach(function (el) {
                el.addEventListener('change', function () { aggiornaCampiSchema(); runAnalysis(); });
            });
            aggiornaCampiSchema();

            titleInput.addEventListener('input', updatePreview);
            descInput.addEventListener('input', updatePreview);
            keywordInput.addEventListener('input', updatePreview);
            updatePreview();

            /* OG Preview */
            var ogTitleInput = document.getElementById('olo-seo-og-title');
            var ogDescInput  = document.getElementById('olo-seo-og-desc');
            var ogImageInput = document.getElementById('olo-seo-og-image');
            if (ogTitleInput) {
                ogTitleInput.addEventListener('input', function(){
                    document.getElementById('olo-seo-og-prev-title').textContent = ogTitleInput.value || titleInput.value || defaultTitle;
                });
                ogDescInput.addEventListener('input', function(){
                    document.getElementById('olo-seo-og-prev-desc').textContent = ogDescInput.value || descInput.value || defaultDesc;
                });
                ogImageInput.addEventListener('change', function(){
                    var imgEl = document.getElementById('olo-seo-og-prev-img');
                    if (ogImageInput.value) {
                        imgEl.style.backgroundImage = 'url(' + ogImageInput.value + ')';
                        imgEl.textContent = '';
                    } else {
                        imgEl.style.backgroundImage = '';
                        imgEl.textContent = 'Nessuna immagine';
                    }
                });
                if (ogImageInput.value) {
                    var imgEl = document.getElementById('olo-seo-og-prev-img');
                    imgEl.style.backgroundImage = 'url(' + ogImageInput.value + ')';
                    imgEl.textContent = '';
                }
            }

            /* Media upload buttons */
            document.querySelectorAll('.olo-seo-upload-btn').forEach(function(btn){
                btn.addEventListener('click', function(e){
                    e.preventDefault();
                    var targetId = btn.dataset.target;
                    var input = document.getElementById(targetId);
                    if (!input) return;
                    var frame = wp.media({ title: 'Seleziona immagine', multiple: false, library: { type: 'image' } });
                    frame.on('select', function(){
                        var url = frame.state().get('selection').first().toJSON().url;
                        input.value = url;
                        input.dispatchEvent(new Event('change'));
                    });
                    frame.open();
                });
            });
        })();
        </script>
        <?php
    }

    public function save_seo_meta_box( $post_id, $post ) {
        if ( ! isset( $_POST['olo_seo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['olo_seo_nonce'] ) ), 'olo_seo_meta_box' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $fields = [
            'olo_seo_title'          => '_olo_seo_title',
            'olo_seo_description'    => '_olo_seo_description',
            'olo_seo_focus_keyword'  => '_olo_seo_focus_keyword',
            'olo_seo_canonical'      => '_olo_seo_canonical',
            'olo_seo_noindex'        => '_olo_seo_noindex',
            'olo_seo_nofollow'       => '_olo_seo_nofollow',
            'olo_seo_og_title'       => '_olo_seo_og_title',
            'olo_seo_og_description' => '_olo_seo_og_description',
            'olo_seo_og_image'       => '_olo_seo_og_image',
            'olo_seo_tw_title'       => '_olo_seo_tw_title',
            'olo_seo_tw_description' => '_olo_seo_tw_description',
            'olo_seo_schema_type'    => '_olo_seo_schema_type',
        ];

        // Nonce verificato sopra (wp_verify_nonce 'olo_seo_meta_box') + current_user_can edit_post.
        foreach ( $fields as $post_key => $meta_key ) {
            $value = sanitize_text_field( wp_unslash( $_POST[ $post_key ] ?? '' ) );
            if ( $value ) {
                update_post_meta( $post_id, $meta_key, $value );
            } else {
                delete_post_meta( $post_id, $meta_key );
            }
        }

        // JSON-LD personalizzato: il riquadro non ha il campo (sta nella scheda Schema del builder). Si tocca solo
        // se arriva: prima salvare la pagina dall'editor di WordPress cancellava quello scritto nel builder.
        if ( isset( $_POST['olo_seo_extra_jsonld'] ) ) {
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- valore unslashato qui e sanitizzato sotto con wp_kses_post() (preserva la struttura JSON-LD) prima di update_post_meta; nonce verificato sopra.
            $extra = wp_unslash( $_POST['olo_seo_extra_jsonld'] );
            if ( is_string( $extra ) && trim( $extra ) !== '' ) {
                update_post_meta( $post_id, '_olo_seo_extra_jsonld', wp_kses_post( $extra ) );
            } else {
                delete_post_meta( $post_id, '_olo_seo_extra_jsonld' );
            }
        }

        // I campi del tipo di dati strutturati scelto (Olobuild_Seo_Head::campi_schema()): arrivano solo quelli del
        // tipo scelto e si cambiano solo quelli; i campi degli altri tipi restano com'erano.
        $tipo_schema  = sanitize_text_field( wp_unslash( $_POST['olo_seo_schema_type'] ?? '' ) );
        $campi_schema = class_exists( 'Olobuild_Seo_Head' ) ? Olobuild_Seo_Head::campi_schema() : [];
        if ( isset( $campi_schema[ $tipo_schema ] ) ) {
            $dati = get_post_meta( $post_id, '_olo_seo_schema_data', true );
            $dati = is_array( $dati ) ? $dati : [];
            foreach ( $campi_schema[ $tipo_schema ] as $c ) {
                unset( $dati[ $c['key'] ] );
            }
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- array grezzo: ogni chiave nota è sanitizzata col suo formato in Olobuild_Seo_Head::sanitizza_dati_schema(); nonce verificato sopra.
            $inviati = ( isset( $_POST['olo_seo_schema_data'] ) && is_array( $_POST['olo_seo_schema_data'] ) ) ? wp_unslash( $_POST['olo_seo_schema_data'] ) : [];
            $pulito  = Olobuild_Seo_Head::sanitizza_dati_schema( array_merge( $dati, $inviati ) );
            if ( $pulito['dati'] ) {
                update_post_meta( $post_id, '_olo_seo_schema_data', $pulito['dati'] );
            } else {
                delete_post_meta( $post_id, '_olo_seo_schema_data' );
            }
        }

        // FAQ Schema data — nonce verificato sopra ('olo_seo_meta_box') + current_user_can edit_post.
        $faq_raw = isset( $_POST['olo_seo_faq'] ) ? wp_unslash( $_POST['olo_seo_faq'] ) : []; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- array grezzo: ogni elemento (q/a) sanitizzato singolarmente sotto con sanitize_text_field/sanitize_textarea_field.
        if ( is_array( $faq_raw ) && ! empty( $faq_raw ) ) {
            $faq_clean = [];
            foreach ( $faq_raw as $item ) {
                $q = sanitize_text_field( $item['q'] ?? '' );
                $a = sanitize_textarea_field( $item['a'] ?? '' );
                if ( $q && $a ) {
                    $faq_clean[] = [ 'q' => $q, 'a' => $a ];
                }
            }
            if ( ! empty( $faq_clean ) ) {
                update_post_meta( $post_id, '_olo_seo_faq', $faq_clean );
            } else {
                delete_post_meta( $post_id, '_olo_seo_faq' );
            }
        } else {
            delete_post_meta( $post_id, '_olo_seo_faq' );
        }
    }

    /* ═══════════════════════════════════════════════════
     * COLONNA SEO nella lista post
     * ═══════════════════════════════════════════════════ */

    public function add_seo_column( $columns ) {
        $columns['olo_seo'] = 'SEO';
        return $columns;
    }

    public function render_seo_column( $column, $post_id ) {
        if ( $column !== 'olo_seo' ) {
            return;
        }

        $title = get_post_meta( $post_id, '_olo_seo_title', true );
        $desc  = get_post_meta( $post_id, '_olo_seo_description', true );
        $kw    = get_post_meta( $post_id, '_olo_seo_focus_keyword', true );

        $score = 0;
        $total = 3;
        if ( $title ) $score++;
        if ( $desc )  $score++;
        if ( $kw )    $score++;

        if ( $score === 0 ) {
            echo '<span style="color:#999;" title="Nessun dato SEO configurato">—</span>';
        } else {
            $color = $score >= 3 ? '#00a32a' : ( $score >= 2 ? '#dba617' : '#d63638' );
            echo '<span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:' . esc_attr( $color ) . ';" title="' . esc_attr( $score . '/' . $total . ' campi SEO compilati' ) . '"></span>';
        }
    }
}
