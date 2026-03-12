<?php
/**
 * Olo_Seo_Settings — Pagina impostazioni SEO + Meta box per post/pagine.
 *
 * Ispirato a Rank Math / Yoast: titoli, meta, social, schema, sitemap, robots.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Seo_Settings {

    private static $instance = null;

    /** Option keys */
    const OPT_GENERAL   = 'olo_seo_general';
    const OPT_TITLES    = 'olo_seo_titles';
    const OPT_SOCIAL    = 'olo_seo_social';
    const OPT_LOCAL     = 'olo_seo_local_business';
    const OPT_WEBMASTER = 'olo_seo_webmaster';
    const OPT_ADVANCED  = 'olo_seo_advanced';
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
        add_submenu_page(
            'olobuilder',
            __( 'SEO', 'olobuilder' ),
            __( 'SEO', 'olobuilder' ),
            'manage_options',
            'olo-seo',
            [ $this, 'render_page' ]
        );
    }

    public function enqueue_admin_assets( $hook ) {
        // Solo nella pagina SEO e negli editor
        if ( $hook === 'olobuild_page_olo-seo' ) {
            wp_enqueue_media();
            wp_enqueue_style( 'olo-seo-admin', OLO_URL . 'assets/css/seo-admin.css', [], OLO_VERSION );
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
            register_setting( 'olo_seo_group', $opt, [
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
                if ( strpos( $k, 'verification' ) !== false ) {
                    $clean[ $k ] = sanitize_text_field( $v );
                } else {
                    $clean[ $k ] = sanitize_text_field( $v );
                }
            }
        }
        return $clean;
    }

    /* ─── Helpers per opzioni ─── */

    public static function get( $option_key, $field = null, $default = '' ) {
        $opts = get_option( $option_key, [] );
        if ( ! is_array( $opts ) ) {
            $opts = [];
        }
        if ( $field === null ) {
            return $opts;
        }
        return $opts[ $field ] ?? $default;
    }

    /* ═══════════════════════════════════════════════════
     * RENDER PAGINA SETTINGS
     * ═══════════════════════════════════════════════════ */

    public function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $active_tab = sanitize_text_field( $_GET['tab'] ?? 'titles' );

        $tabs = [
            'titles'    => 'Titoli &amp; Meta',
            'social'    => 'Social',
            'schema'    => 'Schema / Local SEO',
            'webmaster' => 'Webmaster Tools',
            'sitemap'   => 'Sitemap',
            'advanced'  => 'Avanzate',
        ];

        ?>
        <div class="wrap olo-seo-wrap">
            <h1>
                <img src="<?php echo esc_url( OLO_URL . 'assets/img/ob-menu.png' ); ?>" style="width:24px;height:24px;vertical-align:middle;margin-right:8px;" alt="">
                Olobuild SEO
            </h1>

            <nav class="nav-tab-wrapper olo-seo-tabs">
                <?php foreach ( $tabs as $slug => $label ) : ?>
                    <a
                        href="<?php echo esc_url( admin_url( 'admin.php?page=olo-seo&tab=' . $slug ) ); ?>"
                        class="nav-tab <?php echo $active_tab === $slug ? 'nav-tab-active' : ''; ?>"
                    >
                        <?php echo $this->tab_icon( $slug ); ?>
                        <?php echo $label; ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <form method="post" action="options.php" class="olo-seo-form">
                <?php
                settings_fields( 'olo_seo_group' );
                $this->{'render_tab_' . $active_tab}();
                submit_button( 'Salva Impostazioni' );
                ?>
            </form>
        </div>
        <?php
    }

    private function tab_icon( $slug ) {
        $icons = [
            'titles'    => '<span class="dashicons dashicons-edit" style="font-size:16px;line-height:1.8;margin-right:4px;"></span>',
            'social'    => '<span class="dashicons dashicons-share" style="font-size:16px;line-height:1.8;margin-right:4px;"></span>',
            'schema'    => '<span class="dashicons dashicons-admin-home" style="font-size:16px;line-height:1.8;margin-right:4px;"></span>',
            'webmaster' => '<span class="dashicons dashicons-admin-tools" style="font-size:16px;line-height:1.8;margin-right:4px;"></span>',
            'sitemap'   => '<span class="dashicons dashicons-networking" style="font-size:16px;line-height:1.8;margin-right:4px;"></span>',
            'advanced'  => '<span class="dashicons dashicons-admin-generic" style="font-size:16px;line-height:1.8;margin-right:4px;"></span>',
        ];
        return $icons[ $slug ] ?? '';
    }

    /* ─── Tab: Titoli & Meta ─── */

    private function render_tab_titles() {
        $opts = self::get( self::OPT_TITLES );
        $sep  = $opts['separator'] ?? '-';

        $separators = [ '-', '–', '—', '|', '·', '/', '»', '«', ':', '•' ];
        ?>
        <div class="olo-seo-section">
            <h2>Formato Titoli</h2>
            <p class="description">Definisci il formato predefinito dei titoli per ogni tipo di contenuto. Variabili disponibili: <code>%title%</code>, <code>%sitename%</code>, <code>%sep%</code>, <code>%tagline%</code>, <code>%category%</code>, <code>%tag%</code>, <code>%search_query%</code>, <code>%page%</code>, <code>%date%</code>, <code>%author%</code></p>

            <table class="form-table olo-seo-table">
                <tr>
                    <th>Separatore</th>
                    <td>
                        <div class="olo-seo-separator-picker">
                            <?php foreach ( $separators as $s ) : ?>
                                <label class="olo-seo-sep-option <?php echo $sep === $s ? 'active' : ''; ?>">
                                    <input type="radio" name="<?php echo self::OPT_TITLES; ?>[separator]" value="<?php echo esc_attr( $s ); ?>" <?php checked( $sep, $s ); ?>>
                                    <span><?php echo esc_html( $s ); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>
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
                    $is_desc = strpos( $key, '_desc' ) !== false;
                    ?>
                    <tr>
                        <th><?php echo esc_html( $info[0] ); ?></th>
                        <td>
                            <?php if ( $is_desc ) : ?>
                                <textarea name="<?php echo self::OPT_TITLES; ?>[<?php echo $key; ?>]" class="large-text" rows="2" placeholder="<?php echo esc_attr( $info[1] ); ?>"><?php echo esc_textarea( $val ); ?></textarea>
                            <?php else : ?>
                                <input type="text" name="<?php echo self::OPT_TITLES; ?>[<?php echo $key; ?>]" value="<?php echo esc_attr( $val ); ?>" class="large-text" placeholder="<?php echo esc_attr( $info[1] ); ?>">
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="olo-seo-section">
            <h2>Knowledge Graph</h2>
            <p class="description">Queste informazioni vengono usate per il markup JSON-LD del Knowledge Graph di Google.</p>
            <table class="form-table olo-seo-table">
                <tr>
                    <th>Tipo entità</th>
                    <td>
                        <select name="<?php echo self::OPT_TITLES; ?>[kg_type]">
                            <option value="Organization" <?php selected( $opts['kg_type'] ?? 'Organization', 'Organization' ); ?>>Organizzazione</option>
                            <option value="Person" <?php selected( $opts['kg_type'] ?? '', 'Person' ); ?>>Persona</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Nome</th>
                    <td><input type="text" name="<?php echo self::OPT_TITLES; ?>[kg_name]" value="<?php echo esc_attr( $opts['kg_name'] ?? get_bloginfo( 'name' ) ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th>Logo / Foto (URL)</th>
                    <td>
                        <input type="text" id="olo-seo-kg-logo" name="<?php echo self::OPT_TITLES; ?>[kg_logo]" value="<?php echo esc_attr( $opts['kg_logo'] ?? '' ); ?>" class="regular-text">
                        <button type="button" class="button olo-seo-upload-btn" data-target="olo-seo-kg-logo">Seleziona</button>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }

    /* ─── Tab: Social ─── */

    private function render_tab_social() {
        $opts = self::get( self::OPT_SOCIAL );
        ?>
        <div class="olo-seo-section">
            <h2>Profili Social</h2>
            <p class="description">Usati nel markup JSON-LD <code>sameAs</code> per collegare il sito ai profili social.</p>
            <table class="form-table olo-seo-table">
                <?php
                $social_fields = [
                    'facebook_url'   => [ 'Facebook', 'https://facebook.com/...', 'dashicons-facebook-alt' ],
                    'twitter_user'   => [ 'X (Twitter)', '@username', 'dashicons-twitter' ],
                    'instagram_url'  => [ 'Instagram', 'https://instagram.com/...', 'dashicons-instagram' ],
                    'linkedin_url'   => [ 'LinkedIn', 'https://linkedin.com/...', 'dashicons-linkedin' ],
                    'youtube_url'    => [ 'YouTube', 'https://youtube.com/...', 'dashicons-video-alt3' ],
                    'pinterest_url'  => [ 'Pinterest', 'https://pinterest.com/...', 'dashicons-pinterest' ],
                    'tiktok_url'     => [ 'TikTok', 'https://tiktok.com/@...', 'dashicons-share' ],
                ];
                foreach ( $social_fields as $key => $info ) :
                    ?>
                    <tr>
                        <th>
                            <span class="dashicons <?php echo esc_attr( $info[2] ); ?>" style="margin-right:4px;color:#666;"></span>
                            <?php echo esc_html( $info[0] ); ?>
                        </th>
                        <td><input type="text" name="<?php echo self::OPT_SOCIAL; ?>[<?php echo $key; ?>]" value="<?php echo esc_attr( $opts[ $key ] ?? '' ); ?>" class="regular-text" placeholder="<?php echo esc_attr( $info[1] ); ?>"></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="olo-seo-section">
            <h2>Open Graph — Impostazioni Predefinite</h2>
            <table class="form-table olo-seo-table">
                <tr>
                    <th>Immagine OG predefinita</th>
                    <td>
                        <input type="text" id="olo-seo-og-default" name="<?php echo self::OPT_SOCIAL; ?>[og_default_image]" value="<?php echo esc_attr( $opts['og_default_image'] ?? '' ); ?>" class="regular-text">
                        <button type="button" class="button olo-seo-upload-btn" data-target="olo-seo-og-default">Seleziona</button>
                        <?php if ( ! empty( $opts['og_default_image'] ) ) : ?>
                            <br><img src="<?php echo esc_url( $opts['og_default_image'] ); ?>" style="max-width:200px;margin-top:8px;border-radius:4px;">
                        <?php endif; ?>
                        <p class="description">Usata quando un post/pagina non ha immagine in evidenza. Dimensione consigliata: 1200×630px.</p>
                    </td>
                </tr>
                <tr>
                    <th>Facebook App ID</th>
                    <td><input type="text" name="<?php echo self::OPT_SOCIAL; ?>[fb_app_id]" value="<?php echo esc_attr( $opts['fb_app_id'] ?? '' ); ?>" class="regular-text" placeholder="Es. 123456789012345"></td>
                </tr>
                <tr>
                    <th>Twitter Card Type</th>
                    <td>
                        <select name="<?php echo self::OPT_SOCIAL; ?>[twitter_card_type]">
                            <option value="summary_large_image" <?php selected( $opts['twitter_card_type'] ?? 'summary_large_image', 'summary_large_image' ); ?>>Summary con immagine grande</option>
                            <option value="summary" <?php selected( $opts['twitter_card_type'] ?? '', 'summary' ); ?>>Summary</option>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }

    /* ─── Tab: Schema / Local SEO ─── */

    private function render_tab_schema() {
        $opts = self::get( self::OPT_LOCAL );
        ?>
        <div class="olo-seo-section">
            <h2>Local Business / Organizzazione</h2>
            <p class="description">Schema markup LocalBusiness per Google. Se il nome è vuoto, questo schema non viene generato.</p>
            <table class="form-table olo-seo-table">
                <tr>
                    <th>Tipo attività</th>
                    <td>
                        <select name="<?php echo self::OPT_LOCAL; ?>[type]">
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
                    </td>
                </tr>
                <tr>
                    <th>Nome attività</th>
                    <td><input type="text" name="<?php echo self::OPT_LOCAL; ?>[name]" value="<?php echo esc_attr( $opts['name'] ?? '' ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th>Descrizione</th>
                    <td><textarea name="<?php echo self::OPT_LOCAL; ?>[description]" class="large-text" rows="2"><?php echo esc_textarea( $opts['description'] ?? '' ); ?></textarea></td>
                </tr>
                <tr>
                    <th>Via / Indirizzo</th>
                    <td><input type="text" name="<?php echo self::OPT_LOCAL; ?>[address][street]" value="<?php echo esc_attr( $opts['address']['street'] ?? '' ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th>Città</th>
                    <td><input type="text" name="<?php echo self::OPT_LOCAL; ?>[address][city]" value="<?php echo esc_attr( $opts['address']['city'] ?? '' ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th>Provincia / Stato</th>
                    <td><input type="text" name="<?php echo self::OPT_LOCAL; ?>[address][state]" value="<?php echo esc_attr( $opts['address']['state'] ?? '' ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th>CAP</th>
                    <td><input type="text" name="<?php echo self::OPT_LOCAL; ?>[address][zip]" value="<?php echo esc_attr( $opts['address']['zip'] ?? '' ); ?>" class="small-text"></td>
                </tr>
                <tr>
                    <th>Paese</th>
                    <td><input type="text" name="<?php echo self::OPT_LOCAL; ?>[address][country]" value="<?php echo esc_attr( $opts['address']['country'] ?? 'IT' ); ?>" class="small-text" placeholder="IT"></td>
                </tr>
                <tr>
                    <th>Telefono</th>
                    <td><input type="text" name="<?php echo self::OPT_LOCAL; ?>[phone]" value="<?php echo esc_attr( $opts['phone'] ?? '' ); ?>" class="regular-text" placeholder="+39 ..."></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><input type="email" name="<?php echo self::OPT_LOCAL; ?>[email]" value="<?php echo esc_attr( $opts['email'] ?? '' ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th>Coordinate GPS</th>
                    <td>
                        <input type="text" name="<?php echo self::OPT_LOCAL; ?>[geo_lat]" value="<?php echo esc_attr( $opts['geo_lat'] ?? '' ); ?>" class="small-text" placeholder="Lat"> &nbsp;
                        <input type="text" name="<?php echo self::OPT_LOCAL; ?>[geo_lng]" value="<?php echo esc_attr( $opts['geo_lng'] ?? '' ); ?>" class="small-text" placeholder="Lng">
                    </td>
                </tr>
                <tr>
                    <th>Fascia di prezzo</th>
                    <td>
                        <select name="<?php echo self::OPT_LOCAL; ?>[price_range]">
                            <option value="" <?php selected( $opts['price_range'] ?? '', '' ); ?>>— Non specificato —</option>
                            <option value="$" <?php selected( $opts['price_range'] ?? '', '$' ); ?>>$ — Economico</option>
                            <option value="$$" <?php selected( $opts['price_range'] ?? '', '$$' ); ?>>$$ — Moderato</option>
                            <option value="$$$" <?php selected( $opts['price_range'] ?? '', '$$$' ); ?>>$$$ — Costoso</option>
                            <option value="$$$$" <?php selected( $opts['price_range'] ?? '', '$$$$' ); ?>>$$$$ — Lusso</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Immagine attività (URL)</th>
                    <td>
                        <input type="text" id="olo-seo-biz-image" name="<?php echo self::OPT_LOCAL; ?>[image]" value="<?php echo esc_attr( $opts['image'] ?? '' ); ?>" class="regular-text">
                        <button type="button" class="button olo-seo-upload-btn" data-target="olo-seo-biz-image">Seleziona</button>
                    </td>
                </tr>
            </table>
        </div>

        <div class="olo-seo-section">
            <h2>Orari di apertura</h2>
            <p class="description">Inserisci gli orari nel formato <code>HH:MM</code>. Lascia vuoto per i giorni di chiusura.</p>
            <table class="form-table olo-seo-table">
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
                    <tr>
                        <th><?php echo esc_html( $label ); ?></th>
                        <td>
                            <input type="text" name="<?php echo self::OPT_LOCAL; ?>[hours][<?php echo $key; ?>][open]" value="<?php echo esc_attr( $open ); ?>" class="small-text" placeholder="09:00"> —
                            <input type="text" name="<?php echo self::OPT_LOCAL; ?>[hours][<?php echo $key; ?>][close]" value="<?php echo esc_attr( $close ); ?>" class="small-text" placeholder="18:00">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php
    }

    /* ─── Tab: Webmaster Tools ─── */

    private function render_tab_webmaster() {
        $opts = self::get( self::OPT_WEBMASTER );
        ?>
        <div class="olo-seo-section">
            <h2>Verifica Motori di Ricerca</h2>
            <p class="description">Inserisci il codice di verifica (solo il valore del <code>content</code>, non l'intero meta tag).</p>
            <table class="form-table olo-seo-table">
                <tr>
                    <th>Google Search Console</th>
                    <td><input type="text" name="<?php echo self::OPT_WEBMASTER; ?>[google]" value="<?php echo esc_attr( $opts['google'] ?? '' ); ?>" class="regular-text" placeholder="Es. 1a2b3c4d5e..."></td>
                </tr>
                <tr>
                    <th>Bing Webmaster Tools</th>
                    <td><input type="text" name="<?php echo self::OPT_WEBMASTER; ?>[bing]" value="<?php echo esc_attr( $opts['bing'] ?? '' ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th>Pinterest</th>
                    <td><input type="text" name="<?php echo self::OPT_WEBMASTER; ?>[pinterest]" value="<?php echo esc_attr( $opts['pinterest'] ?? '' ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th>Yandex</th>
                    <td><input type="text" name="<?php echo self::OPT_WEBMASTER; ?>[yandex]" value="<?php echo esc_attr( $opts['yandex'] ?? '' ); ?>" class="regular-text"></td>
                </tr>
            </table>
        </div>
        <?php
    }

    /* ─── Tab: Sitemap ─── */

    private function render_tab_sitemap() {
        $opts = self::get( self::OPT_SITEMAP );
        $public_pts = get_post_types( [ 'public' => true ], 'objects' );
        $public_tax = get_taxonomies( [ 'public' => true ], 'objects' );
        ?>
        <div class="olo-seo-section">
            <h2>Sitemap XML</h2>
            <p class="description">
                URL della sitemap: <a href="<?php echo esc_url( home_url( '/?olo_sitemap=1' ) ); ?>" target="_blank"><code><?php echo esc_html( home_url( '/?olo_sitemap=1' ) ); ?></code></a>
            </p>
            <table class="form-table olo-seo-table">
                <tr>
                    <th>Sitemap attiva</th>
                    <td>
                        <label>
                            <input type="checkbox" name="<?php echo self::OPT_SITEMAP; ?>[enabled]" value="1" <?php checked( $opts['enabled'] ?? '1', '1' ); ?>>
                            Genera la sitemap XML
                        </label>
                    </td>
                </tr>
                <tr>
                    <th>Numero max URL per sitemap</th>
                    <td><input type="number" name="<?php echo self::OPT_SITEMAP; ?>[max_urls]" value="<?php echo esc_attr( $opts['max_urls'] ?? 1000 ); ?>" class="small-text" min="100" max="50000"></td>
                </tr>
                <tr>
                    <th>Includi immagini</th>
                    <td>
                        <label>
                            <input type="checkbox" name="<?php echo self::OPT_SITEMAP; ?>[include_images]" value="1" <?php checked( $opts['include_images'] ?? '1', '1' ); ?>>
                            Aggiungi tag <code>&lt;image:image&gt;</code> nella sitemap
                        </label>
                    </td>
                </tr>
            </table>
        </div>

        <div class="olo-seo-section">
            <h2>Post Type nella Sitemap</h2>
            <table class="form-table olo-seo-table">
                <?php foreach ( $public_pts as $pt ) : ?>
                    <tr>
                        <th><?php echo esc_html( $pt->labels->name ); ?> <code><?php echo esc_html( $pt->name ); ?></code></th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo self::OPT_SITEMAP; ?>[pt][<?php echo esc_attr( $pt->name ); ?>]" value="1" <?php checked( $opts['pt'][ $pt->name ] ?? '1', '1' ); ?>>
                                Includi
                            </label>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="olo-seo-section">
            <h2>Tassonomie nella Sitemap</h2>
            <table class="form-table olo-seo-table">
                <?php foreach ( $public_tax as $tax ) : ?>
                    <tr>
                        <th><?php echo esc_html( $tax->labels->name ); ?> <code><?php echo esc_html( $tax->name ); ?></code></th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo self::OPT_SITEMAP; ?>[tax][<?php echo esc_attr( $tax->name ); ?>]" value="1" <?php checked( $opts['tax'][ $tax->name ] ?? '1', '1' ); ?>>
                                Includi
                            </label>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php
    }

    /* ─── Tab: Avanzate ─── */

    private function render_tab_advanced() {
        $opts = self::get( self::OPT_ADVANCED );
        $public_pts = get_post_types( [ 'public' => true ], 'objects' );
        ?>
        <div class="olo-seo-section">
            <h2>Robots Meta</h2>
            <p class="description">Controlla le direttive robots globali. Queste possono essere sovrascritte per singolo post/pagina.</p>
            <table class="form-table olo-seo-table">
                <tr>
                    <th>Noindex — Archivi</th>
                    <td>
                        <label><input type="checkbox" name="<?php echo self::OPT_ADVANCED; ?>[noindex_categories]" value="1" <?php checked( $opts['noindex_categories'] ?? '', '1' ); ?>> Categorie</label><br>
                        <label><input type="checkbox" name="<?php echo self::OPT_ADVANCED; ?>[noindex_tags]" value="1" <?php checked( $opts['noindex_tags'] ?? '', '1' ); ?>> Tag</label><br>
                        <label><input type="checkbox" name="<?php echo self::OPT_ADVANCED; ?>[noindex_author]" value="1" <?php checked( $opts['noindex_author'] ?? '', '1' ); ?>> Archivi autore</label><br>
                        <label><input type="checkbox" name="<?php echo self::OPT_ADVANCED; ?>[noindex_date]" value="1" <?php checked( $opts['noindex_date'] ?? '', '1' ); ?>> Archivi per data</label><br>
                        <label><input type="checkbox" name="<?php echo self::OPT_ADVANCED; ?>[noindex_search]" value="1" <?php checked( $opts['noindex_search'] ?? '1', '1' ); ?>> Pagine di ricerca</label>
                    </td>
                </tr>
                <tr>
                    <th>Noindex — Post Type</th>
                    <td>
                        <?php foreach ( $public_pts as $pt ) : ?>
                            <label><input type="checkbox" name="<?php echo self::OPT_ADVANCED; ?>[noindex_pt][<?php echo esc_attr( $pt->name ); ?>]" value="1" <?php checked( $opts['noindex_pt'][ $pt->name ] ?? '', '1' ); ?>> <?php echo esc_html( $pt->labels->name ); ?></label><br>
                        <?php endforeach; ?>
                    </td>
                </tr>
            </table>
        </div>

        <div class="olo-seo-section">
            <h2>Link</h2>
            <table class="form-table olo-seo-table">
                <tr>
                    <th>Nofollow link esterni</th>
                    <td>
                        <label>
                            <input type="checkbox" name="<?php echo self::OPT_ADVANCED; ?>[nofollow_external]" value="1" <?php checked( $opts['nofollow_external'] ?? '', '1' ); ?>>
                            Aggiungi <code>rel="nofollow"</code> ai link esterni nel contenuto
                        </label>
                    </td>
                </tr>
                <tr>
                    <th>Apri in nuova tab</th>
                    <td>
                        <label>
                            <input type="checkbox" name="<?php echo self::OPT_ADVANCED; ?>[external_new_tab]" value="1" <?php checked( $opts['external_new_tab'] ?? '', '1' ); ?>>
                            Aggiungi <code>target="_blank"</code> ai link esterni
                        </label>
                    </td>
                </tr>
            </table>
        </div>

        <div class="olo-seo-section">
            <h2>Pulizia &lt;head&gt;</h2>
            <table class="form-table olo-seo-table">
                <tr>
                    <th>Rimuovi</th>
                    <td>
                        <label><input type="checkbox" name="<?php echo self::OPT_ADVANCED; ?>[remove_shortlink]" value="1" <?php checked( $opts['remove_shortlink'] ?? '', '1' ); ?>> Shortlink <code>rel="shortlink"</code></label><br>
                        <label><input type="checkbox" name="<?php echo self::OPT_ADVANCED; ?>[remove_rsd]" value="1" <?php checked( $opts['remove_rsd'] ?? '', '1' ); ?>> RSD link <code>EditURI</code></label><br>
                        <label><input type="checkbox" name="<?php echo self::OPT_ADVANCED; ?>[remove_wlw]" value="1" <?php checked( $opts['remove_wlw'] ?? '', '1' ); ?>> Windows Live Writer link</label><br>
                        <label><input type="checkbox" name="<?php echo self::OPT_ADVANCED; ?>[remove_generator]" value="1" <?php checked( $opts['remove_generator'] ?? '', '1' ); ?>> Meta <code>generator</code> (versione WP)</label><br>
                        <label><input type="checkbox" name="<?php echo self::OPT_ADVANCED; ?>[remove_feed_links]" value="1" <?php checked( $opts['remove_feed_links'] ?? '', '1' ); ?>> Feed RSS extra (commenti, categorie)</label>
                    </td>
                </tr>
            </table>
        </div>

        <div class="olo-seo-section">
            <h2>Robots.txt personalizzato</h2>
            <p class="description">Aggiungi righe personalizzate al file <code>robots.txt</code> virtuale di WordPress. Una regola per riga.</p>
            <textarea name="<?php echo self::OPT_ADVANCED; ?>[robots_txt]" class="large-text code" rows="8" placeholder="User-agent: *&#10;Disallow: /wp-admin/&#10;Allow: /wp-admin/admin-ajax.php"><?php echo esc_textarea( $opts['robots_txt'] ?? '' ); ?></textarea>
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
            .olo-seo-metabox .olo-seo-tab-btn.active { color: #2271b1; border-bottom-color: #2271b1; }
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
                    <select name="olo_seo_schema_type">
                        <option value="" <?php selected( $seo_schema, '' ); ?>>Automatico (predefinito)</option>
                        <option value="Article" <?php selected( $seo_schema, 'Article' ); ?>>Article</option>
                        <option value="BlogPosting" <?php selected( $seo_schema, 'BlogPosting' ); ?>>BlogPosting</option>
                        <option value="NewsArticle" <?php selected( $seo_schema, 'NewsArticle' ); ?>>NewsArticle</option>
                        <option value="WebPage" <?php selected( $seo_schema, 'WebPage' ); ?>>WebPage</option>
                        <option value="FAQPage" <?php selected( $seo_schema, 'FAQPage' ); ?>>FAQPage</option>
                        <option value="HowTo" <?php selected( $seo_schema, 'HowTo' ); ?>>HowTo</option>
                        <option value="Product" <?php selected( $seo_schema, 'Product' ); ?>>Product</option>
                        <option value="Event" <?php selected( $seo_schema, 'Event' ); ?>>Event</option>
                        <option value="Recipe" <?php selected( $seo_schema, 'Recipe' ); ?>>Recipe</option>
                        <option value="LocalBusiness" <?php selected( $seo_schema, 'LocalBusiness' ); ?>>LocalBusiness</option>
                        <option value="none" <?php selected( $seo_schema, 'none' ); ?>>Nessuno schema</option>
                    </select>
                </div>

                <!-- FAQ Schema Builder -->
                <div class="olo-seo-field">
                    <label>FAQ Schema (domande e risposte)</label>
                    <p class="description" style="margin-top:0;">Aggiungi coppie domanda/risposta per generare markup FAQPage. Ottiene rich results su Google.</p>
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
                                    <input type="text" name="olo_seo_faq[<?php echo $i; ?>][q]" value="<?php echo esc_attr( $faq['q'] ?? '' ); ?>" style="width:100%;">
                                </div>
                                <div>
                                    <label style="font-size:11px;font-weight:600;color:#666;text-transform:uppercase;display:block;margin-bottom:2px;">Risposta</label>
                                    <textarea name="olo_seo_faq[<?php echo $i; ?>][a]" rows="2" style="width:100%;"><?php echo esc_textarea( $faq['a'] ?? '' ); ?></textarea>
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
        var oloFaqIndex = <?php echo count( $faq_items ); ?>;
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

            var defaultTitle = <?php echo wp_json_encode( $post_title . ' - ' . $site_name ); ?>;
            var defaultDesc  = <?php echo wp_json_encode( $display_desc ); ?>;

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

            function runAnalysis() {
                var kw = (keywordInput.value || '').trim().toLowerCase();
                var container = document.getElementById('olo-seo-checklist');
                if (!kw) {
                    container.innerHTML = '';
                    document.getElementById('olo-seo-keyword-analysis').innerHTML = '';
                    return;
                }

                var title = (titleInput.value || defaultTitle).toLowerCase();
                var desc  = (descInput.value || defaultDesc).toLowerCase();
                var url   = <?php echo wp_json_encode( strtolower( $display_url ) ); ?>;

                var checks = [];
                // Focus keyword in title
                checks.push({
                    pass: title.indexOf(kw) !== -1,
                    msg: title.indexOf(kw) !== -1
                        ? 'Focus keyword presente nel titolo SEO'
                        : 'Focus keyword non trovata nel titolo SEO'
                });
                // Focus keyword in description
                checks.push({
                    pass: desc.indexOf(kw) !== -1,
                    msg: desc.indexOf(kw) !== -1
                        ? 'Focus keyword presente nella meta description'
                        : 'Focus keyword non trovata nella meta description'
                });
                // Focus keyword in URL
                var kwSlug = kw.replace(/\s+/g, '-');
                checks.push({
                    pass: url.indexOf(kw) !== -1 || url.indexOf(kwSlug) !== -1,
                    msg: url.indexOf(kw) !== -1 || url.indexOf(kwSlug) !== -1
                        ? 'Focus keyword presente nell\'URL'
                        : 'Focus keyword non trovata nell\'URL'
                });
                // Title length
                var tl = (titleInput.value || defaultTitle).length;
                checks.push({
                    pass: tl >= 30 && tl <= 60,
                    warn: tl > 0 && (tl < 30 || tl > 60),
                    msg: tl >= 30 && tl <= 60
                        ? 'Lunghezza titolo ottimale (' + tl + ' caratteri)'
                        : 'Lunghezza titolo: ' + tl + ' caratteri (ideale: 30-60)'
                });
                // Description length
                var dl = (descInput.value || '').length;
                checks.push({
                    pass: dl >= 120 && dl <= 160,
                    warn: dl > 0 && (dl < 120 || dl > 160),
                    msg: dl >= 120 && dl <= 160
                        ? 'Lunghezza description ottimale (' + dl + ' caratteri)'
                        : dl === 0 ? 'Meta description mancante' : 'Lunghezza description: ' + dl + ' caratteri (ideale: 120-160)'
                });

                // Content analysis — grab from WP editor
                var contentEl = document.getElementById('content');
                var contentText = '';
                if (contentEl) {
                    contentText = contentEl.value || '';
                }
                // Also try TinyMCE
                if (typeof tinymce !== 'undefined') {
                    var editor = tinymce.get('content');
                    if (editor) {
                        contentText = editor.getContent({format: 'text'}) || contentText;
                    }
                }

                if (contentText.length > 0) {
                    var wordCount = contentText.trim().split(/\s+/).filter(function(w){ return w.length > 0; }).length;

                    // Word count check
                    checks.push({
                        pass: wordCount >= 300,
                        warn: wordCount >= 100 && wordCount < 300,
                        msg: wordCount >= 300
                            ? 'Lunghezza contenuto buona (' + wordCount + ' parole)'
                            : 'Contenuto breve: ' + wordCount + ' parole (consigliato: 300+)'
                    });

                    // Keyword density
                    var kwLower = kw.toLowerCase();
                    var contentLower = contentText.toLowerCase();
                    var kwCount = 0;
                    var idx = 0;
                    while ((idx = contentLower.indexOf(kwLower, idx)) !== -1) {
                        kwCount++;
                        idx += kwLower.length;
                    }
                    var density = wordCount > 0 ? ((kwCount / wordCount) * 100).toFixed(1) : 0;
                    checks.push({
                        pass: density >= 0.5 && density <= 2.5,
                        warn: (density > 0 && density < 0.5) || density > 2.5,
                        msg: kwCount > 0
                            ? 'Keyword density: ' + density + '% (' + kwCount + ' occorrenze)'
                            : 'Focus keyword non trovata nel contenuto'
                    });

                    // Keyword in first paragraph
                    var firstPara = contentText.substring(0, 200).toLowerCase();
                    checks.push({
                        pass: firstPara.indexOf(kwLower) !== -1,
                        msg: firstPara.indexOf(kwLower) !== -1
                            ? 'Focus keyword presente nel primo paragrafo'
                            : 'Focus keyword assente dal primo paragrafo'
                    });

                    // Readability: average sentence length
                    var sentences = contentText.split(/[.!?]+/).filter(function(s){ return s.trim().length > 10; });
                    if (sentences.length > 0) {
                        var avgWords = wordCount / sentences.length;
                        checks.push({
                            pass: avgWords <= 20,
                            warn: avgWords > 20 && avgWords <= 25,
                            msg: avgWords <= 20
                                ? 'Leggibilita buona (media ' + Math.round(avgWords) + ' parole/frase)'
                                : 'Frasi lunghe: media ' + Math.round(avgWords) + ' parole/frase (ideale: max 20)'
                        });
                    }
                }

                var passCount = checks.filter(function(c){ return c.pass; }).length;
                var total = checks.length;
                var scoreClass = passCount >= 4 ? 'good' : (passCount >= 2 ? 'ok' : 'bad');
                var scoreLabel = passCount >= 4 ? 'Buono' : (passCount >= 2 ? 'Migliorabile' : 'Da migliorare');

                document.getElementById('olo-seo-keyword-analysis').innerHTML =
                    '<span class="olo-seo-keyword-score ' + scoreClass + '">' +
                    '<strong>' + passCount + '/' + total + '</strong> ' + scoreLabel +
                    '</span>';

                var html = '<ul class="olo-seo-checks">';
                checks.forEach(function(c){
                    var cls = c.pass ? 'pass' : (c.warn ? 'warn' : 'fail');
                    var icon = c.pass ? 'yes-alt' : (c.warn ? 'warning' : 'dismiss');
                    html += '<li class="' + cls + '"><span class="dashicons dashicons-' + icon + '"></span>' + c.msg + '</li>';
                });
                html += '</ul>';
                container.innerHTML = html;
            }

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
        if ( ! isset( $_POST['olo_seo_nonce'] ) || ! wp_verify_nonce( $_POST['olo_seo_nonce'], 'olo_seo_meta_box' ) ) {
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

        foreach ( $fields as $post_key => $meta_key ) {
            $value = sanitize_text_field( $_POST[ $post_key ] ?? '' );
            if ( $value ) {
                update_post_meta( $post_id, $meta_key, $value );
            } else {
                delete_post_meta( $post_id, $meta_key );
            }
        }

        // FAQ Schema data
        $faq_raw = $_POST['olo_seo_faq'] ?? [];
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
