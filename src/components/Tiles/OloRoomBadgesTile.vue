<template>
  <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
    <span v-for="badge in badges" :key="badge" :style="badgeStyle">{{ badge }}</span>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { TOKENS } from '@/composables/oloTileDefaults';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { style: 'pill' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const badges = ['50 persone', '85 mq', 'Sala Conferenze', 'Centro', 'Uso pubblico', '€ 25/h'];
// Pill = tinta soft del primario brand (era azzurro #eff6ff/#1e40af off-brand);
// flat = neutri di sistema.
const badgeStyle = computed(() => s.value.style === 'pill'
  ? { background: 'color-mix(in srgb, var(--olo-color-primary, #e1474f) 10%, #fff)', color: TOKENS.primary, padding: '4px 12px', borderRadius: '20px', fontSize: '12px', fontWeight: '600', border: '1px solid color-mix(in srgb, var(--olo-color-primary, #e1474f) 25%, #fff)' }
  : { background: TOKENS.surfaceAlt, color: TOKENS.text, padding: '4px 10px', borderRadius: '4px', fontSize: '12px', fontWeight: '600' }
);
</script>
