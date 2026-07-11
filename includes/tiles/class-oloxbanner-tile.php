<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * OLOX Banner — due varianti del design OLOtheme:
 * - follow: banner "In arrivo" con chip tratteggiata e testo con link (LinkedIn).
 * - next:   riga "prossima fermata" con label piccola e grande link Fraunces con <em>.
 */
class Olobuild_OloxBanner_Tile extends Olobuild_Olox_Base_Tile {

    protected $type     = 'oloxbanner';
    protected $name     = 'OLOX — Banner follow/next';
    protected $icon     = 'dashicons-migrate';
    protected $category = 'marketing';
    protected $defaults = [
        'variant'   => 'next',
        'accent'    => 'olo',
        // follow
        'fk_text'   => 'In arrivo',
        'body_html' => 'Versione demo o gratuita/completa in arrivo: segui <a href="https://www.linkedin.com/company/olotheme/" target="_blank" rel="noopener">OLOtheme su LinkedIn</a> o <a href="https://www.linkedin.com/in/vincoclaudio/" target="_blank" rel="noopener">Claudio Vinco</a> per rimanere aggiornato.',
        // next
        'label'     => 'Prossima fermata',
        'link_html' => 'OLO<em>booking</em> →',
        'link_url'  => 'olobooking',
    ];

    public function render( $settings, $style = [] ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $this->olox_assets();
        $accent = $this->olox_color( $s['accent'] );

        ob_start();
        echo $this->olox_open( '', '--c:' . $accent . ';--nc:' . $accent ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        if ( ( $s['variant'] ?? 'next' ) === 'follow' ) :
            ?>
            <section class="follow">
                <div class="wrap">
                    <span class="fk"><?php echo esc_html( $s['fk_text'] ); ?></span>
                    <p><?php echo $this->olox_rich( $s['body_html'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- kses nel helper. ?></p>
                </div>
            </section>
        <?php else : ?>
            <div class="dnext">
                <div class="wrap">
                    <span class="lbl"><?php echo esc_html( $s['label'] ); ?></span>
                    <a class="nx" href="<?php echo esc_url( $s['link_url'] ?: '#' ); ?>"><?php echo $this->olox_rich( $s['link_html'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
                </div>
            </div>
        <?php endif;
        echo $this->olox_close(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }
}
