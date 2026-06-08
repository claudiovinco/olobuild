<template>
  <div class="olo-timezone" :style="rootStyle">
    <span v-if="s.eyebrow" :style="eyebrowStyle">{{ s.eyebrow }}</span>
    <h2 v-if="s.heading" :style="headingStyle">{{ s.heading }}</h2>
    <p v-if="s.intro" :style="introStyle">{{ s.intro }}</p>
    <div style="display:flex;align-items:baseline;justify-content:space-between;gap:14px;flex-wrap:wrap">
      <span :style="baseLabelStyle">{{ s.base_label }} · {{ items[0] ? items[0].city : '' }}</span>
      <span :style="baseValStyle">{{ hh(cur) }}</span>
    </div>
    <input type="range" :style="rangeStyle" min="0" max="23" step="1" v-model="cur" :aria-label="s.base_label" />
    <div :style="gridStyle">
      <div v-for="(it, i) in items" :key="i" :style="cityStyle">
        <div :style="cityCStyle">{{ it.city }}</div>
        <div v-if="it.label" :style="cityOStyle">{{ it.label }}</div>
        <div :style="cityTStyle"><span :style="dotStyle(localFor(it))"></span>{{ hh(localHour(it)) }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = {
  eyebrow: '', heading: 'Trova un orario che funziona', intro: '', base_label: 'La tua ora',
  input_value: 14, work_start: 9, work_end: 18,
  items: [
    { city: 'San Francisco', offset: -7, label: 'PDT' }, { city: 'London', offset: 1, label: 'BST' },
    { city: 'Berlin', offset: 2, label: 'CEST' }, { city: 'Singapore', offset: 8, label: 'SGT' },
  ],
  zone_accent: '', work_color: '', ok_color: '#e0a23a', sleep_color: '', card_bg: '', card_border: '', align: 'left',
};
const s = computed(() => ({ ...defaults, ...props.settings }));
const items = computed(() => Array.isArray(s.value.items) ? s.value.items : []);
const cur = ref(parseInt(s.value.input_value) || 14);
const baseOff = computed(() => parseFloat(items.value[0]?.offset) || 0);

function wrap(n) { n = Math.round(n); return ((n % 24) + 24) % 24; }
function hh(n) { const h = wrap(n); return String(h).padStart(2, '0') + ':00'; }
function localHour(it) { return wrap((parseFloat(cur.value) || 0) - baseOff.value + (parseFloat(it.offset) || 0)); }
function localFor(it) {
  const h = localHour(it), ws = parseInt(s.value.work_start) || 9, we = parseInt(s.value.work_end) || 18;
  if (h >= ws && h < we) return 'w';
  if ((h >= ws - 3 && h < ws) || (h >= we && h < we + 3)) return 'o';
  return 's';
}

const accent = computed(() => s.value.zone_accent || 'var(--olo-color-primary, #e1474f)');
const work = computed(() => s.value.work_color || accent.value);
const ok = computed(() => s.value.ok_color || '#e0a23a');
const sleep = computed(() => s.value.sleep_color || 'var(--olo-color-text-muted,#9ca3af)');
const line = computed(() => s.value.card_border || 'var(--olo-color-border,#e5e7eb)');
const center = computed(() => s.value.align === 'center');
const SERIF = "var(--olo-font-family-heading, 'Playfair Display',Georgia,serif)";
const SANS = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";
const pct = computed(() => ((parseFloat(cur.value) - 0) / 23 * 100) + '%');

const rootStyle = computed(() => ({ fontFamily: SANS, textAlign: center.value ? 'center' : 'left' }));
const eyebrowStyle = computed(() => ({ fontSize: '12px', fontWeight: 700, letterSpacing: '.16em', textTransform: 'uppercase', color: accent.value, display: 'block', marginBottom: '10px' }));
const headingStyle = computed(() => ({ fontFamily: SERIF, fontSize: '38px', lineHeight: 1.12, margin: 0, color: 'var(--olo-color-text,#111827)' }));
const introStyle = computed(() => ({ fontSize: '15.5px', lineHeight: 1.6, opacity: .8, margin: '14px 0 22px', maxWidth: '560px' }));
const baseLabelStyle = computed(() => ({ fontSize: '12px', fontWeight: 700, letterSpacing: '.06em', textTransform: 'uppercase', opacity: .7 }));
const baseValStyle = computed(() => ({ fontFamily: SERIF, fontSize: '26px', color: accent.value }));
const rangeStyle = computed(() => ({ width: '100%', height: '6px', borderRadius: '99px', cursor: 'pointer', margin: '10px 0 24px', accentColor: accent.value, background: `linear-gradient(to right, ${accent.value} ${pct.value}, ${line.value} ${pct.value})`, WebkitAppearance: 'none', appearance: 'none' }));
const gridStyle = computed(() => ({ display: 'grid', gridTemplateColumns: `repeat(${Math.min(4, Math.max(1, items.value.length))},1fr)`, gap: '12px' }));
const cityStyle = computed(() => ({ background: s.value.card_bg || 'var(--olo-color-surface,#ffffff)', border: '1px solid ' + line.value, borderRadius: '12px', padding: '16px', textAlign: 'left' }));
const cityCStyle = computed(() => ({ fontWeight: 600, fontSize: '14px', color: 'var(--olo-color-text,#111827)' }));
const cityOStyle = computed(() => ({ fontSize: '11px', opacity: .55, letterSpacing: '.04em' }));
const cityTStyle = computed(() => ({ fontFamily: SERIF, fontSize: '24px', marginTop: '8px', color: 'var(--olo-color-text,#111827)', display: 'flex', alignItems: 'center', gap: '8px' }));
function dotStyle(st) { const c = st === 'w' ? work.value : (st === 'o' ? ok.value : sleep.value); return { width: '9px', height: '9px', borderRadius: '50%', background: c, flex: 'none' }; }
</script>
