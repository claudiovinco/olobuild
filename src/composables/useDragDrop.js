import { useTilesStore, generateId, createSection, createRow, createColumn, createInnerColumn, CONTAINER_TYPES } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';
import { useDnDStore } from '@/stores/dnd';
import { useHistory } from '@/composables/useHistory';
import { extractClosestEdge, isOloData } from '@/composables/useDnD';

export function useDragDrop() {
  const tilesStore = useTilesStore();
  const builderStore = useBuilderStore();

  /**
   * Create a tile instance from a registered tile type.
   * Deep clones defaults to avoid shared references.
   */
  function createTileFromType(tileType) {
    const registered = tilesStore.registeredTiles.find(
      (t) => t.type === tileType
    );
    if (!registered) return null;

    const defaults = JSON.parse(JSON.stringify(registered.defaults || {}));
    const tile = {
      id: generateId(),
      type: registered.type,
      settings: defaults,
      style: {},
      advanced: {},
    };

    // Container types get a children array
    if (CONTAINER_TYPES.includes(tile.type)) {
      tile.children = [];
    }

    // Auto-create sub-columns for inner-columns
    if (tile.type === 'inner-columns') {
      const innerLayoutWidths = {
        '50-50': [50, 50],
        '33-33-33': [33.33, 33.33, 33.34],
        '25-75': [25, 75],
        '75-25': [75, 25],
        '25-50-25': [25, 50, 25],
      };
      const widths = innerLayoutWidths[tile.settings.layout] || [50, 50];
      tile.children = widths.map(w => createInnerColumn(w, []));
    }

    return tile;
  }

  /**
   * Handle drop from sidebar to canvas.
   * Non-structure elements get auto-wrapped in Section > Row > Column.
   * Row tiles get wrapped in Section only.
   */
  function handleDropFromSidebar(tileType, index) {
    const newTile = createTileFromType(tileType);
    if (!newTile) return;

    let tileToAdd;
    let tileToSelect = newTile;

    if (tileType === 'section') {
      // Add a section with an empty row + column
      const col = createColumn('1-1', []);
      const row = createRow('100', [col]);
      newTile.children = [row];
      tileToAdd = newTile;
    } else if (tileType === 'row') {
      // Row: wrap in section, set up columns from layout
      const layoutMap = {
        '100': [{ width: '1-1' }],
        '50-50': [{ width: '1-2' }, { width: '1-2' }],
        '33-33-33': [{ width: '1-3' }, { width: '1-3' }, { width: '1-3' }],
        '25-50-25': [{ width: '1-4' }, { width: '1-2' }, { width: '1-4' }],
        '25-25-25-25': [{ width: '1-4' }, { width: '1-4' }, { width: '1-4' }, { width: '1-4' }],
        '66-33': [{ width: '2-3' }, { width: '1-3' }],
        '33-66': [{ width: '1-3' }, { width: '2-3' }],
      };
      const layout = newTile.settings.layout || '50-50';
      const cols = (layoutMap[layout] || layoutMap['50-50']).map(c => createColumn(c.width, []));
      newTile.children = cols;
      tileToAdd = createSection([newTile]);
      tileToSelect = newTile;
    } else {
      // Regular element: wrap in Section > Row > Column
      const column = createColumn('1-1', [newTile]);
      const row = createRow('100', [column]);
      tileToAdd = createSection([row]);
      tileToSelect = newTile;
    }

    if (typeof index === 'number') {
      tilesStore.canvasTiles.splice(index, 0, tileToAdd);
      tilesStore._bumpVersion();
    } else {
      tilesStore.addTile(tileToAdd);
    }

    builderStore.isDirty = true;
    builderStore.selectTile(tileToSelect.id);
    return tileToSelect;
  }

  /**
   * Handle drop into a specific column (within a row in the canvas).
   * The element is added directly to the column's children, no wrapping.
   */
  function handleDropIntoColumn(tileType, columnId) {
    if (!tileType || tileType === 'row' || tileType === 'section') return null;
    const newTile = createTileFromType(tileType);
    if (!newTile) return null;

    tilesStore.addChild(columnId, newTile);
    builderStore.isDirty = true;
    builderStore.selectTile(newTile.id);
    return newTile;
  }

  /**
   * Handle drop of a global widget from sidebar to canvas.
   * Creates the tile from the stored global widget data and wraps it.
   */
  function handleGlobalWidgetDrop(globalId, index) {
    const newTile = tilesStore.insertGlobalWidget(globalId);
    if (!newTile) return null;

    const column = createColumn('1-1', [newTile]);
    const row = createRow('100', [column]);
    const tileToAdd = createSection([row]);

    if (typeof index === 'number') {
      tilesStore.canvasTiles.splice(index, 0, tileToAdd);
      tilesStore._bumpVersion();
    } else {
      tilesStore.addTile(tileToAdd);
    }

    builderStore.isDirty = true;
    builderStore.selectTile(newTile.id);
    return newTile;
  }

  /**
   * Handle drop of a global widget into a specific column.
   */
  function handleGlobalWidgetDropIntoColumn(globalId, columnId) {
    const newTile = tilesStore.insertGlobalWidget(globalId);
    if (!newTile) return null;

    tilesStore.addChild(columnId, newTile);
    builderStore.isDirty = true;
    builderStore.selectTile(newTile.id);
    return newTile;
  }

  function handleReorder(evt) {
    if (evt.moved) {
      builderStore.isDirty = true;
    }
  }

  // ═════════════════════════════════════════════════════════════════
  // Pragmatic monitor drop dispatcher (condiviso Grid + StructureTree)
  // ═════════════════════════════════════════════════════════════════

  /**
   * Aggiunge una nuova row vuota dentro una sezione (invocato da drop row-list).
   */
  function addRowToSection(section) {
    const col1 = createColumn('1-2', []);
    const col2 = createColumn('1-2', []);
    const row = createRow('50-50', [col1, col2]);
    if (!Array.isArray(section.children)) section.children = [];
    section.children.push(row);
    builderStore.selectTile(row.id);
  }

  function insertElementRelativeToRow(tileType, section, rowIndex, edge) {
    const newTile = createTileFromType(tileType);
    if (!newTile) return;
    const col = createColumn('1-1', [newTile]);
    const row = createRow('100', [col]);
    if (!Array.isArray(section.children)) section.children = [];
    const at = edge === 'bottom' || edge === 'right' ? rowIndex + 1 : rowIndex;
    section.children.splice(at, 0, row);
    builderStore.selectTile(newTile.id);
  }

  function insertElementRelativeToElement(tileType, col, elIndex, edge) {
    const newTile = createTileFromType(tileType);
    if (!newTile) return;
    if (!Array.isArray(col.children)) col.children = [];
    const at = edge === 'bottom' || edge === 'right' ? elIndex + 1 : elIndex;
    col.children.splice(at, 0, newTile);
    builderStore.selectTile(newTile.id);
  }

  function insertGlobalWidgetRelativeToElement(globalId, col, elIndex, edge) {
    const newTile = tilesStore.insertGlobalWidget(globalId);
    if (!newTile) return;
    if (!Array.isArray(col.children)) col.children = [];
    const at = edge === 'bottom' || edge === 'right' ? elIndex + 1 : elIndex;
    col.children.splice(at, 0, newTile);
    builderStore.selectTile(newTile.id);
  }

  function applyDropOnNodeEdge(payload, target, edge) {
    if (payload.kind === 'node') {
      let newIndex = target.index;
      if (edge === 'bottom' || edge === 'right') newIndex = target.index + 1;
      if (payload.fromParentId === target.parentId && payload.fromIndex < newIndex) {
        newIndex--;
      }
      tilesStore.moveNodeTo(payload.nodeId, target.parentId, newIndex);
      return;
    }
    if (payload.kind === 'tile-type') {
      const tileType = payload.tileType;
      let insertIndex = target.index;
      if (edge === 'bottom' || edge === 'right') insertIndex = target.index + 1;
      if (target.nodeKind === 'section') {
        handleDropFromSidebar(tileType, insertIndex);
      } else if (target.nodeKind === 'row') {
        const parentSection = tilesStore.getTileById(target.parentId);
        if (parentSection) insertElementRelativeToRow(tileType, parentSection, target.index, edge);
      } else if (target.nodeKind === 'element') {
        const col = tilesStore.getTileById(target.parentId);
        if (col) insertElementRelativeToElement(tileType, col, target.index, edge);
      }
      return;
    }
    if (payload.kind === 'global-widget') {
      let insertIndex = target.index;
      if (edge === 'bottom' || edge === 'right') insertIndex = target.index + 1;
      if (target.nodeKind === 'section') handleGlobalWidgetDrop(payload.globalId, insertIndex);
      else if (target.nodeKind === 'element') {
        const col = tilesStore.getTileById(target.parentId);
        if (col) insertGlobalWidgetRelativeToElement(payload.globalId, col, target.index, edge);
      }
      return;
    }
  }

  function applyDropInColumn(payload, columnId) {
    if (payload.kind === 'tile-type') {
      handleDropIntoColumn(payload.tileType, columnId);
    } else if (payload.kind === 'global-widget') {
      handleGlobalWidgetDropIntoColumn(payload.globalId, columnId);
    } else if (payload.kind === 'node' && payload.nodeKind === 'element') {
      const col = tilesStore.getTileById(columnId);
      if (col) {
        const nextIndex = (col.children || []).length;
        tilesStore.moveNodeTo(payload.nodeId, columnId, nextIndex);
      }
    }
  }

  function applyDropAtListEnd(payload, listKind, parentId) {
    if (listKind === 'sections') {
      if (payload.kind === 'tile-type') handleDropFromSidebar(payload.tileType);
      else if (payload.kind === 'global-widget') handleGlobalWidgetDrop(payload.globalId);
      else if (payload.kind === 'node' && payload.nodeKind === 'section') {
        // parentId=null → zone root del nodo corrente
        const len = tilesStore.canvasTiles.length;
        tilesStore.moveNodeTo(payload.nodeId, null, len);
      }
    } else if (listKind === 'elements' && parentId) {
      applyDropInColumn(payload, parentId);
    } else if (listKind === 'rows' && parentId) {
      if (payload.kind === 'tile-type' && payload.tileType !== 'section') {
        const section = tilesStore.getTileById(parentId);
        if (section) {
          if (payload.tileType === 'row') {
            addRowToSection(section);
          } else {
            const newTile = createTileFromType(payload.tileType);
            if (!newTile) return;
            const col = createColumn('1-1', [newTile]);
            const row = createRow('100', [col]);
            if (!Array.isArray(section.children)) section.children = [];
            section.children.push(row);
            builderStore.selectTile(newTile.id);
          }
        }
      } else if (payload.kind === 'node' && payload.nodeKind === 'row') {
        const section = tilesStore.getTileById(parentId);
        const len = (section?.children || []).length;
        tilesStore.moveNodeTo(payload.nodeId, parentId, len);
      }
    }
  }

  /**
   * Dispatcher unificato per il monitor Pragmatic.
   * Chiamato dal useDragMonitor.onDrop: determina la mutation in base al target più specifico.
   */
  function applyPragmaticDrop({ source, location }) {
    const drops = location.current.dropTargets;
    if (!drops || drops.length === 0) return;
    const target = drops[0].data;
    const payload = source.data;
    if (!isOloData(target) || !isOloData(payload)) return;

    const history = useHistory();
    const dndStore = useDnDStore();

    history.pushStateNow();
    dndStore.markDropping();

    try {
      let placedTileId = null;
      if (target.kind === 'node-edge') {
        const edge = extractClosestEdge(target);
        applyDropOnNodeEdge(payload, target, edge);
        placedTileId = payload.nodeId || builderStore.selectedTileId;
      } else if (target.kind === 'column-body') {
        applyDropInColumn(payload, target.columnId);
        placedTileId = payload.nodeId || builderStore.selectedTileId;
      } else if (target.kind === 'list-end') {
        applyDropAtListEnd(payload, target.listKind, target.parentId);
        placedTileId = builderStore.selectedTileId;
      } else if (target.kind === 'canvas-overlay') {
        // Gestito internamente da CanvasDragOverlay (hit-test + handleDropFromSidebar)
        return;
      }

      if (placedTileId) builderStore.markDirtyForTile(placedTileId);
      else builderStore.isDirty = true;
    } finally {
      if (dndStore.phase === 'dropping') dndStore.endDrag();
    }
  }

  return {
    generateId,
    createTileFromType,
    handleDropFromSidebar,
    handleDropIntoColumn,
    handleGlobalWidgetDrop,
    handleGlobalWidgetDropIntoColumn,
    handleReorder,
    applyPragmaticDrop,
    addRowToSection,
  };
}
