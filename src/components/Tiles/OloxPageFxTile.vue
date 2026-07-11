<template>
  <div class="oloxp">
    <div v-if="s.variant === 'pano'" class="ox-panobg" style="position:relative; height:180px; z-index:0;">
      <div class="strip"></div>
      <div class="degbox"><b class="ox-deg">128°</b>{{ s.deg_label }}</div>
    </div>
    <div v-else-if="s.variant === 'xp'" class="xpfix">
      <span>{{ s.xp_label }}</span>
      <div class="bar"><i style="width:35%"></i></div>
      <b><span class="ox-xpval">190</span> XP</b>
    </div>
    <div v-else class="ox-scan" style="position:relative;"></div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import '../../../assets/css/olox.css';
import def from '@/config/elements/oloxpagefx.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => ({ ...def.defaults, ...props.settings }));
</script>
