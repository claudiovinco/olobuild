<template>
  <div class="oloxp oloxp-home">
    <!-- fermata intro -->
    <section v-if="s.variant === 'intro'" class="panel intro" style="--c:var(--olo)">
      <div class="inner">
        <div>
          <div class="k">{{ s.intro_kicker }} <button class="olw" type="button">{{ s.olw_text }}</button></div>
          <h1 v-html="s.intro_title"></h1>
          <p class="sub" style="max-width:52ch;" v-html="s.intro_sub"></p>
          <div style="display:flex; gap:12px; flex-wrap:wrap;">
            <a class="cta" @click.prevent>{{ s.intro_cta1 }}</a>
            <a class="cta ghost" @click.prevent>{{ s.intro_cta2 }}</a>
          </div>
        </div>
      </div>
      <div class="marq"><span class="in">{{ marqueeLine }}{{ marqueeLine }}</span></div>
    </section>

    <!-- fermata finale -->
    <section v-else-if="s.variant === 'outro'" class="panel outro" style="--c:var(--olo)">
      <div class="inner outro-grid">
        <div>
          <div class="k">{{ s.outro_kicker }}</div>
          <h2 style="font-size:clamp(36px,4.4vw,72px); max-width:14ch;" v-html="s.outro_title"></h2>
          <p class="sub" style="max-width:44ch;" v-html="s.outro_sub"></p>
          <div class="outro-links" style="justify-content:flex-start;">
            <a v-for="(l, i) in outroLinks" :key="i" :style="{ '--c': oloxColor(l.color) }" @click.prevent>{{ l.label }}</a>
          </div>
          <div class="fine" style="margin-top:40px;">{{ s.outro_fine }}</div>
        </div>
        <OloxScenePreview scene="madlib" :s="s" />
      </div>
    </section>

    <!-- fermata prodotto -->
    <section v-else class="panel" :style="{ '--c': oloxColor(s.color) }">
      <span class="idx">·· / ··</span>
      <div class="inner">
        <div>
          <img v-if="s.logo" class="plogo" :src="s.logo" :alt="s.label" />
          <div class="k">{{ s.kicker }}</div>
          <h2 v-html="s.title_html"></h2>
          <p class="sub" v-html="s.sub_html"></p>
          <div class="tags">
            <span v-for="(tg, ti) in tags" :key="ti" :class="{ hot: ti === 0 }">{{ tg }}</span>
          </div>
          <a v-if="s.cta_text" class="cta" @click.prevent>{{ s.cta_text }}</a>
        </div>
        <OloxScenePreview :scene="s.scene" :s="s" />
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import '../../../assets/css/olox.css';
import { oloxColor } from '@/config/elements/_oloxShared.js';
import OloxScenePreview from './OloxScenePreview.vue';
import def from '@/config/elements/oloxpanel.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => ({ ...def.defaults, ...props.settings }));
const tags = computed(() => String(s.value.tags || '').split('|').map(x => x.trim()).filter(Boolean));
const outroLinks = computed(() => (Array.isArray(s.value.outro_links) ? s.value.outro_links : []));
const marqueeLine = computed(() => {
  const items = Array.isArray(s.value.marquee_items) ? s.value.marquee_items : [];
  return items.map(i => i.text).filter(Boolean).join(' ● ') + ' ● ';
});
</script>
