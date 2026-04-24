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
    // CRUCIALE: NON fare teardown+reapply ad ogni re-render, altrimenti il drag
    // attivo viene interrotto quando Vue re-renderizza. Aggiorniamo solo l'oggetto
    // opts in place; le callback registrate in Pragmatic leggono __oloOpts via closure.
    el.__oloOpts = binding.value || {};
  },
  unmounted(el) {
    teardown(el);
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
    // Stabile come vOloDraggable: aggiorna solo __oloOpts, non re-registra Pragmatic.
    el.__oloOpts = binding.value || {};
  },
  unmounted(el) {
    teardown(el);
  },
};

function applyDraggable(el, opts) {
  // Salviamo le opts in un slot mutevole; le callback leggono sempre el.__oloOpts corrente.
  el.__oloOpts = opts;

  const resolveDragHandle = () => {
    const o = el.__oloOpts || {};
    if (!o.dragHandle) return undefined;
    if (typeof o.dragHandle === 'string') {
      return el.querySelector(o.dragHandle) || undefined;
    }
    return o.dragHandle;
  };

  const cleanup = draggable({
    element: el,
    dragHandle: resolveDragHandle(),
    getInitialData: (arg) => el.__oloOpts?.getInitialData?.(arg),
    canDrag: (arg) => (el.__oloOpts?.canDrag ? el.__oloOpts.canDrag(arg) : true),
    onGenerateDragPreview: (arg) => el.__oloOpts?.onGenerateDragPreview?.(arg),
    onDragStart: (arg) => el.__oloOpts?.onDragStart?.(arg),
    onDrag: (arg) => el.__oloOpts?.onDrag?.(arg),
    onDrop: (arg) => el.__oloOpts?.onDrop?.(arg),
  });
  el.__oloCleanup = cleanup;
}

function applyDropTarget(el, opts) {
  el.__oloOpts = opts;

  const cleanup = dropTargetForElements({
    element: el,
    canDrop: (arg) => (el.__oloOpts?.canDrop ? el.__oloOpts.canDrop(arg) : true),
    getData: (arg) => (el.__oloOpts?.getData ? el.__oloOpts.getData(arg) : {}),
    getIsSticky: (arg) => (el.__oloOpts?.getIsSticky ? el.__oloOpts.getIsSticky(arg) : false),
    onDragEnter: (arg) => el.__oloOpts?.onDragEnter?.(arg),
    onDrag: (arg) => el.__oloOpts?.onDrag?.(arg),
    onDragLeave: (arg) => el.__oloOpts?.onDragLeave?.(arg),
    onDrop: (arg) => el.__oloOpts?.onDrop?.(arg),
  });
  el.__oloCleanup = cleanup;
}

function teardown(el) {
  if (typeof el.__oloCleanup === 'function') el.__oloCleanup();
  el.__oloCleanup = null;
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
