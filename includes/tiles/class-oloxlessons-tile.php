<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * OLOX Lessons — "le lezioni si sbloccano scendendo" (pagina tutor):
 * percorso verticale con linea tratteggiata, nodi numerati e card con velo
 * lucchetto che scompare quando la lezione entra nel viewport (via modulo xp).
 */
class Olobuild_OloxLessons_Tile extends Olobuild_Olox_Base_Tile {

    protected $type     = 'oloxlessons';
    protected $name     = 'OLOX — Percorso lezioni';
    protected $icon     = 'dashicons-welcome-learn-more';
    protected $category = 'marketing';
    protected $defaults = [
        'accent'     => 'tutor',
        'accent_custom' => '',
        'testi_pagina'  => 'chiari', // salvate prima: come prima; le nuove 'tema' (partenza)
        'anchor'     => 'lezioni',
        'kicker'     => 'Il percorso',
        'title_html' => 'Le lezioni si <em>sbloccano</em> scendendo',
        'lock_text'  => 'scendi per sbloccare',
        'items'      => [
            [ 'xp' => '+120 xp', 'title' => 'Corsi & lezioni', 'text_html' => 'Strutture di corso, lezioni ordinate, area allievi con i progressi di ciascuno. Il programma lo detti tu.' ],
            [ 'xp' => '+180 xp', 'title' => 'Quiz & gamification', 'text_html' => 'Quiz, mini-giochi, punti e badge. La motivazione fa parte del metodo, non è un plugin in più.' ],
        ],
    ];

    public function render( $settings, $style = [] ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $this->olox_assets();
        // Colore: uno dei 7 prodotti (chiave di olox_color), un ruolo del tema (valore CSS già pronto)
        // o «Personalizzato» (accent_custom). Prima si poteva scegliere solo fra i prodotti.
        $accent = 'custom' === $s['accent'] ? ( $this->safe_color_css( $s['accent_custom'] ) ?: 'var(--olo)' ) : $this->olox_color( $s['accent'] );
        // Titolo sulla pagina col testo del tema (.ox-tema in olox.css): nel crema del design scuro
        // spariva su un tema chiaro. «Chiari» tiene il design per le pagine e le sezioni scure.
        $tono   = 'tema' === $s['testi_pagina'] ? 'ox-tema' : '';
        $anchor = sanitize_html_class( (string) $s['anchor'] );

        ob_start();
        echo $this->olox_open( $tono, '--c:' . $accent ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- olox_open() passa classe e stile da esc_attr()
        ?>
        <section class="dsec" data-olox="xp"<?php echo $anchor ? ' id="' . esc_attr( $anchor ) . '"' : ''; ?>>
            <div class="wrap">
                <div class="head" style="margin-left:auto; margin-right:auto; text-align:center; max-width:640px;">
                    <?php if ( ! empty( $s['kicker'] ) ) : ?><div class="k" style="justify-content:center;"><?php echo esc_html( $s['kicker'] ); ?></div><?php endif; ?>
                    <?php if ( ! empty( $s['title_html'] ) ) : ?><h2 class="d"><?php echo $this->olox_rich( $s['title_html'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2><?php endif; ?>
                </div>
                <div class="path">
                    <?php $n = 0; foreach ( $this->olox_items( $s, 'items' ) as $it ) : $n++; ?>
                    <div class="lez"><span class="node"><?php echo (int) $n; ?></span>
                        <div class="box"><span class="xp"><?php echo esc_html( $it['xp'] ?? '' ); ?></span><h3><?php echo $this->olox_rich( $it['title'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>
                        <p><?php echo $this->olox_rich( $it['text_html'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
                        <div class="lockveil"><?php echo $this->render_icon_html( 'lock', 1 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- icona del set ?> <span><?php echo esc_html( $s['lock_text'] ); ?></span></div></div></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
        echo $this->olox_close(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }
}
