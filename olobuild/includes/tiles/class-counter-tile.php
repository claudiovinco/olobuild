<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Counter_Tile extends Olo_Tile_Base {

    protected $type     = 'counter';
    protected $name     = 'Contatore';
    protected $icon     = 'dashicons-performance';
    protected $category = 'content';
    protected $defaults = [
        'number'             => '1250',
        'label'              => 'Clienti soddisfatti',
        'prefix'             => '',
        'suffix'             => '+',
        'icon_emoji'         => "\xF0\x9F\x8F\x86",
        'icon_size'          => '40',
        'text_color'         => '#F3F4F6',
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
        'border_radius'      => '0',
        'border_width'       => '0',
        'border_color'       => '#374151',
    ];

    public function get_controls() { return []; }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-cnt-' . wp_rand( 10000, 99999 );

        $fg       = $this->safe_color( $s['text_color'] ) ?: '#F3F4F6';
        $lbl_clr  = $this->safe_color( $s['label_color'] ) ?: '';
        $num_fs   = absint( $s['number_font_size'] ) ?: 48;
        $num_fw   = absint( $s['number_font_weight'] ) ?: 700;
        $lbl_fs   = absint( $s['label_font_size'] ) ?: 14;
        $lbl_fw   = absint( $s['label_font_weight'] ) ?: 400;
        $icon_sz  = absint( $s['icon_size'] ) ?: 40;
        $pad      = absint( $s['padding'] );
        $tile_r   = intval( $s['border_radius'] );
        $tile_bw  = intval( $s['border_width'] );
        $tile_bc  = $this->safe_color( $s['border_color'] ) ?: '#374151';

        $bg_type  = $s['bg_type'] ?: 'color';
        $bg_color = $this->safe_color( $s['bg_color'] ) ?: '';

        $label    = $this->sanitize_richtext( $s['label'] );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                position: relative; overflow: hidden; text-align: center;
                padding: <?php echo $pad; ?>px;
                border-radius: <?php echo $tile_r; ?>px;
                color: <?php echo $fg; ?>;
                <?php if ( $bg_type === 'color' && $bg_color ) : ?>
                background: <?php echo $bg_color; ?>;
                <?php endif; ?>
                <?php if ( $tile_bw > 0 ) : ?>
                border: <?php echo $tile_bw; ?>px solid <?php echo $tile_bc; ?>;
                <?php endif; ?>
            }
            <?php if ( $bg_type === 'image' && ! empty( $s['bg_image'] ) ) : ?>
            .<?php echo $uid; ?> .olo-cnt-bg {
                position: absolute; inset: 0;
                background: url('<?php echo esc_url( $s['bg_image'] ); ?>') center/cover no-repeat;
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
                background: <?php echo $this->safe_color( $s['overlay_color'] ) ?: '#000'; ?>;
                opacity: <?php echo ( intval( $s['overlay_opacity'] ) ?: 50 ) / 100; ?>;
            }
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-cnt-inner { position: relative; z-index: 2; }
            .<?php echo $uid; ?> .olo-cnt-icon {
                font-size: <?php echo $icon_sz; ?>px; line-height: 1.2; margin-bottom: 8px;
            }
            .<?php echo $uid; ?> .olo-cnt-number {
                font-size: <?php echo $num_fs; ?>px;
                font-weight: <?php echo $num_fw; ?>;
                line-height: 1.1;
            }
            .<?php echo $uid; ?> .olo-cnt-label {
                font-size: <?php echo $lbl_fs; ?>px;
                font-weight: <?php echo $lbl_fw; ?>;
                margin-top: 8px;
                <?php if ( $lbl_clr ) : ?>
                color: <?php echo $lbl_clr; ?>;
                <?php else : ?>
                opacity: 0.7;
                <?php endif; ?>
            }
        </style>
        <div class="olo-counter <?php echo esc_attr( $uid ); ?>">
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
                            <span style="color:inherit;" uk-icon="icon: <?php echo esc_attr( $s['icon_emoji'] ); ?>; ratio: <?php echo round( $icon_sz / 20, 1 ); ?>"></span>
                        <?php else : ?>
                            <?php echo esc_html( $s['icon_emoji'] ); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div class="olo-cnt-number">
                    <?php echo esc_html( $s['prefix'] ); ?><?php echo esc_html( $s['number'] ); ?><?php echo esc_html( $s['suffix'] ); ?>
                </div>
                <?php if ( ! empty( $s['label'] ) ) : ?>
                    <div class="olo-cnt-label"><?php echo $label; ?></div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
