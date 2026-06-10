/**
 * useDnD — Drag and drop custom basato su pointer events.
 *
 * v3.55.25 — riscritto da zero per sostituire @atlaskit/pragmatic-drag-and-drop
 * (HTML5 drag API). Motivo: l'HTML5 drag mostra il cursore "manina pixelata"
 * di Windows che non è cambiabile via CSS. Con pointer events controlliamo
 * tutto noi: cursor, ghost, hit-test, auto-scroll, touch.
 *
 * API mantenuta identica al precedente per non rompere i call site:
 *   - vOloDraggable  direttiva v-olo-draggable="{ getInitialData, onDragStart, onDrop, dragHandle, ... }"
 *   - vOloDropTarget direttiva v-olo-drop-target="{ canDrop, getData, onDragEnter, onDrag, onDragLeave, onDrop, getIsSticky }"
 *     (getIsSticky: il target resta attivo nei gap senza target entro STICKY_RADIUS px)
 *   - useDragMonitor composable per monitor globale
 *   - useAutoScroll  composable per auto-scroll su container
 *   - installDnDSafetyNet() — registra Esc/blur/visibilitychange/pagehide/pointercancel
 *   - setCustomNativeDragPreview({getOffset, render}) — popola il ghost div
 *   - attachClosestEdge / extractClosestEdge
 *   - makeSidebarPayload / makeGlobalWidgetPayload / makeNodePayload / isOloData
 *
 * Differenze interne:
 *   - location ha shape { current, previous, initial }, ognuno con { input: {clientX, clientY}, dropTargets: [] }
 *   - source ha shape { element, data }
 *   - self (dei drop targets) ha shape { element, data }
 */
import { onMounted, onBeforeUnmount } from 'vue';
import { useDnDStore } from '@/stores/dnd';

// ───────────────────────────────────────────────────────────────────────────
// Module state
// ───────────────────────────────────────────────────────────────────────────

const DRAG_THRESHOLD = 5; // px
const CLOSEST_EDGE_KEY = '__olo_closest_edge__';

// Registry drop target: element → opts. WeakMap: l'hit-test non itera la mappa
// (usa .get(el) sugli elementi da elementsFromPoint), quindi il GC può liberare
// gli elementi smontati anche se l'unmounted della direttiva non firasse.
const dropTargetRegistry = new WeakMap();  // element → opts
const monitors = new Set();                // { canMonitor, onDragStart, onDrag, onDrop }

// Raggio (px) entro cui un drop target sticky resta attivo quando il pointer
// è su una zona senza target (gap tra sezioni/righe). Oltre, decade — così
// trascinare lontano (es. sopra la sidebar) e rilasciare resta un annullo.
const STICKY_RADIUS = 48;

// Stato del drag corrente
const dragState = {
  phase: 'idle',                  // 'idle' | 'pending' | 'dragging'
  source: null,                   // { element, data, opts }
  pointerId: null,
  startInput: null,               // { clientX, clientY }
  initial: null,                  // { input, dropTargets }
  previous: null,
  current: null,
  ghost: null,                    // { element, offset:{x,y}, cleanup }
};

// Contesto attivo durante onGenerateDragPreview — usato da setCustomNativeDragPreview
let _ghostSetupContext = null;

// ───────────────────────────────────────────────────────────────────────────
// Listener globali (installati una sola volta)
// ───────────────────────────────────────────────────────────────────────────

let globalListenersInstalled = false;

function installGlobalListeners() {
  if (globalListenersInstalled) return;
  globalListenersInstalled = true;
  // Capture phase per intercettare prima di altri handler.
  window.addEventListener('pointermove', onGlobalPointerMove, true);
  window.addEventListener('pointerup',   onGlobalPointerUp,   true);
  window.addEventListener('pointercancel', onGlobalPointerCancel, true);
}

// v3.55.29 — blocca HTML5 dragstart al TOP-LEVEL (non dentro install...).
// Si registra appena questo modulo viene importato, prima che Vue mount
// qualunque direttiva → garantisce che il primo dragstart sia già intercettato.
//
// Senza questo: trascinando un elemento che contiene <img>/<a> (draggable
// di default in HTML), il browser parte col drag HTML5 nativo in parallelo
// al nostro pointer-events drag. Senza drop target HTML5 validi (rimossi
// con il refactor v3.55.25), Windows mostra cursor "no-drop" (cerchio rosso
// barrato). Bloccando dragstart il browser non fa nemmeno partire HTML5 drag.
//
// NOTA: non blocca drag esterni dall'OS (file upload drag-drop) — quelli
// non firano dragstart locale ma arrivano con dragover/drop diretti.
if (typeof document !== 'undefined') {
  document.addEventListener('dragstart', (e) => e.preventDefault(), true);
  // Doppio cinturino: anche dragover/drop a livello document, così se qualche
  // listener attivo prima del nostro fa partire il drag, il cursor torna normale.
  document.addEventListener('drag', (e) => e.preventDefault(), true);
}

// RAF-throttle dell'hit-test: i pointermove possono firare ben oltre i 60Hz
// (mouse gaming 500-1000Hz). Il ghost segue OGNI evento (solo un transform,
// economico); hit-test + callback girano al massimo una volta per frame.
let _moveRafId = null;
let _pendingMoveEvent = null;

function flushPendingMove() {
  if (_moveRafId) { cancelAnimationFrame(_moveRafId); _moveRafId = null; }
  if (_pendingMoveEvent && dragState.phase === 'dragging') {
    const e = _pendingMoveEvent;
    _pendingMoveEvent = null;
    updateActiveDrag(e);
  } else {
    _pendingMoveEvent = null;
  }
}

function onGlobalPointerMove(e) {
  if (dragState.phase === 'idle') return;
  // Ignora pointer diversi da quello che ha iniziato il drag (es. tocco
  // accidentale sul touchscreen durante un drag col mouse).
  if (dragState.pointerId !== null && e.pointerId !== dragState.pointerId) return;

  if (dragState.phase === 'pending') {
    const dx = e.clientX - dragState.startInput.clientX;
    const dy = e.clientY - dragState.startInput.clientY;
    if ((dx * dx + dy * dy) < (DRAG_THRESHOLD * DRAG_THRESHOLD)) return;
    startActiveDrag(e);
    return; // startActiveDrag fa già un updateActiveDrag sincrono
  }

  if (dragState.phase === 'dragging') {
    moveGhost(e);
    _pendingMoveEvent = e;
    if (!_moveRafId) {
      _moveRafId = requestAnimationFrame(() => {
        _moveRafId = null;
        if (_pendingMoveEvent && dragState.phase === 'dragging') {
          const ev = _pendingMoveEvent;
          _pendingMoveEvent = null;
          updateActiveDrag(ev);
        }
      });
    }
  }
}

function onGlobalPointerUp(e) {
  if (dragState.phase === 'idle') return;
  if (dragState.pointerId !== null && e.pointerId !== dragState.pointerId) return;

  if (dragState.phase === 'pending') {
    // Mai partito drag effettivo: cancel silenzioso.
    resetState();
    return;
  }

  // Applica l'eventuale ultimo move non ancora processato dal RAF, così il
  // drop usa i drop target dell'ultima posizione reale del puntatore.
  flushPendingMove();

  // v3.55.32 — segna SUBITO la fase come 'finalizing' per evitare doppio drop.
  if (dragState.phase !== 'dragging') return;
  dragState.phase = 'finalizing';

  // Il browser genera un click sintetico dopo il pointerup anche a fine drag
  // (pointer captured → pointerdown e pointerup risultano sullo stesso target).
  // Senza soppressione, droppare una tile dalla sidebar firava anche il
  // @click="addTile" del bottone sorgente → inserimento doppio (prima mascherato
  // dal dedup 500ms in useDragDrop, che però scartava anche click legittimi).
  suppressPostDragClick(dragState.source?.element);

  const source = dragState.source;
  const dropTargets = dragState.current?.dropTargets || [];
  const location = makeLocationSnapshot();

  // v3.55.33 — chiama onDrop SOLO sul drop target più specifico (drops[0] = deepest).
  // Causa del fix: in Olobuild i drop target nested (OlobuilderGrid: section + row +
  // column + element) hanno onDrop che fa SOLO cleanup CSS, e l'inserimento è
  // delegato al monitor (applyPragmaticDrop) che usa drops[0]. Se chiamavamo onDrop
  // su TUTTI i target nested, in iframe mode il CanvasDragOverlay (drops[0]=overlay)
  // inseriva via handleDropFromSidebar mentre eventuali target sotto chiamavano
  // anche loro INSERT-doing onDrop → tile duplicate.
  // I drop target intermedi ricevono onDragLeave per resettare le loro classi CSS.
  if (dropTargets.length > 0) {
    const top = dropTargets[0];
    safeCall(top.opts.onDrop, { source, location, self: { element: top.element, data: top.data } });
    for (let i = 1; i < dropTargets.length; i++) {
      const tgt = dropTargets[i];
      safeCall(tgt.opts.onDragLeave, { source, location, self: { element: tgt.element, data: tgt.data } });
    }
  }
  safeCall(source.opts.onDrop, { source, location });
  for (const m of monitors) {
    if (canMonitorAccept(m, source)) safeCall(m.onDrop, { source, location });
  }

  cleanupGhost();
  resetState();
}

function onGlobalPointerCancel(e) {
  if (dragState.phase === 'idle') return;
  if (e && dragState.pointerId !== null && e.pointerId !== dragState.pointerId) return;
  cancelActiveDrag();
}

/**
 * Sopprime il PRIMO click che arriva subito dopo un drag, se il suo target è
 * dentro l'elemento sorgente del drag. One-shot con finestra breve: i click
 * successivi (intenzionali) passano normalmente.
 */
function suppressPostDragClick(sourceEl) {
  if (!sourceEl) return;
  let tid = null;
  const onClick = (ev) => {
    if (sourceEl === ev.target || (sourceEl.contains && sourceEl.contains(ev.target))) {
      ev.stopPropagation();
      ev.preventDefault();
    }
    cleanup();
  };
  const cleanup = () => {
    window.removeEventListener('click', onClick, true);
    if (tid) { clearTimeout(tid); tid = null; }
  };
  window.addEventListener('click', onClick, true);
  tid = setTimeout(cleanup, 300);
}

function cancelActiveDrag() {
  if (dragState.phase === 'dragging') {
    // Anche un drag cancellato (Esc/pointercancel) può essere seguito da un
    // pointerup → click sintetico sul source: va soppresso pure qui.
    suppressPostDragClick(dragState.source?.element);
    const source = dragState.source;
    const location = makeLocationSnapshot({ dropTargets: [] });
    // onDragLeave su tutti i target attualmente attivi
    for (const tgt of (dragState.current?.dropTargets || [])) {
      safeCall(tgt.opts.onDragLeave, { source, location, self: { element: tgt.element, data: tgt.data } });
    }
    // onDrop con dropTargets vuoti = drag cancellato
    safeCall(source.opts.onDrop, { source, location });
    for (const m of monitors) {
      if (canMonitorAccept(m, source)) safeCall(m.onDrop, { source, location });
    }
  }
  cleanupGhost();
  resetState();
}

function startActiveDrag(e) {
  const source = dragState.source;
  const opts = source.opts;

  // v1.0.61 — setPointerCapture applicato QUI (quando il drag è effettivamente partito),
  // non in pointerdown. Permette ai click semplici sui descendant del row di funzionare.
  try { dragState.captureTarget?.setPointerCapture?.(dragState.pointerId); } catch (err) {}

  // Get initial data
  source.data = opts.getInitialData?.({ element: source.element }) ?? {};

  // Setup ghost: chiamiamo onGenerateDragPreview che a sua volta chiamerà
  // setCustomNativeDragPreview (la nostra) per popolare il ghost div.
  setupGhost(opts, source, e);

  dragState.initial = makeLocationSnapshot({ clientX: e.clientX, clientY: e.clientY, dropTargets: [] }).current;
  dragState.previous = dragState.initial;
  dragState.current  = dragState.initial;
  dragState.phase = 'dragging';

  // Cursor globale + class hook per CSS
  document.body.classList.add('olo-dragging');
  // Disabilita la selezione testo durante il drag
  document.body.style.userSelect = 'none';
  document.body.style.webkitUserSelect = 'none';
  // Pulisce eventuali selezioni testo residue (parent + builder iframe).
  // Il pointerdown sul drag handle può aver iniziato una selezione prima che
  // si superasse la soglia di 5px e partisse il drag effettivo.
  try { window.getSelection?.()?.removeAllRanges?.(); } catch (err) {}
  try {
    const ifr = document.querySelector('.olo-live-iframe');
    ifr?.contentWindow?.getSelection?.()?.removeAllRanges?.();
  } catch (err) {}

  const location = makeLocationSnapshot();
  safeCall(opts.onDragStart, { source, location });
  for (const m of monitors) {
    if (canMonitorAccept(m, source)) safeCall(m.onDragStart, { source, location });
  }

  // Bridge per i componenti che ascoltavano dragstart/dragend HTML5 nativo
  // (es. CanvasDragOverlay che attiva pointer-events sul drop overlay).
  // v3.55.26 — sostituisce i listener dragstart/dragend ormai morti dopo che
  // pragmatic-drag-and-drop è stato rimpiazzato dal sistema custom.
  try {
    window.dispatchEvent(new CustomEvent('olo:drag-start', { detail: { source } }));
  } catch (err) {}

  // Update immediato per posizionare ghost + hit-test al primo frame
  updateActiveDrag(e);
}

function moveGhost(e) {
  if (!dragState.ghost) return;
  const off = dragState.ghost.offset;
  dragState.ghost.element.style.transform = `translate(${e.clientX + off.x}px, ${e.clientY + off.y}px)`;
}

function updateActiveDrag(e) {
  const source = dragState.source;

  moveGhost(e);

  // Hit-test drop targets sotto il puntatore
  let newTargets = hitTestDropTargets(e, source);

  // Stickiness: se il puntatore è su una zona senza target (gap tra sezioni,
  // padding), mantieni i target precedenti che dichiarano getIsSticky() e il
  // cui elemento è ancora vicino (entro STICKY_RADIUS) e nel DOM. Evita il
  // flicker dell'indicatore di drop nei gap. Il data viene ricalcolato con
  // l'input corrente (getData può dipendere dalla posizione, es. hit-test
  // overlay o closest-edge).
  if (newTargets.length === 0) {
    const prev = dragState.current?.dropTargets || [];
    const sticky = [];
    for (const t of prev) {
      if (!t.element || !t.element.isConnected) continue;
      if (typeof t.opts.getIsSticky !== 'function' || !t.opts.getIsSticky({ source })) continue;
      const r = t.element.getBoundingClientRect();
      if (
        e.clientX < r.left - STICKY_RADIUS || e.clientX > r.right + STICKY_RADIUS ||
        e.clientY < r.top - STICKY_RADIUS || e.clientY > r.bottom + STICKY_RADIUS
      ) continue;
      const arg = { source, element: t.element, input: { clientX: e.clientX, clientY: e.clientY } };
      if (t.opts.canDrop && !t.opts.canDrop(arg)) continue;
      const data = t.opts.getData ? (t.opts.getData(arg) ?? {}) : {};
      sticky.push({ element: t.element, opts: t.opts, data });
    }
    newTargets = sticky;
  }

  // Calcola enter/leave rispetto allo stato precedente
  const prevTargets = dragState.current?.dropTargets || [];
  const enterEls = newTargets.filter(t => !prevTargets.find(p => p.element === t.element));
  const leaveEls = prevTargets.filter(p => !newTargets.find(t => t.element === p.element));

  // Update current (necessario PRIMA delle callback per coerenza)
  dragState.previous = dragState.current;
  dragState.current = makeLocationSnapshot({ clientX: e.clientX, clientY: e.clientY, dropTargets: newTargets }).current;

  const location = makeLocationSnapshot();

  // Fire onDragLeave su quelli usciti
  for (const tgt of leaveEls) {
    safeCall(tgt.opts.onDragLeave, { source, location, self: { element: tgt.element, data: tgt.data } });
  }
  // Fire onDragEnter sui nuovi
  for (const tgt of enterEls) {
    safeCall(tgt.opts.onDragEnter, { source, location, self: { element: tgt.element, data: tgt.data } });
  }
  // Fire onDrag su tutti i target attivi (anche quelli rimasti)
  for (const tgt of newTargets) {
    safeCall(tgt.opts.onDrag, { source, location, self: { element: tgt.element, data: tgt.data } });
  }

  // Draggable onDrag
  safeCall(source.opts.onDrag, { source, location });

  // Monitor onDrag
  for (const m of monitors) {
    if (canMonitorAccept(m, source)) safeCall(m.onDrag, { source, location });
  }
}

function hitTestDropTargets(e, source) {
  // Trova tutti gli elementi sotto il puntatore (deepest first → ancestor last).
  // elementsFromPoint(): array ordinato dal più in alto nello z-stack al più in basso.
  const elements = document.elementsFromPoint(e.clientX, e.clientY);
  const results = [];
  for (const el of elements) {
    const opts = dropTargetRegistry.get(el);
    if (!opts) continue;
    // canDrop check
    const canDropFn = opts.canDrop;
    const arg = { source, element: el, input: { clientX: e.clientX, clientY: e.clientY } };
    if (canDropFn && !canDropFn(arg)) continue;
    // getData
    const data = opts.getData ? (opts.getData(arg) ?? {}) : {};
    results.push({ element: el, opts, data });
  }
  return results;
}

function makeLocationSnapshot(override) {
  // Costruisce snapshot {current, previous, initial} con shape consistente.
  // Usato sia per consumare lo stato corrente sia per generarne uno nuovo.
  if (override) {
    const { clientX, clientY, dropTargets = [] } = override;
    return {
      current:  { input: { clientX: clientX ?? 0, clientY: clientY ?? 0 }, dropTargets },
      previous: dragState.current || null,
      initial:  dragState.initial || null,
    };
  }
  return {
    current:  dragState.current,
    previous: dragState.previous,
    initial:  dragState.initial,
  };
}

function canMonitorAccept(monitor, source) {
  if (!monitor.canMonitor) return true;
  return !!monitor.canMonitor({ source });
}

function safeCall(fn, ...args) {
  if (typeof fn !== 'function') return;
  try { return fn(...args); } catch (err) { console.error('[useDnD]', err); }
}

function resetState() {
  dragState.phase = 'idle';
  dragState.source = null;
  dragState.pointerId = null;
  dragState.startInput = null;
  dragState.initial = null;
  dragState.previous = null;
  dragState.current = null;
  dragState.captureTarget = null;
  if (_moveRafId) { cancelAnimationFrame(_moveRafId); _moveRafId = null; }
  _pendingMoveEvent = null;
  document.body.classList.remove('olo-dragging');
  document.body.style.userSelect = '';
  document.body.style.webkitUserSelect = '';
}

// ───────────────────────────────────────────────────────────────────────────
// Ghost (drag preview) — popolato dai consumer via setCustomNativeDragPreview()
// ───────────────────────────────────────────────────────────────────────────

function setupGhost(opts, source, e) {
  const container = document.createElement('div');
  container.className = 'olo-drag-ghost';
  container.style.cssText =
    'position:fixed;top:0;left:0;pointer-events:none;z-index:2147483647;' +
    'will-change:transform;contain:layout style;';
  document.body.appendChild(container);

  // Default offset (può essere sovrascritto dalla render config del consumer)
  _ghostSetupContext = { container, offset: { x: 16, y: 16 }, renderCleanup: null };

  // nativeSetDragImage è alias deprecato per back-compat: non fa nulla nel sistema custom.
  const noop = () => {};

  if (typeof opts.onGenerateDragPreview === 'function') {
    safeCall(opts.onGenerateDragPreview, {
      source,
      location: makeLocationSnapshot({ clientX: e.clientX, clientY: e.clientY }),
      nativeSetDragImage: noop,
    });
  }

  dragState.ghost = {
    element: container,
    offset: _ghostSetupContext.offset,
    cleanup: () => {
      if (typeof _ghostSetupContext?.renderCleanup === 'function') {
        try { _ghostSetupContext.renderCleanup(); } catch (e) {}
      }
      try { container.remove(); } catch (e) {}
    },
  };

  _ghostSetupContext = null;
}

function cleanupGhost() {
  if (dragState.ghost?.cleanup) dragState.ghost.cleanup();
  dragState.ghost = null;
  // Notifica fine drag (bridge per CanvasDragOverlay & friends).
  try {
    window.dispatchEvent(new CustomEvent('olo:drag-end'));
  } catch (err) {}
}

/**
 * Popola il ghost div del drag corrente. Da chiamare DENTRO onGenerateDragPreview.
 * API compatibile con @atlaskit/pragmatic-drag-and-drop/element/set-custom-native-drag-preview.
 *
 * @param {Object} cfg
 * @param {() => {x:number,y:number}} cfg.getOffset  Offset rispetto al puntatore.
 * @param {(args:{container:HTMLElement}) => void|Function} cfg.render
 *   Riceve il container DIV del ghost e può aggiungere figli. Se ritorna una funzione,
 *   viene invocata come cleanup quando il drag termina.
 */
export function setCustomNativeDragPreview(cfg) {
  if (!_ghostSetupContext) {
    console.warn('[useDnD] setCustomNativeDragPreview chiamato fuori da onGenerateDragPreview');
    return;
  }
  if (typeof cfg.getOffset === 'function') {
    const off = cfg.getOffset();
    if (off && typeof off.x === 'number' && typeof off.y === 'number') {
      _ghostSetupContext.offset = { x: -off.x, y: -off.y };
      // Nota: pragmatic usa offset positivo come "distanza dall'angolo TL del ghost al punto
      // sotto il puntatore". Noi traduciamo in "spostamento dell'angolo TL del ghost rispetto
      // al puntatore" → invertiamo segno.
    }
  }
  if (typeof cfg.render === 'function') {
    const r = cfg.render({ container: _ghostSetupContext.container });
    if (typeof r === 'function') _ghostSetupContext.renderCleanup = r;
  }
}

// ───────────────────────────────────────────────────────────────────────────
// Direttive Vue
// ───────────────────────────────────────────────────────────────────────────

export const vOloDraggable = {
  mounted(el, binding) {
    applyDraggable(el, binding.value || {});
  },
  updated(el, binding) {
    const newOpts = binding.value || {};
    el.__oloDraggableOpts = newOpts;
    // Se il dragHandle è cambiato (selettore stringa o ref), ricrea il listener.
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

export const vOloDropTarget = {
  mounted(el, binding) {
    applyDropTarget(el, binding.value || {});
  },
  updated(el, binding) {
    el.__oloDropTargetOpts = binding.value || {};
    dropTargetRegistry.set(el, el.__oloDropTargetOpts);
  },
  unmounted(el) {
    teardownDropTarget(el);
  },
};

function applyDraggable(el, opts) {
  installGlobalListeners();
  el.__oloDraggableOpts = opts;

  // Resolve dragHandle (string selector → element, oppure passa l'element già)
  const handle = (() => {
    if (!opts.dragHandle) return null;
    if (typeof opts.dragHandle === 'string') return el.querySelector(opts.dragHandle) || null;
    return opts.dragHandle;
  })();
  el.__oloDragHandle = handle;

  // Listener pointerdown sul handle (se c'è) o sull'el principale
  const target = handle || el;
  const onPointerDown = (e) => {
    if (e.button !== 0 && e.pointerType === 'mouse') return; // solo left click per mouse
    if (dragState.phase !== 'idle') return;

    // Non avviare drag se il click è su un controllo interattivo (input, button, link)
    // a meno che il dragHandle sia esplicito (utente sa cosa vuole).
    if (!handle) {
      const interactive = e.target.closest('input,textarea,select,button,a,[contenteditable="true"]');
      if (interactive && el.contains(interactive)) return;
    }

    // canDrag check
    const currentOpts = el.__oloDraggableOpts || opts;
    if (currentOpts.canDrag && !currentOpts.canDrag({ element: el })) return;

    dragState.phase = 'pending';
    dragState.source = { element: el, data: null, opts: currentOpts };
    dragState.pointerId = e.pointerId;
    dragState.startInput = { clientX: e.clientX, clientY: e.clientY };
    dragState.captureTarget = target; // memorizzato per applicare setPointerCapture solo se drag parte davvero

    // v1.0.61 — setPointerCapture SPOSTATO in startActiveDrag(). Era qui ma faceva
    // morire tutti i click sui descendant del drag handle: pointerdown applica capture,
    // pointerup sotto pending NON genera click (pointer è captured dal row). Risultato:
    // click su .st-name (rename), click su button toggle/X/Duplica/Salva → nessuno
    // arrivava al rispettivo handler perché il click event non si dispatchava mai.
    // Ora capture solo quando phase passa a 'dragging' (move > threshold).
  };

  target.addEventListener('pointerdown', onPointerDown);
  // Nota: HTML5 dragstart è bloccato globalmente in installGlobalListeners() —
  // qui non serve un listener locale perché copre già tutti gli elementi.

  el.__oloDraggableCleanup = () => {
    target.removeEventListener('pointerdown', onPointerDown);
  };
}

function applyDropTarget(el, opts) {
  installGlobalListeners();
  el.__oloDropTargetOpts = opts;
  dropTargetRegistry.set(el, opts);

  el.__oloDropTargetCleanup = () => {
    dropTargetRegistry.delete(el);
  };
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

export function useDragMonitor(opts) {
  const monitor = {
    canMonitor:  opts?.canMonitor,
    onDragStart: opts?.onDragStart,
    onDrag:      opts?.onDrag,
    onDrop:      opts?.onDrop,
  };
  onMounted(() => {
    installGlobalListeners();
    monitors.add(monitor);
  });
  onBeforeUnmount(() => {
    monitors.delete(monitor);
  });
}

/**
 * Auto-scroll: durante un drag attivo, se il puntatore è vicino ai bordi del
 * container, scrolla automaticamente.
 *
 * opts:
 *  - canScroll({ source }) → bool (opzionale, default true)
 *  - getAllowedAxis({ source }) → 'vertical' | 'horizontal' | 'both' (default 'both')
 *  - getConfiguration({ source }) → { maxScrollSpeed: 'fast'|'standard' } (default 'standard')
 */
export function useAutoScroll(getElement, opts = {}) {
  let cleanup = null;
  let rafId = null;
  // Velocità corrente letta dal tick RAF: aggiornata a ogni pointermove, così
  // avvicinarsi al bordo accelera lo scroll del tick già attivo (prima il tick
  // chiudeva sulla velocità del primo move e non si aggiornava più).
  let curDx = 0, curDy = 0;

  // Risoluzione lazy: l'elemento può non esistere al mount (es. canvas classico
  // montato solo fuori da live preview) o cambiare nel tempo.
  const resolveEl = () => {
    const el = typeof getElement === 'function' ? getElement() : getElement?.value;
    return el || null;
  };

  const stopTick = () => {
    if (rafId) { cancelAnimationFrame(rafId); rafId = null; }
  };

  onMounted(() => {
    const ZONE = 60; // px from edge

    const onMove = (e) => {
      if (dragState.phase !== 'dragging') { stopTick(); return; }
      const el = resolveEl();
      if (!el) { stopTick(); return; }
      if (opts.canScroll && !opts.canScroll({ source: dragState.source })) { stopTick(); return; }

      const axis = opts.getAllowedAxis ? opts.getAllowedAxis({ source: dragState.source }) : 'both';
      const cfg  = opts.getConfiguration ? opts.getConfiguration({ source: dragState.source }) : {};
      const speed = cfg.maxScrollSpeed === 'fast' ? 24 : 12;

      const rect = el.getBoundingClientRect();
      let dx = 0, dy = 0;
      if (axis === 'vertical' || axis === 'both') {
        if (e.clientY < rect.top + ZONE)    dy = -speed;
        if (e.clientY > rect.bottom - ZONE) dy = speed;
      }
      if (axis === 'horizontal' || axis === 'both') {
        if (e.clientX < rect.left + ZONE)   dx = -speed;
        if (e.clientX > rect.right - ZONE)  dx = speed;
      }

      if (dx === 0 && dy === 0) { stopTick(); return; }

      curDx = dx; curDy = dy;
      if (rafId) return; // tick già attivo: ha già la nuova velocità via curDx/curDy
      const tick = () => {
        if (dragState.phase !== 'dragging') { rafId = null; return; }
        const elNow = resolveEl();
        if (!elNow) { rafId = null; return; }
        try { elNow.scrollBy({ top: curDy, left: curDx, behavior: 'auto' }); } catch (err) {}
        rafId = requestAnimationFrame(tick);
      };
      rafId = requestAnimationFrame(tick);
    };

    const onUpOrCancel = () => stopTick();

    window.addEventListener('pointermove', onMove, true);
    window.addEventListener('pointerup',   onUpOrCancel, true);
    window.addEventListener('pointercancel', onUpOrCancel, true);

    cleanup = () => {
      window.removeEventListener('pointermove', onMove, true);
      window.removeEventListener('pointerup',   onUpOrCancel, true);
      window.removeEventListener('pointercancel', onUpOrCancel, true);
      stopTick();
    };
  });

  onBeforeUnmount(() => {
    if (cleanup) cleanup();
    cleanup = null;
  });
}

// ───────────────────────────────────────────────────────────────────────────
// Safety net (Esc, blur, visibilitychange, pagehide, pointercancel)
// ───────────────────────────────────────────────────────────────────────────

let safetyInstalled = false;

export function installDnDSafetyNet() {
  if (safetyInstalled) return;
  safetyInstalled = true;
  installGlobalListeners();

  const cancel = () => {
    if (dragState.phase === 'idle') return;
    cancelActiveDrag();
    try {
      const store = useDnDStore();
      if (store && !store.isIdle) store.cancelDrag();
    } catch (err) {}
  };

  window.addEventListener('keydown', (e) => { if (e.key === 'Escape') cancel(); }, true);
  window.addEventListener('blur', cancel);
  document.addEventListener('visibilitychange', () => { if (document.hidden) cancel(); });
  window.addEventListener('pagehide', cancel);
}

// ───────────────────────────────────────────────────────────────────────────
// Closest edge helpers (per drop ordinati: above/below di un sibling)
// ───────────────────────────────────────────────────────────────────────────

/**
 * Aggiunge a `data` un metadato `__olo_closest_edge__` con il bordo più vicino al puntatore.
 * Usato dai drop target per decidere "before" vs "after" rispetto a un sibling.
 *
 * @param {Object}   data
 * @param {Object}   args
 * @param {Element}  args.element
 * @param {{clientX:number,clientY:number}} args.input
 * @param {Array<'top'|'bottom'|'left'|'right'>} args.allowedEdges
 * @returns {Object} nuovo data con il metadato attached
 */
export function attachClosestEdge(data, { element, input, allowedEdges = ['top', 'bottom'] }) {
  const rect = element.getBoundingClientRect();
  const dist = {};
  if (allowedEdges.includes('top'))    dist.top    = Math.abs(input.clientY - rect.top);
  if (allowedEdges.includes('bottom')) dist.bottom = Math.abs(input.clientY - rect.bottom);
  if (allowedEdges.includes('left'))   dist.left   = Math.abs(input.clientX - rect.left);
  if (allowedEdges.includes('right'))  dist.right  = Math.abs(input.clientX - rect.right);

  let closest = null, min = Infinity;
  for (const [edge, d] of Object.entries(dist)) {
    if (d < min) { min = d; closest = edge; }
  }
  return { ...data, [CLOSEST_EDGE_KEY]: closest };
}

export function extractClosestEdge(data) {
  if (!data || typeof data !== 'object') return null;
  return data[CLOSEST_EDGE_KEY] || null;
}

// ───────────────────────────────────────────────────────────────────────────
// Helpers di convenzione per il payload Olobuild
// ───────────────────────────────────────────────────────────────────────────

export function makeSidebarPayload(tileType) {
  return { _olo: true, source: 'sidebar', kind: 'tile-type', tileType };
}

export function makeGlobalWidgetPayload(globalId) {
  return { _olo: true, source: 'sidebar', kind: 'global-widget', globalId };
}

export function makeNodePayload(nodeId, nodeKind, fromParentId = null, fromIndex = null) {
  return {
    _olo: true,
    source: 'canvas',
    kind: 'node',
    nodeKind,
    nodeId,
    fromParentId,
    fromIndex,
  };
}

export function isOloData(data) {
  return !!(data && data._olo === true);
}
