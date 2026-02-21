<template>
  <dl class="mb-m-0 mb-p-4" :style="dlStyle">
    <template v-for="(item, i) in parsedItems" :key="i">
      <!-- Inline layout -->
      <div v-if="settings.layout === 'inline'" class="mb-flex mb-gap-3 mb-items-baseline" :style="separatorStyle(i)">
        <dt class="mb-font-semibold mb-text-sm mb-whitespace-nowrap" :style="{ color: settings.term_color }" v-html="item.term"></dt>
        <dd class="mb-m-0 mb-text-sm" :style="{ color: settings.definition_color }" v-html="item.definition"></dd>
      </div>

      <!-- Grid layout -->
      <template v-else-if="settings.layout === 'grid'">
        <dt class="mb-font-semibold mb-text-sm" :style="{ color: settings.term_color, ...(settings.separator && i > 0 ? { borderTop: '1px solid ' + settings.border_color, paddingTop: '8px' } : {}) }" v-html="item.term"></dt>
        <dd class="mb-m-0 mb-text-sm" :style="{ color: settings.definition_color, ...(settings.separator && i > 0 ? { borderTop: '1px solid ' + settings.border_color, paddingTop: '8px' } : {}) }" v-html="item.definition"></dd>
      </template>

      <!-- Stacked layout -->
      <div v-else :style="separatorStyle(i)">
        <dt class="mb-font-semibold mb-text-sm mb-mb-1" :style="{ color: settings.term_color }" v-html="item.term"></dt>
        <dd class="mb-m-0 mb-text-sm mb-leading-relaxed" :style="{ color: settings.definition_color }" v-html="item.definition"></dd>
      </div>
    </template>
  </dl>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const parsedItems = computed(() => {
  const raw = props.settings.items;
  // New format: array of objects
  if (Array.isArray(raw)) {
    return raw.filter(item => item && item.term);
  }
  // Legacy format: newline-separated "term|definition"
  if (typeof raw === 'string' && raw) {
    return raw.split('\n').map(l => l.trim()).filter(Boolean).map(line => {
      const parts = line.split('|');
      if (parts.length >= 2) return { term: parts[0].trim(), definition: parts.slice(1).join('|').trim() };
      return null;
    }).filter(Boolean);
  }
  return [];
});

const dlStyle = computed(() => {
  if (props.settings.layout === 'grid') {
    return { display: 'grid', gridTemplateColumns: 'auto 1fr', gap: '8px 24px', alignItems: 'baseline' };
  }
  return {};
});

function separatorStyle(index) {
  if (props.settings.separator && index > 0) {
    return {
      borderTop: '1px solid ' + (props.settings.border_color || '#374151'),
      paddingTop: '12px',
      marginTop: '12px',
    };
  }
  return {};
}
</script>
