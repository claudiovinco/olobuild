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
          :src="conEsempio(slideBg.image, contenuti.esempio.value)"
          class="mb-w-full mb-h-full mb-object-cover"
          draggable="false"
        />
        <!-- Video (mp4 thumbnail) -->
        <video
          v-else-if="slideBg.type === 'video' && slideBg.video && !isYouTube(slideBg.video)"
          ref="bgVideo"
          :src="slideBg.video"
          :poster="conEsempio(slideBg.image, contenuti.esempio.value) || undefined"
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

      <!-- Layers: quelli della slide e, dalla 1.4.1475, quelli su tutte le slide (dietro: sotto lo sfondo della slide, come
           sul sito; davanti: sopra i livelli della slide). Si scelgono e si trascinano come gli altri. -->
      <div
        v-for="layer in livelliTela"
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
        <span v-if="layer.initiallyHidden" class="mps-canvas-hidden-badge" :title="t('Questo livello parte nascosto: lo mostra un altro livello con l’azione «Mostra o nasconde un livello».')">{{ t("nascosto all'avvio") }}</span>
        <!-- Text -->
        <component
          v-if="layer.type === 'text'"
          :is="layer.tag || 'h2'"
          :contenteditable="editingLayerId === layer.id"
          :style="textStyle(layer)"
          class="mb-m-0 mb-outline-none"
          @blur="onTextBlur($event, layer)"
          @keydown.enter.exact.prevent="$event.target.blur()"
          v-html="editingLayerId === layer.id ? testoSicuro(layer.content) : conEsempioTesto(testoSicuro(layer.content), contenuti.esempio.value)"
        ></component>

        <!-- Image -->
        <img
          v-else-if="layer.type === 'image'"
          :src="(layer.coloriDisegno && disegnoRicolorato(layer.imageSrc, layer.coloriDisegno, canvasWrap)) || conEsempio(layer.imageSrc, contenuti.esempio.value) || 'data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22120%22 height=%2280%22><rect fill=%22%23475569%22 width=%22120%22 height=%2280%22/><text x=%2260%22 y=%2244%22 text-anchor=%22middle%22 fill=%22%23cbd5e1%22 font-size=%2213%22>Immagine</text></svg>'"
          class="mb-w-full mb-h-full mb-pointer-events-none"
          :style="{ objectFit: layer.objectFit || 'cover', objectPosition: layer.objectPosition || 'center', borderRadius: (layer.borderRadius || 0) + 'px' }"
          draggable="false"
        />

        <!-- Button -->
        <span
          v-else-if="layer.type === 'button'"
          :style="buttonStyle(layer)"
          class="mb-inline-block mb-cursor-move"
        >{{ conEsempio(layer.content, contenuti.esempio.value) || t('Pulsante') }}</span>

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

      <!-- Il percorso del livello scelto (1.4.1473): tratteggiato, dal livello, coi punti disegnati da trascinare -->
      <svg v-if="percorsoVisto" class="mps-percorso" :width="CANVAS_W" :height="canvasH" :viewBox="'0 0 ' + CANVAS_W + ' ' + canvasH" aria-hidden="true">
        <path :d="percorsoVisto.d" :transform="percorsoVisto.transform" class="mps-percorso-linea" :stroke-width="2 / effectiveScale" vector-effect="non-scaling-stroke" />
        <circle :cx="ancora.x" :cy="ancora.y" :r="5 / effectiveScale" class="mps-percorso-inizio" />
        <circle v-for="(p, i) in percorsoVisto.punti" :key="'pp' + i" :cx="p[0]" :cy="p[1]" :r="6 / effectiveScale" class="mps-percorso-punto" @pointerdown.stop.prevent="trascinaPunto($event, i + 1)" />
      </svg>
      <!-- Disegno del percorso: ogni clic un punto, doppio clic o Invio finisce, Esc annulla -->
      <div v-if="disegnando" class="mps-disegno" @pointerdown.stop @click.stop="aggiungiPunto" @dblclick.stop.prevent="finisciDisegno" @pointermove="cursoreDisegno = puntoDa($event)">
        <svg :width="CANVAS_W" :height="canvasH" :viewBox="'0 0 ' + CANVAS_W + ' ' + canvasH" aria-hidden="true">
          <path :d="dDisegno" class="mps-percorso-linea" :stroke-width="2 / effectiveScale" />
          <circle v-for="(p, i) in puntiDisegno" :key="'pd' + i" :cx="p[0]" :cy="p[1]" :r="(i ? 5 : 6) / effectiveScale" :class="i ? 'mps-percorso-punto' : 'mps-percorso-inizio'" />
        </svg>
      </div>
      </div>
    </div>
    <!-- La barra del disegno (fuori dalla tela scalata, sopra) -->
    <div v-if="disegnando" class="mps-disegno-barra" role="status">
      <span>{{ t('Disegno del percorso') }} · {{ puntiDisegno.length - 1 === 1 ? t('1 punto') : t('%d punti').replace('%d', puntiDisegno.length - 1) }}</span>
      <InfoTip :testo="t('Clicca sulla tela per aggiungere i punti: il primo è il livello. Doppio clic o Invio per finire, Esc per annullare. Dopo puoi trascinare i punti per correggere il percorso.')" :titolo="t('Disegno del percorso')" />
      <button type="button" class="mps-disegno-btn is-si" :disabled="puntiDisegno.length < 2" @click.stop="finisciDisegno">{{ t('Fine') }}</button>
      <button type="button" class="mps-disegno-btn" @click.stop="chiudiDisegno">{{ t('Annulla') }}</button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, onUpdated, watch, nextTick } from 'vue';
import iconsSvg from './uikitIconsSvg.js';
import { interpolateAtTime, easingToCSS } from './timelineUtils.js';
import { fattorePercorso, puntoIniziale, dDaPunti, fotogrammiPercorso } from './percorso.js';
import { svgIcona } from './iconeMie.js';
import InfoTip from '../Builder/InfoTip.vue';
import { useStylesStore } from '@/stores/styles';
import { t } from '@/i18n';
import { statoSM, statoVale, stileComandi, testoSicuro } from './livelloUtils.js';
import { disegnoRicolorato } from './disegni.js';
import { useContenutiSlider, conEsempio, conEsempioTesto } from './datiArticolo.js';
import { templateColorTokens } from '@/utils/colorToken';

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
  // I livelli su tutte le slide (1.4.1475): sulla tela insieme a quelli della slide. Si prendono col mouse solo quando
  // si sta modificando un globale (1.4.1477): davanti com'è, un globale grande (una cornice, un velo) copriva i livelli
  // della slide e un clic sulla tela sceglieva lui.
  globalLayers: { type: Array, default: () => [] },
  editingGlobal: { type: Boolean, default: false },
  // Il percorso (1.4.1473): la tela del formato che si guarda (1440, 1240, 1024, 640), il disegno coi clic acceso,
  // la prova (un numero che cresce a ogni «Prova il percorso»)
  larghezzaFormato: { type: Number, default: 1440 },
  disegnoPercorso: { type: Boolean, default: false },
  provaPercorso: { type: Number, default: 0 },
});

const emit = defineEmits(['deselect', 'select-layer', 'update-layer', 'update-layers', 'update-keyframe', 'start-edit', 'stop-edit', 'fine-disegno']);

// La sovrapposizione dello sfondo, uniforme o sfumata dall'alto e dal basso (gemella di class-proslider-tile.php).
function velo(bg) {
  const c = bg.overlay || 'var(--olo-color-dark, #000)';
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
// ── Il carattere Google sulla tela (1.4.1479) ──
// Il sito serve ogni famiglia dei livelli dai file salvati sul server (PHP Olobuild_Font_Host: pesi da 300 a 900, e i
// corsivi veri se il livello è in corsivo). La tela contava sul carattere già presente nel builder, che lo carica solo
// aprendo l'elenco dei caratteri e col solo peso normale: Cormorant Garamond usciva col carattere di riserva. Qui un
// <link> per famiglia (Google rifiuta tutta la richiesta se una famiglia non esiste) con gli stessi pesi del sito.
const PESI_SITO = [300, 400, 500, 600, 700, 800, 900];
const famiglieTela = new Set();
function inCorsivo(l) {
  return l.fontStyle === 'italic' || l.fontStyle === 'oblique' || /<(em|i)[\s>]/i.test(String(l.content || ''));
}
function caricaCarattereTela(ff, corsivo) {
  // solo i nomi delle famiglie di Google (lettere, numeri, spazi, trattini): le pile di sistema hanno virgole e apici
  if (!/^[A-Za-z0-9][A-Za-z0-9 -]*$/.test(ff)) return;
  const chiave = ff + (corsivo ? '|corsivo' : '');
  if (famiglieTela.has(chiave)) return;
  famiglieTela.add(chiave);
  const assi = corsivo
    ? 'ital,wght@' + [...PESI_SITO.map((p) => '0,' + p), ...PESI_SITO.map((p) => '1,' + p)].join(';')
    : 'wght@' + PESI_SITO.join(';');
  const link = document.createElement('link');
  link.rel = 'stylesheet';
  link.dataset.mpsCarattere = chiave;
  link.href = 'https://fonts.googleapis.com/css2?family=' + encodeURIComponent(ff).replace(/%20/g, '+') + ':' + assi + '&display=swap';
  document.head.appendChild(link);
}
function famigliaTela(l) {
  const ff = String(l.fontFamily || '').trim().replace(/^['"]|['"]$/g, '');
  if (!ff || ff.startsWith('var(')) return l.fontFamily + ', sans-serif';
  caricaCarattereTela(ff, inCorsivo(l));
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
// sulla tela: prima i globali dietro, poi la slide, poi i globali davanti (lo z-index li mette al loro posto)
const idGlobali = computed(() => new Set((props.globalLayers || []).map((l) => l.id)));
const livelliTela = computed(() => {
  const g = props.globalLayers || [];
  return [...g.filter((l) => l.globalPosition === 'back'), ...layers.value, ...g.filter((l) => l.globalPosition !== 'back')];
});
// Slide dagli articoli (1.4.1457): le foto con un segnaposto mostrano quella del primo contenuto trovato
const contenuti = useContenutiSlider();
const slideBg = computed(() => props.slide?.background || { type: 'color', color: 'var(--olo-color-dark)' });

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

// I colori della Palette sulla tela (1.4.1441): i token `--olo-color-*` sono definiti solo dentro `.olo-template`, cioè
// nella pagina dell'anteprima; la tela dell'editor sta fuori e conosceva solo il primario. Scuro, Chiaro, Accento,
// Testo… restavano senza valore: la sfumatura scura del velo di Elastan non si vedeva affatto. Qui gli stessi valori
// che BuilderCanvas dà all'anteprima, come variabili della tela (solo i colori, non le altre regole della pagina).
const coloriPalette = computed(() => {
  const o = {};
  for (const [k, v] of Object.entries(templateColorTokens(stylesStore))) o['--olo-color-' + k] = v;
  return o;
});
const canvasStyle = computed(() => ({
  ...coloriPalette.value,
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
  const st0 = layerStyleStato(l);
  // i globali: dietro le slide (sotto lo sfondo della slide, z 1) o davanti ai loro livelli (z 2)
  if (idGlobali.value.has(l.id) && st0.display !== 'none') {
    st0.zIndex = l.globalPosition === 'back' ? 0 : 3;
    if (!props.editingGlobal) st0.pointerEvents = 'none';
  }
  return st0;
}
function layerStyleStato(l) {
  if (props.statePreview && l.stato?.nome) {
    const nome = String(l.stato.nome).toLowerCase();
    const v = Object.prototype.hasOwnProperty.call(props.statePreview, nome) ? props.statePreview[nome] : (props.initialStates?.[nome] ?? '');
    if (!statoVale(l.stato.vale, v)) return { display: 'none' };
  }
  // Il livello «Da pulire col puntatore» che imposta lo stato guardato (1.4.1480): sul sito, pulito, svanisce e lo stato
  // cambia; nella Scena di quello stato la tela lo copriva ancora sopra a tutto. Si nasconde come sul sito (resta
  // sceglibile dal pannello Livelli).
  if (props.statePreview && l.pulisci?.attivo && l.pulisci.stato) {
    const [n, val = ''] = String(l.pulisci.stato).split('=');
    const nome = n.trim().toLowerCase();
    if (nome && Object.prototype.hasOwnProperty.call(props.statePreview, nome) && String(props.statePreview[nome]) === val.trim()) {
      return { ...layerStyleBase(l), opacity: 0, pointerEvents: 'none' };
    }
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
  if (sm.assi) st.fontVariationSettings = sm.assi;
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
    // il punto della SUA linea del tempo: la testina meno il ritardo del livello (1.4.1472), come sul sito, dove nel
    // ritardo il livello resta al primo fotogramma
    const playhead = props.timelinePlayhead != null ? props.timelinePlayhead : 0;
    const interp = interpolateAtTime(l.timeline, Math.max(0, playhead - (l.timeline.delay || 0)));
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
      const bc = l.borderColor || 'var(--olo-color-light, #fff)';
      st.borderTop = `${bt}px ${bs} ${bc}`;
      st.borderRight = `${br}px ${bs} ${bc}`;
      st.borderBottom = `${bb}px ${bs} ${bc}`;
      st.borderLeft = `${bl}px ${bs} ${bc}`;
    }
  } else if ((l.borderWidth ?? 0) > 0) {
    st.border = `${l.borderWidth}px ${l.borderStyle || 'solid'} ${l.borderColor || 'var(--olo-color-light, #fff)'}`;
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
      st.background = `linear-gradient(${l.shapeGradient.angle ?? 180}deg, ${l.shapeGradient.from || 'var(--olo-color-primary, #e1474f)'}${stop ? ' ' + stop + '%' : ''}, ${l.shapeGradient.to || 'var(--olo-color-accent, #f4a23b)'})`;
    } else {
      st.backgroundColor = l.bgColor || 'var(--olo-color-primary, #e1474f)';
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
        `drop-shadow(${bs.x ?? 0}px ${bs.y ?? 4}px ${bs.blur ?? 10}px ${bs.color || 'color-mix(in srgb, var(--olo-color-dark, #000000) 30%, transparent)'})`;
    } else {
      st.boxShadow = `${bs.x ?? 0}px ${bs.y ?? 4}px ${bs.blur ?? 10}px ${bs.spread ?? 0}px ${bs.color || 'color-mix(in srgb, var(--olo-color-dark, #000000) 30%, transparent)'}`;
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
  } else if (l.type === 'shape' && (l.filterBlur ?? 0) > 0) {
    // 1.4.1462: la sfocatura delle forme (aloni, macchie di luce), come nel renderer PHP
    st.filter = `blur(${l.filterBlur}px)`;
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
    st.background = `url("${l.shapeImage}") ${l.shapeImagePos || 'center'} / ${l.shapeImageSize || 'cover'} ${l.shapeImageRepeat || 'no-repeat'}`;
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
    color: l.color || 'var(--olo-color-light, #fff)',
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
  if (l.textShadow) st.textShadow = `${l.textShadow.x ?? 2}px ${l.textShadow.y ?? 2}px ${l.textShadow.blur ?? 4}px ${l.textShadow.color || 'var(--olo-color-dark, #000)'}`;
  if ((l.textStrokeWidth ?? 0) > 0) {
    st.WebkitTextStroke = `${l.textStrokeWidth}px ${l.textStrokeColor || 'var(--olo-color-dark, #000)'}`;
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
  if (l.textShadow) st.textShadow = `${l.textShadow.x ?? 2}px ${l.textShadow.y ?? 2}px ${l.textShadow.blur ?? 4}px ${l.textShadow.color || 'var(--olo-color-dark, #000)'}`;
  if ((l.textStrokeWidth ?? 0) > 0) {
    st.WebkitTextStroke = `${l.textStrokeWidth}px ${l.textStrokeColor || 'var(--olo-color-dark, #000)'}`;
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
  // 1.4.1475: la grandezza esatta e quella dello schermo scelto, come il sito (prima almeno 24 e sempre quella del Computer)
  const size = Math.max(4, Number(resolveLayerValues(l).fontSize) || 24);
  const st = {
    display: 'inline-flex',
    alignItems: 'center',
    justifyContent: 'center',
    width: size + 'px',
    height: size + 'px',
    color: l.color || 'var(--olo-color-light, #fff)',
  };
  // SVG fill/stroke override via CSS custom properties
  if (l.iconFillColor) st['--icon-fill'] = l.iconFillColor;
  if (l.iconStrokeColor) st['--icon-stroke'] = l.iconStrokeColor;
  if ((l.iconStrokeWidth ?? 0) > 0) st['--icon-stroke-width'] = l.iconStrokeWidth;
  if ((l.iconStrokeDash ?? 0) > 0) st['--icon-stroke-dash'] = l.iconStrokeDash;
  return st;
}

// le icone del set e, dalla 1.4.1475, quelle caricate dall'utente («custom:…», prima una stella al loro posto)
function getIconSvg(name) {
  return svgIcona(name, iconsSvg) || (String(name || '').startsWith('custom:') ? '' : iconsSvg['star']) || '';
}

// --- Dragging ---
let dragState = null;

function startDrag(e, layer) {
  // Maiusc, Ctrl o Cmd + clic: aggiunge o toglie il livello dalla scelta (1.4.1428). Cliccando uno dei livelli già
  // scelti insieme la scelta resta, così si trascinano tutti; se non lo si sposta, al rilascio resta solo lui.
  // Un livello su tutte le slide si sceglie da solo (1.4.1475): si apre come globale.
  const globale = idGlobali.value.has(layer.id);
  const aggiungi = !globale && (e.shiftKey || e.ctrlKey || e.metaKey);
  const nelGruppo = !globale && !aggiungi && props.selectedIds.size > 1 && props.selectedIds.has(layer.id);
  emit('select-layer', layer.id, globale ? { globale: true } : { aggiungi, mantieni: nelGruppo });
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
    const interp = interpolateAtTime(layer.timeline, Math.max(0, props.timelinePlayhead - (layer.timeline.delay || 0)));
    if (interp) {
      origX = interp.x;
      origY = interp.y;
    }
    // Trova il keyframe da aggiornare: selezionato o più vicino al playhead
    timelineKfId = props.selectedKeyframeId || null;
    if (!timelineKfId) {
      let closest = null;
      let minDist = Infinity;
      const ora = props.timelinePlayhead - (layer.timeline.delay || 0);
      for (const kf of layer.timeline.keyframes) {
        const d = Math.abs(kf.time - ora);
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

// ── Il percorso del livello scelto (1.4.1473) ──
// Sulla tela si vede il tracciato che il livello percorrerà, che parte dal centro del livello (il sito sposta tutto il
// livello lungo il tracciato, dal primo punto). Si disegna coi clic e i punti disegnati si trascinano.
const r1 = (v) => Math.round(v * 10) / 10;
const livelloScelto = computed(() => (props.selectedLayerId ? livelliTela.value.find((l) => l.id === props.selectedLayerId) || null : null));
const ancora = ref({ x: 0, y: 0 });
let provaInCorso = null;
function elLivello(id) {
  const c = canvasEl.value;
  if (!c || !id) return null;
  return [...c.querySelectorAll('[data-mps-id]')].find((e) => e.getAttribute('data-mps-id') === id) || null;
}
function misuraAncora() {
  const l = livelloScelto.value;
  const c = canvasEl.value;
  if (!l || !c || (provaInCorso && provaInCorso.playState === 'running')) return;
  const el = elLivello(l.id);
  if (!el) return;
  const r = el.getBoundingClientRect();
  if (!r.width && !r.height) return;
  const cr = c.getBoundingClientRect();
  const s = effectiveScale.value || 1;
  const x = (r.left + r.width / 2 - cr.left) / s;
  const y = (r.top + r.height / 2 - cr.top) / s;
  if (Math.abs(x - ancora.value.x) > 0.05 || Math.abs(y - ancora.value.y) > 0.05) ancora.value = { x, y };
}
onUpdated(() => { if (livelloScelto.value?.motionPath || disegnando.value) misuraAncora(); });
watch([() => props.selectedLayerId, effectiveScale, () => props.activeBreakpoint], () => nextTick(misuraAncora));
const percorsoVisto = computed(() => {
  const l = livelloScelto.value;
  const mp = l?.motionPath;
  if (!mp || !mp.path || disegnando.value || hiddenIdsHas(l.id)) return null;
  const f = fattorePercorso(mp, props.larghezzaFormato);
  const p0 = puntoIniziale(mp.path);
  const a = ancora.value;
  const out = { d: mp.path, transform: `translate(${a.x - p0[0] * f} ${a.y - p0[1] * f}) scale(${f})`, punti: [] };
  if (mp.preset === 'disegnato' && Array.isArray(mp.punti) && mp.punti.length > 1) {
    const q0 = mp.punti[0];
    out.punti = mp.punti.slice(1).map((q) => [a.x + (q[0] - q0[0]) * f, a.y + (q[1] - q0[1]) * f]);
  }
  return out;
});
function hiddenIdsHas(id) { return props.hiddenIds && props.hiddenIds.has(id); }
// un punto trascinato: lo spostamento sulla tela diviso per la grandezza del percorso
function trascinaPunto(e, k) {
  const l = livelloScelto.value;
  if (!l?.motionPath?.punti) return;
  const f = fattorePercorso(l.motionPath, props.larghezzaFormato);
  const da = puntoDa(e);
  const prima = l.motionPath.punti.map((q) => [...q]);
  const muovi = (ev) => {
    const ora = livelloScelto.value;
    if (!ora?.motionPath) return;
    const p = puntoDa(ev);
    const dx = (p[0] - da[0]) / f, dy = (p[1] - da[1]) / f;
    const punti = prima.map((q, j) => (j === k ? [r1(q[0] + dx), r1(q[1] + dy)] : q));
    emit('update-layer', ora.id, { motionPath: { ...ora.motionPath, punti, path: dDaPunti(punti, ora.motionPath.dritte) } });
  };
  const fine = () => { window.removeEventListener('pointermove', muovi); window.removeEventListener('pointerup', fine); };
  window.addEventListener('pointermove', muovi);
  window.addEventListener('pointerup', fine);
}
// il punto della tela (px del formato) sotto il puntatore
function puntoDa(e) {
  const r = canvasEl.value.getBoundingClientRect();
  const s = effectiveScale.value || 1;
  return [r1((e.clientX - r.left) / s), r1((e.clientY - r.top) / s)];
}
// Il disegno: il primo punto è il centro del livello, poi un punto per clic (due clic quasi nello stesso posto, come
// quelli del doppio clic, contano uno)
const disegnando = ref(false);
const puntiDisegno = ref([]);
const cursoreDisegno = ref(null);
const dDisegno = computed(() => {
  const pts = cursoreDisegno.value ? [...puntiDisegno.value, cursoreDisegno.value] : puntiDisegno.value;
  return dDaPunti(pts, !!livelloScelto.value?.motionPath?.dritte);
});
function iniziaDisegno() {
  const l = livelloScelto.value;
  if (!l) { emit('fine-disegno'); return; }
  misuraAncora();
  puntiDisegno.value = [[r1(ancora.value.x), r1(ancora.value.y)]];
  cursoreDisegno.value = null;
  disegnando.value = true;
  window.addEventListener('keydown', tastiDisegno, true);
}
function aggiungiPunto(e) {
  const p = puntoDa(e);
  const u = puntiDisegno.value[puntiDisegno.value.length - 1];
  if (u && Math.hypot(p[0] - u[0], p[1] - u[1]) < 4) return;
  puntiDisegno.value = [...puntiDisegno.value, p];
}
function chiudiDisegno() {
  disegnando.value = false;
  puntiDisegno.value = [];
  cursoreDisegno.value = null;
  window.removeEventListener('keydown', tastiDisegno, true);
  emit('fine-disegno');
}
function finisciDisegno() {
  const l = livelloScelto.value;
  const pts = puntiDisegno.value.slice();
  chiudiDisegno();
  if (!l || pts.length < 2) return;
  const mp = l.motionPath || {};
  const f = fattorePercorso(mp, props.larghezzaFormato);
  const a = pts[0];
  const punti = pts.map((p) => [r1((p[0] - a[0]) / f), r1((p[1] - a[1]) / f)]);
  emit('update-layer', l.id, { motionPath: { duration: 3000, autoRotate: false, loop: true, easing: 'linear', ...mp, preset: 'disegnato', punti, path: dDaPunti(punti, mp.dritte) } });
}
function tastiDisegno(e) {
  if (e.key === 'Escape') { e.preventDefault(); e.stopPropagation(); chiudiDisegno(); }
  else if (e.key === 'Enter') { e.preventDefault(); e.stopPropagation(); finisciDisegno(); }
}
watch(() => props.disegnoPercorso, (si) => { if (si && !disegnando.value) iniziaDisegno(); else if (!si && disegnando.value) chiudiDisegno(); });
watch(() => props.selectedLayerId, () => { if (disegnando.value) chiudiDisegno(); });
// «Prova il percorso»: il livello lo percorre una volta sulla tela, come sul sito (si somma alla sua posizione)
watch(() => props.provaPercorso, () => {
  const l = livelloScelto.value;
  const mp = l?.motionPath;
  const el = l && elLivello(l.id);
  if (!mp?.path || !el?.animate) return;
  const frames = fotogrammiPercorso(mp.path, fattorePercorso(mp, props.larghezzaFormato), !!mp.autoRotate);
  if (frames.length < 2) return;
  if (provaInCorso) { try { provaInCorso.cancel(); } catch (_) { /* già finita */ } }
  try {
    provaInCorso = el.animate(frames, { duration: Number(mp.duration) || 3000, easing: easingToCSS(mp.easing || 'linear'), iterations: 1, composite: 'add' });
  } catch (_) { provaInCorso = null; }
});

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
  window.removeEventListener('keydown', tastiDisegno, true);
  resizeObs?.disconnect();
  window.removeEventListener('pointermove', onPointerMove);
  window.removeEventListener('pointerup', onPointerUp);
});
</script>

<style scoped>
/* Il percorso del livello scelto e il disegno (1.4.1473): il colore d'accento dell'editor */
.mps-percorso { position: absolute; inset: 0; z-index: 150; pointer-events: none; overflow: visible; }
.mps-percorso-linea { fill: none; stroke: rgb(var(--olo-primary-rgb, 129 140 248)); stroke-dasharray: 6 5; stroke-linecap: round; }
.mps-percorso-inizio { fill: rgb(var(--olo-primary-rgb, 129 140 248)); stroke: #fff; stroke-width: 1.5; }
.mps-percorso-punto { fill: #fff; stroke: rgb(var(--olo-primary-rgb, 129 140 248)); stroke-width: 2; pointer-events: auto; cursor: move; }
.mps-disegno { position: absolute; inset: 0; z-index: 160; cursor: crosshair; }
.mps-disegno svg { position: absolute; inset: 0; overflow: visible; pointer-events: none; }
.mps-disegno-barra { position: absolute; top: 8px; left: 50%; transform: translateX(-50%); z-index: 20; display: flex; align-items: center; gap: 8px; padding: 5px 10px; border-radius: 6px; background: #1f2937; border: 1px solid rgb(var(--olo-primary-rgb, 129 140 248)); color: #e5e7eb; font-size: 11px; white-space: nowrap; }
.mps-disegno-btn { font: inherit; font-size: 11px; padding: 2px 10px; border-radius: 4px; border: 1px solid #4b5563; background: #374151; color: #e5e7eb; cursor: pointer; }
.mps-disegno-btn.is-si { background: rgb(var(--olo-primary-rgb, 129 140 248)); border-color: transparent; color: #fff; font-weight: 600; }
.mps-disegno-btn:disabled { opacity: .45; cursor: default; }
.mps-disegno-btn:focus-visible { outline: 2px solid #fff; outline-offset: 1px; }
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
