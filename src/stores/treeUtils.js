/**
 * Tree utility functions for the tile structure.
 * Extracted from tiles.js for maintainability.
 *
 * Tree structure: Section > Row > Column > Element
 * Each node has: { id, type, settings, style, advanced, children? }
 */

import { getElementDefaults } from '../config/elementRegistry.js';

// Container types that hold children
export const CONTAINER_TYPES = ['section', 'row', 'column', 'inner-columns', 'inner-column', 'floatingpanel'];

export function generateId() {
  if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
    return crypto.randomUUID();
  }
  return 'tile-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
}

/**
 * Recursively find a node by ID in a tree
 */
export function findNodeById(nodes, id, _depth = 0) {
  if (_depth > 20) return undefined; // Guard against circular references
  for (const node of nodes) {
    if (node.id === id) return node;
    if (Array.isArray(node.children)) {
      const found = findNodeById(node.children, id, _depth + 1);
      if (found) return found;
    }
    // Legacy: also search in settings.columns_data for backward compat during migration
    if (node.type === 'row' && Array.isArray(node.settings?.columns_data)) {
      for (const col of node.settings.columns_data) {
        if (Array.isArray(col?.tiles)) {
          const found = col.tiles.find(t => t.id === id);
          if (found) return found;
        }
      }
    }
  }
  return undefined;
}

/**
 * Find the ancestor path from root to a tile by ID.
 * Returns an array of tile objects: [section, row, column, tile]
 */
export function findAncestorPath(nodes, id, path = [], _depth = 0) {
  if (_depth > 20) return null;
  for (let i = 0; i < nodes.length; i++) {
    const node = nodes[i];
    const newPath = [...path, node];
    if (node.id === id) return newPath;
    if (Array.isArray(node.children)) {
      const result = findAncestorPath(node.children, id, newPath, _depth + 1);
      if (result) return result;
    }
    // Legacy columns_data support (row → columns → tiles)
    if (node.type === 'row' && Array.isArray(node.settings?.columns_data)) {
      for (let ci = 0; ci < node.settings.columns_data.length; ci++) {
        const col = node.settings.columns_data[ci];
        if (!Array.isArray(col?.tiles)) continue;
        const colNode = { id: col.id || `col-${ci}`, type: 'column', _colIndex: ci, _parentRow: node };
        for (let ti = 0; ti < col.tiles.length; ti++) {
          if (col.tiles[ti].id === id) {
            return [...newPath, colNode, col.tiles[ti]];
          }
        }
      }
    }
  }
  return null;
}

export function findParentAndIndex(nodes, id, _depth = 0) {
  if (_depth > 20) return null; // Guard against circular references
  for (let i = 0; i < nodes.length; i++) {
    if (nodes[i].id === id) return { parent: nodes, index: i };
    if (Array.isArray(nodes[i].children)) {
      const result = findParentAndIndex(nodes[i].children, id, _depth + 1);
      if (result) return result;
    }
    // Legacy columns_data support
    if (nodes[i].type === 'row' && Array.isArray(nodes[i].settings?.columns_data)) {
      for (const col of nodes[i].settings.columns_data) {
        if (Array.isArray(col?.tiles)) {
          const idx = col.tiles.findIndex(t => t.id === id);
          if (idx !== -1) return { parent: col.tiles, index: idx };
        }
      }
    }
  }
  return null;
}

/**
 * Find the node whose .children IS the given array reference
 */
export function findNodeWithChildrenArray(nodes, targetArray, _depth = 0) {
  if (_depth > 20) return null;
  for (const node of nodes) {
    if (Array.isArray(node.children)) {
      if (node.children === targetArray) return node;
      const found = findNodeWithChildrenArray(node.children, targetArray, _depth + 1);
      if (found) return found;
    }
  }
  return null;
}

/**
 * Deep clone a node tree, assigning new IDs to all nodes
 */
export function deepCloneWithNewIds(node) {
  const clone = JSON.parse(JSON.stringify(node));
  assignNewIds(clone);
  return clone;
}

function assignNewIds(node) {
  node.id = generateId();
  if (Array.isArray(node.children)) {
    node.children.forEach(assignNewIds);
  }
  // Legacy
  if (node.type === 'row' && Array.isArray(node.settings?.columns_data)) {
    node.settings.columns_data.forEach(col => {
      col.id = generateId();
      if (Array.isArray(col.tiles)) col.tiles.forEach(assignNewIds);
    });
  }
}

/**
 * Recursively normalize node tree:
 * - Ensure style/advanced are plain objects (PHP json_decode {} → [])
 * - Merge saved settings with element config defaults so missing keys
 *   get their default values (prevents toggles from losing state).
 */
export function normalizeNodes(nodes) {
  if (!Array.isArray(nodes)) return;
  for (const node of nodes) {
    if (Array.isArray(node.style)) node.style = {};
    if (Array.isArray(node.advanced)) node.advanced = {};
    if (Array.isArray(node.settings)) node.settings = {};
    // Merge element defaults into settings (saved values take precedence)
    if (node.type) {
      const defaults = getElementDefaults(node.type);
      if (defaults && Object.keys(defaults).length) {
        node.settings = { ...defaults, ...node.settings };
      }
    }
    if (Array.isArray(node.children)) {
      normalizeNodes(node.children);
    }
  }
}

/**
 * Count all nodes (flat count for display)
 */
export function countNodes(nodes) {
  let count = 0;
  for (const node of nodes) {
    count++;
    if (Array.isArray(node.children)) {
      count += countNodes(node.children);
    }
  }
  return count;
}

/**
 * Check if data is in the old flat format (no section wrappers)
 */
export function isLegacyFormat(content) {
  if (!Array.isArray(content) || content.length === 0) return false;
  // If the first element is not a section, it's legacy
  return content.some(node => node.type !== 'section');
}

/**
 * Migrate legacy flat content to new Section > Row > Column > Element tree
 */
export function migrateLegacyContent(content) {
  if (!Array.isArray(content)) return [];
  const sections = [];

  for (const tile of content) {
    if (tile.type === 'section') {
      // Already migrated
      sections.push(tile);
      continue;
    }

    if (tile.type === 'row') {
      // Wrap row in a section, convert columns_data to Column children
      const row = migrateRow(tile);
      sections.push(createSection([row]));
    } else {
      // Wrap standalone element in Section > Row > Column(1/1)
      const element = { ...tile };
      const column = createColumn('1-1', [element]);
      const row = createRow('100', [column]);
      sections.push(createSection([row]));
    }
  }

  return sections;
}

function migrateRow(rowTile) {
  const settings = { ...rowTile.settings };
  const columnsData = settings.columns_data || [];
  delete settings.columns_data;

  const layoutMap = {
    '100': ['1-1'],
    '50-50': ['1-2', '1-2'],
    '33-33-33': ['1-3', '1-3', '1-3'],
    '25-50-25': ['1-4', '1-2', '1-4'],
    '25-25-25-25': ['1-4', '1-4', '1-4', '1-4'],
    '66-33': ['2-3', '1-3'],
    '33-66': ['1-3', '2-3'],
  };

  const layout = settings.layout || '50-50';
  const widths = layoutMap[layout] || ['1-2', '1-2'];

  const columns = widths.map((width, i) => {
    const colData = columnsData[i] || { tiles: [] };
    const children = Array.isArray(colData.tiles) ? colData.tiles : [];
    return createColumn(width, children);
  });

  return {
    id: rowTile.id || generateId(),
    type: 'row',
    settings: settings,
    style: rowTile.style || {},
    advanced: rowTile.advanced || {},
    children: columns,
  };
}

export function createSection(rows = []) {
  return {
    id: generateId(),
    type: 'section',
    settings: { style: 'default', width: 'default', padding: 'default' },
    style: {},
    advanced: {},
    children: rows,
  };
}

export function createRow(layout = '50-50', columns = []) {
  return {
    id: generateId(),
    type: 'row',
    settings: { layout, gap: '16', column_gap: 'default', vertical_align: 'stretch', stack_mobile: true },
    style: {},
    advanced: {},
    children: columns,
  };
}

export function createColumn(widthMedium = '', children = []) {
  return {
    id: generateId(),
    type: 'column',
    settings: { width_default: '', width_small: '', width_medium: widthMedium, width_large: '' },
    style: {},
    advanced: {},
    children: children,
  };
}

export function createInnerColumn(widthPercent = 50, children = []) {
  return {
    id: generateId(),
    type: 'inner-column',
    settings: { width: String(widthPercent) },
    style: {},
    advanced: {},
    children: children,
  };
}

// ── Incolla: dove può andare un nodo ──
// Sezione > Riga > Colonna > Elemento; le Colonne interne (inner-columns) sono un
// elemento che contiene solo colonne interne (inner-column), che a loro volta non
// accettano section, row né inner-columns (come il trascinamento, InnerColumnsTile).
const NON_ELEMENTI = ['section', 'row', 'column', 'inner-column'];

function isElemento(tipo) {
  return NON_ELEMENTI.indexOf(tipo) === -1;
}

// genitore null = radice di una zona (body, header o footer): solo sezioni.
function accetta(genitore, tipo) {
  if (!genitore) return tipo === 'section';
  switch (genitore.type) {
    case 'section': return tipo === 'row';
    case 'row': return tipo === 'column';
    case 'inner-columns': return tipo === 'inner-column';
    case 'column':
    case 'floatingpanel': return isElemento(tipo);
    case 'inner-column': return isElemento(tipo) && tipo !== 'inner-columns';
    default: return false;
  }
}

// Avvolge un nodo fino al livello sezione (stesse forme della migrazione legacy);
// null per una colonna interna, che vive solo dentro un blocco Colonne interne.
function avvolgiFinoASezione(node) {
  const tipo = node && node.type;
  if (tipo === 'section') return node;
  if (tipo === 'row') return createSection([node]);
  if (tipo === 'column') return createSection([createRow('100', [node])]);
  if (tipo === 'inner-column') return null;
  return createSection([createRow('100', [createColumn('1-1', [node])])]);
}

// Primo figlio di quel tipo con un array children (le righe legacy con
// columns_data non ne hanno: lì si crea un involucro nuovo).
function primoFiglio(node, tipo) {
  return (node.children || []).find((c) => c && c.type === tipo && Array.isArray(c.children)) || null;
}

/**
 * Dove incollare `clip` rispetto alla tile `targetId` (menu contestuale e Ctrl+V).
 * Non modifica niente: chi chiama fa list.splice(index, 0, node).
 *
 * - Nessun bersaglio (o id sconosciuto): avvolto fino a sezione, in fondo al body.
 * - Bersaglio sezione, riga, colonna, colonna interna o blocco Colonne interne che
 *   accetta il tipo: dentro, in fondo (una riga in una sezione, una colonna in una riga,
 *   un elemento in una colonna, una colonna interna nel suo blocco).
 * - Colonna o elemento su sezione o riga: nella prima riga / prima colonna, in fondo;
 *   se mancano, il nodo arriva avvolto nella riga o colonna che manca.
 * - Altrimenti si risale: subito dopo il primo nodo (il bersaglio o un antenato) il cui
 *   genitore accetta il tipo. Stesso livello = dopo il bersaglio; una sezione copiata su
 *   un elemento va dopo la sua sezione, una riga dopo la sua riga, e così via.
 * - Mai un nodo che non sia una sezione alla radice di una zona.
 *
 * @param {Array[]} roots  array radice delle zone, il body per primo
 * @param {string|null} targetId
 * @param {object} clip  il nodo da incollare (già con id nuovi)
 * @returns {{ list: Array, index: number, node: object }|null}  node = clip o il suo
 *   involucro; null = qui non si può incollare (colonna interna fuori dal suo blocco)
 */
export function posizioneIncolla(roots, targetId, clip) {
  const tipo = clip && clip.type;
  let zona = null;
  let path = null;
  if (targetId) {
    for (const r of roots) {
      if (!Array.isArray(r)) continue;
      const p = findAncestorPath(r, targetId);
      if (p) { zona = r; path = p; break; }
    }
  }
  if (!path) {
    const body = roots[0];
    const node = avvolgiFinoASezione(clip);
    return node && Array.isArray(body) ? { list: body, index: body.length, node } : null;
  }

  const target = path[path.length - 1];
  const dentro = ['section', 'row', 'column', 'inner-column', 'inner-columns'];
  if (dentro.indexOf(target.type) !== -1 && Array.isArray(target.children) && accetta(target, tipo)) {
    return { list: target.children, index: target.children.length, node: clip };
  }

  if ((tipo === 'column' || isElemento(tipo)) && (target.type === 'section' || target.type === 'row')
    && Array.isArray(target.children)) {
    if (target.type === 'section') {
      const row = primoFiglio(target, 'row');
      if (!row) {
        const col = tipo === 'column' ? clip : createColumn('1-1', [clip]);
        return { list: target.children, index: target.children.length, node: createRow('100', [col]) };
      }
      if (tipo === 'column') return { list: row.children, index: row.children.length, node: clip };
      const col = primoFiglio(row, 'column');
      if (!col) return { list: row.children, index: row.children.length, node: createColumn('1-1', [clip]) };
      return { list: col.children, index: col.children.length, node: clip };
    }
    // Riga: l'elemento va nella prima colonna (la colonna copiata è già entrata sopra).
    const col = primoFiglio(target, 'column');
    if (!col) return { list: target.children, index: target.children.length, node: createColumn('1-1', [clip]) };
    return { list: col.children, index: col.children.length, node: clip };
  }

  for (let i = path.length - 1; i >= 0; i--) {
    if (!accetta(i > 0 ? path[i - 1] : null, tipo)) continue;
    const pos = findParentAndIndex(zona, path[i].id);
    if (pos) return { list: pos.parent, index: pos.index + 1, node: clip };
  }

  const node = avvolgiFinoASezione(clip);
  if (!node) return null;
  const pos = findParentAndIndex(zona, path[0].id);
  return { list: zona, index: pos ? pos.index + 1 : zona.length, node };
}

/**
 * Copia nuova di una COLONNA (Incolla, Duplica): via grid_column e grid_row, il
 * posto nella griglia della cella d'origine. In una riga a griglia la copia
 * finirebbe sopra la colonna che occupa quella cella; senza, la griglia la mette
 * nella prima cella libera (a capo se la fila è piena), e in una riga Flex non
 * contano. Solo sulla radice: una riga o una sezione copiate tengono le
 * posizioni delle loro colonne, che valgono dentro la riga copiata con loro.
 */
export function liberaPostoGriglia(node) {
  if (node && node.type === 'column' && node.settings && typeof node.settings === 'object'
    && !Array.isArray(node.settings)) {
    delete node.settings.grid_column;
    delete node.settings.grid_row;
  }
  return node;
}
