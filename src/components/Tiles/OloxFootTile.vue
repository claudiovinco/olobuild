<template>
  <div class="oloxp" :style="{ '--c': accent }">
    <footer class="dfoot">
      <div class="wrap">
        <img v-if="s.logo" :src="s.logo" alt="OLOtheme" />
        <div class="lnk">
          <a v-for="(l, i) in s.links" :key="i" @click.prevent>{{ l.label }}</a>
        </div>
        <div class="fine">{{ s.fine }}</div>
      </div>
    </footer>
    <div v-if="s.show_credits" class="credits" v-html="s.credits_html"></div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import '../../../assets/css/olox.css';
import { oloxColor } from '@/config/elements/_oloxShared.js';
import def from '@/config/elements/oloxfoot.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => ({ ...def.defaults, ...props.settings }));
const accent = computed(() => oloxColor(s.value.accent));
</script>
