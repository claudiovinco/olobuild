<template>
  <div
    :class="cellClasses"
    :style="cellStyle"
    :id="tile.advanced?.html_id || undefined"
    :data-tile-id="tile.id"
    @click.stop="selectTile"
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
    <!-- Hidden in viewport indicator -->
    <div v-if="isHiddenInViewport" class="olo-hidden-vp-badge">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" x2="23" y1="1" y2="23"/></svg>
    </div>

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
  if (mode === 'tablet' && adv.visible_tablet === false) return true;
  if (mode === 'mobile' && adv.visible_mobile === false) return true;
  return false;
});

const shadowMap = {
  none: 'none',
  sm: '0 1px 2px 0 rgba(0,0,0,0.05)',
  md: '0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1)',
  lg: '0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1)',
  xl: '0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1)',
};

// Resolve effective background
const effectiveBg = computed(() => {
  const s = props.tile.style || {};
  if (s.bg && s.bg.type && s.bg.type !== 'none') return s.bg;
  if (s.bg_color) return { type: 'solid', color: s.bg_color };
  return { type: 'none' };
});

const hasBgImage = computed(() => effectiveBg.value.type === 'image' && effectiveBg.value.image_url);

const hasOverlay = computed(() => {
  const bg = effectiveBg.value;
  return bg.type !== 'none' && bg.overlay_opacity && bg.overlay_opacity > 0;
});

const bgImageStyle = computed(() => {
  const bg = effectiveBg.value;
  return {
    position: 'absolute',
    inset: '0',
    zIndex: 0,
    backgroundImage: `url(${bg.image_url})`,
    backgroundSize: bg.image_size || 'cover',
    backgroundPosition: bg.image_position || 'center center',
    backgroundRepeat: 'no-repeat',
  };
});

const overlayStyle = computed(() => {
  const bg = effectiveBg.value;
  return {
    position: 'absolute',
    inset: '0',
    zIndex: 1,
    pointerEvents: 'none',
    backgroundColor: bg.overlay_color || '#000000',
    opacity: (bg.overlay_opacity || 0) / 100,
  };
});

const cellClasses = computed(() => {
  const classes = ['olo-grid-cell'];
  if (isSelected.value) classes.push('olo-grid-cell--selected');
  if (props.tile.style?.full_width) classes.push('olo-grid-cell--fullwidth');
  if (hasBgImage.value || hasOverlay.value) classes.push('olo-grid-cell--has-bg');
  if (isHiddenInViewport.value) classes.push('olo-grid-cell--hidden-vp');
  return classes;
});

const cellStyle = computed(() => {
  const s = props.tile.style || {};
  const adv = props.tile.advanced || {};
  const bg = effectiveBg.value;
  const style = {};

  // Popup tiles (non-fullwidth): inline-block so they sit side by side
  if (props.tile.type === 'popup' && !props.tile.settings?.button_fullwidth) {
    style.display = 'inline-block';
  }

  // Margin
  if (s.margin_top) style.marginTop = `${s.margin_top}px`;
  if (s.margin_right) style.marginRight = `${s.margin_right}px`;
  if (s.margin_bottom) style.marginBottom = `${s.margin_bottom}px`;
  if (s.margin_left) style.marginLeft = `${s.margin_left}px`;

  // Padding
  if (s.padding_top) style.paddingTop = `${s.padding_top}px`;
  if (s.padding_right) style.paddingRight = `${s.padding_right}px`;
  if (s.padding_bottom) style.paddingBottom = `${s.padding_bottom}px`;
  if (s.padding_left) style.paddingLeft = `${s.padding_left}px`;

  // Background (solid & gradient applied to main div; image via separate layer)
  if (bg.type === 'solid') {
    const color = bg.color || '';
    const opacity = bg.color_opacity ?? 100;
    if (color && opacity < 100) {
      const h = color.replace('#', '');
      const r = parseInt(h.substring(0, 2), 16) || 0;
      const g = parseInt(h.substring(2, 4), 16) || 0;
      const b = parseInt(h.substring(4, 6), 16) || 0;
      style.backgroundColor = `rgba(${r}, ${g}, ${b}, ${opacity / 100})`;
    } else {
      style.backgroundColor = color;
    }
  } else if (bg.type === 'gradient') {
    style.background = `linear-gradient(${bg.gradient_angle || 180}deg, ${bg.gradient_from || '#ffffff'}, ${bg.gradient_to || '#000000'})`;
  }
  // Image bg handled via bgImageStyle layer

  // Border radius
  if (s.border_radius) style.borderRadius = `${s.border_radius}px`;

  // Border
  if (s.border_width && parseInt(s.border_width) > 0) {
    style.borderWidth = `${s.border_width}px`;
    style.borderStyle = s.border_style || 'solid';
    style.borderColor = s.border_color || '#374151';
  }

  // Shadow
  if (s.shadow && s.shadow !== 'none') {
    style.boxShadow = shadowMap[s.shadow] || 'none';
  }

  // Opacity
  if (s.opacity && parseInt(s.opacity) < 100) {
    style.opacity = parseInt(s.opacity) / 100;
  }

  // Full-width: span full grid
  if (s.full_width) {
    style.gridColumn = '1 / -1';
  }

  // Positioning
  if (adv.position_mode && adv.position_mode !== 'static') {
    style.position = adv.position_mode;
    if (adv.position_top) style.top = adv.position_top.toString().match(/^\d+$/) ? `${adv.position_top}px` : adv.position_top;
    if (adv.position_left) style.left = adv.position_left.toString().match(/^\d+$/) ? `${adv.position_left}px` : adv.position_left;
    if (adv.position_bottom) style.bottom = adv.position_bottom.toString().match(/^\d+$/) ? `${adv.position_bottom}px` : adv.position_bottom;
    if (adv.position_right) style.right = adv.position_right.toString().match(/^\d+$/) ? `${adv.position_right}px` : adv.position_right;
    if (adv.position_width) style.width = adv.position_width.toString().match(/^\d+$/) ? `${adv.position_width}px` : adv.position_width;
    if (adv.position_zindex !== '' && adv.position_zindex != null) style.zIndex = adv.position_zindex;
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
  if (hover.border_color) hoverDecls.push(`border-color: ${hover.border_color}`);
  if (hover.shadow) {
    const val = hover.shadow === 'none' ? 'none' : (shadowMap[hover.shadow] || '');
    if (val) hoverDecls.push(`box-shadow: ${val}`);
  }
  if (hover.opacity != null) hoverDecls.push(`opacity: ${hover.opacity / 100}`);

  // Transform
  const transforms = [];
  if (isFullWidth) transforms.push('translateX(-50%)');
  if (hover.transform_scale != null) transforms.push(`scale(${hover.transform_scale})`);
  if (hover.transform_translateY != null) transforms.push(`translateY(${hover.transform_translateY}px)`);
  if (hover.transform_rotate != null) transforms.push(`rotate(${hover.transform_rotate}deg)`);
  if (transforms.length) hoverDecls.push(`transform: ${transforms.join(' ')}`);

  if (!hoverDecls.length) return '';

  // Transition properties
  const dur = trans.duration ?? 300;
  const ease = trans.easing || 'ease';
  const transProps = ['background-color', 'border-color', 'box-shadow', 'opacity', 'transform'];
  const transVal = transProps.map(p => `${p} ${dur}ms ${ease}`).join(', ');

  return `${sel} { transition: ${transVal}; } ${sel}:hover { ${hoverDecls.join('; ')}; }`;
});

function selectTile() {
  builderStore.selectTile(props.tile.id);
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
</style>
