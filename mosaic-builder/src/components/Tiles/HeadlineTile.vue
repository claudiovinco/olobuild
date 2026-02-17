<template>
  <div class="mb-px-4 mb-py-6" :style="{ textAlign: settings.alignment || 'center' }">
    <!-- Line decoration -->
    <div v-if="settings.decoration === 'line'" class="mb-flex mb-items-center mb-gap-4 mb-mb-4" :style="{ justifyContent: alignJustify }">
      <span v-if="settings.alignment !== 'left'" class="mb-flex-1" style="max-width:80px;height:3px;border-radius:2px;" :style="{ background: settings.decoration_color }"></span>
      <component :is="settings.tag || 'h2'" class="mb-m-0 mb-font-bold mb-leading-tight" :style="headingStyle" v-html="settings.heading"></component>
      <span v-if="settings.alignment !== 'right'" class="mb-flex-1" style="max-width:80px;height:3px;border-radius:2px;" :style="{ background: settings.decoration_color }"></span>
    </div>

    <!-- Other decorations -->
    <template v-else>
      <div v-if="settings.decoration === 'dot'" class="mb-mb-3">
        <span class="mb-inline-block mb-rounded-full" style="width:10px;height:10px;" :style="{ background: settings.decoration_color }"></span>
      </div>
      <div v-if="settings.decoration === 'star'" class="mb-mb-2" style="font-size:1.5em;" :style="{ color: settings.decoration_color }">&#x2605;</div>
      <component :is="settings.tag || 'h2'" class="mb-m-0 mb-font-bold mb-leading-tight" :style="headingStyle" v-html="settings.heading"></component>
    </template>

    <!-- Subtitle -->
    <p v-if="settings.subtitle" class="mb-mt-3 mb-text-base mb-leading-relaxed" :style="{ color: settings.subtitle_color, margin: '12px 0 0' }" v-html="settings.subtitle"></p>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const sizeMap = { sm: '1.25em', md: '1.75em', lg: '2.25em', xl: '3em' };

const headingStyle = computed(() => ({
  color: props.settings.heading_color || '#F3F4F6',
  fontSize: sizeMap[props.settings.heading_size] || '2.25em',
}));

const alignJustify = computed(() => {
  const m = { left: 'flex-start', center: 'center', right: 'flex-end' };
  return m[props.settings.alignment] || 'center';
});
</script>
