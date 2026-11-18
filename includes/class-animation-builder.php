<?php
/**
 * Olobuild Animation Builder
 *
 * Parallax, scrollspy, mouse tracking, and animation utilities
 * extracted from Olobuild_Frontend_Renderer.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Animation_Builder {

    /**
     * Format a parallax value: integers as int, floats with minimal decimals.
     *
     * @param mixed $val Numeric value.
     * @return string Formatted value.
     */
    public function format_parallax_value( $val ) {
        $f = floatval( $val );
        if ( $f == intval( $f ) ) {
            return strval( intval( $f ) );
        }
        return rtrim( rtrim( number_format( $f, 4, '.', '' ), '0' ), '.' );
    }

    /**
     * Build uk-parallax attribute string from a multi-stop parallax object.
     * Supports property keys: x, y, scale, rotate, opacity, blur, bgx, bgy.
     *
     * @param array $parallax Parallax config with property stops.
     * @return string Attribute string or empty.
     */
    public function build_parallax_attr_from_object( $parallax ) {
        $prop_keys = [ 'x', 'y', 'scale', 'rotate', 'opacity', 'blur', 'bgx', 'bgy' ];
        $parts = [];

        foreach ( $prop_keys as $key ) {
            if ( empty( $parallax[ $key ] ) || ! is_array( $parallax[ $key ] ) ) {
                continue;
            }

            $stops = $parallax[ $key ];
            usort( $stops, function( $a, $b ) {
                return ( $a['position'] ?? 0 ) - ( $b['position'] ?? 0 );
            });

            $count = count( $stops );
            if ( $count === 0 ) {
                continue;
            }

            $segments = [];
            foreach ( $stops as $i => $s ) {
                $v   = $this->format_parallax_value( $s['value'] );
                $pos = intval( $s['position'] ?? ( $i === $count - 1 ? 100 : 0 ) );

                $need_pct = false;
                if ( $i === 0 && $pos !== 0 ) {
                    $need_pct = true;
                } elseif ( $i === $count - 1 && $pos !== 100 ) {
                    $need_pct = true;
                } elseif ( $i > 0 && $i < $count - 1 ) {
                    $need_pct = true;
                }

                $segments[] = $need_pct ? $v . ' ' . $pos . '%' : $v;
            }
            $parts[] = $key . ': ' . implode( ',', $segments );
        }

        if ( empty( $parts ) ) {
            return '';
        }

        if ( ! empty( $parallax['start'] ) ) {
            $parts[] = 'start: ' . esc_attr( $parallax['start'] );
        }
        if ( ! empty( $parallax['end'] ) ) {
            $parts[] = 'end: ' . esc_attr( $parallax['end'] );
        }
        if ( isset( $parallax['easing'] ) && $parallax['easing'] !== null && $parallax['easing'] !== '' ) {
            $parts[] = 'easing: ' . floatval( $parallax['easing'] );
        }

        // Regola di prodotto: gli effetti girano ANCHE su mobile. media:@m solo
        // se l'utente ha scelto esplicitamente «Disattiva su mobile».
        if ( ! empty( $parallax['nomobile'] ) ) {
            $parts[] = 'media: @m';
        }

        return ' uk-parallax="' . implode( '; ', $parts ) . '"';
    }

    /**
     * Build uk-parallax attribute value from a background config.
     *
     * @param array $bg Background config.
     * @return string Attribute string or empty.
     */
    public function build_uk_parallax_attr( $bg ) {
        if ( empty( $bg['parallax'] ) ) {
            return '';
        }

        // New multi-stop format
        if ( is_array( $bg['parallax'] ) ) {
            return $this->build_parallax_attr_from_object( $bg['parallax'] );
        }

        // Legacy boolean format
        $parts = [];

        $bgy = isset( $bg['parallax_bgy'] ) ? intval( $bg['parallax_bgy'] ) : -200;
        if ( $bgy !== 0 ) {
            $parts[] = 'bgy: ' . $bgy;
        }

        $bgx = isset( $bg['parallax_bgx'] ) ? intval( $bg['parallax_bgx'] ) : 0;
        if ( $bgx !== 0 ) {
            $parts[] = 'bgx: ' . $bgx;
        }

        if ( ! empty( $bg['parallax_opacity'] ) ) {
            $start = floatval( $bg['parallax_opacity_start'] ?? 0.3 );
            $end   = floatval( $bg['parallax_opacity_end'] ?? 1 );
            $parts[] = 'opacity: ' . $start . ',' . $end;
        }

        if ( ! empty( $bg['parallax_scale'] ) ) {
            $start = floatval( $bg['parallax_scale_start'] ?? 1 );
            $end   = floatval( $bg['parallax_scale_end'] ?? 1.2 );
            $parts[] = 'scale: ' . $start . ',' . $end;
        }

        if ( ! empty( $bg['parallax_blur'] ) ) {
            $start = intval( $bg['parallax_blur_start'] ?? 5 );
            $end   = intval( $bg['parallax_blur_end'] ?? 0 );
            $parts[] = 'blur: ' . $start . ',' . $end;
        }

        // Come sopra: niente disattivazione mobile di default.
        if ( ! empty( $bg['parallax_nomobile'] ) ) {
            $parts[] = 'media: @m';
        }

        if ( empty( $parts ) ) {
            return '';
        }

        return ' uk-parallax="' . implode( '; ', $parts ) . '"';
    }

    /**
     * Classe e dichiarazioni di dimensione e posizione del livello immagine di uno sfondo.
     * Con la parallasse UIkit legge dimensione e posizione dello sfondo dal foglio di stile,
     * togliendo per un attimo lo stile in linea: scritte in linea, per lui erano «auto» e «0%»,
     * così con bgx/bgy non allargava la foto per coprire lo spostamento e sopra (o sotto)
     * restava una striscia vuota. Con la parallasse arrivano quindi da .olo-bg-par
     * (frontend.css), che le legge da due variabili scritte in linea.
     *
     * @param array  $bg   Background config.
     * @param string $size Dimensione già passata da esc_attr().
     * @param string $pos  Posizione già passata da esc_attr().
     * @return array [ classe in più (con lo spazio davanti) o '', dichiarazioni CSS ]
     */
    public function bg_image_layer_props( $bg, $size, $pos ) {
        if ( '' === $this->build_uk_parallax_attr( $bg ) ) {
            return [ '', 'background-size: ' . $size . '; background-position: ' . $pos ];
        }
        return [ ' olo-bg-par', '--olo-bg-size: ' . $size . '; --olo-bg-pos: ' . $pos ];
    }

    /**
     * Build uk-scrollspy attribute from advanced config.
     *
     * @param array $advanced Advanced settings.
     * @return string Attribute string or empty.
     */
    public function build_scrollspy_attr( $advanced ) {
        $animation = $advanced['scrollspy_animation'] ?? '';
        if ( empty( $animation ) ) {
            return '';
        }

        $stagger = intval( $advanced['scrollspy_stagger'] ?? 0 );

        $parts = [ 'cls: uk-animation-' . esc_attr( $animation ) ];

        $delay = intval( $advanced['scrollspy_delay'] ?? 0 );
        if ( $delay > 0 ) {
            $parts[] = 'delay: ' . $delay;
        }

        $repeat = ! empty( $advanced['scrollspy_repeat'] );
        if ( $repeat ) {
            $parts[] = 'repeat: true';
        }

        if ( $stagger > 0 ) {
            $parts[] = 'target: > *';
            $parts[] = 'delay: ' . $stagger;
        }

        return ' uk-scrollspy="' . implode( '; ', $parts ) . '"';
    }

    /**
     * Build uk-parallax attribute for element-level parallax.
     *
     * @param array $advanced Advanced settings.
     * @return string Attribute string or empty.
     */
    public function build_element_parallax_attr( $advanced ) {
        // New multi-stop format
        if ( isset( $advanced['parallax'] ) && is_array( $advanced['parallax'] ) ) {
            return $this->build_parallax_attr_from_object( $advanced['parallax'] );
        }

        // Legacy flat keys format
        $parts = [];

        $y_start = intval( $advanced['parallax_y_start'] ?? 0 );
        $y_end   = intval( $advanced['parallax_y_end'] ?? 0 );
        if ( $y_start !== 0 || $y_end !== 0 ) {
            $parts[] = 'y: ' . $y_start . ',' . $y_end;
        }

        $op_start = $advanced['parallax_opacity_start'] ?? '';
        $op_end   = $advanced['parallax_opacity_end'] ?? '';
        if ( $op_start !== '' && $op_end !== '' ) {
            $parts[] = 'opacity: ' . floatval( $op_start ) . ',' . floatval( $op_end );
        }

        $sc_start = $advanced['parallax_scale_start'] ?? '';
        $sc_end   = $advanced['parallax_scale_end'] ?? '';
        if ( $sc_start !== '' && $sc_end !== '' ) {
            $parts[] = 'scale: ' . floatval( $sc_start ) . ',' . floatval( $sc_end );
        }

        if ( empty( $parts ) ) {
            return '';
        }

        // Niente disattivazione mobile di default: media:@m solo su scelta esplicita.
        if ( ! empty( $advanced['parallax_nomobile'] ) ) {
            $parts[] = 'media: @m';
        }

        return ' uk-parallax="' . implode( '; ', $parts ) . '"';
    }

    /**
     * Tilt 3D col giroscopio (Effetti mouse → Tilt 3D → «Anche col giroscopio»): su telefoni e tablet
     * l'elemento si inclina al contrario del dispositivo, fino all'ampiezza in gradi (4-60, di serie 12:
     * vicino all'inclinazione reale del telefono; l'ampiezza piena arriva a 25° di inclinazione).
     * Il runtime sta in renderer-page; con «Foto interne» l'attributo passa alle foto come quello del tilt.
     *
     * @param array $src Advanced (o settings) con mouse_tilt_gyro / mouse_tilt_gyro_max.
     * @return string Attributo, o '' se spento.
     */
    public static function gyro_attr( $src ) {
        if ( empty( $src['mouse_tilt_gyro'] ) ) {
            return '';
        }
        $max = intval( $src['mouse_tilt_gyro_max'] ?? 12 );
        $max = $max > 0 ? max( 4, min( 60, $max ) ) : 12;
        return ' data-olo-tilt-gyro="' . $max . '"';
    }

    /**
     * Build data attributes for mouse tracking effects (3D tilt + cursor follow).
     *
     * @param array $advanced Advanced settings.
     * @return string HTML attributes string.
     */
    public function build_mouse_attrs( $advanced ) {
        $attrs = '';
        if ( ! empty( $advanced['mouse_tilt'] ) ) {
            $intensity = intval( $advanced['mouse_tilt_intensity'] ?? 15 );
            // target 'items': tilt sulle foto/media interni invece che sul blocco
            // (il runtime espande l'attr sugli img/video figli).
            $tilt_attr = ( 'items' === ( $advanced['mouse_tilt_target'] ?? 'block' ) ) ? 'data-olo-tilt-items' : 'data-olo-tilt';
            $attrs    .= ' ' . $tilt_attr . '="' . $intensity . '"';
            $attrs    .= self::gyro_attr( $advanced );
        }
        if ( ! empty( $advanced['mouse_track'] ) ) {
            $speed = intval( $advanced['mouse_track_speed'] ?? 3 );
            $attrs .= ' data-olo-track="' . $speed . '"';
        }
        return $attrs;
    }

    /**
     * Build the data-olo-spotlight attribute (cursor "torch" effect).
     * Riusabile su section/row/column/element. Legge da $advanced come gli altri effetti puntatore.
     *
     * @param array $advanced Advanced settings.
     * @return string Attribute string (leading space) or empty.
     */
    public function build_spotlight_attr( $advanced ) {
        if ( empty( $advanced['cursor_spotlight'] ) ) {
            return '';
        }
        return self::spotlight_attr( [
            'scope'  => $advanced['cursor_spotlight_scope'] ?? 'element',
            'size'   => $advanced['cursor_spotlight_size'] ?? 300,
            'soft'   => $advanced['cursor_spotlight_softness'] ?? 40,
            'blend'  => $advanced['cursor_spotlight_blend'] ?? 'difference',
            'color'  => $advanced['cursor_spotlight_color'] ?? '',
            'easing' => $advanced['cursor_spotlight_easing'] ?? 22,
        ] );
    }

    /**
     * Attributo data-olo-spotlight dai valori della torcia (anche la vecchia modalità «Torcia» della tile
     * Blend Text passa da qui: un solo motore, nello script della pagina).
     * Ambito: element = dentro l'elemento · section = in tutta la sezione che lo contiene · page = su tutta la
     * pagina. Il colore può essere un token (var(--olo-color-…)): la pagina lo risolve dove sta l'elemento.
     *
     * @param array $v scope, size, soft, blend, color, easing.
     * @return string Attributo (con lo spazio davanti).
     */
    public static function spotlight_attr( $v ) {
        $blend = (string) ( $v['blend'] ?? 'difference' );
        if ( ! in_array( $blend, [ 'difference', 'exclusion', 'screen', 'overlay', 'hard-light', 'color-dodge' ], true ) ) {
            $blend = 'difference';
        }
        $scope = (string) ( $v['scope'] ?? 'element' );
        if ( ! in_array( $scope, [ 'element', 'section', 'page' ], true ) ) {
            $scope = 'element';
        }
        $color = Olobuild_Tile_Utils::safe_color( $v['color'] ?? '' );
        $cfg = [
            'scope' => $scope,
            'size'  => max( 40, min( 1000, intval( $v['size'] ?? 300 ) ) ),
            'soft'  => max( 0, min( 100, intval( $v['soft'] ?? 40 ) ) ),
            'blend' => $blend,
            'color' => '' !== $color ? html_entity_decode( $color, ENT_QUOTES ) : '#ffffff',
            'ease'  => max( 0.02, min( 1, intval( $v['easing'] ?? 22 ) / 100 ) ),
        ];
        return " data-olo-spotlight='" . esc_attr( wp_json_encode( $cfg ) ) . "'";
    }

    /**
     * Pennellata al cursore: muovendo il puntatore (o il dito) sull'elemento si depositano segni di
     * colore che si allargano e svaniscono. Riusabile su section/row/column/element come la torcia;
     * nata nella tile Hero — Smear (che resta com'è), ora vale per qualsiasi elemento.
     * Colori solo dai token del tema: il runtime li usa come sfondo dei segni (var() funziona).
     *
     * @param array $advanced Advanced settings.
     * @return string Attribute string (leading space) or empty.
     */
    /**
     * La sorpresa nascosta (1.4.1218, Avanzate › Sorpresa nascosta): coriandoli, pagina capovolta o un messaggio dopo
     * cinque clic sull'elemento o col codice Konami. Lo script si stampa una volta, solo nelle pagine che la usano.
     *
     * @param array $advanced Advanced settings.
     * @return string Attributo (con lo spazio davanti) o ''.
     */
    public function build_sorpresa_attr( $advanced ) {
        $fx = (string) ( $advanced['sorpresa'] ?? '' );
        if ( ! in_array( $fx, [ 'coriandoli', 'capovolto', 'messaggio' ], true ) ) {
            return '';
        }
        $via   = 'konami' === ( $advanced['sorpresa_innesco'] ?? 'clic' ) ? 'konami' : 'clic';
        $testo = sanitize_text_field( (string) ( $advanced['sorpresa_testo'] ?? '' ) );
        if ( ! has_action( 'wp_footer', [ __CLASS__, 'stampa_sorpresa' ] ) ) {
            add_action( 'wp_footer', [ __CLASS__, 'stampa_sorpresa' ], 30 );
        }
        $cfg = [ 'fx' => $fx, 'via' => $via ];
        if ( '' !== $testo ) {
            $cfg['testo'] = mb_substr( $testo, 0, 160 );
        }
        return " data-olo-sorpresa='" . esc_attr( wp_json_encode( $cfg ) ) . "'";
    }

    /**
     * Lo script della sorpresa nascosta. Cinque clic entro due secondi (un link che porta alla pagina stessa, come il
     * logo in home, non la ricarica); il codice Konami da tastiera (su, su, giù, giù, sinistra, destra, sinistra,
     * destra, B, A) e, sui telefoni, i cinque tocchi. Con meno movimento i coriandoli e il capovolgimento diventano il
     * messaggio. Niente «&&» (WordPress lo rovina negli script in linea).
     */
    public static function stampa_sorpresa() {
        $base = esc_js( __( 'Hai trovato la sorpresa!', 'olobuilder' ) );
        ?>
<script>
(function(){
  var BASE = '<?php echo $base; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_js() ?>';
  var meno = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)').matches : false;
  var tocco = window.matchMedia ? window.matchMedia('(hover: none), (pointer: coarse)').matches : false;
  function colori(){
    var cs = getComputedStyle(document.documentElement), out = [];
    ['primary', 'accent', 'secondary', 'warning', 'success'].forEach(function(r){ var v = cs.getPropertyValue('--olo-color-' + r).trim(); if(v){ out.push(v); } });
    return out.length ? out : ['#e1474f', '#f5b700', '#2bb3a3', '#5b6cff'];
  }
  function messaggio(t){
    var m = document.createElement('div');
    m.setAttribute('role', 'status');
    m.textContent = t || BASE;
    m.style.cssText = 'position:fixed;left:50%;bottom:32px;z-index:2147483000;transform:translate(-50%,20px);opacity:0;max-width:min(92vw,520px);padding:14px 22px;border-radius:999px;background:var(--olo-color-dark,#111);color:var(--olo-color-light,#fff);font:600 15px/1.35 var(--olo-font-family,system-ui,sans-serif);text-align:center;box-shadow:0 18px 40px -12px rgba(0,0,0,.45);transition:opacity .35s ease,transform .35s ease';
    document.body.appendChild(m);
    requestAnimationFrame(function(){ m.style.opacity = '1'; m.style.transform = 'translate(-50%,0)'; });
    setTimeout(function(){ m.style.opacity = '0'; m.style.transform = 'translate(-50%,20px)'; }, 3600);
    setTimeout(function(){ if(m.parentNode){ m.parentNode.removeChild(m); } }, 4100);
  }
  function coriandoli(x, y){
    var c = document.createElement('canvas'), W = window.innerWidth, H = window.innerHeight, dpr = Math.min(2, window.devicePixelRatio || 1);
    c.width = W * dpr; c.height = H * dpr;
    c.style.cssText = 'position:fixed;inset:0;width:100vw;height:100vh;pointer-events:none;z-index:2147483000';
    document.body.appendChild(c);
    var g = c.getContext('2d'), col = colori(), p = [], n = Math.min(220, Math.round(W / 6));
    g.scale(dpr, dpr);
    for(var i = 0; i < n; i++){
      var a = -Math.PI / 2 + (Math.random() - 0.5) * 2.2, v = 7 + Math.random() * 9;
      p.push({ x: x, y: y, vx: Math.cos(a) * v, vy: Math.sin(a) * v, w: 6 + Math.random() * 6, h: 8 + Math.random() * 8, r: Math.random() * 6, vr: (Math.random() - 0.5) * 0.4, c: col[i % col.length] });
    }
    var t0 = performance.now();
    (function giro(t){
      var dt = Math.min(2, (t - (giro.u || t)) / 16.7); giro.u = t;
      g.clearRect(0, 0, W, H);
      var vivi = 0;
      for(var k = 0; k < p.length; k++){
        var q = p[k];
        q.vy += 0.32 * dt; q.vx *= Math.pow(0.99, dt); q.x += q.vx * dt; q.y += q.vy * dt; q.r += q.vr * dt;
        if(q.y < H + 20){ vivi++; }
        g.save(); g.translate(q.x, q.y); g.rotate(q.r); g.fillStyle = q.c;
        g.fillRect(-q.w / 2, -q.h / 2 * Math.abs(Math.cos(q.r * 2)), q.w, q.h * Math.abs(Math.cos(q.r * 2)) + 1);
        g.restore();
      }
      if(vivi > 0){ if(t - t0 < 6000){ requestAnimationFrame(giro); return; } }
      if(c.parentNode){ c.parentNode.removeChild(c); }
    })(t0);
  }
  var capovolta = false;
  function capovolgi(){
    if(capovolta){ return; }
    capovolta = true;
    var b = document.body, prima = b.style.transition;
    b.style.transition = 'transform 1.1s cubic-bezier(.6,-0.2,.3,1.3)';
    b.style.transformOrigin = '50% ' + (window.scrollY + window.innerHeight / 2) + 'px';
    b.style.transform = 'rotate(180deg)';
    setTimeout(function(){ b.style.transform = ''; }, 3200);
    setTimeout(function(){ b.style.transition = prima; b.style.transformOrigin = ''; capovolta = false; }, 4400);
  }
  function scatta(cfg, x, y){
    if(cfg.fx === 'messaggio'){ messaggio(cfg.testo); return; }
    if(meno){ messaggio(cfg.testo); return; }
    if(cfg.fx === 'coriandoli'){ coriandoli(x, y); if(cfg.testo){ messaggio(cfg.testo); } return; }
    capovolgi();
    if(cfg.testo){ setTimeout(function(){ messaggio(cfg.testo); }, 1100); }
  }
  function cinqueClic(el, cfg){
    var n = 0, primo = 0;
    el.addEventListener('click', function(e){
      var ora = Date.now();
      if(ora - primo > 2000){ n = 0; primo = ora; }
      n++;
      var a = e.target.closest ? e.target.closest('a[href]') : null;
      if(a){ if(a.href.split('#')[0] === location.href.split('#')[0]){ e.preventDefault(); } }
      if(n >= 5){ n = 0; scatta(cfg, e.clientX || window.innerWidth / 2, e.clientY || window.innerHeight / 2); }
    });
  }
  var KONAMI = ['ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight', 'b', 'a'];
  var konami = [], passo = 0;
  document.addEventListener('keydown', function(e){
    if(!konami.length){ return; }
    var k = e.key.length === 1 ? e.key.toLowerCase() : e.key;
    passo = (k === KONAMI[passo]) ? passo + 1 : (k === KONAMI[0] ? 1 : 0);
    if(passo === KONAMI.length){
      passo = 0;
      konami.forEach(function(v){ var r = v.el.getBoundingClientRect(); scatta(v.cfg, r.left + r.width / 2, Math.max(40, Math.min(window.innerHeight - 40, r.top + r.height / 2))); });
    }
  });
  function avvia(){
    var els = document.querySelectorAll('[data-olo-sorpresa]');
    for(var i = 0; i < els.length; i++){
      var el = els[i];
      if(el.oloSorpresa){ continue; }
      var cfg = {};
      try { cfg = JSON.parse(el.getAttribute('data-olo-sorpresa')) || {}; } catch(err){ continue; }
      el.oloSorpresa = cfg;
      if(cfg.via === 'konami'){ konami.push({ el: el, cfg: cfg }); if(tocco){ cinqueClic(el, cfg); } }
      else { cinqueClic(el, cfg); }
    }
  }
  if(document.readyState !== 'loading'){ avvia(); } else { document.addEventListener('DOMContentLoaded', avvia); }
  document.addEventListener('olo:lazy-hydrated', avvia);
})();
</script>
        <?php
    }

    public function build_smear_attr( $advanced ) {
        if ( empty( $advanced['cursor_smear'] ) ) {
            return '';
        }
        $tavolozze = [
            'tema'     => [ 'primary', 'accent', 'secondary', 'light' ],
            'caldi'    => [ 'primary', 'accent' ],
            'primario' => [ 'primary' ],
            'accento'  => [ 'accent' ],
            'chiaro'   => [ 'light' ],
            'scuro'    => [ 'dark' ],
        ];
        $scelta = (string) ( $advanced['cursor_smear_colors'] ?? 'tema' );
        if ( ! isset( $tavolozze[ $scelta ] ) ) {
            $scelta = 'tema';
        }
        $colori = [];
        foreach ( $tavolozze[ $scelta ] as $ruolo ) {
            $colori[] = 'var(--olo-color-' . $ruolo . ')';
        }
        $fusione = (string) ( $advanced['cursor_smear_blend'] ?? 'auto' );
        if ( ! in_array( $fusione, [ 'auto', 'screen', 'multiply', 'difference' ], true ) ) {
            $fusione = 'auto';
        }
        $cfg = [
            'colors' => $colori,
            'size'   => max( 10, min( 160, intval( $advanced['cursor_smear_size'] ?? 44 ) ) ),
            'life'   => max( 300, min( 4000, intval( $advanced['cursor_smear_life'] ?? 900 ) ) ),
            'blur'   => max( 0, min( 30, intval( $advanced['cursor_smear_blur'] ?? 2 ) ) ),
            'blend'  => $fusione,
        ];
        return " data-olo-smear='" . esc_attr( wp_json_encode( $cfg ) ) . "'";
    }

    /**
     * Build inline CSS for infinite (looping) animation (legacy element-level).
     *
     * @param array $advanced Advanced settings.
     * @return string CSS declaration or empty.
     */
    public function build_inline_animation_css( $advanced ) {
        $anim = $advanced['infinite_animation'] ?? 'none';
        if ( $anim === 'none' || $anim === '' ) {
            return '';
        }
        // NB: ritorna SOLO la dichiarazione `animation:` inline; i @keyframes globali
        // (olo-anim-float, ecc.) sono definiti in frontend.css. Storicamente mancavano
        // per float/float-rot → ora aggiunti al foglio. Direzione + (additive) speed.
        $valid = [ 'float', 'float-rot', 'pulse', 'spin', 'wiggle', 'bounce', 'swing', 'breathe' ];
        if ( ! in_array( $anim, $valid, true ) ) {
            return '';
        }
        $speed = floatval( $advanced['infinite_speed'] ?? 3 );
        if ( $speed <= 0 ) $speed = 3;
        $dir   = $advanced['infinite_direction'] ?? 'normal';
        if ( ! in_array( $dir, [ 'normal', 'reverse', 'alternate', 'alternate-reverse' ], true ) ) {
            $dir = 'normal';
        }
        $delay = max( 0, min( 5000, intval( $advanced['infinite_delay'] ?? 0 ) ) );
        $delay_css = $delay > 0 ? ' ' . $delay . 'ms' : '';
        $name  = 'olo-anim-' . sanitize_html_class( $anim );
        return "animation: {$name} {$speed}s ease-in-out{$delay_css} {$dir} infinite";
    }

    /**
     * Build clip-path CSS for mask/shape effects (legacy element-level).
     *
     * @param array $advanced Advanced settings.
     * @return string CSS declaration or empty.
     */
    public function build_inline_mask_css( $advanced ) {
        $mask = $advanced['mask_type'] ?? 'none';
        if ( $mask === 'none' || $mask === '' ) {
            return '';
        }
        $shapes = [
            'circle'   => 'circle(50% at 50% 50%)',
            'ellipse'  => 'ellipse(50% 40% at 50% 50%)',
            'triangle' => 'polygon(50% 0%, 100% 100%, 0% 100%)',
            'hexagon'  => 'polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%)',
            'star'     => 'polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%)',
            'diamond'  => 'polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%)',
            'blob'     => 'polygon(30% 0%, 70% 0%, 100% 30%, 100% 70%, 70% 100%, 30% 100%, 0% 70%, 0% 30%)',
        ];
        if ( $mask === 'custom' ) {
            $custom = $advanced['mask_custom'] ?? '';
            if ( $custom === '' ) return '';
            return 'clip-path: ' . $custom;
        }
        if ( ! isset( $shapes[ $mask ] ) ) return '';
        return 'clip-path: ' . $shapes[ $mask ];
    }
}
