<template>
  <div
    class="mb-py-5 mb-px-6"
    :style="{ textAlign: settings.alignment || 'left', minHeight: '60px' }"
  >
    <blockquote
      class="mb-m-0 mb-p-0"
      :style="{ borderLeft: settings.alignment === 'left' || !settings.alignment ? '4px solid #6366F1' : 'none', paddingLeft: settings.alignment === 'left' || !settings.alignment ? '16px' : '0' }"
    >
      <div
        class="mb-text-gray-200 mb-italic mb-leading-relaxed mb-text-lg"
        v-html="settings.content || 'Quote text here.'"
      ></div>
      <div v-if="settings.author" class="mb-mt-3">
        <template v-if="settings.style === 'footer'">
          <footer class="mb-text-sm mb-text-gray-500">
            &mdash; <cite class="mb-not-italic">{{ settings.author }}</cite>
          </footer>
        </template>
        <template v-else>
          <span class="mb-text-sm mb-text-gray-500">&mdash; {{ settings.author }}</span>
        </template>
      </div>
    </blockquote>
  </div>
</template>

<script setup>
defineProps({
  settings: { type: Object, default: () => ({}) },
});
</script>
