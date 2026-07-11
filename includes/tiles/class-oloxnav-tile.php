<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * OLOX Nav — barra di navigazione fissa del sito OLOtheme (design .dnav):
 * logo bianco, link prodotto mono uppercase (attivo colorato per prodotto,
 * pallini colorati su mobile), lang switcher e link "esperienza" pill a destra.
 *
 * Modalità AUTO (per l'uso come template Header condiviso):
 * - active_auto: il link attivo è dedotto dallo slug della pagina corrente
 *   (lo slug `X-manuale` attiva il prodotto `X`).
 * - exp_auto: sulle pagine manuale la pill diventa "← scheda prodotto" e punta
 *   alla scheda; altrove resta il testo/link configurati (default "← il viaggio");
 *   sulla pagina di destinazione stessa (es. la home experience) sparisce.
 */
class Olobuild_OloxNav_Tile extends Olobuild_Olox_Base_Tile {

    protected $type     = 'oloxnav';
    protected $name     = 'OLOX — Nav prodotti';
    protected $icon     = 'dashicons-menu-alt3';
    protected $category = 'navigation';
    protected $defaults = [
        'logo'        => '',
        'logo_url'    => '/',
        'links'       => [
            [ 'label' => 'build', 'url' => '/olobuild/', 'color' => 'build', 'active' => false ],
            [ 'label' => 'booking', 'url' => '/olobooking/', 'color' => 'booking', 'active' => false ],
            [ 'label' => 'lang', 'url' => '/ololang/', 'color' => 'lang', 'active' => false ],
            [ 'label' => 'security', 'url' => '/olosecurity/', 'color' => 'secur', 'active' => false ],
            [ 'label' => 'tour', 'url' => '/olotour/', 'color' => 'tour', 'active' => false ],
            [ 'label' => 'tutor', 'url' => '/olotutor/', 'color' => 'tutor', 'active' => false ],
        ],
        'show_lang'   => true,
        'langs'       => null, // null = default IT/EN/FR/DE/ES (vedi olox_default_langs)
        'exp_text'    => '← il viaggio',
        'exp_url'     => '/',
        'active_auto' => false,
        'exp_auto'    => false,
        'exp_manual_text' => '← scheda prodotto',
        'accent'      => 'olo',
    ];

    /** Slug della pagina correntemente servita ('' fuori dal frontend singolare). */
    private function olox_current_slug() {
        if ( ! function_exists( 'get_queried_object_id' ) ) { return ''; }
        $id = get_queried_object_id();
        if ( ! $id ) { return ''; }
        return (string) get_post_field( 'post_name', $id );
    }

    public function render( $settings, $style = [] ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $this->olox_assets();
        $accent = $this->olox_color( $s['accent'] );

        $slug      = $this->olox_current_slug();
        $is_manual = $slug && substr( $slug, -8 ) === '-manuale';
        $prod_slug = $is_manual ? substr( $slug, 0, -8 ) : $slug;

        $exp_text = $s['exp_text'];
        $exp_url  = $s['exp_url'];
        if ( ! empty( $s['exp_auto'] ) ) {
            if ( $is_manual ) {
                $exp_text = $s['exp_manual_text'];
                $exp_url  = '/' . $prod_slug . '/';
            } else {
                // Sulla destinazione stessa la pill non serve (es. home experience).
                $exp_path = trim( (string) wp_parse_url( (string) ( $exp_url ?: '/' ), PHP_URL_PATH ), '/' );
                $on_dest  = ( '' === $exp_path ) ? ( function_exists( 'is_front_page' ) && is_front_page() ) : ( $exp_path === $slug );
                if ( $on_dest ) { $exp_text = ''; }
            }
        }

        ob_start();
        echo $this->olox_open( '', '--c:' . $accent ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper interno già escapato.
        ?>
        <nav class="dnav">
            <div class="row">
                <a class="logo" href="<?php echo esc_url( $s['logo_url'] ?: '/' ); ?>">
                    <?php if ( ! empty( $s['logo'] ) ) : ?><img src="<?php echo esc_url( $s['logo'] ); ?>" alt="OLOtheme" /><?php endif; ?>
                </a>
                <div class="links">
                    <?php foreach ( $this->olox_items( $s, 'links' ) as $l ) :
                        $on = ! empty( $l['active'] );
                        if ( ! empty( $s['active_auto'] ) && $prod_slug ) {
                            $on = trim( (string) ( $l['url'] ?? '' ), '/' ) === $prod_slug;
                        }
                        $pc = $this->olox_color( $l['color'] ?? 'olo' );
                        ?>
                        <a class="<?php echo $on ? 'on' : ''; ?>" style="--pc:<?php echo esc_attr( $pc ); ?>" href="<?php echo esc_url( $l['url'] ?? '#' ); ?>"><?php echo esc_html( $l['label'] ?? '' ); ?></a>
                    <?php endforeach; ?>
                </div>
                <?php if ( ! empty( $s['show_lang'] ) ) : ?>
                <?php echo $this->olox_langsw( $s['langs'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper interno già escapato. ?>
                <?php endif; ?>
                <?php if ( ! empty( $exp_text ) ) : ?>
                <a class="exp" href="<?php echo esc_url( $exp_url ?: '/' ); ?>"><?php echo esc_html( $exp_text ); ?></a>
                <?php endif; ?>
            </div>
        </nav>
        <?php
        echo $this->olox_close(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }
}
