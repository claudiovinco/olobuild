<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Popup_Tile extends Olobuild_Tile_Base {

    protected $type     = 'popup';
    protected $name     = 'Popup';
    protected $icon     = 'dashicons-external';
    protected $category = 'interactive';
    protected $defaults = [
        'mode'                  => 'simple',
        'button_text'           => 'Apri',
        'button_style'          => 'default',
        'button_size'           => '',
        'button_icon'           => '',
        'button_fullwidth'      => false,
        'modal_size'            => '',
        'modal_close_button'    => true,
        'modal_title'           => '',
        'modal_shadow'          => 'lg',
        'modal_overlay'         => '60',
        'modal_radius'          => '12',
        'modal_border_width'    => '0',
        'modal_border_color'    => '',
        'content'               => '<p>Contenuto del popup...</p>',
        'image'                 => '',
        'image_position'        => 'top',

        // Preset & granular controls (V3.26.1)
        'preset'                    => 'modal-classic',
        'modal_bg'                  => '#ffffff',
        'modal_text_color'          => '#1e293b',
        'modal_title_color'         => '#0f172a',
        'modal_title_size'          => 24,
        'modal_title_weight'        => '700',
        'modal_title_uppercase'     => false,
        'modal_title_letter_spacing'=> 0,
        'button_radius'             => 6,
        'button_uppercase'          => false,
        'button_letter_spacing'     => 0.02,
        'button_weight'             => '600',
        'effect_color'              => '',
        'effect_intensity'          => 'medium',
        'effect_speed'              => 0,

        // Effetti avanzati modale (v1.0.60+) — esposti come field per personalizzazione
        // dei preset audaci (era CSS hardcoded in get_preset_extra_css con !important).
        'modal_backdrop_blur'       => 0,
        'modal_backdrop_saturate'   => 100,
        'modal_border_style'        => 'solid',
        'modal_font_family'         => 'inherit',
        'modal_rotation'            => 0,
        'modal_perspective'         => 0,
        'modal_tilt_x'              => 0,
        'modal_glow_pulse'          => false,
        'modal_title_glow'          => false,
        'modal_scanlines'           => false,
        'modal_terminal_prompt'     => false,

        'template_id'           => 0,
        'popup_trigger'         => 'click',
        'popup_delay'           => 5,
        'popup_scroll_percent'  => 50,
        'popup_frequency'       => 'always',
        'popup_animation'       => 'fade',
        'popup_close_overlay'   => true,
        'popup_overlay_blur'    => 0,
        'scroll_percent'        => '50',
        'timer_delay'           => '5',
        'inactivity_delay'      => '30',
        'show_max_times'        => '0',
        'show_once_per_session' => false,
        'display_device'        => '',
        'display_logged'        => '',
        'display_date_from'     => '',
        'display_date_to'       => '',
        'display_referrer'      => '',
        'display_woo_cart'      => '',
        'display_page_views'    => '0',
            'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'mode',               'type' => 'select', 'label' => 'Mode' ],
            [ 'key' => 'button_text',         'type' => 'text',   'label' => 'Button Text' ],
            [ 'key' => 'button_style',        'type' => 'select', 'label' => 'Button Style' ],
            [ 'key' => 'button_size',         'type' => 'select', 'label' => 'Button Size' ],
            [ 'key' => 'button_icon',         'type' => 'icon',   'label' => 'Button Icon' ],
            [ 'key' => 'button_fullwidth',    'type' => 'toggle', 'label' => 'Full Width' ],
            [ 'key' => 'modal_size',          'type' => 'select', 'label' => 'Modal Size' ],
            [ 'key' => 'modal_shadow',         'type' => 'select', 'label' => 'Modal Shadow' ],
            [ 'key' => 'modal_overlay',        'type' => 'range',  'label' => 'Overlay Opacity' ],
            [ 'key' => 'modal_close_button',  'type' => 'toggle', 'label' => 'Close Button' ],
            [ 'key' => 'modal_title',         'type' => 'text',   'label' => 'Modal Title' ],
            [ 'key' => 'content',             'type' => 'editor', 'label' => 'Content' ],
            [ 'key' => 'image',               'type' => 'image',  'label' => 'Image' ],
            [ 'key' => 'image_position',      'type' => 'select', 'label' => 'Image Position' ],
            [ 'key' => 'template_id',         'type' => 'select', 'label' => 'Template' ],
            [ 'key' => 'popup_trigger',        'type' => 'select', 'label' => 'Trigger' ],
            [ 'key' => 'scroll_percent',       'type' => 'range',  'label' => 'Scroll %' ],
            [ 'key' => 'timer_delay',          'type' => 'range',  'label' => 'Timer Delay' ],
            [ 'key' => 'inactivity_delay',     'type' => 'range',  'label' => 'Inactivity Delay' ],
            [ 'key' => 'show_max_times',       'type' => 'range',  'label' => 'Max Times' ],
            [ 'key' => 'show_once_per_session','type' => 'toggle', 'label' => 'Once Per Session' ],
        ];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );

        // ── Display Rules (server-side gating) ──
        // Logged in/out check
        $display_logged = $s['display_logged'] ?? '';
        if ( $display_logged === 'logged_in' && ! is_user_logged_in() ) return '';
        if ( $display_logged === 'logged_out' && is_user_logged_in() ) return '';

        // Date range check
        $date_from = $s['display_date_from'] ?? '';
        $date_to   = $s['display_date_to'] ?? '';
        $now       = current_time( 'Y-m-d' );
        if ( $date_from && $now < $date_from ) return '';
        if ( $date_to && $now > $date_to ) return '';

        // WooCommerce cart check
        $woo_cart = $s['display_woo_cart'] ?? '';
        if ( $woo_cart && class_exists( 'WooCommerce' ) && WC()->cart ) {
            $cart_count = WC()->cart->get_cart_contents_count();
            if ( $woo_cart === 'has_items' && $cart_count < 1 ) return '';
            if ( $woo_cart === 'empty' && $cart_count > 0 ) return '';
        }

        $uid = 'mpopup-' . wp_rand( 10000, 99999 );

        // Build data attributes for JS display rules
        $display_attrs = '';
        $display_device = sanitize_text_field( $s['display_device'] ?? '' );
        if ( $display_device ) {
            $display_attrs .= ' data-olo-popup-device="' . esc_attr( $display_device ) . '"';
        }
        $display_referrer = sanitize_text_field( $s['display_referrer'] ?? '' );
        if ( $display_referrer ) {
            $display_attrs .= ' data-olo-popup-referrer="' . esc_attr( $display_referrer ) . '"';
        }
        $display_pvs = intval( $s['display_page_views'] ?? 0 );
        if ( $display_pvs > 0 ) {
            $display_attrs .= ' data-olo-popup-pageviews="' . $display_pvs . '"';
        }

        // Advanced trigger settings
        $trigger         = $s['popup_trigger'] ?: 'click';
        $is_auto_trigger = ( $trigger !== 'click' );

        // New advanced fields
        $popup_delay          = max( 0, intval( $s['popup_delay'] ?? 5 ) );
        $popup_scroll_percent = max( 10, min( 100, intval( $s['popup_scroll_percent'] ?? 50 ) ) );
        $popup_frequency      = sanitize_text_field( $s['popup_frequency'] ?? 'always' );
        $popup_animation      = sanitize_html_class( $s['popup_animation'] ?? 'fade' );
        $popup_close_overlay  = ! empty( $s['popup_close_overlay'] );
        $popup_overlay_blur   = max( 0, min( 20, intval( $s['popup_overlay_blur'] ?? 0 ) ) );

        // Button classes
        $btn_classes = [ 'uk-button' ];
        $btn_classes[] = 'uk-button-' . sanitize_html_class( $s['button_style'] ?: 'default' );
        if ( ! empty( $s['button_size'] ) ) {
            $btn_classes[] = 'uk-button-' . sanitize_html_class( $s['button_size'] );
        }
        if ( ! empty( $s['button_fullwidth'] ) ) {
            $btn_classes[] = 'uk-width-1-1';
        }
        $btn_class = implode( ' ', $btn_classes );

        // Modal dialog classes
        $dialog_classes = [ 'uk-modal-dialog' ];
        $is_container = $s['modal_size'] === 'container'
                     || ( $s['mode'] === 'template' && $s['modal_size'] === '' );
        if ( $is_container ) {
            $dialog_classes[] = 'uk-modal-dialog-large';
        }
        $dialog_class = implode( ' ', $dialog_classes );

        // Modal attribute — bg-close depends on popup_close_overlay
        $modal_opts = [];
        if ( $is_container ) {
            $modal_opts[] = 'container: true';
        }
        if ( ! $popup_close_overlay ) {
            $modal_opts[] = 'bg-close: false';
        }
        $modal_attr = 'uk-modal' . ( ! empty( $modal_opts ) ? '=' . implode( '; ', $modal_opts ) : '' );

        // Button icon
        $icon_html = '';
        if ( ! empty( $s['button_icon'] ) && preg_match( '/^[a-z][a-z0-9-]*$/', $s['button_icon'] ) ) {
            $icon_html = '<span uk-icon="icon: ' . esc_attr( $s['button_icon'] ) . '"></span> ';
        }

        // Button text
        $btn_text = esc_html( $s['button_text'] ?: 'Apri' );

        // Shadow
        $shadow = Olobuild_Tile_Utils::shadow( $s['modal_shadow'] ?? 'lg' );

        // Overlay opacity (0-100 -> 0.0-1.0)
        $overlay_pct = max( 0, min( 100, intval( $s['modal_overlay'] ?? 60 ) ) );
        $overlay_alpha = round( $overlay_pct / 100, 2 );

        // Border radius
        $radius = Olobuild_Tile_Utils::border_radius( $s['modal_radius'] ?? 12 );
        $radius_hover_css = Olobuild_Tile_Utils::radius_force_css( $s['modal_radius_hover'] ?? null );

        // Border
        $border_w = max( 0, intval( $s['modal_border_width'] ?? 0 ) );
        $border_c = $this->safe_color_css( $s['modal_border_color'] ?? '' ) ?: 'var(--olo-color-border, #E5E7EB)';
        $border_style_allowed = [ 'solid', 'dashed', 'dotted', 'double' ];
        $border_style = in_array( $s['modal_border_style'] ?? 'solid', $border_style_allowed, true ) ? $s['modal_border_style'] : 'solid';

        // Effetti avanzati modale (v1.0.60+)
        $backdrop_blur     = max( 0, min( 40, intval( $s['modal_backdrop_blur'] ?? 0 ) ) );
        $backdrop_saturate = max( 100, min( 200, intval( $s['modal_backdrop_saturate'] ?? 100 ) ) );
        // Valori legacy ('monospace'/'serif'/'sans') → stack storici della tile;
        // valori nuovi (type 'font-family') → CSS pronto via resolver condiviso.
        $font_family_map   = [
            'monospace' => 'ui-monospace, SFMono-Regular, Menlo, monospace',
            'serif'     => 'Georgia, "Times New Roman", serif',
            'sans'      => '"Helvetica Neue", Helvetica, Arial, sans-serif',
        ];
        $font_family_key   = $s['modal_font_family'] ?? 'inherit';
        $font_family_css   = $this->resolve_font_family( $font_family_key, $font_family_map ) ?: 'inherit';
        $modal_rotation    = max( -10, min( 10, floatval( $s['modal_rotation'] ?? 0 ) ) );
        $modal_perspective = max( 0, min( 2000, intval( $s['modal_perspective'] ?? 0 ) ) );
        $modal_tilt_x      = max( -10, min( 10, floatval( $s['modal_tilt_x'] ?? 0 ) ) );
        $glow_pulse        = ! empty( $s['modal_glow_pulse'] );
        $title_glow        = ! empty( $s['modal_title_glow'] );
        $scanlines         = ! empty( $s['modal_scanlines'] );
        $terminal_prompt   = ! empty( $s['modal_terminal_prompt'] );

        // Preset (V3.26.1)
        $preset_id = $s['preset'] ?? 'modal-classic';

        // Modal/title color tweaks
        $modal_bg     = $this->safe_color_css( $s['modal_bg'] ?? '#ffffff' );
        $modal_text   = $this->safe_color_css( $s['modal_text_color'] ?? '#1e293b' );
        $modal_t_clr  = $this->safe_color_css( $s['modal_title_color'] ?? '#0f172a' );
        $modal_t_size = max( 10, intval( $s['modal_title_size'] ?? 24 ) );
        $modal_t_w    = preg_match( '/^[1-9]00$/', (string) ($s['modal_title_weight'] ?? '700') ) ? $s['modal_title_weight'] : '700';
        $modal_t_up   = ! empty( $s['modal_title_uppercase'] );
        $modal_t_ls   = floatval( $s['modal_title_letter_spacing'] ?? 0 );

        // Button typography
        // Dual-format: numero legacy O oggetto {tl,tr,br,bl}; vuoto/0 = storico 0px.
        $btn_radius_css = $this->build_border_radius_css( $s['button_radius'] ?? 6 );
        if ( $btn_radius_css === '' ) {
            $btn_radius_css = '0px';
        }
        $btn_upper   = ! empty( $s['button_uppercase'] );
        $btn_ls      = floatval( $s['button_letter_spacing'] ?? 0.02 );
        $btn_weight  = preg_match( '/^[1-9]00$/', (string) ($s['button_weight'] ?? '600') ) ? $s['button_weight'] : '600';

        // Data attributes for JS popup trigger handling
        $data_attrs  = ' data-olo-popup-trigger="' . esc_attr( $trigger ) . '"';
        $data_attrs .= ' data-olo-popup-delay="' . esc_attr( $popup_delay ) . '"';
        $data_attrs .= ' data-olo-popup-scroll="' . esc_attr( $popup_scroll_percent ) . '"';
        $data_attrs .= ' data-olo-popup-freq="' . esc_attr( $popup_frequency ) . '"';
        $data_attrs .= ' data-olo-popup-animation="' . esc_attr( $popup_animation ) . '"';
        $data_attrs .= $display_attrs;

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above (safe_color_css/intval/floatval/whitelists/Olobuild_Tile_Utils helpers).
        ?>
        <style>
            /* Overlay darkness + blur */
            #<?php echo esc_attr( $uid ); ?> {
                background: rgba(0, 0, 0, <?php echo (float) $overlay_alpha; ?>) !important;
                <?php if ( $popup_overlay_blur > 0 ) : ?>backdrop-filter: blur(<?php echo (int) $popup_overlay_blur; ?>px); -webkit-backdrop-filter: blur(<?php echo (int) $popup_overlay_blur; ?>px);<?php endif; ?>
            }
            /* a11y: anello di focus visibile da tastiera su trigger + chiudi */
            .olo-popup-<?php echo esc_attr( $uid ); ?> > button:focus-visible,
            #<?php echo esc_attr( $uid ); ?> [uk-close]:focus-visible,
            #<?php echo esc_attr( $uid ); ?> .uk-close:focus-visible {
                outline: none;
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
            }
            <?php if ( empty( $s['button_fullwidth'] ) ) : ?>
            .olo-frontend-tile:has(> .olo-popup-<?php echo esc_attr( $uid ); ?>),
            .olo-frontend-tile:has(> div > .olo-popup-<?php echo esc_attr( $uid ); ?>) {
                display: inline-block;
            }
            .olo-popup-<?php echo esc_attr( $uid ); ?> { display: inline-block; }
            <?php endif; ?>
            /* Shadow + border-radius + border on modal dialog */
            #<?php echo esc_attr( $uid ); ?> .uk-modal-dialog {
                <?php if ( $radius && $radius !== '0px' ) : ?>border-radius: <?php echo $radius; ?>; overflow: hidden;<?php endif; ?>
                <?php if ( $shadow !== 'none' ) : ?>box-shadow: <?php echo $shadow; ?>;<?php endif; ?>
                <?php if ( $border_w > 0 ) : ?>border: <?php echo (int) $border_w; ?>px <?php echo $border_style; ?> <?php echo $border_c; ?>;<?php endif; ?>
            }
            <?php if ( $radius_hover_css !== '' ) : ?>#<?php echo esc_attr( $uid ); ?> .uk-modal-dialog{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}#<?php echo esc_attr( $uid ); ?> .uk-modal-dialog:hover{border-radius:<?php echo $radius_hover_css; ?> !important}<?php endif; ?>
            /* Animation keyframes */
            <?php if ( $popup_animation === 'slide-up' ) : ?>
            #<?php echo esc_attr( $uid ); ?> .uk-modal-dialog { animation: oloPopSlideUp 0.3s ease-out; }
            @keyframes oloPopSlideUp { from { opacity:0; transform: translateY(40px); } to { opacity:1; transform: translateY(0); } }
            <?php elseif ( $popup_animation === 'slide-down' ) : ?>
            #<?php echo esc_attr( $uid ); ?> .uk-modal-dialog { animation: oloPopSlideDown 0.3s ease-out; }
            @keyframes oloPopSlideDown { from { opacity:0; transform: translateY(-40px); } to { opacity:1; transform: translateY(0); } }
            <?php elseif ( $popup_animation === 'zoom' ) : ?>
            #<?php echo esc_attr( $uid ); ?> .uk-modal-dialog { animation: oloPopZoom 0.3s ease-out; }
            @keyframes oloPopZoom { from { opacity:0; transform: scale(0.7); } to { opacity:1; transform: scale(1); } }
            <?php elseif ( $popup_animation === 'flip' ) : ?>
            #<?php echo esc_attr( $uid ); ?> .uk-modal-dialog { animation: oloPopFlip 0.4s ease-out; perspective: 600px; }
            @keyframes oloPopFlip { from { opacity:0; transform: perspective(600px) rotateX(-60deg); } to { opacity:1; transform: perspective(600px) rotateX(0); } }
            <?php else : ?>
            #<?php echo esc_attr( $uid ); ?> .uk-modal-dialog { animation: oloPopFade 0.25s ease-out; }
            @keyframes oloPopFade { from { opacity:0; } to { opacity:1; } }
            <?php endif; ?>
            /* Full-screen: 20px margin from screen edges, template fills all space */
            <?php if ( $s['modal_size'] === 'full' ) : ?>
            #<?php echo esc_attr( $uid ); ?>.uk-modal-full { padding: 20px; box-sizing: border-box; }
            #<?php echo esc_attr( $uid ); ?>.uk-modal-full > .uk-modal-dialog {
                <?php if ( $radius && $radius !== '0px' ) : ?>border-radius: <?php echo $radius; ?>;<?php endif; ?>
                overflow: hidden;
                width: 100%;
                height: calc(100vh - 40px);
                max-height: calc(100vh - 40px);
                display: flex;
                flex-direction: column;
                <?php if ( $shadow !== 'none' ) : ?>box-shadow: <?php echo $shadow; ?>;<?php endif; ?>
                <?php if ( $border_w > 0 ) : ?>border: <?php echo (int) $border_w; ?>px <?php echo $border_style; ?> <?php echo $border_c; ?>;<?php endif; ?>
            }
            #<?php echo esc_attr( $uid ); ?> .olo-popup-fullbody {
                flex: 1;
                overflow-y: auto;
                overflow-x: hidden;
                padding: 0;
            }
            <?php endif; ?>
            #<?php echo esc_attr( $uid ); ?> .uk-modal-body { overflow-x: hidden; }
            /* Il wrapper .olo-template porta il centering full-bleed da viewport
               (left:50% + translateX(-50%) + container): dentro la modale
               sposterebbe il contenuto fuori campo — qui va neutralizzato. */
            #<?php echo esc_attr( $uid ); ?> .olo-template {
                transform: none; left: auto; margin-left: 0; width: 100%; container: none;
            }
            #<?php echo esc_attr( $uid ); ?> .olo-template,
            #<?php echo esc_attr( $uid ); ?> .olo-frontend-grid,
            #<?php echo esc_attr( $uid ); ?> .olo-section,
            #<?php echo esc_attr( $uid ); ?> .olo-row,
            #<?php echo esc_attr( $uid ); ?> .olo-col { max-width: 100%; box-sizing: border-box; overflow-x: hidden; }
            #<?php echo esc_attr( $uid ); ?> .olo-map-canvas,
            #<?php echo esc_attr( $uid ); ?> .olo-map iframe { width: 100% !important; }
            #<?php echo esc_attr( $uid ); ?> .olo-frontend-grid { --olo-container-max-width: none; }
            #<?php echo esc_attr( $uid ); ?> .wp-block-post-title,
            #<?php echo esc_attr( $uid ); ?> .entry-title { display: none; }

            /* V3.26.1 — Modal style tweaks (universal, all presets) */
            /* Header/footer UIkit hanno un background proprio che coprirebbe
               modal_bg: trasparenti, cosi' lo sfondo scelto governa tutto. */
            #<?php echo esc_attr( $uid ); ?> .uk-modal-header,
            #<?php echo esc_attr( $uid ); ?> .uk-modal-footer { background: transparent; }
            #<?php echo esc_attr( $uid ); ?> .uk-modal-dialog {
                background: <?php echo $modal_bg; ?>;
                color: <?php echo $modal_text; ?>;
                <?php if ( $font_family_css !== 'inherit' ) : ?>font-family: <?php echo $font_family_css; ?>;<?php endif; ?>
                <?php if ( $backdrop_blur > 0 || $backdrop_saturate > 100 ) : ?>backdrop-filter: blur(<?php echo (int) $backdrop_blur; ?>px) saturate(<?php echo (int) $backdrop_saturate; ?>%); -webkit-backdrop-filter: blur(<?php echo (int) $backdrop_blur; ?>px) saturate(<?php echo (int) $backdrop_saturate; ?>%);<?php endif; ?>
                <?php if ( abs( $modal_rotation ) > 0.01 || $modal_perspective > 0 || abs( $modal_tilt_x ) > 0.01 ) : ?>
                    transform:
                        <?php if ( $modal_perspective > 0 ) : ?>perspective(<?php echo (int) $modal_perspective; ?>px) <?php endif; ?>
                        <?php if ( abs( $modal_tilt_x ) > 0.01 ) : ?>rotateX(<?php echo (float) $modal_tilt_x; ?>deg) <?php endif; ?>
                        <?php if ( abs( $modal_rotation ) > 0.01 ) : ?>rotate(<?php echo (float) $modal_rotation; ?>deg)<?php endif; ?>
                    ;
                <?php endif; ?>
            }
            #<?php echo esc_attr( $uid ); ?> .uk-modal-title {
                color: <?php echo $modal_t_clr; ?>;
                font-size: <?php echo (int) $modal_t_size; ?>px;
                font-weight: <?php echo $modal_t_w; ?>;
                <?php if ( $modal_t_up ) : ?>text-transform: uppercase;<?php endif; ?>
                letter-spacing: <?php echo (float) $modal_t_ls; ?>em;
                <?php if ( $font_family_css !== 'inherit' ) : ?>font-family: inherit;<?php endif; ?>
                <?php if ( $title_glow ) : $glow_c = $this->safe_color_css( $s['effect_color'] ?? '' ) ?: $modal_t_clr; $glow_rgb = $this->color_to_rgb( $glow_c ); ?>
                text-shadow: 0 0 8px rgba(<?php echo $glow_rgb; ?>,0.6);
                <?php endif; ?>
            }
            .olo-popup-<?php echo esc_attr( $uid ); ?> > button {
                border-radius: <?php echo $btn_radius_css; ?>;
                font-weight: <?php echo $btn_weight; ?>;
                <?php if ( $btn_upper ) : ?>text-transform: uppercase;<?php endif; ?>
                letter-spacing: <?php echo (float) $btn_ls; ?>em;
                transition: all 0.25s ease;
            }

            <?php
            // Effetti avanzati che richiedono CSS dinamico (animation keyframes, ::before/::after,
            // scanlines via background-image): generati solo se il rispettivo toggle è attivo.
            $effect_speed = absint( $s['effect_speed'] ?? 0 );
            if ( $glow_pulse ) :
                $c = $this->safe_color_css( $s['effect_color'] ?? '' ) ?: '#ff6a2a';
                $rgb = $this->color_to_rgb( $c );
                $pulse_ms = $effect_speed > 0 ? $effect_speed : 2200;
            ?>
            @keyframes olo-pop-glow-<?php echo esc_attr( $uid ); ?> {
                0%, 100% { box-shadow: 0 0 12px rgba(<?php echo $rgb; ?>,0.5), inset 0 0 12px rgba(<?php echo $rgb; ?>,0.15); }
                50%      { box-shadow: 0 0 24px rgba(<?php echo $rgb; ?>,0.85), inset 0 0 24px rgba(<?php echo $rgb; ?>,0.30); }
            }
            #<?php echo esc_attr( $uid ); ?> .uk-modal-dialog {
                animation: olo-pop-glow-<?php echo esc_attr( $uid ); ?> <?php echo (int) $pulse_ms; ?>ms ease-in-out infinite;
            }
            <?php endif; ?>

            <?php if ( $scanlines ) :
                $sc_c = $this->safe_color_css( $s['effect_color'] ?? '' ) ?: '#00ff8c';
                $sc_rgb = $this->color_to_rgb( $sc_c );
            ?>
            #<?php echo esc_attr( $uid ); ?> .uk-modal-dialog {
                background-image: repeating-linear-gradient(0deg, transparent 0, transparent 2px, rgba(<?php echo $sc_rgb; ?>,0.06) 2px, rgba(<?php echo $sc_rgb; ?>,0.06) 3px);
            }
            <?php endif; ?>

            <?php if ( $terminal_prompt ) :
                $tp_c = $this->safe_color_css( $s['effect_color'] ?? '' ) ?: $modal_t_clr;
                $tp_rgb = $this->color_to_rgb( $tp_c );
                $blink_ms = $effect_speed > 0 ? $effect_speed : 1000;
            ?>
            @keyframes olo-pop-cursor-<?php echo esc_attr( $uid ); ?> {
                0%, 49% { opacity: 1; } 50%, 100% { opacity: 0; }
            }
            #<?php echo esc_attr( $uid ); ?> .uk-modal-title::before { content: '> '; opacity: 0.7; }
            #<?php echo esc_attr( $uid ); ?> .uk-modal-title::after  {
                content: ' \2588';
                margin-left: 2px;
                animation: olo-pop-cursor-<?php echo esc_attr( $uid ); ?> <?php echo (int) $blink_ms; ?>ms steps(1) infinite;
            }
            <?php endif; ?>
        </style>
        <?php
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped

        // Full-screen modal uses special structure
        if ( $s['modal_size'] === 'full' ) :
        ?>
        <div class="olo-popup olo-pop--preset-<?php echo esc_attr( $preset_id ); ?> olo-popup-<?php echo esc_attr( $uid ); ?>"<?php echo $data_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attribute string built above exclusively from esc_attr()/intval() values ?>>
            <?php if ( ! $is_auto_trigger ) : ?>
            <button class="<?php echo esc_attr( $btn_class ); ?>" type="button" uk-toggle="target: #<?php echo esc_attr( $uid ); ?>">
                <?php echo $icon_html; ?><?php echo $btn_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- icon built with esc_attr() and text escaped with esc_html() above ?>
            </button>
            <?php endif; ?>

            <div id="<?php echo esc_attr( $uid ); ?>" class="uk-modal-full olo-pop--preset-<?php echo esc_attr( $preset_id ); ?>" <?php echo $modal_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- uk-modal options composed of fixed literals only ?>>
                <div class="uk-modal-dialog">
                    <?php if ( ! empty( $s['modal_close_button'] ) ) : ?>
                        <button class="uk-modal-close-full uk-close-large" type="button" uk-close style="z-index:10;"></button>
                    <?php endif; ?>
                    <div class="olo-popup-fullbody">
                        <?php echo $this->render_modal_content( $s ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML generated by render_modal_content(); dynamic values escaped within (esc_html/esc_url) or rendered by the internal template renderer ?>
                    </div>
                </div>
            </div>
        </div>
        <?php else : ?>
        <div class="olo-popup olo-pop--preset-<?php echo esc_attr( $preset_id ); ?> olo-popup-<?php echo esc_attr( $uid ); ?>"<?php echo $data_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attribute string built above exclusively from esc_attr()/intval() values ?>>
            <?php if ( ! $is_auto_trigger ) : ?>
            <button class="<?php echo esc_attr( $btn_class ); ?>" type="button" uk-toggle="target: #<?php echo esc_attr( $uid ); ?>">
                <?php echo $icon_html; ?><?php echo $btn_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- icon built with esc_attr() and text escaped with esc_html() above ?>
            </button>
            <?php endif; ?>

            <div id="<?php echo esc_attr( $uid ); ?>" class="olo-pop--preset-<?php echo esc_attr( $preset_id ); ?>" <?php echo $modal_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- uk-modal options composed of fixed literals only ?>>
                <div class="<?php echo esc_attr( $dialog_class ); ?>">
                    <?php if ( ! empty( $s['modal_close_button'] ) ) : ?>
                        <button class="uk-modal-close-default" type="button" uk-close></button>
                    <?php endif; ?>

                    <?php if ( ! empty( $s['modal_title'] ) ) : ?>
                    <div class="uk-modal-header">
                        <h2 class="uk-modal-title"><?php echo esc_html( $s['modal_title'] ); ?></h2>
                    </div>
                    <?php endif; ?>

                    <div class="uk-modal-body" uk-overflow-auto>
                        <?php echo $this->render_modal_content( $s ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML generated by render_modal_content(); dynamic values escaped within (esc_html/esc_url) or rendered by the internal template renderer ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <script>
        (function(){
            var el = document.getElementById('<?php echo esc_js( $uid ); ?>');
            if (!el) return;
            el.addEventListener('shown', function() {
                var maps = el.querySelectorAll('.olo-map-canvas');
                maps.forEach(function(c) { if (c._oloMap) c._oloMap.invalidateSize(); });
                var iframes = el.querySelectorAll('.olo-map iframe');
                iframes.forEach(function(f) { f.src = f.src; });
            });
            <?php if ( $is_auto_trigger ) : ?>
            /* Advanced popup trigger: <?php echo esc_js( $trigger ); ?> */
            var oloTriggered = false;
            var popupKey = 'olo_popup_<?php echo esc_js( $uid ); ?>';
            var maxTimes = <?php echo intval( $s['show_max_times'] ); ?>;
            var onceSession = <?php echo ! empty( $s['show_once_per_session'] ) ? 'true' : 'false'; ?>;
            var popupFreq = '<?php echo esc_js( $popup_frequency ); ?>';

            function oloGetCookie(name) {
                var m = document.cookie.match('(^|; )' + name + '=([^;]*)');
                return m ? decodeURIComponent(m[2]) : null;
            }
            function oloSetCookie(name, val, days) {
                var d = new Date();
                d.setTime(d.getTime() + (days * 86400000));
                document.cookie = name + '=' + encodeURIComponent(val) + ';expires=' + d.toUTCString() + ';path=/;SameSite=Lax';
            }

            function oloCanShow() {
                /* Legacy once-per-session check */
                if (onceSession) {
                    try { if (sessionStorage.getItem(popupKey + '_shown')) return false; } catch(e){}
                }
                /* Legacy max times check */
                if (maxTimes > 0) {
                    try {
                        var c = parseInt(localStorage.getItem(popupKey + '_count')) || 0;
                        if (c >= maxTimes) return false;
                    } catch(e){}
                }
                /* Frequency check */
                if (popupFreq === 'once_session') {
                    try { if (sessionStorage.getItem(popupKey + '_freq')) return false; } catch(e){}
                }
                if (popupFreq === 'once_day') {
                    if (oloGetCookie(popupKey + '_fd')) return false;
                }
                if (popupFreq === 'once_week') {
                    if (oloGetCookie(popupKey + '_fw')) return false;
                }
                if (popupFreq === 'once_ever') {
                    try { if (localStorage.getItem(popupKey + '_ever')) return false; } catch(e){}
                }
                return true;
            }

            function oloMarkShown() {
                if (onceSession) {
                    try { sessionStorage.setItem(popupKey + '_shown', '1'); } catch(e){}
                }
                if (maxTimes > 0) {
                    try {
                        var c = parseInt(localStorage.getItem(popupKey + '_count')) || 0;
                        localStorage.setItem(popupKey + '_count', String(c + 1));
                    } catch(e){}
                }
                if (popupFreq === 'once_session') {
                    try { sessionStorage.setItem(popupKey + '_freq', '1'); } catch(e){}
                }
                if (popupFreq === 'once_day') {
                    oloSetCookie(popupKey + '_fd', '1', 1);
                }
                if (popupFreq === 'once_week') {
                    oloSetCookie(popupKey + '_fw', '1', 7);
                }
                if (popupFreq === 'once_ever') {
                    try { localStorage.setItem(popupKey + '_ever', '1'); } catch(e){}
                }
            }

            function oloOpenPopup() {
                if (oloTriggered) return;
                if (!oloCanShow()) return;
                oloTriggered = true;
                oloMarkShown();
                if (typeof UIkit !== 'undefined') {
                    UIkit.modal(el).show();
                }
            }

            <?php if ( $trigger === 'scroll' || $trigger === 'scroll_percent' ) : ?>
            /* Scroll trigger: open at <?php echo intval( $s['popup_scroll_percent'] ); ?>% */
            var scrollPct = <?php echo intval( $s['popup_scroll_percent'] ); ?>;
            function oloCheckScroll() {
                var docH = document.documentElement.scrollHeight - window.innerHeight;
                if (docH <= 0) return;
                var pct = (window.scrollY / docH) * 100;
                if (pct >= scrollPct) {
                    oloOpenPopup();
                    window.removeEventListener('scroll', oloCheckScroll);
                }
            }
            window.addEventListener('scroll', oloCheckScroll, {passive: true});
            <?php endif; ?>

            <?php if ( $trigger === 'exit_intent' ) : ?>
            /* Exit intent: open when mouse leaves top of viewport (desktop only) */
            function oloExitIntent(e) {
                if (e.clientY <= 0) {
                    oloOpenPopup();
                    document.documentElement.removeEventListener('mouseleave', oloExitIntent);
                }
            }
            if (window.matchMedia('(pointer: fine)').matches) {
                document.documentElement.addEventListener('mouseleave', oloExitIntent);
            }
            <?php endif; ?>

            <?php if ( $trigger === 'timer' || $trigger === 'time_delay' ) : ?>
            /* Timer trigger: open after <?php echo intval( $popup_delay ); ?> seconds */
            var timerDelay = <?php echo intval( $popup_delay ); ?>;
            if (oloCanShow()) {
                setTimeout(function(){ oloOpenPopup(); }, timerDelay * 1000);
            }
            <?php endif; ?>

            <?php if ( $trigger === 'page_load' ) : ?>
            /* Page load trigger: open after <?php echo intval( $popup_delay ); ?>s delay on page load */
            var loadDelay = <?php echo intval( $popup_delay ); ?>;
            if (oloCanShow()) {
                if (loadDelay > 0) {
                    setTimeout(function(){ oloOpenPopup(); }, loadDelay * 1000);
                } else {
                    /* Piccolo ritardo per assicurare che UIkit sia pronto */
                    setTimeout(function(){ oloOpenPopup(); }, 300);
                }
            }
            <?php endif; ?>

            <?php if ( $trigger === 'inactivity' ) : ?>
            /* Inactivity trigger: open after <?php echo intval( $s['inactivity_delay'] ); ?>s without activity */
            var inactDelay = <?php echo intval( $s['inactivity_delay'] ); ?>;
            var inactTimer = null;
            function oloResetInact() {
                if (oloTriggered) return;
                if (inactTimer) clearTimeout(inactTimer);
                inactTimer = setTimeout(function(){
                    oloOpenPopup();
                    oloCleanupInact();
                }, inactDelay * 1000);
            }
            function oloCleanupInact() {
                window.removeEventListener('mousemove', oloResetInact);
                window.removeEventListener('keydown', oloResetInact);
                window.removeEventListener('scroll', oloResetInact);
                window.removeEventListener('touchstart', oloResetInact);
            }
            if (oloCanShow()) {
                window.addEventListener('mousemove', oloResetInact, {passive: true});
                window.addEventListener('keydown', oloResetInact, {passive: true});
                window.addEventListener('scroll', oloResetInact, {passive: true});
                window.addEventListener('touchstart', oloResetInact, {passive: true});
                oloResetInact();
            }
            <?php endif; ?>
            <?php endif; ?>
        })();
        </script>
        <?php
        $tfx_css = $this->tfx_css( $s, '.olo-popup-' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Text_Effects::css() from fixed effect definitions
        $this->tfx_print_script();

                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS built by Olobuild_Tile_Base::build_border_css() from sanitized border settings
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS built by Olobuild_Tile_Base border helpers from sanitized border settings
        }
        return ob_get_clean();
    }

    private function color_to_rgba( $color, $alpha ) {
        $rgb = $this->color_to_rgb( $color );
        return "rgba({$rgb},{$alpha})";
    }

    /**
     * @deprecated v1.0.60 — Gli effetti dei preset audaci ora arrivano dai field
     * regolari ({@see modal_backdrop_blur, modal_border_style, modal_font_family,
     * modal_rotation, modal_perspective, modal_tilt_x, modal_glow_pulse,
     * modal_title_glow, modal_scanlines, modal_terminal_prompt}) e sono interamente
     * modificabili dall'inspector. Mantenuto come noop per back-compat se richiamato
     * da overrides esterni.
     */
    private function get_preset_extra_css( $preset_id, $modal_id, $btn_sel, $s = [] ) {
        return '';
    }

    /**
     * Render the modal inner content based on mode (simple or template).
     */
    private function render_modal_content( $s ) {
        if ( $s['mode'] === 'template' && ! empty( $s['template_id'] ) ) {
            return $this->render_template_content( (int) $s['template_id'] );
        }

        return $this->render_simple_content( $s );
    }

    /**
     * Render simple mode: image + rich text content.
     */
    private function render_simple_content( $s ) {
        $html      = '';
        $image     = trim( $s['image'] ?? '' );
        $content   = $s['content'] ?? '';
        $position  = $s['image_position'] ?? 'top';
        $has_image = ! empty( $image );

        // Image HTML
        $img_html = '';
        if ( $has_image ) {
            $img_html = '<div class="olo-popup-image"><img src="' . esc_url( $image ) . '" alt="' . esc_attr( wp_strip_all_tags( $s['title'] ?? '' ) ) . '" loading="lazy" style="width:100%;height:auto;" /></div>';
        }

        // Content HTML — il campo e' un editor rich text: si preserva l'HTML
        // lecito (p/ul/strong/em/a…) via wp_kses_post invece di appiattirlo.
        $content_html = '';
        if ( ! empty( $content ) ) {
            list( $pc_cls, $pc_data ) = $this->tfx_attrs( $s, 'content', wp_strip_all_tags( $content ) );
            $content_html = '<div class="olo-popup-content' . $pc_cls . '"' . $pc_data . '>' . wp_kses_post( $content ) . '</div>';
        }

        if ( ! $has_image ) {
            return $content_html;
        }

        // Layout based on image position
        if ( $position === 'left' || $position === 'right' ) {
            $left  = $position === 'left' ? $img_html : $content_html;
            $right = $position === 'left' ? $content_html : $img_html;
            $html  = '<div uk-grid class="uk-child-width-1-2@s uk-grid-small">';
            $html .= '<div>' . $left . '</div>';
            $html .= '<div>' . $right . '</div>';
            $html .= '</div>';
        } elseif ( $position === 'bottom' ) {
            $html = $content_html . $img_html;
        } else {
            // top (default)
            $html = $img_html . $content_html;
        }

        return $html;
    }

    /**
     * Render template mode: embed a Olobuilder template inside the modal.
     */
    private function render_template_content( $template_id ) {
        if ( $template_id <= 0 ) {
            return '<!-- Olobuilder Popup: No template selected -->';
        }

        // Use the frontend renderer's shortcode method
        $renderer = new Olobuild_Frontend_Renderer();
        $output   = $renderer->render_shortcode( [ 'id' => $template_id ] );

        if ( empty( $output ) || str_starts_with( $output, '<!-- Olobuilder' ) ) {
            return '<p><em>Template non disponibile.</em></p>';
        }

        return $output;
    }
}
