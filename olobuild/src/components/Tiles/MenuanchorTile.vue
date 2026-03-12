<template>
  <div
    class="olo-menuanchor mb-py-2 mb-px-3"
    :style="{
      border: '1px dashed #9CA3AF',
      borderRadius: '4px',
      backgroundColor: '#F9FAFB',
      display: 'flex',
      alignItems: 'center',
      gap: '6px',
      fontSize: '12px',
      color: '#6B7280',
      minHeight: '28px',
    }"
  >
    <span style="font-size: 14px;">&#9875;</span>
    <span style="font-family: monospace; font-weight: 500;">
      #{{ s.anchor_id || 'anchor-id' }}
    </span>
    <span v-if="s.label" style="margin-left: 4px; opacity: 0.7;">
      &mdash; {{ s.label }}
    </span>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

const defaults = {
  anchor_id: '',
  offset: '0',
  label: '',
};

const s = computed(() => ({ ...defaults, ...props.settings }));
</script>
