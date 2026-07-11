<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * OLOX Pricing — "la gru cala il Pro": due lastre Free/Pro del design OLOtheme,
 * la Pro scende dall'alto appesa a un cavo tratteggiato con gancio.
 */
class Olobuild_OloxPricing_Tile extends Olobuild_Olox_Base_Tile {

    protected $type     = 'oloxpricing';
    protected $name     = 'OLOX — Pricing gru (Free/Pro)';
    protected $icon     = 'dashicons-money-alt';
    protected $category = 'marketing';
    protected $defaults = [
        'accent'      => 'build',
        'anchor'      => 'prezzi',
        'kicker'      => 'Due edizioni',
        'title_html'  => 'La gru cala il <em>Pro</em>',
        'free_kicker' => 'OLObuild · Free',
        'free_price'  => '€0',
        'free_per'    => 'per sempre · GPL · su WP.org',
        'free_items'  => [
            [ 'text_html' => '<strong>Oltre 100 tile nativi</strong> + form builder + dark mode' ],
            [ 'text_html' => 'Al livello dei <strong>builder Pro a pagamento</strong> della concorrenza' ],
            [ 'text_html' => '<strong>11</strong> effetti testo · <strong>36</strong> animazioni' ],
            [ 'text_html' => '<strong>OLOlang gratis</strong> il primo anno' ],
        ],
        'free_cta'    => 'Scarica Free',
        'free_url'    => './',
        'pro_kicker'  => 'OLObuild · Pro',
        'pro_price'   => '€29<em>*</em>',
        'pro_per'     => '*prezzo lancio · poi €59/anno',
        'pro_items'   => [
            [ 'text_html' => 'L’intera libreria: <strong>187 tile</strong>' ],
            [ 'text_html' => 'Animazioni complete + ricerca media <strong>8 provider</strong>' ],
            [ 'text_html' => '<strong>OLOlang a vita</strong> · supporto prioritario' ],
            [ 'text_html' => '<strong>30 giorni</strong> di rimborso, senza domande' ],
        ],
        'pro_cta'     => 'Passa a Pro',
        'pro_url'     => './',
    ];

    public function render( $settings, $style = [] ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $this->olox_assets();
        $accent = $this->olox_color( $s['accent'] );
        $anchor = sanitize_html_class( (string) $s['anchor'] );

        ob_start();
        echo $this->olox_open( '', '--c:' . $accent ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
        <section class="dsec"<?php echo $anchor ? ' id="' . esc_attr( $anchor ) . '"' : ''; ?> data-olox="proslab reveal">
            <div class="wrap">
                <div class="head">
                    <?php if ( ! empty( $s['kicker'] ) ) : ?><div class="k"><?php echo esc_html( $s['kicker'] ); ?></div><?php endif; ?>
                    <?php if ( ! empty( $s['title_html'] ) ) : ?><h2 class="d"><?php echo $this->olox_rich( $s['title_html'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2><?php endif; ?>
                </div>
                <div class="slabs">
                    <div class="slab rv">
                        <div class="k" style="margin-bottom:6px;"><?php echo esc_html( $s['free_kicker'] ); ?></div>
                        <div class="price"><?php echo $this->olox_rich( $s['free_price'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                        <div class="per"><?php echo esc_html( $s['free_per'] ); ?></div>
                        <ul>
                            <?php foreach ( $this->olox_items( $s, 'free_items' ) as $li ) : ?>
                            <li><?php echo $this->olox_rich( $li['text_html'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php if ( ! empty( $s['free_cta'] ) ) : ?><a class="cta ghost" href="<?php echo esc_url( $s['free_url'] ?: '#' ); ?>"><?php echo esc_html( $s['free_cta'] ); ?></a><?php endif; ?>
                    </div>
                    <div class="cablewrap">
                        <div class="slab pro">
                            <div class="cable"></div><div class="hook"></div>
                            <div class="k" style="margin-bottom:6px;"><?php echo esc_html( $s['pro_kicker'] ); ?></div>
                            <div class="price"><?php echo $this->olox_rich( $s['pro_price'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                            <div class="per"><?php echo esc_html( $s['pro_per'] ); ?></div>
                            <ul>
                                <?php foreach ( $this->olox_items( $s, 'pro_items' ) as $li ) : ?>
                                <li><?php echo $this->olox_rich( $li['text_html'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if ( ! empty( $s['pro_cta'] ) ) : ?><a class="cta" href="<?php echo esc_url( $s['pro_url'] ?: '#' ); ?>"><?php echo esc_html( $s['pro_cta'] ); ?></a><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php
        echo $this->olox_close(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }
}
