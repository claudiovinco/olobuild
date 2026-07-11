<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * OLOX Rail — il binario della Home Experience per la composizione A SEZIONI:
 * si mette in una sezione in cima alla pagina; a runtime raccoglie tutte le
 * fermate (tile OLOX Panel nelle sezioni successive) e le monta nel binario
 * orizzontale, numerandole. Porta con sé il chrome fisso (logo, lingue,
 * pallini di salto), la progress bar, l'hint, l'alone colorato, i credits e
 * la modale "olonica". Il runtime completo vive in assets/js/olox.js
 * (moduli railassemble + home).
 */
class Olobuild_OloxRail_Tile extends Olobuild_Olox_Base_Tile {

    protected $type     = 'oloxrail';
    protected $name     = 'OLOX — Binario Experience (rail)';
    protected $icon     = 'dashicons-slides';
    protected $category = 'marketing';
    protected $defaults = [
        'logo'          => '',
        'langs'         => null, // null = default IT/EN/FR/DE/ES
        // Modale "olonica"
        'op_kicker'     => 'olos · intero e parte',
        'op_title'      => 'La cellula <em>olonica</em>',
        'op_p1'         => 'Un <strong>olone</strong> è qualcosa che è insieme <strong>un tutto e una parte</strong>: completo da solo, più forte dentro un organismo. OLOtheme è costruito così, ogni prodotto è una cellula autonoma che funziona da sola, ma condivide telaio, dati e lingua con le altre.',
        'op_p2'         => 'Niente monolite: <strong>i prodotti si uniscono a seconda della battaglia</strong> da affrontare, e si sciolgono quando non servono.',
        'battles'       => [
            [ 'q' => 'Aprire un B&B', 'chips' => 'build,booking,lang' ],
            [ 'q' => 'Respingere un attacco', 'chips' => 'secur' ],
            [ 'q' => 'Vendere all’estero', 'chips' => 'build,lang' ],
            [ 'q' => 'Far visitare un immobile a distanza', 'chips' => 'tour,booking' ],
            [ 'q' => 'Portare i corsi online', 'chips' => 'tutor,booking,lang' ],
        ],
        'battle_names'  => [ 'build' => 'build', 'booking' => 'booking', 'lang' => 'lang', 'secur' => 'security', 'tour' => 'tour', 'tutor' => 'tutor' ],
        // Chrome
        'hint_desktop'  => 'Scrolla in basso',
        'hint_desktop2' => 'si va a destra',
        'hint_mobile'   => 'Scorri',
        'hint_mobile2'  => 'una fermata alla volta',
        'credits_html'  => 'OLOtheme by <a href="https://clod.eu" target="_blank" rel="noopener">clod.eu</a> | @2026 | sito introduttivo | <a href="mailto:info@olotheme.com">info@olotheme.com</a>',
    ];

    public function render( $settings, $style = [] ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $this->olox_assets();

        ob_start();
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $this->olox_open( 'oloxp-home' );
        echo '<div data-olox="railassemble home">';
        ?>
        <div class="chrome">
            <a class="logo" href="#"><?php if ( ! empty( $s['logo'] ) ) : ?><img src="<?php echo esc_url( $s['logo'] ); ?>" alt="OLOtheme" /><?php endif; ?></a>
            <?php echo $this->olox_langsw( $s['langs'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper interno già escapato. ?>
            <div class="jump"></div>
        </div>
        <div class="progress"><i></i></div>
        <div class="credits"><?php echo $this->olox_rich( $s['credits_html'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
        <div class="hint"><span class="hd"><?php echo esc_html( $s['hint_desktop'] ); ?><b>→</b><?php echo esc_html( $s['hint_desktop2'] ); ?></span><span class="hm"><?php echo esc_html( $s['hint_mobile'] ); ?><b>↓</b><?php echo esc_html( $s['hint_mobile2'] ); ?></span></div>
        <div class="ox-halo"></div>

        <div class="opb">
            <div class="opcard">
                <button class="opclose" type="button">✕</button>
                <div class="k"><?php echo esc_html( $s['op_kicker'] ); ?></div>
                <h3><?php echo $this->olox_rich( $s['op_title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>
                <p><?php echo $this->olox_rich( $s['op_p1'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
                <p><?php echo $this->olox_rich( $s['op_p2'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
                <div class="battles">
                    <?php foreach ( $this->olox_items( $s, 'battles' ) as $b ) : ?>
                    <div class="battle"><span class="bq"><?php echo esc_html( $b['q'] ?? '' ); ?></span><span class="bc"><?php
                        foreach ( array_filter( array_map( 'trim', explode( ',', (string) ( $b['chips'] ?? '' ) ) ) ) as $chip ) {
                            $names = is_array( $s['battle_names'] ) ? $s['battle_names'] : [];
                            $nm    = $names[ $chip ] ?? $chip;
                            echo '<i style="--pc:' . esc_attr( $this->olox_color( $chip ) ) . '">' . esc_html( $nm ) . '</i>';
                        }
                    ?></span></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="ox-rail">
            <div class="ox-view">
                <div class="ox-track"></div>
            </div>
        </div>
        <?php
        echo '</div>';
        echo $this->olox_close(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }
}
