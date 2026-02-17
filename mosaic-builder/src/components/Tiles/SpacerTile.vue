<template>
  <div
    class="mb-flex mb-items-center mb-justify-center mb-relative"
    :style="{ height: `${settings.height || 60}px` }"
  >
    <!-- Visual indicator in builder -->
    <div class="mb-absolute mb-inset-x-0 mb-top-0 mb-h-full mb-border mb-border-dashed mb-border-gray-700 mb-rounded mb-opacity-50"></div>
    <hr
      v-if="settings.show_divider"
      class="mb-border-0 mb-m-0"
      :style="{
        width: `${settings.divider_width || 100}%`,
        borderTop: `${settings.divider_thickness || 1}px solid ${settings.divider_color || '#374151'}`,
      }"
    />
    <span v-else class="mb-text-xs mb-text-gray-600 mb-bg-gray-900 mb-px-2 mb-relative mb-z-10">
      SPACER {{ settings.height || 60 }}px
    </span>
  </div>
</template>

<script setup>
defineProps({
  settings: { type: Object, default: () => ({}) },
});
</script>
