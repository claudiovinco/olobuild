<template>
  <div class="olo-ts">
    <div class="olo-ts-grid">
      <div class="olo-ts-cell">
        <span class="olo-ts-lbl">X</span>
        <NumberScrubber
          :modelValue="value.h" :min="-50" :max="50" :step="1"
          :defaultValue="0" emitAs="number" unit="px" :ariaLabel="t('Offset orizzontale')"
          @update:modelValue="onUpdate('h', $event)"
        />
      </div>
      <div class="olo-ts-cell">
        <span class="olo-ts-lbl">Y</span>
        <NumberScrubber
          :modelValue="value.v" :min="-50" :max="50" :step="1"
          :defaultValue="0" emitAs="number" unit="px" :ariaLabel="t('Offset verticale')"
          @update:modelValue="onUpdate('v', $event)"
        />
      </div>
      <div class="olo-ts-cell">
        <span class="olo-ts-lbl">{{ t('Sfocatura') }}</span>
        <NumberScrubber
          :modelValue="value.blur" :min="0" :max="50" :step="1"
          :defaultValue="0" emitAs="number" unit="px" :ariaLabel="t('Sfocatura')"
          @update:modelValue="onUpdate('blur', Math.max(0, $event))"
        />
      </div>
    </div>
    <div class="olo-ts-color">
      <span class="olo-ts-lbl">{{ t('Colore') }}</span>
      <div class="olo-ts-colorfield">
        <FieldColor
          :modelValue="value.color || '#000000'"
          @update:modelValue="onUpdate('color', $event)"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import FieldColor from './FieldColor.vue';
import NumberScrubber from './NumberScrubber.vue';

/**
 * FieldTextShadow — UI per la 4-tupla (h, v, blur, color) di text-shadow.
 * Contratto INVARIATO: emette oggetto { h, v, blur, color } con h/v/blur interi
 * (blur >= 0) e color stringa; il caller mappa alle 4 chiavi piatte backend
 * tile.style.text_shadow_h/_v/_blur/_color. Cambia solo la UI: NumberScrubber
 * compatto con slider a comparsa + rotellina.
 */
const props = defineProps({
  modelValue: { type: [Object, null], default: () => ({}) },
});
const emit = defineEmits(['update:modelValue']);

const value = computed(() => ({
  h:     props.modelValue?.h     ?? 0,
  v:     props.modelValue?.v     ?? 0,
  blur:  props.modelValue?.blur  ?? 0,
  color: props.modelValue?.color ?? '',
}));

function onUpdate(key, val) {
  emit('update:modelValue', { ...value.value, [key]: val });
}
</script>

<style scoped>
.olo-ts { display: flex; flex-direction: column; gap: 12px; padding: 2px 0; }
.olo-ts-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
.olo-ts-cell { display: flex; flex-direction: column; align-items: center; gap: 4px; }
.olo-ts-lbl { font-size: 10px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: .04em; }
.olo-ts-cell :deep(.olo-ns) { width: 100%; }
.olo-ts-cell :deep(.olo-ns-box) { width: 100%; justify-content: center; }
.olo-ts-color { display: flex; align-items: center; gap: 10px; }
.olo-ts-color .olo-ts-lbl { flex-shrink: 0; }
.olo-ts-colorfield { flex: 1; min-width: 0; }
</style>
