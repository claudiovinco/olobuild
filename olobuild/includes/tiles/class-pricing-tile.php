<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Olo_Pricing_Tile extends Olo_Tile_Base {

    protected $type     = 'pricing';
    protected $name     = 'Listino prezzi';
    protected $icon     = 'dashicons-money-alt';
    protected $category = 'content';
    protected $defaults = [
        'plan_name'    => 'Piano Pro',
        'price'        => '29',
        'currency'     => '€',
        'currency_size' => '14',
        'period'       => '/mese',
        'features'     => "Progetti illimitati\n10 GB di spazio\nSupporto prioritario\nDominio personalizzato",
        'is_popular'   => false,
        'badge_text'   => 'Popolare',
        'badge_style'  => 'pill',
        'badge_top'    => '-12',
        'badge_radius' => '20',
        'badge_bg_color'   => '',
        'badge_text_color' => '#FFFFFF',
        'price_shape'              => 'none',
        'price_shape_color'        => '#374151',
        'price_shape_glow'         => false,
        'price_shape_glow_color'   => '#6366F1',
        'price_shape_glow_intensity' => '15',
        'price_shape_border_width' => '0',
        'price_shape_border_color' => '#6366F1',
        'check_style'  => 'checkmark',
        'bg_color'     => '#1F2937',
        'accent_color' => '#6366F1',
        'text_color'   => '#F3F4F6',
        'bg_type'      => 'color',
        'bg_image'     => '',
        'bg_video'     => '',
        'overlay'      => false,
        'overlay_color'   => '#000000',
        'overlay_opacity' => '50',
        'cta_text'     => 'Inizia ora',
        'cta_url'      => '#',
        'cta_target'   => '_self',
        'cta_bg_color'      => '',
        'cta_text_color'    => '#FFFFFF',
        'cta_width'         => '100',
        'cta_radius'        => '8',
        'cta_border_width'  => '0',
        'cta_border_color'  => '#FFFFFF',
        'cta_hover_effect'      => 'lift',
        'cta_hover_bg_color'    => '',
        'cta_hover_text_color'  => '',
        'border_radius' => '12',
        'border_width'  => '0',
        'border_color'  => '#374151',
    ];

    public function get_controls() { return []; }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-price-' . wp_rand( 10000, 99999 );

        $accent    = $this->safe_color( $s['accent_color'] ) ?: '#6366F1';
        $bg        = $this->safe_color( $s['bg_color'] ) ?: '#1F2937';
        $fg        = $this->safe_color( $s['text_color'] ) ?: '#F3F4F6';
        $features  = array_filter( array_map( 'trim', explode( "\n", $s['features'] ) ) );
        $popular   = filter_var( $s['is_popular'], FILTER_VALIDATE_BOOLEAN );
        $tile_r    = intval( $s['border_radius'] );
        $tile_bw   = intval( $s['border_width'] );
        $tile_bc   = $this->safe_color( $s['border_color'] ) ?: '#374151';

        // Check icons
        $checks = [
            'checkmark'    => '&#10003;',
            'circle-check' => '&#9679;',
            'dot'          => '&#8226;',
            'star'         => '&#9733;',
            'arrow'        => '&#8594;',
            'none'         => '',
        ];
        $check = $checks[ $s['check_style'] ] ?? '&#10003;';

        // CTA
        $cta_bg  = $this->safe_color( $s['cta_bg_color'] ) ?: $accent;
        $cta_fg  = $this->safe_color( $s['cta_text_color'] ) ?: '#FFFFFF';
        $cta_r   = intval( $s['cta_radius'] );
        $cta_bw  = intval( $s['cta_border_width'] );
        $cta_bc  = $this->safe_color( $s['cta_border_color'] ) ?: '#FFFFFF';
        $hover   = $s['cta_hover_effect'] ?: 'none';
        $cta_w   = intval( $s['cta_width'] ) ?: 100;
        $h_bg    = $this->safe_color( $s['cta_hover_bg_color'] ) ?: '';
        $h_fg    = $this->safe_color( $s['cta_hover_text_color'] ) ?: '';

        // Background
        $bg_type = $s['bg_type'] ?: 'color';

        // Price shape
        $shape      = $s['price_shape'] ?: 'none';
        $shape_col  = $this->safe_color( $s['price_shape_color'] ) ?: '#374151';
        $shape_glow = filter_var( $s['price_shape_glow'], FILTER_VALIDATE_BOOLEAN );
        $shape_gc   = $this->safe_color( $s['price_shape_glow_color'] ) ?: '#6366F1';
        $shape_gi   = intval( $s['price_shape_glow_intensity'] ) ?: 15;
        $shape_bw   = intval( $s['price_shape_border_width'] );
        $shape_bc   = $this->safe_color( $s['price_shape_border_color'] ) ?: '#6366F1';

        // Badge
        $badge_bg  = $this->safe_color( $s['badge_bg_color'] ) ?: $accent;
        $badge_fg  = $this->safe_color( $s['badge_text_color'] ) ?: '#FFFFFF';
        $badge_r   = intval( $s['badge_radius'] );
        $badge_st  = $s['badge_style'] ?: 'pill';
        $badge_top = intval( $s['badge_top'] );

        // Currency
        $cur_size = intval( $s['currency_size'] ) ?: 14;

        // Sanitize
        $plan_name = $this->sanitize_richtext( $s['plan_name'] );
        $period    = $this->sanitize_richtext( $s['period'] );
        $cta_text  = $this->sanitize_richtext( $s['cta_text'] );
        $currency  = esc_html( $s['currency'] );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                position: relative; overflow: hidden; text-align: center;
                border-radius: <?php echo $tile_r; ?>px;
                padding: 32px 24px;
                color: <?php echo $fg; ?>;
                <?php if ( $bg_type === 'color' ) : ?>
                background: <?php echo $bg; ?>;
                <?php else : ?>
                background: #111827;
                <?php endif; ?>
                <?php if ( $tile_bw > 0 ) : ?>
                border: <?php echo $tile_bw; ?>px solid <?php echo $tile_bc; ?>;
                <?php endif; ?>
            }
            <?php if ( $bg_type === 'image' && ! empty( $s['bg_image'] ) ) : ?>
            .<?php echo $uid; ?> .olo-price-bg {
                position: absolute; inset: 0;
                background: url('<?php echo esc_url( $s['bg_image'] ); ?>') center/cover no-repeat;
            }
            <?php endif; ?>
            <?php if ( $bg_type === 'video' && ! empty( $s['bg_video'] ) ) : ?>
            .<?php echo $uid; ?> .olo-price-video {
                position: absolute; inset: 0; width: 100%; height: 100%;
                object-fit: cover; z-index: 0;
            }
            <?php endif; ?>
            <?php if ( filter_var( $s['overlay'], FILTER_VALIDATE_BOOLEAN ) && $bg_type !== 'color' ) : ?>
            .<?php echo $uid; ?> .olo-price-overlay {
                position: absolute; inset: 0; z-index: 1;
                background: <?php echo $this->safe_color( $s['overlay_color'] ) ?: '#000'; ?>;
                opacity: <?php echo ( intval( $s['overlay_opacity'] ) ?: 50 ) / 100; ?>;
            }
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-price-inner { position: relative; z-index: 2; }

            /* Badge */
            <?php if ( $popular ) : ?>
            .<?php echo $uid; ?> .olo-price-badge {
                position: absolute; top: <?php echo $badge_top; ?>px; left: 50%; transform: translateX(-50%);
                font-size: 0.75em; font-weight: 600; text-transform: uppercase; white-space: nowrap; z-index: 5;
                color: <?php echo $badge_fg; ?>;
                border-radius: <?php echo $badge_r; ?>px;
                <?php if ( $badge_st === 'minimal' ) : ?>
                background: transparent; color: <?php echo $accent; ?>;
                border-bottom: 2px solid <?php echo $accent; ?>; padding: 2px 12px;
                <?php elseif ( $badge_st === 'classic' ) : ?>
                background: <?php echo $badge_bg; ?>; padding: 4px 16px;
                <?php else : ?>
                background: <?php echo $badge_bg; ?>; padding: 4px 20px;
                <?php endif; ?>
            }
            <?php endif; ?>

            /* Price shape */
            <?php if ( $shape !== 'none' ) : ?>
            .<?php echo $uid; ?> .olo-price-shape {
                display: inline-flex; align-items: center; justify-content: center;
                background: <?php echo $shape_col; ?>;
                <?php if ( $shape === 'circle' ) : ?>
                border-radius: 50%; width: 130px; height: 130px;
                <?php else : ?>
                border-radius: 16px; padding: 16px 24px;
                <?php endif; ?>
                <?php if ( $shape_bw > 0 ) : ?>
                border: <?php echo $shape_bw; ?>px solid <?php echo $shape_bc; ?>;
                <?php endif; ?>
                <?php if ( $shape_glow ) : ?>
                box-shadow: inset 0 0 <?php echo $shape_gi; ?>px <?php echo $shape_gc; ?>66, inset 0 0 <?php echo $shape_gi * 2; ?>px <?php echo $shape_gc; ?>33;
                <?php endif; ?>
            }
            <?php endif; ?>

            /* Features */
            .<?php echo $uid; ?> .olo-price-features { list-style: none; padding: 0; margin: 0 0 24px; text-align: left; }
            .<?php echo $uid; ?> .olo-price-features li {
                padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,.1); font-size: 0.9em;
            }
            .<?php echo $uid; ?> .olo-price-check { margin-right: 8px; color: <?php echo $accent; ?>; }

            /* CTA */
            .<?php echo $uid; ?> .olo-price-cta {
                display: block; width: <?php echo $cta_w; ?>%; margin: 0 auto;
                padding: 12px 24px; text-decoration: none !important;
                font-weight: 600; font-size: 1em; text-align: center;
                background: <?php echo $cta_bg; ?>; color: <?php echo $cta_fg; ?> !important;
                border-radius: <?php echo $cta_r; ?>px;
                <?php if ( $cta_bw > 0 ) : ?>
                border: <?php echo $cta_bw; ?>px solid <?php echo $cta_bc; ?>;
                <?php else : ?>
                border: none;
                <?php endif; ?>
                transition: all .3s ease;
                <?php if ( $hover === 'shine' ) : ?>
                position: relative; overflow: hidden;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-price-cta:hover {
                text-decoration: none !important;
                <?php if ( $h_bg ) : ?>background: <?php echo $h_bg; ?>;<?php endif; ?>
                <?php if ( $h_fg ) : ?>color: <?php echo $h_fg; ?> !important;<?php endif; ?>
                <?php
                switch ( $hover ) {
                    case 'lift':
                        echo 'transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,.3);';
                        break;
                    case 'grow':
                        echo 'transform: scale(1.05);';
                        break;
                    case 'glow':
                        echo "box-shadow: 0 0 20px {$cta_bg}80, 0 0 40px {$cta_bg}40;";
                        break;
                    case 'pulse':
                        echo "animation: olo-pulse-{$uid} .6s ease;";
                        break;
                }
                ?>
            }
            <?php if ( $hover === 'pulse' ) : ?>
            @keyframes olo-pulse-<?php echo $uid; ?> {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.08); }
            }
            <?php endif; ?>
            <?php if ( $hover === 'shine' ) : ?>
            .<?php echo $uid; ?> .olo-price-cta::after {
                content: ''; position: absolute; top: -50%; left: -60%; width: 40%; height: 200%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,.25), transparent);
                transform: skewX(-20deg); transition: left .5s ease;
            }
            .<?php echo $uid; ?> .olo-price-cta:hover::after { left: 120%; }
            <?php endif; ?>
        </style>
        <div class="olo-pricing <?php echo esc_attr( $uid ); ?>">
            <?php if ( $bg_type === 'image' && ! empty( $s['bg_image'] ) ) : ?>
                <div class="olo-price-bg"></div>
            <?php endif; ?>
            <?php if ( $bg_type === 'video' && ! empty( $s['bg_video'] ) ) : ?>
                <video class="olo-price-video" src="<?php echo esc_url( $s['bg_video'] ); ?>" autoplay muted loop playsinline></video>
            <?php endif; ?>
            <?php if ( filter_var( $s['overlay'], FILTER_VALIDATE_BOOLEAN ) && $bg_type !== 'color' ) : ?>
                <div class="olo-price-overlay"></div>
            <?php endif; ?>

            <div class="olo-price-inner">
                <?php if ( $popular ) : ?>
                    <div class="olo-price-badge"><?php echo esc_html( $s['badge_text'] ?: 'Popolare' ); ?></div>
                <?php endif; ?>

                <h3 style="font-size:1.25em;font-weight:600;margin:8px 0 16px;color:<?php echo $fg; ?>"><?php echo $plan_name; ?></h3>

                <div style="margin-bottom:24px">
                    <?php if ( $shape !== 'none' ) : ?>
                        <div class="olo-price-shape">
                            <span style="font-size:<?php echo $cur_size; ?>px;opacity:.8;margin-right:2px"><?php echo $currency; ?></span>
                            <span style="font-size:3em;font-weight:700;line-height:1"><?php echo esc_html( $s['price'] ); ?></span>
                        </div>
                        <div style="font-size:.875em;opacity:.7;margin-top:8px"><?php echo $period; ?></div>
                    <?php else : ?>
                        <span style="font-size:<?php echo $cur_size; ?>px;opacity:.8;margin-right:2px"><?php echo $currency; ?></span>
                        <span style="font-size:3em;font-weight:700;line-height:1"><?php echo esc_html( $s['price'] ); ?></span>
                        <span style="font-size:.875em;opacity:.7"><?php echo $period; ?></span>
                    <?php endif; ?>
                </div>

                <?php if ( ! empty( $features ) ) : ?>
                    <ul class="olo-price-features">
                        <?php foreach ( $features as $f ) : ?>
                            <li><?php if ( $check ) : ?><span class="olo-price-check"><?php echo $check; ?></span><?php endif; ?><?php echo esc_html( $f ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <a href="<?php echo esc_url( $s['cta_url'] ); ?>" class="olo-price-cta"
                   <?php if ( $s['cta_target'] === '_blank' ) echo 'target="_blank" rel="noopener"'; ?>>
                    <?php echo $cta_text; ?>
                </a>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
