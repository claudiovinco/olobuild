<template>
  <div
    :class="cellClasses"
    :style="cellStyle"
    :id="tile.advanced?.html_id || undefined"
    :data-tile-id="tile.id"
    @click.stop="selectTile"
    @contextmenu.prevent="onContextMenu"
  >
    <!-- Background image layer (for image bg preview) -->
    <div
      v-if="hasBgImage"
      class="olo-cell-bg-layer"
      :style="bgImageStyle"
    ></div>
    <!-- Overlay layer -->
    <div
      v-if="hasOverlay"
      class="olo-cell-overlay-layer"
      :style="overlayStyle"
    ></div>

    <!-- Cell toolbar -->
    <div class="olo-cell-toolbar">
      <button title="Sposta" class="move">&#x2630;</button>
      <button title="Duplica" @click.stop="duplicate">&#x2398;</button>
      <button title="Elimina" class="delete" @click.stop="remove">&#x2715;</button>
    </div>

    <!-- Full-width indicator -->
    <div v-if="tile.style?.full_width" class="olo-fullwidth-badge">FULL</div>
    <!-- Global Widget indicator -->
    <div v-if="tile.global_id" class="olo-global-badge">G</div>
    <!-- Hidden in viewport indicator -->
    <div v-if="isHiddenInViewport" class="olo-hidden-vp-badge">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" x2="23" y1="1" y2="23"/></svg>
    </div>
    <!-- Position mode indicator -->
    <div v-if="positionBadge" class="olo-position-badge">{{ positionBadge }}</div>

    <!-- Tile content -->
    <div :class="{ 'olo-cell-content-z': hasBgImage || hasOverlay }">
      <TileBase :tile="tile" />
    </div>

    <!-- Dynamic hover styles -->
    <component v-if="hoverCssTag" is="style" v-text="hoverCssTag" />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useBuilderStore } from '@/stores/builder';
import { useTilesStore } from '@/stores/tiles';
import TileBase from '@/components/Tiles/TileBase.vue';
import { useBackgroundStyle } from '@/composables/useBackgroundStyle';
import { getShadowValue } from '@/composables/useShadowMap';
import { rv } from '@/composables/useResponsiveValue';

const props = defineProps({
  tile: { type: Object, required: true },
});

const builderStore = useBuilderStore();
const tilesStore = useTilesStore();

const isSelected = computed(() => builderStore.selectedTileId === props.tile.id);

// Responsive visibility: check if tile is hidden in current viewMode
const isHiddenInViewport = computed(() => {
  const adv = props.tile.advanced || {};
  const mode = builderStore.viewMode;
  if (mode === 'desktop' && adv.visible_desktop === false) return true;
  if (mode === 'tablet_landscape' && adv.visible_tablet_landscape === false) return true;
  if (mode === 'tablet' && adv.visible_tablet === false) return true;
  if (mode === 'mobile_landscape' && adv.visible_mobile_landscape === false) return true;
  if (mode === 'mobile' && adv.visible_mobile === false) return true;
  return false;
});

// Position mode badge for non-static tiles
const positionBadge = computed(() => {
  const mode = (props.tile.advanced || {}).position_mode;
  if (!mode || mode === 'static') return '';
  const labels = { relative: 'REL', absolute: 'ABS', fixed: 'FIXED', sticky: 'STICKY' };
  return labels[mode] || '';
});

// Background composable — elimina logica duplicata
const { effectiveBg, hasBgImage, hasOverlay, bgImageStyle, overlayStyle, bgInlineStyle } = useBackgroundStyle(() => props.tile);

const cellClasses = computed(() => {
  const classes = ['olo-grid-cell'];
  if (isSelected.value) classes.push('olo-grid-cell--selected');
  if (props.tile.style?.full_width) classes.push('olo-grid-cell--fullwidth');
  if (hasBgImage.value || hasOverlay.value) classes.push('olo-grid-cell--has-bg');
  if (isHiddenInViewport.value) classes.push('olo-grid-cell--hidden-vp');
  const pm = (props.tile.advanced || {}).position_mode;
  if (pm && pm !== 'static' && pm !== 'relative') classes.push('olo-grid-cell--positioned');
  return classes;
});

const cellStyle = computed(() => {
  const s = props.tile.style || {};
  const adv = props.tile.advanced || {};
  const mode = builderStore.viewMode;
  const style = {};

  // Popup tiles (non-fullwidth): inline-block so they sit side by side
  if (props.tile.type === 'popup' && !props.tile.settings?.button_fullwidth) {
    style.display = 'inline-block';
  }

  // Display override (responsive visibility via CSS display)
  const dispVal = rv(s, 'display', undefined, mode);
  if (dispVal) style.display = dispVal;

  // Margin (responsive)
  const mt = rv(s, 'margin_top', undefined, mode);
  const mr = rv(s, 'margin_right', undefined, mode);
  const mb = rv(s, 'margin_bottom', undefined, mode);
  const ml = rv(s, 'margin_left', undefined, mode);
  if (mt) style.marginTop = `${mt}px`;
  if (mr) style.marginRight = `${mr}px`;
  if (mb) style.marginBottom = `${mb}px`;
  if (ml) style.marginLeft = `${ml}px`;

  // Padding (responsive)
  const pt = rv(s, 'padding_top', undefined, mode);
  const pr = rv(s, 'padding_right', undefined, mode);
  const pb = rv(s, 'padding_bottom', undefined, mode);
  const pl = rv(s, 'padding_left', undefined, mode);
  if (pt) style.paddingTop = `${pt}px`;
  if (pr) style.paddingRight = `${pr}px`;
  if (pb) style.paddingBottom = `${pb}px`;
  if (pl) style.paddingLeft = `${pl}px`;

  // Background (solid & gradient via composable; image handled via bgImageStyle layer)
  Object.assign(style, bgInlineStyle.value);

  // Border radius (responsive)
  const brVal = rv(s, 'border_radius', undefined, mode);
  if (brVal) {
    if (typeof brVal === 'object') {
      style.borderRadius = `${brVal.tl||0}px ${brVal.tr||0}px ${brVal.br||0}px ${brVal.bl||0}px`;
    } else {
      style.borderRadius = `${brVal}px`;
    }
  }

  // Border
  if (s.border_width && parseInt(s.border_width) > 0) {
    style.borderWidth = `${s.border_width}px`;
    style.borderStyle = s.border_style || 'solid';
    style.borderColor = s.border_color || '#374151';
  }

  // Shadow
  if (s.shadow && s.shadow !== 'none') {
    const sv = getShadowValue(s);
    if (sv !== 'none') style.boxShadow = sv;
  }

  // Opacity
  if (s.opacity && parseInt(s.opacity) < 100) {
    style.opacity = parseInt(s.opacity) / 100;
  }

  // Width override (responsive)
  const wVal = rv(s, 'width', undefined, mode);
  if (wVal) style.width = isFinite(wVal) ? `${wVal}px` : wVal;

  // Height override (responsive)
  const hVal = rv(s, 'height', undefined, mode);
  if (hVal) style.height = isFinite(hVal) ? `${hVal}px` : hVal;

  // Font size (responsive — applied at cell level for inheritance)
  const fsVal = rv(s, 'font_size', undefined, mode);
  if (fsVal) style.fontSize = `${fsVal}px`;

  // Text align (responsive)
  const taVal = rv(s, 'text_align', undefined, mode);
  if (taVal) style.textAlign = taVal;

  // Flex container (responsive)
  const fdVal = rv(s, 'flex_direction', undefined, mode);
  const fjVal = rv(s, 'flex_justify', undefined, mode);
  const faVal = rv(s, 'flex_align', undefined, mode);
  const fwVal = rv(s, 'flex_wrap', undefined, mode);
  const fgVal = rv(s, 'flex_gap', undefined, mode);
  if (fdVal || fjVal || faVal || fwVal || fgVal) {
    style.display = 'flex';
    if (fdVal) style.flexDirection = fdVal;
    if (fjVal) style.justifyContent = fjVal;
    if (faVal) style.alignItems = faVal;
    if (fwVal) style.flexWrap = fwVal;
    if (fgVal) style.gap = `${fgVal}px`;
  }

  // Gap (responsive) — standalone gap for non-flex contexts
  const gapVal = rv(s, 'gap', undefined, mode);
  if (gapVal && !fgVal) style.gap = `${gapVal}px`;

  // Transform (responsive)
  const transforms = [];
  const tScale = rv(s, 'transform_scale', undefined, mode);
  const tTx = rv(s, 'transform_translateX', undefined, mode);
  const tTy = rv(s, 'transform_translateY', undefined, mode);
  if (tScale != null && tScale !== '' && tScale != 1) transforms.push(`scale(${tScale})`);
  if (tTx) transforms.push(`translateX(${tTx}px)`);
  if (tTy) transforms.push(`translateY(${tTy}px)`);
  if (transforms.length) style.transform = transforms.join(' ');

  // Full-width: span full grid
  if (s.full_width) {
    style.gridColumn = '1 / -1';
  }

  // Positioning — in the builder, keep tiles in flow (no absolute/fixed/sticky).
  // Only apply relative offset and width. Position is shown via a badge.
  if (adv.position_mode && adv.position_mode !== 'static') {
    const posMode = adv.position_mode;
    if (posMode === 'relative') {
      style.position = 'relative';
      if (adv.position_top) style.top = adv.position_top.toString().match(/^\d+$/) ? `${adv.position_top}px` : adv.position_top;
      if (adv.position_left) style.left = adv.position_left.toString().match(/^\d+$/) ? `${adv.position_left}px` : adv.position_left;
    }
    if (adv.position_width) style.width = adv.position_width.toString().match(/^\d+$/) ? `${adv.position_width}px` : adv.position_width;
  }

  // Mask shape
  const maskVal = s.mask || s.mask_type || '';
  if (maskVal && maskVal !== 'none') {
    const svgMap = {
      circle: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="black"/></svg>',
      triangle: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><polygon points="50,0 100,100 0,100" fill="black"/></svg>',
      diamond: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><polygon points="50,0 100,50 50,100 0,50" fill="black"/></svg>',
      hexagon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><polygon points="50,0 93.3,25 93.3,75 50,100 6.7,75 6.7,25" fill="black"/></svg>',
      star: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><polygon points="50,0 61,35 98,35 68,57 79,91 50,70 21,91 32,57 2,35 39,35" fill="black"/></svg>',
      blob: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><path d="M44.7,-76.4C58.8,-69.2,71.8,-59.1,79.6,-45.8C87.4,-32.6,90,-16.3,88.5,-0.9C87,14.6,81.4,29.1,73.1,41.8C64.8,54.4,53.8,65.2,40.8,72.4C27.8,79.6,12.8,83.3,-1.6,86.1C-16,88.8,-32,90.6,-44.6,83.7C-57.2,76.8,-66.4,61.2,-74.2,45.7C-82,30.2,-88.4,14.8,-87.9,0.3C-87.4,-14.2,-80,-28.5,-71,-40.7C-62,-53,-51.4,-63.3,-39,-71.1C-26.6,-78.9,-12.3,-84.2,1.8,-87.4C15.8,-90.6,30.6,-83.6,44.7,-76.4Z" transform="translate(100 100)" fill="black"/></svg>',
      wave: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path d="M0,30 Q25,0 50,30 T100,30 L100,100 L0,100 Z" fill="black"/></svg>',
    };
    const svg = svgMap[maskVal];
    if (svg) {
      const url = `url("data:image/svg+xml,${encodeURIComponent(svg)}")`;
      style.WebkitMaskImage = url;
      style.maskImage = url;
      style.WebkitMaskSize = 'contain';
      style.maskSize = 'contain';
      style.WebkitMaskPosition = 'center';
      style.maskPosition = 'center';
      style.WebkitMaskRepeat = 'no-repeat';
      style.maskRepeat = 'no-repeat';
    }
  }

  // Custom CSS from advanced tab
  if (adv.custom_css) {
    const pairs = adv.custom_css.split(';').filter(Boolean);
    for (const pair of pairs) {
      const [prop, val] = pair.split(':').map((x) => x.trim());
      if (prop && val) {
        const camelProp = prop.replace(/-([a-z])/g, (_, c) => c.toUpperCase());
        style[camelProp] = val;
      }
    }
  }

  return style;
});

const hoverCssTag = computed(() => {
  const hover = props.tile.style?.hover;
  if (!hover) return '';

  const trans = props.tile.style?.transition || { duration: 300, easing: 'ease' };
  const sel = `[data-tile-id="${props.tile.id}"]`;
  const isFullWidth = !!props.tile.style?.full_width;

  // Collect hover declarations
  const hoverDecls = [];
  if (hover.bg_color) hoverDecls.push(`background-color: ${hover.bg_color}`);
  if (hover.text_color) hoverDecls.push(`color: ${hover.text_color}`);
  if (hover.border_color) hoverDecls.push(`border-color: ${hover.border_color}`);
  if (hover.border_radius != null && hover.border_radius !== '') {
    const br = hover.border_radius;
    if (typeof br === 'object') {
      hoverDecls.push(`border-radius: ${br.tl||0}px ${br.tr||0}px ${br.br||0}px ${br.bl||0}px`);
    } else {
      hoverDecls.push(`border-radius: ${br}px`);
    }
  }
  if (hover.shadow) {
    const val = getShadowValue(hover, 'shadow');
    if (val && val !== 'none') hoverDecls.push(`box-shadow: ${val}`);
    else if (hover.shadow === 'none') hoverDecls.push('box-shadow: none');
  }
  if (hover.opacity != null) hoverDecls.push(`opacity: ${hover.opacity / 100}`);

  // Transform
  const transforms = [];
  if (isFullWidth) transforms.push('translateX(-50%)');
  if (hover.transform_scale != null) transforms.push(`scale(${hover.transform_scale})`);
  if (hover.transform_translateX != null) transforms.push(`translateX(${hover.transform_translateX}px)`);
  if (hover.transform_translateY != null) transforms.push(`translateY(${hover.transform_translateY}px)`);
  if (hover.transform_rotate != null) transforms.push(`rotate(${hover.transform_rotate}deg)`);
  if (hover.transform_skewX) transforms.push(`skewX(${hover.transform_skewX}deg)`);
  if (hover.transform_skewY) transforms.push(`skewY(${hover.transform_skewY}deg)`);
  if (transforms.length) hoverDecls.push(`transform: ${transforms.join(' ')}`);

  // CSS filters
  const filters = [];
  if (hover.filter_blur) filters.push(`blur(${hover.filter_blur}px)`);
  if (hover.filter_brightness && hover.filter_brightness != 100) filters.push(`brightness(${hover.filter_brightness}%)`);
  if (hover.filter_contrast && hover.filter_contrast != 100) filters.push(`contrast(${hover.filter_contrast}%)`);
  if (hover.filter_saturate && hover.filter_saturate != 100) filters.push(`saturate(${hover.filter_saturate}%)`);
  if (hover.filter_grayscale) filters.push(`grayscale(${hover.filter_grayscale}%)`);
  if (hover.filter_sepia) filters.push(`sepia(${hover.filter_sepia}%)`);
  if (filters.length) hoverDecls.push(`filter: ${filters.join(' ')}`);

  if (!hoverDecls.length) return '';

  // Transition properties
  const dur = trans.duration ?? 300;
  const ease = trans.easing || 'ease';
  const transProps = ['color', 'background-color', 'border-color', 'border-radius', 'box-shadow', 'opacity', 'transform', 'filter'];
  const transVal = transProps.map(p => `${p} ${dur}ms ${ease}`).join(', ');

  return `${sel} { transition: ${transVal}; } ${sel}:hover { ${hoverDecls.join('; ')}; }`;
});

function selectTile() {
  if (builderStore.selectedTileId === props.tile.id) return;
  builderStore.selectTile(props.tile.id);
}

const emit = defineEmits(['contextmenu']);
function onContextMenu(event) {
  emit('contextmenu', event, props.tile.id);
}

function duplicate() {
  tilesStore.duplicateTile(props.tile.id);
  builderStore.isDirty = true;
}

function remove() {
  tilesStore.removeTile(props.tile.id);
  builderStore.deselectTile();
  builderStore.isDirty = true;
}
</script>

<style scoped>
.olo-grid-cell--has-bg {
  position: relative;
  overflow: hidden;
}
.olo-cell-bg-layer {
  pointer-events: none;
}
.olo-cell-overlay-layer {
  pointer-events: none;
}
.olo-cell-content-z {
  position: relative;
  z-index: 2;
}
.olo-grid-cell--fullwidth {
  border-left: 3px solid var(--olo-color-primary, #6366F1);
}
.olo-fullwidth-badge {
  position: absolute;
  top: 2px;
  left: 6px;
  background: var(--olo-color-primary, #6366F1);
  color: #fff;
  font-size: 9px;
  font-weight: 700;
  padding: 1px 4px;
  border-radius: 3px;
  z-index: 10;
  letter-spacing: 0.5px;
}

/* Global widget badge */
.olo-global-badge {
  position: absolute;
  top: 2px;
  left: 40px;
  background: #D97706;
  color: #fff;
  font-size: 9px;
  font-weight: 700;
  padding: 1px 4px;
  border-radius: 3px;
  z-index: 10;
  letter-spacing: 0.5px;
}

/* Hidden in current viewport */
.olo-grid-cell--hidden-vp {
  opacity: 0.25;
  border: 2px dashed #f59e0b !important;
  position: relative;
}
.olo-hidden-vp-badge {
  position: absolute;
  top: 2px;
  right: 6px;
  background: #f59e0b;
  color: #fff;
  padding: 2px 4px;
  border-radius: 3px;
  z-index: 10;
  display: flex;
  align-items: center;
}

/* Force positioned tiles (fixed/absolute/sticky) to stay in flow in builder */
.olo-grid-cell--positioned {
  position: static !important;
  top: auto !important;
  left: auto !important;
  right: auto !important;
  bottom: auto !important;
  z-index: auto !important;
  border: 2px dashed #8B5CF6 !important;
}

/* Position mode badge */
.olo-position-badge {
  position: absolute;
  bottom: 2px;
  left: 6px;
  background: #8B5CF6;
  color: #fff;
  font-size: 9px;
  font-weight: 700;
  padding: 1px 4px;
  border-radius: 3px;
  z-index: 10;
  letter-spacing: 0.5px;
}
</style>
