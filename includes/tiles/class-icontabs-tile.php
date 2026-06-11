<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_IconTabs_Tile extends Olo_Tile_Base {

    protected $type     = 'icontabs';
    protected $name     = 'Tab a Icone';
    protected $icon     = 'dashicons-menu-alt';
    protected $category = 'interactive';
    protected $defaults = [
        'items' => [
            [ 'icon' => 'location', 'label' => 'Viaggi', 'heading' => 'HeyConad Viaggi', 'title' => 'Catalogo vacanze di primavera ed estate', 'content' => 'Destinazioni da sogno? Prenota subito la tua vacanza ideale.', 'link_text' => 'Scopri tutte le offerte', 'link_url' => '#' ],
            [ 'icon' => 'tablet',   'label' => 'Mobile', 'heading' => 'HeyConad Mobile',  'title' => 'Tariffe mobile esclusive',            'content' => 'Naviga e chiama senza pensieri a prezzi vantaggiosi.', 'link_text' => 'Scopri le tariffe',       'link_url' => '#' ],
            [ 'icon' => 'lock',     'label' => 'Tutela', 'heading' => 'HeyConad Tutela',  'title' => 'Assicurazioni pensate per te',        'content' => 'Protezione per la casa, la persona e i tuoi animali.', 'link_text' => 'Scopri i prodotti',       'link_url' => '#' ],
        ],
        'preset'         => 'pill-default',
        'pill_bg'        => '#F5F2EB',
        'active_bg'      => '',
        'active_color'   => '',
        'inactive_color' => '',
        'card_bg'        => '#F9D7D7',
        'card_radius'    => '16',
        'effect_color'   => '',
        'effect_intensity' => 'medium',
        'effect_speed'   => 0,
        'wow_disable'           => false,
        'wow_backdrop_blur'     => 0,
        'wow_backdrop_saturate' => 100,
        'wow_border_style'      => 'solid',
        'wow_font_family'       => 'inherit',
        'wow_rotation'          => 0,
        'wow_perspective'       => 0,
        'wow_tilt_x'            => 0,
        'wow_glow_pulse'        => false,
        'wow_title_glow'        => false,
        'wow_scanlines'         => false,

        'wow_terminal_prompt' => false,
        'card_border'              => [],
        'card_border_hover'        => [],
        'card_border_hover_duration' => 300,
        'heading_color'  => '',
        'title_color'    => '',
        'text_color'     => '',
        'link_color'     => '',
        'default_index'  => '0',
            'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    /**
     * V3.28.0 — Extra CSS for "audacious" presets, parametric on
     * effect_color / effect_intensity / effect_speed.
     */
    private function get_preset_extra_css( $preset_id, $uid, $s = [] ) {
        // @deprecated v1.0.73 — refactor profondo: i preset audaci ora settano direttamente
        // i field standard tramite TILE_PRESETS in BuilderInspector.vue, e i field wow_* via
        // build_wow_effects_css(). Nessun !important, ogni proprieta personalizzabile.
        return '';
    }

    public function get_controls() {
        return [
            [ 'key' => 'items',          'type' => 'custom', 'label' => 'Schede' ],
            [ 'key' => 'pill_bg',        'type' => 'color',  'label' => 'Sfondo pill' ],
            [ 'key' => 'active_bg',      'type' => 'color',  'label' => 'Sfondo attivo' ],
            [ 'key' => 'active_color',   'type' => 'color',  'label' => 'Colore icona attiva' ],
            [ 'key' => 'inactive_color', 'type' => 'color',  'label' => 'Colore icone inattive' ],
            [ 'key' => 'card_bg',        'type' => 'color',  'label' => 'Sfondo card' ],
            [ 'key' => 'card_radius',    'type' => 'range',  'label' => 'Raggio card' ],
            [ 'key' => 'heading_color',  'type' => 'color',  'label' => 'Colore occhiello' ],
            [ 'key' => 'title_color',    'type' => 'color',  'label' => 'Colore titolo' ],
            [ 'key' => 'text_color',     'type' => 'color',  'label' => 'Colore testo' ],
            [ 'key' => 'link_color',     'type' => 'color',  'label' => 'Colore link' ],
            [ 'key' => 'default_index',  'type' => 'number', 'label' => 'Scheda attiva iniziale (0-based)' ],
        ];
    }

    public function render( $settings ) {
        $s     = wp_parse_args( $settings, $this->defaults );
        $items = is_array( $s['items'] ) ? $s['items'] : [];
        if ( empty( $items ) ) return '';

        $uid           = 'oit-' . wp_rand( 10000, 99999 );
        $default       = max( 0, min( intval( $s['default_index'] ), count( $items ) - 1 ) );
        // TOKEN-FIRST: default '' ⇒ token brand/tema (era #e1474f / grigi nudi / #2563EB off-brand)
        $pill_bg       = $this->safe_color_css( $s['pill_bg'] );
        $active_bg     = $this->safe_color_css( $s['active_bg'] )    ?: 'var(--olo-color-primary, #e1474f)';
        $active_color  = $this->safe_color_css( $s['active_color'] ) ?: 'var(--olo-color-primary-contrast, #ffffff)';
        $inactive_col  = $this->safe_color_css( $s['inactive_color'] ) ?: 'var(--olo-color-text, #1f2937)';
        $card_bg       = $this->safe_color_css( $s['card_bg'] );
        $radius        = max( 0, Olo_Tile_Utils::radius_int( $s['card_radius'] ) );
        $heading_color = $this->safe_color_css( $s['heading_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $title_color   = $this->safe_color_css( $s['title_color'] )  ?: 'var(--olo-color-text, #1f2937)';
        $text_color    = $this->safe_color_css( $s['text_color'] )   ?: 'var(--olo-color-text-soft, #6b7280)';
        $link_color    = $this->safe_color_css( $s['link_color'] )   ?: 'var(--olo-color-link, #2563eb)';

        $preset_id     = $s['preset'] ?? 'pill-default';

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: colors via the safe_color_css() whitelist (with var() token fallbacks), radius via Olo_Tile_Utils::radius_int() with max() clamp, extra CSS via the internal get_preset_extra_css()/build_wow_effects_css() helpers; $uid is internally generated. ?>
        <style>
            .<?php echo esc_attr( $uid ); ?> { display:flex; flex-direction:column; align-items:center; gap:20px; }
            .<?php echo esc_attr( $uid ); ?> .oit-pill { display:inline-flex; align-items:center; gap:6px; padding:6px; background: <?php echo $pill_bg; ?>; border-radius:999px; }
            .<?php echo esc_attr( $uid ); ?> .oit-tab { display:inline-flex; align-items:center; justify-content:center; gap:8px; min-width:56px; height:48px; padding:0 14px; border:none; background:transparent; color: <?php echo $inactive_col; ?>; border-radius:999px; cursor:pointer; font-size:14px; font-weight:600; transition:all .25s ease; }
            .<?php echo esc_attr( $uid ); ?> .oit-tab span.oit-label { display:none; }
            .<?php echo esc_attr( $uid ); ?> .oit-tab svg { display:block; width:22px; height:22px; }
            .<?php echo esc_attr( $uid ); ?> .oit-tab.is-active { background: <?php echo $active_bg; ?>; color: <?php echo $active_color; ?>; padding: 0 22px; }
            .<?php echo esc_attr( $uid ); ?> .oit-tab.is-active span.oit-label { display:inline; }
            .<?php echo esc_attr( $uid ); ?> .oit-tab:hover:not(.is-active) { background: rgba(0,0,0,.04); }
            .<?php echo esc_attr( $uid ); ?> .oit-tab:focus-visible { outline:none; box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent); }
            .<?php echo esc_attr( $uid ); ?> .oit-card { width:100%; background: <?php echo $card_bg; ?>; border-radius: <?php echo $radius; ?>px; padding:32px; }
            .<?php echo esc_attr( $uid ); ?> .oit-card .oit-heading { font-size:16px; font-weight:700; color: <?php echo $heading_color; ?>; margin:0 0 8px; letter-spacing:-.01em; }
            .<?php echo esc_attr( $uid ); ?> .oit-card .oit-title { font-size:28px; font-weight:700; color: <?php echo $title_color; ?>; margin:0 0 12px; letter-spacing:-.01em; line-height:1.2; }
            .<?php echo esc_attr( $uid ); ?> .oit-card .oit-content { font-size:15px; color: <?php echo $text_color; ?>; line-height:1.6; margin:0; }
            .<?php echo esc_attr( $uid ); ?> .oit-card .oit-link { color: <?php echo $link_color; ?>; text-decoration:underline; font-weight:600; }
            .<?php echo esc_attr( $uid ); ?> .oit-card .oit-link:focus-visible { outline:none; border-radius:3px; box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent); }
            .<?php echo esc_attr( $uid ); ?> .oit-panel { display:none; }
            .<?php echo esc_attr( $uid ); ?> .oit-panel.is-active { display:block; animation: <?php echo $uid; ?>-fade .25s ease; }
            @keyframes <?php echo $uid; ?>-fade { from { opacity:0; transform:translateY(4px); } to { opacity:1; transform:none; } }
            @media (max-width: 640px) {
                .<?php echo esc_attr( $uid ); ?> .oit-card { padding:22px; }
                .<?php echo esc_attr( $uid ); ?> .oit-card .oit-title { font-size:22px; }
            }

            <?php
            // V3.28.0 — preset extras (audacious presets)
            echo $this->get_preset_extra_css( $preset_id, esc_attr( $uid ), $s );
            echo $this->build_wow_effects_css( $s, '.' . esc_attr( $uid ), '.uk-tab > * > a' );
            ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <div class="olo-icontabs olo-it--preset-<?php echo esc_attr( $preset_id ); ?> <?php echo esc_attr( $uid ); ?>" data-olo-icontabs>
            <div class="oit-pill" role="tablist">
                <?php foreach ( $items as $i => $item ) :
                    $icon_raw = $item['icon'] ?? '';
                    $label    = $item['label'] ?? '';
                    $active   = ( $i === $default ) ? ' is-active' : '';
                ?>
                <button type="button" class="oit-tab<?php echo $active; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed ' is-active'/'' and 'true'/'false' literals from ternaries; index cast to int ?>" data-idx="<?php echo (int) $i; ?>" role="tab" aria-selected="<?php echo $i === $default ? 'true' : 'false'; ?>" title="<?php echo esc_attr( $label ); ?>">
                    <?php if ( $icon_raw ) : ?>
                        <?php if ( preg_match( '/^[a-z][a-z0-9-]*$/', $icon_raw ) ) : ?>
                            <span uk-icon="icon: <?php echo esc_attr( $icon_raw ); ?>; ratio: 1.2"></span>
                        <?php else : ?>
                            <?php echo wp_kses( $icon_raw, [ 'svg' => [ 'width' => [], 'height' => [], 'viewbox' => [], 'fill' => [], 'stroke' => [], 'xmlns' => [] ], 'path' => [ 'd' => [], 'fill' => [], 'stroke' => [], 'stroke-width' => [], 'stroke-linecap' => [], 'stroke-linejoin' => [] ], 'circle' => [ 'cx'=>[], 'cy'=>[], 'r'=>[], 'fill'=>[], 'stroke'=>[] ], 'rect' => [ 'x'=>[], 'y'=>[], 'width'=>[], 'height'=>[], 'rx'=>[], 'fill'=>[], 'stroke'=>[] ], 'line' => [ 'x1'=>[], 'y1'=>[], 'x2'=>[], 'y2'=>[], 'stroke'=>[] ], 'polyline' => [ 'points'=>[], 'stroke'=>[] ], 'polygon' => [ 'points'=>[], 'fill'=>[] ], 'g' => [] ] ); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    <span class="oit-label"><?php echo esc_html( $label ); ?></span>
                </button>
                <?php endforeach; ?>
            </div>

            <?php foreach ( $items as $i => $item ) :
                $active = ( $i === $default ) ? ' is-active' : '';
            ?>
            <div class="oit-panel<?php echo $active; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed ' is-active'/'' literal from the ternary above; index cast to int ?>" data-panel="<?php echo (int) $i; ?>" role="tabpanel">
                <div class="oit-card">
                    <?php $widget_html = $this->render_widget_template( $item['widget_template_id'] ?? 0 ); ?>
                    <?php if ( $widget_html ) : ?>
                        <div class="olo-item-widget"><?php echo $widget_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- widget HTML rendered by Olo_Tile_Base::render_widget_template() through the frontend renderer (each tile escapes its own output) ?></div>
                    <?php endif; ?>
                    <?php
                    list( $itl_cls, $itl_data ) = $this->tfx_attrs( $s, 'label', $item['label'] ?? '' );
                    list( $ith_cls, $ith_data ) = $this->tfx_attrs( $s, 'heading', $item['heading'] ?? '' );
                    list( $itt_cls, $itt_data ) = $this->tfx_attrs( $s, 'title', $item['title'] ?? '' );
                    list( $itc_cls, $itc_data ) = $this->tfx_attrs( $s, 'content', wp_strip_all_tags( $item['content'] ?? '' ) );
                    ?>
                    <?php if ( ! empty( $item['heading'] ) ) : ?>
                        <div class="oit-heading<?php echo $ith_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); heading escaped inline ?>"<?php echo $ith_data; ?>><?php echo esc_html( $item['heading'] ); ?></div>
                    <?php endif; ?>
                    <?php if ( ! empty( $item['title'] ) ) : ?>
                        <h3 class="oit-title<?php echo $itt_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); title escaped inline ?>"<?php echo $itt_data; ?>><?php echo esc_html( $item['title'] ); ?></h3>
                    <?php endif; ?>
                    <?php if ( ! empty( $item['content'] ) ) : ?>
                        <p class="oit-content<?php echo $itc_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr) ?>"<?php echo $itc_data; ?>>
                            <?php echo $this->safe_richtext_content( $item['content'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitized via wp_kses_post() inside safe_richtext_content() ?>
                            <?php if ( ! empty( $item['link_text'] ) ) : ?>
                                <a class="oit-link" href="<?php echo esc_url( $item['link_url'] ?? '#' ); ?>"><?php echo esc_html( $item['link_text'] ); ?></a>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <script>
        (function () {
            var root = document.currentScript.previousElementSibling;
            if (!root || root.getAttribute('data-olo-icontabs') === null) {
                root = document.querySelector('.<?php echo esc_attr( $uid ); ?>');
            }
            if (!root || root._oloInit) return;
            root._oloInit = 1;
            var tabs = root.querySelectorAll('.oit-tab');
            var panels = root.querySelectorAll('.oit-panel');
            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    var idx = tab.getAttribute('data-idx');
                    tabs.forEach(function (t) {
                        var isActive = t.getAttribute('data-idx') === idx;
                        t.classList.toggle('is-active', isActive);
                        t.setAttribute('aria-selected', isActive ? 'true' : 'false');
                    });
                    panels.forEach(function (p) {
                        p.classList.toggle('is-active', p.getAttribute('data-panel') === idx);
                    });
                });
            });
        })();
        </script>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Text_Effects::css() from whitelisted effects, sanitized colors and integer timings
        $this->tfx_print_script();
                // Border system — wrapper tile
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );

        // Border system — card interna (.oit-card)
        $card_border_sel        = ".{$uid} .oit-card";
        $card_border_css        = $this->build_border_css( $s['card_border'] ?? [] );
        $card_border_hover_css  = $this->build_border_hover_css( $card_border_sel, $s['card_border'] ?? [], $s['card_border_hover'] ?? [], intval( $s['card_border_hover_duration'] ?? 300 ) );

        if ( $border_css || $border_hover_css || $border_effect_css || $card_border_css || $card_border_hover_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_css() from sanitized border settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base border helpers from sanitized border settings
            if ( $card_border_css ) echo "{$card_border_sel}{{$card_border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_css() from sanitized card border settings; selector from internal uid
            echo $card_border_hover_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_hover_css() from sanitized card border settings
        }
        return ob_get_clean();
    }
}
