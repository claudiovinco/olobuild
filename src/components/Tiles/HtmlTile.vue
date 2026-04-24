<template>
  <div class="olo-html-tile">
    <!-- Empty state -->
    <div v-if="!s.html_content" class="mb-border-2 mb-border-dashed mb-border-gray-600 mb-rounded-lg mb-p-8 mb-text-center mb-text-gray-500">
      <div class="mb-text-3xl mb-mb-2">{{ t('&lt;/&gt;') }}</div>
      <div class="mb-text-sm">{{ t('Custom HTML Block') }}</div>
    </div>

    <!-- Sandbox mode (iframe) -->
    <iframe
      v-else-if="s.sandbox"
      :srcdoc="s.html_content"
      sandbox="allow-scripts"
      class="mb-w-full mb-border-0"
      style="min-height:100px;"
    ></iframe>

    <!-- Direct HTML -->
    <div v-else v-html="s.html_content"></div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  html_content: '',
  sandbox: false,
};
const s = computed(() => ({ ...defaults, ...props.settings }));
</script>
