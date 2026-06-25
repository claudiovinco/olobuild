import { defineStore } from 'pinia';
import { useTilesStore as useTilesStoreRef } from './tiles';
import { useToast } from '../composables/useToast.js';

function getOloData() {
  return window.oloData || {};
}

export const useBuilderStore = defineStore('builder', {
  state: () => ({
    currentTemplate: null,
    selectedTileId: null,
    // Multi-selezione (MVP, additiva): set di id selezionati via ctrl/cmd-click.
    // selectedTileId resta la selezione "primaria" (guida l'inspector).
    selectedTileIds: [],
    isDirty: false,
    isSaving: false,
    viewMode: 'desktop', // desktop | widescreen | tablet_landscape | tablet | mobile_landscape | mobile
    previewMode: false,
    wireframeMode: false,
    livePreviewMode: true,  // iframe live preview (Divi-style)
    _iframeContextMenu: null,
    previewHeaderContent: null,
    previewFooterContent: null,
    previewCssUrls: [],
    previewInlineCss: '',
    cleanMode: false,
    pageSettingsOpen: false,
    stylePanelOpen: false,
    inlineEditingTileId: null,
    inlineEditingField: null,
    // Inspector V2: when true, every hoverable field opens its hover variant
    // by default. Visual indicator: amber bar at the top of the inspector panel.
    editingHover: false,
    // ── Unified Editing ──
    activeZone: 'body',          // 'header' | 'body' | 'footer'
    headerTemplate: null,        // Full template object for the active header
    footerTemplate: null,        // Full template object for the active footer
    headerDirty: false,
    footerDirty: false,
    unifiedMode: false,          // True when editing H+B+F together
    insertAfterTileId: null,     // When set, next element added from sidebar goes after this tile
    canvasZoom: 100,              // Canvas zoom percentage (25-200)
    iframeLayout: { sections: [], columns: [] },  // Cached layout snapshot from iframe (usato da CanvasDragOverlay per hit-test)
  }),

  getters: {
    selectedTile(state) {
      if (!state.selectedTileId) return null;
      // Validate tile still exists in canvas
      const tilesStore = useTilesStoreRef();
      const node = tilesStore.getTileById(state.selectedTileId);
      if (!node) {
        state.selectedTileId = null;
        return null;
      }
      return state.selectedTileId;
    },
    isEditing(state) {
      return state.currentTemplate !== null;
    },
    /**
     * True if any zone (body, header, footer) has unsaved changes
     */
    isAnyDirty(state) {
      return state.isDirty || state.headerDirty || state.footerDirty;
    },
    pageSettings(state) {
      const defaults = {
        content_max_width: 1200,
        breakpoints: {
          widescreen: 1400,
          tablet_landscape: 1200,
          tablet: 960,
          mobile_landscape: 640,
          mobile: 480,
        },
        page_bg: {
          type: 'none',
          color: '#ffffff',
          gradient_angle: 180,
          gradient_from: '#ffffff',
          gradient_to: '#000000',
          image_url: '',
          image_size: 'cover',
          image_position: 'center center',
          parallax: false,
          parallax_speed: 0.3,
          overlay_color: '#000000',
          overlay_opacity: 0,
        },
      };
      const settings = state.currentTemplate?.settings || {};
      return {
        ...defaults,
        ...settings,
        page_bg: { ...defaults.page_bg, ...(settings.page_bg || {}) },
      };
    },
  },

  actions: {
    async loadTemplate(id) {
      const olo = getOloData();
      const MAX_RETRIES = 2;
      for (let attempt = 0; attempt <= MAX_RETRIES; attempt++) {
        try {
          const res = await fetch(`${olo.restUrl}/templates/${id}`, {
            headers: { 'X-WP-Nonce': olo.nonce },
          });
          if (!res.ok) {
            const errText = await res.text().catch(() => '');
            throw new Error(`HTTP ${res.status}: ${errText.slice(0, 200)}`);
          }
          const tpl = await res.json();
          // Ensure settings is always a plain object (PHP may return [] instead of {})
          if (!tpl.settings || Array.isArray(tpl.settings)) tpl.settings = {};
          this.currentTemplate = tpl;
          this.isDirty = false;
          return true;
        } catch (err) {
          console.error(`loadTemplate attempt ${attempt + 1}/${MAX_RETRIES + 1} error:`, err);
          if (attempt < MAX_RETRIES) {
            await new Promise(r => setTimeout(r, 500 * (attempt + 1)));
          }
        }
      }
      return false;
    },

    async saveTemplate() {
      if (!this.currentTemplate || this.isSaving) return;

      // In unified mode, save all zones at once
      if (this.unifiedMode) {
        return this.saveAllZones();
      }

      this.isSaving = true;
      const olo = getOloData();
      try {
        const tilesStore = useTilesStoreRef();

        // Sincronizza widget globali modificati → master nel DB
        await tilesStore.syncGlobalWidgetsOnSave();

        const method = this.currentTemplate.id ? 'PUT' : 'POST';
        const url = this.currentTemplate.id
          ? `${olo.restUrl}/templates/${this.currentTemplate.id}`
          : `${olo.restUrl}/templates`;

        const res = await fetch(url, {
          method,
          headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': olo.nonce,
          },
          body: JSON.stringify({
            title: this.currentTemplate.title || 'Untitled',
            type: this.currentTemplate.type || 'page',
            content: tilesStore.canvasTiles,
            settings: this.currentTemplate.settings || {},
            status: this.currentTemplate.status || 'draft',
          }),
        });

        if (!res.ok) throw new Error('Failed to save template');
        const saved = await res.json();
        // Ensure settings is always a plain object (PHP may return [] instead of {})
        if (!saved.settings || Array.isArray(saved.settings)) saved.settings = {};
        this.currentTemplate = saved;
        this.isDirty = false;
        useToast().success('Template salvato');

        // Trigger auto-thumbnail capture (handler standalone in olo-thumb-capture.js)
        if (saved.id && typeof window !== 'undefined') {
          window.dispatchEvent(new CustomEvent('olobuild:saved', {
            detail: { templateId: saved.id, type: saved.type },
          }));
        }
      } catch (err) {
        console.error('saveTemplate error:', err);
        useToast().error('Errore nel salvataggio');
      } finally {
        this.isSaving = false;
      }
    },

    async togglePublish() {
      if (!this.currentTemplate || this.isSaving) return;

      const newStatus = this.currentTemplate.status === 'published' ? 'draft' : 'published';
      this.currentTemplate.status = newStatus;
      this.isDirty = true;
      await this.saveTemplate();
    },

    async togglePreview() {
      this.previewMode = !this.previewMode;
      if (this.previewMode) {
        // Carica HTML renderizzato di header e footer attivi
        const olo = getOloData();
        const type = this.currentTemplate?.type;
        const cssSet = new Set();
        let inlineCss = '';
        // Non caricare header/footer se stai editando un header o footer
        if (type !== 'header' && type !== 'footer') {
          const headerId = olo.activeHeaderId;
          const footerId = olo.activeFooterId;
          if (headerId) {
            try {
              const res = await fetch(`${olo.restUrl}/templates/${headerId}/render`, { headers: { 'X-WP-Nonce': olo.nonce } });
              if (res.ok) {
                const data = await res.json();
                this.previewHeaderContent = data.html || '';
                (data.css || []).forEach(u => cssSet.add(u));
                if (data.inline_css) inlineCss = data.inline_css;
              }
            } catch (e) { /* ignora */ }
          }
          if (footerId) {
            try {
              const res = await fetch(`${olo.restUrl}/templates/${footerId}/render`, { headers: { 'X-WP-Nonce': olo.nonce } });
              if (res.ok) {
                const data = await res.json();
                this.previewFooterContent = data.html || '';
                (data.css || []).forEach(u => cssSet.add(u));
                if (data.inline_css && !inlineCss) inlineCss = data.inline_css;
              }
            } catch (e) { /* ignora */ }
          }
        }
        this.previewCssUrls = [...cssSet];
        this.previewInlineCss = inlineCss;
      } else {
        this.previewHeaderContent = null;
        this.previewFooterContent = null;
        this.previewCssUrls = [];
        this.previewInlineCss = '';
      }
    },

    selectTile(tileId) {
      this.selectedTileId = tileId;
      this.selectedTileIds = tileId ? [tileId] : [];
      this.pageSettingsOpen = false;
      this.stylePanelOpen = false;
    },

    // Ctrl/Cmd-click: aggiunge/toglie una tile dal set, mantenendo l'ultima come primaria.
    toggleTileSelection(tileId) {
      if (!tileId) return;
      const i = this.selectedTileIds.indexOf(tileId);
      if (i === -1) {
        this.selectedTileIds.push(tileId);
        this.selectedTileId = tileId;
      } else {
        this.selectedTileIds.splice(i, 1);
        this.selectedTileId = this.selectedTileIds.length
          ? this.selectedTileIds[this.selectedTileIds.length - 1]
          : null;
      }
      this.pageSettingsOpen = false;
      this.stylePanelOpen = false;
    },

    deselectTile() {
      this.selectedTileId = null;
      this.selectedTileIds = [];
    },

    startInlineEdit(tileId, field) {
      this.inlineEditingTileId = tileId;
      this.inlineEditingField = field;
    },

    stopInlineEdit() {
      this.inlineEditingTileId = null;
      this.inlineEditingField = null;
    },

    setZoom(val) {
      this.canvasZoom = Math.max(25, Math.min(200, val));
    },
    zoomIn() {
      const steps = [25, 50, 75, 100, 125, 150, 175, 200];
      const next = steps.find(s => s > this.canvasZoom);
      this.canvasZoom = next || 200;
    },
    zoomOut() {
      const steps = [25, 50, 75, 100, 125, 150, 175, 200];
      const prev = [...steps].reverse().find(s => s < this.canvasZoom);
      this.canvasZoom = prev || 25;
    },

    togglePageSettings() {
      this.pageSettingsOpen = !this.pageSettingsOpen;
      if (this.pageSettingsOpen) {
        this.selectedTileId = null;
        this.selectedTileIds = [];
        this.stylePanelOpen = false;
      }
    },

    toggleStylePanel() {
      this.stylePanelOpen = !this.stylePanelOpen;
      if (this.stylePanelOpen) {
        this.selectedTileId = null;
        this.selectedTileIds = [];
        this.pageSettingsOpen = false;
      }
    },

    updatePageSetting(path, value) {
      if (!this.currentTemplate) return;
      if (!this.currentTemplate.settings) {
        this.currentTemplate.settings = {};
      }
      // Support nested paths like 'page_bg.color'
      const keys = path.split('.');
      let target = this.currentTemplate.settings;
      for (let i = 0; i < keys.length - 1; i++) {
        if (!target[keys[i]] || typeof target[keys[i]] !== 'object') {
          target[keys[i]] = {};
        }
        target = target[keys[i]];
      }
      target[keys[keys.length - 1]] = value;
      this.isDirty = true;
    },

    setViewMode(mode) {
      this.viewMode = mode;
    },

    // ── Unified Editing ──

    setActiveZone(zone) {
      if (['header', 'body', 'footer'].includes(zone)) {
        this.activeZone = zone;
      }
    },

    /**
     * Load header and footer templates for unified editing.
     * Called after the main template is loaded.
     */
    async loadUnifiedContext() {
      const olo = getOloData();
      const type = this.currentTemplate?.type;

      // Only load H+F for page/single templates, not for header/footer themselves
      if (type === 'header' || type === 'footer') {
        this.unifiedMode = false;
        return;
      }

      const tilesStore = useTilesStoreRef();
      // Coerce stringhe '0' a falsy e ignora id non positivi.
      // Senza questa coercion, `'0'` (stringa) supera `if (id)` (truthy) e fa fetch a /templates/0
      // che restituisce 404 — innocuo ma sporca la console.
      const headerId = parseInt(olo.activeHeaderId, 10) || 0;
      const footerId = parseInt(olo.activeFooterId, 10) || 0;

      // Load header template
      if (headerId > 0) {
        try {
          const res = await fetch(`${olo.restUrl}/templates/${headerId}`, {
            headers: { 'X-WP-Nonce': olo.nonce },
          });
          if (res.ok) {
            const tpl = await res.json();
            if (!tpl.settings || Array.isArray(tpl.settings)) tpl.settings = {};
            this.headerTemplate = tpl;
            tilesStore.setHeaderTiles(tpl.content || []);
          }
        } catch (e) {
          console.warn('[Olobuild] Failed to load header template:', e);
        }
      }

      // Load footer template
      if (footerId > 0) {
        try {
          const res = await fetch(`${olo.restUrl}/templates/${footerId}`, {
            headers: { 'X-WP-Nonce': olo.nonce },
          });
          if (res.ok) {
            const tpl = await res.json();
            if (!tpl.settings || Array.isArray(tpl.settings)) tpl.settings = {};
            this.footerTemplate = tpl;
            tilesStore.setFooterTiles(tpl.content || []);
          }
        } catch (e) {
          console.warn('[Olobuild] Failed to load footer template:', e);
        }
      }

      this.unifiedMode = true;
      this.headerDirty = false;
      this.footerDirty = false;
      this.activeZone = 'body';
    },

    /**
     * Save all dirty zones (body + header + footer) in unified mode
     */
    async saveAllZones() {
      if (this.isSaving) return;
      this.isSaving = true;

      const olo = getOloData();
      const tilesStore = useTilesStoreRef();

      try {
        // Sync global widgets across all zones
        await tilesStore.syncGlobalWidgetsOnSave();

        // Save body (main template)
        if (this.isDirty && this.currentTemplate) {
          const method = this.currentTemplate.id ? 'PUT' : 'POST';
          const url = this.currentTemplate.id
            ? `${olo.restUrl}/templates/${this.currentTemplate.id}`
            : `${olo.restUrl}/templates`;

          const res = await fetch(url, {
            method,
            headers: {
              'Content-Type': 'application/json',
              'X-WP-Nonce': olo.nonce,
            },
            body: JSON.stringify({
              title: this.currentTemplate.title || 'Untitled',
              type: this.currentTemplate.type || 'page',
              content: tilesStore.canvasTiles,
              settings: this.currentTemplate.settings || {},
              status: this.currentTemplate.status || 'draft',
            }),
          });
          if (res.ok) {
            const saved = await res.json();
            if (!saved.settings || Array.isArray(saved.settings)) saved.settings = {};
            this.currentTemplate = saved;
            this.isDirty = false;
          }
        }

        // Save header
        if (this.headerDirty && this.headerTemplate?.id) {
          const res = await fetch(`${olo.restUrl}/templates/${this.headerTemplate.id}`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'X-WP-Nonce': olo.nonce,
            },
            body: JSON.stringify({
              title: this.headerTemplate.title || 'Header',
              type: 'header',
              content: tilesStore.headerTiles,
              settings: this.headerTemplate.settings || {},
              status: this.headerTemplate.status || 'published',
            }),
          });
          if (res.ok) {
            const saved = await res.json();
            if (!saved.settings || Array.isArray(saved.settings)) saved.settings = {};
            this.headerTemplate = saved;
            this.headerDirty = false;
          }
        }

        // Save footer
        if (this.footerDirty && this.footerTemplate?.id) {
          const res = await fetch(`${olo.restUrl}/templates/${this.footerTemplate.id}`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'X-WP-Nonce': olo.nonce,
            },
            body: JSON.stringify({
              title: this.footerTemplate.title || 'Footer',
              type: 'footer',
              content: tilesStore.footerTiles,
              settings: this.footerTemplate.settings || {},
              status: this.footerTemplate.status || 'published',
            }),
          });
          if (res.ok) {
            const saved = await res.json();
            if (!saved.settings || Array.isArray(saved.settings)) saved.settings = {};
            this.footerTemplate = saved;
            this.footerDirty = false;
          }
        }

        useToast().success('Tutto salvato');

        // Trigger auto-thumbnail capture per il template body (l'unico che si vede)
        if (this.currentTemplate?.id && typeof window !== 'undefined') {
          window.dispatchEvent(new CustomEvent('olobuild:saved', {
            detail: { templateId: this.currentTemplate.id, type: this.currentTemplate.type },
          }));
        }
      } catch (err) {
        console.error('saveAllZones error:', err);
        useToast().error('Errore nel salvataggio');
      } finally {
        this.isSaving = false;
      }
    },

    /**
     * Mark the appropriate zone as dirty based on tile ID
     */
    markDirtyForTile(tileId) {
      if (!this.unifiedMode) {
        this.isDirty = true;
        return;
      }
      const tilesStore = useTilesStoreRef();
      const zone = tilesStore.getZoneForTile(tileId);
      if (zone === 'header') this.headerDirty = true;
      else if (zone === 'footer') this.footerDirty = true;
      else this.isDirty = true;
    },
  },
});
