<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Quotation_Tile extends Olobuild_Tile_Base {

    protected $type     = 'quotation';
    protected $name     = 'Citazione';
    protected $icon     = 'dashicons-format-quote';
    protected $category = 'text';
    protected $defaults = [
        'preset' => 'custom',
        'content'   => 'La vita è ciò che ti succede mentre sei occupato a fare altri progetti.',
        'author'    => 'John Lennon',
        'style'     => 'default',
        'alignment' => 'left',
        // Tipografia e colori di citazione e autore ('' = quelli di UIkit, come prima).
        'content_font_family' => '',
        'content_font_size'   => '',
        'content_font_weight' => '',
        'content_font_style'  => '',
        'content_color'       => '',
        'author_font_family'  => '',
        'author_font_size'    => '',
        'author_font_weight'  => '',
        'author_color'        => '',
            'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'content',   'type' => 'textarea', 'label' => 'Quote' ],
            [ 'key' => 'author',    'type' => 'text',     'label' => 'Author' ],
            [ 'key' => 'style',     'type' => 'select',   'label' => 'Style', 'options' => [
                'default' => 'Default',
                'footer'  => 'Footer Citation',
            ]],
            [ 'key' => 'alignment', 'type' => 'select',   'label' => 'Alignment', 'options' => [
                'left'   => 'Left',
                'center' => 'Center',
                'right'  => 'Right',
            ]],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $alignment   = in_array( $s['alignment'] ?? 'left', [ 'left', 'center', 'right' ], true ) ? $s['alignment'] : 'left';
        $align_class = 'uk-text-' . $alignment;
        $quot_uid = 'olo-quot-' . wp_unique_id();
        $preset   = sanitize_key( (string) ( $s['preset'] ?? 'custom' ) );

        // Selettore della tile più forte della regola generica dei <blockquote> dei testi
        // ricchi (frontend.css, `.olo-frontend-tile blockquote`: barra a sinistra di 3px),
        // che prima vinceva su tutto: barra a sinistra anche con la citazione centrata o a
        // destra, e lato sinistro del Bordo dell'inspector coperto dalla barra.
        // Due classi (0,2,0) battono «.olo-frontend-tile blockquote» (0,1,1).
        $qsel = ".olo-quotation.{$quot_uid}";
        $css  = '';
        if ( $alignment === 'center' || $preset === 'big-mark' ) {
            $css .= $qsel . '{border-left:0;padding-left:0}';
        } elseif ( $alignment === 'right' ) {
            $css .= $qsel . '{border-left:0;padding-left:0;border-right:3px solid currentColor;padding-right:.75em}';
        }

        // Tipografia e colori. La tile non ne aveva: restavano i grigi fissi di UIkit
        // (#333 e #999), illeggibili su uno sfondo scuro. Con uno stile tipografico
        // collegato famiglia e peso li governa lui (classe olo-typo-* sul contenitore).
        $tp_on  = sanitize_key( (string) ( $s['typography_preset'] ?? '' ) ) !== '';
        $c_col  = $this->safe_color_css( $s['content_color'] ?? '' );
        $a_col  = $this->safe_color_css( $s['author_color'] ?? '' );
        $c_decl = $this->decl_testo( $s, 'content', $tp_on );
        $a_decl = $this->decl_testo( $s, 'author', $tp_on );
        $c_st   = in_array( $s['content_font_style'] ?? '', [ 'normal', 'italic', 'oblique' ], true ) ? $s['content_font_style'] : '';
        if ( $c_st !== '' ) {
            $c_decl .= 'font-style:' . $c_st . ';';
        }
        if ( $c_col ) {
            // Sul blockquote: la barra (currentColor) segue il colore della citazione.
            $css .= $qsel . '{color:' . $c_col . '}';
            // Autore senza colore suo: lo stesso colore attenuato, leggibile su qualunque
            // fondo vada bene la citazione (il #999 di UIkit no).
            if ( ! $a_col ) {
                $a_col = 'color-mix(in srgb, ' . $c_col . ' 72%, transparent)';
            }
        }
        if ( $a_col ) {
            $a_decl .= 'color:' . $a_col . ';';
        }
        if ( $c_decl !== '' ) {
            $css .= $qsel . ' .olo-quot-content{' . $c_decl . '}';
        }
        if ( $a_decl !== '' ) {
            $css .= $qsel . ' .olo-quot-author,' . $qsel . ' footer{' . $a_decl . '}';
        }

        // Preset con una resa che le chiavi non sanno dare: la virgoletta grande e il
        // testo in sfumatura (che parte dal colore della citazione, se scelto).
        if ( $preset === 'big-mark' ) {
            $css .= $qsel . '::before{content:"\201C";display:block;height:.42em;margin-bottom:.2em;font-family:var(--olo-font-family-heading, Georgia, serif);font-size:4.5em;font-style:normal;font-weight:700;line-height:.85;color:var(--olo-color-primary, #e1474f)}';
        } elseif ( $preset === 'gradient-text' ) {
            $g1   = $c_col ?: 'var(--olo-color-primary, #e1474f)';
            $css .= $qsel . ' .olo-quot-content{background-image:linear-gradient(90deg,' . $g1 . ',var(--olo-color-accent, #f4a23b));-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;color:transparent}';
        }
        $content_plain = wp_strip_all_tags( $s['content'] ?? '' );
        $author_plain  = $s['author'] ?? '';
        list( $c_tfx_cls, $c_tfx_data ) = $this->tfx_attrs( $s, 'content', $content_plain );
        list( $a_tfx_cls, $a_tfx_data ) = $this->tfx_attrs( $s, 'author', $author_plain );

        ob_start();
        ?>
        <blockquote class="olo-quotation <?php echo $align_class; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $align_class from an in_array() whitelist; $quot_uid is internally generated ?> <?php echo $quot_uid; ?> olo-quot-preset-<?php echo esc_attr( $preset ); ?>">
            <p class="olo-quot-content<?php echo $c_tfx_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); content is esc_html()'d (nl2br only adds <br /> tags) ?>"<?php echo $c_tfx_data; ?>><?php echo nl2br( esc_html( $content_plain ) ); ?></p>
            <?php if ( ! empty( $s['author'] ) ) : ?>
                <?php if ( $s['style'] === 'footer' ) : ?>
                    <footer><cite class="olo-quot-author<?php echo $a_tfx_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); author is esc_html()'d ?>"<?php echo $a_tfx_data; ?>><?php echo esc_html( $author_plain ); ?></cite></footer>
                <?php else : ?>
                    <p class="uk-text-meta olo-quot-author<?php echo $a_tfx_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); author is esc_html()'d ?>"<?php echo $a_tfx_data; ?>>&mdash; <?php echo esc_html( $author_plain ); ?></p>
                <?php endif; ?>
            <?php endif; ?>
        </blockquote>
        <?php
        // Dopo il blockquote, non prima: un <style> davanti fa scattare il margine
        // «* + blockquote» di UIkit.
        if ( $css !== '' ) echo '<style>' . $css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS built above from the internally generated uid, the in_array() whitelisted alignment/font-style, safe_color_css() colours, resolve_font_family()/font_weight_css() typography and absint() sizes
        $tfx_css = $this->tfx_css( $s, '.' . $quot_uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Text_Effects::css() from whitelisted effects, sanitized colors and integer timings
        $this->tfx_print_script();
                // Border system — stesso selettore forte (0,2,0) della barra: un Bordo con il
        // lato sinistro (o con i lati uguali, scorciatoia «border:») ora vince sulla barra,
        // che prima lo copriva. Un Bordo senza lato sinistro lascia la barra com'era.
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( $qsel, $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( $qsel, $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo "{$qsel}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $qsel is built from the internally generated uid
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }

    /**
     * Famiglia, dimensione e peso di una parte della citazione ('content' o 'author'),
     * dalle chiavi `<parte>_font_family`, `_font_size` (px), `_font_weight`.
     * Con uno stile tipografico collegato famiglia e peso restano a lui.
     *
     * @return string Dichiarazioni CSS, '' se nessuna è impostata.
     */
    private function decl_testo( $s, $parte, $tp_on ) {
        $d   = '';
        $fam = $tp_on ? '' : $this->resolve_font_family( (string) ( $s[ $parte . '_font_family' ] ?? '' ) );
        // «Mono» del controllo font (e il preset Retro Typewriter) salva il token senza
        // riserva, ma --olo-font-family-mono esiste solo se la tipografia globale lo
        // personalizza: altrimenti non risolve e il testo eredita il font del tema, niente
        // monospazio. Gli si dà la riserva del ruolo «mono».
        if ( preg_match( '/^var\(\s*--olo-font-family-mono\s*\)$/', $fam ) ) {
            $fam = $this->resolve_font_family( 'mono' );
        }
        if ( $fam !== '' ) {
            $d .= 'font-family:' . $fam . ';';
        }
        $size = absint( $s[ $parte . '_font_size' ] ?? 0 );
        if ( $size > 0 ) {
            $d .= 'font-size:' . $size . 'px;';
        }
        $peso = $tp_on ? '' : $this->font_weight_css( $s[ $parte . '_font_weight' ] ?? '' );
        if ( $peso !== '' ) {
            $d .= 'font-weight:' . $peso . ';';
        }
        return $d;
    }
}
