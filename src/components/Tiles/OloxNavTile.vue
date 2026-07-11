<template>
  <div class="oloxp" :style="{ '--c': accent }">
    <nav class="dnav">
      <div class="row">
        <a class="logo" @click.prevent><img v-if="s.logo" :src="s.logo" alt="OLOtheme" /></a>
        <div class="links">
          <a v-for="(l, i) in s.links" :key="i" :class="{ on: l.active }"
            :style="{ '--pc': oloxColor(l.color) }" @click.prevent>{{ l.label }}</a>
        </div>
        <div v-if="s.show_lang" class="langsw"><div class="lsw-list"><a class="on" @click.prevent>IT</a></div></div>
        <a v-if="s.exp_text" class="exp" @click.prevent>{{ s.exp_text }}</a>
      </div>
    </nav>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import '../../../assets/css/olox.css';
import { oloxColor } from '@/config/elements/_oloxShared.js';
import def from '@/config/elements/oloxnav.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => ({ ...def.defaults, ...props.settings }));
const accent = computed(() => oloxColor(s.value.accent));
</script>
