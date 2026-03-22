<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Team_Tile extends Olo_Tile_Base {

    protected $type     = 'team';
    protected $name     = 'Membro del team';
    protected $icon     = 'dashicons-businessperson';
    protected $category = 'marketing';
    protected $defaults = [
        'photo'              => '',
        'hover_image'        => '',
        'hover_video'        => '',
        'name'               => 'Jane Smith',
        'role'               => 'Lead Designer',
        'bio'                => 'Appassionata di creare esperienze utente eccellenti.',
        'link_text'          => 'Profilo',
        'link_url'           => '',
        'photo_size'         => '120',
        'photo_shape'        => 'circle',
        'photo_radius'       => '12',
        'photo_border_width' => '3',
        'photo_border_color' => '#FFFFFF',
        'photo_shadow'       => 'md',
        'photo_gap'          => '12',
        'info_bg_color'      => '',
        'info_text_color'    => '#FFFFFF',
        'info_padding'       => '24',
        'info_width'         => '100',
        'info_margin'        => '0',
        'info_radius'        => '16',
        'info_border_width'  => '0',
        'info_border_color'  => '',
        'info_align'         => 'center',
        'name_size'          => '20',
        'name_weight'        => '600',
        'role_size'          => '14',
        'role_color'         => '',
        'bio_size'           => '14',
        'bg_color'           => '',
        'tile_padding'       => '16',
        'border_radius'      => '16',
        'border_width'       => '0',
        'border_color'       => '',
    ];

    public function get_controls() { return []; }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-team-' . wp_rand( 10000, 99999 );

        $bg       = $this->safe_color_css( $s['bg_color'] ) ?: 'transparent';
        $fg       = $this->safe_color_css( $s['info_text_color'] ) ?: '#FFFFFF';
        $tile_r   = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $tile_bw  = intval( $s['border_width'] );
        $tile_bc  = $this->safe_color_css( $s['border_color'] ) ?: 'var(--olo-color-text, #374151)';
        $tile_pad = intval( $s['tile_padding'] );

        // Photo
        $ph_size   = intval( $s['photo_size'] ) ?: 120;
        $ph_shape  = $s['photo_shape'] ?: 'circle';
        $ph_bw     = intval( $s['photo_border_width'] );
        $ph_bc     = $this->safe_color_css( $s['photo_border_color'] ) ?: '#FFFFFF';
        $ph_gap    = max( intval( $s['photo_gap'] ), 0 );
        $outer_sz  = $ph_size + $ph_bw * 2;
        $ph_shadow  = Olo_Tile_Utils::shadow( $s['photo_shadow'] ?? 'none', 'photo' );

        // Photo shape
        $hex_clip = 'polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%)';
        if ( $ph_shape === 'circle' ) {
            $outer_radius = '50%';
            $inner_radius = '50%';
        } elseif ( $ph_shape === 'rounded' ) {
            $r = intval( $s['photo_radius'] );
            $outer_radius = Olo_Tile_Utils::border_radius( $s['photo_radius'] ?? 0 );
            $inner_radius = max( $r - $ph_bw, 0 ) . 'px';
        } else {
            $outer_radius = '0';
            $inner_radius = '0';
        }

        // Info
        $info_bg  = $this->safe_color_css( $s['info_bg_color'] ) ?: 'var(--olo-color-primary, #6366F1)';
        $info_pad = Olo_Tile_Utils::spacing_css( $s['tile_padding'] ?? $s['info_padding'] ?? 24, 24 );
        $info_w   = intval( $s['info_width'] ) ?: 100;
        $info_m   = intval( $s['info_margin'] );
        $info_r   = Olo_Tile_Utils::border_radius( $s['info_radius'] ?? 0 );
        $info_bw  = intval( $s['info_border_width'] );
        $info_bc  = $this->safe_color_css( $s['info_border_color'] ) ?: 'var(--olo-color-text, #374151)';
        $align    = in_array( $s['info_align'], [ 'left', 'center', 'right' ] ) ? $s['info_align'] : 'center';

        // Alignment
        $jc = 'center';
        if ( $align === 'left' )  $jc = 'flex-start';
        if ( $align === 'right' ) $jc = 'flex-end';

        // Typography
        $name_fs = intval( $s['name_size'] ) ?: 20;
        $name_fw = intval( $s['name_weight'] ) ?: 600;
        $role_fs = intval( $s['role_size'] ) ?: 14;
        $role_cl = $this->safe_color_css( $s['role_color'] ) ?: '';
        $bio_fs  = intval( $s['bio_size'] ) ?: 14;

        // Sanitize
        $name_html = esc_html( wp_strip_all_tags( $s['name'] ) );
        $role_html = esc_html( wp_strip_all_tags( $s['role'] ) );
        $bio_html  = nl2br( esc_html( wp_strip_all_tags( $s['bio'] ) ) );
        $link_text = esc_html( wp_strip_all_tags( $s['link_text'] ?: 'Profilo' ) );

        $photo_is_video = ! empty( $s['photo'] ) && preg_match( '/\.(mp4|webm|ogg)(\?.*)?$/i', $s['photo'] );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                overflow: visible;
                border-radius: <?php echo $tile_r; ?>;
                background: <?php echo $bg; ?>;
                padding: <?php echo $tile_pad; ?>px;
                <?php if ( $tile_bw > 0 ) : ?>
                border: <?php echo $tile_bw; ?>px solid <?php echo $tile_bc; ?>;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-team-photo-wrap {
                display: flex;
                justify-content: <?php echo $jc; ?>;
                margin-bottom: <?php echo $ph_gap; ?>px;
            }
            .<?php echo $uid; ?> .olo-team-photo-outer {
                width: <?php echo $outer_sz; ?>px;
                height: <?php echo $outer_sz; ?>px;
                flex-shrink: 0;
                display: flex; align-items: center; justify-content: center;
                <?php if ( $ph_shape === 'hexagon' ) : ?>
                clip-path: <?php echo $hex_clip; ?>;
                background: <?php echo $ph_bw > 0 ? $ph_bc : 'transparent'; ?>;
                <?php else : ?>
                border-radius: <?php echo $outer_radius; ?>;
                <?php if ( $ph_bw > 0 ) : ?>
                background: <?php echo $ph_bc; ?>;
                <?php endif; ?>
                <?php if ( $ph_shadow !== 'none' ) : ?>
                box-shadow: <?php echo $ph_shadow; ?>;
                <?php endif; ?>
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-team-photo-inner {
                overflow: hidden;
                width: <?php echo $ph_size; ?>px;
                height: <?php echo $ph_size; ?>px;
                flex-shrink: 0;
                <?php if ( $ph_shape === 'hexagon' ) : ?>
                clip-path: <?php echo $hex_clip; ?>;
                <?php else : ?>
                border-radius: <?php echo $inner_radius; ?>;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-team-photo-inner .olo-hover-wrap {
                width: 100%; height: 100%; position: relative;
            }
            .<?php echo $uid; ?> .olo-team-photo-inner .olo-hover-media {
                position: absolute; inset: 0; z-index: 1;
                opacity: 0; transition: opacity .3s;
            }
            .<?php echo $uid; ?> .olo-team-photo-inner .olo-hover-wrap:hover .olo-hover-media {
                opacity: 1;
            }
            .<?php echo $uid; ?> .olo-team-photo-inner img,
            .<?php echo $uid; ?> .olo-team-photo-inner video {
                width: 100%; height: 100%; object-fit: cover; display: block;
            }
            .<?php echo $uid; ?> .olo-team-info-wrap {
                display: flex;
                justify-content: <?php echo $jc; ?>;
                <?php if ( $info_m > 0 ) : ?>
                padding: 0 <?php echo $info_m; ?>px <?php echo $info_m; ?>px;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-team-info {
                width: <?php echo $info_w; ?>%;
                padding: <?php echo $info_pad; ?>;
                text-align: <?php echo $align; ?>;
                color: <?php echo $fg; ?>;
                border-radius: <?php echo $info_r; ?>;
                <?php if ( $info_bg ) : ?>
                background: <?php echo $info_bg; ?>;
                <?php endif; ?>
                <?php if ( $info_bw > 0 ) : ?>
                border: <?php echo $info_bw; ?>px solid <?php echo $info_bc; ?>;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-team-name {
                font-size: <?php echo $name_fs; ?>px;
                font-weight: <?php echo $name_fw; ?>;
                margin: 0 0 4px; line-height: 1.3; color: <?php echo $fg; ?>;
            }
            .<?php echo $uid; ?> .olo-team-role {
                font-size: <?php echo $role_fs; ?>px;
                <?php if ( $role_cl ) : ?>
                color: <?php echo $role_cl; ?>;
                <?php else : ?>
                opacity: 0.75;
                <?php endif; ?>
                margin-bottom: 10px;
            }
            .<?php echo $uid; ?> .olo-team-bio {
                font-size: <?php echo $bio_fs; ?>px;
                opacity: 0.8; line-height: 1.5; margin: 0;
            }
            .<?php echo $uid; ?> .olo-team-link {
                display: inline-block; margin-top: 12px;
                color: <?php echo $fg; ?>; text-decoration: none;
                font-weight: 500; font-size: 14px; opacity: 0.9;
            }
            .<?php echo $uid; ?> .olo-team-link:hover {
                opacity: 1; text-decoration: none;
            }
        </style>
        <div class="olo-team <?php echo esc_attr( $uid ); ?>">
            <div class="olo-team-photo-wrap">
                <div class="olo-team-photo-outer">
                    <div class="olo-team-photo-inner">
                        <?php if ( ! empty( $s['photo'] ) && $photo_is_video ) : ?>
                            <video src="<?php echo esc_url( $s['photo'] ); ?>" autoplay muted loop playsinline></video>
                        <?php elseif ( ! empty( $s['photo'] ) ) : ?>
                            <?php
                            $img = Olo_Tile_Utils::img_srcset( absint( $s['photo_id'] ?? 0 ), $s['photo'], $s['name'] );
                            echo $this->render_hover_wrap( $img, $s['hover_image'] ?? '', $s['hover_video'] ?? '' );
                            ?>
                        <?php else : ?>
                            <div style="width:100%;height:100%;background:var(--olo-color-secondary, #1F2937);display:flex;align-items:center;justify-content:center;font-size:<?php echo round( $ph_size * 0.4 ); ?>px">&#x1F464;</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="olo-team-info-wrap">
                <div class="olo-team-info">
                    <h3 class="olo-team-name"><?php echo $name_html; ?></h3>
                    <div class="olo-team-role"><?php echo $role_html; ?></div>
                    <?php if ( ! empty( $s['bio'] ) ) : ?>
                        <div class="olo-team-bio"><?php echo $bio_html; ?></div>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['link_url'] ) ) : ?>
                        <a href="<?php echo esc_url( $s['link_url'] ); ?>" class="olo-team-link"><?php echo $link_text; ?> &rarr;</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
