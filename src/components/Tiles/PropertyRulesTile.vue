<template>
  <div class="mb-font-sans" :style="wrapStyle">
    <div :style="{ display:'flex', alignItems:'center', gap:'10px', marginBottom:'12px' }">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" :stroke="accent" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/></svg>
      <h3 :style="{ fontSize:'16px', fontWeight:'700', color:'var(--olo-color-text, #1f2937)', margin:0 }">{{ t('Note legali') }}</h3>
    </div>
    <div :style="{ fontSize:'13px', color:'var(--olo-color-text-muted, #6b7280)', lineHeight:1.7 }">
      <p :style="{ margin:'0 0 8px' }">{{ t('Le informazioni riportate in questo annuncio sono indicative e non costituiscono elemento contrattuale. I dati relativi a superfici, vani e accessori sono desunti dalla documentazione catastale e potrebbero non corrispondere allo stato di fatto.') }}</p>
      <p :style="{ margin:0 }">{{ t('Le immagini sono puramente illustrative. Per informazioni dettagliate e per fissare un appuntamento di visita, contattare l\'agenzia ai recapiti indicati. Classe energetica e indice di prestazione come da attestato APE in corso di validit&agrave;.') }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { bg: '', padding: 24, border_radius: 12, accent_color: '' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const accent = computed(() => s.value.accent_color || 'var(--olo-color-primary, #e1474f)');
const wrapStyle = computed(() => ({ background: s.value.bg || 'var(--olo-color-surface-alt, #f6f7f9)', padding: s.value.padding+'px', borderRadius: s.value.border_radius+'px', border:'1px solid var(--olo-color-border, #e5e7eb)' }));
</script>
