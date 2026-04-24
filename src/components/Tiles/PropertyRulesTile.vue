<template>
  <div class="mb-font-sans" :style="wrapStyle">
    <div :style="{ display:'flex', alignItems:'center', gap:'10px', marginBottom:'12px' }">
      <span :style="{ fontSize:'20px' }">&#128737;</span>
      <h3 :style="{ fontSize:'16px', fontWeight:'700', color:'#1F2937', margin:0 }">{{ t('Note legali') }}</h3>
    </div>
    <div :style="{ fontSize:'13px', color:'#6B7280', lineHeight:1.7 }">
      <p :style="{ margin:'0 0 8px' }">{{ t('Le informazioni riportate in questo annuncio sono indicative e non costituiscono elemento contrattuale. I dati relativi a superfici, vani e accessori sono desunti dalla documentazione catastale e potrebbero non corrispondere allo stato di fatto.') }}</p>
      <p :style="{ margin:0 }">{{ t('Le immagini sono puramente illustrative. Per informazioni dettagliate e per fissare un appuntamento di visita, contattare l\'agenzia ai recapiti indicati. Classe energetica e indice di prestazione come da attestato APE in corso di validit&agrave;.') }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { bg: '#F9FAFB', padding: 24, border_radius: 12 };
const s = computed(() => ({ ...defaults, ...props.settings }));
const wrapStyle = computed(() => ({ background: s.value.bg, padding: s.value.padding+'px', borderRadius: s.value.border_radius+'px', border:'1px solid #E5E7EB' }));
</script>
