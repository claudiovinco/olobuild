<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Content_Tile extends Olobuild_Tile_Base {

    protected $type     = 'content';
    protected $name     = 'Contenuto';
    protected $icon     = 'dashicons-text-page';
    protected $category = 'essential';
    protected $defaults = [
        'heading'            => 'Titolo sezione',
        'heading_tag'        => 'h2',
        'heading_size'       => 'md',
        'heading_line_height' => 1.2,
        'heading_align'      => '',
        'heading_color'      => '',
        'text'               => 'Aggiungi il tuo contenuto qui.',
        'text_color'         => '',
        'text_align'         => '',
        'image'              => '',
        'image_alt'          => '',
        'image_position'     => 'top',
        'image_width'        => '40',
        'image_height'       => 'auto',
        'aspect_ratio'       => 'auto',
        'aspect_ratio_custom' => '16/9',
        'image_fit'          => 'cover',
        'object_position'    => 'center center',
        'image_radius'       => '0',
        'image_border_width' => '0',
        'image_border_color' => '',
        'image_shadow'       => 'none',
        'heading_gap'        => '8',
        'image_gap'          => '16',
        'hover_effect'       => 'none',
        'hover_image'        => '',
        'hover_video'        => '',
        'link_url'           => '',
        'link_target'        => '_self',
        'text_effect'        => 'none',
        'text_effect_target' => 'heading',
        'text_effect_speed'  => '50',
        'text_effect_delay'  => '0',
        'text_effect_loop'   => false,
        'text_effect_cursor' => true,
        'text_effect_cursor_char' => '|',
        'text_effect_color'  => '',
        'text_effect_color_to' => '',
        'text_effect_phrases' => '',
        'text_effect_pause'  => '1500',
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
            [ 'key' => 'heading', 'type' => 'text',     'label' => 'Heading' ],
            [ 'key' => 'text',    'type' => 'editor',   'label' => 'Content' ],
            [ 'key' => 'image',   'type' => 'image',    'label' => 'Image' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $uid = 'olo-ct-' . wp_rand( 10000, 99999 );

        // Heading tag/size/color
        $allowed_tags = [ 'h2', 'h3', 'h4', 'h5', 'p' ];
        $htag = in_array( $s['heading_tag'] ?? 'h2', $allowed_tags, true ) ? ( $s['heading_tag'] ?? 'h2' ) : 'h2';

        $size_px_map = [ 'sm' => '1.25rem', 'md' => '1.7rem', 'lg' => '2.3rem', 'xl' => '3rem' ];
        $base_size   = $s['heading_size'] ?? 'md';
        $font_size   = $size_px_map[ $base_size ] ?? '1.7rem';

        $hd_clr = $this->safe_color_css( $s['heading_color'] ?? '' );

        // Text color
        $txt_clr = $this->safe_color_css( $s['text_color'] ?? '' );
        $txt_style = '';
        if ( $txt_clr ) { $txt_style = 'color:' . $txt_clr . ';'; }
        // Stile tipografico collegato: governa famiglia, peso e interlinea di
        // titolo e testo, tramite la classe olo-typo-* sul wrapper. I valori
        // locali allora non si scrivono: sul titolo batterebbero lo stile (inline
        // vince), sul testo lo perderebbero (le regole colpiscono i <p>) — e
        // l'inspector, a stile collegato, quelle righe non le mostra.
        $tp_on = sanitize_key( (string) ( $s['typography_preset'] ?? '' ) ) !== '';
        // Minimo garantito: il corpo testo aveva il solo colore.
        $t_ff = $tp_on ? '' : $this->resolve_font_family( (string) ( $s['text_font_family'] ?? '' ) );
        if ( $t_ff ) { $txt_style .= 'font-family:' . $t_ff . ';'; }
        $t_fs = absint( $s['text_font_size'] ?? 0 );
        if ( $t_fs > 0 ) { $txt_style .= 'font-size:' . $t_fs . 'px;'; }
        $t_fw = $tp_on ? '' : $this->font_weight_css( $s['text_font_weight'] ?? '' );
        if ( $t_fw !== '' ) { $txt_style .= 'font-weight:' . $t_fw . ';'; }

        // Heading (plain text, no HTML)
        $heading_text = esc_html( wp_strip_all_tags( $s['heading'] ) );
        // Text content: supporta sia plain text (legacy) che HTML (da RichTextEditor)
        $text_raw = $s['text'] ?? '';
        if ( preg_match( '/<[a-z!\/][^>]*>/i', $text_raw ) ) {
            $text_content = wp_kses_post( $text_raw );
        } else {
            $text_content = nl2br( esc_html( $text_raw ) );
        }

        // Treat HTML-only-whitespace (e.g. <p><br></p>) as empty so we don't render an empty row
        $text_stripped = trim( wp_strip_all_tags( str_replace( [ '&nbsp;', "\xc2\xa0" ], ' ', $text_raw ) ) );
        $has_text      = ( $text_stripped !== '' );

        // Heading gap only when there's actual text below
        $hd_gap = $has_text ? absint( $s['heading_gap'] ?? 8 ) : 0;
        // Il peso era inchiodato a `bold`: un default della tile scritto inline,
        // indistinguibile da una scelta dell'utente — e quindi impossibile da
        // cambiare, sia dal controllo tipografia sia dallo stile globale.
        $hd_fw = trim( (string) ( $s['heading_font_weight'] ?? '' ) );
        if ( $hd_fw === '' || ! preg_match( '/^(?:[1-9]00|normal|bold|lighter|bolder)$/', $hd_fw ) ) {
            $hd_fw = 'bold';
        }
        $hstyle = 'margin:0 0 ' . $hd_gap . 'px 0;' . ( $tp_on ? '' : 'font-weight:' . $hd_fw . ';' ) . 'font-size:' . $font_size . ';';
        $hd_ff = $tp_on ? '' : $this->resolve_font_family( (string) ( $s['heading_font_family'] ?? '' ) );
        if ( $hd_ff ) { $hstyle .= 'font-family:' . $hd_ff . ';'; }
        $hd_lh  = ( ! $tp_on && isset( $s['heading_line_height'] ) ) ? floatval( $s['heading_line_height'] ) : 0;
        if ( $hd_lh > 0 ) { $hstyle .= 'line-height:' . $hd_lh . ';'; }
        $allowed_align = [ 'left', 'center', 'right', 'justify' ];
        $hd_align = in_array( $s['heading_align'] ?? '', $allowed_align, true ) ? ( $s['heading_align'] ?? '' ) : '';
        if ( $hd_align ) { $hstyle .= 'text-align:' . $hd_align . ';'; }
        if ( $hd_clr ) { $hstyle .= 'color:' . $hd_clr . ';'; }

        // Allineamento del testo (il titolo aveva il suo, il corpo nessuno). Regola nel
        // <style> e non in linea: le media query per dispositivo devono poterla battere.
        $txt_sel      = '.' . $uid . ' .olo-ct-text-body';
        $txt_align    = in_array( $s['text_align'] ?? '', $allowed_align, true ) ? $s['text_align'] : '';
        $txt_align_bp = $this->css_per_dispositivo( $s, 'text_align', $txt_sel, static function ( $a ) use ( $allowed_align ) {
            return in_array( $a, $allowed_align, true ) ? 'text-align:' . $a : '';
        } );

        $position     = $this->validate_position( $s['image_position'] ?? 'top' );
        $image_width  = max( 20, min( 80, absint( $s['image_width'] ) ) );
        $image_height = $s['image_height'];
        $image_fit    = in_array( $s['image_fit'], [ 'cover', 'contain', 'fill' ], true ) ? $s['image_fit'] : 'cover';
        $obj_pos      = trim( (string) ( $s['object_position'] ?? 'center center' ) );
        if ( $obj_pos === '' ) { $obj_pos = 'center center'; }
        $image_radius = Olobuild_Tile_Utils::border_radius( $s['image_radius'] ?? 0 );
        $image_radius_hover_css = Olobuild_Tile_Utils::radius_force_css( $s['image_radius_hover'] ?? null );
        // «Durata» del Raggio in hover: la transizione era fissa a 400 ms e il campo non agiva.
        $image_radius_hover_dur = Olobuild_Tile_Utils::durata_hover( $s, 'image_radius_hover_duration', '400ms' );
        $border_width = absint( $s['image_border_width'] );
        $border_color = $this->safe_color_css( $s['image_border_color'] ) ?: 'var(--olo-color-border, #E5E7EB)';
        $image_border_decl = Olobuild_Tile_Utils::border_css(
            $s['image_border'] ?? null,
            [ 'width' => $border_width, 'color' => $border_color ]
        );
        $image_gap    = absint( $s['image_gap'] );
        $hover_effect = $s['hover_effect'] ?? 'none';
        $link_url     = $s['link_url'] ?? '';
        $link_target  = $s['link_target'] === '_blank' ? '_blank' : '_self';

        // Ombra dell'immagine. «Personalizzata» passava da shadow(), che conosce solo i
        // preset sm…xl: restituiva 'none' e l'ombra disegnata nell'inspector non usciva mai.
        $shadow_key = (string) ( $s['image_shadow'] ?? 'none' );
        $shadow     = $shadow_key === 'custom' ? $this->ombra_personalizzata( $s ) : Olobuild_Tile_Utils::shadow( $shadow_key );

        // Image CSS class
        $img_class = 'olo-ct-img';
        if ( $hover_effect !== 'none' ) {
            $img_class .= ' olo-ct-hover-' . esc_attr( $hover_effect );
        }

        // Proporzioni (cornice): image_frame() legge `aspect_ratio`/`aspect_ratio_custom`
        // e applica la stessa whitelist di tutte le altre tile. Il fit e il punto focale
        // restano letti sopra dalle chiavi storiche (`image_fit`, `object_position`), per
        // questo qui si prende solo il frammento del contenitore.
        $frame_css = Olobuild_Tile_Utils::image_frame( $s, 'aspect', [ 'ratio' => 'auto' ] )['contenitore'];

        // Height CSS
        $height_css = 'auto';
        if ( ! empty( $image_height ) && $image_height !== 'auto' ) {
            $height_css = is_numeric( $image_height ) ? $image_height . 'px' : esc_attr( $image_height );
        }
        // Un'altezza esplicita vincerebbe sull'aspect-ratio: quando la proporzione c'è,
        // comanda lei (ed è quello che il campo dichiara nell'inspector).
        if ( $frame_css !== '' ) {
            $height_css = 'auto';
        }

        // Flex direction map
        $dir_map = [ 'top' => 'column', 'bottom' => 'column-reverse', 'left' => 'row', 'right' => 'row-reverse' ];
        $is_hz   = in_array( $position, [ 'left', 'right' ], true );

        // Responsive breakpoint overrides for image_position
        $bp_map = [
            'tablet_landscape' => 1200,
            'tablet'           => 960,
            'mobile_landscape' => 640,
            'mobile'           => 480,
        ];

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: colors via the safe_color_css() whitelist, integers via absint() with min()/max() clamps, line-height via floatval(), shadow from the fixed Olobuild_Tile_Utils::shadow() map or ombra_personalizzata() (intval() + safe_color_css()), text-align and the css_per_dispositivo() media queries from the $allowed_align whitelist, radius via Olobuild_Tile_Utils::border_radius()/radius_force_css() and its hover duration via durata_hover() (absint ms), position/fit/align from in_array() whitelists and the fixed $dir_map/$size_px_map/$bp_map maps, height numeric-checked or esc_attr()'d, object-position esc_attr()'d, aspect-ratio built by Olobuild_Tile_Utils::image_frame() behind its own regex whitelist; $uid is internally generated.
        ?>
        <style>
            .<?php echo $uid; ?> .olo-ct-layout {
                display: flex;
                flex-direction: <?php echo $dir_map[ $position ]; ?>;
                gap: <?php echo $image_gap; ?>px;
                <?php if ( $is_hz ) : ?>align-items: flex-start;<?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-ct-img-col {
                overflow: hidden;
                border-radius: <?php echo $image_radius; ?>;
                <?php if ( $is_hz ) : ?>
                width: <?php echo $image_width; ?>%;
                flex-shrink: 0;
                <?php endif; ?>
            }
            <?php if ( $image_radius_hover_css !== '' ) : ?>.<?php echo $uid; ?> .olo-ct-img-col{transition:border-radius <?php echo $image_radius_hover_dur; ?> cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?> .olo-ct-img-col:hover{border-radius:<?php echo $image_radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo $uid; ?> .olo-ct-text {
                <?php if ( $is_hz ) : ?>flex: 1; min-width: 0;<?php endif; ?>
            }
            <?php if ( $txt_align !== '' ) : ?><?php echo $txt_sel; ?>{text-align:<?php echo $txt_align; ?>}<?php endif; ?>
            <?php echo $txt_align_bp; ?>
            .<?php echo $uid; ?> .olo-ct-img {
                transition: transform 0.5s ease, filter 0.5s ease;
                width: 100%;
                display: block;
                height: <?php echo $height_css; ?>;
                <?php echo $frame_css; ?>
                object-fit: <?php echo $image_fit; ?>;
                object-position: <?php echo esc_attr( $obj_pos ); ?>;
                border-radius: <?php echo $image_radius; ?>;
                <?php if ( $border_width > 0 ) : ?>
                <?php echo esc_attr( $image_border_decl ); ?>
                <?php endif; ?>
            }
            <?php if ( $shadow !== 'none' ) : ?>
            .<?php echo $uid; ?> .olo-ct-img-col {
                box-shadow: <?php echo $shadow; ?>;
            }
            <?php endif; ?>
            <?php if ( $hover_effect !== 'none' ) : ?>
            .<?php echo $uid; ?>:hover .olo-ct-hover-zoom { transform: scale(1.08); }
            .<?php echo $uid; ?>:hover .olo-ct-hover-zoom-rotate { transform: scale(1.08) rotate(2deg); }
            .<?php echo $uid; ?> .olo-ct-hover-brightness { filter: brightness(0.7); }
            .<?php echo $uid; ?>:hover .olo-ct-hover-brightness { filter: brightness(1); }
            .<?php echo $uid; ?> .olo-ct-hover-desaturate { filter: grayscale(100%); }
            .<?php echo $uid; ?>:hover .olo-ct-hover-desaturate { filter: grayscale(0%); }
            .<?php echo $uid; ?> .olo-ct-hover-blur-in { filter: blur(3px); }
            .<?php echo $uid; ?>:hover .olo-ct-hover-blur-in { filter: blur(0); }
            <?php endif; ?>
            <?php
            // Responsive heading_size overrides
            foreach ( $bp_map as $bp => $max_w ) :
                $sz_key = 'heading_size_' . $bp;
                if ( ! empty( $s[ $sz_key ] ) ) :
                    $bp_font = $size_px_map[ $s[ $sz_key ] ] ?? '';
                    if ( $bp_font ) :
            ?>
            @media (max-width: <?php echo $max_w; ?>px) {
                .<?php echo $uid; ?> .olo-ct-heading { font-size: <?php echo $bp_font; ?>; }
            }
            <?php
                    endif;
                endif;
            endforeach;

            // Responsive heading_line_height overrides
            foreach ( $bp_map as $bp => $max_w ) :
                $lh_key = 'heading_line_height_' . $bp;
                if ( isset( $s[ $lh_key ] ) && $s[ $lh_key ] !== '' ) :
                    $bp_lh = floatval( $s[ $lh_key ] );
                    if ( $bp_lh > 0 ) :
            ?>
            @media (max-width: <?php echo $max_w; ?>px) {
                .<?php echo $uid; ?> .olo-ct-heading { line-height: <?php echo $bp_lh; ?>; }
            }
            <?php
                    endif;
                endif;
            endforeach;

            // Responsive heading_align overrides
            foreach ( $bp_map as $bp => $max_w ) :
                $al_key = 'heading_align_' . $bp;
                if ( ! empty( $s[ $al_key ] ) && in_array( $s[ $al_key ], $allowed_align, true ) ) :
            ?>
            @media (max-width: <?php echo $max_w; ?>px) {
                .<?php echo $uid; ?> .olo-ct-heading { text-align: <?php echo esc_attr( $s[ $al_key ] ); ?>; }
            }
            <?php
                endif;
            endforeach;

            // Responsive image_position overrides
            foreach ( $bp_map as $bp => $max_w ) :
                $pos_key = 'image_position_' . $bp;
                if ( ! empty( $s[ $pos_key ] ) ) :
                    $bp_pos = $this->validate_position( $s[ $pos_key ] );
                    $bp_hz  = in_array( $bp_pos, [ 'left', 'right' ], true );
            ?>
            @media (max-width: <?php echo $max_w; ?>px) {
                .<?php echo $uid; ?> .olo-ct-layout {
                    flex-direction: <?php echo $dir_map[ $bp_pos ]; ?>;
                    <?php if ( $bp_hz ) : ?>align-items: flex-start;<?php else : ?>align-items: stretch;<?php endif; ?>
                }
                .<?php echo $uid; ?> .olo-ct-img-col {
                    <?php if ( $bp_hz ) : ?>
                    width: <?php echo $image_width; ?>%;
                    flex-shrink: 0;
                    <?php else : ?>
                    width: auto;
                    flex-shrink: initial;
                    <?php endif; ?>
                }
                .<?php echo $uid; ?> .olo-ct-text {
                    <?php if ( $bp_hz ) : ?>flex: 1; min-width: 0;<?php else : ?>flex: initial; min-width: initial;<?php endif; ?>
                }
            }
            <?php
                endif;
            endforeach;
            ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <?php
        // ─── Text Effects ───
        $effect = $s['text_effect'] ?? 'none';
        $tgt    = in_array( $s['text_effect_target'] ?? 'heading', [ 'heading', 'text', 'both' ], true ) ? ( $s['text_effect_target'] ?? 'heading' ) : 'heading';
        $h_fx_class = '';
        $t_fx_class = '';
        $h_fx_data  = '';
        $t_fx_data  = '';
        $extra_css  = '';

        if ( $effect && $effect !== 'none' ) {
            $speed   = max( 5, intval( $s['text_effect_speed'] ?? 50 ) );
            $delay   = max( 0, intval( $s['text_effect_delay'] ?? 0 ) );
            $loop    = ! empty( $s['text_effect_loop'] ) ? '1' : '0';
            $color1  = $this->safe_color_css( $s['text_effect_color'] ?? '' );
            $color2  = $this->safe_color_css( $s['text_effect_color_to'] ?? '' );
            $cursor  = ! empty( $s['text_effect_cursor'] ) ? '1' : '0';
            $cursorCh = $s['text_effect_cursor_char'] ?: '|';
            $phrases = trim( (string) ( $s['text_effect_phrases'] ?? '' ) );
            $pause   = max( 200, intval( $s['text_effect_pause'] ?? 1500 ) );

            $data = ' data-olo-text-fx="' . esc_attr( $effect ) . '"'
                  . ' data-fx-speed="' . $speed . '"'
                  . ' data-fx-delay="' . $delay . '"'
                  . ' data-fx-loop="' . $loop . '"'
                  . ' data-fx-cursor="' . $cursor . '"'
                  . ' data-fx-cursor-char="' . esc_attr( $cursorCh ) . '"'
                  . ' data-fx-pause="' . $pause . '"';
            if ( $phrases !== '' ) {
                $data .= ' data-fx-phrases="' . esc_attr( $phrases ) . '"';
            }
            $cls = 'olo-tfx olo-tfx--' . esc_attr( $effect );

            if ( $tgt === 'heading' || $tgt === 'both' ) { $h_fx_class = ' ' . $cls; $h_fx_data = $data; }
            if ( $tgt === 'text'    || $tgt === 'both' ) { $t_fx_class = ' ' . $cls; $t_fx_data = $data; }

            // CSS-only effects (rest are JS-driven via olo-text-fx data-attribute below)
            $sel = '.' . $uid;
            if ( $effect === 'gradient-anim' ) {
                $g1 = $color1 ?: 'var(--olo-color-primary, #e1474f)';
                $g2 = $color2 ?: 'var(--olo-color-accent, #f4a23b)';
                $extra_css .= '@keyframes olo-tfx-grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}';
                $extra_css .= $sel . ' .olo-tfx--gradient-anim{background:linear-gradient(90deg,' . $g1 . ',' . $g2 . ',' . $g1 . ');background-size:200% 100%;-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;animation:olo-tfx-grad 4s ease-in-out infinite;animation-delay:' . $delay . 'ms;}';
            } elseif ( $effect === 'glitch' ) {
                $extra_css .= '@keyframes olo-tfx-glitch-1{0%,100%{clip-path:inset(0 0 0 0);transform:translate(0)}20%{clip-path:inset(20% 0 50% 0);transform:translate(-2px,1px)}40%{clip-path:inset(60% 0 10% 0);transform:translate(2px,-1px)}60%{clip-path:inset(30% 0 30% 0);transform:translate(-1px,2px)}80%{clip-path:inset(80% 0 5% 0);transform:translate(1px,-2px)}}';
                $extra_css .= '@keyframes olo-tfx-glitch-2{0%,100%{clip-path:inset(0 0 0 0);transform:translate(0)}25%{clip-path:inset(40% 0 30% 0);transform:translate(2px,-1px)}50%{clip-path:inset(10% 0 60% 0);transform:translate(-2px,1px)}75%{clip-path:inset(50% 0 20% 0);transform:translate(1px,2px)}}';
                $extra_css .= $sel . ' .olo-tfx--glitch{position:relative;display:inline-block;}';
                $extra_css .= $sel . ' .olo-tfx--glitch::before,' . $sel . ' .olo-tfx--glitch::after{content:attr(data-fx-text);position:absolute;left:0;top:0;width:100%;height:100%;}';
                $extra_css .= $sel . ' .olo-tfx--glitch::before{color:#ff00c1;animation:olo-tfx-glitch-1 2.5s infinite;mix-blend-mode:screen;}';
                $extra_css .= $sel . ' .olo-tfx--glitch::after{color:#00fff9;animation:olo-tfx-glitch-2 3s infinite;mix-blend-mode:screen;}';
            } elseif ( $effect === 'underline-grow' ) {
                $uc = $color1 ?: 'currentColor';
                $extra_css .= $sel . ' .olo-tfx--underline-grow{display:inline-block;background-image:linear-gradient(' . $uc . ',' . $uc . ');background-position:0 100%;background-size:0 3px;background-repeat:no-repeat;transition:background-size 1s cubic-bezier(.4,0,.2,1) ' . $delay . 'ms;padding-bottom:4px;}';
                $extra_css .= $sel . ' .olo-tfx--underline-grow.olo-tfx-active{background-size:100% 3px;}';
            } elseif ( $effect === 'highlight-grow' ) {
                $hc = $color1 ?: 'color-mix(in srgb, var(--olo-color-primary, #e1474f) 25%, transparent)';
                // inline-block keeps highlight working on both <h*> headings AND <div> text wrappers (which contain block-level <p>)
                $extra_css .= $sel . ' .olo-tfx--highlight-grow{display:inline-block;background-image:linear-gradient(' . $hc . ',' . $hc . ');background-position:0 100%;background-size:0 100%;background-repeat:no-repeat;transition:background-size 1.2s cubic-bezier(.4,0,.2,1) ' . $delay . 'ms;padding:0 4px;}';
                $extra_css .= $sel . ' .olo-tfx--highlight-grow.olo-tfx-active{background-size:100% 100%;}';
                // Strip default top/bottom margins from paragraphs inside the highlighted text wrapper so the bg hugs the content
                $extra_css .= $sel . ' .olo-ct-text-body.olo-tfx--highlight-grow > :first-child{margin-top:0;}';
                $extra_css .= $sel . ' .olo-ct-text-body.olo-tfx--highlight-grow > :last-child{margin-bottom:0;}';
            } elseif ( $effect === 'wave' ) {
                $extra_css .= '@keyframes olo-tfx-wave{0%,40%,100%{transform:translateY(0)}20%{transform:translateY(-30%)}}';
                $extra_css .= $sel . ' .olo-tfx--wave .olo-tfx-char{display:inline-block;animation:olo-tfx-wave 2s ease-in-out infinite;animation-delay:calc(var(--i,0) * 80ms + ' . $delay . 'ms);}';
            }

            // A11y (WCAG 2.2.2 Pause/Stop/Hide + 2.3.3): kill the infinite, auto-running
            // text effects when the user asks for reduced motion. Scoped to this tile ($sel).
            if ( in_array( $effect, [ 'gradient-anim', 'glitch', 'wave' ], true ) ) {
                $extra_css .= '@media (prefers-reduced-motion: reduce){'
                    . $sel . ' .olo-tfx--gradient-anim,'
                    . $sel . ' .olo-tfx--glitch,'
                    . $sel . ' .olo-tfx--glitch::before,'
                    . $sel . ' .olo-tfx--glitch::after,'
                    . $sel . ' .olo-tfx--wave .olo-tfx-char{animation:none!important;}'
                    . '}';
            }
        }
        ?>

        <div class="olo-content <?php echo esc_attr( $uid ); ?> uk-panel">
            <div class="olo-ct-layout">
                <?php if ( ! empty( $s['image'] ) ) : ?>
                <div class="olo-ct-img-col">
                    <?php $this->render_image_block( $s, $img_class, $link_url, $link_target ); ?>
                </div>
                <?php endif; ?>
                <div class="olo-ct-text">
                    <<?php echo $htag; ?> class="olo-ct-heading<?php echo $h_fx_class; ?>" style="<?php echo $hstyle; ?>"<?php if ( $h_fx_data ) echo $h_fx_data . ' data-fx-text="' . esc_attr( $heading_text ) . '"'; ?>><?php echo $heading_text; ?></<?php echo $htag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $htag from a fixed in_array() whitelist; $hstyle built only from absint()/floatval() values, whitelisted align and safe_color_css() colors; fx class/data fragments esc_attr()'d at build time; heading esc_html()'d at assignment ?>>
                    <?php if ( $has_text ) : ?>
                    <div class="olo-ct-text-body<?php echo $t_fx_class; ?>"<?php if ( $txt_style ) echo ' style="' . $txt_style . '"'; ?><?php echo $t_fx_data; ?>><?php echo $text_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fx class/data fragments esc_attr()'d at build time; $txt_style holds only a safe_color_css() color; body filtered via wp_kses_post() (or nl2br+esc_html) at assignment ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php if ( $extra_css ) : ?>
        <style><?php echo $extra_css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS built above exclusively from safe_color_css() colors, intval()'d timings and fixed keyframe/selector literals ?></style>
        <?php endif; ?>
        <?php if ( $effect && $effect !== 'none' ) {
            // Il motore degli effetti testo è quello condiviso (Olobuild_Text_Effects), in
            // linea una volta per pagina. Questa tile ne stampava una copia più vecchia con la
            // stessa guardia window.__oloTextFxInit: sulla pagina partiva quella arrivata
            // prima, e se era questa anche le altre tile perdevano gli a capo e lo scramble
            // a frasi.
            $this->tfx_print_script();
        } ?>
        <?php
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized border settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base border helpers from sanitized border settings
        }
        return ob_get_clean();
    }

    /**
     * Motore degli effetti testo: è quello condiviso (Olobuild_Text_Effects::print_script()).
     * Resta come rinvio per chi lo chiamava da fuori; la copia che stava qui è stata tolta.
     */
    public static function print_text_fx_script() {
        if ( class_exists( 'Olobuild_Text_Effects' ) ) {
            Olobuild_Text_Effects::print_script();
        }
    }

    /**
     * Ombra «Personalizzata» dell'immagine: X, Y, sfocatura, estensione, colore, interna.
     * Il controllo (FieldBoxShadow) salva l'oggetto `image_shadow_custom` e, col ponte
     * legacy, le sei chiavi piatte `image_shadow_*`: si legge prima l'oggetto, poi le
     * chiavi piatte, poi i valori di partenza del controllo (0 4 10 0, nero al 15%).
     *
     * @param array $s Settings.
     * @return string Valore di box-shadow, CSS-safe.
     */
    private function ombra_personalizzata( $s ) {
        $obj   = ( isset( $s['image_shadow_custom'] ) && is_array( $s['image_shadow_custom'] ) ) ? $s['image_shadow_custom'] : [];
        $leggi = function ( $k, $def ) use ( $obj, $s ) {
            if ( isset( $obj[ $k ] ) && $obj[ $k ] !== '' ) {
                return $obj[ $k ];
            }
            $flat = $s[ 'image_shadow_' . $k ] ?? '';
            return ( $flat !== '' && $flat !== null ) ? $flat : $def;
        };
        $h      = intval( $leggi( 'h', 0 ) );
        $v      = intval( $leggi( 'v', 4 ) );
        $blur   = max( 0, intval( $leggi( 'blur', 10 ) ) );
        $spread = intval( $leggi( 'spread', 0 ) );
        $color  = $this->safe_color_css( (string) $leggi( 'color', '' ) ) ?: 'rgba(0,0,0,0.15)';
        $in     = $leggi( 'inset', false );
        $inset  = ( $in === true || $in === 1 || $in === '1' || $in === 'true' ) ? 'inset ' : '';
        return $inset . $h . 'px ' . $v . 'px ' . $blur . 'px ' . $spread . 'px ' . $color;
    }

    private function validate_position( $pos ) {
        return in_array( $pos, [ 'top', 'bottom', 'left', 'right' ], true ) ? $pos : 'top';
    }

    private function render_image_block( $s, $img_class, $link_url, $link_target ) {
        if ( empty( $s['image'] ) ) {
            return;
        }

        $att_id = absint( $s['image_id'] ?? 0 );
        if ( ! $att_id ) {
            $att_id = absint( attachment_url_to_postid( (string) $s['image'] ) );
        }
        // Testo alternativo: quello scritto nella tile, altrimenti quello della libreria
        // media. Prima leggeva `title`, una chiave che questa tile non ha: l'alt usciva
        // sempre vuoto (resta come seconda scelta per i dati di prima).
        $alt = trim( wp_strip_all_tags( (string) ( $s['image_alt'] ?? '' ) ) );
        if ( $alt === '' ) {
            $alt = trim( wp_strip_all_tags( (string) ( $s['title'] ?? '' ) ) );
        }
        if ( $alt === '' && $att_id ) {
            $alt = trim( (string) get_post_meta( $att_id, '_wp_attachment_image_alt', true ) );
        }
        $img_html = Olobuild_Tile_Utils::img_srcset( $att_id, $s['image'], $alt, $img_class );
        $img_html = $this->render_hover_wrap( $img_html, $s['hover_image'] ?? '', $s['hover_video'] ?? '' );

        if ( ! empty( $link_url ) ) {
            $target_attr = $link_target === '_blank' ? ' target="_blank" rel="noopener noreferrer"' : '';
            echo '<a href="' . esc_url( $link_url ) . '"' . $target_attr . '>' . $img_html . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $target_attr is a fixed literal from the ternary above; image HTML built by Olobuild_Tile_Utils::img_srcset()/render_hover_wrap() which escape internally
        } else {
            echo $img_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- image HTML built by Olobuild_Tile_Utils::img_srcset()/render_hover_wrap() which escape internally
        }
    }
}
