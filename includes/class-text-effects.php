<?php
/**
 * Centralized text effects helper.
 *
 * Used by tile classes that expose a "Effetti testo" inspector group.
 * Provides:
 *   - Defaults to merge into the tile's $defaults array
 *   - Per-target classes / data-attributes for the rendered HTML
 *   - Per-tile CSS for the CSS-driven effects (gradient/glitch/wave/underline-grow/highlight-grow)
 *   - The runtime <script> (printed once per page)
 *
 * The runtime script binds to elements with `data-olo-text-fx="<effect>"` and
 * looks for `data-fx-*` attributes for params, so any tile that emits the
 * canonical attributes will animate correctly without duplicating JS.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Text_Effects {

    /**
     * Defaults to merge into a tile's $defaults array.
     */
    public static function defaults() {
        return [
            'text_effect'             => 'none',
            'text_effect_target'      => 'heading',
            'text_effect_speed'       => '50',
            'text_effect_delay'       => '0',
            'text_effect_loop'        => false,
            'text_effect_cursor'      => true,
            'text_effect_cursor_char' => '|',
            'text_effect_color'       => '',
            'text_effect_color_to'    => '',
            'text_effect_phrases'     => '',
            'text_effect_pause'       => '1500',
        ];
    }

    /**
     * Whether the user has activated an effect (and we should bother emitting).
     */
    public static function active( $s ) {
        $fx = $s['text_effect'] ?? 'none';
        return ( $fx && $fx !== 'none' );
    }

    /**
     * Whether `$target` is a target the user wants to apply the effect to.
     * Allowed targets: 'heading', 'text', 'both', or any custom string the tile uses.
     * The tile decides which targets exist; we just check membership.
     */
    public static function applies_to( $s, $target ) {
        if ( ! self::active( $s ) ) return false;
        $tgt = $s['text_effect_target'] ?? 'heading';
        if ( $tgt === 'both' ) return true;
        return $tgt === $target;
    }

    /**
     * Return ' class="..."' OR a class string fragment to add to an element
     * if it should receive the effect.
     *
     * @param array  $s        Settings.
     * @param string $target   The semantic target name for THIS element ('heading', 'text', 'subtitle', …).
     * @param bool   $with_lead_space  Whether to prepend a leading space (for inline concat).
     * @return string  e.g. " olo-tfx olo-tfx--gradient-anim" or ''.
     */
    public static function classes( $s, $target, $with_lead_space = true ) {
        if ( ! self::applies_to( $s, $target ) ) return '';
        $fx = sanitize_html_class( $s['text_effect'] );
        return ( $with_lead_space ? ' ' : '' ) . 'olo-tfx olo-tfx--' . $fx;
    }

    /**
     * Return data-* attributes string (with leading space) for the element.
     * Includes data-fx-text mirror for effects that need ::before/::after content.
     *
     * @param array  $s             Settings.
     * @param string $target        Same semantic name as classes().
     * @param string $element_text  Plain text content of the element (used for `data-fx-text` mirror).
     * @return string  ' data-olo-text-fx="..." data-fx-speed="..." …' or ''.
     */
    public static function data_attrs( $s, $target, $element_text = '' ) {
        if ( ! self::applies_to( $s, $target ) ) return '';

        $effect    = $s['text_effect'];
        $speed     = max( 5, intval( $s['text_effect_speed'] ?? 50 ) );
        $delay     = max( 0, intval( $s['text_effect_delay'] ?? 0 ) );
        $loop      = ! empty( $s['text_effect_loop'] ) ? '1' : '0';
        $cursor    = ! empty( $s['text_effect_cursor'] ) ? '1' : '0';
        $cursor_ch = $s['text_effect_cursor_char'] !== '' ? $s['text_effect_cursor_char'] : '|';
        $phrases   = trim( (string) ( $s['text_effect_phrases'] ?? '' ) );
        $pause     = max( 200, intval( $s['text_effect_pause'] ?? 1500 ) );

        $out  = ' data-olo-text-fx="' . esc_attr( $effect ) . '"';
        $out .= ' data-fx-speed="' . $speed . '"';
        $out .= ' data-fx-delay="' . $delay . '"';
        $out .= ' data-fx-loop="' . $loop . '"';
        $out .= ' data-fx-cursor="' . $cursor . '"';
        $out .= ' data-fx-cursor-char="' . esc_attr( $cursor_ch ) . '"';
        $out .= ' data-fx-pause="' . $pause . '"';
        if ( $phrases !== '' ) {
            $out .= ' data-fx-phrases="' . esc_attr( $phrases ) . '"';
        }
        if ( $element_text !== '' ) {
            // `glitch` ::before/::after consume this for the duplicated layers
            $out .= ' data-fx-text="' . esc_attr( $element_text ) . '"';
        }
        return $out;
    }

    /**
     * Per-tile CSS for the CSS-driven effects (the rest are JS-driven via data attribute).
     *
     * @param array  $s    Settings.
     * @param string $sel  Root scope selector (typically `.UID`) — gradient/glitch/wave/underline/highlight rules will
     *                     be emitted under this prefix so they don't bleed into other tiles.
     * @return string CSS (no <style> wrapper).
     */
    public static function css( $s, $sel ) {
        if ( ! self::active( $s ) ) return '';
        $effect = $s['text_effect'];
        $delay  = max( 0, intval( $s['text_effect_delay'] ?? 0 ) );
        $color1 = self::safe_color( $s['text_effect_color'] ?? '' );
        $color2 = self::safe_color( $s['text_effect_color_to'] ?? '' );
        $sel    = trim( $sel );
        $out    = '';

        if ( $effect === 'gradient-anim' ) {
            $g1 = $color1 ?: 'var(--olo-color-primary, #6366F1)';
            $g2 = $color2 ?: '#ec4899';
            $out .= '@keyframes olo-tfx-grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}';
            $out .= $sel . ' .olo-tfx--gradient-anim{background:linear-gradient(90deg,' . $g1 . ',' . $g2 . ',' . $g1 . ');background-size:200% 100%;-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;animation:olo-tfx-grad 4s ease-in-out infinite;animation-delay:' . $delay . 'ms;}';
        } elseif ( $effect === 'glitch' ) {
            $out .= '@keyframes olo-tfx-glitch-1{0%,100%{clip-path:inset(0 0 0 0);transform:translate(0)}20%{clip-path:inset(20% 0 50% 0);transform:translate(-2px,1px)}40%{clip-path:inset(60% 0 10% 0);transform:translate(2px,-1px)}60%{clip-path:inset(30% 0 30% 0);transform:translate(-1px,2px)}80%{clip-path:inset(80% 0 5% 0);transform:translate(1px,-2px)}}';
            $out .= '@keyframes olo-tfx-glitch-2{0%,100%{clip-path:inset(0 0 0 0);transform:translate(0)}25%{clip-path:inset(40% 0 30% 0);transform:translate(2px,-1px)}50%{clip-path:inset(10% 0 60% 0);transform:translate(-2px,1px)}75%{clip-path:inset(50% 0 20% 0);transform:translate(1px,2px)}}';
            $out .= $sel . ' .olo-tfx--glitch{position:relative;display:inline-block;}';
            $out .= $sel . ' .olo-tfx--glitch::before,' . $sel . ' .olo-tfx--glitch::after{content:attr(data-fx-text);position:absolute;left:0;top:0;width:100%;height:100%;}';
            $out .= $sel . ' .olo-tfx--glitch::before{color:#ff00c1;animation:olo-tfx-glitch-1 2.5s infinite;mix-blend-mode:screen;}';
            $out .= $sel . ' .olo-tfx--glitch::after{color:#00fff9;animation:olo-tfx-glitch-2 3s infinite;mix-blend-mode:screen;}';
        } elseif ( $effect === 'underline-grow' ) {
            $uc = $color1 ?: 'currentColor';
            $out .= $sel . ' .olo-tfx--underline-grow{display:inline-block;background-image:linear-gradient(' . $uc . ',' . $uc . ');background-position:0 100%;background-size:0 3px;background-repeat:no-repeat;transition:background-size 1s cubic-bezier(.4,0,.2,1) ' . $delay . 'ms;padding-bottom:4px;}';
            $out .= $sel . ' .olo-tfx--underline-grow.olo-tfx-active{background-size:100% 3px;}';
        } elseif ( $effect === 'highlight-grow' ) {
            $hc = $color1 ?: 'rgba(99,102,241,0.25)';
            $out .= $sel . ' .olo-tfx--highlight-grow{display:inline-block;background-image:linear-gradient(' . $hc . ',' . $hc . ');background-position:0 100%;background-size:0 100%;background-repeat:no-repeat;transition:background-size 1.2s cubic-bezier(.4,0,.2,1) ' . $delay . 'ms;padding:0 4px;}';
            $out .= $sel . ' .olo-tfx--highlight-grow.olo-tfx-active{background-size:100% 100%;}';
            // Strip default top/bottom margins from paragraphs inside the highlighted text wrapper so the bg hugs the content
            $out .= $sel . ' .olo-tfx--highlight-grow > :first-child{margin-top:0;}';
            $out .= $sel . ' .olo-tfx--highlight-grow > :last-child{margin-bottom:0;}';
        } elseif ( $effect === 'wave' ) {
            $out .= '@keyframes olo-tfx-wave{0%,40%,100%{transform:translateY(0)}20%{transform:translateY(-30%)}}';
            $out .= $sel . ' .olo-tfx--wave .olo-tfx-char{display:inline-block;animation:olo-tfx-wave 2s ease-in-out infinite;animation-delay:calc(var(--i,0) * 80ms + ' . $delay . 'ms);}';
        }

        return $out;
    }

    /**
     * Same as css() but wraps the result in <style> tags. Convenience for tiles
     * that don't already have a <style> block.
     */
    public static function style_block( $s, $sel ) {
        $css = self::css( $s, $sel );
        return $css === '' ? '' : '<style>' . $css . '</style>';
    }

    protected static $script_emitted = false;

    /**
     * Emit the runtime script. Idempotent at PHP level (only prints once per request);
     * the script itself also has a window flag (`__oloTextFxInit`) so it can't double-bind.
     */
    public static function print_script() {
        if ( self::$script_emitted ) return;
        self::$script_emitted = true;
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

    /**
     * Sanitize a hex/rgba color. Returns empty string if invalid or empty.
     */
    protected static function safe_color( $color ) {
        $c = trim( (string) $color );
        return $c !== '' ? esc_attr( $c ) : '';
    }
}
