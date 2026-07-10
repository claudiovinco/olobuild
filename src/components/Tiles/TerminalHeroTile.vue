<template>
  <section class="olo-terminalhero tph" :style="rootStyle">
    <template v-if="s.show_crosshairs">
      <span class="tph-cross tph-cross--tl" :style="crossStyle" aria-hidden="true"></span>
      <span class="tph-cross tph-cross--tr" :style="crossStyle" aria-hidden="true"></span>
      <span class="tph-cross tph-cross--bl" :style="crossStyle" aria-hidden="true"></span>
      <span class="tph-cross tph-cross--br" :style="crossStyle" aria-hidden="true"></span>
    </template>
    <img v-if="s.img_left" class="tph-side tph-side--left" :src="s.img_left" :style="sideStyle" alt="" aria-hidden="true" loading="lazy" />
    <img v-if="s.img_right" class="tph-side tph-side--right" :src="s.img_right" :style="sideStyle" alt="" aria-hidden="true" loading="lazy" />

    <div class="tph-in" :style="inStyle">
      <div v-if="s.show_label && labelParts.length" class="tph-label" :style="labelStyle">
        <template v-for="(p, i) in labelParts" :key="i">
          <span v-if="p.chip" class="tph-chip" :style="chipStyle">{{ p.text }}</span>
          <template v-else>{{ p.text }}</template>
        </template>
      </div>

      <h1 v-if="s.heading" class="tph-h" :style="hStyle">{{ s.heading }}</h1>

      <div v-if="phrases.length" class="tph-tw" :style="twStyle">
        <span class="tph-pre">{{ s.type_prefix }}</span><span class="tph-type">{{ typed }}</span><span class="tph-cursor" :style="cursorStyle" aria-hidden="true"></span>
      </div>

      <p v-if="s.subhead" class="tph-sub" :style="subStyle">{{ s.subhead }}</p>

      <form v-if="s.show_form" class="tph-form" :action="s.form_action || '#'" method="get" @submit.prevent>
        <input class="tph-input" type="email" name="email" :placeholder="s.form_placeholder" :aria-label="s.form_placeholder || 'Email'" :style="inputStyle" />
        <button class="tph-btn" type="submit" :style="btnStyle">{{ s.form_button }}</button>
      </form>
      <div v-else-if="s.cta1_text || s.cta2_text" class="tph-cta">
        <a v-if="s.cta1_text" class="tph-btn" :style="btnStyle" :href="s.cta1_url || '#'">{{ s.cta1_text }}</a>
        <a v-if="s.cta2_text" class="tph-btn tph-btn--ghost" :style="ghostStyle" :href="s.cta2_url || '#'">{{ s.cta2_text }}</a>
      </div>

      <small v-if="s.small_text || s.small_link_text" class="tph-small" :style="smallStyle">
        <span v-if="s.small_text">{{ s.small_text }}</span>
        <a v-if="s.small_link_text" :href="s.small_link_url || '#'" :style="smallLinkStyle">{{ s.small_link_text }}
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
      </small>
    </div>
  </section>
</template>

<script setup>
import { computed, ref, watch, onMounted, onBeforeUnmount } from 'vue';
import { buildBgStyle } from '@/composables/useBackgroundStyle';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  show_label: true,
  label: 'UN SOLO [ECOSISTEMA] WORDPRESS',
  heading: 'Costruisci. Traduci. Prenota.',
  type_phrases: [
    { text: 'per chi costruisce siti' },
    { text: 'per chi affitta camere' },
    { text: 'per chi parla al mondo' },
  ],
  type_prefix: '— ',
  type_speed: 55,
  type_pause: 1600,
  subhead: 'La suite modulare per WordPress: page builder, prenotazioni, traduzioni, tour virtuali e corsi. Un solo fornitore, un solo standard.',
  show_form: true,
  form_placeholder: 'La tua email',
  form_button: 'Richiedi una demo',
  form_action: '#',
  small_text: 'Prova i prodotti su siti demo reali.',
  small_link_text: 'Apri la demo',
  small_link_url: '#',
  cta1_text: '', cta1_url: '#', cta2_text: '', cta2_url: '#',
  img_left: '', img_right: '', side_width: 520, side_opacity: 100,
  show_crosshairs: true, show_topline: true,
  bg_color: 'var(--olo-color-light, #f8f9fa)',
  text_color: 'var(--olo-color-dark, #1a1a2e)',
  sub_color: '', accent: '', accent_on: '',
  h_size_min: 44, h_size_vw: 6.5, h_size_max: 92, h_line_height: 1.02,
  type_size_min: 20, type_size_vw: 2.4, type_size_max: 34,
  align: 'center', max_width: 1200, min_height: 76,
  pad_custom: false,
  content_padding: { top: 96, right: 0, bottom: 96, left: 0 },
  btn_radius: { tl: 999, tr: 999, br: 999, bl: 999 },
  bg: { type: 'none' },
  shadow: 'none',
  border: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' },
  border_hover: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: '', color: '' },
  border_hover_duration: 300,
  border_effect: 'none',
  border_effect_intensity: 'medium',
  border_effect_color2: '',
  border_effect_angle: 135,
  border_effect_speed: 4,
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const DISP = "var(--olo-font-family-heading, 'Inter',-apple-system,sans-serif)";
const SANS = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";
const MONO = 'var(--olo-font-family-mono, ui-monospace, SFMono-Regular, Menlo, monospace)';

const accent   = computed(() => s.value.accent || 'var(--olo-color-primary, #e1474f)');
const accentOn = computed(() => s.value.accent_on || 'var(--olo-color-on-primary, #ffffff)');
const txt      = computed(() => s.value.text_color || 'var(--olo-color-dark, #1a1a2e)');
const sub      = computed(() => s.value.sub_color || `color-mix(in srgb, ${txt.value} 72%, transparent)`);
const hair     = computed(() => `color-mix(in srgb, ${txt.value} 26%, transparent)`);

const align  = computed(() => (s.value.align === 'left' ? 'left' : 'center'));
const alignI = computed(() => (align.value === 'center' ? 'center' : 'flex-start'));

// Label: parti tra [parentesi] → chip accento
const labelParts = computed(() => {
  const raw = String(s.value.label || '');
  if (!raw) return [];
  return raw.split(/(\[[^\]]*\])/)
    .filter(p => p.length)
    .map(p => (p.startsWith('[') && p.endsWith(']'))
      ? { chip: true, text: p.slice(1, -1) }
      : { chip: false, text: p });
});

// ── Typewriter ──
const phrases = computed(() =>
  (Array.isArray(s.value.type_phrases) ? s.value.type_phrases : [])
    .map(p => String((p && p.text) || '').trim())
    .filter(p => p.length));
const typed = ref('');
let timer = null;
function stopType() { if (timer) { clearTimeout(timer); timer = null; } }
function startType() {
  stopType();
  const list = phrases.value;
  if (!list.length) { typed.value = ''; return; }
  const reduced = typeof window !== 'undefined' && window.matchMedia
    ? window.matchMedia('(prefers-reduced-motion: reduce)').matches : false;
  if (reduced || list.length === 1) { typed.value = list[0]; return; }
  const speed = Math.max(20, Number(s.value.type_speed) || 55);
  const pause = Math.max(300, Number(s.value.type_pause) || 1600);
  let i = 0, pos = 0, del = false;
  const tick = () => {
    const w = list[i];
    if (!del) {
      pos++;
      typed.value = w.slice(0, pos);
      if (pos === w.length) { del = true; timer = setTimeout(tick, pause); return; }
    } else {
      pos--;
      typed.value = w.slice(0, pos);
      if (pos === 0) { del = false; i = (i + 1) % list.length; }
    }
    timer = setTimeout(tick, del ? Math.max(18, speed / 2) : speed);
  };
  timer = setTimeout(tick, 350);
}
onMounted(startType);
onBeforeUnmount(stopType);
watch([phrases, () => s.value.type_speed, () => s.value.type_pause], startType);

// ── Spaziatura: override GATED del padding responsive (clamp) — parità PHP ──
const padDecl = computed(() => {
  const cp = s.value.content_padding;
  if (!s.value.pad_custom || !cp || typeof cp !== 'object') return 'clamp(80px,12vh,128px) 0';
  const pt = parseInt(cp.top ?? 0, 10) || 0;
  const pr = parseInt(cp.right ?? 0, 10) || 0;
  const pb = parseInt(cp.bottom ?? 0, 10) || 0;
  const pl = parseInt(cp.left ?? 0, 10) || 0;
  return `${pt}px ${pr}px ${pb}px ${pl}px`;
});

// ── Forma: raggio input/bottoni — parità con build_border_radius_css PHP ──
const btnRadius = computed(() => {
  const r = s.value.btn_radius;
  if (!r || typeof r !== 'object') return '999px';
  const tl = parseInt(r.tl ?? 0, 10) || 0;
  const tr = parseInt(r.tr ?? 0, 10) || 0;
  const br = parseInt(r.br ?? 0, 10) || 0;
  const bl = parseInt(r.bl ?? 0, 10) || 0;
  if (!tl && !tr && !br && !bl) return '999px';
  return `${tl}px ${tr}px ${br}px ${bl}px`;
});

// ── KIT standard: sfondo completo / ombra / bordo (no-op coi default) ──
const kitBgStyle = computed(() => {
  const bg = s.value.bg;
  if (!bg || !bg.type || bg.type === 'none') return {};
  return buildBgStyle(bg);
});
const SHADOW_MAP = {
  sm: '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
  md: '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
  lg: '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
  xl: '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
};
const kitShadow = computed(() => {
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
const kitBorder = computed(() => {
  const b = s.value.border;
  if (!b || typeof b !== 'object') return {};
  const tp = Math.max(0, parseInt(b.top ?? 0, 10) || 0);
  const rt = Math.max(0, parseInt(b.right ?? 0, 10) || 0);
  const bt = Math.max(0, parseInt(b.bottom ?? 0, 10) || 0);
  const lf = Math.max(0, parseInt(b.left ?? 0, 10) || 0);
  if (tp === 0 && rt === 0 && bt === 0 && lf === 0) return {};
  const style = b.style || 'solid';
  const color = b.color || 'currentColor';
  if (tp === rt && rt === bt && bt === lf) return { border: `${tp}px ${style} ${color}` };
  const st = {};
  if (tp) st.borderTop = `${tp}px ${style} ${color}`;
  if (rt) st.borderRight = `${rt}px ${style} ${color}`;
  if (bt) st.borderBottom = `${bt}px ${style} ${color}`;
  if (lf) st.borderLeft = `${lf}px ${style} ${color}`;
  return st;
});

const rootStyle = computed(() => ({
  position: 'relative', overflow: 'hidden', boxSizing: 'border-box',
  minHeight: Math.max(0, Math.min(100, Number(s.value.min_height) || 0)) + 'vh',
  display: 'flex', flexDirection: 'column', justifyContent: 'center',
  background: s.value.bg_color || '#f8f9fa',
  color: txt.value, fontFamily: SANS, padding: padDecl.value,
  '--tph-accent': accent.value, '--tph-hair': hair.value,
  ...(s.value.show_topline ? { borderTop: `1px solid ${hair.value}` } : {}),
  ...kitBgStyle.value,
  ...(kitShadow.value ? { boxShadow: kitShadow.value } : {}),
  ...kitBorder.value,
}));
const crossStyle = computed(() => ({ '--tph-hair': hair.value }));
const sideStyle = computed(() => ({
  width: Math.max(100, Number(s.value.side_width) || 520) + 'px',
  opacity: Math.max(0.1, Math.min(1, (Number(s.value.side_opacity) || 100) / 100)),
}));
const inStyle = computed(() => ({
  position: 'relative', zIndex: 2, width: '100%', boxSizing: 'border-box',
  maxWidth: (Number(s.value.max_width) || 1200) + 'px', margin: '0 auto', padding: '0 28px',
  display: 'flex', flexDirection: 'column', alignItems: alignI.value,
  textAlign: align.value,
}));
const labelStyle = computed(() => ({
  display: 'inline-flex', alignItems: 'center', flexWrap: 'wrap', gap: '2px',
  fontFamily: MONO, fontSize: '13px', letterSpacing: '.08em', textTransform: 'uppercase',
  color: txt.value, marginBottom: '28px',
}));
const chipStyle = computed(() => ({
  background: accent.value, color: accentOn.value, padding: '0 .3em .04em', margin: '0 .15em',
}));
const hStyle = computed(() => ({
  fontFamily: DISP, fontWeight: 700,
  fontSize: `clamp(${Math.max(20, Number(s.value.h_size_min) || 44)}px,${Number(s.value.h_size_vw) || 6.5}vw,${Number(s.value.h_size_max) || 92}px)`,
  lineHeight: Number(s.value.h_line_height) || 1.02, letterSpacing: '-.02em',
  color: txt.value, margin: 0, textWrap: 'balance',
}));
const twStyle = computed(() => ({
  display: 'flex', alignItems: 'baseline', justifyContent: alignI.value,
  fontFamily: DISP, fontWeight: 600,
  fontSize: `clamp(${Math.max(12, Number(s.value.type_size_min) || 20)}px,${Number(s.value.type_size_vw) || 2.4}vw,${Number(s.value.type_size_max) || 34}px)`,
  color: txt.value, marginTop: '16px', minHeight: '1.4em', letterSpacing: '-.01em',
}));
const cursorStyle = computed(() => ({ background: accent.value }));
const subStyle = computed(() => ({
  maxWidth: '560px', fontSize: '18px', lineHeight: 1.6, color: sub.value, margin: '24px 0 0',
  textWrap: 'pretty',
}));
const inputStyle = computed(() => ({
  fontFamily: SANS, fontSize: '15px', color: txt.value,
  background: 'color-mix(in srgb, currentColor 6%, transparent)',
  border: `1px solid ${hair.value}`, borderRadius: btnRadius.value,
  padding: '0 20px', height: '46px', minWidth: '240px', outline: 'none',
}));
const btnStyle = computed(() => ({
  display: 'inline-flex', alignItems: 'center', justifyContent: 'center', gap: '8px',
  fontFamily: SANS, fontWeight: 600, fontSize: '15px', textDecoration: 'none',
  background: accent.value, color: accentOn.value, border: `1px solid ${accent.value}`,
  borderRadius: btnRadius.value, padding: '0 24px', height: '46px', cursor: 'pointer',
}));
const ghostStyle = computed(() => ({
  ...btnStyle.value, background: 'transparent', color: txt.value, border: `1px solid ${hair.value}`,
}));
const smallStyle = computed(() => ({
  display: 'inline-flex', alignItems: 'center', gap: '10px', marginTop: '16px',
  fontFamily: SANS, fontSize: '13px', color: sub.value,
}));
const smallLinkStyle = computed(() => ({
  display: 'inline-flex', alignItems: 'center', gap: '4px', color: txt.value,
  textDecoration: 'underline', textUnderlineOffset: '3px',
}));
</script>

<style scoped>
.tph-cross { position: absolute; width: 11px; height: 11px; pointer-events: none; z-index: 3; }
.tph-cross::before { content: ""; position: absolute; left: 50%; top: 0; bottom: 0; width: 1px; margin-left: -.5px; background: var(--tph-hair, rgba(0,0,0,.25)); }
.tph-cross::after { content: ""; position: absolute; top: 50%; left: 0; right: 0; height: 1px; margin-top: -.5px; background: var(--tph-hair, rgba(0,0,0,.25)); }
.tph-cross--tl { top: -5.5px; left: 22px; }
.tph-cross--tr { top: -5.5px; right: 22px; }
.tph-cross--bl { bottom: -5.5px; left: 22px; }
.tph-cross--br { bottom: -5.5px; right: 22px; }
.tph-side { position: absolute; bottom: 0; z-index: 1; pointer-events: none; max-width: 38vw; height: auto; }
.tph-side--left { left: 0; }
.tph-side--right { right: 0; }
@media (max-width: 999px) { .tph-side { display: none; } }
.tph-form { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 32px; justify-content: center; }
.tph-cta { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 32px; }
.tph-cursor { display: inline-block; width: .55em; height: 1.05em; margin-left: 3px; transform: translateY(.12em); animation: tph-blink 1.1s steps(2, start) infinite; }
@keyframes tph-blink { to { visibility: hidden; } }
@media (prefers-reduced-motion: reduce) { .tph-cursor { animation: none; } }
.tph-btn { transition: transform .15s, filter .2s, background .2s; }
.tph-btn:hover { transform: translateY(-1px); filter: brightness(1.05); }
.tph-btn:focus-visible, .tph-input:focus-visible, .tph-small a:focus-visible {
  outline: 2px solid var(--tph-accent, #e1474f); outline-offset: 3px;
}
.tph-input:focus { border-color: var(--tph-accent, #e1474f); }
</style>
