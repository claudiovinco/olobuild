<?php
/**
 * Olo_Global_Popups — Display popups globally based on conditions.
 *
 * Allows popups to be shown across the entire site based on:
 * - Page/post type conditions
 * - Taxonomy conditions
 * - User logged in/out
 * - Specific pages/posts
 * - Device type
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Global_Popups {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init() {
        // Output global popups in footer
        add_action( 'wp_footer', [ $this, 'render_global_popups' ], 50 );

        // REST API for managing global popups
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );

        // Admin menu
        add_action( 'admin_menu', [ $this, 'add_admin_page' ] );
    }

    /* ─────────────────────────────────────────────
     * Frontend Output
     * ───────────────────────────────────────────── */

    public function render_global_popups() {
        if ( is_admin() ) {
            return;
        }

        $popups = get_option( 'olo_global_popups', [] );
        if ( empty( $popups ) || ! is_array( $popups ) ) {
            return;
        }

        foreach ( $popups as $popup ) {
            if ( empty( $popup['enabled'] ) ) {
                continue;
            }

            // Check display conditions
            if ( ! $this->should_display( $popup ) ) {
                continue;
            }

            // Render the popup template
            $template_id = intval( $popup['template_id'] ?? 0 );
            if ( ! $template_id ) {
                continue;
            }

            $uid             = 'olo-gpop-' . $template_id;
            $trigger         = sanitize_text_field( $popup['trigger'] ?? 'page_load' );
            $delay           = max( 0, intval( $popup['delay'] ?? 3 ) );
            $scroll_percent  = max( 10, min( 100, intval( $popup['scroll_percent'] ?? 50 ) ) );
            $frequency       = sanitize_text_field( $popup['frequency'] ?? 'once_session' );
            $animation       = sanitize_html_class( $popup['animation'] ?? 'fade' );
            $overlay_opacity = max( 0, min( 100, intval( $popup['overlay_opacity'] ?? 60 ) ) );
            $overlay_blur    = max( 0, min( 20, intval( $popup['overlay_blur'] ?? 0 ) ) );
            $close_overlay   = ! isset( $popup['close_overlay'] ) || ! empty( $popup['close_overlay'] );
            $radius          = max( 0, intval( $popup['radius'] ?? 12 ) );
            $max_width       = intval( $popup['max_width'] ?? 700 );

            // Render template content
            $db       = new Olo_Database();
            $template = $db->get_template( $template_id );
            if ( ! $template || empty( $template['content'] ) ) {
                continue;
            }

            $renderer  = new Olo_Frontend_Renderer();
            $content   = do_shortcode( '[olo_template id="' . $template_id . '"]' );

            $overlay_alpha = round( $overlay_opacity / 100, 2 );
            $modal_opts = [];
            if ( ! $close_overlay ) {
                $modal_opts[] = 'bg-close: false';
            }
            $modal_attr = 'uk-modal' . ( ! empty( $modal_opts ) ? '="' . implode( '; ', $modal_opts ) . '"' : '' );

            ?>
            <style>
                #<?php echo esc_attr( $uid ); ?> { background: rgba(0,0,0,<?php echo (float) $overlay_alpha; ?>) !important; <?php if ( $overlay_blur > 0 ) : ?>backdrop-filter:blur(<?php echo (int) $overlay_blur; ?>px);-webkit-backdrop-filter:blur(<?php echo (int) $overlay_blur; ?>px);<?php endif; ?> }
                #<?php echo esc_attr( $uid ); ?> .uk-modal-dialog { max-width:<?php echo (int) $max_width; ?>px; <?php if ( $radius > 0 ) : ?>border-radius:<?php echo (int) $radius; ?>px;overflow:hidden;<?php endif; ?> box-shadow:0 20px 60px rgba(0,0,0,.2); }
                #<?php echo esc_attr( $uid ); ?> .olo-template { width:100%;left:0;transform:none; }
                #<?php echo esc_attr( $uid ); ?> .olo-frontend-grid { --olo-container-max-width:none; }
            </style>
            <div id="<?php echo esc_attr( $uid ); ?>" <?php echo $modal_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attribute composed only of the fixed internal literals 'uk-modal' and 'bg-close: false' ?>>
                <div class="uk-modal-dialog uk-margin-auto-vertical">
                    <button class="uk-modal-close-default" type="button" uk-close></button>
                    <div class="uk-modal-body" uk-overflow-auto style="max-height:80vh">
                        <?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- popup body is the rendered [olo_template] shortcode output (Olo_Frontend_Renderer); each tile escapes its own output at build time ?>
                    </div>
                </div>
            </div>
            <script>
            (function(){
                var el=document.getElementById('<?php echo esc_js( $uid ); ?>');
                if(!el)return;
                var triggered=false;
                var popupKey='olo_gpop_<?php echo intval( $template_id ); ?>';
                var freq='<?php echo esc_js( $frequency ); ?>';

                function getCk(n){var m=document.cookie.match('(^|; )'+n+'=([^;]*)');return m?decodeURIComponent(m[2]):null}
                function setCk(n,v,d){var dt=new Date();dt.setTime(dt.getTime()+(d*864e5));document.cookie=n+'='+encodeURIComponent(v)+';expires='+dt.toUTCString()+';path=/;SameSite=Lax'}

                function canShow(){
                    if(freq==='once_session'){try{if(sessionStorage.getItem(popupKey))return false}catch(e){}}
                    if(freq==='once_day'){if(getCk(popupKey+'_d'))return false}
                    if(freq==='once_week'){if(getCk(popupKey+'_w'))return false}
                    if(freq==='once_ever'){try{if(localStorage.getItem(popupKey))return false}catch(e){}}
                    return true;
                }
                function markShown(){
                    if(freq==='once_session'){try{sessionStorage.setItem(popupKey,'1')}catch(e){}}
                    if(freq==='once_day'){setCk(popupKey+'_d','1',1)}
                    if(freq==='once_week'){setCk(popupKey+'_w','1',7)}
                    if(freq==='once_ever'){try{localStorage.setItem(popupKey,'1')}catch(e){}}
                }
                function openPopup(){
                    if(triggered)return;
                    if(!canShow())return;
                    triggered=true;markShown();
                    if(typeof UIkit!=='undefined'){UIkit.modal(el).show()}
                }

                <?php if ( $trigger === 'page_load' ) : ?>
                if(canShow()){setTimeout(openPopup,<?php echo (int) max( 300, $delay * 1000 ); ?>)}
                <?php elseif ( $trigger === 'scroll_percent' ) : ?>
                var sp=<?php echo intval( $scroll_percent ); ?>;
                function chkScr(){var dh=document.documentElement.scrollHeight-window.innerHeight;if(dh<=0)return;if((window.scrollY/dh)*100>=sp){openPopup();window.removeEventListener('scroll',chkScr)}}
                if(canShow()){window.addEventListener('scroll',chkScr,{passive:true})}
                <?php elseif ( $trigger === 'exit_intent' ) : ?>
                function exitI(e){if(e.clientY<=0){openPopup();document.documentElement.removeEventListener('mouseleave',exitI)}}
                if(canShow()){if(window.matchMedia('(pointer:fine)').matches){document.documentElement.addEventListener('mouseleave',exitI)}}
                <?php elseif ( $trigger === 'timer' ) : ?>
                if(canShow()){setTimeout(openPopup,<?php echo (int) max( 300, $delay * 1000 ); ?>)}
                <?php elseif ( $trigger === 'inactivity' ) : ?>
                var inD=<?php echo intval( $popup['inactivity_delay'] ?? 30 ); ?>;var inT=null;
                function rstI(){if(triggered)return;if(inT)clearTimeout(inT);inT=setTimeout(function(){openPopup();clnI()},inD*1000)}
                function clnI(){window.removeEventListener('mousemove',rstI);window.removeEventListener('keydown',rstI);window.removeEventListener('scroll',rstI);window.removeEventListener('touchstart',rstI)}
                if(canShow()){window.addEventListener('mousemove',rstI,{passive:true});window.addEventListener('keydown',rstI,{passive:true});window.addEventListener('scroll',rstI,{passive:true});window.addEventListener('touchstart',rstI,{passive:true});rstI()}
                <?php endif; ?>
            })();
            </script>
            <?php
        }
    }

    /* ─────────────────────────────────────────────
     * Display Conditions
     * ───────────────────────────────────────────── */

    private function should_display( $popup ) {
        $conditions = $popup['conditions'] ?? [];
        if ( empty( $conditions ) ) {
            return true; // No conditions = show everywhere
        }

        $logic = $popup['conditions_logic'] ?? 'OR';

        $results = [];
        foreach ( $conditions as $cond ) {
            $results[] = $this->evaluate_condition( $cond );
        }

        if ( $logic === 'AND' ) {
            return ! in_array( false, $results, true );
        }

        // OR logic
        return in_array( true, $results, true );
    }

    private function evaluate_condition( $cond ) {
        $type   = $cond['type'] ?? '';
        $value  = $cond['value'] ?? '';
        $negate = ! empty( $cond['negate'] );

        $result = false;

        switch ( $type ) {
            case 'entire_site':
                $result = true;
                break;

            case 'front_page':
                $result = is_front_page();
                break;

            case 'blog_page':
                $result = is_home();
                break;

            case 'page':
                if ( $value === 'all' ) {
                    $result = is_page();
                } else {
                    $result = is_page( intval( $value ) );
                }
                break;

            case 'post':
                if ( $value === 'all' ) {
                    $result = is_singular( 'post' );
                } else {
                    $result = is_singular( 'post' ) && get_the_ID() === intval( $value );
                }
                break;

            case 'post_type':
                $result = is_singular( sanitize_text_field( $value ) );
                break;

            case 'archive':
                if ( empty( $value ) || $value === 'all' ) {
                    $result = is_archive();
                } else {
                    $result = is_post_type_archive( sanitize_text_field( $value ) );
                }
                break;

            case 'category':
                if ( is_singular( 'post' ) ) {
                    $result = has_category( intval( $value ) );
                } else {
                    $result = is_category( intval( $value ) );
                }
                break;

            case 'tag':
                if ( is_singular( 'post' ) ) {
                    $result = has_tag( sanitize_text_field( $value ) );
                } else {
                    $result = is_tag( sanitize_text_field( $value ) );
                }
                break;

            case 'taxonomy':
                $parts = explode( ':', $value );
                if ( count( $parts ) === 2 ) {
                    $result = is_tax( $parts[0], $parts[1] );
                }
                break;

            case 'user_logged_in':
                $result = is_user_logged_in();
                break;

            case 'user_logged_out':
                $result = ! is_user_logged_in();
                break;

            case 'user_role':
                if ( is_user_logged_in() ) {
                    $user = wp_get_current_user();
                    $result = in_array( sanitize_text_field( $value ), $user->roles, true );
                }
                break;

            case 'device_desktop':
                $result = ! wp_is_mobile();
                break;

            case 'device_mobile':
                $result = wp_is_mobile();
                break;

            case '404':
                $result = is_404();
                break;

            case 'search':
                $result = is_search();
                break;

            case 'woocommerce_shop':
                if ( function_exists( 'is_shop' ) ) {
                    $result = is_shop();
                }
                break;

            case 'woocommerce_product':
                if ( function_exists( 'is_product' ) ) {
                    $result = is_product();
                }
                break;

            case 'woocommerce_cart':
                if ( function_exists( 'is_cart' ) ) {
                    $result = is_cart();
                }
                break;

            case 'woocommerce_checkout':
                if ( function_exists( 'is_checkout' ) ) {
                    $result = is_checkout();
                }
                break;
        }

        return $negate ? ! $result : $result;
    }

    /* ─────────────────────────────────────────────
     * REST API
     * ───────────────────────────────────────────── */

    public function register_routes() {
        register_rest_route( 'olo/v1', '/global-popups', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_popups' ],
                // Popup iniettati su TUTTO il frontend: impostazione globale, solo admin.
                'permission_callback' => function () {
                    return current_user_can( 'manage_options' );
                },
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'save_popups' ],
                // Scrittura globale: manage_options + nonce wp_rest (anti-CSRF).
                'permission_callback' => function ( $request ) {
                    if ( ! current_user_can( 'manage_options' ) ) {
                        return false;
                    }
                    $nonce = $request->get_header( 'x-wp-nonce' );
                    if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
                        return new WP_Error( 'rest_forbidden', 'Nonce non valido.', [ 'status' => 403 ] );
                    }
                    return true;
                },
            ],
        ] );
    }

    public function get_popups( $request ) {
        return rest_ensure_response( get_option( 'olo_global_popups', [] ) );
    }

    public function save_popups( $request ) {
        $popups = $request->get_json_params();
        if ( ! is_array( $popups ) ) {
            return new WP_Error( 'invalid', __( 'Dati non validi', 'olobuild' ), [ 'status' => 400 ] );
        }
        // Il client (PopupsTab.vue) invia { popups: [...] }; accetta anche l'array nudo.
        // Senza questo unwrap il salvataggio dei popup dalla UI non persisteva.
        if ( isset( $popups['popups'] ) && is_array( $popups['popups'] ) ) {
            $popups = $popups['popups'];
        }

        // Sanitize each popup
        $clean = [];
        foreach ( $popups as $popup ) {
            $clean[] = [
                'enabled'          => ! empty( $popup['enabled'] ),
                'name'             => sanitize_text_field( $popup['name'] ?? '' ),
                'template_id'      => intval( $popup['template_id'] ?? 0 ),
                'trigger'          => sanitize_text_field( $popup['trigger'] ?? 'page_load' ),
                'delay'            => max( 0, intval( $popup['delay'] ?? 3 ) ),
                'scroll_percent'   => max( 10, min( 100, intval( $popup['scroll_percent'] ?? 50 ) ) ),
                'inactivity_delay' => max( 5, intval( $popup['inactivity_delay'] ?? 30 ) ),
                'frequency'        => sanitize_text_field( $popup['frequency'] ?? 'once_session' ),
                'animation'        => sanitize_html_class( $popup['animation'] ?? 'fade' ),
                'overlay_opacity'  => max( 0, min( 100, intval( $popup['overlay_opacity'] ?? 60 ) ) ),
                'overlay_blur'     => max( 0, min( 20, intval( $popup['overlay_blur'] ?? 0 ) ) ),
                'close_overlay'    => ! isset( $popup['close_overlay'] ) || ! empty( $popup['close_overlay'] ),
                'radius'           => max( 0, intval( $popup['radius'] ?? 12 ) ),
                'max_width'        => max( 300, intval( $popup['max_width'] ?? 700 ) ),
                'conditions'       => $this->sanitize_conditions( $popup['conditions'] ?? [] ),
                'conditions_logic' => in_array( $popup['conditions_logic'] ?? 'OR', [ 'AND', 'OR' ], true ) ? $popup['conditions_logic'] : 'OR',
            ];
        }

        update_option( 'olo_global_popups', $clean, false );
        return rest_ensure_response( [ 'success' => true ] );
    }

    private function sanitize_conditions( $conditions ) {
        if ( ! is_array( $conditions ) ) {
            return [];
        }

        $clean = [];
        foreach ( $conditions as $cond ) {
            $clean[] = [
                'type'   => sanitize_text_field( $cond['type'] ?? '' ),
                'value'  => sanitize_text_field( $cond['value'] ?? '' ),
                'negate' => ! empty( $cond['negate'] ),
            ];
        }
        return $clean;
    }

    /* ─────────────────────────────────────────────
     * Admin Page
     * ───────────────────────────────────────────── */

    public function add_admin_page() {
        // v1.0.31 — pagina migrata in ?page=olobuilder-settings&tab=popups
        // La classe resta attiva per il rendering dei popup nel frontend in base alle condizioni.
    }

    public function render_admin_page() {
        $popups   = get_option( 'olo_global_popups', [] );
        $db       = new Olo_Database();
        $result   = $db->list_templates( [ 'per_page' => 500, 'orderby' => 'title', 'order' => 'ASC' ] );
        $tpls_raw = $result['items'] ?? [];
        $templates = [];
        foreach ( $tpls_raw as $t ) {
            $templates[] = [
                'id'   => $t['id'],
                'name' => $t['title'] ?? '',
                'type' => $t['type'] ?? 'page',
            ];
        }
        $popup_count = is_array( $popups ) ? count( $popups ) : 0;
        $active_count = 0;
        if ( is_array( $popups ) ) {
            foreach ( $popups as $p ) if ( ! empty( $p['enabled'] ) ) $active_count++;
        }
        ?>
        <?php Olo_Builder::cockpit_shell_open( '<b>' . esc_html__( 'Popup Globali', 'olobuild' ) . '</b>' ); ?>
        <main class="olo-cockpit-main olo-cockpit-legacy">
            <?php
            // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML built by Olo_Builder::cockpit_page_head(), which escapes via esc_html()/wp_kses_post() internally; counts are int-cast.
            echo Olo_Builder::cockpit_page_head( [
                'title' => __( 'Popup Globali', 'olobuild' ),
                'sub'   => sprintf(
                    /* translators: 1: total popups, 2: active popups */
                    __( '%1$s popup configurati · %2$s attivi · trigger automatici per condizioni di visualizzazione', 'olobuild' ),
                    '<b>' . (int) $popup_count . '</b>',
                    '<b>' . (int) $active_count . '</b>'
                ),
            ] );
            // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
            ?>

            <div class="olo-card" style="margin-top:16px;margin-bottom:24px">
                <div class="olo-card-body" style="padding-top:0">
                    <div id="olo-global-popups-app">
                        <noscript><?php esc_html_e( 'JavaScript richiesto.', 'olobuild' ); ?></noscript>
                    </div>
                </div>
            </div>

            <div id="olo-gpop-msg"></div>

            <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline admin JS below: every dynamic value is emitted via wp_json_encode() or esc_js(). ?>
            <script>
            (function(){
                var popups = <?php echo wp_json_encode( $popups ); ?>;
                var templates = <?php echo wp_json_encode( $templates ); ?>;
                var restUrl = '<?php echo esc_js( rest_url( 'olo/v1/global-popups' ) ); ?>';
                var nonce = '<?php echo esc_js( wp_create_nonce( 'wp_rest' ) ); ?>';

                var app = document.getElementById('olo-global-popups-app');
                if (!app) return;

                var gpopI18n = {
                    noPopupsConfigured: <?php echo wp_json_encode( __( 'Nessun popup configurato', 'olobuild' ) ); ?>,
                    active:             <?php echo wp_json_encode( __( 'Attivo', 'olobuild' ) ); ?>,
                    disabled:           <?php echo wp_json_encode( __( 'Disattivato', 'olobuild' ) ); ?>,
                    popup:              <?php echo wp_json_encode( __( 'Popup', 'olobuild' ) ); ?>,
                    remove:             <?php echo wp_json_encode( __( 'Rimuovi', 'olobuild' ) ); ?>,
                    popupName:          <?php echo wp_json_encode( __( 'Nome popup', 'olobuild' ) ); ?>,
                    template:           <?php echo wp_json_encode( __( 'Template', 'olobuild' ) ); ?>,
                    selectPlaceholder:  <?php echo wp_json_encode( __( 'Seleziona', 'olobuild' ) ); ?>,
                    trigger:            <?php echo wp_json_encode( __( 'Trigger', 'olobuild' ) ); ?>,
                    frequency:          <?php echo wp_json_encode( __( 'Frequenza', 'olobuild' ) ); ?>,
                    conditions:         <?php echo wp_json_encode( __( 'Condizioni', 'olobuild' ) ); ?>,
                    orAtLeastOne:       <?php echo wp_json_encode( __( 'OR (almeno una)', 'olobuild' ) ); ?>,
                    andAll:             <?php echo wp_json_encode( __( 'AND (tutte)', 'olobuild' ) ); ?>,
                    condition:          <?php echo wp_json_encode( __( 'Condizione', 'olobuild' ) ); ?>,
                    not:                <?php echo wp_json_encode( __( 'NON', 'olobuild' ) ); ?>,
                    valueIdSlug:        <?php echo wp_json_encode( __( 'Valore (ID, slug)', 'olobuild' ) ); ?>,
                    addPopup:           <?php echo wp_json_encode( __( 'Aggiungi Popup', 'olobuild' ) ); ?>,
                    saveAll:            <?php echo wp_json_encode( __( 'Salva Tutto', 'olobuild' ) ); ?>,
                    saved:              <?php echo wp_json_encode( __( 'Popup salvati con successo!', 'olobuild' ) ); ?>,
                    error:              <?php echo wp_json_encode( __( 'Errore', 'olobuild' ) ); ?>
                };

                var triggerOptions = [
                    {v:'page_load',l:<?php echo wp_json_encode( __( 'Caricamento pagina', 'olobuild' ) ); ?>},
                    {v:'scroll_percent',l:<?php echo wp_json_encode( __( 'Scroll %', 'olobuild' ) ); ?>},
                    {v:'exit_intent',l:<?php echo wp_json_encode( __( 'Exit Intent', 'olobuild' ) ); ?>},
                    {v:'timer',l:<?php echo wp_json_encode( __( 'Timer', 'olobuild' ) ); ?>},
                    {v:'inactivity',l:<?php echo wp_json_encode( __( "Inattività", 'olobuild' ) ); ?>}
                ];

                var freqOptions = [
                    {v:'always',l:<?php echo wp_json_encode( __( 'Sempre', 'olobuild' ) ); ?>},
                    {v:'once_session',l:<?php echo wp_json_encode( __( '1 volta per sessione', 'olobuild' ) ); ?>},
                    {v:'once_day',l:<?php echo wp_json_encode( __( '1 volta al giorno', 'olobuild' ) ); ?>},
                    {v:'once_week',l:<?php echo wp_json_encode( __( '1 volta a settimana', 'olobuild' ) ); ?>},
                    {v:'once_ever',l:<?php echo wp_json_encode( __( 'Solo 1 volta', 'olobuild' ) ); ?>}
                ];

                var conditionTypes = [
                    {v:'entire_site',l:<?php echo wp_json_encode( __( 'Tutto il sito', 'olobuild' ) ); ?>},
                    {v:'front_page',l:<?php echo wp_json_encode( __( 'Homepage', 'olobuild' ) ); ?>},
                    {v:'page',l:<?php echo wp_json_encode( __( 'Pagina specifica', 'olobuild' ) ); ?>},
                    {v:'post',l:<?php echo wp_json_encode( __( 'Articolo specifico', 'olobuild' ) ); ?>},
                    {v:'post_type',l:<?php echo wp_json_encode( __( 'Tipo contenuto', 'olobuild' ) ); ?>},
                    {v:'archive',l:<?php echo wp_json_encode( __( 'Archivio', 'olobuild' ) ); ?>},
                    {v:'category',l:<?php echo wp_json_encode( __( 'Categoria', 'olobuild' ) ); ?>},
                    {v:'user_logged_in',l:<?php echo wp_json_encode( __( 'Utente loggato', 'olobuild' ) ); ?>},
                    {v:'user_logged_out',l:<?php echo wp_json_encode( __( 'Utente non loggato', 'olobuild' ) ); ?>},
                    {v:'device_desktop',l:<?php echo wp_json_encode( __( 'Desktop', 'olobuild' ) ); ?>},
                    {v:'device_mobile',l:<?php echo wp_json_encode( __( 'Mobile', 'olobuild' ) ); ?>},
                    {v:'404',l:<?php echo wp_json_encode( __( 'Pagina 404', 'olobuild' ) ); ?>},
                    {v:'search',l:<?php echo wp_json_encode( __( 'Ricerca', 'olobuild' ) ); ?>}
                ];

                function render(){
                    var html = '';

                    if(popups.length === 0){
                        html += '<div class="olo-empty">';
                        html += '<div class="olo-empty-icon"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#bbb" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg></div>';
                        html += '<p>' + escHtml(gpopI18n.noPopupsConfigured) + '</p>';
                        html += '</div>';
                    }

                    popups.forEach(function(p,i){
                        var statusBadge = p.enabled ? '<span class="olo-badge green">' + escHtml(gpopI18n.active) + '</span>' : '<span class="olo-badge gray">' + escHtml(gpopI18n.disabled) + '</span>';

                        html += '<div class="olo-card" style="margin-bottom:16px">';
                        html += '<div class="olo-card-head">';
                        html += '<div class="olo-card-icon black"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg></div>';
                        html += '<div style="flex:1;min-width:0">';
                        html += '<div style="display:flex;align-items:center;gap:8px">';
                        html += '<strong>' + escHtml(p.name || gpopI18n.popup + ' #'+(i+1)) + '</strong> ' + statusBadge;
                        html += '</div>';
                        html += '</div>';
                        html += '<div style="display:flex;align-items:center;gap:10px">';
                        html += '<label class="olo-toggle"><input type="checkbox" '+(p.enabled?'checked':'')+' onchange="oloGPToggle('+i+',this.checked)"><span class="olo-toggle-slider"></span></label>';
                        html += '<button class="olo-btn-danger" onclick="oloGPRemove('+i+')"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg> ' + escHtml(gpopI18n.remove) + '</button>';
                        html += '</div>';
                        html += '</div>';

                        html += '<div class="olo-card-body">';

                        // Nome popup
                        html += '<div class="olo-field-row">';
                        html += '<div class="olo-field-info"><label>' + escHtml(gpopI18n.popupName) + '</label></div>';
                        html += '<div class="olo-field-input-wrap"><input type="text" class="olo-field-input" value="'+escHtml(p.name||'')+'" placeholder="'+escHtml(gpopI18n.popupName)+'" onchange="oloGPField('+i+',\'name\',this.value)"></div>';
                        html += '</div>';

                        // Template
                        html += '<div class="olo-field-row">';
                        html += '<div class="olo-field-info"><label>' + escHtml(gpopI18n.template) + '</label></div>';
                        html += '<div class="olo-field-input-wrap"><select class="olo-field-input" onchange="oloGPField('+i+',\'template_id\',this.value)">';
                        html += '<option value="0">\u2014 ' + escHtml(gpopI18n.selectPlaceholder) + ' \u2014</option>';
                        templates.forEach(function(t){
                            html += '<option value="'+t.id+'"'+(p.template_id==t.id?' selected':'')+'>'+escHtml(t.name)+' ('+t.type+')</option>';
                        });
                        html += '</select></div>';
                        html += '</div>';

                        // Trigger
                        html += '<div class="olo-field-row">';
                        html += '<div class="olo-field-info"><label>' + escHtml(gpopI18n.trigger) + '</label></div>';
                        html += '<div class="olo-field-input-wrap"><select class="olo-field-input" onchange="oloGPField('+i+',\'trigger\',this.value)">';
                        triggerOptions.forEach(function(o){
                            html += '<option value="'+o.v+'"'+(p.trigger===o.v?' selected':'')+'>'+o.l+'</option>';
                        });
                        html += '</select></div>';
                        html += '</div>';

                        // Frequenza
                        html += '<div class="olo-field-row">';
                        html += '<div class="olo-field-info"><label>' + escHtml(gpopI18n.frequency) + '</label></div>';
                        html += '<div class="olo-field-input-wrap"><select class="olo-field-input" onchange="oloGPField('+i+',\'frequency\',this.value)">';
                        freqOptions.forEach(function(o){
                            html += '<option value="'+o.v+'"'+(p.frequency===o.v?' selected':'')+'>'+o.l+'</option>';
                        });
                        html += '</select></div>';
                        html += '</div>';

                        // Condizioni
                        html += '<div class="olo-field-row" style="align-items:flex-start">';
                        html += '<div class="olo-field-info"><label>' + escHtml(gpopI18n.conditions) + '</label></div>';
                        html += '<div class="olo-field-input-wrap">';
                        html += '<div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">';
                        html += '<select class="olo-field-input" style="width:auto" onchange="oloGPField('+i+',\'conditions_logic\',this.value)">';
                        html += '<option value="OR"'+(p.conditions_logic!=='AND'?' selected':'')+'>'+escHtml(gpopI18n.orAtLeastOne)+'</option>';
                        html += '<option value="AND"'+(p.conditions_logic==='AND'?' selected':'')+'>'+escHtml(gpopI18n.andAll)+'</option>';
                        html += '</select>';
                        html += '<button class="olo-btn-orange" onclick="oloGPAddCond('+i+')"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg> ' + escHtml(gpopI18n.condition) + '</button>';
                        html += '</div>';

                        if(p.conditions){if(p.conditions.length>0){
                            p.conditions.forEach(function(c,ci){
                                html += '<div style="display:flex;gap:6px;margin:6px 0;align-items:center">';
                                html += '<label class="olo-toggle" style="min-width:auto"><input type="checkbox" '+(c.negate?'checked':'')+' onchange="oloGPCondField('+i+','+ci+',\'negate\',this.checked)"><span class="olo-toggle-slider"></span></label>';
                                html += '<span style="font-size:12px;color:#888;min-width:30px">' + escHtml(gpopI18n.not) + '</span>';
                                html += '<select class="olo-field-input" onchange="oloGPCondField('+i+','+ci+',\'type\',this.value)">';
                                conditionTypes.forEach(function(ct){
                                    html += '<option value="'+ct.v+'"'+(c.type===ct.v?' selected':'')+'>'+ct.l+'</option>';
                                });
                                html += '</select>';
                                html += '<input type="text" class="olo-field-input" value="'+escHtml(c.value||'')+'" placeholder="'+escHtml(gpopI18n.valueIdSlug)+'" style="width:130px" onchange="oloGPCondField('+i+','+ci+',\'value\',this.value)">';
                                html += '<button class="olo-btn-danger" style="padding:4px 8px;font-size:12px" onclick="oloGPRemoveCond('+i+','+ci+')"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"/></svg></button>';
                                html += '</div>';
                            });
                        }}

                        html += '</div>';
                        html += '</div>';

                        html += '</div>'; // card-body
                        html += '</div>'; // card
                    });

                    html += '<div style="display:flex;gap:10px;margin-top:20px">';
                    html += '<button class="olo-btn-orange" onclick="oloGPAdd()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg> ' + escHtml(gpopI18n.addPopup) + '</button>';
                    html += '<button class="olo-btn-save" onclick="oloGPSave()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> ' + escHtml(gpopI18n.saveAll) + '</button>';
                    html += '</div>';

                    app.innerHTML = html;
                }

                function escHtml(s){var d=document.createElement('div');d.textContent=s;return d.innerHTML}

                function showMsg(type, text){
                    var msgEl = document.getElementById('olo-gpop-msg');
                    if(msgEl){ msgEl.innerHTML = '<div class="olo-msg '+type+'">'+text+'</div>'; setTimeout(function(){ msgEl.innerHTML=''; }, 4000); }
                }

                window.oloGPToggle = function(i,v){ popups[i].enabled=v; render(); };
                window.oloGPField = function(i,k,v){ popups[i][k]=v; render(); };
                window.oloGPRemove = function(i){ popups.splice(i,1); render(); };
                window.oloGPAdd = function(){
                    popups.push({enabled:true,name:'',template_id:0,trigger:'page_load',delay:3,scroll_percent:50,inactivity_delay:30,frequency:'once_session',animation:'fade',overlay_opacity:60,overlay_blur:0,close_overlay:true,radius:12,max_width:700,conditions:[],conditions_logic:'OR'});
                    render();
                };
                window.oloGPAddCond = function(i){
                    if(!popups[i].conditions) popups[i].conditions=[];
                    popups[i].conditions.push({type:'entire_site',value:'',negate:false});
                    render();
                };
                window.oloGPCondField = function(i,ci,k,v){ popups[i].conditions[ci][k]=v; render(); };
                window.oloGPRemoveCond = function(i,ci){ popups[i].conditions.splice(ci,1); render(); };
                window.oloGPSave = function(){
                    fetch(restUrl,{method:'POST',headers:{'Content-Type':'application/json','X-WP-Nonce':nonce},body:JSON.stringify(popups)})
                    .then(function(r){return r.json()})
                    .then(function(d){if(d.success){showMsg('success',gpopI18n.saved)}else{showMsg('error',gpopI18n.error+': '+JSON.stringify(d))}})
                    .catch(function(e){showMsg('error',gpopI18n.error+': '+e.message)});
                };

                render();
            })();
            </script>
            <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </main>
        <?php Olo_Builder::cockpit_shell_close(); ?>
        <?php
    }
}
