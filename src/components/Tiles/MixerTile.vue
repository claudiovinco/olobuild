<template>
  <div class="olo-mixer" :style="rootStyle">
    <span v-if="s.eyebrow" :style="eyebrowStyle">{{ s.eyebrow }}</span>
    <h2 v-if="s.heading" :style="headingStyle">{{ s.heading }}</h2>
    <p v-if="s.intro" :style="introStyle">{{ s.intro }}</p>
    <div :style="panelStyle">
      <div style="display:flex;flex-wrap:wrap;gap:12px">
        <button v-for="(it, i) in items" :key="i" type="button"
          :style="swStyle(i, it)" @click="toggle(i)" :aria-label="it.name"></button>
      </div>
      <div style="text-align:center">
        <div :style="previewStyle"></div>
        <div :style="outStyle">{{ outText }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = {
  eyebrow: 'Prova', heading: 'Componi la tua tinta', intro: '', max: 3,
  empty_label: 'Tocca i campioni per fondere',
  items: [
    { name: 'Ocra', color: '#caa44a' }, { name: 'Terra', color: '#9c6b4a' },
    { name: 'Crema', color: '#efe5da' }, { name: 'Inchiostro', color: '#1a1a1a' },
  ],
  zone_accent: '', zone_on: '#ffffff', card_bg: '', card_border: '', align: 'left',
};
const s = computed(() => ({ ...defaults, ...props.settings }));
const items = computed(() => Array.isArray(s.value.items) ? s.value.items : []);
const max = computed(() => Math.max(1, parseInt(s.value.max) || 3));
const sel = ref([]);
watch(items, () => { sel.value = []; });

function toggle(i) {
  const idx = sel.value.indexOf(i);
  if (idx === -1) { if (sel.value.length === max.value) sel.value.shift(); sel.value.push(i); }
  else sel.value.splice(idx, 1);
}
function hexToRgb(h) {
  h = String(h).replace('#', '');
  if (h.length === 3) h = h[0] + h[0] + h[1] + h[1] + h[2] + h[2];
  return [parseInt(h.substr(0, 2), 16) || 0, parseInt(h.substr(2, 2), 16) || 0, parseInt(h.substr(4, 2), 16) || 0];
}
function toHex(n) { const v = Math.round(n).toString(16); return v.length === 1 ? '0' + v : v; }
const blend = computed(() => {
  if (!sel.value.length) return 'transparent';
  let r = 0, g = 0, b = 0;
  sel.value.forEach(i => { const c = hexToRgb(items.value[i]?.color || '#ccc'); r += c[0]; g += c[1]; b += c[2]; });
  const n = sel.value.length;
  return '#' + toHex(r / n) + toHex(g / n) + toHex(b / n);
});
const outText = computed(() => sel.value.length ? sel.value.map(i => items.value[i]?.name || '').join(' + ') : s.value.empty_label);

const SERIF = "var(--olo-font-family-heading, 'Playfair Display',Georgia,serif)";
const SANS = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";
const accent = computed(() => s.value.zone_accent || 'var(--olo-color-primary, #e1474f)');
const center = computed(() => s.value.align === 'center');
const cardbd = computed(() => s.value.card_border || 'var(--olo-color-border,#e5e7eb)');

const rootStyle = computed(() => ({ fontFamily: SANS, textAlign: center.value ? 'center' : 'left' }));
const eyebrowStyle = computed(() => ({ fontSize: '12px', fontWeight: 700, letterSpacing: '.16em', textTransform: 'uppercase', color: accent.value, display: 'block', marginBottom: '10px' }));
const headingStyle = computed(() => ({ fontFamily: SERIF, fontSize: '38px', lineHeight: 1.12, margin: 0, color: 'var(--olo-color-text,#111827)' }));
const introStyle = computed(() => ({ fontSize: '15.5px', lineHeight: 1.6, opacity: .8, margin: center.value ? '14px auto 0' : '14px 0 0', maxWidth: '560px' }));
const panelStyle = computed(() => ({ marginTop: '26px', display: 'grid', gridTemplateColumns: '1.1fr .9fr', gap: '40px', alignItems: 'center', background: s.value.card_bg || 'var(--olo-color-surface-alt,#f6f7f9)', border: '1px solid ' + cardbd.value, borderRadius: '16px', padding: '34px', textAlign: 'left', maxWidth: center.value ? '760px' : 'none', marginLeft: center.value ? 'auto' : '0', marginRight: center.value ? 'auto' : '0' }));
function swStyle(i, it) {
  const onSel = sel.value.indexOf(i) !== -1;
  return { width: '64px', height: '64px', borderRadius: '14px', background: it.color || '#ccc', cursor: 'pointer', transition: 'all .15s', border: '2px solid ' + (onSel ? accent.value : 'transparent'), boxShadow: onSel ? '0 0 0 3px rgba(127,127,127,.2)' : 'none' };
}
const previewStyle = computed(() => ({ width: '100%', height: '150px', borderRadius: '14px', border: '1px solid ' + cardbd.value, background: blend.value, transition: 'background .35s' }));
const outStyle = computed(() => ({ marginTop: '14px', fontFamily: SERIF, fontSize: '18px', color: 'var(--olo-color-text,#111827)', minHeight: '1.4em' }));
</script>
