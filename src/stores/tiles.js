import { defineStore } from 'pinia';
import { TEMPLATES_MAP } from '../config/gridTemplates.js';
import {
  generateId, createSection, createRow, createColumn, createInnerColumn,
  CONTAINER_TYPES, migrateLegacyContent, isLegacyFormat, deepCloneWithNewIds,
  findNodeById, findAncestorPath, findParentAndIndex, findNodeWithChildrenArray,
  normalizeNodes, countNodes,
} from './treeUtils.js';

const oloData = window.oloData || {};

// Re-export tree utilities so existing imports from tiles.js keep working
export { generateId, createSection, createRow, createColumn, createInnerColumn, CONTAINER_TYPES, migrateLegacyContent, isLegacyFormat, deepCloneWithNewIds };

export const useTilesStore = defineStore('tiles', {
  state: () => ({
    registeredTiles: [],
    canvasTiles: [],    // Array of Section nodes (tree root) — body zone
    headerTiles: [],    // Array of Section nodes — header zone (unified editing)
    footerTiles: [],    // Array of Section nodes — footer zone (unified editing)
    clipboardTile: null,   // Deep-cloned tile for copy/paste
    clipboardStyle: null,  // Copied style object for paste-style
    globalWidgets: [],     // Global widgets from DB
  }),

  getters: {
    tilesByCategory(state) {
      const unordered = {};
      for (const tile of state.registeredTiles) {
        // Don't show structure tiles (section, column) in the palette
        if (tile.category === 'structure') continue;
        const cat = tile.category || 'general';
        if (!unordered[cat]) unordered[cat] = [];
        unordered[cat].push(tile);
      }
      // Return in fixed order
      const order = [
        'essential', 'layout', 'text', 'media', 'marketing',
        'interactive', 'navigation', 'dynamic', 'booking', 'olo-space',
      ];
      const groups = {};
      for (const cat of order) {
        if (unordered[cat]) groups[cat] = unordered[cat];
      }
      // Append any remaining categories not in the order list
      for (const cat in unordered) {
        if (!groups[cat]) groups[cat] = unordered[cat];
      }
      return groups;
    },

    getTileById(state) {
      return (id) => {
        // Search across all three zones (body, header, footer)
        return findNodeById(state.canvasTiles, id)
          || findNodeById(state.headerTiles, id)
          || findNodeById(state.footerTiles, id);
      };
    },

    totalElementCount(state) {
      return countNodes(state.canvasTiles)
        + countNodes(state.headerTiles)
        + countNodes(state.footerTiles);
    },
  },

  actions: {
    findSectionIndexForTile(tileId) {
      for (let i = 0; i < this.canvasTiles.length; i++) {
        if (findNodeById([this.canvasTiles[i]], tileId)) return i;
      }
      return -1;
    },

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
          { type: 'inner-columns', name: 'Colonne interne', icon: 'dashicons-table-col-after', category: 'layout', defaults: { layout: '50-50', gap: '16', vertical_align: 'stretch', stack_mobile: true } },
          { type: 'spacer', name: 'Spaziatore', icon: 'dashicons-arrows-alt', category: 'essential', defaults: { height: '60', show_divider: false, divider_color: '', divider_width: '100', divider_thickness: '1' } },
          { type: 'divider', name: 'Divisore', icon: 'dashicons-minus', category: 'essential', defaults: { style: 'solid', width: '100', thickness: '1', color: '', alignment: 'center', text: '', text_color: '', icon_emoji: '' } },
          // Tile essenziale
          { type: 'content', name: 'Contenuto', icon: 'dashicons-text-page', category: 'essential', defaults: { heading: 'Titolo sezione', text: 'Aggiungi il tuo contenuto qui.', image: '' } },
          { type: 'button', name: 'Pulsante', icon: 'dashicons-button', category: 'essential', defaults: { text: 'Clicca qui', url: '#', target: '_self', alignment: 'center', bg_color: '', text_color: '#FFFFFF', border_radius: '6', padding_x: '32', padding_y: '14', font_size: '16', full_width: false } },
          { type: 'headline', name: 'Titolo', icon: 'dashicons-heading', category: 'essential', defaults: { heading: 'Titolo sezione', subtitle: 'Un breve sottotitolo va qui', tag: 'h2', alignment: 'center', decoration: 'line', decoration_color: '', heading_color: '', subtitle_color: '', heading_size: 'lg' } },
          { type: 'testimonial', name: 'Testimonianza', icon: 'dashicons-format-quote', category: 'marketing', defaults: { quote: 'Un prodotto fantastico!', author_name: 'Mario Rossi', author_role: 'CEO', avatar: '', rating: '5', bg_color: '', text_color: '' } },
          { type: 'pricing', name: 'Listino prezzi', icon: 'dashicons-money-alt', category: 'marketing', defaults: { plan_name: 'Piano Pro', price: '29', period: '/mese', features: 'Progetti illimitati\n10 GB di spazio', cta_text: 'Inizia ora', cta_url: '#', is_popular: false, bg_color: '', accent_color: '', text_color: '' } },
          { type: 'counter', name: 'Contatore', icon: 'dashicons-performance', category: 'marketing', defaults: { number: '1250', label: 'Clienti soddisfatti', prefix: '', suffix: '+', icon_emoji: 'bolt', text_color: '' } },
          { type: 'iconbox', name: 'Riquadro icona', icon: 'dashicons-star-filled', category: 'marketing', defaults: { icon_emoji: 'star', title: 'Titolo funzionalità', description: 'Una breve descrizione.', link_url: '', link_text: 'Scopri di più', alignment: 'center', text_color: '' } },
          { type: 'alert', name: 'Avviso', icon: 'dashicons-warning', category: 'text', defaults: { alert_type: 'info', title: 'Attenzione!', message: 'Questo è un avviso informativo.', show_icon: true } },
          { type: 'team', name: 'Membro team', icon: 'dashicons-businessperson', category: 'marketing', defaults: { photo: '', name: 'Maria Bianchi', role: 'Lead Designer', bio: 'Appassionata di UX e design.', link_url: '', bg_color: '', text_color: '' } },
          { type: 'accordion', name: 'Fisarmonica', icon: 'dashicons-list-view', category: 'interactive', defaults: { panels: [ { id: 'p-1', title: 'Cos\'è Olobuild?', content: 'Un potente page builder.' }, { id: 'p-2', title: 'Come funziona?', content: 'Trascina e rilascia le tile.' } ], toggle_mode: false, default_open: 'first', icon_position: 'right', icon_style: 'chevron', animate_icon: true, animation_speed: '300', header_bg: '', header_bg_active: '', header_text_color: '', content_bg: '', border_color: '', text_color: '', gap: '0', border_radius: '8', faq_schema: false, separator_style: 'border' } },
          { type: 'tabs', name: 'Schede', icon: 'dashicons-category', category: 'interactive', defaults: { tabs_data: 'Scheda 1\nContenuto della prima scheda.\n---\nScheda 2\nContenuto della seconda scheda.', accent_color: '', text_color: '' } },
          { type: 'social', name: 'Link social', icon: 'dashicons-share', category: 'marketing', defaults: { links: 'facebook|https://facebook.com\ntwitter|https://twitter.com', size: '32', alignment: 'center', gap: '12' } },
          { type: 'countdown', name: 'Conto alla rovescia', icon: 'dashicons-clock', category: 'marketing', defaults: { target_date: '2026-12-31T23:59', show_days: true, show_hours: true, show_minutes: true, show_seconds: true, expired_message: 'L\'evento è iniziato!', label_days: 'Giorni', label_hours: 'Ore', label_minutes: 'Minuti', label_seconds: 'Secondi', separator: ':', bg_color: '', text_color: '', accent_color: '' } },
          { type: 'html', name: 'HTML / Codice', icon: 'dashicons-editor-code', category: 'text', defaults: { html_content: '<div style="padding:20px;text-align:center;color:#9CA3AF;">HTML personalizzato</div>', sandbox: false } },
          { type: 'list', name: 'Lista', icon: 'dashicons-editor-ul', category: 'text', defaults: { items: 'check|Funzionalità uno\ncheck|Funzionalità due\ncheck|Funzionalità tre', icon_default: 'check', icon_color: '', text_color: '', spacing: '12', icon_size: '18' } },
          { type: 'table', name: 'Tabella', icon: 'dashicons-editor-table', category: 'text', defaults: { table_data: 'Funzionalità|Base|Pro\nSpazio|5 GB|50 GB\nUtenti|1|10', striped: true, bordered: true, hover_effect: true, header_bg: '', header_text_color: '', text_color: '', border_color: '' } },
          { type: 'progress', name: 'Barra progresso', icon: 'dashicons-chart-bar', category: 'marketing', defaults: { bars: 'HTML|90\nJavaScript|80\nVue.js|75', bar_color: '', bar_bg: '', text_color: '', height: '20', show_percentage: true, animated: true, border_radius: '10' } },
          { type: 'desclist', name: 'Lista descrittiva', icon: 'dashicons-editor-justify', category: 'text', defaults: { items: 'Framework|Vue.js 3\nLinguaggio|PHP 7.4+', layout: 'stacked', term_color: '', definition_color: '', separator: true, border_color: '' } },
          // Tile essenziale (media)
          { type: 'image', name: 'Immagine', icon: 'dashicons-format-image', category: 'essential', defaults: { image_url: '', alt_text: '', caption: '', link_url: '', link_target: '_self', object_fit: 'cover', height: '300px' } },
          { type: 'video', name: 'Video', icon: 'dashicons-video-alt3', category: 'essential', defaults: { video_url: '', aspect_ratio: '16:9', autoplay: false, muted: false, caption: '' } },
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

    /**
     * Set header tiles for unified editing
     */
    setHeaderTiles(content) {
      if (isLegacyFormat(content)) {
        this.headerTiles = migrateLegacyContent(content);
      } else {
        this.headerTiles = content || [];
      }
      normalizeNodes(this.headerTiles);
    },

    /**
     * Set footer tiles for unified editing
     */
    setFooterTiles(content) {
      if (isLegacyFormat(content)) {
        this.footerTiles = migrateLegacyContent(content);
      } else {
        this.footerTiles = content || [];
      }
      normalizeNodes(this.footerTiles);
    },

    /**
     * Determine which zone a tile belongs to
     * Returns 'header' | 'body' | 'footer' | null
     */
    getZoneForTile(tileId) {
      if (findNodeById(this.headerTiles, tileId)) return 'header';
      if (findNodeById(this.canvasTiles, tileId)) return 'body';
      if (findNodeById(this.footerTiles, tileId)) return 'footer';
      return null;
    },

    /**
     * Get the ancestor path from root to a tile.
     * Returns array of { id, type, label } objects.
     */
    getAncestorPath(tileId) {
      if (!tileId) return [];
      const path = findAncestorPath(this.headerTiles, tileId)
        || findAncestorPath(this.canvasTiles, tileId)
        || findAncestorPath(this.footerTiles, tileId);
      return path || [];
    },

    /**
     * Get the root tiles array for a given zone
     */
    getZoneTiles(zone) {
      if (zone === 'header') return this.headerTiles;
      if (zone === 'footer') return this.footerTiles;
      return this.canvasTiles;
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
     * Remove tile from anywhere in the tree (searches all zones)
     */
    removeTile(tileId) {
      const result = findParentAndIndex(this.canvasTiles, tileId)
        || findParentAndIndex(this.headerTiles, tileId)
        || findParentAndIndex(this.footerTiles, tileId);
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

    applyStylePreset(tileId, styleObj) {
      const tile = this.getTileById(tileId);
      if (!tile) return;
      const existing = (!tile.style || Array.isArray(tile.style)) ? {} : tile.style;
      const presetCopy = JSON.parse(JSON.stringify(styleObj || {}));
      tile.style = { ...existing, ...presetCopy };
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
      const result = findParentAndIndex(this.canvasTiles, tileId)
        || findParentAndIndex(this.headerTiles, tileId)
        || findParentAndIndex(this.footerTiles, tileId);
      if (!result) return;
      const original = result.parent[result.index];
      const clone = deepCloneWithNewIds(original);
      result.parent.splice(result.index + 1, 0, clone);
      return clone;
    },

    copyTile(tileId) {
      const tile = this.getTileById(tileId);
      if (tile) {
        this.clipboardTile = JSON.parse(JSON.stringify(tile));
      }
    },

    pasteTile(parentId, index) {
      if (!this.clipboardTile) return null;
      const clone = deepCloneWithNewIds(this.clipboardTile);
      if (parentId) {
        this.addChild(parentId, clone, index);
      } else {
        this.canvasTiles.push(clone);
      }
      return clone;
    },

    pasteAfterTile(tileId) {
      if (!this.clipboardTile) return null;
      const result = findParentAndIndex(this.canvasTiles, tileId)
        || findParentAndIndex(this.headerTiles, tileId)
        || findParentAndIndex(this.footerTiles, tileId);
      if (result) {
        const clone = deepCloneWithNewIds(this.clipboardTile);
        result.parent.splice(result.index + 1, 0, clone);
        return clone;
      }
      return this.pasteTile(null);
    },

    copyStyle(tileId) {
      const tile = this.getTileById(tileId);
      if (tile) {
        this.clipboardStyle = {
          type: tile.type,
          style: JSON.parse(JSON.stringify(tile.style || {})),
          settings: JSON.parse(JSON.stringify(tile.settings || {})),
        };
        try { localStorage.setItem('olo_clipboard_style', JSON.stringify(this.clipboardStyle)); } catch(e) {}
      }
    },

    pasteStyle(tileId) {
      const tile = this.getTileById(tileId);
      if (!this.clipboardStyle) {
        try { const s = localStorage.getItem('olo_clipboard_style'); if (s) this.clipboardStyle = JSON.parse(s); } catch(e) {}
      }
      if (!tile || !this.clipboardStyle) return;

      // Always paste style properties (margin, padding, bg, border, shadow, etc.)
      const s = this.clipboardStyle.style || this.clipboardStyle;
      const existing = (!tile.style || Array.isArray(tile.style)) ? {} : { ...tile.style };
      const styleKeys = [
        'margin_top', 'margin_right', 'margin_bottom', 'margin_left',
        'padding_top', 'padding_right', 'padding_bottom', 'padding_left',
        'bg_type', 'bg_color', 'bg_gradient_from', 'bg_gradient_to', 'bg_gradient_angle',
        'bg_image_url', 'bg_image_size', 'bg_image_position',
        'border_width', 'border_style', 'border_color', 'border_radius',
        'shadow', 'opacity', 'hover', 'transition',
        'custom_css',
      ];
      for (const key of styleKeys) {
        if (s[key] !== undefined) {
          existing[key] = JSON.parse(JSON.stringify(s[key]));
        }
      }
      tile.style = existing;

      // If same tile type, also paste settings (except content-specific ones)
      if (this.clipboardStyle.type === tile.type && this.clipboardStyle.settings) {
        const srcSettings = this.clipboardStyle.settings;
        const tgtSettings = tile.settings || {};
        // Keys to NEVER paste (content, not style)
        const contentKeys = [
          'images', 'items', 'slides', 'content', 'text', 'title', 'subtitle',
          'description', 'html', 'url', 'link', 'href', 'video_url', 'file_url',
          'embed', 'shortcode', 'icon', 'label', 'caption', 'alt',
          'post_type', 'posts_per_page', 'query', 'taxonomy', 'terms',
          'service_id', 'source_type', 'wp_menu_id',
        ];
        for (const key of Object.keys(srcSettings)) {
          if (!contentKeys.includes(key)) {
            tgtSettings[key] = JSON.parse(JSON.stringify(srcSettings[key]));
          }
        }
        tile.settings = { ...tgtSettings };
      }
    },

    moveUp(tileId) {
      const result = findParentAndIndex(this.canvasTiles, tileId)
        || findParentAndIndex(this.headerTiles, tileId)
        || findParentAndIndex(this.footerTiles, tileId);
      if (!result || result.index === 0) return;
      const [item] = result.parent.splice(result.index, 1);
      result.parent.splice(result.index - 1, 0, item);
    },

    moveDown(tileId) {
      const result = findParentAndIndex(this.canvasTiles, tileId)
        || findParentAndIndex(this.headerTiles, tileId)
        || findParentAndIndex(this.footerTiles, tileId);
      if (!result || result.index >= result.parent.length - 1) return;
      const [item] = result.parent.splice(result.index, 1);
      result.parent.splice(result.index + 1, 0, item);
    },

    /**
     * Move a tile to the adjacent column (left or right).
     * direction: -1 = previous column, +1 = next column
     */
    moveToSiblingColumn(tileId, direction) {
      const allRoots = [this.canvasTiles, this.headerTiles, this.footerTiles];

      // Find the tile and the column it's in
      for (const root of allRoots) {
        const tileResult = findParentAndIndex(root, tileId);
        if (!tileResult) continue;

        // The parent array is the column's children — find the column node
        // Walk the tree to find the column that contains this parent array
        const colInfo = findNodeWithChildrenArray(root, tileResult.parent);
        if (!colInfo || (colInfo.type !== 'column' && colInfo.type !== 'inner-column')) continue;

        // Now find the column's parent (the row) and its index
        const colResult = findParentAndIndex(root, colInfo.id);
        if (!colResult) continue;

        const siblingIdx = colResult.index + direction;
        if (siblingIdx < 0 || siblingIdx >= colResult.parent.length) return; // No sibling

        const siblingCol = colResult.parent[siblingIdx];
        if (!siblingCol || !Array.isArray(siblingCol.children)) return;

        // Remove from current column
        const [item] = tileResult.parent.splice(tileResult.index, 1);
        // Add to sibling column (at the end)
        siblingCol.children.push(item);
        return;
      }
    },

    /**
     * Insert a tile after another tile (same parent array).
     */
    insertAfter(targetTileId, newTile) {
      const allRoots = [this.canvasTiles, this.headerTiles, this.footerTiles];
      for (const root of allRoots) {
        const result = findParentAndIndex(root, targetTileId);
        if (result) {
          result.parent.splice(result.index + 1, 0, newTile);
          return true;
        }
      }
      return false;
    },

    /**
     * Move a node near another node (before or after).
     * Works across all zones and at any tree depth.
     */
    moveNodeNear(sourceId, targetId, before) {
      const allRoots = [this.canvasTiles, this.headerTiles, this.footerTiles];

      // Don't move structural containers (section, row, column) via grip drag
      // to avoid breaking the tree structure
      let sourceCheck = null;
      for (const root of allRoots) {
        sourceCheck = findNodeById(root, sourceId);
        if (sourceCheck) break;
      }
      if (!sourceCheck) return;
      const structuralTypes = ['section', 'row', 'column', 'inner-column'];

      // Find target node to check its type
      let targetNode = null;
      for (const root of allRoots) {
        targetNode = findNodeById(root, targetId);
        if (targetNode) break;
      }
      if (!targetNode) return;

      // If target is a column/inner-column and source is a regular element,
      // insert INTO the column's children instead of next to it
      if ((targetNode.type === 'column' || targetNode.type === 'inner-column') &&
          !structuralTypes.includes(sourceCheck.type)) {
        // Remove source from its current position
        for (const root of allRoots) {
          const result = findParentAndIndex(root, sourceId);
          if (result) {
            result.parent.splice(result.index, 1);
            break;
          }
        }
        if (!Array.isArray(targetNode.children)) targetNode.children = [];
        if (before) {
          targetNode.children.unshift(sourceCheck);
        } else {
          targetNode.children.push(sourceCheck);
        }
        return;
      }

      // If target is a section or row and source is a regular element, skip
      // (don't insert elements at section/row level)
      if ((targetNode.type === 'section' || targetNode.type === 'row') &&
          !structuralTypes.includes(sourceCheck.type)) {
        return;
      }

      // Find and remove source
      let sourceNode = null;
      for (const root of allRoots) {
        const result = findParentAndIndex(root, sourceId);
        if (result) {
          sourceNode = result.parent.splice(result.index, 1)[0];
          break;
        }
      }
      if (!sourceNode) return;

      // Find target and insert near it
      for (const root of allRoots) {
        const result = findParentAndIndex(root, targetId);
        if (result) {
          const idx = before ? result.index : result.index + 1;
          result.parent.splice(idx, 0, sourceNode);
          return;
        }
      }

      // Fallback: put it back in canvasTiles
      this.canvasTiles.push(sourceNode);
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

    /**
     * Switch a row to CSS Grid layout using a grid template.
     * Preserves existing children content.
     */
    changeRowToGrid(rowId, templateId) {
      const tpl = TEMPLATES_MAP[templateId];
      if (!tpl) return;

      const row = this.getTileById(rowId);
      if (!row || row.type !== 'row') return;

      const currentCols = row.children || [];
      const cellCount = tpl.cells.length;

      // Create new columns matching cell count
      const newCols = tpl.cells.map((cell, i) => {
        let col;
        if (currentCols[i]) {
          col = currentCols[i];
        } else {
          col = createColumn('1-1', []);
        }
        col.settings = {
          ...col.settings,
          grid_column: cell.gridColumn || '',
          grid_row: cell.gridRow || '',
        };
        return col;
      });

      // If reducing cells, merge excess children into last cell
      if (currentCols.length > cellCount) {
        const lastCol = newCols[newCols.length - 1];
        for (let i = cellCount; i < currentCols.length; i++) {
          if (Array.isArray(currentCols[i].children)) {
            lastCol.children = [...(lastCol.children || []), ...currentCols[i].children];
          }
        }
      }

      row.settings = {
        ...row.settings,
        layout_mode: 'grid',
        grid_template: templateId,
        grid_columns: tpl.gridTemplateColumns,
        grid_rows: tpl.gridTemplateRows,
      };
      row.children = newCols;
    },

    /**
     * Switch a grid row back to flex layout.
     */
    changeRowToFlex(rowId, layoutKey = '50-50') {
      const row = this.getTileById(rowId);
      if (!row || row.type !== 'row') return;

      // Clear grid settings
      row.settings = {
        ...row.settings,
        layout_mode: 'flex',
        grid_template: '',
        grid_columns: '',
        grid_rows: '',
      };

      // Clear grid placement from columns
      (row.children || []).forEach(col => {
        if (col.settings) {
          delete col.settings.grid_column;
          delete col.settings.grid_row;
        }
      });

      // Apply flex layout
      this.changeRowLayout(rowId, layoutKey);
    },

    changeInnerLayout(innerColsId, layoutKey) {
      const innerLayoutWidths = {
        '50-50': [50, 50],
        '33-33-33': [33.33, 33.33, 33.34],
        '25-75': [25, 75],
        '75-25': [75, 25],
        '25-50-25': [25, 50, 25],
      };
      const node = this.getTileById(innerColsId);
      if (!node || node.type !== 'inner-columns') return;

      const newWidths = innerLayoutWidths[layoutKey] || innerLayoutWidths['50-50'];
      const currentCols = node.children || [];
      node.settings = { ...node.settings, layout: layoutKey };

      const newCols = newWidths.map((w, i) => {
        if (currentCols[i]) {
          currentCols[i].settings = { ...currentCols[i].settings, width: String(w) };
          return currentCols[i];
        }
        return createInnerColumn(w, []);
      });

      // If reducing columns, move excess children to last column
      if (currentCols.length > newCols.length) {
        const lastCol = newCols[newCols.length - 1];
        for (let i = newCols.length; i < currentCols.length; i++) {
          if (Array.isArray(currentCols[i].children)) {
            lastCol.children = [...(lastCol.children || []), ...currentCols[i].children];
          }
        }
      }

      node.children = newCols;
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

    // === Global Widgets ===

    async fetchGlobalWidgets() {
      try {
        const res = await fetch(`${oloData.restUrl}/global-widgets`, {
          headers: { 'X-WP-Nonce': oloData.nonce },
        });
        if (!res.ok) throw new Error('Failed to fetch global widgets');
        this.globalWidgets = await res.json();
      } catch (err) {
        console.error('fetchGlobalWidgets error:', err);
      }
    },

    async saveAsGlobalWidget(tileId) {
      const tile = this.getTileById(tileId);
      if (!tile) return;

      const tileData = JSON.parse(JSON.stringify(tile));
      // Remove the id so the stored data is a clean snapshot
      delete tileData.id;
      delete tileData.children;
      delete tileData.global_id;

      const name = tile.settings?.heading || tile.settings?.title || tile.settings?.text || tile.type || 'Widget globale';

      try {
        const res = await fetch(`${oloData.restUrl}/global-widgets`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': oloData.nonce,
          },
          body: JSON.stringify({ name, tile_data: tileData }),
        });
        if (!res.ok) throw new Error('Failed to save global widget');
        const result = await res.json();

        // Set global_id on the original tile
        tile.global_id = result.id;

        // Refresh list
        await this.fetchGlobalWidgets();
        return result;
      } catch (err) {
        console.error('saveAsGlobalWidget error:', err);
      }
    },

    async updateGlobalWidget(globalId, tileId) {
      const tile = this.getTileById(tileId);
      if (!tile) return;

      const tileData = JSON.parse(JSON.stringify(tile));
      delete tileData.id;
      delete tileData.children;
      delete tileData.global_id;

      try {
        const res = await fetch(`${oloData.restUrl}/global-widgets/${globalId}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': oloData.nonce,
          },
          body: JSON.stringify({ tile_data: tileData }),
        });
        if (!res.ok) throw new Error('Failed to update global widget');
        await this.fetchGlobalWidgets();
      } catch (err) {
        console.error('updateGlobalWidget error:', err);
      }
    },

    detachGlobalWidget(tileId) {
      const tile = this.getTileById(tileId);
      if (tile) {
        delete tile.global_id;
      }
    },

    insertGlobalWidget(globalId) {
      const gw = this.globalWidgets.find(w => String(w.id) === String(globalId));
      if (!gw) return null;

      let tileData;
      if (typeof gw.tile_data === 'string') {
        try { tileData = JSON.parse(gw.tile_data); } catch { return null; }
      } else {
        tileData = JSON.parse(JSON.stringify(gw.tile_data));
      }

      // Create a new tile from the global widget data
      const newTile = {
        ...tileData,
        id: generateId(),
        global_id: parseInt(gw.id),
      };

      return newTile;
    },

    async deleteGlobalWidget(globalId) {
      try {
        const res = await fetch(`${oloData.restUrl}/global-widgets/${globalId}`, {
          method: 'DELETE',
          headers: { 'X-WP-Nonce': oloData.nonce },
        });
        if (!res.ok) throw new Error('Failed to delete global widget');
        await this.fetchGlobalWidgets();
      } catch (err) {
        console.error('deleteGlobalWidget error:', err);
      }
    },

    /**
     * Al salvataggio: aggiorna il master nel DB per ogni tile con global_id.
     */
    async syncGlobalWidgetsOnSave() {
      const globals = [];
      const walk = (nodes) => {
        for (const n of nodes) {
          if (n.global_id) globals.push(n);
          if (Array.isArray(n.children)) walk(n.children);
        }
      };
      walk(this.canvasTiles);
      walk(this.headerTiles);
      walk(this.footerTiles);
      for (const tile of globals) {
        await this.updateGlobalWidget(tile.global_id, tile.id);
      }
    },

    /**
     * Al caricamento: sostituisci i dati locali con quelli del master dal DB.
     */
    syncGlobalWidgetsOnLoad() {
      if (!this.globalWidgets.length) return;
      const gwMap = {};
      for (const gw of this.globalWidgets) {
        gwMap[String(gw.id)] = gw;
      }
      const walk = (nodes) => {
        for (const n of nodes) {
          if (n.global_id) {
            const master = gwMap[String(n.global_id)];
            if (master) {
              let masterData;
              if (typeof master.tile_data === 'string') {
                try { masterData = JSON.parse(master.tile_data); } catch { masterData = null; }
              } else {
                masterData = master.tile_data;
              }
              if (masterData) {
                // Sovrascrivi settings e style dal master, mantieni id, global_id, children
                n.type = masterData.type || n.type;
                n.settings = JSON.parse(JSON.stringify(masterData.settings || {}));
                n.style = JSON.parse(JSON.stringify(masterData.style || {}));
                if (masterData.advanced) n.advanced = JSON.parse(JSON.stringify(masterData.advanced));
              }
            }
          }
          if (Array.isArray(n.children)) walk(n.children);
        }
      };
      walk(this.canvasTiles);
      walk(this.headerTiles);
      walk(this.footerTiles);
    },
  },
});
