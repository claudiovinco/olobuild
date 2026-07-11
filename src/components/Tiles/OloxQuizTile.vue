<template>
  <div class="oloxp" :style="{ '--c': accent }">
    <section class="dsec">
      <div class="wrap">
        <div class="head" style="margin-left:auto; margin-right:auto; text-align:center; max-width:640px;">
          <div v-if="s.kicker" class="k" style="justify-content:center;">{{ s.kicker }}</div>
          <h2 v-if="s.title_html" class="d" v-html="s.title_html"></h2>
        </div>
        <div class="quiz">
          <p class="q" v-html="s.question_html"></p>
          <div class="ans">
            <button v-for="(a, i) in answers" :key="i" type="button">{{ a.text }}</button>
          </div>
          <div class="verdict">{{ s.hint }}</div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import '../../../assets/css/olox.css';
import { oloxColor } from '@/config/elements/_oloxShared.js';
import def from '@/config/elements/oloxquiz.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => ({ ...def.defaults, ...props.settings }));
const accent = computed(() => oloxColor(s.value.accent));
const answers = computed(() => (Array.isArray(s.value.answers) ? s.value.answers : []));
</script>
