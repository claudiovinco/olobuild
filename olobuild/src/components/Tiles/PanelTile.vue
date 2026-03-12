<template>
  <div
    class="mb-rounded-lg mb-overflow-hidden"
    :style="{ background: cardBg, border: '1px solid var(--olo-color-border, #E5E7EB)', minHeight: '80px' }"
  >
    <!-- Card media top -->
    <div v-if="s.image" class="mb-w-full" style="max-height: 180px; overflow: hidden;">
      <img :src="s.image" alt="" class="mb-w-full mb-object-cover" style="height: 180px;" />
    </div>

    <!-- Card body -->
    <div class="mb-p-5">
      <component
        :is="s.title_element || 'h3'"
        v-if="s.title"
        class="mb-font-semibold mb-text-gray-100 mb-mb-2 mb-text-lg"
      data-olo-editable="title">{{ s.title }}</component>

      <div
        v-if="s.meta"
        class="mb-text-xs mb-text-gray-500 mb-mb-3"
        data-olo-editable="meta"
        v-text="s.meta"
      ></div>

      <div
        class="mb-text-sm mb-text-gray-300 mb-leading-relaxed"
        data-olo-editable="content"
        data-olo-richtext
        data-olo-multiline
        v-html="contentHtml"
      ></div>

      <div v-if="s.link_url" class="mb-mt-4">
        <span class="mb-text-primary-400 mb-text-sm mb-font-medium">Read more &rarr;</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  style: 'default',
  title: '',
  meta: '',
  content: 'Panel content goes here.',
  image: '',
  link_url: '',
  title_element: 'h3',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const contentHtml = computed(() => s.value.content || '<span class="olo-editable-ph">Panel content goes here.</span>');

const styleBgs = {
  default:   'var(--olo-color-muted, #F3F4F6)',
  primary:   'var(--olo-color-muted, #F3F4F6)',
  secondary: 'var(--olo-color-muted, #F3F4F6)',
  hover:     'var(--olo-color-muted, #F3F4F6)',
};

const cardBg = computed(() => styleBgs[s.value.style] || styleBgs.default);
</script>
