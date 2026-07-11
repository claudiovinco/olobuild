<template>
  <div class="oloxp" :style="{ '--c': accent }">
    <section class="dsec">
      <div class="wrap">
        <div class="head">
          <div v-if="s.kicker" class="k">{{ s.kicker }}</div>
          <h2 v-if="s.title_html" class="d" v-html="s.title_html"></h2>
        </div>
        <div class="slabs">
          <div class="slab">
            <div class="k" style="margin-bottom:6px;">{{ s.free_kicker }}</div>
            <div class="price" v-html="s.free_price"></div>
            <div class="per">{{ s.free_per }}</div>
            <ul><li v-for="(li, i) in freeItems" :key="i" v-html="li.text_html"></li></ul>
            <a v-if="s.free_cta" class="cta ghost" @click.prevent>{{ s.free_cta }}</a>
          </div>
          <div class="cablewrap">
            <div class="slab pro in">
              <div class="cable"></div><div class="hook"></div>
              <div class="k" style="margin-bottom:6px;">{{ s.pro_kicker }}</div>
              <div class="price" v-html="s.pro_price"></div>
              <div class="per">{{ s.pro_per }}</div>
              <ul><li v-for="(li, i) in proItems" :key="i" v-html="li.text_html"></li></ul>
              <a v-if="s.pro_cta" class="cta" @click.prevent>{{ s.pro_cta }}</a>
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
import def from '@/config/elements/oloxpricing.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => ({ ...def.defaults, ...props.settings }));
const accent = computed(() => oloxColor(s.value.accent));
const freeItems = computed(() => (Array.isArray(s.value.free_items) ? s.value.free_items : []));
const proItems = computed(() => (Array.isArray(s.value.pro_items) ? s.value.pro_items : []));
</script>
