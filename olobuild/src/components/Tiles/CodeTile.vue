<template>
  <div class="mb-rounded-lg mb-overflow-hidden" style="background: #0d1117; border: 1px solid #30363d;">
    <!-- Language badge -->
    <div v-if="settings.language" class="mb-flex mb-justify-between mb-items-center mb-px-4 mb-py-2" style="background: #161b22; border-bottom: 1px solid #30363d;">
      <span class="mb-text-xs mb-text-gray-500 mb-font-mono mb-uppercase">{{ settings.language }}</span>
    </div>

    <!-- Code block -->
    <pre class="mb-m-0 mb-p-4 mb-overflow-x-auto" style="background: transparent;"><code class="mb-text-sm mb-font-mono mb-text-gray-300 mb-leading-relaxed mb-whitespace-pre">{{ formattedCode }}</code></pre>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const formattedCode = computed(() => {
  const code = props.settings.code || 'console.log("Hello World");';
  if (!props.settings.show_line_numbers) return code;

  const lines = code.split('\n');
  const pad = String(lines.length).length;
  return lines.map((line, i) => `${String(i + 1).padStart(pad, ' ')} | ${line}`).join('\n');
});
</script>
