<template>
  <div :style="wrapStyle">
    <div :style="imgStyle"></div>
    <div :style="overlayStyle"></div>
    <div style="position:relative;z-index:2;text-align:center;color:#fff;padding:30px 20px">
      <span v-if="s.show_badge" :style="badgeStyle">{{ t('Sala Conferenze') }}</span>
      <div :style="{fontSize:'24px',fontWeight:'700',margin:'8px 0 4px',textShadow:'0 2px 8px rgba(0,0,0,.4)'}">{{ t('Nome Sala') }}</div>
      <div v-if="s.show_address" style="font-size:13px;opacity:.85;margin-bottom:14px">{{ t('Via Roma 1, 38068 Rovereto (TN)') }}</div>
      <div v-if="s.cta_text" :style="ctaStyle">{{ s.cta_text }}</div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { TOKENS } from '@/composables/oloTileDefaults';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { height: 420, overlay: true, overlay_opacity: 45, show_badge: true, show_address: true, cta_text: 'Prenota questa sala' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const wrapStyle = computed(() => ({ position: 'relative', height: Math.min(parseInt(s.value.height) || 420, 250) + 'px', borderRadius: '12px', overflow: 'hidden', display: 'flex', alignItems: 'center', justifyContent: 'center' }));
// Sfondo hero = gradiente brand (navy → primario). Era #1e3a5f→#0e7490 off-brand.
const imgStyle = computed(() => ({ position: 'absolute', inset: '0', background: `linear-gradient(135deg, ${TOKENS.secondary} 0%, ${TOKENS.primary} 100%)` }));
const overlayStyle = computed(() => ({ position: 'absolute', inset: '0', background: s.value.overlay ? `rgba(0,0,0,${(parseInt(s.value.overlay_opacity)||45)/100})` : 'transparent', zIndex: '1' }));
// Badge/CTA = primario brand su testo onPrimary (era #e1474f).
const badgeStyle = computed(() => ({ background: TOKENS.primary, color: TOKENS.onPrimary, padding: '3px 12px', borderRadius: '4px', fontSize: '11px', fontWeight: '600', display: 'inline-block', marginBottom: '6px' }));
const ctaStyle = computed(() => ({ display: 'inline-block', background: TOKENS.primary, color: TOKENS.onPrimary, padding: '8px 20px', borderRadius: '8px', fontSize: '13px', fontWeight: '600' }));
</script>
