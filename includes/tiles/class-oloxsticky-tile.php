<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * OLOX Sticky — sezioni sticky scroll-driven del design OLOtheme:
 * - assembler: "il sito si monta da solo" — browser mockup coi blocchi tile che
 *   entrano in scena una fase per volta (pagina build, 340vh).
 * - day: "una giornata col motore" — orario gigante + slot che si confermano
 *   con timbro Confermato man mano che l'agenda si riempie (booking, 380vh).
 */
class Olobuild_OloxSticky_Tile extends Olobuild_Olox_Base_Tile {

    protected $type     = 'oloxsticky';
    protected $name     = 'OLOX — Sezione sticky (assembler/day)';
    protected $icon     = 'dashicons-editor-insertmore';
    protected $category = 'marketing';
    protected $defaults = [
        'accent'     => 'build',
        'variant'    => 'assembler',
        'anchor'     => 'cantiere',
        'kicker'     => 'Il cantiere',
        // assembler
        'browser_url' => 'https://il-tuo-sito.it, costruito con OLObuild',
        'asm_hint'    => '▼ continua a scorrere',
        'asm_blocks'  => [
            [ 'text' => 'header + menu' ],
            [ 'text' => 'hero animato' ],
            [ 'text' => 'galleria media' ],
            [ 'text' => 'form builder' ],
            [ 'text' => 'footer' ],
        ],
        'asm_steps'   => [
            [ 'text' => 'Scrolla: il sito si <em>monta da solo</em>.' ],
            [ 'text' => 'Fase 1 · <em>header</em> e menu al loro posto.' ],
            [ 'text' => 'Fase 2, l’<em>hero</em> animato entra in scena.' ],
            [ 'text' => 'Fase 3, la <em>galleria</em> aggancia i media.' ],
            [ 'text' => 'Fase 4, il <em>form</em> raccoglie contatti.' ],
            [ 'text' => 'Fase 5 · <em>footer</em>: sito consegnato. ~1h30.' ],
        ],
        // day
        'day_label'  => 'agenda riempita',
        'day_hint'   => 'scrolla per far passare le ore',
        'day_stamp'  => 'Confermato',
        'day_slots'  => [
            [ 'hh' => '09:00', 'what' => 'Visita immobile, via Verdi 8', 'who' => 'real estate' ],
            [ 'hh' => '10:30', 'what' => 'Consulenza fiscale, Studio B.', 'who' => 'appuntamenti' ],
            [ 'hh' => '12:00', 'what' => 'Check-in camera Doppia Nord', 'who' => 'accommodation' ],
            [ 'hh' => '13:00', 'what' => 'Tavolo 4, pranzo ×2', 'who' => 'ristorante' ],
            [ 'hh' => '15:30', 'what' => 'Noleggio e-bike, 3 ore', 'who' => 'rentals' ],
            [ 'hh' => '17:00', 'what' => 'Estetica, slot 45 min', 'who' => 'appuntamenti' ],
            [ 'hh' => '19:00', 'what' => 'Workshop serale, 24 posti', 'who' => 'eventi' ],
            [ 'hh' => '20:30', 'what' => 'Tavolo 12, cena ×6 (caparra)', 'who' => 'ristorante' ],
        ],
    ];

    public function render( $settings, $style = [] ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $this->olox_assets();
        $accent = $this->olox_color( $s['accent'] );
        $anchor = sanitize_html_class( (string) $s['anchor'] );

        ob_start();
        echo $this->olox_open( '', '--c:' . $accent ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        if ( ( $s['variant'] ?? 'assembler' ) === 'day' ) :
            $slots = [];
            foreach ( $this->olox_items( $s, 'day_slots' ) as $sl ) {
                $slots[] = [ (string) ( $sl['hh'] ?? '' ), (string) ( $sl['what'] ?? '' ), (string) ( $sl['who'] ?? '' ) ];
            }
            ?>
            <div class="ox-dayrail" data-olox="dayrail" data-slots="<?php echo esc_attr( wp_json_encode( $slots ) ); ?>" data-stamp="<?php echo esc_attr( $s['day_stamp'] ); ?>">
                <div class="ox-dayview">
                    <div class="wrap day-grid"<?php echo $anchor ? ' id="' . esc_attr( $anchor ) . '"' : ''; ?>>
                        <div>
                            <div class="k"><?php echo esc_html( $s['kicker'] ); ?></div>
                            <div class="daytime">08:<em>00</em></div>
                            <div class="dayocc"><?php echo esc_html( $s['day_label'] ); ?> <b class="ox-dayocc">0%</b> · <?php echo esc_html( $s['day_hint'] ); ?></div>
                        </div>
                        <div class="slots"></div>
                    </div>
                </div>
            </div>
        <?php else :
            $steps = [];
            foreach ( $this->olox_items( $s, 'asm_steps' ) as $st ) {
                $steps[] = $this->olox_rich( $st['text'] ?? '' );
            }
            $first = $steps ? $steps[0] : '';
            ?>
            <div class="ox-asmrail" data-olox="assembler" data-steps="<?php echo esc_attr( wp_json_encode( $steps ) ); ?>">
                <div class="ox-asmview">
                    <div class="wrap asm-grid"<?php echo $anchor ? ' id="' . esc_attr( $anchor ) . '"' : ''; ?>>
                        <div class="asm-copy">
                            <div class="k"><?php echo esc_html( $s['kicker'] ); ?></div>
                            <div class="step">fase <b class="ox-stepno">0</b> / <?php echo count( $this->olox_items( $s, 'asm_blocks' ) ); ?></div>
                            <div class="stepname"><?php echo $first; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- kses sopra. ?></div>
                            <div class="asm-hint"><?php echo esc_html( $s['asm_hint'] ); ?></div>
                        </div>
                        <div class="browser">
                            <div class="bar"><i></i><i></i><i></i><span class="url"><?php echo esc_html( $s['browser_url'] ); ?></span></div>
                            <div class="stage">
                                <?php $b = 0; foreach ( $this->olox_items( $s, 'asm_blocks' ) as $blk ) : ?>
                                <div class="blk" data-b="<?php echo (int) $b; ?>"><b>tile</b> <?php echo esc_html( $blk['text'] ?? '' ); ?></div>
                                <?php $b++; endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif;
        echo $this->olox_close(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }
}
