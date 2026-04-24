<template>
  <div class="olo-shortcode-tile">
    <!-- Empty state -->
    <div
      v-if="!s.shortcode_text"
      class="mb-border-2 mb-border-dashed mb-border-gray-600 mb-rounded-lg mb-p-8 mb-text-center mb-text-gray-500"
    >
      <div class="mb-text-3xl mb-mb-2">[/]</div>
      <div class="mb-text-sm">{{ t('Inserisci uno shortcode') }}</div>
    </div>

    <!-- Shortcode preview -->
    <div v-else :style="codeBlockStyle">
      <div style="font-size:10px;text-transform:uppercase;letter-spacing:0.5px;color:#9ca3af;margin-bottom:6px;">
        {{ t('Shortcode') }}
      </div>
      <code :style="codeStyle">{{ s.shortcode_text }}</code>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  shortcode_text: '[gallery]',
  parse_shortcodes: true,
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const codeBlockStyle = computed(() => ({
  border: '2px dashed var(--olo-color-border, #E5E7EB)',
  borderRadius: '8px',
  padding: '16px',
  background: 'var(--olo-color-muted, #F3F4F6)',
}));

const codeStyle = computed(() => ({
  fontFamily: "'JetBrains Mono', 'Fira Code', 'Consolas', monospace",
  fontSize: '14px',
  color: 'var(--olo-color-primary, #6366F1)',
  wordBreak: 'break-all',
  whiteSpace: 'pre-wrap',
}));
</script>
