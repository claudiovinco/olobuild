/**
 * useListSort — riordino di liste UI (item dell'inspector, immagini gallery)
 * costruito sulle primitive del motore DnD custom (useDnD, pointer events).
 *
 * v1.4.387 — nasce per sostituire vuedraggable/sortablejs nei componenti lista
 * (ContentItemsEditor, FieldGallery): un solo motore DnD in tutto il builder.
 * A differenza di vuedraggable non fa sort "live" del DOM durante il drag:
 * mostra un indicatore di inserimento sull'item sotto il puntatore (come il
 * canvas e lo StructureTree) e riordina il modello SOLO al rilascio.
 *
 * Uso (nel setup del componente):
 *   const { itemDraggable, itemDrop } = useListSort({
 *     handleSelector: '.cie-grip',          // il drag parte solo dal grip
 *     onMove: (from, to) => { ...splice... }, // richiesto: applica il riordino
 *     ghostLabel: (index, el) => 'testo',   // opzionale: testo del ghost
 *   });
 * Template:
 *   <div v-for="(item, i) in items" :key="item.id"
 *        v-olo-draggable="itemDraggable(i)" v-olo-drop-target="itemDrop(i)">
 * (importare anche vOloDraggable / vOloDropTarget da useDnD per le direttive)
 *
 * Ogni istanza ha un listId univoco: più liste sulla stessa pagina non si
 * parlano (niente drag cross-lista — per quello c'è il motore nodi del canvas).
 */
import {
  useDragMonitor,
  attachClosestEdge,
  extractClosestEdge,
  setCustomNativeDragPreview,
} from './useDnD';

const KIND = 'list-sort';
let _seq = 0;

export function useListSort({ onMove, handleSelector = null, ghostLabel = null }) {
  const listId = 'ls-' + (++_seq);

  const isMine = (data) => !!data && data._olo === true && data.kind === KIND && data.listId === listId;

  const EDGE_ABOVE = 'olo-listsort-above';
  const EDGE_BELOW = 'olo-listsort-below';

  function clearEdge(el) {
    if (el) el.classList.remove(EDGE_ABOVE, EDGE_BELOW);
  }

  function showEdge(self) {
    if (!self?.element) return;
    const edge = extractClosestEdge(self.data);
    self.element.classList.toggle(EDGE_ABOVE, edge === 'top');
    self.element.classList.toggle(EDGE_BELOW, edge === 'bottom');
  }

  function itemDraggable(index) {
    return {
      dragHandle: handleSelector || undefined,
      getInitialData: () => ({ _olo: true, kind: KIND, listId, index }),
      onGenerateDragPreview: ({ nativeSetDragImage, source }) => {
        setCustomNativeDragPreview({
          getOffset: () => ({ x: 12, y: 12 }),
          render: ({ container }) => {
            const ghost = document.createElement('div');
            ghost.className = 'olo-listsort-ghost';
            let label = null;
            if (typeof ghostLabel === 'function') label = ghostLabel(index, source.element);
            if (!label) label = (source.element.textContent || '').trim().replace(/\s+/g, ' ').slice(0, 40);
            ghost.textContent = label || '⋮';
            container.appendChild(ghost);
            return () => { try { container.removeChild(ghost); } catch (e) {} };
          },
          nativeSetDragImage,
        });
      },
      onDragStart: ({ source }) => { source?.element?.classList.add('olo-listsort-dragging'); },
      onDrop: ({ source }) => { source?.element?.classList.remove('olo-listsort-dragging'); },
    };
  }

  function itemDrop(index) {
    return {
      canDrop: ({ source }) => isMine(source.data),
      getData: ({ element, input }) => attachClosestEdge(
        { _olo: true, kind: KIND + '-target', listId, index },
        { element, input, allowedEdges: ['top', 'bottom'] }
      ),
      getIsSticky: () => true,
      onDragEnter: ({ self }) => showEdge(self),
      onDrag: ({ self }) => showEdge(self),
      onDragLeave: ({ self }) => clearEdge(self?.element),
      onDrop: ({ self }) => clearEdge(self?.element),
    };
  }

  // Monitor locale all'istanza: applica il riordino al rilascio.
  useDragMonitor({
    canMonitor: ({ source }) => isMine(source.data),
    onDrop: ({ source, location }) => {
      const targets = location?.current?.dropTargets || [];
      const t = targets.find((d) => d.data && d.data.kind === KIND + '-target' && d.data.listId === listId);
      if (!t) return;
      const from = source.data.index;
      const edge = extractClosestEdge(t.data);
      let to = t.data.index + (edge === 'bottom' ? 1 : 0);
      if (from < to) to--;
      if (to === from || from == null) return;
      onMove(from, to);
    },
  });

  return { itemDraggable, itemDrop };
}
