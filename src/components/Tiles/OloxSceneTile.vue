<template>
  <!-- contain: twin del PHP — le scene non propagano larghezze intrinseche -->
  <div class="oloxp oloxp-home oloxp-scene" :style="{ '--c': oloxColor(s.color), position: 'relative', contain: 'inline-size' }">
    <OloxScenePreview :scene="s.scene" :s="s" />
    <div v-if="s.scene !== 'madlib' && s.coord" class="deco d-coord" style="position:static; margin-top:8px; text-align:center;">{{ s.coord }}</div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import '../../../assets/css/olox.css';
import { oloxColor } from '@/config/elements/_oloxShared.js';
import OloxScenePreview from './OloxScenePreview.vue';
import def from '@/config/elements/oloxscene.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => ({ ...def.defaults, ...props.settings }));
</script>
