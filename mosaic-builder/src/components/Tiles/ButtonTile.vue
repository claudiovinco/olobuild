<template>
  <div
    class="mb-py-4"
    :style="{ display: 'flex', justifyContent: alignMap[settings.alignment] || 'center' }"
  >
    <span
      :style="{
        display: 'inline-block',
        width: settings.full_width ? '100%' : 'auto',
        textAlign: 'center',
        padding: `${settings.padding_y || 14}px ${settings.padding_x || 32}px`,
        backgroundColor: settings.bg_color || '#6366F1',
        color: settings.text_color || '#FFFFFF',
        borderRadius: `${settings.border_radius || 6}px`,
        fontSize: `${settings.font_size || 16}px`,
        fontWeight: 600,
        cursor: 'pointer',
      }"
    >
      {{ settings.text || 'Click Here' }}
    </span>
  </div>
</template>

<script setup>
defineProps({
  settings: { type: Object, default: () => ({}) },
});

const alignMap = {
  left: 'flex-start',
  center: 'center',
  right: 'flex-end',
};
</script>
