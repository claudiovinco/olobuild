<template>
  <dl class="mb-m-0 mb-p-4" :style="dlStyle">
    <template v-for="(item, i) in parsedItems" :key="i">
      <!-- Inline layout -->
      <div v-if="s.layout === 'inline'" class="mb-flex mb-gap-3 mb-items-baseline" :style="separatorStyle(i)">
        <dt class="mb-font-semibold mb-text-sm mb-whitespace-nowrap" :style="{ color: s.term_color }" :data-olo-editable="'items.' + i + '.term'">{{ item.term }}</dt>
        <dd class="mb-m-0 mb-text-sm" :style="{ color: s.definition_color, whiteSpace: 'pre-wrap' }" :data-olo-editable="'items.' + i + '.definition'" data-olo-multiline>{{ item.definition }}</dd>
      </div>

      <!-- Grid layout -->
      <template v-else-if="s.layout === 'grid'">
        <dt class="mb-font-semibold mb-text-sm" :style="{ color: s.term_color, ...(s.separator && i > 0 ? { borderTop: '1px solid ' + s.border_color, paddingTop: '8px' } : {}) }" :data-olo-editable="'items.' + i + '.term'">{{ item.term }}</dt>
        <dd class="mb-m-0 mb-text-sm" :style="{ color: s.definition_color, whiteSpace: 'pre-wrap', ...(s.separator && i > 0 ? { borderTop: '1px solid ' + s.border_color, paddingTop: '8px' } : {}) }" :data-olo-editable="'items.' + i + '.definition'" data-olo-multiline>{{ item.definition }}</dd>
      </template>

      <!-- Stacked layout -->
      <div v-else :style="separatorStyle(i)">
        <dt class="mb-font-semibold mb-text-sm mb-mb-1" :style="{ color: s.term_color }" :data-olo-editable="'items.' + i + '.term'">{{ item.term }}</dt>
        <dd class="mb-m-0 mb-text-sm mb-leading-relaxed" :style="{ color: s.definition_color, whiteSpace: 'pre-wrap' }" :data-olo-editable="'items.' + i + '.definition'" data-olo-multiline>{{ item.definition }}</dd>
      </div>
    </template>
  </dl>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  layout: 'stacked',
  term_color: 'var(--olo-color-text, #374151)',
  definition_color: 'var(--olo-color-text, #374151)',
  separator: true,
  border_color: 'var(--olo-color-border, #E5E7EB)',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const parsedItems = computed(() => {
  const raw = s.value.items;
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
  if (s.value.layout === 'grid') {
    return { display: 'grid', gridTemplateColumns: 'auto 1fr', gap: '8px 24px', alignItems: 'baseline' };
  }
  return {};
});

function separatorStyle(index) {
  if (s.value.separator && index > 0) {
    return {
      borderTop: '1px solid ' + (s.value.border_color || 'var(--olo-color-border, #E5E7EB)'),
      paddingTop: '12px',
      marginTop: '12px',
    };
  }
  return {};
}
</script>
