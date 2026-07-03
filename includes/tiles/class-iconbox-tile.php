<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_IconBox_Tile extends Olobuild_Tile_Base {

    protected $type     = 'iconbox';
    protected $name     = 'Icon Box';
    protected $icon     = 'dashicons-star-filled';
    protected $category = 'marketing';
    protected $defaults = [
        'preset' => 'custom',
        'icon_emoji'        => 'star',
        'title'             => 'Titolo funzionalità',
        'description'       => 'Una breve descrizione di questa funzionalità e del suo valore.',
        'link_url'          => '',
        'link_text'         => 'Scopri di più',
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
        'link_color'              => '',
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
            [ 'key' => 'icon_emoji',  'type' => 'text',     'label' => 'Icon (emoji)' ],
            [ 'key' => 'title',       'type' => 'text',     'label' => 'Title' ],
            [ 'key' => 'description', 'type' => 'textarea', 'label' => 'Description' ],
            [ 'key' => 'link_url',    'type' => 'text',     'label' => 'Link URL' ],
            [ 'key' => 'link_text',   'type' => 'text',     'label' => 'Link Text' ],
            [ 'key' => 'alignment',   'type' => 'select',   'label' => 'Alignment', 'options' => [ 'left' => 'Left', 'center' => 'Center', 'right' => 'Right' ] ],
            [ 'key' => 'text_color',  'type' => 'color',    'label' => 'Text Color' ],
        ];
    }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'mib-' . wp_rand( 10000, 99999 );

        $fg         = $this->safe_color_css( $s['text_color'] );
        $title_clr  = $this->safe_color_css( $s['title_color'] ?? '' );
        $link_clr   = $this->safe_color_css( $s['link_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $icon_size  = floatval( $s['icon_size'] ) ?: 3;
        $title_fs   = absint( $s['title_font_size'] ) ?: 20;
        $title_fw   = absint( $s['title_font_weight'] ) ?: 600;
        $align_class = 'uk-text-' . esc_attr( $s['alignment'] );
        $icon_pos   = in_array( $s['icon_position'], [ 'top', 'left', 'right' ], true ) ? $s['icon_position'] : 'top';
        $is_horiz   = ( $icon_pos === 'left' || $icon_pos === 'right' );
        $icon_bg    = $this->safe_color_css( $s['icon_bg_color'] );
        $icon_clr   = $this->safe_color_css( $s['icon_color'] );
        $icon_shape = in_array( $s['icon_bg_shape'], [ 'circle', 'square', 'rounded' ], true ) ? $s['icon_bg_shape'] : 'circle';

        // Sfondo unificato box icona: pannello media_bg (immagine/video/gradiente/…) con
        // precedenza sui campi legacy bg_type/bg_color/bg_gradient/bg_image (tenuti come
        // fallback). Quando media_bg è impostato, il box diventa contenitore posizionato
        // (relative + overflow:hidden) e il layer/il <video> assoluti rendono lo sfondo.
        $mb     = $this->bg_media_parts( $s['media_bg'] ?? null, $uid );
        $has_mb = $mb['has'];

        // Tile background (mutually exclusive via bg_type) — fallback su style.bg
        // se settings.bg_type non è settato (utente preferisce tab Stile).
        // Applicato SOLO se media_bg non è impostato (precedenza al pannello unico).
        $tile_bg_css = '';
        $bg_type = $s['bg_type'] ?? 'none';
        if ( $has_mb ) {
            $tile_bg_css = 'position:relative;overflow:hidden;';
        } elseif ( $bg_type === 'none' && is_array( $style ) && isset( $style['bg'] ) && is_array( $style['bg'] ) ) {
            $tile_bg_css = $this->build_bg_css_from_style_bg( $style['bg'] );
        } elseif ( $bg_type === 'color' ) {
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
            $tile_border_css = 'border:' . $bw . 'px ' . esc_attr( $s['border_style'] ?? 'solid' ) . ' ' . ( $this->safe_color_css( $s['border_color'] ?? '' ) ?: 'var(--olo-color-border, #e5e7eb)' ) . ';';
        }

        // Border radius
        $br_val = $this->build_border_radius_css( $s['border_radius'] ?? null );
        $br_val_hover_css = Olobuild_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
        $tile_radius_css = $br_val ? 'border-radius:' . $br_val . ';' : '';
        if ( $br_val_hover_css !== '' ) {
            // ensure transition is on the wrapper so we can animate even when base radius is 0
            $tile_radius_css .= 'transition:border-radius 400ms cubic-bezier(.4,0,.2,1);';
        }

        // Shadow
        $shadow_val = Olobuild_Tile_Utils::shadow_value( $s, 'shadow' );
        $tile_shadow_css = ( $shadow_val && $shadow_val !== 'none' ) ? 'box-shadow:' . $shadow_val . ';' : '';

        // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );

        // Icon/title/text spacing
        $icon_gap = intval( $s['icon_gap'] ?? 16 );
        $title_gap = intval( $s['title_gap'] ?? 8 );
        $desc_gap = intval( $s['desc_gap'] ?? 16 );

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: colours via the safe_color_css() whitelist, intval()/absint()/floatval() numerics, in_array() whitelists with fixed-literal ternaries for position/shape, the media_bg declaration via Olobuild_CSS_Builder::get_bg_inline_css() (sanitized internally), Olobuild_Tile_Utils::radius_force_css() and build_border_css()/build_border_hover_css()/build_border_effect_css() helpers; $uid is internally generated. ?>
        <style>
            .<?php echo $uid; ?> {
                <?php if ( $fg ) : ?>color: <?php echo $fg; ?>;<?php endif; ?>
            }
            <?php if ( $has_mb ) : ?>
            .<?php echo $uid; ?> .mib-bg { position: absolute; inset: 0; z-index: 0; <?php echo $mb['css']; ?> }
            .<?php echo $uid; ?> > *:not(.mib-bg):not(.olo-bg-video) { position: relative; z-index: 1; }
            <?php endif; ?>
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
            .<?php echo $uid; ?> .mib-link:focus-visible {
                outline: none;
                border-radius: 3px;
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
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
        <?php if ( $border_css || $border_hover_css || $border_effect_css ) : ?><style>
        <?php if ( $border_css ) echo ".{$uid}{{$border_css}}"; ?>
        <?php echo $border_hover_css; ?>
        <?php echo $border_effect_css; ?>
        </style><?php endif; ?>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-iconbox <?php echo $align_class; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $align_class built above with esc_attr(); inline style composed only of esc_url()/esc_attr()/safe_color_css()/intval()-sanitized fragments ?> <?php echo esc_attr( $uid ); ?> olo-ib-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" style="<?php echo $tile_bg_css . $tile_padding_css . $tile_border_css . $tile_radius_css . $tile_shadow_css; ?>">
          <?php if ( $has_mb ) : ?><div class="mib-bg"></div><?php echo $mb['markup']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup generato da Olobuild_CSS_Builder::get_bg_html_markup(), che escapa il proprio output ?><?php endif; ?>
          <?php if ( $is_horiz ) : ?><div class="mib-flex"><?php endif; ?>

            <?php if ( ! empty( $s['icon_emoji'] ) ) : ?>
                <div class="<?php echo $is_horiz ? 'mib-icon-col' : ''; ?>" style="<?php echo $is_horiz ? '' : 'margin-bottom:' . (int) $icon_gap . 'px;'; ?>">
                    <?php
                    $icon_inline = 'font-size:' . esc_attr( $icon_size ) . 'em;line-height:1;';
                    if ( $icon_clr ) { $icon_inline .= 'color:' . $icon_clr . ';'; }
                    ?>
                    <div style="<?php echo $icon_inline; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $icon_inline composed above from esc_attr()'d floatval size and safe_color_css()-whitelisted colour ?>" <?php echo $icon_bg ? 'class="mib-icon-bg"' : ''; ?>>
                        <?php if ( preg_match( '/^[a-z][a-z0-9-]*$/', $s['icon_emoji'] ) ) : ?>
                            <?php echo $this->render_icon_html( $s['icon_emoji'], floatval( $icon_size ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- icon markup generated by the render_icon_html() helper (sanitized SVG / esc_attr()'d uk-icon attrs) ?>
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
                <h3 class="mib-title<?php echo $t_tfx_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr) ?>"<?php echo $t_tfx_data; ?>><?php echo esc_html( $title_plain ); ?></h3>
                <div class="mib-desc<?php echo $d_tfx_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); gap is intval()'d ?>" style="margin: 0 0 <?php echo (int) $desc_gap; ?>px; opacity: 0.8; line-height: 1.6;"<?php echo $d_tfx_data; ?>><?php echo nl2br( esc_html( $desc_plain ) ); ?></div>
                <?php if ( ! empty( $s['link_url'] ) ) : ?>
                    <a href="<?php echo esc_url( $s['link_url'] ); ?>" class="mib-link" style="color:<?php echo $link_clr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- safe_color_css()-whitelisted colour; may legitimately be a var() value with fallback that esc_attr() could alter ?>"><?php echo esc_html( wp_strip_all_tags( $s['link_text'] ) ); ?> &rarr;</a>
                <?php endif; ?>
            <?php if ( $is_horiz ) : ?></div><?php endif; ?>

          <?php if ( $is_horiz ) : ?></div><?php endif; ?>
        </div>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Text_Effects::css() from whitelisted effects, sanitized colors and integer timings
        $this->tfx_print_script();
        return ob_get_clean();
    }
}
