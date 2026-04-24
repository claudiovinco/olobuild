<template>
  <div class="mb-rounded-lg mb-overflow-hidden" :style="{ background: theme.bg, border: '1px solid ' + theme.line }">
    <!-- Header: language badge + copy button -->
    <div
      v-if="s.language || s.show_copy_button"
      class="mb-flex mb-justify-between mb-items-center mb-px-4 mb-py-2"
      :style="{ background: theme.header, borderBottom: '1px solid ' + theme.line }"
    >
      <span class="mb-text-xs mb-font-mono mb-uppercase" :style="{ color: theme.line }">{{ s.language }}</span>
      <span
        v-if="s.show_copy_button"
        class="mb-text-xs mb-font-mono mb-cursor-pointer mb-select-none mb-px-2 mb-py-1 mb-rounded"
        :style="{ color: theme.text, opacity: 0.7, border: '1px solid ' + theme.line }"
        :title="t('Copia codice')"
      >{{ t('Copy') }}</span>
    </div>

    <!-- Code block -->
    <pre
      class="mb-m-0 mb-p-4 mb-overflow-x-auto"
      :style="codeBlockStyle"
    ><code class="mb-font-mono mb-leading-relaxed" :style="{ color: theme.text, fontSize: rv(settings, 'font_size', s.font_size, builderStore.viewMode) + 'px', whiteSpace: s.wrap_lines ? 'pre-wrap' : 'pre' }">{{ formattedCode }}</code></pre>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
import { useBuilderStore } from '@/stores/builder';
import { rv } from '@/composables/useResponsiveValue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const builderStore = useBuilderStore();

const themes = {
  'github-dark':    { bg: '#0d1117', text: '#c9d1d9', line: '#484f58', header: '#161b22' },
  'monokai':        { bg: '#272822', text: '#f8f8f2', line: '#75715e', header: '#1e1f1c' },
  'dracula':        { bg: '#282a36', text: '#f8f8f2', line: '#6272a4', header: '#21222c' },
  'one-dark':       { bg: '#282c34', text: '#abb2bf', line: '#636d83', header: '#21252b' },
  'solarized-dark': { bg: '#002b36', text: '#839496', line: '#586e75', header: '#073642' },
  'light':          { bg: '#ffffff', text: '#24292e', line: '#babbbd', header: '#f6f8fa' },
};

const defaults = {
  code: 'console.log("Hello World");',
  language: 'javascript',
  show_line_numbers: false,
  theme: 'github-dark',
  show_copy_button: true,
  font_size: '14',
  max_height: '',
  wrap_lines: false,
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const theme = computed(() => themes[s.value.theme] || themes['github-dark']);

const codeBlockStyle = computed(() => {
  const style = { background: 'transparent' };
  if (s.value.max_height) {
    style.maxHeight = s.value.max_height + 'px';
    style.overflowY = 'auto';
  }
  return style;
});

const formattedCode = computed(() => {
  const code = s.value.code;
  if (!s.value.show_line_numbers) return code;

  const lines = code.split('\n');
  const pad = String(lines.length).length;
  return lines.map((line, i) => `${String(i + 1).padStart(pad, ' ')} | ${line}`).join('\n');
});
</script>
