<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Accordion_Tile extends Olo_Tile_Base {

    protected $type     = 'accordion';
    protected $name     = 'Fisarmonica';
    protected $icon     = 'dashicons-list-view';
    protected $category = 'interactive';
    protected $defaults = [
        'panels'            => [
            [ 'title' => 'Prima voce', 'content' => 'Contenuto della prima voce dell\'accordion.', 'children' => [] ],
            [ 'title' => 'Seconda voce', 'content' => 'Contenuto della seconda voce dell\'accordion.', 'children' => [] ],
            [ 'title' => 'Terza voce', 'content' => 'Contenuto della terza voce dell\'accordion.', 'children' => [] ],
        ],
        'preset'            => 'card-soft',
        'toggle_mode'       => false,
        'default_open'      => 'first',
        'icon_position'     => 'right',
        'icon_style'        => 'chevron',
        'animate_icon'      => true,
        'animation_speed'   => '300',
        'content_transition'=> 'fade',
        'media_align'       => 'right',
        'media_width'       => '35',
        'media_radius'      => '8',
        'header_bg'         => '',
        'header_bg_active'  => '',
        'header_text_color' => '',
        'header_text_color_active' => '',
        'header_padding_y'  => '16',
        'header_padding_x'  => '20',
        'header_font_size'  => '15',
        'header_font_weight' => '600',
        'header_font_family' => 'sans',
        'content_bg'        => '',
        'content_padding_y' => '20',
        'content_padding_x' => '20',
        'content_font_size' => '14',
        'border_color'      => '',
        'border_width'      => '1',
        'text_color'        => '',
        'gap'               => '0',
        'border_radius'     => '8',
        'faq_schema'        => false,
        'separator_style'   => 'border',
        'backdrop_blur'     => '0',
        'backdrop_saturate' => '100',
        'icon_shape'        => 'none',
        'icon_shape_size'   => '32',
        'icon_shape_bg'     => '',
        'panel_hover_lift'  => false,
        'panel_hover_shadow' => 'none',
        'effect_color'      => '',
        'effect_intensity'  => 'medium',
        'effect_speed'      => 0,
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
            [ 'key' => 'panels',            'type' => 'custom',  'label' => 'Panels' ],
            [ 'key' => 'toggle_mode',       'type' => 'toggle',  'label' => 'Toggle Mode' ],
            [ 'key' => 'default_open',      'type' => 'select',  'label' => 'Default Open' ],
            [ 'key' => 'icon_position',     'type' => 'select',  'label' => 'Icon Position' ],
            [ 'key' => 'icon_style',        'type' => 'select',  'label' => 'Icon Style' ],
            [ 'key' => 'animate_icon',      'type' => 'toggle',  'label' => 'Animate Icon' ],
            [ 'key' => 'animation_speed',   'type' => 'range',   'label' => 'Animation Speed' ],
            [ 'key' => 'header_bg',         'type' => 'color',   'label' => 'Header Background' ],
            [ 'key' => 'header_bg_active',  'type' => 'color',   'label' => 'Header Active BG' ],
            [ 'key' => 'header_text_color', 'type' => 'color',   'label' => 'Header Text' ],
            [ 'key' => 'content_bg',        'type' => 'color',   'label' => 'Content Background' ],
            [ 'key' => 'border_color',      'type' => 'color',   'label' => 'Border Color' ],
            [ 'key' => 'text_color',        'type' => 'color',   'label' => 'Content Text' ],
            [ 'key' => 'gap',               'type' => 'range',   'label' => 'Gap' ],
            [ 'key' => 'border_radius',     'type' => 'range',   'label' => 'Border Radius' ],
            [ 'key' => 'faq_schema',        'type' => 'toggle',  'label' => 'FAQ Schema' ],
            [ 'key' => 'separator_style',   'type' => 'select',  'label' => 'Separator Style' ],
        ];
    }

    public function render( $settings ) {
        $s      = wp_parse_args( $settings, $this->defaults );
        $panels = $this->parse_panels( $s['panels'] );
        $uid    = 'macc-' . wp_rand( 10000, 99999 );
        $count  = count( $panels );

        if ( $count === 0 ) {
            return '';
        }

        $open_indices = [];
        if ( $s['default_open'] === 'first' ) {
            $open_indices = [ 0 ];
        } elseif ( $s['default_open'] === 'all' ) {
            $open_indices = range( 0, $count - 1 );
        }

        $toggle_mode = ! empty( $s['toggle_mode'] );
        $multiple    = $toggle_mode ? 'multiple: true' : '';
        $rad_raw     = $s['border_radius'];
        $is_4corners = is_array( $rad_raw );
        if ( $is_4corners ) {
            $r_tl = absint( $rad_raw['tl'] ?? 0 );
            $r_tr = absint( $rad_raw['tr'] ?? 0 );
            $r_br = absint( $rad_raw['br'] ?? 0 );
            $r_bl = absint( $rad_raw['bl'] ?? 0 );
            $radius_css = "{$r_tl}px {$r_tr}px {$r_br}px {$r_bl}px";
            $radius = max( $r_tl, $r_tr, $r_br, $r_bl );
        } else {
            $radius = intval( $rad_raw );
            $radius_css = $radius . 'px';
        }
        $gap         = intval( $s['gap'] );
        $speed       = intval( $s['animation_speed'] );

        // V3.23.1 — preset is applied JS-side at the moment the user picks it
        // (BuilderInspector.applyPreset). The PHP renderer just reads the
        // already-populated fields, so manual edits on top of a preset win.
        $preset_id = $s['preset'] ?? 'card-soft';

        $header_bg     = $this->safe_color_css( $s['header_bg'] );
        $header_active = $this->safe_color_css( $s['header_bg_active'] );
        $header_text   = $this->safe_color_css( $s['header_text_color'] );
        $content_bg    = $this->safe_color_css( $s['content_bg'] );
        $text_clr      = $this->safe_color_css( $s['text_color'] );
        // V3.21: separator border falls back to a light gray so old templates
        // without an explicit border_color still render a visible separator.
        $border_clr    = $this->safe_color_css( $s['border_color'] );
        if ( ! $border_clr && ( $s['separator_style'] ?? 'border' ) === 'border' ) {
            $border_clr = '#e5e7eb';
        }

        $icon_pos = $s['icon_position'];
        $icon_svg = $icon_pos !== 'none' ? $this->get_icon_svg( $s['icon_style'] ) : '';
        $animate  = ! empty( $s['animate_icon'] );
        $rotation = $s['icon_style'] === 'plus' ? '45' : '180';

        $content_transition = $s['content_transition'] ?? 'fade';
        $media_align  = ( $s['media_align'] ?? 'right' ) === 'left' ? 'left' : 'right';
        $media_margin = $media_align === 'right' ? '0 0 12px 16px' : '0 16px 12px 0';
        $media_width  = min( max( intval( $s['media_width'] ?? 35 ), 20 ), 50 );
        $media_radius = Olo_Tile_Utils::border_radius( $s['media_radius'] ?? 8 );
        $media_radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['media_radius_hover'] ?? null );

        // Brand accent for active state (Olobuild orange).
        $brand_accent = 'var(--olo-color-primary, #e8622a)';

        // V3.22: granular header/content controls.
        $header_pad_y = max( 0, intval( $s['header_padding_y'] ?? 16 ) );
        $header_pad_x = max( 0, intval( $s['header_padding_x'] ?? 20 ) );
        $header_fs    = max( 10, intval( $s['header_font_size'] ?? 15 ) );
        $header_fw    = preg_match( '/^[1-9]00$/', (string) ($s['header_font_weight'] ?? '600') ) ? $s['header_font_weight'] : '600';
        $header_ff    = $s['header_font_family'] ?? 'sans';
        $header_ff_css = $header_ff === 'mono' ? 'ui-monospace, SFMono-Regular, Menlo, monospace'
                       : ($header_ff === 'serif' ? "Georgia, 'Times New Roman', serif" : 'inherit');
        $content_pad_y = max( 0, intval( $s['content_padding_y'] ?? 20 ) );
        $content_pad_x = max( 0, intval( $s['content_padding_x'] ?? 20 ) );
        $content_fs    = max( 10, intval( $s['content_font_size'] ?? 14 ) );
        $bw            = max( 0, intval( $s['border_width'] ?? 1 ) );
        $header_text_active = $this->safe_color_css( $s['header_text_color_active'] ?? '' );
        $bp_blur       = max( 0, intval( $s['backdrop_blur'] ?? 0 ) );
        $bp_sat        = max( 100, intval( $s['backdrop_saturate'] ?? 100 ) );
        $icon_shape    = in_array( $s['icon_shape'] ?? 'none', [ 'none', 'pill', 'circle' ], true ) ? $s['icon_shape'] : 'none';
        $icon_sh_size  = max( 16, intval( $s['icon_shape_size'] ?? 32 ) );
        $icon_sh_bg    = $this->safe_color_css( $s['icon_shape_bg'] ?? '' );
        $panel_lift    = ! empty( $s['panel_hover_lift'] );
        $panel_h_shadow = $s['panel_hover_shadow'] ?? 'none';

        ob_start();
        ?>
        <style>
            .<?php echo esc_attr( $uid ); ?> .uk-accordion-title {
                <?php if ( $header_bg ) : ?>background: <?php echo $header_bg; ?>;<?php else : ?>background: transparent;<?php endif; ?>
                <?php if ( $header_text ) : ?>color: <?php echo $header_text; ?>;<?php endif; ?>
                padding: <?php echo $header_pad_y; ?>px <?php echo $header_pad_x; ?>px;
                font-weight: <?php echo $header_fw; ?>;
                font-size: <?php echo $header_fs; ?>px;
                font-family: <?php echo $header_ff_css; ?>;
                line-height: 1.4;
                display: flex;
                align-items: center;
                gap: 12px;
                cursor: pointer;
                transition: background <?php echo $speed; ?>ms ease, color <?php echo $speed; ?>ms ease, box-shadow <?php echo $speed; ?>ms ease;
                <?php if ( $bp_blur > 0 ) : ?>backdrop-filter: blur(<?php echo $bp_blur; ?>px) saturate(<?php echo $bp_sat; ?>%); -webkit-backdrop-filter: blur(<?php echo $bp_blur; ?>px) saturate(<?php echo $bp_sat; ?>%);<?php endif; ?>
                <?php if ( $gap > 0 ) : ?>
                <?php if ( $is_4corners ) : ?>
                border-radius: <?php echo $r_tl; ?>px <?php echo $r_tr; ?>px 0 0;
                <?php else : ?>
                border-radius: <?php echo $radius_css; ?>; border-bottom-left-radius: 0; border-bottom-right-radius: 0;
                <?php endif; ?>
                <?php endif; ?>
            }
            .<?php echo esc_attr( $uid ); ?> .uk-accordion-title::before {
                content: none !important;
            }
            .<?php echo esc_attr( $uid ); ?> .uk-accordion-title:hover {
                <?php if ( $header_bg ) : ?>filter: brightness(0.97);<?php else : ?>background: rgba(0,0,0,0.025);<?php endif; ?>
            }
            .<?php echo esc_attr( $uid ); ?> .macc-title-text {
                flex: 1;
                letter-spacing: -0.01em;
            }
            .<?php echo esc_attr( $uid ); ?> .macc-panel-icon {
                flex-shrink: 0;
                display: inline-flex;
                align-items: center;
                margin-right: 4px;
                color: <?php echo $brand_accent; ?>;
            }
            .<?php echo esc_attr( $uid ); ?> .uk-open .uk-accordion-title {
                <?php if ( $header_active ) : ?>background: <?php echo $header_active; ?>;<?php endif; ?>
                <?php if ( $header_text_active ) : ?>color: <?php echo $header_text_active; ?>;<?php endif; ?>
            }
            .<?php echo esc_attr( $uid ); ?> .uk-open .macc-icon {
                color: <?php echo $header_text_active ?: $brand_accent; ?>;
            }
            .<?php echo esc_attr( $uid ); ?> .uk-accordion-content {
                <?php if ( $content_bg ) : ?>background: <?php echo $content_bg; ?>;<?php endif; ?>
                <?php if ( $text_clr ) : ?>color: <?php echo $text_clr; ?>;<?php endif; ?>
                margin-top: 0;
                padding: 4px <?php echo $content_pad_x; ?>px <?php echo $content_pad_y; ?>px;
                font-size: <?php echo $content_fs; ?>px;
                line-height: 1.65;
                <?php if ( $bp_blur > 0 ) : ?>backdrop-filter: blur(<?php echo max( 0, $bp_blur - 4 ); ?>px) saturate(<?php echo max( 100, $bp_sat - 20 ); ?>%); -webkit-backdrop-filter: blur(<?php echo max( 0, $bp_blur - 4 ); ?>px) saturate(<?php echo max( 100, $bp_sat - 20 ); ?>%);<?php endif; ?>
            }
            .<?php echo esc_attr( $uid ); ?> .macc-content-text {
                color: inherit;
            }
            <?php if ( $content_transition === 'fade' ) : ?>
            .<?php echo esc_attr( $uid ); ?> .uk-accordion-content .macc-content-inner {
                animation: macc-fade-<?php echo $uid; ?> <?php echo $speed; ?>ms ease;
            }
            @keyframes macc-fade-<?php echo $uid; ?> {
                from { opacity: 0; transform: translateY(-4px); }
                to { opacity: 1; transform: translateY(0); }
            }
            <?php elseif ( $content_transition === 'slide' ) : ?>
            .<?php echo esc_attr( $uid ); ?> .uk-accordion-content .macc-content-inner {
                animation: macc-slide-<?php echo $uid; ?> <?php echo $speed; ?>ms ease;
            }
            @keyframes macc-slide-<?php echo $uid; ?> {
                from { opacity: 0; transform: translateY(12px); }
                to { opacity: 1; transform: translateY(0); }
            }
            <?php endif; ?>
            .<?php echo esc_attr( $uid ); ?> .macc-panel-media {
                float: <?php echo $media_align; ?>;
                margin: <?php echo $media_margin; ?>;
                max-width: <?php echo $media_width; ?>%;
                width: <?php echo $media_width; ?>%;
                border-radius: <?php echo $media_radius; ?>;
                overflow: hidden;
            }
            <?php if ( $media_radius_hover_css !== '' ) : ?>.<?php echo esc_attr( $uid ); ?> .macc-panel-media{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo esc_attr( $uid ); ?> .macc-panel-media:hover{border-radius:<?php echo $media_radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo esc_attr( $uid ); ?> .macc-panel-media img,
            .<?php echo esc_attr( $uid ); ?> .macc-panel-media video,
            .<?php echo esc_attr( $uid ); ?> .macc-panel-media iframe {
                display: block;
                width: 100%;
                height: auto;
                border-radius: <?php echo $media_radius; ?>;
            }
            .<?php echo esc_attr( $uid ); ?> .macc-panel-media iframe {
                aspect-ratio: 16/9;
            }
            <?php if ( $icon_pos !== 'none' ) : ?>
            .<?php echo esc_attr( $uid ); ?> .macc-icon {
                flex-shrink: 0;
                display: flex;
                align-items: center;
                <?php if ( $animate ) : ?>
                transition: transform <?php echo $speed; ?>ms ease;
                <?php endif; ?>
                <?php if ( $icon_pos === 'left' ) : ?>order: -1;<?php endif; ?>
            }
            <?php if ( $animate ) : ?>
            .<?php echo esc_attr( $uid ); ?> .uk-open .macc-icon {
                transform: rotate(<?php echo $rotation; ?>deg);
            }
            <?php endif; ?>
            <?php endif; ?>
            .<?php echo esc_attr( $uid ); ?> > li {
                <?php if ( $s['separator_style'] === 'border' && $border_clr && $bw > 0 ) : ?>
                border: <?php echo $bw; ?>px solid <?php echo $border_clr; ?>;
                <?php elseif ( $s['separator_style'] === 'shadow' ) : ?>
                box-shadow: 0 1px 2px rgba(16,24,40,0.05), 0 1px 3px rgba(16,24,40,0.06);
                <?php endif; ?>
                <?php if ( $gap > 0 ) : ?>
                border-radius: <?php echo $radius_css; ?>;
                <?php endif; ?>
                transition: border-color <?php echo $speed; ?>ms ease, box-shadow <?php echo $speed; ?>ms ease, transform <?php echo $speed; ?>ms ease;
                overflow: hidden;
            }
            <?php
            // Panel hover lift + shadow
            $_h_shadow_map = [
                'sm' => '0 1px 2px rgba(16,24,40,0.06), 0 1px 3px rgba(16,24,40,0.08)',
                'md' => '0 4px 6px rgba(16,24,40,0.08), 0 2px 4px rgba(16,24,40,0.06)',
                'lg' => '0 12px 24px rgba(16,24,40,0.10), 0 4px 8px rgba(16,24,40,0.08)',
            ];
            if ( $panel_lift || ( isset( $_h_shadow_map[ $panel_h_shadow ] ) ) ) :
            ?>
            .<?php echo esc_attr( $uid ); ?> > li:hover {
                <?php if ( $panel_lift ) : ?>transform: translateY(-1px);<?php endif; ?>
                <?php if ( isset( $_h_shadow_map[ $panel_h_shadow ] ) ) : ?>box-shadow: <?php echo $_h_shadow_map[ $panel_h_shadow ]; ?>;<?php endif; ?>
            }
            <?php endif; ?>
            <?php if ( $gap > 0 && $s['separator_style'] === 'border' && $border_clr && $bw > 0 ) : ?>
            .<?php echo esc_attr( $uid ); ?> > li.uk-open {
                border-color: <?php echo $brand_accent; ?>;
                box-shadow: 0 1px 2px rgba(232,98,42,0.05), 0 4px 12px rgba(232,98,42,0.08);
            }
            <?php endif; ?>

            <?php if ( $icon_shape !== 'none' ) :
                $_shape_radius = $icon_shape === 'circle' ? '50%' : '4px';
            ?>
            .<?php echo esc_attr( $uid ); ?> .macc-icon {
                width: <?php echo $icon_sh_size; ?>px;
                height: <?php echo $icon_sh_size; ?>px;
                border-radius: <?php echo $_shape_radius; ?>;
                <?php if ( $icon_sh_bg ) : ?>background: <?php echo $icon_sh_bg; ?>;<?php endif; ?>
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
            <?php endif; ?>
            .<?php echo esc_attr( $uid ); ?> > :nth-child(n+2) {
                margin-top: <?php echo $gap; ?>px;
            }
            .<?php echo esc_attr( $uid ); ?> {
                margin: 0;
            }
            <?php if ( $gap === 0 ) : ?>
            .<?php echo esc_attr( $uid ); ?> {
                border-radius: <?php echo $radius_css; ?>;
                overflow: hidden;
            }
            .<?php echo esc_attr( $uid ); ?> > li + li {
                border-top: none;
            }
            <?php endif; ?>

            <?php
            // v1.0.73 — refactor profondo: get_preset_extra_css è ora noop. Tutti i
            // valori dei preset audaci (colori, bordi, ombra, tipografia) sono nei
            // field standard tramite TILE_PRESETS.accordion in BuilderInspector.vue.
            // Gli effetti speciali (backdrop blur, font monospace, rotation, glow
            // pulse, title glow, scanlines, terminal prompt) sono nei field wow_*
            // dell'helper condiviso wowEffectsFields(). Nessun !important: ogni
            // singola proprietà è personalizzabile dall'inspector.
            echo $this->build_wow_effects_css( $s, '.' . esc_attr( $uid ) . ' > li', '.uk-accordion-title' );
            ?>
        </style>

        <ul class="olo-accordion uk-accordion <?php echo esc_attr( $uid ); ?>" uk-accordion="<?php echo esc_attr( $multiple ); ?>; animation: <?php echo $speed; ?>">
            <?php foreach ( $panels as $i => $panel ) :
                $is_open    = in_array( $i, $open_indices, true );
                $panel_img  = $panel['image'] ?? '';
                $panel_vid  = $panel['video'] ?? '';
                $panel_icon = $panel['icon'] ?? '';
                $has_media  = ! empty( $panel_vid ) || ! empty( $panel_img );
            ?>
            <li <?php echo $is_open ? 'class="uk-open"' : ''; ?>>
                <a class="uk-accordion-title" href="#" role="button" aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $uid . '-panel-' . $i ); ?>">
                    <?php if ( ! empty( $panel_icon ) ) : ?>
                        <span class="macc-panel-icon">
                            <?php if ( preg_match( '/^[a-z][a-z0-9-]*$/', $panel_icon ) ) : ?>
                                <span uk-icon="icon: <?php echo esc_attr( $panel_icon ); ?>"></span>
                            <?php else : ?>
                                <?php echo esc_html( $panel_icon ); ?>
                            <?php endif; ?>
                        </span>
                    <?php endif; ?>
                    <?php list( $at_tfx_cls, $at_tfx_data ) = $this->tfx_attrs( $s, 'title', wp_strip_all_tags( $panel['title'] ?? '' ) ); ?>
                    <span class="macc-title-text<?php echo $at_tfx_cls; ?>"<?php echo $at_tfx_data; ?>><?php echo wp_kses_post( $panel['title'] ); ?></span>
                    <?php if ( $icon_pos !== 'none' ) : ?>
                        <span class="macc-icon"><?php echo $icon_svg; ?></span>
                    <?php endif; ?>
                </a>
                <div class="uk-accordion-content" id="<?php echo esc_attr( $uid . '-panel-' . $i ); ?>" role="region">
                    <div class="macc-content-inner">
                        <?php $widget_html = $this->render_widget_template( $panel['widget_template_id'] ?? 0 ); ?>
                        <?php if ( $widget_html ) : ?>
                            <div class="olo-item-widget"><?php echo $widget_html; ?></div>
                        <?php endif; ?>
                        <?php if ( $has_media ) : ?>
                            <div class="macc-panel-media">
                                <?php if ( ! empty( $panel_vid ) ) :
                                    $embed = $this->get_video_embed( $panel_vid );
                                    echo $embed;
                                endif; ?>
                                <?php if ( ! empty( $panel_img ) ) : ?>
                                    <?php
                                    $acc_img = '<img src="' . esc_url( $panel_img ) . '" alt="' . esc_attr( wp_strip_all_tags( $panel['title'] ?? '' ) ) . '" loading="lazy" />';
                                    echo $this->render_hover_wrap( $acc_img, $panel['hover_image'] ?? '', '' );
                                    ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php
                        $acc_content_plain = wp_strip_all_tags( $panel['content'] );
                        list( $ac_tfx_cls, $ac_tfx_data ) = $this->tfx_attrs( $s, 'content', $acc_content_plain );
                        ?>
                        <div class="macc-content-text<?php echo $ac_tfx_cls; ?>"<?php echo $ac_tfx_data; ?>><?php echo nl2br( esc_html( $acc_content_plain ) ); ?></div>
                        <?php if ( $has_media ) : ?>
                            <div style="clear:both"></div>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>

        <?php if ( ! empty( $s['faq_schema'] ) && $count > 0 ) : ?>
        <script type="application/ld+json">
        <?php
            $faq_entities = [];
            foreach ( $panels as $panel ) {
                $faq_entities[] = [
                    '@type'          => 'Question',
                    'name'           => wp_strip_all_tags( $panel['title'] ),
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => wp_strip_all_tags( $panel['content'] ),
                    ],
                ];
            }
            echo wp_json_encode( [
                '@context'   => 'https://schema.org',
                '@type'      => 'FAQPage',
                'mainEntity' => $faq_entities,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
        ?>
        </script>
        <?php endif; ?>

        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}";
            echo $border_hover_css . $border_effect_css . '</style>';
        }
        return ob_get_clean();
    }

    /**
     * Parse panels from JSON array or legacy string format.
     */
    private function parse_panels( $raw ) {
        // New format: already an array of objects
        if ( is_array( $raw ) ) {
            $panels = [];
            foreach ( $raw as $item ) {
                if ( is_array( $item ) && ! empty( $item['title'] ) ) {
                    $panels[] = [
                        'title'              => $item['title'],
                        'content'            => $item['content'] ?? '',
                        'image'              => $item['image'] ?? '',
                        'hover_image'        => $item['hover_image'] ?? '',
                        'video'              => $item['video'] ?? '',
                        'icon'               => $item['icon'] ?? '',
                        'widget_template_id' => absint( $item['widget_template_id'] ?? 0 ),
                    ];
                }
            }
            return $panels;
        }

        // Legacy format: string with --- delimiter
        if ( is_string( $raw ) && ! empty( $raw ) ) {
            $panels = [];
            $blocks = preg_split( '/^---$/m', $raw );
            foreach ( $blocks as $block ) {
                $lines = array_filter( array_map( 'trim', explode( "\n", trim( $block ) ) ) );
                $lines = array_values( $lines );
                if ( count( $lines ) >= 2 ) {
                    $title  = array_shift( $lines );
                    $panels[] = [ 'title' => $title, 'content' => implode( "\n", $lines ) ];
                } elseif ( count( $lines ) === 1 ) {
                    $panels[] = [ 'title' => $lines[0], 'content' => '' ];
                }
            }
            return $panels;
        }

        return [];
    }

    /**
     * Convert a video URL to embeddable HTML (YouTube, Vimeo, or direct file).
     */
    private function get_video_embed( $url ) {
        $url = trim( $url );

        // YouTube
        if ( preg_match( '/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m ) ) {
            return '<iframe src="https://www.youtube-nocookie.com/embed/' . esc_attr( $m[1] ) . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe>';
        }

        // Vimeo
        if ( preg_match( '/vimeo\.com\/(\d+)/', $url, $m ) ) {
            return '<iframe src="https://player.vimeo.com/video/' . esc_attr( $m[1] ) . '?dnt=1" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen loading="lazy"></iframe>';
        }

        // Direct video file
        $ext = strtolower( pathinfo( parse_url( $url, PHP_URL_PATH ) ?: '', PATHINFO_EXTENSION ) );
        $mime_map = [ 'mp4' => 'video/mp4', 'webm' => 'video/webm', 'ogg' => 'video/ogg' ];
        $mime = $mime_map[ $ext ] ?? 'video/mp4';
        return '<video controls preload="metadata"><source src="' . esc_url( $url ) . '" type="' . esc_attr( $mime ) . '"></video>';
    }

    /**
     * V3.22: Return per-preset style overrides. Each preset is a curated
     * combo of header/border/radius/shadow tuned for a specific aesthetic.
     */
    private function get_preset_styles( $preset_id ) {
        $presets = [
            'card-soft' => [
                'header_bg'         => '#ffffff',
                'header_bg_active'  => '#fdf2ec',
                'header_text_color' => '#1e293b',
                'header_text_color_active' => '',
                'header_padding_y'  => 16,
                'header_padding_x'  => 20,
                'header_font_size'  => 15,
                'header_font_weight' => '600',
                'header_font_family' => 'sans',
                'content_bg'        => '#ffffff',
                'content_padding_y' => 20,
                'content_padding_x' => 20,
                'content_font_size' => 14,
                'text_color'        => '#475569',
                'border_color'      => '#e5e7eb',
                'border_width'      => 1,
                'gap'               => 12,
                'border_radius'     => 12,
                'icon_style'        => 'plus',
                'icon_shape'        => 'none',
                'separator_style'   => 'border',
                'shadow'            => 'sm',
                'backdrop_blur'     => 0,
                'backdrop_saturate' => 100,
                'panel_hover_lift'  => false,
                'panel_hover_shadow' => 'none',
            ],
            'minimal-underline' => [
                'header_bg'         => '',
                'header_bg_active'  => '',
                'header_text_color' => '#0f172a',
                'header_text_color_active' => '#e8622a',
                'header_padding_y'  => 22,
                'header_padding_x'  => 0,
                'header_font_size'  => 17,
                'header_font_weight' => '600',
                'header_font_family' => 'sans',
                'content_bg'        => '',
                'content_padding_y' => 0,
                'content_padding_x' => 0,
                'content_font_size' => 15,
                'text_color'        => '#475569',
                'border_color'      => '#e5e7eb',
                'border_width'      => 1,
                'gap'               => 0,
                'border_radius'     => 0,
                'icon_style'        => 'plus',
                'icon_shape'        => 'none',
                'separator_style'   => 'border',
                'shadow'            => 'none',
                'backdrop_blur'     => 0,
                'backdrop_saturate' => 100,
                'panel_hover_lift'  => false,
                'panel_hover_shadow' => 'none',
            ],
            'pill-brand' => [
                'header_bg'         => '#ffffff',
                'header_bg_active'  => '#e8622a',
                'header_text_color' => '#1e293b',
                'header_text_color_active' => '#ffffff',
                'header_padding_y'  => 16,
                'header_padding_x'  => 22,
                'header_font_size'  => 15,
                'header_font_weight' => '600',
                'header_font_family' => 'sans',
                'content_bg'        => '#ffffff',
                'content_padding_y' => 18,
                'content_padding_x' => 22,
                'content_font_size' => 14,
                'text_color'        => '#475569',
                'border_color'      => '',
                'border_width'      => 0,
                'gap'               => 8,
                'border_radius'     => 14,
                'icon_style'        => 'chevron',
                'icon_shape'        => 'none',
                'separator_style'   => 'shadow',
                'shadow'            => 'sm',
                'backdrop_blur'     => 0,
                'backdrop_saturate' => 100,
                'panel_hover_lift'  => true,
                'panel_hover_shadow' => 'md',
            ],
            'outline-sharp' => [
                'header_bg'         => '#ffffff',
                'header_bg_active'  => '#fdf2ec',
                'header_text_color' => '#0f172a',
                'header_text_color_active' => '#0f172a',
                'header_padding_y'  => 14,
                'header_padding_x'  => 18,
                'header_font_size'  => 14,
                'header_font_weight' => '600',
                'header_font_family' => 'mono',
                'content_bg'        => '#ffffff',
                'content_padding_y' => 18,
                'content_padding_x' => 18,
                'content_font_size' => 13,
                'text_color'        => '#475569',
                'border_color'      => '#e8622a',
                'border_width'      => 2,
                'gap'               => 0,
                'border_radius'     => 6,
                'icon_style'        => 'plus',
                'icon_shape'        => 'pill',
                'icon_shape_size'   => 32,
                'icon_shape_bg'     => '#fdf2ec',
                'separator_style'   => 'border',
                'shadow'            => 'none',
                'backdrop_blur'     => 0,
                'backdrop_saturate' => 100,
                'panel_hover_lift'  => false,
                'panel_hover_shadow' => 'none',
            ],
            'glass-soft' => [
                'header_bg'         => 'rgba(255,255,255,0.55)',
                'header_bg_active'  => 'rgba(255,255,255,0.75)',
                'header_text_color' => '#0f172a',
                'header_text_color_active' => '#0f172a',
                'header_padding_y'  => 18,
                'header_padding_x'  => 22,
                'header_font_size'  => 15,
                'header_font_weight' => '600',
                'header_font_family' => 'sans',
                'content_bg'        => 'rgba(255,255,255,0.85)',
                'content_padding_y' => 20,
                'content_padding_x' => 22,
                'content_font_size' => 14,
                'text_color'        => '#475569',
                'border_color'      => 'rgba(255,255,255,0.6)',
                'border_width'      => 1,
                'gap'               => 14,
                'border_radius'     => 16,
                'icon_style'        => 'plus',
                'icon_shape'        => 'none',
                'separator_style'   => 'border',
                'shadow'            => 'lg',
                'backdrop_blur'     => 12,
                'backdrop_saturate' => 160,
                'panel_hover_lift'  => false,
                'panel_hover_shadow' => 'none',
            ],
        ];
        return $presets[ $preset_id ] ?? null;
    }

    /**
     * V3.28.0 — Extra CSS rules per preset.
     * - 5 sicuri (card-soft, minimal-underline, pill-brand, outline-sharp, glass-soft):
     *   piccoli ritocchi standard.
     * - 7 audaci (liquid-glass, neon-cyber, brutalist-block, magnetic-liquid,
     *   sticker, retro-terminal, 3d-tilt): parametrici su effect_color /
     *   effect_intensity / effect_speed.
     */
    private function get_preset_extra_css( $preset_id, $uid, $brand_accent, $speed, $s = [] ) {
        // @deprecated v1.0.73 — refactor profondo: i preset audaci settano direttamente i
        // field standard (header_bg, header_text_color, border_*, shadow, ecc.) tramite
        // TILE_PRESETS.accordion in BuilderInspector.vue, e i field wow_* (build_wow_effects_css).
        return '';
    }

    /**
     * Get SVG icon markup for accordion.
     */
    private function get_icon_svg( $style ) {
        switch ( $style ) {
            case 'plus':
                return '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 3v10M3 8h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
            case 'arrow':
                return '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 3v10m0 0l-3-3m3 3l3-3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            case 'caret':
                return '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M4 6l4 5 4-5" fill="currentColor"/></svg>';
            case 'chevron':
            default:
                return '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        }
    }
}
