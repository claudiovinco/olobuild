<template>
  <div style="padding:10px;background:var(--olo-color-surface-alt, #f6f7f9);border-radius:8px;min-height:60px;">
    <div style="font-size:13px;font-weight:700;color:var(--olo-color-text, #1f2937);margin-bottom:10px;">{{ t('Cross-sell') }}</div>
    <div :style="gridStyle">
      <div
        v-for="i in cols"
        :key="i"
        style="background:var(--olo-color-surface, #ffffff);border-radius:6px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.08);"
      >
        <div :style="imgBoxStyle">
          <svg style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:24px;height:24px;opacity:0.25;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <rect x="3" y="3" width="18" height="18" rx="2"/>
            <circle cx="8.5" cy="8.5" r="1.5"/>
            <path d="M21 15l-5-5L5 21"/>
          </svg>
        </div>
        <div style="padding:8px;">
          <div style="font-size:12px;font-weight:600;color:var(--olo-color-text, #1f2937);margin-bottom:4px;">Prodotto {{ i }}</div>
          <div style="font-size:13px;font-weight:700;color:var(--olo-color-text, #374151);">€ 29,00</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  columns: '3',
  gap: '16',
  image_ratio: '4-3',
  image_ratio_custom: '4/3',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const cols = computed(() => Math.max(1, Math.min(6, parseInt(s.value.columns) || 3)));

// Stessa tabella del renderer PHP, valori compresi: il canvas è uno schema (i prodotti
// veri li ha solo WooCommerce) ma la FORMA della scatola immagine deve essere quella
// del sito, altrimenti scegliere le proporzioni qui non mostrerebbe niente.
const RATIO_PAD = {
  '1-1': '100%', '4-3': '75%', '3-2': '66.6667%', '16-9': '56.25%', '21-9': '42.8571%',
  '3-4': '133.33%', '4-5': '125%', '9-16': '177.7778%', '2-3': '150%', auto: '0',
};
const imgBoxStyle = computed(() => {
  const r = String(s.value.image_ratio || '4-3');
  let pad = RATIO_PAD[r] || '75%';
  if (r === 'custom') {
    const m = /^(\d+(?:\.\d+)?)\s*\/\s*(\d+(?:\.\d+)?)$/.exec(String(s.value.image_ratio_custom || '').replace(/[-:]/g, '/'));
    pad = (m && parseFloat(m[1]) > 0) ? (parseFloat(m[2]) / parseFloat(m[1]) * 100) + '%' : '75%';
  }
  return {
    width: '100%',
    // 'auto' = nessuna scatola imposta, l'immagine tiene la sua altezza: nello schema
    // si mostra comunque un riquadro, se no la card sembrerebbe rotta.
    paddingTop: r === 'auto' ? '56.25%' : pad,
    background: 'var(--olo-color-surface-alt, #f6f7f9)',
    position: 'relative',
  };
});

const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${cols.value}, 1fr)`,
  gap: (parseInt(s.value.gap) || 16) + 'px',
}));
</script>
