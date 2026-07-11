<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * OLOX Cards — sezione .dsec con testata (kicker + H2 con <em> + paragrafo)
 * e griglia di card nelle varianti del design OLOtheme:
 * - brick:  brickcard con numero grande e barra laterale (12 famiglie, build)
 * - ticket: biglietti con fori laterali, strappo e codice (6 verticali, booking)
 * - red:    schede "classified" 2 colonne con velo che si toglie (security)
 * - room:   stanze collegate da corridoi tratteggiati (tour)
 * - hs:     card hotspot con pallino ping (tour)
 * - dcard:  card scure generiche 3 colonne
 */
class Olobuild_OloxCards_Tile extends Olobuild_Olox_Base_Tile {

    protected $type     = 'oloxcards';
    protected $name     = 'OLOX — Sezione card';
    protected $icon     = 'dashicons-grid-view';
    protected $category = 'marketing';
    protected $defaults = [
        'accent'      => 'build',
        'variant'     => 'brick',
        'anchor'      => '',
        'kicker'      => 'La libreria',
        'title_html'  => '12 famiglie, posate come <em>mattoni</em>',
        'lead'        => 'Ogni famiglia arriva da sinistra e da destra, come in cantiere. 187 tile, un solo motore.',
        'head_center' => false,
        'items'       => [
            [ 'label' => '31', 'title' => 'WooCommerce', 'text_html' => 'Quickview, wishlist, comparazione, bundle, filtro AJAX, checkout multi-step.', 'extra' => '' ],
            [ 'label' => '22', 'title' => 'Booking', 'text_html' => 'Calendario disponibilità, picker, slot orari, reception olo-spaces.', 'extra' => '' ],
        ],
        'foot_html'   => '',
        'foot_cta'    => '',
        'foot_url'    => '',
        'section_bg'  => '',
    ];

    public function render( $settings, $style = [] ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $this->olox_assets();
        $accent  = $this->olox_color( $s['accent'] );
        $variant = $s['variant'] ?? 'dcard';
        $items   = $this->olox_items( $s, 'items' );
        $anchor  = sanitize_html_class( (string) $s['anchor'] );
        $sec_st  = '';
        if ( ! empty( $s['section_bg'] ) ) {
            $bgc = $this->safe_color_css( $s['section_bg'] );
            if ( $bgc ) { $sec_st = 'background:' . $bgc . '; backdrop-filter:blur(2px);'; }
        }

        ob_start();
        echo $this->olox_open( '', '--c:' . $accent ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
        <section class="dsec"<?php echo $anchor ? ' id="' . esc_attr( $anchor ) . '"' : ''; ?><?php echo $sec_st ? ' style="' . esc_attr( $sec_st ) . '"' : ''; ?>>
            <div class="wrap">
                <?php if ( ! empty( $s['kicker'] ) || ! empty( $s['title_html'] ) || ! empty( $s['lead'] ) ) : ?>
                <div class="head"<?php echo ! empty( $s['head_center'] ) ? ' style="margin-left:auto; margin-right:auto; text-align:center; max-width:640px;"' : ''; ?>>
                    <?php if ( ! empty( $s['kicker'] ) ) : ?><div class="k"<?php echo ! empty( $s['head_center'] ) ? ' style="justify-content:center;"' : ''; ?>><?php echo esc_html( $s['kicker'] ); ?></div><?php endif; ?>
                    <?php if ( ! empty( $s['title_html'] ) ) : ?><h2 class="d"><?php echo $this->olox_rich( $s['title_html'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2><?php endif; ?>
                    <?php if ( ! empty( $s['lead'] ) ) : ?><p><?php echo $this->olox_rich( $s['lead'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p><?php endif; ?>
                </div>
                <?php endif; ?>
                <?php echo $this->olox_grid( $variant, $items ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <?php if ( ! empty( $s['foot_html'] ) ) : ?>
                <p class="sub" style="margin-top:44px;"><?php echo $this->olox_rich( $s['foot_html'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
                <?php endif; ?>
                <?php if ( ! empty( $s['foot_cta'] ) ) : ?>
                <a class="cta" href="<?php echo esc_url( $s['foot_url'] ?: '#' ); ?>"><?php echo esc_html( $s['foot_cta'] ); ?></a>
                <?php endif; ?>
            </div>
        </section>
        <?php
        echo $this->olox_close(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }

    /** Griglia card per variante. Ogni item: label, title, text_html, extra. */
    private function olox_grid( $variant, $items ) {
        ob_start();
        switch ( $variant ) {
            case 'brick':
                ?>
                <div class="bricks" data-olox="bricks">
                    <?php foreach ( $items as $it ) : ?>
                    <div class="brickcard"><span class="n"><?php echo esc_html( $it['label'] ?? '' ); ?></span><h3><?php echo esc_html( $it['title'] ?? '' ); ?></h3><p><?php echo $this->olox_rich( $it['text_html'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p></div>
                    <?php endforeach; ?>
                </div>
                <?php
                break;
            case 'ticket':
                ?>
                <div class="tickets" data-olox="tickets">
                    <?php foreach ( $items as $it ) : ?>
                    <div class="ticket"><span class="tk"><?php echo esc_html( $it['label'] ?? '' ); ?></span><h3><?php echo esc_html( $it['title'] ?? '' ); ?></h3><p><?php echo $this->olox_rich( $it['text_html'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p><div class="rip"></div><div class="code"><span><?php echo esc_html( $it['extra'] ?? '' ); ?></span><i></i></div></div>
                    <?php endforeach; ?>
                </div>
                <?php
                break;
            case 'red':
                ?>
                <div class="redgrid" data-olox="redgrid">
                    <?php $d = 0; foreach ( $items as $it ) : ?>
                    <div class="redcard" style="--d:<?php echo esc_attr( number_format( $d, 2, '.', '' ) ); ?>s"><div class="veil">▮▮▮▮ <b>classified</b> ▮▮▮▮</div>
                        <span class="kk"><?php echo esc_html( $it['label'] ?? '' ); ?></span><h3><?php echo $this->olox_rich( $it['title'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>
                        <p><?php echo $this->olox_rich( $it['text_html'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p></div>
                    <?php $d += 0.15; endforeach; ?>
                </div>
                <?php
                break;
            case 'room':
                ?>
                <div class="rooms" data-olox="rooms">
                    <?php $n = count( $items ); $i = 0; foreach ( $items as $it ) : $i++;
                        $hl = ! empty( $it['extra'] ); ?>
                    <div class="room"<?php echo $hl ? ' style="border-color:color-mix(in srgb, var(--c) 60%, transparent);"' : ''; ?>><span class="rn"><?php echo esc_html( $it['label'] ?? '' ); ?></span><h3><?php echo $this->olox_rich( $it['title'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3><p><?php echo $this->olox_rich( $it['text_html'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p></div>
                    <?php if ( $i < $n ) : ?><div class="corridor"></div><?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <?php
                break;
            case 'hs':
                ?>
                <div class="hs-grid" data-olox="reveal">
                    <?php foreach ( $items as $it ) : ?>
                    <div class="hs rv"><span class="dot"></span><h3><?php echo $this->olox_rich( $it['title'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3><p><?php echo $this->olox_rich( $it['text_html'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p></div>
                    <?php endforeach; ?>
                </div>
                <?php
                break;
            default:
                ?>
                <div class="dgrid3" data-olox="reveal">
                    <?php foreach ( $items as $it ) : ?>
                    <div class="dcard rv"><?php if ( ! empty( $it['label'] ) ) : ?><span class="kk"><?php echo esc_html( $it['label'] ); ?></span><?php endif; ?><h3><?php echo $this->olox_rich( $it['title'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3><p><?php echo $this->olox_rich( $it['text_html'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p></div>
                    <?php endforeach; ?>
                </div>
                <?php
        }
        return ob_get_clean();
    }
}
