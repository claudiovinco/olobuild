<template>
  <div
    class="olo-viewscounter mb-py-3 mb-px-4"
    :style="{
      display: 'flex',
      flexDirection: s.layout === 'block' ? 'column' : 'row',
      alignItems: s.layout === 'block' ? 'center' : 'center',
      gap: s.layout === 'block' ? '4px' : '6px',
      fontSize: s.font_size + 'px',
      fontWeight: s.font_weight,
      color: s.text_color || 'var(--olo-color-text-soft, #6b7280)',
    }"
  >
    <!-- Icon before -->
    <span
      v-if="s.show_icon && s.icon_position === 'before'"
      class="olo-vc-icon"
      :style="{ color: s.icon_color || s.text_color || 'var(--olo-color-text-soft, #6b7280)' }"
      v-html="eyeSvg"
    ></span>

    <!-- Number + Label -->
    <span class="olo-vc-text">
      <strong>{{ formattedNumber }}</strong>
      <span v-if="s.show_label && s.label" style="margin-left: 4px;" data-olo-editable="label">{{ s.label }}</span>
    </span>

    <!-- Icon after -->
    <span
      v-if="s.show_icon && s.icon_position === 'after'"
      class="olo-vc-icon"
      :style="{ color: s.icon_color || s.text_color || 'var(--olo-color-text-soft, #6b7280)' }"
      v-html="eyeSvg"
    ></span>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

const defaults = {
  show_icon: true,
  icon_position: 'before',
  label: 'visualizzazioni',
  show_label: true,
  text_color: '',
  icon_color: '',
  font_size: '14',
  font_weight: '400',
  layout: 'inline',
  icon_size: '16',
  number_format: true,
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const eyeSvg = computed(() => {
  const sz = parseInt(s.value.icon_size) || 16;
  return `<svg xmlns="http://www.w3.org/2000/svg" width="${sz}" height="${sz}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`;
});

const formattedNumber = computed(() => {
  const n = 1234;
  if (s.value.number_format) {
    return n.toLocaleString('it-IT');
  }
  return String(n);
});
</script>

<style scoped>
.olo-vc-icon {
  display: inline-flex;
  align-items: center;
  flex-shrink: 0;
}
.olo-vc-text {
  display: inline-flex;
  align-items: baseline;
  gap: 0;
}
</style>
