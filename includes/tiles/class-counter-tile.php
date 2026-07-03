<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Counter_Tile extends Olobuild_Tile_Base {

    protected $type     = 'counter';
    protected $name     = 'Contatore';
    protected $icon     = 'dashicons-performance';
    protected $category = 'marketing';
    protected $defaults = [
        'preset' => 'custom',
        'number'             => '1250',
        'label'              => 'Clienti soddisfatti',
        'prefix'             => '',
        'suffix'             => '+',
        'icon_emoji'         => 'bolt',
        'icon_size'          => '40',
        'text_color'         => '',
        'number_font_size'   => '48',
        'number_font_weight' => '700',
        'label_color'        => '',
        'label_font_size'    => '14',
        'label_font_weight'  => '400',
        'bg_type'            => 'color',
        'bg_color'           => '',
        'bg_image'           => '',
        'bg_video'           => '',
        'overlay'            => false,
        'overlay_color'      => '#000000',
        'overlay_opacity'    => '50',
        'padding'            => '32',
        'border_radius'           => '0',
        'border_width'            => '0',
        'border_color'            => '',
        'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() { return []; }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-cnt-' . wp_rand( 10000, 99999 );

        $fg       = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text, #374151)';
        $lbl_clr  = $this->safe_color_css( $s['label_color'] ) ?: '';
        $num_fs   = absint( $s['number_font_size'] ) ?: 48;
        $num_fw   = absint( $s['number_font_weight'] ) ?: 700;
        $lbl_fs   = absint( $s['label_font_size'] ) ?: 14;
        $lbl_fw   = absint( $s['label_font_weight'] ) ?: 400;
        $icon_sz  = absint( $s['icon_size'] ) ?: 40;
        $pad = Olobuild_Tile_Utils::spacing_css( $s['tile_padding'] ?? $s['padding'] ?? 32, 32 );
        $tile_r   = Olobuild_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $tile_r_hover_css = Olobuild_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
        $tile_bw  = intval( $s['border_width'] );
        $tile_bc  = $this->safe_color_css( $s['border_color'] ) ?: 'var(--olo-color-text, #374151)';

        // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );

        $bg_type  = $s['bg_type'] ?: 'color';
        $bg_color = $this->safe_color_css( $s['bg_color'] ) ?: '';

        $label    = esc_html( wp_strip_all_tags( $s['label'] ) );

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above (safe_color_css/absint/intval/Olobuild_Tile_Utils spacing-radius helpers/esc_url/Olobuild_Tile_Base build_border_* helpers); $uid is an internal generated class name. ?>
        <style>
            .<?php echo $uid; ?> {
                position: relative; overflow: hidden; text-align: center;
                padding: <?php echo $pad; ?>;
                border-radius: <?php echo $tile_r; ?>;
                color: <?php echo $fg; ?>;
                <?php if ( $bg_type === 'color' && $bg_color ) : ?>
                background: <?php echo $bg_color; ?>;
                <?php endif; ?>
                <?php if ( $tile_bw > 0 ) : ?>
                border: <?php echo (int) $tile_bw; ?>px solid <?php echo $tile_bc; ?>;
                <?php endif; ?>
            }
            <?php if ( $tile_r_hover_css !== '' ) : ?>.<?php echo $uid; ?>{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?>:hover{border-radius:<?php echo $tile_r_hover_css; ?> !important}<?php endif; ?>
            <?php if ( $bg_type === 'image' && ! empty( $s['bg_image'] ) ) : ?>
            .<?php echo $uid; ?> .olo-cnt-bg {
                position: absolute; inset: 0;
                background: url('<?php echo esc_url( $s['bg_image'] ); ?>') <?php echo esc_attr( Olobuild_Tile_Utils::focal_pos( $s, 'bg_image' ) ); ?>/cover no-repeat;
            }
            <?php endif; ?>
            <?php if ( $bg_type === 'video' && ! empty( $s['bg_video'] ) ) : ?>
            .<?php echo $uid; ?> .olo-cnt-video {
                position: absolute; inset: 0; width: 100%; height: 100%;
                object-fit: cover; z-index: 0;
            }
            <?php endif; ?>
            <?php if ( filter_var( $s['overlay'], FILTER_VALIDATE_BOOLEAN ) && $bg_type !== 'color' ) : ?>
            .<?php echo $uid; ?> .olo-cnt-overlay {
                position: absolute; inset: 0; z-index: 1;
                background: <?php echo $this->safe_color_css( $s['overlay_color'] ) ?: '#000'; ?>;
                opacity: <?php echo ( intval( $s['overlay_opacity'] ) ?: 50 ) / 100; ?>;
            }
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-cnt-inner { position: relative; z-index: 2; }
            .<?php echo $uid; ?> .olo-cnt-icon {
                font-size: <?php echo (int) $icon_sz; ?>px; line-height: 1.2; margin-bottom: 8px;
            }
            .<?php echo $uid; ?> .olo-cnt-number {
                font-size: <?php echo (int) $num_fs; ?>px;
                font-weight: <?php echo (int) $num_fw; ?>;
                line-height: 1.1;
                letter-spacing: -0.02em;
                font-variant-numeric: tabular-nums;
            }
            .<?php echo $uid; ?> .olo-cnt-suffix { color: <?php echo $this->safe_color_css( $s['number_color'] ?? '' ) ?: 'var(--olo-color-primary, #e1474f)'; ?>; }
            .<?php echo $uid; ?> .olo-cnt-label {
                font-size: <?php echo (int) $lbl_fs; ?>px;
                font-weight: <?php echo (int) $lbl_fw; ?>;
                margin-top: 8px;
                <?php if ( $lbl_clr ) : ?>
                color: <?php echo $lbl_clr; ?>;
                <?php else : ?>
                opacity: 0.7;
                <?php endif; ?>
            }
        </style>
        <?php if ( $border_css || $border_hover_css || $border_effect_css ) : ?><style>
        <?php if ( $border_css ) echo ".{$uid}{{$border_css}}"; ?>
        <?php echo $border_hover_css; ?>
        <?php echo $border_effect_css; ?>
        </style><?php endif; ?>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-counter <?php echo esc_attr( $uid ); ?> olo-cnt-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>">
            <?php if ( $bg_type === 'image' && ! empty( $s['bg_image'] ) ) : ?>
                <div class="olo-cnt-bg"></div>
            <?php endif; ?>
            <?php if ( $bg_type === 'video' && ! empty( $s['bg_video'] ) ) : ?>
                <video class="olo-cnt-video" src="<?php echo esc_url( $s['bg_video'] ); ?>" autoplay muted loop playsinline></video>
            <?php endif; ?>
            <?php if ( filter_var( $s['overlay'], FILTER_VALIDATE_BOOLEAN ) && $bg_type !== 'color' ) : ?>
                <div class="olo-cnt-overlay"></div>
            <?php endif; ?>

            <div class="olo-cnt-inner">
                <?php if ( ! empty( $s['icon_emoji'] ) ) : ?>
                    <div class="olo-cnt-icon">
                        <?php if ( preg_match( '/^[a-z][a-z0-9-]*$/', $s['icon_emoji'] ) ) : ?>
                            <span style="color:inherit;" uk-icon="icon: <?php echo esc_attr( $s['icon_emoji'] ); ?>; ratio: <?php echo (float) round( $icon_sz / 20, 1 ); ?>"></span>
                        <?php else : ?>
                            <?php echo esc_html( $s['icon_emoji'] ); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div class="olo-cnt-number">
                    <?php echo esc_html( $s['prefix'] ); ?><?php echo esc_html( $s['number'] ); ?><?php if ( $s['suffix'] !== '' ) : ?><span class="olo-cnt-suffix"><?php echo esc_html( $s['suffix'] ); ?></span><?php endif; ?>
                </div>
                <?php if ( ! empty( $s['label'] ) ) : ?>
                    <?php list( $l_tfx_cls, $l_tfx_data ) = $this->tfx_attrs( $s, 'label', wp_strip_all_tags( $s['label'] ) ); ?>
                    <div class="olo-cnt-label<?php echo $l_tfx_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx attrs escaped internally by Olobuild_Text_Effects; $label escaped via esc_html() at assignment above ?>"<?php echo $l_tfx_data; ?>><?php echo $label; ?></div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated internally by Olobuild_Text_Effects::css() from sanitized settings.
        $this->tfx_print_script();
        return ob_get_clean();
    }
}
