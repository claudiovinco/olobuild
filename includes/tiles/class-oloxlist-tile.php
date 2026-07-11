<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * OLOX List — sezione .dsec con liste animate del design OLOlang:
 * - flip: flipboard — righe che girano come un tabellone (src ⇄ dst con label)
 * - url:  SEO stream — pill URL che entrano alternate da sinistra/destra
 */
class Olobuild_OloxList_Tile extends Olobuild_Olox_Base_Tile {

    protected $type     = 'oloxlist';
    protected $name     = 'OLOX — Lista flip/URL';
    protected $icon     = 'dashicons-list-view';
    protected $category = 'marketing';
    protected $defaults = [
        'accent'     => 'lang',
        'variant'    => 'flip',
        'anchor'     => '',
        'kicker'     => 'Tradotto davvero',
        'title_html' => 'Ogni riga <em>gira</em> come un tabellone',
        'lead'       => 'Non solo i testi: menu, stringhe di tema e plugin, tutto passa dal database e torna fuori nella lingua giusta.',
        // flip: items {src_label, src_html, dst_label, dst_html}
        'flip_items' => [
            [ 'src_label' => 'contenuto · it', 'src_html' => 'Prenota il tuo soggiorno', 'dst_label' => 'content · en', 'dst_html' => 'Book your stay' ],
        ],
        // url: items {html, ok}
        'url_items'  => [
            [ 'html' => 'https://tuosito.it<b>/it/</b>camere-vista-lago', 'ok' => 'indicizzata' ],
        ],
    ];

    public function render( $settings, $style = [] ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $this->olox_assets();
        $accent = $this->olox_color( $s['accent'] );
        $anchor = sanitize_html_class( (string) $s['anchor'] );

        ob_start();
        echo $this->olox_open( '', '--c:' . $accent ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
        <section class="dsec"<?php echo $anchor ? ' id="' . esc_attr( $anchor ) . '"' : ''; ?>>
            <div class="wrap">
                <div class="head">
                    <?php if ( ! empty( $s['kicker'] ) ) : ?><div class="k"><?php echo esc_html( $s['kicker'] ); ?></div><?php endif; ?>
                    <?php if ( ! empty( $s['title_html'] ) ) : ?><h2 class="d"><?php echo $this->olox_rich( $s['title_html'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2><?php endif; ?>
                    <?php if ( ! empty( $s['lead'] ) ) : ?><p><?php echo $this->olox_rich( $s['lead'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p><?php endif; ?>
                </div>
                <?php if ( ( $s['variant'] ?? 'flip' ) === 'url' ) : ?>
                <div class="urlstream" data-olox="urls">
                    <?php foreach ( $this->olox_items( $s, 'url_items' ) as $u ) : ?>
                    <div class="url"><?php echo $this->olox_rich( $u['html'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <span class="ok"><?php echo esc_html( $u['ok'] ?? '' ); ?></span></div>
                    <?php endforeach; ?>
                </div>
                <?php else : ?>
                <div class="flips" data-olox="flips">
                    <?php foreach ( $this->olox_items( $s, 'flip_items' ) as $f ) : ?>
                    <div class="fliprow"><span class="src"><span class="lab"><?php echo esc_html( $f['src_label'] ?? '' ); ?></span><?php echo $this->olox_rich( $f['src_html'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><span class="arrow">⇄</span><span class="dst"><span class="lab"><?php echo esc_html( $f['dst_label'] ?? '' ); ?></span><?php echo $this->olox_rich( $f['dst_html'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span></div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>
        <?php
        echo $this->olox_close(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }
}
