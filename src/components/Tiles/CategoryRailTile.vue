<template>
  <div class="olo-categoryrail" :style="rootStyle">
    <div v-if="s.show_hint && s.hint_text" class="ocr-head" :style="{ display: 'flex', justifyContent: 'flex-end', marginBottom: '12px' }">
      <span :style="hintStyle">{{ s.hint_text }}</span>
    </div>
    <div class="ocr-track" :style="trackStyle">
      <a v-for="(it, i) in items" :key="'c'+i" class="ocr-card" :style="cardStyle" :href="it.link || '#'">
        <span class="ocr-media" :style="mediaStyle(it.image)"></span>
        <span class="ocr-ov" :style="ovStyle"></span>
        <span class="ocr-cap" :style="{ position: 'absolute', left: 0, right: 0, bottom: 0, padding: capPadCss || '16px 18px' }">
          <h3 v-if="it.title" :style="titleStyle">{{ it.title }}</h3>
          <p v-if="it.subtitle" :style="subStyle">{{ it.subtitle }}</p>
        </span>
      </a>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { buildBgStyle } from '@/composables/useBackgroundStyle';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  items: [
    { image: '', title: 'Ceramics', subtitle: '', link: '#' },
    { image: '', title: 'Art & prints', subtitle: '', link: '#' },
    { image: '', title: 'Jewellery', subtitle: '', link: '#' },
    { image: '', title: 'Homeware', subtitle: '', link: '#' },
    { image: '', title: 'Vintage', subtitle: '', link: '#' },
    { image: '', title: 'Stationery', subtitle: '', link: '#' },
  ],
  card_width: 260, card_aspect: '4/5', gap: 16, media_bg: '',
  overlay_color: 'rgba(16,16,21,0.5)', title_color: '#ffffff', subtitle_color: 'rgba(255,255,255,0.8)',
  radius: 14, show_hint: true, hint_text: '← drag →', hint_color: '',

  // Controlli additivi (no-op): padding caption + raggio tessera.
  // Default = resa attuale; usati come override solo se modificati (gating).
  cap_padding: { top: 16, right: 18, bottom: 16, left: 18 },
  card_radius: { tl: 14, tr: 14, br: 14, bl: 14 },

  // KIT standard OLObuild — sfondo completo + ombra + bordo sul contenitore.
  // Default no-op: bg none / shadow none / border 0 → render invariato.
  bg: { type: 'none' },
  shadow: 'none',
  border: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' },
  border_hover: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: '', color: '' },
  border_hover_duration: 300,
  border_effect: 'none', border_effect_intensity: 'medium',
  border_effect_color2: '', border_effect_angle: 135, border_effect_speed: 4,
};

// Mappa ombre IDENTICA al PHP build_shadow_decl (parità render).
const SHADOW_MAP = {
  sm: '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
  md: '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
  lg: '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
  xl: '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
};

const s = computed(() => ({ ...defaults, ...props.settings }));
const items = computed(() => Array.isArray(s.value.items) ? s.value.items : []);

const SERIF = "var(--olo-font-family-heading, 'Playfair Display',Georgia,serif)";
const SANS = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";
const w = computed(() => Math.max(140, Math.min(480, parseInt(s.value.card_width, 10) || 260)) + 'px');
const asp = computed(() => String(s.value.card_aspect || '4/5').replace(/[^0-9/]/g, '') || '4/5');
const rad = computed(() => (parseInt(s.value.radius, 10) || 0) + 'px');
const mbg = computed(() => s.value.media_bg || 'var(--olo-color-surface-alt, #eceff3)');

// ── Override additivi gated (parità PHP, no-op coi default) ──
// Raggio tessera: '' (usa rad) se card_radius == default {14,14,14,14}.
const cardRadiusCss = computed(() => {
  const cr = s.value.card_radius;
  if (!cr || typeof cr !== 'object') return '';
  const tl = parseInt(cr.tl, 10) || 0;
  const tr = parseInt(cr.tr, 10) || 0;
  const br = parseInt(cr.br, 10) || 0;
  const bl = parseInt(cr.bl, 10) || 0;
  if (tl === 14 && tr === 14 && br === 14 && bl === 14) return '';
  if (!tl && !tr && !br && !bl) return '';
  return `${tl}px ${tr}px ${br}px ${bl}px`;
});
// Padding didascalia: '' (usa '16px 18px') se cap_padding == default.
const capPadCss = computed(() => {
  const cp = s.value.cap_padding;
  if (!cp || typeof cp !== 'object') return '';
  const tp = Math.max(0, parseInt(cp.top, 10) || 0);
  const rp = Math.max(0, parseInt(cp.right, 10) || 0);
  const bp = Math.max(0, parseInt(cp.bottom, 10) || 0);
  const lp = Math.max(0, parseInt(cp.left, 10) || 0);
  if (tp === 16 && rp === 18 && bp === 16 && lp === 18) return '';
  return `${tp}px ${rp}px ${bp}px ${lp}px`;
});

const hintStyle = computed(() => ({ fontSize: '11px', fontWeight: 600, letterSpacing: '.16em', textTransform: 'uppercase', color: s.value.hint_color || 'var(--olo-color-text-muted, #6b7280)' }));
const trackStyle = computed(() => ({ display: 'flex', gap: (parseInt(s.value.gap, 10) || 16) + 'px', overflowX: 'auto', paddingBottom: '6px', scrollSnapType: 'x proximity', scrollbarWidth: 'none', msOverflowStyle: 'none' }));
const cardStyle = computed(() => ({ flex: '0 0 ' + w.value, width: w.value, aspectRatio: asp.value, position: 'relative', borderRadius: cardRadiusCss.value || rad.value, overflow: 'hidden', textDecoration: 'none', display: 'block', background: mbg.value, scrollSnapAlign: 'start' }));
function mediaStyle(img) {
  const st = { position: 'absolute', inset: 0, backgroundSize: 'cover', backgroundPosition: 'center', transition: 'transform .5s ease' };
  if (img) st.backgroundImage = 'url(' + img + ')';
  return st;
}
const ovStyle = computed(() => ({ position: 'absolute', inset: 0, background: 'linear-gradient(to top, ' + (s.value.overlay_color || 'rgba(16,16,21,0.5)') + ', transparent 62%)' }));

// KIT standard: ombra (preset/custom) — IDENTICO al PHP build_shadow_decl.
const shadowDecl = computed(() => {
  const p = s.value.shadow || 'none';
  if (p === 'none' || p === '') return '';
  if (p === 'custom') {
    const h = parseInt(s.value.shadow_h, 10) || 0;
    const v = parseInt(s.value.shadow_v, 10) || 0;
    const blur = Math.max(0, parseInt(s.value.shadow_blur, 10) || 0);
    const spread = parseInt(s.value.shadow_spread, 10) || 0;
    const color = s.value.shadow_color || 'rgba(0,0,0,0.15)';
    const inset = s.value.shadow_inset ? 'inset ' : '';
    return `${inset}${h}px ${v}px ${blur}px ${spread}px ${color}`;
  }
  return SHADOW_MAP[p] || '';
});

// KIT standard: bordo base (parità con build_border_css PHP, no-op coi default).
const borderDecl = computed(() => {
  const b = s.value.border;
  if (!b || typeof b !== 'object') return null;
  const color = (b.color || '').trim();
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

// KIT standard: sfondo completo opzionale (override SOLO se valorizzato).
const bgDecl = computed(() => {
  const bg = s.value.bg;
  if (!bg || !bg.type || bg.type === 'none') return {};
  return buildBgStyle(bg);
});

// Stile del contenitore principale: font + KIT (bg/ombra/bordo). No-op coi default.
const rootStyle = computed(() => {
  const st = { fontFamily: SANS, ...bgDecl.value };
  const bd = borderDecl.value;
  if (bd) Object.assign(st, bd);
  if (shadowDecl.value) st.boxShadow = shadowDecl.value;
  if (Object.keys(bgDecl.value).length || bd || shadowDecl.value) st.position = 'relative';
  return st;
});
const titleStyle = computed(() => ({ fontFamily: SERIF, fontSize: '19px', lineHeight: 1.2, margin: 0, color: s.value.title_color || '#ffffff' }));
const subStyle = computed(() => ({ fontSize: '12.5px', margin: '4px 0 0', color: s.value.subtitle_color || 'rgba(255,255,255,0.8)' }));
</script>

<style scoped>
.ocr-track::-webkit-scrollbar { display: none; }
.ocr-card:hover .ocr-media { transform: scale(1.05); }
.ocr-card:focus-visible { outline: 2px solid var(--olo-color-primary, #e1474f); outline-offset: 3px; }
</style>
