<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Menuanchor_Tile extends Olo_Tile_Base {

    protected $type     = 'menuanchor';
    protected $name     = 'Ancora Menu';
    protected $icon     = 'dashicons-admin-links';
    protected $category = 'navigation';
    protected $defaults = [
        'anchor_id' => '',
        'offset'    => '0',
        'label'     => '',
    ];

    /** Tracks whether the smooth-scroll script has been output. */
    private static $script_enqueued = false;

    public function get_controls() { return []; }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        // Sanitize anchor ID — remove # if present, sanitize
        $anchor_id = sanitize_html_class( ltrim( $s['anchor_id'], '#' ) );

        // If no anchor_id, render nothing
        if ( empty( $anchor_id ) ) {
            return '';
        }

        $offset = absint( $s['offset'] );

        ob_start();
        ?>
        <div id="<?php echo esc_attr( $anchor_id ); ?>" class="olo-menuanchor" data-offset="<?php echo $offset; ?>" style="height:0; overflow:hidden;"></div>
        <?php

        // Smooth scroll script — only once per page
        if ( ! self::$script_enqueued ) {
            self::$script_enqueued = true;
            ?>
            <script>
            (function(){
                document.addEventListener('click', function(e) {
                    var link = e.target.closest('a[href^="#"]');
                    if (!link) return;

                    var hash = link.getAttribute('href');
                    if (!hash) return;
                    if (hash.length < 2) return;

                    var targetId = hash.substring(1);
                    var target = document.getElementById(targetId);
                    if (!target) return;

                    e.preventDefault();

                    var offset = 0;
                    var anchor = target.closest('.olo-menuanchor');
                    if (!anchor) {
                        anchor = target;
                    }
                    if (anchor) {
                        if (anchor.dataset.offset) {
                            offset = parseInt(anchor.dataset.offset, 10) || 0;
                        }
                    }

                    var rect = target.getBoundingClientRect();
                    var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    var targetTop = rect.top + scrollTop - offset;

                    window.scrollTo({ top: targetTop, behavior: 'smooth' });

                    // Update URL hash without jumping
                    if (history.pushState) {
                        history.pushState(null, null, hash);
                    }
                });
            })();
            </script>
            <?php
        }

        return ob_get_clean();
    }
}
