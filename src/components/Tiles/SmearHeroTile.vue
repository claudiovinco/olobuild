<template>
  <section ref="rootEl" class="olo-smearhero sh" :style="rootStyle">
    <div v-if="s.smear_enabled" ref="zoneEl" class="sh-smear" :style="smearStyle" aria-hidden="true"></div>
    <div class="sh-in" :style="inStyle">
      <span v-if="s.eyebrow_text" class="sh-eyebrow" :style="eyebrowStyle">{{ s.eyebrow_text }}</span>
      <h1 class="sh-h" :style="hStyle">{{ s.headline_text }}<em v-if="s.accent_text" class="sh-acc" :style="accStyle"> {{ s.accent_text }}</em></h1>
      <p v-if="s.subhead" class="sh-sub" :style="subStyle">{{ s.subhead }}</p>
    </div>
    <span v-if="s.hint_text" class="sh-hint" :style="hintStyle">{{ s.hint_text }}</span>
  </section>
</template>

<script setup>
import { computed, ref, onMounted, onBeforeUnmount } from 'vue';
import { buildBgStyle } from '@/composables/useBackgroundStyle';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const borderDefault = { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' };
const borderHoverDefault = { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: '', color: '' };

const defaults = {
  eyebrow_text: 'Painter · oil & pigment',
  headline_text: 'Jonah',
  accent_text: 'Veld',
  subhead: 'Large-scale abstracts about colour, weather and memory. Move your cursor — leave a mark.',
  hint_text: '↑ drag the colour around',
  bg_color: '#1c1a17',
  glow_color: 'rgba(224,177,58,0.08)',
  eyebrow_color: '#e0b13a',
  text_color: '#f0ebe1',
  accent_color: '',
  sub_color: '#f0ebe1',
  hint_color: '#857c6d',
  smear_palette: '#e0b13a,#cc5b3f,#5b86b8,#f0ebe1',
  smear_enabled: true,
  min_height: 72,

  // Spaziatura (gated): padding di base responsivo clamp(48px,9vh,90px) 30px.
  // Override attivo SOLO se pad_custom=true → no-op coi default.
  pad_custom: false,
  content_padding: { top: 90, right: 30, bottom: 90, left: 30 },

  // Forma: raggio del contenitore hero (full-bleed) — default 0 = no-op.
  container_radius: { tl: 0, tr: 0, br: 0, bl: 0 },

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

const DISP = "var(--olo-font-family-heading, 'Gilda Display',Georgia,serif)";
const SANS = "var(--olo-font-family, 'Mulish',-apple-system,sans-serif)";

const bg = computed(() => s.value.bg_color || '#1c1a17');
const glow = computed(() => s.value.glow_color || 'rgba(224,177,58,0.08)');
const eyebrow = computed(() => s.value.eyebrow_color || 'var(--olo-color-primary, #e0b13a)');
const txt = computed(() => s.value.text_color || '#f0ebe1');
const accent = computed(() => s.value.accent_color || 'var(--olo-color-primary, #e0b13a)');
const sub = computed(() => s.value.sub_color || '#f0ebe1');
const hint = computed(() => s.value.hint_color || '#857c6d');
const mh = computed(() => Math.max(40, Math.min(100, Number(s.value.min_height) || 72)));

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

// ── KIT standard: bordo — parità con parse_border/build_border_css PHP (no-op se color vuoto/lati 0) ──
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

// ── Forma: raggio contenitore — parità con build_border_radius_css PHP (no-op se tutti 0) ──
const radiusDecl = computed(() => {
  const r = s.value.container_radius;
  if (!r || typeof r !== 'object') return '';
  const tl = parseInt(r.tl ?? 0, 10) || 0;
  const tr = parseInt(r.tr ?? 0, 10) || 0;
  const br = parseInt(r.br ?? 0, 10) || 0;
  const bl = parseInt(r.bl ?? 0, 10) || 0;
  if (!tl && !tr && !br && !bl) return '';
  return `${tl}px ${tr}px ${br}px ${bl}px`;
});

// ── Spaziatura (gated): default = padding responsivo invariato; override solo se pad_custom ──
const inPad = computed(() => {
  if (s.value.pad_custom && s.value.content_padding && typeof s.value.content_padding === 'object') {
    const cp = s.value.content_padding;
    const pt = parseInt(cp.top ?? 0, 10) || 0;
    const pr = parseInt(cp.right ?? 0, 10) || 0;
    const pb = parseInt(cp.bottom ?? 0, 10) || 0;
    const pl = parseInt(cp.left ?? 0, 10) || 0;
    return `${pt}px ${pr}px ${pb}px ${pl}px`;
  }
  return 'clamp(48px,9vh,90px) 30px';
});

const rootStyle = computed(() => {
  const base = {
    position: 'relative', overflow: 'hidden', minHeight: mh.value + 'vh', display: 'flex',
    alignItems: 'center', color: txt.value, fontFamily: SANS,
    '--sh-glow': glow.value, '--sh-accent': accent.value,
  };
  // Sfondo completo del KIT: sostituisce l'intero blocco background di base solo se valorizzato.
  const kitBg = bgKitStyle.value;
  if (kitBg && Object.keys(kitBg).length) {
    Object.assign(base, kitBg);
  } else {
    base.background = bg.value;
  }
  if (radiusDecl.value) base.borderRadius = radiusDecl.value;
  if (shadowDecl.value) base.boxShadow = shadowDecl.value;
  Object.assign(base, borderStyle.value);
  return base;
});
const smearStyle = { position: 'absolute', inset: 0, zIndex: 2, overflow: 'hidden', pointerEvents: 'auto' };
const inStyle = computed(() => ({ position: 'relative', zIndex: 3, maxWidth: '760px', margin: '0 auto', textAlign: 'center', padding: inPad.value, pointerEvents: 'none' }));
const eyebrowStyle = computed(() => ({ display: 'block', marginBottom: '22px', fontFamily: SANS, fontWeight: 700, fontSize: '12px', letterSpacing: '.24em', textTransform: 'uppercase', color: eyebrow.value }));
const hStyle = computed(() => ({ fontFamily: DISP, fontWeight: 400, fontSize: 'clamp(52px,10vw,140px)', lineHeight: .96, letterSpacing: '.01em', color: txt.value, margin: 0 }));
const accStyle = computed(() => ({ fontStyle: 'italic', color: accent.value }));
const subStyle = computed(() => ({ fontSize: '18px', lineHeight: 1.7, color: sub.value, maxWidth: '460px', margin: '24px auto 0' }));
const hintStyle = computed(() => ({ position: 'absolute', bottom: '22px', left: '50%', transform: 'translateX(-50%)', zIndex: 3, fontFamily: SANS, fontSize: '12px', letterSpacing: '.1em', textTransform: 'uppercase', color: hint.value, pointerEvents: 'none' }));

// ── Signature: paint-smear runtime (scoped to this instance's zone) ──
const rootEl = ref(null);
const zoneEl = ref(null);
let handler = null;
let zoneRef = null;

onMounted(() => {
  const zone = zoneEl.value;
  if (!zone) return;
  const fine = window.matchMedia('(pointer:fine)').matches;
  const motion = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (!fine) return;
  if (!motion) return;
  let cols = String(s.value.smear_palette || '').split(',').map(c => c.trim()).filter(Boolean);
  if (!cols.length) cols = ['#e0b13a', '#cc5b3f', '#5b86b8', '#f0ebe1'];
  let last = 0;
  handler = (e) => {
    const now = Date.now();
    if (now - last < 36) return;
    last = now;
    const r = zone.getBoundingClientRect();
    const b = document.createElement('span');
    b.className = 'sh-blob';
    b.style.left = (e.clientX - r.left) + 'px';
    b.style.top = (e.clientY - r.top) + 'px';
    b.style.background = cols[Math.floor(Math.random() * cols.length)];
    const sz = 24 + Math.random() * 40;
    b.style.width = sz + 'px';
    b.style.height = sz + 'px';
    zone.appendChild(b);
    setTimeout(() => { b.style.opacity = 0; b.style.transform = 'translate(-50%,-50%) scale(2.2)'; }, 20);
    setTimeout(() => { b.remove(); }, 900);
  };
  zone.addEventListener('pointermove', handler);
  zoneRef = zone;
});

onBeforeUnmount(() => {
  if (zoneRef && handler) zoneRef.removeEventListener('pointermove', handler);
});
</script>

<style scoped>
.sh::before {
  content: "";
  position: absolute;
  inset: 0;
  z-index: 1;
  background: radial-gradient(80% 90% at 70% 20%, var(--sh-glow, rgba(224,177,58,.08)), transparent 60%);
  pointer-events: none;
}
.sh-blob {
  position: absolute;
  border-radius: 50%;
  transform: translate(-50%, -50%) scale(1);
  pointer-events: none;
  mix-blend-mode: screen;
  filter: blur(2px);
  transition: opacity .9s ease, transform .9s ease;
}
.sh a:focus-visible,
.sh button:focus-visible {
  outline: 2px solid var(--sh-accent, #e0b13a);
  outline-offset: 3px;
}
</style>
