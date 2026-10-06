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

class Olobuild_Text_Effects {

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
            // display:inline-block + !important su text-fill-color per garantire che background-clip:text
            // funzioni anche su tile (es. button) il cui parent forza `color: ... !important`.
            $out .= $sel . ' .olo-tfx--gradient-anim{display:inline-block;background:linear-gradient(90deg,' . $g1 . ',' . $g2 . ',' . $g1 . ');background-size:200% 100%;-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent !important;color:transparent !important;animation:olo-tfx-grad 4s ease-in-out infinite;animation-delay:' . $delay . 'ms;}';
        } elseif ( $effect === 'glitch' ) {
            $out .= '@keyframes olo-tfx-glitch-1{0%,100%{clip-path:inset(0 0 0 0);transform:translate(0)}20%{clip-path:inset(20% 0 50% 0);transform:translate(-2px,1px)}40%{clip-path:inset(60% 0 10% 0);transform:translate(2px,-1px)}60%{clip-path:inset(30% 0 30% 0);transform:translate(-1px,2px)}80%{clip-path:inset(80% 0 5% 0);transform:translate(1px,-2px)}}';
            $out .= '@keyframes olo-tfx-glitch-2{0%,100%{clip-path:inset(0 0 0 0);transform:translate(0)}25%{clip-path:inset(40% 0 30% 0);transform:translate(2px,-1px)}50%{clip-path:inset(10% 0 60% 0);transform:translate(-2px,1px)}75%{clip-path:inset(50% 0 20% 0);transform:translate(1px,2px)}}';
            $out .= $sel . ' .olo-tfx--glitch{position:relative;display:inline-block;}';
            $out .= $sel . ' .olo-tfx--glitch::before,' . $sel . ' .olo-tfx--glitch::after{content:attr(data-fx-text);position:absolute;left:0;top:0;width:100%;height:100%;}';
            // Le due copie sfasate prendono i colori del tema (primario e accento): prima erano
            // magenta #ff00c1 e ciano #00fff9 fissi, uguali su ogni sito e fuori da ogni palette.
            // I colori dell'effetto NON si leggono qui: l'inspector non li mostra per il glitch, e
            // quelli rimasti da un gradient provato prima finivano sulle copie senza poterli vedere.
            $out .= $sel . ' .olo-tfx--glitch::before{color:var(--olo-color-primary, #e1474f);animation:olo-tfx-glitch-1 2.5s infinite;mix-blend-mode:screen;}';
            $out .= $sel . ' .olo-tfx--glitch::after{color:var(--olo-color-accent, #f4a23b);animation:olo-tfx-glitch-2 3s infinite;mix-blend-mode:screen;}';
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

        // Gradient, glitch e wave sono animazioni infinite che partono da sole: con «riduci
        // movimento» si fermano (WCAG 2.2.2). Le copie del glitch spariscono: ferme sopra il
        // testo lo tingerebbero di primario e accento, poco leggibile su fondo chiaro.
        if ( in_array( $effect, [ 'gradient-anim', 'glitch', 'wave' ], true ) ) {
            $out .= '@media (prefers-reduced-motion: reduce){'
                . $sel . ' .olo-tfx--gradient-anim,'
                . $sel . ' .olo-tfx--wave .olo-tfx-char{animation:none!important;}'
                . $sel . ' .olo-tfx--glitch::before,'
                . $sel . ' .olo-tfx--glitch::after{animation:none!important;display:none;}'
                . '}';
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
  // Estrae il testo preservando i ritorni a capo: <br>, </p>, </div>, </h1-6>, </li>, </blockquote>
  // diventano '\n'. Serve solo al loop di frasi, che usa le righe del contenuto come frasi.
  function getTextWithBreaks(el){
    var html = el.getAttribute('data-fx-original-html') || el.innerHTML;
    el.setAttribute('data-fx-original-html', html);
    var normalized = html
      .replace(/<br\s*\/?>(\s*)/gi, '\n')
      .replace(/<\/(p|div|h[1-6]|li|tr|blockquote)>/gi, '\n');
    var tmp = document.createElement('div');
    tmp.innerHTML = normalized;
    return tmp.textContent.replace(/\n{2,}/g, '\n').replace(/^\n+|\n+$/g, '');
  }
  // Escape HTML + converte '\n' in '<br>' per lo scramble a frasi
  function htmlEscapeWithBreaks(s){
    return s.replace(/[&<>]/g, function(m){return ({'&':'&amp;','<':'&lt;','>':'&gt;'}[m]);}).replace(/\n/g,'<br>');
  }
  // Macchina da scrivere, reveal (lettera e parola), wave e scramble lavorano DENTRO il
  // markup, sui soli nodi di testo: prima appiattivano tutto in testo + <br>, e i paragrafi
  // del tile Testo (con grassetti, link ed elenchi) diventavano righe spezzate, l'icona del
  // pulsante spariva e lo <span> delle linee del Titolo pure. Il markup originale si
  // conserva per ripartire da capo nei giri in loop.
  function restoreHtml(el){
    var html = el.getAttribute('data-fx-original-html');
    if (html === null){ el.setAttribute('data-fx-original-html', el.innerHTML); } else { el.innerHTML = html; }
  }
  function textNodes(el){
    var out = [], w = document.createTreeWalker(el, NodeFilter.SHOW_TEXT, null), n;
    while ((n = w.nextNode())){
      if (!/\S/.test(n.nodeValue)) continue;
      if (n.parentNode.closest('svg,script,style,.olo-tfx-cursor')) continue;
      out.push(n);
    }
    return out;
  }
  // Il carattere del cursore è un ::after (attributo data-ch), non un nodo di testo: ora che
  // il cursore sta DENTRO il titolo o l'etichetta, la modifica in linea del canvas (che salva
  // il textContent) altrimenti salverebbe anche la «|». In linea e non inline-block: prima
  // di un inline-block il browser può andare a capo, e a fine riga la «|» scendeva da sola.
  function cursorEl(opts){
    var c = document.createElement('span');
    c.className = 'olo-tfx-cursor'; c.setAttribute('aria-hidden', 'true');
    c.setAttribute('data-ch', opts.cursorCh || '|');
    c.style.cssText = 'display:inline;animation:olo-tfx-blink 1s step-end infinite;';
    return c;
  }
  // Le lettere di una parola stanno in uno span che non va a capo: fra due lettere
  // inline-block (wave) il browser può spezzare la riga, e le parole si dividevano a metà.
  function splitIntoChars(el){
    restoreHtml(el);
    var idx = 0;
    textNodes(el).forEach(function(n){
      var t = n.nodeValue, frag = document.createDocumentFragment(), w = null;
      for (var i=0;i<t.length;i++){
        var ch = t.charAt(i);
        if (/\s/.test(ch)){ w = null; frag.appendChild(document.createTextNode(ch)); continue; }
        if (!w){ w = document.createElement('span'); w.style.whiteSpace = 'nowrap'; frag.appendChild(w); }
        var s=document.createElement('span'); s.className='olo-tfx-char'; s.style.setProperty('--i', idx++); s.textContent = ch; w.appendChild(s);
      }
      n.parentNode.replaceChild(frag, n);
    });
  }
  function splitIntoWords(el){
    restoreHtml(el);
    var idx = 0;
    textNodes(el).forEach(function(n){
      var w = n.nodeValue.split(/(\s+)/), frag = document.createDocumentFragment();
      for (var i=0;i<w.length;i++){
        if (w[i] === '') continue;
        if (/^\s+$/.test(w[i])){ frag.appendChild(document.createTextNode(w[i])); continue; }
        var s=document.createElement('span'); s.className='olo-tfx-word'; s.style.setProperty('--i', idx++); s.textContent=w[i]; frag.appendChild(s);
      }
      n.parentNode.replaceChild(frag, n);
    });
  }
  function typewriter(el, opts){
    restoreHtml(el);
    var nodes = textNodes(el), full = [];
    nodes.forEach(function(n){ full.push(n.nodeValue); n.nodeValue = ''; });
    // Il cursore segue l'ultimo carattere scritto, nel suo paragrafo: prima stava in coda al
    // contenitore della tile e, su un blocco, finiva da solo su una riga sotto il testo.
    var cursor = opts.cursor ? cursorEl(opts) : null, solo = null;
    function place(k){
      if (!cursor) return;
      var n = nodes[k];
      if (n){ n.parentNode.insertBefore(cursor, n.nextSibling); return; }
      // Nessun testo da scrivere: il cursore va in un suo span, mai figlio diretto
      // dell'elemento (sul Titolo a linee UIkit disegna una linea su ogni figlio diretto).
      if (!solo){ solo = document.createElement('span'); el.appendChild(solo); }
      solo.appendChild(cursor);
    }
    var ni=0, ci=0;
    function step(){
      if (ni < nodes.length){
        var t = full[ni];
        // Uno scatto = un carattere visibile: gli spazi (anche quelli dell'indentazione) passano insieme.
        do { ci++; } while (ci < t.length ? /\s/.test(t.charAt(ci - 1)) : false);
        nodes[ni].nodeValue = t.slice(0, ci);
        place(ni);
        if (ci >= t.length){ ni++; ci = 0; }
        setTimeout(step, opts.speed);
      } else if (opts.loop){
        setTimeout(function(){ nodes.forEach(function(n){ n.nodeValue = ''; }); ni=0; ci=0; place(0); setTimeout(step, opts.speed); }, opts.pause||1500);
      }
    }
    place(0);
    setTimeout(step, opts.delay);
  }
  function typewriterLoop(el, opts){
    var phrases = (opts.phrases||'').split(/\n+/).map(function(s){return s.trim();}).filter(Boolean);
    // Fallback: se la textarea è vuota, usa le righe del contenuto come frasi
    // (così il tile testo multi-riga funziona out-of-the-box senza compilare il campo)
    if (!phrases.length){
      phrases = getTextWithBreaks(el).split('\n').map(function(s){return s.trim();}).filter(Boolean);
    }
    if (!phrases.length) phrases = [el.textContent.trim()];
    // Frase e cursore dentro l'elemento, uno accanto all'altro (prima il cursore andava in
    // coda al contenitore, su una riga sua quando l'elemento è un blocco). Tutti e due in
    // UN solo span figlio: sul Titolo a linee la regola di UIkit «.uk-heading-line > *»
    // trasformava un cursore figlio diretto in una linea da 2000px, e la «|» spariva.
    el.innerHTML = '';
    var out = document.createElement('span'), txt = document.createTextNode('');
    out.appendChild(txt);
    if (opts.cursor) out.appendChild(cursorEl(opts));
    el.appendChild(out);
    var pi=0, ci=0, mode='type';
    function step(){
      var p = phrases[pi];
      if (mode==='type'){ ci++; txt.nodeValue = p.slice(0,ci); if (ci>=p.length){ mode='wait'; setTimeout(step, opts.pause); return; } setTimeout(step, opts.speed); }
      else if (mode==='wait'){ mode='delete'; setTimeout(step, opts.speed); }
      else if (mode==='delete'){ ci--; txt.nodeValue = p.slice(0,ci); if (ci<=0){ mode='type'; pi=(pi+1)%phrases.length; setTimeout(step, opts.speed*4); return; } setTimeout(step, opts.speed/2); }
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
    var chars = '!@#$%^&*()_+-=[]{}|;:,.<>?ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    // Cycle mode: se sono fornite più frasi (data-fx-phrases, una per riga) lo scramble
    // CICLA tra le parole (MOVE→SHINE→SPEAK→…) come il blueprint NOVA, anziché
    // ri-scramblare sempre lo stesso testo. Fallback: single-word re-scramble in loop.
    var phrases = (opts.phrases||'').split(/\n+/).map(function(s){return s.trim();}).filter(Boolean);
    function scrambleTo(target, onDone){
      el.setAttribute('data-fx-original', target);
      var len = target.length, frame = 0, totalFrames = Math.ceil(len*opts.speed/30) + 6;
      (function step(){
        var output = '';
        for (var i=0;i<len;i++){
          var revealAt = (i/len)*totalFrames;
          if (target[i] === '\n') { output += '\n'; continue; }
          if (target[i] === ' ')  { output += ' '; continue; }
          output += (frame >= revealAt) ? target[i] : chars[Math.floor(Math.random()*chars.length)];
        }
        el.innerHTML = htmlEscapeWithBreaks(output);
        frame++;
        if (frame <= totalFrames + 5) requestAnimationFrame(step);
        else if (onDone) onDone();
      })();
    }
    if (phrases.length >= 2){
      var wi = 0;
      el.textContent = phrases[0];
      setTimeout(function(){
        scrambleTo(phrases[0]);
        setInterval(function(){ wi = (wi+1) % phrases.length; scrambleTo(phrases[wi]); }, Math.max(1200, opts.pause||2600));
      }, opts.delay);
    } else {
      // Testo della tile: si mescolano i soli nodi di testo, il markup resta (paragrafi, link).
      restoreHtml(el);
      var nodes = textNodes(el), orig = [], tot = 0;
      nodes.forEach(function(n){ orig.push(n.nodeValue); tot += n.nodeValue.length; });
      var scrambleNodes = function(onDone){
        var frame = 0, totalFrames = Math.ceil(tot*opts.speed/30) + 6;
        (function step(){
          var g = 0;
          for (var k=0;k<nodes.length;k++){
            var t = orig[k], o = '';
            for (var i=0;i<t.length;i++){
              var ch = t.charAt(i);
              o += (/\s/.test(ch) || frame >= (g/tot)*totalFrames) ? ch : chars.charAt(Math.floor(Math.random()*chars.length));
              g++;
            }
            nodes[k].nodeValue = o;
          }
          frame++;
          if (frame <= totalFrames + 5) requestAnimationFrame(step);
          else if (onDone) onDone();
        })();
      };
      if (tot > 0){
        setTimeout(function(){ scrambleNodes(opts.loop ? function(){ setTimeout(function(){ scrambleNodes(); }, 2500); } : null); }, opts.delay);
      }
    }
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
  st.textContent = '@keyframes olo-tfx-blink{0%,50%{opacity:1}50.01%,100%{opacity:0}}.olo-tfx-cursor::after{content:attr(data-ch)}';
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
