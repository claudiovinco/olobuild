<template>
  <div class="oloxp" :style="{ '--c': accent }">
    <section class="dsec">
      <div class="wrap">
        <div class="head" style="margin-left:auto; margin-right:auto; text-align:center; max-width:640px;">
          <div v-if="s.kicker" class="k" style="justify-content:center;">{{ s.kicker }}</div>
          <h2 v-if="s.title_html" class="d" v-html="s.title_html"></h2>
        </div>
        <div class="path">
          <div v-for="(it, i) in items" :key="i" class="lez unlocked">
            <span class="node">{{ i + 1 }}</span>
            <div class="box">
              <span class="xp">{{ it.xp }}</span>
              <h3 v-html="it.title"></h3>
              <p v-html="it.text_html"></p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import '../../../assets/css/olox.css';
import { oloxColor } from '@/config/elements/_oloxShared.js';
import def from '@/config/elements/oloxlessons.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => ({ ...def.defaults, ...props.settings }));
const accent = computed(() => oloxColor(s.value.accent));
const items = computed(() => (Array.isArray(s.value.items) ? s.value.items : []));
</script>
