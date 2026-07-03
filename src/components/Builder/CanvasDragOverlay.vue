<template>
  <!-- Overlay parent-side sempre montato: il drop target Pragmatic deve essere
       disponibile PRIMA del primo dragover (se no il browser mostra cursor not-allowed).
       La visibilità e pointer-events sono toggle via CSS class, sincronizzata da
       listener nativi dragstart/dragend su document per zero-latency. -->
  <div
    ref="overlayEl"
    v-olo-drop-target="dropTargetOpts"
    class="olo-dnd-overlay"
    :class="{
      'olo-dnd-overlay--active': isActive,
      'olo-dnd-overlay--hit': hasHit,
    }"
  >
    <!-- Drop line (tra sezioni o a fine canvas) -->
    <div
      v-if="isActive && showLine"
      class="olo-dnd-dropline"
      :style="dropLineStyle"
    ></div>

    <!-- Highlight colonna (drop dentro una colonna esistente) -->
    <div
      v-if="isActive && showColumnHi"
      class="olo-dnd-colhi"
      :style="columnHiStyle"
    ></div>

    <!-- Label informativo -->
    <div
      v-if="isActive && hasHit"
      class="olo-dnd-label"
      :style="labelStyle"
    >{{ labelText }}</div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { useBuilderStore } from '@/stores/builder';
import { useDnDStore } from '@/stores/dnd';
import { useDragDrop } from '@/composables/useDragDrop';
import { useHistory } from '@/composables/useHistory';
import { vOloDropTarget, isOloData } from '@/composables/useDnD';

const props = defineProps({
  /** Ref al DOM <iframe> target. Obbligatorio per il hit-test. */
  iframeRef: { type: Object, required: true },
});

const builderStore = useBuilderStore();
const dnd = useDnDStore();
const { handleDropFromSidebar, handleDropIntoColumn, handleDropIntoColumnAt, handleGlobalWidgetDrop, handleGlobalWidgetDropIntoColumn, handleGlobalWidgetDropIntoColumnAt } = useDragDrop();
const { pushStateNow } = useHistory();

const overlayEl = ref(null);

// ── Visibilità sincronizzata con dragstart/dragend nativo ─────
// Usa un ref locale aggiornato da listener DOM (non reattivo a Pinia) per evitare
// latenza del reactivity system Vue. Il drop target Pragmatic resta sempre registrato.
const nativeDragActive = ref(false);

const isActive = computed(() => {
  if (!nativeDragActive.value) return false;
  const p = dnd.payload;
  if (!p) return false;
  return p.source === 'sidebar';
});

// Durante il drag l'iframe può rifluire (immagini lazy, font, auto-scroll):
// ri-richiedi lo snapshot layout a intervalli così l'hit-test non lavora su
// rettangoli stale (lo snapshot iniziale arriva dall'onDragStart della sidebar).
let layoutRefreshTimer = null;

function onNativeDragStart() {
  nativeDragActive.value = true;
  // Sync DOM update: attiva pointer-events E visibilità immediatamente, bypass
  // del Vue reactivity async (altrimenti il primo dragover arriva prima del re-render).
  if (overlayEl.value) {
    overlayEl.value.style.pointerEvents = 'auto';
    overlayEl.value.style.opacity = '1';
  }
  if (!layoutRefreshTimer) {
    layoutRefreshTimer = setInterval(() => {
      const iframe = getIframeEl();
      if (iframe && iframe.contentWindow) {
        iframe.contentWindow.postMessage({ type: 'olo:request-layout' }, '*');
      }
    }, 400);
  }
}
function onNativeDragEnd() {
  nativeDragActive.value = false;
  stopAutoScroll();
  if (layoutRefreshTimer) { clearInterval(layoutRefreshTimer); layoutRefreshTimer = null; }
  if (overlayEl.value) {
    overlayEl.value.style.pointerEvents = '';
    overlayEl.value.style.opacity = '';
  }
}

// ── Auto-scroll iframe ────────────────────────────────────────
// Quando il pointer è in una "hot zone" ai bordi superiore/inferiore dell'iframe,
// un loop RAF invia messaggi allo runtime iframe-side per scrollare il contenuto.
// Il parent non può scrollare direttamente l'iframe (contenuto cross-doc).
// Il loop continua anche a PUNTATORE FERMO nella zona (il gesto naturale è
// "porta il mouse al bordo e aspetta") e ricalcola l'hit mentre il contenuto
// scorre, così dropline/insertIndex restano allineati a ciò che si vede.
const AUTO_SCROLL_ZONE = 80; // px
let autoScrollActive = false;
let autoScrollDir = 0;       // -1 = up, 0 = idle, +1 = down
let autoScrollStrength = 0;  // 0..1 (più vicino al bordo = più veloce)
let autoScrollRaf = null;
let autoScrollLastTs = 0;
let lastDragInput = null;

function updateAutoScroll(input) {
  if (!input) { stopAutoScroll(); return; }
  lastDragInput = input;
  const iframe = getIframeEl();
  if (!iframe || typeof iframe.getBoundingClientRect !== 'function') { stopAutoScroll(); return; }
  const rect = iframe.getBoundingClientRect();
  const y = input.clientY;
  let dir = 0;
  let strength = 0;
  if (y - rect.top < AUTO_SCROLL_ZONE) {
    dir = -1;
    strength = Math.max(0, (AUTO_SCROLL_ZONE - (y - rect.top)) / AUTO_SCROLL_ZONE);
  } else if (rect.bottom - y < AUTO_SCROLL_ZONE) {
    dir = 1;
    strength = Math.max(0, (AUTO_SCROLL_ZONE - (rect.bottom - y)) / AUTO_SCROLL_ZONE);
  }
  if (dir === 0) { stopAutoScroll(); return; }

  autoScrollDir = dir;
  autoScrollStrength = strength;
  if (!autoScrollActive) {
    autoScrollActive = true;
    autoScrollLastTs = 0;
    autoScrollRaf = requestAnimationFrame(autoScrollTick);
  }
}

function autoScrollTick(ts) {
  if (!autoScrollActive || !nativeDragActive.value) { stopAutoScroll(); return; }
  // Delta scalato sul tempo reale del frame: stessa velocità su 60/120/144Hz.
  const dt = autoScrollLastTs ? Math.min(ts - autoScrollLastTs, 50) : 16.7;
  autoScrollLastTs = ts;
  const perFrame = (4 + autoScrollStrength * 18) * (dt / 16.7); // 4–22 px @60fps
  postScroll(Math.round(perFrame * autoScrollDir) || autoScrollDir);

  // Il contenuto scorre sotto il puntatore (anche fermo): ricalcola l'hit così
  // la dropline e il punto di inserimento seguono ciò che l'utente vede.
  if (lastDragInput) {
    const result = hitTest(lastDragInput.clientX, lastDragInput.clientY);
    hit.value = result;
    dnd.setDropTarget(
      result
        ? (result.columnId ? { kind: 'column', id: result.columnId } : { kind: 'section-gap', index: result.insertIndex })
        : null,
      result?.colRect || null
    );
  }
  autoScrollRaf = requestAnimationFrame(autoScrollTick);
}

function stopAutoScroll() {
  if (autoScrollRaf) { cancelAnimationFrame(autoScrollRaf); autoScrollRaf = null; }
  if (!autoScrollActive) return;
  autoScrollActive = false;
  autoScrollDir = 0;
  autoScrollStrength = 0;
  const iframe = getIframeEl();
  if (iframe && iframe.contentWindow) {
    iframe.contentWindow.postMessage({ type: 'olo:auto-scroll-stop' }, '*');
  }
}

function postScroll(delta) {
  const iframe = getIframeEl();
  if (!iframe || !iframe.contentWindow) return;
  iframe.contentWindow.postMessage({ type: 'olo:auto-scroll', delta }, '*');
}

// Listener scroll dentro l'iframe: forza re-render degli overlay positions.
// Attaccato al contentWindow dell'iframe quando pronto.
let scrollListenerAttached = false;
function onIframeScroll() {
  scrollTick.value = (scrollTick.value + 1) & 0xffff;
}
function tryAttachIframeScroll() {
  if (scrollListenerAttached) return;
  const iframe = getIframeEl();
  try {
    const w = iframe?.contentWindow;
    if (w) {
      w.addEventListener('scroll', onIframeScroll, { passive: true });
      scrollListenerAttached = true;
    }
  } catch (e) { /* cross-origin, ignora */ }
}

onMounted(() => {
  // v3.55.26 — useDnD.js (drag custom pointer-events) emette olo:drag-start /
  // olo:drag-end. Prima ascoltavamo dragstart/dragend HTML5 nativo, ma ora
  // il drag non passa più dall'API HTML5 → eventi mai firati → overlay morto.
  window.addEventListener('olo:drag-start', onNativeDragStart);
  window.addEventListener('olo:drag-end',   onNativeDragEnd);
  // Prova subito + retry su un paio di tick (l'iframe carica async)
  tryAttachIframeScroll();
  setTimeout(tryAttachIframeScroll, 500);
  setTimeout(tryAttachIframeScroll, 2000);
});
onBeforeUnmount(() => {
  window.removeEventListener('olo:drag-start', onNativeDragStart);
  window.removeEventListener('olo:drag-end',   onNativeDragEnd);
  const iframe = getIframeEl();
  try {
    iframe?.contentWindow?.removeEventListener('scroll', onIframeScroll);
  } catch (e) {}
});

// ── Hit-state reattivo (aggiornato in onDrag) ─────────────────
const hit = ref(null); // { columnId, insertIndex, lineY, colRect, x, y, iframeRect }

const hasHit = computed(() => hit.value !== null);
// Y (document-relative iframe) della dropline: dentro una colonna usa la posizione
// element-level calcolata dall'hit-test, altrimenti il gap tra sezioni.
const lineYDoc = computed(() => {
  if (!hit.value) return null;
  return hit.value.columnId ? (hit.value.columnLineY ?? null) : (hit.value.lineY ?? null);
});
const showLine = computed(() => hasHit.value && lineYDoc.value != null);
const showColumnHi = computed(() => hasHit.value && !!hit.value.columnId);

// Le coordinate in hit sono DOCUMENT-relative nell'iframe; per piazzare
// gli overlay viewport-relative sottraiamo lo scrollY/scrollX corrente.
// Leggiamo ad ogni tick lo scroll dell'iframe (può cambiare anche durante drag).
const scrollTick = ref(0); // forza reattività
function readScroll() { return hit.value?.scroll || getIframeScroll(); }

const dropLineStyle = computed(() => {
  if (!hit.value || lineYDoc.value == null) return {};
  void scrollTick.value;
  const zoom = builderStore.canvasZoom / 100;
  const cur = getIframeScroll();
  const top = (lineYDoc.value - cur.y) * zoom;
  // Dentro una colonna la dropline è larga quanto la colonna (non full-width).
  if (hit.value.columnId && hit.value.colRect) {
    const r = hit.value.colRect;
    return {
      top: `${top}px`,
      left: `${(r.left - cur.x) * zoom}px`,
      width: `${(r.right - r.left) * zoom}px`,
      right: 'auto',
    };
  }
  return { top: `${top}px` };
});

const columnHiStyle = computed(() => {
  if (!hit.value || !hit.value.colRect) return {};
  void scrollTick.value;
  const zoom = builderStore.canvasZoom / 100;
  const cur = getIframeScroll();
  const r = hit.value.colRect;
  return {
    top: `${(r.top - cur.y) * zoom}px`,
    left: `${(r.left - cur.x) * zoom}px`,
    width: `${(r.right - r.left) * zoom}px`,
    height: `${(r.bottom - r.top) * zoom}px`,
  };
});

const labelText = computed(() => {
  if (!hit.value) return '';
  if (hit.value.columnId) return 'Inserisci qui';
  if (dnd.payload?.kind === 'global-widget') return 'Inserisci widget';
  if (dnd.payload?.tileType === 'section') return 'Inserisci sezione';
  if (dnd.payload?.tileType === 'row') return 'Inserisci riga';
  return 'Inserisci qui';
});

const labelStyle = computed(() => {
  if (!hit.value) return {};
  void scrollTick.value;
  const zoom = builderStore.canvasZoom / 100;
  const cur = getIframeScroll();
  const yDoc = hit.value.columnId ? hit.value.colRect.top : hit.value.lineY;
  const y = (yDoc - cur.y) * zoom;
  return { top: `${Math.max(8, y - 28)}px`, left: '50%' };
});

// ── Hit-test contro iframeLayout snapshot ─────────────────────
// Le coordinate in layout.sections/columns sono DOCUMENT-RELATIVE (include
// scrollY dell'iframe al momento dello snapshot). Per confrontarle con la
// posizione del pointer bisogna aggiungere lo scrollY CORRENTE dell'iframe.
function getIframeEl() {
  return (props.iframeRef && 'value' in props.iframeRef)
    ? props.iframeRef.value
    : props.iframeRef;
}
function getIframeScroll() {
  const iframe = getIframeEl();
  try {
    const w = iframe?.contentWindow;
    return {
      x: w?.pageXOffset || w?.scrollX || 0,
      y: w?.pageYOffset || w?.scrollY || 0,
    };
  } catch (e) {
    return { x: 0, y: 0 };
  }
}

function hitTest(clientX, clientY) {
  const iframe = getIframeEl();
  if (!iframe || typeof iframe.getBoundingClientRect !== 'function') return null;
  const iframeRect = iframe.getBoundingClientRect();
  const zoom = builderStore.canvasZoom / 100;

  if (
    clientX < iframeRect.left || clientX > iframeRect.right ||
    clientY < iframeRect.top || clientY > iframeRect.bottom
  ) return null;

  const scroll = getIframeScroll();
  // x/y in coordinate DOCUMENT dell'iframe (stesse usate in layout)
  const x = (clientX - iframeRect.left) / zoom + scroll.x;
  const y = (clientY - iframeRect.top) / zoom + scroll.y;
  const layout = builderStore.iframeLayout || { sections: [], columns: [], containers: [] };

  // 1a. Prova a matchare un container generico (floatingpanel) — più specifico delle colonne
  let columnId = null;
  let colRect = null;
  const payloadTile = dnd.payload?.tileType;
  const canDropInColumn = !(payloadTile === 'section' || payloadTile === 'row');
  if (canDropInColumn && Array.isArray(layout.containers)) {
    for (const cnr of layout.containers) {
      if (x >= cnr.left && x <= cnr.right && y >= cnr.top && y <= cnr.bottom) {
        columnId = cnr.id;
        colRect = cnr;
        break;
      }
    }
  }

  // 1b. Se non si è in un container, prova a matchare una colonna
  if (canDropInColumn && !columnId) {
    for (const col of layout.columns) {
      if (x >= col.left && x <= col.right && y >= col.top && y <= col.bottom) {
        columnId = col.id;
        colRect = col;
        break;
      }
    }
  }

  // 1c. Dentro una colonna: calcola l'INDICE element-level (dropline precisa "tra
  // due tile") dai rettangoli degli elementi figli. Prima si appendeva sempre in
  // fondo; ora si inserisce esattamente dove cade il puntatore.
  let columnInsertIndex = null;
  let columnLineY = null;
  if (columnId) {
    const els = (layout.elements || []).filter(el => el.columnId === columnId);
    if (els.length === 0) {
      columnInsertIndex = 0;
      columnLineY = colRect ? (colRect.top + colRect.bottom) / 2 : y;
    } else {
      const sorted = els.slice().sort((a, b) => a.top - b.top);
      let placed = false;
      for (const el of sorted) {
        const mid = (el.top + el.bottom) / 2;
        if (y < mid) { columnInsertIndex = el.index; columnLineY = el.top; placed = true; break; }
      }
      if (!placed) {
        const last = sorted[sorted.length - 1];
        columnInsertIndex = last.index + 1;
        columnLineY = last.bottom;
      }
    }
  }

  // 2. Calcola insertion index sezione
  let insertIndex = layout.sections.length;
  let lineY = 0; // document-relative nell'iframe
  const sects = layout.sections;
  if (sects.length === 0) {
    // Iframe vuoto: centro viewport → converti in doc-relative
    lineY = scroll.y + iframeRect.height / zoom / 2;
  } else {
    let found = false;
    for (let i = 0; i < sects.length; i++) {
      const midY = (sects[i].top + sects[i].bottom) / 2;
      if (y < midY) {
        insertIndex = i;
        lineY = sects[i].top;
        found = true;
        break;
      }
    }
    if (!found) lineY = sects[sects.length - 1].bottom;
  }

  return { columnId, colRect, columnInsertIndex, columnLineY, insertIndex, lineY, x, y, iframeRect, scroll };
}

// ── Drop target options per Pragmatic (oggetto STABILE, no computed) ─────────
// Le callback fanno closure sui ref/store correnti, quindi non serve ricreare
// l'oggetto a ogni re-render (ricrearlo interromperebbe il drag in corso).
const dropTargetOpts = {
  canDrop: ({ source }) => {
    if (!isOloData(source.data)) return false;
    return source.data.source === 'sidebar';
  },
  getData: ({ input }) => {
    const result = hitTest(input.clientX, input.clientY);
    return {
      _olo: true,
      kind: 'canvas-overlay',
      hit: result,
    };
  },
  getIsSticky: () => true,
  onDragEnter: ({ self, location } = {}) => {
    if (!self?.data) return;
    hit.value = self.data.hit;
    dnd.setDropTarget(
      hit.value
        ? (hit.value.columnId ? { kind: 'column', id: hit.value.columnId } : { kind: 'section-gap', index: hit.value.insertIndex })
        : null,
      hit.value?.colRect || null
    );
    updateAutoScroll(location?.current?.input);
  },
  onDrag: ({ self, location } = {}) => {
    if (!self?.data) return;
    hit.value = self.data.hit;
    dnd.setDropTarget(
      hit.value
        ? (hit.value.columnId ? { kind: 'column', id: hit.value.columnId } : { kind: 'section-gap', index: hit.value.insertIndex })
        : null,
      hit.value?.colRect || null
    );
    updateAutoScroll(location?.current?.input);
  },
  onDragLeave: () => {
    hit.value = null;
    dnd.clearDropTarget();
    stopAutoScroll();
  },
  onDrop: ({ source, self } = {}) => {
    if (!source?.data || !self?.data) return;
    const data = source.data;
    // Usa hit.value (tenuto fresco anche dal tick di auto-scroll a puntatore
    // fermo) e fallback su self.data.hit (ultimo getData del motore).
    const target = hit.value || self.data.hit;
    hit.value = null;
    if (!target) return;

    stopAutoScroll();
    pushStateNow();
    dnd.markDropping();

    try {
      if (data.kind === 'tile-type') {
        if (target.columnId && data.tileType !== 'section' && data.tileType !== 'row') {
          if (typeof target.columnInsertIndex === 'number') {
            handleDropIntoColumnAt(data.tileType, target.columnId, target.columnInsertIndex);
          } else {
            handleDropIntoColumn(data.tileType, target.columnId);
          }
        } else {
          handleDropFromSidebar(data.tileType, target.insertIndex);
        }
      } else if (data.kind === 'global-widget') {
        if (target.columnId) {
          if (typeof target.columnInsertIndex === 'number') {
            handleGlobalWidgetDropIntoColumnAt(data.globalId, target.columnId, target.columnInsertIndex);
          } else {
            handleGlobalWidgetDropIntoColumn(data.globalId, target.columnId);
          }
        } else {
          handleGlobalWidgetDrop(data.globalId, target.insertIndex);
        }
      }
    } finally {
      if (dnd.phase === 'dropping') dnd.endDrag();
    }
  },
};
</script>

<style scoped>
.olo-dnd-overlay {
  /* Accent fisso del chrome builder (mai --olo-color-primary, che è rimappato
     col colore cliente). Definito locale al componente come da convenzione. */
  --olo-ui-accent: #e8622a;
  --olo-ui-accent-rgb: 232, 98, 42;
  /* Sempre montato per garantire che il motore registri il drop target in anticipo.
     Invisibile e inerte quando non c'è drag (pointer-events: none non blocca
     l'iframe sotto per i click normali dell'utente). */
  position: absolute;
  inset: 0;
  z-index: 100;
  pointer-events: none;
  opacity: 0;
  background: rgba(var(--olo-ui-accent-rgb), 0.08);
  border: 3px dashed rgba(var(--olo-ui-accent-rgb), 0.8);
  border-radius: 6px;
  cursor: copy;
  /* NO transition: l'overlay deve apparire istantaneamente al dragstart,
     altrimenti l'utente percepisce un lag o non vede l'overlay affatto. */
}
.olo-dnd-overlay--active {
  pointer-events: auto;
  opacity: 1;
  animation: olo-dnd-pulse 1.6s ease-in-out infinite;
}
.olo-dnd-overlay--hit {
  background: rgba(var(--olo-ui-accent-rgb), 0.07);
  border-color: rgba(var(--olo-ui-accent-rgb), 0.6);
  animation: none;
}
@keyframes olo-dnd-pulse {
  0%, 100% { border-color: rgba(232, 98, 42, 0.25); }
  50%      { border-color: rgba(232, 98, 42, 0.55); }
}

.olo-dnd-dropline {
  position: absolute;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg,
    rgba(var(--olo-ui-accent-rgb), 0) 0%,
    rgba(var(--olo-ui-accent-rgb), 0.9) 20%,
    rgba(var(--olo-ui-accent-rgb), 0.9) 80%,
    rgba(var(--olo-ui-accent-rgb), 0) 100%);
  border-radius: 2px;
  box-shadow: 0 0 8px rgba(var(--olo-ui-accent-rgb), 0.5);
  pointer-events: none;
  transform: translateY(-1.5px);
  transition: top 120ms cubic-bezier(0.4, 0, 0.2, 1);
}
.olo-dnd-dropline::before,
.olo-dnd-dropline::after {
  content: '';
  position: absolute;
  top: 50%;
  width: 9px;
  height: 9px;
  border-radius: 50%;
  background: var(--olo-ui-accent);
  transform: translateY(-50%);
  box-shadow: 0 0 6px rgba(var(--olo-ui-accent-rgb), 0.7);
}
.olo-dnd-dropline::before { left: -4px; }
.olo-dnd-dropline::after  { right: -4px; }

.olo-dnd-colhi {
  position: absolute;
  background: rgba(var(--olo-ui-accent-rgb), 0.12);
  border: 2px solid rgba(var(--olo-ui-accent-rgb), 0.7);
  border-radius: 6px;
  pointer-events: none;
  box-shadow: 0 0 0 4px rgba(var(--olo-ui-accent-rgb), 0.15);
  transition: top 120ms cubic-bezier(0.4, 0, 0.2, 1),
              left 120ms cubic-bezier(0.4, 0, 0.2, 1),
              width 120ms cubic-bezier(0.4, 0, 0.2, 1),
              height 120ms cubic-bezier(0.4, 0, 0.2, 1);
}

.olo-dnd-label {
  position: absolute;
  transform: translateX(-50%);
  padding: 3px 10px;
  background: var(--olo-ui-accent);
  color: #fff;
  font-size: 11px;
  font-weight: 600;
  border-radius: 4px;
  box-shadow: 0 2px 8px rgba(var(--olo-ui-accent-rgb), 0.4);
  pointer-events: none;
  white-space: nowrap;
  letter-spacing: 0.02em;
}
</style>
