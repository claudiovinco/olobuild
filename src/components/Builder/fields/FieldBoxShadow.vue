<template>
  <div class="olo-shadowfield">
    <div class="olo-sh-row">
      <span class="olo-sh-rl">{{ t('Offset X') }}</span>
      <NumberScrubber class="olo-sh-ctrl" :modelValue="val.h" :min="-50" :max="50" :step="1"
        :defaultValue="0" emitAs="number" unit="px" :sliderOnFocus="false"
        :ariaLabel="t('Offset orizzontale ombra (px)')" @update:modelValue="update('h', $event)" />
    </div>
    <div class="olo-sh-row">
      <span class="olo-sh-rl">{{ t('Offset Y') }}</span>
      <NumberScrubber class="olo-sh-ctrl" :modelValue="val.v" :min="-50" :max="50" :step="1"
        :defaultValue="4" emitAs="number" unit="px" :sliderOnFocus="false"
        :ariaLabel="t('Offset verticale ombra (px)')" @update:modelValue="update('v', $event)" />
    </div>
    <div class="olo-sh-row">
      <span class="olo-sh-rl">{{ t('Sfocatura') }}</span>
      <NumberScrubber class="olo-sh-ctrl" :modelValue="val.blur" :min="0" :max="100" :step="1"
        :defaultValue="10" emitAs="number" unit="px" :sliderOnFocus="false"
        :ariaLabel="t('Sfocatura ombra (px)')" @update:modelValue="update('blur', $event)" />
    </div>
    <div class="olo-sh-row">
      <span class="olo-sh-rl">{{ t('Estensione') }}</span>
      <NumberScrubber class="olo-sh-ctrl" :modelValue="val.spread" :min="-30" :max="50" :step="1"
        :defaultValue="0" emitAs="number" unit="px" :sliderOnFocus="false"
        :ariaLabel="t('Diffusione ombra (px)')" @update:modelValue="update('spread', $event)" />
    </div>
    <div class="olo-sh-row olo-sh-row--col">
      <span class="olo-sh-rl">{{ t('Colore') }}</span>
      <div class="olo-sh-color">
        <FieldColor :modelValue="val.color || 'rgba(0,0,0,0.15)'" @update:modelValue="update('color', $event)" />
      </div>
    </div>
    <div class="olo-sh-row olo-sh-row--last">
      <span class="olo-sh-rl">{{ t('Inset') }}</span>
      <button type="button" role="switch" :aria-checked="String(!!val.inset)"
              :class="['olo-sh-switch', { on: val.inset }]"
              :aria-label="t('Ombra interna (inset)')"
              @click="update('inset', !val.inset)"></button>
    </div>
    <div class="olo-sh-preview" aria-hidden="true">{{ previewText }}</div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
import FieldColor from './FieldColor.vue';
import NumberScrubber from './NumberScrubber.vue';

const props = defineProps({
  modelValue: { type: Object, default: () => ({ h: 0, v: 4, blur: 10, spread: 0, color: 'rgba(0,0,0,0.15)', inset: false }) },
});
const emit = defineEmits(['update:modelValue']);

const val = computed(() => ({
  h: props.modelValue?.h ?? 0,
  v: props.modelValue?.v ?? 4,
  blur: props.modelValue?.blur ?? 10,
  spread: props.modelValue?.spread ?? 0,
  color: props.modelValue?.color ?? 'rgba(0,0,0,0.15)',
  inset: props.modelValue?.inset ?? false,
}));

const previewText = computed(() => {
  const v = val.value;
  return `${v.inset ? 'inset ' : ''}${v.h}px ${v.v}px ${v.blur}px ${v.spread}px ${v.color}`;
});

function update(key, value) {
  const numKeys = ['h', 'v', 'blur', 'spread'];
  let v = value;
  if (numKeys.includes(key)) {
    v = parseInt(value) || 0;
    if (key === 'blur') v = Math.max(0, v); // sfocatura non negativa (coerente con FieldTextShadow)
  }
  const newVal = { ...val.value, [key]: v };
  emit('update:modelValue', newVal);
}
</script>

<style scoped>
.olo-shadowfield { display: flex; flex-direction: column; gap: 10px; padding: 2px 0; }
.olo-sh-row { display: flex; align-items: center; gap: 10px; }
.olo-sh-row--last { margin-bottom: 0; }
.olo-sh-row--col { align-items: flex-start; }
.olo-sh-rl { width: 64px; flex-shrink: 0; font-size: 9px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: #94a3b8; }
.olo-sh-row--col .olo-sh-rl { padding-top: 9px; }
.olo-sh-ctrl { flex: 1; min-width: 0; }
.olo-sh-color { flex: 1; min-width: 0; }
.olo-sh-switch { width: 34px; height: 19px; border: 0; border-radius: 99px; background: #cbd5e1; position: relative; cursor: pointer; transition: background .15s; flex-shrink: 0; }
.olo-sh-switch::after { content: ""; position: absolute; top: 2px; left: 2px; width: 15px; height: 15px; border-radius: 50%; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,.2); transition: left .15s; }
.olo-sh-switch.on { background: var(--olo-ui-accent, #e8622a); }
.olo-sh-switch.on::after { left: 17px; }
.olo-sh-switch:focus-visible { outline: 2px solid var(--olo-ui-accent, #e8622a); outline-offset: 2px; }
.olo-sh-preview { font: 500 10px ui-monospace, monospace; color: #6b7280; background: #f6f7f9; border-radius: 7px; padding: 7px 9px; word-break: break-all; }
</style>
