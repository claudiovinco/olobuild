<template>
  <div class="olo-hoverlist" :style="{ borderTop: '1px solid ' + line, position: 'relative' }">
    <component
      :is="it.link_url ? 'a' : 'div'"
      v-for="(it, idx) in items"
      :key="idx"
      :href="it.link_url || undefined"
      class="olo-hoverlist__row"
      :class="{ 'olo-hoverlist__row--num': isNumber }"
      :style="rowStyleFor(it)"
      @mouseenter="onRowEnter(it, idx)"
      @mousemove="onRowMove"
      @mouseleave="onRowLeave"
    >
      <template v-if="isNumber">
        <span class="olo-hoverlist__num" :style="numStyle">{{ itemNumber(it, idx) }}</span>
        <span class="olo-hoverlist__nm" :style="nameStyle">{{ it.name }}</span>
        <span v-if="it.desc" class="olo-hoverlist__desc" :style="descStyle">{{ it.desc }}</span>
        <span v-else-if="it.sub" class="olo-hoverlist__sub olo-hoverlist__desc" :style="subStyleNum">{{ it.sub }}</span>
      </template>
      <template v-else>
        <span class="olo-hoverlist__sw" :style="swStyle(it.color)"></span>
        <span class="olo-hoverlist__nm" :style="nameStyle">{{ it.name }}</span>
        <span v-if="it.sub" class="olo-hoverlist__sub" :style="subStyle">{{ it.sub }}</span>
      </template>
    </component>

    <!-- Peek "monitor regia": viewfinder + ● STILL + barra label, segue il cursore -->
    <div v-if="peekEnabled && isMonitor" class="olo-hoverlist__peek olo-hoverlist__peek--mon" aria-hidden="true" :style="monPeekStyle">
      <span class="olo-hl-mon-scr" :style="monScrStyle">
        <i :style="monCorner('tl')"></i>
        <i :style="monCorner('tr')"></i>
        <i :style="monCorner('bl')"></i>
        <i :style="monCorner('br')"></i>
        <span class="olo-hl-mon-rec" :style="monRecStyle">&#9679; STILL</span>
      </span>
      <span class="olo-hl-mon-lab" :style="monLabStyle">
        <b :style="{ color: ACC, fontWeight: 700 }">{{ peekNum }}</b><span>{{ peekName }}</span>
      </span>
    </div>
    <!-- Peek immagine (comportamento storico) -->
    <div v-else-if="peekEnabled" class="olo-hoverlist__peek" aria-hidden="true" :style="imgPeekStyle">
      <span class="olo-hl-peek-img" :style="imgPeekInnerStyle"></span>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { resolveFontFamily } from '@/composables/oloTileDefaults';
import { buildBgStyle } from '@/composables/useBackgroundStyle';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  items: [
    { color: '#9a3b52', name: 'Rosewood', sub: 'Cool · matte', link_url: '' },
    { color: '#c77a6a', name: 'Terracotta', sub: 'Warm · matte', link_url: '' },
    { color: '#e79aa6', name: 'Peony', sub: 'Cool · blush', link_url: '' },
    { color: '#e6a17e', name: 'Apricot', sub: 'Warm · blush', link_url: '' },
    { color: '#7d2e3e', name: 'Merlot', sub: 'Deep · matte', link_url: '' },
  ],
  lead_mode: 'swatch',
  swatch_size: 26, swatch_shape: 'circle',
  number_color: '', number_hover_color: '',
  name_font_family: 'heading', name_color: '#f6e9ec', name_size: 22, name_uppercase: false,
  sub_color: '#9c7e8c', sub_size: 12, sub_uppercase: true,
  desc_color: '', desc_size: 14,
  row_padding_y: 20, hover_indent: 20, hover_bg: '#4d2f40', line_color: 'rgba(246,233,236,.13)',
  peek: true, peek_mode: 'image', peek_width: 170, peek_ratio: '4/5', mono_font_family: '',
};

const HEADING = "var(--olo-font-family-heading, 'DM Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
const BODY = "var(--olo-font-family, 'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
const MONO_FB = "ui-monospace,'SF Mono',Menlo,Consolas,monospace";
// Monitor "sala di regia" — palette token-first (ink-3/ink-2/bone/signal del blueprint come fallback).
const ACC = 'var(--olo-color-primary, #C6F24E)';
const MON_BORDER = 'var(--olo-color-border, rgba(236,234,227,.2))';
const MON_SCR_BG = 'var(--olo-color-muted, #161922)';
const MON_LAB_BG = 'color-mix(in srgb, var(--olo-color-background, #0b0c0f) 55%, var(--olo-color-muted, #161922))';
const MON_STRIPE = 'color-mix(in srgb, var(--olo-color-text, #ECEAE3) 5%, transparent)';
const MON_TEXT = 'var(--olo-color-text, #ECEAE3)';
const IMG_PH = 'repeating-linear-gradient(135deg,rgba(255,255,255,.06) 0 16px,transparent 16px 32px)';

const s = computed(() => ({ ...defaults, ...props.settings }));
const items = computed(() => Array.isArray(s.value.items) ? s.value.items : []);
const line = computed(() => s.value.line_color || 'rgba(246,233,236,.13)');
const isNumber = computed(() => s.value.lead_mode === 'number');
const mono = computed(() => {
  const fam = resolveFontFamily(s.value.mono_font_family);
  if (!fam) return MONO_FB;
  // Nome font puro (legacy campo text) → wrap con lo stack mono di fallback storico.
  return /^[A-Za-z0-9 \-]+$/.test(fam) ? `'${fam}',${MONO_FB}` : fam;
});
const nfam = computed(() => resolveFontFamily(s.value.name_font_family, { heading: HEADING, body: BODY, mono: mono.value }) || HEADING);

const numColor = computed(() => s.value.number_color || 'var(--olo-color-text-faint, #6a6c64)');
const numHoverColor = computed(() => s.value.number_hover_color || 'var(--olo-color-primary, #C6F24E)');
const descSize = computed(() => {
  const raw = s.value.desc_size;
  if (raw === '' || raw === null || raw === undefined) return 14;
  return parseInt(raw) || 14;
});

function itemNumber(it, idx) {
  const ov = String(it.number || '').trim();
  return ov !== '' ? ov : String(idx + 1).padStart(2, '0');
}

const rowStyle = computed(() => {
  if (isNumber.value) {
    // Riga "sala di regia": griglia 64px 1fr auto (blueprint .srv__row).
    return {
      display: 'grid', gridTemplateColumns: '64px 1fr auto', gap: '24px', alignItems: 'center',
      padding: 'clamp(20px,2.6vw,32px) 4px',
      borderBottom: '1px solid ' + line.value, color: 'inherit', textDecoration: 'none', position: 'relative',
      '--olo-hl-indent': '18px', '--olo-hl-bg': 'var(--olo-color-surface-alt, #101218)',
      '--olo-hl-numhc': numHoverColor.value,
    };
  }
  return {
    display: 'flex', alignItems: 'center', gap: '18px', padding: (s.value.row_padding_y || 20) + 'px 8px',
    borderBottom: '1px solid ' + line.value, color: 'inherit', textDecoration: 'none',
    '--olo-hl-indent': (s.value.hover_indent || 0) + 'px', '--olo-hl-bg': s.value.hover_bg || 'rgba(255,255,255,.05)',
  };
});
// Sfondo per-voce (row_bg: solid/gradient/image). La voce con sfondo proprio lo
// mantiene anche in hover (override della custom property usata dalla regola :hover).
function rowStyleFor(it) {
  const bg = it && it.row_bg;
  if (!bg || !bg.type || bg.type === 'none') return rowStyle.value;
  const bgStyle = buildBgStyle(bg);
  const st = { ...rowStyle.value, ...bgStyle };
  const short = bgStyle.background || bgStyle.backgroundColor
    || (bgStyle.backgroundImage ? `${bgStyle.backgroundImage} ${bgStyle.backgroundPosition || 'center'} / ${bgStyle.backgroundSize || 'cover'} no-repeat` : '');
  if (short) st['--olo-hl-bg'] = short;
  return st;
}
function swStyle(color) {
  return { width: (s.value.swatch_size || 26) + 'px', height: (s.value.swatch_size || 26) + 'px',
    borderRadius: s.value.swatch_shape === 'square' ? '7px' : '50%', flex: 'none', background: color || '#999',
    boxShadow: 'inset 0 0 0 1.5px rgba(246,233,236,.3)' };
}
const numStyle = computed(() => ({
  fontFamily: mono.value, fontSize: '13px', color: numColor.value, transition: 'color .2s ease',
}));
const nameStyle = computed(() => {
  const st = { fontFamily: nfam.value, color: s.value.name_color || '#f6e9ec', lineHeight: 1.1 };
  if (isNumber.value) {
    st.fontWeight = 700;
    st.fontSize = `clamp(26px,3.4vw,${s.value.name_size || 22}px)`;
  } else {
    st.fontSize = (s.value.name_size || 22) + 'px';
  }
  if (s.value.name_uppercase) st.textTransform = 'uppercase';
  return st;
});
const subStyle = computed(() => ({ marginLeft: 'auto', fontFamily: mono.value, fontSize: (s.value.sub_size || 12) + 'px', letterSpacing: '0.06em', color: s.value.sub_color || '#9c7e8c', textTransform: s.value.sub_uppercase ? 'uppercase' : 'none' }));
const subStyleNum = computed(() => ({ justifySelf: 'end', fontFamily: mono.value, fontSize: (s.value.sub_size || 12) + 'px', letterSpacing: '0.06em', color: s.value.sub_color || '#9c7e8c', textAlign: 'right', textTransform: s.value.sub_uppercase ? 'uppercase' : 'none' }));
const descStyle = computed(() => ({
  justifySelf: 'end', fontSize: descSize.value + 'px',
  color: s.value.desc_color || 'var(--olo-color-text-soft, #a0a298)',
  maxWidth: '30ch', textAlign: 'right',
}));

// ── Peek che segue il cursore (replica del runtime frontend nel canvas) ──
const peekEnabled = computed(() => !!s.value.peek);
const isMonitor = computed(() => s.value.peek_mode === 'monitor');
const peekOn = ref(false);
const peekX = ref(0);
const peekY = ref(0);
const peekImage = ref('');
const peekNum = ref('01');
const peekName = ref('');

function onRowEnter(it, idx) {
  if (!peekEnabled.value) return;
  peekImage.value = (it.image || '').trim();
  peekNum.value = itemNumber(it, idx);
  peekName.value = it.name || '';
  peekOn.value = true;
}
function onRowMove(e) {
  if (!peekEnabled.value) return;
  peekX.value = e.clientX;
  peekY.value = e.clientY;
}
function onRowLeave() {
  peekOn.value = false;
}

const imgPeekStyle = computed(() => ({
  position: 'fixed', left: peekX.value + 'px', top: peekY.value + 'px',
  zIndex: 90, pointerEvents: 'none',
  opacity: peekOn.value ? 1 : 0,
  transform: 'translate(16px,-50%)', transition: 'opacity .18s ease',
}));
const imgPeekInnerStyle = computed(() => ({
  display: 'block', width: (s.value.peek_width || 170) + 'px',
  aspectRatio: s.value.peek_ratio || '4/5', borderRadius: '14px', overflow: 'hidden',
  background: 'var(--olo-color-muted, #2b2b2b)',
  backgroundSize: 'cover', backgroundPosition: 'center',
  backgroundImage: peekImage.value ? `url(${peekImage.value})` : IMG_PH,
  boxShadow: '0 18px 50px rgba(0,0,0,.45)',
}));

const monPeekStyle = computed(() => ({
  position: 'fixed', left: peekX.value + 'px', top: peekY.value + 'px',
  zIndex: 90, pointerEvents: 'none', width: '200px',
  opacity: peekOn.value ? 1 : 0,
  transform: peekOn.value ? 'translate(-50%,-112%) scale(1)' : 'translate(-50%,-112%) scale(.92)',
  transition: 'opacity .16s ease, transform .16s ease',
}));
const monScrStyle = computed(() => ({
  position: 'relative', display: 'block', height: '118px',
  border: '1px solid ' + MON_BORDER,
  background: `repeating-linear-gradient(-45deg,${MON_STRIPE} 0 9px,transparent 9px 18px),${MON_SCR_BG}`,
}));
function monCorner(pos) {
  const st = { position: 'absolute', width: '13px', height: '13px', border: '2px solid ' + ACC };
  if (pos === 'tl') Object.assign(st, { left: '8px', top: '8px', borderRight: 0, borderBottom: 0 });
  if (pos === 'tr') Object.assign(st, { right: '8px', top: '8px', borderLeft: 0, borderBottom: 0 });
  if (pos === 'bl') Object.assign(st, { left: '8px', bottom: '8px', borderRight: 0, borderTop: 0 });
  if (pos === 'br') Object.assign(st, { right: '8px', bottom: '8px', borderLeft: 0, borderTop: 0 });
  return st;
}
const monRecStyle = computed(() => ({
  position: 'absolute', left: '50%', top: '50%', transform: 'translate(-50%,-50%)',
  fontFamily: mono.value, fontSize: '9.5px', fontWeight: 700, letterSpacing: '.14em',
  color: ACC, whiteSpace: 'nowrap',
}));
const monLabStyle = computed(() => ({
  display: 'flex', justifyContent: 'space-between', gap: '8px',
  background: MON_LAB_BG, border: '1px solid ' + line.value, borderTop: 0,
  padding: '7px 10px', fontFamily: mono.value, fontSize: '10px',
  letterSpacing: '.08em', textTransform: 'uppercase', color: MON_TEXT,
}));
</script>

<style scoped>
.olo-hoverlist__row { transition: padding .25s ease, background .2s ease; }
.olo-hoverlist__row--num { transition: background .2s ease, padding .2s ease; }
.olo-hoverlist__row:hover { padding-left: var(--olo-hl-indent) !important; background: var(--olo-hl-bg); }
.olo-hoverlist__row--num:hover .olo-hoverlist__num { color: var(--olo-hl-numhc); }
@media (max-width: 680px) {
  .olo-hoverlist__row--num { grid-template-columns: 44px 1fr !important; }
  .olo-hoverlist__row--num .olo-hoverlist__desc { display: none; }
}
</style>
