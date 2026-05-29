<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Hotspot_Tile extends Olo_Tile_Base {

    protected $type     = 'hotspot';
    protected $name     = 'Hotspot';
    protected $icon     = 'dashicons-location-alt';
    protected $category = 'interactive';
    protected $defaults = [
        'preset' => 'custom',
        'image'           => '',
        'image_height'    => '400',
        'markers'         => [
            [ 'pos_x' => '30', 'pos_y' => '40', 'title' => 'Punto di interesse', 'description' => 'Descrizione del primo hotspot.', 'icon' => 'pin', 'tooltip_position' => 'top' ],
            [ 'pos_x' => '65', 'pos_y' => '55', 'title' => 'Secondo punto', 'description' => 'Descrizione del secondo hotspot.', 'icon' => 'pin', 'tooltip_position' => 'bottom' ],
        ],
        'marker_color'    => '',
        'marker_size'     => '24',
        'pulse_animation' => true,
        'tooltip_bg'      => '',
        'tooltip_color'   => '',
        'tooltip_width'   => '220',
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
            [ 'key' => 'image',           'type' => 'image',  'label' => 'Image' ],
            [ 'key' => 'image_height',    'type' => 'range',  'label' => 'Image Height' ],
            [ 'key' => 'markers',         'type' => 'custom', 'label' => 'Markers' ],
            [ 'key' => 'marker_color',    'type' => 'color',  'label' => 'Marker Color' ],
            [ 'key' => 'marker_size',     'type' => 'range',  'label' => 'Marker Size' ],
            [ 'key' => 'pulse_animation', 'type' => 'toggle', 'label' => 'Pulse Animation' ],
            [ 'key' => 'tooltip_bg',      'type' => 'color',  'label' => 'Tooltip Background' ],
            [ 'key' => 'tooltip_color',   'type' => 'color',  'label' => 'Tooltip Text Color' ],
            [ 'key' => 'tooltip_width',   'type' => 'range',  'label' => 'Tooltip Width' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $markers = is_array( $s['markers'] ) ? $s['markers'] : [];
        if ( empty( $markers ) ) {
            return '';
        }

        $uid           = 'olo-hotspot-' . wp_unique_id();
        $image         = esc_url( $s['image'] );
        $img_height    = max( 200, min( 800, intval( $s['image_height'] ) ) );
        $marker_color  = $this->safe_color_css( $s['marker_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $marker_size   = max( 16, min( 40, intval( $s['marker_size'] ) ) );
        $pulse         = ! empty( $s['pulse_animation'] );
        $tooltip_bg    = $this->safe_color_css( $s['tooltip_bg'] ) ?: 'var(--olo-color-surface, #FFFFFF)';
        $tooltip_color = $this->safe_color_css( $s['tooltip_color'] ) ?: 'var(--olo-color-text, #374151)';
        $tooltip_width = max( 150, min( 350, intval( $s['tooltip_width'] ) ) );

        $pin_svg = '<svg width="' . $marker_size . '" height="' . $marker_size . '" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/></svg>';

        ob_start();
        ?>
        <style>
            .<?php echo esc_attr( $uid ); ?> {
                position: relative;
                width: 100%;
                height: <?php echo $img_height; ?>px;
                overflow: hidden;
                border-radius: <?php echo $this->build_border_radius_css( $s['border_radius'] ?? '0' ) ?: '8px'; ?>;
            }
            .<?php echo esc_attr( $uid ); ?> > img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-marker {
                position: absolute;
                transform: translate(-50%, -50%);
                cursor: pointer;
                z-index: 10;
                display: flex;
                align-items: center;
                justify-content: center;
                color: <?php echo $marker_color; ?>;
                filter: drop-shadow(0 2px 4px rgba(0,0,0,0.4));
                transition: transform 0.2s ease;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-marker:hover {
                transform: translate(-50%, -50%) scale(1.15);
            }
            <?php if ( $pulse ) : ?>
            @keyframes <?php echo esc_attr( $uid ); ?>-pulse {
                0%, 100% { box-shadow: 0 0 0 0 color-mix(in srgb, <?php echo $marker_color; ?> 50%, transparent); }
                50% { box-shadow: 0 0 0 <?php echo round( $marker_size * 0.6 ); ?>px color-mix(in srgb, <?php echo $marker_color; ?> 0%, transparent); }
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-ring {
                position: absolute;
                width: <?php echo $marker_size + 8; ?>px;
                height: <?php echo $marker_size + 8; ?>px;
                border-radius: 50%;
                animation: <?php echo esc_attr( $uid ); ?>-pulse 2s ease-in-out infinite;
            }
            <?php endif; ?>
            .<?php echo esc_attr( $uid ); ?> .olo-hs-tooltip {
                display: none;
                position: absolute;
                z-index: 20;
                width: <?php echo $tooltip_width; ?>px;
                background: <?php echo $tooltip_bg; ?>;
                color: <?php echo $tooltip_color; ?>;
                padding: 12px 14px;
                border-radius: 6px;
                font-size: 13px;
                line-height: 1.5;
                box-shadow: 0 4px 16px rgba(0,0,0,0.3);
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-tooltip.is-visible {
                display: block;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-tooltip-title {
                font-weight: 700;
                font-size: 14px;
                margin-bottom: 4px;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-tooltip::after {
                content: '';
                position: absolute;
                width: 0;
                height: 0;
                border: 6px solid transparent;
            }
            /* Arrow positions */
            .<?php echo esc_attr( $uid ); ?> .olo-hs-tooltip[data-pos="top"] {
                bottom: 100%;
                left: 50%;
                transform: translateX(-50%);
                margin-bottom: 10px;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-tooltip[data-pos="top"]::after {
                top: 100%;
                left: 50%;
                transform: translateX(-50%);
                border-top-color: <?php echo $tooltip_bg; ?>;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-tooltip[data-pos="bottom"] {
                top: 100%;
                left: 50%;
                transform: translateX(-50%);
                margin-top: 10px;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-tooltip[data-pos="bottom"]::after {
                bottom: 100%;
                left: 50%;
                transform: translateX(-50%);
                border-bottom-color: <?php echo $tooltip_bg; ?>;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-tooltip[data-pos="left"] {
                right: 100%;
                top: 50%;
                transform: translateY(-50%);
                margin-right: 10px;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-tooltip[data-pos="left"]::after {
                left: 100%;
                top: 50%;
                transform: translateY(-50%);
                border-left-color: <?php echo $tooltip_bg; ?>;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-tooltip[data-pos="right"] {
                left: 100%;
                top: 50%;
                transform: translateY(-50%);
                margin-left: 10px;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-tooltip[data-pos="right"]::after {
                right: 100%;
                top: 50%;
                transform: translateY(-50%);
                border-right-color: <?php echo $tooltip_bg; ?>;
            }
        </style>

        <div class="<?php echo esc_attr( $uid ); ?> olo-hs-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" id="<?php echo esc_attr( $uid ); ?>">
            <?php if ( $image ) : ?>
                <img src="<?php echo $image; ?>" alt="" loading="lazy" />
            <?php else : ?>
                <div style="width:100%;height:100%;background:var(--olo-color-surface-alt, #F3F4F6);display:flex;align-items:center;justify-content:center;color:var(--olo-color-text-faint, #9CA3AF);font-size:14px;">Seleziona un'immagine</div>
            <?php endif; ?>

            <?php foreach ( $markers as $idx => $marker ) :
                $pos_x   = max( 0, min( 100, floatval( $marker['pos_x'] ?? 50 ) ) );
                $pos_y   = max( 0, min( 100, floatval( $marker['pos_y'] ?? 50 ) ) );
                $title   = wp_kses_post( $marker['title'] ?? '' );
                $desc    = wp_kses_post( $marker['description'] ?? '' );
                $m_icon  = sanitize_text_field( $marker['icon'] ?? 'pin' );
                $tt_pos  = in_array( $marker['tooltip_position'] ?? 'top', [ 'top', 'bottom', 'left', 'right' ], true ) ? $marker['tooltip_position'] : 'top';
            ?>
            <div class="olo-hs-marker"
                 style="left:<?php echo $pos_x; ?>%;top:<?php echo $pos_y; ?>%;"
                 data-idx="<?php echo $idx; ?>">
                <?php if ( $pulse ) : ?><span class="olo-hs-ring"></span><?php endif; ?>
                <?php
                if ( $m_icon === 'pin' || $m_icon === '' ) {
                    echo $pin_svg;
                } else {
                    echo '<span style="font-size:' . $marker_size . 'px;">' . esc_html( $m_icon ) . '</span>';
                }
                ?>
                <?php
                list( $hst_cls, $hst_data ) = $this->tfx_attrs( $s, 'title', wp_strip_all_tags( $title ) );
                list( $hsd_cls, $hsd_data ) = $this->tfx_attrs( $s, 'description', wp_strip_all_tags( $desc ) );
                ?>
                <div class="olo-hs-tooltip" data-pos="<?php echo esc_attr( $tt_pos ); ?>">
                    <?php if ( $title ) : ?>
                        <div class="olo-hs-tooltip-title<?php echo $hst_cls; ?>"<?php echo $hst_data; ?>><?php echo $title; ?></div>
                    <?php endif; ?>
                    <?php if ( $desc ) : ?>
                        <div class="olo-hs-tooltip-desc<?php echo $hsd_cls; ?>"<?php echo $hsd_data; ?>><?php echo $desc; ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <script>
        (function(){
            var container = document.getElementById('<?php echo esc_js( $uid ); ?>');
            if(!container){return}
            container.addEventListener('click', function(e){
                var marker = e.target.closest('.olo-hs-marker');
                if(!marker){
                    /* Click outside marker: close all tooltips */
                    var allTips = container.querySelectorAll('.olo-hs-tooltip');
                    allTips.forEach(function(t){ t.classList.remove('is-visible'); });
                    return;
                }
                var tooltip = marker.querySelector('.olo-hs-tooltip');
                if(!tooltip){return}
                var isVisible = tooltip.classList.contains('is-visible');
                /* Close all tooltips first */
                var allTips = container.querySelectorAll('.olo-hs-tooltip');
                allTips.forEach(function(t){ t.classList.remove('is-visible'); });
                /* Toggle clicked tooltip */
                if(!isVisible){
                    tooltip.classList.add('is-visible');
                }
            });
        })();
        </script>
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
}
