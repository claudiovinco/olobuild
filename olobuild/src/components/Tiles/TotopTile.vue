<template>
  <div
    class="mb-py-4"
    :style="{ textAlign: settings.alignment || 'right' }"
  >
    <span
      class="mb-inline-flex mb-items-center mb-justify-center mb-rounded-full"
      :style="{
        width: '36px',
        height: '36px',
        border: settings.style === 'primary' ? 'none' : '1px solid #6b7280',
        background: settings.style === 'primary' ? '#6366F1' : 'transparent',
        color: settings.style === 'primary' ? '#fff' : '#9CA3AF',
        fontSize: '16px',
        cursor: 'pointer',
      }"
    >&#9650;</span>
  </div>
</template>

<script setup>
defineProps({
  settings: { type: Object, default: () => ({}) },
});
</script>
