<template>
  <div class="oloxp" :style="{ '--c': accent, '--nc': accent }">
    <section v-if="s.variant === 'follow'" class="follow">
      <div class="wrap">
        <span class="fk">{{ s.fk_text }}</span>
        <p v-html="s.body_html"></p>
      </div>
    </section>
    <div v-else class="dnext">
      <div class="wrap">
        <span class="lbl">{{ s.label }}</span>
        <a class="nx" @click.prevent v-html="s.link_html"></a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import '../../../assets/css/olox.css';
import { oloxColor } from '@/config/elements/_oloxShared.js';
import def from '@/config/elements/oloxbanner.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => ({ ...def.defaults, ...props.settings }));
const accent = computed(() => oloxColor(s.value.accent));
</script>
