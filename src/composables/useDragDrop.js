import { useTilesStore, generateId, createSection, createRow, createColumn, createInnerColumn, CONTAINER_TYPES } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';
import { useDnDStore } from '@/stores/dnd';
import { useHistory } from '@/composables/useHistory';
import { extractClosestEdge, isOloData } from '@/composables/useDnD';
import { getElementDef } from '@/config/elementRegistry';

// v3.55.36 — placeholder universali per nuove tile.
// Quando si trascina una tile dalla sidebar, i field testo lunghi (textarea/rich-text)
// e i field immagine vuoti vengono popolati con un Lorem ipsum standard / un'immagine
// segnaposto grigia. Pensato per dare contesto visivo immediato senza che l'utente
// debba scrivere/uploadare prima di vedere la tile renderizzata.
const PLACEHOLDER_LOREM = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.';

// Rettangolo grigio 800×450 PNG — file statico servito dal plugin.
// PNG (non SVG) perché molti server WP hanno upload SVG disabilitato per sicurezza
// e alcuni filtri sanitize_url/wp_check_filetype potrebbero rifiutare data: URI o
// SVG inline. Il PNG è il formato universale più sicuro.
function _placeholderImageUrl() {
  const base = (typeof window !== 'undefined' && window.oloData && window.oloData.pluginUrl)
    ? window.oloData.pluginUrl
    : '/wp-content/plugins/olobuild/';
  return base.replace(/\/$/, '') + '/assets/img/placeholder-image.png';
}
const PLACEHOLDER_IMAGE = _placeholderImageUrl();

// Field text "lunghi" (descrizioni, contenuti rich) → Lorem ipsum se vuoto o default banale.
const LONG_TEXT_TYPES = new Set(['textarea', 'rich-text', 'wysiwyg']);

// Field text che NON ricevono Lorem ipsum (URL, ID, target, alt-text, didascalia, link).
// `_url$` invece di `_url` per non escludere `image_url` (che è image, non text).
// Inclusi anche heading/title/name/label: sono testi CORTI per natura — il Lorem ipsum
// lungo li trasforma in paragrafi illeggibili nel builder. Il default del tile (es.
// "Nuovo Titolo") è già appropriato come placeholder.
// v1.0.58 — ampliata copertura:
//   - _time$, _seconds: campi numerici di durata (start_time, end_time, pause_time)
//   - overlay_text|overlay: testi opzionali che se vuoti devono restare vuoti (no overlay)
//   - code|html|css|js$|expression: codice tecnico, mai testo libero
//   - _date|_date_format|count|index|value|min|max|step|font_size: numerici/select
//   - search|placeholder: input UI (placeholder è già il proprio "valore")
//   - _from|_to|_after|_before: range temporali/numerici
const TEXT_PROTECTED_RE = /(_url$|^url$|_href|^href$|_id$|_target|^target$|_class|email|phone|^slug$|^icon|_icon|font_family|font_weight|color|align|width|height|size|radius|padding|margin|border|shadow|effect|preset|opacity|^tag|layout|columns|gap|speed|duration|delay|enabled|visible|show|hide|type$|kind|mode|style$|^alt$|alt_text|caption|^link|_link|^heading$|^title$|^name$|^label$|^cta_text$|^button_text$|_time$|^time$|_seconds$|_overlay_text$|^overlay_text$|^overlay$|_overlay$|^code$|_code$|^html$|_html$|^css$|_css$|^js$|_js$|^expression$|^script$|_format$|count|^index$|^value$|^min$|^max$|^step$|font_size|placeholder|^search$|_search$|_from$|_to$|_after$|_before$|^path$|_path$|custom_path|^date$|_date$|target_date|_message$|expired_message)/i;

// Field image SECONDARI (hover, fallback, alt) che restano vuoti — il placeholder va
// solo sul campo principale, non su quelli "opzionali". Senza questo: trascini un'immagine
// e vedi il placeholder al passaggio del mouse invece che nello stato base.
const IMAGE_SECONDARY_RE = /(hover|secondary|alternate|fallback|backup|^alt_image|_alt$)/i;

/**
 * Applica placeholder ai field "vuoti" della tile appena creata.
 * - textarea / rich-text → Lorem ipsum
 * - text vuoto (escluse label tecniche/URL/alt/caption) → Lorem ipsum
 * - image vuota PRINCIPALE → PNG segnaposto grigio (NO hover_image, alt_image, ecc.)
 *
 * Mantiene intatti i default già configurati dal tile (es. cta_text='Inizia ora').
 */
function applyContentPlaceholders(settings, fields) {
  if (!Array.isArray(fields) || !settings) return;
  for (const f of fields) {
    if (!f || !f.key || !f.type) continue;

    const cur = settings[f.key];
    const isEmpty = cur === '' || cur === undefined || cur === null;

    if (LONG_TEXT_TYPES.has(f.type) && isEmpty) {
      if (TEXT_PROTECTED_RE.test(f.key)) continue;
      settings[f.key] = PLACEHOLDER_LOREM;
    } else if (f.type === 'text' && isEmpty) {
      if (TEXT_PROTECTED_RE.test(f.key)) continue;
      settings[f.key] = PLACEHOLDER_LOREM;
    } else if (f.type === 'image' && isEmpty) {
      if (IMAGE_SECONDARY_RE.test(f.key)) continue;
      settings[f.key] = PLACEHOLDER_IMAGE;
    }
  }
}

// v3.55.49 — guard di idempotenza AGGRESSIVO: blocca insert duplicati con stessa
// tile-type entro 500ms, IGNORANDO indice/columnId/parentId. Causa: 2 path di
// drop (CanvasDragOverlay.onDrop + monitor applyPragmaticDrop) possono firare
// quasi simultaneamente con signature diverse (es. insertIndex=0 vs columnId='x')
// per la stessa tile dropped → il dedup precedente non li riconosceva come duplicati.
// Rischio teorico: utente che droppa 2 tile dello stesso tipo entro 500ms → la 2a
// viene ignorata. Trade-off accettabile (raro caso reale).
let _lastInsertKind = null;
let _lastInsertAt = 0;
const INSERT_DEDUP_MS = 500;

function shouldDedupInsert(kind) {
  const now = Date.now();
  if (_lastInsertKind === kind && (now - _lastInsertAt) < INSERT_DEDUP_MS) {
    return true; // duplicato → skippa
  }
  _lastInsertKind = kind;
  _lastInsertAt = now;
  return false;
}

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

    // v3.55.36 — applica placeholder universali (Lorem ipsum, immagine segnaposto)
    // ai field testo lunghi/immagine vuoti, leggendo i fields[] dall'elementRegistry.
    const def = getElementDef(tileType);
    applyContentPlaceholders(defaults, def?.fields);

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
    if (shouldDedupInsert('tile:' + tileType)) return;
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
    if (shouldDedupInsert('tile:' + tileType)) return null;
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
    if (shouldDedupInsert('gw:' + globalId)) return null;
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
    if (shouldDedupInsert('gw:' + globalId)) return null;
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
    tilesStore._bumpVersion();
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
    tilesStore._bumpVersion();
    builderStore.selectTile(newTile.id);
  }

  function insertElementRelativeToElement(tileType, col, elIndex, edge) {
    const newTile = createTileFromType(tileType);
    if (!newTile) return;
    if (!Array.isArray(col.children)) col.children = [];
    const at = edge === 'bottom' || edge === 'right' ? elIndex + 1 : elIndex;
    col.children.splice(at, 0, newTile);
    tilesStore._bumpVersion();
    builderStore.selectTile(newTile.id);
  }

  function insertGlobalWidgetRelativeToElement(globalId, col, elIndex, edge) {
    const newTile = tilesStore.insertGlobalWidget(globalId);
    if (!newTile) return;
    if (!Array.isArray(col.children)) col.children = [];
    const at = edge === 'bottom' || edge === 'right' ? elIndex + 1 : elIndex;
    col.children.splice(at, 0, newTile);
    tilesStore._bumpVersion();
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

    // v3.55.49 — guard signature semplificato: solo tile/node/widget identifier,
    // ignoro target. Stesso fix di handleDropFromSidebar per evitare che 2 path
    // con target diversi (es. canvas-overlay vs column-body) bypassino il dedup.
    const sig = 'tile:' + (payload.tileType || payload.nodeId || payload.globalId || '');
    if (shouldDedupInsert(sig)) return;

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
