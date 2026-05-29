<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Content_Tile extends Olo_Tile_Base {

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
        'image'              => '',
        'image_position'     => 'top',
        'image_width'        => '40',
        'image_height'       => 'auto',
        'image_fit'          => 'cover',
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
        $htag = in_array( $s['heading_tag'] ?? 'h2', $allowed_tags, true ) ? $s['heading_tag'] : 'h2';

        $size_px_map = [ 'sm' => '1.25rem', 'md' => '1.7rem', 'lg' => '2.3rem', 'xl' => '3rem' ];
        $base_size   = $s['heading_size'] ?? 'md';
        $font_size   = $size_px_map[ $base_size ] ?? '1.7rem';

        $hd_clr = $this->safe_color_css( $s['heading_color'] ?? '' );

        // Text color
        $txt_clr = $this->safe_color_css( $s['text_color'] ?? '' );
        $txt_style = '';
        if ( $txt_clr ) { $txt_style = 'color:' . $txt_clr . ';'; }

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
        $hstyle = 'margin:0 0 ' . $hd_gap . 'px 0;font-weight:bold;font-size:' . $font_size . ';';
        $hd_lh  = isset( $s['heading_line_height'] ) ? floatval( $s['heading_line_height'] ) : 0;
        if ( $hd_lh > 0 ) { $hstyle .= 'line-height:' . $hd_lh . ';'; }
        $allowed_align = [ 'left', 'center', 'right', 'justify' ];
        $hd_align = in_array( $s['heading_align'] ?? '', $allowed_align, true ) ? $s['heading_align'] : '';
        if ( $hd_align ) { $hstyle .= 'text-align:' . $hd_align . ';'; }
        if ( $hd_clr ) { $hstyle .= 'color:' . $hd_clr . ';'; }

        $position     = $this->validate_position( $s['image_position'] ?? 'top' );
        $image_width  = max( 20, min( 80, absint( $s['image_width'] ) ) );
        $image_height = $s['image_height'];
        $image_fit    = in_array( $s['image_fit'], [ 'cover', 'contain', 'fill' ], true ) ? $s['image_fit'] : 'cover';
        $image_radius = Olo_Tile_Utils::border_radius( $s['image_radius'] ?? 0 );
        $image_radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['image_radius_hover'] ?? null );
        $border_width = absint( $s['image_border_width'] );
        $border_color = $this->safe_color_css( $s['image_border_color'] ) ?: 'var(--olo-color-border, #E5E7EB)';
        $image_gap    = absint( $s['image_gap'] );
        $hover_effect = $s['hover_effect'] ?? 'none';
        $link_url     = $s['link_url'] ?? '';
        $link_target  = $s['link_target'] === '_blank' ? '_blank' : '_self';

        // Shadow
        $shadow = Olo_Tile_Utils::shadow( $s['image_shadow'] ?? 'none' );

        // Image CSS class
        $img_class = 'olo-ct-img';
        if ( $hover_effect !== 'none' ) {
            $img_class .= ' olo-ct-hover-' . esc_attr( $hover_effect );
        }

        // Height CSS
        $height_css = 'auto';
        if ( ! empty( $image_height ) && $image_height !== 'auto' ) {
            $height_css = is_numeric( $image_height ) ? $image_height . 'px' : esc_attr( $image_height );
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
            <?php if ( $image_radius_hover_css !== '' ) : ?>.<?php echo $uid; ?> .olo-ct-img-col{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?> .olo-ct-img-col:hover{border-radius:<?php echo $image_radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo $uid; ?> .olo-ct-text {
                <?php if ( $is_hz ) : ?>flex: 1; min-width: 0;<?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-ct-img {
                transition: transform 0.5s ease, filter 0.5s ease;
                width: 100%;
                display: block;
                height: <?php echo $height_css; ?>;
                object-fit: <?php echo $image_fit; ?>;
                border-radius: <?php echo $image_radius; ?>;
                <?php if ( $border_width > 0 ) : ?>
                border: <?php echo $border_width; ?>px solid <?php echo $border_color; ?>;
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

        <?php
        // ─── Text Effects ───
        $effect = $s['text_effect'] ?? 'none';
        $tgt    = in_array( $s['text_effect_target'] ?? 'heading', [ 'heading', 'text', 'both' ], true ) ? $s['text_effect_target'] : 'heading';
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
                $g1 = $color1 ?: 'var(--olo-color-primary, #6366F1)';
                $g2 = $color2 ?: '#ec4899';
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
                $hc = $color1 ?: 'rgba(99,102,241,0.25)';
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
        }
        ?>

        <div class="olo-content <?php echo $uid; ?> uk-panel">
            <div class="olo-ct-layout">
                <?php if ( ! empty( $s['image'] ) ) : ?>
                <div class="olo-ct-img-col">
                    <?php $this->render_image_block( $s, $img_class, $link_url, $link_target ); ?>
                </div>
                <?php endif; ?>
                <div class="olo-ct-text">
                    <<?php echo $htag; ?> class="olo-ct-heading<?php echo $h_fx_class; ?>" style="<?php echo $hstyle; ?>"<?php if ( $h_fx_data ) echo $h_fx_data . ' data-fx-text="' . esc_attr( $heading_text ) . '"'; ?>><?php echo $heading_text; ?></<?php echo $htag; ?>>
                    <?php if ( $has_text ) : ?>
                    <div class="olo-ct-text-body<?php echo $t_fx_class; ?>"<?php if ( $txt_style ) echo ' style="' . $txt_style . '"'; ?><?php echo $t_fx_data; ?>><?php echo $text_content; ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php if ( $extra_css ) : ?>
        <style><?php echo $extra_css; ?></style>
        <?php endif; ?>
        <?php if ( $effect && $effect !== 'none' ) {
            // Print inline once per page (guard via window.__oloTextFxInit).
            // Inline (not wp_footer) so it works in builder iframe REST render too.
            if ( ! self::$text_fx_inline_emitted ) {
                self::$text_fx_inline_emitted = true;
                self::print_text_fx_script();
            }
        } ?>
        <?php
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}";
            echo $border_hover_css . $border_effect_css . '</style>';
        }
        return ob_get_clean();
    }

    /**
     * Inline-emit guard: ensure the runner script is only printed once per render pass.
     */
    protected static $text_fx_inline_emitted = false;

    public static function print_text_fx_script() {
        ?>
<script>
(function(){
  if (window.__oloTextFxInit) return; window.__oloTextFxInit = true;
  function splitIntoChars(el){ var t = el.textContent; el.innerHTML = ''; var idx = 0; for (var i=0;i<t.length;i++){ var ch = t[i]; if (ch === ' ' || ch === '\t' || ch === '\n') { el.appendChild(document.createTextNode(' ')); continue; } var s=document.createElement('span'); s.className='olo-tfx-char'; s.style.setProperty('--i', idx++); s.textContent = ch; el.appendChild(s); } }
  function splitIntoWords(el){ var t = el.textContent; el.innerHTML = ''; var w=t.split(/(\s+)/); for(var i=0;i<w.length;i++){ if(/^\s+$/.test(w[i])){ el.appendChild(document.createTextNode(w[i])); continue;} var s=document.createElement('span'); s.className='olo-tfx-word'; s.style.setProperty('--i',i); s.textContent=w[i]; el.appendChild(s); } }
  function typewriter(el, opts){
    var full = el.getAttribute('data-fx-original') || el.textContent.trim();
    el.setAttribute('data-fx-original', full);
    el.textContent = '';
    var cursor = opts.cursor ? document.createElement('span') : null;
    if (cursor){ cursor.className='olo-tfx-cursor'; cursor.textContent = opts.cursorCh || '|'; cursor.style.cssText='display:inline-block;animation:olo-tfx-blink 1s step-end infinite;'; el.parentElement.insertAdjacentElement('beforeend', cursor); }
    var i=0;
    function step(){ if (i<=full.length){ el.textContent = full.slice(0,i); i++; setTimeout(step, opts.speed); } else if (opts.loop){ setTimeout(function(){ i=0; el.textContent=''; setTimeout(step, opts.speed); }, opts.pause||1500); } }
    setTimeout(step, opts.delay);
  }
  function typewriterLoop(el, opts){
    var phrases = (opts.phrases||'').split(/\n+/).map(function(s){return s.trim();}).filter(Boolean);
    if (!phrases.length) phrases = [el.textContent.trim()];
    el.textContent = '';
    var cursor = opts.cursor ? document.createElement('span') : null;
    if (cursor){ cursor.className='olo-tfx-cursor'; cursor.textContent = opts.cursorCh || '|'; cursor.style.cssText='display:inline-block;animation:olo-tfx-blink 1s step-end infinite;'; el.parentElement.appendChild(cursor); }
    var pi=0, ci=0, mode='type';
    function step(){
      var p = phrases[pi];
      if (mode==='type'){ ci++; el.textContent = p.slice(0,ci); if (ci>=p.length){ mode='wait'; setTimeout(step, opts.pause); return; } setTimeout(step, opts.speed); }
      else if (mode==='wait'){ mode='delete'; setTimeout(step, opts.speed); }
      else if (mode==='delete'){ ci--; el.textContent = p.slice(0,ci); if (ci<=0){ mode='type'; pi=(pi+1)%phrases.length; setTimeout(step, opts.speed*4); return; } setTimeout(step, opts.speed/2); }
    }
    setTimeout(step, opts.delay);
  }
  function revealLetter(el, opts){
    splitIntoChars(el);
    var chars = el.querySelectorAll('.olo-tfx-char');
    chars.forEach(function(c,i){ c.style.opacity='0'; c.style.transition='opacity .3s, transform .4s'; c.style.transform='translateY(8px)'; setTimeout(function(){ c.style.opacity='1'; c.style.transform='translateY(0)'; }, opts.delay + i*opts.speed); });
    if (opts.loop){ setTimeout(function(){ chars.forEach(function(c,i){ setTimeout(function(){ c.style.opacity='0'; c.style.transform='translateY(8px)'; }, i*opts.speed/2); }); setTimeout(function(){ revealLetter(el, opts); }, chars.length*opts.speed + 800); }, chars.length*opts.speed + 2000);
    }
  }
  function revealWord(el, opts){
    splitIntoWords(el);
    var ws = el.querySelectorAll('.olo-tfx-word');
    ws.forEach(function(w,i){ w.style.opacity='0'; w.style.filter='blur(6px)'; w.style.transition='opacity .5s, filter .5s, transform .5s'; w.style.display='inline-block'; w.style.transform='translateY(10px)'; setTimeout(function(){ w.style.opacity='1'; w.style.filter='blur(0)'; w.style.transform='translateY(0)'; }, opts.delay + i*opts.speed); });
  }
  function scramble(el, opts){
    var full = el.getAttribute('data-fx-original') || el.textContent.trim();
    el.setAttribute('data-fx-original', full);
    var chars = '!@#$%^&*()_+-=[]{}|;:,.<>?ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var len = full.length, frame = 0, totalFrames = Math.ceil(len*opts.speed/30);
    function step(){
      var output = '';
      for (var i=0;i<len;i++){
        var revealAt = (i/len)*totalFrames;
        if (frame >= revealAt){ output += full[i]; }
        else { output += chars[Math.floor(Math.random()*chars.length)]; }
      }
      el.textContent = output;
      frame++;
      if (frame <= totalFrames + 5) requestAnimationFrame(step);
      else if (opts.loop){ setTimeout(function(){ frame=0; step(); }, 2500); }
    }
    setTimeout(step, opts.delay);
  }
  function activateGrow(el){ el.classList.add('olo-tfx-active'); }
  function activateWave(el){ if(!el.querySelector('.olo-tfx-char')) splitIntoChars(el); }
  function run(el){
    var fx = el.getAttribute('data-olo-text-fx');
    var opts = {
      speed: parseInt(el.getAttribute('data-fx-speed')||50),
      delay: parseInt(el.getAttribute('data-fx-delay')||0),
      loop: el.getAttribute('data-fx-loop')==='1',
      cursor: el.getAttribute('data-fx-cursor')==='1',
      cursorCh: el.getAttribute('data-fx-cursor-char')||'|',
      phrases: el.getAttribute('data-fx-phrases')||'',
      pause: parseInt(el.getAttribute('data-fx-pause')||1500),
    };
    if (fx==='typewriter') typewriter(el, opts);
    else if (fx==='typewriter-loop') typewriterLoop(el, opts);
    else if (fx==='reveal-letter') revealLetter(el, opts);
    else if (fx==='reveal-word') revealWord(el, opts);
    else if (fx==='scramble') scramble(el, opts);
    else if (fx==='underline-grow' || fx==='highlight-grow') activateGrow(el);
    else if (fx==='wave') activateWave(el);
  }
  // Inject keyframes for cursor
  var st = document.createElement('style');
  st.textContent = '@keyframes olo-tfx-blink{0%,50%{opacity:1}50.01%,100%{opacity:0}}';
  document.head.appendChild(st);
  // IntersectionObserver to trigger on viewport entry
  var io = new IntersectionObserver(function(entries){
    entries.forEach(function(e){ if (e.isIntersecting){ run(e.target); io.unobserve(e.target); } });
  }, { threshold: 0.2 });
  function init(){
    document.querySelectorAll('[data-olo-text-fx]:not([data-olo-text-fx-init])').forEach(function(el){
      el.setAttribute('data-olo-text-fx-init','1');
      io.observe(el);
    });
  }
  init();
  // Re-init for dynamically loaded content (lazy templates)
  var mo = new MutationObserver(init);
  mo.observe(document.body, { childList:true, subtree:true });
})();
</script>
        <?php
    }

    private function validate_position( $pos ) {
        return in_array( $pos, [ 'top', 'bottom', 'left', 'right' ], true ) ? $pos : 'top';
    }

    private function render_image_block( $s, $img_class, $link_url, $link_target ) {
        if ( empty( $s['image'] ) ) {
            return;
        }

        $att_id   = absint( $s['image_id'] ?? 0 );
        $img_html = Olo_Tile_Utils::img_srcset( $att_id, $s['image'], wp_strip_all_tags( $s['title'] ?? '' ), $img_class );
        $img_html = $this->render_hover_wrap( $img_html, $s['hover_image'] ?? '', $s['hover_video'] ?? '' );

        if ( ! empty( $link_url ) ) {
            $target_attr = $link_target === '_blank' ? ' target="_blank" rel="noopener noreferrer"' : '';
            echo '<a href="' . esc_url( $link_url ) . '"' . $target_attr . '>' . $img_html . '</a>';
        } else {
            echo $img_html;
        }
    }
}
