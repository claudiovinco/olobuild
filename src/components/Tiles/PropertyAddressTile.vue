<template>
  <div class="mb-font-sans">
    <div :style="{ display:'flex', alignItems:'flex-start', gap:'10px', marginBottom: s.show_map ? '16px' : '0' }">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" :stroke="accent" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
      <div>
        <div :style="{ fontSize:'15px', fontWeight:'600', color:'var(--olo-color-text, #1f2937)', marginBottom:'2px' }">{{ t('Via Nazionale 45') }}</div>
        <div :style="{ fontSize:'14px', color:'var(--olo-color-text-muted, #6b7280)' }">{{ t('Mattarello, Trento (TN) 38123') }}</div>
      </div>
    </div>
    <div v-if="s.show_map" :style="mapStyle">
      <div :style="{ position:'absolute', inset:0, display:'flex', alignItems:'center', justifyContent:'center' }">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--olo-color-text-faint, #94a3b8)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
      </div>
      <div :style="{ position:'absolute', inset:0, opacity:0.06, backgroundImage:'linear-gradient(var(--olo-color-text, #374151) 1px, transparent 1px), linear-gradient(90deg, var(--olo-color-text, #374151) 1px, transparent 1px)', backgroundSize:'40px 40px' }"></div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { show_map: true, map_height: 200, border_radius: 10, map_bg: '', accent_color: '' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const accent = computed(() => s.value.accent_color || 'var(--olo-color-primary, #e1474f)');
const mapStyle = computed(() => ({ position:'relative', height: s.value.map_height+'px', background: s.value.map_bg || 'var(--olo-color-surface-alt, #f6f7f9)', borderRadius: s.value.border_radius+'px', overflow:'hidden' }));
</script>
