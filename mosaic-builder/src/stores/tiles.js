import { defineStore } from 'pinia';

const oloData = window.oloData || {};

// Container types that hold children
const CONTAINER_TYPES = ['section', 'row', 'column'];

function generateId() {
  if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
    return crypto.randomUUID();
  }
  return 'tile-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
}

/**
 * Recursively find a node by ID in a tree
 */
function findNodeById(nodes, id) {
  for (const node of nodes) {
    if (node.id === id) return node;
    if (Array.isArray(node.children)) {
      const found = findNodeById(node.children, id);
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
 * Find parent array and index of a node
 * Returns { parent: Array, index: number } or null
 */
function findParentAndIndex(nodes, id) {
  for (let i = 0; i < nodes.length; i++) {
    if (nodes[i].id === id) return { parent: nodes, index: i };
    if (Array.isArray(nodes[i].children)) {
      const result = findParentAndIndex(nodes[i].children, id);
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
 * Deep clone a node tree, assigning new IDs to all nodes
 */
function deepCloneWithNewIds(node) {
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
 * Recursively normalize node tree to ensure style/advanced are plain objects.
 * PHP's json_decode converts {} to [] which becomes a JS Array,
 * causing named properties (like hover) to be lost during JSON.stringify.
 */
function normalizeNodes(nodes) {
  if (!Array.isArray(nodes)) return;
  for (const node of nodes) {
    if (Array.isArray(node.style)) node.style = {};
    if (Array.isArray(node.advanced)) node.advanced = {};
    if (Array.isArray(node.children)) {
      normalizeNodes(node.children);
    }
  }
}

/**
 * Count all nodes (flat count for display)
 */
function countNodes(nodes) {
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
function isLegacyFormat(content) {
  if (!Array.isArray(content) || content.length === 0) return false;
  // If the first element is not a section, it's legacy
  return content.some(node => node.type !== 'section');
}

/**
 * Migrate legacy flat content to new Section > Row > Column > Element tree
 */
function migrateLegacyContent(content) {
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

function createSection(rows = []) {
  return {
    id: generateId(),
    type: 'section',
    settings: { style: 'default', width: 'default', padding: 'default' },
    style: {},
    advanced: {},
    children: rows,
  };
}

function createRow(layout = '50-50', columns = []) {
  return {
    id: generateId(),
    type: 'row',
    settings: { layout, gap: '16', column_gap: 'default', vertical_align: 'stretch', stack_mobile: true },
    style: {},
    advanced: {},
    children: columns,
  };
}

function createColumn(widthMedium = '', children = []) {
  return {
    id: generateId(),
    type: 'column',
    settings: { width_default: '', width_small: '', width_medium: widthMedium, width_large: '' },
    style: {},
    advanced: {},
    children: children,
  };
}

export { generateId, createSection, createRow, createColumn, CONTAINER_TYPES, migrateLegacyContent, isLegacyFormat };

export const useTilesStore = defineStore('tiles', {
  state: () => ({
    registeredTiles: [],
    canvasTiles: [],    // Array of Section nodes (tree root)
  }),

  getters: {
    tilesByCategory(state) {
      const groups = {};
      for (const tile of state.registeredTiles) {
        // Don't show structure tiles (section, column) in the palette
        if (tile.category === 'structure') continue;
        const cat = tile.category || 'general';
        if (!groups[cat]) groups[cat] = [];
        groups[cat].push(tile);
      }
      return groups;
    },

    getTileById(state) {
      return (id) => findNodeById(state.canvasTiles, id);
    },

    totalElementCount(state) {
      return countNodes(state.canvasTiles);
    },
  },

  actions: {
    async fetchRegisteredTiles() {
      try {
        const res = await fetch(`${oloData.restUrl}/tiles`, {
          headers: { 'X-WP-Nonce': oloData.nonce },
        });
        if (!res.ok) throw new Error('Failed to fetch tiles');
        this.registeredTiles = await res.json();
      } catch (err) {
        console.error('fetchRegisteredTiles error:', err);
        this.registeredTiles = [
          // Tile struttura (non mostrate nella sidebar)
          { type: 'section', name: 'Sezione', icon: 'dashicons-align-center', category: 'structure', defaults: { style: 'default', width: 'default', padding: 'default' } },
          { type: 'column', name: 'Colonna', icon: 'dashicons-editor-insertmore', category: 'structure', defaults: { width_default: '', width_small: '', width_medium: '', width_large: '' } },
          // Tile layout
          { type: 'hero', name: 'Hero', icon: 'dashicons-cover-image', category: 'layout', defaults: { title: 'Benvenuto nel nostro sito', subtitle: 'Scopri qualcosa di straordinario', background_color: '#6366F1', text_color: '#FFFFFF', cta_text: 'Inizia ora', cta_url: '#' } },
          { type: 'row', name: 'Riga / Colonne', icon: 'dashicons-columns', category: 'layout', defaults: { layout: '50-50', gap: '16', column_gap: 'default', vertical_align: 'stretch', stack_mobile: true } },
          { type: 'spacer', name: 'Spaziatore', icon: 'dashicons-arrows-alt', category: 'layout', defaults: { height: '60', show_divider: false, divider_color: '#374151', divider_width: '100', divider_thickness: '1' } },
          { type: 'divider', name: 'Divisore', icon: 'dashicons-minus', category: 'layout', defaults: { style: 'solid', width: '100', thickness: '1', color: '#374151', alignment: 'center', text: '', text_color: '#9CA3AF', icon_emoji: '' } },
          // Tile contenuto
          { type: 'content', name: 'Contenuto', icon: 'dashicons-text-page', category: 'content', defaults: { heading: 'Titolo sezione', text: 'Aggiungi il tuo contenuto qui.', image: '' } },
          { type: 'button', name: 'Pulsante', icon: 'dashicons-button', category: 'content', defaults: { text: 'Clicca qui', url: '#', target: '_self', alignment: 'center', bg_color: '#6366F1', text_color: '#FFFFFF', border_radius: '6', padding_x: '32', padding_y: '14', font_size: '16', full_width: false } },
          { type: 'headline', name: 'Titolo', icon: 'dashicons-heading', category: 'content', defaults: { heading: 'Titolo sezione', subtitle: 'Un breve sottotitolo va qui', tag: 'h2', alignment: 'center', decoration: 'line', decoration_color: '#6366F1', heading_color: '#F3F4F6', subtitle_color: '#9CA3AF', heading_size: 'lg' } },
          { type: 'testimonial', name: 'Testimonianza', icon: 'dashicons-format-quote', category: 'content', defaults: { quote: 'Un prodotto fantastico!', author_name: 'Mario Rossi', author_role: 'CEO', avatar: '', rating: '5', bg_color: '#1F2937', text_color: '#F3F4F6' } },
          { type: 'pricing', name: 'Listino prezzi', icon: 'dashicons-money-alt', category: 'content', defaults: { plan_name: 'Piano Pro', price: '29', period: '/mese', features: 'Progetti illimitati\n10 GB di spazio', cta_text: 'Inizia ora', cta_url: '#', is_popular: false, bg_color: '#1F2937', accent_color: '#6366F1', text_color: '#F3F4F6' } },
          { type: 'counter', name: 'Contatore', icon: 'dashicons-performance', category: 'content', defaults: { number: '1250', label: 'Clienti soddisfatti', prefix: '', suffix: '+', icon_emoji: '\uD83C\uDFC6', text_color: '#F3F4F6' } },
          { type: 'iconbox', name: 'Riquadro icona', icon: 'dashicons-star-filled', category: 'content', defaults: { icon_emoji: '\uD83D\uDE80', title: 'Titolo funzionalità', description: 'Una breve descrizione.', link_url: '', link_text: 'Scopri di più', alignment: 'center', text_color: '#F3F4F6' } },
          { type: 'alert', name: 'Avviso', icon: 'dashicons-warning', category: 'content', defaults: { alert_type: 'info', title: 'Attenzione!', message: 'Questo è un avviso informativo.', show_icon: true } },
          { type: 'team', name: 'Membro team', icon: 'dashicons-businessperson', category: 'content', defaults: { photo: '', name: 'Maria Bianchi', role: 'Lead Designer', bio: 'Appassionata di UX e design.', link_url: '', bg_color: '#1F2937', text_color: '#F3F4F6' } },
          { type: 'accordion', name: 'Fisarmonica', icon: 'dashicons-list-view', category: 'content', defaults: { panels: [ { id: 'p-1', title: 'Cos\'è Olobuilder?', content: 'Un potente page builder.' }, { id: 'p-2', title: 'Come funziona?', content: 'Trascina e rilascia le tile.' } ], toggle_mode: false, default_open: 'first', icon_position: 'right', icon_style: 'chevron', animate_icon: true, animation_speed: '300', header_bg: '#111827', header_bg_active: '#1a2332', header_text_color: '#F3F4F6', content_bg: '#0d1117', border_color: '#374151', text_color: '#d1d5db', gap: '0', border_radius: '8', faq_schema: false, separator_style: 'border' } },
          { type: 'tabs', name: 'Schede', icon: 'dashicons-category', category: 'content', defaults: { tabs_data: 'Scheda 1\nContenuto della prima scheda.\n---\nScheda 2\nContenuto della seconda scheda.', accent_color: '#6366F1', text_color: '#F3F4F6' } },
          { type: 'social', name: 'Link social', icon: 'dashicons-share', category: 'content', defaults: { links: 'facebook|https://facebook.com\ntwitter|https://twitter.com', size: '32', alignment: 'center', gap: '12' } },
          { type: 'countdown', name: 'Conto alla rovescia', icon: 'dashicons-clock', category: 'content', defaults: { target_date: '2026-12-31T23:59', show_days: true, show_hours: true, show_minutes: true, show_seconds: true, expired_message: 'L\'evento è iniziato!', label_days: 'Giorni', label_hours: 'Ore', label_minutes: 'Minuti', label_seconds: 'Secondi', separator: ':', bg_color: '#111827', text_color: '#F3F4F6', accent_color: '#6366F1' } },
          { type: 'html', name: 'HTML / Codice', icon: 'dashicons-editor-code', category: 'content', defaults: { html_content: '<div style="padding:20px;text-align:center;color:#9CA3AF;">HTML personalizzato</div>', sandbox: false } },
          { type: 'list', name: 'Lista', icon: 'dashicons-editor-ul', category: 'content', defaults: { items: 'check|Funzionalità uno\ncheck|Funzionalità due\ncheck|Funzionalità tre', icon_default: 'check', icon_color: '#22C55E', text_color: '#F3F4F6', spacing: '12', icon_size: '18' } },
          { type: 'table', name: 'Tabella', icon: 'dashicons-editor-table', category: 'content', defaults: { table_data: 'Funzionalità|Base|Pro\nSpazio|5 GB|50 GB\nUtenti|1|10', striped: true, bordered: true, hover_effect: true, header_bg: '#1F2937', header_text_color: '#F3F4F6', text_color: '#D1D5DB', border_color: '#374151' } },
          { type: 'progress', name: 'Barra progresso', icon: 'dashicons-chart-bar', category: 'content', defaults: { bars: 'HTML|90\nJavaScript|80\nVue.js|75', bar_color: '#6366F1', bar_bg: '#1F2937', text_color: '#F3F4F6', height: '20', show_percentage: true, animated: true, border_radius: '10' } },
          { type: 'desclist', name: 'Lista descrittiva', icon: 'dashicons-editor-justify', category: 'content', defaults: { items: 'Framework|Vue.js 3\nLinguaggio|PHP 7.4+', layout: 'stacked', term_color: '#F3F4F6', definition_color: '#9CA3AF', separator: true, border_color: '#374151' } },
          // Tile media
          { type: 'image', name: 'Immagine', icon: 'dashicons-format-image', category: 'media', defaults: { image_url: '', alt_text: '', caption: '', link_url: '', link_target: '_self', object_fit: 'cover', height: '300px' } },
          { type: 'video', name: 'Video', icon: 'dashicons-video-alt3', category: 'media', defaults: { video_url: '', aspect_ratio: '16:9', autoplay: false, muted: false, caption: '' } },
          { type: 'gallery', name: 'Galleria', icon: 'dashicons-format-gallery', category: 'media', defaults: { images: [], columns: '3', gap: '8', img_height: '200px', object_fit: 'cover' } },
          { type: 'map', name: 'Mappa', icon: 'dashicons-location', category: 'media', defaults: { address: 'Roma, Italia', zoom: '13', height: '350' } },
          { type: 'slideshow', name: 'Slideshow', icon: 'dashicons-slides', category: 'media', defaults: { slides: [ { id: 's-1', image: '', title: 'Slide uno', subtitle: 'Prima slide', link: '' } ], autoplay: true, autoplay_speed: '5000', show_arrows: true, show_dots: true, slide_height: '400', overlay_color: '#000000', text_color: '#FFFFFF', transition: 'slide' } },
          { type: 'overlay', name: 'Overlay', icon: 'dashicons-format-image', category: 'media', defaults: { image_url: '', title: 'Titolo progetto', description: 'Descrizione.', link_url: '', link_target: '_self', overlay_color: '#000000', text_color: '#FFFFFF', hover_effect: 'fade', overlay_opacity: '70', border_radius: '8', height: '300' } },
        ];
      }
    },

    /**
     * Set canvas tiles, auto-migrating legacy format
     */
    setCanvasTiles(content) {
      if (isLegacyFormat(content)) {
        console.log('[OlobuilderBuilder] Migrating legacy content to tree format');
        this.canvasTiles = migrateLegacyContent(content);
      } else {
        this.canvasTiles = content || [];
      }
      // Fix PHP json_decode round-trip: {} → [] → Array (named props lost in JSON.stringify)
      normalizeNodes(this.canvasTiles);
    },

    // === CRUD Operations (tree-aware) ===

    addTile(tile) {
      this.canvasTiles.push(tile);
    },

    /**
     * Add a child to a parent node's children array
     */
    addChild(parentId, child, index) {
      const parent = this.getTileById(parentId);
      if (!parent) return;
      if (!Array.isArray(parent.children)) parent.children = [];
      if (typeof index === 'number') {
        parent.children.splice(index, 0, child);
      } else {
        parent.children.push(child);
      }
    },

    /**
     * Remove tile from anywhere in the tree
     */
    removeTile(tileId) {
      const result = findParentAndIndex(this.canvasTiles, tileId);
      if (result) {
        result.parent.splice(result.index, 1);
      }
    },

    /**
     * Update tile settings (merges into tile.settings)
     */
    updateTile(tileId, settings) {
      const tile = this.getTileById(tileId);
      if (tile) {
        tile.settings = { ...tile.settings, ...settings };
      }
    },

    updateTileStyle(tileId, styleProps) {
      const tile = this.getTileById(tileId);
      if (tile) {
        const existing = (!tile.style || Array.isArray(tile.style)) ? {} : tile.style;
        tile.style = { ...existing, ...styleProps };
      }
    },

    updateTileAdvanced(tileId, advancedProps) {
      const tile = this.getTileById(tileId);
      if (tile) {
        const existing = (!tile.advanced || Array.isArray(tile.advanced)) ? {} : tile.advanced;
        tile.advanced = { ...existing, ...advancedProps };
      }
    },

    updateTileHover(tileId, hoverProps) {
      const tile = this.getTileById(tileId);
      if (tile) {
        if (!tile.style || Array.isArray(tile.style)) tile.style = {};
        tile.style.hover = { ...(tile.style.hover || {}), ...hoverProps };
      }
    },

    updateTileTransition(tileId, transitionProps) {
      const tile = this.getTileById(tileId);
      if (tile) {
        if (!tile.style || Array.isArray(tile.style)) tile.style = {};
        tile.style.transition = { ...(tile.style.transition || {}), ...transitionProps };
      }
    },

    updateTileDynamic(tileId, dynamicProps) {
      const tile = this.getTileById(tileId);
      if (tile) {
        tile.dynamic = { ...(tile.dynamic || {}), ...dynamicProps };
      }
    },

    removeTileDynamicField(tileId, fieldKey) {
      const tile = this.getTileById(tileId);
      if (tile && tile.dynamic) {
        delete tile.dynamic[fieldKey];
        if (Object.keys(tile.dynamic).length === 0) {
          delete tile.dynamic;
        }
      }
    },

    moveTile(fromIndex, toIndex) {
      const [moved] = this.canvasTiles.splice(fromIndex, 1);
      this.canvasTiles.splice(toIndex, 0, moved);
    },

    duplicateTile(tileId) {
      const result = findParentAndIndex(this.canvasTiles, tileId);
      if (!result) return;
      const original = result.parent[result.index];
      const clone = deepCloneWithNewIds(original);
      result.parent.splice(result.index + 1, 0, clone);
      return clone;
    },

    // === Row layout restructuring ===

    changeRowLayout(rowId, layoutKey) {
      const layoutColWidths = {
        '100': ['1-1'],
        '50-50': ['1-2', '1-2'],
        '33-33-33': ['1-3', '1-3', '1-3'],
        '25-50-25': ['1-4', '1-2', '1-4'],
        '25-25-25-25': ['1-4', '1-4', '1-4', '1-4'],
        '66-33': ['2-3', '1-3'],
        '33-66': ['1-3', '2-3'],
      };
      const row = this.getTileById(rowId);
      if (!row || row.type !== 'row') return;

      if (layoutKey === 'custom') {
        row.settings = { ...row.settings, layout: 'custom' };
        return;
      }

      const newWidths = layoutColWidths[layoutKey] || layoutColWidths['50-50'];
      const currentCols = row.children || [];
      row.settings = { ...row.settings, layout: layoutKey, custom_widths: '' };

      const newCols = newWidths.map((width, i) => {
        if (currentCols[i]) {
          currentCols[i].settings = { ...currentCols[i].settings, width_medium: width, width_custom: '' };
          return currentCols[i];
        }
        return createColumn(width, []);
      });

      if (currentCols.length > newCols.length) {
        const lastCol = newCols[newCols.length - 1];
        for (let i = newCols.length; i < currentCols.length; i++) {
          if (Array.isArray(currentCols[i].children)) {
            lastCol.children = [...(lastCol.children || []), ...currentCols[i].children];
          }
        }
      }

      row.children = newCols;
    },

    applyCustomWidths(rowId, value) {
      const row = this.getTileById(rowId);
      if (!row || row.type !== 'row') return;

      let parts = value.split(',').map(s => parseFloat(s.trim())).filter(n => !isNaN(n) && n > 0);
      if (parts.length === 0) return;
      if (parts.length > 12) parts = parts.slice(0, 12);

      const sum = parts.reduce((a, b) => a + b, 0);
      if (sum !== 100) {
        parts = parts.map(p => Math.round((p / sum) * 10000) / 100);
        const roundedSum = parts.reduce((a, b) => a + b, 0);
        parts[parts.length - 1] = Math.round((parts[parts.length - 1] + (100 - roundedSum)) * 100) / 100;
      }

      const widthsStr = parts.join(',');
      row.settings = { ...row.settings, layout: 'custom', custom_widths: widthsStr };

      const currentCols = row.children || [];
      const newCols = parts.map((pct, i) => {
        if (currentCols[i]) {
          currentCols[i].settings = { ...currentCols[i].settings, width_custom: pct, width_medium: '1-1' };
          return currentCols[i];
        }
        const col = createColumn('1-1', []);
        col.settings.width_custom = pct;
        return col;
      });

      if (currentCols.length > newCols.length) {
        const lastCol = newCols[newCols.length - 1];
        for (let i = newCols.length; i < currentCols.length; i++) {
          if (Array.isArray(currentCols[i].children)) {
            lastCol.children = [...(lastCol.children || []), ...currentCols[i].children];
          }
        }
      }

      row.children = newCols;
      return widthsStr;
    },

    // === Legacy Row/Column support (kept for backward compatibility during migration) ===

    addTileToColumn(rowTileId, colIndex, newTile) {
      const rowTile = this.getTileById(rowTileId);
      if (!rowTile) return;

      // New format: Row has Column children
      if (Array.isArray(rowTile.children)) {
        const column = rowTile.children[colIndex];
        if (column && Array.isArray(column.children)) {
          column.children.push(newTile);
          return;
        }
      }

      // Legacy format fallback
      if (rowTile.type === 'row' && rowTile.settings) {
        if (!Array.isArray(rowTile.settings.columns_data)) {
          rowTile.settings.columns_data = [];
        }
        while (rowTile.settings.columns_data.length <= colIndex) {
          rowTile.settings.columns_data.push({
            id: 'col-' + Date.now() + '-' + rowTile.settings.columns_data.length,
            tiles: [],
          });
        }
        if (!Array.isArray(rowTile.settings.columns_data[colIndex].tiles)) {
          rowTile.settings.columns_data[colIndex].tiles = [];
        }
        rowTile.settings.columns_data[colIndex].tiles.push(newTile);
      }
    },

    removeTileFromColumn(rowTileId, colIndex, childTileId) {
      const rowTile = this.getTileById(rowTileId);
      if (!rowTile) return;

      // New format
      if (Array.isArray(rowTile.children)) {
        const column = rowTile.children[colIndex];
        if (column && Array.isArray(column.children)) {
          const idx = column.children.findIndex(t => t.id === childTileId);
          if (idx !== -1) {
            column.children.splice(idx, 1);
            return;
          }
        }
      }

      // Legacy fallback
      const col = rowTile.settings?.columns_data?.[colIndex];
      if (col && Array.isArray(col.tiles)) {
        const idx = col.tiles.findIndex(t => t.id === childTileId);
        if (idx !== -1) col.tiles.splice(idx, 1);
      }
    },
  },
});
