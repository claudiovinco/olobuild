<template>
  <div
    ref="canvasWrap"
    class="mb-flex-1 mb-flex mb-items-center mb-justify-center mb-bg-gray-950 mb-overflow-auto mb-p-4"
    @click.self="$emit('deselect')"
  >
    <div class="mb-shadow-2xl" :style="scaledWrapStyle" @click.self="$emit('deselect')">
      <div
        ref="canvasEl"
        class="mb-relative mb-overflow-hidden"
        :style="canvasStyle"
        @click.self="$emit('deselect')"
      >
      <!-- Guide magnetiche (1.4.1430): le linee dove il livello trascinato si è agganciato -->
      <div v-for="(g, i) in guide.v" :key="'gv' + i" class="mps-guida mps-guida--v" :style="{ left: g + '%' }" aria-hidden="true"></div>
      <div v-for="(g, i) in guide.h" :key="'gh' + i" class="mps-guida mps-guida--h" :style="{ top: g + '%' }" aria-hidden="true"></div>
      <!-- Global background (behind slide bg) -->
      <div v-if="globalBackground" class="mb-absolute mb-inset-0 mb-overflow-hidden mb-rounded" style="z-index:0;">
        <img v-if="globalBackground.type === 'image' && globalBackground.image" :src="globalBackground.image" class="mb-w-full mb-h-full mb-object-cover" draggable="false" />
        <video v-else-if="globalBackground.type === 'video' && globalBackground.video && !isYouTube(globalBackground.video)" :src="globalBackground.video" class="mb-w-full mb-h-full mb-object-cover" muted></video>
        <div v-else-if="globalBackground.type !== 'transparent'" class="mb-w-full mb-h-full" :style="globalBgStyle"></div>
      </div>

      <!-- Slide background -->
      <div class="mb-absolute mb-inset-0 mb-overflow-hidden mb-rounded" style="z-index:1;">
        <!-- Image -->
        <img
          v-if="slideBg.type === 'image' && slideBg.image"
          :src="slideBg.image"
          class="mb-w-full mb-h-full mb-object-cover"
          draggable="false"
        />
        <!-- Video (mp4 thumbnail) -->
        <video
          v-else-if="slideBg.type === 'video' && slideBg.video && !isYouTube(slideBg.video)"
          ref="bgVideo"
          :src="slideBg.video"
          :poster="slideBg.image || undefined"
          class="mb-w-full mb-h-full mb-object-cover"
          muted
          preload="metadata"
        ></video>
        <!-- Color / Gradient -->
        <div v-else class="mb-w-full mb-h-full" :style="bgColorStyle"></div>

        <!-- Overlay -->
        <div
          v-if="slideBg.overlayOpacity > 0"
          class="mb-absolute mb-inset-0"
          :style="{ background: velo(slideBg), opacity: slideBg.overlayOpacity || 0.3 }"
        ></div>
      </div>

      <!-- Layers -->
      <div
        v-for="layer in layers"
        v-show="!hiddenIds.has(layer.id)"
        :key="layer.id"
        :class="[
          'mb-absolute mb-cursor-move mb-select-none',
          layer.id === selectedLayerId ? 'mb-ring-2 mb-ring-primary-400 mb-ring-offset-1 mb-ring-offset-transparent' : '',
          layer.id !== selectedLayerId && selectedIds.has(layer.id) ? 'mps-canvas-insieme' : '',
          layer.initiallyHidden ? 'mps-canvas-initially-hidden' : ''
        ]"
        :data-mps-id="layer.id"
        :style="[layerStyle(layer), lockedIds.has(layer.id) ? { pointerEvents: 'none' } : null]"
        @pointerdown.stop="startDrag($event, layer)"
        @dblclick.stop="onDblClick(layer)"
      >
        <span v-if="layer.initiallyHidden" class="mps-canvas-hidden-badge" :title="t(&quot;Questo layer parte nascosto. Sarà mostrato solo se un altro layer con azione 'Toggle visibilità' lo prende come target.&quot;)">{{ t("nascosto all'avvio") }}</span>
        <!-- Text -->
        <component
          v-if="layer.type === 'text'"
          :is="layer.tag || 'h2'"
          :contenteditable="editingLayerId === layer.id"
          :style="textStyle(layer)"
          class="mb-m-0 mb-outline-none"
          @blur="onTextBlur($event, layer)"
          @keydown.enter.exact.prevent="$event.target.blur()"
          v-html="testoSicuro(layer.content)"
        ></component>

        <!-- Image -->
        <img
          v-else-if="layer.type === 'image'"
          :src="(layer.coloriDisegno && disegnoRicolorato(layer.imageSrc, layer.coloriDisegno, canvasWrap)) || layer.imageSrc || 'data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22120%22 height=%2280%22><rect fill=%22%23475569%22 width=%22120%22 height=%2280%22/><text x=%2260%22 y=%2244%22 text-anchor=%22middle%22 fill=%22%23cbd5e1%22 font-size=%2213%22>Immagine</text></svg>'"
          class="mb-w-full mb-h-full mb-pointer-events-none"
          :style="{ objectFit: layer.objectFit || 'cover', objectPosition: layer.objectPosition || 'center', borderRadius: (layer.borderRadius || 0) + 'px' }"
          draggable="false"
        />

        <!-- Button -->
        <span
          v-else-if="layer.type === 'button'"
          :style="buttonStyle(layer)"
          class="mb-inline-block mb-cursor-move"
        >{{ layer.content || t('Pulsante') }}</span>

        <!-- Icon -->
        <span
          v-else-if="layer.type === 'icon'"
          class="mps-canvas-icon"
          :style="iconStyle(layer)"
          v-html="getIconSvg(layer.iconName || 'star')"
        ></span>

        <!-- Shape -->
        <div
          v-else-if="layer.type === 'shape'"
          class="mb-w-full mb-h-full"
          :style="shapeStyle(layer)"
        ></div>

        <!-- Video -->
        <video
          v-else-if="layer.type === 'video' && layer.videoSrc && !isYouTube(layer.videoSrc)"
          :src="layer.videoSrc"
          class="mb-w-full mb-h-full mb-object-cover"
          :autoplay="layer.videoAutoplay"
          :muted="layer.videoMuted"
          :loop="layer.videoLoop"
          playsinline
          :style="{ borderRadius: (layer.borderRadius || 0) + 'px' }"
        ></video>
        <div
          v-else-if="layer.type === 'video' && layer.videoSrc && isYouTube(layer.videoSrc)"
          class="mb-w-full mb-h-full mb-flex mb-items-center mb-justify-center mb-bg-gray-800 mb-rounded"
          :style="{ borderRadius: (layer.borderRadius || 0) + 'px' }"
        >
          <span class="mb-text-4xl mb-text-white mb-opacity-60">&#9654;</span>
        </div>
        <div
          v-else-if="layer.type === 'video' && !layer.videoSrc"
          class="mb-w-full mb-h-full mb-flex mb-items-center mb-justify-center mb-bg-gray-700 mb-rounded"
        >
          <span class="mb-text-xs mb-text-gray-400">{{ t('Video') }}</span>
        </div>

        <!-- Resize handles (selected only) -->
        <template v-if="layer.id === selectedLayerId">
          <div
            v-for="handle in resizeHandles"
            :key="handle"
            :class="'mps-handle mps-handle-' + handle"
            @pointerdown.stop="startResize($event, layer, handle)"
          ></div>
        </template>
      </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import iconsSvg from './uikitIconsSvg.js';
import { interpolateAtTime } from './timelineUtils.js';
import { useStylesStore } from '@/stores/styles';
import { t } from '@/i18n';
import { statoSM, statoVale, stileComandi, testoSicuro } from './livelloUtils.js';
import { disegnoRicolorato } from './disegni.js';

const props = defineProps({
  slide: { type: Object, required: true },
  sliderHeight: { type: Number, default: 600 },
  globalBackground: { type: Object, default: null },
  selectedLayerId: { type: String, default: null },
  editingLayerId: { type: String, default: null },
  timelinePlayhead: { type: Number, default: null },
  selectedKeyframeId: { type: String, default: null },
  activeBreakpoint: { type: String, default: 'desktop' },
  canvasMaxWidth: { type: Number, default: null },
  zoom: { type: Number, default: 1 },
  hiddenIds: { type: Set, default: () => new Set() },
  // Bloccati nel pannello Livelli (1.4.1424): sulla tela non si prendono e i clic passano al livello sotto
  lockedIds: { type: Set, default: () => new Set() },
  // Più livelli scelti insieme (1.4.1428): si spostano insieme trascinandone uno
  selectedIds: { type: Set, default: () => new Set() },
  // Anteprime (1.4.1352): lo scorrimento della pagina (0–100, null = vista ferma per modificare) e lo stato scelto
  // ({ nome: valore }, null = tutti i livelli), con i valori degli stati all'avvio (impostazione «Stati all'avvio»).
  scrollPreview: { type: Number, default: null },
  statePreview: { type: Object, default: null },
  initialStates: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['deselect', 'select-layer', 'update-layer', 'update-layers', 'update-keyframe', 'start-edit', 'stop-edit']);

// La sovrapposizione dello sfondo, uniforme o sfumata dall'alto e dal basso (gemella di class-proslider-tile.php).
function velo(bg) {
  const c = bg.overlay || '#000';
  const alto = Math.max(0, Math.min(100, +bg.overlayFadeTop || 0));
  const basso = Math.max(0, Math.min(100, +bg.overlayFadeBottom || 0));
  if (!alto && !basso) return c;
  const tappe = [(alto ? c : 'transparent') + ' 0%'];
  if (alto) tappe.push('transparent ' + alto + '%');
  if (basso) tappe.push('transparent ' + (100 - basso) + '%');
  tappe.push((basso ? c : 'transparent') + ' 100%');
  return 'linear-gradient(to bottom,' + tappe.join(',') + ')';
}

const canvasWrap = ref(null);

// ── Il carattere variabile sulla tela (1.4.1435) ──
// Il sito carica la versione variabile di un carattere quando il livello ne usa la larghezza (fontWdth) o muove larghezza
// e peso con lo scorrimento (PHP assi_carattere / carattere_variabile). La tela usava il file fisso del builder e la
// larghezza non si vedeva (Elastan rifatto a mano). Qui gli stessi assi, sotto un nome proprio («Anybody VF»).
const facceVariabili = new Set();
function assiCarattere(l) {
  const assi = {};
  const tappe = l.scrollMove ? (Array.isArray(l.scrollMove.tappe) && l.scrollMove.tappe.length ? l.scrollMove.tappe : [l.scrollMove.da || {}, l.scrollMove.a || {}]) : [];
  for (const k of ['wdth', 'wght']) {
    const v = tappe.map((t) => Number(t?.[k])).filter((n) => Number.isFinite(n));
    if (v.length) assi[k] = [Math.min(...v), Math.max(...v)];
  }
  const w = Number(l.fontWdth);
  if (l.fontWdth !== undefined && l.fontWdth !== '' && Number.isFinite(w)) {
    assi.wdth = assi.wdth ? [Math.min(assi.wdth[0], w), Math.max(assi.wdth[1], w)] : [w, w];
    const p = Number(l.fontWeight);
    if (!assi.wght && Number.isFinite(p)) assi.wght = [p, p];
  }
  return assi;
}
function famigliaTela(l) {
  const ff = String(l.fontFamily || '').trim().replace(/^['"]|['"]$/g, '');
  if (!ff || ff.startsWith('var(')) return l.fontFamily + ', sans-serif';
  const assi = assiCarattere(l);
  const chiavi = Object.keys(assi).sort();
  if (!chiavi.length) return `'${ff}', sans-serif`;
  const alias = ff + ' VF';
  const q = 'family=' + encodeURIComponent(ff) + ':' + chiavi.join(',') + '@' + chiavi.map((k) => (assi[k][0] === assi[k][1] ? assi[k][0] : assi[k][0] + '..' + assi[k][1])).join(',');
  if (!facceVariabili.has(q)) {
    facceVariabili.add(q);
    fetch('https://fonts.googleapis.com/css2?' + q + '&display=swap')
      .then((r) => (r.ok ? r.text() : ''))
      .then((css) => {
        if (!css) return;
        const st = document.createElement('style');
        st.dataset.mpsVf = q;
        st.textContent = css.replace(/font-family:\s*['"]?[^;'"]+['"]?\s*;/g, `font-family: '${alias}';`);
        document.head.appendChild(st);
      })
      .catch(() => {});
  }
  return `'${alias}', '${ff}', sans-serif`;
}
const canvasEl = ref(null);
// Guide magnetiche (1.4.1430): mentre si trascina, il livello si aggancia ai bordi e al centro della tela e degli altri
// livelli visibili (sinistra, centro, destra; alto, metà, basso). Le linee si vedono finché si tiene premuto; Alt le spegne.
const guide = ref({ v: [], h: [] });
const SOGLIA_GUIDA = 0.8; // punti percentuali della tela
const bgVideo = ref(null);

// Lo sfondo video che «Segue lo scorrimento»: nell'anteprima avanza col cursore, come sul sito.
watch(() => props.scrollPreview, (p) => {
  const v = bgVideo.value;
  if (!v || p == null || !props.slide?.background?.videoScroll || !(v.duration > 0)) return;
  try { v.currentTime = Math.max(0, Math.min(v.duration - 0.05, (p / 100) * v.duration)); } catch (e) { /* non ancora caricato */ }
});

const CANVAS_W = computed(() => {
  if (props.canvasMaxWidth) {
    return props.canvasMaxWidth;
  }
  return 1200;
});
const canvasH = computed(() => props.sliderHeight || 600);

const scale = ref(1);
const resizeHandles = ['nw', 'n', 'ne', 'e', 'se', 's', 'sw', 'w'];

const layers = computed(() => props.slide?.layers || []);
const slideBg = computed(() => props.slide?.background || { type: 'color', color: '#1e293b' });

const effectiveScale = computed(() => scale.value * props.zoom);

const scaledWrapStyle = computed(() => {
  const s = effectiveScale.value;
  return {
    width: Math.round(CANVAS_W.value * s) + 'px',
    height: Math.round(canvasH.value * s) + 'px',
    overflow: 'hidden',
    flexShrink: 0,
  };
});

const canvasStyle = computed(() => ({
  width: CANVAS_W.value + 'px',
  height: canvasH.value + 'px',
  transform: `scale(${effectiveScale.value})`,
  transformOrigin: '0 0',
}));

const bgColorStyle = computed(() => {
  const bg = slideBg.value;
  if (bg.type === 'transparent') {
    return { background: 'transparent' };
  }
  if (bg.type === 'gradient') {
    return { background: `linear-gradient(${bg.gradientAngle || 180}deg, ${resolveColor(bg.gradientFrom) || '#1e293b'}, ${resolveColor(bg.gradientTo) || '#0f172a'})` };
  }
  return { background: resolveColor(bg.color) || '#1e293b' };
});

const globalBgStyle = computed(() => {
  const gb = props.globalBackground;
  if (!gb) return { background: '#1e293b' };
  if (gb.type === 'gradient') {
    return { background: `linear-gradient(${gb.gradientAngle || 180}deg, ${resolveColor(gb.gradientFrom) || '#1e293b'}, ${resolveColor(gb.gradientTo) || '#0f172a'})` };
  }
  return { background: resolveColor(gb.color) || '#1e293b' };
});

const stylesStore = useStylesStore();

// Risolve var(--olo-color-*) al valore hex reale per l'anteprima.
// ⚠️ La forma con RISERVA — `var(--olo-color-dark, #16263d)` — e' quella usata da
// tutti i default delle tile: il vecchio regex la leggeva come un id chiamato
// «dark, #16263d», non lo trovava, e l'anteprima restava sul token grezzo.
function resolveColor(val) {
  if (!val || !val.startsWith('var(--olo-color-')) return val;
  const m = val.match(/^var\(\s*--olo-color-([a-z0-9_-]+)\s*(?:,\s*([^)]+))?\)$/i);
  if (m) {
    const gc = (stylesStore.globalColors || []).find(c => c.id === m[1].toLowerCase());
    if (gc) return gc.value;
    // Token non ancora nella palette: vale la riserva scritta dentro.
    if (m[2]) return m[2].trim();
  }
  return val;
}

function isYouTube(url) {
  return /youtube\.com|youtu\.be/i.test(url || '');
}

// Risolve valori layer con responsive override per il breakpoint attivo
function resolveLayerValues(l) {
  const bp = props.activeBreakpoint;
  if (bp === 'desktop' || !l.responsive) return l;
  // Ordine ereditarietà: mobile ← tablet ← notebook ← desktop
  const chain = ['notebook', 'tablet', 'mobile'];
  const idx = chain.indexOf(bp);
  if (idx < 0) return l;
  // Merge override dal breakpoint più alto al corrente
  const merged = {};
  for (let i = 0; i <= idx; i++) {
    const ov = l.responsive?.[chain[i]];
    if (ov) Object.assign(merged, ov);
  }
  return { ...l, ...merged };
}

// Lo stile di un livello con le anteprime (1.4.1352): nello stato scelto i livelli degli altri valori spariscono, e
// col cursore «Scorrimento» il livello si muove come sul sito (translate/scale/rotate si sommano al suo transform).
function layerStyle(l) {
  if (props.statePreview && l.stato?.nome) {
    const nome = String(l.stato.nome).toLowerCase();
    const v = Object.prototype.hasOwnProperty.call(props.statePreview, nome) ? props.statePreview[nome] : (props.initialStates?.[nome] ?? '');
    if (!statoVale(l.stato.vale, v)) return { display: 'none' };
  }
  const st = layerStyleBase(l);
  if (st.display === 'none' || props.scrollPreview == null || !l.scrollMove) return st;
  const sm = statoSM(l.scrollMove, props.scrollPreview, CANVAS_W.value / 1440);
  if (!sm) return st;
  if (sm.translate) st.translate = sm.translate;
  if (sm.scale) st.scale = sm.scale;
  if (sm.rotate) st.rotate = sm.rotate;
  if (sm.opacita != null) st.opacity = (st.opacity ?? 1) * sm.opacita;
  if (sm.clipPath) st.clipPath = sm.clipPath;
  return st;
}

function layerStyleBase(l) {
  const rl = resolveLayerValues(l);
  // Nascondi layer se visible === false nel breakpoint
  if (rl.visible === false) {
    return { display: 'none' };
  }

  // Se il layer ha una timeline, mostra sempre lo stato interpolato
  const hasTimeline = l.timeline?.keyframes?.length >= 2;

  if (hasTimeline) {
    // Usa il playhead globale per tutti i layer con timeline quando è attivo, altrimenti time=0
    const playhead = props.timelinePlayhead != null ? props.timelinePlayhead : 0;
    const interp = interpolateAtTime(l.timeline, playhead);
    if (interp) {
      const w = rl.width === 'auto' ? 'auto' : rl.width + '%';
      const h = rl.height === 'auto' ? 'auto' : rl.height + '%';
      const tx = (interp.x / 100) * CANVAS_W.value;
      const ty = (interp.y / 100) * canvasH.value;
      const rx = interp.rotationX ?? 0;
      const ry = interp.rotationY ?? 0;
      const sx = interp.skewX ?? 0;
      const sy = interp.skewY ?? 0;
      let transform = '';
      if (rx !== 0 || ry !== 0) transform += 'perspective(800px) ';
      transform += `translate(${tx}px, ${ty}px) scale(${interp.scale ?? 1}) rotate(${interp.rotation ?? 0}deg)`;
      if (rx !== 0) transform += ` rotateX(${rx}deg)`;
      if (ry !== 0) transform += ` rotateY(${ry}deg)`;
      if (sx !== 0) transform += ` skewX(${sx}deg)`;
      if (sy !== 0) transform += ` skewY(${sy}deg)`;
      // Build filter
      const filterParts = [];
      if ((interp.blur ?? 0) > 0) filterParts.push(`blur(${interp.blur}px)`);
      if ((interp.brightness ?? 100) !== 100) filterParts.push(`brightness(${interp.brightness}%)`);
      if ((interp.grayscale ?? 0) > 0) filterParts.push(`grayscale(${interp.grayscale}%)`);

      // Clip-path mask
      const mt = interp.maskTop ?? 0;
      const mr = interp.maskRight ?? 0;
      const mb = interp.maskBottom ?? 0;
      const ml = interp.maskLeft ?? 0;
      const hasClip = mt > 0 || mr > 0 || mb > 0 || ml > 0;

      const st = {
        left: '0',
        top: '0',
        width: w,
        height: h,
        opacity: (interp.opacity ?? 100) / 100,
        transform,
        filter: filterParts.length > 0 ? filterParts.join(' ') : 'none',
        transformOrigin: `${interp.originX ?? 50}% ${interp.originY ?? 50}%`,
        zIndex: 2,
      };
      if (hasClip) st.clipPath = `inset(${mt}% ${mr}% ${mb}% ${ml}%)`;
      if (l.blendMode && l.blendMode !== 'normal') st.mixBlendMode = l.blendMode;
      return st;
    }
  }

  const st = {
    left: rl.x + '%',
    top: rl.y + '%',
    width: rl.width === 'auto' ? 'auto' : rl.width + '%',
    height: rl.height === 'auto' ? 'auto' : rl.height + '%',
    opacity: (rl.opacity ?? 100) / 100,
    zIndex: 2,
  };
  // Border
  if (l.borderWidthLinked === false) {
    const bt = l.borderWidthTop ?? 0, br = l.borderWidthRight ?? 0, bb = l.borderWidthBottom ?? 0, bl = l.borderWidthLeft ?? 0;
    if (bt > 0 || br > 0 || bb > 0 || bl > 0) {
      const bs = l.borderStyle || 'solid';
      const bc = l.borderColor || '#fff';
      st.borderTop = `${bt}px ${bs} ${bc}`;
      st.borderRight = `${br}px ${bs} ${bc}`;
      st.borderBottom = `${bb}px ${bs} ${bc}`;
      st.borderLeft = `${bl}px ${bs} ${bc}`;
    }
  } else if ((l.borderWidth ?? 0) > 0) {
    st.border = `${l.borderWidth}px ${l.borderStyle || 'solid'} ${l.borderColor || '#fff'}`;
  }
  // Border-radius — supporta angoli individuali
  if (l.borderRadiusLinked === false) {
    const tl = l.borderRadiusTL ?? 0, tr = l.borderRadiusTR ?? 0, br2 = l.borderRadiusBR ?? 0, bl2 = l.borderRadiusBL ?? 0;
    if (tl > 0 || tr > 0 || br2 > 0 || bl2 > 0) {
      st.borderRadius = `${tl}px ${tr}px ${br2}px ${bl2}px`;
      st.overflow = 'hidden';
    }
  } else if (l.borderRadius) {
    st.borderRadius = l.borderRadius + 'px';
    st.overflow = 'hidden';
  }
  // Background — ogni tipo ha il suo default/fallback
  if (l.type === 'shape') {
    if (l.shapeGradient) {
      const stop = Math.max(0, Math.min(95, parseInt(l.shapeGradient.fromStop, 10) || 0));
      st.background = `linear-gradient(${l.shapeGradient.angle ?? 180}deg, ${l.shapeGradient.from || '#3b82f6'}${stop ? ' ' + stop + '%' : ''}, ${l.shapeGradient.to || '#8b5cf6'})`;
    } else {
      st.backgroundColor = l.bgColor || '#3b82f6';
    }
  } else if (l.type === 'button') {
    // Come il PHP: senza colore il pulsante prende il primario del sito (prima un blu fisso).
    st.backgroundColor = l.bgColor || 'var(--olo-color-primary, #e1474f)';
  } else if (l.type === 'icon') {
    if (l.bgColor) st.backgroundColor = l.bgColor;
    if (l.padding) st.padding = l.padding + 'px';
  } else if (l.type === 'text') {
    st.backgroundColor = l.bgColor || 'transparent';
  }
  // Padding — supporta lati individuali
  if (l.type !== 'image' && l.type !== 'video') {
    if (l.paddingLinked === false) {
      const pt = l.paddingTop ?? 0, pr = l.paddingRight ?? 0, pb = l.paddingBottom ?? 0, pl = l.paddingLeft ?? 0;
      if (pt > 0 || pr > 0 || pb > 0 || pl > 0) {
        st.padding = `${pt}px ${pr}px ${pb}px ${pl}px`;
      }
    } else if (l.type === 'button' && l.padding) {
      st.padding = l.padding + 'px ' + (l.padding * 2) + 'px';
    } else if (l.padding) {
      st.padding = l.padding + 'px';
    }
  }
  // Box shadow — per icone senza bg usa drop-shadow (segue forma SVG),
  // per tutti gli altri usa box-shadow (segue border-radius del div)
  if (l.boxShadow) {
    const bs = l.boxShadow;
    const isTransparentIcon = l.type === 'icon' && !l.bgColor;
    if (isTransparentIcon) {
      // drop-shadow segue la forma visibile del contenuto (stella SVG, ecc.)
      st.filter = (st.filter && st.filter !== 'none' ? st.filter + ' ' : '') +
        `drop-shadow(${bs.x ?? 0}px ${bs.y ?? 4}px ${bs.blur ?? 10}px ${bs.color || 'rgba(0,0,0,0.3)'})`;
    } else {
      st.boxShadow = `${bs.x ?? 0}px ${bs.y ?? 4}px ${bs.blur ?? 10}px ${bs.spread ?? 0}px ${bs.color || 'rgba(0,0,0,0.3)'}`;
    }
  }
  // CSS filters (image/video)
  if (l.type === 'image' || l.type === 'video') {
    const parts = [];
    if ((l.filterBrightness ?? 100) !== 100) parts.push(`brightness(${l.filterBrightness}%)`);
    if ((l.filterContrast ?? 100) !== 100) parts.push(`contrast(${l.filterContrast}%)`);
    if ((l.filterSaturate ?? 100) !== 100) parts.push(`saturate(${l.filterSaturate}%)`);
    if ((l.filterGrayscale ?? 0) > 0) parts.push(`grayscale(${l.filterGrayscale}%)`);
    if ((l.filterHueRotate ?? 0) > 0) parts.push(`hue-rotate(${l.filterHueRotate}deg)`);
    if ((l.filterBlur ?? 0) > 0) parts.push(`blur(${l.filterBlur}px)`);
    if ((l.filterSepia ?? 0) > 0) parts.push(`sepia(${l.filterSepia}%)`);
    if ((l.filterInvert ?? 0) > 0) parts.push(`invert(${l.filterInvert}%)`);
    if (parts.length) st.filter = parts.join(' ');
  }
  // Backdrop filter (glassmorphism)
  const bdParts = [];
  if ((l.backdropBlur ?? 0) > 0) bdParts.push(`blur(${l.backdropBlur}px)`);
  if ((l.backdropBrightness ?? 100) !== 100) bdParts.push(`brightness(${l.backdropBrightness}%)`);
  if ((l.backdropGrayscale ?? 0) > 0) bdParts.push(`grayscale(${l.backdropGrayscale}%)`);
  if ((l.backdropContrast ?? 100) !== 100) bdParts.push(`contrast(${l.backdropContrast}%)`);
  if ((l.backdropSaturate ?? 100) !== 100) bdParts.push(`saturate(${l.backdropSaturate}%)`);
  if ((l.backdropInvert ?? 0) > 0) bdParts.push(`invert(${l.backdropInvert}%)`);
  if ((l.backdropHueRotate ?? 0) > 0) bdParts.push(`hue-rotate(${l.backdropHueRotate}deg)`);
  if (bdParts.length) st.backdropFilter = bdParts.join(' ');
  // Blend mode
  if (l.blendMode && l.blendMode !== 'normal') st.mixBlendMode = l.blendMode;
  // Cursor
  if (l.cursor && l.cursor !== 'auto') st.cursor = l.cursor;
  // I comandi che prima erano CSS su misura (1.4.1430): ora si vedono anche sulla tela (il CSS su misura qui no)
  if (l.origine && /^[a-z0-9.%\s-]{1,40}$/i.test(String(l.origine))) st.transformOrigin = l.origine;
  if (l.retroNascosto) { st.backfaceVisibility = 'hidden'; st.WebkitBackfaceVisibility = 'hidden'; }
  if (Number(l.rotazione)) st.rotate = Number(l.rotazione) + 'deg';
  if (l.type === 'shape' && l.shapeImage && !/[\s"'()<>]/.test(String(l.shapeImage))) {
    st.background = `url("${l.shapeImage}") ${l.shapeImagePos || 'center'} / ${l.shapeImageSize || 'cover'}${l.shapeImageRepeat ? ' ' + l.shapeImageRepeat : ''}`;
  }
  // il resto dei comandi ex CSS (1.4.1431): sfumature, ombre, ritaglio, bordi sfumati… (stileComandi in livelloUtils)
  // il clic che passa attraverso vale solo sul sito: sulla tela il livello resta prendibile
  const sc = stileComandi(l);
  delete sc.pointerEvents;
  // l'ombra che segue la forma si aggiunge ai filtri dell'immagine, come nel PHP (un solo «filter» vince)
  if (sc.filter && st.filter && st.filter !== 'none' && (l.type === 'image' || l.type === 'video')) sc.filter = st.filter + ' ' + sc.filter;
  Object.assign(st, sc);
  return st;
}

function textStyle(l) {
  const rl = resolveLayerValues(l);
  const st = {
    fontSize: rl.fontSize + 'px',
    fontWeight: l.fontWeight || '700',
    color: l.color || '#fff',
    textAlign: rl.textAlign || 'left',
    lineHeight: l.lineHeight ?? 1.2,
    whiteSpace: l.unaRiga ? 'nowrap' : 'pre-wrap',
    wordBreak: 'break-word',
  };
  if (l.fontStyle && l.fontStyle !== 'normal') st.fontStyle = l.fontStyle;
  if (l.fontFamily) st.fontFamily = famigliaTela(l);
  if (rl.letterSpacing) st.letterSpacing = rl.letterSpacing + 'px';
  if (l.textTransform && l.textTransform !== 'none') st.textTransform = l.textTransform;
  if (l.textDecoration && l.textDecoration !== 'none') st.textDecoration = l.textDecoration;
  if (l.textShadow) st.textShadow = `${l.textShadow.x ?? 2}px ${l.textShadow.y ?? 2}px ${l.textShadow.blur ?? 4}px ${l.textShadow.color || '#000'}`;
  if ((l.textStrokeWidth ?? 0) > 0) {
    st.WebkitTextStroke = `${l.textStrokeWidth}px ${l.textStrokeColor || '#000'}`;
  }
  if (l.selectableText) st.userSelect = 'text';
  return st;
}

function buttonStyle(l) {
  // la grandezza e la spaziatura dello schermo scelto (1.4.1433): prima i pulsanti restavano quelli del computer
  const rl = resolveLayerValues(l);
  const st = {
    fontSize: rl.fontSize + 'px',
    fontWeight: l.fontWeight || '600',
    color: l.color || (l.bgColor ? '#fff' : 'var(--olo-color-primary-contrast, #ffffff)'),
    display: 'inline-block',
    textAlign: 'center',
    lineHeight: l.lineHeight ?? 1.2,
  };
  if (l.fontStyle && l.fontStyle !== 'normal') st.fontStyle = l.fontStyle;
  if (l.fontFamily) st.fontFamily = famigliaTela(l);
  if (rl.letterSpacing) st.letterSpacing = rl.letterSpacing + 'px';
  if (l.textTransform && l.textTransform !== 'none') st.textTransform = l.textTransform;
  if (l.textDecoration && l.textDecoration !== 'none') st.textDecoration = l.textDecoration;
  if (l.textShadow) st.textShadow = `${l.textShadow.x ?? 2}px ${l.textShadow.y ?? 2}px ${l.textShadow.blur ?? 4}px ${l.textShadow.color || '#000'}`;
  if ((l.textStrokeWidth ?? 0) > 0) {
    st.WebkitTextStroke = `${l.textStrokeWidth}px ${l.textStrokeColor || '#000'}`;
  }
  return st;
}

function shapeStyle(l) {
  // borderRadius, bgColor e gradient sono ora sul wrapper (layerStyle) per shadow/border
  return {
    minWidth: '40px',
    minHeight: '40px',
  };
}

function iconStyle(l) {
  const size = Math.max(24, l.fontSize || 24);
  const st = {
    display: 'inline-flex',
    alignItems: 'center',
    justifyContent: 'center',
    width: size + 'px',
    height: size + 'px',
    color: l.color || '#fff',
  };
  // SVG fill/stroke override via CSS custom properties
  if (l.iconFillColor) st['--icon-fill'] = l.iconFillColor;
  if (l.iconStrokeColor) st['--icon-stroke'] = l.iconStrokeColor;
  if ((l.iconStrokeWidth ?? 0) > 0) st['--icon-stroke-width'] = l.iconStrokeWidth;
  if ((l.iconStrokeDash ?? 0) > 0) st['--icon-stroke-dash'] = l.iconStrokeDash;
  return st;
}

function getIconSvg(name) {
  return iconsSvg[name] || iconsSvg['star'] || '';
}

// --- Dragging ---
let dragState = null;

function startDrag(e, layer) {
  // Maiusc, Ctrl o Cmd + clic: aggiunge o toglie il livello dalla scelta (1.4.1428). Cliccando uno dei livelli già
  // scelti insieme la scelta resta, così si trascinano tutti; se non lo si sposta, al rilascio resta solo lui.
  const aggiungi = e.shiftKey || e.ctrlKey || e.metaKey;
  const nelGruppo = !aggiungi && props.selectedIds.size > 1 && props.selectedIds.has(layer.id);
  emit('select-layer', layer.id, { aggiungi, mantieni: nelGruppo });
  if (aggiungi) return;
  if (props.editingLayerId === layer.id) return;

  const rect = canvasEl.value.getBoundingClientRect();
  const hasTimeline = layer.timeline?.keyframes?.length >= 2;
  const isTimelineActive = hasTimeline && props.timelinePlayhead != null && layer.id === props.selectedLayerId;

  // la posizione di partenza è quella dello schermo che si sta guardando (prima quella del computer: su tablet e
  // telefono il livello saltava al primo spostamento)
  const vista = resolveLayerValues(layer);
  let origX = Number(vista.x) || 0;
  let origY = Number(vista.y) || 0;
  let timelineKfId = null;

  if (isTimelineActive) {
    // Usa i valori interpolati come punto di partenza
    const interp = interpolateAtTime(layer.timeline, props.timelinePlayhead);
    if (interp) {
      origX = interp.x;
      origY = interp.y;
    }
    // Trova il keyframe da aggiornare: selezionato o più vicino al playhead
    timelineKfId = props.selectedKeyframeId || null;
    if (!timelineKfId) {
      let closest = null;
      let minDist = Infinity;
      for (const kf of layer.timeline.keyframes) {
        const d = Math.abs(kf.time - props.timelinePlayhead);
        if (d < minDist) { minDist = d; closest = kf; }
      }
      if (closest) timelineKfId = closest.id;
    }
  }

  // le misure per le guide: il riquadro del livello e quelli degli altri livelli visibili, in % della tela
  const cr = canvasEl.value.getBoundingClientRect();
  const box = (r) => ({ l: ((r.left - cr.left) / cr.width) * 100, t: ((r.top - cr.top) / cr.height) * 100, w: (r.width / cr.width) * 100, h: (r.height / cr.height) * 100 });
  const mio = box(e.currentTarget.getBoundingClientRect());
  const muoviInsieme = new Set([layer.id, ...(nelGruppo ? [...props.selectedIds] : [])]);
  const lineeX = [0, 50, 100], lineeY = [0, 50, 100];
  canvasEl.value.querySelectorAll('[data-mps-id]').forEach((el) => {
    if (muoviInsieme.has(el.getAttribute('data-mps-id')) || el.offsetParent === null) return;
    const b2 = box(el.getBoundingClientRect());
    if (b2.w <= 0 || b2.h <= 0 || b2.w > 98) return;
    lineeX.push(b2.l, b2.l + b2.w / 2, b2.l + b2.w);
    lineeY.push(b2.t, b2.t + b2.h / 2, b2.t + b2.h);
  });
  dragState = {
    layerId: layer.id,
    startX: e.clientX,
    startY: e.clientY,
    origX,
    origY,
    mio,
    lineeX,
    lineeY,
    canvasW: rect.width / effectiveScale.value,
    canvasH: rect.height / effectiveScale.value,
    mode: 'drag',
    timelineKfId,
    mosso: false,
    nelGruppo,
    // gli altri livelli scelti insieme (non bloccati), ognuno dalla sua posizione su questo schermo
    insieme: nelGruppo && !timelineKfId
      ? layers.value.filter((l) => props.selectedIds.has(l.id) && l.id !== layer.id && !props.lockedIds.has(l.id))
        .map((l) => { const v = resolveLayerValues(l); return { id: l.id, x: Number(v.x) || 0, y: Number(v.y) || 0 }; })
      : [],
  };
  e.target.setPointerCapture?.(e.pointerId);
  window.addEventListener('pointermove', onPointerMove);
  window.addEventListener('pointerup', onPointerUp);
}

// --- Resizing ---
function startResize(e, layer, handle) {
  const canvasRect = canvasEl.value.getBoundingClientRect();
  const cw = canvasRect.width / effectiveScale.value;
  const ch = canvasRect.height / effectiveScale.value;
  // Per layer con width/height 'auto', calcola la % reale dal DOM
  let origW = typeof layer.width === 'number' ? layer.width : null;
  let origH = typeof layer.height === 'number' ? layer.height : null;
  if (origW === null || origH === null) {
    const layerEl = e.target.closest('[class*="mb-absolute"]');
    if (layerEl) {
      const lr = layerEl.getBoundingClientRect();
      if (origW === null) origW = (lr.width / effectiveScale.value / cw) * 100;
      if (origH === null) origH = (lr.height / effectiveScale.value / ch) * 100;
    } else {
      if (origW === null) origW = 20;
      if (origH === null) origH = 10;
    }
  }
  dragState = {
    layerId: layer.id,
    startX: e.clientX,
    startY: e.clientY,
    origX: layer.x,
    origY: layer.y,
    origW,
    origH,
    canvasW: cw,
    canvasH: ch,
    handle,
    mode: 'resize',
  };
  e.target.setPointerCapture?.(e.pointerId);
  window.addEventListener('pointermove', onPointerMove);
  window.addEventListener('pointerup', onPointerUp);
}

function onPointerMove(e) {
  if (!dragState) return;
  const dx = (e.clientX - dragState.startX) / effectiveScale.value;
  const dy = (e.clientY - dragState.startY) / effectiveScale.value;

  const round1 = (v) => Math.round(v * 10) / 10;
  if (dragState.mode === 'drag') {
    let pctX = (dx / CANVAS_W.value) * 100;
    let pctY = (dy / canvasH.value) * 100;
    // aggancio alle guide (Alt per spostare libero)
    const gv = [], gh = [];
    if (!e.altKey && dragState.mio) {
      const m = dragState.mio;
      const aggancia = (inizio, misura, linee) => {
        let meglio = null;
        for (const punto of [inizio, inizio + misura / 2, inizio + misura]) {
          for (const linea of linee) {
            const d = linea - punto;
            if (Math.abs(d) <= SOGLIA_GUIDA && (!meglio || Math.abs(d) < Math.abs(meglio.d))) meglio = { d, linea };
          }
        }
        return meglio;
      };
      const ax = aggancia(m.l + pctX, m.w, dragState.lineeX);
      if (ax) { pctX += ax.d; gv.push(Math.round(ax.linea * 100) / 100); }
      const ay = aggancia(m.t + pctY, m.h, dragState.lineeY);
      if (ay) { pctY += ay.d; gh.push(Math.round(ay.linea * 100) / 100); }
    }
    guide.value = { v: gv, h: gh };
    const newX = round1(Math.max(0, Math.min(100, dragState.origX + pctX)));
    const newY = round1(Math.max(0, Math.min(100, dragState.origY + pctY)));
    if (Math.abs(dx) + Math.abs(dy) > 2) dragState.mosso = true;
    if (dragState.timelineKfId) {
      emit('update-keyframe', dragState.timelineKfId, { props: { x: newX, y: newY } });
    } else if (dragState.insieme.length) {
      const sx = newX - dragState.origX, sy = newY - dragState.origY;
      emit('update-layers', [{ id: dragState.layerId, x: newX, y: newY },
        ...dragState.insieme.map((o) => ({ id: o.id, x: round1(o.x + sx), y: round1(o.y + sy) }))]);
    } else {
      emit('update-layer', dragState.layerId, { x: newX, y: newY });
    }
  } else if (dragState.mode === 'resize') {
    const pctX = (dx / CANVAS_W.value) * 100;
    const pctY = (dy / canvasH.value) * 100;
    const h = dragState.handle;
    const updates = {};

    if (h.includes('e')) updates.width = round1(Math.max(5, dragState.origW + pctX));
    if (h.includes('w')) {
      updates.width = round1(Math.max(5, dragState.origW - pctX));
      updates.x = round1(dragState.origX + pctX);
    }
    if (h.includes('s')) updates.height = round1(Math.max(5, dragState.origH + pctY));
    if (h.includes('n')) {
      updates.height = round1(Math.max(5, dragState.origH - pctY));
      updates.y = round1(dragState.origY + pctY);
    }
    emit('update-layer', dragState.layerId, updates);
  }
}

function onPointerUp() {
  guide.value = { v: [], h: [] };
  // cliccato (non trascinato) uno dei livelli scelti insieme: resta scelto solo lui
  if (dragState && dragState.mode === 'drag' && dragState.nelGruppo && !dragState.mosso) emit('select-layer', dragState.layerId, {});
  dragState = null;
  window.removeEventListener('pointermove', onPointerMove);
  window.removeEventListener('pointerup', onPointerUp);
}

function onDblClick(layer) {
  if (layer.type === 'text') {
    emit('start-edit', layer.id);
  }
}

function onTextBlur(e, layer) {
  // Il testo con parole colorate, a capo, in grassetto (1.4.1431): si salva com'è, non solo le lettere. Prima la tela
  // mostrava i segni HTML in chiaro e, modificando, li toglieva insieme ai colori.
  const html = testoSicuro(e.target.innerHTML).replace(/(<br>)+$/, '');
  if (html !== testoSicuro(layer.content)) emit('update-layer', layer.id, { content: html });
  emit('stop-edit');
}

// --- Canvas scaling ---
function recalcScale() {
  if (!canvasWrap.value) return;
  const rect = canvasWrap.value.getBoundingClientRect();
  const pad = 32;
  const scaleX = (rect.width - pad * 2) / CANVAS_W.value;
  const scaleY = (rect.height - pad * 2) / canvasH.value;
  scale.value = Math.min(1, scaleX, scaleY);
}

let resizeObs;
onMounted(() => {
  recalcScale();
  resizeObs = new ResizeObserver(recalcScale);
  if (canvasWrap.value) resizeObs.observe(canvasWrap.value);
});
watch(() => props.activeBreakpoint, () => {
  setTimeout(recalcScale, 50);
});

onUnmounted(() => {
  resizeObs?.disconnect();
  window.removeEventListener('pointermove', onPointerMove);
  window.removeEventListener('pointerup', onPointerUp);
});
</script>

<style scoped>
/* Guide magnetiche (1.4.1430) */
.mps-guida { position: absolute; z-index: 60; pointer-events: none; background: #f472b6; }
.mps-guida--v { top: 0; bottom: 0; width: 1px; transform: translateX(-0.5px); }
.mps-guida--h { left: 0; right: 0; height: 1px; transform: translateY(-0.5px); }
/* Gli altri livelli scelti insieme (1.4.1428): contorno tratteggiato, il principale ha l'anello pieno */
.mps-canvas-insieme { outline: 2px dashed #60a5fa; outline-offset: 2px; }
.mps-canvas-initially-hidden {
  opacity: 0.4;
  outline: 1px dashed rgba(239, 68, 68, 0.6);
  outline-offset: 2px;
}
.mps-canvas-hidden-badge {
  position: absolute;
  top: -18px;
  left: 0;
  background: rgba(239, 68, 68, 0.9);
  color: #fff;
  font-size: 9px;
  font-weight: 600;
  letter-spacing: 0.02em;
  padding: 2px 6px;
  border-radius: 3px;
  text-transform: uppercase;
  white-space: nowrap;
  pointer-events: none;
  z-index: 50;
}
.mps-handle {
  position: absolute;
  width: 10px;
  height: 10px;
  background: #3b82f6;
  border: 1px solid #fff;
  border-radius: 2px;
  z-index: 100;
}
.mps-handle-nw { top: -5px; left: -5px; cursor: nw-resize; }
.mps-handle-n  { top: -5px; left: 50%; margin-left: -5px; cursor: n-resize; }
.mps-handle-ne { top: -5px; right: -5px; cursor: ne-resize; }
.mps-handle-e  { top: 50%; right: -5px; margin-top: -5px; cursor: e-resize; }
.mps-handle-se { bottom: -5px; right: -5px; cursor: se-resize; }
.mps-handle-s  { bottom: -5px; left: 50%; margin-left: -5px; cursor: s-resize; }
.mps-handle-sw { bottom: -5px; left: -5px; cursor: sw-resize; }
.mps-handle-w  { top: 50%; left: -5px; margin-top: -5px; cursor: w-resize; }
.mps-canvas-icon :deep(svg) {
  width: 100%;
  height: 100%;
}
/* Override fill="none" sugli elementi SVG interni
   così che drop-shadow crei una sola ombra (non doppia sui bordi dello stroke)
   Supporta override via custom properties: --icon-fill, --icon-stroke, --icon-stroke-width, --icon-stroke-dash */
.mps-canvas-icon :deep(svg path),
.mps-canvas-icon :deep(svg polygon),
.mps-canvas-icon :deep(svg circle),
.mps-canvas-icon :deep(svg rect),
.mps-canvas-icon :deep(svg ellipse),
.mps-canvas-icon :deep(svg polyline),
.mps-canvas-icon :deep(svg line) {
  fill: var(--icon-fill, currentColor);
  stroke: var(--icon-stroke, currentColor);
  stroke-width: var(--icon-stroke-width, inherit);
  stroke-dasharray: var(--icon-stroke-dash, none);
}
</style>
