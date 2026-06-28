<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_LinkInBio_Tile extends Olobuild_Tile_Base {

    protected $type     = 'linkinbio';
    protected $name     = 'Link in Bio';
    protected $icon     = 'dashicons-smartphone';
    protected $category = 'marketing';
    protected $defaults = [
        'items'              => [],
        'profile_image'      => '',
        'profile_name'       => 'Il tuo nome',
        'profile_bio'        => 'Una breve descrizione qui',
        'max_width'          => '420',
        'link_color'         => '',
        'link_bg'            => '',
        'link_hover_bg'      => '',
        'link_border_radius' => '12',
        'link_padding'       => '14',
        'gap'                => '12',
        'text_align'         => 'center',
        'profile_name_color' => '',
        'bio_color'          => '',
        'background_color'   => '',
        'background_gradient' => '',
        'show_social_icons'  => false,
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
            [ 'key' => 'items',              'type' => 'content-items', 'label' => olobuild_t( 'Link' ) ],
            [ 'key' => 'profile_image',      'type' => 'image',   'label' => olobuild_t( 'Foto profilo' ) ],
            [ 'key' => 'profile_name',       'type' => 'text',    'label' => olobuild_t( 'Nome' ) ],
            [ 'key' => 'profile_bio',        'type' => 'text',    'label' => olobuild_t( 'Bio' ) ],
            [ 'key' => 'profile_name_color', 'type' => 'color',   'label' => olobuild_t( 'Colore nome' ) ],
            [ 'key' => 'bio_color',          'type' => 'color',   'label' => olobuild_t( 'Colore bio' ) ],
            [ 'key' => 'max_width',          'type' => 'range',   'label' => olobuild_t( 'Larghezza max' ), 'min' => 300, 'max' => 600 ],
            [ 'key' => 'text_align',         'type' => 'select',  'label' => olobuild_t( 'Allineamento' ), 'options' => [ 'left' => 'Sinistra', 'center' => 'Centro', 'right' => 'Destra' ] ],
            [ 'key' => 'gap',                'type' => 'range',   'label' => olobuild_t( 'Gap (px)' ), 'min' => 4, 'max' => 24 ],
            [ 'key' => 'link_color',         'type' => 'color',   'label' => olobuild_t( 'Colore testo link' ) ],
            [ 'key' => 'link_bg',            'type' => 'color',   'label' => olobuild_t( 'Sfondo link' ) ],
            [ 'key' => 'link_hover_bg',      'type' => 'color',   'label' => olobuild_t( 'Sfondo hover' ) ],
            [ 'key' => 'link_border_radius', 'type' => 'range',   'label' => olobuild_t( 'Arrotondamento' ), 'min' => 0, 'max' => 30 ],
            [ 'key' => 'link_padding',       'type' => 'range',   'label' => olobuild_t( 'Padding' ), 'min' => 8, 'max' => 24 ],
            [ 'key' => 'background_color',   'type' => 'color',   'label' => olobuild_t( 'Colore sfondo' ) ],
            [ 'key' => 'background_gradient','type' => 'text',    'label' => olobuild_t( 'Gradiente CSS' ) ],
            [ 'key' => 'show_social_icons',  'type' => 'toggle',  'label' => olobuild_t( 'Mostra icone social' ) ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $items = is_array( $s['items'] ) ? $s['items'] : [];
        if ( empty( $items ) ) {
            return '<div class="olo-linkinbio" style="text-align:center;padding:20px;color:var(--olo-color-text-faint, #9CA3AF);">' . olobuild_t( 'Aggiungi dei link nell\'inspector.' ) . '</div>';
        }

        $max_width    = max( 300, min( 600, absint( $s['max_width'] ) ) );
        $gap          = max( 4, min( 24, absint( $s['gap'] ) ) );
        $radius       = Olobuild_Tile_Utils::border_radius( $s['link_border_radius'] ?? 0 );
        $radius_hover_css = Olobuild_Tile_Utils::radius_force_css( $s['link_border_radius_hover'] ?? null );
        $padding = Olobuild_Tile_Utils::spacing_css( $s['tile_padding'] ?? $s['link_padding'] ?? 14, 14 );
        $text_align   = in_array( $s['text_align'], [ 'left', 'center', 'right' ], true ) ? $s['text_align'] : 'center';

        $link_color      = $this->safe_color_css( $s['link_color'] )      ?: 'var(--olo-color-primary, #e1474f)';
        $link_bg         = $this->safe_color_css( $s['link_bg'] )         ?: 'var(--olo-color-surface, #FFFFFF)';
        $link_hover_bg   = $this->safe_color_css( $s['link_hover_bg'] )   ?: 'var(--olo-color-surface-alt, #F3F4F6)';
        $name_color      = $this->safe_color_css( $s['profile_name_color'] ) ?: 'var(--olo-color-text, #374151)';
        $bio_color       = $this->safe_color_css( $s['bio_color'] )       ?: 'var(--olo-color-text-soft, #6b7280)';
        $bg_color        = $this->safe_color_css( $s['background_color'] ) ?: 'var(--olo-color-surface-alt, #F3F4F6)';

        $bg_gradient = '';
        if ( ! empty( $s['background_gradient'] ) ) {
            // Basic sanitization: only allow safe gradient chars
            $bg_gradient = preg_replace( '/[^a-zA-Z0-9(),.\s%#\-deg]/', '', $s['background_gradient'] );
        }

        $bg_style = $bg_gradient ? $bg_gradient : $bg_color;

        $uid = 'olo-lib-' . wp_unique_id();

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: colours via the safe_color_css() whitelist, gradient via a strict preg_replace() character whitelist, absint()/min()/max() clamps for sizes, in_array() whitelist for alignment, Olobuild_Tile_Utils border_radius()/radius_force_css()/spacing_css() helpers; $uid is internally generated. ?>
        <style>
            #<?php echo $uid; ?> { background: <?php echo $bg_style; ?>; padding: 32px 16px; display: flex; justify-content: center; }
            #<?php echo $uid; ?> .olo-lib-inner { width: 100%; max-width: <?php echo $max_width; ?>px; text-align: <?php echo $text_align; ?>; }
            #<?php echo $uid; ?> .olo-lib-avatar { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; <?php echo $text_align === 'center' ? 'margin: 0 auto 12px;' : 'margin: 0 0 12px;'; ?> display: block; }
            #<?php echo $uid; ?> .olo-lib-avatar-placeholder { width: 80px; height: 80px; border-radius: 50%; background: var(--olo-color-surface-alt, #F3F4F6); color: var(--olo-color-text-faint, #9CA3AF); <?php echo $text_align === 'center' ? 'margin: 0 auto 12px;' : 'margin: 0 0 12px;'; ?> display: flex; align-items: center; justify-content: center; }
            #<?php echo $uid; ?> .olo-lib-avatar-placeholder svg { width: 38px; height: 38px; fill: currentColor; stroke: currentColor; }
            #<?php echo $uid; ?> .olo-lib-name { font-weight: 700; font-size: 1.2em; color: <?php echo $name_color; ?>; margin: 0 0 4px; }
            #<?php echo $uid; ?> .olo-lib-bio { color: <?php echo $bio_color; ?>; font-size: 0.9em; margin: 0 0 20px; }
            #<?php echo $uid; ?> .olo-lib-links { display: flex; flex-direction: column; gap: <?php echo $gap; ?>px; }
            #<?php echo $uid; ?> .olo-lib-btn { display: block; width: 100%; text-align: <?php echo $text_align; ?>; padding: <?php echo $padding; ?>; font-size: 0.95em; font-weight: 500; text-decoration: none; transition: all 0.2s ease; box-sizing: border-box; }
            #<?php echo $uid; ?> .olo-lib-btn--filled { background: <?php echo $link_bg; ?>; color: <?php echo $link_color; ?>; border: 1px solid rgba(0,0,0,0.08); border-radius: <?php echo $radius; ?>; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
            #<?php echo $uid; ?> .olo-lib-btn--filled:hover { background: <?php echo $link_hover_bg; ?>; }
            #<?php echo $uid; ?> .olo-lib-btn--outline { background: transparent; color: <?php echo $link_color; ?>; border: 2px solid <?php echo $link_color; ?>; border-radius: <?php echo $radius; ?>; }
            #<?php echo $uid; ?> .olo-lib-btn--outline:hover { background: <?php echo $link_hover_bg; ?>; }
            #<?php echo $uid; ?> .olo-lib-btn--minimal { background: transparent; color: <?php echo $link_color; ?>; border: none; text-decoration: underline; border-radius: <?php echo $radius; ?>; }
            #<?php echo $uid; ?> .olo-lib-btn--minimal:hover { background: <?php echo $link_hover_bg; ?>; }
            #<?php echo $uid; ?> .olo-lib-btn:hover { transform: translateY(-1px); }
            #<?php echo $uid; ?> .olo-lib-btn:focus-visible { outline: none; box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent); }
            #<?php echo $uid; ?> .olo-lib-icon { margin-right: 8px; opacity: 0.7; display: inline-flex; width: 1em; height: 1em; vertical-align: -0.1em; }
            #<?php echo $uid; ?> .olo-lib-icon svg { width: 100%; height: 100%; fill: currentColor; stroke: currentColor; }
            <?php if ( $radius_hover_css !== '' ) : ?>#<?php echo $uid; ?> .olo-lib-btn{transition:border-radius 400ms cubic-bezier(.4,0,.2,1),background-color 0.2s,color 0.2s,transform 0.2s}#<?php echo $uid; ?> .olo-lib-btn:hover{border-radius:<?php echo $radius_hover_css; ?> !important}<?php endif; ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div id="<?php echo esc_attr( $uid ); ?>" class="olo-linkinbio">
            <div class="olo-lib-inner">
                <!-- Profile -->
                <div style="margin-bottom:20px;">
                    <?php if ( ! empty( $s['profile_image'] ) ) : ?>
                        <img class="olo-lib-avatar" src="<?php echo esc_url( $s['profile_image'] ); ?>" alt="<?php echo esc_attr( $s['profile_name'] ); ?>" loading="lazy" />
                    <?php else : ?>
                        <div class="olo-lib-avatar-placeholder"><?php echo $this->render_icon_html( 'user', 1 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- icon markup generated by the render_icon_html() helper (sanitized SVG / esc_attr()'d uk-icon attrs) ?></div>
                    <?php endif; ?>

                    <?php if ( ! empty( $s['profile_name'] ) ) : ?>
                        <div class="olo-lib-name"><?php echo esc_html( $s['profile_name'] ); ?></div>
                    <?php endif; ?>

                    <?php if ( ! empty( $s['profile_bio'] ) ) : ?>
                        <div class="olo-lib-bio"><?php echo esc_html( $s['profile_bio'] ); ?></div>
                    <?php endif; ?>
                </div>

                <!-- Links -->
                <div class="olo-lib-links">
                    <?php foreach ( $items as $item ) :
                        $title = isset( $item['title'] ) ? sanitize_text_field( $item['title'] ) : '';
                        $url   = isset( $item['url'] )   ? esc_url( $item['url'] ) : '#';
                        $style = isset( $item['style'] )  ? sanitize_key( $item['style'] ) : 'filled';
                        $icon  = isset( $item['icon'] )  ? trim( (string) $item['icon'] ) : '';

                        if ( ! in_array( $style, [ 'filled', 'outline', 'minimal' ], true ) ) {
                            $style = 'filled';
                        }

                        if ( empty( $title ) ) {
                            continue;
                        }
                    ?>
                        <?php list( $lt_cls, $lt_data ) = $this->tfx_attrs( $s, 'title', $title ); ?>
                        <a href="<?php echo $url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $url esc_url()'d above; $style from in_array() whitelist; tfx_attrs() fragments escaped internally ?>" class="olo-lib-btn olo-lib-btn--<?php echo $style; ?><?php echo $lt_cls; ?>" target="_blank" rel="noopener noreferrer"<?php echo $lt_data; ?>>
                            <?php if ( $icon !== '' ) :
                                // Parity col builder (LinkInBioTile.vue): icona dal set SVG, fallback testo/emoji letterale.
                                $icon_html = $this->render_icon_html( $icon, 1 );
                            ?><span class="olo-lib-icon"><?php echo $icon_html !== '' ? $icon_html : esc_html( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $icon_html generated by the render_icon_html() helper (sanitized SVG); fallback is esc_html()'d ?></span><?php endif; ?>
                            <?php echo esc_html( $title ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
        $tfx_css = $this->tfx_css( $s, '#' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Text_Effects::css() from whitelisted effects, sanitized colors and integer timings
        $this->tfx_print_script();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }
}
