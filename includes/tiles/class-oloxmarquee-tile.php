<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * OLOX Marquee — nastro mono uppercase a scorrimento infinito (design .dmarq)
 * con separatore colorato configurabile (● ▪ ✕ · ★) e direzione invertibile.
 */
class Olobuild_OloxMarquee_Tile extends Olobuild_Olox_Base_Tile {

    protected $type     = 'oloxmarquee';
    protected $name     = 'OLOX — Marquee';
    protected $icon     = 'dashicons-ellipsis';
    protected $category = 'marketing';
    protected $defaults = [
        'items'    => [
            [ 'text' => 'no SaaS' ],
            [ 'text' => 'GPL' ],
            [ 'text' => '187 tile' ],
            [ 'text' => '28 lingue' ],
            [ 'text' => '6 verticali booking' ],
            [ 'text' => '100% locale' ],
            [ 'text' => 'made in Trento' ],
        ],
        'sep'      => '●',
        'reverse'  => false,
        'duration' => 28,
        'accent'   => 'olo',
    ];

    public function render( $settings, $style = [] ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $this->olox_assets();
        $accent = $this->olox_color( $s['accent'] );
        $dur    = max( 5, intval( $s['duration'] ) );

        $parts = [];
        foreach ( $this->olox_items( $s, 'items' ) as $it ) {
            $txt = trim( (string) ( $it['text'] ?? '' ) );
            if ( $txt !== '' ) {
                $parts[] = esc_html( $txt ) . ' <b>' . esc_html( $s['sep'] ) . '</b>';
            }
        }
        $line = implode( "\n", $parts );

        ob_start();
        echo $this->olox_open( '', '--c:' . $accent . ';--marq-dur:' . $dur . 's' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
        <div class="dmarq<?php echo ! empty( $s['reverse'] ) ? ' rev' : ''; ?>"><span class="in">
            <?php echo $line . "\n" . $line; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- parti già escapate sopra. ?>
        </span></div>
        <?php
        echo $this->olox_close(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }
}
