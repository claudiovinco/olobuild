<template>
  <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
    <span v-for="badge in badges" :key="badge" :style="badgeStyle">{{ badge }}</span>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { style: 'pill' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const badges = ['50 persone', '85 mq', 'Sala Conferenze', 'Centro', 'Uso pubblico', '\u20AC 25/h'];
const badgeStyle = computed(() => s.value.style === 'pill'
  ? { background: '#eff6ff', color: '#1e40af', padding: '4px 12px', borderRadius: '20px', fontSize: '12px', fontWeight: '600', border: '1px solid #bfdbfe' }
  : { background: '#f3f4f6', color: '#374151', padding: '4px 10px', borderRadius: '4px', fontSize: '12px', fontWeight: '600' }
);
</script>
