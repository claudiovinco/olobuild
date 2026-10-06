<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Shortcode_Tile extends Olobuild_Tile_Base {

    protected $type     = 'shortcode';
    protected $name     = 'Shortcode';
    protected $icon     = 'dashicons-shortcode';
    protected $category = 'dynamic';
    protected $defaults = [
        'shortcode_text'    => '[gallery]',
        'parse_shortcodes'  => true,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'shortcode_text',   'type' => 'textarea', 'label' => olobuild_t( 'Shortcode' ) ],
            [ 'key' => 'parse_shortcodes', 'type' => 'toggle',   'label' => olobuild_t( 'Esegui shortcode' ) ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $shortcode_text = trim( $s['shortcode_text'] );

        if ( empty( $shortcode_text ) ) {
            return $this->messaggio( esc_html( olobuild_t( 'Inserisci uno shortcode nell\'inspector.' ) ) );
        }

        $resa = '';
        if ( ! empty( $s['parse_shortcodes'] ) ) {
            // Molti shortcode stampano con echo invece di restituire: qui, fuori dal buffer
            // della tile, quel testo uscirebbe prima del div (e l'avviso qui sotto lo darebbe
            // per vuoto). Si raccoglie anche la stampa, nell'ordine in cui avviene.
            ob_start();
            $resa = do_shortcode( $shortcode_text );
            $resa = ob_get_clean() . $resa;
            // Nel canvas del builder uno shortcode che non produce nulla (il [gallery] di
            // partenza senza «ids», su una pagina senza immagini allegate) lasciava una tile
            // alta 0 px, senza dire perché. Lì si mostra un avviso; sul sito resta vuota.
            if ( ! empty( $s['_builder_mode'] ) && $this->resa_vuota( $resa ) ) {
                return $this->avviso_vuoto( $shortcode_text );
            }
        }

        ob_start();
        ?>
        <div class="olo-shortcode">
        <?php
            if ( ! empty( $s['parse_shortcodes'] ) ) {
                // Il testo viene sanificato con wp_kses_post al salvataggio per chi
                // non ha unfiltered_html (Olobuild_Rest_Api::sanitize_unfiltered_tile_fields).
                echo $resa; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- do_shortcode() output cannot be escaped without breaking shortcode markup; the raw text is sanitized with wp_kses_post() on save for users without unfiltered_html (Olobuild_Rest_Api::sanitize_unfiltered_tile_fields).
            } else {
                // Display as code without execution
                echo '<pre style="background:var(--olo-color-muted, #f3f4f6);padding:12px;border-radius:6px;overflow-x:auto;"><code>'
                   . esc_html( $shortcode_text )
                   . '</code></pre>';
            }
        ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Vero se lo shortcode non ha restituito niente (al più spazi e commenti HTML).
     * Un contenitore vuoto non conta come vuoto: molti shortcode (mappe, moduli caricati
     * da script) lo riempiono dopo, e l'avviso ne coprirebbe l'anteprima.
     */
    private function resa_vuota( $html ) {
        return trim( (string) preg_replace( '/<!--.*?-->/s', '', (string) $html ) ) === '';
    }

    /**
     * Avviso del canvas per lo shortcode che non produce nulla, con il motivo quando si
     * conosce: [gallery] senza «ids» mostra solo le immagini allegate alla pagina.
     */
    private function avviso_vuoto( $shortcode_text ) {
        $tag = preg_match( '/\[\s*([A-Za-z0-9_-]+)/', $shortcode_text, $m ) ? $m[1] : '';
        // Con «id» (galleria di un altro post) o «include» le immagini sono indicate: il
        // motivo specifico sarebbe falso, si ricade su quello generico.
        if ( $tag === 'gallery' && ! preg_match( '/\b(?:ids|include|id)\s*=/i', $shortcode_text ) ) {
            $motivo = olobuild_t( '[gallery] senza «ids» mostra le immagini allegate a questa pagina, e qui non ce ne sono. Indica le immagini, per esempio [gallery ids="12,34,56"].' );
        } else {
            $motivo = olobuild_t( 'Lo shortcode qui non produce nulla: controlla il nome e i parametri.' );
        }
        return $this->messaggio(
            '<code style="color:var(--olo-color-text, #374151);">' . esc_html( $shortcode_text ) . '</code><br>'
            . esc_html( $motivo )
        );
    }

    /**
     * Riquadro dei messaggi della tile (shortcode mancante o che non produce nulla).
     *
     * @param string $html Contenuto già escapato.
     */
    private function messaggio( $html ) {
        return '<div class="olo-shortcode olo-shortcode--vuoto" style="text-align:center;padding:20px;color:var(--olo-color-text-muted, #9ca3af);">'
             . $html
             . '</div>';
    }
}
