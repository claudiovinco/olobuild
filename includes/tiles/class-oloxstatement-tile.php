<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * OLOX Statement — sezioni "statement" del design OLOtheme, quattro varianti:
 * - counter:  contatore attacchi gigante centrato che sale quando visibile (security)
 * - zerozero: "0/0" Fraunces gigante + blocco testo a fianco (security)
 * - stamp:    pannello con timbro ruotato sul bordo (anti no-show, booking)
 * - plain:    testata semplice kicker + H2 + paragrafo + CTA (ololang "Incluso")
 */
class Olobuild_OloxStatement_Tile extends Olobuild_Olox_Base_Tile {

    protected $type     = 'oloxstatement';
    protected $name     = 'OLOX — Statement (counter/0-0/stamp)';
    protected $icon     = 'dashicons-megaphone';
    protected $category = 'marketing';
    protected $defaults = [
        'accent'      => 'secur',
        'variant'     => 'counter',
        'anchor'      => '',
        'kicker'      => 'Mentre leggevi questa pagina',
        'title_html'  => 'WP Plugin Check: zero errori, <em>zero warning</em>',
        'body_html'   => 'bloccati da un WordPress medio esposto in rete. Non serve essere famosi per essere un bersaglio: basta essere online.',
        // counter
        'counter_to'    => 47,
        'counter_after' => 'tentativi',
        // zerozero
        'zz_text'     => '0/0',
        // stamp
        'stamp_text'  => 'No-show ◦ Coperto',
        // plain
        'cta_text'    => '',
        'cta_url'     => '',
    ];

    public function render( $settings, $style = [] ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $this->olox_assets();
        $accent  = $this->olox_color( $s['accent'] );
        $variant = $s['variant'] ?? 'plain';
        $anchor  = sanitize_html_class( (string) $s['anchor'] );

        ob_start();
        echo $this->olox_open( '', '--c:' . $accent ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
        <section class="dsec"<?php echo $anchor ? ' id="' . esc_attr( $anchor ) . '"' : ''; ?><?php echo 'counter' === $variant ? ' data-olox="counter"' : ''; ?>>
            <?php if ( 'counter' === $variant ) : ?>
            <div class="wrap" style="text-align:center;">
                <div class="k" style="justify-content:center;"><?php echo esc_html( $s['kicker'] ); ?></div>
                <div class="bigcounter"><span class="ox-atk" data-to="<?php echo (int) $s['counter_to']; ?>">0</span> <em><?php echo esc_html( $s['counter_after'] ); ?></em></div>
                <p class="sub" style="margin:22px auto 0; text-align:center;"><?php echo $this->olox_rich( $s['body_html'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
            </div>
            <?php elseif ( 'zerozero' === $variant ) : ?>
            <div class="wrap">
                <div class="zerozero">
                    <span class="zz"><?php echo esc_html( $s['zz_text'] ); ?></span>
                    <div style="max-width:52ch;">
                        <div class="k"><?php echo esc_html( $s['kicker'] ); ?></div>
                        <h2 class="d" style="font-size:clamp(26px,3vw,40px);"><?php echo $this->olox_rich( $s['title_html'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
                        <p class="sub" style="margin-bottom:0;"><?php echo $this->olox_rich( $s['body_html'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
                    </div>
                </div>
            </div>
            <?php elseif ( 'stamp' === $variant ) : ?>
            <div class="wrap">
                <div class="noshow rv" data-olox="reveal">
                    <div class="k"><?php echo esc_html( $s['kicker'] ); ?></div>
                    <h2 class="d" style="max-width:12ch;"><?php echo $this->olox_rich( $s['title_html'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
                    <p class="sub" style="margin-bottom:0;"><?php echo $this->olox_rich( $s['body_html'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
                    <div class="bigstamp"><?php echo esc_html( $s['stamp_text'] ); ?></div>
                </div>
            </div>
            <?php else : ?>
            <div class="wrap">
                <div class="head" style="margin-bottom:0; max-width:820px;">
                    <div class="k"><?php echo esc_html( $s['kicker'] ); ?></div>
                    <h2 class="d"><?php echo $this->olox_rich( $s['title_html'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
                    <p style="margin-bottom:30px;"><?php echo $this->olox_rich( $s['body_html'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
                    <?php if ( ! empty( $s['cta_text'] ) ) : ?><a class="cta" href="<?php echo esc_url( $s['cta_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta_text'] ); ?></a><?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </section>
        <?php
        echo $this->olox_close(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }
}
