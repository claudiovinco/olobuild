<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_ServiceGallery_Tile extends Olo_Tile_Base {

    protected $type     = 'servicegallery';
    protected $name     = 'Galleria Servizio';
    protected $icon     = 'dashicons-images-alt2';
    protected $category = 'booking';
    protected $defaults = [
        'meta_prefix'   => '_olo_service_',
        'columns'       => 3,
        'rows'          => 2,
        'gap'           => 8,
        'thumb_height'  => 200,
        'thumb_radius'  => 12,
        'lightbox'      => true,
        // Effects — automatic
        'fx_kenburns'         => false,
        'fx_kenburns_speed'   => 20,
        'fx_kenburns_scale'   => 1.12,
        // Effects — hover
        'fx_hover_zoom'       => true,
        'fx_hover_zoom_scale' => 1.08,
        'fx_hover_tilt'       => false,
        'fx_hover_tilt_angle' => 8,
        // Effects — visual filters
        'fx_vignette'          => false,
        'fx_vignette_strength' => 40,
        'fx_grain'             => false,
        'fx_grain_opacity'     => 6,
        'fx_tint'              => false,
        'fx_tint_color'        => '#1E3A5F',
        'fx_tint_opacity'      => 10,
        'fx_tint_blend'        => 'multiply',
        // "+N" overlay
        'more_bg'       => 'rgba(0,0,0,0.55)',
        'more_color'    => '#FFFFFF',
        'more_size'     => 28,
        // Mobile
        'mobile_columns' => 2,
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        global $post;
        if ( ! $post || ! is_singular() ) {
            return '<div style="padding:24px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);background:var(--olo-color-muted, #F3F4F6);border-radius:8px">'
                 . '<p style="margin:0">Inserisci in un template single.</p></div>';
        }

        $pid   = $post->ID;
        $pfx   = rtrim( $s['meta_prefix'], '_' ) . '_';
        $title = get_the_title( $pid );

        $gallery_ids = get_post_meta( $pid, $pfx . 'gallery', true );
        if ( is_string( $gallery_ids ) ) {
            $gallery_ids = maybe_unserialize( $gallery_ids );
        }
        if ( ! is_array( $gallery_ids ) || empty( $gallery_ids ) ) {
            return '';
        }

        $cols    = max( 2, min( 6, absint( $s['columns'] ) ) );
        $rows    = max( 1, min( 5, absint( $s['rows'] ) ) );
        $gap     = absint( $s['gap'] );
        $th      = absint( $s['thumb_height'] ) ?: 200;
        $radius  = Olo_Tile_Utils::border_radius( $s['thumb_radius'] ?? 0 );
        $uid     = 'olo-sgal-' . wp_rand( 10000, 99999 );
        $mob_cols = max( 1, min( 4, absint( $s['mobile_columns'] ) ) );

        $max_visible = $cols * $rows;
        $total       = count( $gallery_ids );
        $extra       = max( 0, $total - $max_visible );

        $lightbox_attr = ! empty( $s['lightbox'] ) ? ' uk-lightbox="animation: slide"' : '';

        // Effects
        $kb_speed  = max( 10, absint( $s['fx_kenburns_speed'] ) );
        $kb_scale  = max( 1.05, min( 1.3, floatval( $s['fx_kenburns_scale'] ) ) );
        $hz_scale  = max( 1.02, min( 1.2, floatval( $s['fx_hover_zoom_scale'] ) ) );
        $tilt_ang  = max( 3, min( 15, absint( $s['fx_hover_tilt_angle'] ) ) );
        $vig_str   = max( 15, min( 60, absint( $s['fx_vignette_strength'] ) ) );
        $grain_opa = max( 3, min( 20, absint( $s['fx_grain_opacity'] ) ) );
        $tint_opa  = max( 5, min( 50, absint( $s['fx_tint_opacity'] ) ) );
        $more_size = max( 16, min( 48, absint( $s['more_size'] ) ) );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                display: grid;
                grid-template-columns: repeat(<?php echo $cols; ?>, 1fr);
                gap: <?php echo $gap; ?>px;
            }
            .<?php echo $uid; ?> .olo-sgal-item {
                position: relative;
                display: block;
                border-radius: <?php echo $radius; ?>;
                overflow: hidden;
                height: <?php echo $th; ?>px;
                cursor: pointer;
            }
            .<?php echo $uid; ?> .olo-sgal-item img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
                transition: transform 0.5s cubic-bezier(.25,.46,.45,.94), filter 0.5s ease;
                will-change: transform;
            }

            /* Ken Burns */
            <?php if ( ! empty( $s['fx_kenburns'] ) ) : ?>
            @keyframes <?php echo $uid; ?>-kb {
                0%   { transform: scale(1) translate(0,0); }
                33%  { transform: scale(<?php echo $kb_scale; ?>) translate(-1.5%,-1%); }
                66%  { transform: scale(<?php echo $kb_scale - 0.03; ?>) translate(1%,0.5%); }
                100% { transform: scale(1) translate(0,0); }
            }
            .<?php echo $uid; ?> .olo-sgal-item img {
                animation: <?php echo $uid; ?>-kb <?php echo $kb_speed; ?>s ease-in-out infinite;
            }
            .<?php echo $uid; ?> .olo-sgal-item:nth-child(2n) img { animation-delay: -<?php echo round( $kb_speed / 3 ); ?>s; }
            .<?php echo $uid; ?> .olo-sgal-item:nth-child(3n) img { animation-delay: -<?php echo round( $kb_speed * 2 / 3 ); ?>s; }
            <?php endif; ?>

            /* Hover zoom */
            <?php if ( ! empty( $s['fx_hover_zoom'] ) && empty( $s['fx_hover_tilt'] ) ) : ?>
            .<?php echo $uid; ?> .olo-sgal-item:hover img {
                transform: scale(<?php echo $hz_scale; ?>);
            }
            <?php endif; ?>

            /* Hover tilt 3D */
            <?php if ( ! empty( $s['fx_hover_tilt'] ) ) : ?>
            .<?php echo $uid; ?> .olo-sgal-item {
                perspective: 500px;
                transform-style: preserve-3d;
            }
            .<?php echo $uid; ?> .olo-sgal-item img {
                transform-origin: center center;
            }
            .<?php echo $uid; ?> .olo-sgal-item:hover img {
                transform: scale(1.05) rotateX(<?php echo $tilt_ang; ?>deg) rotateY(-<?php echo $tilt_ang; ?>deg);
                box-shadow: <?php echo $tilt_ang; ?>px <?php echo $tilt_ang; ?>px <?php echo $tilt_ang * 3; ?>px rgba(0,0,0,0.3);
            }
            <?php endif; ?>

            /* Vignette */
            <?php if ( ! empty( $s['fx_vignette'] ) ) : ?>
            .<?php echo $uid; ?> .olo-sgal-item::after {
                content: '';
                position: absolute;
                inset: 0;
                border-radius: <?php echo $radius; ?>;
                box-shadow: inset 0 0 <?php echo $vig_str * 2; ?>px <?php echo $vig_str; ?>px rgba(0,0,0,0.<?php echo min( 45, $vig_str ); ?>);
                pointer-events: none;
                z-index: 2;
            }
            <?php endif; ?>

            /* Grain */
            <?php if ( ! empty( $s['fx_grain'] ) ) : ?>
            .<?php echo $uid; ?> .olo-sgal-grain {
                position: absolute;
                inset: 0;
                z-index: 2;
                opacity: 0.<?php echo str_pad( $grain_opa, 2, '0', STR_PAD_LEFT ); ?>;
                pointer-events: none;
                background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
                background-repeat: repeat;
                background-size: 128px 128px;
                mix-blend-mode: overlay;
            }
            <?php endif; ?>

            /* Tint */
            <?php if ( ! empty( $s['fx_tint'] ) ) : ?>
            .<?php echo $uid; ?> .olo-sgal-tint {
                position: absolute;
                inset: 0;
                z-index: 1;
                background: <?php echo $this->safe_color_css( $s['fx_tint_color'] ); ?>;
                opacity: 0.<?php echo str_pad( $tint_opa, 2, '0', STR_PAD_LEFT ); ?>;
                mix-blend-mode: <?php echo esc_attr( $s['fx_tint_blend'] ); ?>;
                pointer-events: none;
                transition: opacity 0.4s ease;
            }
            .<?php echo $uid; ?> .olo-sgal-item:hover .olo-sgal-tint {
                opacity: 0.<?php echo str_pad( max( 2, $tint_opa - 5 ), 2, '0', STR_PAD_LEFT ); ?>;
            }
            <?php endif; ?>

            /* "+N" overlay */
            .<?php echo $uid; ?> .olo-sgal-more {
                position: absolute;
                inset: 0;
                z-index: 5;
                display: flex;
                align-items: center;
                justify-content: center;
                background: <?php echo $this->safe_color_css( $s['more_bg'] ); ?>;
                color: <?php echo $this->safe_color_css( $s['more_color'] ); ?>;
                font-size: <?php echo $more_size; ?>px;
                font-weight: 700;
                letter-spacing: -0.5px;
                pointer-events: none;
                transition: background 0.3s ease;
                border-radius: <?php echo $radius; ?>;
            }
            .<?php echo $uid; ?> .olo-sgal-item:hover .olo-sgal-more {
                background: rgba(0,0,0,0.4);
            }

            /* Hidden lightbox items */
            .<?php echo $uid; ?> .olo-sgal-hidden {
                position: absolute;
                width: 0;
                height: 0;
                overflow: hidden;
                pointer-events: none;
                opacity: 0;
            }

            /* Mobile */
            @media (max-width: 640px) {
                .<?php echo $uid; ?> {
                    grid-template-columns: repeat(<?php echo $mob_cols; ?>, 1fr);
                }
            }
        </style>
        <div class="<?php echo esc_attr( $uid ); ?>"<?php echo $lightbox_attr; ?>>
            <?php
            $i = 0;
            foreach ( $gallery_ids as $att_id ) :
                $full      = wp_get_attachment_image_url( $att_id, 'large' );
                $thumb_url = wp_get_attachment_image_url( $att_id, 'medium' );
                $alt       = get_post_meta( $att_id, '_wp_attachment_image_alt', true ) ?: $title;
                if ( ! $full ) continue;
                $i++;

                $is_visible   = ( $i <= $max_visible );
                $is_last_vis  = ( $i === $max_visible && $extra > 0 );
            ?>
                <?php if ( $is_visible ) : ?>
                <a class="olo-sgal-item" href="<?php echo esc_url( $full ); ?>" data-caption="<?php echo esc_attr( $alt ); ?> (<?php echo $i; ?>/<?php echo $total; ?>)">
                    <?php echo Olo_Tile_Utils::img_srcset( $att_id, $thumb_url, $alt ); ?>
                    <?php if ( ! empty( $s['fx_tint'] ) ) : ?><div class="olo-sgal-tint"></div><?php endif; ?>
                    <?php if ( ! empty( $s['fx_grain'] ) ) : ?><div class="olo-sgal-grain"></div><?php endif; ?>
                    <?php if ( $is_last_vis ) : ?>
                        <div class="olo-sgal-more">+<?php echo $extra; ?></div>
                    <?php endif; ?>
                </a>
                <?php else : ?>
                <a class="olo-sgal-hidden" href="<?php echo esc_url( $full ); ?>" data-caption="<?php echo esc_attr( $alt ); ?> (<?php echo $i; ?>/<?php echo $total; ?>)"></a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
