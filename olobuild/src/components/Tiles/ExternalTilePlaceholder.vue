<template>
  <div class="olo-external-tile-placeholder" :style="wrapStyle">
    <div class="olo-external-tile-icon">{{ icon }}</div>
    <div class="olo-external-tile-label">{{ label }}</div>
    <div v-if="hint" class="olo-external-tile-hint">{{ hint }}</div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { getElementDef } from '@/config/elementRegistry.js';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

const tileDef = computed(() => {
  // Try to find the element definition from external elements
  const type = props.settings?._type || '';
  return getElementDef(type);
});

const icon = computed(() => tileDef.value?.icon || '🔌');
const label = computed(() => tileDef.value?.name || 'Elemento esterno');
const hint = computed(() => tileDef.value?.placeholder || '');

const wrapStyle = computed(() => ({
  display: 'flex',
  flexDirection: 'column',
  alignItems: 'center',
  justifyContent: 'center',
  padding: '24px',
  background: '#f0f4f8',
  border: '2px dashed #94a3b8',
  borderRadius: '8px',
  color: '#475569',
  textAlign: 'center',
  minHeight: '80px',
}));
</script>
