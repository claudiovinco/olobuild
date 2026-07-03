<template>
  <div
    ref="cellRef"
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
      <button :title="t('Sposta')" class="move">&#x2630;</button>
      <button :title="t('Duplica')" @click.stop="duplicate">&#x2398;</button>
      <button :title="t('Elimina')" class="delete" @click.stop="remove">&#x2715;</button>
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
    <div :class="{ 'olo-cell-content-z': hasBgImage || hasOverlay }" :style="contentFilterStyle">
      <TileBase :tile="tile" />
    </div>

    <!-- Container children slot (for floatingpanel etc.) -->
    <slot name="after" />

    <!-- Dynamic hover styles -->
    <component v-if="hoverCssTag" is="style" v-text="hoverCssTag" />
  </div>
</template>

<script setup>
import { computed, ref, onMounted, onBeforeUnmount, watch } from 'vue';
import { t } from '@/i18n';
import { useBuilderStore } from '@/stores/builder';
import { useTilesStore } from '@/stores/tiles';
import TileBase from '@/components/Tiles/TileBase.vue';
import { useBackgroundStyle } from '@/composables/useBackgroundStyle';
import { getShadowValue, getDropShadowValue } from '@/composables/useShadowMap';
import { rv } from '@/composables/useResponsiveValue';
import { useTileActions } from '@/composables/useTileActions';

const props = defineProps({
  tile: { type: Object, required: true },
});

const builderStore = useBuilderStore();
const tilesStore = useTilesStore();
const { removeTiles } = useTileActions();

// ── Anteprima "Spotlight cursore (torcia)" nel canvas builder ──────────────
// Effetto advanced cursor_spotlight: nel frontend è gestito dal runtime di
// class-frontend-renderer; qui lo replichiamo per-elemento così è WYSIWYG.
const cellRef = ref(null);
let _spotDisc = null, _spotH = null;
function teardownSpotlight() {
  const host = cellRef.value;
  if (host && _spotH) {
    host.removeEventListener('pointerenter', _spotH.enter);
    host.removeEventListener('pointermove', _spotH.move);
    host.removeEventListener('pointerleave', _spotH.leave);
  }
  if (_spotDisc && _spotDisc.parentNode) _spotDisc.parentNode.removeChild(_spotDisc);
  _spotDisc = null; _spotH = null;
}
function setupSpotlight() {
  teardownSpotlight();
  const host = cellRef.value, adv = props.tile.advanced || {};
  if (!host || !adv.cursor_spotlight) return;
  const size = +adv.cursor_spotlight_size || 300;
  const soft = adv.cursor_spotlight_softness != null ? +adv.cursor_spotlight_softness : 40;
  const blend = adv.cursor_spotlight_blend || 'difference';
  const color = adv.cursor_spotlight_color || '#ffffff';
  const ease = (+adv.cursor_spotlight_easing || 22) / 100;
  const inner = Math.max(0, 100 - soft), half = size / 2;
  // Falloff a curva, IDENTICO al runtime frontend PHP: una rampa lineare verso
  // transparent lascia un bordo percepibile (Mach band) anche a morbidezza 100.
  let hx = String(color).replace('#', ''); if (hx.length === 3) hx = hx[0]+hx[0]+hx[1]+hx[1]+hx[2]+hx[2];
  let nn = parseInt(hx, 16); if (isNaN(nn)) nn = 16777215;
  const rgb = (nn>>16&255) + ',' + (nn>>8&255) + ',' + (nn&255);
  const C = (a) => 'rgba(' + rgb + ',' + a + ')';
  // Falloff "gaussiano": il nucleo a piena alfa = inner% si riduce con la morbidezza, poi
  // una coda ease-out (esponente che cresce con la morbidezza) arriva a ~0 BEN PRIMA del bordo
  // del disco → nessun salto/anello percepibile a morbidezza 100. 10 stop = curva fluida.
  const core = inner / 100;
  const exp = 2.0 + (soft / 100) * 1.8;
  let stops = C(1) + ' 0%';
  for (let i = 1; i <= 10; i++) {
    const p = i / 10;
    let a = p <= core ? 1 : Math.pow(1 - (p - core) / (1 - core), exp);
    if (a < 0.004) a = 0;
    stops += ', ' + C(+a.toFixed(3)) + ' ' + (p * 100).toFixed(1) + '%';
  }
  const grad = 'radial-gradient(circle, ' + stops + ')';
  let disc = null, tx = 0, ty = 0, cx = 0, cy = 0, running = false, inside = false;
  function build() {
    if (disc) return;
    if (getComputedStyle(host).position === 'static') host.style.position = 'relative';
    host.style.overflow = 'hidden'; host.style.isolation = 'isolate';
    disc = document.createElement('div');
    disc.setAttribute('aria-hidden', 'true');
    disc.style.cssText = 'position:absolute;top:0;left:0;z-index:99999;width:' + size + 'px;height:' + size + 'px;border-radius:50%;pointer-events:none;will-change:transform,opacity;opacity:0;transition:opacity .2s ease;background:' + grad + ';mix-blend-mode:' + blend + ';';
    host.appendChild(disc); _spotDisc = disc;
  }
  function frame() {
    cx += (tx - cx) * ease; cy += (ty - cy) * ease;
    if (disc) disc.style.transform = 'translate(' + (cx - half) + 'px,' + (cy - half) + 'px)';
    if (inside || Math.abs(tx - cx) > 0.5 || Math.abs(ty - cy) > 0.5) { requestAnimationFrame(frame); } else { running = false; }
  }
  function start() { if (!running) { running = true; requestAnimationFrame(frame); } }
  const enter = (e) => { if (e.pointerType === 'touch') return; build(); const r = host.getBoundingClientRect(); tx = cx = e.clientX - r.left; ty = cy = e.clientY - r.top; inside = true; if (disc) disc.style.opacity = '1'; start(); };
  const move = (e) => { if (e.pointerType === 'touch' || !disc) return; const r = host.getBoundingClientRect(); tx = e.clientX - r.left; ty = e.clientY - r.top; start(); };
  const leave = () => { inside = false; if (disc) disc.style.opacity = '0'; };
  host.addEventListener('pointerenter', enter);
  host.addEventListener('pointermove', move);
  host.addEventListener('pointerleave', leave);
  _spotH = { enter, move, leave };
}
onMounted(setupSpotlight);
onBeforeUnmount(teardownSpotlight);
watch(() => {
  const a = props.tile.advanced || {};
  return [a.cursor_spotlight, a.cursor_spotlight_blend, a.cursor_spotlight_color, a.cursor_spotlight_size, a.cursor_spotlight_softness, a.cursor_spotlight_easing].join('|');
}, setupSpotlight);

// ── Anteprima "Tilt 3D" e "Segui cursore" nel canvas builder ────────────────
// Stessa fisica del runtime frontend (gradi/spostamento clampati), ma confinata
// all'hover sull'elemento: nel frontend il track segue il mouse su tutta la
// pagina, nel canvas disturberebbe l'editing. Sospesa durante il drag.
let _fxH = null;
function teardownMouseFx() {
  const host = cellRef.value;
  if (host && _fxH) {
    host.removeEventListener('pointermove', _fxH.move);
    host.removeEventListener('pointerleave', _fxH.leave);
    _fxH.leave();
  }
  _fxH = null;
}
function setupMouseFx() {
  teardownMouseFx();
  const host = cellRef.value, adv = props.tile.advanced || {}, st = props.tile.settings || {};
  if (!host) return;
  // Come il frontend: le chiavi possono stare in advanced (pannello Effetti
  // mouse) o in settings (styleField _shared).
  const tiltOn = adv.mouse_tilt === true || st.mouse_tilt === true;
  const trackOn = adv.mouse_track === true || st.mouse_track === true;
  if (!tiltOn && !trackOn) return;
  const intensity = parseInt(adv.mouse_tilt_intensity ?? st.mouse_tilt_intensity) || 15;
  const speed = parseInt(adv.mouse_track_speed ?? st.mouse_track_speed) || 3;
  const items = (adv.mouse_tilt_target ?? st.mouse_tilt_target) === 'items';
  const tiltOf = (el, e) => {
    const r = el.getBoundingClientRect();
    let rx = -((e.clientY - (r.top + r.height / 2)) / (r.height / 2)) * intensity;
    let ry = ((e.clientX - (r.left + r.width / 2)) / (r.width / 2)) * intensity;
    rx = Math.max(-intensity, Math.min(intensity, rx));
    ry = Math.max(-intensity, Math.min(intensity, ry));
    return 'perspective(1000px) rotateX(' + rx + 'deg) rotateY(' + ry + 'deg)';
  };
  const move = (e) => {
    if (e.pointerType === 'touch' || document.body.classList.contains('olo-dragging')) return;
    const parts = [];
    if (tiltOn && !items) parts.push(tiltOf(host, e));
    if (trackOn) {
      const r = host.getBoundingClientRect();
      const max = speed * 10;
      const tx = Math.max(-max, Math.min(max, ((e.clientX - (r.left + r.width / 2)) / r.width) * max));
      const ty = Math.max(-max, Math.min(max, ((e.clientY - (r.top + r.height / 2)) / r.height) * max));
      parts.push('translate(' + tx + 'px,' + ty + 'px)');
    }
    if (parts.length) {
      host.style.transition = 'transform .15s ease-out';
      host.style.transform = parts.join(' ');
    }
    if (tiltOn && items) {
      host.querySelectorAll('img, video').forEach((it) => {
        const r = it.getBoundingClientRect();
        const inside = e.clientX >= r.left && e.clientX <= r.right && e.clientY >= r.top && e.clientY <= r.bottom;
        it.style.transition = 'transform .15s ease-out';
        it.style.transform = inside ? tiltOf(it, e) : '';
      });
    }
  };
  const leave = () => {
    if (cellRef.value) cellRef.value.style.transform = '';
    if (items && cellRef.value) cellRef.value.querySelectorAll('img, video').forEach((it) => { it.style.transform = ''; });
  };
  host.addEventListener('pointermove', move);
  host.addEventListener('pointerleave', leave);
  _fxH = { move, leave };
}
onMounted(setupMouseFx);
onBeforeUnmount(teardownMouseFx);
watch(() => {
  const a = props.tile.advanced || {}, s = props.tile.settings || {};
  return [a.mouse_tilt, s.mouse_tilt, a.mouse_tilt_intensity, s.mouse_tilt_intensity, a.mouse_tilt_target, s.mouse_tilt_target, a.mouse_track, s.mouse_track, a.mouse_track_speed, s.mouse_track_speed].join('|');
}, setupMouseFx);

const isSelected = computed(() => builderStore.selectedTileId === props.tile.id || builderStore.selectedTileIds.includes(props.tile.id));

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
  if (pm && pm !== 'static' && pm !== 'relative' && !builderStore.cleanMode) classes.push('olo-grid-cell--positioned');
  // Entrance animation preview nel builder canvas:
  //   olo-entrance-X applica le keyframe (definite in frontend.css)
  //   olo-visible le attiva (frontend lo fa via IntersectionObserver, qui sempre)
  //   Storage: tile.settings.entrance_animation (NON tile.advanced — coerente con PHP renderer)
  const entrance = props.tile.settings?.entrance_animation;
  if (entrance && entrance !== 'none') {
    classes.push('olo-entrance-' + entrance);
    classes.push('olo-visible');
  }
  return classes;
});

const cellStyle = computed(() => {
  const s = props.tile.style || {};
  const adv = props.tile.advanced || {};
  const set = props.tile.settings || {};
  const mode = builderStore.viewMode;
  const style = {};

  // Entrance animation custom controls (CSS vars consumed by frontend.css rules)
  const eAnim = set.entrance_animation;
  if (eAnim && eAnim !== 'none') {
    const eDur = parseInt(set.entrance_duration);
    if (eDur > 0) style['--olo-e-dur'] = Math.max(50, Math.min(5000, eDur)) + 'ms';
    const eDelay = parseInt(set.entrance_delay);
    if (eDelay > 0) style['--olo-e-delay'] = Math.min(5000, eDelay) + 'ms';
    const eEase = set.entrance_easing;
    if (eEase && eEase !== 'auto' && /^(linear|ease|ease-in|ease-out|ease-in-out|cubic-bezier\([0-9.,\s\-]+\))$/.test(eEase)) {
      style['--olo-e-ease'] = eEase;
    }
    const eInt = parseFloat(set.entrance_intensity);
    if (eInt > 0 && Math.abs(eInt - 1) > 0.01) {
      style['--olo-e-int'] = Math.max(0.1, Math.min(5, eInt));
    }
  }

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

  // Text color (usato dai preset stilistici: i discendenti ereditano)
  if (s.text_color) style.color = s.text_color;

  // Border radius (responsive) — il check !== undefined gestisce anche il valore 0
  const brVal = rv(s, 'border_radius', undefined, mode);
  if (brVal !== undefined && brVal !== null && brVal !== '') {
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

  // Shadow — box-shadow solo per elementi con sfondo (segue border-radius del div).
  // Per elementi trasparenti, l'ombra va applicata sul div contenuto (contentFilterStyle).
  if (s.shadow && s.shadow !== 'none') {
    const cellHasBg = !!(bgInlineStyle.value?.backgroundColor || hasBgImage.value);
    if (cellHasBg) {
      const sv = getShadowValue(s);
      if (sv !== 'none') style.boxShadow = sv;
    }
    // Altrimenti: drop-shadow via contentFilterStyle (applicato al div contenuto)
  }

  // Opacity (responsive)
  const opVal = rv(s, 'opacity', undefined, mode);
  if (opVal != null && opVal !== '' && parseInt(opVal) < 100) {
    style.opacity = parseInt(opVal) / 100;
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
  const fcgVal = rv(s, 'flex_column_gap', undefined, mode);
  const frgVal = rv(s, 'flex_row_gap', undefined, mode);
  // backward compat: read old flex_gap if new fields are empty
  const fgLegacy = (!fcgVal && !frgVal) ? rv(s, 'flex_gap', undefined, mode) : undefined;
  const hasFlexGap = fcgVal || frgVal || fgLegacy;
  if (fdVal || fjVal || faVal || fwVal || hasFlexGap) {
    style.display = 'flex';
    if (fdVal) style.flexDirection = fdVal;
    if (fjVal) style.justifyContent = fjVal;
    if (faVal) style.alignItems = faVal;
    if (fwVal) style.flexWrap = fwVal;
    if (fgLegacy) {
      style.gap = `${fgLegacy}px`;
    } else {
      if (fcgVal) style.columnGap = `${fcgVal}px`;
      if (frgVal) style.rowGap = `${frgVal}px`;
    }
  }

  // Gap (responsive) — standalone gap for non-flex contexts
  const gapVal = rv(s, 'gap', undefined, mode);
  if (gapVal && !hasFlexGap) style.gap = `${gapVal}px`;

  // Transform (responsive). Desktop = oggetto style.transform; per-device = chiavi piatte
  // transform_<prop>_<bp> (fallback all'oggetto desktop). Include rotate/skew, non solo scale/translate.
  const tBase = (s.transform && typeof s.transform === 'object') ? s.transform : {};
  const transforms = [];
  const tScale = rv(s, 'transform_scale', tBase.scale, mode);
  const tTx = rv(s, 'transform_translateX', tBase.translateX, mode);
  const tTy = rv(s, 'transform_translateY', tBase.translateY, mode);
  const tRot = rv(s, 'transform_rotate', tBase.rotate, mode);
  const tSkx = rv(s, 'transform_skewX', tBase.skewX, mode);
  const tSky = rv(s, 'transform_skewY', tBase.skewY, mode);
  if (tScale != null && tScale !== '' && tScale != 1) transforms.push(`scale(${tScale})`);
  if (tTx) transforms.push(`translateX(${tTx}px)`);
  if (tTy) transforms.push(`translateY(${tTy}px)`);
  if (tRot) transforms.push(`rotate(${tRot}deg)`);
  if (tSkx) transforms.push(`skewX(${tSkx}deg)`);
  if (tSky) transforms.push(`skewY(${tSky}deg)`);
  if (transforms.length) style.transform = transforms.join(' ');

  // Text shadow (responsive)
  const tsH = rv(s, 'text_shadow_h', undefined, mode);
  const tsV = rv(s, 'text_shadow_v', undefined, mode);
  const tsB = rv(s, 'text_shadow_blur', undefined, mode);
  if (parseInt(tsH) || parseInt(tsV) || parseInt(tsB)) {
    const tsC = rv(s, 'text_shadow_color', 'rgba(0,0,0,0.3)', mode);
    style.textShadow = `${parseInt(tsH) || 0}px ${parseInt(tsV) || 0}px ${parseInt(tsB) || 0}px ${tsC}`;
  }

  // Backdrop filter (responsive)
  const bdParts = [];
  const bdBlur = rv(s, 'backdrop_blur', undefined, mode);
  const bdBr = rv(s, 'backdrop_brightness', undefined, mode);
  const bdSat = rv(s, 'backdrop_saturate', undefined, mode);
  if (parseInt(bdBlur)) bdParts.push(`blur(${parseInt(bdBlur)}px)`);
  if (bdBr != null && bdBr !== '' && parseInt(bdBr) !== 100) bdParts.push(`brightness(${parseInt(bdBr)}%)`);
  if (bdSat != null && bdSat !== '' && parseInt(bdSat) !== 100) bdParts.push(`saturate(${parseInt(bdSat)}%)`);
  if (bdParts.length) {
    style.backdropFilter = bdParts.join(' ');
    style.WebkitBackdropFilter = style.backdropFilter;
  }

  // Full-width: span full grid
  if (s.full_width) {
    style.gridColumn = '1 / -1';
  }

  // Positioning — in clean mode, apply real positioning (like frontend).
  // In normal mode, keep tiles in flow (no absolute/fixed/sticky).
  if (adv.position_mode && adv.position_mode !== 'static') {
    const posMode = adv.position_mode;
    const isClean = builderStore.cleanMode;
    if (isClean) {
      // Clean mode: apply real CSS positioning
      style.position = posMode;
      if (adv.position_top) style.top = adv.position_top.toString().match(/^\d+$/) ? `${adv.position_top}px` : adv.position_top;
      if (adv.position_right) style.right = adv.position_right.toString().match(/^\d+$/) ? `${adv.position_right}px` : adv.position_right;
      if (adv.position_bottom) style.bottom = adv.position_bottom.toString().match(/^\d+$/) ? `${adv.position_bottom}px` : adv.position_bottom;
      if (adv.position_left) style.left = adv.position_left.toString().match(/^\d+$/) ? `${adv.position_left}px` : adv.position_left;
      if (adv.position_zindex) style.zIndex = adv.position_zindex;
    } else {
      if (posMode === 'relative') {
        style.position = 'relative';
        if (adv.position_top) style.top = adv.position_top.toString().match(/^\d+$/) ? `${adv.position_top}px` : adv.position_top;
        if (adv.position_left) style.left = adv.position_left.toString().match(/^\d+$/) ? `${adv.position_left}px` : adv.position_left;
      }
    }
    if (adv.position_width) style.width = adv.position_width.toString().match(/^\d+$/) ? `${adv.position_width}px` : adv.position_width;
  }

  // Mask shape (da style o advanced)
  const maskVal = s.mask || s.mask_type || adv.mask_type || '';
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
      const maskSize = s.mask_size || adv.mask_size || 'contain';
      const maskPos = s.mask_position || adv.mask_position || 'center';
      const maskRep = s.mask_repeat || adv.mask_repeat || 'no-repeat';
      const url = `url("data:image/svg+xml,${encodeURIComponent(svg)}")`;
      style.WebkitMaskImage = url;
      style.maskImage = url;
      style.WebkitMaskSize = maskSize;
      style.maskSize = maskSize;
      style.WebkitMaskPosition = maskPos;
      style.maskPosition = maskPos;
      style.WebkitMaskRepeat = maskRep;
      style.maskRepeat = maskRep;
    } else if (maskVal === 'custom' && adv.mask_custom) {
      // Clip-path personalizzato (es. polygon, circle, ellipse)
      style.clipPath = adv.mask_custom;
      style.WebkitClipPath = adv.mask_custom;
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

// Stile per il div contenuto — applica drop-shadow quando la cella NON ha sfondo.
// drop-shadow sul div contenuto segue la forma visibile del tile (SVG stelle, icone, ecc.)
// perché toolbar e badge sono FUORI da questo div.
const contentFilterStyle = computed(() => {
  const s = props.tile.style || {};
  if (!s.shadow || s.shadow === 'none') return null;
  const cellHasBg = !!(bgInlineStyle.value?.backgroundColor || hasBgImage.value);
  if (cellHasBg) return null; // box-shadow è già sul div esterno
  const ds = getDropShadowValue(s);
  if (ds === 'none') return null;
  return { filter: ds };
});

const hoverCssTag = computed(() => {
  const hover = props.tile.style?.hover;
  if (!hover) return '';

  const s = props.tile.style || {};
  const advH = props.tile.advanced || {};
  const trans = s.transition || { duration: 300, easing: 'ease' };
  const sel = `[data-tile-id="${props.tile.id}"]`;
  const isFullWidth = !!s.full_width;
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

function selectTile(e) {
  // Ctrl/Cmd-click: aggiunge/toglie dalla multi-selezione (azioni bulk: Canc, Ctrl+D).
  if (e && (e.ctrlKey || e.metaKey)) {
    builderStore.toggleTileSelection(props.tile.id);
    return;
  }
  if (builderStore.selectedTileId === props.tile.id && builderStore.selectedTileIds.length <= 1) return;
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
  removeTiles(props.tile.id);
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
  border-left: 3px solid var(--olo-ui-accent, #e8622a);
}
.olo-fullwidth-badge {
  position: absolute;
  top: 2px;
  left: 6px;
  background: var(--olo-ui-accent, #e8622a);
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
