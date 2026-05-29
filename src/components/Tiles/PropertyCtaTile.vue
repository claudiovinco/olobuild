<template>
  <div class="mb-font-sans" :style="cardStyle">
    <div :style="{ display:'flex', alignItems:'center', gap:'16px', marginBottom:'16px' }">
      <div :style="avatarStyle">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--olo-color-primary-contrast, #fff)" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>
      </div>
      <div>
        <div :style="{ fontSize:'16px', fontWeight:'700', color:'var(--olo-color-text, #1f2937)' }">{{ t('Mario Rossi') }}</div>
        <div :style="{ fontSize:'13px', color:'var(--olo-color-text-muted, #6b7280)' }">{{ t('Agente Immobiliare') }}</div>
      </div>
    </div>
    <div :style="{ display:'flex', flexDirection:'column', gap:'10px' }">
      <div :style="infoRow">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" :stroke="accent" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        <span>+39 0461 123 456</span>
      </div>
      <div :style="infoRow">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" :stroke="accent" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
        <span>{{ t('mario.rossi@agenzia.it') }}</span>
      </div>
    </div>
    <button type="button" class="olo-pcta-wa" :style="whatsappStyle">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2 22l4.832-1.438A9.955 9.955 0 0 0 12 22c5.523 0 10-4.477 10-10S17.523 2 12 2z"/></svg>
      <span>{{ t('Contatta su WhatsApp') }}</span>
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { max_width: 380, border_radius: 12, shadow: 'md', padding: 24, accent_color: '' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const accent = computed(() => s.value.accent_color || 'var(--olo-color-primary, #e1474f)');
const shadowMap = { none:'none', sm:'0 1px 3px rgba(0,0,0,0.1)', md:'0 4px 12px rgba(0,0,0,0.1)', lg:'0 8px 24px rgba(0,0,0,0.15)' };
const cardStyle = computed(() => ({ maxWidth: s.value.max_width+'px', background:'var(--olo-color-surface, #fff)', borderRadius: s.value.border_radius+'px', boxShadow: shadowMap[s.value.shadow]||shadowMap.md, padding: s.value.padding+'px', border:'1px solid var(--olo-color-border, #e5e7eb)' }));
const avatarStyle = computed(() => ({ width:'56px', height:'56px', borderRadius:'50%', background: accent.value, display:'flex', alignItems:'center', justifyContent:'center', flexShrink:0 }));
const infoRow = { display:'flex', alignItems:'center', gap:'10px', fontSize:'14px', color:'var(--olo-color-text, #374151)' };
const whatsappStyle = { display:'flex', alignItems:'center', justifyContent:'center', gap:'8px', width:'100%', marginTop:'16px', padding:'12px', border:'none', background:'#25D366', color:'#fff', borderRadius:'8px', textAlign:'center', fontWeight:'600', fontSize:'14px', cursor:'pointer', fontFamily:'inherit' };
</script>

<style scoped>
.olo-pcta-wa:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, #25D366 45%, transparent);
}
</style>
