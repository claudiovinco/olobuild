<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_IconBox_Tile extends Olo_Tile_Base {

    protected $type     = 'iconbox';
    protected $name     = 'Icon Box';
    protected $icon     = 'dashicons-star-filled';
    protected $category = 'marketing';
    protected $defaults = [
        'icon_emoji'        => 'star',
        'title'             => 'Feature Title',
        'description'       => 'A short description of this feature and why it matters.',
        'link_url'          => '',
        'link_text'         => 'Learn more',
        'alignment'         => 'center',
        'text_color'        => '',
        'title_color'       => '',
        'icon_size'         => '3',
        'icon_position'     => 'top',
        'icon_bg_color'     => '',
        'icon_bg_shape'     => 'circle',
        'icon_color'        => '',
        'title_font_size'   => '20',
        'title_font_weight' => '600',
        'link_color'        => '',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'icon_emoji',  'type' => 'text',     'label' => 'Icon (emoji)' ],
            [ 'key' => 'title',       'type' => 'text',     'label' => 'Title' ],
            [ 'key' => 'description', 'type' => 'textarea', 'label' => 'Description' ],
            [ 'key' => 'link_url',    'type' => 'text',     'label' => 'Link URL' ],
            [ 'key' => 'link_text',   'type' => 'text',     'label' => 'Link Text' ],
            [ 'key' => 'alignment',   'type' => 'select',   'label' => 'Alignment', 'options' => [ 'left' => 'Left', 'center' => 'Center', 'right' => 'Right' ] ],
            [ 'key' => 'text_color',  'type' => 'color',    'label' => 'Text Color' ],
        ];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'mib-' . wp_rand( 10000, 99999 );

        $fg         = $this->safe_color_css( $s['text_color'] );
        $title_clr  = $this->safe_color_css( $s['title_color'] ?? '' );
        $link_clr   = $this->safe_color_css( $s['link_color'] ) ?: 'var(--olo-color-primary, #6366F1)';
        $icon_size  = floatval( $s['icon_size'] ) ?: 3;
        $title_fs   = absint( $s['title_font_size'] ) ?: 20;
        $title_fw   = absint( $s['title_font_weight'] ) ?: 600;
        $align_class = 'uk-text-' . esc_attr( $s['alignment'] );
        $icon_pos   = in_array( $s['icon_position'], [ 'top', 'left', 'right' ], true ) ? $s['icon_position'] : 'top';
        $is_horiz   = ( $icon_pos === 'left' || $icon_pos === 'right' );
        $icon_bg    = $this->safe_color_css( $s['icon_bg_color'] );
        $icon_clr   = $this->safe_color_css( $s['icon_color'] );
        $icon_shape = in_array( $s['icon_bg_shape'], [ 'circle', 'square', 'rounded' ], true ) ? $s['icon_bg_shape'] : 'circle';

        // Tile background (mutually exclusive via bg_type)
        $tile_bg_css = '';
        $bg_type = $s['bg_type'] ?? 'none';
        if ( $bg_type === 'color' ) {
            $c = $this->safe_color_css( $s['bg_color'] ?? '' );
            if ( $c ) $tile_bg_css = 'background-color:' . $c . ';';
        } elseif ( $bg_type === 'gradient' ) {
            $g = $s['bg_gradient'] ?? null;
            if ( is_array( $g ) ) {
                $stops = [];
                foreach ( ($g['stops'] ?? []) as $stop ) {
                    $stops[] = esc_attr( $stop['color'] ?? '#000' ) . ' ' . intval( $stop['position'] ?? 0 ) . '%';
                }
                if ( $stops ) {
                    $gtype = ($g['type'] ?? 'linear') === 'radial' ? 'radial-gradient(circle, ' : 'linear-gradient(' . intval( $g['angle'] ?? 180 ) . 'deg, ';
                    $tile_bg_css = 'background:' . $gtype . implode(', ', $stops) . ');';
                }
            }
        } elseif ( $bg_type === 'image' ) {
            $img = $s['bg_image'] ?? '';
            if ( $img ) {
                $tile_bg_css = 'background-image:url(' . esc_url( $img ) . ');';
                $tile_bg_css .= 'background-size:' . esc_attr( $s['bg_image_size'] ?? 'cover' ) . ';';
                $tile_bg_css .= 'background-position:' . esc_attr( $s['bg_image_position'] ?? 'center center' ) . ';';
                $tile_bg_css .= 'background-repeat:no-repeat;';
            }
        }

        // Tile padding
        $tile_padding_css = '';
        $tp = $s['tile_padding'] ?? null;
        if ( is_array( $tp ) ) {
            $tile_padding_css = 'padding:' . intval($tp['top'] ?? 24) . 'px ' . intval($tp['right'] ?? 24) . 'px ' . intval($tp['bottom'] ?? 24) . 'px ' . intval($tp['left'] ?? 24) . 'px;';
        } elseif ( is_numeric( $tp ) ) {
            $tile_padding_css = 'padding:' . intval($tp) . 'px;';
        }

        // Tile border
        $tile_border_css = '';
        $bw = intval( $s['border_width'] ?? 0 );
        if ( $bw > 0 ) {
            $tile_border_css = 'border:' . $bw . 'px ' . esc_attr( $s['border_style'] ?? 'solid' ) . ' ' . ( $this->safe_color_css( $s['border_color'] ?? '' ) ?: '#e5e7eb' ) . ';';
        }

        // Border radius
        $br_val = $this->build_border_radius_css( $s['border_radius'] ?? null );
        $br_val_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
        $tile_radius_css = $br_val ? 'border-radius:' . $br_val . ';' : '';
        if ( $br_val_hover_css !== '' ) {
            // ensure transition is on the wrapper so we can animate even when base radius is 0
            $tile_radius_css .= 'transition:border-radius 400ms cubic-bezier(.4,0,.2,1);';
        }

        // Shadow
        $shadow_val = Olo_Tile_Utils::shadow_value( $s, 'shadow' );
        $tile_shadow_css = ( $shadow_val && $shadow_val !== 'none' ) ? 'box-shadow:' . $shadow_val . ';' : '';

        // Icon/title/text spacing
        $icon_gap = intval( $s['icon_gap'] ?? 16 );
        $title_gap = intval( $s['title_gap'] ?? 8 );
        $desc_gap = intval( $s['desc_gap'] ?? 16 );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                <?php if ( $fg ) : ?>color: <?php echo $fg; ?>;<?php endif; ?>
            }
            <?php if ( $is_horiz ) : ?>
            .<?php echo $uid; ?> .mib-flex {
                display: flex;
                flex-direction: <?php echo $icon_pos === 'right' ? 'row-reverse' : 'row'; ?>;
                align-items: flex-start;
                gap: <?php echo $icon_gap; ?>px;
                text-align: left;
            }
            .<?php echo $uid; ?> .mib-icon-col { flex-shrink: 0; }
            .<?php echo $uid; ?> .mib-content-col { flex: 1; }
            <?php endif; ?>
            .<?php echo $uid; ?> .mib-title {
                font-size: <?php echo $title_fs; ?>px;
                font-weight: <?php echo $title_fw; ?>;
                margin: 0 0 <?php echo $title_gap; ?>px;
                <?php if ( $title_clr ) : ?>color: <?php echo $title_clr; ?>;<?php endif; ?>
            }
            .<?php echo $uid; ?> .mib-link {
                color: <?php echo $link_clr; ?> !important;
                text-decoration: none !important;
                font-weight: 500;
            }
            .<?php echo $uid; ?> .mib-link:hover {
                text-decoration: underline !important;
            }
            <?php if ( $icon_bg ) : ?>
            .<?php echo $uid; ?> .mib-icon-bg {
                background: <?php echo $icon_bg; ?>;
                padding: 16px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: <?php echo $icon_shape === 'circle' ? '50%' : ( $icon_shape === 'rounded' ? '12px' : '4px' ); ?>;
            }
            <?php endif; ?>
            <?php if ( $br_val_hover_css !== '' ) : ?>.<?php echo $uid; ?>:hover{border-radius:<?php echo $br_val_hover_css; ?> !important}<?php endif; ?>
        </style>
        <div class="olo-iconbox <?php echo $align_class; ?> <?php echo esc_attr( $uid ); ?>" style="<?php echo $tile_bg_css . $tile_padding_css . $tile_border_css . $tile_radius_css . $tile_shadow_css; ?>">
          <?php if ( $is_horiz ) : ?><div class="mib-flex"><?php endif; ?>

            <?php if ( ! empty( $s['icon_emoji'] ) ) : ?>
                <div class="<?php echo $is_horiz ? 'mib-icon-col' : ''; ?>" style="<?php echo $is_horiz ? '' : 'margin-bottom:' . $icon_gap . 'px;'; ?>">
                    <?php
                    $icon_inline = 'font-size:' . esc_attr( $icon_size ) . 'em;line-height:1;';
                    if ( $icon_clr ) { $icon_inline .= 'color:' . $icon_clr . ';'; }
                    ?>
                    <div style="<?php echo $icon_inline; ?>" <?php echo $icon_bg ? 'class="mib-icon-bg"' : ''; ?>>
                        <?php if ( preg_match( '/^[a-z][a-z0-9-]*$/', $s['icon_emoji'] ) ) : ?>
                            <span style="color:inherit;" uk-icon="icon: <?php echo esc_attr( $s['icon_emoji'] ); ?>; ratio: <?php echo esc_attr( $icon_size ); ?>"></span>
                        <?php else : ?>
                            <?php echo esc_html( $s['icon_emoji'] ); ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php
            $title_plain = wp_strip_all_tags( $s['title'] );
            $desc_plain  = wp_strip_all_tags( $s['description'] );
            list( $t_tfx_cls, $t_tfx_data ) = $this->tfx_attrs( $s, 'title', $title_plain );
            list( $d_tfx_cls, $d_tfx_data ) = $this->tfx_attrs( $s, 'description', $desc_plain );
            ?>
            <?php if ( $is_horiz ) : ?><div class="mib-content-col"><?php endif; ?>
                <h3 class="mib-title<?php echo $t_tfx_cls; ?>"<?php echo $t_tfx_data; ?>><?php echo esc_html( $title_plain ); ?></h3>
                <div class="mib-desc<?php echo $d_tfx_cls; ?>" style="margin: 0 0 <?php echo $desc_gap; ?>px; opacity: 0.8; line-height: 1.6;"<?php echo $d_tfx_data; ?>><?php echo nl2br( esc_html( $desc_plain ) ); ?></div>
                <?php if ( ! empty( $s['link_url'] ) ) : ?>
                    <a href="<?php echo esc_url( $s['link_url'] ); ?>" class="mib-link" style="color:<?php echo $link_clr; ?>"><?php echo esc_html( wp_strip_all_tags( $s['link_text'] ) ); ?> &rarr;</a>
                <?php endif; ?>
            <?php if ( $is_horiz ) : ?></div><?php endif; ?>

          <?php if ( $is_horiz ) : ?></div><?php endif; ?>
        </div>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();
        return ob_get_clean();
    }
}
