<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Olo_Pricing_Tile extends Olo_Tile_Base {

    protected $type     = 'pricing';
    protected $name     = 'Listino prezzi';
    protected $icon     = 'dashicons-money-alt';
    protected $category = 'marketing';
    protected $defaults = [
        'preset' => 'custom',
        'plan_name'    => 'Piano Pro',
        'price'        => '29',
        'currency'     => '€',
        'currency_size' => '14',
        'period'       => '/mese',
        'features'     => "Progetti illimitati\n10 GB di spazio\nSupporto prioritario\nDominio personalizzato",
        'sale_price'        => '',
        'sale_badge_text'   => 'OFFERTA',
        'sale_badge_color'  => '',
        'currency_position' => 'before',
        'feature_dividers'  => true,
        'is_popular'   => false,
        'badge_text'   => 'Popolare',
        'badge_style'  => 'pill',
        'badge_top'    => '-12',
        'badge_radius' => '20',
        'badge_bg_color'   => '',
        'badge_text_color' => '#FFFFFF',
        'price_shape'              => 'none',
        'price_shape_color'        => '',
        'price_shape_glow'         => false,
        'price_shape_glow_color'   => '',
        'price_shape_glow_intensity' => '15',
        'price_shape_border_width' => '0',
        'price_shape_border_color' => '',
        'check_style'  => 'checkmark',
        'check_size'   => '14',
        'price_color'  => '',
        'bg_color'     => '',
        'accent_color' => '',
        'text_color'   => '',
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
        'additional_info'       => '',
        'enable_toggle'         => false,
        'toggle_label_1'        => 'Mensile',
        'toggle_label_2'        => 'Annuale',
        'toggle_color'          => '',
        'price_yearly'          => '',
        'border_radius'           => '12',
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
        $uid = 'olo-price-' . wp_rand( 10000, 99999 );

        $price_clr = $this->safe_color_css( $s['price_color'] ) ?: '';
        $check_size = intval( $s['check_size'] ) ?: 14;
        $accent    = $this->safe_color_css( $s['accent_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $bg        = $this->safe_color_css( $s['bg_color'] ) ?: 'var(--olo-color-secondary, #1F2937)';
        $fg        = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-muted, #F3F4F6)';
        $feat_raw  = is_array( $s['features'] ) ? implode( "\n", $s['features'] ) : (string) $s['features'];
        $features  = array_filter( array_map( 'trim', explode( "\n", $feat_raw ) ) );
        $popular   = filter_var( $s['is_popular'], FILTER_VALIDATE_BOOLEAN );
        $tile_r    = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $tile_r_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
        $tile_bw   = intval( $s['border_width'] );
        $tile_bc   = $this->safe_color_css( $s['border_color'] ) ?: 'var(--olo-color-text, #374151)';

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
        $cta_bg  = $this->safe_color_css( $s['cta_bg_color'] ) ?: $accent;
        $cta_fg  = $this->safe_color_css( $s['cta_text_color'] ) ?: '#FFFFFF';
        $cta_r   = Olo_Tile_Utils::border_radius( $s['cta_radius'] ?? 0 );
        $cta_r_hover_css = Olo_Tile_Utils::radius_force_css( $s['cta_radius_hover'] ?? null );
        $cta_bw  = intval( $s['cta_border_width'] );
        $cta_bc  = $this->safe_color_css( $s['cta_border_color'] ) ?: '#FFFFFF';
        $hover   = $s['cta_hover_effect'] ?: 'none';
        $cta_w   = intval( $s['cta_width'] ) ?: 100;
        $h_bg    = $this->safe_color_css( $s['cta_hover_bg_color'] ) ?: '';
        $h_fg    = $this->safe_color_css( $s['cta_hover_text_color'] ) ?: '';

        // Background
        $bg_type = $s['bg_type'] ?: 'color';

        // Price shape
        $shape      = $s['price_shape'] ?: 'none';
        $shape_col  = $this->safe_color_css( $s['price_shape_color'] ) ?: 'var(--olo-color-text, #374151)';
        $shape_glow = filter_var( $s['price_shape_glow'], FILTER_VALIDATE_BOOLEAN );
        $shape_gc   = $this->safe_color_css( $s['price_shape_glow_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $shape_gi   = intval( $s['price_shape_glow_intensity'] ) ?: 15;
        $shape_bw   = intval( $s['price_shape_border_width'] );
        $shape_bc   = $this->safe_color_css( $s['price_shape_border_color'] ) ?: 'var(--olo-color-primary, #e1474f)';

        // Badge
        $badge_bg  = $this->safe_color_css( $s['badge_bg_color'] ) ?: $accent;
        $badge_fg  = $this->safe_color_css( $s['badge_text_color'] ) ?: '#FFFFFF';
        $badge_r   = Olo_Tile_Utils::border_radius( $s['badge_radius'] ?? 0 );
        $badge_r_hover_css = Olo_Tile_Utils::radius_force_css( $s['badge_radius_hover'] ?? null );
        $badge_st  = $s['badge_style'] ?: 'pill';
        $badge_top = intval( $s['badge_top'] );

        // Currency
        $cur_size = intval( $s['currency_size'] ) ?: 14;
        $cur_pos  = $s['currency_position'] ?: 'before';

        // Sale price
        $sale_price      = trim( $s['sale_price'] );
        $has_sale         = ! empty( $sale_price );
        $sale_badge_text  = esc_html( $s['sale_badge_text'] ?: 'OFFERTA' );
        $sale_badge_color = $this->safe_color_css( $s['sale_badge_color'] ) ?: 'var(--olo-color-danger, #EF4444)';

        // Feature dividers
        $feat_dividers = filter_var( $s['feature_dividers'], FILTER_VALIDATE_BOOLEAN );

        // Additional info
        $additional_info = esc_html( wp_strip_all_tags( $s['additional_info'] ) );

        // Sanitize
        $plan_name = esc_html( wp_strip_all_tags( $s['plan_name'] ) );
        $period    = esc_html( wp_strip_all_tags( $s['period'] ) );
        $cta_text  = esc_html( wp_strip_all_tags( $s['cta_text'] ) );
        $currency  = esc_html( $s['currency'] );

        // Toggle mensile/annuale
        $enable_toggle = filter_var( $s['enable_toggle'], FILTER_VALIDATE_BOOLEAN );
        $toggle_label1 = esc_html( $s['toggle_label_1'] ?: 'Mensile' );
        $toggle_label2 = esc_html( $s['toggle_label_2'] ?: 'Annuale' );
        $toggle_color  = $this->safe_color_css( $s['toggle_color'] ) ?: $accent;
        $price_yearly  = esc_html( trim( $s['price_yearly'] ) );
        $price_monthly = esc_html( $s['price'] );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                position: relative; overflow: hidden; text-align: center;
                border-radius: <?php echo $tile_r; ?>;
                padding: 32px 24px;
                color: <?php echo $fg; ?>;
                <?php if ( $bg_type === 'color' ) : ?>
                background: <?php echo $bg; ?>;
                <?php else : ?>
                background: #1F2937;
                <?php endif; ?>
                <?php if ( $tile_bw > 0 ) : ?>
                border: <?php echo $tile_bw; ?>px solid <?php echo $tile_bc; ?>;
                <?php endif; ?>
            }
            <?php if ( $tile_r_hover_css !== '' ) : ?>.<?php echo $uid; ?>{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?>:hover{border-radius:<?php echo $tile_r_hover_css; ?> !important}<?php endif; ?>
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
                background: <?php echo $this->safe_color_css( $s['overlay_color'] ) ?: '#000'; ?>;
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
                border-radius: <?php echo $badge_r; ?>;
                <?php if ( $badge_st === 'minimal' ) : ?>
                background: transparent; color: <?php echo $accent; ?>;
                border-bottom: 2px solid <?php echo $accent; ?>; padding: 2px 12px;
                <?php elseif ( $badge_st === 'classic' ) : ?>
                background: <?php echo $badge_bg; ?>; padding: 4px 16px;
                <?php else : ?>
                background: <?php echo $badge_bg; ?>; padding: 4px 20px;
                <?php endif; ?>
            }
            <?php if ( $badge_r_hover_css !== '' ) : ?>.<?php echo $uid; ?> .olo-price-badge{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?> .olo-price-badge:hover{border-radius:<?php echo $badge_r_hover_css; ?> !important}<?php endif; ?>
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
                box-shadow: inset 0 0 <?php echo $shape_gi; ?>px <?php echo 'color-mix(in srgb, ' . $shape_gc . ' 40%, transparent)'; ?>, inset 0 0 <?php echo $shape_gi * 2; ?>px <?php echo 'color-mix(in srgb, ' . $shape_gc . ' 20%, transparent)'; ?>;
                <?php endif; ?>
            }
            <?php endif; ?>

            /* Features */
            .<?php echo $uid; ?> .olo-price-features { list-style: none; padding: 0; margin: 0 0 24px; text-align: left; }
            .<?php echo $uid; ?> .olo-price-features li {
                padding: 8px 0; font-size: 0.9em;
                <?php if ( $feat_dividers ) : ?>
                border-bottom: 1px solid rgba(255,255,255,.1);
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-price-check { margin-right: 8px; color: <?php echo $accent; ?>; font-size: <?php echo $check_size; ?>px; }

            /* Sale badge */
            <?php if ( $has_sale ) : ?>
            .<?php echo $uid; ?> .olo-price-sale-badge {
                position: absolute; top: 12px; right: 12px; z-index: 10;
                padding: 2px 8px; border-radius: 4px;
                font-size: 9px; font-weight: 700; color: var(--olo-color-primary-contrast, #FFFFFF);
                background: <?php echo $sale_badge_color; ?>;
            }
            .<?php echo $uid; ?> .olo-price-original {
                text-decoration: line-through; opacity: 0.5;
                font-size: 0.7em; margin-right: 8px;
            }
            <?php endif; ?>

            /* Additional info */
            .<?php echo $uid; ?> .olo-price-addinfo {
                margin-top: 8px; font-size: 10px; color: var(--olo-color-text-muted, #9CA3AF); text-align: center;
            }

            /* CTA */
            .<?php echo $uid; ?> .olo-price-cta {
                display: block; box-sizing: border-box; width: <?php echo $cta_w; ?>%; margin: 0 auto;
                padding: 12px 24px; text-decoration: none !important;
                font-weight: 600; font-size: 1em; text-align: center;
                background: <?php echo $cta_bg; ?>; color: <?php echo $cta_fg; ?> !important;
                border-radius: <?php echo $cta_r; ?>;
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
            <?php if ( $cta_r_hover_css !== '' ) : ?>.<?php echo $uid; ?> .olo-price-cta{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?> .olo-price-cta:hover{border-radius:<?php echo $cta_r_hover_css; ?> !important}<?php endif; ?>
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
            <?php if ( $enable_toggle ) : ?>
            /* Toggle switch */
            .<?php echo $uid; ?> .olo-price-toggle-wrap {
                display: flex; align-items: center; justify-content: center;
                gap: 12px; margin-bottom: 20px; position: relative; z-index: 3;
            }
            .<?php echo $uid; ?> .olo-price-toggle-label {
                font-size: 0.875em; font-weight: 500; opacity: 0.6; transition: opacity .3s ease; cursor: pointer;
            }
            .<?php echo $uid; ?> .olo-price-toggle-label.olo-active { opacity: 1; font-weight: 600; }
            .<?php echo $uid; ?> .olo-price-toggle {
                position: relative; width: 48px; height: 26px; cursor: pointer;
                background: rgba(255,255,255,0.15); border-radius: 13px; border: none;
                transition: background .3s ease; padding: 0;
            }
            .<?php echo $uid; ?> .olo-price-toggle::after {
                content: ''; position: absolute; top: 3px; left: 3px;
                width: 20px; height: 20px; border-radius: 50%;
                background: <?php echo $toggle_color; ?>; transition: transform .3s ease;
            }
            .<?php echo $uid; ?>.olo-pricing-yearly .olo-price-toggle::after {
                transform: translateX(22px);
            }
            .<?php echo $uid; ?> .olo-pricing-amount {
                transition: opacity .3s ease;
                <?php if ( $price_clr ) : ?>color: <?php echo $price_clr; ?>;<?php endif; ?>
            }
            <?php endif; ?>
        </style>
        <div class="olo-pricing <?php echo esc_attr( $uid ); ?> olo-pricing-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>">
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
                <?php if ( $has_sale ) : ?>
                    <div class="olo-price-sale-badge"><?php echo $sale_badge_text; ?></div>
                <?php endif; ?>
                <?php if ( $popular ) : ?>
                    <div class="olo-price-badge"><?php echo esc_html( $s['badge_text'] ?: 'Popolare' ); ?></div>
                <?php endif; ?>

                <?php if ( $enable_toggle ) : ?>
                <div class="olo-price-toggle-wrap">
                    <span class="olo-price-toggle-label olo-active" data-olo-toggle-l1><?php echo $toggle_label1; ?></span>
                    <button type="button" class="olo-price-toggle" data-olo-price-toggle aria-label="<?php echo esc_attr( $toggle_label1 . ' / ' . $toggle_label2 ); ?>"></button>
                    <span class="olo-price-toggle-label" data-olo-toggle-l2><?php echo $toggle_label2; ?></span>
                </div>
                <?php endif; ?>

                <h3 style="font-size:1.25em;font-weight:600;margin:<?php echo $popular ? '24px' : '8px'; ?> 0 16px;color:<?php echo $fg; ?>"><?php echo $plan_name; ?></h3>

                <div style="margin-bottom:24px">
                    <?php
                    // Build price display with currency position
                    $display_price = $has_sale ? esc_html( $sale_price ) : esc_html( $s['price'] );
                    $original_price_html = '';
                    if ( $has_sale ) {
                        if ( $cur_pos === 'after' ) {
                            $original_price_html = '<span class="olo-price-original">' . esc_html( $s['price'] ) . $currency . '</span>';
                        } else {
                            $original_price_html = '<span class="olo-price-original">' . $currency . esc_html( $s['price'] ) . '</span>';
                        }
                    }

                    $toggle_attrs = '';
                    if ( $enable_toggle ) {
                        $toggle_attrs = ' data-monthly="' . esc_attr( $price_monthly ) . '" data-yearly="' . esc_attr( $price_yearly ) . '"';
                    }
                    ?>
                    <?php if ( $shape !== 'none' ) : ?>
                        <div class="olo-price-shape">
                            <?php echo $original_price_html; ?>
                            <?php if ( $cur_pos === 'before' ) : ?>
                                <span style="font-size:<?php echo $cur_size; ?>px;opacity:.8;margin-right:2px"><?php echo $currency; ?></span>
                            <?php endif; ?>
                            <span class="olo-pricing-amount" style="font-size:3em;font-weight:700;line-height:1"<?php echo $toggle_attrs; ?>><?php echo $display_price; ?></span>
                            <?php if ( $cur_pos === 'after' ) : ?>
                                <span style="font-size:<?php echo $cur_size; ?>px;opacity:.8;margin-left:2px"><?php echo $currency; ?></span>
                            <?php endif; ?>
                        </div>
                        <div style="font-size:.875em;opacity:.7;margin-top:8px"><?php echo $period; ?></div>
                    <?php else : ?>
                        <?php echo $original_price_html; ?>
                        <?php if ( $cur_pos === 'before' ) : ?>
                            <span style="font-size:<?php echo $cur_size; ?>px;opacity:.8;margin-right:2px"><?php echo $currency; ?></span>
                        <?php endif; ?>
                        <span class="olo-pricing-amount" style="font-size:3em;font-weight:700;line-height:1"<?php echo $toggle_attrs; ?>><?php echo $display_price; ?></span>
                        <?php if ( $cur_pos === 'after' ) : ?>
                            <span style="font-size:<?php echo $cur_size; ?>px;opacity:.8;margin-left:2px"><?php echo $currency; ?></span>
                        <?php endif; ?>
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
                <?php if ( ! empty( $additional_info ) ) : ?>
                    <div class="olo-price-addinfo"><?php echo $additional_info; ?></div>
                <?php endif; ?>

                <?php
                // === Countdown urgency (inside card) ===
                $cd_enabled = ! empty( $s['countdown_enabled'] );
                $cd_date    = sanitize_text_field( $s['countdown_date'] ?? '' );
                if ( $cd_enabled && $cd_date !== '' ) :
                    $cd_label        = esc_html( $s['countdown_label'] ?? 'Offerta scade tra:' );
                    $cd_expired_text = esc_html( $s['countdown_expired_text'] ?? 'Offerta scaduta' );
                    $cd_hide         = ! empty( $s['countdown_hide_on_expire'] );
                    $cd_bg           = sanitize_hex_color( $s['countdown_bg_color'] ?? '' );
                    $cd_color        = sanitize_hex_color( $s['countdown_text_color'] ?? '' );
                    $cd_style        = '';
                    if ( $cd_bg )    $cd_style .= 'background:' . $cd_bg . ';';
                    if ( $cd_color ) $cd_style .= 'color:' . $cd_color . ';';
                ?>
                <div class="olo-pricing-countdown <?php echo $uid; ?>-cd" style="text-align:center;padding:10px 12px;font-size:13px;margin-top:10px;border-radius:6px;<?php echo $cd_style; ?>">
                    <div class="olo-cd-label" style="font-size:11px;opacity:0.8;margin-bottom:4px"><?php echo $cd_label; ?></div>
                    <div class="olo-cd-timer" style="font-weight:700;font-variant-numeric:tabular-nums;font-size:16px" data-olo-countdown="<?php echo esc_attr( $cd_date ); ?>" data-expired="<?php echo $cd_expired_text; ?>" data-hide="<?php echo $cd_hide ? '1' : '0'; ?>">
                        --g --h --m --s
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php if ( $enable_toggle ) : ?>
        <script>
        (function(){
            var wrap = document.querySelector('.<?php echo $uid; ?>');
            if(!wrap) return;
            var btn = wrap.querySelector('[data-olo-price-toggle]');
            if(!btn) return;
            var l1 = wrap.querySelector('[data-olo-toggle-l1]');
            var l2 = wrap.querySelector('[data-olo-toggle-l2]');
            var amounts = wrap.querySelectorAll('.olo-pricing-amount');
            btn.addEventListener('click', function(){
                var isYearly = wrap.classList.contains('olo-pricing-yearly');
                if(isYearly){
                    wrap.classList.remove('olo-pricing-yearly');
                    if(l1){ l1.classList.add('olo-active'); }
                    if(l2){ l2.classList.remove('olo-active'); }
                    for(var i=0;i<amounts.length;i++){
                        var el = amounts[i];
                        if(el.dataset.monthly){
                            el.style.opacity = '0';
                            setTimeout(function(e){ e.textContent = e.dataset.monthly; e.style.opacity = '1'; }, 150, el);
                        }
                    }
                } else {
                    wrap.classList.add('olo-pricing-yearly');
                    if(l1){ l1.classList.remove('olo-active'); }
                    if(l2){ l2.classList.add('olo-active'); }
                    for(var j=0;j<amounts.length;j++){
                        var el2 = amounts[j];
                        if(el2.dataset.yearly){
                            el2.style.opacity = '0';
                            setTimeout(function(e){ e.textContent = e.dataset.yearly; e.style.opacity = '1'; }, 150, el2);
                        }
                    }
                }
            });
        })();
        </script>
        <?php endif; ?>
        <?php if ( $cd_enabled && $cd_date !== '' ) : ?>
        <script>
        (function(){
            var el = document.querySelector('.<?php echo $uid; ?>-cd [data-olo-countdown]');
            if(!el) return;
            var target = new Date(el.getAttribute('data-olo-countdown')).getTime();
            var expired = el.getAttribute('data-expired');
            var hideCard = el.getAttribute('data-hide') === '1';
            function tick(){
                var now = Date.now();
                var diff = target - now;
                if(diff <= 0){
                    el.textContent = expired;
                    if(hideCard){
                        var card = el.closest('.olo-pricing');
                        if(card){ card.style.display = 'none'; }
                    }
                    return;
                }
                var d = Math.floor(diff / 86400000);
                var h = Math.floor((diff % 86400000) / 3600000);
                var m = Math.floor((diff % 3600000) / 60000);
                var s = Math.floor((diff % 60000) / 1000);
                var parts = [];
                if(d > 0) parts.push(d + 'g');
                parts.push(String(h).padStart(2,'0') + 'h');
                parts.push(String(m).padStart(2,'0') + 'm');
                parts.push(String(s).padStart(2,'0') + 's');
                el.textContent = parts.join(' ');
                requestAnimationFrame(function(){ setTimeout(tick, 1000); });
            }
            tick();
        })();
        </script>
        <?php endif; ?>
        <?php
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
