<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Newsticker_Tile extends Olo_Tile_Base {

    protected $type     = 'newsticker';
    protected $name     = 'News Ticker';
    protected $icon     = 'dashicons-megaphone';
    protected $category = 'dynamic';
    protected $defaults = [
        'items'          => [
            [ 'title' => 'Nuova funzionalità disponibile per tutti gli utenti', 'url' => '', 'badge' => 'Novità', 'icon' => 'star', 'badge_bg' => '', 'timestamp' => '' ],
            [ 'title' => 'Manutenzione programmata venerdì 21:00 - 23:00', 'url' => '', 'badge' => 'Avviso', 'icon' => 'warning', 'badge_bg' => '', 'timestamp' => '' ],
            [ 'title' => 'Aggiornamento versione 2.0 rilasciato con successo', 'url' => '', 'badge' => '', 'icon' => 'bolt', 'badge_bg' => '', 'timestamp' => '' ],
        ],
        'direction'         => 'horizontal',
        'show_label'        => true,
        'label_text'        => 'Breaking',
        'label_icon'        => '',
        'label_shape'       => 'arrow',
        'label_position'    => 'left',
        'label_bg'          => '#dc2626',
        'label_color'       => '#ffffff',
        'badge_bg'          => '',
        'badge_color'       => '',
        'bg_color'          => '',
        'bg_gradient'       => false,
        'bg_color2'         => '',
        'bg_angle'          => 90,
        'text_color'        => '#f3f4f6',
        'height'            => '42',
        'separator'         => '|',
        'font_size'         => 14,
        'font_weight'       => '400',
        'text_transform'    => 'none',
        'letter_spacing'    => 0,
        'tile_padding'      => [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 ],
        'animation_type'    => 'slide-up',
        'transition_duration' => 400,
        'speed'             => '3000',
        'auto_scroll'       => true,
        'pause_on_hover'    => true,
        'loop'              => true,
        'random_order'      => false,
        'stop_on_click'     => false,
        'marquee_direction' => 'left',
        'marquee_gap'       => 60,
        'marquee_duration'  => 25,
        'show_controls'     => false,
        'show_indicators'   => false,
        'show_counter'      => false,
        'logo_height'       => 32,
        'logo_grayscale'    => false,
        'logo_opacity'      => 100,
        'item_hover_effect' => 'none',
        'border'                       => [],
        'border_hover'                 => [],
        'border_hover_duration'        => 300,
        'border_radius'                => [],
        'border_radius_hover'          => null,
        'border_radius_hover_duration' => 300,
        'border_effect'                => 'none',
        'border_effect_intensity'      => 'medium',
        'border_effect_color2'         => '',
        'border_effect_angle'          => 135,
        'border_effect_speed'          => 4,
    ];

    public function get_controls() {
        return [];
    }

    /**
     * Render icona: uk-icon se nome (es. 'star'), altrimenti emoji testuale.
     */
    private function render_icon( $val, $ratio = 1 ) {
        $val = trim( (string) $val );
        if ( $val === '' ) return '';
        if ( preg_match( '/^[a-z][a-z0-9-]*$/', $val ) ) {
            return '<span uk-icon="icon: ' . esc_attr( $val ) . '; ratio: ' . esc_attr( $ratio ) . '"></span>';
        }
        return esc_html( $val );
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-newsticker-' . wp_unique_id();

        // Parse items
        $items = is_array( $s['items'] ) ? $s['items'] : [];
        $items = array_filter( $items, function( $item ) {
            return is_array( $item ) && ! empty( $item['title'] );
        });
        $items = array_values( $items );
        $count = count( $items );

        if ( $count === 0 ) {
            return '';
        }

        // Settings principali
        $direction      = in_array( $s['direction'], [ 'horizontal', 'vertical' ], true ) ? $s['direction'] : 'horizontal';
        $animation      = in_array( $s['animation_type'], [ 'slide-up', 'slide-down', 'fade', 'marquee' ], true ) ? $s['animation_type'] : 'slide-up';
        $show_label     = ! empty( $s['show_label'] );
        $label_text     = wp_kses_post( $s['label_text'] );
        $label_icon     = sanitize_text_field( $s['label_icon'] );
        $label_shape    = in_array( $s['label_shape'], [ 'arrow', 'pill', 'square', 'tag' ], true ) ? $s['label_shape'] : 'arrow';
        $label_position = $s['label_position'] === 'right' ? 'right' : 'left';
        $label_bg       = $this->safe_color_css( $s['label_bg'] ) ?: 'var(--olo-color-danger, #EF4444)';
        $label_clr      = $this->safe_color_css( $s['label_color'] ) ?: 'var(--olo-color-primary-contrast, #FFFFFF)';
        $badge_bg       = $this->safe_color_css( $s['badge_bg'] ) ?: 'rgba(255,255,255,0.15)';
        $badge_clr      = $this->safe_color_css( $s['badge_color'] ) ?: 'inherit';
        $text_clr       = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-muted, #F3F4F6)';

        // Background (gradient o solid)
        $bg_solid = $this->safe_color_css( $s['bg_color'] ) ?: 'var(--olo-color-secondary, #1F2937)';
        if ( ! empty( $s['bg_gradient'] ) ) {
            $bg_c2  = $this->safe_color_css( $s['bg_color2'] ) ?: $bg_solid;
            $angle  = max( 0, min( 360, intval( $s['bg_angle'] ) ) );
            $bg_css = "linear-gradient({$angle}deg, {$bg_solid} 0%, {$bg_c2} 100%)";
        } else {
            $bg_css = $bg_solid;
        }

        $speed       = max( 1500, intval( $s['speed'] ) );
        $trans_dur   = max( 100, min( 1500, intval( $s['transition_duration'] ) ) );
        $height      = max( 30, min( 120, intval( $s['height'] ) ) );
        $auto        = ! empty( $s['auto_scroll'] );
        $pause       = ! empty( $s['pause_on_hover'] );
        $loop        = ! empty( $s['loop'] );
        $random      = ! empty( $s['random_order'] );
        $stop_click  = ! empty( $s['stop_on_click'] );

        // Tipografia
        $font_size      = max( 10, min( 24, intval( $s['font_size'] ) ) );
        $font_weight    = in_array( $s['font_weight'], [ '300', '400', '500', '600', '700' ], true ) ? $s['font_weight'] : '400';
        $text_transform = in_array( $s['text_transform'], [ 'none', 'uppercase', 'lowercase', 'capitalize' ], true ) ? $s['text_transform'] : 'none';
        $letter_spacing = floatval( $s['letter_spacing'] );

        // Padding container
        $tp = $s['tile_padding'] ?? [];
        $pt = is_array( $tp ) ? absint( $tp['top']    ?? 0 ) : 0;
        $pr = is_array( $tp ) ? absint( $tp['right']  ?? 0 ) : 0;
        $pb = is_array( $tp ) ? absint( $tp['bottom'] ?? 0 ) : 0;
        $pl = is_array( $tp ) ? absint( $tp['left']   ?? 0 ) : 0;

        // Marquee
        $mq_dir      = $s['marquee_direction'] === 'right' ? 'right' : 'left';
        $mq_gap      = max( 0, min( 200, intval( $s['marquee_gap'] ) ) );
        $mq_duration = max( 5, min( 120, intval( $s['marquee_duration'] ) ) );

        // Controlli
        $show_controls   = ! empty( $s['show_controls'] );
        $show_indicators = ! empty( $s['show_indicators'] );
        $show_counter    = ! empty( $s['show_counter'] );
        $hover_effect    = in_array( $s['item_hover_effect'], [ 'none', 'scale', 'brighten', 'underline', 'shift' ], true ) ? $s['item_hover_effect'] : 'none';

        // Loghi (monocromatici per "trusted by")
        $logo_height    = max( 16, min( 60, intval( $s['logo_height'] ) ) );
        $logo_grayscale = ! empty( $s['logo_grayscale'] );
        $logo_opacity   = max( 0, min( 100, intval( $s['logo_opacity'] ) ) ) / 100;

        // Build items
        $items_clean = [];
        foreach ( $items as $item ) {
            $items_clean[] = [
                'title'     => wp_kses_post( $item['title'] ),
                'url'       => ! empty( $item['url'] ) ? esc_url( $item['url'] ) : '',
                'logo'      => ! empty( $item['logo'] ) ? esc_url( $item['logo'] ) : '',
                'badge'     => ! empty( $item['badge'] ) ? esc_html( $item['badge'] ) : '',
                'badge_bg'  => ! empty( $item['badge_bg'] ) ? $this->safe_color_css( $item['badge_bg'] ) : '',
                'icon'      => ! empty( $item['icon'] ) ? sanitize_text_field( $item['icon'] ) : '',
                'timestamp' => ! empty( $item['timestamp'] ) ? esc_html( $item['timestamp'] ) : '',
            ];
        }

        $half_h          = intval( $height / 2 );
        $is_vertical     = ( $direction === 'vertical' );
        $is_marquee      = ( $animation === 'marquee' );

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist for every colour, intval()/absint()/floatval() with min/max clamps for numerics, in_array() whitelists and fixed-literal ternaries for enums, and the internally generated $uid.
        ?>
        <style>
            .<?php echo $uid; ?> {
                display: flex;
                <?php if ( $is_vertical ) : ?>
                flex-direction: column;
                <?php else : ?>
                align-items: center;
                <?php endif; ?>
                background: <?php echo $bg_css; ?>;
                <?php if ( ! $is_vertical ) : ?>
                height: <?php echo $height; ?>px;
                <?php else : ?>
                min-height: <?php echo $height * 2; ?>px;
                <?php endif; ?>
                padding: <?php echo $pt; ?>px <?php echo $pr; ?>px <?php echo $pb; ?>px <?php echo $pl; ?>px;
                overflow: hidden;
                font-family: inherit;
                position: relative;
            }

            <?php // Label ?>
            .<?php echo $uid; ?> .olo-nt-label {
                background: <?php echo $label_bg; ?>;
                color: <?php echo $label_clr; ?>;
                padding: 0 14px;
                <?php if ( ! $is_vertical ) : ?>
                height: 100%;
                <?php else : ?>
                padding: 6px 14px;
                <?php endif; ?>
                display: flex;
                align-items: center;
                gap: 6px;
                font-weight: 700;
                font-size: 12px;
                text-transform: uppercase;
                letter-spacing: 1px;
                white-space: nowrap;
                flex-shrink: 0;
                position: relative;
                z-index: 2;
                <?php if ( $label_shape === 'pill' ) : ?>
                border-radius: 999px;
                margin: 4px;
                padding: 0 18px;
                <?php elseif ( $label_shape === 'square' ) : ?>
                border-radius: 0;
                <?php elseif ( $label_shape === 'tag' ) : ?>
                border-radius: 4px 0 0 4px;
                <?php endif; ?>
                <?php if ( $label_position === 'right' ) : ?>
                order: 2;
                <?php endif; ?>
            }

            <?php // Freccia (solo per shape arrow + horizontal) ?>
            <?php if ( $label_shape === 'arrow' && ! $is_vertical ) : ?>
            .<?php echo $uid; ?> .olo-nt-label::after {
                content: '';
                position: absolute;
                <?php if ( $label_position === 'right' ) : ?>
                left: -8px;
                <?php else : ?>
                right: -8px;
                <?php endif; ?>
                top: 0;
                width: 0;
                height: 0;
                border-style: solid;
                <?php if ( $label_position === 'right' ) : ?>
                border-width: <?php echo $half_h; ?>px 8px <?php echo $half_h; ?>px 0;
                border-color: transparent <?php echo $label_bg; ?> transparent transparent;
                <?php else : ?>
                border-width: <?php echo $half_h; ?>px 0 <?php echo $half_h; ?>px 8px;
                border-color: transparent transparent transparent <?php echo $label_bg; ?>;
                <?php endif; ?>
            }
            <?php endif; ?>

            .<?php echo $uid; ?> .olo-nt-label-icon {
                font-size: 14px;
                line-height: 1;
            }

            <?php // Viewport ?>
            .<?php echo $uid; ?> .olo-nt-viewport {
                flex: 1;
                overflow: hidden;
                position: relative;
                <?php if ( ! $is_vertical ) : ?>
                height: 100%;
                margin-<?php echo $label_position === 'right' ? 'right' : 'left'; ?>: <?php echo $show_label ? '8px' : '0'; ?>;
                <?php else : ?>
                width: 100%;
                margin-top: 4px;
                <?php endif; ?>
            }

            <?php if ( $is_marquee ) : ?>
            <?php // ─── MARQUEE ─── ?>
            .<?php echo $uid; ?> .olo-nt-marquee {
                display: flex;
                gap: <?php echo $mq_gap; ?>px;
                <?php if ( $is_vertical ) : ?>
                flex-direction: column;
                animation: olo-nt-mq-v-<?php echo $uid; ?> <?php echo $mq_duration; ?>s linear infinite;
                <?php else : ?>
                animation: olo-nt-mq-h-<?php echo $uid; ?> <?php echo $mq_duration; ?>s linear infinite;
                animation-direction: <?php echo $mq_dir === 'right' ? 'reverse' : 'normal'; ?>;
                <?php endif; ?>
                width: max-content;
                will-change: transform;
            }
            .<?php echo $uid; ?> .olo-nt-marquee-item {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                color: <?php echo $text_clr; ?>;
                font-size: <?php echo $font_size; ?>px;
                font-weight: <?php echo $font_weight; ?>;
                text-transform: <?php echo $text_transform; ?>;
                letter-spacing: <?php echo $letter_spacing; ?>px;
                white-space: nowrap;
                flex-shrink: 0;
            }
            <?php if ( $pause ) : ?>
            .<?php echo $uid; ?>:hover .olo-nt-marquee {
                animation-play-state: paused;
            }
            <?php endif; ?>
            @keyframes olo-nt-mq-h-<?php echo $uid; ?> {
                from { transform: translateX(0); }
                to   { transform: translateX(-50%); }
            }
            @keyframes olo-nt-mq-v-<?php echo $uid; ?> {
                from { transform: translateY(0); }
                to   { transform: translateY(-50%); }
            }
            <?php else : ?>
            <?php // ─── SLIDE / FADE / TYPEWRITER ─── ?>
            .<?php echo $uid; ?> .olo-nt-item {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 0 14px;
                color: <?php echo $text_clr; ?>;
                font-size: <?php echo $font_size; ?>px;
                font-weight: <?php echo $font_weight; ?>;
                text-transform: <?php echo $text_transform; ?>;
                letter-spacing: <?php echo $letter_spacing; ?>px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                opacity: 0;
                pointer-events: none;
                transition: opacity <?php echo $trans_dur; ?>ms ease, transform <?php echo $trans_dur; ?>ms ease;
                <?php if ( $animation === 'slide-up' ) : ?>
                transform: translateY(100%);
                <?php elseif ( $animation === 'slide-down' ) : ?>
                transform: translateY(-100%);
                <?php elseif ( $animation === 'fade' ) : ?>
                transform: none;
                <?php endif; ?>
                <?php if ( $stop_click ) : ?>
                cursor: pointer;
                <?php endif; ?>
            }

            .<?php echo $uid; ?> .olo-nt-item.olo-nt-active {
                opacity: 1;
                pointer-events: auto;
                transform: translateY(0);
            }

            .<?php echo $uid; ?> .olo-nt-item.olo-nt-exit {
                opacity: 0;
                <?php if ( $animation === 'slide-up' ) : ?>
                transform: translateY(-100%);
                <?php elseif ( $animation === 'slide-down' ) : ?>
                transform: translateY(100%);
                <?php endif; ?>
            }
            <?php endif; ?>

            .<?php echo $uid; ?> .olo-nt-item a,
            .<?php echo $uid; ?> .olo-nt-marquee-item a {
                color: inherit;
                text-decoration: none;
            }

            <?php // Hover item effect ?>
            <?php if ( $hover_effect === 'scale' ) : ?>
            .<?php echo $uid; ?> .olo-nt-item:hover { transform: scale(1.03); transition: transform 200ms ease; }
            <?php elseif ( $hover_effect === 'brighten' ) : ?>
            .<?php echo $uid; ?> .olo-nt-item:hover { filter: brightness(1.2); }
            .<?php echo $uid; ?> .olo-nt-marquee-item:hover { filter: brightness(1.2); }
            <?php elseif ( $hover_effect === 'underline' ) : ?>
            .<?php echo $uid; ?> .olo-nt-item:hover .olo-nt-title,
            .<?php echo $uid; ?> .olo-nt-marquee-item:hover .olo-nt-title { text-decoration: underline; }
            <?php elseif ( $hover_effect === 'shift' ) : ?>
            .<?php echo $uid; ?> .olo-nt-item:hover { padding-left: 22px; transition: padding-left 200ms ease; }
            <?php endif; ?>

            <?php // Badge ?>
            .<?php echo $uid; ?> .olo-nt-badge {
                background: <?php echo $badge_bg; ?>;
                color: <?php echo $badge_clr; ?>;
                padding: 2px 8px;
                border-radius: 3px;
                font-size: 11px;
                font-weight: 600;
                flex-shrink: 0;
                text-transform: none;
                letter-spacing: 0;
            }

            <?php // Icon ?>
            .<?php echo $uid; ?> .olo-nt-icon {
                font-size: <?php echo min( $font_size + 2, 18 ); ?>px;
                line-height: 1;
                flex-shrink: 0;
            }

            <?php // Logo (trusted-by) ?>
            .<?php echo $uid; ?> .olo-nt-logo {
                height: <?php echo $logo_height; ?>px;
                width: auto;
                display: block;
                flex-shrink: 0;
                object-fit: contain;
                <?php if ( $logo_grayscale ) : ?>
                filter: grayscale(1);
                opacity: <?php echo $logo_opacity; ?>;
                transition: filter 250ms ease, opacity 250ms ease;
                <?php endif; ?>
            }
            <?php if ( $logo_grayscale ) : ?>
            .<?php echo $uid; ?> .olo-nt-marquee-item:hover .olo-nt-logo,
            .<?php echo $uid; ?> .olo-nt-item:hover .olo-nt-logo {
                filter: grayscale(0);
                opacity: 1;
            }
            <?php endif; ?>

            <?php // Timestamp ?>
            .<?php echo $uid; ?> .olo-nt-time {
                font-size: 11px;
                opacity: 0.65;
                font-weight: 400;
                flex-shrink: 0;
                margin-left: auto;
                padding-left: 12px;
                text-transform: none;
                letter-spacing: 0;
            }

            <?php // Controlli ?>
            .<?php echo $uid; ?> .olo-nt-controls {
                display: flex;
                gap: 4px;
                align-items: center;
                padding: 0 8px;
                flex-shrink: 0;
                z-index: 3;
            }
            .<?php echo $uid; ?> .olo-nt-ctrl {
                width: 24px;
                height: 24px;
                border: none;
                border-radius: 50%;
                background: rgba(255,255,255,0.15);
                color: inherit;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 14px;
                line-height: 1;
                transition: background 150ms ease;
            }
            .<?php echo $uid; ?> .olo-nt-ctrl:hover {
                background: rgba(255,255,255,0.3);
            }
            .<?php echo $uid; ?> .olo-nt-ctrl:focus-visible,
            .<?php echo $uid; ?> .olo-nt-dot:focus-visible {
                outline: none;
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
            }
            .<?php echo $uid; ?> .olo-nt-counter {
                font-size: 11px;
                opacity: 0.7;
                padding: 0 8px;
                flex-shrink: 0;
                font-variant-numeric: tabular-nums;
                color: <?php echo $text_clr; ?>;
            }

            <?php // Indicators ?>
            .<?php echo $uid; ?> .olo-nt-indicators {
                display: flex;
                gap: 6px;
                padding: 0 8px;
                flex-shrink: 0;
                align-items: center;
                z-index: 3;
            }
            .<?php echo $uid; ?> .olo-nt-dot {
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background: rgba(255,255,255,0.3);
                border: none;
                cursor: pointer;
                transition: background 150ms ease, transform 150ms ease;
                padding: 0;
            }
            .<?php echo $uid; ?> .olo-nt-dot.olo-nt-dot-active {
                background: <?php echo $text_clr; ?>;
                transform: scale(1.4);
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <div class="olo-newsticker <?php echo esc_attr( $uid ); ?>" id="<?php echo esc_attr( $uid ); ?>" data-animation="<?php echo esc_attr( $animation ); ?>">
            <?php if ( $show_label ) : ?>
                <div class="olo-nt-label">
                    <?php if ( $label_icon ) : ?><span class="olo-nt-label-icon"><?php echo $this->render_icon( $label_icon, 0.85 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- icon HTML built by render_icon(), which escapes via esc_attr()/esc_html() internally ?></span><?php endif; ?>
                    <?php echo $label_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitized via wp_kses_post() above ?>
                </div>
            <?php endif; ?>

            <div class="olo-nt-viewport"<?php if ( ! $is_marquee ) : ?> role="region" aria-label="<?php esc_attr_e( 'Notizie', 'olobuild' ); ?>" aria-live="polite" aria-atomic="true"<?php endif; ?>>
                <?php if ( $is_marquee ) : ?>
                    <div class="olo-nt-marquee">
                        <?php for ( $repeat = 0; $repeat < 2; $repeat++ ) : ?>
                            <?php foreach ( $items_clean as $item ) :
                                $title_html = $item['title'];
                                if ( ! empty( $item['url'] ) ) {
                                    $title_html = '<a href="' . $item['url'] . '">' . $item['title'] . '</a>';
                                }
                                $bbg = $item['badge_bg'] ?: '';
                            ?>
                                <span class="olo-nt-marquee-item">
                                    <?php if ( ! empty( $item['logo'] ) ) : ?>
                                        <?php if ( ! empty( $item['url'] ) ) : ?>
                                            <a href="<?php echo esc_url( $item['url'] ); ?>"><img class="olo-nt-logo" src="<?php echo esc_url( $item['logo'] ); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( $item['title'] ) ); ?>" loading="lazy" decoding="async"></a>
                                        <?php else : ?>
                                            <img class="olo-nt-logo" src="<?php echo esc_url( $item['logo'] ); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( $item['title'] ) ); ?>" loading="lazy" decoding="async">
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <?php if ( ! empty( $item['icon'] ) ) : ?>
                                            <span class="olo-nt-icon"><?php echo $this->render_icon( $item['icon'], 0.9 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- icon HTML built by render_icon(), which escapes via esc_attr()/esc_html() internally ?></span>
                                        <?php endif; ?>
                                        <?php if ( ! empty( $item['badge'] ) ) : ?>
                                            <span class="olo-nt-badge"<?php echo $bbg ? ' style="background:' . esc_attr( $bbg ) . ';"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- badge esc_html()'d and $bbg safe_color_css()'d when built above, esc_attr()'d here ?>><?php echo $item['badge']; ?></span>
                                        <?php endif; ?>
                                        <?php list( $nt_cls, $nt_data ) = $this->tfx_attrs( $s, 'title', wp_strip_all_tags( $item['title'] ) ); ?>
                                        <span class="olo-nt-title<?php echo $nt_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); $title_html built above from the wp_kses_post()'d title and esc_url()'d link ?>"<?php echo $nt_data; ?>><?php echo $title_html; ?></span>
                                        <?php if ( ! empty( $item['timestamp'] ) ) : ?>
                                            <span class="olo-nt-time"><?php echo $item['timestamp']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_html()'d when built above ?></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </span>
                            <?php endforeach; ?>
                        <?php endfor; ?>
                    </div>
                <?php else : ?>
                    <?php foreach ( $items_clean as $i => $item ) :
                        $active_class = $i === 0 ? 'olo-nt-active' : '';
                        $title_html   = $item['title'];
                        if ( ! empty( $item['url'] ) ) {
                            $title_html = '<a href="' . $item['url'] . '">' . $item['title'] . '</a>';
                        }
                        $bbg = $item['badge_bg'] ?: '';
                    ?>
                    <div class="olo-nt-item <?php echo esc_attr( $active_class ); ?>" data-index="<?php echo (int) $i; ?>" aria-hidden="<?php echo $i === 0 ? 'false' : 'true'; ?>">
                        <?php if ( ! empty( $item['logo'] ) ) : ?>
                            <?php if ( ! empty( $item['url'] ) ) : ?>
                                <a href="<?php echo esc_url( $item['url'] ); ?>"><img class="olo-nt-logo" src="<?php echo esc_url( $item['logo'] ); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( $item['title'] ) ); ?>" loading="lazy" decoding="async"></a>
                            <?php else : ?>
                                <img class="olo-nt-logo" src="<?php echo esc_url( $item['logo'] ); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( $item['title'] ) ); ?>" loading="lazy" decoding="async">
                            <?php endif; ?>
                        <?php else : ?>
                            <?php if ( ! empty( $item['icon'] ) ) : ?>
                                <span class="olo-nt-icon"><?php echo $item['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitize_text_field()'d (tag-stripped) when built above ?></span>
                            <?php endif; ?>
                            <?php if ( ! empty( $item['badge'] ) ) : ?>
                                <span class="olo-nt-badge"<?php echo $bbg ? ' style="background:' . esc_attr( $bbg ) . ';"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- badge esc_html()'d and $bbg safe_color_css()'d when built above, esc_attr()'d here ?>><?php echo $item['badge']; ?></span>
                            <?php endif; ?>
                            <?php list( $nt_cls, $nt_data ) = $this->tfx_attrs( $s, 'title', wp_strip_all_tags( $item['title'] ) ); ?>
                            <span class="olo-nt-title<?php echo $nt_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); $title_html built above from the wp_kses_post()'d title and esc_url()'d link ?>"<?php echo $nt_data; ?>><?php echo $title_html; ?></span>
                            <?php if ( ! empty( $item['timestamp'] ) ) : ?>
                                <span class="olo-nt-time"><?php echo $item['timestamp']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_html()'d when built above ?></span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if ( $show_counter && ! $is_marquee ) : ?>
                <div class="olo-nt-counter"><span class="olo-nt-counter-current">1</span>/<?php echo (int) $count; ?></div>
            <?php endif; ?>

            <?php if ( $show_controls && ! $is_marquee ) : ?>
                <div class="olo-nt-controls">
                    <button type="button" class="olo-nt-ctrl olo-nt-prev" aria-label="<?php esc_attr_e( 'Precedente', 'olobuild' ); ?>">‹</button>
                    <button type="button" class="olo-nt-ctrl olo-nt-next" aria-label="<?php esc_attr_e( 'Successivo', 'olobuild' ); ?>">›</button>
                </div>
            <?php endif; ?>

            <?php if ( $show_indicators && ! $is_marquee ) : ?>
                <div class="olo-nt-indicators">
                    <?php for ( $j = 0; $j < $count; $j++ ) : ?>
                        <button type="button" class="olo-nt-dot <?php echo $j === 0 ? 'olo-nt-dot-active' : ''; ?>" data-target="<?php echo (int) $j; ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Notizia %d', 'olobuild' ), $j + 1 ) ); ?>"></button>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ( ! $is_marquee ) : ?>
        <script>
        (function(){
            var el = document.getElementById('<?php echo $uid; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- internally generated unique id (wp_unique_id) ?>');
            if (!el) { return; }
            var items = el.querySelectorAll('.olo-nt-item');
            var total = items.length;
            if (total === 0) { return; }

            var auto      = <?php echo $auto ? 'true' : 'false'; ?>;
            var pause     = <?php echo $pause ? 'true' : 'false'; ?>;
            var loop      = <?php echo $loop ? 'true' : 'false'; ?>;
            var random    = <?php echo $random ? 'true' : 'false'; ?>;
            var stopClick = <?php echo $stop_click ? 'true' : 'false'; ?>;
            var speed     = <?php echo (int) $speed; ?>;
            var current   = 0;
            var timer     = null;
            var paused    = false;
            var stopped   = false;

            var counter   = el.querySelector('.olo-nt-counter-current');
            var dots      = el.querySelectorAll('.olo-nt-dot');

            function updateUI(idx) {
                if (counter) { counter.textContent = (idx + 1); }
                if (dots.length) {
                    for (var d = 0; d < dots.length; d++) {
                        if (d === idx) { dots[d].classList.add('olo-nt-dot-active'); }
                        else { dots[d].classList.remove('olo-nt-dot-active'); }
                    }
                }
            }

            function nextIndex(from) {
                if (random) {
                    if (total <= 1) { return from; }
                    var n;
                    do { n = Math.floor(Math.random() * total); } while (n === from);
                    return n;
                }
                var nx = from + 1;
                if (nx >= total) {
                    if (!loop) { return -1; }
                    nx = 0;
                }
                return nx;
            }

            function prevIndex(from) {
                var p = from - 1;
                if (p < 0) { p = total - 1; }
                return p;
            }

            function showItem(idx) {
                if (idx < 0 || idx >= total) { return; }
                var prev = current;
                current  = idx;
                if (prev !== idx) {
                    items[prev].classList.remove('olo-nt-active');
                    items[prev].classList.add('olo-nt-exit');
                    items[prev].setAttribute('aria-hidden', 'true');
                    setTimeout(function(){ items[prev].classList.remove('olo-nt-exit'); }, 600);
                }
                items[idx].classList.remove('olo-nt-exit');
                items[idx].classList.add('olo-nt-active');
                items[idx].setAttribute('aria-hidden', 'false');
                updateUI(idx);
            }

            function tick() {
                if (paused || stopped) { return; }
                var nx = nextIndex(current);
                if (nx < 0) { stopTimer(); return; }
                showItem(nx);
            }

            function startTimer() {
                stopTimer();
                if (!auto) { return; }
                if (total <= 1) { return; }
                timer = setInterval(tick, speed);
            }

            function stopTimer() {
                if (timer) { clearInterval(timer); timer = null; }
            }

            // Init
            if (random) { if (total > 1) {
                current = Math.floor(Math.random() * total);
                if (current !== 0) { showItem(current); }
            } }
            startTimer();

            <?php if ( $pause ) : ?>
            el.addEventListener('mouseenter', function(){ paused = true; });
            el.addEventListener('mouseleave', function(){ paused = false; });
            <?php endif; ?>

            <?php if ( $stop_click ) : ?>
            for (var ci = 0; ci < items.length; ci++) {
                items[ci].addEventListener('click', function(e){
                    if (e.target.tagName === 'A') { return; }
                    stopped = !stopped;
                });
            }
            <?php endif; ?>

            // Controlli
            var btnPrev = el.querySelector('.olo-nt-prev');
            var btnNext = el.querySelector('.olo-nt-next');
            if (btnPrev) {
                btnPrev.addEventListener('click', function(){ showItem(prevIndex(current)); startTimer(); });
            }
            if (btnNext) {
                btnNext.addEventListener('click', function(){
                    var nx = nextIndex(current);
                    if (nx >= 0) { showItem(nx); }
                    startTimer();
                });
            }

            // Indicators
            for (var di = 0; di < dots.length; di++) {
                (function(idx){
                    dots[idx].addEventListener('click', function(){ showItem(idx); startTimer(); });
                })(di);
            }
        })();
        </script>
        <?php endif; ?>

        <?php
        $tfx_css = $this->tfx_css( $s, '#' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Text_Effects::css() from whitelisted effects, sanitized colors and integer timings
        $this->tfx_print_script();

        // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        $radius_css        = $this->build_border_radius_css( $s['border_radius'] ?? [] );
        $radius_hover      = $this->build_hover_css( $s, [ 'border_radius' => 'border-radius' ] );
        $radius_hover_css  = '';
        if ( ! empty( $radius_hover['hover_decls'] ) ) {
            $trans = ! empty( $radius_hover['transitions'] ) ? "transition: " . implode( ', ', $radius_hover['transitions'] ) . ";" : '';
            $radius_hover_css = ".{$uid}{{$trans}}.{$uid}:hover{{$radius_hover['hover_decls']}}";
        }
        if ( $border_css || $border_hover_css || $border_effect_css || $radius_css || $radius_hover_css ) {
            echo '<style>';
            $base_decls = '';
            if ( $border_css ) $base_decls .= $border_css;
            if ( $radius_css ) $base_decls .= "border-radius:{$radius_css};";
            if ( $base_decls ) echo ".{$uid}{{$base_decls}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS from build_border_css()/build_border_radius_css() (sanitized internally); $uid is internally generated
            echo $border_hover_css . $border_effect_css . $radius_hover_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base border/hover helpers from sanitized settings
        }
        return ob_get_clean();
    }
}
