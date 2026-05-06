/**
 * useDnD — API Vue 3 per Pragmatic drag-and-drop (Atlassian).
 *
 * Espone:
 *   - vOloDraggable  direttiva v-olo-draggable="{ getInitialData, onDragStart, onDrop, dragHandle }"
 *   - vOloDropTarget direttiva v-olo-drop-target="{ canDrop, getData, onDragEnter, onDrag, onDragLeave, onDrop }"
 *   - useDragMonitor composable per monitor globale
 *   - useAutoScroll  composable per auto-scroll su container
 *   - installDnDSafetyNet() — registra listener Esc/blur/visibilitychange una sola volta per app
 *
 * Tutte le cleanup function restituite da Pragmatic vengono salvate su __oloCleanup e
 * invocate in unmounted. I re-mount applicano nuove opzioni.
 *
 * Il sistema si integra con lo store `dnd.js` (FSM) che è l'unico source-of-truth
 * per phase / payload / dropTarget.
 */
import { onMounted, onBeforeUnmount } from 'vue';
import {
  draggable,
  dropTargetForElements,
  monitorForElements,
} from '@atlaskit/pragmatic-drag-and-drop/element/adapter';
import { autoScrollForElements } from '@atlaskit/pragmatic-drag-and-drop-auto-scroll/element';
import { attachClosestEdge, extractClosestEdge } from '@atlaskit/pragmatic-drag-and-drop-hitbox/closest-edge';
import { useDnDStore } from '@/stores/dnd';

// ───────────────────────────────────────────────────────────────────────────
// Direttive
// ───────────────────────────────────────────────────────────────────────────

/**
 * v-olo-draggable="{ getInitialData, onDragStart, onDrop, canDrag, dragHandle, onGenerateDragPreview }"
 *
 * `getInitialData` DEVE ritornare un oggetto serializzabile usato nel monitor.
 * Convenzione Olobuild: includere sempre { _olo: true, source, kind, ... }.
 *
 * `dragHandle` può essere un selettore CSS (es. '.olo-section-grip'); se passato,
 * il drag parte solo se il pointerdown è sul sotto-elemento.
 */
export const vOloDraggable = {
  mounted(el, binding) {
    applyDraggable(el, binding.value || {});
  },
  updated(el, binding) {
    const newOpts = binding.value || {};
    el.__oloDraggableOpts = newOpts;
    // Se il dragHandle è un selettore stringa, Pragmatic ha salvato un Element
    // reference RISOLTO al mount-time. Se Vue re-renderizza la riga (es.
    // StructureTree dopo selezione / expand) l'Element originale viene
    // detached → Pragmatic rifiuta il drag (mostra il cursor "no-drop"
    // ovunque). Quando il selettore non match più sull'handle salvato,
    // ricreiamo la registrazione Pragmatic con il nuovo Element.
    if (typeof newOpts.dragHandle === 'string') {
      const currentHandle = el.querySelector(newOpts.dragHandle) || null;
      if (el.__oloDragHandle !== currentHandle) {
        teardownDraggable(el);
        applyDraggable(el, newOpts);
      }
    }
  },
  unmounted(el) {
    teardownDraggable(el);
  },
};

/**
 * v-olo-drop-target="{ canDrop, getData, onDragEnter, onDrag, onDragLeave, onDrop, getIsSticky }"
 *
 * `getData` riceve { source, element, input } e DEVE ritornare un oggetto che
 * identifica il target (es. { _olo: true, kind: 'column', id: 'abc' }).
 * Per drop ordinati (sopra/sotto un sibling), usare `attachClosestEdge`:
 *
 *   getData: ({ input, element }) => attachClosestEdge(
 *     { kind: 'section-edge', id: section.id },
 *     { element, input, allowedEdges: ['top', 'bottom'] }
 *   )
 */
export const vOloDropTarget = {
  mounted(el, binding) {
    applyDropTarget(el, binding.value || {});
  },
  updated(el, binding) {
    // Stabile: aggiorna solo le opts, non re-registra Pragmatic.
    el.__oloDropTargetOpts = binding.value || {};
  },
  unmounted(el) {
    teardownDropTarget(el);
  },
};

function applyDraggable(el, opts) {
  // CRITICO: usiamo uno slot SEPARATO da __oloDropTargetOpts. Quando
  // v-olo-draggable e v-olo-drop-target sono usati sullo stesso element
  // (es. StructureTree st-item) condividere uno slot unico fa sì che la
  // seconda directive sovrascriva la prima → getInitialData del draggable
  // viene perso → source.data undefined → tutti i drop vengono rifiutati.
  el.__oloDraggableOpts = opts;

  const resolveDragHandle = () => {
    const o = el.__oloDraggableOpts || {};
    if (!o.dragHandle) return undefined;
    if (typeof o.dragHandle === 'string') {
      return el.querySelector(o.dragHandle) || undefined;
    }
    return o.dragHandle;
  };

  const handle = resolveDragHandle();
  el.__oloDragHandle = handle || null;
  const cleanup = draggable({
    element: el,
    dragHandle: handle,
    getInitialData: (arg) => el.__oloDraggableOpts?.getInitialData?.(arg),
    canDrag: (arg) => (el.__oloDraggableOpts?.canDrag ? el.__oloDraggableOpts.canDrag(arg) : true),
    onGenerateDragPreview: (arg) => el.__oloDraggableOpts?.onGenerateDragPreview?.(arg),
    onDragStart: (arg) => el.__oloDraggableOpts?.onDragStart?.(arg),
    onDrag: (arg) => el.__oloDraggableOpts?.onDrag?.(arg),
    onDrop: (arg) => el.__oloDraggableOpts?.onDrop?.(arg),
  });
  el.__oloDraggableCleanup = cleanup;
}

function applyDropTarget(el, opts) {
  // Vedi commento in applyDraggable: slot separato per non sovrascrivere
  // gli opts del draggable montato sullo stesso element.
  el.__oloDropTargetOpts = opts;

  const cleanup = dropTargetForElements({
    element: el,
    canDrop: (arg) => (el.__oloDropTargetOpts?.canDrop ? el.__oloDropTargetOpts.canDrop(arg) : true),
    getData: (arg) => (el.__oloDropTargetOpts?.getData ? el.__oloDropTargetOpts.getData(arg) : {}),
    getIsSticky: (arg) => (el.__oloDropTargetOpts?.getIsSticky ? el.__oloDropTargetOpts.getIsSticky(arg) : false),
    onDragEnter: (arg) => el.__oloDropTargetOpts?.onDragEnter?.(arg),
    onDrag: (arg) => el.__oloDropTargetOpts?.onDrag?.(arg),
    onDragLeave: (arg) => el.__oloDropTargetOpts?.onDragLeave?.(arg),
    onDrop: (arg) => el.__oloDropTargetOpts?.onDrop?.(arg),
  });
  el.__oloDropTargetCleanup = cleanup;
}

function teardownDraggable(el) {
  if (typeof el.__oloDraggableCleanup === 'function') el.__oloDraggableCleanup();
  el.__oloDraggableCleanup = null;
  el.__oloDragHandle = null;
}

function teardownDropTarget(el) {
  if (typeof el.__oloDropTargetCleanup === 'function') el.__oloDropTargetCleanup();
  el.__oloDropTargetCleanup = null;
}

// ───────────────────────────────────────────────────────────────────────────
// Composables
// ───────────────────────────────────────────────────────────────────────────

/**
 * Registra un monitor globale per osservare tutti i drag in-app.
 * Chiamato tipicamente una sola volta dal componente radice del builder.
 */
export function useDragMonitor(opts) {
  let cleanup = null;
  onMounted(() => {
    cleanup = monitorForElements({
      canMonitor: opts.canMonitor,
      onDragStart: opts.onDragStart,
      onDrag: opts.onDrag,
      onDrop: opts.onDrop,
    });
  });
  onBeforeUnmount(() => {
    if (cleanup) cleanup();
    cleanup = null;
  });
}

/**
 * Attiva auto-scroll per un container quando si trascina vicino ai bordi.
 * `getElement` riceve il ref al DOM element; opts vengono passati direttamente.
 */
export function useAutoScroll(getElement, opts = {}) {
  let cleanup = null;
  onMounted(() => {
    const el = typeof getElement === 'function' ? getElement() : getElement?.value;
    if (!el) return;
    cleanup = autoScrollForElements({
      element: el,
      canScroll: opts.canScroll,
      getAllowedAxis: opts.getAllowedAxis,
      getConfiguration: opts.getConfiguration,
    });
  });
  onBeforeUnmount(() => {
    if (cleanup) cleanup();
    cleanup = null;
  });
}

// ───────────────────────────────────────────────────────────────────────────
// Safety net globale (Esc, blur, visibilitychange, pagehide, pointercancel)
// ───────────────────────────────────────────────────────────────────────────

let safetyInstalled = false;

export function installDnDSafetyNet() {
  if (safetyInstalled) return;
  safetyInstalled = true;

  const onKeyDown = (e) => {
    if (e.key !== 'Escape') return;
    const store = useDnDStore();
    if (!store.isIdle) store.cancelDrag();
  };
  const onBlur = () => {
    const store = useDnDStore();
    if (!store.isIdle) store.cancelDrag();
  };
  const onVisibility = () => {
    if (!document.hidden) return;
    const store = useDnDStore();
    if (!store.isIdle) store.cancelDrag();
  };
  const onPageHide = () => {
    const store = useDnDStore();
    if (!store.isIdle) store.cancelDrag();
  };
  const onPointerCancel = () => {
    const store = useDnDStore();
    if (!store.isIdle) store.cancelDrag();
  };

  window.addEventListener('keydown', onKeyDown, true);
  window.addEventListener('blur', onBlur);
  window.addEventListener('visibilitychange', onVisibility);
  window.addEventListener('pagehide', onPageHide);
  window.addEventListener('pointercancel', onPointerCancel, true);
}

// ───────────────────────────────────────────────────────────────────────────
// Re-exports utili (centralizzano import per i consumer)
// ───────────────────────────────────────────────────────────────────────────

export { attachClosestEdge, extractClosestEdge };

// ───────────────────────────────────────────────────────────────────────────
// Helpers di convenzione per il payload Olobuild
// ───────────────────────────────────────────────────────────────────────────

/**
 * Crea un payload standard per drag di una tile dalla sidebar.
 */
export function makeSidebarPayload(tileType) {
  return { _olo: true, source: 'sidebar', kind: 'tile-type', tileType };
}

/**
 * Crea un payload standard per drag di un global widget dalla sidebar.
 */
export function makeGlobalWidgetPayload(globalId) {
  return { _olo: true, source: 'sidebar', kind: 'global-widget', globalId };
}

/**
 * Crea un payload standard per drag di un nodo esistente nel canvas/albero.
 */
export function makeNodePayload(nodeId, nodeKind, fromParentId = null, fromIndex = null) {
  return {
    _olo: true,
    source: 'canvas',
    kind: 'node',
    nodeKind, // 'section' | 'row' | 'column' | 'element'
    nodeId,
    fromParentId,
    fromIndex,
  };
}

/**
 * Verifica rapida se un oggetto di dati Pragmatic appartiene al sistema Olobuild.
 */
export function isOloData(data) {
  return !!(data && data._olo === true);
}
