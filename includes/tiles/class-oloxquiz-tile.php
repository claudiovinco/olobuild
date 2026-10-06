<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * OLOX Quiz — "un quiz vero, provalo" (pagina tutor): domanda Fraunces,
 * risposte cliccabili (giusta = verde + confetti + bonus XP alla barra fissa,
 * sbagliata = rossa con verdetto), verdetto mono sotto.
 */
class Olobuild_OloxQuiz_Tile extends Olobuild_Olox_Base_Tile {

    protected $type     = 'oloxquiz';
    protected $name     = 'OLOX — Quiz verifica';
    protected $icon     = 'dashicons-editor-help';
    protected $category = 'marketing';
    protected $defaults = [
        'accent'       => 'tutor',
        'accent_custom' => '',
        'testi_pagina' => 'chiari', // salvate prima: come prima; le nuove 'tema' (partenza)
        'anchor'       => 'quiz',
        'kicker'       => 'Verifica finale',
        'title_html'   => 'Un quiz <em>vero</em>, provalo',
        'question_html' => 'Dove vivono i tuoi corsi con <em>OLOtutor</em>?',
        'answers'      => [
            [ 'text' => 'Su un marketplace, in fila coi concorrenti', 'ok' => false ],
            [ 'text' => 'Sul mio WordPress, con i miei allievi e i miei dati', 'ok' => true ],
            [ 'text' => 'In un cloud di terzi, a canone mensile', 'ok' => false ],
        ],
        'hint'         => 'rispondi per guadagnare +90 xp',
        'ok_html'      => 'esatto · <b>+90 xp</b> · badge sbloccato',
        'ko_text'      => 'mmh, riprova: la risposta è nel nome della suite…',
        'bonus'        => 90,
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
        <section class="dsec"<?php echo $anchor ? ' id="' . esc_attr( $anchor ) . '"' : ''; ?>>
            <div class="wrap">
                <div class="head" style="margin-left:auto; margin-right:auto; text-align:center; max-width:640px;">
                    <?php if ( ! empty( $s['kicker'] ) ) : ?><div class="k" style="justify-content:center;"><?php echo esc_html( $s['kicker'] ); ?></div><?php endif; ?>
                    <?php if ( ! empty( $s['title_html'] ) ) : ?><h2 class="d"><?php echo $this->olox_rich( $s['title_html'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2><?php endif; ?>
                </div>
                <div class="quiz" data-olox="quiz"
                    data-bonus="<?php echo (int) $s['bonus']; ?>"
                    data-ok="<?php echo esc_attr( $this->olox_rich( $s['ok_html'] ) ); ?>"
                    data-ko="<?php echo esc_attr( $s['ko_text'] ); ?>">
                    <p class="q"><?php echo $this->olox_rich( $s['question_html'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
                    <div class="ans">
                        <?php foreach ( $this->olox_items( $s, 'answers' ) as $a ) : ?>
                        <button data-ok="<?php echo ! empty( $a['ok'] ) ? '1' : '0'; ?>" type="button"><?php echo esc_html( $a['text'] ?? '' ); ?></button>
                        <?php endforeach; ?>
                    </div>
                    <div class="verdict"><?php echo esc_html( $s['hint'] ); ?></div>
                </div>
            </div>
        </section>
        <?php
        echo $this->olox_close(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }
}
