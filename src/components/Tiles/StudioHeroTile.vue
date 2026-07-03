<template>
  <section ref="rootEl" class="olo-studiohero sth" :style="rootStyle">
    <div class="sth-wrap">
      <div v-if="s.show_meta && metaItems.length" ref="metaEl" class="sth-meta" data-sth-parallax="0.05">
        <span v-for="(m, mi) in metaItems" :key="mi"><b v-if="m.strong">{{ m.strong }}</b><template v-if="m.text">{{ m.strong ? ' ' : '' }}{{ m.text }}</template></span>
      </div>
      <div class="sth-grid">
        <div class="sth-copy">
          <p v-if="s.eyebrow" class="sth-eyebrow">{{ s.eyebrow }}</p>
          <h1 v-if="s.title_line1 || s.title_line2" class="sth-h" data-olo-wave><template v-if="s.letters_entrance"><template v-for="(ch, ci) in line1Chars" :key="ci"><span v-if="ch.trim() !== ''" class="fx-lt" :style="{ '--i': ci }">{{ ch }}</span><template v-else>{{ ch }}</template></template></template><template v-else>{{ s.title_line1 }}</template><span v-if="s.title_line2" ref="l2El" class="sth-l2">{{ s.title_line2 }}</span></h1>
          <p v-if="s.subtitle" class="sth-sub" v-html="subHtml"></p>
          <div v-if="s.cta1_text || s.cta2_text" class="sth-cta">
            <a v-if="s.cta1_text" class="sth-btn sth-btn--fill" data-olo-cta :href="s.cta1_url || '#'" @click.prevent>{{ s.cta1_text }}<svg v-if="s.cta1_show_arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
            <a v-if="s.cta2_text" class="sth-btn sth-btn--ghost" :href="s.cta2_url || '#'" @click.prevent>{{ s.cta2_text }}</a>
          </div>
        </div>
        <div v-if="hasMedia" ref="mediaEl" class="sth-media" data-sth-parallax="0.08">
          <div v-if="mapOn" class="sth-olomap" data-olo-tilt-child role="img" :aria-label="mapAria">
            <span v-if="s.map_label" class="sth-map-lab">{{ s.map_label }}</span>
            <span ref="depthEl" class="sth-map-depth"><i></i>L1 / L4</span>
            <svg ref="svgEl" class="sth-map-svg" viewBox="0 0 1000 1000" preserveAspectRatio="xMidYMid slice" aria-hidden="true"></svg>
          </div>
          <div v-else class="sth-imgbox" data-olo-tilt-child>
            <!-- Media unificato (media_bg): immagine/gradiente/colore via background + video element -->
            <div v-if="hasMediaBg" class="sth-imgbox__bg" :style="mediaBgStyle"></div>
            <video
              v-if="isMediaVideo"
              class="sth-imgbox__video"
              :src="s.media_bg.video_url"
              autoplay loop muted playsinline
            ></video>
            <!-- Fallback legacy (solo se media_bg non impostato) -->
            <img v-if="!hasMediaBg && s.media_image" :src="s.media_image" :alt="s.media_label" :style="{ objectPosition: s.media_object_position || 'center center' }" />
            <span v-else-if="!hasMediaBg && s.media_label" class="sth-ph">{{ s.media_label }}</span>
          </div>
          <span v-if="s.cap_text" ref="capEl" class="sth-cap">{{ s.cap_text }}</span>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, ref, watch, nextTick, onMounted, onBeforeUnmount } from 'vue';
import { buildBgStyle } from '@/composables/useBackgroundStyle';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const borderDefault = { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' };
const borderHoverDefault = { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: '', color: '' };

const defaults = {
  media_bg: { type: 'none' },
  eyebrow: 'R&S · divisione idee',
  eyebrow_color: '',
  title_line1: 'Visual',
  title_line2: 'studio',
  title_color: '',
  title_size_min: 74,
  title_size_max: 210,
  line2_stroke_width: 1.4,
  line2_stroke_color: '',
  line2_scroll_fill: true,
  line2_fill_color: '',
  letters_entrance: true,
  subtitle: 'Aiuto le aziende a <b>farsi vedere</b>: strategia, produzione media e identità visiva, con contenuti originali che lavorano davvero.',
  subtitle_color: '',
  show_meta: true,
  meta_items: [
    { strong: 'EST.', text: 'Trento — Italia' },
    { strong: '46.07°N', text: '11.12°E' },
    { strong: 'R&S', text: 'divisione idee' },
    { strong: '2026', text: '— project media manager' },
  ],
  cta1_text: 'Progettiamo assieme',
  cta1_url: '#contatto',
  cta1_show_arrow: true,
  cta2_text: 'Selezione progetti',
  cta2_url: '#lavori',
  accent_color: '',
  media_mode: 'olomap',
  media_image: '',
  media_object_position: 'center center',
  media_label: 'Visual studio — still',
  cap_text: 'OLObuild · sistema',
  map_label: 'Mappa del sistema',
  map_root: 'OLObuild',
  map_l1: 'Forge,Prisma*,Saffron,Soundwave,+46\\ntemi',
  map_l2: 'Hero*,Galleria,Griglia,CTA',
  map_l3: 'Spazi,Bordi,Ombra,Colore*',
  map_tokens: [
    { label: 'Primario', color: '#e1474f' },
    { label: 'Accento', color: '#f4a23b' },
    { label: 'Lime', color: '#C6F24E' },
    { label: 'Scuro', color: '#16263d' },
  ],
  map_duration: 21,
  parallax_internal: true,
  bg_color: '',

  // Spaziatura (gated): il padding di base è clamp(40px,7vw,84px) 0 clamp(44px,6vw,72px).
  // Override attivo SOLO se pad_custom=true → no-op coi default.
  pad_custom: false,
  content_padding: { top: 84, right: 0, bottom: 72, left: 0 },

  // KIT standard OLObuild — additivi, no-op coi default (sfondo none, ombra none, bordo 0)
  bg: { type: 'none' },
  shadow: 'none',
  border: { ...borderDefault },
  border_hover: { ...borderHoverDefault },
  border_hover_duration: 300,
  border_effect: 'none',
  border_effect_intensity: 'medium',
  border_effect_color2: '',
  border_effect_angle: 135,
  border_effect_speed: 4,
};

const s = computed(() => ({ ...defaults, ...props.settings }));

// ── Font (ruoli tema, fallback blueprint Clod) — IDENTICI al PHP ──
const DISP = "var(--olo-font-family-heading, 'Big Shoulders Display', sans-serif)";
const SANS = "var(--olo-font-family, 'Hanken Grotesk', sans-serif)";
const MONO = "var(--olo-font-family-mono, 'Space Mono', ui-monospace, monospace)";

// ── Colori token-first (neutri blueprint come fallback nei var()) — IDENTICI al PHP ──
const LINE  = 'var(--olo-color-border, rgba(236,234,227,.10))';
const LINE2 = 'color-mix(in srgb, var(--olo-color-text, #ECEAE3) 20%, transparent)';
const FAINT = 'var(--olo-color-text-faint, #6a6c64)';
const TSOFT = 'var(--olo-color-text-soft, #a0a298)';
const INK3  = 'var(--olo-color-muted, #161922)';
const ONACC = 'var(--olo-color-on-primary, #0b0c0f)';

const bgcol  = computed(() => s.value.bg_color || 'var(--olo-color-background, #0b0c0f)');
const txt    = computed(() => s.value.title_color || 'var(--olo-color-text, #ECEAE3)');
const acc    = computed(() => s.value.accent_color || 'var(--olo-color-primary, #C6F24E)');
const eyebrw = computed(() => s.value.eyebrow_color || acc.value);
const fillc  = computed(() => s.value.line2_fill_color || acc.value);
const subc   = computed(() => s.value.subtitle_color || 'var(--olo-color-text-soft, #a0a298)');
const stroke = computed(() => s.value.line2_stroke_color || LINE2);

// ── Dimensioni titolo / stroke — parità coi clamp PHP ──
const tmin = computed(() => {
  const v = parseInt(s.value.title_size_min, 10);
  return v > 0 ? v : 74;
});
const tmax = computed(() => {
  let v = parseInt(s.value.title_size_max, 10);
  if (!(v > 0)) v = 210;
  return v < tmin.value ? tmin.value : v;
});
const sw = computed(() => {
  let v = parseFloat(s.value.line2_stroke_width);
  if (!(v > 0)) v = 1.4;
  return Math.min(10, v);
});

const hasMedia  = computed(() => s.value.media_mode !== 'none');
const mapOn     = computed(() => s.value.media_mode === 'olomap');
const metaItems = computed(() => (Array.isArray(s.value.meta_items) ? s.value.meta_items : []).filter((m) => m && (m.strong || m.text)));
const line1Chars = computed(() => Array.from(String(s.value.title_line1 || '')));

// ── Media hero unificato (pannello media_bg) con fallback all'immagine legacy ──
// Precedenza: se media_bg è impostato (≠ none) lo usa; immagine/gradiente/colore come
// layer background (parità col box <img> object-fit), video come <video>. Fallback = <img>.
const hasMediaBg  = computed(() => {
  const m = s.value.media_bg;
  return !!(m && typeof m === 'object' && m.type && m.type !== 'none');
});
const mediaBgStyle = computed(() => (hasMediaBg.value ? buildBgStyle(s.value.media_bg) : {}));
const isMediaVideo = computed(() => {
  const m = s.value.media_bg;
  return hasMediaBg.value && m.type === 'video' && !!m.video_url;
});

// ── Sottotitolo: HTML inline passa, plain → escape + nl2br (parità PHP) ──
const subHtml = computed(() => {
  const raw = String(s.value.subtitle || '');
  if (/<[a-z!\/][^>]*>/i.test(raw)) return raw;
  const esc = raw.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  return esc.replace(/\n/g, '<br />\n');
});

// ── Spaziatura (gated): default = padding verticale responsivo del blueprint ──
const inPad = computed(() => {
  if (s.value.pad_custom && s.value.content_padding && typeof s.value.content_padding === 'object') {
    const cp = s.value.content_padding;
    const pt = parseInt(cp.top ?? 0, 10) || 0;
    const pr = parseInt(cp.right ?? 0, 10) || 0;
    const pb = parseInt(cp.bottom ?? 0, 10) || 0;
    const pl = parseInt(cp.left ?? 0, 10) || 0;
    return `${pt}px ${pr}px ${pb}px ${pl}px`;
  }
  return 'clamp(40px,7vw,84px) 0 clamp(44px,6vw,72px)';
});

// ── KIT standard: sfondo completo (override del bg di base SOLO se valorizzato) ──
const bgKitStyle = computed(() => {
  const b = s.value.bg;
  if (!b || !b.type || b.type === 'none') return {};
  return buildBgStyle(b);
});

// ── KIT standard: ombra (preset sm/md/lg/xl o custom) — parità con build_shadow_decl PHP ──
const shadowDecl = computed(() => {
  const preset = s.value.shadow || 'none';
  if (preset === 'none' || preset === '') return '';
  if (preset === 'custom') {
    const h = parseInt(s.value.shadow_h ?? 0, 10) || 0;
    const v = parseInt(s.value.shadow_v ?? 4, 10) || 0;
    const blur = Math.max(0, parseInt(s.value.shadow_blur ?? 10, 10) || 0);
    const spread = parseInt(s.value.shadow_spread ?? 0, 10) || 0;
    const color = s.value.shadow_color || 'rgba(0,0,0,0.15)';
    const inset = s.value.shadow_inset ? 'inset ' : '';
    return `${inset}${h}px ${v}px ${blur}px ${spread}px ${color}`;
  }
  const map = {
    sm: '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
    md: '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
    lg: '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
    xl: '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
  };
  return map[preset] || '';
});

// ── KIT standard: bordo — parità con parse_border/build_border_css PHP ──
const borderStyle = computed(() => {
  const b = s.value.border;
  if (!b || typeof b !== 'object') return {};
  const color = String(b.color || '').trim();
  if (color === '') return {};
  const style = b.style || 'solid';
  const t = Math.max(0, parseInt(b.top ?? 0, 10) || 0);
  const r = Math.max(0, parseInt(b.right ?? 0, 10) || 0);
  const bo = Math.max(0, parseInt(b.bottom ?? 0, 10) || 0);
  const l = Math.max(0, parseInt(b.left ?? 0, 10) || 0);
  if (!t && !r && !bo && !l) return {};
  if (t === r && r === bo && bo === l) {
    return { border: `${t}px ${style} ${color}` };
  }
  const out = {};
  if (t) out.borderTop = `${t}px ${style} ${color}`;
  if (r) out.borderRight = `${r}px ${style} ${color}`;
  if (bo) out.borderBottom = `${bo}px ${style} ${color}`;
  if (l) out.borderLeft = `${l}px ${style} ${color}`;
  return out;
});

const rootStyle = computed(() => {
  const base = {
    position: 'relative',
    padding: inPad.value,
    color: txt.value,
    fontFamily: SANS,
    lineHeight: 1.55,
    '--sth-line': LINE,
    '--sth-line2': LINE2,
    '--sth-acc': acc.value,
    '--sth-onacc': ONACC,
    '--sth-txt': txt.value,
    '--sth-subc': subc.value,
    '--sth-eyebrow': eyebrw.value,
    '--sth-stroke': stroke.value,
    '--sth-fillc': fillc.value,
    '--sth-bg': bgcol.value,
    '--sth-ink3': INK3,
    '--sth-faint': FAINT,
    '--sth-tsoft': TSOFT,
    '--sth-mono': MONO,
    '--sth-disp': DISP,
    '--sth-sw': sw.value + 'px',
    '--sth-cols': hasMedia.value ? '1.15fr .85fr' : '1fr',
    '--sth-hsize': `clamp(${tmin.value}px,15vw,${tmax.value}px)`,
  };
  const kitBg = bgKitStyle.value;
  if (kitBg && Object.keys(kitBg).length) {
    Object.assign(base, kitBg);
  } else {
    base.background = bgcol.value;
  }
  if (shadowDecl.value) base.boxShadow = shadowDecl.value;
  Object.assign(base, borderStyle.value);
  return base;
});

// ════ Runtime (replica fedele dello script PHP per-istanza) ════
const rootEl = ref(null);
const metaEl = ref(null);
const mediaEl = ref(null);
const l2El = ref(null);
const capEl = ref(null);
const depthEl = ref(null);
const svgEl = ref(null);

const reduce = typeof window !== 'undefined' && window.matchMedia
  ? window.matchMedia('(prefers-reduced-motion: reduce)').matches
  : false;

// ── OLOmap: parse livelli CSV (parità con parse_map_level PHP) ──
function parseLevel(csv) {
  const out = [];
  String(csv || '').split(',').forEach((part) => {
    let label = part.trim();
    if (label === '') return;
    let focus = false;
    if (label.endsWith('*')) {
      focus = true;
      label = label.slice(0, -1).replace(/\s+$/, '');
    }
    label = label.split('\\n').join('\n');
    out.push({ label, focus });
  });
  if (out.length && !out.some((n) => n.focus)) out[0].focus = true;
  return out;
}

function mapCfg() {
  let l1 = parseLevel(s.value.map_l1); if (!l1.length) l1 = parseLevel(defaults.map_l1);
  let l2 = parseLevel(s.value.map_l2); if (!l2.length) l2 = parseLevel(defaults.map_l2);
  let l3 = parseLevel(s.value.map_l3); if (!l3.length) l3 = parseLevel(defaults.map_l3);
  const accv = acc.value;
  const tokens = (Array.isArray(s.value.map_tokens) ? s.value.map_tokens : [])
    .filter((tk) => tk && typeof tk === 'object')
    .map((tk) => ({ label: String(tk.label || ''), color: tk.color ? tk.color : accv }));
  let durS = parseInt(s.value.map_duration, 10);
  if (!(durS > 0)) durS = 21;
  if (durS < 4) durS = 4;
  const fidx = (lvl) => { for (let i = 0; i < lvl.length; i++) { if (lvl[i].focus) return i; } return 0; };
  const nice = (lvl, idx) => lvl[idx].label.split('\n').join(' ');
  const f1 = nice(l1, fidx(l1));
  const f2 = nice(l2, fidx(l2));
  const f3 = nice(l3, fidx(l3));
  const rootLabel = String(s.value.map_root || '') || 'OLObuild';
  const capTxt = String(s.value.cap_text || '');
  const p1 = capTxt !== '' ? capTxt : rootLabel;
  const p2 = rootLabel + ' / ' + f1;
  const p3 = p2 + ' / ' + f2;
  const p4 = p3 + ' / ' + f3;
  return { root: rootLabel, l1, l2, l3, tokens, dur: durS * 1000, paths: [p1, p2, p3, p4] };
}

const mapAria = computed(() => {
  if (!mapOn.value) return '';
  const cfg = mapCfg();
  const lbl = String(s.value.map_label || '');
  return (lbl !== '' ? lbl + ': ' : '') + cfg.paths[3];
});

// ── OLOmap: motore (geometria pol(), camera dive, fade per livello, readout) ──
let mapRaf = 0;
function stopMap() {
  if (mapRaf) { cancelAnimationFrame(mapRaf); mapRaf = 0; }
}
function buildMap() {
  stopMap();
  const svg = svgEl.value;
  if (!svg) return;
  svg.innerHTML = '';
  if (!mapOn.value) return;
  const cfg = mapCfg();
  const cap = capEl.value;
  const depth = depthEl.value;
  const NS = 'http://www.w3.org/2000/svg';
  function el(t, attrs) {
    const e = document.createElementNS(NS, t);
    if (attrs) { for (const k in attrs) e.setAttribute(k, attrs[k]); }
    return e;
  }
  function pol(c, r, deg) {
    const a = deg * Math.PI / 180;
    return { x: c.x + r * Math.cos(a), y: c.y + r * Math.sin(a) };
  }
  const D = [[-110, -32, 38, 110, 175], [-150, -60, 30, 120], [-140, -50, 40, 130], [-135, -45, 45, 135]];
  function angles(n, li) {
    const a = D[li];
    if (n === a.length) return a;
    const out = [];
    for (let i = 0; i < n; i++) out.push(a[0] + i * 360 / n);
    return out;
  }
  function focusIdx(items) {
    for (let i = 0; i < items.length; i++) { if (items[i].focus) return i; }
    return 0;
  }
  const C0 = { x: 500, y: 500 }, R1 = 300, R2 = 64, R3 = 15, R4 = 3.4;
  const a1 = angles(cfg.l1.length, 0), a2 = angles(cfg.l2.length, 1), a3 = angles(cfg.l3.length, 2), a4 = angles(cfg.tokens.length, 3);
  const F1 = pol(C0, R1, a1[focusIdx(cfg.l1)]);
  const F2 = pol(F1, R2, a2[focusIdx(cfg.l2)]);
  const F3 = pol(F2, R3, a3[focusIdx(cfg.l3)]);
  const gZoom = el('g', { class: 'zoom' });
  svg.appendChild(gZoom);
  function addLink(layer, a, b, focus) {
    layer.appendChild(el('path', { class: focus ? 'lk is-focus' : 'lk', d: 'M' + a.x + ' ' + a.y + 'L' + b.x + ' ' + b.y }));
  }
  function addText(layer, x, y, label, fs) {
    const lines = String(label).split('\n');
    const t = el('text', { x, y, 'font-size': fs });
    const start = -(lines.length - 1) * 0.5 * fs;
    lines.forEach((ln, i) => {
      const ts = el('tspan', { x, dy: i === 0 ? start : fs });
      ts.textContent = ln;
      t.appendChild(ts);
    });
    layer.appendChild(t);
  }
  function addNode(layer, c, r, label, fs, opt) {
    let cls = 'nd';
    if (opt.focus) cls += ' is-focus';
    if (opt.root) cls += ' is-root';
    const g = el('g', { class: cls });
    if (opt.chip) {
      g.appendChild(el('circle', { class: 'chip', cx: c.x, cy: c.y, r, fill: opt.chip }));
      addText(g, c.x, c.y + r * 2.5, label, fs);
    } else {
      g.appendChild(el('circle', { cx: c.x, cy: c.y, r }));
      addText(g, c.x, c.y, label, fs);
    }
    layer.appendChild(g);
    return g;
  }
  function mkLayer(nat) {
    const g = el('g', { class: 'layer' });
    g._nat = nat;
    gZoom.appendChild(g);
    return g;
  }
  const L1 = mkLayer(1), L2 = mkLayer(5.15), L3 = mkLayer(22), L4 = mkLayer(97), L0 = mkLayer(0.85);
  cfg.l1.forEach((n, i) => { const p = pol(C0, R1, a1[i]); addLink(L1, C0, p, n.focus); addNode(L1, p, 54, n.label, 19, { focus: n.focus }); });
  cfg.l2.forEach((n, i) => { const p = pol(F1, R2, a2[i]); addLink(L2, F1, p, n.focus); addNode(L2, p, 11, n.label, 4.0, { focus: n.focus }); });
  cfg.l3.forEach((n, i) => { const p = pol(F2, R3, a3[i]); addLink(L3, F2, p, n.focus); addNode(L3, p, 2.55, n.label, 0.98, { focus: n.focus }); });
  cfg.tokens.forEach((n, i) => { const p = pol(F3, R4, a4[i]); addLink(L4, F3, p, false); addNode(L4, p, 0.62, n.label, 0.2, { chip: n.color }); });
  addNode(L0, C0, 58, cfg.root, 16, { root: true });
  const kf = [
    { t: 0.00, c: C0, s: 1.00 },
    { t: 0.13, c: C0, s: 1.07 },
    { t: 0.30, c: F1, s: 5.15 },
    { t: 0.45, c: F1, s: 5.45 },
    { t: 0.60, c: F2, s: 22 },
    { t: 0.72, c: F2, s: 23.2 },
    { t: 0.84, c: F3, s: 97 },
    { t: 0.90, c: F3, s: 100 },
    { t: 1.00, c: C0, s: 1.00 },
  ];
  function smooth(u) {
    if (u < 0) return 0;
    if (u > 1) return 1;
    return u * u * (3 - 2 * u);
  }
  function camAt(tt) {
    let a = kf[0], b = kf[kf.length - 1];
    for (let i = 0; i < kf.length - 1; i++) {
      if (tt >= kf[i].t) { if (tt <= kf[i + 1].t) { a = kf[i]; b = kf[i + 1]; break; } }
    }
    let den = b.t - a.t;
    if (!den) den = 1;
    const u = (tt - a.t) / den, e = smooth(u);
    const sc = Math.exp(Math.log(a.s) + (Math.log(b.s) - Math.log(a.s)) * e);
    return { x: a.c.x + (b.c.x - a.c.x) * e, y: a.c.y + (b.c.y - a.c.y) * e, s: sc };
  }
  function lvlOp(sc, nat) {
    const lr = Math.log(sc / nat);
    if (lr < -1.6) return 0;
    if (lr > 1.75) return 0;
    if (lr < -0.55) return (lr + 1.6) / 1.05;
    if (lr <= 0.9) return 1;
    return 1 - (lr - 0.9) / 0.85;
  }
  const layers = [L0, L1, L2, L3, L4];
  let lastPath = '';
  function setReadout(sc) {
    let d;
    if (sc < 2.6) d = 1;
    else if (sc < 11) d = 2;
    else if (sc < 50) d = 3;
    else d = 4;
    const path = cfg.paths[d - 1];
    if (path !== lastPath) {
      lastPath = path;
      if (cap) cap.textContent = path;
      if (depth) depth.innerHTML = '<i></i>L' + d + ' / L4';
    }
  }
  function applyCam(cam) {
    gZoom.setAttribute('transform', 'translate(' + (500 - cam.s * cam.x).toFixed(2) + ',' + (500 - cam.s * cam.y).toFixed(2) + ') scale(' + cam.s.toFixed(4) + ')');
    for (let i = 0; i < layers.length; i++) layers[i].setAttribute('opacity', lvlOp(cam.s, layers[i]._nat).toFixed(3));
    setReadout(cam.s);
  }
  if (reduce) {
    applyCam({ x: C0.x, y: C0.y, s: 1.0 });
    return;
  }
  const DUR = cfg.dur, t0 = performance.now();
  function frame(now) {
    const tt = ((now - t0) % DUR) / DUR;
    applyCam(camAt(tt));
    mapRaf = requestAnimationFrame(frame);
  }
  mapRaf = requestAnimationFrame(frame);
}

// ── Riempimento riga 2 allo scroll. Nel canvas builder lo scroller può essere
//    interno: si usa il delta del rect rispetto alla posizione iniziale (stessa
//    formula del blueprint: span = max(150,(top-80)*0.5)). Frontend = script PHP. ──
let l2Base = null;
function updFill() {
  const l2 = l2El.value;
  if (!l2) return;
  if (!s.value.line2_scroll_fill) { l2.style.removeProperty('--fill'); return; }
  const r = l2.getBoundingClientRect();
  if (l2Base === null) l2Base = r.top;
  const span = Math.max(150, (l2Base - 80) * 0.5);
  const p = Math.max(0, Math.min(1, (l2Base - r.top) / span));
  l2.style.setProperty('--fill', (p * 100).toFixed(1) + '%');
}

// ── Parallax interno (meta .05 / media .08) — translate3d dalla distanza dal centro ──
function updParallax() {
  const pairs = [[metaEl.value, 0.05], [mediaEl.value, 0.08]];
  const vh = window.innerHeight;
  pairs.forEach(([el, sp]) => {
    if (!el) return;
    if (reduce || !s.value.parallax_internal) { el.style.transform = ''; return; }
    const r = el.getBoundingClientRect();
    if (r.bottom < -200) return;
    if (r.top > vh + 200) return;
    const c = (r.top + r.height / 2) - vh / 2;
    el.style.transform = 'translate3d(0,' + (-c * sp).toFixed(1) + 'px,0)';
  });
}

let fillTick = false;
let parTick = false;
function onScroll() {
  if (!fillTick) {
    fillTick = true;
    requestAnimationFrame(() => { fillTick = false; updFill(); });
  }
  if (!parTick) {
    parTick = true;
    requestAnimationFrame(() => { parTick = false; updParallax(); });
  }
}
function onResize() {
  l2Base = null;
  updFill();
  updParallax();
}

const mapSig = computed(() => JSON.stringify([
  s.value.media_mode, s.value.map_root, s.value.map_l1, s.value.map_l2, s.value.map_l3,
  s.value.map_tokens, s.value.map_duration, s.value.cap_text, s.value.accent_color,
]));

watch(mapSig, () => { nextTick(buildMap); });
watch(() => [s.value.parallax_internal, s.value.line2_scroll_fill], () => {
  nextTick(() => { updFill(); updParallax(); });
});

onMounted(() => {
  window.addEventListener('scroll', onScroll, { passive: true, capture: true });
  window.addEventListener('resize', onResize);
  updFill();
  updParallax();
  buildMap();
});

onBeforeUnmount(() => {
  window.removeEventListener('scroll', onScroll, { capture: true });
  window.removeEventListener('resize', onResize);
  stopMap();
});
</script>

<style scoped>
.sth-wrap { max-width: 1280px; margin: 0 auto; padding-left: clamp(20px, 5vw, 72px); padding-right: clamp(20px, 5vw, 72px); }
.sth-meta { display: flex; gap: 26px; flex-wrap: wrap; border-bottom: 1px solid var(--sth-line); padding-bottom: 20px; margin-bottom: clamp(28px, 4vw, 46px); }
.sth-meta span { font-family: var(--sth-mono); font-size: 12px; letter-spacing: .05em; color: var(--sth-faint); }
.sth-meta span b { color: var(--sth-tsoft); font-weight: 400; }
.sth-grid { display: grid; grid-template-columns: var(--sth-cols); gap: clamp(28px, 4vw, 56px); align-items: end; }
@media (max-width: 880px) { .sth-grid { grid-template-columns: 1fr; } }
.sth-eyebrow { margin: 0; font-family: var(--sth-mono); font-size: 12.5px; letter-spacing: .18em; text-transform: uppercase; color: var(--sth-eyebrow); }
.sth-h { margin: 0; font-family: var(--sth-disp); font-weight: 800; line-height: .92; letter-spacing: -.01em; text-transform: uppercase; font-size: var(--sth-hsize); color: var(--sth-txt); }
.sth-h .fx-lt { display: inline-block; }
@media (prefers-reduced-motion: no-preference) {
  .sth-h .fx-lt { animation: sth-lt .8s cubic-bezier(.16, .8, .26, 1) both; animation-delay: calc(var(--i) * 55ms); }
}
@keyframes sth-lt {
  from { opacity: 0; transform: translateY(.32em) rotate(3deg); filter: blur(7px); }
  to { opacity: 1; transform: none; filter: none; }
}
.sth-l2 { display: block; color: var(--sth-bg); -webkit-text-stroke: var(--sth-sw) var(--sth-stroke); background: linear-gradient(var(--sth-fillc), var(--sth-fillc)) bottom/100% var(--fill, 0%) no-repeat; -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; transition: --fill .1s linear; }
.sth-sub { margin: 26px 0 0; max-width: 30ch; font-size: clamp(17px, 2vw, 21px); line-height: 1.5; color: var(--sth-subc); }
.sth-sub :deep(b) { color: var(--olo-color-text, #ECEAE3); font-weight: 600; }
.sth-cta { display: flex; gap: 14px; margin-top: 30px; flex-wrap: wrap; }
.sth-btn { display: inline-flex; align-items: center; gap: 9px; font-weight: 700; font-size: 14.5px; padding: 13px 22px; border-radius: 8px; text-decoration: none; cursor: pointer; transition: transform .14s, background .15s, border-color .15s, color .15s; }
.sth-btn svg { width: 16px; height: 16px; }
.sth-btn--fill { background: var(--sth-acc); color: var(--sth-onacc); }
.sth-btn--fill:hover { background: color-mix(in srgb, var(--sth-acc) 85%, #000); transform: translateY(-2px); }
.sth-btn--ghost { border: 1px solid var(--sth-line2); color: var(--sth-txt); }
.sth-btn--ghost:hover { border-color: var(--sth-acc); color: var(--sth-acc); }
.sth-btn:focus-visible { outline: none; box-shadow: 0 0 0 3px color-mix(in srgb, var(--sth-acc) 30%, transparent); }
.sth-media { position: relative; perspective: 1100px; }
.sth-olomap {
  position: relative; width: 100%; height: clamp(320px, 42vw, 500px); overflow: hidden;
  border: 1px solid var(--sth-line2);
  background:
    radial-gradient(120% 90% at 70% 30%, color-mix(in srgb, var(--sth-acc) 5%, transparent), transparent 60%),
    radial-gradient(100% 100% at 50% 50%, var(--sth-ink3), var(--sth-bg) 78%);
  transition: transform .25s cubic-bezier(.2, .7, .3, 1); transform-style: preserve-3d; will-change: transform;
}
.sth-olomap::before {
  content: ""; position: absolute; inset: 0; pointer-events: none; z-index: 3;
  background:
    linear-gradient(color-mix(in srgb, var(--olo-color-text, #ECEAE3) 2.5%, transparent) 1px, transparent 1px) 0 0/100% 26px,
    linear-gradient(90deg, color-mix(in srgb, var(--olo-color-text, #ECEAE3) 2.5%, transparent) 1px, transparent 1px) 0 0/26px 100%;
  -webkit-mask: radial-gradient(120% 120% at 50% 50%, #000, transparent 75%);
  mask: radial-gradient(120% 120% at 50% 50%, #000, transparent 75%);
}
.sth-olomap::after {
  content: ""; position: absolute; inset: 0; pointer-events: none; z-index: 4;
  box-shadow: inset 0 0 60px 8px rgba(8, 9, 12, .55);
}
.sth-map-svg { position: absolute; inset: 0; width: 100%; height: 100%; display: block; z-index: 2; }
.sth-map-lab { position: absolute; left: 14px; top: 13px; z-index: 5; font-family: var(--sth-mono); font-size: 10px; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; color: var(--sth-tsoft); }
.sth-map-depth { position: absolute; right: 14px; top: 13px; z-index: 5; display: flex; align-items: center; gap: 7px; font-family: var(--sth-mono); font-size: 10px; font-weight: 700; letter-spacing: .08em; color: var(--sth-acc); }
.sth-map-depth :deep(i) { width: 6px; height: 6px; border-radius: 50%; background: var(--sth-acc); box-shadow: 0 0 8px 1px color-mix(in srgb, var(--sth-acc) 60%, transparent); }
.sth-olomap :deep(.nd circle) { fill: var(--sth-ink3); stroke: var(--sth-line2); stroke-width: 1.4; vector-effect: non-scaling-stroke; }
.sth-olomap :deep(.nd text) { fill: var(--sth-txt); font-family: var(--sth-disp); font-weight: 700; text-transform: uppercase; text-anchor: middle; dominant-baseline: middle; letter-spacing: -.01em; }
.sth-olomap :deep(.nd.is-focus circle) { stroke: var(--sth-acc); stroke-width: 2.2; vector-effect: non-scaling-stroke; }
.sth-olomap :deep(.nd.is-root circle) { fill: var(--sth-acc); stroke: none; }
.sth-olomap :deep(.nd.is-root text) { fill: var(--sth-onacc); }
.sth-olomap :deep(.lk) { stroke: var(--sth-line2); stroke-width: 1.2; fill: none; vector-effect: non-scaling-stroke; }
.sth-olomap :deep(.lk.is-focus) { stroke: var(--sth-acc); stroke-width: 1.6; vector-effect: non-scaling-stroke; }
.sth-imgbox {
  position: relative; width: 100%; height: clamp(320px, 42vw, 500px); overflow: hidden;
  border: 1px solid var(--sth-line2); background: var(--sth-ink3);
  transition: transform .25s cubic-bezier(.2, .7, .3, 1); transform-style: preserve-3d; will-change: transform;
}
.sth-imgbox img { width: 100%; height: 100%; object-fit: cover; display: block; }
.sth-imgbox__bg { position: absolute; inset: 0; background-size: cover; background-position: center; background-repeat: no-repeat; }
.sth-imgbox__video { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
.sth-imgbox .sth-ph { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-family: var(--sth-mono); font-size: 12px; letter-spacing: .08em; text-transform: uppercase; color: var(--sth-tsoft); }
.sth-cap { position: absolute; left: 14px; bottom: 14px; font-family: var(--sth-mono); font-size: 11px; letter-spacing: .06em; text-transform: uppercase; color: var(--olo-color-text, #ECEAE3); background: color-mix(in srgb, var(--olo-color-background, #0b0c0f) 60%, transparent); -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px); padding: 7px 11px; border-radius: 6px; border: 1px solid var(--sth-line2); }
@media (prefers-reduced-motion: reduce) {
  .sth-olomap { transition: none; }
  .sth-imgbox { transition: none; }
}
</style>
