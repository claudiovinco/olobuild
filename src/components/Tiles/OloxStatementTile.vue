<template>
  <div class="oloxp" :style="{ '--c': accent }">
    <section class="dsec">
      <div v-if="s.variant === 'counter'" class="wrap" style="text-align:center;">
        <div class="k" style="justify-content:center;">{{ s.kicker }}</div>
        <div class="bigcounter"><span>{{ s.counter_to }}</span> <em>{{ s.counter_after }}</em></div>
        <p class="sub" style="margin:22px auto 0; text-align:center;" v-html="s.body_html"></p>
      </div>
      <div v-else-if="s.variant === 'zerozero'" class="wrap">
        <div class="zerozero">
          <span class="zz">{{ s.zz_text }}</span>
          <div style="max-width:52ch;">
            <div class="k">{{ s.kicker }}</div>
            <h2 class="d" style="font-size:clamp(26px,3vw,40px);" v-html="s.title_html"></h2>
            <p class="sub" style="margin-bottom:0;" v-html="s.body_html"></p>
          </div>
        </div>
      </div>
      <div v-else-if="s.variant === 'stamp'" class="wrap">
        <div class="noshow">
          <div class="k">{{ s.kicker }}</div>
          <h2 class="d" style="max-width:12ch;" v-html="s.title_html"></h2>
          <p class="sub" style="margin-bottom:0;" v-html="s.body_html"></p>
          <div class="bigstamp">{{ s.stamp_text }}</div>
        </div>
      </div>
      <div v-else class="wrap">
        <div class="head" style="margin-bottom:0; max-width:820px;">
          <div class="k">{{ s.kicker }}</div>
          <h2 class="d" v-html="s.title_html"></h2>
          <p style="margin-bottom:30px;" v-html="s.body_html"></p>
          <a v-if="s.cta_text" class="cta" @click.prevent>{{ s.cta_text }}</a>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import '../../../assets/css/olox.css';
import { oloxColor } from '@/config/elements/_oloxShared.js';
import def from '@/config/elements/oloxstatement.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => ({ ...def.defaults, ...props.settings }));
const accent = computed(() => oloxColor(s.value.accent));
</script>
