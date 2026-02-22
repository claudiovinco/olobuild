<?php
/**
 * Olo Lang — Interfaccia admin per gestione lingue e traduzioni.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Lang_Admin {

    public function init() {
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
    }

    public function add_admin_menu() {
        add_menu_page(
            'Olo Lang',
            'Olo Lang',
            'edit_posts',
            'olo-lang',
            [ $this, 'render_admin_page' ],
            'dashicons-translation',
            31
        );

        add_submenu_page(
            'olo-lang',
            'Traduzioni',
            'Traduzioni',
            'edit_posts',
            'olo-lang',
            [ $this, 'render_admin_page' ]
        );

        add_submenu_page(
            'olo-lang',
            'Contenuti',
            'Contenuti',
            'edit_posts',
            'olo-lang-content',
            [ $this, 'render_content_page' ]
        );

        add_submenu_page(
            'olo-lang',
            'Stringhe Globali',
            'Stringhe Globali',
            'edit_posts',
            'olo-lang-global',
            [ $this, 'render_global_strings_page' ]
        );

        add_submenu_page(
            'olo-lang',
            'Impostazioni Lingua',
            'Impostazioni',
            'manage_options',
            'olo-lang-settings',
            [ $this, 'render_settings_page' ]
        );
    }

    public function enqueue_admin_assets( $hook ) {
        if ( strpos( $hook, 'olo-lang' ) === false ) {
            return;
        }

        wp_enqueue_style(
            'olo-lang-admin',
            OLO_LANG_URL . 'assets/css/admin.css',
            [],
            OLO_LANG_VERSION
        );

        wp_enqueue_script(
            'olo-lang-admin',
            OLO_LANG_URL . 'assets/js/admin.js',
            [ 'jquery' ],
            OLO_LANG_VERSION,
            true
        );

        wp_localize_script( 'olo-lang-admin', 'oloLangData', [
            'restUrl'            => rest_url( 'olo-lang/v1/' ),
            'nonce'              => wp_create_nonce( 'wp_rest' ),
            'config'             => Olo_Lang_Language::get_config(),
            'availableLanguages' => Olo_Lang_Language::get_available_languages(),
        ] );
    }

    // =========================================================================
    // Pagina Contenuti — traduzione post/CPT tramite copie per lingua
    // =========================================================================

    public function render_content_page() {
        $config    = Olo_Lang_Language::get_config();
        $default   = $config['default_lang'];
        $languages = Olo_Lang_Language::get_active_languages();

        $target_langs = array_filter( $languages, function ( $l ) use ( $default ) {
            return $l['code'] !== $default;
        } );

        // Post types da mostrare
        $post_types = [
            'olo_service' => 'Baite / Servizi',
            'page'        => 'Pagine',
            'post'        => 'Articoli',
        ];

        ?>
        <div class="wrap olo-lang-wrap">
            <h1 class="olo-lang-title">
                <span class="dashicons dashicons-translation"></span>
                Olo Lang — Contenuti
            </h1>
            <p class="description">Crea copie dei contenuti per ogni lingua. Ogni copia si traduce direttamente nell'editor WordPress.</p>

            <?php if ( empty( $target_langs ) ) : ?>
                <div class="notice notice-warning">
                    <p>Nessuna lingua target configurata. <a href="<?php echo esc_url( admin_url( 'admin.php?page=olo-lang-settings' ) ); ?>">Configura lingue</a>.</p>
                </div>
                <?php return; endif; ?>

            <?php foreach ( $post_types as $pt_slug => $pt_label ) :
                $posts = get_posts( [
                    'post_type'      => $pt_slug,
                    'post_status'    => 'publish',
                    'posts_per_page' => 100,
                    'orderby'        => 'title',
                    'order'          => 'ASC',
                    'meta_query'     => [
                        'relation' => 'OR',
                        [ 'key' => '_olo_lang_source', 'compare' => 'NOT EXISTS' ],
                        [ 'key' => '_olo_lang_source', 'value' => '' ],
                    ],
                ] );

                if ( empty( $posts ) ) continue;
            ?>
            <h2 style="margin:24px 0 8px;font-size:16px"><?php echo esc_html( $pt_label ); ?></h2>
            <table class="widefat striped olo-lang-table">
                <thead>
                    <tr>
                        <th>Titolo</th>
                        <?php foreach ( $target_langs as $lang ) : ?>
                            <th class="olo-lang-col-lang" style="width:140px"><?php echo esc_html( strtoupper( $lang['code'] ) ); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $posts as $p ) :
                        $translations = get_post_meta( $p->ID, '_olo_lang_translations', true );
                        $translations = is_array( $translations ) ? $translations : [];
                    ?>
                    <tr>
                        <td>
                            <strong><?php echo esc_html( $p->post_title ); ?></strong>
                            <br><small>
                                <a href="<?php echo esc_url( get_edit_post_link( $p->ID ) ); ?>">Modifica originale</a>
                                | <a href="<?php echo esc_url( get_permalink( $p->ID ) ); ?>" target="_blank">Vedi</a>
                            </small>
                        </td>
                        <?php foreach ( $target_langs as $lang ) :
                            $code    = $lang['code'];
                            $copy_id = $translations[ $code ] ?? 0;
                            $copy    = $copy_id ? get_post( $copy_id ) : null;
                            $valid   = $copy && $copy->post_status !== 'trash';
                        ?>
                        <td class="olo-lang-col-lang" style="text-align:center">
                            <?php if ( $valid ) :
                                $is_pub = $copy->post_status === 'publish';
                            ?>
                                <a href="<?php echo esc_url( get_edit_post_link( $copy_id ) ); ?>"
                                   class="olo-lang-badge olo-lang-badge--<?php echo $is_pub ? 'complete' : 'partial'; ?>">
                                    <?php echo $is_pub ? 'Pubblicata' : 'Bozza'; ?>
                                </a>
                            <?php else : ?>
                                <button type="button"
                                        class="button button-small button-primary olo-lang-create-copy"
                                        data-post-id="<?php echo esc_attr( $p->ID ); ?>"
                                        data-lang="<?php echo esc_attr( $code ); ?>">
                                    + Crea <?php echo esc_html( strtoupper( $code ) ); ?>
                                </button>
                            <?php endif; ?>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endforeach; ?>
        </div>

        <script>
        jQuery(function($){
            $('.olo-lang-create-copy').on('click', function(){
                var $btn = $(this).prop('disabled', true).text('Creazione...');
                $.post(ajaxurl, {
                    action: 'olo_lang_duplicate_post',
                    post_id: $btn.data('post-id'),
                    lang: $btn.data('lang'),
                    _wpnonce: '<?php echo wp_create_nonce( 'olo_lang_duplicate' ); ?>'
                }, function(res){
                    if (res.success && res.data.edit_url) {
                        window.open(res.data.edit_url, '_blank');
                        $btn.replaceWith('<a href="' + res.data.edit_url + '" class="olo-lang-badge olo-lang-badge--partial">Bozza</a>');
                    } else {
                        alert(res.data || 'Errore');
                        $btn.prop('disabled', false).text('+ Crea');
                    }
                });
            });
        });
        </script>
        <?php
    }

    // =========================================================================
    // Pagina principale — Lista template e editor traduzioni
    // =========================================================================

    public function render_admin_page() {
        $translate_id = absint( $_GET['translate'] ?? 0 );
        $lang         = sanitize_text_field( $_GET['lang'] ?? '' );

        if ( $translate_id && $lang ) {
            $this->render_translation_editor( $translate_id, $lang );
            return;
        }

        $this->render_template_list();
    }

    /**
     * Lista template con stato traduzioni.
     */
    private function render_template_list() {
        if ( ! class_exists( 'Olo_Database' ) ) {
            echo '<div class="wrap"><div class="notice notice-warning"><p>Per tradurre i template del page builder, attiva il plugin <strong>Olobuild</strong>.</p></div></div>';
            return;
        }
        $db        = new Olo_Database();
        $result    = $db->list_templates( [ 'per_page' => 100 ] );
        $templates = $result['items'] ?? [];
        $config    = Olo_Lang_Language::get_config();
        $languages = Olo_Lang_Language::get_active_languages();
        $default   = $config['default_lang'];

        // Lingue target (esclusa la predefinita)
        $target_langs = array_filter( $languages, function ( $l ) use ( $default ) {
            return $l['code'] !== $default;
        } );

        // Statistiche per template
        $lang_db = new Olo_Lang_Database();
        $stats_map = [];
        foreach ( $templates as $tpl ) {
            $stats = $lang_db->get_template_stats( $tpl['id'] );
            foreach ( $stats as $s ) {
                $stats_map[ $tpl['id'] ][ $s['lang'] ] = $s;
            }
        }

        ?>
        <div class="wrap olo-lang-wrap">
            <h1 class="olo-lang-title">
                <span class="dashicons dashicons-translation"></span>
                Olo Lang — Traduzioni
            </h1>

            <?php if ( empty( $target_langs ) ) : ?>
                <div class="notice notice-warning">
                    <p>Nessuna lingua di traduzione configurata. <a href="<?php echo esc_url( admin_url( 'admin.php?page=olo-lang-settings' ) ); ?>">Aggiungi lingue nelle impostazioni</a>.</p>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $templates ) && ! empty( $target_langs ) ) : ?>
            <table class="widefat striped olo-lang-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Template</th>
                        <th>Tipo</th>
                        <?php foreach ( $target_langs as $lang ) : ?>
                            <th class="olo-lang-col-lang"><?php echo esc_html( strtoupper( $lang['code'] ) ); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $templates as $tpl ) :
                        // Conta stringhe traducibili
                        $strings = Olo_Lang_Scanner::scan_template( $tpl['content'] );
                        $total   = count( $strings );
                        $type_map = [
                            'page'      => 'Pagina',
                            'header'    => 'Header',
                            'footer'    => 'Footer',
                            'megapanel' => 'Mega Panel',
                            'single'    => 'Single',
                        ];
                    ?>
                    <tr>
                        <td><?php echo esc_html( $tpl['id'] ); ?></td>
                        <td>
                            <strong><?php echo esc_html( $tpl['title'] ?: '(senza titolo)' ); ?></strong>
                            <br><small><?php echo esc_html( $total ); ?> stringhe</small>
                        </td>
                        <td><?php echo esc_html( $type_map[ $tpl['type'] ] ?? $tpl['type'] ); ?></td>
                        <?php foreach ( $target_langs as $lang ) :
                            $s = $stats_map[ $tpl['id'] ][ $lang['code'] ] ?? null;
                            $translated = $s ? intval( $s['tradotte'] ) : 0;
                            $pct = $total > 0 ? round( ( $translated / $total ) * 100 ) : 0;
                            $url = admin_url( 'admin.php?page=olo-lang&translate=' . $tpl['id'] . '&lang=' . $lang['code'] );
                        ?>
                            <td class="olo-lang-col-lang">
                                <a href="<?php echo esc_url( $url ); ?>" class="olo-lang-badge olo-lang-badge--<?php echo $pct >= 100 ? 'complete' : ( $pct > 0 ? 'partial' : 'empty' ); ?>">
                                    <?php echo esc_html( $pct ); ?>%
                                </a>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php elseif ( empty( $templates ) ) : ?>
                <p>Nessun template Olobuild trovato. Crea prima un template nel builder.</p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Editor di traduzione side-by-side per un template + lingua.
     */
    private function render_translation_editor( $template_id, $lang ) {
        if ( ! class_exists( 'Olo_Database' ) ) {
            echo '<div class="wrap"><div class="notice notice-warning"><p>Per tradurre i template del page builder, attiva il plugin <strong>Olobuild</strong>.</p></div></div>';
            return;
        }
        $db       = new Olo_Database();
        $template = $db->get_template( $template_id );

        if ( ! $template ) {
            echo '<div class="wrap"><div class="notice notice-error"><p>Template non trovato.</p></div></div>';
            return;
        }

        $config    = Olo_Lang_Language::get_config();
        $languages = Olo_Lang_Language::get_active_languages();
        $lang_name = '';
        foreach ( $languages as $l ) {
            if ( $l['code'] === $lang ) {
                $lang_name = $l['name'];
                break;
            }
        }

        // Stringhe traducibili
        $strings = Olo_Lang_Scanner::scan_template( $template['content'] );

        // Traduzioni esistenti
        $lang_db      = new Olo_Lang_Database();
        $translations = $lang_db->get_template_translations( $template_id, $lang );
        $trans_map    = [];
        foreach ( $translations as $t ) {
            $key              = $t['tile_id'] . '::' . $t['field_path'];
            $trans_map[ $key ] = $t;
        }

        // Raggruppa per tipo tile
        $grouped = [];
        foreach ( $strings as $s ) {
            $type = $s['tile_type'] ?: 'altro';
            $grouped[ $type ][] = $s;
        }

        $back_url = admin_url( 'admin.php?page=olo-lang' );
        $total    = count( $strings );
        $done     = 0;
        foreach ( $strings as $s ) {
            $key = $s['tile_id'] . '::' . $s['field_path'];
            if ( isset( $trans_map[ $key ] ) && ! empty( $trans_map[ $key ]['translation'] ) && $trans_map[ $key ]['status'] === 'tradotto' ) {
                $done++;
            }
        }
        $pct = $total > 0 ? round( ( $done / $total ) * 100 ) : 0;

        ?>
        <div class="wrap olo-lang-wrap">
            <div class="olo-lang-editor-header">
                <a href="<?php echo esc_url( $back_url ); ?>" class="olo-lang-back">&larr; Torna alla lista</a>
                <h1>
                    Traduci: <strong><?php echo esc_html( $template['title'] ); ?></strong>
                    &rarr; <?php echo esc_html( strtoupper( $lang ) ); ?> (<?php echo esc_html( $lang_name ); ?>)
                </h1>
                <div class="olo-lang-progress-wrap">
                    <div class="olo-lang-progress-bar">
                        <div class="olo-lang-progress-fill" style="width: <?php echo $pct; ?>%"></div>
                    </div>
                    <span class="olo-lang-progress-text" id="olo-lang-progress-text"><?php echo $done; ?>/<?php echo $total; ?> (<?php echo $pct; ?>%)</span>
                </div>
                <button type="button" class="button button-primary olo-lang-save-btn" id="olo-lang-save-all">
                    Salva tutte le traduzioni
                </button>
            </div>

            <form id="olo-lang-editor-form"
                  data-template-id="<?php echo esc_attr( $template_id ); ?>"
                  data-lang="<?php echo esc_attr( $lang ); ?>">

                <?php foreach ( $grouped as $type => $items ) :
                    $type_label = Olo_Lang_Scanner::get_field_context( $type, '' );
                    $type_label = explode( ' / ', $type_label )[0];
                ?>
                <div class="olo-lang-group">
                    <h2 class="olo-lang-group-title"><?php echo esc_html( $type_label ); ?> (<?php echo count( $items ); ?>)</h2>

                    <?php foreach ( $items as $s ) :
                        $key       = $s['tile_id'] . '::' . $s['field_path'];
                        $existing  = $trans_map[ $key ] ?? null;
                        $trans_val = $existing ? $existing['translation'] : '';
                        $status    = $existing ? $existing['status'] : 'bozza';
                        $is_html   = ( strip_tags( $s['value'] ) !== $s['value'] );
                        $is_long   = strlen( $s['value'] ) > 120;
                    ?>
                    <div class="olo-lang-row <?php echo $status === 'tradotto' ? 'olo-lang-row--done' : ( $status === 'obsoleta' ? 'olo-lang-row--outdated' : '' ); ?>"
                         data-tile-id="<?php echo esc_attr( $s['tile_id'] ); ?>"
                         data-field-path="<?php echo esc_attr( $s['field_path'] ); ?>">

                        <div class="olo-lang-row-meta">
                            <span class="olo-lang-field-context"><?php echo esc_html( $s['context'] ); ?></span>
                            <?php if ( $status === 'obsoleta' ) : ?>
                                <span class="olo-lang-status olo-lang-status--outdated">obsoleta</span>
                            <?php elseif ( $status === 'tradotto' ) : ?>
                                <span class="olo-lang-status olo-lang-status--done">tradotto</span>
                            <?php endif; ?>
                        </div>

                        <div class="olo-lang-row-fields">
                            <div class="olo-lang-col-original">
                                <label>Originale (<?php echo esc_html( strtoupper( $config['default_lang'] ) ); ?>)</label>
                                <?php if ( $is_html ) : ?>
                                    <div class="olo-lang-original-html"><?php echo wp_kses_post( $s['value'] ); ?></div>
                                <?php else : ?>
                                    <div class="olo-lang-original-text"><?php echo esc_html( $s['value'] ); ?></div>
                                <?php endif; ?>
                                <input type="hidden" class="olo-lang-original-value" value="<?php echo esc_attr( $s['value'] ); ?>">
                            </div>

                            <div class="olo-lang-col-translation">
                                <label>Traduzione (<?php echo esc_html( strtoupper( $lang ) ); ?>)</label>
                                <?php if ( $is_long || $is_html ) : ?>
                                    <textarea class="olo-lang-input" rows="4" placeholder="Inserisci traduzione..."><?php echo esc_textarea( $trans_val ); ?></textarea>
                                <?php else : ?>
                                    <input type="text" class="olo-lang-input" value="<?php echo esc_attr( $trans_val ); ?>" placeholder="Inserisci traduzione...">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>

                <?php if ( empty( $strings ) ) : ?>
                    <p class="olo-lang-empty">Nessuna stringa traducibile trovata in questo template.</p>
                <?php endif; ?>

            </form>

            <?php if ( ! empty( $strings ) ) : ?>
            <div class="olo-lang-editor-footer">
                <button type="button" class="button button-primary olo-lang-save-btn" id="olo-lang-save-bottom">
                    Salva tutte le traduzioni
                </button>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }

    // =========================================================================
    // Pagina Stringhe Globali
    // =========================================================================

    public function render_global_strings_page() {
        $config    = Olo_Lang_Language::get_config();
        $languages = Olo_Lang_Language::get_active_languages();
        $default   = $config['default_lang'];

        $target_langs = array_filter( $languages, function ( $l ) use ( $default ) {
            return $l['code'] !== $default;
        } );

        ?>
        <div class="wrap olo-lang-wrap" id="olo-gs-app">
            <h1 class="olo-lang-title">
                <span class="dashicons dashicons-translation"></span>
                Olo Lang — Stringhe Globali
            </h1>

            <?php if ( empty( $target_langs ) ) : ?>
                <div class="notice notice-warning">
                    <p>Nessuna lingua configurata. <a href="<?php echo esc_url( admin_url( 'admin.php?page=olo-lang-settings' ) ); ?>">Configura lingue</a>.</p>
                </div>
                <?php return; endif; ?>

            <!-- Toolbar -->
            <div class="olo-gs-toolbar">
                <div class="olo-gs-toolbar-left">
                    <label for="olo-gs-lang">Lingua:</label>
                    <select id="olo-gs-lang" class="olo-gs-select">
                        <?php foreach ( $target_langs as $lang ) : ?>
                            <option value="<?php echo esc_attr( $lang['code'] ); ?>">
                                <?php echo esc_html( strtoupper( $lang['code'] ) ); ?> — <?php echo esc_html( $lang['name'] ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" id="olo-gs-search" class="olo-gs-search" placeholder="Cerca stringa...">
                </div>
                <div class="olo-gs-toolbar-right">
                    <span id="olo-gs-counter" class="olo-gs-counter"></span>
                    <button type="button" class="button button-primary olo-lang-save-btn" id="olo-gs-save">
                        Salva (Ctrl+S)
                    </button>
                </div>
            </div>

            <!-- Tab bar -->
            <div class="olo-gs-tabs">
                <button class="olo-gs-tab olo-gs-tab--active" data-tab="wp-menu">Menu WP</button>
                <button class="olo-gs-tab" data-tab="plugin">Plugin</button>
                <button class="olo-gs-tab" data-tab="custom">Personalizzate</button>
            </div>

            <!-- Tab: Menu WP -->
            <div class="olo-gs-panel" id="olo-gs-panel-wp-menu">
                <div class="olo-gs-scan-bar">
                    <button type="button" class="button" id="olo-gs-scan-menus">
                        <span class="dashicons dashicons-search"></span> Scansiona Menu
                    </button>
                    <span class="olo-gs-scan-info" id="olo-gs-menu-info"></span>
                </div>
                <table class="widefat striped olo-gs-table" id="olo-gs-table-wp-menu">
                    <thead>
                        <tr>
                            <th class="olo-gs-col-original">Originale (<?php echo esc_html( strtoupper( $default ) ); ?>)</th>
                            <th class="olo-gs-col-translation">Traduzione</th>
                            <th class="olo-gs-col-status">Stato</th>
                            <th class="olo-gs-col-actions">Azioni</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <!-- Tab: Plugin -->
            <div class="olo-gs-panel" id="olo-gs-panel-plugin" style="display:none">
                <div class="olo-gs-scan-bar">
                    <button type="button" class="button" id="olo-gs-scan-plugins">
                        <span class="dashicons dashicons-search"></span> Scansiona Plugin
                    </button>
                    <span class="olo-gs-scan-info" id="olo-gs-plugin-info"></span>
                </div>
                <table class="widefat striped olo-gs-table" id="olo-gs-table-plugin">
                    <thead>
                        <tr>
                            <th class="olo-gs-col-original">Originale (<?php echo esc_html( strtoupper( $default ) ); ?>)</th>
                            <th class="olo-gs-col-translation">Traduzione</th>
                            <th class="olo-gs-col-status">Stato</th>
                            <th class="olo-gs-col-actions">Azioni</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <!-- Tab: Personalizzate -->
            <div class="olo-gs-panel" id="olo-gs-panel-custom" style="display:none">
                <div class="olo-gs-add-form">
                    <input type="text" id="olo-gs-custom-original" class="regular-text" placeholder="Stringa originale (IT)">
                    <input type="text" id="olo-gs-custom-translation" class="regular-text" placeholder="Traduzione">
                    <button type="button" class="button button-primary" id="olo-gs-add-custom">
                        <span class="dashicons dashicons-plus-alt2"></span> Aggiungi
                    </button>
                </div>
                <table class="widefat striped olo-gs-table" id="olo-gs-table-custom">
                    <thead>
                        <tr>
                            <th class="olo-gs-col-original">Originale (<?php echo esc_html( strtoupper( $default ) ); ?>)</th>
                            <th class="olo-gs-col-translation">Traduzione</th>
                            <th class="olo-gs-col-status">Stato</th>
                            <th class="olo-gs-col-actions">Azioni</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <?php
    }

    // =========================================================================
    // Pagina impostazioni
    // =========================================================================

    public function render_settings_page() {
        $config    = Olo_Lang_Language::get_config();
        $available = Olo_Lang_Language::get_available_languages();

        // Lingue gia' aggiunte
        $added_codes = array_column( $config['languages'] ?? [], 'code' );

        // Lingue non ancora aggiunte
        $addable = array_filter( $available, function ( $l ) use ( $added_codes ) {
            return ! in_array( $l['code'], $added_codes, true );
        } );

        ?>
        <div class="wrap olo-lang-wrap">
            <h1 class="olo-lang-title">
                <span class="dashicons dashicons-translation"></span>
                Olo Lang — Impostazioni
            </h1>

            <form id="olo-lang-settings-form" class="olo-lang-settings">

                <!-- Lingua predefinita -->
                <div class="olo-lang-setting-section">
                    <h2>Lingua predefinita</h2>
                    <p class="description">La lingua in cui sono scritti i contenuti originali di Olobuild.</p>
                    <select id="olo-lang-default" class="regular-text">
                        <?php foreach ( $config['languages'] ?? [] as $lang ) : ?>
                            <option value="<?php echo esc_attr( $lang['code'] ); ?>"
                                    <?php selected( $config['default_lang'], $lang['code'] ); ?>>
                                <?php echo esc_html( $lang['name'] ); ?> (<?php echo esc_html( strtoupper( $lang['code'] ) ); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Lingue attive -->
                <div class="olo-lang-setting-section">
                    <h2>Lingue attive</h2>
                    <p class="description">Lingue in cui i contenuti verranno tradotti.</p>

                    <table class="widefat olo-lang-languages-table" id="olo-lang-languages-table">
                        <thead>
                            <tr>
                                <th>Codice</th>
                                <th>Nome</th>
                                <th>Attiva</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $config['languages'] ?? [] as $lang ) : ?>
                            <tr data-code="<?php echo esc_attr( $lang['code'] ); ?>">
                                <td><strong><?php echo esc_html( strtoupper( $lang['code'] ) ); ?></strong></td>
                                <td><?php echo esc_html( $lang['name'] ); ?></td>
                                <td>
                                    <input type="checkbox" class="olo-lang-active-cb"
                                           <?php checked( ! empty( $lang['active'] ) ); ?>
                                           <?php if ( $lang['code'] === $config['default_lang'] ) echo 'disabled checked'; ?>>
                                </td>
                                <td>
                                    <?php if ( $lang['code'] !== $config['default_lang'] ) : ?>
                                        <button type="button" class="button button-small olo-lang-remove-btn">Rimuovi</button>
                                    <?php else : ?>
                                        <em>predefinita</em>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php if ( ! empty( $addable ) ) : ?>
                    <div class="olo-lang-add-row">
                        <select id="olo-lang-add-select" class="regular-text">
                            <option value="">— Aggiungi lingua —</option>
                            <?php foreach ( $addable as $lang ) : ?>
                                <option value="<?php echo esc_attr( $lang['code'] ); ?>"
                                        data-name="<?php echo esc_attr( $lang['name'] ); ?>"
                                        data-locale="<?php echo esc_attr( $lang['locale'] ); ?>">
                                    <?php echo esc_html( $lang['name'] ); ?> (<?php echo esc_html( strtoupper( $lang['code'] ) ); ?>) — <?php echo esc_html( $lang['native'] ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="button" id="olo-lang-add-btn">Aggiungi</button>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Modalita' URL -->
                <div class="olo-lang-setting-section">
                    <h2>Formato URL multilingua</h2>
                    <p class="description">Come viene indicata la lingua nell'URL del sito.</p>
                    <select id="olo-lang-url-mode" class="regular-text">
                        <option value="parameter" <?php selected( $config['url_mode'] ?? '', 'parameter' ); ?>>
                            Parametro: ?lang=en
                        </option>
                        <option value="path" <?php selected( $config['url_mode'] ?? '', 'path' ); ?>>
                            Percorso: /en/pagina
                        </option>
                        <option value="polylang" <?php selected( $config['url_mode'] ?? '', 'polylang' ); ?>>
                            Polylang (usa la lingua di Polylang)
                        </option>
                    </select>
                </div>

                <!-- Selettore lingua frontend -->
                <div class="olo-lang-setting-section">
                    <h2>Selettore lingua (frontend)</h2>

                    <label class="olo-lang-toggle-label">
                        <input type="checkbox" id="olo-lang-show-switcher"
                               <?php checked( ! empty( $config['show_switcher'] ) ); ?>>
                        Mostra selettore lingua nel frontend
                    </label>

                    <div class="olo-lang-switcher-options" id="olo-lang-switcher-options">
                        <p>
                            <label>Stile:</label>
                            <select id="olo-lang-switcher-style">
                                <option value="dropdown" <?php selected( $config['switcher_style'] ?? '', 'dropdown' ); ?>>Dropdown</option>
                                <option value="inline" <?php selected( $config['switcher_style'] ?? '', 'inline' ); ?>>Inline (bandierine)</option>
                            </select>
                        </p>
                        <p>
                            <label>Posizione:</label>
                            <select id="olo-lang-switcher-position">
                                <option value="bottom-right" <?php selected( $config['switcher_position'] ?? '', 'bottom-right' ); ?>>Basso a destra</option>
                                <option value="bottom-left" <?php selected( $config['switcher_position'] ?? '', 'bottom-left' ); ?>>Basso a sinistra</option>
                                <option value="top-right" <?php selected( $config['switcher_position'] ?? '', 'top-right' ); ?>>Alto a destra</option>
                                <option value="top-left" <?php selected( $config['switcher_position'] ?? '', 'top-left' ); ?>>Alto a sinistra</option>
                            </select>
                        </p>
                        <p class="description">
                            Puoi anche usare lo shortcode <code>[olo_lang_switcher]</code> per posizionare il selettore ovunque.
                        </p>
                    </div>
                </div>

                <p class="submit">
                    <button type="button" class="button button-primary" id="olo-lang-save-settings">
                        Salva impostazioni
                    </button>
                    <span class="olo-lang-save-feedback" id="olo-lang-save-feedback"></span>
                </p>

            </form>
        </div>
        <?php
    }
}
