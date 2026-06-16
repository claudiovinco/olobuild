<template>
  <section :class="['olo-filmreel', uid]" :style="kitStyle">
    <component :is="'style'">{{ cssText }}</component>
    <div v-if="s.show_title || (s.show_hint && s.hint_text !== '')" class="ofr-bar">
      <h2 v-if="s.show_title" class="ofr-title" data-olo-wave>{{ s.title }}</h2>
      <span v-if="s.show_hint && s.hint_text !== ''" class="ofr-hint"><span>{{ s.hint_text }}</span> <svg viewBox="0 0 40 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 7h34M30 2l6 5-6 5"/></svg></span>
    </div>
    <div ref="scrollerEl" class="ofr-scroller" data-olo-tilt-child role="region" :aria-label="s.title !== '' ? s.title : t('Lavori')" tabindex="0">
      <div class="ofr-track">
        <div v-if="s.show_intro" class="ofr-pcap">
          <span v-if="s.intro_eyebrow !== ''" class="ofr-eyebrow">{{ s.intro_eyebrow }}</span>
          <p v-if="s.intro_text !== ''">{{ s.intro_text }}</p>
        </div>
        <component
          v-for="(it, i) in items"
          :key="'f' + i"
          :is="itemLink(it) !== '' ? 'a' : 'article'"
          :href="itemLink(it) !== '' ? itemLink(it) : undefined"
          :class="['ofr-item', sizeClass(it)]"
          :data-olo-cta="i === 0 ? '' : undefined"
        >
          <video v-if="itemMediaKind(it) === 'video'" class="ofr-img" :src="it.media_bg.video_url" :poster="it.media_bg.video_poster || undefined" autoplay muted loop playsinline aria-hidden="true"></video>
          <span v-else-if="itemMediaKind(it) === 'bg'" class="ofr-img" :style="buildBgStyle(it.media_bg)"></span>
          <img v-else-if="(it.image || '') !== ''" class="ofr-img" :src="it.image" :alt="it.name || ''" />
          <span v-else class="ofr-ph"><span v-if="(it.media_label || '') !== ''">{{ it.media_label }}</span></span>
          <span v-if="s.rec_overlay" class="ofr-rec" aria-hidden="true">
            <span class="ofr-vf"><span class="tl"></span><span class="tr"></span><span class="bl"></span><span class="br"></span></span>
            <span class="ofr-recbadge"><i></i>REC</span>
            <span class="ofr-tc" data-ofr-tc>00:00:00</span>
          </span>
          <span v-if="(it.name || '') !== '' || (it.tag || '') !== ''" class="ofr-meta">
            <span v-if="(it.name || '') !== ''" class="ofr-name">{{ it.name }}</span>
            <span v-if="(it.tag || '') !== ''" class="ofr-tag">{{ it.tag }}</span>
          </span>
        </component>
      </div>
    </div>
    <div v-if="s.progress_bar" class="ofr-prog"><i ref="progEl"></i></div>
  </section>
</template>

<script setup>
/**
 * Film Reel — anteprima WYSIWYG del reel orizzontale cinematografico "Lavori"
 * (blueprint "Clod — Evoluzione v2", section.reel). CSS iniettato per istanza
 * con le STESSE stringhe del render PHP (Olo_FilmReel_Tile); runtime canvas
 * replica drag + rotella + snap + progress + skew da velocità + REC timecode
 * in onMounted, con cleanup completo in onBeforeUnmount.
 */
import { computed, ref, onMounted, onBeforeUnmount } from 'vue';
import { resolveColor } from '@/composables/oloTileDefaults';
import { buildBgStyle } from '@/composables/useBackgroundStyle';
import { t } from '@/i18n';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const borderDefault = { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' };
const borderHoverDefault = { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: '', color: '' };

const defaults = {
  title: 'Lavori',
  show_title: true,
  hint_text: 'Trascina · rotella · scorri in orizzontale',
  show_hint: true,
  intro_eyebrow: 'Selezione · photograph & video',
  intro_text: 'Nove progetti tra industria, retail, ritratto ed eventi. Trascina i tuoi fotogrammi nelle cornici.',
  show_intro: true,
  items: [
    { image: '', media_label: 'Comifo — still', name: 'Comifo', tag: 'Industriale · Video', size: 'tall', link: '' },
    { image: '', media_label: 'Valorizza', name: 'Valorizza', tag: 'Retail · Video', size: 'short', link: '' },
    { image: '', media_label: 'Confesercenti', name: 'Confesercenti', tag: 'Istituzionale', size: 'normal', link: '' },
    { image: '', media_label: 'Foto tecniche', name: 'Foto tecniche', tag: 'Industria · Foto', size: 'short', link: '' },
    { image: '', media_label: 'Wedding', name: 'Wedding', tag: 'Event · Video', size: 'tall', link: '' },
    { image: '', media_label: 'Darja Wilson', name: 'Darja Wilson', tag: 'Ritratto', size: 'normal', link: '' },
    { image: '', media_label: 'Antibrina', name: 'Antibrina', tag: 'Industriale', size: 'short', link: '' },
    { image: '', media_label: 'Industry', name: 'Industry', tag: 'Industria · Foto', size: 'normal', link: '' },
    { image: '', media_label: 'Event', name: 'Event', tag: 'Evento · Video', size: 'tall', link: '' },
  ],
  rec_overlay: true,
  velocity_skew: true,
  skew_max: '7',
  progress_bar: true,
  progress_color: '',
  accent: '',
  bg_color: '',
  border_color: '',

  // Punto focale globale (object-position) di immagini/video nei fotogrammi.
  media_object_position: 'center center',

  // Spaziatura (gated): padding verticale di base clamp(42px,6vw,78px) 0.
  // Override attivo SOLO se pad_custom=true → no-op coi default.
  pad_custom: false,
  padding: { top: 78, right: 0, bottom: 78, left: 0 },

  // KIT standard OLObuild — sfondo completo + ombra + bordo sul contenitore.
  // Default no-op: bg none / shadow none / border 0 → render invariato.
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
const items = computed(() => (Array.isArray(s.value.items) ? s.value.items : []));

const uid = 'ofr-' + Math.floor(10000 + Math.random() * 90000);

// ── Token (parità byte col PHP) ──
const DISP = "var(--olo-font-family-heading, 'Big Shoulders Display',sans-serif)";
const SANS = "var(--olo-font-family, 'Hanken Grotesk',sans-serif)";
const MONO = "var(--olo-font-family-mono, 'Space Mono',ui-monospace,monospace)";
const PAD = 'clamp(20px,5vw,72px)';
const TEXT = 'var(--olo-color-text, #ECEAE3)';
const MUTED = 'var(--olo-color-text-muted, #a0a298)';
const ITEMBG = 'var(--olo-color-muted, #161922)';

const accent = computed(() => resolveColor(s.value.accent, 'var(--olo-color-primary, #C6F24E)'));
const bg = computed(() => resolveColor(s.value.bg_color, 'var(--olo-color-surface-alt, #101218)'));
const line = computed(() => resolveColor(s.value.border_color, 'var(--olo-color-border, rgba(236,234,227,.10))'));
const line2 = computed(() => resolveColor(s.value.border_color, 'rgba(236,234,227,.20)'));
const prog = computed(() => resolveColor(s.value.progress_color, accent.value));
const skmax = computed(() => Math.max(0, Math.min(20, parseFloat(s.value.skew_max !== '' ? s.value.skew_max : 7) || 0)));
// Punto focale globale (object-position) — '' → 'center center' (= resa attuale).
const objPos = computed(() => {
  const v = String(s.value.media_object_position ?? '').trim();
  return v !== '' ? v : 'center center';
});

function sizeClass(it) {
  const sz = it.size || 'normal';
  if (sz === 'tall') return 'tall';
  if (sz === 'short') return 'short';
  return '';
}
function itemLink(it) {
  return String(it.link || '').trim();
}
// Media del fotogramma: 'video' (media_bg video con url) · 'bg' (solid/gradient/image
// via media_bg) · 'none' (fallback al campo image legacy o al placeholder).
function itemMediaKind(it) {
  const bg = it && it.media_bg;
  if (!bg || !bg.type || bg.type === 'none') return 'none';
  if (bg.type === 'video') return String(bg.video_url || '').trim() !== '' ? 'video' : 'none';
  return 'bg';
}

// ── Spaziatura (gated): default = padding verticale responsivo invariato ──
const rootPad = computed(() => {
  if (s.value.pad_custom && s.value.padding && typeof s.value.padding === 'object') {
    const pv = s.value.padding;
    const pt = parseInt(pv.top ?? 0, 10) || 0;
    const pr = parseInt(pv.right ?? 0, 10) || 0;
    const pb = parseInt(pv.bottom ?? 0, 10) || 0;
    const pl = parseInt(pv.left ?? 0, 10) || 0;
    return `${pt}px ${pr}px ${pb}px ${pl}px`;
  }
  return 'clamp(42px,6vw,78px) 0';
});

// ── CSS per istanza — STESSE stringhe del render PHP (Olo_FilmReel_Tile) ──
const cssText = computed(() => {
  const u = '.' + uid;
  let css = '';
  css += `${u}{position:relative;color:${TEXT};font-family:${SANS};border-top:1px solid ${line2.value};border-bottom:1px solid ${line2.value};padding:${rootPad.value};overflow:hidden;background:${bg.value};}`;
  css += `${u} .ofr-bar{display:flex;align-items:baseline;justify-content:space-between;gap:20px;padding:0 ${PAD};flex-wrap:wrap;}`;
  css += `${u} .ofr-title{font-family:${DISP};font-weight:800;font-size:clamp(40px,6vw,82px);line-height:.92;letter-spacing:-.01em;text-transform:uppercase;color:${TEXT};margin:0;}`;
  css += `${u} .ofr-hint{font-family:${MONO};font-size:12px;letter-spacing:.06em;text-transform:uppercase;color:${MUTED};display:inline-flex;align-items:center;gap:9px;}`;
  css += `${u} .ofr-hint svg{width:24px;height:13px;color:${accent.value};flex:none;}`;
  css += `${u} .ofr-scroller{overflow-x:auto;overflow-y:hidden;scroll-snap-type:x proximity;-webkit-overflow-scrolling:touch;cursor:grab;scrollbar-width:none;margin-top:8px;}`;
  css += `${u} .ofr-scroller::-webkit-scrollbar{display:none;}`;
  css += `${u} .ofr-scroller.drag{cursor:grabbing;scroll-snap-type:none;}`;
  css += `${u} .ofr-scroller.drag *{pointer-events:none;}`;
  css += `${u} .ofr-scroller:focus-visible{outline:none;box-shadow:inset 0 0 0 3px color-mix(in srgb, ${accent.value} 30%, transparent);}`;
  css += `${u} .ofr-track{display:flex;gap:clamp(14px,1.8vw,26px);padding:26px ${PAD};width:max-content;}`;
  css += `${u} .ofr-pcap{flex:0 0 clamp(220px,22vw,300px);display:flex;flex-direction:column;justify-content:center;padding-right:20px;align-self:center;}`;
  css += `${u} .ofr-eyebrow{display:block;margin-bottom:16px;font-family:${MONO};font-size:12.5px;letter-spacing:.18em;text-transform:uppercase;color:${accent.value};}`;
  css += `${u} .ofr-pcap p{color:${MUTED};font-size:15px;line-height:1.6;margin:14px 0 0;max-width:30ch;}`;
  css += `${u} .ofr-item{position:relative;flex:0 0 clamp(260px,30vw,420px);height:clamp(320px,56vh,560px);overflow:hidden;border:1px solid ${line.value};background:${ITEMBG};scroll-snap-align:center;display:block;color:${TEXT};text-decoration:none;}`;
  css += `${u} .ofr-item.tall{height:clamp(360px,62vh,610px);align-self:flex-start;}`;
  css += `${u} .ofr-item.short{height:clamp(260px,46vh,460px);align-self:center;}`;
  css += `${u} .ofr-item:focus-visible{outline:none;box-shadow:0 0 0 3px color-mix(in srgb, ${accent.value} 30%, transparent);}`;
  css += `${u} .ofr-img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:${objPos.value};background-position:${objPos.value};display:block;}`;
  css += `${u} .ofr-ph{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(236,234,227,.05);}`;
  css += `${u} .ofr-ph span{font-family:${MONO};font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:${MUTED};text-align:center;padding:0 18px;}`;
  css += `${u} .ofr-meta{position:absolute;left:0;right:0;bottom:0;display:flex;align-items:flex-end;justify-content:space-between;gap:10px;padding:14px 15px;background:linear-gradient(transparent,rgba(8,9,12,.82));pointer-events:none;z-index:5;}`;
  css += `${u} .ofr-name{font-family:${DISP};font-weight:700;font-size:22px;text-transform:uppercase;line-height:1;color:${TEXT};}`;
  css += `${u} .ofr-tag{font-family:${MONO};font-size:10px;letter-spacing:.08em;text-transform:uppercase;color:${accent.value};white-space:nowrap;}`;
  if (s.value.progress_bar) {
    css += `${u} .ofr-prog{margin:6px ${PAD} 0;height:2px;background:${line.value};position:relative;}`;
    css += `${u} .ofr-prog i{position:absolute;left:0;top:0;height:100%;width:0;background:${prog.value};transition:width .08s linear;}`;
  }
  if (s.value.rec_overlay) {
    css += `${u} .ofr-rec{position:absolute;inset:0;z-index:4;pointer-events:none;opacity:0;transition:opacity .28s ease;}`;
    css += `${u} .ofr-item:hover .ofr-rec{opacity:1;}`;
    css += `${u} .ofr-rec::after{content:"";position:absolute;inset:0;background:repeating-linear-gradient(to bottom,rgba(255,255,255,.05) 0 1px,transparent 1px 3px);mix-blend-mode:overlay;}`;
    css += `${u} .ofr-vf{position:absolute;inset:12px;}`;
    css += `${u} .ofr-vf span{position:absolute;width:15px;height:15px;border:2px solid rgba(255,255,255,.85);}`;
    css += `${u} .ofr-vf .tl{left:0;top:0;border-right:0;border-bottom:0;}`;
    css += `${u} .ofr-vf .tr{right:0;top:0;border-left:0;border-bottom:0;}`;
    css += `${u} .ofr-vf .bl{left:0;bottom:0;border-right:0;border-top:0;}`;
    css += `${u} .ofr-vf .br{right:0;bottom:0;border-left:0;border-top:0;}`;
    css += `${u} .ofr-recbadge{position:absolute;left:20px;top:20px;display:flex;align-items:center;gap:7px;font-family:${MONO};font-size:11px;font-weight:700;letter-spacing:.14em;color:#fff;text-shadow:0 1px 7px rgba(0,0,0,.8);}`;
    css += `${u} .ofr-recbadge i{width:9px;height:9px;border-radius:50%;background:${accent.value};box-shadow:0 0 10px 1px color-mix(in srgb, ${accent.value} 85%, transparent);animation:${uid}-recblink 1.1s steps(2,end) infinite;}`;
    css += `@keyframes ${uid}-recblink{50%{opacity:.18;}}`;
    css += `${u} .ofr-tc{position:absolute;right:20px;top:20px;font-family:${MONO};font-size:11px;font-weight:700;letter-spacing:.05em;color:#fff;text-shadow:0 1px 7px rgba(0,0,0,.8);font-variant-numeric:tabular-nums;}`;
    css += `@media(prefers-reduced-motion:reduce){${u} .ofr-recbadge i{animation:none;}}`;
  }
  return css;
});

// ── KIT standard (sfondo completo / ombra / bordo) — no-op coi default.
// Inline sul root: vince sulla regola di .uid SOLO se l'utente lo valorizza.
const SHADOW_MAP = {
  sm: '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
  md: '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
  lg: '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
  xl: '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
};
const shadowDecl = computed(() => {
  const p = s.value.shadow || 'none';
  if (p === 'none' || p === '') return '';
  if (p === 'custom') {
    const h = parseInt(s.value.shadow_h ?? 0, 10) || 0;
    const v = parseInt(s.value.shadow_v ?? 4, 10) || 0;
    const blur = Math.max(0, parseInt(s.value.shadow_blur ?? 10, 10) || 0);
    const spread = parseInt(s.value.shadow_spread ?? 0, 10) || 0;
    const color = s.value.shadow_color || 'rgba(0,0,0,0.15)';
    const inset = s.value.shadow_inset ? 'inset ' : '';
    return `${inset}${h}px ${v}px ${blur}px ${spread}px ${color}`;
  }
  return SHADOW_MAP[p] || '';
});
const borderDecl = computed(() => {
  const b = s.value.border;
  if (!b || typeof b !== 'object') return null;
  const color = String(b.color || '').trim();
  if (color === '') return null;
  const style = b.style || 'solid';
  const top = Math.max(0, parseInt(b.top, 10) || 0);
  const right = Math.max(0, parseInt(b.right, 10) || 0);
  const bottom = Math.max(0, parseInt(b.bottom, 10) || 0);
  const left = Math.max(0, parseInt(b.left, 10) || 0);
  if (!top && !right && !bottom && !left) return null;
  if (top === right && right === bottom && bottom === left) {
    return { border: `${top}px ${style} ${color}` };
  }
  const st = {};
  if (top) st.borderTop = `${top}px ${style} ${color}`;
  if (right) st.borderRight = `${right}px ${style} ${color}`;
  if (bottom) st.borderBottom = `${bottom}px ${style} ${color}`;
  if (left) st.borderLeft = `${left}px ${style} ${color}`;
  return st;
});
const bgKitStyle = computed(() => {
  const b = s.value.bg;
  if (!b || !b.type || b.type === 'none') return {};
  return buildBgStyle(b);
});
// Come il PHP: il KIT decora il <section> root. Inline style → vince sulla
// regola .uid del CSS iniettato SOLO quando l'utente valorizza il KIT.
const kitStyle = computed(() => {
  const st = { ...bgKitStyle.value };
  const bd = borderDecl.value;
  if (bd) Object.assign(st, bd);
  if (shadowDecl.value) st.boxShadow = shadowDecl.value;
  return st;
});

// ── Runtime canvas: drag + rotella + progress + skew + REC (con cleanup) ──
const scrollerEl = ref(null);
const progEl = ref(null);
const cleanups = [];

onMounted(() => {
  const sc = scrollerEl.value;
  if (!sc) return;
  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Progress bar.
  const updateProg = () => {
    const p = progEl.value;
    if (!p) return;
    const max = sc.scrollWidth - sc.clientWidth;
    p.style.width = (max > 0 ? (sc.scrollLeft / max) * 100 : 0).toFixed(1) + '%';
  };
  sc.addEventListener('scroll', updateProg, { passive: true });
  cleanups.push(() => sc.removeEventListener('scroll', updateProg));
  updateProg();

  // Rotella verticale → orizzontale (lascia scorrere la pagina ai bordi).
  const onWheel = (e) => {
    if (Math.abs(e.deltaY) <= Math.abs(e.deltaX)) return;
    const max = sc.scrollWidth - sc.clientWidth;
    const atStart = sc.scrollLeft <= 0;
    const atEnd = sc.scrollLeft >= max - 1;
    if ((e.deltaY < 0 && atStart) || (e.deltaY > 0 && atEnd)) return;
    e.preventDefault();
    sc.scrollLeft += e.deltaY;
  };
  sc.addEventListener('wheel', onWheel, { passive: false });
  cleanups.push(() => sc.removeEventListener('wheel', onWheel));

  // Drag to scroll (+ soppressione click dopo drag).
  let down = false, sx = 0, sl = 0, moved = 0;
  const onDown = (e) => { down = true; moved = 0; sx = e.clientX; sl = sc.scrollLeft; sc.classList.add('drag'); };
  const onMove = (e) => {
    if (!down) return;
    const dx = e.clientX - sx;
    moved = Math.max(moved, Math.abs(dx));
    sc.scrollLeft = sl - dx;
  };
  const onUp = () => {
    if (!down) return;
    down = false;
    sc.classList.remove('drag');
  };
  const onClick = (e) => { if (moved > 6) e.preventDefault(); };
  sc.addEventListener('pointerdown', onDown);
  window.addEventListener('pointermove', onMove);
  window.addEventListener('pointerup', onUp);
  sc.addEventListener('click', onClick, true);
  cleanups.push(() => {
    sc.removeEventListener('pointerdown', onDown);
    window.removeEventListener('pointermove', onMove);
    window.removeEventListener('pointerup', onUp);
    sc.removeEventListener('click', onClick, true);
  });

  // Skew dei fotogrammi proporzionale alla velocità di scroll (blueprint [7b]).
  if (!reduce && s.value.velocity_skew) {
    const clampv = (v, a, b) => Math.max(a, Math.min(b, v));
    let last = sc.scrollLeft, sk = 0, raf = 0;
    const loop = () => {
      const its = sc.querySelectorAll('.ofr-item');
      const v = sc.scrollLeft - last; last = sc.scrollLeft;
      sk += (clampv(v * 0.18, -skmax.value, skmax.value) - sk) * 0.14;
      if (Math.abs(sk) < 0.04 && v === 0) {
        its.forEach((it) => { it.style.transform = ''; });
        raf = 0;
        return;
      }
      its.forEach((it) => { it.style.transform = 'skewX(' + (-sk).toFixed(2) + 'deg)'; });
      raf = requestAnimationFrame(loop);
    };
    const onSkewScroll = () => { if (!raf) raf = requestAnimationFrame(loop); };
    sc.addEventListener('scroll', onSkewScroll, { passive: true });
    cleanups.push(() => {
      sc.removeEventListener('scroll', onSkewScroll);
      if (raf) cancelAnimationFrame(raf);
    });
  }

  // REC timecode mm:ss:ff (25fps) — delegato, robusto al re-render degli item.
  if (s.value.rec_overlay) {
    const rafs = new Map();
    const pad = (n) => (n < 10 ? '0' : '') + n;
    const startTc = (it) => {
      const tc = it.querySelector('[data-ofr-tc]');
      if (!tc) return;
      const start = performance.now();
      const prev = rafs.get(it);
      if (prev) cancelAnimationFrame(prev);
      const tick = (now) => {
        const el = (now - start) / 1000;
        tc.textContent = pad(Math.floor(el / 60)) + ':' + pad(Math.floor(el % 60)) + ':' + pad(Math.floor((el * 25) % 25));
        rafs.set(it, requestAnimationFrame(tick));
      };
      rafs.set(it, requestAnimationFrame(tick));
    };
    const stopTc = (it) => {
      const prev = rafs.get(it);
      if (prev) cancelAnimationFrame(prev);
      rafs.delete(it);
    };
    const onOver = (e) => {
      if (reduce) return;
      const it = e.target.closest('.ofr-item');
      if (!it) return;
      if (it.contains(e.relatedTarget)) return;
      startTc(it);
    };
    const onOut = (e) => {
      const it = e.target.closest('.ofr-item');
      if (!it) return;
      if (it.contains(e.relatedTarget)) return;
      stopTc(it);
    };
    sc.addEventListener('pointerover', onOver);
    sc.addEventListener('pointerout', onOut);
    cleanups.push(() => {
      sc.removeEventListener('pointerover', onOver);
      sc.removeEventListener('pointerout', onOut);
      rafs.forEach((r) => cancelAnimationFrame(r));
      rafs.clear();
    });
  }
});

onBeforeUnmount(() => {
  cleanups.forEach((fn) => fn());
  cleanups.length = 0;
});
</script>
