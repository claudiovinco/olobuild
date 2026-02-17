<template>
  <div
    class="mb-rounded-lg mb-overflow-hidden"
    :style="{ background: cardBg, border: '1px solid #374151', minHeight: '80px' }"
  >
    <!-- Card media top -->
    <div v-if="settings.image" class="mb-w-full" style="max-height: 180px; overflow: hidden;">
      <img :src="settings.image" alt="" class="mb-w-full mb-object-cover" style="height: 180px;" />
    </div>

    <!-- Card body -->
    <div class="mb-p-5">
      <component
        :is="settings.title_element || 'h3'"
        v-if="settings.title"
        class="mb-font-semibold mb-text-gray-100 mb-mb-2 mb-text-lg"
        v-html="settings.title"
      ></component>

      <div
        v-if="settings.meta"
        class="mb-text-xs mb-text-gray-500 mb-mb-3"
        v-text="settings.meta"
      ></div>

      <div
        class="mb-text-sm mb-text-gray-300 mb-leading-relaxed"
        v-html="settings.content || 'Panel content goes here.'"
      ></div>

      <div v-if="settings.link_url" class="mb-mt-4">
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

const styleBgs = {
  default:   '#1F2937',
  primary:   '#1E3A5F',
  secondary: '#1a1a2e',
  hover:     '#1F2937',
};

const cardBg = computed(() => styleBgs[props.settings.style] || styleBgs.default);
</script>
