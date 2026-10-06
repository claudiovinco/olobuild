<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Olobuild_Starrating_Tile extends Olobuild_Tile_Base {
    protected $type     = 'starrating';
    protected $name     = 'Valutazione';
    protected $icon     = 'dashicons-star-filled';
    protected $category = 'marketing';
    protected $defaults = [
        'preset' => 'custom',
        'rating'         => '4',
        'max_stars'      => '5',
        'star_size'      => '32',
        'star_color'     => '',
        'empty_color'    => '',
        'style'          => 'filled',
        // '' = dal preset: cuori con «Hearts Pink», diamanti con «Diamonds Luxury», stelle negli
        // altri. I due preset si chiamavano così ma disegnavano stelle.
        'shape'          => '',
        // Il punteggio in cifre («4,5 / 5») sotto i simboli: c'era sempre, senza modo di toglierlo.
        'show_value'     => true,
        'title'          => '',
        'subtitle'       => '',
        'title_color'    => '',
        'subtitle_color' => '',
        'alignment'      => 'center',
            'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() { return []; }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $max    = absint( $s['max_stars'] ) ?: 5;
        // Il voto non supera le stelle: con un voto salvato più alto (prima il cursore andava
        // sempre fino a 5) la tile scriveva «5 / 3» sotto tre stelle piene.
        $rating = max( 0, min( $max, floatval( $s['rating'] ) ) );
        $size   = absint( $s['star_size'] ) ?: 32;
        $clr    = $this->safe_color_css( $s['star_color'] ) ?: 'var(--olo-color-accent, #f4a23b)';
        $empty  = $this->safe_color_css( $s['empty_color'] ) ?: 'var(--olo-color-border, #E5E7EB)';
        $align  = in_array( $s['alignment'], ['left','center','right'], true ) ? $s['alignment'] : 'center';
        $stile  = in_array( $s['style'], [ 'filled', 'outline', 'rounded' ], true ) ? $s['style'] : 'filled';
        $star_d = $this->tracciato_simbolo( $s );

        // Punteggio in cifre con la virgola della lingua del sito («4,5 / 5», non «4.5 / 5»).
        $decimali   = round( $rating, 1 ) == round( $rating ) ? 0 : ( round( $rating, 2 ) == round( $rating, 1 ) ? 1 : 2 );
        $voto_testo = number_format_i18n( $rating, $decimali );
        $mostra_voto = ! isset( $s['show_value'] ) || filter_var( $s['show_value'], FILTER_VALIDATE_BOOLEAN );

        ob_start();
        ?>
        <?php $sr_uid = 'olo-sr-' . wp_unique_id(); ?>
        <div class="olo-starrating <?php echo esc_attr( $sr_uid ); ?> olo-sr-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" style="text-align:<?php echo esc_attr( $align ); ?>;padding:16px;">
            <?php
            list( $srt_cls, $srt_data ) = $this->tfx_attrs( $s, 'title', wp_strip_all_tags( $s['title'] ?? '' ) );
            list( $srs_cls, $srs_data ) = $this->tfx_attrs( $s, 'subtitle', wp_strip_all_tags( $s['subtitle'] ?? '' ) );
            ?>
            <?php if ( ! empty( $s['title'] ) ) : ?>
                <div class="olo-sr-title<?php echo $srt_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); colour validated by safe_color_css() whitelist or fixed var() fallback ?>" style="font-weight:600;margin-bottom:8px;color:<?php echo $this->safe_color_css($s['title_color']) ?: 'var(--olo-color-text, #374151)'; ?>;font-size:16px;"<?php echo $srt_data; ?>>
                    <?php echo esc_html( wp_strip_all_tags( $s['title'] ) ); ?>
                </div>
            <?php endif; ?>
            <?php // Il voto si legge anche senza il punteggio in cifre: i simboli sono un'unica immagine con l'etichetta. ?>
            <div style="display:inline-flex;gap:4px;" role="img" aria-label="<?php echo esc_attr( sprintf( olobuild_t( 'Valutazione: %1$s su %2$s' ), $voto_testo, number_format_i18n( $max ) ) ); ?>">
                <?php
                // Mezza stella: ceil() restituisce un float e il confronto stretto con
                // l'intero $i non era mai vero, così con 4.5 la quinta stella restava vuota.
                $half_at  = (int) ceil( $rating );
                $has_half = fmod( $rating, 1 ) > 0;
                for ( $i = 1; $i <= $max; $i++ ) :
                    $fill = $i <= floor($rating) ? $clr : $empty;
                    $is_half = $has_half && $i === $half_at;
                    ?>
                    <svg width="<?php echo (int) $size; ?>" height="<?php echo (int) $size; ?>" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <?php if ( $is_half ) :
                            // id legato alla tile: con due valutazioni nella pagina gli id
                            // «olo-half-5» si ripetevano. Col Contorno il primo path aveva due
                            // attributi fill e vinceva il primo: la stella si riempiva.
                            $half_id    = $sr_uid . '-half-' . $i;
                            ?>
                            <defs><clipPath id="<?php echo esc_attr( $half_id ); ?>"><rect x="0" y="0" width="12" height="24"/></clipPath></defs>
                            <path d="<?php echo $star_d; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $star_d is a hardcoded SVG path literal from tracciato_simbolo() ?>" <?php echo $this->attributi_simbolo( $stile, $empty ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributi costruiti da attributi_simbolo() con esc_attr() ?>/>
                            <path d="<?php echo $star_d; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $star_d is a hardcoded SVG path literal from tracciato_simbolo() ?>" <?php echo $this->attributi_simbolo( $stile, $clr ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributi costruiti da attributi_simbolo() con esc_attr() ?> clip-path="url(#<?php echo esc_attr( $half_id ); ?>)"/>
                        <?php else : ?>
                            <path d="<?php echo $star_d; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $star_d is a hardcoded SVG path literal from tracciato_simbolo() ?>" <?php echo $this->attributi_simbolo( $stile, $fill ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributi costruiti da attributi_simbolo() con esc_attr() ?>/>
                        <?php endif; ?>
                    </svg>
                <?php endfor; ?>
            </div>
            <?php if ( $mostra_voto ) : ?>
            <div style="margin-top:4px;font-size:13px;color:<?php echo $clr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- colour validated by safe_color_css() whitelist or fixed var() fallback ?>;font-weight:600;" aria-hidden="true">
                <?php echo esc_html( $voto_testo . ' / ' . number_format_i18n( $max ) ); ?>
            </div>
            <?php endif; ?>
            <?php if ( ! empty( $s['subtitle'] ) ) : ?>
                <div class="olo-sr-subtitle<?php echo $srs_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); colour validated by safe_color_css() whitelist or fixed var() fallback ?>" style="margin-top:4px;font-size:13px;color:<?php echo $this->safe_color_css($s['subtitle_color']) ?: 'var(--olo-color-text-faint, #94a3b8)'; ?>;"<?php echo $srs_data; ?>>
                    <?php echo esc_html( wp_strip_all_tags( $s['subtitle'] ) ); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $sr_uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Text_Effects::css() from whitelisted effects, sanitized colors and integer timings
        $this->tfx_print_script();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$sr_uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$sr_uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$sr_uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $sr_uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }

    /**
     * Tracciato del simbolo (viewBox 24×24, simmetrico sull'asse verticale: la mezza stella si
     * ritaglia a metà). «Simbolo» vuoto = quello del preset: «Hearts Pink» e «Diamonds Luxury»
     * ne portavano il nome ma disegnavano stelle.
     *
     * @param array $s Settings.
     * @return string
     */
    private function tracciato_simbolo( $s ) {
        $tracciati = [
            'star'    => 'M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z',
            'heart'   => 'M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z',
            'diamond' => 'M6 3h12l4 6-10 12L2 9z',
        ];
        $forma = (string) ( $s['shape'] ?? '' );
        if ( ! isset( $tracciati[ $forma ] ) ) {
            $dal_preset = [ 'hearts-pink' => 'heart', 'diamonds-luxury' => 'diamond' ];
            $forma      = $dal_preset[ (string) ( $s['preset'] ?? '' ) ] ?? 'star';
        }
        return $tracciati[ $forma ];
    }

    /**
     * Attributi di riempimento del simbolo per lo «Stile» scelto. «Arrotondato» era nel menu ma
     * disegnava come «Pieno»: ora il simbolo pieno ha un tratto dello stesso colore con gli
     * spigoli tondi (stroke-linejoin round), che smussa le punte senza cambiarne la sagoma.
     *
     * @param string $stile  'filled' | 'outline' | 'rounded'.
     * @param string $colore Colore già validato (safe_color_css o riserva var()).
     * @return string
     */
    private function attributi_simbolo( $stile, $colore ) {
        $c = esc_attr( $colore );
        if ( 'outline' === $stile ) {
            return 'fill="none" stroke="' . $c . '" stroke-width="1.5"';
        }
        if ( 'rounded' === $stile ) {
            return 'fill="' . $c . '" stroke="' . $c . '" stroke-width="2.5" stroke-linejoin="round"';
        }
        return 'fill="' . $c . '"';
    }
}
