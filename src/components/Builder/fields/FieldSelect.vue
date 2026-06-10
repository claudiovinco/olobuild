<template>
  <!-- Segmented (auto): per select brevi i bottoni sono più rapidi del dropdown
       (1 click invece di 2, opzioni sempre visibili). Stesso modello dati del
       select: emette gli stessi value stringa — è SOLO una resa alternativa. -->
  <div v-if="isSegmented" class="fsel-seg" role="radiogroup">
    <button
      v-for="opt in options"
      :key="opt.value"
      type="button"
      role="radio"
      :aria-checked="isActive(opt)"
      class="fsel-seg-btn"
      :class="{ 'fsel-seg-btn--active': isActive(opt) }"
      :title="t(opt.label)"
      @click="$emit('update:modelValue', opt.value)"
    >{{ t(opt.label) }}</button>
  </div>

  <select
    v-else
    :value="modelValue"
    @change="$emit('update:modelValue', $event.target.value)"
    class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
  >
    <option v-for="opt in options" :key="opt.value" :value="opt.value">
      {{ t(opt.label) }}
    </option>
  </select>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';

const props = defineProps({
  modelValue: { type: [String, Number], default: '' },
  options: { type: Array, default: () => [] },
  // 'auto' (default): segmented se 2-4 opzioni con label corte; 'segmented' /
  // 'dropdown' forzano la resa. Dal config si passa con `ui: 'dropdown'`.
  ui: { type: String, default: 'auto' },
});

defineEmits(['update:modelValue']);

const SEG_MAX_OPTIONS = 4;
const SEG_MAX_LABEL = 11; // char: oltre, le label vanno strette e si troncano

const isSegmented = computed(() => {
  if (props.ui === 'dropdown') return false;
  if (props.ui === 'segmented') return true;
  const opts = props.options || [];
  if (opts.length < 2 || opts.length > SEG_MAX_OPTIONS) return false;
  return opts.every(o => String(t(o.label || '')).length <= SEG_MAX_LABEL);
});

function isActive(opt) {
  return String(opt.value) === String(props.modelValue);
}
</script>

<style scoped>
.fsel-seg {
  display: flex;
  width: 100%;
  background: #f3f4f6;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 2px;
  gap: 2px;
}
.fsel-seg-btn {
  flex: 1 1 0;
  min-width: 0;
  padding: 5px 4px;
  border: none;
  background: transparent;
  border-radius: 6px;
  font-size: 12px;
  line-height: 1.2;
  color: #6b7280;
  cursor: pointer;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  transition: background 0.12s, color 0.12s, box-shadow 0.12s;
}
.fsel-seg-btn:hover {
  color: #1f2937;
}
.fsel-seg-btn:focus-visible {
  outline: 2px solid var(--olo-ui-accent, #e8622a);
  outline-offset: -2px;
}
.fsel-seg-btn--active {
  background: #fff;
  color: #111827;
  font-weight: 600;
  box-shadow: 0 1px 2px rgba(16, 24, 40, 0.1), 0 0 0 1px rgba(16, 24, 40, 0.06);
}
</style>
