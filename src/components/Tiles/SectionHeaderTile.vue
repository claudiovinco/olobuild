<template>
  <div class="olo-sechead" :style="gridStyle">
    <div class="olo-sechead__left">
      <div v-if="s.eyebrow_show && s.eyebrow_text" class="olo-sechead__eyebrow" :style="eyebrowStyle">
        <span v-if="isBullet" class="olo-sechead__dot" :style="{ background: s.eyebrow_dot_color || '#b3261e' }"></span>
        <span v-else-if="s.eyebrow_separator" style="white-space:pre">{{ s.eyebrow_separator }}</span>
        <span>{{ s.eyebrow_text }}</span>
      </div>

      <h2 v-if="headlines.length" class="olo-sechead__headline" :style="headlineStyle">
        <span
          v-for="(line, i) in headlines"
          :key="i"
          :style="{ display: 'block', color: line.color || '#0f172a', fontStyle: line.italic ? 'italic' : 'normal' }"
        >{{ line.text }}</span>
      </h2>
    </div>

    <div v-if="showTagline" class="olo-sechead__right" style="text-align:right">
      <div v-if="s.tagline_text" class="olo-sechead__tag" :style="taglineStyle">{{ s.tagline_text }}</div>
      <div v-if="s.tagline_caption" class="olo-sechead__cap" :style="captionStyle">{{ s.tagline_caption }}</div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  eyebrow_show: true,
  eyebrow_text: 'PROVALO SUBITO',
  eyebrow_color: '#b3261e',
  eyebrow_dot_color: '#b3261e',
  eyebrow_separator: '— ',
  headline_lines: [
    { text: 'Nessun rischio,', color: '#0f172a', italic: false },
    { text: 'solo prodotto.',  color: '#b3261e', italic: true  },
  ],
  headline_font_family: 'serif',
  headline_font_size: 96,
  headline_line_height: 1.0,
  headline_font_weight: '700',
  headline_align: 'left',
  tagline_show: true,
  tagline_text: 'Try before you trust',
  tagline_text_italic: true,
  tagline_text_color: '#0f172a',
  tagline_text_size: 22,
  tagline_caption: 'TRE GARANZIE · CINQUE PROMESSE',
  tagline_caption_color: '',
  tagline_caption_size: 11,
  layout: 'split',
  split_ratio: '1.6fr 1fr',
  gap: 60,
  vertical_align: 'end',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const SERIF = "'Playfair Display','Cormorant Garamond',Georgia,'Times New Roman',serif";
const SANS  = "'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif";
const MONO  = "ui-monospace,'SF Mono',Menlo,Consolas,monospace";
const fmap  = { serif: SERIF, 'sans-serif': SANS, mono: MONO };
const hfam  = computed(() => fmap[s.value.headline_font_family] || SERIF);

const headlines = computed(() => (Array.isArray(s.value.headline_lines) ? s.value.headline_lines : []).filter(h => h && h.text));
const isBullet  = computed(() => (s.value.eyebrow_separator || '').trim() === '·');
const showTagline = computed(() => s.value.tagline_show && s.value.layout === 'split');

const gridStyle = computed(() => {
  const base = {
    display: 'grid',
    gap: (s.value.gap || 60) + 'px',
    alignItems: s.value.vertical_align || 'end',
  };
  if (s.value.layout === 'split')  base.gridTemplateColumns = s.value.split_ratio || '1.6fr 1fr';
  else if (s.value.layout === 'center') { base.gridTemplateColumns = '1fr'; base.textAlign = 'center'; }
  else base.gridTemplateColumns = '1fr';
  return base;
});

const eyebrowStyle = computed(() => ({
  display: 'inline-flex', alignItems: 'center', gap: '10px',
  fontFamily: MONO, fontSize: '12px', letterSpacing: '0.1em',
  textTransform: 'uppercase', color: s.value.eyebrow_color || '#b3261e',
  marginBottom: '24px',
}));

const headlineStyle = computed(() => ({
  fontFamily: hfam.value,
  fontSize: (s.value.headline_font_size || 96) + 'px',
  lineHeight: s.value.headline_line_height || 1,
  fontWeight: s.value.headline_font_weight || '700',
  letterSpacing: '-0.02em',
  textAlign: s.value.layout === 'center' ? 'center' : (s.value.headline_align || 'left'),
  margin: 0,
}));

const taglineStyle = computed(() => ({
  fontFamily: hfam.value,
  fontSize: (s.value.tagline_text_size || 22) + 'px',
  color: s.value.tagline_text_color || '#0f172a',
  fontStyle: s.value.tagline_text_italic ? 'italic' : 'normal',
  lineHeight: 1.3, marginBottom: '10px',
}));

const captionStyle = computed(() => ({
  fontFamily: MONO,
  fontSize: (s.value.tagline_caption_size || 11) + 'px',
  letterSpacing: '0.1em', textTransform: 'uppercase',
  color: s.value.tagline_caption_color || 'var(--olo-color-text-faint, #9ca3af)',
}));
</script>

<style scoped>
.olo-sechead__dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
</style>
