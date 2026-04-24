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
const { handleDropFromSidebar, handleDropIntoColumn, handleGlobalWidgetDrop, handleGlobalWidgetDropIntoColumn } = useDragDrop();
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

function onNativeDragStart() {
  nativeDragActive.value = true;
  // Sync DOM update: attiva pointer-events E visibilità immediatamente, bypass
  // del Vue reactivity async (altrimenti il primo dragover arriva prima del re-render).
  if (overlayEl.value) {
    overlayEl.value.style.pointerEvents = 'auto';
    overlayEl.value.style.opacity = '1';
  }
}
function onNativeDragEnd() {
  nativeDragActive.value = false;
  if (overlayEl.value) {
    overlayEl.value.style.pointerEvents = '';
    overlayEl.value.style.opacity = '';
  }
}

onMounted(() => {
  document.addEventListener('dragstart', onNativeDragStart, true);
  document.addEventListener('dragend', onNativeDragEnd, true);
  document.addEventListener('drop', onNativeDragEnd, true);
});
onBeforeUnmount(() => {
  document.removeEventListener('dragstart', onNativeDragStart, true);
  document.removeEventListener('dragend', onNativeDragEnd, true);
  document.removeEventListener('drop', onNativeDragEnd, true);
});

// ── Hit-state reattivo (aggiornato in onDrag) ─────────────────
const hit = ref(null); // { columnId, insertIndex, lineY, colRect, x, y, iframeRect }

const hasHit = computed(() => hit.value !== null);
const showLine = computed(() => hasHit.value && !hit.value.columnId);
const showColumnHi = computed(() => hasHit.value && !!hit.value.columnId);

const dropLineStyle = computed(() => {
  if (!hit.value) return {};
  // lineY è in coordinate iframe-interne (già divise per zoom); riconvertiamo
  // in coordinate overlay (=iframe viewport) moltiplicando per zoom.
  const zoom = builderStore.canvasZoom / 100;
  const top = hit.value.lineY * zoom;
  return { top: `${top}px` };
});

const columnHiStyle = computed(() => {
  if (!hit.value || !hit.value.colRect) return {};
  const zoom = builderStore.canvasZoom / 100;
  const r = hit.value.colRect;
  return {
    top: `${r.top * zoom}px`,
    left: `${r.left * zoom}px`,
    width: `${(r.right - r.left) * zoom}px`,
    height: `${(r.bottom - r.top) * zoom}px`,
  };
});

const labelText = computed(() => {
  if (!hit.value) return '';
  if (hit.value.columnId) return 'Aggiungi dentro colonna';
  if (dnd.payload?.kind === 'global-widget') return 'Inserisci widget';
  if (dnd.payload?.tileType === 'section') return 'Inserisci sezione';
  if (dnd.payload?.tileType === 'row') return 'Inserisci riga';
  return 'Inserisci qui';
});

const labelStyle = computed(() => {
  if (!hit.value) return {};
  const zoom = builderStore.canvasZoom / 100;
  const y = hit.value.columnId
    ? hit.value.colRect.top * zoom
    : hit.value.lineY * zoom;
  return { top: `${Math.max(8, y - 28)}px`, left: '50%' };
});

// ── Hit-test contro iframeLayout snapshot ─────────────────────
function hitTest(clientX, clientY) {
  // Vue 3 può auto-unwrap i ref nelle prop; supportiamo entrambe le forme.
  const iframe = (props.iframeRef && 'value' in props.iframeRef)
    ? props.iframeRef.value
    : props.iframeRef;
  if (!iframe || typeof iframe.getBoundingClientRect !== 'function') return null;
  const iframeRect = iframe.getBoundingClientRect();
  const zoom = builderStore.canvasZoom / 100;

  // Il pointer può essere fuori iframe (es. sopra sidebar); in quel caso niente hit
  if (
    clientX < iframeRect.left || clientX > iframeRect.right ||
    clientY < iframeRect.top || clientY > iframeRect.bottom
  ) return null;

  const x = (clientX - iframeRect.left) / zoom;
  const y = (clientY - iframeRect.top) / zoom;
  const layout = builderStore.iframeLayout || { sections: [], columns: [] };

  // 1. Prova a matchare una colonna (più specifico)
  let columnId = null;
  let colRect = null;
  const payloadTile = dnd.payload?.tileType;
  const canDropInColumn = !(payloadTile === 'section' || payloadTile === 'row');
  if (canDropInColumn) {
    for (const col of layout.columns) {
      if (x >= col.left && x <= col.right && y >= col.top && y <= col.bottom) {
        columnId = col.id;
        colRect = col;
        break;
      }
    }
  }

  // 2. Calcola insertion index sezione
  let insertIndex = layout.sections.length;
  let lineY = 0;
  const sects = layout.sections;
  if (sects.length === 0) {
    lineY = iframeRect.height / zoom / 2;
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

  return { columnId, colRect, insertIndex, lineY, x, y, iframeRect };
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
  onDragEnter: ({ self }) => {
    hit.value = self.data.hit;
    dnd.setDropTarget(
      hit.value
        ? (hit.value.columnId ? { kind: 'column', id: hit.value.columnId } : { kind: 'section-gap', index: hit.value.insertIndex })
        : null,
      hit.value?.colRect || null
    );
  },
  onDrag: ({ self }) => {
    hit.value = self.data.hit;
    dnd.setDropTarget(
      hit.value
        ? (hit.value.columnId ? { kind: 'column', id: hit.value.columnId } : { kind: 'section-gap', index: hit.value.insertIndex })
        : null,
      hit.value?.colRect || null
    );
  },
  onDragLeave: () => {
    hit.value = null;
    dnd.clearDropTarget();
  },
  onDrop: ({ source, self }) => {
    const data = source.data;
    const target = self.data.hit;
    hit.value = null;
    if (!target) return;

    pushStateNow();
    dnd.markDropping();

    try {
      if (data.kind === 'tile-type') {
        if (target.columnId && data.tileType !== 'section' && data.tileType !== 'row') {
          handleDropIntoColumn(data.tileType, target.columnId);
        } else {
          handleDropFromSidebar(data.tileType, target.insertIndex);
        }
      } else if (data.kind === 'global-widget') {
        if (target.columnId) {
          handleGlobalWidgetDropIntoColumn(data.globalId, target.columnId);
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
  /* Sempre montato per garantire che Pragmatic registri il drop target in anticipo.
     Invisibile e inerte quando non c'è drag (pointer-events: none non blocca
     l'iframe sotto per i click normali dell'utente). */
  position: absolute;
  inset: 0;
  z-index: 100;
  pointer-events: none;
  opacity: 0;
  background: rgba(99, 102, 241, 0.08);
  border: 3px dashed rgba(99, 102, 241, 0.8);
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
  background: rgba(99, 102, 241, 0.07);
  border-color: rgba(99, 102, 241, 0.6);
  animation: none;
}
@keyframes olo-dnd-pulse {
  0%, 100% { border-color: rgba(99, 102, 241, 0.25); }
  50%      { border-color: rgba(99, 102, 241, 0.55); }
}

.olo-dnd-dropline {
  position: absolute;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg,
    rgba(99, 102, 241, 0) 0%,
    rgba(99, 102, 241, 0.9) 20%,
    rgba(99, 102, 241, 0.9) 80%,
    rgba(99, 102, 241, 0) 100%);
  border-radius: 2px;
  box-shadow: 0 0 8px rgba(99, 102, 241, 0.5);
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
  background: rgb(99, 102, 241);
  transform: translateY(-50%);
  box-shadow: 0 0 6px rgba(99, 102, 241, 0.7);
}
.olo-dnd-dropline::before { left: -4px; }
.olo-dnd-dropline::after  { right: -4px; }

.olo-dnd-colhi {
  position: absolute;
  background: rgba(99, 102, 241, 0.12);
  border: 2px solid rgba(99, 102, 241, 0.7);
  border-radius: 6px;
  pointer-events: none;
  box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
  transition: top 120ms cubic-bezier(0.4, 0, 0.2, 1),
              left 120ms cubic-bezier(0.4, 0, 0.2, 1),
              width 120ms cubic-bezier(0.4, 0, 0.2, 1),
              height 120ms cubic-bezier(0.4, 0, 0.2, 1);
}

.olo-dnd-label {
  position: absolute;
  transform: translateX(-50%);
  padding: 3px 10px;
  background: rgb(99, 102, 241);
  color: #fff;
  font-size: 11px;
  font-weight: 600;
  border-radius: 4px;
  box-shadow: 0 2px 8px rgba(99, 102, 241, 0.4);
  pointer-events: none;
  white-space: nowrap;
  letter-spacing: 0.02em;
}
</style>
