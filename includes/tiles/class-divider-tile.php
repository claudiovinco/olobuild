<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Divider_Tile extends Olobuild_Tile_Base {

    protected $type     = 'divider';
    protected $name     = 'Divisore';
    protected $icon     = 'dashicons-minus';
    protected $category = 'essential';
    protected $defaults = [
        'style'      => 'solid',
        'width'      => '100',
        'thickness'  => '1',
        'color'      => '',
        'alignment'  => 'center',
        'spacing'    => '16',
        'text'       => '',
        'text_color' => '',
        'text_size'  => '14',
        'icon_emoji' => '',
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
            [ 'key' => 'style',      'type' => 'select', 'label' => 'Style' ],
            [ 'key' => 'width',      'type' => 'range',  'label' => 'Width (%)' ],
            [ 'key' => 'thickness',  'type' => 'range',  'label' => 'Thickness' ],
            [ 'key' => 'color',      'type' => 'color',  'label' => 'Color' ],
            [ 'key' => 'alignment',  'type' => 'select', 'label' => 'Alignment' ],
            [ 'key' => 'spacing',    'type' => 'range',  'label' => 'Spacing' ],
            [ 'key' => 'text',       'type' => 'text',   'label' => 'Center Text' ],
            [ 'key' => 'text_color', 'type' => 'color',  'label' => 'Text Color' ],
            [ 'key' => 'text_size',  'type' => 'range',  'label' => 'Text Size' ],
            [ 'key' => 'icon_emoji', 'type' => 'text',   'label' => 'Center Emoji' ],
        ];
    }

    public function render( $settings, $style = [] ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $w       = min( max( absint( $s['width'] ), 10 ), 100 );
        $thick   = min( max( absint( $s['thickness'] ), 1 ), 10 );
        $spacing = min( max( absint( $s['spacing'] ), 0 ), 80 );
        $txt_sz  = min( max( absint( $s['text_size'] ), 10 ), 32 );
        $align_map = [ 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' ];
        $justify   = $align_map[ $s['alignment'] ] ?? 'center';

        $line_clr = $this->safe_color_css( $s['color'] ) ?: 'var(--olo-color-border, #e5e7eb)';
        $txt_clr  = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text-soft, #6b7280)';
        $has_center = ! empty( $s['text'] ) || ! empty( $s['icon_emoji'] );

        $style_type = $s['style'];
        $wrap_style = "display:flex;justify-content:{$justify};padding:{$spacing}px 16px;";

                $uid = 'olo-divider-' . wp_rand( 10000, 99999 );
ob_start();

        // Sfondo del tab Stile disegnato sul divisore (il contenitore resta trasparente).
        $sfondo = $this->sfondo_elemento( $style, $uid );
        echo '<div class="olo-divider ' . esc_attr( $uid ) . '" style="' . esc_attr( $wrap_style . $sfondo['css_con_livelli'] ) . '">';
        echo $sfondo['markup']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- background layers built by sfondo_elemento() from Olobuild_CSS_Builder::get_bg_html_markup() (esc_url/esc_attr inside) and a safe_color_css()-whitelisted overlay

        if ( $has_center ) {
            $this->render_center( $s, $w, $thick, $style_type, $line_clr, $txt_clr, $txt_sz );
        } elseif ( in_array( $style_type, [ 'wave', 'zigzag', 'dots', 'diamonds' ], true ) ) {
            $this->render_decorative( $style_type, $w, $thick, $line_clr );
        } elseif ( $style_type === 'shadow' ) {
            $this->render_shadow( $w, $thick, $line_clr );
        } elseif ( $style_type === 'fade' ) {
            $this->render_fade( $w, $thick, $line_clr );
        } elseif ( $style_type === 'gradient' ) {
            $this->render_gradient( $w, $thick, $line_clr );
        } else {
            // solid, dashed, dotted, double
            $radius = $thick > 1 ? 'border-radius:' . round( $thick / 2 ) . 'px;' : '';
            // Lo style passa intero da esc_attr(): safe_color_css() lascia passare le virgolette nella
            // riserva di un var() e dentro color-mix(), e un colore così chiudeva l'attributo.
            echo '<span style="' . esc_attr( 'display:block;width:' . (int) $w . '%;border:none;border-top:' . (int) $thick . 'px ' . $style_type . ' ' . $line_clr . ';' . $radius ) . '"></span>';
        }

        echo '</div>';

                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized border settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized border settings
        }
        return ob_get_clean();
    }

    private function render_center( $s, $w, $thick, $style_type, $line_clr, $txt_clr, $txt_sz ) {
        if ( $style_type === 'gradient' ) {
            $line_css = "display:block;flex:1;min-width:20px;border:none;height:{$thick}px;background:linear-gradient(90deg, transparent, {$line_clr}, transparent);";
        } else {
            $bstyle = in_array( $style_type, [ 'solid', 'dashed', 'dotted', 'double' ], true ) ? $style_type : 'solid';
            $line_css = "display:block;flex:1;min-width:20px;border:none;border-top:{$thick}px {$bstyle} {$line_clr};";
        }

        echo '<div style="display:flex;align-items:center;gap:12px;width:' . (int) $w . '%;">';
        echo '<span style="' . esc_attr( $line_css ) . '"></span>';
        echo '<span style="color:' . esc_attr( $txt_clr ) . ';font-size:' . (int) $txt_sz . 'px;white-space:nowrap;line-height:1.2;padding:0 4px;">';

        if ( ! empty( $s['icon_emoji'] ) ) {
            if ( preg_match( '/^[a-z][a-z0-9-]*$/', $s['icon_emoji'] ) ) {
                echo $this->render_icon_html( $s['icon_emoji'] ) . ' '; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- render_icon_html(): nome e attributi passati da esc_attr, SVG dalla libreria delle icone
            } else {
                echo esc_html( $s['icon_emoji'] ) . ' ';
            }
        }
        echo esc_html( $s['text'] );

        echo '</span>';
        echo '<span style="' . esc_attr( $line_css ) . '"></span>';
        echo '</div>';
    }

    private function render_gradient( $w, $thick, $line_clr ) {
        $radius = $thick > 1 ? 'border-radius:' . round( $thick / 2 ) . 'px;' : '';
        echo '<span style="' . esc_attr( 'display:block;width:' . (int) $w . '%;height:' . (int) $thick . 'px;background:linear-gradient(90deg, transparent, ' . $line_clr . ', transparent);' . $radius ) . '"></span>';
    }

    private function render_fade( $w, $thick, $line_clr ) {
        $radius = $thick > 1 ? 'border-radius:' . round( $thick / 2 ) . 'px;' : '';
        echo '<div style="' . esc_attr( 'width:' . (int) $w . '%;height:' . (int) $thick . 'px;background:linear-gradient(90deg, transparent, ' . $line_clr . ' 20%, ' . $line_clr . ' 80%, transparent);' . $radius ) . '"></div>';
    }

    private function render_shadow( $w, $thick, $line_clr ) {
        $blur1 = $thick * 3;
        $blur2 = $thick * 2;
        $radius = $thick > 1 ? 'border-radius:' . round( $thick / 2 ) . 'px;' : '';
        echo '<div style="width:' . (int) $w . '%;"><div style="' . esc_attr( 'height:' . (int) $thick . 'px;background:' . $line_clr . ';box-shadow:0 2px ' . (int) $blur1 . 'px ' . Olobuild_Tile_Utils::con_alfa( $line_clr, '40' ) . ', 0 1px ' . (int) $blur2 . 'px ' . Olobuild_Tile_Utils::con_alfa( $line_clr, '25' ) . ';' . $radius ) . '"></div></div>';
    }

    private function render_decorative( $style_type, $w, $thick, $line_clr ) {
        $svg_h = $thick * 3 + 12;
        echo '<div style="width:' . (int) $w . '%;display:flex;align-items:center;">';

        if ( $style_type === 'wave' ) {
            $amp = max( $thick * 2, 4 );
            $mid = 12;
            $d = "M 0 {$mid}";
            for ( $x = 0; $x < 1200; $x += 60 ) {
                $x15 = $x + 15; $x30 = $x + 30; $x45 = $x + 45; $x60 = $x + 60;
                $top = $mid - $amp; $bot = $mid + $amp;
                $d .= " C {$x15} {$top}, {$x30} {$top}, {$x30} {$mid}";
                $d .= " C {$x30} {$mid}, {$x45} {$bot}, {$x60} {$mid}";
            }
            echo '<svg viewBox="0 0 1200 24" preserveAspectRatio="none" style="width:100%;height:' . (int) $svg_h . 'px;display:block;">';
            echo '<path d="' . $d . '" fill="none" stroke="' . esc_attr( $line_clr ) . '" stroke-width="' . (int) $thick . '" stroke-linecap="round"/>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $d is SVG path data assembled above from integer loop arithmetic only; stroke esc_attr()'d and width cast inline
            echo '</svg>';

        } elseif ( $style_type === 'zigzag' ) {
            $amp = max( $thick * 2, 4 );
            $mid = 12;
            $d = "M 0 {$mid}";
            for ( $x = 0; $x < 1200; $x += 24 ) {
                $x12 = $x + 12; $x24 = $x + 24;
                $top = $mid - $amp;
                $d .= " L {$x12} {$top} L {$x24} {$mid}";
            }
            echo '<svg viewBox="0 0 1200 24" preserveAspectRatio="none" style="width:100%;height:' . (int) $svg_h . 'px;display:block;">';
            echo '<path d="' . $d . '" fill="none" stroke="' . esc_attr( $line_clr ) . '" stroke-width="' . (int) $thick . '" stroke-linejoin="round" stroke-linecap="round"/>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $d is SVG path data assembled above from integer loop arithmetic only; stroke esc_attr()'d and width cast inline
            echo '</svg>';

        } elseif ( $style_type === 'dots' ) {
            // Puntini e diamanti erano un <pattern> SVG dentro un viewBox di 1200 stirato con
            // preserveAspectRatio="none": in ogni colonna più stretta di 1200 px i cerchi diventavano
            // ovali e i rombi si schiacciavano. E l'id del motivo era lo stesso per tutti i divisori
            // della pagina: il secondo prendeva il motivo (e il colore) del primo. Ora il motivo è uno
            // sfondo di misura fissa ripetuto con «space»: forme sempre in proporzione, nessun id, e la
            // fila comincia e finisce con una forma intera.
            $r = min( $thick, 5 );
            $h = max( $svg_h, 12 );
            $css = 'display:block;width:100%;height:' . (int) $h . 'px;background-image:radial-gradient(circle closest-side,' . $line_clr . ' calc(100% - 0.5px),transparent 100%);background-size:24px ' . (int) ( 2 * $r ) . 'px;background-position:center;background-repeat:space no-repeat;';
            echo '<span aria-hidden="true" style="' . esc_attr( $css ) . '"></span>';

        } elseif ( $style_type === 'diamonds' ) {
            // Il rombo si disegna con una maschera (il colore resta un token CSS, che dentro un'immagine
            // SVG non si risolverebbe): cella 28×16 scalata all'altezza della riga, come prima.
            // Il tratto è in px veri (Spessore) e il rombo rientra di mezzo tratto con giunti tondi:
            // a tratto pari allo spessore in unità della cella, da 4 in su punte e lati uscivano dalla
            // cella e venivano tagliati (esagoni, poi ottagoni pieni). sprintf %F: punto decimale
            // anche con un locale che usa la virgola.
            $h      = max( $svg_h, 16 );
            $cell_w = (int) round( 28 * $h / 16 );
            $sw     = max( $thick, 1 ) * 16 / $h;
            $a      = $sw / 2 + 0.5;
            $punti  = sprintf( '14,%1$.2F %2$.2F,8 14,%3$.2F %1$.2F,8', $a, 28 - $a, 16 - $a );
            $rombo  = "<svg xmlns='http://www.w3.org/2000/svg' width='28' height='16' viewBox='0 0 28 16'><polygon points='" . $punti . "' fill='none' stroke='black' stroke-width='" . sprintf( '%.2F', $sw ) . "' stroke-linejoin='round'/></svg>";
            $mask   = 'url(data:image/svg+xml,' . rawurlencode( $rombo ) . ') center/' . $cell_w . 'px ' . (int) $h . 'px space no-repeat';
            $css    = 'display:block;width:100%;height:' . (int) $h . 'px;background:' . $line_clr . ';-webkit-mask:' . $mask . ';mask:' . $mask . ';';
            echo '<span aria-hidden="true" style="' . esc_attr( $css ) . '"></span>';
        }

        echo '</div>';
    }
}
