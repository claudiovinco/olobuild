<template>
  <div
    class="mb-rounded-lg mb-px-5 mb-py-4"
    :style="{ background: typeStyle.bg, borderLeft: `4px solid ${typeStyle.border}`, minHeight: '60px' }"
  >
    <div class="mb-flex mb-items-start mb-gap-3">
      <span v-if="settings.show_icon !== false" class="mb-text-xl mb-flex-shrink-0">{{ typeStyle.icon }}</span>
      <div>
        <div v-if="settings.title" class="mb-font-semibold mb-text-gray-100 mb-mb-1" v-html="settings.title"></div>
        <div class="mb-text-sm mb-text-gray-200 mb-opacity-90 mb-leading-relaxed" v-html="settings.message || 'Alert message here.'"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const typeStyles = {
  info:    { bg: '#1E3A5F', border: '#3B82F6', icon: '\u2139\uFE0F' },
  success: { bg: '#14532D', border: '#22C55E', icon: '\u2705' },
  warning: { bg: '#713F12', border: '#EAB308', icon: '\u26A0\uFE0F' },
  error:   { bg: '#7F1D1D', border: '#EF4444', icon: '\u274C' },
};

const typeStyle = computed(() => typeStyles[props.settings.alert_type] || typeStyles.info);
</script>
