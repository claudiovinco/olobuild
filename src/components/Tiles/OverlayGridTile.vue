<template>
  <div class="olo-overlaygrid">
    <div class="mog-grid" :style="{ '--mog-cols': cols, '--mog-gap': gap }">
      <div
        v-for="(item, i) in items"
        :key="item.id || i"
        class="mog-cell"
        :style="{ height: itemHeight + 'px' }"
      >
        <!-- Image layer (receives hover effects) -->
        <div
          :class="['mog-bg', hoverImageClass]"
          :style="bgStyle(item)"
        ></div>

        <!-- Ribbon -->
        <span
          v-if="item.ribbon"
          class="mog-ribbon"
          :class="'mog-ribbon--' + (s.ribbon_position || 'top-right')"
          :style="{ background: s.ribbon_bg || '#e11d48', color: s.ribbon_color || '#fff' }"
          :data-olo-editable="'items.' + i + '.ribbon'"
        >{{ item.ribbon }}</span>

        <!-- Overlay -->
        <div class="mog-overlay" :class="[...overlayClasses, hoverOverlayClass]" :style="overlayPadStyle">
          <component :is="titleTag" class="mb-font-bold mb-text-white mb-m-0" :style="titleFontStyle" :data-olo-editable="'items.' + i + '.title'">{{ item.title }}</component>
          <div v-if="item.subtitle" class="mb-text-xs mb-text-gray-200 mb-mt-1" :data-olo-editable="'items.' + i + '.subtitle'">{{ item.subtitle }}</div>
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

// gridStyle replaced with CSS custom properties on .mog-grid

function bgStyle(item) {
  if (item.image) {
    return { backgroundImage: `url(${item.image})`, backgroundSize: 'cover', backgroundPosition: 'center' };
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
</style>
