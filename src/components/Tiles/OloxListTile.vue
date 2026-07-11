<template>
  <div class="oloxp" :style="{ '--c': accent }">
    <section class="dsec">
      <div class="wrap">
        <div class="head">
          <div v-if="s.kicker" class="k">{{ s.kicker }}</div>
          <h2 v-if="s.title_html" class="d" v-html="s.title_html"></h2>
          <p v-if="s.lead" v-html="s.lead"></p>
        </div>
        <div v-if="s.variant === 'url'" class="urlstream">
          <div v-for="(u, i) in urlItems" :key="i" class="url">
            <span v-html="u.html"></span> <span class="ok">{{ u.ok }}</span>
          </div>
        </div>
        <div v-else class="flips">
          <div v-for="(f, i) in flipItems" :key="i" class="fliprow">
            <span class="src"><span class="lab">{{ f.src_label }}</span><span v-html="f.src_html"></span></span>
            <span class="arrow">⇄</span>
            <span class="dst"><span class="lab">{{ f.dst_label }}</span><span v-html="f.dst_html"></span></span>
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
import def from '@/config/elements/oloxlist.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => ({ ...def.defaults, ...props.settings }));
const accent = computed(() => oloxColor(s.value.accent));
const flipItems = computed(() => (Array.isArray(s.value.flip_items) ? s.value.flip_items : []));
const urlItems = computed(() => (Array.isArray(s.value.url_items) ? s.value.url_items : []));
</script>
