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
                #<?php echo esc_attr( $uid ); ?> { background: rgba(0,0,0,<?php echo $overlay_alpha; ?>) !important; <?php if ( $overlay_blur > 0 ) : ?>backdrop-filter:blur(<?php echo $overlay_blur; ?>px);-webkit-backdrop-filter:blur(<?php echo $overlay_blur; ?>px);<?php endif; ?> }
                #<?php echo esc_attr( $uid ); ?> .uk-modal-dialog { max-width:<?php echo $max_width; ?>px; <?php if ( $radius > 0 ) : ?>border-radius:<?php echo $radius; ?>px;overflow:hidden;<?php endif; ?> box-shadow:0 20px 60px rgba(0,0,0,.2); }
                #<?php echo esc_attr( $uid ); ?> .olo-template { width:100%;left:0;transform:none; }
                #<?php echo esc_attr( $uid ); ?> .olo-frontend-grid { --olo-container-max-width:none; }
            </style>
            <div id="<?php echo esc_attr( $uid ); ?>" <?php echo $modal_attr; ?>>
                <div class="uk-modal-dialog uk-margin-auto-vertical">
                    <button class="uk-modal-close-default" type="button" uk-close></button>
                    <div class="uk-modal-body" uk-overflow-auto style="max-height:80vh">
                        <?php echo $content; ?>
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
                if(canShow()){setTimeout(openPopup,<?php echo max( 300, $delay * 1000 ); ?>)}
                <?php elseif ( $trigger === 'scroll_percent' ) : ?>
                var sp=<?php echo intval( $scroll_percent ); ?>;
                function chkScr(){var dh=document.documentElement.scrollHeight-window.innerHeight;if(dh<=0)return;if((window.scrollY/dh)*100>=sp){openPopup();window.removeEventListener('scroll',chkScr)}}
                if(canShow()){window.addEventListener('scroll',chkScr,{passive:true})}
                <?php elseif ( $trigger === 'exit_intent' ) : ?>
                function exitI(e){if(e.clientY<=0){openPopup();document.documentElement.removeEventListener('mouseleave',exitI)}}
                if(canShow()){if(window.matchMedia('(pointer:fine)').matches){document.documentElement.addEventListener('mouseleave',exitI)}}
                <?php elseif ( $trigger === 'timer' ) : ?>
                if(canShow()){setTimeout(openPopup,<?php echo max( 300, $delay * 1000 ); ?>)}
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
                'permission_callback' => function () {
                    return current_user_can( 'edit_posts' );
                },
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'save_popups' ],
                'permission_callback' => function () {
                    return current_user_can( 'edit_posts' );
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
            return new WP_Error( 'invalid', 'Dati non validi', [ 'status' => 400 ] );
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
        add_submenu_page(
            'olobuild',
            'Popup Globali',
            'Popup Globali',
            'manage_options',
            'olo-global-popups',
            [ $this, 'render_admin_page' ]
        );
    }

    public function render_admin_page() {
        $popups   = get_option( 'olo_global_popups', [] );
        $db       = new Olo_Database();
        $tpls_raw = $db->get_all_templates();
        $templates = [];
        foreach ( $tpls_raw as $t ) {
            $templates[] = [
                'id'   => $t['id'],
                'name' => $t['name'],
                'type' => $t['type'] ?? 'page',
            ];
        }
        ?>
        <div class="wrap">
            <h1>Popup Globali — Olobuild</h1>
            <p>Configura popup che appaiono automaticamente in base a condizioni di visualizzazione.</p>

            <div id="olo-global-popups-app">
                <noscript>JavaScript richiesto.</noscript>
            </div>

            <script>
            (function(){
                var popups = <?php echo wp_json_encode( $popups ); ?>;
                var templates = <?php echo wp_json_encode( $templates ); ?>;
                var restUrl = '<?php echo esc_js( rest_url( 'olo/v1/global-popups' ) ); ?>';
                var nonce = '<?php echo esc_js( wp_create_nonce( 'wp_rest' ) ); ?>';

                var app = document.getElementById('olo-global-popups-app');
                if (!app) return;

                var triggerOptions = [
                    {v:'page_load',l:'Caricamento pagina'},
                    {v:'scroll_percent',l:'Scroll %'},
                    {v:'exit_intent',l:'Exit Intent'},
                    {v:'timer',l:'Timer'},
                    {v:'inactivity',l:'Inattivita\''}
                ];

                var freqOptions = [
                    {v:'always',l:'Sempre'},
                    {v:'once_session',l:'1 volta per sessione'},
                    {v:'once_day',l:'1 volta al giorno'},
                    {v:'once_week',l:'1 volta a settimana'},
                    {v:'once_ever',l:'Solo 1 volta'}
                ];

                var conditionTypes = [
                    {v:'entire_site',l:'Tutto il sito'},
                    {v:'front_page',l:'Homepage'},
                    {v:'page',l:'Pagina specifica'},
                    {v:'post',l:'Articolo specifico'},
                    {v:'post_type',l:'Tipo contenuto'},
                    {v:'archive',l:'Archivio'},
                    {v:'category',l:'Categoria'},
                    {v:'user_logged_in',l:'Utente loggato'},
                    {v:'user_logged_out',l:'Utente non loggato'},
                    {v:'device_desktop',l:'Desktop'},
                    {v:'device_mobile',l:'Mobile'},
                    {v:'404',l:'Pagina 404'},
                    {v:'search',l:'Ricerca'}
                ];

                function render(){
                    var html = '';
                    popups.forEach(function(p,i){
                        html += '<div style="border:1px solid #ccd0d4;padding:15px;margin:10px 0;background:#fff;border-radius:4px">';
                        html += '<div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">';
                        html += '<label><input type="checkbox" '+(p.enabled?'checked':'')+' onchange="oloGPToggle('+i+',this.checked)"> Attivo</label>';
                        html += '<input type="text" value="'+escHtml(p.name||'')+'" placeholder="Nome popup" style="flex:1" onchange="oloGPField('+i+',\'name\',this.value)">';
                        html += '<button class="button" onclick="oloGPRemove('+i+')">Rimuovi</button>';
                        html += '</div>';

                        html += '<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px">';
                        html += '<label>Template:<br><select onchange="oloGPField('+i+',\'template_id\',this.value)">';
                        html += '<option value="0">— Seleziona —</option>';
                        templates.forEach(function(t){
                            html += '<option value="'+t.id+'"'+(p.template_id==t.id?' selected':'')+'>'+escHtml(t.name)+' ('+t.type+')</option>';
                        });
                        html += '</select></label>';

                        html += '<label>Trigger:<br><select onchange="oloGPField('+i+',\'trigger\',this.value)">';
                        triggerOptions.forEach(function(o){
                            html += '<option value="'+o.v+'"'+(p.trigger===o.v?' selected':'')+'>'+o.l+'</option>';
                        });
                        html += '</select></label>';

                        html += '<label>Frequenza:<br><select onchange="oloGPField('+i+',\'frequency\',this.value)">';
                        freqOptions.forEach(function(o){
                            html += '<option value="'+o.v+'"'+(p.frequency===o.v?' selected':'')+'>'+o.l+'</option>';
                        });
                        html += '</select></label>';
                        html += '</div>';

                        html += '<div style="margin-top:10px"><strong>Condizioni:</strong> ';
                        html += '<select onchange="oloGPField('+i+',\'conditions_logic\',this.value)" style="margin:0 5px">';
                        html += '<option value="OR"'+(p.conditions_logic!=='AND'?' selected':'')+'>OR (almeno una)</option>';
                        html += '<option value="AND"'+(p.conditions_logic==='AND'?' selected':'')+'>AND (tutte)</option>';
                        html += '</select>';
                        html += '<button class="button button-small" onclick="oloGPAddCond('+i+')">+ Condizione</button>';
                        html += '</div>';

                        if(p.conditions){if(p.conditions.length>0){
                            html += '<div style="margin-top:5px;padding-left:10px">';
                            p.conditions.forEach(function(c,ci){
                                html += '<div style="display:flex;gap:5px;margin:3px 0;align-items:center">';
                                html += '<label><input type="checkbox" '+(c.negate?'checked':'')+' onchange="oloGPCondField('+i+','+ci+',\'negate\',this.checked)"> NON</label>';
                                html += '<select onchange="oloGPCondField('+i+','+ci+',\'type\',this.value)">';
                                conditionTypes.forEach(function(ct){
                                    html += '<option value="'+ct.v+'"'+(c.type===ct.v?' selected':'')+'>'+ct.l+'</option>';
                                });
                                html += '</select>';
                                html += '<input type="text" value="'+escHtml(c.value||'')+'" placeholder="Valore (ID, slug)" style="width:120px" onchange="oloGPCondField('+i+','+ci+',\'value\',this.value)">';
                                html += '<button class="button button-small" onclick="oloGPRemoveCond('+i+','+ci+')">x</button>';
                                html += '</div>';
                            });
                            html += '</div>';
                        }}

                        html += '</div>';
                    });

                    html += '<p><button class="button button-primary" onclick="oloGPAdd()">+ Aggiungi Popup</button> ';
                    html += '<button class="button button-primary" onclick="oloGPSave()" style="margin-left:10px">Salva Tutto</button></p>';

                    app.innerHTML = html;
                }

                function escHtml(s){var d=document.createElement('div');d.textContent=s;return d.innerHTML}

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
                    .then(function(d){if(d.success){alert('Salvato!')}else{alert('Errore: '+JSON.stringify(d))}})
                    .catch(function(e){alert('Errore: '+e.message)});
                };

                render();
            })();
            </script>
        </div>
        <?php
    }
}
