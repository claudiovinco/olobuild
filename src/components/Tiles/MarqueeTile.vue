<template>
  <div class="olo-mq-preview" :style="containerStyle">
    <div class="olo-mq-track" :style="trackStyle">
      <!-- Text mode -->
      <template v-if="s.content_type !== 'images'">
        <template v-for="i in textReps" :key="'t'+i">
          <template v-for="(item, j) in textItems" :key="'t'+i+'-'+j">
            <span class="olo-mq-text" :style="textStyle" data-olo-editable="text_items">{{ item }}</span>
            <span v-if="s.separator" class="olo-mq-sep" :style="sepStyle">{{ s.separator }}</span>
          </template>
        </template>
      </template>
      <!-- Image mode -->
      <template v-else>
        <template v-for="loop in 2" :key="'l'+loop">
          <template v-if="imgList.length">
            <img
              v-for="(url, idx) in imgList"
              :key="'i'+loop+'-'+idx"
              :src="url"
              alt=""
              :style="imgStyle"
              class="olo-mq-img"
            />
          </template>
          <span v-else :style="{ ...textStyle, opacity: 0.5 }">{{ t('Aggiungi immagini...') }}</span>
        </template>
      </template>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { resolveFontFamily } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({
  content_type: 'text',
  text_items: 'Testo scorrevole di esempio',
  separator: ' — ',
  images: [],
  image_height: '40',
  speed: '30',
  direction: 'left',
  pause_hover: true,
  gap: '60',
  bg_color: '#1F2937',
  text_color: 'var(--olo-color-text, #374151)',
  font_size: '16',
  font_weight: '500',
  letter_spacing: '1',
  text_transform: 'uppercase',
  font_family: '',
  font_style: 'normal',
  separator_color: '',
  separator_size: '',
  height: '50',
  full_width: true,
  border_top: '0',
  border_bottom: '0',
  border_color: 'var(--olo-color-border, #E5E7EB)',
  ...props.settings,
}));

const imgList = computed(() => {
  const imgs = s.value.images;
  if (!imgs || !Array.isArray(imgs)) return [];
  return imgs.map(img => (typeof img === 'object' && img !== null) ? (img.url || '') : img).filter(Boolean);
});

// Voci multiple: text_items splittato su newline o "|" (come il render PHP);
// stringa semplice = 1 voce, comportamento storico invariato.
const textItems = computed(() => {
  const raw = String(s.value.text_items || 'Testo scorrevole');
  const parts = raw.split(/\r\n|\r|\n|\|/).map(p => p.trim()).filter(Boolean);
  return parts.length ? parts : ['Testo scorrevole'];
});
const textReps = computed(() => Math.max(2, Math.ceil(10 / textItems.value.length)));

const containerStyle = computed(() => {
  const st = {
    background: s.value.bg_color || '#1F2937',
    height: (parseInt(s.value.height) || 50) + 'px',
    overflow: 'hidden',
  };
  const bt = parseInt(s.value.border_top) || 0;
  const bb = parseInt(s.value.border_bottom) || 0;
  const bc = s.value.border_color || 'var(--olo-color-border, #E5E7EB)';
  if (bt > 0) st.borderTop = `${bt}px solid ${bc}`;
  if (bb > 0) st.borderBottom = `${bb}px solid ${bc}`;
  if (s.value.full_width) {
    st.width = '100%';
  }
  return st;
});

const trackStyle = computed(() => {
  const speed = parseInt(s.value.speed) || 30;
  const gap = parseInt(s.value.gap) ?? 60;
  const dir = s.value.direction === 'right' ? 'reverse' : 'normal';
  return {
    display: 'flex',
    alignItems: 'center',
    height: '100%',
    width: 'max-content',
    gap: gap + 'px',
    animation: `olo-mq-scroll ${speed}s linear infinite`,
    animationDirection: dir,
  };
});

const FONT_LEGACY = {
  sans: 'var(--olo-font-family, inherit)',
  serif: "var(--olo-font-family-heading, Georgia, serif)",
  heading: "var(--olo-font-family-heading, Georgia, serif)",
};
const mqFontFamily = computed(() => resolveFontFamily(s.value.font_family, FONT_LEGACY) || undefined);

const textStyle = computed(() => ({
  color: s.value.text_color || 'var(--olo-color-text, #374151)',
  fontSize: (parseInt(s.value.font_size) || 16) + 'px',
  fontWeight: s.value.font_weight || '500',
  letterSpacing: (parseInt(s.value.letter_spacing) || 0) + 'px',
  textTransform: s.value.text_transform || 'uppercase',
  fontFamily: mqFontFamily.value,
  fontStyle: s.value.font_style === 'italic' ? 'italic' : 'normal',
  whiteSpace: 'nowrap',
  flexShrink: 0,
}));

const sepStyle = computed(() => {
  const sepsize = parseInt(s.value.separator_size, 10);
  return {
    ...textStyle.value,
    fontStyle: 'normal',
    color: s.value.separator_color || textStyle.value.color,
    fontSize: (sepsize > 0 ? sepsize : (parseInt(s.value.font_size) || 16)) + 'px',
    opacity: s.value.separator_color ? 1 : 0.5,
  };
});

const imgStyle = computed(() => ({
  height: (parseInt(s.value.image_height) || 40) + 'px',
  width: 'auto',
  flexShrink: 0,
  objectFit: 'contain',
  pointerEvents: 'none',
}));
</script>

<style>
@keyframes olo-mq-scroll {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}
</style>

<style scoped>
.olo-mq-preview {
  position: relative;
}
.olo-mq-track:hover {
  animation-play-state: paused;
}
</style>
