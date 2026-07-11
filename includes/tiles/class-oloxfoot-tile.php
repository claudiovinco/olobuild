<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * OLOX Footer — footer del sito OLOtheme (design .dfoot): logo bianco, link mono,
 * riga "fine" a destra + credits fissi in basso (facoltativi).
 */
class Olobuild_OloxFoot_Tile extends Olobuild_Olox_Base_Tile {

    protected $type     = 'oloxfoot';
    protected $name     = 'OLOX — Footer';
    protected $icon     = 'dashicons-align-wide';
    protected $category = 'navigation';
    protected $defaults = [
        'logo'         => '',
        'links'        => [
            [ 'label' => 'il viaggio', 'url' => './' ],
            [ 'label' => 'build', 'url' => 'olobuild' ],
            [ 'label' => 'booking', 'url' => 'olobooking' ],
            [ 'label' => 'lang', 'url' => 'ololang' ],
            [ 'label' => 'security', 'url' => 'olosecurity' ],
            [ 'label' => 'tour', 'url' => 'olotour' ],
        ],
        'fine'         => 'GPL · Trento · no SaaS',
        'show_credits' => true,
        'credits_html' => 'OLOtheme by <a href="https://clod.eu" target="_blank" rel="noopener">clod.eu</a> | @2026 | sito introduttivo | <a href="mailto:info@olotheme.com">info@olotheme.com</a>',
        'accent'       => 'olo',
    ];

    public function render( $settings, $style = [] ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $this->olox_assets();
        $accent = $this->olox_color( $s['accent'] );

        ob_start();
        echo $this->olox_open( '', '--c:' . $accent ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
        <footer class="dfoot">
            <div class="wrap">
                <?php if ( ! empty( $s['logo'] ) ) : ?><img src="<?php echo esc_url( $s['logo'] ); ?>" alt="OLOtheme" /><?php endif; ?>
                <div class="lnk">
                    <?php foreach ( $this->olox_items( $s, 'links' ) as $l ) : ?>
                        <a href="<?php echo esc_url( $l['url'] ?? '#' ); ?>"><?php echo esc_html( $l['label'] ?? '' ); ?></a>
                    <?php endforeach; ?>
                </div>
                <div class="fine"><?php echo esc_html( $s['fine'] ); ?></div>
            </div>
        </footer>
        <?php if ( ! empty( $s['show_credits'] ) ) : ?>
        <div class="credits"><?php echo $this->olox_rich( $s['credits_html'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
        <?php endif;
        echo $this->olox_close(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }
}
