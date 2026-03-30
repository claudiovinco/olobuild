<template>
  <div style="padding:10px;background:#f9fafb;border-radius:8px;min-height:60px;display:flex;align-items:center;justify-content:center;">
    <button :style="btnStyle">
      <svg style="width:16px;height:16px;margin-right:6px;flex-shrink:0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
        <circle cx="12" cy="12" r="3"/>
      </svg>
      <span data-olo-editable="button_text">{{ s.button_text || 'Anteprima rapida' }}</span>
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  button_text: 'Anteprima rapida',
  button_bg: '#6366F1',
  button_color: '#FFFFFF',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const btnStyle = computed(() => ({
  display: 'inline-flex',
  alignItems: 'center',
  padding: '10px 20px',
  background: s.value.button_bg || '#6366F1',
  color: s.value.button_color || '#fff',
  border: 'none',
  borderRadius: '6px',
  fontSize: '13px',
  fontWeight: 600,
  cursor: 'pointer',
}));
</script>
