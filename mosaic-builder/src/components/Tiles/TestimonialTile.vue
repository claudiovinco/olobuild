<template>
  <div
    class="mb-rounded-xl mb-p-8"
    :style="{ background: settings.bg_color || '#1F2937', color: settings.text_color || '#F3F4F6', minHeight: '80px' }"
  >
    <div v-if="rating > 0" class="mb-mb-3 mb-text-xl">
      <span v-for="i in rating" :key="i">&#11088;</span>
    </div>
    <blockquote class="mb-text-lg mb-italic mb-leading-relaxed mb-mb-5 mb-opacity-95">
      <span v-html="settings.quote || 'Aggiungi qui la tua citazione.'"></span>
    </blockquote>
    <div class="mb-flex mb-items-center mb-gap-3">
      <img
        v-if="settings.avatar"
        :src="settings.avatar"
        alt=""
        class="mb-w-12 mb-h-12 mb-rounded-full mb-object-cover"
      />
      <div>
        <div class="mb-font-semibold" v-html="settings.author_name || 'Nome Autore'"></div>
        <div v-if="settings.author_role" class="mb-text-sm mb-opacity-70" v-html="settings.author_role"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const rating = computed(() => parseInt(props.settings.rating) || 0);
</script>
