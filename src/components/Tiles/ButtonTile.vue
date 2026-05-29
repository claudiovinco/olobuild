<template>
  <div
    class="mb-py-4"
    :style="{ display: 'flex', justifyContent: alignMap[rv(settings, 'alignment', s.alignment, builderStore.viewMode)] || 'center' }"
  >
    <a
      :href="s.url || '#'"
      :target="s.target || '_self'"
      :aria-label="s.text || undefined"
      class="olo-btn mb-relative"
      :style="btnStyle"
      @click.prevent
    >
      <!-- bg video creativo: anteprima fedele -->
      <video
        v-if="bgVideoUrl"
        :src="bgVideoUrl"
        autoplay muted loop playsinline
        style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:0;pointer-events:none;border-radius:inherit"
      ></video>
      <!-- bg image creativo: anteprima fedele -->
      <div
        v-else-if="bgImageUrl"
        :style="{ position:'absolute', inset:0, backgroundImage:`url('${bgImageUrl}')`, backgroundSize:'cover', backgroundPosition:'center', zIndex:0 }"
      ></div>
      <span style="display:inline-flex;align-items:center;position:relative;z-index:2;" :style="{ flexDirection: s.icon_position === 'after' ? 'row-reverse' : 'row', gap: (parseInt(s.icon_spacing) || 8) + 'px' }">
        <span v-if="iconSvg" class="olo-btn-icon" :style="{ width: '1em', height: '1em', display: 'inline-flex', alignItems: 'center', justifyContent: 'center' }" v-html="iconSvg"></span>
        <span data-olo-editable="text">{{ s.text || 'Clicca qui' }}</span>
      </span>
      <!-- hover indicator -->
      <span
        v-if="s.hover_image || s.hover_video"
        class="mb-absolute mb--top-2 mb--right-2 mb-bg-black/70 mb-text-white mb-rounded-full mb-w-5 mb-h-5 mb-flex mb-items-center mb-justify-center"
        style="font-size: 10px; line-height: 1;"
      >{{ s.hover_video ? '▶' : '⇄' }}</span>
    </a>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
import { useBuilderStore } from '@/stores/builder';
import { rv } from '@/composables/useResponsiveValue';
import { getShadowValue } from '@/composables/useShadowMap';
import { useBoxModel } from '@/composables/useBoxModel';
import { resolveColor, TOKENS, buildDefaults } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const builderStore = useBuilderStore();

// Fonte UNICA dei default (stessi del registry button.js) — niente più
// ridichiarazioni divergenti (era bg_color '#e1474f' vs '' nel config).
const s = computed(() => ({ ...buildDefaults('button'), ...props.settings }));

// Box-model normalizzato (gestisce numero|oggetto + legacy padding_x/padding_y).
const { radiusCss, paddingCss } = useBoxModel(s, {
  radiusKey: 'border_radius', radiusFallback: 10,
  paddingKey: 'tile_padding', paddingFallback: [12, 24, 12, 24],
  paddingLegacy: ['padding_y', 'padding_x'],
});

const iconSvg = computed(() => iconsSvg[s.value.icon] || '');

// Bg creativo (unified bg field) — supporta video/image come sfondo del button
const bgVideoUrl = computed(() => {
  const b = s.value.bg;
  if (b && typeof b === 'object' && b.type === 'video') return b.video_url || '';
  return '';
});
const bgImageUrl = computed(() => {
  const b = s.value.bg;
  if (b && typeof b === 'object' && b.type === 'image') return b.image_url || '';
  return '';
});

const alignMap = {
  left: 'flex-start',
  center: 'center',
  right: 'flex-end',
};

const btnStyle = computed(() => {
  const mode = builderStore.viewMode;
  const bw = parseInt(s.value.border_width) || 0;
  const ls = parseFloat(s.value.letter_spacing) || 0;

  const fontSize = rv(props.settings, 'font_size', s.value.font_size, mode);

  // Se c'è bg creativo (video/image/gradient/pattern), il bg_color viene sovrascritto
  const bgObj = s.value.bg;
  const hasCreative = bgObj && typeof bgObj === 'object' && bgObj.type && bgObj.type !== 'none';

  const style = {
    display: 'inline-block',
    width: s.value.full_width ? '100%' : 'auto',
    textAlign: 'center',
    textDecoration: 'none',
    padding: paddingCss.value,
    // TOKEN-FIRST: se l'utente non sceglie, eredita il brand (mai indaco hardcoded)
    backgroundColor: hasCreative ? 'transparent' : resolveColor(s.value.bg_color, TOKENS.primary),
    color: resolveColor(s.value.text_color, TOKENS.onPrimary),
    borderRadius: radiusCss.value,
    fontSize: `${fontSize || 16}px`,
    fontWeight: s.value.font_weight || '600',
    cursor: 'pointer',
    textTransform: s.value.text_transform !== 'none' ? s.value.text_transform : undefined,
    position: 'relative',
    overflow: 'hidden',
    transition: 'transform .15s ease, box-shadow .15s ease',
  };

  if (ls > 0) style.letterSpacing = ls + 'px';
  if (bw > 0) style.border = `${bw}px solid ${resolveColor(s.value.border_color, TOKENS.primary)}`;

  const sh = getShadowValue(s.value);
  if (sh !== 'none') style.boxShadow = sh;

  return style;
});
</script>

<style scoped>
.olo-btn-icon :deep(svg) {
  width: 100%;
  height: 100%;
  fill: currentColor;
  stroke: currentColor;
}
/* a11y: anello di focus visibile da tastiera (color-mix sul primario corrente) */
.olo-btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
</style>
