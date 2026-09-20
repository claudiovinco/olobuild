/**
 * Glow / "Bagliori" background — aloni radiali sfocati + grana film opzionale.
 *
 * NUOVO tipo di sfondo per OLObuild, gemello di patternCSS.js.
 * Va usato nello STESSO modo:
 *   • BackgroundControls.vue  → preview del controllo
 *   • useBackgroundStyle.js   → resa sul canvas del builder (buildBgStyle / bgInlineStyle)
 *   • class-css-builder.php   → copia equivalente build_glow_css() per il frontend
 *
 * Resa: NON usa filter:blur() — la sfocatura è data dai radial-gradient morbidi
 * (colore → trasparente con falloff ampio), così è performante e stampabile.
 *
 * Colore: rispetta i ruoli globali del cliente. Se il valore è un token
 * (var(--olo-color-primary)) l'alfa si applica con color-mix(); se è hex/rgba
 * si converte in rgba() come fa patternCSS.colorToRgba().
 *
 * ⚠️ Mappatura ruoli: il colore di DEFAULT dei bagliori è il PRIMARIO del cliente
 * (var(--olo-color-primary), seed #e1474f). NON è l'arancio #e8622a del chrome:
 * questo è CONTENUTO di pagina, non UI dell'inspector (regola CHROME vs CONTENUTO).
 */

/** Preset di posizione degli aloni: ogni voce è una lista di hotspot { x%, y%, scale }.
 *  `scale` moltiplica glow_size per dare profondità (aloni davanti più grandi). */
export const glowPresets = [
  { value: 'spread',     label: 'Diffuso' },
  { value: 'top',        label: 'Dall’alto' },
  { value: 'top-left',   label: 'Angolo alto sx' },
  { value: 'top-right',  label: 'Angolo alto dx' },
  { value: 'center',     label: 'Centro' },
  { value: 'corners',    label: 'Agli angoli' },
  { value: 'aurora',     label: 'Aurora (in basso)' },
];

const HOTSPOTS = {
  spread:    [ { x: 12, y: 6, s: 1.15 }, { x: 88, y: 30, s: 0.9 } ],
  top:       [ { x: 50, y: -8, s: 1.25 } ],
  'top-left':[ { x: 8,  y: 4,  s: 1.2 } ],
  'top-right':[ { x: 92, y: 6, s: 1.2 } ],
  center:    [ { x: 50, y: 42, s: 1.1 } ],
  corners:   [ { x: 4, y: 4, s: 0.95 }, { x: 96, y: 96, s: 0.95 } ],
  aurora:    [ { x: 30, y: 108, s: 1.3 }, { x: 74, y: 116, s: 1.0 } ],
};

/**
 * Lettura di una manopola numerica. Regola UNICA per i due generativi (Aurora e
 * Bagliori) e per i gemelli PHP, che devono copiarla alla lettera:
 *   chiave assente · null · stringa vuota · valore non numerico = «non impostata»
 *   → vale il default;  un numero vale sempre, **anche 0**, e a riportarlo
 *   nell'intervallo pensa il clamp del chiamante, non questa funzione.
 * Serve perché `?? def` non intercetta la stringa vuota e in PHP `intval('')` vale 0:
 * era da quella asimmetria che le due rese disegnavano numeri diversi (il canvas
 * con la chiave vuota ripiegava sul default, il frontend sul minimo).
 * `parseInt` tronca come `intval()`: "70.5" → 70 di qua e di là.
 */
function numOpt(value, fallback) {
  // `typeof value === 'object'` copre array e oggetti, che il gemello PHP
  // scarta con is_array(): senza, `parseInt([5])` leggeva 5 di qua e il
  // frontend ripiegava sul default, e gli stessi dati davano due rese.
  if (value === '' || value == null || typeof value === 'object') return fallback;
  const n = parseInt(value, 10);
  return Number.isNaN(n) ? fallback : n;
}

/**
 * Un `var(--x)` NUDO dentro un layer di sfondo e' una mina: se quel token non e'
 * definito, il var() non ripiega sul valore iniziale — rende INVALIDA l'intera
 * dichiarazione `background-image`, e spariscono TUTTI i layer, non solo quello.
 * E' quello che rendeva l'Aurora un rettangolo vuoto: il terzo colore di fabbrica
 * e' `var(--olo-color-accent)`, ruolo che il renderer non stampa mai.
 *
 * Qui si aggiunge la riserva al volo, solo quando manca. Un token DEFINITO vince
 * comunque sulla riserva, quindi nessun colore gia' funzionante cambia; e non si
 * tocca ne' il valore salvato nel template ne' i token globali — cioe' i punti
 * del plugin che scrivono di loro `var(--olo-color-accent, #f4a23b)` continuano a
 * rendere la LORO riserva, come hanno sempre fatto.
 */
function conRiserva(v) {
  const m = String(v).match(/^var\(\s*(--[A-Za-z0-9_-]+)\s*\)$/);
  return m ? `var(${m[1]}, var(--olo-color-primary))` : v;
}

/** Converte hex|rgba|var(--token) + alfa (0-1) in un colore CSS valido.
 *  L'alfa si riporta in 0..1 SUBITO, come fa il gemello PHP glow_color_to_css():
 *  prima il clamp viveva solo nel ramo token, così un'intensità fuori scala
 *  arrivava al ramo hex come `rgba(…, -0.200)` — dichiarazione invalida, alone
 *  sparito, e per giunta con una resa diversa da quella del frontend. */
function glowColorToCss(input, alpha) {
  // Nessun chiamante passa vuoto (getGlowColors filtra): la riserva è comunque
  // il ruolo del cliente, mai un hex cablato.
  const s = (input || 'var(--olo-color-primary)').trim();
  const a = Math.max(0, Math.min(1, alpha));
  // Token globale del cliente → usa color-mix per applicare l'alfa
  if (s.startsWith('var(') || s.startsWith('color-mix(')) {
    const pct = Math.round(a * 100);
    return `color-mix(in srgb, ${conRiserva(s)} ${pct}%, transparent)`;
  }
  // rgba()/rgb()
  const m = s.match(/rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*([\d.]+))?\s*\)/);
  if (m) {
    const r = +m[1] || 0, g = +m[2] || 0, b = +m[3] || 0;
    const ca = m[4] != null ? parseFloat(m[4]) : 1;
    return `rgba(${r}, ${g}, ${b}, ${(ca * a).toFixed(3)})`;
  }
  // hex (#rgb | #rrggbb)
  const h = s.replace('#', '');
  let r, g, b;
  if (h.length === 3) { r = parseInt(h[0]+h[0],16); g = parseInt(h[1]+h[1],16); b = parseInt(h[2]+h[2],16); }
  else { r = parseInt(h.substring(0,2),16); g = parseInt(h.substring(2,4),16); b = parseInt(h.substring(4,6),16); }
  return `rgba(${isNaN(r)?0:r}, ${isNaN(g)?0:g}, ${isNaN(b)?0:b}, ${a.toFixed(3)})`;
}

/** Lato del tile di rumore: è la stessa misura del width/height dell'SVG, così la
 *  grana si ripete alla sua dimensione naturale invece di essere stirata. */
const GRAIN_TILE = '140px 140px';

/** Grana film come layer SVG data-URI (overlay, bassa opacità).
 *
 *  ⚠️ Nel sorgente SVG il riferimento al filtro si scrive `url(#n)`: è
 *  `encodeURIComponent` a trasformarlo in `%23` dentro il data-URI. Scriverlo già
 *  come `%23` lo faceva diventare `%2523`, dentro l'SVG il riferimento restava il
 *  letterale `url(%23n)` e non risolveva: senza filtro il `<rect>` è NERO PIENO e
 *  copriva il colore base (misurato: alfa 255 uniforme invece di 0..127). */
function grainLayer(opacity = 0.03) {
  const svg =
    `<svg xmlns='http://www.w3.org/2000/svg' width='140' height='140'>` +
    `<filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.8' numOctaves='2' seed='4'/>` +
    `<feColorMatrix values='0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 ${opacity * 16} -${opacity * 6}'/></filter>` +
    `<rect width='100%' height='100%' filter='url(#n)'/></svg>`;
  return `url("data:image/svg+xml,${encodeURIComponent(svg)}")`;
}

/** Palette aloni: glow_colors[] (nuovo) o i legacy glow_color/glow_color2, fallback al primario.
 *  Stessa gestione colori dell'Aurora (getMeshColors). */
export function getGlowColors(g = {}) {
  if (Array.isArray(g.glow_colors)) {
    const list = g.glow_colors.filter((c) => c !== '' && c != null);
    if (list.length) return list;
  }
  const legacy = [g.glow_color, g.glow_color2].filter((c) => c !== '' && c != null);
  if (legacy.length) return legacy;
  return ['var(--olo-color-primary)'];
}

/**
 * Ritorna { backgroundColor, backgroundImage, backgroundRepeat, backgroundSize
 * [, backgroundPosition, animation, --olo-glow-bs-*] } per lo sfondo "Bagliori".
 * Stessa firma-spirito di getPatternCSS: solo proprietà background-* (+ animazione).
 * Repeat e size sono liste con un valore per layer: vedi il commento più sotto.
 *
 * @param {object} g  l'oggetto bg (chiavi glow_*)
 *   glow_colors[]  palette aloni (nuovo), altrimenti i legacy qui sotto
 *   glow_base      colore di base (hex|rgba|token)             default '#0b0d12'
 *   glow_color     colore alone primario (hex|rgba|token)      default 'var(--olo-color-primary)'
 *   glow_color2    colore alone secondario (vuoto = =primario) default ''
 *   glow_preset    chiave glowPresets                          default 'spread'
 *   glow_intensity 0-100 (alfa max degli aloni)                default 55
 *   glow_size      30-120 (% raggio alone)                     default 70
 *   glow_grain     bool (grana film)                           default true
 *   glow_anim / glow_anim_speed / glow_anim_intensity → glowAnimStyle()
 */
export function getGlowCSS(g = {}) {
  const base      = g.glow_base || '#0b0d12';
  const colors    = getGlowColors(g);
  const preset    = g.glow_preset || 'spread';
  const intensity = numOpt(g.glow_intensity, 55) / 100;   // 0-1
  const sizePct   = numOpt(g.glow_size, 70);              // % falloff
  const hotspots  = HOTSPOTS[preset] || HOTSPOTS.spread;

  const layers = hotspots.map((h, i) => {
    const color = colors[i % colors.length];
    const stop  = Math.max(20, Math.min(110, Math.round(sizePct * h.s)));
    // alone in primo piano leggermente più intenso
    const a = Math.min(1, intensity * (i === 0 ? 1 : 0.8));
    // tre stop: nucleo brillante → plateau → trasparente, così su fondo scuro il
    // bagliore "accende" invece di spegnersi in un singolo fade.
    const core   = glowColorToCss(color, a);
    const mid    = glowColorToCss(color, a * 0.45);
    const fade   = glowColorToCss(color, 0);
    const midPos = Math.round(stop * 0.42);
    return `radial-gradient(circle at ${h.x}% ${h.y}%, ${core} 0%, ${mid} ${midPos}%, ${fade} ${stop}%)`;
  });

  // Dimensione e ripetizione vanno dichiarate PER LAYER, nello stesso ordine di
  // `layers`: gli aloni sono ancorati al box (cover, mai ripetuti), la grana è una
  // texture che deve ripetersi al suo passo naturale. Un valore unico varrebbe per
  // tutti e stirerebbe il rumore sull'intero riquadro, annullandolo. E le liste
  // devono essere lunghe quanto `layers` — il preset ha 1 o 2 aloni e la grana può
  // mancare: una lista più corta CSS la riciclerebbe dall'inizio, sfasando i valori.
  const sizes   = hotspots.map(() => 'cover');
  const repeats = hotspots.map(() => 'no-repeat');
  if (g.glow_grain !== false) {
    // 0.03 e non 0.06: quel valore era stato scelto quando la grana non rendeva
    // affatto (il riferimento al filtro era doppio-encodato e il layer restava un
    // rettangolo pieno), quindi non e' mai stato guardato. Su fondo scuro la
    // differenza non si nota, su fondo chiaro 0.06 legge come carta vetrata
    // mentre 0.03 legge come grana.
    layers.push(grainLayer(0.03));
    sizes.push(GRAIN_TILE);
    repeats.push('repeat');
  }

  const out = {
    backgroundColor: base,
    backgroundImage: layers.join(', '),
    backgroundRepeat: repeats.join(', '),
    backgroundSize: sizes.join(', '),
  };

  // ── Animazione bagliori (additive) ──────────────────────────────────────
  // Anima SOLO background-size/position → gli aloni respirano/derivano senza
  // muovere testo o immagini. I @keyframes (olo-glow-*) vivono in frontend.css.
  // glow_anim: none|pulse|drift|wander|flicker|scroll|vivo|tempesta ; speed 1-10.
  const anim = g.glow_anim || 'none';
  // Solo i nomi che esistono davvero, come fa la whitelist del gemello PHP: con
  // un valore inventato il ramo animato scriveva comunque background-size 140% e
  // position center, quindi il builder mostrava aloni ingranditi e ricentrati
  // mentre il sito li lasciava a `cover`.
  if (anim !== 'none' && (GLOW_ANIM_COMBO[anim] || GLOW_ANIM_EASING[anim])) {
    Object.assign(out, glowAnimStyle(anim, g.glow_anim_speed, g.glow_anim_intensity));
    // Le @keyframes non toccano piu' background-size/position: animano le tre
    // proprieta' --olo-glow-bs/bx/by, che QUI vengono assegnate ai soli aloni.
    // La grana tiene i suoi 140px fermi al centro anche mentre gli aloni si
    // muovono — prima la lista dei keyframe, lunga un valore, si riciclava su
    // tutti i layer e la stirava sull'intero riquadro.
    const conGrana = g.glow_grain !== false;
    out.backgroundSize = hotspots.map(() => ANIM_ALONE_SIZE)
      .concat(conGrana ? [GRAIN_TILE] : [])
      .join(', ');
    out.backgroundPosition = hotspots.map(() => ANIM_ALONE_POS)
      .concat(conGrana ? [GRAIN_POS] : [])
      .join(', ');
  }
  return out;
}

/** Easing per modalità (flicker a scatti, gli altri morbidi). */
const GLOW_ANIM_EASING = { pulse: 'ease-in-out', drift: 'ease-in-out', wander: 'ease-in-out', flicker: 'steps(1,end)', scroll: 'linear' };

/**
 * Preset COMBINATI: due animazioni atomiche (size + position) montate su durate
 * diverse → moto composito non-ripetitivo. `mult` desincronizza il 2° layer.
 *   vivo     = respiro morbido + orbita lenta
 *   tempesta = throb a scatti + ondeggio rapido
 */
const GLOW_ANIM_COMBO = {
  vivo:     { size: 'olo-glow-size-breathe', pos: 'olo-glow-pos-orbit', ease: 'ease-in-out', mult: 1.6 },
  tempesta: { size: 'olo-glow-size-throb',   pos: 'olo-glow-pos-sway',  ease: 'ease-in-out', mult: 0.7 },
};

/** Modalità il cui respiro (background-size) è controllabile via Intensità. */
const GLOW_BREATHE_MODES = new Set(['pulse', 'vivo']);

/** Dimensione a riposo degli aloni quando c'è un'animazione: il 40% in più del box
 *  è il margine dentro cui respirano e derivano senza scoprire gli angoli. */
// A riposo gli aloni stanno al 140%: e' il margine che lascia spazio al respiro
// e alla deriva senza scoprire i bordi del riquadro.
const ANIM_BG_SIZE_START = '140%';
// Quello che finisce nelle liste per layer: gli aloni leggono le proprieta'
// animate, la grana no.
const ANIM_ALONE_SIZE = 'var(--olo-glow-bs, 140%) var(--olo-glow-bs, 140%)';
const ANIM_ALONE_POS  = 'var(--olo-glow-bx, 50%) var(--olo-glow-by, 50%)';
const GRAIN_POS       = '0 0';

/**
 * Calcola le custom property --olo-glow-bs-min/max dall'Intensità (0-100).
 * Più intensità = oscillazione più ampia (min più piccola, max più grande).
 *
 * A non muovere le pagine già pubblicate non è un valore «neutro» di questo slider,
 * ma il chiamante: le due proprietà vengono emesse SOLO quando la chiave è presente,
 * altrimenti restano i fallback scritti nei @keyframes di frontend.css — 120%/175%
 * per olo-glow-pulse, 125%/180% per olo-glow-size-breathe. Sono due coppie diverse:
 * nessun singolo valore dello slider può riprodurle entrambe (46 dà 117%/192%).
 * @param {number} intensity 0-100
 */
function breatheVars(intensity) {
  // Qui il valore c'è per forza (lo garantisce il chiamante): 0 significa 0.
  const t = Math.max(0, Math.min(100, numOpt(intensity, 0))) / 100; // 0..1
  // min scende da 135% (calmo) a 95% (intenso); max sale da 160% a 230%.
  const min = Math.round(135 - t * 40);
  const max = Math.round(160 + t * 70);
  return { '--olo-glow-bs-min': `${min}%`, '--olo-glow-bs-max': `${max}%` };
}

/**
 * Ritorna le proprietà CSS extra per animare il glow.
 * A riposo backgroundSize 140% (margine per respiro/deriva).
 * scroll → animation-timeline: view() (fallback: loop temporale).
 * combo (vivo/tempesta) → due animazioni layerate su durate diverse.
 * @param {string} mode      pulse|drift|wander|flicker|scroll|vivo|tempesta
 * @param {number} speed     1-10 (alto = più veloce)
 * @param {number} intensity 0-100 (ampiezza del respiro; solo pulse/vivo)
 */
export function glowAnimStyle(mode, speed, intensity) {
  const sp = Math.max(1, Math.min(10, numOpt(speed, 6)));
  const dur = Math.max(2, Math.round((11 - sp) * 1.5)); // speed 6 → ~7-8s
  // I valori di PARTENZA delle tre proprieta' animate. Vanno scritti anche se le
  // @keyframes li rimpiazzano subito: dove @property non esiste una custom
  // property non dichiarata e' «guaranteed-invalid» e farebbe cadere l'intera
  // background-size. Le liste per layer le compone getGlowCSS, che sa quanti
  // aloni ci sono e se c'e' la grana.
  const css = {
    '--olo-glow-bs': ANIM_BG_SIZE_START,
    '--olo-glow-bx': '50%',
    '--olo-glow-by': '50%',
  };
  // Ampiezza respiro (solo per le modalità che animano background-size)
  if (GLOW_BREATHE_MODES.has(mode) && intensity != null && intensity !== '') {
    Object.assign(css, breatheVars(intensity));
  }
  const combo = GLOW_ANIM_COMBO[mode];
  if (combo) {
    const dur2 = Math.max(2, Math.round(dur * combo.mult));
    css.animation = `${combo.size} ${dur}s ${combo.ease} infinite, ${combo.pos} ${dur2}s ${combo.ease} infinite`;
    return css;
  }
  const ease = GLOW_ANIM_EASING[mode] || 'ease-in-out';
  css.animation = `olo-glow-${mode} ${dur}s ${ease} infinite`;
  if (mode === 'scroll') {
    css.animationTimeline = 'view()';
  }
  return css;
}
