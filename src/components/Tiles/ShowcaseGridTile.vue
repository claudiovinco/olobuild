<template>
  <div class="olo-showcasegrid ocg" :style="rootStyle">
    <a v-for="(it, i) in items" :key="'c'+i" class="ocg-card" :style="cardStyle" :href="it.link || '#'">
      <span class="ocg-media" :style="mediaStyle(it.image)"></span>
      <span v-if="!it.image && it.media_label" class="ocg-medialabel" :style="mediaLabelStyle">{{ it.media_label }}</span>
      <span class="ocg-veil" :style="veilStyle"></span>
      <span class="ocg-arr" :style="arrStyle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="width:19px;height:19px"><path d="M7 17 17 7M9 7h8v8"/></svg></span>
      <span v-if="it.kicker" class="ocg-k" :style="kStyle">{{ it.kicker }}</span>
      <span v-if="it.title" class="ocg-t" :style="tStyle">{{ it.title }}</span>
    </a>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { buildBgStyle } from '@/composables/useBackgroundStyle';
import { borderDefault, borderHoverDefault, borderEffectDefaults } from '@/config/elements/_shared.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  items: [
    { image: '', media_label: "Men's squad", kicker: '3 squads', title: 'Men', link: '#' },
    { image: '', media_label: "Women's squad", kicker: '1 squad', title: 'Women', link: '#' },
    { image: '', media_label: 'Youth squad', kicker: '4 squads · U14–U21', title: 'Youth', link: '#' },
  ],
  columns: 3, gap: 18, aspect: '3/3.5', radius: 20, media_bg: '#0f3a2a', veil_color: '#0a2a1e',
  kicker_color: '', title_color: '#ffffff', arrow_bg: 'rgba(255,255,255,0.14)', arrow_color: '#ffffff',
  arrow_hover_bg: '', arrow_hover_color: '#0a2a1e',

  // Spaziatura interna card — default = padding attuale (26px) → no-op.
  card_padding: { top: 26, right: 26, bottom: 26, left: 26 },
  // Raggio card a 4 angoli (override gated) — default {0,0,0,0} → usa legacy `radius` → no-op.
  card_radius: { tl: 0, tr: 0, br: 0, bl: 0 },

  // KIT standard OLObuild (contenitore) — default no-op (render invariato).
  bg: { type: 'none' },
  shadow: 'none',
  border: { ...borderDefault },
  border_hover: { ...borderHoverDefault },
  border_hover_duration: 300,
  ...borderEffectDefaults,
};

const s = computed(() => ({ ...defaults, ...props.settings }));
const items = computed(() => Array.isArray(s.value.items) ? s.value.items : []);

const DISP = "var(--olo-font-family-heading, 'Archivo',-apple-system,sans-serif)";
const SANS = "var(--olo-font-family, 'Work Sans',-apple-system,sans-serif)";
const cols = computed(() => Math.max(1, Math.min(4, parseInt(s.value.columns, 10) || 3)));
const mbg = computed(() => s.value.media_bg || '#0f3a2a');
const accent = computed(() => s.value.kicker_color || 'var(--olo-color-primary, #c8ff3c)');
const arrhbg = computed(() => s.value.arrow_hover_bg || 'var(--olo-color-primary, #c8ff3c)');

function hexRgb(hex, fb = '10,42,30') {
  let h = String(hex || '').replace('#', '');
  if (h.length === 3) h = h.split('').map(c => c + c).join('');
  if (!/^[0-9a-fA-F]{6}$/.test(h)) return fb;
  return parseInt(h.slice(0, 2), 16) + ',' + parseInt(h.slice(2, 4), 16) + ',' + parseInt(h.slice(4, 6), 16);
}

// ── KIT standard OLObuild (contenitore) — parità con PHP ──
// Sfondo completo opzionale: applicato SOLO se valorizzato (default none → invariato).
const kitBgStyle = computed(() => buildBgStyle(s.value.bg));

// Ombra (mirror di build_shadow_decl PHP). '' se none.
function shadowDecl(st) {
  const preset = st.shadow || 'none';
  if (preset === 'none' || preset === '') return '';
  if (preset === 'custom') {
    const h = parseInt(st.shadow_h, 10) || 0;
    const v = Number.isNaN(parseInt(st.shadow_v, 10)) ? 4 : parseInt(st.shadow_v, 10);
    const blur = Math.max(0, Number.isNaN(parseInt(st.shadow_blur, 10)) ? 10 : parseInt(st.shadow_blur, 10));
    const spread = parseInt(st.shadow_spread, 10) || 0;
    const color = st.shadow_color || 'rgba(0,0,0,0.15)';
    const inset = st.shadow_inset ? 'inset ' : '';
    return `${inset}${h}px ${v}px ${blur}px ${spread}px ${color}`;
  }
  const map = {
    sm: '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
    md: '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
    lg: '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
    xl: '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
  };
  return map[preset] || '';
}

// Bordo statico (4 lati, stile, colore). Default 0 → niente bordo.
const kitBorderStyle = computed(() => {
  const b = s.value.border || {};
  const style = b.style || 'solid';
  const color = b.color || 'currentColor';
  const out = {};
  const t = parseInt(b.top, 10) || 0;
  const r = parseInt(b.right, 10) || 0;
  const bo = parseInt(b.bottom, 10) || 0;
  const l = parseInt(b.left, 10) || 0;
  if (t) out.borderTop = `${t}px ${style} ${color}`;
  if (r) out.borderRight = `${r}px ${style} ${color}`;
  if (bo) out.borderBottom = `${bo}px ${style} ${color}`;
  if (l) out.borderLeft = `${l}px ${style} ${color}`;
  return out;
});

const rootStyle = computed(() => {
  const kit = { ...kitBgStyle.value, ...kitBorderStyle.value };
  const sh = shadowDecl(s.value);
  if (sh) kit.boxShadow = sh;
  // position:relative se il KIT è valorizzato (parità con PHP, per gli effetti bordo).
  if (Object.keys(kit).length) kit.position = 'relative';
  return {
    display: 'grid', gridTemplateColumns: 'repeat(' + cols.value + ', 1fr)', gap: (parseInt(s.value.gap, 10) || 18) + 'px', fontFamily: SANS,
    '--ocg-arrhbg': arrhbg.value, '--ocg-arrhc': s.value.arrow_hover_color || '#0a2a1e',
    ...kit,
  };
});
// Raggio card a 4 angoli: OVERRIDE solo se valorizzato (default {0,0,0,0} → usa legacy radius → no-op).
const cardRad = computed(() => {
  const r = s.value.card_radius || {};
  const tl = parseInt(r.tl, 10) || 0;
  const tr = parseInt(r.tr, 10) || 0;
  const br = parseInt(r.br, 10) || 0;
  const bl = parseInt(r.bl, 10) || 0;
  if (tl || tr || br || bl) return `${tl}px ${tr}px ${br}px ${bl}px`;
  // Fallback al raggio legacy. Mirror di PHP intval($s['radius']): 0 esplicito → 0px (no ||20).
  const legacy = parseInt(s.value.radius, 10);
  return (Number.isNaN(legacy) ? 20 : legacy) + 'px';
});
// Padding interno card: default 26px su tutti i lati (= valore attuale → no-op).
const cardPad = computed(() => {
  const p = s.value.card_padding || {};
  const t = Number.isNaN(parseInt(p.top, 10)) ? 26 : parseInt(p.top, 10);
  const r = Number.isNaN(parseInt(p.right, 10)) ? 26 : parseInt(p.right, 10);
  const b = Number.isNaN(parseInt(p.bottom, 10)) ? 26 : parseInt(p.bottom, 10);
  const l = Number.isNaN(parseInt(p.left, 10)) ? 26 : parseInt(p.left, 10);
  // Shorthand a valore unico se i 4 lati sono uguali (byte-identico al default '26px').
  return (t === r && r === b && b === l) ? `${t}px` : `${t}px ${r}px ${b}px ${l}px`;
});
const cardStyle = computed(() => ({ position: 'relative', borderRadius: cardRad.value, overflow: 'hidden', aspectRatio: String(s.value.aspect || '3/3.5').replace(/[^0-9.\/]/g, ''), display: 'flex', flexDirection: 'column', justifyContent: 'flex-end', padding: cardPad.value, color: '#fff', textDecoration: 'none', background: mbg.value }));
function mediaStyle(img) {
  const st = { position: 'absolute', inset: 0, zIndex: 0, background: mbg.value, backgroundSize: 'cover', backgroundPosition: 'center', transition: 'transform .5s ease' };
  st.backgroundImage = img ? 'url(' + img + ')' : 'repeating-linear-gradient(135deg, rgba(255,255,255,.05) 0 18px, rgba(255,255,255,0) 18px 36px)';
  return st;
}
const mediaLabelStyle = { position: 'absolute', left: '14px', bottom: '12px', fontSize: '11px', letterSpacing: '.04em', textTransform: 'uppercase', fontWeight: 600, color: 'rgba(255,255,255,.4)', zIndex: 1 };
const veilStyle = computed(() => {
  const rgb = hexRgb(s.value.veil_color || '#0a2a1e');
  return { position: 'absolute', inset: 0, zIndex: 1, background: `linear-gradient(180deg, rgba(${rgb},.05) 30%, rgba(${rgb},.9) 100%)` };
});
const arrStyle = computed(() => ({ position: 'absolute', zIndex: 2, top: '24px', right: '24px', width: '44px', height: '44px', borderRadius: '50%', background: s.value.arrow_bg || 'rgba(255,255,255,0.14)', display: 'grid', placeItems: 'center', color: s.value.arrow_color || '#ffffff', transition: 'background .25s, transform .25s' }));
const kStyle = computed(() => ({ position: 'relative', zIndex: 2, fontWeight: 700, fontSize: '12px', letterSpacing: '.12em', textTransform: 'uppercase', color: accent.value }));
const tStyle = computed(() => ({ position: 'relative', zIndex: 2, fontFamily: DISP, fontWeight: 900, fontSize: '34px', textTransform: 'uppercase', marginTop: '6px', color: s.value.title_color || '#fff', lineHeight: 1 }));
</script>

<style scoped>
.ocg-card:hover .ocg-media { transform: scale(1.04); }
.ocg-card:hover .ocg-arr { background: var(--ocg-arrhbg, #c8ff3c) !important; transform: rotate(-45deg); }
.ocg-card:hover .ocg-arr svg { color: var(--ocg-arrhc, #0a2a1e) !important; }
.ocg-card:focus-visible { outline: 2px solid var(--ocg-arrhbg, #c8ff3c); outline-offset: 3px; }
</style>
