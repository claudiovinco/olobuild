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
        :style="{ position:'absolute', inset:0, backgroundImage:`url('${bgImageUrl}')`, backgroundSize:'cover', backgroundPosition:bgImagePos, zIndex:0 }"
      ></div>
      <span style="display:inline-flex;align-items:center;position:relative;z-index:2;" :style="{ flexDirection: s.icon_position === 'after' ? 'row-reverse' : 'row', gap: iconGap }">
        <span v-if="iconSvg" class="olo-btn-icon" aria-hidden="true" :style="iconStyle" v-html="iconSvg"></span>
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
import { resolveColor, resolveFontFamily, TOKENS, buildDefaults } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const builderStore = useBuilderStore();

// Fonte UNICA dei default (stessi del registry button.js) — niente più
// ridichiarazioni divergenti (era bg_color '#e1474f' vs '' nel config).
const DEFAULTS = buildDefaults('button');
const s = computed(() => ({ ...DEFAULTS, ...props.settings }));

// Box-model normalizzato (gestisce numero|oggetto + legacy padding_x/padding_y).
const { radiusCss, paddingCss } = useBoxModel(s, {
  radiusKey: 'border_radius', radiusFallback: 10,
  paddingKey: 'tile_padding', paddingFallback: [12, 24, 12, 24],
  paddingLegacy: ['padding_y', 'padding_x'],
});

// Libreria unificata (UIkit vince sui nomi doppi, poi Lucide), come render_icon_html()
// in PHP. Le icone «custom:» qui non ci sono: nessuna tile Vue sa ancora caricarle.
const iconSvg = computed(() => iconsSvg[s.value.icon] || '');

// Gemello di icona_pulsante() in class-button-tile.php: la dimensione è in px
// (20 = la resa storica, uk-icon ratio 1; prima qui era 1em e il canvas non
// combaciava col sito) e il colore '' eredita quello del testo, anche in hover.
const iconPx = computed(() => {
  const v = s.value.icon_size;
  const n = (typeof v === 'number' || typeof v === 'string') ? parseInt(v, 10) : NaN;
  return n >= 1 ? Math.min(n, 200) : DEFAULTS.icon_size;
});
const iconStyle = computed(() => ({
  width: `${iconPx.value}px`,
  height: `${iconPx.value}px`,
  display: 'inline-flex',
  alignItems: 'center',
  justifyContent: 'center',
  flexShrink: 0,
  color: resolveColor(s.value.icon_color, 'inherit'),
}));
// Gemello di absint( $s['icon_spacing'] ?? 8 ): «Spazio icona» a 0 è 0 px, non 8.
const iconGap = computed(() => {
  const n = parseInt(s.value.icon_spacing ?? 8, 10);
  return `${Number.isFinite(n) ? Math.abs(n) : 0}px`;
});

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
// Onora il punto focale dello sfondo universale (BackgroundControls salva image_position);
// prima il canvas hardcodava 'center' ignorando la scelta dell'utente.
const bgImagePos = computed(() => {
  const b = s.value.bg;
  return (b && typeof b === 'object' && b.image_position) ? b.image_position : 'center center';
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

  // Stile tipografico: il pulsante lo applica da sé (gemello di
  // class-button-tile.php, il wrapper non riceve la classe olo-typo-*):
  // famiglia, peso e interlinea dallo stile; maiuscolo e spaziatura solo se
  // qui non ne è impostato uno proprio. Senza stile, la famiglia scelta.
  const tp = String(s.value.typography_preset || '').replace(/[^A-Za-z0-9_-]/g, '');
  if (tp) {
    style.fontFamily = `var(--olo-font-${tp}-family)`;
    style.fontWeight = `var(--olo-font-${tp}-weight)`;
    style.lineHeight = `var(--olo-font-${tp}-line-height)`;
    if (!style.textTransform) style.textTransform = `var(--olo-font-${tp}-transform)`;
    if (!(ls > 0)) style.letterSpacing = `var(--olo-font-${tp}-letter-spacing)`;
  } else {
    const ff = resolveFontFamily(s.value.font_family || '');
    if (ff && ff !== 'inherit') style.fontFamily = ff;
  }

  if (ls > 0) style.letterSpacing = ls + 'px';
  if (bw > 0) style.border = `${bw}px solid ${resolveColor(s.value.border_color, TOKENS.primary)}`;

  const sh = getShadowValue(s.value);
  if (sh !== 'none') style.boxShadow = sh;

  return style;
});
</script>

<style scoped>
/* Come .uk-icon: il riempimento arriva ereditato dallo span, così l'SVG di Lucide
   (fill="none" sul radice, tratto in currentColor) resta a contorno invece di
   diventare una sagoma piena, e UIkit disegna senza il tratto in più. */
.olo-btn-icon {
  fill: currentColor;
}
.olo-btn-icon :deep(svg) {
  width: 100%;
  height: 100%;
}
/* a11y: anello di focus visibile da tastiera (color-mix sul primario corrente) */
.olo-btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
</style>
