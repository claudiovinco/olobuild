<template>
  <div class="olo-themedemos otd" :style="rootStyle">
    <div class="otd-row" :style="rowStyle">
      <component
        :is="cardTag(it)"
        v-for="(it, i) in items"
        :key="i"
        class="otd-card"
        :style="cardStyle(it)"
        :href="it.link ? it.link : undefined"
        :data-olo-cta="it.link ? '' : undefined"
      >
        <span :class="['otd-pv', { light: !!it.light }]" :style="pvStyle" data-olo-tilt-child>
          <span class="otd-logo" :style="logoStyle" aria-hidden="true"></span>
          <span v-if="it.name" class="otd-h" :style="hStyle">{{ it.name }}</span>
          <span class="otd-btn" :style="btnStyle" aria-hidden="true"></span>
          <span v-if="it.zone_label" class="otd-z" :style="zStyle">{{ it.zone_label }}</span>
        </span>
        <span class="otd-ft" :style="ftStyle">
          <b v-if="it.name" class="otd-name" :style="nameStyle">{{ it.name }}</b>
          <span v-if="it.category" class="otd-cat" :style="catStyle">{{ it.category }}</span>
        </span>
      </component>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { buildBgStyle } from '@/composables/useBackgroundStyle';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const borderDefault = { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' };
const borderHoverDefault = { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: '', color: '' };

const defaults = {
  items: [
    { name: 'Forge', category: 'Software & Tech', zone_label: 'Contrast', bg: '#121212', ink: '#f4f4f4', accent: '#ff6a2b', font_label: 'Big Shoulders Display', light: false, link: '' },
    { name: 'Prisma', category: 'Creative', zone_label: 'Palette', bg: '#160a24', ink: '#f1e9f7', accent: '#c14bff', font_label: 'Big Shoulders Display', light: false, link: '' },
    { name: 'Saffron', category: 'Food & Drink', zone_label: 'Floor plan', bg: '#f6efe2', ink: '#241a16', accent: '#c75d3a', font_label: 'Big Shoulders Display', light: true, link: '' },
    { name: 'Soundwave', category: 'Artist', zone_label: 'Sequencer', bg: '#0c0c10', ink: '#ffffff', accent: '#27e0a3', font_label: 'Big Shoulders Display', light: false, link: '' },
  ],
  accent: '',
  card_bg: '',
  card_border_color: '',
  card_border_hover_color: '',
  preview_height: 168,
  gap: 16,

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
const items = computed(() => (Array.isArray(s.value.items) ? s.value.items : []));

const DISP = "var(--olo-font-family-heading, 'Big Shoulders Display', sans-serif)";
const SANS = "var(--olo-font-family, 'Hanken Grotesk', sans-serif)";
const MONO = "var(--olo-font-family-mono, 'Space Mono', ui-monospace, monospace)";

const accent = computed(() => s.value.accent || 'var(--olo-color-primary, #C6F24E)');
const cardBg = computed(() => s.value.card_bg || 'var(--olo-color-muted, #101218)');
const cardBorder = computed(() => s.value.card_border_color || 'var(--olo-color-border, rgba(236,234,227,.10))');
const cardBorderHover = computed(() => s.value.card_border_hover_color || 'color-mix(in srgb, var(--olo-color-text, #ECEAE3) 20%, transparent)');
const pvHeight = computed(() => { const n = parseInt(s.value.preview_height, 10); return n > 0 ? Math.max(100, Math.min(320, n)) : 168; });
const gap = computed(() => { const n = parseInt(s.value.gap, 10); return n > 0 ? n : 16; });

function cardTag(it) {
  return it && it.link ? 'a' : 'div';
}

/** Whitelist del nome font item (contenuto: rappresenta il TEMA, non i ruoli del sito). */
function itemFont(it) {
  const f = String(it && it.font_label ? it.font_label : '').replace(/[^a-zA-Z0-9 \-]/g, '').trim();
  return `'${f !== '' ? f : 'Big Shoulders Display'}',sans-serif`;
}

// ── KIT standard: sfondo completo (override SOLO se valorizzato) ──
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

// ── KIT standard: bordo — parità con parse_border/build_border_css PHP (no-op se vuoto) ──
const kitBorderStyle = computed(() => {
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
    fontFamily: SANS,
    '--otd-bhov': cardBorderHover.value,
    '--otd-acc': accent.value,
  };
  const kitBg = bgKitStyle.value;
  if (kitBg && Object.keys(kitBg).length) Object.assign(base, kitBg);
  if (shadowDecl.value) base.boxShadow = shadowDecl.value;
  Object.assign(base, kitBorderStyle.value);
  return base;
});

const rowStyle = computed(() => ({
  display: 'flex',
  gap: gap.value + 'px',
  overflowX: 'auto',
  scrollSnapType: 'x mandatory',
  paddingBottom: '14px',
  WebkitOverflowScrolling: 'touch',
  scrollbarWidth: 'thin',
}));

function cardStyle(it) {
  return {
    flex: '0 0 clamp(250px,28vw,320px)',
    scrollSnapAlign: 'start',
    border: `1px solid ${cardBorder.value}`,
    borderRadius: '12px',
    overflow: 'hidden',
    background: cardBg.value,
    transition: 'transform .18s,border-color .18s',
    display: 'block',
    textDecoration: 'none',
    color: 'inherit',
    '--c-bg': it.bg || '#121212',
    '--c-ink': it.ink || '#f4f4f4',
    '--c-acc': it.accent || accent.value,
    '--c-font': itemFont(it),
  };
}

const pvStyle = computed(() => ({
  position: 'relative',
  height: pvHeight.value + 'px',
  background: 'var(--c-bg)',
  padding: '15px 16px 0',
  display: 'flex',
  flexDirection: 'column',
  overflow: 'hidden',
}));

const logoStyle = { width: '13px', height: '13px', borderRadius: '4px', background: 'var(--c-acc)', flex: 'none' };

const hStyle = {
  fontFamily: 'var(--c-font)',
  fontWeight: 800,
  textTransform: 'uppercase',
  fontSize: '29px',
  lineHeight: .95,
  color: 'var(--c-ink)',
  marginTop: 'auto',
  letterSpacing: '-.01em',
  whiteSpace: 'nowrap',
};

const btnStyle = { height: '14px', width: '52px', borderRadius: '7px', background: 'var(--c-acc)', margin: '11px 0 13px', flex: 'none' };

const zStyle = computed(() => ({
  position: 'absolute',
  right: '13px',
  bottom: '12px',
  fontFamily: MONO,
  fontSize: '9.5px',
  fontWeight: 700,
  letterSpacing: '.05em',
  textTransform: 'uppercase',
  color: '#fff',
  background: 'rgba(8,9,12,.5)',
  WebkitBackdropFilter: 'blur(5px)',
  backdropFilter: 'blur(5px)',
  border: '1px solid rgba(255,255,255,.18)',
  borderRadius: '999px',
  padding: '4px 9px',
}));

const ftStyle = {
  display: 'flex',
  alignItems: 'baseline',
  justifyContent: 'space-between',
  gap: '10px',
  padding: '13px 16px',
};

const nameStyle = {
  fontFamily: DISP,
  fontWeight: 700,
  fontSize: '19px',
  textTransform: 'uppercase',
  color: 'var(--olo-color-text, #ECEAE3)',
};

const catStyle = {
  fontSize: '11.5px',
  color: 'var(--olo-color-text-soft, #a0a298)',
};
</script>

<style scoped>
.otd-row::-webkit-scrollbar { height: 6px; }
.otd-row::-webkit-scrollbar-thumb {
  background: var(--otd-bhov, color-mix(in srgb, var(--olo-color-text, #ECEAE3) 20%, transparent));
  border-radius: 3px;
}
.otd-card:hover {
  transform: translateY(-4px);
  border-color: var(--otd-bhov, color-mix(in srgb, var(--olo-color-text, #ECEAE3) 20%, transparent)) !important;
}
.otd-card:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--otd-acc, var(--olo-color-primary, #C6F24E)) 30%, transparent);
}
.otd-pv.light .otd-z {
  background: rgba(255,255,255,.6) !important;
  border-color: rgba(0,0,0,.12) !important;
  color: #1a1a1a !important;
}
</style>
