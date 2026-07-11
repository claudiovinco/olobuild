<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * OLOX Nav — barra di navigazione fissa del sito OLOtheme (design .dnav):
 * logo bianco, link prodotto mono uppercase (attivo colorato per prodotto,
 * pallini colorati su mobile), lang switcher e link "esperienza" pill a destra.
 */
class Olobuild_OloxNav_Tile extends Olobuild_Olox_Base_Tile {

    protected $type     = 'oloxnav';
    protected $name     = 'OLOX — Nav prodotti';
    protected $icon     = 'dashicons-menu-alt3';
    protected $category = 'navigation';
    protected $defaults = [
        'logo'      => '',
        'logo_url'  => './',
        'links'     => [
            [ 'label' => 'build', 'url' => 'olobuild', 'color' => 'build', 'active' => false ],
            [ 'label' => 'booking', 'url' => 'olobooking', 'color' => 'booking', 'active' => false ],
            [ 'label' => 'lang', 'url' => 'ololang', 'color' => 'lang', 'active' => false ],
            [ 'label' => 'security', 'url' => 'olosecurity', 'color' => 'secur', 'active' => false ],
            [ 'label' => 'tour', 'url' => 'olotour', 'color' => 'tour', 'active' => false ],
            [ 'label' => 'tutor', 'url' => 'olotutor', 'color' => 'tutor', 'active' => false ],
        ],
        'show_lang' => true,
        'exp_text'  => '← il viaggio',
        'exp_url'   => './',
        'accent'    => 'olo',
    ];

    public function render( $settings, $style = [] ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $this->olox_assets();
        $accent = $this->olox_color( $s['accent'] );

        ob_start();
        echo $this->olox_open( '', '--c:' . $accent ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper interno già escapato.
        ?>
        <nav class="dnav">
            <div class="row">
                <a class="logo" href="<?php echo esc_url( $s['logo_url'] ?: './' ); ?>">
                    <?php if ( ! empty( $s['logo'] ) ) : ?><img src="<?php echo esc_url( $s['logo'] ); ?>" alt="OLOtheme" /><?php endif; ?>
                </a>
                <div class="links">
                    <?php foreach ( $this->olox_items( $s, 'links' ) as $l ) :
                        $on = ! empty( $l['active'] );
                        $pc = $this->olox_color( $l['color'] ?? 'olo' );
                        ?>
                        <a class="<?php echo $on ? 'on' : ''; ?>" style="--pc:<?php echo esc_attr( $pc ); ?>" href="<?php echo esc_url( $l['url'] ?? '#' ); ?>"><?php echo esc_html( $l['label'] ?? '' ); ?></a>
                    <?php endforeach; ?>
                </div>
                <?php if ( ! empty( $s['show_lang'] ) ) : ?>
                <div class="langsw" data-olox="langsw"><button class="lsw-t" type="button">IT ▾</button><div class="lsw-list"><a class="on" href="#">IT</a></div></div>
                <?php endif; ?>
                <?php if ( ! empty( $s['exp_text'] ) ) : ?>
                <a class="exp" href="<?php echo esc_url( $s['exp_url'] ?: './' ); ?>"><?php echo esc_html( $s['exp_text'] ); ?></a>
                <?php endif; ?>
            </div>
        </nav>
        <?php
        echo $this->olox_close(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }
}
