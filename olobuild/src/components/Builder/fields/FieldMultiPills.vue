<template>
  <div class="fmp-wrap">
    <button
      v-for="opt in options"
      :key="opt.value"
      type="button"
      :class="[
        'fmp-pill',
        selected.includes(opt.value) ? 'fmp-pill--active' : ''
      ]"
      @click="toggle(opt.value)"
    >
      <span v-if="opt.icon" class="fmp-pill-icon">{{ opt.icon }}</span>
      {{ opt.label }}
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  modelValue: { type: String, default: '' },
  options: { type: Array, default: () => [] },
});

const emit = defineEmits(['update:modelValue']);

const selected = computed(() => {
  if (!props.modelValue) return [];
  return props.modelValue.split(',').map(s => s.trim()).filter(Boolean);
});

function toggle(val) {
  const arr = [...selected.value];
  const idx = arr.indexOf(val);
  if (idx === -1) {
    arr.push(val);
  } else {
    arr.splice(idx, 1);
  }
  emit('update:modelValue', arr.join(','));
}
</script>

<style scoped>
.fmp-wrap {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
}

.fmp-pill {
  display: inline-flex;
  align-items: center;
  gap: 3px;
  padding: 3px 9px;
  font-size: 11px;
  font-weight: 500;
  border: 1px solid #4b5563;
  border-radius: 999px;
  background: transparent;
  color: #9ca3af;
  cursor: pointer;
  transition: all 0.15s;
  line-height: 1.4;
}

.fmp-pill:hover {
  border-color: #6b7280;
  color: #d1d5db;
}

.fmp-pill--active {
  background: rgb(var(--olo-primary-rgb, 99 102 241) / 0.2);
  border-color: var(--olo-color-primary, #6366F1);
  color: #c7d2fe;
}

.fmp-pill--active:hover {
  background: rgb(var(--olo-primary-rgb, 99 102 241) / 0.3);
}

.fmp-pill-icon {
  font-size: 12px;
  line-height: 1;
}
</style>
