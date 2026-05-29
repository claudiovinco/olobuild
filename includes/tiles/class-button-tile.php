<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Button_Tile extends Olo_Tile_Base {

    protected $type     = 'button';
    protected $name     = 'Pulsante';
    protected $icon     = 'dashicons-button';
    protected $category = 'essential';
    protected $defaults = [
        'text'               => 'Clicca qui',
        'url'                => '#',
        'target'             => '_self',
        'alignment'          => 'center',
        'full_width'         => false,
        'bg'                 => [ 'type' => 'none' ],
        'bg_color'           => '',
        'text_color'         => '',
        'border_radius'      => '6',
        'tile_padding'       => [ 'top' => 14, 'right' => 32, 'bottom' => 14, 'left' => 32 ],
        'padding_x'          => '32',
        'padding_y'          => '14',
        'font_size'          => '16',
        'font_weight'        => '600',
        'letter_spacing'     => '0',
        'text_transform'     => 'none',
        'icon'               => '',
        'icon_position'      => 'before',
        'icon_spacing'       => '8',
        'border_width'       => '0',
        'border_color'       => '',
        'shadow'             => 'none',
        'hover_bg_color'     => '',
        'hover_text_color'   => '',
        'hover_border_color' => '',
        'hover_shadow'       => '',
        'hover_effect'       => 'lift',
        'hover_image'             => '',
        'hover_video'             => '',
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
        return [];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-btn-' . wp_rand( 10000, 99999 );

        // Alignment
        $align_class = 'uk-text-' . ( in_array( $s['alignment'], [ 'left', 'center', 'right' ], true ) ? $s['alignment'] : 'center' );

        // Colors
        $bg = $this->safe_color_css( $s['bg_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $fg = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-on-primary, var(--olo-color-primary-contrast, #ffffff))';

        // Sfondo creativo (bg unificato) — se settato (type !== 'none') sovrascrive il
        // bg_color tinta unita usando lo stesso CSS Builder di section/row/iconbox.
        // Per type 'video' e 'gallery' aggiungiamo anche markup HTML dentro il button.
        $bg_creative_css  = '';
        $bg_creative_html = '';
        $bg_obj = $s['bg'] ?? [ 'type' => 'none' ];
        if ( is_array( $bg_obj ) && ( $bg_obj['type'] ?? 'none' ) !== 'none' && class_exists( 'Olo_CSS_Builder' ) ) {
            $cssb = new Olo_CSS_Builder();
            $bg_creative_css = $cssb->get_bg_inline_css( $bg_obj );
            if ( $bg_creative_css && ! str_ends_with( $bg_creative_css, ';' ) ) {
                $bg_creative_css .= ';';
            }
            if ( method_exists( $cssb, 'get_bg_html_markup' ) ) {
                $bg_creative_html = $cssb->get_bg_html_markup( $bg_obj );
            }
        }

        // Border radius — l'utente si aspetta che il valore impostato corrisponda alla
        // curvatura INTERNA del bordo (= dove inizia il content). In CSS standard,
        // `border-radius: X` produce curvatura esterna X e interna max(0, X - border_width).
        // Per ottenere interno = X applichiamo (X + border_width) all'elemento col bordo.
        $border_data_for_bw = $this->parse_border( $s['border'] ?? [] );
        $bw_uniform = 0;
        if ( $border_data_for_bw
            && $border_data_for_bw['top'] === $border_data_for_bw['right']
            && $border_data_for_bw['right'] === $border_data_for_bw['bottom']
            && $border_data_for_bw['bottom'] === $border_data_for_bw['left'] ) {
            $bw_uniform = max( 0, intval( $border_data_for_bw['top'] ) );
        }
        // Include anche il border legacy ($border_width definito più sotto) nel calcolo
        $bw_legacy  = absint( $s['border_width'] ?? 0 );
        $bw_compensate = max( $bw_uniform, $bw_legacy );

        $rad_raw = $s['border_radius'];
        if ( is_array( $rad_raw ) ) {
            $rad_css = sprintf( '%dpx %dpx %dpx %dpx',
                absint( $rad_raw['tl'] ?? 0 ) + $bw_compensate,
                absint( $rad_raw['tr'] ?? 0 ) + $bw_compensate,
                absint( $rad_raw['br'] ?? 0 ) + $bw_compensate,
                absint( $rad_raw['bl'] ?? 0 ) + $bw_compensate
            );
        } else {
            $rad_css = ( absint( $rad_raw ) + $bw_compensate ) . 'px';
        }

        // Hover CSS dichiarativo — duale dell'helper JS withHover() in _shared.js.
        // Vedi class-tile-base.php::build_hover_css() per il design.
        $hover = $this->build_hover_css( $s, [
            'bg_color'      => [ 'css' => 'background-color', 'hover_key' => 'hover_bg_color',   'important' => true ],
            'text_color'    => [ 'css' => 'color',            'hover_key' => 'hover_text_color', 'important' => true ],
            'border_radius' => 'border-radius',
        ] );

        // Typography preset (global style)
        $tp = sanitize_text_field( $s['typography_preset'] ?? '' );
        $tp_css = '';
        if ( $tp ) {
            $tp_css .= "font-family: var(--olo-font-{$tp}-family);\n";
            $tp_css .= "font-weight: var(--olo-font-{$tp}-weight);\n";
            $tp_css .= "text-transform: var(--olo-font-{$tp}-transform);\n";
            $tp_css .= "line-height: var(--olo-font-{$tp}-line-height);\n";
            $tp_css .= "letter-spacing: var(--olo-font-{$tp}-letter-spacing);\n";
        }

        // Padding: tile_padding (standard spacing object) with backward compat for padding_x/padding_y
        $tp = $s['tile_padding'] ?? null;
        if ( is_array( $tp ) ) {
            $pt = absint( $tp['top'] ?? 14 );
            $pr = absint( $tp['right'] ?? 32 );
            $pb = absint( $tp['bottom'] ?? 14 );
            $pl = absint( $tp['left'] ?? 32 );
        } else {
            $py_val = absint( $s['padding_y'] ?? 14 );
            $px_val = absint( $s['padding_x'] ?? 32 );
            $pt = $pb = $py_val;
            $pr = $pl = $px_val;
        }
        $fs = absint( $s['font_size'] );
        $fw_width = ! empty( $s['full_width'] ) ? 'width: 100%;' : '';
        $font_weight = absint( $s['font_weight'] ) ?: 600;
        $letter_spacing = floatval( $s['letter_spacing'] );
        $text_transform = in_array( $s['text_transform'], [ 'none', 'uppercase', 'lowercase', 'capitalize' ], true ) ? $s['text_transform'] : 'none';

        // Border (legacy)
        $border_width = absint( $s['border_width'] ?? 0 );
        $border_color = $this->safe_color_css( $s['border_color'] ?? '' ) ?: 'var(--olo-color-primary, #e1474f)';

        // Border system — applicato a .olo-btn-link (il pulsante visibile),
        // NON al wrapper .uid (che è solo container con allineamento/padding).
        // Nota: usiamo build_border_hover_props (decoupled) invece di build_border_hover_css
        // per evitare che la transition border generi una regola CSS separata che
        // sovrascriverebbe le transition di border-radius/bg/color (CSS shorthand reset).
        $btn_sel              = ".{$uid} .olo-btn-link";
        $border_css           = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_props   = $this->build_border_hover_props( $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css    = $this->build_border_effect_css( $btn_sel, $s['border'] ?? [], $s );

        // Shadow — variant 'button' usa ombre più visibili (alpha 18-35%)
        // invece dei valori "standard" troppo deboli (8-15%) per bottoni colorati.
        $shadow = Olo_Tile_Utils::shadow( $s['shadow'] ?? 'none', 'button' );

        // Hover colors
        $hover_bg     = $this->safe_color_css( $s['hover_bg_color'] );
        $hover_fg     = $this->safe_color_css( $s['hover_text_color'] );
        $hover_bc     = $this->safe_color_css( $s['hover_border_color'] );
        $hover_shadow = ( $s['hover_shadow'] !== '' ) ? Olo_Tile_Utils::shadow( $s['hover_shadow'], 'button' ) : '';
        $hover_effect = $s['hover_effect'] ?? 'lift';

        // Hover image/video
        $has_hover_media = ! empty( $s['hover_image'] ) || ! empty( $s['hover_video'] );

        // Determine the hover-transform target:
        // with hover media → .olo-hover-wrap  (so button + media move together)
        // without           → .olo-btn-link
        $transform_sel = $has_hover_media
            ? '.olo-hover-wrap'
            : '.olo-btn-link';

        // Transition properties — quelle hoverable (bg/color/border-radius) sono già
        // generate dall'helper con la durata custom, qui solo il resto del legacy.
        // `transform` va incluso nella stessa regola SOLO se il target è .olo-btn-link;
        // altrimenti il blocco "Transform effects" più sotto applica una transition
        // separata sul wrap (selettore diverso, niente conflitto di overrideing).
        $base_transitions = [ 'border-color 0.25s ease', 'box-shadow 0.25s ease' ];
        if ( $transform_sel === '.olo-btn-link' ) {
            $base_transitions[] = 'transform 0.25s ease';
        }
        $transitions = array_merge( $base_transitions, $hover['transitions'] );
        // Aggiungi la transition border (con la sua durata custom) al transition shorthand
        // principale, così non collide con border-radius/bg/color.
        if ( $border_hover_props['transition'] !== '' ) {
            $transitions[] = $border_hover_props['transition'];
        }

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> { overflow: visible; }
            .<?php echo $uid; ?> .olo-btn-link {
                display: inline-block;
                <?php echo $fw_width; ?>
                padding: <?php echo $pt; ?>px <?php echo $pr; ?>px <?php echo $pb; ?>px <?php echo $pl; ?>px;
                position: relative;
                overflow: hidden;
                <?php if ( $bg_creative_css ) : ?>
                <?php echo $bg_creative_css; ?>
                <?php else : ?>
                background-color: <?php echo $bg; ?> !important;
                <?php endif; ?>
                <?php if ( $bg_creative_html ) : ?>
                /* video/gallery bg: testo sopra il video */
                <?php endif; ?>
                color: <?php echo $fg; ?> !important;
                border-radius: <?php echo $rad_css; ?>;
                font-size: <?php echo $fs; ?>px;
                font-weight: <?php echo $font_weight; ?>;
                <?php if ( $tp_css ) : echo $tp_css; endif; ?>
                text-decoration: none !important;
                text-align: center;
                cursor: pointer;
                position: relative;
                <?php if ( $letter_spacing > 0 ) : ?>letter-spacing: <?php echo $letter_spacing; ?>px;<?php endif; ?>
                <?php if ( $text_transform !== 'none' ) : ?>text-transform: <?php echo $text_transform; ?>;<?php endif; ?>
                <?php if ( $border_width > 0 ) : ?>border: <?php echo $border_width; ?>px solid <?php echo $border_color; ?>;<?php endif; ?>
                <?php if ( $shadow !== 'none' ) : ?>box-shadow: <?php echo $shadow; ?>;<?php endif; ?>
                transition: <?php echo implode( ', ', $transitions ); ?>;
            }
            .<?php echo $uid; ?> .olo-btn-link > .olo-btn-text,
            .<?php echo $uid; ?> .olo-btn-link > span {
                position: relative;
                z-index: 2;
            }
            .<?php echo $uid; ?> .olo-btn-link > .olo-bg-video,
            .<?php echo $uid; ?> .olo-btn-link > [class*="-bggal"] {
                z-index: 0;
            }
            .<?php echo $uid; ?> .olo-btn-link:hover {
                text-decoration: none !important;
                <?php echo $hover['hover_decls']; ?>
                <?php echo $border_hover_props['decls']; ?>
                <?php if ( $hover_bc ) : ?>border-color: <?php echo $hover_bc; ?>;<?php endif; ?>
                <?php if ( $hover_shadow !== '' ) : ?>box-shadow: <?php echo $hover_shadow; ?>;<?php endif; ?>
            }
            <?php if ( $transform_sel !== '.olo-btn-link' ) : ?>
            /* Transform effects — applied to <?php echo $transform_sel; ?> (hover-wrap) so media follows */
            .<?php echo $uid; ?> <?php echo $transform_sel; ?> {
                transition: transform 0.25s ease;
            }
            <?php endif; ?>
            <?php
            $transform_hover_css = '';
            switch ( $hover_effect ) {
                case 'lift':
                    $transform_hover_css = 'transform: translateY(-2px);';
                    break;
                case 'grow':
                    $transform_hover_css = 'transform: scale(1.05);';
                    break;
                case 'shrink':
                    $transform_hover_css = 'transform: scale(0.95);';
                    break;
                case 'glow':
                    $glow_color = $hover_bg ?: $bg;
                    $transform_hover_css = 'box-shadow: 0 0 20px ' . $glow_color . '80;';
                    break;
            }
            if ( $transform_hover_css ) : ?>
            .<?php echo $uid; ?> <?php echo $transform_sel; ?>:hover {
                <?php echo $transform_hover_css; ?>
            }
            <?php endif; ?>
            .<?php echo $uid; ?> <?php echo $transform_sel; ?>:active {
                transform: translateY(0) scale(1);
            }
            <?php if ( $hover_effect === 'pulse' ) : ?>
            @keyframes olo-btn-pulse-<?php echo $uid; ?> {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.04); }
            }
            .<?php echo $uid; ?> <?php echo $transform_sel; ?>:hover {
                animation: olo-btn-pulse-<?php echo $uid; ?> 1s ease-in-out infinite;
            }
            <?php endif; ?>
            <?php if ( $has_hover_media ) : ?>
            .<?php echo $uid; ?> .olo-hover-wrap {
                display: inline-block;
                <?php echo $fw_width; ?>
                position: relative;
                border-radius: <?php echo $rad_css; ?>;
                overflow: hidden;
            }
            .<?php echo $uid; ?> .olo-hover-media {
                position: absolute;
                inset: 0;
                z-index: 1;
                pointer-events: none;
            }
            .<?php echo $uid; ?> .olo-hover-media img,
            .<?php echo $uid; ?> .olo-hover-media video {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .<?php echo $uid; ?> .olo-btn-link {
                z-index: 2;
            }
            .<?php echo $uid; ?> .olo-btn-text {
                position: relative;
                z-index: 2;
            }
            <?php endif; ?>
        </style>
        <?php if ( $border_css || $border_effect_css ) : ?><style>
        <?php if ( $border_css ) echo "{$btn_sel}{{$border_css}}"; ?>
        <?php echo $border_effect_css; ?>
        </style><?php endif; ?>

        <div class="olo-button <?php echo esc_attr( $align_class ); ?> <?php echo esc_attr( $uid ); ?>" style="padding: 16px 0; overflow: visible;">
            <?php
            $target_attr = $s['target'] === '_blank' ? ' target="_blank" rel="noopener noreferrer"' : ' target="_self"';

            $icon_html = '';
            if ( ! empty( $s['icon'] ) ) {
                $icon_spacing = absint( $s['icon_spacing'] ?? 8 );
                $icon_pos = $s['icon_position'] === 'after' ? 'after' : 'before';
                $icon_html = '<span uk-icon="icon: ' . esc_attr( $s['icon'] ) . '; ratio: 1" style="vertical-align: middle;"></span>';
            }

            $text_html = esc_html( $s['text'] );
            $icon_spacing_px = absint( $s['icon_spacing'] ?? 8 );

            list( $tfx_cls, $tfx_data ) = $this->tfx_attrs( $s, 'text', $s['text'] );
            $text_span_cls = 'olo-btn-text' . $tfx_cls;

            if ( $icon_html && $s['icon_position'] === 'after' ) {
                $inner = '<span class="' . $text_span_cls . '" style="display:inline-flex;align-items:center;gap:' . $icon_spacing_px . 'px;"' . $tfx_data . '>' . $text_html . $icon_html . '</span>';
            } elseif ( $icon_html ) {
                $inner = '<span class="' . $text_span_cls . '" style="display:inline-flex;align-items:center;gap:' . $icon_spacing_px . 'px;"' . $tfx_data . '>' . $icon_html . $text_html . '</span>';
            } else {
                $inner = '<span class="' . $text_span_cls . '"' . $tfx_data . '>' . $text_html . '</span>';
            }

            // Per video/gallery bg, inseriamo il markup DENTRO l'anchor con position:absolute.
            // Il testo del button è già in z-index:2 sopra al video.
            $btn_inner_full = $bg_creative_html . $inner;
            $btn_html = '<a href="' . esc_url( $s['url'] ) . '"' . $target_attr . ' class="olo-btn-link" role="button" style="position:relative;overflow:hidden;">' . $btn_inner_full . '</a>';

            if ( $has_hover_media ) {
                echo $this->render_hover_wrap( $btn_html, $s['hover_image'] ?? '', $s['hover_video'] ?? '' );
            } else {
                echo $btn_html;
            }
            ?>
        </div>
        <?php
        // Text-effects scoped CSS + runtime script (idempotent)
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();
        return ob_get_clean();
    }
}
