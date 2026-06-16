<template>
  <div class="olo-overlaygrid" :class="'olo-og--preset-' + (s.preset || 'editorial-grid')">
    <div class="mog-grid" :class="{ 'mog-grid--masonry': masonry }" :style="gridStyle">
      <div
        v-for="(item, i) in items"
        :key="item.id || i"
        class="mog-cell"
        :class="cellClass(item)"
        :style="masonry ? null : { height: itemHeight + 'px' }"
      >
        <!-- Image layer (receives hover effects) — card_type='image' (default) -->
        <div
          v-if="cardType(item) === 'image'"
          :class="['mog-bg', hoverImageClass]"
          :style="bgStyle(item)"
        ></div>

        <!-- Card piena (text/icon/graphic): niente placeholder grigio -->
        <div
          v-else
          class="mog-card"
          :class="'mog-card--' + cardType(item)"
          :style="{ background: item.card_bg || '#0E1B2E' }"
        >
          <span
            v-if="cardType(item) === 'icon' && item.icon"
            class="mog-card__icon"
            :style="item.icon_color ? { color: item.icon_color } : null"
          ><span :uk-icon="'icon: ' + item.icon + '; ratio: 1.6'"></span></span>
          <span v-else-if="cardType(item) === 'graphic'" class="mog-card__graphic" aria-hidden="true">
            <span v-for="n in 12" :key="n"></span>
          </span>
        </div>

        <!-- Ribbon -->
        <span
          v-if="item.ribbon"
          class="mog-ribbon"
          :class="'mog-ribbon--' + (s.ribbon_position || 'top-right')"
          :style="{ background: s.ribbon_bg || '#e11d48', color: s.ribbon_color || '#fff' }"
          :data-olo-editable="'items.' + i + '.ribbon'"
        >{{ item.ribbon }}</span>

        <!-- Overlay -->
        <div class="mog-overlay" :class="[...overlayClasses, hoverOverlayClass, { 'mog-overlay--bare': cardType(item) !== 'image' }]" :style="overlayPadStyle">
          <component :is="titleTag" class="mb-font-bold mb-text-white mb-m-0" :style="titleFontStyle" :data-olo-editable="'items.' + i + '.title'">{{ item.title }}</component>
          <div v-if="item.subtitle" class="mb-text-xs mb-text-gray-200 mb-mt-1" :data-olo-editable="'items.' + i + '.subtitle'">{{ item.subtitle }}</div>
          <div v-if="cardType(item) !== 'image' && item.body" class="mog-card__body mb-mt-1" :data-olo-editable="'items.' + i + '.body'">{{ item.body }}</div>
          <div v-if="item.link" class="mb-text-[10px] mb-text-blue-300 mb-mt-1 mb-opacity-70">&#128279;</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  columns: '3',
  gap: 'medium',
  height: '300',
  overlay_position: 'bottom',
  overlay_horizontal: 'left',
  overlay_padding: 'medium',
  title_size: 'h3',
  hover_effect: 'none',
  hover_overlay: 'always',
  ribbon_position: 'top-right',
  ribbon_bg: '#e11d48',
  ribbon_color: '#ffffff',
  layout_mode: 'uniform',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const items = computed(() => {
  const raw = s.value.items;
  if (Array.isArray(raw) && raw.length) return raw;
  return [
    { id: 'og-1', image: '', title: 'Elemento 1', subtitle: '' },
    { id: 'og-2', image: '', title: 'Elemento 2', subtitle: '' },
    { id: 'og-3', image: '', title: 'Elemento 3', subtitle: '' },
  ];
});

const cols = computed(() => parseInt(s.value.columns) || 3);
// Scale height down for preview — more columns = shorter cells
const itemHeight = computed(() => {
  const h = parseInt(s.value.height) || 300;
  const c = cols.value;
  if (c >= 4) return Math.min(h, 150);
  if (c >= 3) return Math.min(h, 200);
  return Math.min(h, 300);
});

const gapMap = { collapse: '0px', small: '8px', medium: '16px', large: '24px' };
const gap = computed(() => gapMap[s.value.gap || 'medium'] || '16px');

const masonry = computed(() => (s.value.layout_mode || 'uniform') === 'masonry');
const gridStyle = computed(() => {
  const st = { '--mog-cols': cols.value, '--mog-gap': gap.value };
  if (masonry.value) st['--mog-row'] = itemHeight.value + 'px';
  return st;
});
function cellClass(item) {
  if (!masonry.value) return '';
  return [item.tall ? 'mog-tall' : '', item.wide ? 'mog-wide' : ''];
}

// Tipo card (additivo): default 'image' → resa invariata.
function cardType(item) {
  const t = item && item.card_type;
  return ['image', 'text', 'icon', 'graphic'].includes(t) ? t : 'image';
}

function bgStyle(item) {
  if (item.image) {
    return { backgroundImage: `url(${item.image})`, backgroundSize: 'cover', backgroundPosition: (s.value.object_position || 'center center') };
  }
  return { background: '#374151' };
}

const overlayClasses = computed(() => {
  const pos = s.value.overlay_position || 'bottom';
  const align = s.value.overlay_horizontal || 'left';
  const classes = ['mog-overlay--' + pos];
  if (align === 'center') classes.push('mog-align-center');
  if (align === 'right') classes.push('mog-align-right');
  return classes;
});

const overlayPadStyle = computed(() => {
  const pad = s.value.overlay_padding || 'medium';
  if (pad === 'small') return { padding: '8px 12px' };
  if (pad === 'large') return { padding: '24px 32px' };
  return { padding: '12px 16px' };
});

const titleTag = computed(() => {
  const tag = s.value.title_size || 'h3';
  return ['h1', 'h2', 'h3', 'h4'].includes(tag) ? tag : 'h3';
});

const titleFontStyle = computed(() => {
  const tag = s.value.title_size || 'h3';
  const c = cols.value;
  // Scale font down for many columns in preview
  const scale = c >= 4 ? 0.7 : c >= 3 ? 0.85 : 1;
  const sizes = { h1: 28, h2: 22, h3: 16, h4: 14 };
  const base = sizes[tag] || 16;
  return { fontSize: Math.round(base * scale) + 'px', lineHeight: 1.3 };
});

const hoverImageClass = computed(() => {
  const fx = s.value.hover_effect || 'none';
  return fx !== 'none' ? 'mog-hover-' + fx : '';
});

const hoverOverlayClass = computed(() => {
  const ov = s.value.hover_overlay || 'always';
  return ov !== 'always' ? 'mog-ov-' + ov : '';
});
</script>

<style scoped>
.olo-overlaygrid { min-height: 80px; }

.mog-grid {
  display: grid !important;
  grid-template-columns: repeat(var(--mog-cols, 3), 1fr) !important;
  gap: var(--mog-gap, 16px) !important;
}
.mog-grid--masonry {
  grid-auto-rows: var(--mog-row, 150px);
  grid-auto-flow: dense;
}
.mog-grid--masonry .mog-tall { grid-row: span 2; }
.mog-grid--masonry .mog-wide { grid-column: span 2; }

.mog-cell {
  position: relative;
  overflow: hidden;
  border-radius: 4px;
}

/* Background image layer */
.mog-bg {
  position: absolute;
  inset: 0;
  transition: transform 0.5s ease, filter 0.5s ease;
}

/* Card non-immagine (text/icon/graphic) — niente placeholder grigio */
.mog-card {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  padding: 16px;
  border: 1px solid rgba(255, 255, 255, 0.12);
  box-sizing: border-box;
}
.mog-card__icon { display: inline-flex; align-items: center; justify-content: center; color: #b9fbe7; }
.mog-card__icon :deep(svg) { width: 28px; height: 28px; }
.mog-card__graphic {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 5px;
  width: 88px;
}
.mog-card__graphic > span { display: block; aspect-ratio: 1 / 1; border-radius: 3px; background: #b9fbe7; }
.mog-card__graphic > span:nth-child(3n + 2) { background: #9df5d6; }
.mog-card__graphic > span:nth-child(4n + 1) { opacity: 0.55; }
.mog-card__body { font-size: 11px; line-height: 1.5; color: rgba(255, 255, 255, 0.85); }

/* Overlay "bare": testo direttamente sulla card_bg, nessun fondo scuro */
.mog-overlay--bare { background: transparent !important; }

/* Overlay text layer */
.mog-overlay {
  position: absolute;
  left: 0;
  right: 0;
  background: rgba(0, 0, 0, 0.6);
  z-index: 1;
}
.mog-overlay--bottom { bottom: 0; }
.mog-overlay--top { top: 0; }
.mog-overlay--center { top: 50%; transform: translateY(-50%); }
.mog-overlay--cover { inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; }
.mog-overlay--bottom-left { bottom: 0; left: 0; right: auto; }
.mog-overlay--bottom-center { bottom: 0; left: 50%; right: auto; transform: translateX(-50%); text-align: center; }
.mog-overlay--bottom-right { bottom: 0; left: auto; right: 0; text-align: right; }
.mog-overlay--top-left { top: 0; left: 0; right: auto; }
.mog-overlay--top-center { top: 0; left: 50%; right: auto; transform: translateX(-50%); text-align: center; }
.mog-overlay--top-right { top: 0; left: auto; right: 0; text-align: right; }
.mog-overlay--center-left { top: 50%; left: 0; right: auto; transform: translateY(-50%); }
.mog-overlay--center-right { top: 50%; left: auto; right: 0; transform: translateY(-50%); text-align: right; }
.mog-align-center { text-align: center; }
.mog-align-right { text-align: right; }

/* Ribbon */
.mog-ribbon {
  position: absolute;
  z-index: 2;
  font-size: 9px;
  font-weight: 700;
  padding: 3px 8px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.mog-ribbon--top-right { top: 0; right: 10px; border-radius: 0 0 3px 3px; }
.mog-ribbon--top-left { top: 0; left: 10px; border-radius: 0 0 3px 3px; }

/* Hover image effects — on .mog-bg only */
.mog-cell:hover .mog-hover-zoom { transform: scale(1.08); }
.mog-cell:hover .mog-hover-zoom-rotate { transform: scale(1.08) rotate(2deg); }
.mog-hover-brightness { filter: brightness(0.7); }
.mog-cell:hover .mog-hover-brightness { filter: brightness(1); }
.mog-hover-desaturate { filter: grayscale(100%); }
.mog-cell:hover .mog-hover-desaturate { filter: grayscale(0%); }
.mog-hover-blur-in { filter: blur(3px); }
.mog-cell:hover .mog-hover-blur-in { filter: blur(0); }

/* Hover overlay effects */
.mog-ov-fade { opacity: 0; transition: opacity 0.3s ease; }
.mog-cell:hover .mog-ov-fade { opacity: 1; }
.mog-ov-slide-bottom { transform: translateY(100%); transition: transform 0.3s ease; }
.mog-cell:hover .mog-ov-slide-bottom { transform: translateY(0); }
.mog-ov-slide-top { transform: translateY(-100%); transition: transform 0.3s ease; }
.mog-cell:hover .mog-ov-slide-top { transform: translateY(0); }
.mog-ov-slide-left { transform: translateX(-100%); transition: transform 0.3s ease; }
.mog-cell:hover .mog-ov-slide-left { transform: translateX(0); }
.mog-ov-slide-right { transform: translateX(100%); transition: transform 0.3s ease; }
.mog-cell:hover .mog-ov-slide-right { transform: translateX(0); }

/* ───── Preset visual hints in builder ───── */

/* Liquid Glass */
.olo-og--preset-liquid-glass .mog-cell { border-radius: 14px; overflow: hidden; }
.olo-og--preset-liquid-glass .mog-overlay {
  backdrop-filter: blur(12px) saturate(180%);
  -webkit-backdrop-filter: blur(12px) saturate(180%);
  background: rgba(255,255,255,0.20) !important;
  border: 1px solid rgba(255,255,255,0.4);
  border-radius: 10px;
  margin: 8px;
}

/* Neon Cyber */
.olo-og--preset-neon-cyber .mog-cell {
  border: 2px solid var(--olo-color-primary, #e1474f);
  box-shadow: 0 0 14px color-mix(in srgb, var(--olo-color-primary, #e1474f) 35%, transparent);
}
.olo-og--preset-neon-cyber .mog-overlay {
  background: linear-gradient(180deg, transparent 0%, rgba(10,15,28,0.85) 100%) !important;
}
.olo-og--preset-neon-cyber .mog-overlay :deep(*) {
  color: var(--olo-color-primary, #e1474f) !important;
  text-shadow: 0 0 6px color-mix(in srgb, var(--olo-color-primary, #e1474f) 50%, transparent);
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

/* Brutalist */
.olo-og--preset-brutalist-block .mog-cell {
  border: 3px solid #000;
  box-shadow: 5px 5px 0 0 #000;
}
.olo-og--preset-brutalist-block .mog-overlay {
  background: #fff !important;
  border-top: 3px solid #000;
}
.olo-og--preset-brutalist-block .mog-overlay :deep(*) {
  color: #000 !important;
  font-weight: 900 !important;
  text-transform: uppercase;
}

/* Magnetic */
.olo-og--preset-magnetic-liquid .mog-cell {
  border-radius: 18px;
  box-shadow: 0 12px 26px color-mix(in srgb, var(--olo-color-primary, #e1474f) 18%, transparent);
}
.olo-og--preset-magnetic-liquid .mog-overlay {
  background: linear-gradient(135deg, rgba(0,0,0,0.30) 0%, color-mix(in srgb, var(--olo-color-primary, #e1474f) 55%, transparent) 100%) !important;
}

/* Sticker */
.olo-og--preset-sticker .mog-cell {
  border: 3px dashed color-mix(in srgb, var(--olo-color-primary, #e1474f) 55%, transparent);
  background: #fff;
  padding: 4px;
  border-radius: 6px;
  box-shadow: 0 10px 22px rgba(0,0,0,0.16);
}
.olo-og--preset-sticker .mog-grid > .mog-cell:nth-child(3n+1) { transform: rotate(-1.4deg); }
.olo-og--preset-sticker .mog-grid > .mog-cell:nth-child(3n+2) { transform: rotate(0.9deg); }
.olo-og--preset-sticker .mog-grid > .mog-cell:nth-child(3n+3) { transform: rotate(-0.5deg); }

/* Retro Terminal */
.olo-og--preset-retro-terminal .mog-cell {
  border: 1px solid rgba(0,255,140,0.45);
  background-image: repeating-linear-gradient(0deg, transparent 0, transparent 2px, rgba(0,255,140,0.06) 2px, rgba(0,255,140,0.06) 3px);
}
.olo-og--preset-retro-terminal .mog-bg {
  filter: hue-rotate(70deg) saturate(2) brightness(0.7);
}
.olo-og--preset-retro-terminal .mog-overlay {
  background: rgba(12,12,12,0.85) !important;
  font-family: ui-monospace, SFMono-Regular, Menlo, monospace !important;
}
.olo-og--preset-retro-terminal .mog-overlay :deep(*) {
  color: #00ff8c !important;
  text-shadow: 0 0 6px rgba(0,255,140,0.5);
  font-family: inherit !important;
  text-transform: uppercase;
}

/* 3D Tilt */
.olo-og--preset-3d-tilt .mog-cell {
  box-shadow: 0 18px 36px rgba(0,0,0,0.18);
  transform: perspective(800px) rotateX(2deg);
  transform-origin: center top;
}

/* Duotone Portfolio */
.olo-og--preset-duotone-portfolio .mog-bg {
  filter: grayscale(100%);
  transition: filter 0.45s ease;
}
.olo-og--preset-duotone-portfolio .mog-cell:hover .mog-bg {
  filter: grayscale(0%);
}
</style>
