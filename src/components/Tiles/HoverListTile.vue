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
import def from '@/config/elements/hoverlist.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const HEADING = "var(--olo-font-family-heading, 'DM Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
const BODY = "var(--olo-font-family, 'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
const MONO_FB = "ui-monospace,'SF Mono',Menlo,Consolas,monospace";
const LIGHT = 'var(--olo-color-light, #f8f9fa)';
const LINE_FB = `color-mix(in srgb, ${LIGHT} 13%, transparent)`;
const SW_RING = `color-mix(in srgb, ${LIGHT} 30%, transparent)`;
// Monitor "sala di regia" — palette token-first (ink-3/ink-2/bone/signal del blueprint come fallback).
const ACC = 'var(--olo-color-primary, #C6F24E)';
const MON_BORDER = 'var(--olo-color-border, rgba(236,234,227,.2))';
const MON_SCR_BG = 'var(--olo-color-muted, #161922)';
const MON_LAB_BG = 'color-mix(in srgb, var(--olo-color-background, #0b0c0f) 55%, var(--olo-color-muted, #161922))';
const MON_STRIPE = 'color-mix(in srgb, var(--olo-color-text, #ECEAE3) 5%, transparent)';
const MON_TEXT = 'var(--olo-color-text, #ECEAE3)';
const IMG_PH = 'repeating-linear-gradient(135deg,rgba(255,255,255,.06) 0 16px,transparent 16px 32px)';

// Default da fonte unica: il config dell'inspector (nessuna copia divergente qui).
const s = computed(() => ({ ...def.defaults, ...props.settings }));
const items = computed(() => Array.isArray(s.value.items) ? s.value.items : []);
const line = computed(() => s.value.line_color || LINE_FB);
const isNumber = computed(() => s.value.lead_mode === 'number');
const mono = computed(() => {
  const fam = resolveFontFamily(s.value.mono_font_family);
  if (!fam) return MONO_FB;
  // Nome font puro (legacy campo text) → wrap con lo stack mono di fallback storico.
  return /^[A-Za-z0-9 \-]+$/.test(fam) ? `'${fam}',${MONO_FB}` : fam;
});
const nfam = computed(() => resolveFontFamily(s.value.name_font_family, { heading: HEADING, body: BODY, mono: mono.value }) || HEADING);

// ── Hover bilaterale (gemello di build_hover_css): valore hover vuoto = proprietà invariata. ──
const isSet = (v) => v !== '' && v !== null && v !== undefined;
const hv = (hover, base) => (isSet(hover) ? hover : base);
const clamp = (v, min, max, fb) => { const n = parseInt(v, 10); return Number.isFinite(n) ? Math.max(min, Math.min(max, n)) : fb; };
// Durata: < 1 → 300ms, come il default dell'helper PHP.
const ms = (v) => { const n = parseInt(v, 10); return (n > 0 ? n : 300) + 'ms'; };

const numColor = computed(() => s.value.number_color || 'var(--olo-color-text-faint, #6a6c64)');
// Al hover il numero passa al primario se non specificato (blueprint sala di regia).
const numHoverColor = computed(() => s.value.number_hover_color || ACC);
const nameColor = computed(() => s.value.name_color || LIGHT);
const subColor = computed(() => s.value.sub_color || 'var(--olo-color-text-soft, #6b7280)');
const descColor = computed(() => s.value.desc_color || 'var(--olo-color-text-soft, #a0a298)');
const swSize = computed(() => clamp(s.value.swatch_size, 14, 44, 26));
const swHoverSize = computed(() => (isSet(s.value.swatch_hover_size) ? clamp(s.value.swatch_hover_size, 14, 44, swSize.value) : swSize.value));
// Rientro riga a riposo: '' = automatico (8px pastiglia / 4px numerato = resa storica).
const indentBase = computed(() => (isSet(s.value.row_indent) ? clamp(s.value.row_indent, 0, 60, 8) : (isNumber.value ? 4 : 8)));
const indentHover = computed(() => (isSet(s.value.hover_indent) ? clamp(s.value.hover_indent, 0, 60, indentBase.value) : indentBase.value));
const rowBg = computed(() => s.value.row_bg_color || 'transparent');
const descSize = computed(() => {
  const raw = s.value.desc_size;
  if (raw === '' || raw === null || raw === undefined) return 14;
  return parseInt(raw) || 14;
});

function itemNumber(it, idx) {
  const ov = String(it.number || '').trim();
  return ov !== '' ? ov : String(idx + 1).padStart(2, '0');
}

// Layout e box della riga vivono in <style scoped> (così le regole :hover vincono per
// cascata, senza !important). Inline restano SOLO le custom property che le pilotano
// e lo "scudo" contro gli stili link del tema (color/text-decoration).
const rowStyle = computed(() => ({
  color: 'inherit', textDecoration: 'none',
  '--olo-hl-py': isNumber.value ? 'clamp(20px,2.6vw,32px)' : clamp(s.value.row_padding_y, 8, 40, 20) + 'px',
  '--olo-hl-pr': isNumber.value ? '4px' : '8px',
  '--olo-hl-pl': indentBase.value + 'px',
  '--olo-hl-indent': indentHover.value + 'px',
  '--olo-hl-bg': rowBg.value,
  '--olo-hl-bg-h': hv(s.value.hover_bg, rowBg.value),
  '--olo-hl-line': line.value,
  '--olo-hl-line-h': hv(s.value.line_hover_color, line.value),
  '--olo-hl-nm': nameColor.value,
  '--olo-hl-nm-h': hv(s.value.name_hover_color, nameColor.value),
  '--olo-hl-sub': subColor.value,
  '--olo-hl-sub-h': hv(s.value.sub_hover_color, subColor.value),
  '--olo-hl-desc': descColor.value,
  '--olo-hl-desc-h': hv(s.value.desc_hover_color, descColor.value),
  '--olo-hl-num': numColor.value,
  '--olo-hl-num-h': numHoverColor.value,
  '--olo-hl-sw': swSize.value + 'px',
  '--olo-hl-sw-h': swHoverSize.value + 'px',
  '--olo-hl-t-indent': ms(s.value.hover_indent_duration),
  '--olo-hl-t-bg': ms(s.value.hover_bg_duration),
  '--olo-hl-t-line': ms(s.value.line_color_hover_duration),
  '--olo-hl-t-nm': ms(s.value.name_color_hover_duration),
  '--olo-hl-t-sub': ms(s.value.sub_color_hover_duration),
  '--olo-hl-t-desc': ms(s.value.desc_color_hover_duration),
  '--olo-hl-t-num': ms(s.value.number_color_hover_duration),
  '--olo-hl-t-sw': ms(s.value.swatch_size_hover_duration),
}));
// Sfondo per-voce (row_bg: solid/gradient/image): inline, quindi vince sulle regole
// scoped a riposo E in hover → la voce con sfondo proprio lo mantiene al passaggio del mouse.
function rowStyleFor(it) {
  const bg = it && it.row_bg;
  if (!bg || !bg.type || bg.type === 'none') return rowStyle.value;
  return { ...rowStyle.value, ...buildBgStyle(bg) };
}
function swStyle(color) {
  return { borderRadius: s.value.swatch_shape === 'square' ? '7px' : '50%', flex: 'none',
    background: color || 'var(--olo-color-border, #e5e7eb)',
    boxShadow: 'inset 0 0 0 1.5px ' + SW_RING };
}
const numStyle = computed(() => ({ fontFamily: mono.value, fontSize: '13px' }));
const nameStyle = computed(() => {
  const st = { fontFamily: nfam.value, lineHeight: 1.1 };
  if (isNumber.value) {
    st.fontWeight = 700;
    st.fontSize = `clamp(26px,3.4vw,${s.value.name_size || 22}px)`;
  } else {
    st.fontSize = (s.value.name_size || 22) + 'px';
  }
  if (s.value.name_uppercase) st.textTransform = 'uppercase';
  return st;
});
const subStyle = computed(() => ({ marginLeft: 'auto', fontFamily: mono.value, fontSize: (s.value.sub_size || 12) + 'px', letterSpacing: '0.06em', textTransform: s.value.sub_uppercase ? 'uppercase' : 'none' }));
const subStyleNum = computed(() => ({ justifySelf: 'end', fontFamily: mono.value, fontSize: (s.value.sub_size || 12) + 'px', letterSpacing: '0.06em', textAlign: 'right', textTransform: s.value.sub_uppercase ? 'uppercase' : 'none' }));
const descStyle = computed(() => ({
  justifySelf: 'end', fontSize: descSize.value + 'px',
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
  backgroundSize: 'cover', backgroundPosition: (s.value.object_position || 'center center'),
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
/* Riga: base + hover pilotati dalle custom property inline (gemello del CSS di classe PHP).
   Nessun !important: base e :hover stanno nella stessa foglia, vince la cascata. */
.olo-hoverlist__row {
  display: flex; align-items: center; gap: 18px;
  padding: var(--olo-hl-py) var(--olo-hl-pr) var(--olo-hl-py) var(--olo-hl-pl);
  border-bottom: 1px solid var(--olo-hl-line);
  background-color: var(--olo-hl-bg);
  transition: padding-left var(--olo-hl-t-indent) ease, background-color var(--olo-hl-t-bg) ease, border-bottom-color var(--olo-hl-t-line) ease;
}
/* Riga "sala di regia": griglia 64px 1fr auto (blueprint .srv__row). */
.olo-hoverlist__row--num { display: grid; grid-template-columns: 64px 1fr auto; gap: 24px; position: relative; }
.olo-hoverlist__row:hover {
  padding-left: var(--olo-hl-indent);
  background-color: var(--olo-hl-bg-h);
  border-bottom-color: var(--olo-hl-line-h);
}
.olo-hoverlist__nm { color: var(--olo-hl-nm); transition: color var(--olo-hl-t-nm) ease; }
.olo-hoverlist__row:hover .olo-hoverlist__nm { color: var(--olo-hl-nm-h); }
/* La descrizione esclude il "sub in colonna destra" (che porta entrambe le classi). */
.olo-hoverlist__desc:not(.olo-hoverlist__sub) { color: var(--olo-hl-desc); transition: color var(--olo-hl-t-desc) ease; }
.olo-hoverlist__row:hover .olo-hoverlist__desc:not(.olo-hoverlist__sub) { color: var(--olo-hl-desc-h); }
.olo-hoverlist__sub { color: var(--olo-hl-sub); transition: color var(--olo-hl-t-sub) ease; }
.olo-hoverlist__row:hover .olo-hoverlist__sub { color: var(--olo-hl-sub-h); }
.olo-hoverlist__num { color: var(--olo-hl-num); transition: color var(--olo-hl-t-num) ease; }
.olo-hoverlist__row:hover .olo-hoverlist__num { color: var(--olo-hl-num-h); }
.olo-hoverlist__sw { width: var(--olo-hl-sw); height: var(--olo-hl-sw); transition: width var(--olo-hl-t-sw) ease, height var(--olo-hl-t-sw) ease; }
.olo-hoverlist__row:hover .olo-hoverlist__sw { width: var(--olo-hl-sw-h); height: var(--olo-hl-sw-h); }
a.olo-hoverlist__row:focus-visible { outline: 2px solid var(--olo-hl-nm); outline-offset: -2px; }
@media (max-width: 680px) {
  .olo-hoverlist__row--num { grid-template-columns: 44px 1fr; }
  .olo-hoverlist__row--num .olo-hoverlist__desc { display: none; }
}
</style>
