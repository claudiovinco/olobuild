<template>
  <div class="oloxp" :style="{ '--c': accent }">
    <header class="man-head">
      <div class="wrap">
        <div class="doc"><span v-for="(d, i) in docCodes" :key="i" v-html="d.html"></span></div>
        <img v-if="s.logo" class="plogo" :src="s.logo" alt="" />
        <h1 class="d" v-html="s.title_html"></h1>
        <p class="sub" style="margin-bottom:0;" v-html="s.sub_html"></p>
      </div>
    </header>
    <div class="wrap man-layout">
      <aside class="toc">
        <a v-for="(ch, i) in chapters" :key="i" :class="{ on: i === 0 }" @click.prevent>{{ ch.no }} · {{ strip(ch.title_html) }}</a>
        <a class="tspec" @click.prevent>{{ s.toc_spec }}</a>
      </aside>
      <main>
        <section v-for="(ch, i) in chapters" :key="i" class="ch">
          <span class="chno">{{ ch.no }}</span>
          <h2 v-html="ch.title_html"></h2>
          <div v-html="ch.body_html"></div>
        </section>
        <section class="spec-close">
          <div class="sc-head">
            <h2 v-html="s.spec_title"></h2>
            <div class="vv"><b>{{ s.spec_name }}</b>{{ s.spec_sub }}</div>
          </div>
          <div class="sc-body">
            <table class="dtab">
              <tbody>
                <tr v-for="(r, i) in specRows" :key="i"><td class="f">{{ r.f }}</td><td v-html="r.text_html"></td></tr>
              </tbody>
            </table>
            <div class="sc-foot">
              <a v-if="s.spec_cta1" class="cta" @click.prevent>{{ s.spec_cta1 }}</a>
              <a v-if="s.spec_cta2" class="cta ghost" @click.prevent>{{ s.spec_cta2 }}</a>
            </div>
          </div>
        </section>
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import '../../../assets/css/olox.css';
import { oloxColor } from '@/config/elements/_oloxShared.js';
import def from '@/config/elements/oloxmanual.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => ({ ...def.defaults, ...props.settings }));
const accent = computed(() => oloxColor(s.value.accent));
const docCodes = computed(() => (Array.isArray(s.value.doc_codes) ? s.value.doc_codes : []));
const chapters = computed(() => (Array.isArray(s.value.chapters) ? s.value.chapters : []));
const specRows = computed(() => (Array.isArray(s.value.spec_rows) ? s.value.spec_rows : []));
function strip(html) {
  return String(html || '').replace(/<[^>]*>/g, '');
}
</script>
