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
            [ 'title' => 'Nuova funzionalità disponibile per tutti gli utenti', 'url' => '', 'badge' => 'Novità' ],
            [ 'title' => 'Manutenzione programmata venerdì 21:00 - 23:00', 'url' => '', 'badge' => 'Avviso' ],
            [ 'title' => 'Aggiornamento versione 2.0 rilasciato con successo', 'url' => '', 'badge' => '' ],
        ],
        'label_text'     => 'Breaking',
        'label_bg'       => '#dc2626',
        'label_color'    => '#ffffff',
        'bg_color'       => '',
        'text_color'     => '#f3f4f6',
        'speed'          => '3000',
        'height'         => '42',
        'separator'      => '|',
        'auto_scroll'    => true,
        'pause_on_hover' => true,
    ];

    public function get_controls() {
        return [];
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

        // Settings
        $label_text = esc_html( $s['label_text'] );
        $label_bg   = $this->safe_color_css( $s['label_bg'] ) ?: 'var(--olo-color-danger, #EF4444)';
        $label_clr  = $this->safe_color_css( $s['label_color'] ) ?: 'var(--olo-color-primary-contrast, #FFFFFF)';
        $bg         = $this->safe_color_css( $s['bg_color'] ) ?: 'var(--olo-color-secondary, #1F2937)';
        $text_clr   = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-muted, #F3F4F6)';
        $speed      = max( 2000, intval( $s['speed'] ) );
        $height     = max( 30, min( 60, intval( $s['height'] ) ) );
        $auto       = ! empty( $s['auto_scroll'] );
        $pause      = ! empty( $s['pause_on_hover'] );

        // Build items for rendering
        $items_clean = [];
        foreach ( $items as $item ) {
            $items_clean[] = [
                'title' => wp_kses_post( $item['title'] ),
                'url'   => ! empty( $item['url'] ) ? esc_url( $item['url'] ) : '',
                'badge' => ! empty( $item['badge'] ) ? esc_html( $item['badge'] ) : '',
            ];
        }

        $half_h = intval( $height / 2 );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                display: flex;
                align-items: center;
                background: <?php echo $bg; ?>;
                height: <?php echo $height; ?>px;
                overflow: hidden;
                font-family: inherit;
            }

            .<?php echo $uid; ?> .olo-nt-label {
                background: <?php echo $label_bg; ?>;
                color: <?php echo $label_clr; ?>;
                padding: 0 14px;
                height: 100%;
                display: flex;
                align-items: center;
                font-weight: 700;
                font-size: 12px;
                text-transform: uppercase;
                letter-spacing: 1px;
                white-space: nowrap;
                flex-shrink: 0;
                position: relative;
                z-index: 1;
            }

            .<?php echo $uid; ?> .olo-nt-label::after {
                content: '';
                position: absolute;
                right: -8px;
                top: 0;
                width: 0;
                height: 0;
                border-style: solid;
                border-width: <?php echo $half_h; ?>px 0 <?php echo $half_h; ?>px 8px;
                border-color: transparent transparent transparent <?php echo $label_bg; ?>;
            }

            .<?php echo $uid; ?> .olo-nt-viewport {
                flex: 1;
                overflow: hidden;
                position: relative;
                height: 100%;
                margin-left: 8px;
            }

            .<?php echo $uid; ?> .olo-nt-item {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 100%;
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 0 14px;
                color: <?php echo $text_clr; ?>;
                font-size: 14px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                opacity: 0;
                transform: translateY(100%);
                transition: opacity 0.4s ease, transform 0.4s ease;
                cursor: default;
            }

            .<?php echo $uid; ?> .olo-nt-item.olo-nt-active {
                opacity: 1;
                transform: translateY(0);
            }

            .<?php echo $uid; ?> .olo-nt-item.olo-nt-exit {
                opacity: 0;
                transform: translateY(-100%);
            }

            .<?php echo $uid; ?> .olo-nt-item a {
                color: inherit;
                text-decoration: none;
            }

            .<?php echo $uid; ?> .olo-nt-item a:hover {
                text-decoration: underline;
            }

            .<?php echo $uid; ?> .olo-nt-badge {
                background: rgba(255,255,255,0.15);
                padding: 2px 8px;
                border-radius: 3px;
                font-size: 11px;
                font-weight: 600;
                flex-shrink: 0;
            }
        </style>

        <div class="olo-newsticker <?php echo esc_attr( $uid ); ?>" id="<?php echo esc_attr( $uid ); ?>">
            <?php if ( $label_text ) : ?>
                <div class="olo-nt-label"><?php echo $label_text; ?></div>
            <?php endif; ?>

            <div class="olo-nt-viewport">
                <?php foreach ( $items_clean as $i => $item ) :
                    $active_class = $i === 0 ? 'olo-nt-active' : '';
                    $title_html   = $item['title'];
                    if ( ! empty( $item['url'] ) ) {
                        $title_html = '<a href="' . $item['url'] . '">' . $item['title'] . '</a>';
                    }
                ?>
                <div class="olo-nt-item <?php echo $active_class; ?>" data-index="<?php echo $i; ?>">
                    <?php if ( ! empty( $item['badge'] ) ) : ?>
                        <span class="olo-nt-badge"><?php echo $item['badge']; ?></span>
                    <?php endif; ?>
                    <?php list( $nt_cls, $nt_data ) = $this->tfx_attrs( $s, 'title', wp_strip_all_tags( $item['title'] ) ); ?>
                    <span class="olo-nt-title<?php echo $nt_cls; ?>"<?php echo $nt_data; ?>><?php echo $title_html; ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if ( $auto ) : ?>
        <script>
        (function(){
            var el = document.getElementById('<?php echo $uid; ?>');
            if (!el) { return; }
            var items = el.querySelectorAll('.olo-nt-item');
            var total = items.length;
            if (total <= 1) { return; }
            var current = 0;
            var timer = null;
            var paused = false;

            function nextItem() {
                if (paused) { return; }
                var prev = current;
                current = (current + 1) % total;
                items[prev].classList.remove('olo-nt-active');
                items[prev].classList.add('olo-nt-exit');
                items[current].classList.remove('olo-nt-exit');
                items[current].classList.add('olo-nt-active');
                setTimeout(function(){
                    items[prev].classList.remove('olo-nt-exit');
                }, 500);
            }

            function startTimer() {
                if (timer) { clearInterval(timer); }
                timer = setInterval(nextItem, <?php echo $speed; ?>);
            }

            startTimer();

            <?php if ( $pause ) : ?>
            el.addEventListener('mouseenter', function() {
                paused = true;
            });
            el.addEventListener('mouseleave', function() {
                paused = false;
            });
            <?php endif; ?>
        })();
        </script>
        <?php endif; ?>

        <?php
        $tfx_css = $this->tfx_css( $s, '#' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();
        return ob_get_clean();
    }
}
