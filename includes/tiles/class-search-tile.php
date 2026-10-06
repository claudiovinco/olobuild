<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Search_Tile extends Olobuild_Tile_Base {

    protected $type     = 'search';
    protected $name     = 'Ricerca';
    protected $icon     = 'dashicons-search';
    protected $category = 'navigation';
    protected $defaults = [
        'preset' => 'custom',
        'placeholder'        => 'Cerca...',
        'style'              => 'default',
        'size'               => 'medium',
        'show_icon'          => true,
        'icon_position'      => 'left',
        'show_button'        => false,
        'button_text'        => 'Cerca',
        'button_style'       => 'filled',
        'full_width'         => true,
        'max_width'          => '',
        'alignment'          => 'center',
        'bg_color'           => 'var(--olo-color-light, #FFFFFF)',
        'text_color'         => 'var(--olo-color-text, #374151)',
        'placeholder_color'  => 'var(--olo-color-text-faint, #9CA3AF)',
        'icon_color'         => 'var(--olo-color-text-soft, #6B7280)',
        'border_color'       => 'var(--olo-color-border, #E5E7EB)',
        'border_width'       => '1',
        'border_radius'      => '8',
        'focus_border_color' => '',
        'button_bg'          => '',
        'button_color'       => 'var(--olo-color-light, #FFFFFF)',
        'button_radius'      => '8',
        'input_shadow'       => false,
        'focus_shadow'       => true,
        'animated_placeholder' => false,
        'placeholder_words'  => '',
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
        $uid = 'olo-srch-' . wp_rand( 10000, 99999 );

        $style       = in_array( $s['style'], [ 'default', 'minimal', 'pill', 'underline', 'hero', 'floating' ], true ) ? $s['style'] : 'default';
        $size        = in_array( $s['size'], [ 'small', 'medium', 'large' ], true ) ? $s['size'] : 'medium';
        $placeholder = esc_attr( $s['placeholder'] ?: 'Cerca...' );
        $show_icon   = ! empty( $s['show_icon'] );
        $icon_pos    = $s['icon_position'] === 'right' ? 'right' : 'left';
        $show_btn    = ! empty( $s['show_button'] );
        $btn_text    = esc_html( $s['button_text'] ?: 'Cerca' );
        $btn_style   = in_array( $s['button_style'], [ 'filled', 'outline', 'icon-only' ], true ) ? $s['button_style'] : 'filled';
        $full_width  = ! empty( $s['full_width'] );
        $max_width   = absint( $s['max_width'] );
        $alignment   = in_array( $s['alignment'], [ 'left', 'center', 'right' ], true ) ? $s['alignment'] : 'center';

        // TOKEN-FIRST: neutri → token tema; accento/focus → primary (era #e1474f off-brand)
        $bg_c          = $this->safe_color_css( $s['bg_color'] ) ?: '#FFFFFF';
        $text_c        = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text, #374151)';
        $ph_c          = $this->safe_color_css( $s['placeholder_color'] ) ?: 'var(--olo-color-text-faint, #94a3b8)';
        $icon_c        = $this->safe_color_css( $s['icon_color'] ) ?: 'var(--olo-color-text-soft, #6b7280)';
        $border_c      = $this->safe_color_css( $s['border_color'] ) ?: 'var(--olo-color-border, #e5e7eb)';
        $border_w      = absint( $s['border_width'] ) . 'px';
        $focus_c       = $this->safe_color_css( $s['focus_border_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $btn_bg        = $this->safe_color_css( $s['button_bg'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $btn_color     = $this->safe_color_css( $s['button_color'] ) ?: '#FFFFFF';
        $btn_radius    = $this->build_border_radius_css( $s["button_radius"] );
        $btn_radius_hover_css = Olobuild_Tile_Utils::radius_force_css( $s['button_radius_hover'] ?? null );
        $input_shadow  = ! empty( $s['input_shadow'] );
        $focus_shadow  = ! empty( $s['focus_shadow'] );

        // Radius
        $radius = Olobuild_Tile_Utils::radius_int( $s['border_radius'] ) . 'px';
        if ( $style === 'pill' ) $radius = '50px';
        if ( $style === 'hero' ) $radius = '16px';

        // Size map
        $sizes = [
            'small'  => [ 'fs' => '13px', 'pad' => '8px 12px' ],
            'medium' => [ 'fs' => '15px', 'pad' => '12px 16px' ],
            'large'  => [ 'fs' => '18px', 'pad' => '16px 20px' ],
        ];
        $sz = $sizes[ $size ] ?? $sizes['medium'];
        if ( $style === 'hero' ) {
            $sz = [ 'fs' => '20px', 'pad' => '18px 20px' ];
        }

        // Animated placeholder
        $anim_ph = ! empty( $s['animated_placeholder'] );
        $ph_words = [];
        if ( $anim_ph ) {
            $raw = is_array( $s['placeholder_words'] ) ? implode( "\n", $s['placeholder_words'] ) : (string) $s['placeholder_words'];
            $ph_words = array_filter( array_map( 'trim', explode( "\n", $raw ) ) );
        }

        // Bordo del campo: UN controllo, «Bordo» (chiave `border`, con hover ed effetti), che il
        // ponte legacy tiene in sincronia con border_width/border_color. Prima la stessa chiave
        // era offerta da due controlli e disegnata due volte: sul campo (dalle chiavi piatte) e
        // sul contenitore attorno (dall'oggetto), un rettangolo fuori dalla pillola. Ora va solo
        // sul campo: l'oggetto completo (lati, stile) quando è attivo, se no le chiavi piatte.
        $field_border  = $this->build_border_css( $s['border'] ?? [] );
        $border_decl   = $field_border !== '' ? $field_border : "border:{$border_w} solid {$border_c};";

        // Build form/wrapper styles
        $form_css = "display:flex;align-items:center;border-radius:{$radius};overflow:hidden;transition:all 0.2s ease;";
        if ( $style === 'underline' ) {
            $form_css = "display:flex;align-items:center;border-bottom:2px solid {$border_c};transition:all 0.2s ease;";
        } elseif ( $style === 'minimal' ) {
            $form_css .= "background:transparent;{$border_decl}";
        } elseif ( $style === 'floating' ) {
            // Flottante = senza filo; un Bordo scelto apposta però si vede (prima finiva sul contenitore).
            $form_css .= "background:{$bg_c};" . ( $field_border !== '' ? $field_border : 'border:none;' ) . 'box-shadow:0 4px 20px rgba(0,0,0,0.08),0 1px 4px rgba(0,0,0,0.04);';
        } elseif ( $style === 'hero' ) {
            $form_css .= "background:{$bg_c};{$border_decl}box-shadow:0 8px 32px rgba(0,0,0,0.1),0 2px 8px rgba(0,0,0,0.05);";
        } else {
            $form_css .= "background:{$bg_c};{$border_decl}";
        }
        if ( $input_shadow && ! in_array( $style, [ 'floating', 'hero', 'underline' ], true ) ) {
            $form_css .= 'box-shadow:0 1px 3px rgba(0,0,0,0.06),0 1px 2px rgba(0,0,0,0.04);';
        }

        $wrapper_css = '';
        if ( ! $full_width ) {
            $flex_map = [ 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' ];
            $wrapper_css = "display:flex;justify-content:" . ( $flex_map[ $alignment ] ?? 'center' ) . ";";
            // «Larghezza massima» sul campo: prima era scritta sul contenitore e la riga sopra la
            // sovrascriveva subito (= invece di .=), il controllo non faceva niente.
            if ( $max_width ) {
                $form_css .= "box-sizing:border-box;width:100%;max-width:{$max_width}px;";
            }
        }

        // Bordo in hover ed effetti del bordo: sul campo, come il bordo. Le regole stanno nel
        // blocco <style> insieme a quelle del campo (non più in linea, dove nessun :hover né
        // :focus-within poteva scavalcarle: anche il colore del bordo al focus non arrivava).
        // Quando l'oggetto Bordo non disegna (tile salvate prima del controllo unico: lati a 0,
        // il filo viene da border_width/border_color), l'hover parte da QUEL filo, e i lati a 0
        // dell'hover, copiati dalla base a zero che il controllo mostrava, non lo cancellano:
        // scegliere il solo colore hover dava `border:0px` e al passaggio il filo spariva.
        $hover_base = $s['border'] ?? [];
        $hover_val  = is_array( $s['border_hover'] ?? null ) ? $s['border_hover'] : [];
        if ( $field_border === '' ) {
            if ( $style !== 'floating' ) {
                $bw         = absint( $s['border_width'] );
                $hover_base = [ 'top' => $bw, 'right' => $bw, 'bottom' => $bw, 'left' => $bw, 'style' => 'solid', 'color' => $border_c ];
            }
            $lati_h = 0;
            foreach ( [ 'top', 'right', 'bottom', 'left' ] as $lato ) {
                $lati_h += intval( $hover_val[ $lato ] ?? 0 );
            }
            if ( $lati_h === 0 ) {
                unset( $hover_val['top'], $hover_val['right'], $hover_val['bottom'], $hover_val['left'] );
            }
        }
        $dur_h = intval( $s['border_hover_duration'] ?? 300 );
        if ( $style === 'underline' ) {
            // Sottolineato: il filo è solo quello sotto, l'hover ne cambia il colore (prima la
            // regola finiva sul contenitore; spostata sul campo era scartata e il toggle Hover
            // del Bordo non faceva niente). :focus-within viene dopo e il colore del focus vince.
            $hc           = $this->safe_color_css( (string) ( $hover_val['color'] ?? '' ) );
            $border_hover = $hc !== ''
                ? [ 'decls' => "border-bottom-color:{$hc};", 'transition' => 'border-color ' . max( 50, $dur_h ) . 'ms ease' ]
                : [ 'decls' => '', 'transition' => '' ];
        } else {
            $border_hover = $this->build_border_hover_props( $hover_base, $hover_val, $dur_h );
        }
        if ( $border_hover['transition'] !== '' ) {
            $form_css .= 'transition:all 0.2s ease,' . $border_hover['transition'] . ';';
        }

        $icon_svg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr( $icon_c ) . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>';

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: colours via the safe_color_css() whitelist (with fixed var() fallbacks), radii/widths via radius_int()/absint(), the field border via build_border_css()/build_border_hover_props(), button radius via the absint-built Olobuild_Tile_Utils::radius_force_css(); $uid is internally generated. ?>
        <style>
        .<?php echo $uid; ?> .olo-srch-form { <?php echo $form_css; ?> }
        <?php if ( $border_hover['decls'] !== '' ) : ?>.<?php echo $uid; ?> .olo-srch-form:hover { <?php echo $border_hover['decls']; ?> }<?php endif; ?>
        .<?php echo $uid; ?> input::placeholder { color: <?php echo $ph_c; ?>; }
        .<?php echo $uid; ?> input:focus { outline: none; }
        <?php if ( $focus_shadow ) : ?>
        .<?php echo $uid; ?> .olo-srch-form:focus-within {
            <?php if ( $style !== 'underline' ) : ?>
            border-color: <?php echo $focus_c; ?>;
            box-shadow: 0 0 0 3px color-mix(in srgb, <?php echo $focus_c; ?> 30%, transparent);
            <?php else : ?>
            border-bottom-color: <?php echo $focus_c; ?>;
            <?php endif; ?>
        }
        <?php endif; ?>
        /* a11y: anello di focus visibile da tastiera sul pulsante */
        .<?php echo $uid; ?> button:focus-visible {
            outline: none;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
        }
        <?php if ( $btn_radius_hover_css !== '' ) : ?>.<?php echo $uid; ?> button[type=submit]{transition:border-radius 400ms cubic-bezier(.4,0,.2,1) !important}.<?php echo $uid; ?> button[type=submit]:hover{border-radius:<?php echo $btn_radius_hover_css; ?> !important}<?php endif; ?>
        <?php
        // Raggio del campo in hover. Il modulo ha già `transition:all 0.2s` (e quella del bordo in
        // hover): la durata scelta passa con !important e le riprende, perché vince UNA transition
        // (la voce border-radius, dopo, prevale su `all`).
        $radius_h = $style !== 'underline' ? Olobuild_Tile_Utils::radius_hover( $s, 'border_radius_hover' ) : null;
        if ( $radius_h ) : ?>.<?php echo $uid; ?> .olo-srch-form{transition:all 0.2s ease, <?php echo ( $border_hover['transition'] !== '' ? $border_hover['transition'] . ', ' : '' ) . $radius_h['transition']; ?> !important}.<?php echo $uid; ?> .olo-srch-form:hover{border-radius:<?php echo $radius_h['css']; ?> !important}<?php endif; ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-search <?php echo esc_attr( $uid ); ?> olo-srch-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>"<?php if ( $wrapper_css ) echo ' style="' . esc_attr( $wrapper_css ) . '"'; ?>>
            <form class="olo-srch-form olo-casella" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search">
                <?php if ( $show_icon && $icon_pos === 'left' ) : ?>
                <span style="display:flex;align-items:center;flex-shrink:0;padding-left:<?php echo $style === 'hero' ? '20px' : '14px'; ?>"><?php echo $icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed px literal from the ternary; SVG assembled above from static markup with esc_attr()'d stroke colour ?></span>
                <?php endif; ?>

                <input type="search" name="s" placeholder="<?php echo $placeholder; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- placeholder/aria-label escaped via esc_attr() at assignment above; colour via the safe_color_css() whitelist; font-size/padding from the fixed size map; data attr esc_attr()'d inline ?>" aria-label="<?php echo $placeholder; ?>" style="flex:1;min-width:0;background:transparent;border:none;outline:none;color:<?php echo $text_c; ?>;font-size:<?php echo $sz['fs']; ?>;padding:<?php echo $sz['pad']; ?>;width:100%;"<?php if ( $anim_ph && ! empty( $ph_words ) ) echo ' data-olo-anim-ph="' . esc_attr( wp_json_encode( array_values( $ph_words ) ) ) . '"'; ?> />

                <?php if ( $show_icon && $icon_pos === 'right' && ! $show_btn ) : ?>
                <span style="display:flex;align-items:center;flex-shrink:0;padding-right:<?php echo $style === 'hero' ? '20px' : '14px'; ?>"><?php echo $icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed px literal from the ternary; SVG assembled above from static markup with esc_attr()'d stroke colour ?></span>
                <?php endif; ?>

                <?php if ( $show_btn ) :
                    $bp = $style === 'hero' ? '18px 32px' : ( $btn_style === 'icon-only' ? $sz['pad'] : explode( ' ', $sz['pad'] )[0] . ' 24px' );
                    $br = $style === 'pill' ? '50px' : $btn_radius;
                    $btn_css = "display:inline-flex;align-items:center;justify-content:center;gap:6px;cursor:pointer;font-weight:600;font-size:{$sz['fs']};padding:{$bp};border:none;border-radius:{$br};margin:4px;transition:all 0.2s;";
                    if ( $btn_style === 'outline' ) {
                        $btn_css .= "background:transparent;color:{$btn_bg};border:2px solid {$btn_bg};";
                    } else {
                        $btn_css .= "background:{$btn_bg};color:{$btn_color};";
                    }
                ?>
                <button type="submit" style="<?php echo esc_attr( $btn_css ); ?>">
                    <?php if ( $btn_style === 'icon-only' ) : ?>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <?php else : ?>
                    <?php echo $btn_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped via esc_html() at assignment above ?>
                    <?php endif; ?>
                </button>
                <?php endif; ?>
            </form>
        </div>
        <?php if ( $anim_ph && ! empty( $ph_words ) ) : ?>
        <script>
        (function(){
            var inp = document.querySelector('.<?php echo esc_js( $uid ); ?> input[data-olo-anim-ph]');
            if(!inp) return;
            var words = JSON.parse(inp.getAttribute('data-olo-anim-ph'));
            if(!words.length) return;
            var i = 0;
            setInterval(function(){
                i = (i + 1) % words.length;
                inp.setAttribute('placeholder', words[i]);
            }, 2500);
        })();
        </script>
        <?php endif; ?>
        <?php
        // Effetti del bordo (neon, gradiente) sul campo, dove sta il bordo; il bordo e il suo
        // hover sono già nel blocco <style> del campo, il contenitore non ne disegna un secondo.
        $border_effect_css = $this->build_border_effect_css( ".{$uid} .olo-srch-form", $s['border'] ?? [], $s );
        if ( $border_effect_css ) {
            echo '<style>' . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_effect_css() from sanitized settings; $uid is internally generated
        }
        return ob_get_clean();
    }
}
