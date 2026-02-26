import { defineStore } from 'pinia';
import { useTilesStore as useTilesStoreRef } from './tiles';

function getOloData() {
  return window.oloData || {};
}

export const useBuilderStore = defineStore('builder', {
  state: () => ({
    currentTemplate: null,
    selectedTileId: null,
    isDirty: false,
    isSaving: false,
    viewMode: 'desktop', // desktop | tablet | mobile
    previewMode: false,
    pageSettingsOpen: false,
    stylePanelOpen: false,
  }),

  getters: {
    selectedTile(state) {
      return state.selectedTileId;
    },
    isEditing(state) {
      return state.currentTemplate !== null;
    },
    pageSettings(state) {
      const defaults = {
        content_max_width: 1200,
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

      this.isSaving = true;
      const olo = getOloData();
      try {
        const tilesStore = useTilesStoreRef();

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
      } catch (err) {
        console.error('saveTemplate error:', err);
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

    selectTile(tileId) {
      this.selectedTileId = tileId;
      this.pageSettingsOpen = false;
      this.stylePanelOpen = false;
    },

    deselectTile() {
      this.selectedTileId = null;
    },

    togglePageSettings() {
      this.pageSettingsOpen = !this.pageSettingsOpen;
      if (this.pageSettingsOpen) {
        this.selectedTileId = null;
        this.stylePanelOpen = false;
      }
    },

    toggleStylePanel() {
      this.stylePanelOpen = !this.stylePanelOpen;
      if (this.stylePanelOpen) {
        this.selectedTileId = null;
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
  },
});
