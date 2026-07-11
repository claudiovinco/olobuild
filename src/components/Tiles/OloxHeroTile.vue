<template>
  <div class="oloxp" :style="{ '--c': accent }">
    <header class="dhero" :class="bgClass">
      <div class="wrap grid2">
        <div>
          <img v-if="s.logo" class="plogo" :src="s.logo" alt="" />
          <div v-if="s.kicker" class="k">{{ s.kicker }}</div>
          <h1 class="d" v-html="titleHtml"></h1>
          <p v-if="s.sub_html" class="sub" v-html="s.sub_html"></p>
          <div v-if="tags.length" class="tags">
            <span v-for="(t, i) in tags" :key="i" :class="{ hot: t.hot }">{{ t.text }}</span>
          </div>
          <div v-if="s.cta1_text || s.cta2_text" style="display:flex; gap:12px; flex-wrap:wrap;">
            <a v-if="s.cta1_text" class="cta" @click.prevent>{{ s.cta1_text }}</a>
            <a v-if="s.cta2_text" class="cta ghost" @click.prevent>{{ s.cta2_text }}</a>
          </div>
        </div>
        <div :style="{ display: 'flex', justifyContent: 'center', paddingBottom: s.scene === 'clock' ? '50px' : '0' }">
          <!-- scena statica rappresentativa (runtime completo nel frontend) -->
          <div v-if="s.scene === 'wall'" style="width:min(100%,520px);">
            <div class="ox-hwall">
              <i v-for="n in 84" :key="n" :class="wallCls(n)"></i>
              <div class="count"><b>{{ s.wall_count }}</b>{{ s.wall_label }}</div>
            </div>
          </div>
          <div v-else-if="s.scene === 'clock'" class="clockface">
            <div class="hand h2" style="--rot:300deg"></div>
            <div class="hand hm" style="--rot:60deg"></div>
            <div class="pin"></div>
            <div class="clocklbl"><b>08:00</b>{{ s.clock_label }}</div>
          </div>
          <div v-else-if="s.scene === 'console'" class="console go">
            <div class="cbar"><b>{{ s.console_title }}</b><span>{{ s.console_sub }}</span></div>
            <div class="rows">
              <div v-for="(r, i) in s.console_rows" :key="i" class="crow">
                <span class="lc">{{ r.lc }}</span>
                <span class="bar"><i :style="{ '--w': (r.w || 0) + '%' }"></i></span>
                <span class="pc">{{ r.pc || (r.w + '%') }}</span>
              </div>
            </div>
          </div>
          <div v-else-if="s.scene === 'term'" class="term">
            <div class="tbar"><b>{{ s.term_title }}</b><span>{{ s.term_sub }}</span></div>
            <pre><template v-for="(l, i) in s.term_lines" :key="i"><span :class="l.cls">{{ l.text }}</span>{{ '\n' }}</template><span class="cursor"></span></pre>
          </div>
          <div v-else-if="s.scene === 'porthole'" class="ox-porthole">
            <div class="pano"></div>
            <span class="spot" style="top:38%; left:30%;"></span>
            <span class="spot" style="top:58%; left:66%;"></span>
          </div>
          <div v-else-if="s.scene === 'medal'" class="medal">
            <div class="orbit"><i></i></div>
            <div class="inner">{{ s.medal_top }}<b>{{ s.medal_big }}</b>{{ s.medal_bot }}</div>
          </div>
        </div>
      </div>
    </header>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import '../../../assets/css/olox.css';
import { oloxColor } from '@/config/elements/_oloxShared.js';
import def from '@/config/elements/oloxhero.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => ({ ...def.defaults, ...props.settings }));
const accent = computed(() => oloxColor(s.value.accent));
const tags = computed(() => (Array.isArray(s.value.tags) ? s.value.tags : []));
const bgClass = computed(() => {
  const v = s.value.bg_variant;
  return ['build', 'booking', 'lang', 'secur', 'tutor'].includes(v) ? 'oxbg-' + v : '';
});
const titleHtml = computed(() => {
  const raw = String(s.value.title_html || '');
  if (s.value.title_fx === 'scramble') {
    const words = (Array.isArray(s.value.scramble_words) ? s.value.scramble_words : []).map(w => w.text).filter(Boolean);
    const span = '<span class="scramble">' + (words[0] || '') + '</span>';
    return raw.replace('{scramble}', span);
  }
  return raw;
});
// pattern deterministico dei mattoni per il canvas (stessa resa a ogni render)
function wallCls(n) {
  const r = ((n * 37) % 100) / 100;
  if (r < 0.18) return 'k1';
  if (r < 0.42) return 'k2';
  return '';
}
</script>
