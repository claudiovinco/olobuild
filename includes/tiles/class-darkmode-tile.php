<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Darkmode_Tile extends Olo_Tile_Base {

    protected $type     = 'darkmode';
    protected $name     = 'Dark Mode Toggle';
    protected $icon     = 'dashicons-admin-appearance';
    protected $category = 'interactive';
    protected $defaults = [
        'style'               => 'toggle',
        'light_icon'          => 'sun',
        'dark_icon'           => 'moon',
        'icon_size'           => 24,
        'button_text_light'   => 'Modalità scura',
        'button_text_dark'    => 'Modalità chiara',
        'toggle_color'        => '#333333',
        'toggle_active_color' => '#ffd700',
        'save_preference'     => true,
        'respect_system'      => true,
        'transition_duration' => 300,
    ];

    /** Track whether we already printed the head init script */
    private static $head_script_printed = false;

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $uid       = 'olo-dm-' . wp_rand( 10000, 99999 );
        $style     = in_array( $s['style'], [ 'toggle', 'icon', 'button' ] ) ? $s['style'] : 'toggle';
        $icon_size = max( 16, intval( $s['icon_size'] ) );
        $color     = $this->safe_color_css( $s['toggle_color'] ) ?: '#333333';
        $active    = $this->safe_color_css( $s['toggle_active_color'] ) ?: '#ffd700';
        $duration  = max( 0, intval( $s['transition_duration'] ) );
        $save_pref = ! empty( $s['save_preference'] );
        $respect   = ! empty( $s['respect_system'] );

        $text_light = esc_attr( $s['button_text_light'] );
        $text_dark  = esc_attr( $s['button_text_dark'] );

        // SVG icons
        $sun_svg  = '<svg width="' . $icon_size . '" height="' . $icon_size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>';
        $moon_svg = '<svg width="' . $icon_size . '" height="' . $icon_size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>';

        // Small icons for toggle thumb
        $thumb_size = max( 12, round( $icon_size * 0.6 ) );
        $sun_sm   = '<svg width="' . $thumb_size . '" height="' . $thumb_size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>';
        $moon_sm  = '<svg width="' . $thumb_size . '" height="' . $thumb_size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>';

        // Print the head init script once per page (CSS vars now come from Style System)
        $this->enqueue_head_init( $respect, $duration );

        ob_start();

        // Track dimensions for toggle
        $track_w    = max( 44, round( $icon_size * 2.2 ) );
        $track_h    = max( 24, round( $icon_size * 1.2 ) );
        $thumb_d    = $track_h - 6;
        $travel     = $track_w - $thumb_d - 6;

        ?>
        <style>
            .<?php echo $uid; ?> { display: flex; align-items: center; justify-content: center; }

            /* Toggle style */
            .<?php echo $uid; ?> .olo-dm-track {
                display: inline-flex;
                align-items: center;
                width: <?php echo $track_w; ?>px;
                height: <?php echo $track_h; ?>px;
                border-radius: <?php echo $track_h; ?>px;
                background: <?php echo $color; ?>;
                padding: 3px;
                cursor: pointer;
                transition: background <?php echo $duration; ?>ms ease;
                border: none;
                outline: none;
                position: relative;
            }
            html.olo-dark-mode .<?php echo $uid; ?> .olo-dm-track {
                background: <?php echo $active; ?>;
            }
            .<?php echo $uid; ?> .olo-dm-thumb {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: <?php echo $thumb_d; ?>px;
                height: <?php echo $thumb_d; ?>px;
                border-radius: 50%;
                background: #fff;
                transform: translateX(0);
                transition: transform <?php echo $duration; ?>ms ease;
                color: <?php echo $color; ?>;
            }
            html.olo-dark-mode .<?php echo $uid; ?> .olo-dm-thumb {
                transform: translateX(<?php echo $travel; ?>px);
                color: <?php echo $active; ?>;
            }
            .<?php echo $uid; ?> .olo-dm-thumb .olo-dm-icon-sun,
            .<?php echo $uid; ?> .olo-dm-thumb .olo-dm-icon-moon {
                transition: opacity <?php echo $duration; ?>ms ease, transform <?php echo $duration; ?>ms ease;
                position: absolute;
            }
            .<?php echo $uid; ?> .olo-dm-thumb .olo-dm-icon-sun {
                opacity: 1;
                transform: rotate(0deg);
            }
            .<?php echo $uid; ?> .olo-dm-thumb .olo-dm-icon-moon {
                opacity: 0;
                transform: rotate(-90deg);
            }
            html.olo-dark-mode .<?php echo $uid; ?> .olo-dm-thumb .olo-dm-icon-sun {
                opacity: 0;
                transform: rotate(90deg);
            }
            html.olo-dark-mode .<?php echo $uid; ?> .olo-dm-thumb .olo-dm-icon-moon {
                opacity: 1;
                transform: rotate(0deg);
            }

            /* Icon style */
            .<?php echo $uid; ?> .olo-dm-icon-btn {
                background: none;
                border: none;
                cursor: pointer;
                padding: 8px;
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: <?php echo $color; ?>;
                transition: color <?php echo $duration; ?>ms ease, transform <?php echo $duration; ?>ms ease;
            }
            html.olo-dark-mode .<?php echo $uid; ?> .olo-dm-icon-btn {
                color: <?php echo $active; ?>;
            }
            .<?php echo $uid; ?> .olo-dm-icon-btn:hover {
                transform: scale(1.1);
            }
            .<?php echo $uid; ?> .olo-dm-icon-btn .olo-dm-icon-sun,
            .<?php echo $uid; ?> .olo-dm-icon-btn .olo-dm-icon-moon {
                transition: opacity <?php echo $duration; ?>ms ease, transform <?php echo $duration; ?>ms ease;
                position: absolute;
            }
            .<?php echo $uid; ?> .olo-dm-icon-btn .olo-dm-icon-sun {
                opacity: 1; transform: rotate(0deg) scale(1);
            }
            .<?php echo $uid; ?> .olo-dm-icon-btn .olo-dm-icon-moon {
                opacity: 0; transform: rotate(-90deg) scale(0.5);
            }
            html.olo-dark-mode .<?php echo $uid; ?> .olo-dm-icon-btn .olo-dm-icon-sun {
                opacity: 0; transform: rotate(90deg) scale(0.5);
            }
            html.olo-dark-mode .<?php echo $uid; ?> .olo-dm-icon-btn .olo-dm-icon-moon {
                opacity: 1; transform: rotate(0deg) scale(1);
            }

            /* Button style */
            .<?php echo $uid; ?> .olo-dm-button {
                display: inline-flex;
                align-items: center;
                padding: 10px 20px;
                border-radius: 8px;
                border: 2px solid <?php echo $color; ?>;
                background: transparent;
                color: <?php echo $color; ?>;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: all <?php echo $duration; ?>ms ease;
                font-family: inherit;
            }
            html.olo-dark-mode .<?php echo $uid; ?> .olo-dm-button {
                border-color: <?php echo $active; ?>;
                color: <?php echo $active; ?>;
            }
            .<?php echo $uid; ?> .olo-dm-button:hover {
                transform: scale(1.03);
            }
            .<?php echo $uid; ?> .olo-dm-track:focus-visible,
            .<?php echo $uid; ?> .olo-dm-icon-btn:focus-visible,
            .<?php echo $uid; ?> .olo-dm-button:focus-visible {
                outline: none;
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
            }
            .<?php echo $uid; ?> .olo-dm-button .olo-dm-icon-sun,
            .<?php echo $uid; ?> .olo-dm-button .olo-dm-icon-moon {
                transition: opacity <?php echo $duration; ?>ms ease, transform <?php echo $duration; ?>ms ease;
                margin-right: 8px;
                flex-shrink: 0;
            }
            .<?php echo $uid; ?> .olo-dm-button .olo-dm-icon-sun {
                display: inline-flex;
            }
            .<?php echo $uid; ?> .olo-dm-button .olo-dm-icon-moon {
                display: none;
            }
            html.olo-dark-mode .<?php echo $uid; ?> .olo-dm-button .olo-dm-icon-sun {
                display: none;
            }
            html.olo-dark-mode .<?php echo $uid; ?> .olo-dm-button .olo-dm-icon-moon {
                display: inline-flex;
            }
            .<?php echo $uid; ?> .olo-dm-button .olo-dm-text-light {
                display: inline;
            }
            .<?php echo $uid; ?> .olo-dm-button .olo-dm-text-dark {
                display: none;
            }
            html.olo-dark-mode .<?php echo $uid; ?> .olo-dm-button .olo-dm-text-light {
                display: none;
            }
            html.olo-dark-mode .<?php echo $uid; ?> .olo-dm-button .olo-dm-text-dark {
                display: inline;
            }
        </style>

        <div class="olo-darkmode <?php echo esc_attr( $uid ); ?>"
             data-olo-darkmode="1"
             data-save="<?php echo $save_pref ? '1' : '0'; ?>"
             role="switch"
             aria-label="<?php echo esc_attr( olo_t( 'Dark mode toggle' ) ); ?>">

            <?php if ( $style === 'toggle' ) : ?>
                <button type="button" class="olo-dm-track" aria-label="<?php echo esc_attr( olo_t( 'Toggle dark mode' ) ); ?>">
                    <span class="olo-dm-thumb" style="position:relative;">
                        <span class="olo-dm-icon-sun"><?php echo $sun_sm; ?></span>
                        <span class="olo-dm-icon-moon"><?php echo $moon_sm; ?></span>
                    </span>
                </button>

            <?php elseif ( $style === 'icon' ) : ?>
                <button type="button" class="olo-dm-icon-btn" aria-label="<?php echo esc_attr( olo_t( 'Toggle dark mode' ) ); ?>" style="position:relative;">
                    <span class="olo-dm-icon-sun"><?php echo $sun_svg; ?></span>
                    <span class="olo-dm-icon-moon"><?php echo $moon_svg; ?></span>
                </button>

            <?php else : ?>
                <button type="button" class="olo-dm-button" aria-label="<?php echo esc_attr( olo_t( 'Toggle dark mode' ) ); ?>">
                    <span class="olo-dm-icon-sun"><?php echo $sun_svg; ?></span>
                    <span class="olo-dm-icon-moon"><?php echo $moon_svg; ?></span>
                    <span class="olo-dm-text-light"><?php echo esc_html( $s['button_text_light'] ); ?></span>
                    <span class="olo-dm-text-dark"><?php echo esc_html( $s['button_text_dark'] ); ?></span>
                </button>
            <?php endif; ?>
        </div>

        <script>
        (function(){
            var wrap = document.querySelector('.<?php echo $uid; ?>');
            if (!wrap) return;
            var btn = wrap.querySelector('button');
            if (!btn) return;
            var savePref = wrap.getAttribute('data-save') === '1';

            btn.addEventListener('click', function() {
                var html = document.documentElement;
                var isDark = html.classList.contains('olo-dark-mode');
                if (isDark) {
                    html.classList.remove('olo-dark-mode');
                    if (savePref) {
                        try { localStorage.setItem('olo-dark-mode', 'light'); } catch(e){}
                    }
                } else {
                    html.classList.add('olo-dark-mode');
                    if (savePref) {
                        try { localStorage.setItem('olo-dark-mode', 'dark'); } catch(e){}
                    }
                }
            });
        })();
        </script>
        <?php

        return ob_get_clean();
    }

    /**
     * Enqueue the head initialization script (runs before render to avoid FOUC).
     * Uses wp_head hook at priority 1 to run as early as possible.
     * IMPORTANT: NO && in inline scripts — use nested if() instead.
     */
    private function enqueue_head_init( $respect_system, $duration ) {
        if ( self::$head_script_printed ) {
            return;
        }
        self::$head_script_printed = true;

        $respect_js = $respect_system ? 'true' : 'false';
        $dur = intval( $duration );

        add_action( 'wp_head', function() use ( $respect_js, $dur ) {
            ?>
            <script>
            (function(){
                var html = document.documentElement;
                var stored = null;
                try { stored = localStorage.getItem('olo-dark-mode'); } catch(e){}
                if (stored === 'dark') {
                    html.classList.add('olo-dark-mode');
                } else if (stored === 'light') {
                    html.classList.remove('olo-dark-mode');
                } else {
                    if (<?php echo $respect_js; ?>) {
                        if (window.matchMedia) {
                            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                                html.classList.add('olo-dark-mode');
                            }
                        }
                    }
                }
            })();
            </script>
            <style>
                html { transition: background-color <?php echo $dur; ?>ms ease, color <?php echo $dur; ?>ms ease; }
            </style>
            <?php
        }, 1 );
    }

}
