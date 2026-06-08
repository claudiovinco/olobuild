<template>
  <div class="olo-hotspots" :style="rootStyle">
    <span v-if="s.eyebrow" :style="eyebrowStyle">{{ s.eyebrow }}</span>
    <h2 v-if="s.heading" :style="headingStyle">{{ s.heading }}</h2>
    <p v-if="s.intro" :style="introStyle">{{ s.intro }}</p>
    <div :style="panelStyle">
      <span :style="labelStyle">{{ s.panel_label }}</span>
      <button v-for="(it, i) in items" :key="i" type="button"
        :style="mkStyle(it)" @click.stop="active = (active === i ? -1 : i)" :aria-label="it.title">
        <span v-if="active === i" :style="tipStyle">
          <span v-if="it.meta" :style="tipMetaStyle">{{ it.meta }}</span>
          <span v-if="it.title" :style="tipTitleStyle">{{ it.title }}</span>
          <span v-if="it.text" :style="tipTextStyle">{{ it.text }}</span>
        </span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = {
  eyebrow: '', heading: 'Esplora', intro: '', panel_label: 'SCENE', aspect_ratio: '16/10',
  items: [
    { x: 28, y: 36, title: 'Punto 1', text: 'Descrizione.', meta: '' },
    { x: 62, y: 58, title: 'Punto 2', text: 'Descrizione.', meta: '' },
    { x: 44, y: 74, title: 'Punto 3', text: 'Descrizione.', meta: '' },
  ],
  zone_accent: '', zone_on: '#ffffff', panel_bg: '', card_bg: '', card_border: '', align: 'left',
};
const s = computed(() => ({ ...defaults, ...props.settings }));
const items = computed(() => Array.isArray(s.value.items) ? s.value.items : []);
const active = ref(-1);

const accent = computed(() => s.value.zone_accent || 'var(--olo-color-primary, #e1474f)');
const on = computed(() => s.value.zone_on || '#ffffff');
const line = computed(() => s.value.card_border || 'var(--olo-color-border,#e5e7eb)');
const center = computed(() => s.value.align === 'center');
const SERIF = "var(--olo-font-family-heading, 'Playfair Display',Georgia,serif)";
const SANS = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";

const rootStyle = computed(() => ({ fontFamily: SANS, textAlign: center.value ? 'center' : 'left' }));
const eyebrowStyle = computed(() => ({ fontSize: '12px', fontWeight: 700, letterSpacing: '.16em', textTransform: 'uppercase', color: accent.value, display: 'block', marginBottom: '10px' }));
const headingStyle = computed(() => ({ fontFamily: SERIF, fontSize: '38px', lineHeight: 1.12, margin: 0, color: 'var(--olo-color-text,#111827)' }));
const introStyle = computed(() => ({ fontSize: '15.5px', lineHeight: 1.6, opacity: .8, margin: center.value ? '14px auto 0' : '14px 0 0', maxWidth: '560px' }));
const panelStyle = computed(() => ({ position: 'relative', marginTop: '24px', aspectRatio: s.value.aspect_ratio || '16/10', borderRadius: '16px', overflow: 'hidden', border: '1px solid ' + line.value, background: (s.value.panel_bg || 'var(--olo-color-surface-alt,#f1f1f1)'), backgroundImage: 'repeating-linear-gradient(135deg, rgba(127,127,127,.05) 0 16px, transparent 16px 32px)' }));
const labelStyle = computed(() => ({ position: 'absolute', left: '16px', bottom: '14px', fontSize: '10.5px', fontWeight: 600, letterSpacing: '.05em', textTransform: 'uppercase', opacity: .45 }));
function mkStyle(it) {
  return { position: 'absolute', left: (it.x || 50) + '%', top: (it.y || 50) + '%', transform: 'translate(-50%,-50%)', width: '26px', height: '26px', borderRadius: '50%', border: 0, cursor: 'pointer', background: accent.value, padding: 0 };
}
const tipStyle = computed(() => ({ position: 'absolute', bottom: '130%', left: '50%', transform: 'translateX(-50%)', width: '220px', background: (s.value.card_bg || 'var(--olo-color-surface,#ffffff)'), border: '1px solid ' + line.value, borderRadius: '12px', padding: '14px 16px', textAlign: 'left', boxShadow: '0 12px 30px -10px rgba(16,24,40,.3)', zIndex: 5 }));
const tipMetaStyle = computed(() => ({ fontSize: '11px', fontWeight: 700, letterSpacing: '.08em', textTransform: 'uppercase', color: accent.value, display: 'block' }));
const tipTitleStyle = computed(() => ({ fontFamily: SERIF, fontSize: '16px', margin: '4px 0 0', color: 'var(--olo-color-text,#111827)', display: 'block' }));
const tipTextStyle = computed(() => ({ fontSize: '13px', lineHeight: 1.5, opacity: .75, margin: '6px 0 0', display: 'block' }));
</script>
