<template>
  <div class="oloxp" :style="{ '--c': accent, '--marq-dur': (s.duration || 28) + 's' }">
    <div class="dmarq" :class="{ rev: s.reverse }"><span class="in">
      <template v-for="n in 2">
        <template v-for="(it, i) in items" :key="n + '-' + i">{{ it }} <b>{{ s.sep }}</b>&nbsp;</template>
      </template>
    </span></div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import '../../../assets/css/olox.css';
import { oloxColor } from '@/config/elements/_oloxShared.js';
import def from '@/config/elements/oloxmarquee.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => ({ ...def.defaults, ...props.settings }));
const accent = computed(() => oloxColor(s.value.accent));
const items = computed(() => (Array.isArray(s.value.items) ? s.value.items : []).map(i => i.text).filter(Boolean));
</script>
