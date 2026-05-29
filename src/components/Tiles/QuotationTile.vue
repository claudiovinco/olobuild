<template>
  <div
    class="mb-py-5 mb-px-6"
    :style="{ textAlign: effectiveAlignment, minHeight: '60px' }"
  >
    <blockquote
      class="mb-m-0 mb-p-0"
      :style="{ borderLeft: effectiveAlignment === 'left' ? '4px solid var(--olo-color-primary, #e1474f)' : 'none', paddingLeft: effectiveAlignment === 'left' ? '16px' : '0' }"
    >
      <div
        class="mb-text-gray-200 mb-italic mb-leading-relaxed mb-text-lg"
        style="white-space:pre-wrap;"
        data-olo-editable="content"
        data-olo-multiline
      >{{ s.content }}</div>
      <div v-if="s.author" class="mb-mt-3">
        <template v-if="s.style === 'footer'">
          <footer class="mb-text-sm mb-text-gray-500">
            &mdash; <cite class="mb-not-italic" data-olo-editable="author">{{ s.author }}</cite>
          </footer>
        </template>
        <template v-else>
          <span class="mb-text-sm mb-text-gray-500">&mdash; <span data-olo-editable="author">{{ s.author }}</span></span>
        </template>
      </div>
    </blockquote>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useBuilderStore } from '@/stores/builder';
import { rv } from '@/composables/useResponsiveValue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const builderStore = useBuilderStore();

const defaults = {
  content: 'La vita \u00e8 quello che ti succede mentre sei impegnato a fare altri progetti.',
  author: 'John Lennon',
  style: 'default',
  alignment: 'left',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const effectiveAlignment = computed(() => rv(props.settings, 'alignment', s.value.alignment, builderStore.viewMode));
</script>
