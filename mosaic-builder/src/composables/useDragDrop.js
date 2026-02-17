import { useTilesStore, generateId, createSection, createRow, createColumn, CONTAINER_TYPES } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';

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

    const defaults = JSON.parse(JSON.stringify(registered.defaults));
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

  function handleReorder(evt) {
    if (evt.moved) {
      builderStore.isDirty = true;
    }
  }

  return {
    generateId,
    createTileFromType,
    handleDropFromSidebar,
    handleDropIntoColumn,
    handleReorder,
  };
}
