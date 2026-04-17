<template>
  <div
    class="mb-rounded-lg mb-overflow-hidden mb-p-4"
    :style="{ background: 'var(--olo-color-background, #FFFFFF)', minHeight: '80px', border: '1px solid var(--olo-color-border, #E5E7EB)' }"
  >
    <!-- Trigger button preview -->
    <div v-if="s.show_trigger" class="mb-flex mb-items-center mb-gap-2 mb-mb-3">
      <div
        class="mb-px-3 mb-py-1.5 mb-rounded mb-text-sm mb-font-medium mb-flex mb-items-center mb-gap-1.5"
        :style="{ background: 'var(--olo-color-muted, #F3F4F6)', color: s.text_color || 'var(--olo-color-text, #374151)' }"
      >
        <span v-if="s.trigger_icon" class="mb-text-xs">&#9776;</span>
        <span>{{ s.trigger_text || 'Apri pannello' }}</span>
      </div>
    </div>

    <!-- Panel position indicator -->
    <div
      class="mb-relative mb-rounded mb-overflow-hidden"
      :style="{ background: 'var(--olo-color-muted, #F3F4F6)', height: '100px', border: '1px dashed var(--olo-color-border, #E5E7EB)' }"
    >
      <!-- Position indicator bar -->
      <div
        class="mb-absolute"
        :style="positionStyle"
      ></div>

      <!-- Label -->
      <div class="mb-absolute mb-inset-0 mb-flex mb-flex-col mb-items-center mb-justify-center mb-text-xs mb-text-gray-500">
        <span class="mb-font-semibold mb-text-gray-400">Off-Canvas</span>
        <span class="mb-mt-1">{{ positionLabel }} &middot; {{ s.transition }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  trigger_selector: '',
  position: 'right',
  transition: 'slide',
  width: '300',
  height: '300',
  overlay: true,
  overlay_color: '#000000',
  overlay_opacity: '50',
  close_button: true,
  close_color: '#ffffff',
  bg_color: 'var(--olo-color-muted, #F3F4F6)',
  text_color: 'var(--olo-color-text, #374151)',
  trigger_text: 'Apri pannello',
  trigger_icon: 'menu',
  show_trigger: true,
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const positionLabels = { left: 'Sinistra', right: 'Destra', top: 'Alto', bottom: 'Basso' };
const positionLabel = computed(() => positionLabels[s.value.position] || 'Destra');

const positionStyle = computed(() => {
  const bg = s.value.bg_color || 'var(--olo-color-muted, #F3F4F6)';
  const pos = s.value.position;
  const base = { background: bg, opacity: '0.8' };

  if (pos === 'left') return { ...base, top: '0', left: '0', width: '30%', height: '100%' };
  if (pos === 'right') return { ...base, top: '0', right: '0', width: '30%', height: '100%' };
  if (pos === 'top') return { ...base, top: '0', left: '0', width: '100%', height: '30%' };
  if (pos === 'bottom') return { ...base, bottom: '0', left: '0', width: '100%', height: '30%' };
  return { ...base, top: '0', right: '0', width: '30%', height: '100%' };
});
</script>
