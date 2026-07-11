<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * OLOX Footer — footer del sito OLOtheme (design .dfoot): logo bianco, link mono,
 * riga "fine" a destra + credits fissi in basso (facoltativi).
 *
 * Modalità AUTO (per l'uso come template Footer condiviso), fedele al sorgente:
 * - pagine prodotto: "il viaggio" + i 5 prodotti (esclusa la pagina corrente);
 * - pagine manuale: i 6 link ai manuali e fine "manuali base · GPL · Trento";
 * - fine_overrides: eccezioni per-slug, formato "slug:testo|slug:testo"
 *   (es. security che aggiunge "· 100% locale").
 */
class Olobuild_OloxFoot_Tile extends Olobuild_Olox_Base_Tile {

    protected $type     = 'oloxfoot';
    protected $name     = 'OLOX — Footer';
    protected $icon     = 'dashicons-align-wide';
    protected $category = 'navigation';
    protected $defaults = [
        'logo'           => '',
        'links'          => [
            [ 'label' => 'il viaggio', 'url' => '/' ],
            [ 'label' => 'build', 'url' => '/olobuild/' ],
            [ 'label' => 'booking', 'url' => '/olobooking/' ],
            [ 'label' => 'lang', 'url' => '/ololang/' ],
            [ 'label' => 'security', 'url' => '/olosecurity/' ],
            [ 'label' => 'tour', 'url' => '/olotour/' ],
        ],
        'fine'           => 'GPL · Trento · no SaaS',
        'links_auto'     => false,
        'home_label'     => 'il viaggio',
        'products'       => [
            [ 'label' => 'build', 'slug' => 'olobuild' ],
            [ 'label' => 'booking', 'slug' => 'olobooking' ],
            [ 'label' => 'lang', 'slug' => 'ololang' ],
            [ 'label' => 'security', 'slug' => 'olosecurity' ],
            [ 'label' => 'tour', 'slug' => 'olotour' ],
            [ 'label' => 'tutor', 'slug' => 'olotutor' ],
        ],
        'fine_manual'    => 'manuali base · GPL · Trento',
        'fine_overrides' => 'olosecurity:GPL · Trento · no SaaS · 100% locale',
        'show_credits'   => true,
        'credits_html'   => 'OLOtheme by <a href="https://clod.eu" target="_blank" rel="noopener">clod.eu</a> | @2026 | sito introduttivo | <a href="mailto:info@olotheme.com">info@olotheme.com</a>',
        'accent'         => 'olo',
    ];

    private function olox_current_slug() {
        if ( ! function_exists( 'get_queried_object_id' ) ) { return ''; }
        $id = get_queried_object_id();
        if ( ! $id ) { return ''; }
        return (string) get_post_field( 'post_name', $id );
    }

    /** Costruisce i link in modalità auto secondo il contesto (prodotto/manuale). */
    private function olox_auto_links( $s, $slug, $is_manual ) {
        $products = $this->olox_items( $s, 'products' );
        $links    = [];
        if ( $is_manual ) {
            foreach ( $products as $p ) {
                $links[] = [ 'label' => $p['label'] ?? '', 'url' => '/' . ( $p['slug'] ?? '' ) . '-manuale/' ];
            }
            return $links;
        }
        $links[] = [ 'label' => $s['home_label'], 'url' => '/' ];
        foreach ( $products as $p ) {
            if ( ( $p['slug'] ?? '' ) === $slug ) { continue; }
            $links[] = [ 'label' => $p['label'] ?? '', 'url' => '/' . ( $p['slug'] ?? '' ) . '/' ];
        }
        return $links;
    }

    public function render( $settings, $style = [] ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $this->olox_assets();
        $accent = $this->olox_color( $s['accent'] );

        $slug      = $this->olox_current_slug();
        $is_manual = $slug && substr( $slug, -8 ) === '-manuale';

        $links = $this->olox_items( $s, 'links' );
        $fine  = (string) $s['fine'];
        if ( ! empty( $s['links_auto'] ) && $slug ) {
            $links = $this->olox_auto_links( $s, $slug, $is_manual );
            if ( $is_manual ) {
                $fine = (string) $s['fine_manual'];
            } else {
                foreach ( explode( '|', (string) $s['fine_overrides'] ) as $pair ) {
                    $pos = strpos( $pair, ':' );
                    if ( $pos !== false && trim( substr( $pair, 0, $pos ) ) === $slug ) {
                        $fine = trim( substr( $pair, $pos + 1 ) );
                    }
                }
            }
        }

        ob_start();
        echo $this->olox_open( '', '--c:' . $accent ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
        <footer class="dfoot">
            <div class="wrap">
                <?php if ( ! empty( $s['logo'] ) ) : ?><img src="<?php echo esc_url( $s['logo'] ); ?>" alt="OLOtheme" /><?php endif; ?>
                <div class="lnk">
                    <?php foreach ( $links as $l ) : ?>
                        <a href="<?php echo esc_url( $l['url'] ?? '#' ); ?>"><?php echo esc_html( $l['label'] ?? '' ); ?></a>
                    <?php endforeach; ?>
                </div>
                <div class="fine"><?php echo esc_html( $fine ); ?></div>
            </div>
        </footer>
        <?php if ( ! empty( $s['show_credits'] ) ) : ?>
        <div class="credits"><?php echo $this->olox_rich( $s['credits_html'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
        <?php endif;
        echo $this->olox_close(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }
}
